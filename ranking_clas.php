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
$sqlClans = "SELECT nomecla, points, siglacla FROM cla WHERE points > 0 ORDER BY points DESC";
$resultClans = $conn->query($sqlClans);

// Consultar todos os clãs para o modal de adição de pontos
$sqlAllClans = "SELECT idcla, nomecla FROM cla ORDER BY nomecla ASC";
$resultAllClans = $conn->query($sqlAllClans);

// Consultar as 10 primeiras posições da tabela rankingx1prata
$sqlRanking = "SELECT clan FROM rankingx1prata ORDER BY posicao ASC LIMIT 10";
$resultRanking = $conn->query($sqlRanking);

// Pontos de acordo com a posição
$pointsMapping = [15, 10, 7, 7, 5, 5, 3, 3, 2, 2];

// Armazena os pontos extras para cada clã
$extraPoints = [];

// Atribuir pontos extras para cada jogador de acordo com sua posição
if ($resultRanking->num_rows > 0) {
    $position = 0;
    while ($row = $resultRanking->fetch_assoc()) {
        $clanSigla = $row['clan'];

        // Verificar se o clã já está na lista e adicionar os pontos
        if (!isset($extraPoints[$clanSigla])) {
            $extraPoints[$clanSigla] = 0;
        }
        $extraPoints[$clanSigla] += $pointsMapping[$position++];
    }
}

// Armazena clãs e suas pontuações totais em um array
$clansWithTotalPoints = [];

if ($resultClans->num_rows > 0) {
    while ($clan = $resultClans->fetch_assoc()) {
        $clanName = $clan['nomecla'];
        $basePoints = $clan['points'];
        $sigla = strtoupper($clan['siglacla']); // Use a coluna siglacla

        // Pontos extras para o clã
        $additionalPoints = isset($extraPoints[$sigla]) ? $extraPoints[$sigla] : 0;

        // Pontuação total
        $totalPoints = $basePoints + $additionalPoints;

        // Armazenar no array
        $clansWithTotalPoints[] = [
            'name' => $clanName,
            'basePoints' => $basePoints,
            'additionalPoints' => $additionalPoints,
            'totalPoints' => $totalPoints
        ];
    }

    // Ordenar o array com base na pontuação total (de forma decrescente)
    usort($clansWithTotalPoints, function ($a, $b) {
        return $b['totalPoints'] <=> $a['totalPoints'];
    });
}
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
        .alert {
            margin: 10px;
        }
        .container-list {
            margin: 0 auto;
            width: 90%;
            box-shadow: 0 4px 19px rgba(0, 0, 0, 0.9);
            background-color: #ffab07;
            border-radius: 15px;
            opacity: 0.9;
            padding: 10px;
        }
        td, th {
            opacity: 0.9;
            background-color: #ffab07 !important;
            color: #215d94 !important;
            border: none;
        }
    </style>
</head>
<body class="bg-1">
<?php if ($admmaster): ?>
    <button type="button" class="btn btn-default btn-add-points" data-bs-toggle="modal" data-bs-target="#adicionarPontosModal">
        Adicionar Pontos
    </button>
<?php endif; ?>
    <div class="container-list mt-5">
        <h2 class='title-container'>Ranking de Clãs</h2>

        <?php if (!empty($clansWithTotalPoints)): ?>
            <table class="table table-striped tabela-list-clas">
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
    foreach ($clansWithTotalPoints as $clan): 
    ?>
        <tr>
            <td><?= $position++; ?></td>
            <td><?= htmlspecialchars($clan['name']); ?></td>
            <td><?= htmlspecialchars($clan['totalPoints']); ?></td>
        </tr>
    <?php endforeach; ?>
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
