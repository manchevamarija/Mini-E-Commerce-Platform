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

<div style="font-family: 'Inter', sans-serif; max-width: 860px; margin: 0 auto;">

    <h1 style="font-size: 26px; font-weight: 800; color: #111; margin: 0 0 24px 0; letter-spacing: -0.5px;">My Orders</h1>

    @if($orders->isEmpty())
        <div style="text-align: center; padding: 80px 0; color: #aaa;">
            <p style="font-size: 16px; margin: 0 0 12px 0;">No orders yet.</p>
            <a href="{{ route('market.index') }}" style="color: #6366f1; font-weight: 600; text-decoration: none;">Start Shopping</a>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 14px;">
            @foreach($orders as $order)
                <div style="background: white; border-radius: 16px; border: 1px solid #ebebeb; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="font-size: 15px; font-weight: 700; color: #111; margin: 0 0 4px 0;">
                            Order #{{ substr($order->id, -8) }}
                        </p>
                        <p style="font-size: 13px; color: #999; margin: 0 0 10px 0;">
                            {{ $order->created_at->format('M d, Y \a\t H:i') }}
                        </p>
                        <span style="font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;
                            background: {{ $order->status->value === 'paid' ? '#dcfce7' : ($order->status->value === 'pending' ? '#fef9c3' : ($order->status->value === 'shipped' ? '#dbeafe' : '#f3f4f6')) }};
                            color: {{ $order->status->value === 'paid' ? '#16a34a' : ($order->status->value === 'pending' ? '#ca8a04' : ($order->status->value === 'shipped' ? '#2563eb' : '#6b7280')) }};">
                            {{ ucfirst($order->status->value) }}
                        </span>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 20px; font-weight: 800; color: #111; margin: 0 0 8px 0;">
                            ${{ number_format($order->total, 2) }}
                        </p>
                        <a href="{{ route('buyer.orders.show', $order) }}"
                           style="font-size: 13px; font-weight: 600; color: #6366f1; text-decoration: none;">
                            View Details →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 24px;">
            {{ $orders->links() }}
        </div>
    @endif
</div>
