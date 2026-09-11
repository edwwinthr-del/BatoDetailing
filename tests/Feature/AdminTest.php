<?php

namespace Tests\Feature;

use App\Jobs\SendCampaign;
use App\Mail\CampaignMail;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
        $this->actingAs($user)->get('/admin/reports')->assertForbidden();
    }

    public function test_admin_can_view_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_admin_can_block_and_unblock_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)->patch("/admin/users/{$user->id}/toggle-block");
        $this->assertTrue($user->fresh()->is_blocked);

        $this->actingAs($admin)->patch("/admin/users/{$user->id}/toggle-block");
        $this->assertFalse($user->fresh()->is_blocked);
    }

    public function test_admin_cannot_block_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch("/admin/users/{$admin->id}/toggle-block");

        $this->assertFalse($admin->fresh()->is_blocked);
    }

    public function test_admin_can_promote_a_user_to_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => Role::ADMIN]);

        $this->actingAs($admin)->put("/admin/users/{$user->id}", [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => 'promoted_user',
            'email' => $user->email,
            'phone' => $user->phone,
            'role_id' => $adminRole->id,
        ])->assertSessionHasNoErrors();

        $this->assertTrue($user->fresh()->isAdmin());
    }

    public function test_admin_can_create_and_deactivate_a_service(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/services', [
            'name' => 'Headlight Restoration',
            'description' => 'Polish and seal foggy headlights.',
            'base_price' => 45,
            'duration_minutes' => 60,
            'is_active' => '1',
        ])->assertRedirect(route('admin.services.index'));

        $service = Service::where('name', 'Headlight Restoration')->first();
        $this->assertNotNull($service);

        $this->actingAs($admin)->patch("/admin/services/{$service->id}/toggle");
        $this->assertFalse($service->fresh()->is_active);
    }

    public function test_admin_can_update_pricing_modifiers_and_business_hours(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put('/admin/pricing', [
            'modifiers' => [
                'coupe' => 7, 'hatchback' => 1, 'sedan' => 6,
                'suv' => 20, 'minivan' => 25, 'pickup' => 18,
            ],
            'open' => 9,
            'close' => 17,
            'days' => [1, 2, 3, 4, 5],
        ])->assertRedirect();

        $settings = app(SettingsService::class);
        $this->assertSame(20.0, $settings->modifierFor('suv'));
        $this->assertSame(['open' => 9, 'close' => 17, 'days' => [1, 2, 3, 4, 5]], $settings->businessHours());
    }

    public function test_campaign_is_queued_and_only_targets_opted_in_users(): void
    {
        Bus::fake();

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/campaigns', [
            'subject' => 'Spring discount',
            'body' => 'Get 20% off all exterior services this week!',
        ])->assertRedirect();

        Bus::assertDispatched(SendCampaign::class);

        // Run the job directly and check recipients.
        Mail::fake();

        $optedIn = User::factory()->optedIn()->create();
        User::factory()->create(); // not opted in
        User::factory()->optedIn()->blocked()->create(); // blocked

        (new SendCampaign('Spring discount', 'Body'))->handle();

        Mail::assertQueued(CampaignMail::class, 1);
        Mail::assertQueued(CampaignMail::class, fn (CampaignMail $mail) => $mail->hasTo($optedIn->email));
    }

    public function test_admin_can_export_reports(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/reports')->assertOk();
        $this->actingAs($admin)->get('/admin/reports/export/csv')->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
