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

    $total = 0;
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
            width: 90%;
        }
    </style>
</head>
<body >
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
                        $total += 1;
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
        <?php
            echo "<span>Total: $total</span>";
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>
