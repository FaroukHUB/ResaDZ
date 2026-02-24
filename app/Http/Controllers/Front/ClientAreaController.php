<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingConversation;
use App\Models\BookingMessage;
use Illuminate\Http\Request;

class ClientAreaController extends Controller
{
    /**
     * Show client dashboard (accessed via token).
     */
    public function dashboard(string $token)
    {
        $booking = Booking::where('confirmation_token', $token)
            ->with(['vehicle.brand', 'loueur'])
            ->firstOrFail();

        // Get all bookings for this client (by email)
        $bookings = Booking::where('client_email', $booking->client_email)
            ->with(['vehicle.brand', 'loueur'])
            ->orderByDesc('created_at')
            ->get();

        return view('front.pages.client-area.dashboard', [
            'currentBooking' => $booking,
            'bookings' => $bookings,
            'token' => $token,
        ]);
    }

    /**
     * Show conversation for a booking.
     */
    public function conversation(string $token)
    {
        $booking = Booking::where('confirmation_token', $token)
            ->with(['vehicle.brand', 'loueur'])
            ->firstOrFail();

        // Only allow messaging for confirmed bookings
        if (!in_array($booking->status, ['confirmed', 'active', 'completed'])) {
            return redirect()->route('client.dashboard', $token)
                ->with('error', 'La messagerie est disponible uniquement après confirmation de votre réservation.');
        }

        $conversation = BookingConversation::getOrCreateForBooking($booking);
        $conversation->load('messages');
        $conversation->markAsReadFor('client');

        return view('front.pages.client-area.conversation', [
            'booking' => $booking,
            'conversation' => $conversation,
            'token' => $token,
        ]);
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(Request $request, string $token)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $booking = Booking::where('confirmation_token', $token)->firstOrFail();

        if (!in_array($booking->status, ['confirmed', 'active', 'completed'])) {
            return back()->with('error', 'Messagerie non disponible.');
        }

        $conversation = BookingConversation::getOrCreateForBooking($booking);

        BookingMessage::create([
            'booking_conversation_id' => $conversation->id,
            'sender_type' => 'client',
            'sender_id' => $booking->client_id,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Message envoyé.');
    }

    /**
     * Refresh messages via AJAX.
     */
    public function refreshMessages(string $token)
    {
        $booking = Booking::where('confirmation_token', $token)->firstOrFail();
        $conversation = BookingConversation::where('booking_id', $booking->id)
            ->with('messages')
            ->first();

        if (!$conversation) {
            return response()->json(['messages' => []]);
        }

        $conversation->markAsReadFor('client');

        return response()->json([
            'messages' => $conversation->messages->map(fn ($m) => [
                'id' => $m->id,
                'content' => $m->display_content,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender_name,
                'created_at' => $m->created_at->format('d/m H:i'),
                'is_mine' => $m->sender_type === 'client',
            ]),
        ]);
    }
}
