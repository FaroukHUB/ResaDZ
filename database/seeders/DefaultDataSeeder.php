<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Setting;
use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        // Créneaux horaires (de 06:00 à 23:30 par tranches de 30 minutes)
        $order = 0;
        for ($hour = 6; $hour < 24; $hour++) {
            foreach (['00', '30'] as $minute) {
                $startTime = sprintf('%02d:%s:00', $hour, $minute);
                $endHour = $minute === '30' ? $hour + 1 : $hour;
                $endMinute = $minute === '30' ? '00' : '30';
                $endTime = sprintf('%02d:%s:00', $endHour, $endMinute);

                TimeSlot::firstOrCreate(
                    ['start_time' => $startTime, 'end_time' => $endTime],
                    [
                        'label' => sprintf('%02d:%s - %02d:%s', $hour, $minute, $endHour, $endMinute),
                        'is_active' => true,
                        'sort_order' => $order++,
                    ]
                );
            }
        }

        // Options par défaut
        $options = [
            [
                'name' => 'GPS',
                'slug' => 'gps',
                'description' => 'Système de navigation GPS intégré',
                'price' => 500,
                'price_type' => 'per_day',
                'sort_order' => 1,
            ],
            [
                'name' => 'Siège bébé',
                'slug' => 'siege-bebe',
                'description' => 'Siège auto pour bébé (0-12 mois)',
                'price' => 300,
                'price_type' => 'per_day',
                'sort_order' => 2,
            ],
            [
                'name' => 'Siège enfant',
                'slug' => 'siege-enfant',
                'description' => 'Siège auto pour enfant (1-4 ans)',
                'price' => 300,
                'price_type' => 'per_day',
                'sort_order' => 3,
            ],
            [
                'name' => 'Conducteur additionnel',
                'slug' => 'conducteur-additionnel',
                'description' => 'Ajout d\'un conducteur supplémentaire',
                'price' => 1000,
                'price_type' => 'per_rental',
                'sort_order' => 4,
            ],
            [
                'name' => 'Livraison aéroport',
                'slug' => 'livraison-aeroport',
                'description' => 'Livraison et récupération à l\'aéroport',
                'price' => 0,
                'price_type' => 'free',
                'sort_order' => 5,
            ],
            [
                'name' => 'Kilométrage illimité',
                'slug' => 'kilometrage-illimite',
                'description' => 'Pas de limite de kilomètres',
                'price' => 0,
                'price_type' => 'free',
                'sort_order' => 6,
            ],
        ];

        foreach ($options as $optionData) {
            Option::firstOrCreate(
                ['slug' => $optionData['slug']],
                $optionData
            );
        }

        // Paramètres par défaut
        $settings = [
            // Général
            ['group' => 'general', 'key' => 'site_name', 'value' => 'ResaDZ', 'type' => 'text', 'label' => 'Nom du site'],
            ['group' => 'general', 'key' => 'site_description', 'value' => 'Location de voitures en Algérie', 'type' => 'text', 'label' => 'Description'],
            ['group' => 'general', 'key' => 'currency', 'value' => 'DZD', 'type' => 'text', 'label' => 'Devise'],

            // Contact
            ['group' => 'contact', 'key' => 'email', 'value' => 'contact@mbcarsdzrouiba.com', 'type' => 'text', 'label' => 'Email'],
            ['group' => 'contact', 'key' => 'phone', 'value' => '+213656697788', 'type' => 'text', 'label' => 'Téléphone'],
            ['group' => 'contact', 'key' => 'whatsapp', 'value' => '+213656697788', 'type' => 'text', 'label' => 'WhatsApp'],
            ['group' => 'contact', 'key' => 'address', 'value' => 'Rouiba, Alger, Algérie', 'type' => 'text', 'label' => 'Adresse'],

            // Réservations
            ['group' => 'booking', 'key' => 'min_rental_days', 'value' => '1', 'type' => 'number', 'label' => 'Durée minimum (jours)'],
            ['group' => 'booking', 'key' => 'advance_booking_days', 'value' => '1', 'type' => 'number', 'label' => 'Réservation à l\'avance (jours)'],
            ['group' => 'booking', 'key' => 'deposit_percentage', 'value' => '30', 'type' => 'number', 'label' => 'Acompte (%)'],

            // Social
            ['group' => 'social', 'key' => 'facebook', 'value' => '', 'type' => 'text', 'label' => 'Facebook'],
            ['group' => 'social', 'key' => 'instagram', 'value' => '', 'type' => 'text', 'label' => 'Instagram'],
            ['group' => 'social', 'key' => 'tiktok', 'value' => '', 'type' => 'text', 'label' => 'TikTok'],
        ];

        $order = 0;
        foreach ($settings as $settingData) {
            Setting::firstOrCreate(
                ['key' => $settingData['key']],
                array_merge($settingData, ['sort_order' => $order++])
            );
        }

        $this->command->info('Créneaux horaires: ' . TimeSlot::count());
        $this->command->info('Options: ' . Option::count());
        $this->command->info('Paramètres: ' . Setting::count());
    }
}
