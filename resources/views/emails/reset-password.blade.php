<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
</head>
@php
    $companyName = \App\Models\Setting::get('company_name', 'ResaDZ');
@endphp
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 30px; border-radius: 16px 16px 0 0; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px;">{{ $companyName }}</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 14px;">Réinitialisation de votre mot de passe</p>
        </div>

        <!-- Content -->
        <div style="background: white; padding: 30px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">
                Bonjour <strong>{{ $userName }}</strong>,
            </p>

            <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">
                Vous avez demandé la réinitialisation de votre mot de passe sur {{ $companyName }}. Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
            </p>

            <!-- CTA Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/mot-de-passe/reset/' . $token . '?email=' . urlencode($email)) }}"
                   style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);">
                    Réinitialiser mon mot de passe
                </a>
            </div>

            <div style="background: #FEF3C7; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                <p style="font-size: 14px; color: #92400E; margin: 0;">
                    <strong>Ce lien expire dans 60 minutes.</strong> Si vous n'avez pas demandé cette réinitialisation, ignorez simplement cet email — votre mot de passe ne sera pas modifié.
                </p>
            </div>

            <p style="font-size: 13px; color: #9CA3AF; margin-top: 20px;">
                Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
                <span style="color: #6B7280; word-break: break-all;">{{ url('/mot-de-passe/reset/' . $token . '?email=' . urlencode($email)) }}</span>
            </p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 20px; padding: 20px;">
            <p style="color: #9CA3AF; font-size: 12px; margin: 0;">
                &copy; {{ date('Y') }} {{ $companyName }}. Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>
