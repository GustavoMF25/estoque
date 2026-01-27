function abrirModal(id, conteudo = '', textoConfirmar = '', formId = '') {
    const modal = document.getElementById(id);
    if (!modal) return console.error(`Modal com ID '${id}' não encontrado.`);

    // Atualiza conteúdo
    const content = modal.querySelector('#modalContent');
    if (content && conteudo) content.innerText = conteudo;

    const confirmBtn = modal.querySelector('#confirmarBtn');
    if (confirmBtn && textoConfirmar) confirmBtn.innerText = textoConfirmar;

    if (typeof modal.open === 'boolean' && modal.open) {
        modal.close();
    }

    if (typeof modal.showModal === 'function') {
        modal.showModal();
    } else {
        modal.setAttribute('open', true);
    }
}

function abrirModalDinamico(dados) {
    window.dispatchEvent(new CustomEvent('abrirModal', {
        detail: dados

    }));
    $('#modal-sm').modal('show');
    $(document).ready(function () {
        $('#categoriaSelect').select2();
        $('.select2').on('change', function (e) {
            var data = $(this).select2("val");
            console.log(data)
        });
    });
}

document.addEventListener('click', function (event) {
    const openDialogs = document.querySelectorAll('dialog[open]');
    openDialogs.forEach(dialog => {
        const rect = dialog.getBoundingClientRect();
        if (
            event.clientX < rect.left ||
            event.clientX > rect.right ||
            event.clientY < rect.top ||
            event.clientY > rect.bottom
        ) {
            dialog.close();
        }
    });
});

// CEP auto-fill helper (ViaCEP)
(function () {
    function limparCep(cep) {
        return (cep || '').replace(/\D/g, '');
    }

    function setValue(el, value) {
        if (!el) return;
        el.value = value || '';
        el.dispatchEvent(new Event('input', { bubbles: true }));
        el.dispatchEvent(new Event('change', { bubbles: true }));
    }

    const timers = new WeakMap();

    function notifyCepNotFound(input) {
        if (window.toastr && typeof window.toastr.error === 'function') {
            window.toastr.error('CEP nao encontrado.');
        } else {
            alert('CEP nao encontrado.');
        }
        if (!input || !input.dataset) return;
        const rua = document.getElementById(input.dataset.cepRua);
        const bairro = document.getElementById(input.dataset.cepBairro);
        const cidade = document.getElementById(input.dataset.cepCidade);
        const estado = document.getElementById(input.dataset.cepEstado);
        setValue(rua, '');
        setValue(bairro, '');
        setValue(cidade, '');
        setValue(estado, '');
    }

    function handleCepInput(input) {
        if (!input || !input.dataset || !input.dataset.cepLookup) return;

        clearTimeout(timers.get(input));
        // Apply mask 00000-000 while typing
        const raw = limparCep(input.value);
        let masked = raw.slice(0, 8);
        if (masked.length > 5) {
            masked = masked.slice(0, 5) + '-' + masked.slice(5);
        }
        input.value = masked;

        const timer = setTimeout(function () {
            const cep = limparCep(input.value);
            if (cep.length !== 8) return;

            fetch('https://viacep.com.br/ws/' + cep + '/json/')
                .then(function (resp) { return resp.json(); })
                .then(function (data) {
                    if (!data || data.erro) {
                        notifyCepNotFound(input);
                        return;
                    }
                    const rua = document.getElementById(input.dataset.cepRua);
                    const bairro = document.getElementById(input.dataset.cepBairro);
                    const cidade = document.getElementById(input.dataset.cepCidade);
                    const estado = document.getElementById(input.dataset.cepEstado);
                    setValue(rua, data.logradouro);
                    setValue(bairro, data.bairro);
                    setValue(cidade, data.localidade);
                    setValue(estado, data.uf);
                })
                .catch(function () { });
        }, 400);

        timers.set(input, timer);
    }

    document.addEventListener('input', function (event) {
        const target = event.target;
        if (target && target.matches && target.matches('[data-cep-lookup]')) {
            handleCepInput(target);
        }
    });
})();
