<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur ResaDZ</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #F8FAFF; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">

        <!-- Header (dégradé indigo) -->
        <div style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); padding: 40px 30px; border-radius: 16px 16px 0 0; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 32px; font-weight: 700; letter-spacing: 1px;">{{ $companyName }}</h1>
            <p style="color: rgba(255,255,255,0.95); margin: 12px 0 0 0; font-size: 16px; font-weight: 400;">Votre espace chauffeur est prêt 🧑‍✈️</p>
        </div>

        <!-- Corps -->
        <div style="background: white; padding: 32px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">

            <p style="font-size: 18px; color: #1E293B; margin: 0 0 16px 0;">
                Salam <strong>{{ $user->name ?? 'chauffeur' }}</strong> 👋
            </p>

            <p style="font-size: 15px; color: #1E293B; margin: 0 0 12px 0;">
                Bienvenue sur <strong>ResaDZ</strong>, la première plateforme algérienne de mise en relation entre chauffeurs et clients.
            </p>

            <p style="font-size: 15px; color: #1E293B; margin: 0 0 28px 0;">
                Votre compte chauffeur a bien été créé. Voici comment commencer à recevoir vos premières courses.
            </p>

            <!-- Section Étapes -->
            <h2 style="font-size: 17px; color: #1E293B; margin: 0 0 20px 0;">🚀 Les 3 étapes pour recevoir vos premières demandes</h2>

            <!-- Étape 1 -->
            <div style="border-left: 3px solid #6366F1; padding: 16px 20px; margin-bottom: 16px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <div style="display: inline-block; width: 28px; height: 28px; background: #6366F1; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-size: 14px; font-weight: 700; margin-bottom: 8px;">1</div>
                <strong style="color: #1E293B; font-size: 15px; margin-left: 8px;">Complétez votre profil</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Un profil complet rassure les clients avant de réserver. Décrivez votre expérience, votre style de conduite et ajoutez une belle photo de votre véhicule.
                </p>
            </div>

            <!-- Étape 2 -->
            <div style="border-left: 3px solid #6366F1; padding: 16px 20px; margin-bottom: 16px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <div style="display: inline-block; width: 28px; height: 28px; background: #6366F1; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-size: 14px; font-weight: 700; margin-bottom: 8px;">2</div>
                <strong style="color: #1E293B; font-size: 15px; margin-left: 8px;">Configurez vos services</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Indiquez les types de services que vous proposez :
                </p>
                <ul style="font-size: 14px; color: #475569; margin: 8px 0 0 0; padding-left: 20px; list-style: none;">
                    <li style="margin-bottom: 4px;">✈️ Transferts aéroport/gare</li>
                    <li style="margin-bottom: 4px;">🏙️ Courses en ville</li>
                    <li>📦 Livraisons</li>
                </ul>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Plus vous proposez de services, plus vous recevez de demandes.
                </p>
            </div>

            <!-- Étape 3 -->
            <div style="border-left: 3px solid #6366F1; padding: 16px 20px; margin-bottom: 28px; background: #FAFAFA; border-radius: 0 8px 8px 0;">
                <div style="display: inline-block; width: 28px; height: 28px; background: #6366F1; color: white; border-radius: 50%; text-align: center; line-height: 28px; font-size: 14px; font-weight: 700; margin-bottom: 8px;">3</div>
                <strong style="color: #1E293B; font-size: 15px; margin-left: 8px;">Définissez vos disponibilités</strong>
                <p style="font-size: 14px; color: #475569; margin: 8px 0 0 0;">
                    Configurez vos jours et créneaux horaires disponibles.
                </p>
                <p style="font-size: 14px; color: #475569; margin: 6px 0 0 0;">
                    Les chauffeurs disponibles en soirée et le weekend reçoivent 40% de demandes en plus 🌙
                </p>
            </div>

            <!-- Section Commission -->
            <div style="background: #FFF3ED; border: 1px solid #6366F1; border-radius: 12px; padding: 24px; margin-bottom: 28px;">
                <h2 style="font-size: 17px; color: #1E293B; margin: 0 0 12px 0;">💰 Comment fonctionne la commission ResaDZ</h2>
                <p style="font-size: 14px; color: #1E293B; margin: 0 0 12px 0;">
                    Vous ne payez rien tant que vous n'avez pas de course.
                </p>
                <p style="font-size: 14px; color: #1E293B; margin: 0 0 8px 0;">
                    Quand une course est confirmée :
                </p>
                <ul style="font-size: 14px; color: #1E293B; margin: 0 0 12px 0; padding-left: 20px;">
                    <li><strong style="color: #6366F1;">10%</strong> du montant de la course</li>
                </ul>
                <p style="font-size: 14px; color: #1E293B; margin: 0;">
                    Les clients vous paient directement. ResaDZ vous envoie une facture hebdomadaire récapitulant les commissions dues.
                </p>
            </div>

            <!-- Section Trajets -->
            <div style="background: #F0FDF4; border: 1px solid #22C55E; border-radius: 12px; padding: 24px; margin-bottom: 28px;">
                <h2 style="font-size: 17px; color: #1E293B; margin: 0 0 12px 0;">🗺️ Ajoutez vos trajets réguliers</h2>
                <p style="font-size: 14px; color: #1E293B; margin: 0 0 8px 0;">
                    Si vous faites des transferts, ajoutez vos trajets habituels :
                </p>
                <ul style="font-size: 14px; color: #1E293B; margin: 0 0 12px 0; padding-left: 20px;">
                    <li style="margin-bottom: 4px;">Alger centre → Aéroport</li>
                    <li style="margin-bottom: 4px;">Alger → Oran</li>
                    <li>Etc.</li>
                </ul>
                <p style="font-size: 14px; color: #1E293B; margin: 0;">
                    Vos trajets apparaissent dans les recherches des clients — plus vous en ajoutez, plus vous êtes visible.
                </p>
            </div>

            <!-- Section PWA -->
            <div style="background: #EEF2FF; border-radius: 12px; padding: 24px; margin-bottom: 28px;">
                <h2 style="font-size: 17px; color: #1E293B; margin: 0 0 12px 0;">📱 Installez ResaDZ sur votre téléphone</h2>
                <p style="font-size: 14px; color: #1E293B; margin: 0 0 12px 0;">
                    Ne ratez aucune demande de course — installez ResaDZ comme une vraie application et recevez les notifications en temps réel.
                </p>
                <p style="font-size: 14px; color: #1E293B; margin: 0 0 6px 0;">
                    <strong>Sur Android :</strong> Menu ⋮ → "Ajouter à l'écran d'accueil"
                </p>
                <p style="font-size: 14px; color: #1E293B; margin: 0;">
                    <strong>Sur iPhone :</strong> Bouton Partager → "Sur l'écran d'accueil"
                </p>
            </div>

            <!-- Section Notifications -->
            <div style="margin-bottom: 28px;">
                <h2 style="font-size: 17px; color: #1E293B; margin: 0 0 12px 0;">🔔 Vous serez notifié immédiatement pour :</h2>
                <ul style="font-size: 14px; color: #1E293B; margin: 0; padding-left: 20px; list-style: none;">
                    <li style="margin-bottom: 8px;">📩 Chaque nouvelle demande de course</li>
                    <li style="margin-bottom: 8px;">💬 Les messages de vos clients</li>
                    <li>🧾 Vos factures hebdomadaires ResaDZ</li>
                </ul>
            </div>

            <!-- Bouton CTA (dégradé indigo) -->
            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ url('/loueur') }}"
                   style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);">
                    Accéder à mon espace chauffeur →
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
