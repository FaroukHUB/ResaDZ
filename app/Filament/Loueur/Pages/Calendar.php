<?php

namespace App\Filament\Loueur\Pages;

use App\Models\Availability;
use App\Models\Booking;
use App\Models\Vehicle;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Réservations';

    protected static ?string $navigationLabel = 'Calendrier';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.loueur.pages.calendar';

    public int $currentMonth;
    public int $currentYear;
    public ?int $selectedVehicle = null;

    public function mount()
    {
        $this->currentMonth = (int) request('month', now()->month);
        $this->currentYear = (int) request('year', now()->year);
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function goToToday(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    /**
     * Toggle a blocked date for a vehicle.
     * If the date is already blocked, remove the block.
     * If the date is free (no booking), create a block.
     */
    public function toggleBlock(int $vehicleId, string $date): void
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) return;

        // Verify the vehicle belongs to this loueur
        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('loueur_id', $loueur->id)
            ->first();

        if (!$vehicle) return;

        $dateObj = Carbon::parse($date);

        // Check if date is outside vehicle availability range
        if ($vehicle->available_from && $dateObj->lt($vehicle->available_from)) {
            Notification::make()
                ->title('Hors période')
                ->body('Ce véhicule n\'est disponible qu\'à partir du ' . $vehicle->available_from->format('d/m/Y'))
                ->warning()
                ->duration(3000)
                ->send();
            return;
        }
        if ($vehicle->available_until && $dateObj->gt($vehicle->available_until)) {
            Notification::make()
                ->title('Hors période')
                ->body('Ce véhicule n\'est disponible que jusqu\'au ' . $vehicle->available_until->format('d/m/Y'))
                ->warning()
                ->duration(3000)
                ->send();
            return;
        }

        // Check if there's already an availability block for this exact date
        $existingBlock = Availability::where('vehicle_id', $vehicleId)
            ->where('type', 'blocked')
            ->where('start_date', '<=', $dateObj)
            ->where('end_date', '>=', $dateObj)
            ->first();

        if ($existingBlock) {
            // If block is exactly one day, delete it
            if ($existingBlock->start_date->eq($existingBlock->end_date)) {
                $existingBlock->delete();
                Notification::make()
                    ->title('Date débloquée')
                    ->success()
                    ->duration(2000)
                    ->send();
            } else {
                // Multi-day block - need to split or trim
                if ($existingBlock->start_date->eq($dateObj)) {
                    // Trim start
                    $existingBlock->update(['start_date' => $dateObj->copy()->addDay()]);
                } elseif ($existingBlock->end_date->eq($dateObj)) {
                    // Trim end
                    $existingBlock->update(['end_date' => $dateObj->copy()->subDay()]);
                } else {
                    // Split in two
                    $originalEnd = $existingBlock->end_date->copy();
                    $existingBlock->update(['end_date' => $dateObj->copy()->subDay()]);
                    Availability::create([
                        'vehicle_id' => $vehicleId,
                        'start_date' => $dateObj->copy()->addDay(),
                        'end_date' => $originalEnd,
                        'type' => 'blocked',
                        'reason' => $existingBlock->reason,
                    ]);
                }
                Notification::make()
                    ->title('Date débloquée')
                    ->success()
                    ->duration(2000)
                    ->send();
            }
            return;
        }

        // Check if there's a booking on this date - can't block booked dates
        $hasBooking = Booking::where('vehicle_id', $vehicleId)
            ->where('loueur_id', $loueur->id)
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where('start_date', '<=', $dateObj)
            ->where('end_date', '>=', $dateObj)
            ->exists();

        if ($hasBooking) {
            Notification::make()
                ->title('Impossible')
                ->body('Cette date a déjà une réservation')
                ->warning()
                ->duration(3000)
                ->send();
            return;
        }

        // Check for maintenance blocks
        $hasMaintenance = Availability::where('vehicle_id', $vehicleId)
            ->where('type', 'maintenance')
            ->where('start_date', '<=', $dateObj)
            ->where('end_date', '>=', $dateObj)
            ->exists();

        if ($hasMaintenance) {
            Notification::make()
                ->title('Impossible')
                ->body('Cette date est en maintenance')
                ->warning()
                ->duration(3000)
                ->send();
            return;
        }

        // Try to merge with adjacent blocks
        $dayBefore = $dateObj->copy()->subDay();
        $dayAfter = $dateObj->copy()->addDay();

        $blockBefore = Availability::where('vehicle_id', $vehicleId)
            ->where('type', 'blocked')
            ->where('end_date', $dayBefore)
            ->first();

        $blockAfter = Availability::where('vehicle_id', $vehicleId)
            ->where('type', 'blocked')
            ->where('start_date', $dayAfter)
            ->first();

        if ($blockBefore && $blockAfter) {
            // Merge all three
            $blockBefore->update(['end_date' => $blockAfter->end_date]);
            $blockAfter->delete();
        } elseif ($blockBefore) {
            $blockBefore->update(['end_date' => $dateObj]);
        } elseif ($blockAfter) {
            $blockAfter->update(['start_date' => $dateObj]);
        } else {
            Availability::create([
                'vehicle_id' => $vehicleId,
                'start_date' => $dateObj,
                'end_date' => $dateObj,
                'type' => 'blocked',
                'reason' => 'Bloqué manuellement',
            ]);
        }

        Notification::make()
            ->title('Date bloquée')
            ->success()
            ->duration(2000)
            ->send();
    }

    /**
     * Block a range of dates for a vehicle.
     */
    public function blockRange(int $vehicleId, string $startDate, string $endDate, string $reason = ''): void
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) return;

        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('loueur_id', $loueur->id)
            ->first();

        if (!$vehicle) return;

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        // Check for conflicts
        $hasBooking = Booking::where('vehicle_id', $vehicleId)
            ->where('loueur_id', $loueur->id)
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->exists();

        if ($hasBooking) {
            Notification::make()
                ->title('Conflit')
                ->body('Des réservations existent sur cette période')
                ->warning()
                ->send();
            return;
        }

        // Remove existing blocks in range
        Availability::where('vehicle_id', $vehicleId)
            ->where('type', 'blocked')
            ->where('start_date', '<=', $end)
            ->where('end_date', '>=', $start)
            ->delete();

        Availability::create([
            'vehicle_id' => $vehicleId,
            'start_date' => $start,
            'end_date' => $end,
            'type' => 'blocked',
            'reason' => $reason ?: 'Bloqué manuellement',
        ]);

        Notification::make()
            ->title('Période bloquée')
            ->body($start->format('d/m') . ' → ' . $end->format('d/m'))
            ->success()
            ->send();
    }

    /**
     * Import events from a Google Calendar iCal URL.
     */
    public function importGoogleCalendar(int $vehicleId, string $icalUrl): void
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) return;

        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('loueur_id', $loueur->id)
            ->first();

        if (!$vehicle) return;

        try {
            $icalContent = @file_get_contents($icalUrl);

            if (!$icalContent) {
                Notification::make()
                    ->title('Erreur')
                    ->body('Impossible de récupérer le calendrier. Vérifiez le lien.')
                    ->danger()
                    ->send();
                return;
            }

            $events = $this->parseIcal($icalContent);

            if (empty($events)) {
                Notification::make()
                    ->title('Aucun événement')
                    ->body('Le calendrier ne contient aucun événement à importer.')
                    ->warning()
                    ->send();
                return;
            }

            $imported = 0;
            foreach ($events as $event) {
                $start = Carbon::parse($event['start']);
                $end = Carbon::parse($event['end']);

                // Skip past events
                if ($end->isPast()) continue;

                // Skip if date already has a booking
                $hasBooking = Booking::where('vehicle_id', $vehicleId)
                    ->where('loueur_id', $loueur->id)
                    ->whereIn('status', ['pending', 'confirmed', 'active'])
                    ->where('start_date', '<=', $end)
                    ->where('end_date', '>=', $start)
                    ->exists();

                if ($hasBooking) continue;

                // Remove existing blocks in this range
                Availability::where('vehicle_id', $vehicleId)
                    ->where('type', 'blocked')
                    ->where('start_date', '<=', $end)
                    ->where('end_date', '>=', $start)
                    ->delete();

                Availability::create([
                    'vehicle_id' => $vehicleId,
                    'start_date' => $start,
                    'end_date' => $end,
                    'type' => 'blocked',
                    'reason' => 'Google Calendar: ' . ($event['summary'] ?? 'Événement'),
                ]);

                $imported++;
            }

            Notification::make()
                ->title('Import réussi')
                ->body($imported . ' événement(s) importé(s) comme dates bloquées.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur d\'import')
                ->body('Le format du calendrier n\'est pas reconnu.')
                ->danger()
                ->send();
        }
    }

    /**
     * Parse iCal content and extract events.
     */
    private function parseIcal(string $content): array
    {
        $events = [];
        $lines = explode("\n", str_replace("\r\n", "\n", $content));

        $inEvent = false;
        $currentEvent = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $inEvent = true;
                $currentEvent = [];
                continue;
            }

            if ($line === 'END:VEVENT') {
                $inEvent = false;
                if (!empty($currentEvent['start']) && !empty($currentEvent['end'])) {
                    $events[] = $currentEvent;
                }
                continue;
            }

            if (!$inEvent) continue;

            if (str_starts_with($line, 'DTSTART')) {
                $currentEvent['start'] = $this->parseIcalDate($line);
            } elseif (str_starts_with($line, 'DTEND')) {
                $date = $this->parseIcalDate($line);
                if ($date) {
                    // iCal end date is exclusive for DATE values, adjust
                    $parsed = Carbon::parse($date);
                    if (str_contains($line, 'VALUE=DATE') && !str_contains($line, 'VALUE=DATE-TIME')) {
                        $parsed->subDay();
                    }
                    $currentEvent['end'] = $parsed->format('Y-m-d');
                }
            } elseif (str_starts_with($line, 'SUMMARY:')) {
                $currentEvent['summary'] = substr($line, 8);
            }
        }

        return $events;
    }

    /**
     * Parse an iCal date line.
     */
    private function parseIcalDate(string $line): ?string
    {
        // Extract the date value after the last colon
        $parts = explode(':', $line);
        $value = end($parts);

        // Remove any trailing Z or timezone info
        $value = preg_replace('/[TZ]/', '', trim($value));

        if (strlen($value) >= 8) {
            $year = substr($value, 0, 4);
            $month = substr($value, 4, 2);
            $day = substr($value, 6, 2);
            return "$year-$month-$day";
        }

        return null;
    }

    public function getViewData(): array
    {
        $loueur = Auth::user()->loueur;

        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Generate days array
        $days = [];
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
        foreach ($period as $date) {
            $days[] = $date;
        }

        // Base data that's always needed
        $baseData = [
            'vehicles' => collect(),
            'statusMap' => [],
            'bookingInfo' => [],
            'blockInfo' => [],
            'statsData' => [],
            'days' => $days,
            'currentMonth' => $this->currentMonth,
            'currentYear' => $this->currentYear,
            'monthName' => $startOfMonth->translatedFormat('F Y'),
            'icalUrl' => '',
            'today' => now()->format('Y-m-d'),
        ];

        if (!$loueur) {
            return $baseData;
        }

        // Get all vehicles
        $vehicles = Vehicle::where('loueur_id', $loueur->id)
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        // Get all bookings for this month
        $bookings = Booking::where('loueur_id', $loueur->id)
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('start_date', '<=', $endOfMonth)
                      ->where('end_date', '>=', $startOfMonth);
            })
            ->with('vehicle')
            ->get();

        // Get all availability blocks for this month
        $vehicleIds = $vehicles->pluck('id');
        $blocks = Availability::whereIn('vehicle_id', $vehicleIds)
            ->where('start_date', '<=', $endOfMonth)
            ->where('end_date', '>=', $startOfMonth)
            ->get();

        // Build booking map per vehicle per day
        $bookingMap = [];
        foreach ($bookings as $booking) {
            $start = $booking->start_date->copy()->max($startOfMonth);
            $end = $booking->end_date->copy()->min($endOfMonth);
            $period = CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $key = $booking->vehicle_id . '_' . $date->format('Y-m-d');
                $bookingMap[$key] = $booking;
            }
        }

        // Build block map per vehicle per day
        $blockMap = [];
        foreach ($blocks as $block) {
            $start = $block->start_date->copy()->max($startOfMonth);
            $end = $block->end_date->copy()->min($endOfMonth);
            $period = CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $key = $block->vehicle_id . '_' . $date->format('Y-m-d');
                $blockMap[$key] = $block;
            }
        }

        // Build unavailable map for dates outside available_from/available_until
        $unavailableMap = [];
        foreach ($vehicles as $vehicle) {
            if ($vehicle->available_from || $vehicle->available_until) {
                $monthPeriod = CarbonPeriod::create($startOfMonth, $endOfMonth);
                foreach ($monthPeriod as $date) {
                    $outOfRange = false;
                    if ($vehicle->available_from && $date->lt($vehicle->available_from)) {
                        $outOfRange = true;
                    }
                    if ($vehicle->available_until && $date->gt($vehicle->available_until)) {
                        $outOfRange = true;
                    }
                    if ($outOfRange) {
                        $unavailableMap[$vehicle->id . '_' . $date->format('Y-m-d')] = true;
                    }
                }
            }
        }

        // Build status map & booking info per vehicle per day (for Blade rendering)
        $todayStr = now()->format('Y-m-d');
        $statusMap = [];   // key => status string
        $bookingInfo = []; // key => [ status, client_name, start, end, id ]
        $blockInfo = [];   // key => [ type, reason ]
        $statsData = [];   // vehicleId => [ available, blocked, booked ]

        foreach ($vehicles as $vehicle) {
            $available = 0;
            $blocked = 0;
            $booked = 0;

            $monthPeriod = CarbonPeriod::create($startOfMonth, $endOfMonth);
            foreach ($monthPeriod as $date) {
                $dateStr = $date->format('Y-m-d');
                $key = $vehicle->id . '_' . $dateStr;

                // Determine status in priority order
                if (isset($bookingMap[$key])) {
                    $statusMap[$key] = 'booked';
                    $b = $bookingMap[$key];
                    $bookingInfo[$key] = [
                        'status' => $b->status,
                        'client_name' => $b->client_name ?? 'Client',
                        'start' => $b->start_date->format('d/m'),
                        'end' => $b->end_date->format('d/m'),
                        'id' => $b->id,
                    ];
                    $booked++;
                } elseif (isset($blockMap[$key]) && $blockMap[$key]->type === 'maintenance') {
                    $statusMap[$key] = 'maintenance';
                    $blockInfo[$key] = [
                        'type' => $blockMap[$key]->type,
                        'reason' => $blockMap[$key]->reason ?? '',
                    ];
                } elseif (isset($blockMap[$key])) {
                    $statusMap[$key] = 'blocked';
                    $blockInfo[$key] = [
                        'type' => $blockMap[$key]->type,
                        'reason' => $blockMap[$key]->reason ?? '',
                    ];
                    $blocked++;
                } elseif (isset($unavailableMap[$key])) {
                    $statusMap[$key] = 'unavailable';
                } elseif ($dateStr < $todayStr) {
                    $statusMap[$key] = 'past';
                } else {
                    $statusMap[$key] = 'available';
                    $available++;
                }
            }

            $statsData[$vehicle->id] = [
                'available' => $available,
                'blocked' => $blocked,
                'booked' => $booked,
            ];
        }

        // Generate iCal URL
        $token = \App\Http\Controllers\Api\CalendarController::generateToken($loueur->id);
        $icalUrl = url('/calendar/ical/' . $token . '.ics');

        return [
            'vehicles' => $vehicles,
            'statusMap' => $statusMap,
            'bookingInfo' => $bookingInfo,
            'blockInfo' => $blockInfo,
            'statsData' => $statsData,
            'days' => $days,
            'currentMonth' => $this->currentMonth,
            'currentYear' => $this->currentYear,
            'monthName' => $startOfMonth->translatedFormat('F Y'),
            'icalUrl' => $icalUrl,
            'today' => $todayStr,
        ];
    }
}
