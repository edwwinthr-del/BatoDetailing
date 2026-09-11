<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingRejected extends Notification implements ShouldQueue
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
            ->subject(__('Booking update — BatoDetailing'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->first_name]))
            ->line(__('Unfortunately we could not accept your appointment on :date.', ['date' => $this->appointment->scheduled_at->format('d.m.Y H:i')]))
            ->line(__('Any redeemed loyalty points have been returned to your account.'))
            ->line(__('Please book another time slot — we would love to take care of your car.'))
            ->action(__('Book again'), route('appointments.create'));
    }
}
