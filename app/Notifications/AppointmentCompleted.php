<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your car is ready! — BatoDetailing'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->first_name]))
            ->line(__('Your appointment from :date is completed.', ['date' => $this->appointment->scheduled_at->format('d.m.Y H:i')]))
            ->line(__('Loyalty points have been added to your account.'))
            ->line(__('Your invoice will arrive in a separate email.'))
            ->action(__('Leave a review'), route('appointments.show', $this->appointment));
    }
}
