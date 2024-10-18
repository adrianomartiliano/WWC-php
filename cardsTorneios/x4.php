<?php

// Verifica a conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Consulta para contar as equipes inscritas
$sql = "SELECT COUNT(*) as total FROM teams_x4";
$result = $conn->query($sql);

// Verifica a quantidade de equipes
$row = $result->fetch_assoc();
$total_teams = $row['total'];

// Define o status do botão de inscrição
$inscricao_habilitada = $total_teams < 18;

?>

<body class="bg-1">
    <div class="card border-secondary mb-3">
        <div class="card-header flex-between">
            Torneio de Equipes - X4
            <img class="icon-info" src="assets/info.png" alt="Icon de Informações" data-bs-toggle="modal" data-bs-target='#infoModal'>
        </div>
        
        <div class="card-body">
            <h5 class="card-title">Inscrições <?php echo $inscricao_habilitada ? 'Liberadas' : 'Encerradas'; ?></h5>
            <p class="card-text">
                Prepare-se para a competição mais acirrada do cenário WW2! Neste torneio de pontos corridos, equipes de 4 jogadores vão se enfrentar em batalhas intensas, onde cada tiro conta e a estratégia é tudo. Somente os mais habilidosos e coordenados alcançarão o topo. Afie sua mira, fortaleça sua equipe e domine a arena. A glória aguarda aqueles que forem implacáveis!
            </p>
            <div>
                <a href="inscricao_x4.php" class="btn btn-default <?php echo $inscricao_habilitada ? '' : 'disabled'; ?>" title="Inscrição">
                    <?php echo $inscricao_habilitada ? 'Inscrição' : 'Inscrição Encerrada'; ?>
                </a>
                <a href="#" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#regrasModal" title="Regras">Regras</a><br />
                <a href="teams_x4.php" class="btn btn-default" title="Equipes">Equipes</a>
                <a href="torneio_x4.php" class="btn btn-default" title="Tabela">Tabelas</a>

                <?php
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                        
                        // Exibe botões de Prazos e Partidas apenas para o admin master
                        if ($_SESSION['admmaster'] === 'S') {
                            echo '<a href="painel_prazo_final.php" class="btn btn-default" title="Tabela">Prazos</a>';
                            echo '<a href="painel_salvar_confrontos_x4.php" class="btn btn-default" title="Partidas">Partidas</a>';
                        }
                
                        // Verifica se o usuário é member1 em alguma equipe
                        $userId = $_SESSION['iduser'];
                        $sql = "SELECT * FROM teams_x4 WHERE member1 = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('i', $userId);
                        $stmt->execute();
                        $result = $stmt->get_result();
                
                        if ($result->num_rows > 0) {   
                            echo '<a href="painel.php" class="btn btn-default" title="Tabela">Enviar Resultados</a>';
                        }
                
                        $stmt->close();
                    }
                ?>

            </div>
        </div>
    </div>
</body>

<!-- Modal de Informações -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel">Informações Complementares</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Esse torneio dará pontos de RANKING para os 10 primeiros colocados.</p>
                <p>1º - 20 pontos<br />
                    2º - 9 pontos<br />
                    3º - 8 pontos<br />
                    4º - 7 pontos<br />
                    5º - 6 pontos<br />
                    6º - 5 pontos<br />
                    7º - 4 pontos<br />
                    8º - 3 pontos<br />
                    9º - 2 pontos<br />
                    10º - 1 ponto
                </p>
            </div>
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
                    // Consulta para carregar os grupos de regras
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
                                        <button class='btn text-left btn-default' type='button' data-cod-grupo='{$grupo['cod_grupo']}' data-bs-toggle='collapse' data-bs-target='#collapse$index' aria-expanded='true' aria-controls='collapse$index'>
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
