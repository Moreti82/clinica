<?php
try {
    $db = new PDO("sqlite:" . __DIR__ . "/../db/clinica.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("PRAGMA foreign_keys = ON");
} catch (Exception $e) {
    die("Erro ao conectar com o banco: " . $e->getMessage());
}
