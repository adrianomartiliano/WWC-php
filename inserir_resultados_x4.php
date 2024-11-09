<?php
session_start();
include 'db/db.php';
include 'components/menu.php';

// Verifica se o usuário está logado e tem permissão
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['admmaster'] !== 'S') {
    header("Location: login.php");
    exit;
}

// Consulta para obter todas as rodadas
$sql_rounds = "SELECT DISTINCT round FROM matches_x4 ORDER BY round";
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
    $observation = $_POST['observation'];
    $libera_imagem = $_POST['libera_imagem'];
    $realizada = $_POST['realizada'];

    // Coleta das kills de cada jogador para os dois times
    $kills_team1 = [];
    $kills_team2 = [];
    for ($i = 1; $i <= 12; $i++) {
        $kills_team1[] = $_POST["kills_team1_$i"];
        $kills_team2[] = $_POST["kills_team2_$i"];
    }

    $sql_update_match = "UPDATE matches_x4 
                        SET 
                        score_team1 = ?, score_team2 = ?, 
                        kills_team1_1 = ?, kills_team1_2 = ?, kills_team1_3 = ?, kills_team1_4 = ?, 
                        kills_team1_5 = ?, kills_team1_6 = ?, kills_team1_7 = ?, kills_team1_8 = ?, 
                        kills_team1_9 = ?, kills_team1_10 = ?, kills_team1_11 = ?, kills_team1_12 = ?, 
                        kills_team2_1 = ?, kills_team2_2 = ?, kills_team2_3 = ?, kills_team2_4 = ?, 
                        kills_team2_5 = ?, kills_team2_6 = ?, kills_team2_7 = ?, kills_team2_8 = ?, 
                        kills_team2_9 = ?, kills_team2_10 = ?, kills_team2_11 = ?, kills_team2_12 = ?,
                        realizada = ?, libera_imagem = ?,
                        observation = ?  
                        WHERE id = ?";

    $stmt_update_match = $conn->prepare($sql_update_match);
    $stmt_update_match->bind_param(
        "iiiiiiiiiiiiiiiiiiiiiiiiiisssi", 
        $score_team1, $score_team2, 
        $kills_team1[0], $kills_team1[1], $kills_team1[2], $kills_team1[3], $kills_team1[4], $kills_team1[5], 
        $kills_team1[6], $kills_team1[7], $kills_team1[8], $kills_team1[9], $kills_team1[10], $kills_team1[11],
        $kills_team2[0], $kills_team2[1], $kills_team2[2], $kills_team2[3], $kills_team2[4], $kills_team2[5], 
        $kills_team2[6], $kills_team2[7], $kills_team2[8], $kills_team2[9], $kills_team2[10], $kills_team2[11],
        $realizada, $libera_imagem,
        $observation,
        $match_id // Adicionando o match_id aqui
    );
    

    if ($stmt_update_match->execute()) {
        // Atualizar a pontuação das equipes
        $sql_get_scores = "SELECT t1.id AS team1_id, t2.id AS team2_id, m.score_team1, m.score_team2
                           FROM matches_x4 m
                           JOIN teams_x4 t1 ON m.team1_id = t1.id
                           JOIN teams_x4 t2 ON m.team2_id = t2.id
                           WHERE m.id = ?";
        $stmt = $conn->prepare($sql_get_scores);
        $stmt->bind_param("i", $match_id);
        $stmt->execute();
        $result_scores = $stmt->get_result();
        $match_data = $result_scores->fetch_assoc();

        // Calcular a diferença de pontuação
        $diff_team1 = $score_team1 - $match_data['score_team1'];
        $diff_team2 = $score_team2 - $match_data['score_team2'];

        // Somar as kills
        $total_kills_team1 = array_sum($kills_team1);
        $total_kills_team2 = array_sum($kills_team2);

        // Atualizar a pontuação das equipes apenas se a soma dos scores for maior que 1
        if (($score_team1 + $score_team2) > 1) {
            $sql_update_team1 = "UPDATE teams_x4 SET points = points + ?, kills = kills + ? WHERE id = ?";
            $stmt_team1 = $conn->prepare($sql_update_team1);
            $stmt_team1->bind_param("iii", $diff_team1, $total_kills_team1, $match_data['team1_id']);
            $stmt_team1->execute();

            $sql_update_team2 = "UPDATE teams_x4 SET points = points + ?, kills = kills + ? WHERE id = ?";
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
            background-color: #d4edda;
            border-color: #c3e6cb; 
        }
        .text_title{
            font-size: 25px;
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
            $sql_matches = "SELECT m.id, t1.team_name AS team1_name, t2.team_name AS team2_name, m.score_team1, m.score_team2, 
            m.kills_team1_1, m.kills_team2_1, m.kills_team1_2, m.kills_team2_2, m.kills_team1_3, m.kills_team2_3, 
            m.kills_team1_4, m.kills_team2_4, m.kills_team1_5, m.kills_team2_5, m.kills_team1_6, m.kills_team2_6, 
            m.kills_team1_7, m.kills_team2_7, m.kills_team1_8, m.kills_team2_8, m.kills_team1_9, m.kills_team2_9, 
            m.kills_team1_10, m.kills_team2_10, m.kills_team1_11, m.kills_team2_11, m.kills_team1_12, m.kills_team2_12,
            m.realizada, m.libera_imagem,
            m.observation
            FROM matches_x4 m
            JOIN teams_x4 t1 ON m.team1_id = t1.id
            JOIN teams_x4 t2 ON m.team2_id = t2.id
            WHERE m.round = ?
            ORDER BY m.id DESC";
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
                        <!-- Formulário para atualizar o resultado de uma partida -->
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

                            <div class="team-form-group">
    <div>
        <label><span class="text_title">Kills</span> - <?php echo htmlspecialchars($row['team1_name']); ?></label>
        <?php for ($i = 1; $i <= 12; $i++): ?>
            <?php 
            // Exibe o rótulo de acordo com o número do input
            if ($i == 1) {
                echo "<p>Partida 1</p>";
            } elseif ($i == 5) {
                echo "<p>Partida 2</p>";
            } elseif ($i == 9) {
                echo "<p>Partida 3</p>";
            }
            ?>
            <input type="number" class="form-control" name="kills_team1_<?php echo $i; ?>" value="<?php echo htmlspecialchars($row['kills_team1_' . $i]); ?>" required>
        <?php endfor; ?>
    </div>

    <div>
        <label><span class="text_title">Kills</span> - <?php echo htmlspecialchars($row['team2_name']); ?></label>
        <?php for ($i = 1; $i <= 12; $i++): ?>
            <?php 
            // Exibe o rótulo de acordo com o número do input
            if ($i == 1) {
                echo "<p>Partida 1</p>";
            } elseif ($i == 5) {
                echo "<p>Partida 2</p>";
            } elseif ($i == 9) {
                echo "<p>Partida 3</p>";
            }
            ?>
            <input type="number" class="form-control" name="kills_team2_<?php echo $i; ?>" value="<?php echo htmlspecialchars($row['kills_team2_' . $i]); ?>" required>
        <?php endfor; ?>
    </div>
</div>
        <div class="form-group">
            <label for="observation">Observação</label>
            <textarea class="form-control" id="observation" name="observation" rows="3">
                <?php echo htmlspecialchars($row['observation']); ?>
            </textarea>
        </div>
        <div class="form-group">
            <label for="liberarImagens">Liberar Imagens</label>
            <select id="libera_imagem" name="libera_imagem" class="form-control">
                <option value="N" <?php echo ($row['libera_imagem'] == 'N') ? 'selected' : ''; ?>>Não</option>
                <option value="S" <?php echo ($row['libera_imagem'] == 'S') ? 'selected' : ''; ?>>Sim</option>
            </select>
        </div>

        <div class="form-group">
            <label for="realizada">Realizada</label>
            <select id="realizada" name="realizada" class="form-control">
                <option value="N" <?php echo ($row['realizada'] == 'N') ? 'selected' : ''; ?>>Não</option>
                <option value="S" <?php echo ($row['realizada'] == 'S') ? 'selected' : ''; ?>>Sim</option>
            </select>
        </div>

        


            <button type="submit" class="btn btn-primary">Atualizar</button>
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
