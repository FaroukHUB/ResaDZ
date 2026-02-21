<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Response;

class InvoicePdfController extends Controller
{
    public function download(Invoice $invoice)
    {
        $html = view('invoices.pdf', [
            'invoice' => $invoice,
            'items' => $invoice->items,
            'loueur' => $invoice->loueur,
        ])->render();

        // If DomPDF is available, generate actual PDF
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            return $pdf->download('facture-' . $invoice->invoice_number . '.pdf');
        }

        // Fallback: return HTML that can be printed as PDF
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="facture-' . $invoice->invoice_number . '.html"');
    }

    public function stream(Invoice $invoice)
    {
        $html = view('invoices.pdf', [
            'invoice' => $invoice,
            'items' => $invoice->items,
            'loueur' => $invoice->loueur,
        ])->render();

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            return $pdf->stream('facture-' . $invoice->invoice_number . '.pdf');
        }

        return response($html)->header('Content-Type', 'text/html');
    }
}
