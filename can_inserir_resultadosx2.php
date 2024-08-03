<?php
session_start();
include 'db/db.php';
include 'components/menu.php';

// Verifica se o usuário está logado e tem permissão
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['admcan'] !== 'S') {
    header("Location: login.php");
    exit;
}

// Consulta para obter todas as rodadas
$sql_rounds = "SELECT DISTINCT round FROM matches ORDER BY round";
$result_rounds = $conn->query($sql_rounds);

// Array para armazenar as rodadas
$rounds = [];
if ($result_rounds->num_rows > 0) {
    while($row = $result_rounds->fetch_assoc()) {
        $rounds[] = $row['round'];
    }
}

// Verificar se a rodada foi selecionada
$selected_round = isset($_POST['selected_round']) ? $_POST['selected_round'] : null;

// Atualização dos resultados
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_result'])) {
    $match_id = $_POST['match_id'];
    $score_team1 = $_POST['score_team1'];
    $score_team2 = $_POST['score_team2'];
    $kills_team1_1 = $_POST['kills_team1_1'];
    $kills_team1_2 = $_POST['kills_team1_2'];
    $kills_team1_3 = $_POST['kills_team1_3'];
    $kills_team2_1 = $_POST['kills_team2_1'];
    $kills_team2_2 = $_POST['kills_team2_2'];
    $kills_team2_3 = $_POST['kills_team2_3'];


    // Atualizar os resultados da partida
    $sql_update_match = "UPDATE matches SET score_team1 = ?, score_team2 = ?, kills_team1_1 = ?, kills_team2_1 = ?, kills_team1_2 = ?, kills_team2_2 = ?, kills_team1_3 = ?, kills_team2_3 = ? WHERE id = ?";
    $stmt_update_match = $conn->prepare($sql_update_match);
    $stmt_update_match->bind_param("iiiiiiiis", 
        $score_team1, $score_team2, $kills_team1_1, $kills_team2_1, $kills_team1_2, 
        $kills_team2_2, $kills_team1_3, $kills_team2_3, $match_id);
    if ($stmt_update_match->execute()) {
        // Atualizar a pontuação das equipes
        $sql_get_scores = "SELECT t1.id AS team1_id, t2.id AS team2_id, m.score_team1, m.score_team2
                           FROM matches m
                           JOIN teams t1 ON m.team1_id = t1.id
                           JOIN teams t2 ON m.team2_id = t2.id
                           WHERE m.id = ?";
        $stmt = $conn->prepare($sql_get_scores);
        $stmt->bind_param("i", $match_id);
        $stmt->execute();
        $result_scores = $stmt->get_result();
        $match_data = $result_scores->fetch_assoc();

        // Calcular a diferença de pontuação
        $diff_team1 = $score_team1 - $match_data['score_team1'];
        $diff_team2 = $score_team2 - $match_data['score_team2'];

        // Atualizar a pontuação das equipes apenas se a soma dos scores for maior que 1
        if (($score_team1 + $score_team2) > 1) {
            $sql_update_team1 = "UPDATE teams SET points = points + ?, kills = kills + ? WHERE id = ?";
            $stmt_team1 = $conn->prepare($sql_update_team1);
            $stmt_team1->bind_param("iii", $diff_team1, $total_kills_team1, $match_data['team1_id']);
            $stmt_team1->execute();

            $sql_update_team2 = "UPDATE teams SET points = points + ?, kills = kills + ? WHERE id = ?";
            $stmt_team2 = $conn->prepare($sql_update_team2);
            $stmt_team2->bind_param("iii", $diff_team2, $total_kills_team2, $match_data['team2_id']);
            $stmt_team2->execute();
        }

        $success = "Resultado atualizado com sucesso.";
    } else {
        $error = "Erro ao atualizar o resultado.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserir Resultados</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-container {
            margin: 20px auto;
            max-width: 600px;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 2.5em;
            margin: 20px auto;
        }
        form {
            background-color: white;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #2c3e50;
            color: white;
            border: none;
            font-size: 1.1em;
            cursor: pointer;
        }
        .match-container {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .match-container p {
            font-size: 1.2em;
            margin: 0;
            padding: 10px 0;
            text-align: center;
        }
        .match-container button {
            margin-top: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
        }
        .match-container form {
            display: none;
            padding: 10px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .team-form-group {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }
        .team-form-group div {
            width: 48%;
        }
        .match-container.updated {
            background-color: #d4edda; /* Verde claro */
            border-color: #c3e6cb; /* Verde mais escuro */
        }
        .text_title{
            font-size: 25px;
            font-
        }
    </style>
</head>
<body>
    <div class="container form-container">
        <h1 class="mt-2">Inserir Resultados</h1>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulário para selecionar a rodada -->
        <form method="post">
            <div class="form-group">
                <label for="selected_round">Selecione a Rodada</label>
                <select class="form-control" id="selected_round" name="selected_round" onchange="this.form.submit()">
                    <option value="">Selecione a rodada</option>
                    <?php foreach ($rounds as $round): ?>
                        <option value="<?php echo $round; ?>" <?php echo ($selected_round == $round) ? 'selected' : ''; ?>><?php echo $round; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <!-- Mostrar as partidas da rodada selecionada -->
        <?php if ($selected_round): ?>
            <h2 class="mt-4">Partidas da Rodada <?php echo htmlspecialchars($selected_round); ?></h2>
            <?php
            $sql_matches = "SELECT m.id, t1.name AS team1_name, t2.name AS team2_name, m.score_team1, m.score_team2, m.kills_team1_1, m.kills_team2_1, m.kills_team1_2, m.kills_team2_2, m.kills_team1_3, m.kills_team2_3
                            FROM matches m
                            JOIN teams t1 ON m.team1_id = t1.id
                            JOIN teams t2 ON m.team2_id = t2.id
                            WHERE m.round = ?
                            ORDER BY rand(); ";
            $stmt = $conn->prepare($sql_matches);
            $stmt->bind_param("i", $selected_round);
            $stmt->execute();
            $result_matches = $stmt->get_result();
            if ($result_matches->num_rows > 0): ?>
                <?php while ($row = $result_matches->fetch_assoc()): ?>
                    <?php
                    // Verificar se a partida já foi atualizada com base na soma dos scores
                    $is_updated = ($row['score_team1'] + $row['score_team2']) > 0;
                    $class = $is_updated ? 'updated' : '';
                    ?>
                    <div class="match-container <?php echo $class; ?>">
                        <p><?php echo htmlspecialchars($row['team1_name']); ?> vs <?php echo htmlspecialchars($row['team2_name']); ?> - Placar: <?php echo htmlspecialchars($row['score_team1']); ?> - <?php echo htmlspecialchars($row['score_team2']); ?></p>
                        <button type="button" onclick="document.getElementById('form-<?php echo $row['id']; ?>').style.display='block'">Atualizar Resultado</button>
                        <form method="post" id="form-<?php echo $row['id']; ?>">
                            <input type="hidden" name="match_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="update_result" value="1">
                            <div class="form-group">
                                <label for="score_team1"><span class="text_title">Placar</span> - <?php echo htmlspecialchars($row['team1_name']); ?></label>
                                <input type="number" class="form-control" id="score_team1" name="score_team1" value="<?php echo htmlspecialchars($row['score_team1']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="score_team2"><span class="text_title">Placar</span> - <?php echo htmlspecialchars($row['team2_name']); ?></label>
                                <input type="number" class="form-control" id="score_team2" name="score_team2" value="<?php echo htmlspecialchars($row['score_team2']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="kills_team1_1"><span class="text_title">Kills</span> -<?php echo htmlspecialchars($row['team1_name']); ?></label>
                                <input type="number" class="form-control" id="kills_team1_1" name="kills_team1_1" value="<?php echo htmlspecialchars($row['kills_team1_1']); ?>" required>
                            </div>
                            <div class="form-group">
                                <input type="number" class="form-control" id="kills_team1_2" name="kills_team1_2" value="<?php echo htmlspecialchars($row['kills_team1_2']); ?>" required>
                            </div>
                            <div class="form-group">
                                <input type="number" class="form-control" id="kills_team1_3" name="kills_team1_3" value="<?php echo htmlspecialchars($row['kills_team1_3']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="kills_team2_1"><span class="text_title">Kills</span> -<?php echo htmlspecialchars($row['team2_name']); ?></label>
                                <input type="number" class="form-control" id="kills_team2_1" name="kills_team2_1" value="<?php echo htmlspecialchars($row['kills_team2_1']); ?>" required>
                            </div>
                            <div class="form-group">
                                <input type="number" class="form-control" id="kills_team2_2" name="kills_team2_2" value="<?php echo htmlspecialchars($row['kills_team2_2']); ?>" required>
                            </div>
                            <div class="form-group">
                                <input type="number" class="form-control" id="kills_team2_3" name="kills_team2_3" value="<?php echo htmlspecialchars($row['kills_team2_3']); ?>" required>
                            </div>
                            <button type="submit" name="update_result" class="btn btn-primary">Salvar</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhuma partida encontrada para esta rodada.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
