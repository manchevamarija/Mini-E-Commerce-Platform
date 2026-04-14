<?php

use App\Domain\OrderManagement\Models\Order;
use Livewire\Volt\Component;

new class extends Component {
    public Order $order;

    public function mount(Order $order): void
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        $this->order = $order;
    }

    public function with(): array
    {
        return [
            'items' => $this->order->items()->with(['product', 'vendor'])->get(),
        ];
    }
}; ?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <a href="{{ route('buyer.orders.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">← Back to Orders</a>

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold">Order #{{ substr($order->id, -8) }}</h1>
                <p class="text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</p>
                <p class="text-sm mt-1">Payment: {{ ucfirst(str_replace('_', ' ', $order->payment_method->value)) }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                {{ $order->status->value === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                {{ $order->status->value === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $order->status->value === 'shipped' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $order->status->value === 'delivered' ? 'bg-gray-100 text-gray-700' : '' }}">
                {{ ucfirst($order->status->value) }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="font-semibold text-lg mb-4">Items</h2>
        @foreach($items as $item)
            <div class="flex justify-between items-center py-3 border-b last:border-0">
                <div>
                    <p class="font-medium">{{ $item->product->name }}</p>
                    <p class="text-sm text-gray-500">{{ $item->vendor->shop_name }} × {{ $item->quantity }}</p>
                </div>
                <p class="font-semibold">${{ number_format($item->price * $item->quantity, 2) }}</p>
            </div>
        @endforeach
        <div class="flex justify-between font-bold text-lg mt-4">
            <span>Total</span>
            <span>${{ number_format($order->total, 2) }}</span>
        </div>
    </div>
</div>
