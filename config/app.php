<?php
/**
 * Configuração da aplicação
 *
 * BASE_URL: caminho público da app a partir da raiz do servidor.
 * - Servidor embutido (php -S) ou vhost na raiz: deixe ''
 * - Subpasta no Apache/WAMP (ex.: http://localhost/clinica_odonto_completa/): use '/clinica_odonto_completa'
 */
if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}
