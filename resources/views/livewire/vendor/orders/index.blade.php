<?php

use App\Domain\OrderManagement\Actions\UpdateOrderStatusAction;
use App\Domain\OrderManagement\Enums\OrderStatus;
use App\Domain\OrderManagement\Models\OrderItem;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function updateStatus(string $orderId, string $status): void
    {
        $vendor = auth()->user()->vendor;
        $orderItem = OrderItem::where('vendor_id', $vendor->id)
            ->where('order_id', $orderId)
            ->firstOrFail();

        $order = $orderItem->order;

        try {
            app(UpdateOrderStatusAction::class)->execute($order, OrderStatus::from($status));
        } catch (\RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function with(): array
    {
        $vendor = auth()->user()->vendor;

        $orderItems = OrderItem::where('vendor_id', $vendor->id)
            ->with(['order.user', 'product'])
            ->latest()
            ->paginate(10);

        return compact('orderItems');
    }
}; ?>

<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Vendor Orders</h1>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-3 text-sm font-medium text-gray-600">Order</th>
                <th class="text-left px-4 py-3 text-sm font-medium text-gray-600">Buyer</th>
                <th class="text-left px-4 py-3 text-sm font-medium text-gray-600">Product</th>
                <th class="text-left px-4 py-3 text-sm font-medium text-gray-600">Qty</th>
                <th class="text-left px-4 py-3 text-sm font-medium text-gray-600">Status</th>
                <th class="text-left px-4 py-3 text-sm font-medium text-gray-600">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($orderItems as $item)
                <tr class="border-t">
                    <td class="px-4 py-3 text-sm">#{{ substr($item->order_id, -8) }}</td>
                    <td class="px-4 py-3 text-sm">{{ $item->order->user->name }}</td>
                    <td class="px-4 py-3 text-sm">{{ $item->product->name }}</td>
                    <td class="px-4 py-3 text-sm">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 text-sm">{{ ucfirst($item->order->status->value) }}</td>
                    <td class="px-4 py-3 text-sm">
                        @if($item->order->status === OrderStatus::Paid)
                            <button wire:click="updateStatus('{{ $item->order_id }}', 'shipped')"
                                    class="text-blue-600 hover:underline">Mark Shipped</button>
                        @elseif($item->order->status === OrderStatus::Shipped)
                            <button wire:click="updateStatus('{{ $item->order_id }}', 'delivered')"
                                    class="text-green-600 hover:underline">Mark Delivered</button>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No orders yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orderItems->links() }}</div>
</div>
