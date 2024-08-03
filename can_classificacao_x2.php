<?php
session_start();
include 'db/db.php';
include 'components/menu.php';

// Verifica se o usuário está logado e tem permissão
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['cla_id'] !== 2) {
    header("Location: login.php");
    exit;
}

// Zerando os totais antes de recalcular
$sql_reset_totals = "UPDATE teams SET points = 0, kills = 0;";

if ($conn->query($sql_reset_totals) === FALSE) {
    echo "Erro ao zerar totais: " . $conn->error;
}

// Atualiza os totais na tabela de equipes
$sql_update_totals = "
UPDATE teams t
LEFT JOIN (
    SELECT team_id,
           SUM(total_points) AS total_points,
           SUM(total_kills) AS total_kills
    FROM (
        SELECT team1_id AS team_id, 
               SUM(score_team1) AS total_points, 
               SUM(kills_team1_1 + kills_team1_2 + kills_team1_3) AS total_kills
        FROM matches
        GROUP BY team1_id
        UNION ALL
        SELECT team2_id AS team_id, 
               SUM(score_team2) AS total_points, 
               SUM(kills_team2_1 + kills_team2_2 + kills_team2_3) AS total_kills
        FROM matches
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
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 2.5em;
            margin: 0 auto;
            margin-top: 20px;
        }

        table {
            width: 95%;
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
        tr{
            background-color: white;
        }

        tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        tr:hover {
            background-color: gray;
            color: white;
        }
    </style>
</head>
<body>

<h1>Classificação</h1>

<?php
// Consulta para recuperar a classificação dos times ordenados por pontos
$sql = "SELECT t.name, t.points, t.kills 
        FROM teams t 
        ORDER BY t.points DESC, t.kills DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>#</th><th>Equipe</th><th>Pontos</th><th>Kills</th></tr>";

    $position = 1;

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $position . "</td>
                <td>" . htmlspecialchars($row['name']) . "</td>
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
