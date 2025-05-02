<x-app-layout>
    <x-basic.content-page :class="'card-secondary'" :title="__('Cadastrar Usuario')">
        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nome</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" required>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" class="form-control @error('cpf') is-invalid @enderror"
                    value="{{ old('cpf') }}">
                @error('cpf')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="ativo" {{ old('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="inativo" {{ old('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
                @error('status')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="perfil">Perfil</label>
                <select name="perfil" class="form-control @error('perfil') is-invalid @enderror" required>
                    <option value="admin" {{ old('perfil') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="operador" {{ old('perfil') == 'operador' ? 'selected' : '' }}>Operador</option>
                    <option value="gerente" {{ old('perfil') == 'gerente' ? 'selected' : '' }}>Gerente</option>
                </select>
                @error('perfil')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    required>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Senha</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success">Salvar</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>

    </x-basic.content-page>
</x-app-layout>
