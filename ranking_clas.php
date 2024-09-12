<?php

include 'components/menu.php';

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    if ($message == 'success') {
        echo "<div class='alert alert-success'>Pontos adicionados com sucesso!</div>";
    } elseif ($message == 'error_log') {
        echo "<div class='alert alert-danger'>Erro ao registrar a adição de pontos!</div>";
    } elseif ($message == 'error_update') {
        echo "<div class='alert alert-danger'>Erro ao atualizar os pontos do clã!</div>";
    } elseif ($message == 'invalid_data') {
        echo "<div class='alert alert-warning'>Dados inválidos fornecidos!</div>";
    }
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db/db.php';

$admmaster = isset($_SESSION['admmaster']) && $_SESSION['admmaster'] === 'S';

// Consultar os clãs com mais de 0 pontos para o ranking
$sqlClans = "SELECT nomecla, points FROM cla WHERE points > 0 ORDER BY points DESC";
$resultClans = $conn->query($sqlClans);

// Consultar todos os clãs para o modal de adição de pontos
$sqlAllClans = "SELECT idcla, nomecla FROM cla ORDER BY nomecla ASC";
$resultAllClans = $conn->query($sqlAllClans);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking de Clãs</title>
    <style>
        .btn-add-points {
            margin: 20px;
        }
        .alert{
            margin: 10px;
        }
    </style>
</head>
<body>
<?php if ($admmaster): ?>
    <button type="button" class="btn btn-default btn-add-points" data-bs-toggle="modal" data-bs-target="#adicionarPontosModal">
        Adicionar Pontos
    </button>
<?php endif; ?>
    <div class="container mt-5">
        <h2>Ranking de Clãs</h2>

        <?php if ($resultClans->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Clã</th>
                        <th>Pontos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $position = 1;
                    while ($clan = $resultClans->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><?= $position++; ?></td>
                            <td><?= htmlspecialchars($clan['nomecla']); ?></td>
                            <td><?= htmlspecialchars($clan['points']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum clã com pontos no momento.</p>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="adicionarPontosModal" tabindex="-1" aria-labelledby="adicionarPontosModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adicionarPontosModalLabel">Adicionar Pontos ao Clã</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body">
                    <form id="adicionarPontosForm" action="processamento/add_points_cla.php" method="POST">
                    <div class="mb-3">
                        <label for="id_cla" class="form-label">Clã</label>
                        <select class="form-select" id="id_cla" name="id_cla" required>
                        <option value="" selected disabled>Selecione o Clã</option>
                        <?php if ($resultAllClans->num_rows > 0): ?>
                            <?php while ($cla = $resultAllClans->fetch_assoc()): ?>
                            <option value="<?= $cla['idcla'] ?>"><?= htmlspecialchars($cla['nomecla']) ?></option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="" disabled>Nenhum clã disponível</option>
                        <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pontos_adicionados" class="form-label">Pontos</label>
                        <input type="number" class="form-control" id="pontos_adicionados" name="pontos_adicionados" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Adicionar Pontos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
