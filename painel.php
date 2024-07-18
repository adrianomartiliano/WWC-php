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
        body{
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
        <div class="viewer">
            <h2>Admin Viewer</h2>
            <p>Conteúdo restrito a administradores.</p>
            <!-- Conteúdo adicional para administradores -->
            <a href="add_cla.php">Adicionar Clã</a>
        </div>
    <?php endif; ?>
    <div class="card border-secondary mb-3" style="max-width: 18rem;">
        <div class="card-header">Aviso</div>
        <div class="card-body">
            <h5 class="card-title">Nova versão em Construção</h5>
            <p class="card-text">Obrigado por se cadastrar, continue visitando, semanalmente novas funcionalidades ao site serão adicionadas. Nesse espaço poderá alterar informações da sua conta aqui no site.</p>
        </div>
    </div>
</body>
</html>
