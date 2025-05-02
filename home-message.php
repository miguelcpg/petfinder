<?php
session_start();
require_once "connection.php";

$data = json_decode(file_get_contents("php://input"), true);
$usuario_logado = $_SESSION["usuario_id"];
$usuario_alvo = $data["usuario_id"];

$sql = "SELECT id FROM conversas 
        WHERE (usuario1_id = ? AND usuario2_id = ?) OR (usuario1_id = ? AND usuario2_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $usuario_logado, $usuario_alvo, $usuario_alvo, $usuario_logado);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $conversa_id = $row["id"];
} else {
  $stmt = $conn->prepare("INSERT INTO conversas (usuario1_id, usuario2_id) VALUES (?, ?)");
  $stmt->bind_param("ii", $usuario_logado, $usuario_alvo);
  $stmt->execute();
  $conversa_id = $stmt->insert_id;
}

echo json_encode(["conversa_id" => $conversa_id]);
