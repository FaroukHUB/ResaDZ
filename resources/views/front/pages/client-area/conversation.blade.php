@extends('front.layouts.app')

@section('title', 'Messages - ' . $booking->reference . ' - ResaDZ')

@section('content')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('client.dashboard', $token) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Messages</h1>
                    <p class="text-sm text-gray-500">{{ $booking->vehicle->full_name ?? 'Véhicule' }} - {{ $booking->reference }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-xl">
                    @if($booking->loueur->logo)
                        <img src="{{ asset('storage/' . $booking->loueur->logo) }}" alt="" class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold text-primary-600">{{ substr($booking->loueur->company_name ?? 'L', 0, 1) }}</span>
                        </div>
                    @endif
                    <span class="text-sm font-medium text-gray-700">{{ $booking->loueur->company_name ?? 'Loueur' }}</span>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- Chat Container -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden flex flex-col" style="height: calc(100vh - 280px); min-height: 500px;">

            <!-- Messages Area -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
                @forelse($conversation->messages as $message)
                    @if($message->sender_type === 'client')
                        <!-- My message (right) -->
                        <div class="flex justify-end">
                            <div class="max-w-[75%] bg-primary-600 text-white rounded-2xl rounded-br-md px-4 py-3">
                                <p class="text-sm whitespace-pre-wrap">{{ $message->display_content }}</p>
                                <p class="text-[11px] text-primary-200 mt-1 text-right">{{ $message->created_at->format('d/m H:i') }}</p>
                            </div>
                        </div>
                    @else
                        <!-- Their message (left) -->
                        <div class="flex justify-start">
                            <div class="max-w-[75%]">
                                <p class="text-xs text-gray-500 mb-1 ml-1">{{ $message->sender_name }}</p>
                                <div class="bg-gray-100 text-gray-800 rounded-2xl rounded-bl-md px-4 py-3">
                                    <p class="text-sm whitespace-pre-wrap">{{ $message->display_content }}</p>
                                    <p class="text-[11px] text-gray-400 mt-1">{{ $message->created_at->format('d/m H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-center py-12">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-700 mb-1">Aucun message</h3>
                        <p class="text-sm text-gray-500">Envoyez un message au loueur pour commencer la conversation.</p>
                    </div>
                @endforelse
            </div>

            <!-- Info Banner -->
            <div class="px-4 py-2 bg-amber-50 border-t border-amber-100">
                <p class="text-xs text-amber-700 text-center">
                    <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Les numéros de téléphone et emails sont automatiquement masqués pour votre sécurité.
                </p>
            </div>

            <!-- Message Input -->
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                <form action="{{ route('client.send-message', $token) }}" method="POST" class="flex gap-3">
                    @csrf
                    <div class="flex-1 relative">
                        <textarea
                            name="content"
                            placeholder="Écrivez votre message..."
                            rows="1"
                            class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring focus:ring-primary-200 resize-none px-4 py-3 text-sm"
                            required
                            maxlength="2000"
                            onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); this.form.submit(); }"
                        >{{ old('content') }}</textarea>
                    </div>
                    <button type="submit" class="px-5 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition flex items-center gap-2">
                        <span class="hidden sm:inline">Envoyer</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        // Scroll to bottom on load
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('messages-container');
            container.scrollTop = container.scrollHeight;
        });

        // Optional: Auto-refresh messages every 15 seconds
        setInterval(function() {
            fetch('{{ route("client.refresh-messages", $token) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        const container = document.getElementById('messages-container');
                        const wasAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 50;

                        // Simple: just reload if there are new messages
                        const currentCount = container.querySelectorAll('.flex.justify-start, .flex.justify-end').length;
                        if (data.messages.length > currentCount) {
                            location.reload();
                        }
                    }
                })
                .catch(err => console.log('Refresh error:', err));
        }, 15000);
    </script>

@endsection
