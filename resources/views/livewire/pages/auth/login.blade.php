<?php

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\User;

new #[Layout('layouts.guest')] class extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);

        // Cek apakah input adalah email atau username
        $fieldType = filter_var($this->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // Cari user terlebih dahulu
        $user = User::where($fieldType, $this->email)->first();

        if (!$user) {
            $this->addError('email', 'Email atau username tidak ditemukan.');
            return;
        }

        // Cek apakah user adalah admin/super-admin (tidak bisa dikunci)
        if (!$user->isLockable()) {
            // Admin/Super-admin langsung login tanpa pengecekan lock
            if (Auth::attempt([$fieldType => $this->email, 'password' => $this->password], $this->remember)) {
                request()->session()->regenerate();
                $this->redirect(RouteServiceProvider::HOME);
            } else {
                $this->addError('email', 'Password yang Anda masukkan salah.');
            }
            return;
        }

        // Untuk user biasa (debitur/investor), cek status akun
        if ($user->isAccountLocked()) {
            $this->addError('email', 'Akun Anda telah dikunci karena terlalu banyak percobaan login gagal. Silahkan hubungi admin untuk membuka kembali akun Anda.');
            return;
        }

        if ($user->isAccountNonActive()) {
            $this->addError('email', 'Akun Anda tidak aktif. Silahkan hubungi admin untuk informasi lebih lanjut.');
            return;
        }

        // Coba login
        if (Auth::attempt([$fieldType => $this->email, 'password' => $this->password], $this->remember)) {
            // Login sukses - reset login attempts
            $user->resetLoginAttempts();
            request()->session()->regenerate();
            $this->redirect(RouteServiceProvider::HOME);
        } else {
            // Login gagal - increment attempts
            $attempts = $user->incrementLoginAttempts();
            $remainingAttempts = User::MAX_LOGIN_ATTEMPTS - $attempts;

            if ($attempts >= User::MAX_LOGIN_ATTEMPTS) {
                // Lock the account
                $user->lockAccount();
                $this->addError('email', 'Akun Anda telah dikunci karena 3 kali percobaan login gagal. Silahkan hubungi admin untuk membuka kembali akun Anda.');
            } else {
                $this->addError('email', "Password salah. Sisa percobaan: {$remainingAttempts}");
            }
        }
    }
};
?>
<div>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">
                <!-- Login -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-6">
                            <a href="{{ route('login') }}" class="app-brand-link">
                                <span class="app-brand-link d-flex align-items-center gap-3">
                                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" width="40" />
                                </span>
                                <span class="app-brand-text demo text-heading fw-bold fs-2">SYFA</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <p class="mb-2">Login to your account</p>

                        <form class="mb-4" wire:submit.prevent="login">
                            <div class="mb-6">
                                <input wire:model="email" type="text"
                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                    placeholder="Email or Username" autocomplete="username" autofocus />
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-6 form-password-toggle">
                                <div class="input-group input-group-merge">
                                    <input wire:model="password" type="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Password" autocomplete="current-password"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer" id="togglePassword"
                                        onclick="togglePasswordVisibility()">
                                        <i class="ti ti-eye-off" id="togglePasswordIcon"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="my-8">
                                <div class="d-flex justify-content-between">
                                    <div class="form-check mb-0 ms-2">
                                        <input wire:model="remember" class="form-check-input" type="checkbox"
                                            id="remember-me" />
                                        <label class="form-check-label" for="remember-me"> Remember Me </label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot
                                            Password?</a>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-6">
                                <button class="btn btn-primary d-grid w-100" type="submit"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="login">Login</span>
                                    <span wire:loading wire:target="login">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"
                                            aria-hidden="true"></span>
                                        Loading...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('ti-eye-off');
                toggleIcon.classList.add('ti-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('ti-eye');
                toggleIcon.classList.add('ti-eye-off');
            }
        }
    </script>
</div>
