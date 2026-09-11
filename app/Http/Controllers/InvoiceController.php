<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        return view('invoices.index', [
            'invoices' => $request->user()->invoices()
                ->with('appointment.vehicle')
                ->orderByDesc('issued_at')
                ->paginate(10),
        ]);
    }

    public function show(Invoice $invoice): View
    {
        Gate::authorize('view', $invoice);

        return view('invoices.show', [
            'invoice' => $invoice->load(['appointment.vehicle', 'appointment.services']),
        ]);
    }

    public function download(Invoice $invoice, InvoiceService $invoices): StreamedResponse
    {
        Gate::authorize('download', $invoice);

        if (! $invoice->pdf_path || ! Storage::exists($invoice->pdf_path)) {
            $invoices->generatePdf($invoice);
            $invoice->refresh();
        }

        return Storage::download($invoice->pdf_path, $invoice->number.'.pdf');
    }
}
