
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
        <div class="card-header">Torneio de Equipes - X4</div>
        <div class="card-body">
            <h5 class="card-title">Inscrições Liberadas</h5>
            <p class="card-text">Torneio de X4 com premiação para o 1º, 2º e 3º lugar. Valor: R$ 40,00 por equipe.</p>
            <div>
                <a href="inscricao_x4.php" class="btn btn-secondary" title="Inscrição">
                    Inscrição
                </a>
                <a href="#" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#regrasModal" title="Regras">
                    Regras
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de Regras -->
    <div class="modal fade" id="regrasModal" tabindex="-1" aria-labelledby="regrasModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="regrasModalLabel">Regras - X4</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="accordion" id="accordionRegras">
                        <?php
                            include 'db/db.php';
                            $sqlGrupos = "SELECT DISTINCT cod_grupo, desc_grupo FROM regras_x4 ORDER BY cod_grupo";
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
                        url: 'processamento/fetch_regras_x4.php',
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
