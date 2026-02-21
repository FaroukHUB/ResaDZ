<?php

namespace App\Filament\Admin\Resources\InvoiceResource\Pages;

use App\Filament\Admin\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected static string $view = 'filament.admin.pages.view-invoice';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('download')
                ->label('Télécharger PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => route('admin.invoices.pdf', $this->record))
                ->openUrlInNewTab(),

            Actions\Action::make('send')
                ->label('Envoyer par email')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'draft')
                ->action(function () {
                    $this->record->markAsSent();

                    if ($this->record->loueur->user) {
                        $this->record->loueur->user->notify(
                            new \App\Notifications\InvoiceSentNotification($this->record)
                        );
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Facture envoyée')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getViewData(): array
    {
        return [
            'invoice' => $this->record,
            'items' => $this->record->items,
            'loueur' => $this->record->loueur,
        ];
    }
}
