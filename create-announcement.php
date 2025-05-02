<?php
require_once("connection.php");
session_start();

if (!isset($_SESSION["usuario_id"])) {
  http_response_code(401);
  echo json_encode(["status" => "erro", "mensagem" => "Usuário não autenticado."]);
  exit;
}

$usuario_id = $_SESSION["usuario_id"];
$nome = $_POST["nome"] ?? "";
$raca = $_POST["raca"] ?? "";
$cor = $_POST["cor"] ?? "";
$ultima_localizacao = $_POST["ultima_localizacao"] ?? "";
$descricao = $_POST["descricao"] ?? "";
$imagem_url = $_POST["imagem_url"] ?? "";

// Criar pet primeiro
$stmt = $conn->prepare("INSERT INTO pets (nome, raca, cor, descricao, ultima_localizacao, imagem_url) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $nome, $raca, $cor, $descricao, $ultima_localizacao, $imagem_url);
if (!$stmt->execute()) {
  echo json_encode(["status" => "erro", "mensagem" => "Erro ao cadastrar pet."]);
  exit;
}
$pet_id = $stmt->insert_id;
$stmt->close();

// Criar anúncio
$stmt = $conn->prepare("INSERT INTO anuncios (usuario_id, pet_id, ultima_localizacao, descricao, imagem_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $usuario_id, $pet_id, $ultima_localizacao, $descricao, $imagem_url);
if ($stmt->execute()) {
  echo json_encode(["status" => "ok"]);
} else {
  echo json_encode(["status" => "erro", "mensagem" => "Erro ao criar anúncio."]);
}
$stmt->close();
