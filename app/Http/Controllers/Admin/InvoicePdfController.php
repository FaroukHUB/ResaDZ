<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class InvoicePdfController extends Controller
{
    /**
     * Verify that the authenticated user can access this invoice.
     */
    private function authorizeAccess(Invoice $invoice): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Non autorisé');
        }

        // Super admin can access all invoices
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return;
        }

        // Loueur can only access their own invoices
        if ($user->loueur && $invoice->loueur_id === $user->loueur->id) {
            return;
        }

        abort(403, 'Vous n\'avez pas accès à cette facture');
    }

    public function download(Invoice $invoice)
    {
        $this->authorizeAccess($invoice);

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
        $this->authorizeAccess($invoice);

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
