<?php
session_start(); 
include '../db/db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['admmaster'] !== 'S') {
    header("Location: ../login.php");
    exit;
}

// Verifica se os dados necessários foram enviados
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['round']) && isset($_POST['team1_id']) && isset($_POST['team2_id'])) {
    $round = intval($_POST['round']);
    $team1_ids = $_POST['team1_id'];
    $team2_ids = $_POST['team2_id'];

    if (count($team1_ids) === count($team2_ids) && count($team1_ids) == 9) {
        // Percorre os confrontos e faz as inserções/atualizações no banco de dados
        for ($i = 0; $i < 9; $i++) {
            $team1_id = intval($team1_ids[$i]);
            $team2_id = intval($team2_ids[$i]);

            // Verifica se os times são válidos
            if ($team1_id > 0 && $team2_id > 0 && $team1_id !== $team2_id) {
                // Verifica se já existe uma partida para a rodada
                $sql_check = "SELECT id FROM matches_x4 WHERE round = ? AND team1_id = ? AND team2_id = ?";
                if ($stmt_check = $conn->prepare($sql_check)) {
                    $stmt_check->bind_param("iii", $round, $team1_id, $team2_id);
                    $stmt_check->execute();
                    $stmt_check->store_result();

                    if ($stmt_check->num_rows > 0) {
                        // Atualiza a partida existente
                        $sql_update = "UPDATE matches_x4 SET team1_id = ?, team2_id = ? WHERE round = ? AND team1_id = ? AND team2_id = ?";
                        if ($stmt_update = $conn->prepare($sql_update)) {
                            $stmt_update->bind_param("iiiii", $team1_id, $team2_id, $round, $team1_id, $team2_id);
                            if (!$stmt_update->execute()) {
                                echo "Erro ao atualizar o confronto: " . $stmt_update->error;
                            }
                            $stmt_update->close();
                        }
                    } else {
                        // Insere uma nova partida se não existir
                        $sql_insert = "INSERT INTO matches_x4 (round, team1_id, team2_id) VALUES (?, ?, ?)";
                        if ($stmt_insert = $conn->prepare($sql_insert)) {
                            $stmt_insert->bind_param("iii", $round, $team1_id, $team2_id);
                            if (!$stmt_insert->execute()) {
                                echo "Erro ao salvar o confronto: " . $stmt_insert->error;
                            }
                            $stmt_insert->close();
                        }
                    }
                    $stmt_check->close();
                } else {
                    echo "Erro ao preparar a consulta: " . $conn->error;
                }
            } else {
                echo "Dados inválidos para o confronto " . ($i + 1);
            }
        }
    } else {
        echo "Dados insuficientes para salvar todos os confrontos.";
    }

    // Redireciona de volta ao painel após salvar
    header("Location: ../painel_salvar_confrontos_x4.php?msg=success");
    exit;
} else {
    echo "Dados inválidos recebidos.";
}

$conn->close();
?>
