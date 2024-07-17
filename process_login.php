<?php
session_start(); // Inicia a sessão para uso posterior

// Incluir o arquivo de conexão com o banco de dados
require_once 'db.php';

// Capturar dados do formulário
$iduser = $_POST['iduser'];
$password = $_POST['password'];

// Debug: Verificar se os dados estão sendo capturados corretamente
if (empty($iduser) || empty($password)) {
    die("Id no Jogo ou Password não foram preenchidos corretamente.");
} else {
    echo "Id no Jogo: $iduser<br>";
    echo "Password: $password<br>";
}

// Preparar e executar consulta
$stmt = $conn->prepare("SELECT password FROM users WHERE iduser=?");
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("i", $iduser); // Usar "i" para inteiro
$stmt->execute();
$stmt->store_result();

// Debug: Verificar se a consulta encontrou algum resultado
if ($stmt->num_rows > 0) {
    echo "Usuário encontrado.<br>";
    $stmt->bind_result($stored_password);
    $stmt->fetch();

    // Verificar a senha
    if ($password === $stored_password) { // Comparação direta sem hashing
        // Credenciais válidas
        $_SESSION['loggedin'] = true; // Definir uma variável de sessão
        $_SESSION['iduser'] = $iduser;
        header("Location: painel.php");
        exit(); // Certifique-se de sair após redirecionar
    } else {
        // Senha incorreta
        $error_message = "Password incorreto.";
    }
} else {
    // Id no Jogo não encontrado
    $error_message = "Id no Jogo não encontrado.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <?php
    if (isset($error_message)) {
        echo "<p>$error_message</p>";
    }
    ?>
    <a href="index.html">Voltar</a>
</body>
</html>
