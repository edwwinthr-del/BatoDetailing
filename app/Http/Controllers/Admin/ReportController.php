<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reports): View
    {
        [$from, $to] = $this->dateRange($request);

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'report' => $reports->build($from, $to),
        ]);
    }

    public function exportCsv(Request $request, ReportService $reports): StreamedResponse
    {
        [$from, $to] = $this->dateRange($request);
        $report = $reports->build($from, $to);

        $filename = 'report-'.$from->format('Y-m-d').'-'.$to->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($report, $from, $to): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['BatoDetailing report', $from->format('Y-m-d'), $to->format('Y-m-d')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Revenue', $report['revenue']]);
            fputcsv($handle, ['Invoices issued', $report['invoiceCount']]);
            fputcsv($handle, ['Appointments', $report['appointmentCount']]);
            fputcsv($handle, ['Completed appointments', $report['completedCount']]);
            fputcsv($handle, ['Average rating', $report['averageRating']]);
            fputcsv($handle, ['Reviews', $report['reviewCount']]);
            fputcsv($handle, []);
            fputcsv($handle, ['Service', 'Bookings', 'Revenue']);

            foreach ($report['servicePopularity'] as $row) {
                fputcsv($handle, [$row->name, $row->bookings, $row->revenue]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPdf(Request $request, ReportService $reports): Response
    {
        [$from, $to] = $this->dateRange($request);

        $pdf = Pdf::loadView('pdf.report', [
            'from' => $from,
            'to' => $to,
            'report' => $reports->build($from, $to),
        ]);

        return $pdf->download('report-'.$from->format('Y-m-d').'-'.$to->format('Y-m-d').'.pdf');
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function dateRange(Request $request): array
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : now()->subMonth()->startOfDay();
        $to = $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : now()->endOfDay();

        return [$from, $to];
    }
}
