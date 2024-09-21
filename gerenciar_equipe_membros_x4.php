<?php
ob_start();
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

// Atualizar membros da equipe e o nome (ao enviar o formulário)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTeamName = isset($_POST['team_name']) ? trim($_POST['team_name']) : '';
    $member2 = isset($_POST['member2']) ? intval($_POST['member2']) : 0;
    $member3 = isset($_POST['member3']) ? intval($_POST['member3']) : 0;
    $member4 = isset($_POST['member4']) ? intval($_POST['member4']) : 0;
    $newCaptain = isset($_POST['member1']) ? intval($_POST['member1']) : 0;

    // Verificar se os membros são diferentes entre si, exceto quando trocados
    $members = [$newCaptain, $member2, $member3, $member4];
    $uniqueMembers = array_unique($members);
    
    // Se o número de membros únicos for menor do que 4, há repetição
    if (count($uniqueMembers) < 3) {
        echo "<p>Erro: Os membros não podem se repetir.</p>";
    } else {
        // Criar variáveis para os novos membros com base no novo capitão
        if ($newCaptain == $team['member1']) {
            // O capitão não muda
            $newMember2 = $member2;
            $newMember3 = $member3;
            $newMember4 = $member4;
        } elseif ($newCaptain == $team['member2']) {
            // Muda membro 2 para capitão
            $newCaptain = $member2;
            $newMember2 = $team['member1'];
            $newMember3 = $member3;
            $newMember4 = $member4;
        } elseif ($newCaptain == $team['member3']) {
            // Muda membro 3 para capitão
            $newCaptain = $member3;
            $newMember2 = $member2;
            $newMember3 = $team['member1'];
            $newMember4 = $member4;
        } else {
            // Muda membro 4 para capitão
            $newCaptain = $member4;
            $newMember2 = $member2;
            $newMember3 = $member3;
            $newMember4 = $team['member1'];
        }

        // Atualizar o nome da equipe e a disposição dos membros
        $sqlUpdate = "UPDATE teams_x4 SET team_name = ?, member1 = ?, member2 = ?, member3 = ?, member4 = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param('siiiii', $newTeamName, $newCaptain, $newMember2, $newMember3, $newMember4, $teamId);

        if ($stmtUpdate->execute()) {
            echo "<script>alert('Equipe atualizada com sucesso!'); window.location.href='teams_x4.php';</script>";
            exit; // Sempre use exit após redirecionamentos
        } else {
            echo "<p>Erro ao atualizar equipe!</p>";
        }
        
    }
}



// Buscar todos os usuários que estão no mesmo cla_id e não estão inscritos em nenhuma equipe
$sqlAvailableMembers = "
    SELECT iduser, nickname
    FROM users
    WHERE cla_id = ? 
    AND iduser NOT IN (?, ?, ?, ?)
    OR iduser IN (?, ?, ?)
";
$stmtAvailable = $conn->prepare($sqlAvailableMembers);
$stmtAvailable->bind_param('iiiiiiii', $userClaId, $team['member1'], $team['member2'], $team['member3'], $team['member4'], $team['member2'], $team['member3'], $team['member4']);
$stmtAvailable->execute();
$resultAvailableMembers = $stmtAvailable->get_result();


function getNickname($iduser, $conn) {
    $sql = "SELECT nickname FROM users WHERE iduser = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $iduser);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['nickname'];
    } else {
        return "Desconhecido";
    }
}
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
        <form action="gerenciar_equipe_membros_x4.php?team_id=<?= $teamId; ?>" method="POST">
            <div class="mb-3">
                <label for="team_name" class="form-label">Nome da Equipe</label>
                <input type="text" class="form-control" id="team_name" name="team_name" value="<?= htmlspecialchars($team['team_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="member2" class="form-label">Membro 2</label>
                <select class="form-control" id="member2" name="member2" required>
                    <option value="">Selecione o Membro 2</option>
                    <?php 
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
                        $resultAvailableMembers->data_seek(0);
                        while ($member = $resultAvailableMembers->fetch_assoc()): 
                    ?>
                        <option value="<?= $member['iduser']; ?>" <?= ($member['iduser'] == $team['member4']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($member['nickname']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="member1" class="form-label">Capitão</label>
                <select class="form-control" id="member1" name="member1" required>
                    <option value="">Selecione o novo capitão</option>
                    <option value="<?= $team['member1']; ?>" selected>
                        <?= htmlspecialchars(getNickname($team['member1'], $conn)); ?>
                    </option>
                    <option value="<?= $team['member2']; ?>">
                        <?= htmlspecialchars(getNickname($team['member2'], $conn)); ?>
                    </option>
                    <option value="<?= $team['member3']; ?>">
                        <?= htmlspecialchars(getNickname($team['member3'], $conn)); ?>
                    </option>
                    <option value="<?= $team['member4']; ?>">
                        <?= htmlspecialchars(getNickname($team['member4'], $conn)); ?>
                    </option>
                </select>
            </div>
            <button type="submit" class="btn btn-default">Atualizar Equipe</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
ob_end_flush();
