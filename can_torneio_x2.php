<?php
include 'db/db.php';
include 'components/menu.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['cla_id'] !== 2) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rodadas e Partidas</title>
    <style>

        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 2.5em;
            margin: 20px auto;
        }

        h2 {
            color: #2c3e50;
            font-size: 2em;
            margin-top: 20px;
            text-align: center;
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

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
        .btn-classificacao-can{
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .btn-classificacao-can > a {
            margin-top: 20px;
            font-size: 30px;
            width: 90%;
        }
    </style>
</head>
<body>
    <div class="btn-classificacao-can">
        <a class="btn btn-secondary" href="can_classificacao_x2.php">Classificação</a>
    </div>
    <?php
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION['admcan'] === 'S'){
            echo "
              <div class='btn-classificacao-can'>
                <a class='btn btn-secondary' href='can_inserir_resultadosx2.php'>Inserir Resultados</a>
              </div>
            ";
          } ?>
    

<h1>Rodadas e Partidas</h1>

<?php

// Consulta para recuperar as rodadas e partidas
$sql = "SELECT m.round, t1.name AS team1_name, t2.name AS team2_name, m.score_team1, m.score_team2
        FROM matches m
        JOIN teams t1 ON m.team1_id = t1.id
        JOIN teams t2 ON m.team2_id = t2.id
        ORDER BY m.round, m.id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $current_round = 0;

    while($row = $result->fetch_assoc()) {
        if ($row['round'] != $current_round) {
            if ($current_round != 0) {
                echo "</table>";
            }
            $current_round = $row['round'];
            echo "<h2>Rodada " . $current_round . "</h2>";
            echo "<table>";
            echo "<tr><th>Equipe 1</th><th>Placar</th><th>Equipe 2</th></tr>";
        }
        echo "<tr>
                <td>" . $row['team1_name'] . "</td>
                <td>" . $row['score_team1'] . " - " . $row['score_team2'] . "</td>
                <td>" . $row['team2_name'] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Nenhuma partida encontrada.";
}

$conn->close();
?>

</body>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</html>
