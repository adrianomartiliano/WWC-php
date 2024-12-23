<?php require 'components/menu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    #form-cadastro{
        margin: 30% auto;
        max-width: 400px;
        border: 1px solid gray;
        padding: 30px;
        background-color: #ffffff;
        color: #1FB6FF;
        box-shadow: 0 0 18px rgba(0, 0, 0, 0.8);
    }
    .btn-login{
        color: white !important;
        background-color: #1FB6FF !important; 
    }
</style>
<body class="bg-1">
    <div>
        <?php
        // Incluir o arquivo de conexão com o banco de dados
        require_once 'db/db.php';

        // Consultar a tabela 'cla'
        $query = "SELECT idcla, siglacla FROM cla";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            // Array para armazenar os dados dos clãs
            $clas = [];
            while ($row = $result->fetch_assoc()) {
                $clas[] = $row;
            }
        } else {
            $clas = [];
        }

        $conn->close();
        ?>
        
        <form id="form-cadastro" action='processamento/process_cadastro.php' method='post'>
            <h1>Formulário de Cadastro</h1>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="inputNickname" name='nickname' required>
                <label for="inputNickname">Nickname</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="inputId" name='iduser' required>
                <label for="inputId">Id no jogo</label>
            </div>
            <div class="form-floating mb-3">
                <select class="form-select" id="claSelect" name="cla" required>
                    <option selected disabled>Selecione um clã</option>
                    <?php foreach ($clas as $cla): ?>
                        <option value="<?= $cla['idcla']; ?>"><?= $cla['siglacla']; ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="claSelect">Clã</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="inputWhatsApp" name="whatsapp" value="+55" required>
                <label for="inputWhatsApp">WhatsApp</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="inputPassword" name="password" required>
                <label for="inputPassword">Senha</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="inputConfirmPassword" name='confirm_password' required>
                <label for="inputConfirmPassword">Confirme a senha</label>
                <div id="passwordError" class="text-danger mt-2" style="display:none;">As senhas não coincidem.</div>
            </div>
            
            <input class='btn btn-login' type='submit' value='Enviar' />
        </form>

    </div>
    
    <script>
        document.getElementById('cadastroForm').addEventListener('submit', function(event) {
            var password = document.getElementById('inputPassword').value;
            var confirmPassword = document.getElementById('inputConfirmPassword').value;
            var passwordError = document.getElementById('passwordError');

            if (password !== confirmPassword) {
                passwordError.style.display = 'block'; // Mostra a mensagem de erro
                event.preventDefault(); // Impede o envio do formulário
            } else {
                passwordError.style.display = 'none'; // Esconde a mensagem de erro
            }
        });
    </script>
</body>
</html>
