<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rappel : Votre location démarre demain</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
</head>
@php
    $companyName = \App\Models\Setting::get('company_name', 'ResaDZ');
    $companySlogan = \App\Models\Setting::get('company_slogan', 'Location de véhicules en Algérie');
@endphp
<body style="margin: 0; padding: 0; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 30px; border-radius: 16px 16px 0 0; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px;">{{ $companyName }}</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 14px;">Rappel de réservation</p>
        </div>

        <!-- Content -->
        <div style="background: white; padding: 30px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

            <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">
                Bonjour <strong>{{ $booking->client_name }}</strong>,
            </p>

            <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">
                Nous vous rappelons que votre location commence <strong>demain</strong>.
            </p>

            <!-- Booking Summary -->
            <div style="background: #eff6ff; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                <h2 style="color: #1e40af; margin: 0 0 15px 0; font-size: 18px;">Détails de la réservation</h2>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #1e40af; font-weight: 600;">Référence</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ $booking->reference }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #1e40af; font-weight: 600;">Véhicule</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ $vehicle->full_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #1e40af; font-weight: 600;">Date & Heure</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">
                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }} à {{ $booking->pickup_time }}
                        </td>
                    </tr>
                    @if($booking->pickup_address)
                    <tr>
                        <td style="padding: 8px 0; color: #1e40af; font-weight: 600;">Lieu</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ $booking->pickup_address }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 8px 0; color: #1e40af; font-weight: 600;">Loueur</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ $loueur->company_name ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>

            <!-- Checklist -->
            <div style="background: #fefce8; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                <h2 style="color: #854d0e; margin: 0 0 15px 0; font-size: 18px;">N'oubliez pas :</h2>
                <ul style="margin: 0; padding-left: 20px; color: #374151;">
                    <li style="margin-bottom: 8px;">Votre <strong>permis de conduire</strong> valide</li>
                    <li style="margin-bottom: 8px;">Votre <strong>pièce d'identité</strong></li>
                    @if($booking->deposit_amount > 0)
                    <li style="margin-bottom: 8px;">La <strong>caution</strong> de {{ number_format($booking->deposit_amount, 0, ',', ' ') }} {{ $booking->deposit_currency === 'EUR' ? '€' : 'DA' }}</li>
                    @endif
                    @if($booking->amount_remaining > 0)
                    <li style="margin-bottom: 8px;">Le <strong>solde à payer</strong> de {{ number_format($booking->amount_remaining, 0, ',', ' ') }} {{ $booking->currency === 'EUR' ? '€' : 'DA' }}</li>
                    @endif
                </ul>
            </div>

            <!-- Contact Info -->
            @if($loueur && ($loueur->phone || $loueur->whatsapp))
            <div style="background: #f3f4f6; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
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

            <!-- CTA Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/ma-reservation/' . $booking->confirmation_token) }}"
                   style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);">
                    Voir ma réservation
                </a>
            </div>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 25px 0;">

            <p style="font-size: 12px; color: #9ca3af; text-align: center; margin: 0;">
                Cet email a été envoyé par {{ $companyName }} pour vous rappeler votre réservation.<br>
                À demain !
            </p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; padding: 20px; color: #9ca3af; font-size: 12px;">
            <p>&copy; {{ date('Y') }} {{ $companyName }} - {{ $companySlogan }}</p>
        </div>
    </div>
</body>
</html>
