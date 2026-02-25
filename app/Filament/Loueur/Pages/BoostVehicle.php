<?php

namespace App\Filament\Loueur\Pages;

use App\Models\BoostPackage;
use App\Models\Vehicle;
use App\Models\VehicleBoost;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class BoostVehicle extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $navigationLabel = 'Booster un véhicule';
    protected static ?string $title = 'Booster un véhicule';
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;
        return $loueur && $loueur->isLoueur();
    }

    protected static string $view = 'filament.loueur.pages.boost-vehicle';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $loueur = Auth::user()->loueur;

        return $form
            ->schema([
                Forms\Components\Section::make('Choisir un véhicule')
                    ->description('Sélectionnez le véhicule que vous souhaitez mettre en avant')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Véhicule')
                            ->options(function () use ($loueur) {
                                return Vehicle::where('loueur_id', $loueur->id)
                                    ->where('is_active', true)
                                    ->where('status', 'available')
                                    ->get()
                                    ->mapWithKeys(function ($vehicle) {
                                        $boosted = $vehicle->is_boosted ? ' (déjà boosté)' : '';
                                        return [$vehicle->id => $vehicle->brand->name . ' ' . $vehicle->model . ' - ' . $vehicle->price_per_day . ' DA/jour' . $boosted];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->helperText('Les véhicules déjà boostés peuvent être boostés à nouveau (le boost sera prolongé)'),
                    ]),

                Forms\Components\Section::make('Choisir un pack')
                    ->description('Sélectionnez la durée du boost')
                    ->schema([
                        Forms\Components\Radio::make('boost_package_id')
                            ->label('')
                            ->options(function () {
                                return BoostPackage::active()
                                    ->ordered()
                                    ->get()
                                    ->mapWithKeys(function ($package) {
                                        return [
                                            $package->id => $package->name . ' - ' . $package->duration_days . ' jours - ' . $package->formatted_price,
                                        ];
                                    });
                            })
                            ->descriptions(function () {
                                return BoostPackage::active()
                                    ->ordered()
                                    ->get()
                                    ->mapWithKeys(function ($package) {
                                        return [
                                            $package->id => $package->description ?? 'Votre véhicule sera mis en avant pendant ' . $package->duration_days . ' jours',
                                        ];
                                    });
                            })
                            ->required(),
                    ]),

                Forms\Components\Section::make('Mode de paiement')
                    ->description('Comment souhaitez-vous payer ?')
                    ->schema([
                        Forms\Components\Radio::make('payment_method')
                            ->label('')
                            ->options([
                                'cash' => 'Espèces (paiement en main propre)',
                                'paypal' => 'PayPal (paiement en ligne)',
                            ])
                            ->descriptions([
                                'cash' => 'Le boost sera activé après validation du paiement par notre équipe',
                                'paypal' => 'Le boost sera activé immédiatement après le paiement',
                            ])
                            ->required()
                            ->default('cash'),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $loueur = Auth::user()->loueur;

        $vehicle = Vehicle::where('id', $data['vehicle_id'])
            ->where('loueur_id', $loueur->id)
            ->firstOrFail();

        $package = BoostPackage::active()->findOrFail($data['boost_package_id']);

        if ($data['payment_method'] === 'paypal') {
            // Redirect to PayPal
            $this->redirectToPayPal($vehicle, $package);
            return;
        }

        // Cash payment - create pending boost
        $boost = VehicleBoost::create([
            'vehicle_id' => $vehicle->id,
            'loueur_id' => $loueur->id,
            'boost_package_id' => $package->id,
            'amount_paid' => $package->price,
            'payment_method' => 'cash',
            'status' => 'pending',
        ]);

        Notification::make()
            ->title('Demande de boost envoyée')
            ->body('Votre demande de boost pour "' . $vehicle->brand->name . ' ' . $vehicle->model . '" a été enregistrée. Le boost sera activé après réception du paiement de ' . $package->formatted_price . '.')
            ->success()
            ->persistent()
            ->send();

        $this->form->fill();
    }

    protected function redirectToPayPal(Vehicle $vehicle, BoostPackage $package): void
    {
        $loueur = Auth::user()->loueur;

        // Create pending boost first
        $boost = VehicleBoost::create([
            'vehicle_id' => $vehicle->id,
            'loueur_id' => $loueur->id,
            'boost_package_id' => $package->id,
            'amount_paid' => $package->price,
            'payment_method' => 'paypal',
            'status' => 'pending_payment',
        ]);

        // Store boost ID in session for callback
        session(['pending_boost_id' => $boost->id]);

        // Redirect to PayPal payment route
        $this->redirect(route('boost.paypal.create', ['boost' => $boost->id]));
    }

    public function getActiveBoostsProperty()
    {
        $loueur = Auth::user()->loueur;

        return VehicleBoost::with(['vehicle.brand', 'boostPackage'])
            ->where('loueur_id', $loueur->id)
            ->where('status', 'active')
            ->where('ends_at', '>=', now())
            ->get();
    }

    public function getPendingBoostsProperty()
    {
        $loueur = Auth::user()->loueur;

        return VehicleBoost::with(['vehicle.brand', 'boostPackage'])
            ->where('loueur_id', $loueur->id)
            ->where('status', 'pending')
            ->get();
    }

    public function getAvailablePackagesProperty()
    {
        return BoostPackage::active()->ordered()->get();
    }
}
