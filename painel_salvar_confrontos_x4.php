<?php
session_start();
include 'db/db.php';
include 'components/menu.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['admmaster'] !== 'S') {
    header("Location: login.php");
    exit;
}

// Obtém os times disponíveis
$sql_teams = "SELECT id, team_name FROM teams_x4";
$result_teams = $conn->query($sql_teams);
$teams = [];

if ($result_teams->num_rows > 0) {
    while ($row = $result_teams->fetch_assoc()) {
        $teams[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preencher Confrontos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
    <style>
        .card {
            margin-bottom: 15px;
        }

        .form-container {
            margin: 20px auto;
            max-width: 800px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 2em;
            margin-bottom: 20px;
        }

        .form-select {
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }
    </style>
    <script>
        function updateCardSelects() {
            // Atualiza a lista de equipes disponíveis em todos os selects
            const selectedTeams = Array.from(document.querySelectorAll('select.team-select')).map(select => select.value);
            document.querySelectorAll('select.team-select').forEach(select => {
                const previousValue = select.value;
                select.querySelectorAll('option').forEach(option => {
                    if (selectedTeams.includes(option.value) && option.value !== previousValue) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
            });
        }

        function loadMatchData(round) {
            $.ajax({
                url: 'get_matches.php',
                type: 'GET',
                data: { round: round },
                success: function(response) {
                    const data = JSON.parse(response);

                    // Limpa todos os selects de times
                    $('select.team-select').each(function() {
                        $(this).val('');
                    });

                    if (data.length > 0) {
                        data.forEach((match, index) => {
                            $('#team1_' + (index + 1)).val(match.team1_id);
                            $('#team2_' + (index + 1)).val(match.team2_id);
                        });
                    }

                    updateCardSelects();
                },
                error: function() {
                    alert('Erro ao carregar os confrontos. Tente novamente.');
                }
            });
        }
    </script>
</head>

<body>
    <div class="container form-container">
        <h1>Preencher Confrontos</h1>
        <form method="post" action="processamento/process_matches_x4.php">
            <div class="form-group">
                <label for="round">Escolha a Rodada</label>
                <select class="form-control form-select" id="round" name="round" required>
                    <option value="">Selecione a Rodada</option>
                    <?php for ($i = 1; $i <= 17; $i++) : ?>
                        <option value="<?php echo $i; ?>">Rodada <?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div id="cards-container" style="display:none;">
                <?php for ($i = 1; $i <= 9; $i++) : ?>
                    <div class="card">
                        <div class="card-header">Confronto <?php echo $i; ?></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="team1_<?php echo $i; ?>">Time 1</label>
                                <select class="form-control team-select" id="team1_<?php echo $i; ?>" name="team1_id[]" required onchange="updateCardSelects();">
                                    <option value="">Selecione um time</option>
                                    <?php foreach ($teams as $team) : ?>
                                        <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['team_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="team2_<?php echo $i; ?>">Time 2</label>
                                <select class="form-control team-select" id="team2_<?php echo $i; ?>" name="team2_id[]" required onchange="updateCardSelects();">
                                    <option value="">Selecione um time</option>
                                    <?php foreach ($teams as $team) : ?>
                                        <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['team_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <button type="submit" class="btn btn-success mt-3">Salvar Confrontos</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#round').change(function() {
                const selectedRound = $(this).val();
                if (selectedRound) {
                    $('#cards-container').show();
                    loadMatchData(selectedRound);
                } else {
                    $('#cards-container').hide();
                }
            });
        });
         // Verifica se existe um parâmetro "msg" na URL e se o valor é "success"
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('msg') === 'success') {
        alert('Rodada salva com sucesso!');
    }
    </script>
</body>

</html>
