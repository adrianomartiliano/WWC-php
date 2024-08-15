<?php
    include '../db/db.php';

    if (isset($_POST['cod_grupo'])) {
        $cod_grupo = $_POST['cod_grupo'];


        $sql = "SELECT cod, desc_regra FROM regras_x4 WHERE cod_grupo = ? ORDER BY cod";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('Erro na preparação: ' . $conn->error);
        }

        $stmt->bind_param("i", $cod_grupo);

        if (!$stmt->execute()) {
            die('Erro na execução: ' . $stmt->error);
        }

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<ul class='regras-lista'>";
            while ($row = $result->fetch_assoc()) {
                echo "<li><strong>". $row['cod'] ."</strong> - ". $row['desc_regra'] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nenhuma regra encontrada para este grupo.</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Parâmetro `cod_grupo` não encontrado.</p>";
    }

    $conn->close();
?>

<style>
    .regras-lista {
        padding-left: 20px;
        margin: 15px 0; 
    }

    .regras-lista li {
        margin-bottom: 10px; 
        font-size: 1.1em; 
        line-height: 1.4em; 
        list-style: none;
    }

    .regras-lista li strong {
        color: #0056b3; 
    }
</style>
