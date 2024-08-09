<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../db/db.php';

    $user_id = $_POST['iduser'];
    $new_password = $_POST['new_password'];

    // Hash da nova senha
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Atualizar a senha no banco de dados
    $sql = "UPDATE users SET password = ? WHERE iduser = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashed_password, $user_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>
