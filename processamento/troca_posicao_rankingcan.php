<?php
session_start();
include '../db/db.php';

// Verifica se o usuário está logado e é um administrador do clã
if (!isset($_SESSION['loggedin']) || $_SESSION['admcan'] != 'S') {
    header("Location: login.php");
    exit();
}

// Obtém os parâmetros da URL e valida
if (!isset($_GET['acao']) || !isset($_GET['posicao']) || !is_numeric($_GET['posicao'])) {
    die("Parâmetros inválidos");
}

$acao = $_GET['acao'];
$posicao = (int)$_GET['posicao'];

// Define a nova posição com base na ação
if ($acao == 'subir') {
    $nova_posicao = $posicao - 1;
} elseif ($acao == 'descer') {
    $nova_posicao = $posicao + 1;
} else {
    die("Ação inválida");
}

// Verifica se a nova posição é válida
if ($nova_posicao < 1) {
    die("Posição inválida");
}

// Troca as posições no banco de dados
$conn->begin_transaction();

try {
    // Atualiza a posição do jogador na nova posição
    $stmt = $conn->prepare("UPDATE rankingx1prata_can SET posicao = 0 WHERE posicao = ?");
    $stmt->bind_param("i", $nova_posicao);
    $stmt->execute();

    // Atualiza a posição do jogador na posição atual
    $stmt = $conn->prepare("UPDATE rankingx1prata_can SET posicao = ? WHERE posicao = ?");
    $stmt->bind_param("ii", $nova_posicao, $posicao);
    $stmt->execute();

    // Atualiza a posição temporária do jogador na nova posição para a posição atual
    $stmt = $conn->prepare("UPDATE rankingx1prata_can SET posicao = ? WHERE posicao = 0");
    $stmt->bind_param("i", $posicao);
    $stmt->execute();

    // Confirma a transação
    $conn->commit();
    // Redireciona para a página específica
    header("Location: ../can_ranking_x1_prata.php");
    exit();
} catch (Exception $e) {
    // Desfaz a transação em caso de erro
    $conn->rollback();
    die("Erro ao trocar posições: " . $e->getMessage());
}

$conn->close();
?>
