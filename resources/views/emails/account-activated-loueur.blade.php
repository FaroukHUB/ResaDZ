<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte activé - ResaDZ</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #F8FAFF; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">

        <!-- Header -->
        <div style="background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%); padding: 40px 30px; border-radius: 16px 16px 0 0; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 32px; font-weight: 700; letter-spacing: 1px;">{{ $companyName }}</h1>
            <p style="color: rgba(255,255,255,0.95); margin: 12px 0 0 0; font-size: 16px; font-weight: 400;">Votre compte est activé ! 🎉</p>
        </div>

        <!-- Corps -->
        <div style="background: white; padding: 32px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">

            <p style="font-size: 18px; color: #1E293B; margin: 0 0 16px 0;">
                Salam <strong>{{ $loueur->company_name ?? 'loueur' }}</strong> 👋
            </p>

            <!-- Statut -->
            <div style="background: #F0FDF4; border: 1px solid #22C55E; border-radius: 12px; padding: 24px; margin: 0 0 24px 0; text-align: center;">
                <p style="font-size: 28px; margin: 0 0 8px 0;">✅</p>
                <h2 style="font-size: 18px; color: #166534; margin: 0 0 8px 0;">Votre compte a été vérifié et activé !</h2>
                <p style="font-size: 14px; color: #166534; margin: 0;">
                    Vous pouvez maintenant accéder à votre espace et commencer à publier vos véhicules.
                </p>
            </div>

            <!-- Étapes -->
            <h2 style="font-size: 17px; color: #1E293B; margin: 0 0 20px 0;">🚀 Pour bien démarrer</h2>

            <div style="border-left: 3px solid #22C55E; padding: 16px 20px; margin-bottom: 16px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <div style="display: inline-block; width: 28px; height: 28px; background: #22C55E; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-size: 14px; font-weight: 700; margin-bottom: 8px;">1</div>
                <strong style="color: #1E293B; font-size: 15px; margin-left: 8px;">Complétez votre profil</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Photo, description, coordonnées — un profil complet attire 3x plus de clients.
                </p>
            </div>

            <div style="border-left: 3px solid #22C55E; padding: 16px 20px; margin-bottom: 16px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <div style="display: inline-block; width: 28px; height: 28px; background: #22C55E; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-size: 14px; font-weight: 700; margin-bottom: 8px;">2</div>
                <strong style="color: #1E293B; font-size: 15px; margin-left: 8px;">Ajoutez votre premier véhicule</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Publiez gratuitement en 5 minutes. Photo sur fond blanc ou noir recommandée.
                </p>
            </div>

            <div style="border-left: 3px solid #22C55E; padding: 16px 20px; margin-bottom: 28px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <div style="display: inline-block; width: 28px; height: 28px; background: #22C55E; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-size: 14px; font-weight: 700; margin-bottom: 8px;">3</div>
                <strong style="color: #1E293B; font-size: 15px; margin-left: 8px;">Configurez vos disponibilités</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Bloquez les dates indisponibles pour éviter les conflits de réservation.
                </p>
            </div>

            <!-- Bouton CTA -->
            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ url('/loueur') }}"
                   style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.4);">
                    Accéder à mon espace loueur →
                </a>
            </div>

            <!-- Section Support -->
            <div style="text-align: center; padding-top: 20px; border-top: 1px solid #E2E8F0;">
                <p style="font-size: 15px; color: #1E293B; margin: 0 0 8px 0;">
                    <strong>Besoin d'aide ? On est là.</strong>
                </p>
                <p style="font-size: 14px; color: #475569; margin: 0 0 12px 0;">
                    Répondez à cet email ou contactez-nous sur WhatsApp — on répond 7j/7.
                </p>
                <p style="font-size: 16px; color: #1E293B; margin: 0;">
                    Bienvenue dans la famille ResaDZ 🇩🇿
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
