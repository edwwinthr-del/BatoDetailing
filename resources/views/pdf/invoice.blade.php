<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; padding: 40px; }
        .header { width: 100%; border-bottom: 3px solid #f59e0b; padding-bottom: 20px; }
        .brand { font-size: 24px; font-weight: bold; color: #111827; }
        .brand span { color: #f59e0b; }
        .company-info { margin-top: 6px; color: #6b7280; font-size: 10px; line-height: 1.5; }
        .invoice-meta { margin-top: 25px; width: 100%; }
        .invoice-meta td { vertical-align: top; width: 50%; }
        .label { color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .value { margin-top: 3px; font-size: 12px; line-height: 1.5; }
        h1 { font-size: 18px; margin-top: 25px; color: #111827; }
        .number { color: #f59e0b; }
        table.items { width: 100%; margin-top: 20px; border-collapse: collapse; }
        table.items th { background: #111827; color: #fff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
        table.items th.right, table.items td.right { text-align: right; }
        table.items td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        .totals { width: 40%; margin-left: 60%; margin-top: 15px; }
        .totals td { padding: 4px 10px; }
        .totals .grand td { font-weight: bold; font-size: 14px; border-top: 2px solid #111827; padding-top: 8px; }
        .status { margin-top: 25px; display: inline-block; padding: 4px 12px; border: 1px solid #f59e0b; color: #b45309; border-radius: 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .footer { margin-top: 50px; padding-top: 15px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Bato<span>Detailing</span></div>
        <div class="company-info">
            {{ $company['address'] }}<br>
            {{ $company['phone'] }} · {{ $company['email'] }}
        </div>
    </div>

    <h1>Invoice <span class="number">{{ $invoice->number }}</span></h1>

    <table class="invoice-meta">
        <tr>
            <td>
                <div class="label">Billed to</div>
                <div class="value">
                    {{ $invoice->user->name }}<br>
                    {{ $invoice->user->email }}<br>
                    {{ $invoice->user->phone }}
                </div>
            </td>
            <td>
                <div class="label">Details</div>
                <div class="value">
                    Issued: {{ $invoice->issued_at->format('d.m.Y') }}<br>
                    Appointment: {{ $invoice->appointment->scheduled_at->format('d.m.Y H:i') }}<br>
                    Vehicle: {{ $invoice->appointment->vehicle->displayName() }} ({{ $invoice->appointment->vehicle->license_plate }})
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Service</th>
                <th class="right">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->appointment->services as $service)
                <tr>
                    <td>{{ $service->name }}</td>
                    <td class="right">{{ number_format((float) $service->pivot->price, 2) }} €</td>
                </tr>
            @endforeach
            <tr>
                <td>Vehicle type surcharge ({{ ucfirst($invoice->appointment->vehicle->type) }})</td>
                <td class="right">{{ number_format((float) $invoice->appointment->type_modifier, 2) }} €</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td class="right">{{ number_format((float) $invoice->subtotal, 2) }} €</td>
        </tr>
        @if ((float) $invoice->discount > 0)
            <tr>
                <td>Loyalty discount</td>
                <td class="right">-{{ number_format((float) $invoice->discount, 2) }} €</td>
            </tr>
        @endif
        <tr class="grand">
            <td>Total</td>
            <td class="right">{{ number_format((float) $invoice->total, 2) }} €</td>
        </tr>
    </table>

    <div class="status">{{ ucfirst($invoice->status) }}</div>

    <div class="footer">
        Thank you for choosing {{ $company['name'] }} — we make your car shine.
    </div>
</body>
</html>
