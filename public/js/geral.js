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
