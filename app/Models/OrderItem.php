<?php

namespace App\Models;

use App\Models\OrderStatusLog;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'customer_id', 'product_id', 'variant_id', 'service_id', 'description',
        'quantity', 'unit_price', 'subtotal', 'delivery_type',
        'production_type', 'item_stage', 'production_path', 'measurements',
        'design_notes', 'design_file', 'production_notes',
        'washing_required', 'washing_skipped', 'washing_skip_reason', 'stage_updated_at',
        'staff_marked_done', 'staff_done_at', 'staff_done_by',
    ];

    protected $casts = [
        'unit_price'        => 'decimal:2',
        'subtotal'          => 'decimal:2',
        'measurements'      => 'array',
        'production_path'   => 'array',
        'washing_required'  => 'boolean',
        'washing_skipped'   => 'boolean',
        'stage_updated_at'  => 'datetime',
        'staff_marked_done' => 'boolean',
        'staff_done_at'     => 'datetime',
    ];

    // Item stage flow
    public const STAGES = [
        'pending', 'sewing', 'embroidery', 'printing', 'finishing', 'ready', 'dispatched', 'delivered',
    ];

    // Active production stages shown on the Production Tracker
    public const PRODUCTION_STAGES = [
        'sewing'     => 'Sewing',
        'embroidery' => 'Embroidery',
        'printing'   => 'Printing',
        'finishing'  => 'Finishing',
        'ready'      => 'Ready',
    ];

    // Stage → role mapping (used by Production Tracker)
    public const STAGE_ROLES = [
        'sewing'     => 'tailor',
        'embroidery' => 'embroidery',
        'printing'   => 'printer',
        'finishing'  => 'dry_cleaner',
    ];

    // Named production path presets
    public const PATH_LABELS = [
        'none'                       => 'Ready-made',
        'sewing_only'                => 'Sewing → Finishing',
        'sewing_embroidery'          => 'Sewing → Embroidery → Finishing',
        'sewing_printing'            => 'Sewing → Printing → Finishing',
        'sewing_embroidery_printing' => 'Sewing → Embroidery → Printing → Finishing',
        'embroidery_only'            => 'Embroidery → Finishing',
        'printing_only'              => 'Printing only',
        'embroidery_printing'        => 'Embroidery → Printing → Finishing',
    ];

    public const PATHS = [
        'none'                       => [],
        'sewing_only'                => ['sewing', 'finishing', 'ready'],
        'sewing_embroidery'          => ['sewing', 'embroidery', 'finishing', 'ready'],
        'sewing_printing'            => ['sewing', 'printing', 'finishing', 'ready'],
        'sewing_embroidery_printing' => ['sewing', 'embroidery', 'printing', 'finishing', 'ready'],
        'embroidery_only'            => ['embroidery', 'finishing', 'ready'],
        'printing_only'              => ['printing', 'ready'],
        'embroidery_printing'        => ['embroidery', 'printing', 'finishing', 'ready'],
    ];

    /**
     * Auto-detect the correct production path from product properties and
     * the category's default path key. Product-level flags take precedence
     * so that e.g. a tailoring order with an embroidery product gets the
     * right combined path automatically.
     */
    public static function detectPath(Product $product, string $categoryPathKey = 'none'): string
    {
        // Non-production products always skip the pipeline
        if (! $product->requiresProduction()) return 'none';

        // Start from the category's declared default
        $base = $categoryPathKey !== 'none' ? $categoryPathKey : 'sewing_only';

        // Product-type overrides
        if ($product->product_type === 'embroidery') return 'embroidery_only';
        if ($product->product_type === 'printing')   return 'printing_only';

        // A sewing product that also carries embroidery work
        if ($product->is_embroidery) {
            return match ($base) {
                'sewing_only'     => 'sewing_embroidery',
                'sewing_printing' => 'sewing_embroidery_printing',
                default           => 'sewing_embroidery',
            };
        }

        return $base;
    }

    // ── Relationships ───────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function assignments()
    {
        return $this->hasMany(OrderAssignment::class);
    }

    public function activeAssignment()
    {
        return $this->hasOne(OrderAssignment::class)->where('status', '!=', 'complete')->latestOfMany();
    }

    // ── Stage logic ─────────────────────────────────────────

    public function isProduction(): bool
    {
        return $this->production_type === 'production';
    }

    public function nextStage(): ?string
    {
        if (empty($this->production_path)) return null;

        $current = array_search($this->item_stage, $this->production_path);
        if ($current === false) return null;

        return $this->production_path[$current + 1] ?? null;
    }

    // Customer-facing messages sent on each stage advancement.
    const STAGE_CLIENT_MESSAGES = [
        'sewing'     => 'Your item is now being tailored by our team. We\'ll keep you posted!',
        'embroidery' => 'Your item has entered the embroidery stage.',
        'printing'   => 'Your item is now in the printing stage.',
        'finishing'  => 'Your item is in the final finishing stage — almost ready!',
        'ready'      => 'Great news! Your order is ready for collection at Styledinee. Please come in at your convenience.',
    ];

    public function advanceToNextStage(int $actorId): void
    {
        $next = $this->nextStage();
        if (! $next) return;

        $this->activeAssignment?->update([
            'status'       => 'complete',
            'completed_at' => now(),
        ]);

        $this->update([
            'item_stage'        => $next,
            'stage_updated_at'  => now(),
            'staff_marked_done' => false,
            'staff_done_at'     => null,
            'staff_done_by'     => null,
        ]);

        $order         = $this->order;
        $clientMessage = self::STAGE_CLIENT_MESSAGES[$next] ?? null;
        $isPublished   = $clientMessage !== null;

        if ($order) {
            $order->syncStatusFromItems();

            OrderStatusLog::create([
                'order_id'       => $order->id,
                'order_item_id'  => $this->id,
                'changed_by'     => $actorId,
                'status'         => $next,
                'notes'          => 'Stage advanced to ' . (self::PRODUCTION_STAGES[$next] ?? ucfirst($next)) . '.',
                'is_published'   => $isPublished,
                'client_message' => $clientMessage,
            ]);

            // Notify the customer via WhatsApp for every published stage
            if ($isPublished && $order->customer_phone) {
                try {
                    $notif = app(\App\Services\NotificationService::class);
                    if ($next === 'ready') {
                        $notif->notifyOrderReady($order);
                    } else {
                        $notif->stageUpdated($order, $clientMessage);
                    }
                } catch (\Throwable) {
                    // Notification failures must never block stage advancement
                }
            }
        }
    }
}
