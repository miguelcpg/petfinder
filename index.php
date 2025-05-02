<?php
session_start();
require_once("connection.php"); // Arquivo de conexão com o banco de dados

// Consultar os anúncios com nome do pet
$sql = "
  SELECT 
    a.id AS anuncio_id,
    a.pet_id,
    a.ultima_localizacao,
    a.descricao,
    a.imagem_url,
    a.tipo,
    u.nome AS nome_usuario,
    u.id AS usuario_id,
    p.nome AS nome_pet,
    p.raca,
    p.cor
  FROM anuncios a
  JOIN usuarios u ON a.usuario_id = u.id
  JOIN pets p ON a.pet_id = p.id
  ORDER BY a.criado_em DESC
";

$resultado = $conn->query($sql);
$anuncios = [];

while ($linha = $resultado->fetch_assoc()) {
  $anuncios[] = $linha;
}

$_SESSION['anuncios'] = $anuncios;

$indice_atual = $_SESSION['indice_atual'] ?? 0;

if (isset($_GET['navegar'])) {
  if ($_GET['navegar'] === 'anterior') {
    $indice_atual = ($indice_atual > 0) ? $indice_atual - 1 : count($anuncios) - 1;
  } elseif ($_GET['navegar'] === 'proximo') {
    $indice_atual = ($indice_atual < count($anuncios) - 1) ? $indice_atual + 1 : 0;
  }
  $_SESSION['indice_atual'] = $indice_atual;
}

$anuncio_atual = $anuncios[$indice_atual];
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PetFinder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="style.css">
</head>
<body>
    
  <!-- Header -->
  <header class="bg-primary text-white py-3 mb-4 w-100 fixed-top shadow">
    <div class="container d-flex justify-content-between align-items-center">
      <h1 class="m-0">
        <a href="/petfinder/index.php" class="text-white text-decoration-none">🐾 PetFinder</a>
      </h1>

      <?php if (isset($_SESSION["usuario_nome"])): ?>
        <div class="dropdown">
          <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i>
            <?= htmlspecialchars($_SESSION["usuario_nome"]) ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="/petfinder/dashboard.php">Painel do Usuário</a></li>
            <li><a class="dropdown-item" href="#">Configurações</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="/petfinder/logout.php">Sair</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="/petfinder/login.php" class="btn btn-outline-light d-flex align-items-center gap-2">
          <i class="bi bi-box-arrow-in-right"></i> Entrar
        </a>
      <?php endif; ?>
    </div>
  </header>

  <main class="flex-grow-1 d-flex mt-5 pt-5 flex-column align-items-center justify-content-center">
  <div class="container mb-4">
  <div class="row justify-content-center align-items-center">

    <!-- Lado esquerdo: Anunciante -->
    <div class="col-md-4 text-center mb-4 mb-md-0 d-flex justify-content-center">
      <div class="bg-light border rounded p-4 shadow w-100">
        <h5 class="text-secondary mb-2">Anunciante</h5>
        <h4 class="text-primary fw-bold"><?= htmlspecialchars($anuncio_atual['nome_usuario']) ?></h4>
        <button 
          class="btn btn-success btn-lg mt-3 w-100" 
          data-bs-toggle="modal" 
          data-bs-target="#modalConversa" 
          data-usuario-id="<?= $anuncio_atual['usuario_id'] ?>">
          <i class="bi bi-chat-dots-fill me-2"></i> Conversar
        </button>
      </div>
    </div>

    <!-- Imagem centralizada -->
    <div class="col-md-6 d-flex justify-content-center">
      <div class="card-container">
        <div class="card-inner" id="cardInner">
          <div class="card-front">
            <img src="<?= htmlspecialchars($anuncio_atual['imagem_url']) ?>" alt="Pet Desaparecido" id="petImage" class="img-fluid rounded shadow">
          </div>
        </div>
      </div>
    </div>

  </div>
</div>



    <div class="description text-center px-3 mb-4">
      <h5 id="petName">Nome: <?= htmlspecialchars($anuncio_atual['nome_pet']) ?></h5>
      <p id="petDescription">
        <span id="anunciante"><?= htmlspecialchars($anuncio_atual['nome_usuario']) ?></span><br>
        <span id="tipo"><?= htmlspecialchars($anuncio_atual['tipo']) ?></span><br>
        <span id="raca"><?= htmlspecialchars($anuncio_atual['raca']) ?></span><br>
        <span id="cor"><?= htmlspecialchars($anuncio_atual['cor']) ?></span><br>
        <span id="ultimaloc"><?= htmlspecialchars($anuncio_atual['ultima_localizacao']) ?></span>
      </p>
    </div>

    <div class="d-flex gap-3 mb-4">
      <a href="?navegar=anterior" class="btn btn-warning d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left-circle-fill"></i> Anterior
      </a>
      <a href="?navegar=proximo" class="btn btn-warning d-flex align-items-center gap-2">
        <i class="bi bi-arrow-right-circle-fill"></i> Próximo
      </a>        
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-success text-center text-muted py-3 mt-4 w-100">
    <small class="text-white">© 2025 PetFinder. Todos os direitos reservados.</small>
  </footer>

  <!-- Modal de Conversa -->
  <div class="modal fade" id="modalConversa" tabindex="-1" aria-labelledby="modalConversaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="enviar_mensagem.php" method="post">
          <div class="modal-header">
            <h5 class="modal-title" id="modalConversaLabel">Nova conversa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="destinatario_id" id="destinatarioId">
            <div class="mb-3">
              <label for="mensagem" class="form-label">Mensagem</label>
              <textarea class="form-control" name="mensagem" id="mensagem" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Enviar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const modalConversa = document.getElementById('modalConversa');
    modalConversa.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const usuarioId = button.getAttribute('data-usuario-id');
      const input = modalConversa.querySelector('#destinatarioId');
      input.value = usuarioId;
    });

    document.getElementById("formEnviarMensagem").addEventListener("submit", function(e) {
    e.preventDefault();

    const dados = new FormData(this);
    fetch("send-message.php", {
      method: "POST",
      body: dados
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "ok") {
        abrirChat(dados.get("conversa_id"), document.getElementById("chatNome").innerText);
        this.mensagem.value = "";
      } else {
        alert("Erro ao enviar mensagem");
      }
    });
    });

  </script>


  <script src="/petfinder/script.js"></script>
</body>
</html>
