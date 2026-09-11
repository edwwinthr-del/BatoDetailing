<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.invoices.index', [
            'invoices' => Invoice::with(['user', 'appointment.vehicle'])
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->when($request->filled('search'), function ($query) use ($request): void {
                    $search = '%'.$request->string('search').'%';
                    $query->where('number', 'like', $search)
                        ->orWhereHas('user', fn ($q) => $q
                            ->where('first_name', 'like', $search)
                            ->orWhere('last_name', 'like', $search)
                            ->orWhere('email', 'like', $search));
                })
                ->orderByDesc('issued_at')
                ->paginate(20)
                ->withQueryString(),
            'statuses' => Invoice::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Invoice::STATUSES)],
        ]);

        $invoice->update($validated);

        return back()->with('status', __('Invoice status updated.'));
    }

    public function resend(Invoice $invoice, InvoiceService $invoices): RedirectResponse
    {
        $invoices->resend($invoice);

        return back()->with('status', __('Invoice email queued for resend.'));
    }

    public function regenerate(Invoice $invoice, InvoiceService $invoices): RedirectResponse
    {
        $invoices->generatePdf($invoice);

        return back()->with('status', __('Invoice PDF regenerated.'));
    }

    public function export(): StreamedResponse
    {
        $filename = 'invoices-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Number', 'Customer', 'Email', 'Subtotal', 'Discount', 'Total', 'Status', 'Issued at']);

            Invoice::with('user')->orderBy('issued_at')->chunk(200, function ($invoices) use ($handle): void {
                foreach ($invoices as $invoice) {
                    fputcsv($handle, [
                        $invoice->number,
                        $invoice->user->name,
                        $invoice->user->email,
                        $invoice->subtotal,
                        $invoice->discount,
                        $invoice->total,
                        $invoice->status,
                        $invoice->issued_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
