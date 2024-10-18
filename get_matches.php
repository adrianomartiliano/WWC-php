<?php
include 'db/db.php';

if (isset($_GET['round'])) {
    $round = intval($_GET['round']);

    $sql = "SELECT team1_id, team2_id FROM matches_x4 WHERE round = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $round);
    $stmt->execute();
    $result = $stmt->get_result();

    $matches = [];
    while ($row = $result->fetch_assoc()) {
        $matches[] = $row;
    }

    echo json_encode($matches);
    $stmt->close();
}

$conn->close();
?>
