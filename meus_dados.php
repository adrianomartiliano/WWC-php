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
    <!-- Inclua aqui o CSS do Bootstrap -->
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
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Alterar Senha</button>
        </div>
    </div>

    <!-- Modal para Alteração de Senha -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Alterar Senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Senha</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts do Bootstrap e JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>

    <!-- Script para validar e enviar a nova senha -->
    <script>
        $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault();
            var newPassword = $('#new_password').val();
            var confirmPassword = $('#confirm_password').val();

            if (newPassword !== confirmPassword) {
                alert('As senhas não coincidem.');
                return;
            }

            $.ajax({
                url: 'processamento/process_change_password.php',
                type: 'POST',
                data: { 
                    iduser: <?php echo $user_id; ?>,
                    new_password: newPassword
                },
                success: function(response) {
                    alert('Senha alterada com sucesso!');
                    $('#changePasswordModal').modal('hide');
                },
                error: function() {
                    alert('Ocorreu um erro ao alterar a senha.');
                }
            });
        });
    </script>
</body>
</html>
