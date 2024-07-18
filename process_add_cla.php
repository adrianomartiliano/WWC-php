<?php
session_start();

// Verifique se o usuário está logado e é administrador
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['admmaster'] !== 'S') {
    header("Location: login.php");
    exit;
}

// Incluir o arquivo de conexão com o banco de dados
require_once 'db.php';

// Capturar dados do formulário
$sigla = $_POST['sigla'];
$nome = $_POST['nome'];

// Verificar se os dados foram preenchidos
if (empty($sigla) || empty($nome)) {
    die("Sigla ou Nome não foram preenchidos corretamente.");
}

// Preparar e executar a consulta de inserção
$stmt = $conn->prepare("INSERT INTO cla (siglacla, nomecla) VALUES (?, ?)");
if (!$stmt) {
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("ss", $sigla, $nome); // Usar "ss" para strings
if ($stmt->execute()) {
    echo "Clã adicionado com sucesso!";
} else {
    echo "Erro ao adicionar clã: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado da Adição</title>
</head>
<body>
    <a href="add_cla.php">Adicionar outro clã</a>
    <a href="painel.php">Voltar ao Painel</a>
</body>
</html>
