<div>
    <dialog id="logoutSessionsModal" wire:model="confirmingLogout" class="modal fade" tabindex="-1" role="dialog"
        style="padding: 0; border: none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Log Out Other Browser Sessions') }}</h5>
                    <button type="button" class="close"
                        onclick="document.getElementById('logoutSessionsModal').close()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}

                    <div class="mt-4" x-data
                        x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                        <input type="password" class="form-control mt-1" placeholder="{{ __('Password') }}"
                            autocomplete="current-password" x-ref="password" wire:model.defer="password"
                            wire:keydown.enter="logoutOtherBrowserSessions" />

                        @error('password')
                            <span class="text-danger mt-2">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('confirmingLogout', false)"
                        wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </button>

                    <button type="button" class="btn btn-primary" wire:click="logoutOtherBrowserSessions"
                        wire:loading.attr="disabled">
                        {{ __('Log Out Other Browser Sessions') }}
                    </button>
                </div>

            </div>
        </div>
    </dialog>

    <style>
        dialog::backdrop {
            background: rgba(0, 0, 0, 0.5);
        }

        dialog[open] {
            display: flex !important;
            position: fixed !important;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            justify-content: center;
            align-items: center;
            background: transparent;
            padding: 0;
            border: none;
            margin: 0;
            z-index: 1050;
        }
    </style>

</div>
