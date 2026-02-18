<?php

namespace App\Http\Controllers\Loueur;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\ContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    public function __construct(
        private ContractService $contractService
    ) {}

    /**
     * Download the contract PDF for a booking.
     */
    public function download(Booking $booking)
    {
        $user = Auth::user();

        // Verify access: loueur must own this booking or be super admin
        if ($user->role !== 'super_admin') {
            $loueur = $user->loueur;
            if (!$loueur || $booking->loueur_id !== $loueur->id) {
                abort(403, 'Accès non autorisé');
            }
        }

        $pdf = $this->contractService->generateContract($booking);
        $filename = $this->contractService->getFilename($booking);

        return $pdf->download($filename);
    }

    /**
     * Preview the contract PDF in browser.
     */
    public function preview(Booking $booking)
    {
        $user = Auth::user();

        // Verify access
        if ($user->role !== 'super_admin') {
            $loueur = $user->loueur;
            if (!$loueur || $booking->loueur_id !== $loueur->id) {
                abort(403, 'Accès non autorisé');
            }
        }

        $pdf = $this->contractService->generateContract($booking);

        return $pdf->stream($this->contractService->getFilename($booking));
    }

    /**
     * Download contract by token (for client confirmation page - no auth required).
     */
    public function downloadByToken(string $token)
    {
        $booking = Booking::where('confirmation_token', $token)->firstOrFail();

        // Only allow if booking is confirmed by loueur
        if (!$booking->isConfirmedByLoueur()) {
            abort(403, 'Le contrat n\'est pas encore disponible.');
        }

        $pdf = $this->contractService->generateContract($booking);
        $filename = $this->contractService->getFilename($booking);

        return $pdf->download($filename);
    }
}
