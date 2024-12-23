<?php
    session_start();
    include 'components/menu.php';
    include 'db/db.php';

    // Verifica se o usuário está logado
    if (!isset($_SESSION['loggedin'])) {
        header("Location: login.php");
        exit();
    }

    // Verifica se o usuário é administrador
    $admmaster = isset($_SESSION['admmaster']) && $_SESSION['admmaster'] === 'S';

    // Busca os grupos de regras
    $sqlGrupos = "SELECT DISTINCT cod_grupo, desc_grupo FROM regras_x1_ranking_prata ORDER BY cod_grupo ASC";
    $resultGrupos = $conn->query($sqlGrupos);

    // Busca o ranking
    $sqlRanking = "SELECT posicao, nickname, contato FROM rankingx1prata ORDER BY posicao ASC";
    $resultRanking = $conn->query($sqlRanking);

    // Obtém o número total de registros
    $sql_total = "SELECT COUNT(*) as total FROM rankingx1prata";
    $result_total = $conn->query($sql_total);
    $total_records = $result_total->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking X1 Prata</title>
    <style>
        .card{
            width: 90%;
            margin: 20px auto;
        }
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
        img {
            width: 20px;
        }
        .btn-icon{
            border: none;
        }
        #btn-desafiar{
            width: 40px;
        }
        
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body>
    <div class='container menu-x1prata'>
        <span class='btn btn-default' data-bs-toggle="modal" data-bs-target="#carouselModal">Equipamentos</span>
        <span class='btn btn-default' data-bs-toggle="modal" data-bs-target="#regrasModal">Regras</span>
    </div>
    <div class="container">
        <h1 class="mt-2">Ranking X1 Prata <img class="icon-info" src="assets/info.png" alt="Icon de Informações" data-bs-toggle="modal" data-bs-target='#infoModal'></h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nick</th>
                    <th>Desafiar</th>
                    <?php if ($admmaster): ?>
                        <th>Ações</th>
                    <?php endif; ?>
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
                                <td><a href='$whatsappLink' target='_blank'><img id='btn-desafiar' src='assets/armas.png'></img></a></td>";
                                
                        if ($admmaster) {
                            echo "<td>";
                            if ($row["posicao"] > 1) {
                                echo "<a href='processamento/troca_posicao_rankingx1prata.php?acao=subir&posicao=" . $row["posicao"] . "' class='btn-icon'>
                                <img src='assets/seta-para-cima-verde.png'></img></a> ";
                            } else {
                                echo "<button class='btn-icon' disabled></button>";
                            }
                            if ($row["posicao"] < $total_records) {
                                echo "<a href='processamento/troca_posicao_rankingx1prata.php?acao=descer&posicao=" . $row["posicao"] . "' class='btn-icon'><img src='assets/seta-para-baixo-vermelha.png'></img></a>";
                            } else {
                                echo "<button class='btn-icon' disabled></button>";
                            }
                            echo "</td>";
                        }
                        
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Nenhum resultado encontrado</td></tr>";
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

    <!--Modal de Informações-->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document"> >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Informações Complementares</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                            <p>As posições incluem pontos automáticos aos clâs de seus respectivos jogadores.</p>
                            <p>1º - 15 pontos<br />
                            2º - 10 pontos<br />
                            3º - 7 pontos<br />
                            4º - 7 pontos<br />
                            5º - 5 pontos<br />
                            6º - 5 pontos<br />
                            7º - 3 pontos<br />
                            8º - 3 pontos<br />
                            9º - 2 pontos<br />
                            10º - 2 pontos<br />
                            </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>


    <script>
        $(document).ready(function() {
            $('.collapse').on('show.bs.collapse', function () {
                var codGrupo = $(this).prev('.card-header').find('.btn').data('cod-grupo');
                var targetId = $(this).attr('id');

                if (!$(this).data('loaded')) {
                    $.ajax({
                        url: 'processamento/fetch_regras_x1_ranking_prata.php',
                        method: 'POST',
                        data: { cod_grupo: codGrupo },
                        success: function(response) {
                            $('#' + targetId + ' .card-body').html(response);
                        }
                    });
                    $(this).data('loaded', true);
                }
            });
        });
    </script>
</body>
</html>
