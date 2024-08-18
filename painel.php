<?php

    include 'components/menu.php';

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: login.php");
        exit;
    }

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
            margin-top: 20px;
            text-align: center;
        }
        .card{
            margin: 0 auto;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-1">
    <h1>Bem-vindo a sua conta</h1>

    <?php if ($admmaster === 'S'): ?>
        <div class="card mb-3" >
            <div class="card-body">
                <h5 class="card-title">Lista dos usuários</h5>
                <p class="card-text">Lista com todos os usuários do site.</p>
                <a href="lista_users.php" class="btn btn-primary">Usuários</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Aqui ficarão os torneios que estou inscrito</h5>
            <p class="card-text">Em desenvolvimento.</p>
        </div>
    </div>


    <?php
                include 'meus_dados.php';
            ?>
    
</body>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</html>
