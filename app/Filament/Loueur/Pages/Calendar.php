<?php

namespace App\Filament\Loueur\Pages;

use App\Models\Booking;
use App\Models\Vehicle;
use Filament\Pages\Page;
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

    public $currentMonth;
    public $currentYear;

    public function mount()
    {
        $this->currentMonth = request('month', now()->month);
        $this->currentYear = request('year', now()->year);
    }

    public function getViewData(): array
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) {
            return ['vehicles' => collect(), 'bookings' => collect(), 'days' => []];
        }

        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Get all vehicles
        $vehicles = Vehicle::where('loueur_id', $loueur->id)
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        // Get all bookings for this month
        $bookings = Booking::where('loueur_id', $loueur->id)
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('start_date', '<=', $startOfMonth)
                          ->where('end_date', '>=', $endOfMonth);
                    });
            })
            ->with('vehicle')
            ->get();

        // Build booking map per vehicle per day
        $bookingMap = [];
        foreach ($bookings as $booking) {
            $period = CarbonPeriod::create($booking->start_date, $booking->end_date);
            foreach ($period as $date) {
                $key = $booking->vehicle_id . '_' . $date->format('Y-m-d');
                $bookingMap[$key] = $booking;
            }
        }

        // Generate days array
        $days = [];
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
        foreach ($period as $date) {
            $days[] = $date;
        }

        return [
            'vehicles' => $vehicles,
            'bookings' => $bookings,
            'bookingMap' => $bookingMap,
            'days' => $days,
            'currentMonth' => $this->currentMonth,
            'currentYear' => $this->currentYear,
            'monthName' => $startOfMonth->translatedFormat('F Y'),
            'prevMonth' => $startOfMonth->copy()->subMonth(),
            'nextMonth' => $startOfMonth->copy()->addMonth(),
        ];
    }
}
