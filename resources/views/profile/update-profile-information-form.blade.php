<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        <h4 class="mb-1">{{ __('Profile Information') }}</h4>
    </x-slot>

    <x-slot name="description">
        <p class="text-muted mb-3">{{ __("Update your account's profile information and email address.") }}</p>
    </x-slot>

    <x-slot name="form">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="mb-3">
                <label for="photo" class="form-label">{{ __('Photo') }}</label>

                <!-- Current Profile Photo -->
                <div x-show="! photoPreview" class="mb-2">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}"
                        class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                </div>

                <!-- New Profile Photo Preview -->
                <div x-show="photoPreview" class="mb-2" style="display: none;">
                    <span class="d-block rounded-circle border"
                        style="width: 80px; height: 80px; background-size: cover; background-position: center;"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <input type="file" id="photo" class="d-none"
                    wire:model.live="photo" x-ref="photo"
                    x-on:change="
                        photoName = $refs.photo.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => photoPreview = e.target.result;
                        reader.readAsDataURL($refs.photo.files[0]);
                    ">

                <div class="d-flex gap-2 mt-2">
                    <button type="button" class="btn btn-outline-primary btn-sm"
                        x-on:click.prevent="$refs.photo.click()">
                        {{ __('Select A New Photo') }}
                    </button>

                    @if ($this->user->profile_photo_path)
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            wire:click="deleteProfilePhoto">
                            {{ __('Remove Photo') }}
                        </button>
                    @endif
                </div>

                <x-input-error for="photo" class="text-danger mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" type="text" class="form-control"
                wire:model="state.name" required autocomplete="name">
            <x-input-error for="name" class="text-danger mt-1" />
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" class="form-control"
                wire:model="state.email" required autocomplete="username">
            <x-input-error for="email" class="text-danger mt-1" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-muted small mb-1">
                        {{ __('Your email address is unverified.') }}
                    </p>
                    <button type="button" class="btn btn-link p-0"
                        wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>

                    @if ($this->verificationLinkSent)
                        <p class="text-success small mt-1">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="d-flex align-items-center gap-2">
            <x-action-message class="text-success" on="saved">
                {{ __('Saved.') }}
            </x-action-message>

            <button type="submit" class="btn btn-primary"
                wire:loading.attr="disabled" wire:target="photo">
                {{ __('Save') }}
            </button>
        </div>
    </x-slot>
</x-form-section>