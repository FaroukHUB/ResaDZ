<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription reçue - ResaDZ</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #F8FAFF; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">

        <!-- Header -->
        <div style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); padding: 40px 30px; border-radius: 16px 16px 0 0; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 32px; font-weight: 700; letter-spacing: 1px;">{{ $companyName }}</h1>
            <p style="color: rgba(255,255,255,0.95); margin: 12px 0 0 0; font-size: 16px; font-weight: 400;">Inscription reçue ✅</p>
        </div>

        <!-- Corps -->
        <div style="background: white; padding: 32px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">

            <p style="font-size: 18px; color: #1E293B; margin: 0 0 16px 0;">
                Salam <strong>{{ $user->name ?? 'chauffeur' }}</strong> 👋
            </p>

            <p style="font-size: 15px; color: #1E293B; margin: 0 0 12px 0;">
                Merci de votre inscription sur <strong>ResaDZ</strong>, la première plateforme algérienne de mise en relation entre chauffeurs et clients.
            </p>

            <!-- Statut -->
            <div style="background: #FEF3C7; border: 1px solid #F59E0B; border-radius: 12px; padding: 24px; margin: 24px 0; text-align: center;">
                <p style="font-size: 28px; margin: 0 0 8px 0;">⏳</p>
                <h2 style="font-size: 18px; color: #92400E; margin: 0 0 8px 0;">Votre compte est en cours de vérification</h2>
                <p style="font-size: 14px; color: #92400E; margin: 0;">
                    Notre équipe vérifie chaque inscription pour garantir la qualité de la plateforme.
                    Vous recevrez un email dès que votre compte sera activé.
                </p>
            </div>

            <p style="font-size: 15px; color: #1E293B; margin: 0 0 12px 0;">
                <strong>Délai habituel :</strong> moins de 24h (souvent beaucoup plus rapide).
            </p>

            <p style="font-size: 15px; color: #1E293B; margin: 0 0 28px 0;">
                En attendant, voici ce qui vous attend une fois votre compte activé :
            </p>

            <!-- Ce qui vous attend -->
            <div style="border-left: 3px solid #6366F1; padding: 16px 20px; margin-bottom: 16px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <strong style="color: #1E293B; font-size: 15px;">🧑‍✈️ Proposez vos services</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Transferts aéroport, courses en ville, livraisons — configurez vos services.
                </p>
            </div>

            <div style="border-left: 3px solid #6366F1; padding: 16px 20px; margin-bottom: 16px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <strong style="color: #1E293B; font-size: 15px;">📩 Recevez des demandes de course</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Les clients réservent directement en ligne. Vous êtes notifié en temps réel.
                </p>
            </div>

            <div style="border-left: 3px solid #6366F1; padding: 16px 20px; margin-bottom: 28px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <strong style="color: #1E293B; font-size: 15px;">💰 Commission simple : 10%</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Vous ne payez rien sans course. Les clients vous paient directement.
                </p>
            </div>

            <!-- Section Support -->
            <div style="text-align: center; padding-top: 20px; border-top: 1px solid #E2E8F0;">
                <p style="font-size: 15px; color: #1E293B; margin: 0 0 8px 0;">
                    <strong>Une question ? On est là.</strong>
                </p>
                <p style="font-size: 14px; color: #475569; margin: 0 0 12px 0;">
                    Répondez directement à cet email ou contactez-nous sur WhatsApp — on répond 7j/7.
                </p>
                <p style="font-size: 16px; color: #1E293B; margin: 0;">
                    À très vite sur ResaDZ 🇩🇿
                </p>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    L'équipe ResaDZ
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div style="background: #1E293B; padding: 30px; border-radius: 16px; margin-top: 20px; text-align: center;">
            <h2 style="color: white; margin: 0 0 12px 0; font-size: 22px; font-weight: 700;">{{ $companyName }}</h2>
            <p style="margin: 0 0 16px 0;">
                <a href="mailto:contact@resadz.com" style="color: #94A3B8; text-decoration: none; font-size: 13px;">contact@resadz.com</a>
            </p>
            @if($whatsappNumber)
            <p style="margin: 0 0 8px 0;">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}" style="color: #94A3B8; text-decoration: none; font-size: 13px;">WhatsApp : {{ $whatsappNumber }}</a>
            </p>
            @endif
            <p style="margin: 0 0 16px 0;">
                <a href="{{ url('/') }}" style="color: #94A3B8; text-decoration: none; font-size: 13px;">resadz.com</a>
            </p>
            <p style="color: #64748B; font-size: 11px; margin: 0;">
                © {{ date('Y') }} {{ $companyName }}. Tous droits réservés.
            </p>
        </div>

    </div>
</body>
</html>
