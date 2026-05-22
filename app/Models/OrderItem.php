<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'customer_id', 'product_id', 'variant_id', 'service_id', 'description',
        'quantity', 'unit_price', 'subtotal',
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
        'embroidery' => 'tailor',
        'printing'   => 'printer',
        'finishing'  => 'dry_cleaner',
    ];

    // Named production path presets
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

    // Auto-detect path from product type
    public static function detectPath(Product $product): string
    {
        return match ($product->product_type) {
            'embroidery' => 'embroidery_only',
            'printing'   => 'printing_only',
            'ready_made',
            'fabric',
            'accessory'  => 'none',
            default      => 'sewing_only', // garment / production products
        };
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
}
