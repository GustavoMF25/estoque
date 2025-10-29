<x-app-layout>
    <x-basic.content-page :title="__('Assinaturas')" :class="'card-secondary'" :btnCadastrar="['route' => route('assinaturas.create'), 'title' => 'Cadastrar Assinante']">
        <div class="text-center py-5">
            <h2>🚫 Assinatura Expirada</h2>
            <p>Sua assinatura está inativa. Entre em contato com o suporte para renovação.</p>

            <a href="https://wa.me/5521974332531?text=Olá,%20minha%20assinatura%20está%20expirada" target="_blank"
                class="btn btn-success mt-3">
                Falar com Suporte via WhatsApp
            </a>
        </div>
    </x-basic.content-page>
</x-app-layout>
