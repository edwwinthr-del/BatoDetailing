<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingApproved extends Notification implements ShouldQueue
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
            ->subject(__('Booking approved — BatoDetailing'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->first_name]))
            ->line(__('Your appointment on :date has been approved.', ['date' => $this->appointment->scheduled_at->format('d.m.Y H:i')]))
            ->line(__('We look forward to seeing you!'))
            ->action(__('View appointment'), route('appointments.show', $this->appointment));
    }
}
