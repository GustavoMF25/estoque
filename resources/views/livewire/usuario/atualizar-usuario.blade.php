<div>
    <form wire:submit.prevent="save" id="{{ $formId }}" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" class="form-control" wire:model.defer="name">
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" class="form-control" wire:model.defer="email">
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Perfil</label>
            <select class="form-control" wire:model.defer="perfil">
                <option value="">Selecione</option>
                <option value="admin">Administrador</option>
                <option value="operador">Operador</option>
                <option value="vendedor">Vendedor</option>
            </select>
            @error('perfil')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Nova Senha</label>
            <input type="password" class="form-control" wire:model.defer="password">
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Confirmar Nova Senha</label>
            <input type="password" class="form-control" wire:model.defer="password_confirmation">
        </div>

        <div class="mb-3">
            <label class="form-label">Foto de Perfil</label>
            <input type="file" class="form-control" wire:model="profile_photo">
            @if ($profile_photo)
                <img src="{{ $profile_photo->temporaryUrl() }}" class="mt-2 rounded" style="max-height: 120px;">
            @elseif($user->profile_photo_path)
                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" class="mt-2 rounded"
                    style="max-height: 120px;">
            @endif
            @error('profile_photo')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </form>
</div>
