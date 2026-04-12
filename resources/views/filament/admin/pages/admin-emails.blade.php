<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Tabs --}}
        <div class="flex gap-2">
            <button wire:click="$set('activeTab', 'templates')" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition {{ $activeTab === 'templates' ? 'bg-amber-500 text-white shadow' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
                Modèles prédéfinis
            </button>
            <button wire:click="$set('activeTab', 'ai')" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition {{ $activeTab === 'ai' ? 'bg-indigo-500 text-white shadow' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
                Écrire avec l'IA
            </button>
            <button wire:click="$set('activeTab', 'history')" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition {{ $activeTab === 'history' ? 'bg-gray-700 text-white shadow' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
                Historique ({{ $recentEmails->count() }})
            </button>
        </div>

        {{-- TAB 1: Templates --}}
        @if($activeTab === 'templates')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Template selection --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Choisir un modèle</h3>

                <div class="space-y-2">
                    @foreach($templates as $key => $template)
                        <label wire:click="$set('selectedTemplate', '{{ $key }}')" class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition border-2 {{ $selectedTemplate === $key ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-amber-300' }}">
                            <input type="radio" wire:model="selectedTemplate" value="{{ $key }}" class="text-amber-500" />
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $template['label'] }}</span>
                        </label>
                    @endforeach
                </div>

                @if($selectedTemplate)
                    <button wire:click="selectTemplate" class="w-full py-2.5 bg-amber-500 text-white font-semibold rounded-xl hover:bg-amber-600 transition">
                        Aperçu
                    </button>
                @endif

                <div class="border-t pt-4 space-y-3">
                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Destinataire</h4>
                    <select wire:model.live="recipientType" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                        <option value="loueur">Un loueur</option>
                        <option value="custom">Email personnalisé</option>
                    </select>

                    @if($recipientType === 'loueur')
                        <select wire:model="selectedLoueurId" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                            <option value="">— Choisir un loueur —</option>
                            @foreach($loueurs as $l)
                                <option value="{{ $l->id }}">{{ $l->company_name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="email" wire:model="customEmail" placeholder="email@exemple.com" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm" />
                    @endif
                </div>
            </div>

            {{-- Preview & Send --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Aperçu et envoi</h3>

                @if($previewSubject)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Sujet</label>
                        <input type="text" wire:model="previewSubject" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm mt-1" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Corps</label>
                        <textarea wire:model="previewBody" rows="12" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm mt-1"></textarea>
                    </div>
                    <button wire:click="sendTemplate" class="w-full py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Envoyer l'email
                    </button>
                @else
                    <div class="text-center py-12 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <p>Sélectionnez un modèle et cliquez "Aperçu"</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- TAB 2: AI --}}
        @if($activeTab === 'ai')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- AI Prompt --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Écrire avec l'IA</h3>
                        <p class="text-xs text-gray-500">Décrivez ce que vous voulez dire, l'IA rédige l'email</p>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Destinataire</label>
                    <select wire:model.live="aiRecipientType" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm mt-1">
                        <option value="loueur">Un loueur</option>
                        <option value="custom">Email personnalisé</option>
                    </select>
                </div>

                @if($aiRecipientType === 'loueur')
                    <select wire:model="aiSelectedLoueurId" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
                        <option value="">— Choisir un loueur —</option>
                        @foreach($loueurs as $l)
                            <option value="{{ $l->id }}">{{ $l->company_name }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="email" wire:model="aiCustomEmail" placeholder="email@exemple.com" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm" />
                @endif

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Décrivez l'email à générer</label>
                    <textarea wire:model="aiPrompt" rows="4" placeholder="Ex: Dis-lui qu'on a ajouté le paiement par CB et qu'il devrait configurer son compte Stripe pour recevoir les paiements en ligne..." class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm mt-1"></textarea>
                </div>

                <button wire:click="generateWithAI" wire:loading.attr="disabled" class="w-full py-3 bg-indigo-500 text-white font-bold rounded-xl hover:bg-indigo-600 transition flex items-center justify-center gap-2 disabled:opacity-50">
                    <svg class="w-5 h-5" wire:loading.class="animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <span wire:loading.remove>Générer avec l'IA</span>
                    <span wire:loading>Génération en cours...</span>
                </button>
            </div>

            {{-- AI Result --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Résultat</h3>

                @if($aiSubject || $aiBody)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Sujet</label>
                        <input type="text" wire:model="aiSubject" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm mt-1" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Corps (modifiable)</label>
                        <textarea wire:model="aiBody" rows="12" class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm mt-1"></textarea>
                    </div>
                    <button wire:click="sendAIEmail" class="w-full py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Envoyer l'email
                    </button>
                @else
                    <div class="text-center py-12 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        <p>Décrivez ce que vous voulez dire et l'IA rédigera l'email</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- TAB 3: History --}}
        @if($activeTab === 'history')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Date</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Destinataire</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Sujet</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Type</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($recentEmails as $email)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="py-3 px-4 text-gray-500 whitespace-nowrap">{{ $email->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $email->to_name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $email->to_email }}</div>
                                </td>
                                <td class="py-3 px-4 text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ $email->subject }}</td>
                                <td class="py-3 px-4">
                                    @if($email->template_key === 'ai_generated')
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">IA</span>
                                    @elseif($email->template_key)
                                        <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">Modèle</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Manuel</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($email->status === 'sent')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Envoyé</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Échec</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-400">Aucun email envoyé pour le moment</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>
