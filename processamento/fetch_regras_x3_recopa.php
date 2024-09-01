<?php
    include '../db/db.php';

    if (isset($_POST['cod_grupo'])) {
        $cod_grupo = intval($_POST['cod_grupo']); // Garantir que seja um inteiro

        $sql = "SELECT cod, desc_regra FROM regras_x3_recopa WHERE cod_grupo = ? ORDER BY cod";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            // Logar o erro em vez de exibir para o usuário
            error_log('Erro na preparação: ' . $conn->error);
            echo "<p>Erro ao processar o pedido. Por favor, tente novamente mais tarde.</p>";
            exit;
        }

        $stmt->bind_param("i", $cod_grupo);

        if (!$stmt->execute()) {
            error_log('Erro na execução: ' . $stmt->error);
            echo "<p>Erro ao processar o pedido. Por favor, tente novamente mais tarde.</p>";
            exit;
        }

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<ul class='regras-lista'>";
            while ($row = $result->fetch_assoc()) {
                echo "<li><strong>". htmlspecialchars($row['cod']) ."</strong> - ". htmlspecialchars($row['desc_regra']) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='regras-notfound'>Nenhuma regra encontrada para este grupo.</p>";
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

    .regras-notfound {
        color: #ff0000;
        font-weight: bold;
    }
</style>
