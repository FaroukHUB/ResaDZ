<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur ResaDZ</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #F8FAFF; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">

        <!-- Header -->
        <div style="background: linear-gradient(135deg, #FF6B2C 0%, #F59E0B 100%); padding: 40px 30px; border-radius: 16px 16px 0 0; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 32px; font-weight: 700; letter-spacing: 1px;">{{ $companyName }}</h1>
            <p style="color: rgba(255,255,255,0.95); margin: 12px 0 0 0; font-size: 16px; font-weight: 400;">Votre espace loueur est prêt !</p>
        </div>

        <!-- Corps -->
        <div style="background: white; padding: 32px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">

            <p style="font-size: 18px; color: #1E293B; margin: 0 0 16px 0;">
                Salam <strong>{{ $user->name ?? 'loueur' }}</strong> !
            </p>

            <p style="font-size: 15px; color: #1E293B; margin: 0 0 12px 0;">
                Bienvenue sur <strong>ResaDZ</strong>, la première plateforme algérienne de location de voiture auprès de loueurs vérifiés.
            </p>

            <p style="font-size: 15px; color: #1E293B; margin: 0 0 28px 0;">
                Votre compte loueur a bien été créé. Voici comment démarrer en quelques minutes.
            </p>

            <!-- Section Étapes -->
            <h2 style="font-size: 17px; color: #1E293B; margin: 0 0 20px 0;">Les 3 étapes pour recevoir vos premières réservations</h2>

            <div style="border-left: 3px solid #FF6B2C; padding: 16px 20px; margin-bottom: 16px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <div style="display: inline-block; width: 28px; height: 28px; background: #FF6B2C; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-size: 14px; font-weight: 700; margin-bottom: 8px;">1</div>
                <strong style="color: #1E293B; font-size: 15px; margin-left: 8px;">Complétez votre profil</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Un profil complet inspire confiance et attire 3x plus de clients. Ajoutez votre photo de couverture, votre description et vos coordonnées.
                </p>
            </div>

            <div style="border-left: 3px solid #FF6B2C; padding: 16px 20px; margin-bottom: 16px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <div style="display: inline-block; width: 28px; height: 28px; background: #FF6B2C; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-size: 14px; font-weight: 700; margin-bottom: 8px;">2</div>
                <strong style="color: #1E293B; font-size: 15px; margin-left: 8px;">Ajoutez votre premier véhicule</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Publiez votre véhicule gratuitement en 5 minutes. Si un visuel studio ResaDZ existe pour votre modèle, il sera utilisé automatiquement.
                </p>
            </div>

            <div style="border-left: 3px solid #FF6B2C; padding: 16px 20px; margin-bottom: 28px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <div style="display: inline-block; width: 28px; height: 28px; background: #FF6B2C; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-size: 14px; font-weight: 700; margin-bottom: 8px;">3</div>
                <strong style="color: #1E293B; font-size: 15px; margin-left: 8px;">Configurez vos disponibilités</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Bloquez les dates où votre véhicule n'est pas disponible pour éviter les conflits de réservation.
                </p>
            </div>

            <!-- Section Commission -->
            <div style="background: #FFF3ED; border: 1px solid #FF6B2C; border-radius: 12px; padding: 24px; margin-bottom: 28px;">
                <h2 style="font-size: 17px; color: #1E293B; margin: 0 0 12px 0;">Comment fonctionne la commission ResaDZ</h2>
                <p style="font-size: 14px; color: #1E293B; margin: 0 0 8px 0;">
                    Vous ne payez rien tant que vous ne louez pas.
                </p>
                <ul style="font-size: 14px; color: #1E293B; margin: 0 0 12px 0; padding-left: 20px;">
                    <li style="margin-bottom: 6px;">Location 1 à 10 jours : <strong style="color: #FF6B2C;">8%</strong> du montant total</li>
                    <li>Location plus de 10 jours : <strong style="color: #FF6B2C;">6%</strong> du montant total</li>
                </ul>
            </div>

            <!-- Bouton CTA -->
            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ url('/loueur') }}"
                   style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #FF6B2C 0%, #F59E0B 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(255, 107, 44, 0.4);">
                    Accéder à mon espace loueur
                </a>
            </div>

            <!-- Section Support -->
            <div style="text-align: center; padding-top: 20px; border-top: 1px solid #E2E8F0;">
                <p style="font-size: 15px; color: #1E293B; margin: 0 0 8px 0;">
                    <strong>Une question ? On est là.</strong>
                </p>
                <p style="font-size: 14px; color: #475569; margin: 0 0 12px 0;">
                    Répondez directement à cet email ou contactez-nous sur WhatsApp — on répond 7j/7.
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
