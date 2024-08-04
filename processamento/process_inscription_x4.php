<?php
session_start();
include '../db/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_name = $_POST['team_name'];
    $member1 = $_POST['member1'];
    $member2 = $_POST['member2'];
    $member3 = $_POST['member3'];
    $member4 = $_POST['member4'];

    // Consulta para inserir a nova equipe na tabela teams_x4
    $sql_insert = "INSERT INTO teams_x4 (team_name, member1, member2, member3, member4) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("siiii", $team_name, $member1, $member2, $member3, $member4);

    if ($stmt_insert->execute()) {
        // Sucesso na inserção, redireciona para a página de sucesso
        header("Location: success.php");
        exit;
    } else {
        echo "Erro ao inscrever a equipe: " . $conn->error;
    }
}
?>

