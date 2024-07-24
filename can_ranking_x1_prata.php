<?php
session_start();
include 'components/menu.php';
include 'db/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin'])) {
    // Redireciona para a página de login
    header("Location: login.php");
    exit();
}

// Verifica se o usuário é um administrador do clã
$is_admin_can = isset($_SESSION['admcan']) && $_SESSION['admcan'] == 'S';

// Obtém o número total de registros
$sql_total = "SELECT COUNT(*) as total FROM rankingx1prata_can";
$result_total = $conn->query($sql_total);
$total_records = $result_total->fetch_assoc()['total'];

$sql = "SELECT posicao, nickname FROM rankingx1prata_can ORDER BY posicao ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking X1 Prata</title>
    <style>
        .card {
            width: 90%;
        }
        .card span {
            width: 200px;
            margin: 10px auto;
        }
        h1{
            text-align: center;
        }
        .menu-x1prata{
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .menu-x1prata span{
            margin: 10px 10px;
        }
    </style>
</head>
<body>
    <div class='container menu-x1prata'>
        <span class='btn btn-primary' data-toggle="modal" data-target="#carouselModal">Equipamentos</span>
        <span class='btn btn-primary' data-toggle="modal" data-target="#regrasModal">Regras</span>
    </div>
    <div class="container card">
        <table class="table table-striped">
            <h1 class="mt-2">X1 de Prata dos Cangaceiros</h1>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nick</th>
                    <?php if ($is_admin_can): ?>
                        <th>Ações</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row["posicao"] . "</td>
                                <td>" . $row["nickname"] . "</td>";
                        if ($is_admin_can) {
                            echo "<td>";
                            if ($row["posicao"] > 1) {
                                echo "<a href='processamento/troca_posicao_rankingcan.php?acao=subir&posicao=" . $row["posicao"] . "' class='btn btn-success btn-sm'>Subir</a> ";
                            } else {
                                echo "<button class='btn btn-success btn-sm' disabled>Subir</button> ";
                            }
                            if ($row["posicao"] < $total_records) {
                                echo "<a href='processamento/troca_posicao_rankingcan.php?acao=descer&posicao=" . $row["posicao"] . "' class='btn btn-danger btn-sm'>Descer</a>";
                            } else {
                                echo "<button class='btn btn-danger btn-sm' disabled>Descer</button>";
                            }
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Nenhum resultado encontrado</td></tr>";
                }
                $conn->close();
            ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="carouselModal" tabindex="-1" role="dialog" aria-labelledby="carouselModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="carouselModalLabel">Equipamentos Liberados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="carouselEquipamentos" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            <?php
                                $totalImages = 26; 
                                for ($i = 1; $i <= $totalImages; $i++) {
                                    $activeClass = ($i === 1) ? 'active' : ''; 
                                    echo "<div class='carousel-item $activeClass'>
                                            <img class='d-block w-100' src='https://ww2cup.app.br/images/armaspermitidas/$i.png' alt='Equipamento $i'>
                                          </div>";
                                }
                            ?>
                        </div>
                        <a class="carousel-control-prev" href="#carouselEquipamentos" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only"></span>
                        </a>
                        <a class="carousel-control-next" href="#carouselEquipamentos" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS e dependências -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
