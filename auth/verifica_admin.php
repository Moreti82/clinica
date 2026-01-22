<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['perfil'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}
