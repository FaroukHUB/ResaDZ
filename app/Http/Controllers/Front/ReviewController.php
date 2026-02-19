<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Show the review form (accessed via booking token).
     */
    public function create(string $token)
    {
        $booking = Booking::with(['vehicle.brand', 'loueur'])
            ->where('confirmation_token', $token)
            ->where('status', 'completed')
            ->firstOrFail();

        // Check if already reviewed
        if ($booking->client_reviewed) {
            return redirect()->route('booking.client-confirmation', $token)
                ->with('info', 'Vous avez déjà laissé un avis pour cette location.');
        }

        return view('front.pages.review-form', compact('booking'));
    }

    /**
     * Store a review from a client.
     */
    public function store(Request $request, string $token)
    {
        $booking = Booking::with(['loueur', 'vehicle'])
            ->where('confirmation_token', $token)
            ->where('status', 'completed')
            ->firstOrFail();

        if ($booking->client_reviewed) {
            return redirect()->route('booking.client-confirmation', $token)
                ->with('info', 'Vous avez déjà laissé un avis.');
        }

        $validated = $request->validate([
            'rating_overall' => 'required|integer|min:1|max:5',
            'rating_vehicle' => 'nullable|integer|min:1|max:5',
            'rating_communication' => 'nullable|integer|min:1|max:5',
            'rating_punctuality' => 'nullable|integer|min:1|max:5',
            'rating_cleanliness' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'booking_id' => $booking->id,
            'loueur_id' => $booking->loueur_id,
            'type' => 'client_to_loueur',
            'reviewer_id' => $booking->client_id,
            'reviewed_user_id' => $booking->loueur->user_id ?? null,
            'rating_overall' => $validated['rating_overall'],
            'rating_vehicle' => $validated['rating_vehicle'] ?? null,
            'rating_communication' => $validated['rating_communication'] ?? null,
            'rating_punctuality' => $validated['rating_punctuality'] ?? null,
            'rating_cleanliness' => $validated['rating_cleanliness'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'is_public' => true,
            'is_approved' => true,
        ]);

        // Mark booking as reviewed
        $booking->update(['client_reviewed' => true]);

        // Update loueur average rating
        $this->updateLoueurRating($booking->loueur_id);

        return redirect()->route('booking.client-confirmation', $token)
            ->with('success', 'Merci pour votre avis !');
    }

    /**
     * Recalculate and update loueur's average rating.
     */
    private function updateLoueurRating(int $loueurId): void
    {
        $stats = Review::where('loueur_id', $loueurId)
            ->where('type', 'client_to_loueur')
            ->where('is_public', true)
            ->where('is_approved', true)
            ->selectRaw('AVG(rating_overall) as avg_rating, COUNT(*) as total')
            ->first();

        if ($stats) {
            \App\Models\Loueur::where('id', $loueurId)->update([
                'rating' => round($stats->avg_rating, 2),
                'total_reviews' => $stats->total,
            ]);
        }
    }
}
