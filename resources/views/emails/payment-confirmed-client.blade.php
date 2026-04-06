<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement confirmé - ResaDZ</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #F8FAFF; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">

        <div style="background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%); padding: 40px 30px; border-radius: 16px 16px 0 0; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 32px; font-weight: 700;">{{ $companyName }}</h1>
            <p style="color: rgba(255,255,255,0.95); margin: 12px 0 0 0; font-size: 16px;">Paiement confirmé !</p>
        </div>

        <div style="background: white; padding: 32px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">

            <p style="font-size: 18px; color: #1E293B; margin: 0 0 16px 0;">
                Salam <strong>{{ $clientName }}</strong>,
            </p>

            <div style="background: #F0FDF4; border: 1px solid #22C55E; border-radius: 12px; padding: 20px; margin: 0 0 24px 0; text-align: center;">
                <p style="font-size: 28px; margin: 0 0 8px 0;">✅</p>
                <h2 style="font-size: 18px; color: #166534; margin: 0 0 4px 0;">{{ $paymentLabel }} reçu</h2>
                <p style="font-size: 24px; font-weight: 800; color: #166534; margin: 0;">{{ $amount }}</p>
            </div>

            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px; margin: 0 0 24px 0;">
                <h3 style="font-size: 16px; color: #1E293B; margin: 0 0 12px 0;">Détails de la réservation</h3>
                <table style="width: 100%; font-size: 14px; color: #475569;">
                    <tr><td style="padding: 4px 0;"><strong>Référence</strong></td><td style="text-align: right;">{{ $reference }}</td></tr>
                    <tr><td style="padding: 4px 0;"><strong>Véhicule</strong></td><td style="text-align: right;">{{ $vehicleName }}</td></tr>
                    <tr><td style="padding: 4px 0;"><strong>Dates</strong></td><td style="text-align: right;">{{ $dates }}</td></tr>
                    <tr><td style="padding: 4px 0;"><strong>Total</strong></td><td style="text-align: right;">{{ $totalPrice }}</td></tr>
                    <tr><td style="padding: 4px 0;"><strong>Loueur</strong></td><td style="text-align: right;">{{ $loueurName }}</td></tr>
                </table>
            </div>

            @if($isAdvance)
            <p style="font-size: 14px; color: #475569; margin: 0 0 16px 0;">
                Le reste du montant ({{ $remainingAmount }}) sera à régler directement auprès du loueur à la remise du véhicule.
            </p>
            @endif

            <p style="font-size: 14px; color: #475569; margin: 0 0 24px 0;">
                Le loueur a été notifié de votre paiement. Il vous contactera pour organiser la remise du véhicule.
            </p>

            <div style="text-align: center; padding-top: 20px; border-top: 1px solid #E2E8F0;">
                <p style="font-size: 14px; color: #475569; margin: 0;">
                    L'équipe {{ $companyName }}
                </p>
            </div>
        </div>

        <div style="background: #1E293B; padding: 24px; border-radius: 16px; margin-top: 20px; text-align: center;">
            <p style="margin: 0;"><a href="{{ url('/') }}" style="color: #94A3B8; text-decoration: none; font-size: 13px;">resadz.com</a></p>
            <p style="color: #64748B; font-size: 11px; margin: 8px 0 0 0;">© {{ date('Y') }} {{ $companyName }}</p>
        </div>
    </div>
</body>
</html>
