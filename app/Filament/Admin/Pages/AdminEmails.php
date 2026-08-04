<?php

namespace App\Filament\Admin\Pages;

use App\Models\AdminEmail;
use App\Models\Loueur;
use App\Models\Setting;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminEmails extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?string $navigationLabel = 'Emails / WhatsApp';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.admin.pages.admin-emails';

    public string $activeTab = 'templates';

    // Template form
    public string $selectedTemplate = '';
    public string $recipientType = 'loueur';
    public string $selectedLoueurId = '';
    public string $customPhone = '';
    public string $prefillLoueurId = '';
    public string $selectedClientEmail = '';

    // Onglet Clients
    public string $clientSelectedTemplate = '';
    public string $clientRecipientType = 'client';
    public string $clientCustomEmail = '';
    public string $clientCustomPhone = '';
    public string $clientPreviewSubject = '';
    public string $clientPreviewBody = '';
    public string $customEmail = '';
    public string $previewSubject = '';
    public string $previewBody = '';

    // AI form
    public string $aiPrompt = '';
    public string $aiRecipientType = 'loueur';
    public string $aiSelectedLoueurId = '';
    public string $aiCustomEmail = '';
    public string $aiSubject = '';
    public string $aiBody = '';
    public bool $aiLoading = false;

    public static function getTemplates(): array
    {
        return [
            'relance_paiement' => [
                'label' => 'Relance paiement commission',
                'subject' => 'Rappel : Commission ResaDZ en attente',
                'body' => "Salam {nom},\n\nNous vous rappelons qu'une commission ResaDZ est en attente de règlement pour vos dernières réservations.\n\nMerci de procéder au paiement dans les plus brefs délais afin de maintenir votre compte actif.\n\nVous pouvez consulter le détail de vos commissions dans votre espace loueur → Finances.\n\nPour toute question, n'hésitez pas à nous contacter.\n\nCordialement,\nL'équipe ResaDZ",
            ],
            'loueur_inactif' => [
                'label' => 'Relance loueur inactif (pas de véhicule)',
                'subject' => 'Votre compte ResaDZ attend vos véhicules !',
                'body' => "Salam {nom},\n\nNous avons remarqué que vous n'avez pas encore publié de véhicule sur ResaDZ.\n\nPublier un véhicule est gratuit et ne prend que 5 minutes. Des clients cherchent déjà des véhicules dans votre wilaya !\n\nPour ajouter votre premier véhicule :\n1. Connectez-vous sur resadz.com/loueur\n2. Allez dans Catalogue → Véhicules → Créer\n3. Remplissez les infos et publiez\n\nSi vous avez besoin d'aide, notre assistant Résabot est disponible 24/7 dans votre espace.\n\nÀ très vite,\nL'équipe ResaDZ",
            ],
            'avertissement_suppression' => [
                'label' => 'Avertissement suppression compte 24h',
                'subject' => 'Action requise : votre compte ResaDZ sera désactivé dans 24h',
                'body' => "Salam {nom},\n\nVotre compte ResaDZ est inactif depuis votre inscription — aucun véhicule n'a été publié.\n\nSans action de votre part dans les prochaines 24 heures, votre compte sera automatiquement désactivé.\n\nPour garder votre compte actif, il vous suffit de publier au moins un véhicule.\n\nSi vous rencontrez des difficultés, contactez-nous directement sur WhatsApp — nous sommes là pour vous aider.\n\nCordialement,\nL'équipe ResaDZ",
            ],
            'bienvenue_personnalise' => [
                'label' => 'Bienvenue personnalisé',
                'subject' => 'Bienvenue sur ResaDZ — On est là pour vous aider !',
                'body' => "Salam {nom},\n\nMerci d'avoir rejoint ResaDZ ! Nous sommes ravis de vous compter parmi nos partenaires.\n\nVotre espace loueur est prêt. Voici les prochaines étapes :\n• Complétez votre profil (logo, description, coordonnées)\n• Ajoutez vos véhicules avec de belles photos\n• Configurez vos tarifs et disponibilités\n\nSi vous avez la moindre question, notre assistant Résabot est disponible directement dans votre panel. Vous pouvez aussi nous contacter sur WhatsApp.\n\nBonne route sur ResaDZ !\nL'équipe ResaDZ",
            ],
            'rappel_calendrier' => [
                'label' => 'Rappel mise à jour calendrier',
                'subject' => 'Pensez à mettre à jour votre calendrier ResaDZ',
                'body' => "Salam {nom},\n\nPetit rappel : pensez à mettre à jour la disponibilité de vos véhicules sur ResaDZ.\n\nUn calendrier à jour évite les conflits de réservation et améliore votre visibilité auprès des clients.\n\nPour bloquer des dates : Calendrier → sélectionnez un véhicule → cliquez sur les dates à bloquer.\n\nMerci et bonne continuation,\nL'équipe ResaDZ",
            ],
            'demande_documents' => [
                'label' => 'Demande de documents',
                'subject' => 'Documents requis pour votre compte ResaDZ',
                'body' => "Salam {nom},\n\nAfin de finaliser la vérification de votre compte, nous avons besoin des documents suivants :\n• Copie de votre pièce d'identité (CNI ou passeport)\n• Registre de commerce ou attestation d'activité\n\nVous pouvez nous envoyer ces documents en répondant à cet email ou via WhatsApp.\n\nMerci de votre coopération,\nL'équipe ResaDZ",
            ],
            'felicitations_reservation' => [
                'label' => 'Félicitations première réservation',
                'subject' => 'Bravo ! Votre première réservation sur ResaDZ 🎉',
                'body' => "Salam {nom},\n\nFélicitations ! Vous venez de recevoir votre première réservation sur ResaDZ !\n\nC'est le début d'une belle aventure. Voici quelques conseils pour bien démarrer :\n• Répondez rapidement au client (idéalement dans l'heure)\n• Préparez le véhicule (nettoyage, plein, vérifications)\n• Gardez votre calendrier à jour pour éviter les doublons\n\nN'hésitez pas à nous contacter si vous avez des questions.\n\nBonne route,\nL'équipe ResaDZ",
            ],
            'promotion' => [
                'label' => 'Annonce promotion/nouveauté',
                'subject' => 'Nouveau sur ResaDZ — Ne manquez pas !',
                'body' => "Salam {nom},\n\nNous avons une nouveauté sur ResaDZ qui pourrait vous intéresser !\n\n[Décrivez la nouveauté ici]\n\nPour en profiter, connectez-vous sur votre espace loueur : resadz.com/loueur\n\nBonne continuation,\nL'équipe ResaDZ",
            ],
            'reset_password' => [
                'label' => 'Réinitialisation mot de passe',
                'subject' => 'Réinitialisez votre mot de passe ResaDZ',
                'body' => "Salam {nom},\n\nVous avez demandé la réinitialisation de votre mot de passe ResaDZ (ou notre équipe l'a fait pour vous).\n\nCliquez sur le lien ci-dessous pour créer un nouveau mot de passe :\n\n{reset_link}\n\nCe lien est valable 60 minutes. Passé ce délai, vous devrez refaire une demande.\n\nSi vous n'avez pas demandé cette réinitialisation, ignorez cet email — votre mot de passe actuel reste inchangé.\n\nCordialement,\nL'équipe ResaDZ",
            ],
        ];
    }

    public function updatedPrefillLoueurId(): void
    {
        if (!$this->prefillLoueurId) {
            return;
        }

        $loueur = Loueur::with('user')->find($this->prefillLoueurId);
        if ($loueur) {
            $this->customEmail = $loueur->user?->email ?? '';
            $this->customPhone = $loueur->whatsapp ?: ($loueur->phone ?? '');
        }
    }

    public static function getClientTemplates(): array
    {
        return [
            'avis_google_client' => [
                'label' => 'Merci + demande avis Google',
                'subject' => 'Merci d\'avoir choisi ResaDZ ! Votre avis compte',
                'body' => "Salam {nom},\n\nMerci d'avoir choisi ResaDZ pour votre location ! Nous espérons que votre expérience s'est parfaitement déroulée et que le véhicule était à la hauteur de vos attentes.\n\nVotre avis est précieux : il aide d'autres clients à louer en confiance et nous permet de nous améliorer.\n\nNous vous serions reconnaissants de prendre 30 secondes pour laisser un avis ici :\nhttps://www.google.com/maps/place//data=!4m3!3m2!1s0x211686839698b1eb:0x3c296773135ccdf7!12e1?source=g.page.m.nd._&laa=nmx-reviews-dialog\n\nMerci encore pour votre confiance, et à très bientôt sur ResaDZ !\n\nL'équipe ResaDZ",
            ],
            'suivi_reservation' => [
                'label' => 'Suivi après réservation',
                'subject' => 'Votre réservation ResaDZ — tout se passe bien ?',
                'body' => "Salam {nom},\n\nNous espérons que votre location se passe à merveille !\n\nSi vous avez la moindre question ou difficulté (véhicule, contact avec le loueur, prolongation...), répondez simplement à ce message — notre équipe est là pour vous aider.\n\nBonne route,\nL'équipe ResaDZ",
            ],
            'promo_client' => [
                'label' => 'Promotion / offre spéciale',
                'subject' => 'Une offre spéciale pour vous sur ResaDZ',
                'body' => "Salam {nom},\n\nMerci de faire partie des clients ResaDZ !\n\n[Décrivez votre offre ici]\n\nRéservez dès maintenant sur resadz.com et profitez-en.\n\nÀ très bientôt,\nL'équipe ResaDZ",
            ],
        ];
    }

    public function updatedSelectedTemplate(): void
    {
        $templates = static::getTemplates();
        if (isset($templates[$this->selectedTemplate])) {
            $t = $templates[$this->selectedTemplate];
            $this->previewSubject = $t['subject'];
            $name = $this->getRecipientName($this->recipientType, $this->selectedLoueurId);
            $body = str_replace('{nom}', $name, $t['body']);

            // Generate reset link for password reset template
            if ($this->selectedTemplate === 'reset_password' && $this->selectedLoueurId) {
                $email = $this->getRecipientEmail($this->recipientType, $this->selectedLoueurId, $this->customEmail);
                if ($email) {
                    $token = \Illuminate\Support\Str::random(64);
                    \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->delete();
                    \Illuminate\Support\Facades\DB::table('password_reset_tokens')->insert([
                        'email' => $email,
                        'token' => \Illuminate\Support\Facades\Hash::make($token),
                        'created_at' => now(),
                    ]);
                    $resetLink = url('/mot-de-passe/reset/' . $token . '?email=' . urlencode($email));
                    $body = str_replace('{reset_link}', $resetLink, $body);
                } else {
                    $body = str_replace('{reset_link}', '[Sélectionnez un loueur pour générer le lien]', $body);
                }
            }

            $this->previewBody = $body;
        }
    }

    public function updatedSelectedLoueurId(): void
    {
        if ($this->selectedTemplate && $this->selectedLoueurId) {
            $this->updatedSelectedTemplate();
        }
    }

    public function updatedSelectedClientEmail(): void
    {
        if ($this->selectedTemplate && $this->selectedClientEmail) {
            $this->updatedSelectedTemplate();
        }
        if ($this->clientSelectedTemplate && $this->selectedClientEmail) {
            $this->updatedClientSelectedTemplate();
        }
    }

    public function selectTemplate(): void
    {
        $this->updatedSelectedTemplate();
    }

    public function sendTemplate(): void
    {
        $email = $this->getRecipientEmail($this->recipientType, $this->selectedLoueurId, $this->customEmail);
        $name = $this->getRecipientName($this->recipientType, $this->selectedLoueurId);

        if (!$email || !$this->previewSubject || !$this->previewBody) {
            Notification::make()->title('Remplissez tous les champs')->danger()->send();
            return;
        }

        $body = str_replace('{nom}', $name, $this->previewBody);

        $this->sendEmail($email, $name, $this->previewSubject, $body, $this->selectedTemplate);
    }

    public function generateWithAI(): void
    {
        if (!$this->aiPrompt) {
            Notification::make()->title('Décrivez ce que vous voulez dire')->danger()->send();
            return;
        }

        $apiKey = config('services.groq.api_key');
        if (!$apiKey) {
            Notification::make()->title('API Groq non configurée')->danger()->send();
            return;
        }

        $name = $this->getRecipientName($this->aiRecipientType, $this->aiSelectedLoueurId);

        $systemPrompt = "Tu es l'assistant email de ResaDZ. Tu rédiges des emails professionnels mais chaleureux en français. "
            . "Tu utilises 'Salam' pour saluer. Tu tutoies pas, tu vouvoies. Tu signes toujours 'L'équipe ResaDZ'. "
            . "Le destinataire s'appelle {$name}. ResaDZ est une marketplace de location de véhicules en Algérie. "
            . "Génère UNIQUEMENT le sujet et le corps de l'email, séparés par '---'. "
            . "Format: Sujet: [le sujet]\n---\n[le corps de l'email]";

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => "Bearer {$apiKey}"])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $this->aiPrompt],
                    ],
                    'max_tokens' => 800,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                if (str_contains($content, '---')) {
                    [$subjectPart, $bodyPart] = explode('---', $content, 2);
                    $this->aiSubject = trim(str_replace('Sujet:', '', $subjectPart));
                    $this->aiBody = trim($bodyPart);
                } else {
                    $this->aiSubject = 'Email ResaDZ';
                    $this->aiBody = $content;
                }
                Notification::make()->title('Email généré !')->success()->send();
            } else {
                Notification::make()->title('Erreur IA')->danger()->send();
            }
        } catch (\Exception $e) {
            Log::error('AI email generation failed: ' . $e->getMessage());
            Notification::make()->title('Erreur de connexion IA')->danger()->send();
        }
    }

    public function sendAIEmail(): void
    {
        $email = $this->getRecipientEmail($this->aiRecipientType, $this->aiSelectedLoueurId, $this->aiCustomEmail);
        $name = $this->getRecipientName($this->aiRecipientType, $this->aiSelectedLoueurId);

        if (!$email || !$this->aiSubject || !$this->aiBody) {
            Notification::make()->title('Remplissez tous les champs')->danger()->send();
            return;
        }

        $this->sendEmail($email, $name, $this->aiSubject, $this->aiBody, 'ai_generated');
    }

    // ===== Onglet Clients =====

    public function updatedClientSelectedTemplate(): void
    {
        $templates = static::getClientTemplates();
        if (isset($templates[$this->clientSelectedTemplate])) {
            $t = $templates[$this->clientSelectedTemplate];
            $this->clientPreviewSubject = $t['subject'];
            $this->clientPreviewBody = str_replace('{nom}', $this->getClientName() ?: '', $t['body']);
        }
    }

    public function updatedSelectedClientEmailForClientTab(): void
    {
        // hook manuel déclenché depuis updatedSelectedClientEmail
    }

    private function getClientName(): string
    {
        if ($this->clientRecipientType === 'client' && $this->selectedClientEmail) {
            $booking = \App\Models\Booking::where('client_email', $this->selectedClientEmail)->latest()->first();
            return $booking?->client_name ?: '';
        }
        return '';
    }

    public function sendClientTemplate(): void
    {
        $email = $this->clientRecipientType === 'custom'
            ? $this->clientCustomEmail
            : $this->selectedClientEmail;

        if (!$email || !$this->clientPreviewSubject || !$this->clientPreviewBody) {
            Notification::make()->title('Remplissez tous les champs')->danger()->send();
            return;
        }

        $this->sendEmail($email, $this->getClientName() ?: 'Client', $this->clientPreviewSubject, $this->clientPreviewBody, $this->clientSelectedTemplate);
    }

    public function getClientWhatsappUrl(): ?string
    {
        if (!$this->clientPreviewBody) {
            return null;
        }

        $phone = null;
        if ($this->clientRecipientType === 'client' && $this->selectedClientEmail) {
            $booking = \App\Models\Booking::where('client_email', $this->selectedClientEmail)->latest()->first();
            $phone = $booking?->client_whatsapp ?: $booking?->client_phone;
        } elseif ($this->clientCustomPhone) {
            $phone = $this->clientCustomPhone;
        }

        if (!$phone) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '0')) {
            $digits = '213' . substr($digits, 1);
        } elseif (!str_starts_with($digits, '213')) {
            $digits = '213' . $digits;
        }

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode($this->clientPreviewBody);
    }

    /**
     * URL WhatsApp (wa.me) avec le message pré-rempli pour le loueur sélectionné.
     * Retourne null si aucun numéro n'est disponible.
     */
    public function getWhatsappUrl(string $body): ?string
    {
        $phone = null;

        if ($this->recipientType === 'loueur' && $this->selectedLoueurId) {
            $loueur = \App\Models\Loueur::find($this->selectedLoueurId);
            $phone = $loueur?->whatsapp ?: $loueur?->phone;
        } elseif ($this->recipientType === 'client' && $this->selectedClientEmail) {
            $booking = \App\Models\Booking::where('client_email', $this->selectedClientEmail)->latest()->first();
            $phone = $booking?->client_whatsapp ?: $booking?->client_phone;
        } elseif ($this->customPhone) {
            $phone = $this->customPhone;
        }

        if (!$phone) {
            return null;
        }

        // Normalise en format international algérien (213...)
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '0')) {
            $digits = '213' . substr($digits, 1);
        } elseif (!str_starts_with($digits, '213')) {
            $digits = '213' . $digits;
        }

        // Remplace {nom} par le nom du destinataire
        $name = $this->getRecipientName($this->recipientType, $this->selectedLoueurId);
        $body = str_replace('{nom}', $name ?: '', $body);

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode($body);
    }

    /**
     * URL WhatsApp pour l'onglet Rédacteur IA (destinataire IA + corps généré).
     */
    public function getAiWhatsappUrl(): ?string
    {
        if (!$this->aiBody) {
            return null;
        }

        $phone = null;
        if ($this->aiRecipientType === 'loueur' && $this->aiSelectedLoueurId) {
            $loueur = \App\Models\Loueur::find($this->aiSelectedLoueurId);
            $phone = $loueur?->whatsapp ?: $loueur?->phone;
        }

        if (!$phone) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '0')) {
            $digits = '213' . substr($digits, 1);
        } elseif (!str_starts_with($digits, '213')) {
            $digits = '213' . $digits;
        }

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode($this->aiBody);
    }

    private function sendEmail(string $email, string $name, string $subject, string $body, ?string $templateKey = null): void
    {
        try {
            Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)
                    ->subject($subject)
                    ->from(
                        config('resadz_emails.contact.address', config('mail.from.address')),
                        config('resadz_emails.contact.name', config('mail.from.name'))
                    );
            });

            AdminEmail::create([
                'to_email' => $email,
                'to_name' => $name,
                'subject' => $subject,
                'body' => $body,
                'template_key' => $templateKey,
                'status' => 'sent',
                'sent_by' => Auth::id(),
            ]);

            Notification::make()->title('Email envoyé à ' . $email)->success()->send();
            $this->reset(['selectedTemplate', 'previewSubject', 'previewBody', 'customEmail', 'aiPrompt', 'aiSubject', 'aiBody', 'aiCustomEmail']);

        } catch (\Exception $e) {
            Log::error('Admin email failed: ' . $e->getMessage());

            AdminEmail::create([
                'to_email' => $email,
                'to_name' => $name,
                'subject' => $subject,
                'body' => $body,
                'template_key' => $templateKey,
                'status' => 'failed',
                'sent_by' => Auth::id(),
            ]);

            Notification::make()->title('Erreur d\'envoi : ' . $e->getMessage())->danger()->send();
        }
    }

    private function getRecipientEmail(string $type, string $loueurId, string $customEmail = ''): ?string
    {
        if ($type === 'custom') return $customEmail ?: null;
        if ($type === 'client') return $this->selectedClientEmail ?: null;
        if ($type === 'loueur' && $loueurId) {
            $loueur = Loueur::find($loueurId);
            return $loueur?->email_contact ?: $loueur?->user?->email;
        }
        return null;
    }

    private function getRecipientName(string $type, string $loueurId): string
    {
        if ($type === 'loueur' && $loueurId) {
            return Loueur::find($loueurId)?->company_name ?? 'loueur';
        }
        if ($type === 'client' && $this->selectedClientEmail) {
            $booking = \App\Models\Booking::where('client_email', $this->selectedClientEmail)->latest()->first();
            return $booking?->client_name ?: 'client';
        }
        return 'client';
    }

    public function getViewData(): array
    {
        return [
            'templates' => static::getTemplates(),
            'clientTemplates' => static::getClientTemplates(),
            'loueurs' => Loueur::where('is_active', true)->orderBy('company_name')->get(['id', 'company_name']),
            'clients' => \App\Models\Booking::whereNotNull('client_email')->where('client_email', '!=', '')
                ->orderByDesc('created_at')
                ->get(['client_name', 'client_email'])
                ->unique('client_email')
                ->values(),
            'recentEmails' => AdminEmail::with('sender')->orderByDesc('created_at')->limit(30)->get(),
        ];
    }
}
