<?php
session_start(); // Inicia a sessão para uso posterior

// Incluir o arquivo de conexão com o banco de dados
require_once 'db/db.php';

// Inicializar variáveis
$error_message = "";

// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar dados do formulário
    $iduser = $_POST['iduser'];
    $password = $_POST['password'];

    // Debug: Verificar se os dados estão sendo capturados corretamente
    if (empty($iduser) || empty($password)) {
        $error_message = "Por favor, preencha todos os campos.";
    } else {
        // Preparar e executar consulta
        $stmt = $conn->prepare("SELECT iduser, password, admmaster, nickname, cla_id, admcan, admcla FROM users WHERE iduser = ?");
        if (!$stmt) {
            die("Erro na preparação da consulta: " . $conn->error);
        }

        $stmt->bind_param("i", $iduser); // Usar "i" para inteiro
        $stmt->execute();
        $stmt->store_result();

        // Verificar se a consulta encontrou algum resultado
        if ($stmt->num_rows > 0) {
            // Aqui você precisa ligar todas as colunas que você selecionou
            $stmt->bind_result($db_iduser, $stored_password, $admmaster, $nickname, $cla_id, $admcan, $admcla);
            $stmt->fetch();

            // Verificar a senha
            if (password_verify($password, $stored_password)) {
                $_SESSION['loggedin'] = true;
                $_SESSION['iduser'] = $db_iduser;
                $_SESSION['admmaster'] = $admmaster;
                $_SESSION['nickname'] = $nickname;
                $_SESSION['cla_id'] = $cla_id;
                $_SESSION['admcan'] = $admcan;
                $_SESSION['admcla'] = $admcla;
                header("Location: painel.php");
                exit();
            } else {
                // Senha incorreta
                $error_message = "Senha incorreta.";
            }

        } else {
            // Id no Jogo não encontrado
            $error_message = "Id no Jogo não encontrado.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        form {
            margin: 40px auto;
            max-width: 400px;
            border: 1px solid gray;
            background-color: white;
            color: #1FB6FF;
            box-shadow: 0 0 18px rgba(0, 0, 0, 0.8);
        }
    </style>
</head>
<body class="bg-1">
    <?php require 'components/menu.php'; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="flex-between">
            <h2>Login</h2>
            <img src="assets/website-password.png" />
        </div>
        <div class="mb-3">
            <label for="InputId" class="form-label">Id no Jogo</label>
            <input type="number" class="form-control" id="InputId" name="iduser" aria-describedby="InputId" required>
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" class="form-control" id="exampleInputPassword1" name="password" required>
        </div>

        <?php
        // Exibir mensagem de erro se houver
        if (!empty($error_message)) {
            echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
        }
        ?>

        <button type="submit" class="btn btn-primary">Entrar</button>
        <a class="btn btn-secondary" href="cadastro.php">Cadastre-se</a>
    </form>

    </body>
</html>
