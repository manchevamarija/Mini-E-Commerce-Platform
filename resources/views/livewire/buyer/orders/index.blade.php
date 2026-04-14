<?php

use App\Domain\OrderManagement\Models\Order;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return compact('orders');
    }
}; ?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">My Orders</h1>

    @if($orders->isEmpty())
        <div class="text-center text-gray-500 py-16">
            <p class="text-xl mb-4">No orders yet.</p>
            <a href="{{ route('market.index') }}" class="text-blue-600 hover:underline">Start Shopping</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-xl shadow p-4 flex justify-between items-center">
                    <div>
                        <p class="font-semibold">Order #{{ substr($order->id, -8) }}</p>
                        <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                        <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full
                            {{ $order->status->value === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $order->status->value === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $order->status->value === 'shipped' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $order->status->value === 'delivered' ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ ucfirst($order->status->value) }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-lg">${{ number_format($order->total, 2) }}</p>
                        <a href="{{ route('buyer.orders.show', $order) }}"
                           class="text-blue-600 hover:underline text-sm">View Details</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>
