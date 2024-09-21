<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Certifique-se de que o conteúdo seja tratado como HTML
    header('Content-Type: text/html; charset=UTF-8');

    include '../db/db.php';

    $matchId = isset($_POST['match_id']) ? intval($_POST['match_id']) : 0;

    if ($matchId == 0) {
        echo "ID da batalha inválido!";
        exit;
    }

    // Consultar a rodada
    $sqlRound = "SELECT round FROM matches_x4 WHERE id = ?";
    $stmtRound = $conn->prepare($sqlRound);
    $stmtRound->bind_param('i', $matchId);
    $stmtRound->execute();
    $resultRound = $stmtRound->get_result();
    
    if ($resultRound->num_rows == 0) {
        echo "Batalha não encontrada!";
        exit;
    }

    $roundData = $resultRound->fetch_assoc();
    $roundNumber = $roundData['round'];

    // Pasta de upload com base na rodada
    $uploadDir = "../uploads/x4/rodada" . $roundNumber;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imgPaths = [];
    for ($i = 1; $i <= 3; $i++) {
        $imgField = "img_batalha$i";
        if (isset($_FILES[$imgField]) && $_FILES[$imgField]['error'] === UPLOAD_ERR_OK) {
            $imgName = basename($_FILES[$imgField]['name']);
            $imgPath = $uploadDir . "/" . $imgName;

            if (move_uploaded_file($_FILES[$imgField]['tmp_name'], $imgPath)) {
                $imgPaths[$i] = $imgName;
            } else {
                echo "Erro ao mover a imagem $i.";
                exit;
            }
        } else {
            // Caso a imagem não tenha sido enviada, seta o valor como NULL para evitar erro no bind_param
            $imgPaths[$i] = NULL;
        }
    }

    // Atualizar caminhos das imagens na tabela
    $sqlUpdate = "UPDATE matches_x4 SET img_batalha1 = ?, img_batalha2 = ?, img_batalha3 = ?, recebido = 'S' WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param('sssi', $imgPaths[1], $imgPaths[2], $imgPaths[3], $matchId);

    if ($stmtUpdate->execute()) {
        echo "Resultados recebidos com sucesso! Aguarde a atualização!";
        exit; // Garantir que o restante do código não seja executado
    } else {
        echo "Erro ao atualizar resultados no banco de dados.";
    }
}
?>
