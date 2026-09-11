<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; padding: 40px; }
        .brand { font-size: 22px; font-weight: bold; border-bottom: 3px solid #f59e0b; padding-bottom: 14px; }
        .brand span { color: #f59e0b; }
        h1 { font-size: 16px; margin-top: 20px; }
        .range { color: #6b7280; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #111827; color: #fff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
        td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        .right { text-align: right; }
        .summary td:first-child { font-weight: bold; width: 50%; }
    </style>
</head>
<body>
    <div class="brand">Bato<span>Detailing</span></div>

    <h1>Business report</h1>
    <p class="range">{{ $from->format('d.m.Y') }} — {{ $to->format('d.m.Y') }}</p>

    <table class="summary">
        <tr><td>Revenue</td><td class="right">{{ number_format($report['revenue'], 2) }} €</td></tr>
        <tr><td>Invoices issued</td><td class="right">{{ $report['invoiceCount'] }}</td></tr>
        <tr><td>Appointments</td><td class="right">{{ $report['appointmentCount'] }}</td></tr>
        <tr><td>Completed appointments</td><td class="right">{{ $report['completedCount'] }}</td></tr>
        <tr><td>Average rating (satisfaction)</td><td class="right">{{ $report['averageRating'] ?: '—' }} / 5 ({{ $report['reviewCount'] }} reviews)</td></tr>
    </table>

    <h1 style="margin-top: 30px;">Service popularity</h1>
    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th class="right">Bookings</th>
                <th class="right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report['servicePopularity'] as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td class="right">{{ $row->bookings }}</td>
                    <td class="right">{{ number_format((float) $row->revenue, 2) }} €</td>
                </tr>
            @empty
                <tr><td colspan="3">No data for this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
