<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifique se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Obter o ID do usuário da sessão
$user_id = $_SESSION['iduser'];

// Inclua a conexão com o banco de dados
include 'db/db.php';

// Consulta para obter os dados do usuário
$sql = "SELECT users.iduser, users.nickname, users.whatsapp, cla.siglacla AS cla_sigla 
        FROM users 
        JOIN cla ON users.cla_id = cla.idcla 
        WHERE users.iduser = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Usuário não encontrado");
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
    <style>
        h1 {
            text-align: center;
        }
        .card {
            width: 90%;
            margin: 20px auto;
        }
    </style>
    <!-- Inclua aqui o CSS do Bootstrap, se necessário -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
</head>
<body class="bg-1">
    <div class="card">

    
        <div class="card-body">
            <h5 class="card-title">Meus Dados</h5>
            <p class="card-text"><strong>ID:</strong> <?php echo htmlspecialchars($user['iduser']); ?></p>
            <p class="card-text"><strong>Nickname:</strong> <?php echo htmlspecialchars($user['nickname']); ?></p>
            <p class="card-text"><strong>Clã:</strong> <?php echo htmlspecialchars($user['cla_sigla']); ?></p>
            <p class="card-text"><strong>Whatsapp:</strong> <?php echo htmlspecialchars($user['whatsapp']); ?></p>
            <a href="editar_usuario.php?id=<?php echo $user['iduser']; ?>" class="btn btn-primary">Editar</a>
        </div>
    </div>
    <!-- Inclua aqui o JS do Bootstrap, se necessário -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
