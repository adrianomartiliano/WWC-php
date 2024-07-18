<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wwc";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

if($conn){
    echo 'Conexão realizada com sucesso!';
}

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
