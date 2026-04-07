<?php
/**
 * Footer padrão do sistema OdontoCare
 */
?>
    </main>
    
    <script>
        // Auto-hide alerts após 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
        
        // Confirmação para ações destrutivas
        function confirmarExclusao(mensagem) {
            return confirm(mensagem || 'Tem certeza que deseja excluir este registro?');
        }
        
        // Formatar valores monetários
        function formatarMoeda(valor) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(valor);
        }
        
        // Formatar datas
        function formatarData(data) {
            return new Intl.DateTimeFormat('pt-BR').format(new Date(data));
        }
        
        // Máscara para CPF
        function mascararCPF(cpf) {
            return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        }
        
        // Máscara para telefone
        function mascararTelefone(telefone) {
            return telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        }
    </script>
    
    <?php if (isset($page_js)): ?>
    <script src="<?php echo $page_js; ?>"></script>
    <?php endif; ?>
    
</body>
</html>
