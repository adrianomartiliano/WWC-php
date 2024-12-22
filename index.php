<?php

        include 'components/menu.php';
        include 'cardsTorneios/x4.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <style>
                .card-modalidade {
                        width: 332px;
                        height: 106px;
                        background-size: cover;
                        background-position: center;
                        border-radius: 8px;
                        box-shadow: 1px 1px 20px white;
                        margin: 0 auto;
                        position: relative;
                        cursor: pointer;
                        transition: all 0.3s ease; /* Suaviza a transição */
                }

                .card-modalidade.expanded {
                        height: 200px;
                }

                .card-modalidade::before {
                        content: "";
                        display: block;
                        width: 100%;
                        height: 106px;
                        background-image: url('assets/x2.png');
                        background-size: cover;
                        background-position: center;
                        border-radius: 8px 8px 0 0;
                        position: absolute;
                        top: 0;
                        left: 0;
                        z-index: 1; /* Garante que a imagem fique acima do conteúdo */
                }

                .card-modalidade-content {
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        width: 100%;
                        height: 0; 
                        background-color: #323232;
                        color: white;
                        text-align: center;
                        overflow: hidden; /* Esconde o conteúdo até expandir */
                        transition: height 0.3s ease; /* Suaviza a expansão */
                        z-index: 2; /* Fica acima da imagem */
                        border-radius: 0 0 8px 8px;
                }

                .card-modalidade.expanded .card-modalidade-content {
                        height: 94px; /* Altura da área expandida */
                }

                .btns-modalidade-container {
                        margin-top: 30px;
                }

                .btn-modalidade {
                        background-color: #ffab07;
                        color: #215d94;
                        border: none;
                        font-weight: bold;
                        text-decoration: none;
                        padding: 8px 16px;
                        border-radius: 4px;
                        cursor: pointer;
                        margin: 5px;
                        transition: background-color 0.3s ease;
                }

                .btn-modalidade:hover {
                        background-color: #0056b3;
                }
        </style>
</head>
<body>
        <div class="card-modalidade" onclick="toggleCard(this)">
                <div class="card-modalidade-content">
                        <div class="btns-modalidade-container">
                                <a href='#' class='btn-modalidade'>Nível Prata</a>
                                <a href='#' class='btn-modalidade'>Nível Elite</a>
                        </div>
                </div>
        </div>

        <script>
                function toggleCard(card) {
                        card.classList.toggle('expanded');
                }
        </script>
</body>
</html>
