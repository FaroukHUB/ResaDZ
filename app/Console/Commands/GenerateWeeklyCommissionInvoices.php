<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\DeliveryBooking;
use App\Models\Invoice;
use App\Models\Loueur;
use App\Models\Setting;
use App\Models\TransferBooking;
use App\Notifications\InvoiceSentNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateWeeklyCommissionInvoices extends Command
{
    protected $signature = 'invoices:generate-weekly
                            {--week= : Week to generate for (format: YYYY-WXX, e.g. 2026-W12, defaults to previous week)}
                            {--loueur= : Generate for specific loueur ID only}
                            {--dry-run : Show what would be generated without actually creating invoices}';

    protected $description = 'Generate weekly commission invoices for all loueurs (locations, transferts, livraisons)';

    public function handle(): int
    {
        $weekParam = $this->option('week');
        $loueurId = $this->option('loueur');
        $dryRun = $this->option('dry-run');

        // Determine the week to process (Monday to Sunday)
        if ($weekParam) {
            // Parse YYYY-WXX format
            $targetMonday = Carbon::now()->setISODate(
                (int) substr($weekParam, 0, 4),
                (int) substr($weekParam, 6)
            )->startOfDay();
        } else {
            // Previous week: last Monday to last Sunday
            $targetMonday = Carbon::now()->previous(Carbon::MONDAY)->startOfDay();
        }

        $startDate = $targetMonday->copy();
        $endDate = $targetMonday->copy()->addDays(6)->endOfDay(); // Sunday 23:59:59

        $weekNumber = $startDate->isoWeek();
        $weekYear = $startDate->isoWeekYear();

        $this->info("Generating commission invoices for week {$weekYear}-W{$weekNumber}");
        $this->info("Period: {$startDate->format('d/m/Y')} (Lun) - {$endDate->format('d/m/Y')} (Dim)");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No invoices will be created');
        }

        // Get loueurs to process (both loueurs and taxis)
        $loueursQuery = Loueur::query()->where('is_active', true);
        if ($loueurId) {
            $loueursQuery->where('id', $loueurId);
        }
        $loueurs = $loueursQuery->get();

        $this->info("Processing {$loueurs->count()} loueurs/chauffeurs...");

        $invoicesGenerated = 0;
        $totalCommission = 0;

        foreach ($loueurs as $loueur) {
            $result = $this->processLoueur($loueur, $startDate, $endDate, $weekYear, $weekNumber, $dryRun);

            if ($result) {
                $invoicesGenerated++;
                $totalCommission += $result['commission'];
            }
        }

        $this->newLine();
        $this->info("=== Résumé ===");
        $this->info("Factures générées: {$invoicesGenerated}");
        $this->info("Commission totale: " . number_format($totalCommission, 2) . " DZD");

        return Command::SUCCESS;
    }

    protected function processLoueur(Loueur $loueur, Carbon $startDate, Carbon $endDate, int $weekYear, int $weekNumber, bool $dryRun): ?array
    {
        $items = [];
        $totalCommission = 0;

        // 1. Location véhicules (Booking) — completed, commission impayée, end_date dans la semaine
        $bookings = Booking::where('loueur_id', $loueur->id)
            ->where('status', 'completed')
            ->where('commission_paid', false)
            ->where('commission_amount', '>', 0)
            ->whereBetween('end_date', [$startDate, $endDate])
            ->get();

        if ($bookings->isNotEmpty()) {
            $bookingCommission = $bookings->sum('commission_amount');
            $bookingRevenue = $bookings->sum('total_price');
            $avgRate = $bookingRevenue > 0 ? round(($bookingCommission / $bookingRevenue) * 100, 1) : 0;
            $totalCommission += $bookingCommission;

            $items[] = [
                'type' => 'commission_location',
                'description' => "Locations véhicules ({$bookings->count()}) — Semaine {$weekNumber}\nCA: " . number_format($bookingRevenue, 0, ',', ' ') . " DA — Taux moyen: {$avgRate}%",
                'amount' => $bookingCommission,
                'models' => $bookings,
            ];
        }

        // 2. Transferts (TransferBooking) — completed, commission impayée, transfer_date dans la semaine
        $transfers = TransferBooking::where('loueur_id', $loueur->id)
            ->where('status', 'completed')
            ->where('commission_paid', false)
            ->where('commission_amount', '>', 0)
            ->whereBetween('transfer_date', [$startDate, $endDate])
            ->get();

        if ($transfers->isNotEmpty()) {
            $transferCommission = $transfers->sum('commission_amount');
            $transferRevenue = $transfers->sum('price');
            $totalCommission += $transferCommission;

            $items[] = [
                'type' => 'commission_transfert',
                'description' => "Transferts ({$transfers->count()}) — Semaine {$weekNumber}\nCA: " . number_format($transferRevenue, 0, ',', ' ') . " DA — Commission: 10%",
                'amount' => $transferCommission,
                'models' => $transfers,
            ];
        }

        // 3. Livraisons (DeliveryBooking) — delivered, commission impayée, pickup_date dans la semaine
        $deliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->where('status', 'delivered')
            ->where('commission_paid', false)
            ->where('commission_amount', '>', 0)
            ->whereBetween('pickup_date', [$startDate, $endDate])
            ->get();

        if ($deliveries->isNotEmpty()) {
            $deliveryCommission = $deliveries->sum('commission_amount');
            $deliveryRevenue = $deliveries->sum('price');
            $totalCommission += $deliveryCommission;

            $items[] = [
                'type' => 'commission_livraison',
                'description' => "Livraisons ({$deliveries->count()}) — Semaine {$weekNumber}\nCA: " . number_format($deliveryRevenue, 0, ',', ' ') . " DA — Commission: 10%",
                'amount' => $deliveryCommission,
                'models' => $deliveries,
            ];
        }

        // Nothing to invoice
        if (empty($items)) {
            return null;
        }

        $this->info("  [{$loueur->company_name}]");
        foreach ($items as $item) {
            $this->line("    - {$item['type']}: " . number_format($item['amount'], 2) . " DZD ({$item['models']->count()} opérations)");
        }
        $this->line("    - Total commission: " . number_format($totalCommission, 2) . " DZD");

        if ($dryRun) {
            return ['commission' => $totalCommission];
        }

        // Create the invoice
        $weekLabel = "S{$weekNumber} ({$startDate->format('d/m')} - {$endDate->format('d/m/Y')})";
        $invoiceNumber = sprintf("FAC-%d-S%02d-%04d", $weekYear, $weekNumber, $this->getNextSequence($weekYear, $weekNumber));

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'loueur_id' => $loueur->id,
            'status' => Invoice::STATUS_SENT,
            'issue_date' => now(),
            'due_date' => now()->addDays((int) Setting::get('commission_invoice_due_days', 7)),
            'billing_name' => $loueur->company_name,
            'billing_address' => $loueur->address,
            'billing_city' => $loueur->city,
            'billing_phone' => $loueur->phone,
            'billing_email' => $loueur->user->email ?? null,
            'notes' => "Commissions — {$weekLabel}",
            'sent_at' => now(),
        ]);

        // Add line items per category
        foreach ($items as $item) {
            $invoice->addItem(
                description: $item['description'],
                unitPrice: $item['amount'],
                quantity: 1,
                type: $item['type']
            );
        }

        $invoice->calculateTotals();

        // Send notification
        try {
            if ($loueur->user) {
                $loueur->user->notify(new InvoiceSentNotification($invoice));
            }
        } catch (\Exception $e) {
            Log::warning("Failed to send invoice notification to loueur {$loueur->id}: " . $e->getMessage());
        }

        $this->line("    => Facture #{$invoiceNumber} créée");

        return [
            'invoice' => $invoice,
            'commission' => $totalCommission,
        ];
    }

    /**
     * Get next sequence number for a given week.
     */
    protected function getNextSequence(int $year, int $week): int
    {
        $prefix = sprintf("FAC-%d-S%02d-", $year, $week);

        $lastInvoice = Invoice::where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            return (int) substr($lastInvoice->invoice_number, -4) + 1;
        }

        return 1;
    }
}
