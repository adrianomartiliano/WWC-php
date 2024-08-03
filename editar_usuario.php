<?php
session_start();
include 'components/menu.php';
include 'db/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o usuário tem permissão de admmaster
$admmaster = isset($_SESSION['admmaster']) ? $_SESSION['admmaster'] : 'N';

if ($admmaster === 'S' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
} else {
    $user_id = $_SESSION['iduser'];
}

// Consulta para obter os dados do usuário, incluindo a sigla do clã
$sql = "SELECT u.iduser, u.nickname, u.cla_id, u.whatsapp, u.admcla, c.siglacla 
        FROM users u
        LEFT JOIN cla c ON u.cla_id = c.idcla
        WHERE u.iduser = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Usuário não encontrado");
}

$user = $result->fetch_assoc();

// Consulta para obter todos os clãs
$sql_clans = "SELECT idcla, siglacla FROM cla";
$result_clans = $conn->query($sql_clans);

// Atualiza os dados do usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname = $_POST['nickname'];
    $whatsapp = $_POST['whatsapp'];
    $admcla = isset($_POST['admcla']) ? 'S' : 'N';

    $sql = "UPDATE users SET nickname = ?, whatsapp = ?, admcla = ? WHERE iduser = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nickname, $whatsapp, $admcla, $user_id);

    if ($stmt->execute()) {
        header("Location: lista_users.php");
        exit();
    } else {
        echo "Erro ao atualizar os dados do usuário";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <style>
        .form-container {
            margin: 20px auto;
        }
        form {
            background-color: white;
        }
        h1 {
            text-align: center;
        }
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
</head>
<body class="bg-1">
    <div class="container form-container">
        <h1 class="mt-2">Editar Usuário</h1>
        <form method="post">
            <div class="form-group">
                <label for="iduser">ID do Usuário</label>
                <input type="text" class="form-control" id="iduser" name="iduser" value="<?php echo htmlspecialchars($user['iduser']); ?>" readonly disabled>
            </div>
            <div class="form-group">
                <label for="nickname">Nome</label>
                <input type="text" class="form-control" id="nickname" name="nickname" value="<?php echo htmlspecialchars($user['nickname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="cla_id">Clã</label>
                <input type="text" class="form-control" id="cla_id" name="cla_id" value="<?php echo htmlspecialchars($user['siglacla']); ?>" readonly disabled>
            </div>
            <div class="form-group">
                <label for="whatsapp">WhatsApp</label>
                <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo htmlspecialchars($user['whatsapp']); ?>" required>
            </div>
            <?php 
                $admcla = isset($_SESSION['admcla']) ? $_SESSION['admcla'] : 'N';
                if($admcla == 'S'): ?>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="admcla" name="admcla" <?php echo ($user['admcla'] == 'S') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="admcla">Adm do Clã</label>
                        </div>
                    </div>
                <?php endif; ?>
            
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
