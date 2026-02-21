<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'loueur_id',
        'status',
        'issue_date',
        'due_date',
        'paid_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total',
        'currency',
        'payment_method',
        'payment_reference',
        'billing_name',
        'billing_address',
        'billing_city',
        'billing_phone',
        'billing_email',
        'billing_nif',
        'notes',
        'footer_text',
        'pdf_path',
        'sent_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'sent_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_OVERDUE = 'overdue';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');

        // Get the last invoice number for this month
        $lastInvoice = self::where('invoice_number', 'like', "FAC-{$year}{$month}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf("FAC-%s%s-%04d", $year, $month, $newNumber);
    }

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items->sum('total');
        $taxAmount = ($subtotal - $this->discount_amount) * ($this->tax_rate / 100);
        $total = $subtotal - $this->discount_amount + $taxAmount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ]);
    }

    public function addItem(string $description, float $unitPrice, int $quantity = 1, ?string $type = null, ?Model $itemable = null, float $discount = 0): InvoiceItem
    {
        $total = ($unitPrice * $quantity) - $discount;

        $item = $this->items()->create([
            'type' => $type,
            'itemable_type' => $itemable ? get_class($itemable) : null,
            'itemable_id' => $itemable?->id,
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount' => $discount,
            'total' => $total,
        ]);

        $this->calculateTotals();

        return $item;
    }

    public function markAsPaid(?string $paymentMethod = null, ?string $paymentReference = null): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'paid_date' => now(),
            'payment_method' => $paymentMethod ?? $this->payment_method,
            'payment_reference' => $paymentReference ?? $this->payment_reference,
        ]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_SENT && $this->due_date < now();
    }

    public function getPdfUrl(): ?string
    {
        if ($this->pdf_path) {
            return Storage::url($this->pdf_path);
        }
        return null;
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_SENT => 'Envoyée',
            self::STATUS_PAID => 'Payée',
            self::STATUS_CANCELLED => 'Annulée',
            self::STATUS_OVERDUE => 'En retard',
        ];
    }

    public static function getPaymentMethods(): array
    {
        return [
            'cash' => 'Espèces',
            'bank_transfer' => 'Virement bancaire',
            'cib' => 'Carte CIB',
            'dahabia' => 'Dahabia',
            'baridimob' => 'BaridiMob',
            'paypal' => 'PayPal',
        ];
    }

    /**
     * Create an invoice for a boost purchase
     */
    public static function createForBoost(VehicleBoost $boost): self
    {
        $loueur = $boost->vehicle->loueur;
        $package = $boost->boostPackage;

        $invoice = self::create([
            'loueur_id' => $loueur->id,
            'status' => $boost->status === 'active' ? self::STATUS_PAID : self::STATUS_DRAFT,
            'issue_date' => now(),
            'due_date' => now()->addDays((int) Setting::get('invoice_due_days', 30)),
            'paid_date' => $boost->status === 'active' ? now() : null,
            'payment_method' => $boost->payment_method,
            'payment_reference' => $boost->payment_reference,
            'billing_name' => $loueur->company_name,
            'billing_address' => $loueur->address,
            'billing_city' => $loueur->city,
            'billing_phone' => $loueur->phone,
            'billing_email' => $loueur->user->email ?? null,
            'notes' => "Boost pour véhicule: {$boost->vehicle->full_name}",
        ]);

        $invoice->addItem(
            description: "Pack Boost \"{$package->name}\" - {$package->duration_days} jours",
            unitPrice: $package->price,
            quantity: 1,
            type: 'boost',
            itemable: $boost
        );

        return $invoice;
    }
}
