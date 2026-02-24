@extends('front.layouts.app')

@section('title', 'Support - ' . $conversation->subject . ' - ResaDZ')

@section('content')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('client.support', $token) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $conversation->subject }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-{{ $conversation->status_color }}-100 text-{{ $conversation->status_color }}-700">
                            {{ $conversation->status_label }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $conversation->category_label }}</span>
                        @if($conversation->booking)
                            <span class="text-xs text-gray-400">&bull; {{ $conversation->booking->reference }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- Chat Container -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden flex flex-col" style="height: calc(100vh - 300px); min-height: 450px;">

            <!-- Messages Area -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
                @forelse($conversation->messages as $message)
                    @if($message->is_internal_note)
                        @continue
                    @endif

                    @if($message->sender_type === 'client')
                        <!-- My message (right) -->
                        <div class="flex justify-end">
                            <div class="max-w-[75%] bg-primary-600 text-white rounded-2xl rounded-br-md px-4 py-3">
                                <p class="text-sm whitespace-pre-wrap">{{ $message->content }}</p>
                                <p class="text-[11px] text-primary-200 mt-1 text-right">{{ $message->created_at->format('d/m H:i') }}</p>
                            </div>
                        </div>
                    @else
                        <!-- Admin message (left) -->
                        <div class="flex justify-start">
                            <div class="max-w-[75%]">
                                <p class="text-xs text-gray-500 mb-1 ml-1">Support ResaDZ</p>
                                <div class="bg-gray-100 text-gray-800 rounded-2xl rounded-bl-md px-4 py-3">
                                    <p class="text-sm whitespace-pre-wrap">{{ $message->content }}</p>
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
                        <p class="text-sm text-gray-500">Votre demande a été envoyée.</p>
                        <p class="text-xs text-gray-400 mt-1">Nous vous répondrons dans les plus brefs délais.</p>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            @if($conversation->status !== 'closed')
                <div class="p-4 border-t border-gray-100 bg-gray-50">
                    <form action="{{ route('client.support.reply', [$token, $conversation->id]) }}" method="POST" class="flex gap-3">
                        @csrf
                        <div class="flex-1 relative">
                            <textarea
                                name="message"
                                placeholder="Écrivez votre message..."
                                rows="1"
                                class="w-full rounded-xl border-gray-200 focus:border-primary-500 focus:ring focus:ring-primary-200 resize-none px-4 py-3 text-sm"
                                required
                                maxlength="5000"
                                onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); this.form.submit(); }"
                            >{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="px-5 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition flex items-center gap-2">
                            <span class="hidden sm:inline">Envoyer</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </form>
                </div>
            @else
                <div class="p-4 border-t border-gray-100 bg-gray-50 text-center">
                    <p class="text-sm text-gray-500">Cette conversation est fermée.</p>
                    <a href="{{ route('client.support', $token) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">Créer une nouvelle demande</a>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('messages-container');
            container.scrollTop = container.scrollHeight;
        });
    </script>

@endsection
