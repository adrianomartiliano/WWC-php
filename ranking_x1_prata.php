<?php
    session_start();
    include 'components/menu.php';
    include 'db/db.php';

    if (!isset($_SESSION['loggedin'])) {
        header("Location: login.php");
        exit();
    }

    // Busca os grupos de regras
    $sqlGrupos = "SELECT DISTINCT cod_grupo, desc_grupo FROM regras_x1_ranking_prata ORDER BY cod_grupo ASC";
    $resultGrupos = $conn->query($sqlGrupos);

    // Busca o ranking
    $sqlRanking = "SELECT posicao, nickname, contato FROM rankingx1prata ORDER BY posicao ASC";
    $resultRanking = $conn->query($sqlRanking);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking X1 Prata</title>
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
        ul{
            margin: 0;
        }
        li {
            list-style: none;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class='container menu-x1prata'>
        <span class='btn btn-primary' data-bs-toggle="modal" data-bs-target="#carouselModal">Equipamentos</span>
        <span class='btn btn-primary' data-bs-toggle="modal" data-bs-target="#regrasModal">Regras</span>
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
                if ($resultRanking->num_rows > 0) {
                    while ($row = $resultRanking->fetch_assoc()) {
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

    <div class="modal fade" id="carouselModal" tabindex="-1" aria-labelledby="carouselModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="carouselModalLabel">Equipamentos Liberados</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="carouselEquipamentos" class="carousel slide" data-bs-ride="carousel">
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
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselEquipamentos" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselEquipamentos" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="regrasModal" tabindex="-1" aria-labelledby="regrasModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="regrasModalLabel">Regras</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="accordion" id="accordionRegras">
                        <?php
                            if ($resultGrupos->num_rows > 0) {
                                $index = 0;
                                while ($grupo = $resultGrupos->fetch_assoc()) {
                                    $index++;
                                    echo "
                                    <div class='card'>
                                        <div class='card-header' id='heading$index'>
                                            <h2 class='mb-0'>
                                                <button class='btn btn-block text-left' type='button' data-cod-grupo='{$grupo['cod_grupo']}' data-bs-toggle='collapse' data-bs-target='#collapse$index' aria-expanded='true' aria-controls='collapse$index'>
                                                    {$grupo['desc_grupo']}
                                                </button>
                                            </h2>
                                        </div>
                                        <div id='collapse$index' class='collapse' aria-labelledby='heading$index' data-bs-parent='#accordionRegras'>
                                            <div class='card-body' id='regrasDetalhes$index'>
                                                <!-- Regras serão carregadas aqui via AJAX -->
                                            </div>
                                        </div>
                                    </div>";
                                }
                            } else {
                                echo "<p>Nenhum grupo de regras encontrado.</p>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>


    <script>
        $(document).ready(function() {
            $('.collapse').on('show.bs.collapse', function () {
                var codGrupo = $(this).prev('.card-header').find('.btn').data('cod-grupo');
                var targetId = $(this).attr('id');

                if (!$(this).attr('data-loaded')) {
                    $.ajax({
                        url: 'processamento/fetch_regras_x1_ranking_prata.php',
                        method: 'POST',
                        data: { cod_grupo: codGrupo },
                        success: function(response) {
                            $('#' + targetId + ' .card-body').html(response);
                            $('#' + targetId).attr('data-loaded', true);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
