<?php

include 'components/menu.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$userId = isset($_SESSION['iduser']) ? $_SESSION['iduser'] : 0;
$teamId = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0;

if ($teamId == 0) {
    echo "Equipe inválida!";
    exit;
}

// Conectar ao banco de dados
include 'db/db.php';

// Verificar o cla_id do usuário logado
$sqlClaId = "SELECT cla_id FROM users WHERE iduser = ?";
$stmtClaId = $conn->prepare($sqlClaId);
$stmtClaId->bind_param('i', $userId);
$stmtClaId->execute();
$resultClaId = $stmtClaId->get_result();

if ($resultClaId->num_rows == 0) {
    echo "Usuário não encontrado!";
    exit;
}

$userData = $resultClaId->fetch_assoc();
$userClaId = $userData['cla_id'];

// Verificar se o usuário logado é o member1 da equipe
$sqlCheck = "SELECT team_name, member1, member2, member3, member4 FROM teams_x4 WHERE id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param('i', $teamId);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows == 0) {
    echo "Equipe não encontrada!";
    exit;
}

$team = $resultCheck->fetch_assoc();

if ($team['member1'] != $userId) {
    echo "Você não tem permissão para gerenciar esta equipe!";
    exit;
}

// Atualizar membros da equipe (ao enviar o formulário)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member2 = isset($_POST['member2']) ? intval($_POST['member2']) : 0;
    $member3 = isset($_POST['member3']) ? intval($_POST['member3']) : 0;
    $member4 = isset($_POST['member4']) ? intval($_POST['member4']) : 0;

    // Verificar se os membros são diferentes entre si
    if ($member2 == $team['member1'] || $member3 == $team['member1'] || $member4 == $team['member1'] || 
        $member2 == $member3 || $member2 == $member4 || $member3 == $member4) {
        echo "<p>Erro: Os membros não podem se repetir.</p>";
    } else {
        $sqlUpdate = "UPDATE teams_x4 SET member2 = ?, member3 = ?, member4 = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param('iiii', $member2, $member3, $member4, $teamId);
        
        if ($stmtUpdate->execute()) {
            echo "<p>Equipe atualizada com sucesso!</p>";
            // Atualiza os dados da equipe na página após a atualização.
            $team['member2'] = $member2;
            $team['member3'] = $member3;
            $team['member4'] = $member4;
        } else {
            echo "<p>Erro ao atualizar equipe!</p>";
        }
    }
}

// Buscar todos os usuários que estão no mesmo cla_id e não estão inscritos em nenhuma equipe
$sqlAvailableMembers = "
    SELECT iduser, nickname
    FROM users
    WHERE cla_id = ? AND iduser NOT IN (
        SELECT member1 FROM teams_x4
        UNION
        SELECT member2 FROM teams_x4
        UNION
        SELECT member3 FROM teams_x4
        UNION
        SELECT member4 FROM teams_x4
    )
    OR iduser IN (?, ?, ?)
";
$stmtAvailable = $conn->prepare($sqlAvailableMembers);
$stmtAvailable->bind_param('iiii', $userClaId, $team['member2'], $team['member3'], $team['member4']);
$stmtAvailable->execute();
$resultAvailableMembers = $stmtAvailable->get_result();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Equipe - <?= htmlspecialchars($team['team_name']); ?></title>
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body>
    <div class="container mt-5">
        <h2>Gerenciar Equipe: <?= htmlspecialchars($team['team_name']); ?></h2>
        <form action="gerenciar_equipe_x4.php?team_id=<?= $teamId; ?>" method="POST">
            <div class="mb-3">
                <label for="member2" class="form-label">Membro 2</label>
                <select class="form-control" id="member2" name="member2" required>
                    <option value="">Selecione o Membro 2</option>
                    <?php 
                        // Reiniciar o cursor para exibir os membros no select novamente
                        $resultAvailableMembers->data_seek(0);
                        while ($member = $resultAvailableMembers->fetch_assoc()): 
                    ?>
                        <option value="<?= $member['iduser']; ?>" <?= ($member['iduser'] == $team['member2']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($member['nickname']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="member3" class="form-label">Membro 3</label>
                <select class="form-control" id="member3" name="member3" required>
                    <option value="">Selecione o Membro 3</option>
                    <?php 
                        // Reiniciar o cursor para exibir os membros no select novamente
                        $resultAvailableMembers->data_seek(0);
                        while ($member = $resultAvailableMembers->fetch_assoc()): 
                    ?>
                        <option value="<?= $member['iduser']; ?>" <?= ($member['iduser'] == $team['member3']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($member['nickname']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="member4" class="form-label">Membro 4</label>
                <select class="form-control" id="member4" name="member4" required>
                    <option value="">Selecione o Membro 4</option>
                    <?php 
                        // Reiniciar o cursor para exibir os membros no select novamente
                        $resultAvailableMembers->data_seek(0);
                        while ($member = $resultAvailableMembers->fetch_assoc()): 
                    ?>
                        <option value="<?= $member['iduser']; ?>" <?= ($member['iduser'] == $team['member4']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($member['nickname']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-default">Atualizar Equipe</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
