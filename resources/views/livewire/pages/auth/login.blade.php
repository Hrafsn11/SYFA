<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            request()->session()->regenerate();
            $this->redirect('/dashboard');
        } else {
            $this->addError('email', 'Invalid credentials.');
        }
    }
}; ?>

<div class="authentication-wrapper authentication-cover">
    <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
        <span class="app-brand-logo demo">
            <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name') }}" class="w-px-40">
        </span>
        <span class="app-brand-text demo text-heading fw-bold">SYFA</span>
    </a>

    <div class="authentication-inner row m-0">
        <div class="d-none d-lg-flex col-lg-8 p-0">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                <img
                    src="{{ asset('assets/img/illustrations/auth-login-illustration-light.png') }}"
                    alt="auth-login-cover"
                    class="my-5 auth-illustration"
                    data-app-light-img="illustrations/auth-login-illustration-light.png"
                    data-app-dark-img="illustrations/auth-login-illustration-dark.png" />

                <img
                    src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
                    alt="auth-login-cover"
                    class="platform-bg"
                    data-app-light-img="illustrations/bg-shape-image-light.png"
                    data-app-dark-img="illustrations/bg-shape-image-dark.png" />
            </div>
        </div>

        <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
            <div class="w-px-400 mx-auto mt-12 pt-5">
                <h4 class="mb-1">Welcome back</h4>
                <p class="mb-6">Please sign-in to your account and start the adventure</p>

                <form wire:submit="login" class="mb-6" autocomplete="off">
                    <div class="mb-6">
                        <label for="email" class="form-label">Email</label>
                        <input
                            wire:model.defer="email"
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            placeholder="Enter your email"
                            required
                            autofocus>
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

                    <div class="d-flex justify-content-between align-items-center my-8">
                        <div class="form-check mb-0 ms-2">
                            <input wire:model="remember" class="form-check-input" type="checkbox" id="remember-me">
                            <label class="form-check-label" for="remember-me"> Remember Me </label>
                        </div>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot Password?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3" wire:loading.attr="disabled" wire:target="login">
                        <span wire:loading.remove wire:target="login" class="fw-semibold">Sign in</span>
                    </button>
                </form>

                @if (Route::has('register'))
                    <p class="text-center">
                        <span>New on our platform?</span>
                        <a href="{{ route('register') }}" class="text-decoration-none"> Create an account</a>
                    </p>
                @endif

                <div class="divider my-6">
                    <div class="divider-text">or</div>
                </div>

                <div class="d-flex justify-content-center">
                    <a href="#" class="btn btn-icon rounded-pill btn-text-facebook me-2" aria-label="Login with Facebook">
                        <i class="tf-icons ti ti-brand-facebook-filled"></i>
                    </a>
                    <a href="#" class="btn btn-icon rounded-pill btn-text-twitter me-2" aria-label="Login with Twitter">
                        <i class="tf-icons ti ti-brand-twitter-filled"></i>
                    </a>
                    <a href="#" class="btn btn-icon rounded-pill btn-text-github me-2" aria-label="Login with Github">
                        <i class="tf-icons ti ti-brand-github-filled"></i>
                    </a>
                    <a href="#" class="btn btn-icon rounded-pill btn-text-google-plus" aria-label="Login with Google">
                        <i class="tf-icons ti ti-brand-google-filled"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>