<?php
/**
 * Funções utilitárias globais do sistema OdontoCare
 */

/**
 * Redireciona para uma URL com mensagem opcional
 */
function redirecionar($url, $mensagem = null, $tipo = 'success') {
    if ($mensagem) {
        session_start();
        $_SESSION['flash_message'] = [
            'texto' => $mensagem,
            'tipo' => $tipo
        ];
    }
    header("Location: $url");
    exit;
}

/**
 * Obtém mensagem flash da sessão
 */
function getFlashMessage() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

/**
 * Exibe mensagem flash se existir
 */
function exibirFlashMessage() {
    $msg = getFlashMessage();
    if ($msg) {
        $classe = 'alert-' . $msg['tipo'];
        echo "<div class='alert $classe'>" . htmlspecialchars($msg['texto']) . "</div>";
    }
}

/**
 * Formata valor monetário
 */
function formatarDinheiro($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Converte valor monetário para decimal
 */
function parseDinheiro($valor) {
    if (empty($valor)) return 0;
    $valor = preg_replace('/[^\d,-]/', '', (string)$valor);
    $valor = str_replace(',', '.', $valor);
    return floatval($valor);
}

/**
 * Formata data para exibição
 */
function formatarData($data) {
    if (empty($data)) return '';
    return date('d/m/Y', strtotime($data));
}

/**
 * Formata data e hora
 */
function formatarDataHora($data) {
    if (empty($data)) return '';
    return date('d/m/Y H:i', strtotime($data));
}

/**
 * Converte data do formato brasileiro para ISO
 */
function parseData($data) {
    if (empty($data)) return null;
    $partes = explode('/', $data);
    if (count($partes) === 3) {
        return "$partes[2]-$partes[1]-$partes[0]";
    }
    return $data;
}

/**
 * Calcula idade a partir da data de nascimento
 */
function calcularIdade($dataNascimento) {
    if (empty($dataNascimento)) return null;
    $nascimento = new DateTime($dataNascimento);
    $hoje = new DateTime();
    return $hoje->diff($nascimento)->y;
}

/**
 * Máscara para CPF
 */
function mascararCPF($cpf) {
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) !== 11) return $cpf;
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
}

/**
 * Máscara para telefone
 */
function mascararTelefone($telefone) {
    $telefone = preg_replace('/\D/', '', $telefone);
    if (strlen($telefone) === 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
    }
    if (strlen($telefone) === 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
    }
    return $telefone;
}

/**
 * Gera slug a partir de string
 */
function gerarSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = preg_replace('/[^a-z0-9-]/', '', $string);
    return $string;
}

/**
 * Limita texto
 */
function limitarTexto($texto, $limite = 100) {
    if (strlen($texto) <= $limite) return $texto;
    return substr($texto, 0, $limite) . '...';
}

/**
 * Verifica se usuário é admin
 */
function isAdmin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['perfil']) && strtolower($_SESSION['perfil']) === 'admin';
}

/**
 * Verifica permissão e redireciona se não tiver
 */
function requerAdmin() {
    if (!isAdmin()) {
        redirecionar('/clinica_odonto_completa/dashboard/index.php', 
            'Acesso negado. Permissão de administrador necessária.', 'danger');
    }
}

/**
 * Sanitiza input
 */
function sanitizar($dado) {
    return htmlspecialchars(trim($dado), ENT_QUOTES, 'UTF-8');
}

/**
 * Gera token CSRF
 */
function gerarTokenCSRF() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica token CSRF
 */
function verificarTokenCSRF($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Obtém primeiro e último nome
 */
function primeiroUltimoNome($nomeCompleto) {
    $partes = explode(' ', trim($nomeCompleto));
    if (count($partes) === 1) return $partes[0];
    return $partes[0] . ' ' . end($partes);
}

/**
 * Status com cores
 */
function statusBadge($status) {
    $status = strtolower($status);
    $classes = [
        'ativo' => 'badge-success',
        'inativo' => 'badge-danger',
        'pendente' => 'badge-warning',
        'confirmado' => 'badge-info',
        'agendado' => 'badge-info',
        'concluido' => 'badge-success',
        'cancelado' => 'badge-danger',
        'pago' => 'badge-success',
        'recebido' => 'badge-success',
    ];
    
    $classe = $classes[$status] ?? 'badge-secondary';
    return "<span class='badge $classe'>" . ucfirst($status) . "</span>";
}

/**
 * Gera cor aleatória para gráficos
 */
function gerarCor($index = null) {
    $cores = [
        '#667eea', '#764ba2', '#f093fb', '#f5576c',
        '#4facfe', '#00f2fe', '#43e97b', '#38f9d7',
        '#fa709a', '#fee140', '#30cfd0', '#330867'
    ];
    
    if ($index !== null) {
        return $cores[$index % count($cores)];
    }
    
    return $cores[array_rand($cores)];
}

/**
 * Obtém nome do mês em português
 */
function nomeMes($mes) {
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
        4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
        7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
        10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];
    return $meses[$mes] ?? '';
}

/**
 * Dia da semana em português
 */
function diaSemana($data) {
    $dias = [
        'Sunday' => 'Domingo',
        'Monday' => 'Segunda-feira',
        'Tuesday' => 'Terça-feira',
        'Wednesday' => 'Quarta-feira',
        'Thursday' => 'Quinta-feira',
        'Friday' => 'Sexta-feira',
        'Saturday' => 'Sábado'
    ];
    
    $dia = date('l', strtotime($data));
    return $dias[$dia] ?? $dia;
}
