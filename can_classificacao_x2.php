<?php
session_start();
include 'db/db.php';
include 'components/menu.php';

// Verifica se o usuário está logado e se tem permissão
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['cla_id'] !== 2) {
    header("Location: login.php");
    exit;
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
<body class="bg-1">

<h1>Classificação</h1>

<?php

// Consulta para recuperar a classificação dos times ordenados por pontos
$sql = "SELECT name, points, kills FROM teams ORDER BY points DESC, kills DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>#</th><th>Equipe</th><th>Pontos</th><th>Kills</th></tr>";

    $position = 1;

    while($row = $result->fetch_assoc()) {
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
