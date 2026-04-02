<?php

namespace App\Console\Commands;

use App\Models\Loueur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLoueurReminders extends Command
{
    protected $signature = 'loueurs:send-reminders {--dry-run : Afficher sans envoyer}';

    protected $description = 'Envoyer des emails de relance aux loueurs sans véhicule actif';

    private const REMINDERS = [
        1 => [
            'days' => 1,
            'subject' => 'Ajoutez votre premier véhicule en 5 minutes',
            'body' => 'Votre espace loueur est prêt ! Il ne vous reste plus qu\'à ajouter votre premier véhicule pour commencer à recevoir des réservations. C\'est rapide — 5 minutes suffisent.',
        ],
        2 => [
            'days' => 3,
            'subject' => 'Vous n\'avez pas encore ajouté de véhicule — besoin d\'aide ?',
            'body' => 'On a remarqué que vous n\'avez pas encore publié de véhicule sur votre espace. Si vous rencontrez une difficulté ou si vous avez une question, on est là pour vous aider.',
        ],
        3 => [
            'days' => 7,
            'subject' => 'Votre espace loueur vous attend',
            'body' => 'Ça fait déjà une semaine que votre espace loueur est créé. Vos futurs clients sont sur ResaDZ — ajoutez votre premier véhicule et commencez à recevoir des demandes.',
        ],
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $sent = 0;
        $skipped = 0;

        foreach (self::REMINDERS as $step => $config) {
            $loueurs = Loueur::where('is_active', true)
                ->where('account_type', 'loueur')
                ->where('reminder_step', $step - 1)
                ->whereDoesntHave('vehicles', fn ($q) => $q->where('is_active', true))
                ->whereHas('user')
                ->whereRaw('DATEDIFF(NOW(), created_at) >= ?', [$config['days']])
                ->get();

            foreach ($loueurs as $loueur) {
                $email = $loueur->user->email ?? null;
                if (!$email) {
                    $skipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->line("  [DRY] Step {$step} → {$loueur->company_name} ({$email})");
                    $sent++;
                    continue;
                }

                try {
                    Mail::send('emails.reminder-add-vehicle', [
                        'userName' => $loueur->user->name ?? $loueur->company_name,
                        'subjectLine' => $config['subject'],
                        'bodyText' => $config['body'],
                        'step' => $step,
                    ], function ($message) use ($email, $config) {
                        $message->to($email);
                        $message->subject($config['subject'] . ' — ResaDZ');
                    });

                    $loueur->update(['reminder_step' => $step]);
                    $sent++;

                } catch (\Exception $e) {
                    Log::warning('SendLoueurReminders: email failed', [
                        'loueur_id' => $loueur->id,
                        'step' => $step,
                        'error' => $e->getMessage(),
                    ]);
                    $skipped++;
                }
            }
        }

        $this->info("Résumé : {$sent} email(s) envoyé(s), {$skipped} ignoré(s)");

        return self::SUCCESS;
    }
}
