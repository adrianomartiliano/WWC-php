<?php

include '../db/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário é um administrador
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['admmaster'] !== 'S') {
    header("Location: ../login.php");
    exit;
}

// Obtém os dados do formulário
$idUsuario = isset($_POST['id_user']) ? intval($_POST['id_user']) : 0;
$valorAdicionado = isset($_POST['valor_apoiado']) ? floatval($_POST['valor_apoiado']) : 0.00;

if ($idUsuario > 0 && $valorAdicionado > 0) {
    // Atualizar o valor apoiado do usuário
    $sqlUpdateUser = "UPDATE users SET valor_apoiado = valor_apoiado + ? WHERE iduser = ?";
    $stmtUpdateUser = $conn->prepare($sqlUpdateUser);
    $stmtUpdateUser->bind_param('di', $valorAdicionado, $idUsuario);

    if ($stmtUpdateUser->execute()) {
        // Redireciona para a página de apoiadores com uma mensagem de sucesso
        header("Location: ../apoiadores.php?message=success");
    } else {
        // Redireciona com mensagem de erro
        header("Location: ../apoiadores.php?message=error_update");
    }
} else {
    // Redireciona com mensagem de dados inválidos
    header("Location: ../apoiadores.php?message=invalid_data");
}

?>
