<form wire:submit="updatePassword">
    <div class="card card-primary">
        <div class="card-header">
            <h4 class="card-title w-100">
                {{ __('Update Password') }}
            </h4>
        </div>
        <div class="card-body">


            <small>
                {{ __('Ensure your account is using a long, random password to stay secure.') }}
            </small>

            <div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="current_password" value="{{ __('Current Password') }}" />
                    <x-input id="current_password" type="password" wire:model="state.current_password" autocomplete="current-password" :icon="'fas fa-lock'" />
                    <x-input-error for="current_password" class="mt-2" />
                </div>

                <div class="col-span-6 sm:col-span-4">
                    <x-label for="password" value="{{ __('New Password') }}" />
                    <x-input id="password" type="password" wire:model="state.password" autocomplete="new-password" :icon="'fas fa-lock'" />
                    <x-input-error for="password" class="mt-2" />
                </div>

                <div class="col-span-6 sm:col-span-4">
                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                    <x-input id="password_confirmation" type="password" wire:model="state.password_confirmation" autocomplete="new-password" :icon="'fas fa-lock'" />
                    <x-input-error for="password_confirmation" class="mt-2" />
                </div>
            </div>

            <div name="actions">
                <x-action-message class="me-3" on="saved">
                    {{ __('Saved.') }}
                </x-action-message>

                <x-button>
                    {{ __('Save') }}
                </x-button>
            </div>
        </div>
    </div>

</form>
