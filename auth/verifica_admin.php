<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['perfil']) ||
    strtolower($_SESSION['perfil']) !== 'admin'
) {
    $_SESSION['erro_login'] = "Você não tem permissão para acessar esta página.";
    header("Location: ../auth/login.php");
    exit;
}
