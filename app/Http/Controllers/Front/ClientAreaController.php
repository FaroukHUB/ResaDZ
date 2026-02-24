<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingConversation;
use App\Models\BookingMessage;
use App\Models\ClientSupportConversation;
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

    /**
     * Show support page with conversation list and form.
     */
    public function support(string $token)
    {
        $booking = Booking::where('confirmation_token', $token)
            ->with(['vehicle.brand', 'loueur'])
            ->firstOrFail();

        // Get support conversations for this client email
        $conversations = ClientSupportConversation::where('client_email', $booking->client_email)
            ->with('latestMessage')
            ->orderByDesc('last_message_at')
            ->get();

        return view('front.pages.client-area.support', [
            'booking' => $booking,
            'conversations' => $conversations,
            'categories' => ClientSupportConversation::getCategories(),
            'token' => $token,
        ]);
    }

    /**
     * Create a new support conversation.
     */
    public function createSupportTicket(Request $request, string $token)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:general,booking,payment,complaint,other',
            'message' => 'required|string|max:5000',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        $booking = Booking::where('confirmation_token', $token)->firstOrFail();

        $conversation = ClientSupportConversation::create([
            'client_id' => $booking->client_id,
            'client_email' => $booking->client_email,
            'client_name' => $booking->client_name,
            'subject' => $request->subject,
            'category' => $request->category,
            'booking_id' => $request->booking_id,
            'last_message_at' => now(),
        ]);

        $conversation->addMessage($request->message, 'client', $booking->client_id);

        return redirect()->route('client.support.show', [$token, $conversation->id])
            ->with('success', 'Votre demande a été envoyée. Nous vous répondrons dans les plus brefs délais.');
    }

    /**
     * Show a specific support conversation.
     */
    public function showSupportConversation(string $token, int $conversationId)
    {
        $booking = Booking::where('confirmation_token', $token)
            ->with(['vehicle.brand', 'loueur'])
            ->firstOrFail();

        $conversation = ClientSupportConversation::where('id', $conversationId)
            ->where('client_email', $booking->client_email)
            ->with('messages')
            ->firstOrFail();

        $conversation->markAsReadByClient();

        return view('front.pages.client-area.support-conversation', [
            'booking' => $booking,
            'conversation' => $conversation,
            'token' => $token,
        ]);
    }

    /**
     * Reply to a support conversation.
     */
    public function replySupportConversation(Request $request, string $token, int $conversationId)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $booking = Booking::where('confirmation_token', $token)->firstOrFail();

        $conversation = ClientSupportConversation::where('id', $conversationId)
            ->where('client_email', $booking->client_email)
            ->firstOrFail();

        if ($conversation->status === 'closed') {
            return back()->with('error', 'Cette conversation est fermée.');
        }

        $conversation->addMessage($request->message, 'client', $booking->client_id);

        // Reopen if resolved
        if ($conversation->status === 'resolved') {
            $conversation->update(['status' => 'open']);
        }

        return back()->with('success', 'Message envoyé.');
    }
}
