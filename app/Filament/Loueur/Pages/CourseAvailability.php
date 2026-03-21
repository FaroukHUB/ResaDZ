<?php

namespace App\Filament\Loueur\Pages;

use App\Models\CourseAvailability as CourseAvailabilityModel;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CourseAvailability extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Courses';

    protected static ?string $navigationLabel = 'Mes Disponibilites';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.loueur.pages.course-availability';

    public int $currentMonth;
    public int $currentYear;

    // Form data
    public ?string $type = 'available';
    public bool $for_transfer = true;
    public bool $for_delivery = true;
    public ?string $date = null;
    public ?string $start_time = null;
    public ?string $end_time = null;
    public ?string $recurrence = 'none';
    public ?string $recurrence_end = null;
    public ?array $recurrence_days = [];
    public ?string $notes = null;

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
     * Ajouter une disponibilite rapidement (clic sur une date)
     */
    public function quickAddAvailability(string $date): void
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) return;

        $dateObj = Carbon::parse($date);

        // Verifier si une disponibilite existe deja pour ce jour
        $existing = CourseAvailabilityModel::where('loueur_id', $loueur->id)
            ->where('date', $dateObj)
            ->where('recurrence', 'none')
            ->first();

        if ($existing) {
            // Toggle: si c'est available, passer a unavailable, sinon supprimer
            if ($existing->type === 'available') {
                $existing->update(['type' => 'unavailable']);
                Notification::make()
                    ->title('Marque comme indisponible')
                    ->warning()
                    ->send();
            } else {
                $existing->delete();
                Notification::make()
                    ->title('Disponibilite supprimee')
                    ->success()
                    ->send();
            }
        } else {
            // Creer une nouvelle disponibilite
            CourseAvailabilityModel::create([
                'loueur_id' => $loueur->id,
                'type' => 'available',
                'for_transfer' => $loueur->offers_transfer,
                'for_delivery' => $loueur->offers_delivery,
                'date' => $dateObj,
                'recurrence' => 'none',
            ]);
            Notification::make()
                ->title('Disponible ce jour')
                ->success()
                ->send();
        }
    }

    /**
     * Supprimer une disponibilite
     */
    public function deleteAvailability(int $id): void
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) return;

        CourseAvailabilityModel::where('id', $id)
            ->where('loueur_id', $loueur->id)
            ->delete();

        Notification::make()
            ->title('Disponibilite supprimee')
            ->success()
            ->send();
    }

    /**
     * Formulaire pour ajouter une disponibilite avancee
     */
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('type')
                ->label('Type')
                ->options(CourseAvailabilityModel::TYPES)
                ->default('available')
                ->required(),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Toggle::make('for_transfer')
                        ->label('Transferts')
                        ->default(true),

                    Forms\Components\Toggle::make('for_delivery')
                        ->label('Livraisons')
                        ->default(true),
                ]),

            Forms\Components\DatePicker::make('date')
                ->label('Date de debut')
                ->required()
                ->default(now()),

            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TimePicker::make('start_time')
                        ->label('Heure debut')
                        ->seconds(false),

                    Forms\Components\TimePicker::make('end_time')
                        ->label('Heure fin')
                        ->seconds(false),
                ]),

            Forms\Components\Select::make('recurrence')
                ->label('Recurrence')
                ->options(CourseAvailabilityModel::RECURRENCES)
                ->default('none')
                ->live(),

            Forms\Components\CheckboxList::make('recurrence_days')
                ->label('Jours de la semaine')
                ->options(CourseAvailabilityModel::DAYS_OF_WEEK)
                ->columns(4)
                ->visible(fn (Forms\Get $get) => $get('recurrence') === 'weekly'),

            Forms\Components\DatePicker::make('recurrence_end')
                ->label('Fin de recurrence')
                ->visible(fn (Forms\Get $get) => $get('recurrence') !== 'none'),

            Forms\Components\Textarea::make('notes')
                ->label('Notes')
                ->rows(2),
        ];
    }

    /**
     * Soumettre le formulaire
     */
    public function createAvailability(): void
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) return;

        $data = $this->form->getState();

        CourseAvailabilityModel::create([
            'loueur_id' => $loueur->id,
            'type' => $data['type'],
            'for_transfer' => $data['for_transfer'],
            'for_delivery' => $data['for_delivery'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'recurrence' => $data['recurrence'],
            'recurrence_days' => $data['recurrence_days'],
            'recurrence_end' => $data['recurrence_end'],
            'notes' => $data['notes'],
        ]);

        $this->form->fill();

        Notification::make()
            ->title('Disponibilite ajoutee')
            ->success()
            ->send();
    }

    public function getViewData(): array
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) {
            return ['days' => [], 'availabilities' => collect(), 'availabilityMap' => []];
        }

        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Recuperer toutes les disponibilites
        $availabilities = CourseAvailabilityModel::where('loueur_id', $loueur->id)
            ->where('is_active', true)
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($endOfMonth) {
                        $q->where('recurrence', '!=', 'none')
                            ->where('date', '<=', $endOfMonth)
                            ->where(function ($sq) use ($endOfMonth) {
                                $sq->whereNull('recurrence_end')
                                    ->orWhere('recurrence_end', '>=', $endOfMonth->copy()->startOfMonth());
                            });
                    });
            })
            ->orderBy('date')
            ->get();

        // Construire la carte des disponibilites par jour
        $availabilityMap = [];
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $dayAvailabilities = [];

            foreach ($availabilities as $availability) {
                if ($availability->appliesTo($date)) {
                    $dayAvailabilities[] = $availability;
                }
            }

            if (!empty($dayAvailabilities)) {
                // Determiner le statut global du jour
                $hasAvailable = collect($dayAvailabilities)->contains('type', 'available');
                $hasUnavailable = collect($dayAvailabilities)->contains('type', 'unavailable');

                $availabilityMap[$key] = [
                    'status' => $hasUnavailable ? 'unavailable' : ($hasAvailable ? 'available' : 'none'),
                    'items' => $dayAvailabilities,
                ];
            }
        }

        // Generer les jours du mois
        $days = [];
        foreach ($period as $date) {
            $days[] = $date;
        }

        // Liste des disponibilites recurrentes
        $recurringAvailabilities = $availabilities->where('recurrence', '!=', 'none');

        return [
            'days' => $days,
            'availabilityMap' => $availabilityMap,
            'recurringAvailabilities' => $recurringAvailabilities,
            'currentMonth' => $this->currentMonth,
            'currentYear' => $this->currentYear,
            'monthName' => $startOfMonth->translatedFormat('F Y'),
            'today' => now()->format('Y-m-d'),
            'offersTransfer' => $loueur->offers_transfer,
            'offersDelivery' => $loueur->offers_delivery,
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) return false;

        // Afficher seulement si le loueur propose des courses (pas un taxi)
        return !$loueur->isTaxi() && ($loueur->offers_transfer || $loueur->offers_delivery);
    }
}
