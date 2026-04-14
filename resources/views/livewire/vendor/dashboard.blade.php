<?php

use App\Domain\OrderManagement\Models\OrderItem;
use App\Domain\ProductCatalog\Models\Product;
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        $vendor = auth()->user()->vendor;

        if (!$vendor) {
            return [
                'totalProducts' => 0,
                'totalOrders' => 0,
                'totalRevenue' => 0,
                'recentOrders' => collect(),
            ];
        }

        $totalProducts = Product::where('vendor_id', $vendor->id)->count();
        $totalOrders = OrderItem::where('vendor_id', $vendor->id)->count();
        $totalRevenue = OrderItem::where('vendor_id', $vendor->id)->sum(\Illuminate\Support\Facades\DB::raw('price * quantity'));
        $recentOrders = OrderItem::where('vendor_id', $vendor->id)
            ->with(['order.user', 'product'])
            ->latest()
            ->take(5)
            ->get();

        return compact('totalProducts', 'totalOrders', 'totalRevenue', 'recentOrders');
    }
}; ?>

<div style="font-family: 'Inter', sans-serif; max-width: 1000px; margin: 0 auto;">

    <h1 style="font-size: 26px; font-weight: 800; color: #111; margin: 0 0 24px 0;">Vendor Dashboard</h1>

    {{-- Stats --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
        <div style="background: white; border-radius: 16px; border: 1px solid #ebebeb; padding: 24px;">
            <p style="font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px 0;">Total Products</p>
            <p style="font-size: 32px; font-weight: 800; color: #111; margin: 0;">{{ $totalProducts }}</p>
        </div>
        <div style="background: white; border-radius: 16px; border: 1px solid #ebebeb; padding: 24px;">
            <p style="font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px 0;">Total Orders</p>
            <p style="font-size: 32px; font-weight: 800; color: #111; margin: 0;">{{ $totalOrders }}</p>
        </div>
        <div style="background: white; border-radius: 16px; border: 1px solid #ebebeb; padding: 24px;">
            <p style="font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px 0;">Total Revenue</p>
            <p style="font-size: 32px; font-weight: 800; color: #6366f1; margin: 0;">${{ number_format($totalRevenue, 2) }}</p>
        </div>
    </div>

    {{-- Quick Links --}}
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px;">
        <a href="{{ route('vendor.products.index') }}"
           style="background: #111; color: white; border-radius: 12px; padding: 16px 20px; text-decoration: none; font-weight: 600; font-size: 14px;">
            Manage Products →
        </a>
        <a href="{{ route('vendor.orders.index') }}"
           style="background: white; border: 1.5px solid #111; color: #111; border-radius: 12px; padding: 16px 20px; text-decoration: none; font-weight: 600; font-size: 14px;">
            View All Orders →
        </a>
    </div>

    {{-- Recent Orders --}}
    <div style="background: white; border-radius: 16px; border: 1px solid #ebebeb; overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #f0f0f0;">
            <h2 style="font-size: 15px; font-weight: 700; color: #111; margin: 0;">Recent Orders</h2>
        </div>
        @if($recentOrders->isEmpty())
            <p style="padding: 40px; text-align: center; color: #aaa; margin: 0;">No orders yet.</p>
        @else
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                <tr style="background: #fafafa;">
                    <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #999; font-weight: 600;">Order</th>
                    <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #999; font-weight: 600;">Buyer</th>
                    <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #999; font-weight: 600;">Product</th>
                    <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #999; font-weight: 600;">Amount</th>
                    <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #999; font-weight: 600;">Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($recentOrders as $item)
                    <tr style="border-top: 1px solid #f5f5f5;">
                        <td style="padding: 14px 20px; font-size: 13px; color: #555;">#{{ substr($item->order_id, -8) }}</td>
                        <td style="padding: 14px 20px; font-size: 13px; color: #555;">{{ $item->order->user->name }}</td>
                        <td style="padding: 14px 20px; font-size: 13px; font-weight: 600; color: #111;">{{ $item->product->name }}</td>
                        <td style="padding: 14px 20px; font-size: 13px; font-weight: 700; color: #111;">${{ number_format($item->price * $item->quantity, 2) }}</td>
                        <td style="padding: 14px 20px;">
                                <span style="font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase;
                                    background: {{ $item->order->status->value === 'paid' ? '#dcfce7' : ($item->order->status->value === 'shipped' ? '#dbeafe' : '#f3f4f6') }};
                                    color: {{ $item->order->status->value === 'paid' ? '#16a34a' : ($item->order->status->value === 'shipped' ? '#2563eb' : '#6b7280') }};">
                                    {{ ucfirst($item->order->status->value) }}
                                </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
