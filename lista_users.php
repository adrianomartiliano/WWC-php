<?php
session_start();
include 'components/menu.php';
include 'db/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin'])) {
    // Redireciona para a página de login
    header("Location: login.php");
    exit();
}

// Verifica se o usuário tem permissões de admmaster
if ($_SESSION['admmaster'] !== 'S') {
    // Redireciona para uma página de erro ou de acesso negado
    header("Location: painel.php");
    exit();
}

// Consulta para obter todos os usuários
$sql = "SELECT users.nickname, cla.siglacla AS cla_sigla, users.iduser AS user_id
        FROM users
        JOIN cla ON users.cla_id = cla.idcla
        ORDER BY users.nickname ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuários</title>
    <style>
        h1 {
            text-align: center;
        }
        .table-container {
            margin: 20px auto;
            width: 80%;
        }
    </style>
    <!-- Inclua aqui o CSS do Bootstrap, se necessário -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
</head>
<body>
    <div class="container table-container">
        <h1 class="mt-2">Lista de Usuários</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Clã</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row["nickname"] . "</td>
                                <td>" . $row["cla_sigla"] . "</td>
                                <td>
                                    <a href='editar_usuario.php?id=" . $row["user_id"] . "' class='btn btn-primary btn-sm'>Editar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Nenhum usuário encontrado</td></tr>";
                }
                $conn->close();
            ?>
            </tbody>
        </table>
    </div>

    <!-- Inclua aqui o JS do Bootstrap, se necessário -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
