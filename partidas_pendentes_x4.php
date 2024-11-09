<?php
    session_start();
    include 'db/db.php';
    include 'components/menu.php';

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['admmaster'] !== 'S') {
        header("Location: login.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Partidas com Imagens</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .confronto-section {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .confronto-item {
            display: flex;
            flex-direction: column;
            align-items: start;
            color: #215d94;
        }
        .confronto-header {
            font-weight: bold;
            font-size: 1.2em;
            color: #333;
        }
        .team-names {
            margin: 10px 0;
            font-size: 1.1em;
        }
        .image-container {
            display: flex;
            gap: 10px;
        }
        .image-container img {
            width: 40px;
            height: 40px;
            cursor: pointer;
        }
        .modal-body img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .cabecalho{
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="cabecalho">
        <h1>Pendentes de Atualiazação</h1>
        <a class="btn btn-default" href="javascript:history.back()">Voltar</a>

    </div>
    

    <?php
        include 'db/db.php';

        $sql = "SELECT t1.team_name AS team1_name, t2.team_name AS team2_name, m.round, 
                       m.img_batalha1, m.img_batalha2, m.img_batalha3
                FROM matches_x4 m
                JOIN teams_x4 t1 ON m.team1_id = t1.id
                JOIN teams_x4 t2 ON m.team2_id = t2.id
                WHERE m.realizada = 'N' 
                  AND (m.img_batalha1 IS NOT NULL OR m.img_batalha2 IS NOT NULL OR m.img_batalha3 IS NOT NULL)";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<div class='confronto-section'>";

            while ($row = $result->fetch_assoc()) {
                echo "<div class='confronto-item'>
                        <div class='confronto-header'>Rodada " . htmlspecialchars($row['round']) . "</div>
                        <div class='team-names'>" . htmlspecialchars($row['team1_name']) . " VS " . htmlspecialchars($row['team2_name']) . "</div>
                        <div class='image-container'>";

                $imageFolder = 'uploads/x4/rodada' . $row['round'] . '/';
                for ($i = 1; $i <= 3; $i++) {
                    $imageCol = 'img_batalha' . $i;
                    if (!empty($row[$imageCol])) {
                        $imagePath = $imageFolder . $row[$imageCol];
                        echo "<a data-bs-toggle='modal' data-bs-target='#imageModal' data-image='$imagePath'>
                                <img src='assets/icon-image.png' alt='Ver Imagem'>
                              </a>";
                    }
                }

                echo "</div></div>";
            }
            echo "</div>";
        } else {
            echo "<p>Nenhuma partida encontrada.</p>";
        }

        $conn->close();
    ?>

    <!-- Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">&times;</button>
                <div class="modal-body">
                    <img id="imagemModal" src="#" alt="Imagem da Batalha">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#imageModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var imagePath = button.data('image');
                var modal = $(this);
                modal.find('#imagemModal').attr('src', imagePath);
            });
        });
    </script>
</body>
</html>
