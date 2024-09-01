<?php

include 'db/db.php';
include 'components/menu.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário é admmaster
$admmaster = isset($_SESSION['admmaster']) && $_SESSION['admmaster'] === 'S';

// Definir o tipo de lista a ser exibida (por jogador ou por clã)
$viewType = isset($_GET['view']) ? $_GET['view'] : 'players'; // default to players

// Consultar apoiadores por jogadores ou clãs
if ($viewType === 'players') {
    // Consulta para selecionar os apoiadores por jogador, incluindo o valor doado
    $sqlApoiadores = "SELECT u.nickname, c.nomecla, u.valor_apoiado
                      FROM users u
                      LEFT JOIN cla c ON u.cla_id = c.idcla
                      WHERE u.valor_apoiado > 0
                      ORDER BY u.valor_apoiado DESC";
    $resultApoiadores = $conn->query($sqlApoiadores);
} else {
    // Consulta para selecionar os clãs com a soma dos valores apoiados igual a zero
    $sqlClans = "SELECT c.nomecla, SUM(u.valor_apoiado) AS total_valor
                 FROM users u
                 INNER JOIN cla c ON u.cla_id = c.idcla
                 GROUP BY c.idcla
                 HAVING total_valor > 0
                 ORDER BY total_valor DESC";
    $resultClans = $conn->query($sqlClans);
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apoiadores do Site</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <style>
        .active-btn {
            background-color: #007bff;
            color: white;
        }
        .inactive-btn {
            background-color: #f8f9fa;
            color: #007bff;
        }
        .title-apoiadores {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="container mt-4">
        <div class='title-apoiadores'>
            <h1 id='title-apoiadores'>Apoiadores dos Torneios</h1>
            <a class='btn btn-success' data-bs-toggle="modal" data-bs-target='#infoModalApoio'>Apoiar</a>
        </div>
        

        <?php if ($admmaster): ?>
            <button type="button" class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#adicionarValorModal">
                Adicionar Apoio
            </button>
        <?php endif; ?>

        <!-- Botões de filtro -->
        <div class="mb-4">
            <button type="button" class="btn <?= $viewType === 'players' ? 'active-btn' : 'inactive-btn' ?>" onclick="window.location.href='?view=players'">
                Jogadores
            </button>
            <button type="button" class="btn <?= $viewType === 'clans' ? 'active-btn' : 'inactive-btn' ?>" onclick="window.location.href='?view=clans'">
                Clãs
            </button>
        </div>

        <?php if ($viewType === 'players'): ?>
            <?php if ($resultApoiadores->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nickname</th>
                            <th>Clã</th>
                            <?php if ($admmaster): ?>
                                <th>Valor Apoiado</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $position = 1;
                        while ($apoiador = $resultApoiadores->fetch_assoc()): ?>
                            <tr>
                                <td><?= $position++; ?></td>
                                <td><?= htmlspecialchars($apoiador['nickname']); ?></td>
                                <td><?= htmlspecialchars($apoiador['nomecla']); ?></td>
                                <?php if ($admmaster): ?>
                                    <td><?= htmlspecialchars(number_format($apoiador['valor_apoiado'], 2, ',', '.')); ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum apoiador encontrado.</p>
            <?php endif; ?>
        <?php elseif ($viewType === 'clans'): ?>
            <?php if ($resultClans->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Clã</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($clan = $resultClans->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($clan['nomecla']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum clã encontrado.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Modal para adicionar valor apoiado -->
    <div class="modal fade" id="adicionarValorModal" tabindex="-1" aria-labelledby="adicionarValorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adicionarValorModalLabel">Adicionar Valor Apoiado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="processamento/add_support_value.php" method="POST">
                        <div class="mb-3">
                            <label for="id_user" class="form-label">Usuário</label>
                            <select class="form-select" id="id_user" name="id_user" required>
                                <option value="" selected disabled>Selecione o usuário</option>
                                <?php
                                // Consulta para buscar todos os usuários
                                $sqlUsers = "SELECT iduser, nickname FROM users ORDER BY nickname ASC";
                                $resultUsers = $conn->query($sqlUsers);
                                if ($resultUsers->num_rows > 0):
                                    while ($user = $resultUsers->fetch_assoc()):
                                ?>
                                    <option value="<?= $user['iduser'] ?>"><?= htmlspecialchars($user['nickname']) ?></option>
                                <?php
                                    endwhile;
                                endif;
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="valor_apoiado" class="form-label">Valor Apoiado</label>
                            <input type="number" step="0.01" class="form-control" id="valor_apoiado" name="valor_apoiado" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Modal de Informações-->
    <div class="modal fade" id="infoModalApoio" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document"> >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Informações</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                            <p>Brasil <br />
                            Pix: 43999929497</p>
                            <p>Fora do Brasil <br />
                            PayPal: (consulte o e-mail para transferência)</p>
                            <p>Mercado Pago: <a href='https://link.mercadopago.com.br/ww2cup' target='_blank'>Clique Aqui!</a></p>
                            <br /><br />
                            
                            
                            <p>Manter este site de torneios funcionando envolve diversos custos, desde hospedagem e manutenção até a melhoria contínua da nossa plataforma. Criamos este espaço para que jogadores possam se conectar, competir e se divertir, e é graças ao apoio da nossa comunidade que conseguimos manter tudo isso ativo.</p>

                            <p>Se você aprecia o trabalho que fazemos e quer garantir que continuemos a oferecer os melhores torneis de WW2, considere fazer uma doação mensal. Cada contribuição, por menor que seja, faz uma grande diferença e nos ajuda a manter essa comunidade viva e próspera.</p>

<p>Obrigado por fazer parte desta jornada! Sua doação é fundamental para continuarmos juntos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
