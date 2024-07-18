<?php
// Iniciar a sessão
session_start();

// Incluir o arquivo de conexão com o banco de dados
require_once '../db/db.php';

// Capturar dados do formulário
$nickname = $_POST['nickname'];
$iduser = $_POST['iduser'];
$cla = $_POST['cla'];
$whatsapp = $_POST['whatsapp'];
$password = $_POST['password'];

// Verificar se todos os campos foram preenchidos
if (empty($nickname) || empty($iduser) || empty($cla) || empty($password) || empty($whatsapp)) {
    die("Por favor, preencha todos os campos.");
}

// Verificar se o ID do usuário já existe
$stmt = $conn->prepare("SELECT iduser FROM users WHERE iduser = ?");
$stmt->bind_param("i", $iduser);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("Este ID já está cadastrado.");
}

$stmt->close();

// Encriptografar a senha
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Inserir os dados na tabela users
$stmt = $conn->prepare("INSERT INTO users (nickname, iduser, cla_id, password, whatsapp) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sisss", $nickname, $iduser, $cla, $hashed_password, $whatsapp);

if ($stmt->execute()) {
    $message = "Cadastro realizado com sucesso!";
    echo "<script>alert('$message'); window.location.href = '../login.php';</script>";
    exit();
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
