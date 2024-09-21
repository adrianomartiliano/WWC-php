<?php
include '../db/db.php'; 

if (isset($_POST['team_id'])) {
    $teamId = $_POST['team_id'];

    $sqlTeam = "SELECT member1, member2, member3, member4 FROM teams_x4 WHERE id = ?";
    $stmt = $conn->prepare($sqlTeam);
    $stmt->bind_param('i', $teamId);
    $stmt->execute();
    $result = $stmt->get_result();
    $team = $result->fetch_assoc();

    if ($team) {
        $memberIds = [$team['member1'], $team['member2'], $team['member3'], $team['member4']];

        $placeholders = implode(',', array_fill(0, count($memberIds), '?'));
        $sqlMembers = "SELECT iduser, nickname FROM users WHERE iduser IN ($placeholders)";
        $stmtMembers = $conn->prepare($sqlMembers);

        $stmtMembers->bind_param(str_repeat('i', count($memberIds)), ...$memberIds);
        $stmtMembers->execute();
        $resultMembers = $stmtMembers->get_result();

        if ($resultMembers->num_rows > 0) {
            echo "<ul class='list-group'>";
            while ($member = $resultMembers->fetch_assoc()) {
                echo "<li class='list-group-item'>{$member['nickname']} - {$member['iduser']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nenhum membro encontrado.</p>";
        }

        $stmtMembers->close();
    } else {
        echo "<p>Equipe não encontrada.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
