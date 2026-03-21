<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation confirmée</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
</head>
@php
    $companyName = \App\Models\Setting::get('company_name', 'ResaDZ');
    $companySlogan = \App\Models\Setting::get('company_slogan', 'Location de véhicules en Algérie');
@endphp
<body style="margin: 0; padding: 0; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 30px; border-radius: 16px 16px 0 0; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px;">{{ $companyName }}</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 14px;">Votre réservation est confirmée</p>
        </div>

        <!-- Content -->
        <div style="background: white; padding: 30px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

            <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">
                Bonjour <strong>{{ $booking->client_name }}</strong>,
            </p>

            <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">
                Bonne nouvelle ! Votre demande de réservation a été acceptée par <strong>{{ $loueur->company_name ?? 'le loueur' }}</strong>.
            </p>

            <!-- Booking Summary -->
            <div style="background: #fef3c7; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                <h2 style="color: #92400e; margin: 0 0 15px 0; font-size: 18px;">Récapitulatif</h2>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #78350f; font-weight: 600;">Référence</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ $booking->reference }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #78350f; font-weight: 600;">Véhicule</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ $vehicle->full_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #78350f; font-weight: 600;">Prise en charge</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }} à {{ $booking->pickup_time }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #78350f; font-weight: 600;">Retour</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }} avant {{ $booking->return_time }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #78350f; font-weight: 600;">Durée</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ $booking->total_days }} jour(s)</td>
                    </tr>
                    <tr style="border-top: 2px solid #fbbf24;">
                        <td style="padding: 12px 0 0 0; color: #78350f; font-weight: 700; font-size: 18px;">Total</td>
                        <td style="padding: 12px 0 0 0; color: #d97706; text-align: right; font-weight: 700; font-size: 18px;">{{ $booking->getFormattedTotal() }}</td>
                    </tr>
                </table>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $confirmationUrl }}"
                   style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);">
                    Accéder à ma réservation
                </a>
            </div>

            <p style="font-size: 14px; color: #6b7280; text-align: center; margin-bottom: 25px;">
                Sur cette page, vous pourrez :
            </p>

            <ul style="font-size: 14px; color: #374151; margin-bottom: 25px; padding-left: 20px;">
                <li style="margin-bottom: 8px;">Consulter le détail de votre réservation</li>
                <li style="margin-bottom: 8px;">Télécharger votre contrat de location</li>
                <li style="margin-bottom: 8px;">Envoyer vos documents (permis, carte d'identité)</li>
                <li style="margin-bottom: 8px;">Contacter le loueur si besoin</li>
            </ul>

            <!-- Contact Info -->
            @if($loueur)
            <div style="background: #f3f4f6; border-radius: 12px; padding: 20px; margin-top: 25px;">
                <h3 style="color: #374151; margin: 0 0 10px 0; font-size: 14px;">Contact loueur</h3>
                <p style="color: #6b7280; margin: 0; font-size: 14px;">
                    <strong>{{ $loueur->company_name }}</strong><br>
                    @if($loueur->phone)
                        Tél : {{ $loueur->phone }}<br>
                    @endif
                    @if($loueur->whatsapp)
                        WhatsApp : {{ $loueur->whatsapp }}
                    @endif
                </p>
            </div>
            @endif

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 25px 0;">

            <p style="font-size: 12px; color: #9ca3af; text-align: center; margin: 0;">
                Cet email a été envoyé par {{ $companyName }} suite à la confirmation de votre réservation.<br>
                Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email.
            </p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; padding: 20px; color: #9ca3af; font-size: 12px;">
            <p>&copy; {{ date('Y') }} {{ $companyName }} - {{ $companySlogan }}</p>
        </div>
    </div>
</body>
</html>
