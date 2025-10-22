<x-action-section>
    <x-slot name="title">
        <h4 class="mb-1">{{ __('Two Factor Authentication') }}</h4>
    </x-slot>

    <x-slot name="description">
        <p class="text-muted mb-3">
            {{ __('Add additional security to your account using two factor authentication.') }}
        </p>
    </x-slot>

    <x-slot name="content">
        <!-- STATUS INFO -->
        <h5 class="fw-semibold mb-2">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    {{ __('Finish enabling two factor authentication.') }}
                @else
                    {{ __('You have enabled two factor authentication.') }}
                @endif
            @else
                {{ __('You have not enabled two factor authentication.') }}
            @endif
        </h5>

        <p class="text-muted mb-4">
            {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
        </p>

        <!-- QR CODE AREA -->
        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="alert alert-info small">
                    @if ($showingConfirmation)
                        {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                    @else
                        {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                    @endif
                </div>

                <div class="bg-white p-3 d-inline-block rounded border">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div class="mt-3">
                    <p class="fw-semibold mb-0">
                        {{ __('Setup Key') }}:
                        <span class="text-monospace">{{ decrypt($this->user->two_factor_secret) }}</span>
                    </p>
                </div>

                @if ($showingConfirmation)
                    <div class="mt-4 w-50">
                        <label for="code" class="form-label">{{ __('Code') }}</label>
                        <input id="code" type="text"
                               class="form-control @error('code') is-invalid @enderror"
                               wire:model="code"
                               wire:keydown.enter="confirmTwoFactorAuthentication"
                               inputmode="numeric" autofocus autocomplete="one-time-code">
                        <x-input-error for="code" class="invalid-feedback d-block" />
                    </div>
                @endif
            @endif

            <!-- RECOVERY CODES -->
            @if ($showingRecoveryCodes)
                <div class="alert alert-warning small mt-4">
                    {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                </div>

                <div class="bg-light p-3 rounded border font-monospace small">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <!-- ACTION BUTTONS -->
        <div class="mt-4 d-flex flex-wrap gap-2">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <button type="button" class="btn btn-primary" wire:loading.attr="disabled">
                        {{ __('Enable') }}
                    </button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <button type="button" class="btn btn-outline-primary" wire:loading.attr="disabled">
                            {{ __('Regenerate Recovery Codes') }}
                        </button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <button type="button" class="btn btn-success" wire:loading.attr="disabled">
                            {{ __('Confirm') }}
                        </button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <button type="button" class="btn btn-outline-secondary" wire:loading.attr="disabled">
                            {{ __('Show Recovery Codes') }}
                        </button>
                    </x-confirms-password>
                @endif

                @if ($showingConfirmation)
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <button type="button" class="btn btn-outline-danger" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <button type="button" class="btn btn-danger" wire:loading.attr="disabled">
                            {{ __('Disable') }}
                        </button>
                    </x-confirms-password>
                @endif
            @endif
        </div>
    </x-slot>
</x-action-section>
