<?php
session_start();
include("connection.php");

$usuario_id = $_SESSION["usuario_id"] ?? null;
$conversa_id = $_GET["conversa_id"] ?? 0;

$resposta = [];
if ($usuario_id && $conversa_id) {
  $stmt = $conn->prepare("
    SELECT m.texto, m.usuario_id
    FROM mensagens m
    WHERE m.conversa_id = ?
    ORDER BY m.enviada_em ASC
  ");
  $stmt->bind_param("i", $conversa_id);
  $stmt->execute();
  $resultado = $stmt->get_result();

  while ($msg = $resultado->fetch_assoc()) {
    $resposta[] = [
      "texto" => htmlspecialchars($msg["texto"]),
      "eh_usuario_logado" => $msg["usuario_id"] == $usuario_id
    ];
  }
}

header("Content-Type: application/json");
echo json_encode($resposta);
