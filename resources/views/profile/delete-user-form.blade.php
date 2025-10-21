<x-action-section>
    <x-slot name="title">
        <h4 class="mb-1">{{ __('Delete Account') }}</h4>
    </x-slot>

    <x-slot name="description">
        <p class="text-muted mb-3">
            {{ __('Permanently delete your account.') }}
        </p>
    </x-slot>

    <x-slot name="content">
        <p class="text-muted small mb-4">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>

        <div class="mt-3">
            <button type="button"
                    class="btn btn-danger"
                    wire:click="confirmUserDeletion"
                    wire:loading.attr="disabled">
                <i class="bi bi-trash me-1"></i> {{ __('Delete Account') }}
            </button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingUserDeletion">
            <x-slot name="title">
                <h5 class="mb-0 text-danger">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ __('Delete Account') }}
                </h5>
            </x-slot>

            <x-slot name="content">
                <p class="text-muted small mb-3">
                    {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently removed. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <div class="mt-3" x-data="{}"
                     x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <input type="password"
                           class="form-control"
                           placeholder="{{ __('Password') }}"
                           x-ref="password"
                           wire:model="password"
                           wire:keydown.enter="deleteUser"
                           autocomplete="current-password">

                    <x-input-error for="password" class="text-danger mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button"
                        class="btn btn-secondary"
                        wire:click="$toggle('confirmingUserDeletion')"
                        wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </button>

                <button type="button"
                        class="btn btn-danger ms-2"
                        wire:click="deleteUser"
                        wire:loading.attr="disabled">
                    <i class="bi bi-trash me-1"></i> {{ __('Delete Account') }}
                </button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>