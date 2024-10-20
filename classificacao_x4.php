<?php
    session_start();
    include 'db/db.php';
    include 'components/menu.php';

    // Zerando os totais antes de recalcular
    $sql_reset_totals = "UPDATE teams_x4 SET points = 0, kills = 0;";

    if ($conn->query($sql_reset_totals) === FALSE) {
        echo "Erro ao zerar totais: " . $conn->error;
    }

    // Atualiza os totais na tabela de equipes
    $sql_update_totals = "
    UPDATE teams_x4 t
    LEFT JOIN (
        SELECT team_id,
            SUM(total_points) AS total_points,
            SUM(total_kills) AS total_kills
        FROM (
            SELECT team1_id AS team_id, 
                SUM(score_team1) AS total_points, 
                SUM(kills_team1_1 + kills_team1_2 + kills_team1_3 + kills_team1_4 + 
                kills_team1_5 + kills_team1_6 + kills_team1_7 + kills_team1_8 + 
                kills_team1_9 + kills_team1_10 + kills_team1_11 + kills_team1_12) AS total_kills
            FROM matches_x4
            GROUP BY team1_id
            UNION ALL
            SELECT team2_id AS team_id, 
                SUM(score_team2) AS total_points, 
                SUM(kills_team2_1 + kills_team2_2 + kills_team2_3 + kills_team2_4 +
                kills_team2_5 + kills_team2_6 + kills_team2_7 + kills_team2_8 +
                kills_team2_9 + kills_team2_10 + kills_team2_11 + kills_team2_12) AS total_kills
            FROM matches_x4
            GROUP BY team2_id
        ) combined
        GROUP BY team_id
    ) m ON t.id = m.team_id
    SET t.points = COALESCE(m.total_points, 0),
        t.kills = COALESCE(m.total_kills, 0);

    ";

    if ($conn->query($sql_update_totals) === TRUE) {
        // Atualização bem-sucedida
    } else {
        echo "Erro ao atualizar totais: " . $conn->error;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Classificação</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background: linear-gradient(to bottom, #212529, #212529, rgb(255 171 7), rgb(255 171 7));
                color: #212529;
                margin: 0;
            }

            h1 {
                text-align: center;
                color: white;
                font-size: 2.5em;
                margin: 0 auto;
            }

            table {
                width: 90%;
                margin: 0 auto;
                border-collapse: collapse;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            table, th, td {
                border: 1px solid #ddd;
            }

            th, td {
                padding: 12px;
                text-align: center;
                font-size: 1.1em;
            }

            th {
                background-color: #212529;
                color: white;
                text-align: center;
            }

            tr {
                background-color: white;
            }

            tr:nth-child(even) {
                background-color: #f5f5f5;
            }

            tr:hover {
                background-color: gray;
                color: white;
            }

            #btn-voltar {
                margin: 20px;
            }
        </style>
    </head>
    <body>
        <a id="btn-voltar" class="btn btn-default" href="javascript:history.back()">Voltar</a>

        <?php
            // Consulta para recuperar a classificação dos times ordenados por pontos
            $sql = "SELECT t.team_name, t.points, t.kills 
                    FROM teams_x4 t 
                    ORDER BY t.points DESC, t.kills DESC";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>#</th><th>Equipe</th><th>Pontos</th><th>Kills</th></tr>";

                $position = 1;

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $position . "</td>
                            <td>" . htmlspecialchars($row['team_name']) . "</td>
                            <td>" . htmlspecialchars($row['points']) . "</td>
                            <td>" . htmlspecialchars($row['kills']) . "</td>
                        </tr>";
                    $position++;
                }
                echo "</table>";
            } else {
                echo "<p style='text-align: center;'>Nenhuma equipe encontrada.</p>";
            }

            $conn->close();
        ?>
    </body>
</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
