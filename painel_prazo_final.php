<?php
session_start();
include 'db/db.php';
include 'components/menu.php';

// Verifica se o usuário está logado e é um administrador
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['admmaster'] !== 'S') {
    header("Location: login.php");
    exit;
}

// Atualiza o prazo_final para todas as linhas da mesma rodada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['prazo_final'] as $round => $prazo_final) {
        $sql_update = "UPDATE matches_x4 SET prazo_final = ? WHERE round = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param('si', $prazo_final, $round);
        $stmt_update->execute();
    }
    echo '<div class="alert mt-2 alert-success">Prazos atualizados com sucesso!</div>';
}

// Consulta para obter as rodadas únicas e seus prazos finais
$sql = "SELECT round, MIN(prazo_final) as prazo_final FROM matches_x4 GROUP BY round ORDER BY round ASC";
$result = $conn->query($sql);

// Array para armazenar as rodadas e prazos
$rounds = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $rounds[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Prazo Final por Rodada</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Painel de Edição de Prazo Final</h1>
        <form method="POST" action="">
            <?php foreach ($rounds as $round): ?>
                <div class="form-group mb-3">
                    <label for="prazo_final_<?php echo $round['round']; ?>">Rodada <?php echo $round['round']; ?> - Prazo Final</label>
                    <input type="datetime-local" class="form-control" id="prazo_final_<?php echo $round['round']; ?>" name="prazo_final[<?php echo $round['round']; ?>]" value="<?php echo date('Y-m-d\TH:i', strtotime($round['prazo_final'])); ?>" required>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-success mb-4">Salvar Prazos</button>
        </form>
    </div>
</body>
</html>
