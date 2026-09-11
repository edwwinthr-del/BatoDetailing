<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendCampaign implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $subjectLine,
        public string $body,
    ) {}

    public function handle(): void
    {
        User::where('promo_emails', true)
            ->where('is_blocked', false)
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    Mail::to($user->email)->queue(
                        new CampaignMail($this->subjectLine, $this->body, $user->name)
                    );
                }
            });
    }
}
