<?php
include 'db/db.php';

if (isset($_GET['team_id'])) {
    $team_id = intval($_GET['team_id']);

    // Consulta para obter os dados do time e dos membros
    $sql = "SELECT t.team_name, u1.nickname AS member1_nickname, u1.whatsapp AS member1_whatsapp,
                   u2.nickname AS member2_nickname, u3.nickname AS member3_nickname, u4.nickname AS member4_nickname
            FROM teams_x4 t
            LEFT JOIN users u1 ON t.member1 = u1.iduser
            LEFT JOIN users u2 ON t.member2 = u2.iduser
            LEFT JOIN users u3 ON t.member3 = u3.iduser
            LEFT JOIN users u4 ON t.member4 = u4.iduser
            WHERE t.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();


        // Exibir informações do time
        echo "<h1>" . htmlspecialchars($row['team_name'], ENT_QUOTES, 'UTF-8') . "</h1>";

        // Mensagem para WhatsApp
        $whatsapp_message = "Olá, sou do time " . htmlspecialchars($row['team_name'], ENT_QUOTES, 'UTF-8') . ". Podemos agendar nossa partida de X4 do Torneio?";
        $whatsapp_url = "https://wa.me/" . htmlspecialchars($row['member1_whatsapp'], ENT_QUOTES, 'UTF-8') . "?text=" . urlencode($whatsapp_message);

        
        echo "<p><strong>Membros:</strong></p>";
        echo "<p><strong>Capitão:</strong> " . htmlspecialchars($row['member1_nickname'], ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<ul>";
        if (!empty($row['member2_nickname'])) {
            echo "<li>" . htmlspecialchars($row['member2_nickname'], ENT_QUOTES, 'UTF-8') . "</li>";
        }
        if (!empty($row['member3_nickname'])) {
            echo "<li>" . htmlspecialchars($row['member3_nickname'], ENT_QUOTES, 'UTF-8') . "</li>";
        }
        if (!empty($row['member4_nickname'])) {
            echo "<li>" . htmlspecialchars($row['member4_nickname'], ENT_QUOTES, 'UTF-8') . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Time não encontrado.</p>";
    }

    // Botão de agendamento no WhatsApp
    echo "<button class='btn btn-success' onclick=\"window.open('$whatsapp_url', '_blank')\">Agendar</button>";

    $stmt->close();
} else {
    echo "<p>Time não especificado.</p>";
}

$conn->close();
?>
