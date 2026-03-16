<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation annulée</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
</head>
@php
    $companyName = \App\Models\Setting::get('company_name', 'ResaDZ');
    $companySlogan = \App\Models\Setting::get('company_slogan', 'Location de véhicules en Algérie');
    $isClient = $recipientType === 'client';
@endphp
<body style="margin: 0; padding: 0; font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 30px; border-radius: 16px 16px 0 0; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px;">{{ $companyName }}</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 14px;">Réservation annulée</p>
        </div>

        <!-- Content -->
        <div style="background: white; padding: 30px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

            <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">
                @if($isClient)
                    Bonjour <strong>{{ $booking->client_name }}</strong>,
                @else
                    Bonjour,
                @endif
            </p>

            <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">
                @if($isClient)
                    Nous vous informons que votre réservation <strong>#{{ $booking->reference }}</strong> a été annulée.
                @else
                    La réservation <strong>#{{ $booking->reference }}</strong> a été annulée par le client.
                @endif
            </p>

            <!-- Booking Summary -->
            <div style="background: #fef2f2; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                <h2 style="color: #991b1b; margin: 0 0 15px 0; font-size: 18px;">Détails de la réservation</h2>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #991b1b; font-weight: 600;">Référence</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ $booking->reference }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #991b1b; font-weight: 600;">Véhicule</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ $vehicle->full_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #991b1b; font-weight: 600;">Dates prévues</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">
                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}
                        </td>
                    </tr>
                    @if($booking->cancellation_reason)
                    <tr>
                        <td style="padding: 8px 0; color: #991b1b; font-weight: 600;">Raison</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ $booking->cancellation_reason }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            @if($isClient && $booking->refund_amount > 0)
            <!-- Refund Info -->
            <div style="background: #ecfdf5; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                <h2 style="color: #065f46; margin: 0 0 15px 0; font-size: 18px;">Remboursement</h2>
                <p style="color: #374151; margin: 0;">
                    Un remboursement de <strong>{{ number_format($booking->refund_amount, 0, ',', ' ') }} {{ $booking->currency === 'EUR' ? '€' : 'DA' }}</strong> sera effectué selon la méthode de paiement initiale.
                </p>
            </div>
            @endif

            <!-- CTA Button -->
            <div style="text-align: center; margin: 30px 0;">
                @if($isClient)
                    <a href="{{ url('/vehicules') }}"
                       style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.4);">
                        Rechercher un autre véhicule
                    </a>
                @else
                    <a href="{{ url('/loueur/bookings/' . $booking->id) }}"
                       style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);">
                        Voir les détails
                    </a>
                @endif
            </div>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 25px 0;">

            <p style="font-size: 12px; color: #9ca3af; text-align: center; margin: 0;">
                Cet email a été envoyé par {{ $companyName }} suite à l'annulation d'une réservation.<br>
                Si vous avez des questions, n'hésitez pas à nous contacter.
            </p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; padding: 20px; color: #9ca3af; font-size: 12px;">
            <p>&copy; {{ date('Y') }} {{ $companyName }} - {{ $companySlogan }}</p>
        </div>
    </div>
</body>
</html>
