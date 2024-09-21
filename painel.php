<?php

include 'components/menu.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$admmaster = isset($_SESSION['admmaster']) ? $_SESSION['admmaster'] : 'N';
$userId = isset($_SESSION['iduser']) ? $_SESSION['iduser'] : 0; // ID do usuário logado

// Conectar ao banco de dados
include 'db/db.php';

// Consultar os times que o usuário está inscrito
$sqlTeams = "
    SELECT id, team_name, member1 
    FROM teams_x4 
    WHERE member1 = ? OR member2 = ? OR member3 = ? OR member4 = ?
";

$stmt = $conn->prepare($sqlTeams);
$stmt->bind_param('iiii', $userId, $userId, $userId, $userId);
$stmt->execute();
$resultTeams = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body class="bg-1">
    <h1 id='title-welcome-painel'>Bem-vindo(a) ao seu painel</h1>

    <?php if ($admmaster === 'S'): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Lista dos usuários</h5>
                <p class="card-text">Lista com todos os usuários do site.</p>
                <a href="lista_users.php" class="btn btn-primary">Usuários</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Torneios e Equipes que estou participando</h5>
            <?php if ($resultTeams->num_rows > 0): ?>
                    <?php while ($team = $resultTeams->fetch_assoc()): ?>
                        <p>Equipe: <?= htmlspecialchars($team['team_name']); ?></p>
                        <span>
                            <?php if ($team['member1'] == $userId): ?>
                                <!-- Botão para Gerenciar Equipe aparece apenas para member1 -->
                                <a href="gerenciar_equipe_x4.php?team_id=<?= $team['id']; ?>" class="btn btn-default manage-team-btn">Gerenciar Equipe</a>
                            <?php endif; ?>
                        </span>
                    <?php endwhile; ?>
            <?php else: ?>
                <p>Você não está inscrito em nenhuma equipe no momento.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'meus_dados.php'; ?>
    
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</html>
