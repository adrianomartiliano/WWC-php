<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trilha do Campeonato de Futebol</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
        }

        .trilha {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            max-width: 1200px;
            margin: 0 auto;
        }

        .round {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 40px;
            position: relative;
        }

        .match {
            background-color: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            position: relative;
            width: 150px;
        }

        .team {
            padding: 5px 0;
        }

        .team:first-child {
            border-bottom: 1px solid #ccc;
        }

        .team.winner {
            font-weight: bold;
            color: #28a745;
        }

        /* Linha horizontal entre rounds */
        .round::after {
            content: "";
            position: absolute;
            top: 50%;
            right: -20px;
            width: 40px;
            height: 2px;
            background-color: #ccc;
            z-index: 1;
        }

        /* Linha vertical conectando partidas */
        .match::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 100%;
            width: 2px;
            height: 100%;
            background-color: #ccc;
            z-index: 0;
        }

        .round:last-child::after {
            display: none;
        }

        .match:last-child::after {
            display: none;
        }

        /* Ajustes para telas menores */
        @media (max-width: 768px) {
            .trilha {
                flex-direction: column;
                align-items: center;
            }

            .round {
                margin-right: 0;
                margin-bottom: 40px;
            }

            .round::after {
                width: 2px;
                height: 40px;
                right: 50%;
                top: auto;
                bottom: -20px;
            }

            .match::after {
                width: 100%;
                height: 2px;
                top: auto;
                bottom: -10px;
                left: 0;
            }
        }
    </style>
</head>
<body>

    <h1>Trilha do Campeonato de Futebol</h1>

    <div class="trilha">
        <!-- Round 1 - Quartas de Final -->
        <div class="round">
            <h2>Quartas de Final</h2>
            <div class="match">
                <div class="team">Time A</div>
                <div class="team">Time B</div>
            </div>
            <div class="match">
                <div class="team">Time C</div>
                <div class="team">Time D</div>
            </div>
            <div class="match">
                <div class="team">Time E</div>
                <div class="team">Time F</div>
            </div>
            <div class="match">
                <div class="team">Time G</div>
                <div class="team">Time H</div>
            </div>
        </div>

        <!-- Round 2 - Semifinais -->
        <div class="round">
            <h2>Semifinais</h2>
            <div class="match">
                <div class="team">Vencedor 1</div>
                <div class="team">Vencedor 2</div>
            </div>
            <div class="match">
                <div class="team">Vencedor 3</div>
                <div class="team">Vencedor 4</div>
            </div>
        </div>

        <!-- Final -->
        <div class="round">
            <h2>Final</h2>
            <div class="match">
                <div class="team winner">Vencedor SF1</div>
                <div class="team">Vencedor SF2</div>
            </div>
        </div>
    </div>

</body>
</html>
