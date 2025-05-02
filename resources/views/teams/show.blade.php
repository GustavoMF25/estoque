<x-app-layout>

    <div class="row content">
        <div class="col-6">
            @livewire('teams.update-team-name-form', ['team' => $team])
        </div>
        <div class="col-6">
            @livewire('teams.team-member-manager', ['team' => $team])
        </div>

        {{-- @if (Gate::check('delete', $team) && !$team->personal_team)
            <div class="col-12">


                <div class="mt-10 sm:mt-0">
                    @livewire('teams.delete-team-form', ['team' => $team])
                </div>
            </div>
        @endif --}}


    </div>







</x-app-layout>
