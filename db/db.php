<?php
$servername = "auth-db1186.hstgr.io";
$username = "u516367395_teste";
$password = "9Vk;eFVRn";
$dbname = "u516367395_teste";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
