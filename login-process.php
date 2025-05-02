<?php
session_start();
include("connection.php");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST["email"] ?? "";
  $senha = $_POST["senha"] ?? "";

  if (empty($email) || empty($senha)) {
    echo json_encode(["status" => "erro", "mensagem" => "Preencha todos os campos."]);
    exit;
  }

  $query = "SELECT * FROM usuarios WHERE email = ?";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($result)) {
    if (password_verify($senha, $row["senha_hash"])) {
      $_SESSION["usuario_id"] = $row["id"];
      $_SESSION["usuario_nome"] = $row["nome"];
      echo json_encode(["status" => "ok"]);
    } else {
      echo json_encode(["status" => "erro", "mensagem" => "Senha incorreta."]);
    }
  } else {
    echo json_encode(["status" => "erro", "mensagem" => "Usuário não encontrado."]);
  }
} else {
  echo json_encode(["status" => "erro", "mensagem" => "Requisição inválida."]);
}
