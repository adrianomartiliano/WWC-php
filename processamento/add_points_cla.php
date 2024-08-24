<?php
include '../db/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['admmaster'] !== 'S') {
    header("Location: login.php");
    exit;
}

$idCla = isset($_POST['id_cla']) ? intval($_POST['id_cla']) : 0;
$pontosAdicionados = isset($_POST['pontos_adicionados']) ? intval($_POST['pontos_adicionados']) : 0;
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
$idAdm = $_SESSION['iduser'];

if ($idCla > 0 && $pontosAdicionados != 0) {
    // Atualizar pontos do clã
    $sqlUpdateCla = "UPDATE cla SET points = points + ? WHERE idcla = ?";
    $stmtUpdateCla = $conn->prepare($sqlUpdateCla);
    $stmtUpdateCla->bind_param('ii', $pontosAdicionados, $idCla);
    
    if ($stmtUpdateCla->execute()) {
        // Registrar log de adição de pontos
        $sqlLog = "INSERT INTO log_pontos_cla (data_adicao, id_cla, pontos_adicionados, id_adm, motivo)
                   VALUES (NOW(), ?, ?, ?, ?)";
        $stmtLog = $conn->prepare($sqlLog);
        $stmtLog->bind_param('iiis', $idCla, $pontosAdicionados, $idAdm, $motivo);
        
        if ($stmtLog->execute()) {
            // Redirecionar com mensagem de sucesso
            header("Location: ../ranking_clas.php?message=success");
            exit;
        } else {
            // Redirecionar com mensagem de erro no log
            header("Location: ../ranking_clas.php?message=error_log");
            exit;
        }
    } else {
        // Redirecionar com mensagem de erro na atualização
        header("Location: ../ranking_clas.php?message=error_update");
        exit;
    }
} else {
    // Redirecionar com mensagem de dados inválidos
    header("Location: ../ranking_clas.php?message=invalid_data");
    exit;
}
?>
