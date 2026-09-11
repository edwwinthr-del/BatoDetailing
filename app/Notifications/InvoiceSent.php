<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class InvoiceSent extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invoice $invoice) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Your BatoDetailing Invoice')
            ->greeting(__('Hello :name,', ['name' => $notifiable->first_name]))
            ->line(__('Thank you for choosing BatoDetailing. Your invoice :number is attached.', ['number' => $this->invoice->number]))
            ->line(__('Total: :total €', ['total' => number_format((float) $this->invoice->total, 2)]))
            ->action(__('View invoices'), route('invoices.index'));

        if ($this->invoice->pdf_path && Storage::exists($this->invoice->pdf_path)) {
            $message->attachData(
                Storage::get($this->invoice->pdf_path),
                $this->invoice->number.'.pdf',
                ['mime' => 'application/pdf'],
            );
        }

        return $message;
    }
}
