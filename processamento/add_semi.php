<?php
include '../db/db.php';

// Função para processar uploads de imagens
function processarUpload($inputName) {
    // Verificar se o arquivo foi realmente enviado
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == 0) {
        $targetDir = "../uploads/";
        $targetFile = $targetDir . basename($_FILES[$inputName]["name"]);

        // Verificar se o arquivo é uma imagem real
        $check = getimagesize($_FILES[$inputName]["tmp_name"]);
        if ($check !== false) {
            // Verifica se o upload foi bem-sucedido
            if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $targetFile)) {
                return basename($_FILES[$inputName]["name"]);  // Retorna o nome da imagem
            } else {
                echo "Houve um erro ao fazer o upload da imagem.";
            }
        } else {
            echo "Arquivo não é uma imagem válida.";
        }
    }
    return null;  // Se não houver imagem, retorna nulo
}

// Processar cada quartas de final
for ($i = 1; $i <= 2; $i++) {
    $equipe1 = $_POST["equipe1_$i"];
    $equipe2 = $_POST["equipe2_$i"];
    $placar1 = $_POST["placar1_$i"];
    $placar2 = $_POST["placar2_$i"];

    // Processar uploads de imagens
    $img1 = processarUpload("img1_$i");
    $img2 = processarUpload("img2_$i");
    $img3 = processarUpload("img3_$i");

    // Atualizar o banco de dados
    $query = "UPDATE s" . $i . "_copaelite SET equipe1='$equipe1', equipe2='$equipe2', placar1='$placar1', placar2='$placar2'";
    
    // Adicionar imagens ao banco de dados se existirem
    if ($img1) {
        $query .= ", batalha1_img='$img1'";
    }
    if ($img2) {
        $query .= ", batalha2_img='$img2'";
    }
    if ($img3) {
        $query .= ", batalha3_img='$img3'";
    }

    $query .= " WHERE id=1";
    $conn->query($query);
}

header("Location: ../index.php");
?>
