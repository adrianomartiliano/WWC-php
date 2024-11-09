<?php
include 'components/menu.php';
include 'db/db.php'; 

// Consulta SQL para buscar as equipes e o status
$sqlTeams = "SELECT id, team_name, member1, member2, member3, member4, `status` FROM teams_x4";
$resultTeams = $conn->query($sqlTeams);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipes - X4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .accordion-button {
            text-transform: capitalize;
        }
        .status-c {
            background-color: #d4edda;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center">Equipes - X4</h2>

    <div class="accordion" id="accordionTeams">
        <?php
        if ($resultTeams->num_rows > 0) {
            $index = 0;
            while ($team = $resultTeams->fetch_assoc()) {
                $index++;
                
                // Verifica se o status da equipe é 'C'
                $statusClass = ($team['status'] == 'C') ? 'status-c' : '';
                
                echo "
<div class='accordion-item'>
    <h2 class='accordion-header ' id='heading$index'>
        <button class='accordion-button collapsed $statusClass' type='button' data-bs-toggle='collapse' data-bs-target='#collapse$index' aria-expanded='false' aria-controls='collapse$index' data-team-id='{$team['id']}'>
            {$team['team_name']}
        </button>
    </h2>
    <div id='collapse$index' class='accordion-collapse collapse' aria-labelledby='heading$index' data-bs-parent='#accordionTeams'>
        <div class='accordion-body'>
            <p>Carregando integrantes...</p>
        </div>
    </div>
</div>";

            }
        } else {
            echo "<p>Nenhuma equipe encontrada.</p>";
        }
        ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $('.accordion-button').on('click', function () {
    var button = $(this);
    var collapse = button.closest('.accordion-item').find('.accordion-collapse');
    var teamId = button.data('team-id'); // Agora pegando diretamente o ID da equipe

    if (!collapse.attr('data-loaded')) {
        $.ajax({
            url: 'processamento/fetch_team_members_x4.php',
            method: 'POST',
            data: { team_id: teamId },
            success: function (response) {
                collapse.find('.accordion-body').html(response);
                collapse.attr('data-loaded', true); // Marca como carregado
            }
        });
    }
});
</script>

</body>
</html>
