<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f7f7f8; }
        * { box-sizing: border-box; }
    </style>
</head>
<body style="min-height: 100vh;">

<nav style="background: white; border-bottom: 1px solid #ebebeb; position: sticky; top: 0; z-index: 50;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 32px; height: 60px; display: flex; justify-content: space-between; align-items: center;">

        <a href="{{ route('market.index') }}"
           style="font-size: 20px; font-weight: 800; color: #111; text-decoration: none; letter-spacing: -0.5px;">
            ShopX
        </a>

        <div style="display: flex; align-items: center; gap: 24px;">
            <a href="{{ route('market.index') }}"
               style="font-size: 13px; font-weight: 500; color: #555; text-decoration: none;">
                Marketplace
            </a>

            @auth
                @if(auth()->user()->isVendor())
                    <a href="{{ route('vendor.dashboard') }}"
                       style="font-size: 13px; font-weight: 500; color: #555; text-decoration: none;">
                        Dashboard
                    </a>
                    <a href="{{ route('vendor.products.index') }}"
                       style="font-size: 13px; font-weight: 500; color: #555; text-decoration: none;">
                        My Products
                    </a>
                    <a href="{{ route('vendor.orders.index') }}"
                       style="font-size: 13px; font-weight: 500; color: #555; text-decoration: none;">
                        Vendor Orders
                    </a>
                @endif

                @if(auth()->user()->isBuyer())
                    <a href="{{ route('cart.index') }}"
                       style="font-size: 13px; font-weight: 500; color: #555; text-decoration: none;">
                        Cart
                    </a>
                    <a href="{{ route('buyer.orders.index') }}"
                       style="font-size: 13px; font-weight: 500; color: #555; text-decoration: none;">
                        My Orders
                    </a>
                @endif

                <a href="{{ route('profile') }}"
                   style="font-size: 13px; color: #aaa; text-decoration: none;">
                    {{ auth()->user()->name }}
                </a>

                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit"
                            style="font-size: 13px; font-weight: 500; color: #ef4444; background: none; border: none; cursor: pointer; padding: 0;">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   style="font-size: 13px; font-weight: 500; color: #555; text-decoration: none;">
                    Login
                </a>
                <a href="{{ route('register') }}"
                   style="font-size: 13px; font-weight: 600; color: white; background: #111; padding: 8px 18px; border-radius: 8px; text-decoration: none;">
                    Register
                </a>
            @endauth
        </div>
    </div>
</nav>

<main style="max-width: 1280px; margin: 0 auto; padding: 32px;">
    {{ $slot }}
</main>

@livewireScripts
</body>
</html>
