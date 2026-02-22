<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contrat de Location - {{ $contract_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            color: #666;
        }

        .contract-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .contract-info-left, .contract-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .contract-info-right {
            text-align: right;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            background-color: #2563eb;
            color: white;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .section-content {
            padding: 0 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table th, table td {
            padding: 6px 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 10px;
            color: #555;
        }

        .grid-2 {
            display: table;
            width: 100%;
        }

        .grid-2-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }

        .grid-2-col:last-child {
            padding-right: 0;
            padding-left: 10px;
        }

        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .info-box h4 {
            font-size: 11px;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .info-box p {
            margin-bottom: 3px;
        }

        .pricing-table td {
            padding: 8px 10px;
        }

        .pricing-table .label {
            font-weight: normal;
        }

        .pricing-table .value {
            text-align: right;
            font-weight: bold;
        }

        .pricing-table .total-row {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }

        .pricing-table .total-row td {
            padding: 10px;
        }

        .conditions {
            font-size: 10px;
        }

        .conditions h4 {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .conditions p {
            margin-bottom: 8px;
            color: #555;
        }

        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-grid {
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 45%;
            border: 1px solid #ddd;
            padding: 15px;
            vertical-align: top;
        }

        .signature-box h4 {
            font-size: 11px;
            margin-bottom: 10px;
            color: #2563eb;
        }

        .signature-box .signature-line {
            border-bottom: 1px solid #333;
            height: 50px;
            margin-top: 30px;
        }

        .signature-spacer {
            display: table-cell;
            width: 10%;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .badge {
            display: inline-block;
            background-color: #e5e7eb;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    {{-- En-tête --}}
    <div class="header">
        <h1>CONTRAT DE LOCATION DE VEHICULE</h1>
        <p>{{ $loueur_name }} - Location de voitures</p>
    </div>

    {{-- Informations du contrat --}}
    <div class="contract-info">
        <div class="contract-info-left">
            <strong>N du contrat:</strong> {{ $contract_number }}<br>
            <strong>Date:</strong> {{ $contract_date }}
        </div>
        <div class="contract-info-right">
            <strong>Reference reservation:</strong> {{ $booking->reference }}
        </div>
    </div>

    {{-- Parties --}}
    <div class="section">
        <div class="section-title">PARTIES AU CONTRAT</div>
        <div class="section-content">
            <div class="grid-2">
                <div class="grid-2-col">
                    <div class="info-box">
                        <h4>LOUEUR</h4>
                        <p><strong>{{ $loueur_name }}</strong></p>
                        @if($loueur_address)
                            <p>{{ $loueur_address }}</p>
                        @endif
                        @if($loueur_city || $loueur_wilaya)
                            <p>{{ $loueur_city }}@if($loueur_city && $loueur_wilaya), @endif{{ $loueur_wilaya }}</p>
                        @endif
                        @if($loueur_phone)
                            <p>Tel: {{ $loueur_phone }}</p>
                        @endif
                        @if($loueur_email)
                            <p>Email: {{ $loueur_email }}</p>
                        @endif
                    </div>
                </div>
                <div class="grid-2-col">
                    <div class="info-box">
                        <h4>LOCATAIRE</h4>
                        <p><strong>{{ $client_name }}</strong></p>
                        <p>Tel: {{ $client_phone }}</p>
                        @if($client_email)
                            <p>Email: {{ $client_email }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Vehicule --}}
    <div class="section">
        <div class="section-title">VEHICULE LOUE</div>
        <div class="section-content">
            <table>
                <tr>
                    <th style="width: 25%;">Vehicule</th>
                    <td style="width: 25%;"><strong>{{ $vehicle_name }}</strong></td>
                    <th style="width: 25%;">Marque</th>
                    <td style="width: 25%;">{{ $vehicle_brand }}</td>
                </tr>
                <tr>
                    <th>Categorie</th>
                    <td>{{ $vehicle_category }}</td>
                    <th>Annee</th>
                    <td>{{ $vehicle_year }}</td>
                </tr>
                <tr>
                    <th>Transmission</th>
                    <td>{{ $vehicle_transmission }}</td>
                    <th>Carburant</th>
                    <td>{{ $vehicle_fuel }}</td>
                </tr>
                <tr>
                    <th>Couleur</th>
                    <td>{{ $vehicle_color }}</td>
                    <th>Places</th>
                    <td>{{ $vehicle_seats }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Periode de location --}}
    <div class="section">
        <div class="section-title">PERIODE DE LOCATION</div>
        <div class="section-content">
            <table>
                <tr>
                    <th style="width: 25%;">Date de debut</th>
                    <td style="width: 25%;"><strong>{{ $start_date }}</strong></td>
                    <th style="width: 25%;">Heure</th>
                    <td style="width: 25%;">{{ $start_time }}</td>
                </tr>
                <tr>
                    <th>Date de fin</th>
                    <td><strong>{{ $end_date }}</strong></td>
                    <th>Heure</th>
                    <td>{{ $end_time }}</td>
                </tr>
                <tr>
                    <th>Duree totale</th>
                    <td colspan="3"><strong>{{ $total_days }} jour(s)</strong></td>
                </tr>
            </table>

            @if($pickup_address || $return_address)
            <div class="grid-2" style="margin-top: 10px;">
                <div class="grid-2-col">
                    <div class="info-box">
                        <h4>LIEU DE PRISE EN CHARGE</h4>
                        <p>{{ $pickup_address ?: 'A definir' }}</p>
                    </div>
                </div>
                <div class="grid-2-col">
                    <div class="info-box">
                        <h4>LIEU DE RESTITUTION</h4>
                        <p>{{ $return_address ?: 'A definir' }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Tarification --}}
    <div class="section">
        <div class="section-title">TARIFICATION</div>
        <div class="section-content">
            <table class="pricing-table">
                <tr>
                    <td class="label">Prix par jour</td>
                    <td class="value">{{ $price_per_day }} {{ $currency_symbol }}</td>
                </tr>
                <tr>
                    <td class="label">Location ({{ $total_days }} jours)</td>
                    <td class="value">{{ $total_rental }} {{ $currency_symbol }}</td>
                </tr>
                @if($duration_discount && $duration_discount != '0')
                <tr>
                    <td class="label">Remise duree</td>
                    <td class="value">- {{ $duration_discount }} {{ $currency_symbol }}</td>
                </tr>
                @endif
                @if($delivery_fee && $delivery_fee != '0')
                <tr>
                    <td class="label">Frais de livraison</td>
                    <td class="value">{{ $delivery_fee }} {{ $currency_symbol }}</td>
                </tr>
                @endif
                @if($return_fee && $return_fee != '0')
                <tr>
                    <td class="label">Frais de retour</td>
                    <td class="value">{{ $return_fee }} {{ $currency_symbol }}</td>
                </tr>
                @endif
                @if($options_total && $options_total != '0')
                <tr>
                    <td class="label">Options supplementaires</td>
                    <td class="value">{{ $options_total }} {{ $currency_symbol }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label">TOTAL A PAYER</td>
                    <td class="value">{{ $total_price }} {{ $currency_symbol }}</td>
                </tr>
            </table>

            <div class="grid-2">
                <div class="grid-2-col">
                    <div class="info-box">
                        <h4>CAUTION</h4>
                        <p><strong>{{ $deposit_amount }} {{ $deposit_currency }}</strong></p>
                        <p style="font-size: 9px; color: #666;">A restituer au retour du vehicule en bon etat</p>
                    </div>
                </div>
                <div class="grid-2-col">
                    <div class="info-box">
                        <h4>ACOMPTE VERSE</h4>
                        <p><strong>{{ $advance_amount }} {{ $currency_symbol }}</strong></p>
                        <p style="font-size: 9px; color: #666;">Reste a payer: {{ $remaining_amount }} {{ $currency_symbol }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Options --}}
    @if(!empty($selected_options))
    <div class="section">
        <div class="section-title">OPTIONS SUPPLEMENTAIRES</div>
        <div class="section-content">
            @foreach($selected_options as $option)
                <span class="badge">{{ $option }}</span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Conditions --}}
    @if(!empty($conditions))
    <div class="section">
        <div class="section-title">CONDITIONS DE LOCATION</div>
        <div class="section-content conditions">
            @foreach($conditions as $condition)
                <h4>{{ $condition['title'] }}</h4>
                <p>{{ $condition['description'] }}</p>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Conditions generales --}}
    <div class="section">
        <div class="section-title">CONDITIONS GENERALES</div>
        <div class="section-content conditions">
            <p>Le locataire s'engage a restituer le vehicule dans l'etat ou il l'a recu, avec le plein de carburant. Tout dommage constate lors de la restitution sera facture au locataire. Le locataire certifie etre titulaire d'un permis de conduire valide et avoir l'age minimum requis pour la conduite du vehicule loue.</p>
            <p>En cas de retard de restitution non signale, des frais supplementaires seront appliques. Le locataire s'engage a respecter le code de la route et a ne pas utiliser le vehicule pour des activites illegales.</p>
        </div>
    </div>

    {{-- Signatures --}}
    <div class="signature-section">
        <div class="section-title">SIGNATURES</div>
        <div class="section-content">
            <p style="margin-bottom: 15px; font-size: 10px; color: #666;">Les parties reconnaissent avoir pris connaissance des conditions ci-dessus et les acceptent.</p>

            <div class="signature-grid">
                <div class="signature-box">
                    <h4>LE LOUEUR</h4>
                    <p>{{ $loueur_name }}</p>
                    <p style="font-size: 9px; color: #666;">Fait a {{ $loueur_city ?: '____________' }}, le {{ $contract_date }}</p>
                    <div class="signature-line"></div>
                    <p style="font-size: 9px; color: #666; margin-top: 5px;">Signature et cachet</p>
                </div>
                <div class="signature-spacer"></div>
                <div class="signature-box">
                    <h4>LE LOCATAIRE</h4>
                    <p>{{ $client_name }}</p>
                    <p style="font-size: 9px; color: #666;">Lu et approuve, le {{ $contract_date }}</p>
                    <div class="signature-line"></div>
                    <p style="font-size: 9px; color: #666; margin-top: 5px;">Signature</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Pied de page --}}
    <div class="footer">
        Document genere automatiquement par {{ \App\Models\Setting::get('company_name', 'ResaDZ') }} - {{ now()->format('d/m/Y H:i') }}<br>
        {{ url('/') }}
    </div>
</body>
</html>
