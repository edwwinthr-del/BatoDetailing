<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_a_vehicle(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/vehicles', [
            'brand' => 'BMW',
            'model' => '320d',
            'year' => 2021,
            'type' => 'sedan',
            'license_plate' => 'NS-123-AB',
            'color' => 'black',
            'notes' => 'Careful with the rims.',
        ]);

        $response->assertRedirect(route('vehicles.index'));

        $vehicle = Vehicle::first();
        $this->assertSame($user->id, $vehicle->user_id);
        $this->assertSame('sedan', $vehicle->type);
    }

    public function test_user_can_add_a_vehicle_with_image(): void
    {
        if (! function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD extension is not installed.');
        }

        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/vehicles', [
            'brand' => 'BMW',
            'model' => '320d',
            'type' => 'sedan',
            'license_plate' => 'NS-123-AB',
            'image' => UploadedFile::fake()->image('car.jpg'),
        ]);

        $response->assertRedirect(route('vehicles.index'));

        $vehicle = Vehicle::first();
        $this->assertNotNull($vehicle->image_path);
        Storage::disk('public')->assertExists($vehicle->image_path);
    }

    public function test_vehicle_type_must_be_valid(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/vehicles', [
            'brand' => 'BMW',
            'model' => '320d',
            'type' => 'spaceship',
            'license_plate' => 'NS-123-AB',
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_user_cannot_update_someone_elses_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create();
        $other = User::factory()->create();

        $response = $this->actingAs($other)->put("/vehicles/{$vehicle->id}", [
            'brand' => 'Hacked',
            'model' => 'Hacked',
            'type' => 'sedan',
            'license_plate' => 'HAX-000',
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_delete_own_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create();

        $this->actingAs($vehicle->user)->delete("/vehicles/{$vehicle->id}")
            ->assertRedirect(route('vehicles.index'));

        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }

    public function test_vehicles_index_only_shows_own_vehicles(): void
    {
        $user = User::factory()->create();
        Vehicle::factory()->for($user)->create(['brand' => 'OwnBrand']);
        Vehicle::factory()->create(['brand' => 'OtherBrand']);

        $this->actingAs($user)->get('/vehicles')
            ->assertOk()
            ->assertSee('OwnBrand')
            ->assertDontSee('OtherBrand');
    }
}
