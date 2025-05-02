<?php
session_start();
header("Content-Type: application/json");
include("connection.php");

$usuario_id = $_SESSION["usuario_id"] ?? null;

if (!$usuario_id || $_SERVER["REQUEST_METHOD"] !== "POST") {
  echo json_encode(["status" => "erro", "mensagem" => "Requisição inválida."]);
  exit;
}

$telefone = $_POST["telefone"] ?? "";
$senha = $_POST["senha"] ?? "";

if (!$telefone || !$senha) {
  echo json_encode(["status" => "erro", "mensagem" => "Telefone e senha são obrigatórios."]);
  exit;
}

$stmt = $conn->prepare("SELECT senha_hash FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($senha_hash);
$stmt->fetch();
$stmt->close();

if (!password_verify($senha, $senha_hash)) {
  echo json_encode(["status" => "erro", "mensagem" => "Senha incorreta."]);
  exit;
}

$stmt = $conn->prepare("UPDATE usuarios SET telefone = ? WHERE id = ?");
$stmt->bind_param("si", $telefone, $usuario_id);
if ($stmt->execute()) {
  echo json_encode(["status" => "ok"]);
} else {
  echo json_encode(["status" => "erro", "mensagem" => "Erro ao atualizar."]);
}
$stmt->close();
