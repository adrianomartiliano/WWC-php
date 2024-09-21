<?php
include 'components/menu.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$userId = isset($_SESSION['iduser']) ? $_SESSION['iduser'] : 0;
$teamId = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0;

if ($teamId == 0) {
    echo "<script>alert('Equipe inválida!')</script>";
    exit;
}

include 'db/db.php';

// Buscar dados da equipe
$sqlTeam = "SELECT * FROM teams_x4 WHERE id = ?";
$stmtTeam = $conn->prepare($sqlTeam);
$stmtTeam->bind_param('i', $teamId);
$stmtTeam->execute();
$resultTeam = $stmtTeam->get_result();

if ($resultTeam->num_rows == 0) {
    echo "Equipe não encontrada!";
    exit;
}

$team = $resultTeam->fetch_assoc(); // Aqui estamos definindo a variável $team corretamente

// Buscar batalhas da equipe do usuário
$sqlMatches = "SELECT id, round, team1_id, team2_id, img_batalha1, img_batalha2, img_batalha3, recebido, finalizada 
               FROM matches_x4 
               WHERE team1_id = ? OR team2_id = ?";
$stmtMatches = $conn->prepare($sqlMatches);
$stmtMatches->bind_param('ii', $teamId, $teamId);
$stmtMatches->execute();
$resultMatches = $stmtMatches->get_result();

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Equipe - <?= htmlspecialchars($teamId['team_name']); ?></title>
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <style>
        td, th{
            opacity: 0.9;
            background-color: #ffab07 !important;
            color: #215d94 !important;
            border: none;
        }
        .container-list{
            margin: 0 auto;
            width: 90%;
            box-shadow: 0 4px 19px rgba(0, 0, 0, 0.9);
            background-color: #ffab07;
            border-radius: 15px;
            padding: 10px;
        }
    </style>
</head>
<body class='bg-1'>
    <div class="mt-3 container-center">
        <a href="gerenciar_equipe_membros_x4.php?team_id=<?= $teamId ?>" class="btn btn-default">Alterar Dados da Equipe</a>
    </div>
    <div class="container mt-5 container-list">
        <h2 class="title-container">Gerenciar Equipe: <?= htmlspecialchars($team['team_name']); ?></h2>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Rodada</th>
                    <th>Contra</th>
                    <th>Anexar</th>
                    <th>E</th>
                    <th>A</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($match = $resultMatches->fetch_assoc()): 
                    // Determinar o time oponente
                    $opponentId = ($match['team1_id'] == $teamId) ? $match['team2_id'] : $match['team1_id'];

                    // Buscar nome do time oponente
                    $sqlOpponent = "SELECT team_name FROM teams_x4 WHERE id = ?";
                    $stmtOpponent = $conn->prepare($sqlOpponent);
                    $stmtOpponent->bind_param('i', $opponentId);
                    $stmtOpponent->execute();
                    $resultOpponent = $stmtOpponent->get_result();
                    $opponent = $resultOpponent->fetch_assoc();
                ?>
                    <tr>
                        <td><?= $match['round']; ?></td>
                        <td><?= htmlspecialchars($opponent['team_name']); ?></td>
                        <td>
                            <button class="btn-icon" data-bs-toggle="modal" data-bs-target="#uploadModal" data-match-id="<?= $match['id']; ?>" data-round="<?= $match['round']; ?>">
                                <img class="icon-info" src='assets/anexar.png'></img>
                            </button>
                        </td>
                        <td>
                            <?php if ($match['recebido'] == 'N'): ?>
                                <img src='assets/icon-x.png' class="icon-info" ></img>
                            <?php else: ?>
                                <img src='assets/icon-success.png' class="icon-info" ></img>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($match['finalizada'] == 'N'): ?>
                                <img src='assets/icon-x.png' class="icon-info" ></img>
                            <?php else: ?>
                                <img src='assets/icon-success.png' class="icon-info" ></img>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Modal para envio de resultados -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Enviar Resultado da Rodada <span id="roundNumber"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="uploadForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" id="matchId" name="match_id" value="">
                            
                            <div class="mb-3">
                                <label for="img_batalha1" class="form-label">Imagem da Batalha 1</label>
                                <input type="file" class="form-control" id="img_batalha1" name="img_batalha1" required>
                            </div>
                            <div class="mb-3">
                                <label for="img_batalha2" class="form-label">Imagem da Batalha 2</label>
                                <input type="file" class="form-control" id="img_batalha2" name="img_batalha2" required>
                            </div>
                            <div class="mb-3">
                                <label for="img_batalha3" class="form-label">Imagem da Batalha 3</label>
                                <input type="file" class="form-control" id="img_batalha3" name="img_batalha3">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Salvar Resultados</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
        
        

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Abrir modal e preencher rodada
        $('#uploadModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var matchId = button.data('match-id');
            var round = button.data('round');
            
            var modal = $(this);
            modal.find('#matchId').val(matchId);
            modal.find('#roundNumber').text(round);
        });

        // Enviar formulário via AJAX
        $('#uploadForm').on('submit', function (e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: 'processamento/process_upload_results.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    alert(response);
                    $('#uploadModal').modal('hide');
                    if (response.includes("sucesso")) { // Verifica se a resposta contém "sucesso"
                        window.location.reload(); // Recarrega a página
                    }
                },
                error: function () {
                    alert('Erro ao enviar os resultados.');
                }
            });
        });
    </script>

    
</body>
</html>
