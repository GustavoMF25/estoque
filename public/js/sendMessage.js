// sendMessage.js
const wppconnect = require('@wppconnect-team/wppconnect');

const numero = process.argv[2];  // O número de WhatsApp
const mensagem = process.argv[3]; // A mensagem a ser enviada

wppconnect.create({
    session: 'whatsapp_session', // Nome da sessão
    headless: true,
    useChrome: true,
    debug: false,
    disableSpins: true,
    multiDevice: true,
})
.then(client => {
    // Envia a mensagem
    client.sendText(numero, mensagem)
        .then(() => {
            console.log('Mensagem enviada com sucesso!');
        })
        .catch(error => {
            console.error('Erro ao enviar a mensagem: ', error);
        });
})
.catch(error => {
    console.error('Erro ao iniciar o WhatsApp: ', error);
});
