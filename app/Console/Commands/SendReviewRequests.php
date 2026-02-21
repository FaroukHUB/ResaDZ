<?php

namespace App\Console\Commands;

use App\Mail\ReviewRequestMail;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReviewRequests extends Command
{
    protected $signature = 'reviews:send-requests
                            {--days=2 : Days after booking completion to send request}
                            {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Send review request emails to clients after completed bookings';

    public function handle(): int
    {
        $daysAfter = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        // Find bookings completed X days ago that haven't been reviewed
        $targetDate = Carbon::now()->subDays($daysAfter)->startOfDay();
        $endDate = Carbon::now()->subDays($daysAfter)->endOfDay();

        $this->info("Looking for bookings completed on: {$targetDate->format('d/m/Y')}");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No emails will be sent');
        }

        $bookings = Booking::with(['loueur', 'vehicle', 'client'])
            ->where('status', 'completed')
            ->where('client_reviewed', false)
            ->whereBetween('end_date', [$targetDate, $endDate])
            ->whereNotNull('client_email')
            ->get();

        $this->info("Found {$bookings->count()} bookings to send review requests for");

        $sent = 0;
        $failed = 0;

        foreach ($bookings as $booking) {
            $this->line("  Processing: {$booking->reference} - {$booking->client_email}");

            if ($dryRun) {
                $sent++;
                continue;
            }

            try {
                Mail::to($booking->client_email)
                    ->send(new ReviewRequestMail($booking));

                $sent++;
                $this->info("    ✓ Email sent to {$booking->client_email}");

            } catch (\Exception $e) {
                $failed++;
                $this->error("    ✗ Failed: {$e->getMessage()}");
                Log::warning("Failed to send review request for booking {$booking->id}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("=== Summary ===");
        $this->info("Emails sent: {$sent}");
        if ($failed > 0) {
            $this->error("Failed: {$failed}");
        }

        return Command::SUCCESS;
    }
}
