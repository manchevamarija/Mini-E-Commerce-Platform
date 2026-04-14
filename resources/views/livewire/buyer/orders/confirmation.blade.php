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

<div style="font-family: 'Inter', sans-serif; max-width: 600px; margin: 0 auto; text-align: center;">

    {{-- Success Icon --}}
    <div style="margin-bottom: 24px;">
        <div style="width: 72px; height: 72px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <h1 style="font-size: 28px; font-weight: 800; color: #111; margin: 0 0 8px 0;">Order Confirmed!</h1>
        <p style="font-size: 15px; color: #888; margin: 0;">Thank you for your purchase. Your order has been placed successfully.</p>
    </div>

    {{-- Order Info --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #ebebeb; padding: 24px; margin-bottom: 20px; text-align: left;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <p style="font-size: 13px; color: #999; margin: 0 0 4px 0;">Order Number</p>
                <p style="font-size: 16px; font-weight: 800; color: #111; margin: 0;">#{{ substr($order->id, -8) }}</p>
            </div>
            <span style="font-size: 11px; font-weight: 700; padding: 6px 14px; border-radius: 20px; background: #dcfce7; color: #16a34a; text-transform: uppercase;">
                {{ ucfirst($order->status->value) }}
            </span>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 13px; color: #888; margin-bottom: 16px;">
            <span>{{ $order->created_at->format('M d, Y \a\t H:i') }}</span>
            <span>{{ ucfirst(str_replace('_', ' ', $order->payment_method->value)) }}</span>
        </div>

        <hr style="border: none; border-top: 1px solid #f0f0f0; margin: 0 0 16px 0;" />

        {{-- Items --}}
        @foreach($items as $item)
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
            <div>
                <p style="font-size: 13px; font-weight: 600; color: #111; margin: 0 0 2px 0;">{{ $item->product->name }}</p>
                <p style="font-size: 11px; color: #aaa; margin: 0;">{{ $item->vendor->shop_name }} × {{ $item->quantity }}</p>
            </div>
            <p style="font-size: 13px; font-weight: 700; color: #111; margin: 0;">${{ number_format($item->price * $item->quantity, 2) }}</p>
        </div>
        @endforeach

        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px;">
            <p style="font-size: 15px; font-weight: 700; color: #111; margin: 0;">Total</p>
            <p style="font-size: 18px; font-weight: 800; color: #111; margin: 0;">${{ number_format($order->total, 2) }}</p>
        </div>
    </div>

    {{-- Actions --}}
    <div style="display: flex; gap: 12px; justify-content: center;">
        <a href="{{ route('buyer.orders.show', $order) }}"
           style="border: 1.5px solid #111; color: #111; padding: 12px 24px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none;">
            View Order Details
        </a>
        <a href="{{ route('market.index') }}"
           style="background: #111; color: white; padding: 12px 24px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none;">
            Continue Shopping
        </a>
    </div>
</div>
