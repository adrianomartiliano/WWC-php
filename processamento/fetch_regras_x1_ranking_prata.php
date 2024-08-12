<?php
    include '../db/db.php';

    if (isset($_POST['cod_grupo'])) {
        $cod_grupo = $_POST['cod_grupo'];

        $sql = "SELECT cod, desc_regra FROM regras_x1_ranking_prata WHERE cod_grupo = ? ORDER BY cod";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cod_grupo);
        $stmt->execute();
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
    }

    .regras-lista li strong {
        color: #0056b3; 
    }

    
    p {
        font-size: 1.1em; 
        color: #ff0000; 
    }
</style>
