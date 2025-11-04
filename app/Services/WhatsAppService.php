<?php

namespace App\Services;

use WPPConnect\WPPService;

class WhatsAppService
{
    public function enviarMensagem($numero, $mensagem)
    {
        // Certifique-se de que a instância do WPPConnect está ativa
        if (WPPService::isLoggedIn()) {
            // Enviar a mensagem via WhatsApp
            WPPService::sendMessage($numero, $mensagem);
        } else {
            // Aqui você pode implementar a lógica de reenvio ou erro
            throw new \Exception('WhatsApp não está logado');
        }
    }

    
}
