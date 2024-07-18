<?php
    include 'components/menu.php';
    include 'db/db.php';

    // Verifica se o usuário está logado
    if (!isset($_SESSION['loggedin'])) {
        // Redireciona para a página de login
        header("Location: login.php");
    exit();
}


    $sql = "SELECT posicao, nickname, contato FROM rankingx1prata ORDER BY posicao ASC";
    $result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking X1 Prata</title>
    <style>
        .card span{
            width: 200px;
            margin: 10px auto;
        }
    </style>
</head>
<body>
    <div class=''>
        <span class='btn btn-secondary'>Equipamentos</span><span class='btn btn-secondary'>Regras</span>
    </div>
    <div class="container">
        
        <h1 class="mt-5">Ranking X1 Prata</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nick</th>
                    <th>Contato</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if ($result->num_rows > 0) {
                    // Saída de dados de cada linha
                    while ($row = $result->fetch_assoc()) {
                        $whatsappLink = "https://wa.me/" . $row["contato"] . "?text=Olá, quero te desafiar pela sua posição no ranking de X1 nível prata! Quando podemos marcar nossa partida?";
                        echo "<tr>
                                <td>" . $row["posicao"] . "</td>
                                <td>" . $row["nickname"] . "</td>
                                <td><a href='$whatsappLink' target='_blank'>Desafiar</a></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Nenhum resultado encontrado</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS e dependências -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>