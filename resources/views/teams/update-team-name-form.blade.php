<x-basic.content-page :title="__('Team Name')" :class="'card-primary'">
    <form submit="updateTeamName">
        {{ __('The team\'s name and owner information.') }}

        <div name="form">
            <!-- Team Owner Information -->
            <div class="col-span-6">
                <x-label value="{{ __('Team Owner') }}" />

                <div class="flex items-center mt-2">
                    <img class="w-12 h-12 rounded-full object-cover" src="{{ $team->owner->profile_photo_url }}"
                        alt="{{ $team->owner->name }}">

                    <div class="ms-4 leading-tight">
                        <div class="text-gray-900">{{ $team->owner->name }}</div>
                        <div class="text-gray-700 text-sm">{{ $team->owner->email }}</div>
                    </div>
                </div>
            </div>

            <!-- Team Name -->
            <div class="col-span-6 sm:col-span-4">
                <x-label for="name" value="{{ __('Team Name') }}" />

                <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name"
                    :disabled="!Gate::check('update', $team)" />

                <x-input-error for="name" class="mt-2" />
            </div>
        </div>

        @if (Gate::check('update', $team))
            <!-- Actions -->
            <div class="row mt-4">
                <div class="col-2">
                    <x-action-message class="me-3" on="saved">
                        {{ __('Saved.') }}
                    </x-action-message>

                    <x-button wire:loading.attr="disabled">
                        {{ __('Save') }}
                    </x-button>
                </div>
            </div>
            
        @endif
    </form>

</x-basic.content-page>
