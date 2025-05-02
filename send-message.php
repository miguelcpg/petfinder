<?php
session_start();
include("connection.php");

$usuario_id = $_SESSION["usuario_id"] ?? null;
$conversa_id = $_POST["conversa_id"] ?? null;
$mensagem = trim($_POST["mensagem"] ?? "");

if ($usuario_id && $conversa_id && $mensagem !== "") {
  $stmt = $conn->prepare("INSERT INTO mensagens (conversa_id, usuario_id, texto) VALUES (?, ?, ?)");
  $stmt->bind_param("iis", $conversa_id, $usuario_id, $mensagem);
  $stmt->execute();

  echo json_encode(["status" => "ok"]);
} else {
  echo json_encode(["status" => "erro"]);
}
