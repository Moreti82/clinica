document.addEventListener("DOMContentLoaded", function() {
        // Selecionar as mensagens de sucesso ou erro
        const mensagens = document.querySelectorAll(".mensagem-alerta");

        // Verificar se existem mensagens
        if (mensagens.length > 0) {
            // Após 3 segundos, as mensagens vão desaparecer
            setTimeout(() => {
                mensagens.forEach(function(mensagem) {
                    // Adiciona a transição de opacidade para sumir
                    mensagem.style.opacity = '0';

                    // Após 1 segundo, remove a mensagem do DOM
                    setTimeout(() => {
                        mensagem.remove();
                    }, 1000); // Aguardar 1 segundo para a transição
                });
            }, 3000); // Tempo para a mensagem desaparecer
        }
    });