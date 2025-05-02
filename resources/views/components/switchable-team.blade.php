@props(['team', 'component' => 'dropdown-link'])

<form method="POST" action="{{ route('current-team.update') }}" x-data>
    @method('PUT')
    @csrf

    <!-- Hidden Team ID -->
    <input type="hidden" name="team_id" value="{{ $team->id }}">

    {{-- <x-dynamic-component :component="$component" href="#" x-on:click.prevent="$root.submit();"> --}}
        <button class="dropdown-item d-flex">
            @if (Auth::user()->isCurrentTeam($team))
                
            @endif

            <div class="truncate">{{ $team->name }}</div>
        </button>
    {{-- </x-dynamic-component> --}}
</form>
