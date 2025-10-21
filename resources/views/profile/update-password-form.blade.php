<x-form-section submit="updatePassword">
    <x-slot name="title">
        <h4 class="mb-1">{{ __('Update Password') }}</h4>
    </x-slot>

    <x-slot name="description">
        <p class="text-muted mb-3">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </x-slot>

    <x-slot name="form">
        <!-- Current Password -->
        <div class="mb-3">
            <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
            <input id="current_password" type="password"
                   class="form-control @error('state.current_password') is-invalid @enderror"
                   wire:model="state.current_password"
                   autocomplete="current-password">
            <x-input-error for="state.current_password" class="invalid-feedback d-block" />
        </div>

        <!-- New Password -->
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('New Password') }}</label>
            <input id="password" type="password"
                   class="form-control @error('state.password') is-invalid @enderror"
                   wire:model="state.password"
                   autocomplete="new-password">
            <x-input-error for="state.password" class="invalid-feedback d-block" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" type="password"
                   class="form-control @error('state.password_confirmation') is-invalid @enderror"
                   wire:model="state.password_confirmation"
                   autocomplete="new-password">
            <x-input-error for="state.password_confirmation" class="invalid-feedback d-block" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="d-flex align-items-center gap-2">
            <x-action-message class="text-success small fw-semibold" on="saved">
                {{ __('Saved.') }}
            </x-action-message>

            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                {{ __('Save') }}
            </button>
        </div>
    </x-slot>
</x-form-section>
