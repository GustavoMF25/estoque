{{-- updateProfileInformation --}}
<div>
    <form wire:submit.prevent="updateProfileInformation" id="profileForm" enctype="multipart/form-data">

        <div class="card card-secondary">
            <div class="card-header">
                <h4 class="card-title w-100">
                    {{ __('Profile Information') }}
                </h4>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <!-- Dropzone de Foto de Perfil -->
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">

                                <label class="form-label">{{ __('Profile Photo') }}</label>

                                <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">

                                    <!-- Ações -->
                                    <div id="actions" class="row">
                                        <div class="col-lg-6">
                                            <div class="btn-group w-100">
                                                <!-- Botão de escolher arquivo -->
                                                <label class="btn btn-success col fileinput-button mb-0">
                                                    <i class="fas fa-plus"></i>
                                                    <span>Add files</span>
                                                    <input id="photo" type="file" accept="image/png, image/jpeg" class="d-none"
                                                        wire:model.live="photo" x-ref="photo"
                                                        x-on:change="
                                                            photoName = $refs.photo.files[0].name;
                                                            const reader = new FileReader();
                                                            reader.onload = (e) => {
                                                                photoPreview = e.target.result;
                                                            };
                                                            reader.readAsDataURL($refs.photo.files[0]);
                                                        " />
                                                </label>

                                                <!-- Botão de cancelar -->
                                                <button type="button" class="btn btn-warning col cancel"
                                                    x-on:click.prevent="photoName = null; photoPreview = null; $refs.photo.value = '';">
                                                    <i class="fas fa-times-circle"></i>
                                                    <span>Cancel upload</span>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- <!-- Barra de progresso -->
                                        <div class="col-lg-6 d-flex align-items-center">
                                            <div class="fileupload-process w-100">
                                                <div id="total-progress" class="progress progress-striped active"
                                                    role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                    aria-valuenow="0">
                                                    <div class="progress-bar progress-bar-success"
                                                        x-bind:style="photoPreview ? 'width: 100%' : 'width: 0%'"></div>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>

                                    <!-- Preview Atual -->
                                    <div class="mt-4" x-show="photoPreview" style="display: none;">
                                        <img :src="photoPreview" class="rounded-full object-cover" width="150" height="150">
                                    </div>

                                    <x-input-error for="photo" class="mt-2" />
                                </div>

                                <!-- Preview Atual -->
                                <div class="mt-2">
                                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}"
                                        class="rounded-full object-cover" width="150" height="150"
                                        id="previewPhoto">
                                </div>

                                <x-input-error for="photo" class="mt-2" />
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <!-- Name -->
                        <div class="col-span-6 sm:col-span-4 mt-4">
                            <x-label for="name" value="{{ __('Name') }}" />
                            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name"
                                required autocomplete="name" />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-4 mt-4">
                            <x-label for="email" value="{{ __('Email') }}" />
                            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email"
                                required autocomplete="username" />
                            <x-input-error for="email" class="mt-2" />

                            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) &&
                                    !$this->user->hasVerifiedEmail())
                                <p class="text-sm mt-2">
                                    {{ __('Your email address is unverified.') }}
                                    <button type="button"
                                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        wire:click.prevent="sendEmailVerification">
                                        {{ __('Click here to re-send the verification email.') }}
                                    </button>
                                </p>

                                @if ($this->verificationLinkSent)
                                    <p class="mt-2 font-medium text-sm text-green-600">
                                        {{ __('A new verification link has been sent to your email address.') }}
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>



                <!-- Actions -->
                <div class="row mt-4">
                    <div class="col-md-10">
                        <b>
                            {{ __('Update your account\'s profile information and email address.') }}
                        </b>
                    </div>
                    <div class="col-md-2 col-12">
                        <x-action-message class="me-3" on="saved">
                            {{ __('Saved.') }}
                        </x-action-message>

                        <x-button wire:loading.attr="disabled">
                            {{ __('Save') }}
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

@section('js')
    <script>
        Dropzone.autoDiscover = false;

        const photoDropzone = new Dropzone("#photoDropzone", {
            url: "/#",
            autoProcessQueue: false,
            uploadMultiple: false,
            maxFiles: 1,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            dictDefaultMessage: "Arraste a imagem ou clique para enviar",
            init: function() {
                this.on("addedfile", file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        document.getElementById('previewPhoto').src = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    const livewireInput = document.createElement('input');
                    livewireInput.type = 'file';
                    livewireInput.files = this.files;
                    livewireInput.style.display = 'none';
                    document.body.appendChild(livewireInput);

                    // Simular o wire:model no componente
                    Livewire.find(document.querySelector('[wire\\:submit]').getAttribute('wire:id'))
                        .upload('photo', file, (uploadedFilename) => {
                            console.log('Upload concluído:', uploadedFilename);
                        }, () => {
                            console.error('Erro ao enviar.');
                        });
                });

                this.on("maxfilesexceeded", file => {
                    this.removeAllFiles();
                    this.addFile(file);
                });
            }
        });
    </script>
@endsection
