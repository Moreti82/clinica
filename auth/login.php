<?php
session_start();

$erro_login = $_SESSION['erro_login'] ?? '';
unset($_SESSION['erro_login']);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>OdontoCare - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style-login.css">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body id="login-page">

    <!-- Header do sistema dentro do body -->
    <div class="system-header">
        <h1 class="system-title">OdontoCare</h1>
        <p class="system-subtitle">Gestão inteligente para clínicas odontológicas</p>
    </div>

    <div class="container login-container">
        <h2 class="login-title">Login</h2>
        <p id="erroLogin" style="display:none; color:red;"><?php echo htmlspecialchars($erro_login); ?></p>


        <form action="processa_login.php" method="POST" class="login-form">
            <label for="email" class="login-label"><i class="fa-solid fa-envelope"></i> Email</label>
            <input type="email" name="email" id="email" class="login-input">

            <div class="password-wrapper">
                <label for="senha" class="login-label"><i class="fa-solid fa-lock"></i> Senha</label>
                <input type="password" name="senha" id="senha" class="login-input">
                <span class="toggle-password">
                    <i class="fa-solid fa-eye" onclick="togglePassword()"></i>
                </span>
            </div>

            <button type="submit" class="btn login-btn">
                <i class="fa-solid fa-right-to-bracket"></i> Entrar
            </button>
        </form>
    </div>

        <script src="../assets/js/login.js"></script>

</body>
</html>
