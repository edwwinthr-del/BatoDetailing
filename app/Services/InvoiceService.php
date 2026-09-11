<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Notifications\InvoiceSent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function __construct(protected SettingsService $settings) {}

    /**
     * Generate (or return the existing) invoice for a completed appointment,
     * render its PDF to storage, and queue the invoice email.
     */
    public function generateFor(Appointment $appointment): Invoice
    {
        $existing = Invoice::firstWhere('appointment_id', $appointment->id);

        if ($existing) {
            return $existing;
        }

        $invoice = Invoice::create([
            'number' => $this->nextNumber(),
            'user_id' => $appointment->user_id,
            'appointment_id' => $appointment->id,
            'subtotal' => (float) $appointment->subtotal + (float) $appointment->type_modifier,
            'discount' => $appointment->discount,
            'total' => $appointment->total,
            'status' => Invoice::STATUS_ISSUED,
            'issued_at' => now(),
        ]);

        $this->generatePdf($invoice);

        $appointment->user->notify(new InvoiceSent($invoice));

        return $invoice;
    }

    public function generatePdf(Invoice $invoice): string
    {
        $invoice->load(['user', 'appointment.vehicle', 'appointment.services']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'company' => [
                'name' => $this->settings->get('company.name'),
                'email' => $this->settings->get('company.email'),
                'phone' => $this->settings->get('company.phone'),
                'address' => $this->settings->get('company.address'),
            ],
        ]);

        $path = 'invoices/'.$invoice->number.'.pdf';

        Storage::put($path, $pdf->output());

        $invoice->update(['pdf_path' => $path]);

        return $path;
    }

    public function resend(Invoice $invoice): void
    {
        if (! $invoice->pdf_path || ! Storage::exists($invoice->pdf_path)) {
            $this->generatePdf($invoice);
        }

        $invoice->user->notify(new InvoiceSent($invoice));
    }

    /**
     * Format: BATO-YYYY-MM-XXXXXX (sequential within the month).
     */
    protected function nextNumber(): string
    {
        $prefix = 'BATO-'.now()->format('Y-m').'-';

        $countThisMonth = Invoice::where('number', 'like', $prefix.'%')->count();

        do {
            $number = $prefix.str_pad((string) (++$countThisMonth), 6, '0', STR_PAD_LEFT);
        } while (Invoice::where('number', $number)->exists());

        return $number;
    }
}
