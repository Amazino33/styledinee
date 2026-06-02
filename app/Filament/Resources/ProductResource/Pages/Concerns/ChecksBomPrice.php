<?php

namespace App\Filament\Resources\ProductResource\Pages\Concerns;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

trait ChecksBomPrice
{
    /**
     * Calculates the total BOM cost from the current form data.
     * Returns 0 if the product has no materials or is not a production product.
     */
    protected function getBomTotal(): float
    {
        $materials = $this->data['materials'] ?? [];
        if (empty($materials)) return 0.0;

        $total = 0.0;
        foreach ($materials as $line) {
            $materialId = $line['material_id'] ?? null;
            $quantity   = (float) ($line['quantity'] ?? 0);
            if (! $materialId || $quantity <= 0) continue;

            $material = Product::find($materialId);
            if ($material) {
                $total += (float) $material->price * $quantity;
            }
        }

        return round($total, 2);
    }

    protected function beforeCreate(): void
    {
        $this->checkBomPriceWarning();
    }

    protected function beforeSave(): void
    {
        $this->checkBomPriceWarning();
    }

    private function checkBomPriceWarning(): void
    {
        $price    = (float) ($this->data['price'] ?? 0);
        $bomTotal = $this->getBomTotal();

        if ($bomTotal <= 0 || $price >= $bomTotal) return;

        // Store that we've warned so a second save attempt goes through
        if ($this->data['_bom_warning_acknowledged'] ?? false) return;

        $this->data['_bom_warning_acknowledged'] = true;

        Notification::make()
            ->title('Price below material cost')
            ->body(
                'The selling price (₦' . number_format($price, 2) . ') is below '
                . 'the total material cost (₦' . number_format($bomTotal, 2) . '). '
                . 'Click Save again to continue anyway, or adjust the price.'
            )
            ->warning()
            ->persistent()
            ->send();

        $this->halt();
    }
}
