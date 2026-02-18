<?php

namespace App\Filament\Loueur\Pages;

use App\Models\Availability;
use App\Models\Booking;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Calendar extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Réservations';

    protected static ?string $navigationLabel = 'Calendrier';

    protected static ?string $title = 'Calendrier des disponibilités';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.loueur.pages.calendar';

    public ?int $selectedVehicleId = null;
    public ?string $currentMonth = null;
    public array $calendarData = [];

    public function mount(): void
    {
        $this->currentMonth = now()->format('Y-m');
        $this->loadCalendarData();
    }

    public function loadCalendarData(): void
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) {
            return;
        }

        $startOfMonth = Carbon::parse($this->currentMonth . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Get vehicles
        $vehiclesQuery = Vehicle::where('loueur_id', $loueur->id);
        if ($this->selectedVehicleId) {
            $vehiclesQuery->where('id', $this->selectedVehicleId);
        }
        $vehicles = $vehiclesQuery->get();

        // Get bookings for this period
        $bookings = Booking::where('loueur_id', $loueur->id)
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($sq) use ($startOfMonth, $endOfMonth) {
                        $sq->where('start_date', '<=', $startOfMonth)
                            ->where('end_date', '>=', $endOfMonth);
                    });
            })
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->with('vehicle')
            ->get();

        // Get availability blocks
        $vehicleIds = $vehicles->pluck('id');
        $availabilities = Availability::whereIn('vehicle_id', $vehicleIds)
            ->forDateRange($startOfMonth, $endOfMonth)
            ->get();

        // Build calendar data
        $this->calendarData = [
            'month' => $startOfMonth->translatedFormat('F Y'),
            'startOfMonth' => $startOfMonth->toDateString(),
            'endOfMonth' => $endOfMonth->toDateString(),
            'vehicles' => $vehicles->map(fn($v) => [
                'id' => $v->id,
                'name' => $v->full_name,
                'image' => $v->image,
            ])->toArray(),
            'days' => [],
            'events' => [],
        ];

        // Generate days of the month
        $current = $startOfMonth->copy();
        while ($current->lte($endOfMonth)) {
            $this->calendarData['days'][] = [
                'date' => $current->toDateString(),
                'day' => $current->day,
                'dayOfWeek' => $current->translatedFormat('D'),
                'isToday' => $current->isToday(),
                'isWeekend' => $current->isFriday() || $current->isSaturday(),
            ];
            $current->addDay();
        }

        // Add bookings as events
        foreach ($bookings as $booking) {
            $this->calendarData['events'][] = [
                'id' => 'booking-' . $booking->id,
                'type' => 'booking',
                'vehicleId' => $booking->vehicle_id,
                'vehicleName' => $booking->vehicle->full_name ?? '',
                'startDate' => $booking->start_date->toDateString(),
                'endDate' => $booking->end_date->toDateString(),
                'title' => $booking->client_name,
                'status' => $booking->status,
                'color' => match($booking->status) {
                    'pending' => 'amber',
                    'confirmed' => 'blue',
                    'active' => 'green',
                    default => 'gray',
                },
            ];
        }

        // Add availability blocks as events
        foreach ($availabilities as $availability) {
            $this->calendarData['events'][] = [
                'id' => 'availability-' . $availability->id,
                'type' => 'availability',
                'vehicleId' => $availability->vehicle_id,
                'vehicleName' => $availability->vehicle->full_name ?? '',
                'startDate' => $availability->start_date->toDateString(),
                'endDate' => $availability->end_date->toDateString(),
                'title' => $availability->reason ?? $availability->type_label,
                'availabilityType' => $availability->type,
                'color' => match($availability->type) {
                    'blocked' => 'red',
                    'maintenance' => 'orange',
                    default => 'gray',
                },
            ];
        }
    }

    public function previousMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->subMonth()->format('Y-m');
        $this->loadCalendarData();
    }

    public function nextMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->addMonth()->format('Y-m');
        $this->loadCalendarData();
    }

    public function goToToday(): void
    {
        $this->currentMonth = now()->format('Y-m');
        $this->loadCalendarData();
    }

    public function selectVehicle(?int $vehicleId): void
    {
        $this->selectedVehicleId = $vehicleId;
        $this->loadCalendarData();
    }

    // Block dates form
    public ?array $blockData = [];

    public function blockDatesForm(Form $form): Form
    {
        $loueur = Auth::user()->loueur;
        $vehicles = $loueur ? Vehicle::where('loueur_id', $loueur->id)->pluck('full_name', 'id') : [];

        return $form
            ->schema([
                Forms\Components\Select::make('vehicle_id')
                    ->label('Véhicule')
                    ->options($vehicles)
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Date de début')
                    ->required()
                    ->minDate(now()),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Date de fin')
                    ->required()
                    ->afterOrEqual('start_date'),
                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'blocked' => 'Bloqué (indisponible)',
                        'maintenance' => 'Maintenance',
                    ])
                    ->required()
                    ->default('blocked'),
                Forms\Components\TextInput::make('reason')
                    ->label('Raison (optionnel)')
                    ->maxLength(255),
            ])
            ->statePath('blockData');
    }

    public function blockDates(): void
    {
        $data = $this->blockDatesForm->getState();
        $loueur = Auth::user()->loueur;

        // Verify vehicle belongs to loueur
        $vehicle = Vehicle::where('id', $data['vehicle_id'])
            ->where('loueur_id', $loueur->id)
            ->first();

        if (!$vehicle) {
            Notification::make()
                ->title('Erreur')
                ->body('Véhicule non trouvé.')
                ->danger()
                ->send();
            return;
        }

        // Check for conflicts with bookings
        $conflictingBooking = Booking::where('vehicle_id', $data['vehicle_id'])
            ->whereIn('status', ['confirmed', 'active'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                    ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                    ->orWhere(function ($sq) use ($data) {
                        $sq->where('start_date', '<=', $data['start_date'])
                            ->where('end_date', '>=', $data['end_date']);
                    });
            })
            ->first();

        if ($conflictingBooking) {
            Notification::make()
                ->title('Conflit détecté')
                ->body('Une réservation existe déjà pour ces dates (Réf: ' . $conflictingBooking->reference . ')')
                ->danger()
                ->send();
            return;
        }

        Availability::create([
            'vehicle_id' => $data['vehicle_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'type' => $data['type'],
            'reason' => $data['reason'] ?? null,
        ]);

        Notification::make()
            ->title('Dates bloquées')
            ->body('Les dates ont été bloquées avec succès.')
            ->success()
            ->send();

        $this->blockData = [];
        $this->loadCalendarData();
    }

    public function deleteAvailability(int $id): void
    {
        $loueur = Auth::user()->loueur;

        $availability = Availability::whereHas('vehicle', fn($q) => $q->where('loueur_id', $loueur->id))
            ->where('id', $id)
            ->first();

        if ($availability) {
            $availability->delete();

            Notification::make()
                ->title('Blocage supprimé')
                ->success()
                ->send();

            $this->loadCalendarData();
        }
    }

    protected function getForms(): array
    {
        return [
            'blockDatesForm',
        ];
    }
}
