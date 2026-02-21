<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donnez votre avis</title>
</head>
@php
    $companyName = \App\Models\Setting::get('company_name', 'ResaDZ');
    $companySlogan = \App\Models\Setting::get('company_slogan', 'Location de véhicules en Algérie');
    $companyEmail = \App\Models\Setting::get('company_email', 'support@resadz.com');
@endphp
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); padding: 30px; border-radius: 16px 16px 0 0; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px;">{{ $companyName }}</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 14px;">Comment s'est passée votre location ?</p>
        </div>

        <!-- Content -->
        <div style="background: white; padding: 30px; border-radius: 0 0 16px 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

            <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">
                Bonjour <strong>{{ $booking->client_name }}</strong>,
            </p>

            <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">
                Votre location avec <strong>{{ $loueur->company_name ?? 'le loueur' }}</strong> est maintenant terminée.
                Nous espérons que tout s'est bien passé !
            </p>

            <!-- Booking Summary -->
            <div style="background: #fef2f2; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                <h2 style="color: #991b1b; margin: 0 0 15px 0; font-size: 18px;">Votre location</h2>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #7f1d1d; font-weight: 600;">Véhicule</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ $vehicle->full_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #7f1d1d; font-weight: 600;">Du</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #7f1d1d; font-weight: 600;">Au</td>
                        <td style="padding: 8px 0; color: #374151; text-align: right;">{{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </div>

            <p style="font-size: 16px; color: #374151; margin-bottom: 20px; text-align: center;">
                Votre avis compte ! En quelques clics, aidez les autres clients<br>
                à choisir en partageant votre expérience.
            </p>

            <!-- CTA Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $reviewUrl }}"
                   style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 14px rgba(220, 38, 38, 0.4);">
                    Donner mon avis
                </a>
            </div>

            <p style="font-size: 14px; color: #6b7280; text-align: center; margin-bottom: 25px;">
                Cela ne prend que 2 minutes et aide la communauté {{ $companyName }} !
            </p>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 25px 0;">

            <p style="font-size: 12px; color: #9ca3af; text-align: center; margin: 0;">
                Cet email a été envoyé par {{ $companyName }} suite à la fin de votre location.<br>
                Si vous avez des questions, contactez-nous à {{ $companyEmail }}
            </p>
        </div>

        <!-- Footer -->
        <div style="text-align: center; padding: 20px; color: #9ca3af; font-size: 12px;">
            <p>&copy; {{ date('Y') }} {{ $companyName }} - {{ $companySlogan }}</p>
        </div>
    </div>
</body>
</html>
