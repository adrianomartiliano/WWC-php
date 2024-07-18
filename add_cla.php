<?php
session_start();

// Verifique se o usuário está logado e é administrador
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['admmaster'] !== 'S') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Clã</title>
    <link rel="stylesheet" href="path_to_your_css_file.css"> <!-- Adicione seu arquivo CSS aqui -->
</head>
<body>
    <h1>Adicionar Clã</h1>
    <form action="process_add_cla.php" method="post">
        <div class='mb-3'>
            <label for='InputSigla' class='form-label'>Sigla</label>
            <input type='text' class='form-control' id='InputSigla' name='sigla' required>
        </div>
        <div class='mb-3'>
            <label for='InputNome' class='form-label'>Nome</label>
            <input type='text' class='form-control' id='InputNome' name='nome' required>
        </div>
        <button type='submit' class='btn btn-secondary'>Adicionar</button>
    </form>
</body>
</html>
