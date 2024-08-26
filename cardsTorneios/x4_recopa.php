
<head>
    <style>
        .card{
            width: 90%;
            margin: 20px auto;
        }
    </style>
</head>

<body class="bg-1">
    <div class="card border-secondary mb-3">
        <div class="card-header flex-between">Copa dos Campeões
            <img class="icon-info" src="assets/info.png" alt="Icon de Informações" data-bs-toggle="modal" data-bs-target='#infoModalrecopa'>
        </div>
        
        <div class="card-body">
            <h5 class="card-title">Recopa de X3 com os 8 melhores times!</h5>
            <p class="card-text"></p>
            <div>
                <a href="#" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#regrasModalRecopa" title="Regras">
                    Regras
                </a>
                <!-- <a href="#" class="btn btn-secondary" title="Equipes">
                    Tabela
                </a> -->
            </div>
        </div>
    </div>

    <!--Modal de Informações-->
    <div class="modal fade" id="infoModalrecopa" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document"> >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Informações Complementares</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                            <p>Esse torneio dará pontos de RANKING para os 10 primeiros colocados.</p>
                            <p>1º - 10 pontos<br />
                            2º - 8 pontos<br />
                            3º - 5 pontos<br />
                            </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <!-- Modal de Regras -->
    <div class="modal fade" id="regrasModalRecopa" tabindex="-1" aria-labelledby="regrasModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="regrasModalLabel">Regras e Formato - Recopa de X3</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="accordion" id="accordionRegras">
                        <?php
                            include 'db/db.php';
                            $sqlGrupos = "SELECT DISTINCT cod_grupo, desc_grupo FROM regras_x4_recopa ORDER BY cod_grupo";
                            $resultGrupos = $conn->query($sqlGrupos);

                            if ($resultGrupos->num_rows > 0) {
                                $index = 0;
                                while ($grupo = $resultGrupos->fetch_assoc()) {
                                    $index++;
                                    echo "
                                    <div class='card'>
                                        <div class='card-header' id='heading$index'>
                                            <h2 class='mb-0'>
                                                <button class='btn text-left' type='button' data-cod-grupo='{$grupo['cod_grupo']}' data-bs-toggle='collapse' data-bs-target='#collapse$index' aria-expanded='true' aria-controls='collapse$index'>
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


    <script>
        $(document).ready(function() {
            $('.collapse').on('show.bs.collapse', function () {
                var codGrupo = $(this).prev('.card-header').find('.btn').data('cod-grupo');
                var targetId = $(this).attr('id');

                if (!$(this).attr('data-loaded')) {
                    $.ajax({
                        url: 'processamento/fetch_regras_x4_recopa.php',
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
