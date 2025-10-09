<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: RouteServiceProvider::HOME);

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="container-xxl">
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0">{{ __('Profile Information') }}</h5>
                <small class="text-muted">{{ __("Update your account's profile information and email address.") }}</small>
            </div>
        </div>
        <div class="card-body">
            <form wire:submit="updateProfileInformation" class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input wire:model="name" id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="name" />
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input wire:model="email" id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" required autocomplete="username" />
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                        <div class="mt-2">
                            <p class="mb-1 small text-muted">{{ __('Your email address is unverified.') }}</p>
                            <button wire:click.prevent="sendVerification" class="btn btn-sm btn-outline-primary">{{ __('Click here to re-send the verification email.') }}</button>

                            @if (session('status') === 'verification-link-sent')
                                <div class="mt-2 alert alert-success py-1">{{ __('A new verification link has been sent to your email address.') }}</div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="col-12 mt-3 d-flex align-items-center">
                    <button type="submit" class="btn btn-primary me-3">{{ __('Save') }}</button>
                    <div wire:loading class="text-muted small">{{ __('Saving...') }}</div>
                    <div wire:loading.remove class="text-success small ms-3" wire:target="updateProfileInformation">{{ __('Saved.') }}</div>
                </div>
            </form>
        </div>
    </div>
</section>
