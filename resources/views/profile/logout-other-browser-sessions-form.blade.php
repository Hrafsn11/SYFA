<x-action-section>
    <x-slot name="title">
        <h4 class="mb-1">{{ __('Browser Sessions') }}</h4>
    </x-slot>

    <x-slot name="description">
        <p class="text-muted mb-3">
            {{ __('Manage and log out your active sessions on other browsers and devices.') }}
        </p>
    </x-slot>

    <x-slot name="content">
        <p class="text-muted small mb-4">
            {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.') }}
        </p>

        @if (count($this->sessions) > 0)
            <div class="mt-3">
                @foreach ($this->sessions as $session)
                    <div class="d-flex align-items-center border rounded-3 p-3 mb-3 bg-light">
                        <div class="me-3">
                            @if ($session->agent->isDesktop())
                                <!-- SVG Computer -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                     class="text-secondary" width="36" height="36">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 
                                          2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 
                                          0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 
                                          0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 
                                          2.25 0 013 12V5.25" />
                                </svg>
                            @else
                                <!-- SVG Phone -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                     class="text-secondary" width="36" height="36">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M10.5 1.5H8.25A2.25 2.25 0 006 
                                          3.75v16.5a2.25 2.25 0 002.25 
                                          2.25h7.5A2.25 2.25 0 0018 
                                          20.25V3.75a2.25 2.25 0 
                                          00-2.25-2.25H13.5m-3 
                                          0V3h3V1.5m-3 0h3m-3 
                                          18.75h3" />
                                </svg>
                            @endif
                        </div>

                        <div>
                            <div class="fw-semibold">
                                {{ $session->agent->platform() ?: __('Unknown') }}
                                -
                                {{ $session->agent->browser() ?: __('Unknown') }}
                            </div>

                            <div class="text-muted small">
                                {{ $session->ip_address }},
                                @if ($session->is_current_device)
                                    <span class="text-success fw-semibold">{{ __('This device') }}</span>
                                @else
                                    {{ __('Last active') }} {{ $session->last_active }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="d-flex align-items-center mt-4 gap-2">
            <button type="button" class="btn btn-danger"
                    wire:click="confirmLogout"
                    wire:loading.attr="disabled">
                {{ __('Log Out Other Browser Sessions') }}
            </button>

            <x-action-message class="text-success ms-2" on="loggedOut">
                {{ __('Done.') }}
            </x-action-message>
        </div>

        <!-- Modal Konfirmasi Logout -->
        <x-dialog-modal wire:model.live="confirmingLogout">
            <x-slot name="title">
                {{ __('Log Out Other Browser Sessions') }}
            </x-slot>

            <x-slot name="content">
                <p class="text-muted small mb-3">
                    {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}
                </p>

                <div class="mt-3" x-data="{}"
                     x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                    <input type="password"
                           class="form-control"
                           placeholder="{{ __('Password') }}"
                           x-ref="password"
                           wire:model="password"
                           wire:keydown.enter="logoutOtherBrowserSessions"
                           autocomplete="current-password">

                    <x-input-error for="password" class="text-danger mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button" class="btn btn-secondary"
                        wire:click="$toggle('confirmingLogout')"
                        wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </button>

                <button type="button" class="btn btn-danger ms-2"
                        wire:click="logoutOtherBrowserSessions"
                        wire:loading.attr="disabled">
                    {{ __('Log Out Other Browser Sessions') }}
                </button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
