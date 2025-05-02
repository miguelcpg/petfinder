<?php
session_start();
require_once "connection.php";

$conversa_id = $_GET["conversa_id"];
$usuario_logado = $_SESSION["usuario_id"];

$sql = "SELECT remetente_id, texto FROM mensagens WHERE conversa_id = ? ORDER BY criado_em ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $conversa_id);
$stmt->execute();
$result = $stmt->get_result();

$msgs = [];
while ($row = $result->fetch_assoc()) {
  $msgs[] = [
    "texto" => $row["texto"],
    "eu" => $row["remetente_id"] == $usuario_logado
  ];
}
echo json_encode($msgs);
