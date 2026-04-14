<?php

use Livewire\Volt\Component;

new class extends Component {
}; ?>

<div style="font-family: 'Inter', sans-serif; max-width: 860px; margin: 0 auto;">

    <h1 style="font-size: 26px; font-weight: 800; color: #111; margin: 0 0 24px 0; letter-spacing: -0.5px;">
        My Profile
    </h1>

    <div style="display: flex; flex-direction: column; gap: 20px;">

        {{-- Profile Information --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #ebebeb; padding: 28px 32px;">
            <livewire:profile.update-profile-information-form />
        </div>

        {{-- Update Password --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #ebebeb; padding: 28px 32px;">
            <livewire:profile.update-password-form />
        </div>

        {{-- Delete Account --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #fee2e2; padding: 28px 32px;">
            <livewire:profile.delete-user-form />
        </div>

    </div>
</div>
