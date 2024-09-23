<?php
session_start();
include 'db/db.php';
include 'components/menu.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Obtém o nickname e cla_id do usuário logado
$user_nickname = $_SESSION['nickname'];
$cla_id = $_SESSION['cla_id'];
$userid = $_SESSION['iduser'];

// Função para verificar se já há 10 equipes inscritas
function isTournamentFull($conn) {
    $sql_count_teams = "SELECT COUNT(*) as total_teams FROM teams_x4";
    $result_count = $conn->query($sql_count_teams);
    $row = $result_count->fetch_assoc();
    return $row['total_teams'] >= 10;  // Limite de 10 equipes
}

// Verifica se o torneio já atingiu o limite de equipes
if (isTournamentFull($conn)) {
    echo '<script>
            alert("O torneio atingiu o limite de 10 equipes. Não há mais vagas disponíveis.");
            window.location.href = "index.php";  // Redireciona para a página inicial
          </script>';
    exit;
}

// Função para verificar se um usuário já está inscrito em uma equipe
function isUserInTeam($conn, $userid) {
    $sql_check_team = "
        SELECT team_name FROM teams_x4 
        WHERE member1 = ? OR member2 = ? OR member3 = ? OR member4 = ?
    ";
    $stmt_check = $conn->prepare($sql_check_team);
    $stmt_check->bind_param("iiii", $userid, $userid, $userid, $userid);
    $stmt_check->execute();
    return $stmt_check->get_result()->num_rows > 0;
}

// Verifica se o usuário logado já está inscrito em uma equipe
if (isUserInTeam($conn, $userid)) {
    $sql_get_team_name = "
        SELECT team_name FROM teams_x4 
        WHERE member1 = ? OR member2 = ? OR member3 = ? OR member4 = ?
    ";
    $stmt_get_team_name = $conn->prepare($sql_get_team_name);
    $stmt_get_team_name->bind_param("iiii", $userid, $userid, $userid, $userid);
    $stmt_get_team_name->execute();
    $result_get_team_name = $stmt_get_team_name->get_result();
    $row = $result_get_team_name->fetch_assoc();
    $team_name = $row['team_name'];

    echo '<script>
            window.onload = function() {
                $("#teamModal").modal("show");
            };
          </script>';
} else {
    // Consulta para obter todos os usuários do mesmo cla que não estão em uma equipe
    $sql_users = "
        SELECT iduser, nickname FROM users 
        WHERE iduser != ? AND cla_id = ? 
        AND iduser NOT IN (
            SELECT member1 FROM teams_x4
            UNION
            SELECT member2 FROM teams_x4
            UNION
            SELECT member3 FROM teams_x4
            UNION
            SELECT member4 FROM teams_x4
        )
    ";
    $stmt = $conn->prepare($sql_users);
    $stmt->bind_param("ii", $userid, $cla_id);
    $stmt->execute();
    $result_users = $stmt->get_result();

    // Array para armazenar os usuários
    $users = [];
    if ($result_users->num_rows > 0) {
        while($row = $result_users->fetch_assoc()) {
            $users[] = $row;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição para Torneio X4</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
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
        select, input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #2c3e50;
            color: white;
            border: none;
            font-size: 1.1em;
            cursor: pointer;
        }
    </style>
    <script>
        function validateSelects() {
            const selects = document.querySelectorAll('select');
            const values = [];
            for (let select of selects) {
                if (values.includes(select.value)) {
                    alert('Não é possível selecionar o mesmo membro mais de uma vez.');
                    return false;
                }
                values.push(select.value);
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container form-container">
        <?php if (isset($team_name)): ?>
            <div class="modal fade" id="teamModal" tabindex="-1" aria-labelledby="teamModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="teamModalLabel">Atenção!</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Você já está inscrito na equipe: <strong><?php echo htmlspecialchars($team_name); ?></strong>. Não é possível nova inscrição.
                        </div>
                        <div class="modal-footer">
                            <a href="index.php" class="btn btn-primary">Voltar à Página Inicial</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <h1 class="mt-2">Inscrição para Torneio X4</h1>
            <form method="post" action="processamento/process_inscription_x4.php" onsubmit="return validateSelects();">
                <div class="form-group">
                    <label for="team_name">Nome da Equipe</label>
                    <input type="text" class="form-control" id="team_name" name="team_name" required>
                </div>
                <div class="form-group">
                    <label for="member1">Membro 1 (Você) - Capitão</label>
                    <select class="form-control" id="member1" name="member1" readonly>
                        <option value="<?php echo $_SESSION['iduser']; ?>"><?php echo htmlspecialchars($user_nickname); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="member2">Membro 2</label>
                    <select class="form-control" id="member2" name="member2" required>
                        <option value="">Selecione um membro</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['iduser']; ?>"><?php echo htmlspecialchars($user['nickname']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="member3">Membro 3</label>
                    <select class="form-control" id="member3" name="member3" required>
                        <option value="">Selecione um membro</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['iduser']; ?>"><?php echo htmlspecialchars($user['nickname']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="member4">Membro 4</label>
                    <select class="form-control" id="member4" name="member4" required>
                        <option value="">Selecione um membro</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['iduser']; ?>"><?php echo htmlspecialchars($user['nickname']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Inscrever Equipe</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>

    <?php if (isset($team_name)): ?>
        <script>
            $(document).ready(function() {
                $("#teamModal").modal("show");
            });
        </script>
    <?php endif; ?>
</body>
</html>
