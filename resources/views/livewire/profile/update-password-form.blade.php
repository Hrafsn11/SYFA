<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="container-xxl">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Update Password') }}</h5>
            <small class="text-muted">{{ __('Ensure your account is using a long, random password to stay secure.') }}</small>
        </div>
        <div class="card-body">
            <form wire:submit="updatePassword" class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
                    <input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" />
                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
                    <input wire:model="password" id="update_password_password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" />
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="update_password_password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                    <input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" autocomplete="new-password" />
                    @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    <div wire:loading class="text-muted small ms-3">{{ __('Saving...') }}</div>
                    <div wire:loading.remove class="text-success small ms-3" wire:target="updatePassword">{{ __('Saved.') }}</div>
                </div>
            </form>
        </div>
    </div>
</section>
