<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Cairo', 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
        }
        .logo span {
            color: #dc2626;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h1 {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .invoice-number {
            font-size: 14px;
            color: #6b7280;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 10px;
        }
        .status-draft { background: #f3f4f6; color: #4b5563; }
        .status-sent { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .addresses {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .address-block {
            width: 45%;
        }
        .address-block h3 {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .address-block p {
            margin-bottom: 3px;
        }
        .address-block .name {
            font-weight: bold;
            font-size: 14px;
            color: #1f2937;
        }

        .dates {
            margin-bottom: 30px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .dates table {
            width: 100%;
        }
        .dates td {
            padding: 5px 0;
        }
        .dates td:first-child {
            color: #6b7280;
            width: 150px;
        }
        .dates td:last-child {
            font-weight: 500;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background: #1f2937;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }
        .items-table th:nth-child(2),
        .items-table th:nth-child(3),
        .items-table th:nth-child(4),
        .items-table td:nth-child(2),
        .items-table td:nth-child(3),
        .items-table td:nth-child(4) {
            text-align: center;
        }
        .items-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .items-table tbody tr:hover {
            background: #f9fafb;
        }

        .totals {
            width: 300px;
            margin-left: auto;
            margin-bottom: 30px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 8px 0;
        }
        .totals td:first-child {
            color: #6b7280;
        }
        .totals td:last-child {
            text-align: right;
            font-weight: 500;
        }
        .totals .total-row {
            border-top: 2px solid #1f2937;
            font-size: 16px;
            font-weight: bold;
        }
        .totals .total-row td {
            padding-top: 15px;
            color: #1f2937;
        }

        .notes {
            margin-top: 30px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .notes h3 {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }

        @media print {
            body {
                padding: 20px;
            }
            .header {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
@php
    $companyName = \App\Models\Setting::get('company_name', 'ResaDZ');
    $companySlogan = \App\Models\Setting::get('company_slogan', 'Marketplace de location de voitures');
    $companyAddress = \App\Models\Setting::get('company_address', 'Algérie');
    $companyEmail = \App\Models\Setting::get('company_email', 'contact@resadz.com');
    $companyPhone = \App\Models\Setting::get('company_phone', '');
    $companyNif = \App\Models\Setting::get('company_nif', '');
@endphp
<body>
    <div class="header">
        <div>
            <div class="logo">{{ $companyName }}</div>
            <p style="color: #6b7280; margin-top: 5px;">{{ $companySlogan }}</p>
            <p style="color: #6b7280;">{{ $companyAddress }}</p>
        </div>
        <div class="invoice-title">
            <h1>FACTURE</h1>
            <p class="invoice-number">{{ $invoice->invoice_number }}</p>
            <span class="status status-{{ $invoice->status }}">
                {{ \App\Models\Invoice::getStatuses()[$invoice->status] ?? $invoice->status }}
            </span>
        </div>
    </div>

    <div class="addresses">
        <div class="address-block">
            <h3>Facturé par</h3>
            <p class="name">{{ $companyName }}</p>
            <p>{{ $companySlogan }}</p>
            <p>{{ $companyAddress }}</p>
            @if($companyPhone)<p>Tél: {{ $companyPhone }}</p>@endif
            <p>{{ $companyEmail }}</p>
            @if($companyNif)<p>NIF: {{ $companyNif }}</p>@endif
        </div>
        <div class="address-block">
            <h3>Facturé à</h3>
            <p class="name">{{ $invoice->billing_name }}</p>
            @if($invoice->billing_address)<p>{{ $invoice->billing_address }}</p>@endif
            @if($invoice->billing_city)<p>{{ $invoice->billing_city }}</p>@endif
            @if($invoice->billing_phone)<p>Tél: {{ $invoice->billing_phone }}</p>@endif
            @if($invoice->billing_email)<p>{{ $invoice->billing_email }}</p>@endif
            @if($invoice->billing_nif)<p>NIF: {{ $invoice->billing_nif }}</p>@endif
        </div>
    </div>

    <div class="dates">
        <table>
            <tr>
                <td>Date d'émission:</td>
                <td>{{ $invoice->issue_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Date d'échéance:</td>
                <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
            </tr>
            @if($invoice->paid_date)
            <tr>
                <td>Date de paiement:</td>
                <td>{{ $invoice->paid_date->format('d/m/Y') }}</td>
            </tr>
            @endif
            @if($invoice->payment_method)
            <tr>
                <td>Méthode de paiement:</td>
                <td>{{ \App\Models\Invoice::getPaymentMethods()[$invoice->payment_method] ?? $invoice->payment_method }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Qté</th>
                <th>Prix unitaire</th>
                <th>Remise</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2, ',', ' ') }} DA</td>
                <td>{{ $item->discount > 0 ? number_format($item->discount, 2, ',', ' ') . ' DA' : '-' }}</td>
                <td>{{ number_format($item->total, 2, ',', ' ') }} DA</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 30px; color: #6b7280;">Aucune ligne de facture</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Sous-total:</td>
                <td>{{ number_format($invoice->subtotal, 2, ',', ' ') }} DA</td>
            </tr>
            @if($invoice->discount_amount > 0)
            <tr>
                <td>Remise:</td>
                <td style="color: #dc2626;">-{{ number_format($invoice->discount_amount, 2, ',', ' ') }} DA</td>
            </tr>
            @endif
            <tr>
                <td>TVA ({{ number_format($invoice->tax_rate, 0) }}%):</td>
                <td>{{ number_format($invoice->tax_amount, 2, ',', ' ') }} DA</td>
            </tr>
            <tr class="total-row">
                <td>Total TTC:</td>
                <td>{{ number_format($invoice->total, 2, ',', ' ') }} DA</td>
            </tr>
        </table>
    </div>

    @if($invoice->notes)
    <div class="notes">
        <h3>Notes</h3>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>{{ $companyName }} - {{ $companySlogan }}</p>
        <p>Facture générée le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>
