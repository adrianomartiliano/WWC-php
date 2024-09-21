<?php
include 'db/db.php';
include 'components/menu.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Verificar se a rodada foi selecionada via GET
$selected_round = isset($_GET['round']) ? (int)$_GET['round'] : 1; // Rodada padrão é 1
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tabela de jogos</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body class='bg-2'>
    <div class='menu-de-fases'>
        <a class="btn btn-default" href="#">Classificação</a>
    </div>
    
    <?php
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION['admcan'] === 'S') {
        echo "
            <div class='menu-de-fases'>
                <a class='btn btn-default' href='inserir_resultados_x4.php'>Inserir Resultados</a>
            </div>
        ";
    }
    ?>

    <!-- Formulário para selecionar a rodada -->
    <form class='form-select-round' method="GET" action="">
        <select name="round" id="round" onchange="this.form.submit()">
            <?php 
                for ($i = 1; $i <= 11; $i++) {
                    $selected = $i == $selected_round ? 'selected' : '';
                    echo "<option value='$i' $selected>Rodada $i</option>";
                }
            ?>
        </select>
    </form>

    <?php
    // Consulta para recuperar a rodada e partidas com base na seleção
    $sql = "SELECT m.round, t1.team_name AS team1_name, t2.team_name AS team2_name, m.score_team1, m.score_team2, m.img_batalha1, m.img_batalha2, m.img_batalha3
            FROM matches_x4 m
            JOIN teams_x4 t1 ON m.team1_id = t1.id
            JOIN teams_x4 t2 ON m.team2_id = t2.id
            WHERE m.round = ?
            ORDER BY m.id DESC";

    // Preparar a consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $selected_round); // Associar a rodada selecionada
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='confronto-section'>";
            echo "<p class='flex-between'><span>" . htmlspecialchars($row['team1_name'], ENT_QUOTES, 'UTF-8') . "</span> <span class='placar'>" . htmlspecialchars($row['score_team1'], ENT_QUOTES, 'UTF-8') . "</span></p>";
            echo "<p class='flex-between'><span>" . htmlspecialchars($row['team2_name'], ENT_QUOTES, 'UTF-8') . "</span> <span class='placar'>" . htmlspecialchars($row['score_team2'], ENT_QUOTES, 'UTF-8') . "</span></p>";

            // Exibir ícones de imagens, se houver imagens no banco
            echo "<div class='conteiner-icons-images'>";
            if (!empty($row['img_batalha1'])) {
                echo "<a href='#' data-bs-toggle='modal' data-bs-target='#imageModal' data-image='uploads/x4/rodada" . $selected_round . "/" . htmlspecialchars($row['img_batalha1'], ENT_QUOTES, 'UTF-8') . "'>";
                echo "<img class='icon-image' src='assets/icon-image.png' alt='Imagem da Batalha 1'>";
                echo "</a>";
            }
            if (!empty($row['img_batalha2'])) {
                echo "<a href='#' data-bs-toggle='modal' data-bs-target='#imageModal' data-image='uploads/x4/rodada" . $selected_round . "/" . htmlspecialchars($row['img_batalha2'], ENT_QUOTES, 'UTF-8') . "'>";
                echo "<img class='icon-image' src='assets/icon-image.png' alt='Imagem da Batalha 2'>";
                echo "</a>";
            }
            if (!empty($row['img_batalha3'])) {
                echo "<a href='#' data-bs-toggle='modal' data-bs-target='#imageModal' data-image='uploads/x4/rodada" . $selected_round . "/" . htmlspecialchars($row['img_batalha3'], ENT_QUOTES, 'UTF-8') . "'>";
                echo "<img class='icon-image' src='assets/icon-image.png' alt='Imagem da Batalha 3'>";
                echo "</a>";
            }
            echo "</div>";

            echo "</div>";
        }
    } else {
        echo "Nenhuma partida encontrada para a rodada $selected_round.";
    }

    $stmt->close();
    $conn->close();
    ?>

    <!-- Modal para exibir a imagem -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Batalha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="modalImage" src="" class="img-fluid" alt="Imagem da Batalha">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>

    <script>
        // Atualizar a imagem do modal quando o link da imagem for clicado
        $('#imageModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Botão que acionou o modal
            var imageUrl = button.data('image'); // Recuperar o URL da imagem

            var modal = $(this);
            modal.find('#modalImage').attr('src', imageUrl); // Atualizar o src da imagem no modal
        });
    </script>
</body>
</html>
