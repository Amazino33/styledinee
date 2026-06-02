<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\OrderType;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    use Concerns\ChecksBomPrice;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    /**
     * Pre-populate the cascading category selects (_cat_l1 … _cat_l4) from the
     * product's stored order_type_id so the form reflects the full ancestor path.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $orderTypeId = $data['order_type_id'] ?? null;

        if ($orderTypeId) {
            $cat   = OrderType::with('parent.parent.parent.parent')->find($orderTypeId);
            $chain = $cat ? $cat->ancestorChain() : [];

            $data['_cat_l1'] = isset($chain[0]) ? $chain[0]->id : null;
            $data['_cat_l2'] = isset($chain[1]) ? $chain[1]->id : null;
            $data['_cat_l3'] = isset($chain[2]) ? $chain[2]->id : null;
            $data['_cat_l4'] = isset($chain[3]) ? $chain[3]->id : null;
        }

        return $data;
    }
}
