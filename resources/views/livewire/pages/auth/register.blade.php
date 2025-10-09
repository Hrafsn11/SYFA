<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(RouteServiceProvider::HOME, navigate: true);
    }
}; ?>

<div class="authentication-wrapper authentication-cover">
    <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
        <span class="app-brand-logo demo">
            <img src="{{ asset('assets/img/favicon/favicon.ico') }}" alt="{{ config('app.name') }}" class="w-px-40">
        </span>
        <span class="app-brand-text demo text-heading fw-bold">{{ config('app.name', 'Vuexy') }}</span>
    </a>

    <div class="authentication-inner row m-0">
        <div class="d-none d-lg-flex col-lg-8 p-0">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                <img
                    src="{{ asset('assets/img/illustrations/auth-register-illustration-light.png') }}"
                    alt="auth-register-cover"
                    class="my-5 auth-illustration"
                    data-app-light-img="illustrations/auth-register-illustration-light.png"
                    data-app-dark-img="illustrations/auth-register-illustration-dark.png" />

                <img
                    src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
                    alt="auth-register-cover"
                    class="platform-bg"
                    data-app-light-img="illustrations/bg-shape-image-light.png"
                    data-app-dark-img="illustrations/bg-shape-image-dark.png" />
            </div>
        </div>

        <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
            <div class="w-px-400 mx-auto mt-12 pt-5">
                <h4 class="mb-1">Adventure starts here 🚀</h4>
                <p class="mb-6">Make your app management easy and fun!</p>

                <form wire:submit="register" class="mb-6" autocomplete="off">
                    <div class="mb-6">
                        <label for="name" class="form-label">Full Name</label>
                        <input
                            wire:model.defer="name"
                            type="text"
                            id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter your name"
                            required
                            autofocus>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="email" class="form-label">Email</label>
                        <input
                            wire:model.defer="email"
                            type="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Enter your email"
                            required>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6 form-password-toggle">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge">
                            <input
                                wire:model.defer="password"
                                type="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="••••••••"
                                required>
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6 form-password-toggle">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <div class="input-group input-group-merge">
                            <input
                                wire:model.defer="password_confirmation"
                                type="password"
                                id="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                placeholder="••••••••"
                                required>
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-6 ms-2">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#">privacy policy & terms</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary d-grid w-100" wire:loading.attr="disabled" wire:target="register">
                        <span wire:loading.remove wire:target="register">Sign up</span>

                    </button>
                </form>

                <p class="text-center">
                    <span>Already have an account?</span>
                    <a href="{{ route('login') }}" class="text-decoration-none"> Sign in instead</a>
                </p>

                <div class="divider my-6">
                    <div class="divider-text">or</div>
                </div>

                <div class="d-flex justify-content-center">
                    <a href="#" class="btn btn-icon rounded-pill btn-text-facebook me-2" aria-label="Sign up with Facebook">
                        <i class="tf-icons ti ti-brand-facebook-filled"></i>
                    </a>
                    <a href="#" class="btn btn-icon rounded-pill btn-text-twitter me-2" aria-label="Sign up with Twitter">
                        <i class="tf-icons ti ti-brand-twitter-filled"></i>
                    </a>
                    <a href="#" class="btn btn-icon rounded-pill btn-text-github me-2" aria-label="Sign up with Github">
                        <i class="tf-icons ti ti-brand-github-filled"></i>
                    </a>
                    <a href="#" class="btn btn-icon rounded-pill btn-text-google-plus" aria-label="Sign up with Google">
                        <i class="tf-icons ti ti-brand-google-filled"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
