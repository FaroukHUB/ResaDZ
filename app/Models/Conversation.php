<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'loueur_id',
        'subject',
        'status',
        'priority',
        'category',
        'last_message_at',
        'loueur_unread',
        'admin_unread',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'loueur_unread' => 'boolean',
        'admin_unread' => 'boolean',
    ];

    // Status constants
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_ARCHIVED = 'archived';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Category constants
    const CATEGORY_BOOST = 'boost';
    const CATEGORY_INVOICE = 'invoice';
    const CATEGORY_SUPPORT = 'support';
    const CATEGORY_GENERAL = 'general';

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeWithUnreadForAdmin($query)
    {
        return $query->where('admin_unread', true);
    }

    public function scopeWithUnreadForLoueur($query)
    {
        return $query->where('loueur_unread', true);
    }

    public function markAsReadByAdmin(): void
    {
        $this->update(['admin_unread' => false]);
        $this->messages()
            ->where('sender_type', 'loueur')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function markAsReadByLoueur(): void
    {
        $this->update(['loueur_unread' => false]);
        $this->messages()
            ->where('sender_type', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function addMessage(string $content, string $senderType, ?int $senderId = null, array $attachments = [], bool $isSystemMessage = false): Message
    {
        $message = $this->messages()->create([
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'content' => $content,
            'attachments' => $attachments ?: null,
            'is_system_message' => $isSystemMessage,
        ]);

        // Update conversation
        $this->update([
            'last_message_at' => now(),
            'loueur_unread' => $senderType === 'admin',
            'admin_unread' => $senderType === 'loueur',
        ]);

        return $message;
    }

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_BOOST => 'Boost / Sponsoring',
            self::CATEGORY_INVOICE => 'Facturation',
            self::CATEGORY_SUPPORT => 'Support technique',
            self::CATEGORY_GENERAL => 'Général',
        ];
    }

    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Basse',
            self::PRIORITY_NORMAL => 'Normale',
            self::PRIORITY_HIGH => 'Haute',
            self::PRIORITY_URGENT => 'Urgente',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_OPEN => 'Ouvert',
            self::STATUS_CLOSED => 'Fermé',
            self::STATUS_ARCHIVED => 'Archivé',
        ];
    }

    /**
     * Get message templates grouped by category
     */
    public static function getMessageTemplates(): array
    {
        return [
            'boost' => [
                'boost_activated' => [
                    'subject' => 'Votre boost a été activé',
                    'message' => "Bonjour,\n\nNous avons le plaisir de vous informer que votre pack boost a été activé avec succès.\n\nVotre véhicule bénéficie désormais d'une visibilité accrue sur notre plateforme.\n\nMerci pour votre confiance !",
                ],
                'boost_expiring' => [
                    'subject' => 'Votre boost expire bientôt',
                    'message' => "Bonjour,\n\nNous vous informons que votre pack boost arrive à expiration dans les prochains jours.\n\nPour continuer à bénéficier d'une visibilité optimale, n'hésitez pas à renouveler votre boost.\n\nCordialement,",
                ],
                'boost_expired' => [
                    'subject' => 'Votre boost a expiré',
                    'message' => "Bonjour,\n\nVotre pack boost est arrivé à expiration.\n\nVous pouvez à tout moment souscrire à un nouveau pack pour remettre en avant vos véhicules.\n\nCordialement,",
                ],
                'boost_promo' => [
                    'subject' => 'Offre spéciale boost',
                    'message' => "Bonjour,\n\nNous avons une offre spéciale sur nos packs boost !\n\nProfitez-en pour augmenter la visibilité de vos véhicules à tarif réduit.\n\nCordialement,",
                ],
            ],
            'invoice' => [
                'invoice_sent' => [
                    'subject' => 'Nouvelle facture disponible',
                    'message' => "Bonjour,\n\nUne nouvelle facture est disponible dans votre espace.\n\nVous pouvez la consulter et la télécharger en PDF depuis la section \"Mes factures\".\n\nCordialement,",
                ],
                'invoice_reminder' => [
                    'subject' => 'Rappel de facture',
                    'message' => "Bonjour,\n\nNous vous rappelons qu'une facture est en attente de règlement.\n\nMerci de procéder au paiement dans les meilleurs délais.\n\nCordialement,",
                ],
                'payment_received' => [
                    'subject' => 'Paiement reçu - Merci !',
                    'message' => "Bonjour,\n\nNous avons bien reçu votre paiement. Merci !\n\nVotre facture a été marquée comme payée.\n\nCordialement,",
                ],
                'payment_issue' => [
                    'subject' => 'Problème de paiement',
                    'message' => "Bonjour,\n\nNous avons rencontré un problème avec votre paiement.\n\nMerci de nous contacter pour régulariser la situation.\n\nCordialement,",
                ],
            ],
            'support' => [
                'welcome' => [
                    'subject' => 'Bienvenue sur ' . Setting::get('company_name', 'ResaDZ') . ' !',
                    'message' => "Bonjour et bienvenue sur " . Setting::get('company_name', 'ResaDZ') . " !\n\nNous sommes ravis de vous compter parmi nos loueurs partenaires.\n\nN'hésitez pas à nous contacter si vous avez des questions.\n\nCordialement,",
                ],
                'account_verified' => [
                    'subject' => 'Votre compte a été vérifié',
                    'message' => "Bonjour,\n\nVotre compte a été vérifié avec succès !\n\nVous pouvez maintenant publier vos véhicules et recevoir des réservations.\n\nCordialement,",
                ],
                'documents_required' => [
                    'subject' => 'Documents requis',
                    'message' => "Bonjour,\n\nAfin de finaliser la vérification de votre compte, nous avons besoin des documents suivants :\n\n- \n- \n\nMerci de les télécharger dans votre espace.\n\nCordialement,",
                ],
                'vehicle_approved' => [
                    'subject' => 'Véhicule approuvé',
                    'message' => "Bonjour,\n\nVotre véhicule a été approuvé et est maintenant visible sur la plateforme.\n\nBonne location !\n\nCordialement,",
                ],
                'vehicle_rejected' => [
                    'subject' => 'Véhicule non approuvé',
                    'message' => "Bonjour,\n\nAprès examen, votre véhicule n'a pas pu être approuvé pour les raisons suivantes :\n\n- \n\nMerci de corriger ces points et de soumettre à nouveau.\n\nCordialement,",
                ],
            ],
            'general' => [
                'custom' => [
                    'subject' => '',
                    'message' => '',
                ],
                'maintenance' => [
                    'subject' => 'Maintenance prévue',
                    'message' => "Bonjour,\n\nNous vous informons qu'une maintenance est prévue sur notre plateforme.\n\nLe service pourrait être temporairement indisponible.\n\nMerci de votre compréhension.\n\nCordialement,",
                ],
                'new_feature' => [
                    'subject' => 'Nouvelle fonctionnalité',
                    'message' => "Bonjour,\n\nNous avons le plaisir de vous annoncer une nouvelle fonctionnalité sur " . Setting::get('company_name', 'ResaDZ') . " !\n\n\n\nN'hésitez pas à l'essayer.\n\nCordialement,",
                ],
                'thank_you' => [
                    'subject' => 'Merci !',
                    'message' => "Bonjour,\n\nNous tenions à vous remercier pour votre confiance et votre fidélité.\n\nCordialement,",
                ],
            ],
        ];
    }

    /**
     * Get flat list of templates for select dropdown
     */
    public static function getTemplateOptions(): array
    {
        $options = [];
        $templates = self::getMessageTemplates();

        foreach ($templates as $category => $categoryTemplates) {
            $categoryLabel = self::getCategories()[$category] ?? ucfirst($category);
            foreach ($categoryTemplates as $key => $template) {
                if (!empty($template['subject'])) {
                    $options["{$category}.{$key}"] = "[{$categoryLabel}] {$template['subject']}";
                } else {
                    $options["{$category}.{$key}"] = "[{$categoryLabel}] Message personnalisé";
                }
            }
        }

        return $options;
    }

    /**
     * Get a specific template
     */
    public static function getTemplate(string $templateKey): ?array
    {
        $parts = explode('.', $templateKey);
        if (count($parts) !== 2) {
            return null;
        }

        $templates = self::getMessageTemplates();
        return $templates[$parts[0]][$parts[1]] ?? null;
    }
}
