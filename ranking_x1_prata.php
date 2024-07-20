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

    $sql = "SELECT posicao, nickname, contato FROM rankingx1prata ORDER BY posicao ASC";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking X1 Prata</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card span {
            width: 200px;
            margin: 10px auto;
        }
        h1{
            text-align: center;
        }
        .menu-x1prata{
            display: flex;
            justify-content: center;
        }
        .menu-x1prata span{
            margin: 10px 10px;
        }
    </style>
</head>
<body>
    <div class='container menu-x1prata'>
        <span class='btn btn-primary' data-toggle="modal" data-target="#carouselModal">Equipamentos</span>
        <span class='btn btn-primary' data-toggle="modal" data-target="#regrasModal">Regras</span>
    </div>
    <div class="container">
        <h1 class="mt-2">Ranking X1 Prata</h1>
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

    <div class="modal fade" id="carouselModal" tabindex="-1" role="dialog" aria-labelledby="carouselModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="carouselModalLabel">Equipamentos Liberados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="carouselEquipamentos" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            <?php
                                $totalImages = 26; 
                                for ($i = 1; $i <= $totalImages; $i++) {
                                    $activeClass = ($i === 1) ? 'active' : ''; 
                                    echo "<div class='carousel-item $activeClass'>
                                            <img class='d-block w-100' src='https://ww2cup.app.br/images/armaspermitidas/$i.png' alt='Equipamento $i'>
                                          </div>";
                                }
                            ?>
                        </div>
                        <a class="carousel-control-prev" href="#carouselEquipamentos" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselEquipamentos" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS e dependências -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
