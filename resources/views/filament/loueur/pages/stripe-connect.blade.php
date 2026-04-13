<x-filament-panels::page>
    <div class="space-y-8 max-w-3xl">

        {{-- Header --}}
        <div style="background: linear-gradient(135deg, #635BFF 0%, #7B73FF 100%); border-radius: 16px; padding: 32px; color: white;">
            <div class="flex items-center gap-4">
                <div style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/></svg>
                </div>
                <div>
                    <h2 style="font-size: 24px; font-weight: 700; margin: 0;">Paiement en ligne</h2>
                    <p style="opacity: 0.85; margin: 4px 0 0 0;">Recevez des paiements par CB et PayPal directement sur votre compte</p>
                </div>
            </div>
        </div>

        @if($isConnected)
            {{-- Connected state --}}
            <div style="background: #f0fdf4; border: 2px solid #22c55e; border-radius: 16px; padding: 24px;">
                <div class="flex items-center gap-3">
                    <div style="width: 48px; height: 48px; background: #22c55e; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </div>
                    <div>
                        <p style="font-weight: 700; color: #166534; font-size: 18px; margin: 0;">Compte Stripe connecté</p>
                        <p style="color: #15803d; margin: 4px 0 0 0;">Vos clients peuvent payer en ligne par CB ou PayPal. L'argent arrive directement sur votre compte.</p>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <a href="{{ route('stripe.dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #635BFF; color: white; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        Mon dashboard Stripe
                    </a>
                </div>
            </div>

            {{-- Payment mode selection --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 16px; padding: 32px;">
                <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 8px 0;">Mode de paiement en ligne</h3>
                <p style="color: #6b7280; font-size: 14px; margin: 0 0 20px 0;">Choisissez ce que vos clients peuvent payer en ligne.</p>

                <div class="space-y-3">
                    <label style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; border: 2px solid {{ $this->onlinePaymentMode === 'advance_only' ? '#635BFF' : '#e5e7eb' }}; border-radius: 12px; cursor: pointer; background: {{ $this->onlinePaymentMode === 'advance_only' ? '#f0f4ff' : 'white' }};"
                           wire:click="$set('onlinePaymentMode', 'advance_only')">
                        <input type="radio" wire:model="onlinePaymentMode" value="advance_only" style="margin-top: 4px; accent-color: #635BFF;">
                        <div>
                            <p style="font-weight: 700; color: #111827; margin: 0; font-size: 15px;">Acompte en ligne uniquement</p>
                            <p style="color: #6b7280; font-size: 13px; margin: 4px 0 0 0;">Le client paie l'acompte par CB en ligne. Le reste est réglé en espèces à la remise du véhicule. <strong>Aucune commission ResaDZ</strong> n'est prélevée sur l'acompte — vous recevez 100%.</p>
                        </div>
                    </label>

                    <label style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; border: 2px solid {{ $this->onlinePaymentMode === 'advance_and_full' ? '#635BFF' : '#e5e7eb' }}; border-radius: 12px; cursor: pointer; background: {{ $this->onlinePaymentMode === 'advance_and_full' ? '#f0f4ff' : 'white' }};"
                           wire:click="$set('onlinePaymentMode', 'advance_and_full')">
                        <input type="radio" wire:model="onlinePaymentMode" value="advance_and_full" style="margin-top: 4px; accent-color: #635BFF;">
                        <div>
                            <p style="font-weight: 700; color: #111827; margin: 0; font-size: 15px;">Acompte + totalité en ligne</p>
                            <p style="color: #6b7280; font-size: 13px; margin: 4px 0 0 0;">Le client peut payer l'acompte OU la totalité par CB en ligne. <strong>Si le client paie la totalité</strong>, la commission ResaDZ (8% ou 6%) est prélevée automatiquement — vous recevez le reste directement.</p>
                        </div>
                    </label>

                    <label style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; border: 2px solid {{ $this->onlinePaymentMode === 'disabled' ? '#635BFF' : '#e5e7eb' }}; border-radius: 12px; cursor: pointer; background: {{ $this->onlinePaymentMode === 'disabled' ? '#f0f4ff' : 'white' }};"
                           wire:click="$set('onlinePaymentMode', 'disabled')">
                        <input type="radio" wire:model="onlinePaymentMode" value="disabled" style="margin-top: 4px; accent-color: #635BFF;">
                        <div>
                            <p style="font-weight: 700; color: #111827; margin: 0; font-size: 15px;">Désactivé</p>
                            <p style="color: #6b7280; font-size: 13px; margin: 4px 0 0 0;">Pas de paiement en ligne. Tout se règle en espèces à la remise du véhicule.</p>
                        </div>
                    </label>
                </div>

                <button wire:click="updatePaymentMode" style="margin-top: 16px; padding: 12px 24px; background: #635BFF; color: white; border: none; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer;">
                    Enregistrer mes préférences
                </button>
            </div>

            {{-- Warning calendrier --}}
            <div style="background: #FEF3C7; border: 2px solid #F59E0B; border-radius: 16px; padding: 24px;">
                <div class="flex items-start gap-3">
                    <span style="font-size: 28px; flex-shrink: 0;">⚠️</span>
                    <div>
                        <h3 style="font-weight: 700; color: #92400E; font-size: 16px; margin: 0 0 8px 0;">Évitez les doubles réservations</h3>
                        <p style="color: #92400E; font-size: 14px; margin: 0 0 8px 0;">
                            Quand le paiement en ligne est activé, <strong>les réservations sont automatiquement confirmées</strong> dès que le client paie. Il n'y a pas de validation manuelle.
                        </p>
                        <p style="color: #92400E; font-size: 14px; margin: 0 0 8px 0;">
                            Si vous louez aussi vos véhicules par d'autres moyens (votre propre site, bouche-à-oreille, autres plateformes), pensez à <strong>bloquer les dates sur ResaDZ immédiatement</strong> quand vous acceptez une réservation ailleurs.
                        </p>
                        <p style="color: #92400E; font-size: 14px; margin: 0 0 8px 0;">
                            Pour éviter tout conflit :
                        </p>
                        <ul style="color: #92400E; font-size: 14px; margin: 0; padding-left: 20px;">
                            <li style="margin-bottom: 4px;"><strong>Bloquez les dates</strong> dès qu'un véhicule est réservé (peu importe le canal)</li>
                            <li style="margin-bottom: 4px;"><strong>Mettez à jour le statut</strong> de vos véhicules (Disponible / Indisponible)</li>
                            <li style="margin-bottom: 4px;">Si un véhicule est en location, passez-le en <strong>"Réservé"</strong></li>
                            <li>Consultez votre <strong>Calendrier</strong> régulièrement</li>
                        </ul>
                        <p style="color: #92400E; font-size: 13px; margin: 12px 0 0 0; font-style: italic;">
                            ResaDZ bloque automatiquement les dates quand une réservation est confirmée sur la plateforme. Mais les réservations faites en dehors de ResaDZ doivent être bloquées manuellement.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Astuce --}}
            <div style="background: #EFF6FF; border: 1px solid #93C5FD; border-radius: 16px; padding: 20px;">
                <div class="flex items-start gap-3">
                    <span style="font-size: 22px; flex-shrink: 0;">💡</span>
                    <div>
                        <p style="font-weight: 600; color: #1E40AF; font-size: 14px; margin: 0 0 4px 0;">Astuce</p>
                        <p style="color: #1E40AF; font-size: 13px; margin: 0;">
                            Si vous ne souhaitez pas que les réservations soient automatiquement confirmées, choisissez le mode <strong>"Désactivé"</strong> ci-dessus. Les clients enverront une demande et vous pourrez la valider manuellement avant de confirmer.
                        </p>
                    </div>
                </div>
            </div>

        @elseif($hasAccount)
            {{-- Account created but onboarding incomplete --}}
            <div style="background: #fefce8; border: 2px solid #eab308; border-radius: 16px; padding: 24px;">
                <div class="flex items-center gap-3">
                    <div style="width: 48px; height: 48px; background: #eab308; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <div>
                        <p style="font-weight: 700; color: #854d0e; font-size: 18px; margin: 0;">Vérification en cours</p>
                        <p style="color: #92400e; margin: 4px 0 0 0;">Votre compte Stripe a été créé mais la vérification n'est pas terminée. Cliquez ci-dessous pour compléter.</p>
                    </div>
                </div>
                <a href="{{ route('stripe.onboard') }}" style="display: inline-flex; margin-top: 16px; align-items: center; gap: 8px; padding: 12px 24px; background: #635BFF; color: white; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px;">
                    Compléter la vérification
                </a>
            </div>

        @else
            {{-- Not connected --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 16px; padding: 32px;">
                <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 16px 0;">Pourquoi activer le paiement en ligne ?</h3>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <span style="font-size: 24px;">💳</span>
                        <div>
                            <p style="font-weight: 600; color: #111827; margin: 0;">Carte bancaire + PayPal</p>
                            <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 14px;">Vos clients peuvent payer par Visa, Mastercard, American Express ou PayPal. Idéal pour la diaspora en France/Europe.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span style="font-size: 24px;">⚡</span>
                        <div>
                            <p style="font-weight: 600; color: #111827; margin: 0;">Argent direct sur votre compte</p>
                            <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 14px;">L'argent arrive directement sur votre compte bancaire, pas sur celui de ResaDZ. Virements automatiques.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span style="font-size: 24px;">🔒</span>
                        <div>
                            <p style="font-weight: 600; color: #111827; margin: 0;">Paiement 100% sécurisé</p>
                            <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 14px;">Stripe est utilisé par des millions d'entreprises (Uber, Amazon, Airbnb). Vos données bancaires sont protégées.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span style="font-size: 24px;">📊</span>
                        <div>
                            <p style="font-weight: 600; color: #111827; margin: 0;">Commission automatique</p>
                            <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 14px;">Quand le client paie la totalité en ligne, la commission ResaDZ (8% ou 6%) est prélevée automatiquement. Pas d'acompte = pas de commission.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- How to connect --}}
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 16px; padding: 32px;">
                <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 16px 0;">Comment ça marche ?</h3>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div style="width: 36px; height: 36px; background: #635BFF; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0;">1</div>
                        <div>
                            <p style="font-weight: 600; color: #111827; margin: 0;">Cliquez sur "Connecter mon compte"</p>
                            <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 14px;">Vous serez redirigé vers Stripe (site sécurisé) pour créer votre compte.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div style="width: 36px; height: 36px; background: #635BFF; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0;">2</div>
                        <div>
                            <p style="font-weight: 600; color: #111827; margin: 0;">Remplissez vos informations</p>
                            <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 14px;">Stripe vous demandera :</p>
                            <ul style="color: #6b7280; font-size: 14px; margin: 8px 0 0 0; padding-left: 20px;">
                                <li>Votre <strong>email</strong></li>
                                <li>Votre <strong>numéro de téléphone</strong></li>
                                <li>Une <strong>pièce d'identité</strong> (CNI ou passeport)</li>
                                <li>Votre <strong>IBAN</strong> (compte bancaire EUR où recevoir l'argent)</li>
                                <li>Quelques infos sur votre activité (location de véhicules)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div style="width: 36px; height: 36px; background: #635BFF; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0;">3</div>
                        <div>
                            <p style="font-weight: 600; color: #111827; margin: 0;">C'est prêt !</p>
                            <p style="color: #6b7280; margin: 4px 0 0 0; font-size: 14px;">Une fois vérifié (quelques minutes à 24h), vos clients pourront payer en ligne. L'argent arrive sur votre compte bancaire sous 2-7 jours ouvrés.</p>
                        </div>
                    </div>
                </div>

                <div style="background: #f0f4ff; border: 1px solid #c7d2fe; border-radius: 12px; padding: 16px; margin-top: 24px;">
                    <p style="font-size: 14px; color: #4338ca; margin: 0;"><strong>Bon à savoir :</strong> Stripe prélève environ 1,5% + 0,25€ par transaction (frais Stripe standard). Ces frais sont en plus de la commission ResaDZ et sont à la charge du loueur.</p>
                </div>

                <div style="text-align: center; margin-top: 32px;">
                    <a href="{{ route('stripe.onboard') }}" style="display: inline-flex; align-items: center; gap: 10px; padding: 16px 40px; background: #635BFF; color: white; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(99, 91, 255, 0.4); transition: all 0.2s;">
                        <svg width="20" height="20" fill="white" viewBox="0 0 24 24"><path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/></svg>
                        Connecter mon compte Stripe
                    </a>
                    <p style="color: #9ca3af; font-size: 13px; margin-top: 12px;">Gratuit • Prend 5 minutes • 100% sécurisé</p>
                </div>
            </div>
        @endif

        {{-- FAQ --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 16px; padding: 32px;">
            <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 20px 0;">Questions fréquentes</h3>

            <div class="space-y-4" x-data="{ open: null }">
                <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                    <button type="button" x-on:click="open = open === 1 ? null : 1" class="w-full text-left px-4 py-3 flex justify-between items-center hover:bg-gray-50">
                        <span style="font-weight: 600; color: #111827; font-size: 14px;">Est-ce que ça coûte quelque chose ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition" :class="open === 1 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 1" x-collapse style="padding: 0 16px 16px; font-size: 14px; color: #6b7280;">
                        La création du compte est gratuite. Stripe prélève environ 1,5% + 0,25€ par transaction. Si un client paie 100€, vous recevez environ 98€ (après frais Stripe). La commission ResaDZ est prélevée uniquement sur le paiement total, pas sur les acomptes.
                    </div>
                </div>

                <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                    <button type="button" x-on:click="open = open === 2 ? null : 2" class="w-full text-left px-4 py-3 flex justify-between items-center hover:bg-gray-50">
                        <span style="font-weight: 600; color: #111827; font-size: 14px;">Quand est-ce que je reçois l'argent ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition" :class="open === 2 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 2" x-collapse style="padding: 0 16px 16px; font-size: 14px; color: #6b7280;">
                        L'argent arrive sur votre compte bancaire (IBAN) sous 2 à 7 jours ouvrés après le paiement. Stripe gère les virements automatiquement.
                    </div>
                </div>

                <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                    <button type="button" x-on:click="open = open === 3 ? null : 3" class="w-full text-left px-4 py-3 flex justify-between items-center hover:bg-gray-50">
                        <span style="font-weight: 600; color: #111827; font-size: 14px;">J'ai besoin de quoi pour m'inscrire ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition" :class="open === 3 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 3" x-collapse style="padding: 0 16px 16px; font-size: 14px; color: #6b7280;">
                        Un email, un numéro de téléphone, une pièce d'identité (CNI ou passeport) et un IBAN (compte bancaire en EUR). Si vous avez un compte Wise, Revolut ou PayPal avec IBAN, ça marche aussi.
                    </div>
                </div>

                <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                    <button type="button" x-on:click="open = open === 4 ? null : 4" class="w-full text-left px-4 py-3 flex justify-between items-center hover:bg-gray-50">
                        <span style="font-weight: 600; color: #111827; font-size: 14px;">Les clients peuvent toujours payer en espèces ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition" :class="open === 4 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 4" x-collapse style="padding: 0 16px 16px; font-size: 14px; color: #6b7280;">
                        Oui bien sûr ! Le paiement en ligne est une option supplémentaire. Les clients peuvent toujours choisir de payer en espèces à la remise du véhicule.
                    </div>
                </div>

                <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                    <button type="button" x-on:click="open = open === 5 ? null : 5" class="w-full text-left px-4 py-3 flex justify-between items-center hover:bg-gray-50">
                        <span style="font-weight: 600; color: #111827; font-size: 14px;">C'est quoi la commission sur les paiements en ligne ?</span>
                        <svg class="w-5 h-5 text-gray-400 transition" :class="open === 5 && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === 5" x-collapse style="padding: 0 16px 16px; font-size: 14px; color: #6b7280;">
                        <strong>Acompte :</strong> 0% commission ResaDZ — vous recevez 100% de l'acompte.<br>
                        <strong>Paiement total :</strong> la commission ResaDZ habituelle (8% pour 1-10 jours, 6% pour +10 jours) est prélevée automatiquement. Vous recevez le reste directement.<br>
                        <strong>Frais Stripe :</strong> environ 1,5% + 0,25€ par transaction (frais du prestataire de paiement, en plus de la commission ResaDZ).
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
