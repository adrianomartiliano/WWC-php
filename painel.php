<?php
require 'components/menu.php';

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

// Verifique se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Obter o valor de admmaster da sessão
$admmaster = isset($_SESSION['admmaster']) ? $_SESSION['admmaster'] : 'N';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
    <style>
        h1{
            text-align: center;
        }
        .card{
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <h1>Bem-vindo a sua conta</h1>

    <?php if ($admmaster === 'S'): ?>
        <div class="card mb-3" style="width: 18rem;">
            <div class="card-body">
                <h5 class="card-title">Lista dos usuários</h5>
                <p class="card-text">Lista com todos os usuários do site.</p>
                <a href="lista_users.php" class="btn btn-primary">Usuários</a>
            </div>
        </div>
    <?php endif; ?>
    <?php
                include 'meus_dados.php';
            ?>
            
</body>
</html>
