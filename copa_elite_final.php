<?php
session_start();
include 'components/menu.php';
include 'db/db.php';

$admmaster = isset($_SESSION['admmaster']) && $_SESSION['admmaster'] === 'S';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fases do Torneio - Semi Final</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body class='bg-2'>
    
    <div class='menu-de-fases'>
        <a href="copa_elite.php" class='btn btn-success btn-opacity'>Quartas de Final</a><a href="copa_elite_semi.php" class='btn btn-success btn-opacity'>Semi Final</a>
    </div>

    <h1 class='title-fase'>Final</h1>

    <!-- Botão para atualizar partidas (apenas para admmaster) -->
    <?php if ($admmaster): ?>
    <div class='att-partida'>
        <button type="button" class="btn btn-success btn-opacity" data-bs-toggle="modal" data-bs-target="#updateModal">
            Atualizar Partidas
        </button>
    </div>
    <?php endif; ?>

    <?php
    // Função para exibir confrontos, pegando o último confronto adicionado
    function exibirConfronto($confronto, $conn) {
        // Ordenar pelo ID (ou outra coluna relevante) em ordem decrescente e pegar o último
        $query = "SELECT equipe1, equipe2, placar1, placar2, batalha1_img, batalha2_img, batalha3_img FROM $confronto ORDER BY id LIMIT 1";
        $result = $conn->query($query);
    
        if ($result->num_rows > 0) {
            // Pega a última linha do resultado
            $row = $result->fetch_assoc();
            
            echo "<p class='flex-between'><span>" . $row['equipe1'] . "</span> <span class='placar'>" . $row['placar1'] . "</span></p>";
            echo "<p class='flex-between'><span>" . $row['equipe2'] . "</span> <span class='placar'>" . $row['placar2'] . "</span></p>";
    
            // Exibir ícones de imagens, se houver imagens no banco
            echo "<div class='conteiner-icons-images'>";
            if ($row['batalha2_img']) {
                echo "<img class='icon-image' src='assets/icon-image.png' data-image='uploads/" . htmlspecialchars($row['batalha1_img'], ENT_QUOTES, 'UTF-8') . "'>";
            }
            if ($row['batalha2_img']) {
                echo "<img class='icon-image' src='assets/icon-image.png' data-image='uploads/" . $row['batalha2_img'] . "'>";
            }
            if ($row['batalha3_img']) {
                echo "<img class='icon-image' src='assets/icon-image.png' data-image='uploads/" . $row['batalha3_img'] . "'>";
            }
            echo "</div>";
        } else {
            echo "<p>Aguardando confrontos</p>";
        }
    }
    ?>

    <div class="confronto-section">

        <?php exibirConfronto('f1_copaelite', $conn); ?>
    </div>

    <div class="confronto-section">
        <p>Disputa do Terceiro Lugar</p>
        <?php exibirConfronto('f2_copaelite', $conn); ?>
    </div>


    <!-- Modal de Atualização -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="updateModalLabel">Atualizar Confrontos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="processamento/add_final.php" method="POST" enctype="multipart/form-data">
                        <?php
                        // Função para preencher os valores do formulário com base nos dados da tabela
                        function preencherForm($confronto, $conn, $index, $titleFase) {
                            $query = "SELECT equipe1, equipe2, placar1, placar2 FROM $confronto";
                            $result = $conn->query($query);
                            $row = $result->fetch_assoc();

                            // Defina os valores padrão para cada campo
                            $equipe1 = $row['equipe1'] ?? '';
                            $equipe2 = $row['equipe2'] ?? '';
                            $placar1 = $row['placar1'] ?? '';
                            $placar2 = $row['placar2'] ?? '';

                            echo "
                            <div class='form-section'>
                                <h4>$titleFase</h4>
                                <div class='input-group'>
                                    <label class='desc-time' for='equipe1_$index'>Time 1:</label>
                                    <select class='input-time' name='equipe1_$index'>
                                        <option value='$equipe1'>$equipe1</option>
                                        " . listarEquipes($conn) . "
                                    </select>
                                </div>
                                <div class='input-group'>
                                    <label class='desc-time' for='equipe2_$index'>Time 2:</label>
                                    <select class='input-time' name='equipe2_$index'>
                                        <option value='$equipe2'>$equipe2</option>
                                        " . listarEquipes($conn) . "
                                    </select>
                                </div>
                                <div class='input-group'>
                                    <label class='desc-placar' for='placar1_$index'>Placar Equipe 1:</label>
                                    <input class='input-placar' type='number' name='placar1_$index' min='0' value='$placar1'>
                                </div>
                                <div class='input-group'>
                                    <label class='desc-placar' for='placar2_$index'>Placar Equipe 2:</label>
                                    <input class='input-placar' type='number' name='placar2_$index' min='0' value='$placar2'>
                                </div>
                                <div>
                                    <label for='imagem'>Batalha 1:</label>
                                    <input type='file' name='img1_$index' accept='image/*'>
                                    <label for='imagem'>Batalha 2:</label>
                                    <input type='file' name='img2_$index' accept='image/*'>
                                    <label for='imagem'>Batalha 3:</label>
                                    <input type='file' name='img3_$index' accept='image/*'>
                                </div>
                            </div>";
                        }

                        // Função para listar equipes no select
                        function listarEquipes($conn) {
                            $query = "SELECT nomeequipe FROM copa_elite_classificados";
                            $result = $conn->query($query);
                            $options = '';
                            while ($row = $result->fetch_assoc()) {
                                $options .= "<option value='" . $row['nomeequipe'] . "'>" . $row['nomeequipe'] . "</option>";
                            }
                            return $options;
                        }

                        // Preenchendo os formulários para cada quartas
                        preencherForm('f1_copaelite', $conn, 1, 'Final');
                        preencherForm('f2_copaelite', $conn, 2, 'Terceiro Lugar');
                        ?>
                        <button type="submit" class="btn btn-success">Salvar Confrontos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('.icon-image').forEach(function(icon) {
    icon.addEventListener('click', function() {
        var imagePath = this.getAttribute('data-image');
        console.log('Caminho da Imagem:', imagePath);  // Verifique se o caminho está correto
        if (imagePath) {
            var imageModal = document.getElementById('imageModal');
            var modalImage = document.getElementById('modalImage');
            modalImage.src = imagePath;
            var modal = new bootstrap.Modal(imageModal);
            modal.show();
        }
    });
});

</script>

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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

</body>
</html>
