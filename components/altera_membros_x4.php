<?php
include '../db/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userId = isset($_SESSION['iduser']) ? $_SESSION['iduser'] : 0;
$teamId = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0;

if ($teamId == 0) {
    echo "<script>alert('Equipe inválida!')</script>";
    exit;
}

// Buscar dados da equipe
$sqlTeam = "SELECT * FROM teams_x4 WHERE id = ?";
$stmtTeam = $conn->prepare($sqlTeam);
$stmtTeam->bind_param('i', $teamId);
$stmtTeam->execute();
$resultTeam = $stmtTeam->get_result();

if ($resultTeam->num_rows == 0) {
    echo "Equipe não encontrada!";
    exit;
}

$team = $resultTeam->fetch_assoc();
?>

<div class="container mt-5">
    <h2>Alterar Membros da Equipe: <?= htmlspecialchars($team['team_name']); ?></h2>
    <form action="processamento/process_alterar_membros.php" method="POST">
        <input type="hidden" name="team_id" value="<?= $teamId; ?>">
        
        <div class="mb-3">
            <label for="member1" class="form-label">Membro 1</label>
            <select class="form-select" id="member1" name="member1">
                <!-- Adicione as opções aqui -->
                <option value="<?= $team['member1']; ?>" selected><?= htmlspecialchars($team['member1']); ?></option>
                <!-- Adicione mais opções conforme necessário -->
            </select>
        </div>

        <div class="mb-3">
            <label for="member2" class="form-label">Membro 2</label>
            <select class="form-select" id="member2" name="member2">
                <option value="<?= $team['member2']; ?>" selected><?= htmlspecialchars($team['member2']); ?></option>
            </select>
        </div>

        <div class="mb-3">
            <label for="member3" class="form-label">Membro 3</label>
            <select class="form-select" id="member3" name="member3">
                <option value="<?= $team['member3']; ?>" selected><?= htmlspecialchars($team['member3']); ?></option>
            </select>
        </div>

        <div class="mb-3">
            <label for="member4" class="form-label">Membro 4</label>
            <select class="form-select" id="member4" name="member4">
                <option value="<?= $team['member4']; ?>" selected><?= htmlspecialchars($team['member4']); ?></option>
            </select>
        </div>

        <div class="mb-3">
            <label for="team_name" class="form-label">Nome da Equipe</label>
            <input type="text" class="form-control" id="team_name" name="team_name" value="<?= htmlspecialchars($team['team_name']); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>
