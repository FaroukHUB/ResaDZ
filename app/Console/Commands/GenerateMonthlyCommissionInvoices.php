<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Loueur;
use App\Notifications\InvoiceSentNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyCommissionInvoices extends Command
{
    protected $signature = 'invoices:generate-monthly
                            {--month= : Month to generate for (format: YYYY-MM, defaults to previous month)}
                            {--loueur= : Generate for specific loueur ID only}
                            {--dry-run : Show what would be generated without actually creating invoices}';

    protected $description = 'Generate monthly commission invoices for all loueurs based on completed bookings';

    public function handle(): int
    {
        $monthParam = $this->option('month');
        $loueurId = $this->option('loueur');
        $dryRun = $this->option('dry-run');

        // Determine the month to process
        if ($monthParam) {
            $targetMonth = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
        } else {
            $targetMonth = Carbon::now()->subMonth()->startOfMonth();
        }

        $startDate = $targetMonth->copy()->startOfMonth();
        $endDate = $targetMonth->copy()->endOfMonth();

        $this->info("Generating commission invoices for: {$targetMonth->format('F Y')}");
        $this->info("Period: {$startDate->format('d/m/Y')} - {$endDate->format('d/m/Y')}");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No invoices will be created');
        }

        // Get loueurs to process
        $loueursQuery = Loueur::query()->where('status', 'active');
        if ($loueurId) {
            $loueursQuery->where('id', $loueurId);
        }
        $loueurs = $loueursQuery->get();

        $this->info("Processing {$loueurs->count()} loueurs...");

        $invoicesGenerated = 0;
        $totalCommission = 0;

        foreach ($loueurs as $loueur) {
            $result = $this->processLoueur($loueur, $startDate, $endDate, $dryRun);

            if ($result) {
                $invoicesGenerated++;
                $totalCommission += $result['commission'];
            }
        }

        $this->newLine();
        $this->info("=== Summary ===");
        $this->info("Invoices generated: {$invoicesGenerated}");
        $this->info("Total commission: " . number_format($totalCommission, 2) . " DZD");

        return Command::SUCCESS;
    }

    protected function processLoueur(Loueur $loueur, Carbon $startDate, Carbon $endDate, bool $dryRun): ?array
    {
        // Get completed bookings for this loueur in the period with unpaid commission
        $bookings = Booking::where('loueur_id', $loueur->id)
            ->where('status', 'completed')
            ->where('commission_paid', false)
            ->where('commission_amount', '>', 0)
            ->whereBetween('end_date', [$startDate, $endDate])
            ->get();

        if ($bookings->isEmpty()) {
            $this->line("  [{$loueur->company_name}] No unpaid commissions for this period");
            return null;
        }

        $totalBookings = $bookings->sum('total_price');
        $totalCommission = $bookings->sum('commission_amount');
        $avgRate = $bookings->avg('commission_rate');

        $this->info("  [{$loueur->company_name}]");
        $this->line("    - Bookings: {$bookings->count()}");
        $this->line("    - Total revenue: " . number_format($totalBookings, 2) . " DZD");
        $this->line("    - Commission ({$avgRate}%): " . number_format($totalCommission, 2) . " DZD");

        if ($dryRun) {
            return ['commission' => $totalCommission];
        }

        // Create the invoice
        $invoice = Invoice::create([
            'loueur_id' => $loueur->id,
            'status' => Invoice::STATUS_SENT,
            'issue_date' => now(),
            'due_date' => now()->addDays(15),
            'billing_name' => $loueur->company_name,
            'billing_address' => $loueur->address,
            'billing_city' => $loueur->city,
            'billing_phone' => $loueur->phone,
            'billing_email' => $loueur->user->email ?? null,
            'notes' => "Commission sur les locations - {$startDate->format('F Y')}",
            'sent_at' => now(),
        ]);

        // Add summary line item
        $invoice->addItem(
            description: "Commission sur {$bookings->count()} location(s) - {$startDate->format('F Y')}\nTotal des locations: " . number_format($totalBookings, 2) . " DZD\nTaux moyen: {$avgRate}%",
            unitPrice: $totalCommission,
            quantity: 1,
            type: 'commission'
        );

        // Add individual booking details as separate items (optional, for transparency)
        foreach ($bookings as $booking) {
            $invoice->addItem(
                description: "Location #{$booking->reference} - {$booking->vehicle->full_name ?? 'Véhicule'}\n{$booking->start_date->format('d/m/Y')} - {$booking->end_date->format('d/m/Y')}\nMontant: " . number_format($booking->total_price, 2) . " DZD × {$booking->commission_rate}%",
                unitPrice: $booking->commission_amount,
                quantity: 1,
                type: 'commission_detail',
                itemable: $booking
            );

            // Mark commission as invoiced (not paid yet)
            $booking->update(['commission_paid' => false]); // Keep false until actually paid
        }

        // Recalculate totals (the individual items detail, but we charge the summary amount)
        // We need to adjust - remove detail items from total, keep only the summary
        $invoice->items()->where('type', 'commission_detail')->delete();
        $invoice->calculateTotals();

        // Send notification
        try {
            if ($loueur->user) {
                $loueur->user->notify(new InvoiceSentNotification($invoice));
            }
        } catch (\Exception $e) {
            Log::warning("Failed to send invoice notification to loueur {$loueur->id}: " . $e->getMessage());
        }

        $this->line("    - Invoice #{$invoice->invoice_number} created");

        return [
            'invoice' => $invoice,
            'commission' => $totalCommission,
        ];
    }
}
