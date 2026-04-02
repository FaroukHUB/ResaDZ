<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajoutez votre premier véhicule</title>
</head>
@php
    $companyName = \App\Models\Setting::get('company_name', 'ResaDZ');
@endphp
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #F8FAFF; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">

        <!-- Header -->
        <div style="background: linear-gradient(135deg, #FF6B2C 0%, #F59E0B 100%); padding: 40px 30px; border-radius: 16px 16px 0 0; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px; font-weight: 700;">{{ $companyName }}</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 12px 0 0 0; font-size: 15px;">{{ $subjectLine }}</p>
        </div>

        <!-- Corps -->
        <div style="background: white; padding: 32px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">

            <p style="font-size: 18px; color: #1E293B; margin: 0 0 16px 0;">
                Salam <strong>{{ $userName }}</strong> 👋
            </p>

            <p style="font-size: 15px; color: #1E293B; margin: 0 0 16px 0;">
                {{ $bodyText }}
            </p>

            @if($step === 1)
            <div style="background: #FFF3ED; border: 1px solid #FF6B2C; border-radius: 12px; padding: 20px; margin: 20px 0;">
                <p style="font-size: 14px; color: #1E293B; margin: 0 0 8px 0; font-weight: bold;">En 5 minutes, c'est fait :</p>
                <p style="font-size: 14px; color: #475569; margin: 0;">1. Connectez-vous à votre espace<br>2. Cliquez sur "Ajouter un véhicule"<br>3. Remplissez les infos + une photo<br>4. Votre véhicule est en ligne !</p>
            </div>
            @endif

            @if($step === 2)
            <div style="background: #EEF2FF; border: 1px solid #6366F1; border-radius: 12px; padding: 20px; margin: 20px 0;">
                <p style="font-size: 14px; color: #3730A3; margin: 0;">
                    <strong>Besoin d'aide ?</strong> Répondez directement à cet email ou utilisez le chatbot sur le site — on vous guide pas à pas.
                </p>
            </div>
            @endif

            @if($step === 3)
            <div style="background: #DCFCE7; border: 1px solid #10B981; border-radius: 12px; padding: 20px; margin: 20px 0;">
                <p style="font-size: 14px; color: #065F46; margin: 0;">
                    <strong>On est là pour vous.</strong> Si vous avez des questions ou besoin d'aide pour configurer votre espace, n'hésitez pas à nous contacter. Votre espace loueur est prêt et vous attend.
                </p>
            </div>
            @endif

            <!-- Bouton CTA -->
            <div style="text-align: center; margin: 28px 0;">
                <a href="{{ url('/loueur') }}"
                   style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #FF6B2C 0%, #F59E0B 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(255, 107, 44, 0.4);">
                    Accéder à mon espace loueur →
                </a>
            </div>

            <p style="font-size: 14px; color: #64748B; text-align: center;">
                L'équipe {{ $companyName }}
            </p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 20px; padding: 20px;">
            <p style="color: #94A3B8; font-size: 12px; margin: 0;">
                &copy; {{ date('Y') }} {{ $companyName }}. Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>
