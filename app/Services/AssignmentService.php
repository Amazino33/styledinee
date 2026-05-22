<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\OrderItem;
use App\Models\User;

class AssignmentService
{
    /**
     * Auto-assign a user with the fewest active assignments in the given department/role.
     */
    public function assignLeastBusy(Order $order, string $department, ?OrderItem $item = null): ?OrderAssignment
    {
        $candidate = User::role($department)
            ->withCount(['orderAssignments' => fn ($q) => $q->where('status', '!=', 'complete')])
            ->orderBy('order_assignments_count')
            ->first();

        if (! $candidate) return null;

        return OrderAssignment::create([
            'order_id'      => $order->id,
            'order_item_id' => $item?->id,
            'assigned_to'   => $candidate->id,
            'assigned_by'   => auth()->id() ?? 1,
            'department'    => $department,
            'status'        => 'assigned',
            'assigned_at'   => now(),
        ]);
    }

    /**
     * Assign all production items in an order to tailors.
     */
    public function assignProductionItems(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->isProduction()) {
                $this->assignLeastBusy($order, 'tailor', $item);
            }
        }
    }

    /**
     * Assign dry-cleaning for the whole order.
     */
    public function assignWashing(Order $order): void
    {
        $this->assignLeastBusy($order, 'dry_cleaner');
    }

    /**
     * Assign delivery for the whole order.
     */
    public function assignDelivery(Order $order): void
    {
        $this->assignLeastBusy($order, 'delivery');
    }
}
