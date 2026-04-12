<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Tabs --}}
        <div style="display: flex; gap: 8px; background: #f1f5f9; padding: 6px; border-radius: 14px; width: fit-content;">
            <button wire:click="$set('activeTab', 'templates')"
                style="padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; border: none; cursor: pointer; transition: all 0.2s;
                {{ $activeTab === 'templates' ? 'background: linear-gradient(135deg, #FF6B2C, #F59E0B); color: white; box-shadow: 0 4px 12px rgba(255,107,44,0.3);' : 'background: transparent; color: #64748b;' }}">
                Modèles prédéfinis
            </button>
            <button wire:click="$set('activeTab', 'ai')"
                style="padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; border: none; cursor: pointer; transition: all 0.2s;
                {{ $activeTab === 'ai' ? 'background: linear-gradient(135deg, #6366F1, #8B5CF6); color: white; box-shadow: 0 4px 12px rgba(99,102,241,0.3);' : 'background: transparent; color: #64748b;' }}">
                Écrire avec l'IA
            </button>
            <button wire:click="$set('activeTab', 'history')"
                style="padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; border: none; cursor: pointer; transition: all 0.2s;
                {{ $activeTab === 'history' ? 'background: #1e293b; color: white;' : 'background: transparent; color: #64748b;' }}">
                Historique ({{ $recentEmails->count() }})
            </button>
        </div>

        {{-- TAB 1: Templates --}}
        @if($activeTab === 'templates')
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- Template list --}}
            <div class="lg:col-span-2 space-y-3">
                <h3 style="font-size: 13px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Modèle d'email</h3>
                @php
                    $icons = [
                        'relance_paiement' => '💰',
                        'loueur_inactif' => '😴',
                        'avertissement_suppression' => '⚠️',
                        'bienvenue_personnalise' => '👋',
                        'rappel_calendrier' => '📅',
                        'demande_documents' => '📄',
                        'felicitations_reservation' => '🎉',
                        'promotion' => '📢',
                    ];
                @endphp
                @foreach($templates as $key => $template)
                    <div wire:click="$set('selectedTemplate', '{{ $key }}')"
                         style="padding: 14px 16px; border-radius: 12px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 12px;
                         {{ $selectedTemplate === $key ? 'background: linear-gradient(135deg, #FFF7ED, #FEF3C7); border: 2px solid #F59E0B; box-shadow: 0 2px 8px rgba(245,158,11,0.15);' : 'background: white; border: 1px solid #e2e8f0;' }}">
                        <span style="font-size: 22px;">{{ $icons[$key] ?? '📧' }}</span>
                        <span style="font-weight: 600; font-size: 14px; color: #1e293b;">{{ $template['label'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Preview + Recipient + Send --}}
            <div class="lg:col-span-3 space-y-4">

                {{-- Recipient --}}
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px;">
                    <h3 style="font-size: 13px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">Destinataire</h3>
                    <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                        <button wire:click="$set('recipientType', 'loueur')"
                            style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none;
                            {{ $recipientType === 'loueur' ? 'background: #1e293b; color: white;' : 'background: #f1f5f9; color: #64748b;' }}">
                            Un loueur
                        </button>
                        <button wire:click="$set('recipientType', 'custom')"
                            style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none;
                            {{ $recipientType === 'custom' ? 'background: #1e293b; color: white;' : 'background: #f1f5f9; color: #64748b;' }}">
                            Email libre
                        </button>
                    </div>
                    @if($recipientType === 'loueur')
                        <select wire:model.live="selectedLoueurId" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #d1d5db; font-size: 14px;">
                            <option value="">— Choisir un loueur —</option>
                            @foreach($loueurs as $l)
                                <option value="{{ $l->id }}">{{ $l->company_name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="email" wire:model="customEmail" placeholder="email@exemple.com" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #d1d5db; font-size: 14px;" />
                    @endif
                </div>

                {{-- Email preview --}}
                @if($previewSubject)
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden;">
                        {{-- Email header --}}
                        <div style="background: linear-gradient(135deg, #1e293b, #334155); padding: 16px 20px; display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: white; font-weight: 700; font-size: 14px; margin: 0;">Aperçu de l'email</p>
                                <p style="color: rgba(255,255,255,0.6); font-size: 12px; margin: 2px 0 0 0;">Modifiable avant envoi</p>
                            </div>
                        </div>

                        <div style="padding: 20px;">
                            <div style="margin-bottom: 16px;">
                                <label style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Sujet</label>
                                <input type="text" wire:model="previewSubject" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 15px; font-weight: 600; margin-top: 4px;" />
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Corps du message</label>
                                <textarea wire:model="previewBody" rows="14" style="width: 100%; padding: 14px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px; line-height: 1.6; margin-top: 4px; resize: vertical;"></textarea>
                            </div>
                        </div>

                        <div style="padding: 0 20px 20px;">
                            <button wire:click="sendTemplate"
                                style="width: 100%; padding: 14px; background: linear-gradient(135deg, #22c55e, #16a34a); color: white; font-weight: 700; font-size: 15px; border: none; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(34,197,94,0.3);">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                                Envoyer l'email
                            </button>
                        </div>
                    </div>
                @else
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 60px 20px; text-align: center;">
                        <svg width="64" height="64" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1" style="margin: 0 auto 16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <p style="color: #94a3b8; font-size: 15px;">Sélectionnez un modèle à gauche</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- TAB 2: AI --}}
        @if($activeTab === 'ai')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- AI Input --}}
            <div style="background: linear-gradient(135deg, #EEF2FF, #F5F3FF); border: 2px solid #C7D2FE; border-radius: 20px; padding: 28px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div>
                        <h3 style="font-weight: 800; color: #312E81; font-size: 18px; margin: 0;">Rédaction IA</h3>
                        <p style="color: #6366F1; font-size: 13px; margin: 2px 0 0 0;">Décrivez, l'IA rédige</p>
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="font-size: 12px; font-weight: 700; color: #4338CA; text-transform: uppercase;">Destinataire</label>
                    <div style="display: flex; gap: 8px; margin-top: 6px;">
                        <button wire:click="$set('aiRecipientType', 'loueur')"
                            style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none;
                            {{ $aiRecipientType === 'loueur' ? 'background: #4338CA; color: white;' : 'background: white; color: #6366F1; border: 1px solid #C7D2FE;' }}">
                            Un loueur
                        </button>
                        <button wire:click="$set('aiRecipientType', 'custom')"
                            style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none;
                            {{ $aiRecipientType === 'custom' ? 'background: #4338CA; color: white;' : 'background: white; color: #6366F1; border: 1px solid #C7D2FE;' }}">
                            Email libre
                        </button>
                    </div>
                </div>

                @if($aiRecipientType === 'loueur')
                    <select wire:model="aiSelectedLoueurId" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #C7D2FE; font-size: 14px; margin-bottom: 16px; background: white;">
                        <option value="">— Choisir un loueur —</option>
                        @foreach($loueurs as $l)
                            <option value="{{ $l->id }}">{{ $l->company_name }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="email" wire:model="aiCustomEmail" placeholder="email@exemple.com" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #C7D2FE; font-size: 14px; margin-bottom: 16px; background: white;" />
                @endif

                <div style="margin-bottom: 16px;">
                    <label style="font-size: 12px; font-weight: 700; color: #4338CA; text-transform: uppercase;">Que voulez-vous lui dire ?</label>
                    <textarea wire:model="aiPrompt" rows="4" placeholder="Ex: Dis-lui qu'on a ajouté le paiement par CB et qu'il devrait configurer Stripe..."
                        style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #C7D2FE; font-size: 14px; margin-top: 6px; background: white; resize: vertical;"></textarea>
                </div>

                <button wire:click="generateWithAI" wire:loading.attr="disabled"
                    style="width: 100%; padding: 14px; background: linear-gradient(135deg, #6366F1, #8B5CF6); color: white; font-weight: 700; font-size: 15px; border: none; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(99,102,241,0.3);">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" wire:loading.class="animate-spin"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <span wire:loading.remove>Générer avec l'IA</span>
                    <span wire:loading>Génération...</span>
                </button>
            </div>

            {{-- AI Result --}}
            <div>
                @if($aiSubject || $aiBody)
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden;">
                        <div style="background: linear-gradient(135deg, #6366F1, #8B5CF6); padding: 16px 20px; display: flex; align-items: center; gap: 10px;">
                            <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            <p style="color: white; font-weight: 700; font-size: 14px; margin: 0;">Email généré par l'IA — modifiable</p>
                        </div>
                        <div style="padding: 20px;">
                            <div style="margin-bottom: 16px;">
                                <label style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Sujet</label>
                                <input type="text" wire:model="aiSubject" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 15px; font-weight: 600; margin-top: 4px;" />
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Corps</label>
                                <textarea wire:model="aiBody" rows="14" style="width: 100%; padding: 14px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px; line-height: 1.6; margin-top: 4px; resize: vertical;"></textarea>
                            </div>
                        </div>
                        <div style="padding: 0 20px 20px;">
                            <button wire:click="sendAIEmail"
                                style="width: 100%; padding: 14px; background: linear-gradient(135deg, #22c55e, #16a34a); color: white; font-weight: 700; font-size: 15px; border: none; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(34,197,94,0.3);">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                                Envoyer l'email
                            </button>
                        </div>
                    </div>
                @else
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 80px 20px; text-align: center;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #EEF2FF, #E0E7FF); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <svg width="40" height="40" fill="none" stroke="#6366F1" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <p style="color: #6366F1; font-weight: 600; font-size: 16px; margin: 0 0 4px 0;">L'IA est prête</p>
                        <p style="color: #94a3b8; font-size: 14px;">Décrivez ce que vous voulez dire et l'IA rédigera l'email</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- TAB 3: History --}}
        @if($activeTab === 'history')
        <div style="background: white; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="text-align: left; padding: 14px 16px; font-weight: 600; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
                            <th style="text-align: left; padding: 14px 16px; font-weight: 600; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Destinataire</th>
                            <th style="text-align: left; padding: 14px 16px; font-weight: 600; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Sujet</th>
                            <th style="text-align: left; padding: 14px 16px; font-weight: 600; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Type</th>
                            <th style="text-align: left; padding: 14px 16px; font-weight: 600; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentEmails as $email)
                            <tr style="border-top: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px; color: #64748b; white-space: nowrap;">{{ $email->created_at->format('d/m/Y H:i') }}</td>
                                <td style="padding: 12px 16px;">
                                    <div style="font-weight: 600; color: #1e293b;">{{ $email->to_name ?? '-' }}</div>
                                    <div style="font-size: 12px; color: #94a3b8;">{{ $email->to_email }}</div>
                                </td>
                                <td style="padding: 12px 16px; color: #475569; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $email->subject }}</td>
                                <td style="padding: 12px 16px;">
                                    @if($email->template_key === 'ai_generated')
                                        <span style="padding: 4px 10px; background: linear-gradient(135deg, #EEF2FF, #E0E7FF); color: #4338CA; font-size: 11px; font-weight: 700; border-radius: 6px;">IA</span>
                                    @elseif($email->template_key)
                                        <span style="padding: 4px 10px; background: #FEF3C7; color: #92400E; font-size: 11px; font-weight: 700; border-radius: 6px;">Modèle</span>
                                    @else
                                        <span style="padding: 4px 10px; background: #f1f5f9; color: #64748b; font-size: 11px; font-weight: 700; border-radius: 6px;">Manuel</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px;">
                                    @if($email->status === 'sent')
                                        <span style="padding: 4px 10px; background: #DCFCE7; color: #166534; font-size: 11px; font-weight: 700; border-radius: 6px;">Envoyé ✓</span>
                                    @else
                                        <span style="padding: 4px 10px; background: #FEE2E2; color: #991B1B; font-size: 11px; font-weight: 700; border-radius: 6px;">Échec</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 60px 20px; text-align: center; color: #94a3b8;">Aucun email envoyé pour le moment</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>
