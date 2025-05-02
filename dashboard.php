<?php
// Carrega dados do usuário
require_once("connection.php");
session_start();
$usuario_id = $_SESSION["usuario_id"] ?? null;

$nome = $email = $telefone = $endereco = "";
if ($usuario_id) {
  $stmt = $conn->prepare("SELECT nome, email, telefone  FROM usuarios WHERE id = ?");
  $stmt->bind_param("i", $usuario_id);
  $stmt->execute();
  $stmt->bind_result($nome, $email, $telefone);
  $stmt->fetch();
  $stmt->close();
}

//Conversas
$conversas = [];

if ($usuario_id) {
  $sql = "
    SELECT c.id, u.nome AS nome_destinatario
    FROM conversas c
    JOIN usuarios u ON 
      (u.id = CASE 
                WHEN c.usuario_origem_id = ? THEN c.usuario_destino_id 
                ELSE c.usuario_origem_id 
             END)
    WHERE c.usuario_origem_id = ? OR c.usuario_destino_id = ?
    ORDER BY c.criado_em DESC
  ";
  
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iii", $usuario_id, $usuario_id, $usuario_id);
  $stmt->execute();
  $resultado = $stmt->get_result();

  while ($linha = $resultado->fetch_assoc()) {
    $conversas[] = $linha;
  }

  $stmt->close();
}

//Anuncios
$meus_anuncios = [];

if ($usuario_id) {
  $sql = "
    SELECT a.id, p.nome AS nome_pet, p.raca, p.cor, a.ultima_localizacao, a.descricao, a.imagem_url, a.criado_em
    FROM anuncios a
    JOIN pets p ON a.pet_id = p.id
    WHERE a.usuario_id = ?
    ORDER BY a.criado_em DESC
  ";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $usuario_id);
  $stmt->execute();
  $resultado = $stmt->get_result();

  while ($linha = $resultado->fetch_assoc()) {
    $meus_anuncios[] = $linha;
  }

  $stmt->close();
}


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gerenciamento - PetFinder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="/petfinder/style.css">
  <style>
    .sidebar {
      min-width: 200px;
      background-color: #f1f1f1;
      padding: 20px;
      height: 100%;
    }
    .sidebar .nav-link.active {
      font-weight: bold;
      color: #0d6efd;
    }
  </style>
</head>
<body>

  <!-- Header -->
  
  <header class="bg-primary text-white py-3 mb-4 w-100 fixed-top shadow">
    <div class="container d-flex justify-content-between align-items-center">

      <!-- Título clicável -->
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



  <!-- Main content -->
  <div class="container-fluid mt-5 pt-4">
    <div class="row">
      
      <!-- Sidebar -->
      <nav class="col-md-3 sidebar">
        <ul class="nav flex-column">
          <li class="nav-item mb-2">
            <a class="nav-link active" href="#info" onclick="mostrarSecao('info')">
              <i class="bi bi-person-circle"></i> Informações
            </a>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link" href="#conversas" onclick="mostrarSecao('conversas')">
              <i class="bi bi-chat-left-text"></i> Conversas
            </a>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link" href="#meusAnuncios" onclick="mostrarSecao('meusAnuncios')">
              <i class="bi bi-card-list"></i> Meus Anúncios
            </a>
          </li>
          <li class="nav-item mt-4">
            <a class="nav-link text-success fw-bold" href="#anuncio" onclick="mostrarSecao('anuncio')">
              <i class="bi bi-megaphone-fill"></i> Criar Anúncio
            </a>
          </li>
        </ul>
      </nav>

      <!-- Conteúdo das seções -->
      <div class="col-md-9 p-4">
        <!-- Informações -->
        <section id="info" class="conteudo">
          <h3>Minhas Informações</h3>
          <form id="formInfo">
            <div class="mb-3">
              <label class="form-label">Nome</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($nome) ?>" disabled>
            </div>
            <div class="mb-3">
              <label class="form-label">E-mail</label>
              <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" disabled>
            </div>
            <div class="mb-3">
              <label class="form-label">Telefone</label>
              <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($telefone) ?>">
            </div>
            <!--
            <div class="mb-3">
              <label class="form-label">Endereço</label>
              <input type="text" name="endereco" class="form-control" value="<?= htmlspecialchars($endereco) ?>">
            </div>
            -->
            <button type="button" class="btn btn-primary" onclick="confirmarAlteracao()">Salvar alterações</button>
          </form>
        </section>

        <!-- Conversas -->
        <section id="conversas" class="conteudo d-none">
          <h3>Minhas Conversas</h3>

          <?php if (empty($conversas)): ?>
            <p class="text-muted">Você ainda não tem conversas.</p>
          <?php else: ?>
            <ul class="list-group">
              <?php foreach ($conversas as $conv): ?>
                <li class="list-group-item d-flex justify-content-between">
                  <span><strong><?= htmlspecialchars($conv["nome_destinatario"]) ?>:</strong> Nova conversa iniciada.</span>
                  <button class="btn btn-sm btn-outline-primary" onclick="abrirChat(<?= $conv['id'] ?>, '<?= htmlspecialchars($conv['nome_destinatario']) ?>')">Abrir</button>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>


        <!-- Criar Anúncio -->
        <section id="anuncio" class="conteudo d-none">
          <h3>Criar Anúncio de Pet</h3>
          <form id="formAnuncio">
            <div class="mb-3">
              <label class="form-label">Nome do Pet</label>
              <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Raça</label>
              <input type="text" name="raca" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Cor</label>
              <input type="text" name="cor" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Última Localização</label>
              <input type="text" name="ultima_localizacao" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Descrição</label>
              <textarea name="descricao" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Imagem do Pet (URL)</label>
              <input type="text" name="imagem_url" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Publicar Anúncio</button>
          </form>
        </section>
      </div>
    </div>
  </div>

  <!-- Meus Anúncios -->
  <section id="meusAnuncios" class="conteudo d-none">
    <h3>Meus Anúncios</h3>

    <?php if (empty($meus_anuncios)): ?>
      <p class="text-muted">Você ainda não criou nenhum anúncio.</p>
    <?php else: ?>
      <div class="row">
        <?php foreach ($meus_anuncios as $anuncio): ?>
          <div class="col-md-4 mb-4">
            <div class="card h-100">
              <div class="img-container">
                <img src="<?= htmlspecialchars($anuncio['imagem_url']) ?>" class="card-img-top" alt="Imagem do pet">
              </div>
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($anuncio['nome_pet']) ?></h5>
                <p class="card-text text-muted"><strong>Última localização:</strong> <?= htmlspecialchars($anuncio['ultima_localizacao']) ?></p>
                <p class="card-text"><?= htmlspecialchars($anuncio['descricao']) ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>


  <!-- Footer -->
  <footer class="bg-success text-center text-muted py-3 fixed-bottom w-100 z-3">
    <small class="text-white">© 2025 PetFinder. Todos os direitos reservados.</small>
  </footer>

  <!-- Modal de confirmação -->
  <div class="modal fade" id="confirmarSenhaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form id="formConfirmarSenha" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmação de Senha</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Para salvar as alterações, insira sua senha:</p>
          <input type="password" name="senha" class="form-control" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Confirmar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal de Chat -->
  <div class="modal fade" id="modalChat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Conversando com <span id="chatNome"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="chatMensagens" style="max-height: 400px; overflow-y: auto;">
          <!-- Mensagens serão carregadas aqui -->
        </div>
        <div class="modal-footer">
          <form id="formEnviarMensagem" class="d-flex w-100 gap-2">
            <input type="hidden" name="conversa_id" id="chatConversaId">
            <input type="text" name="mensagem" class="form-control" placeholder="Digite sua mensagem..." required>
            <button class="btn btn-primary" type="submit">Enviar</button>
          </form>
        </div>
      </div>
    </div>
  </div>


  <script>
    function mostrarSecao(secao) {
      document.querySelectorAll('.conteudo').forEach(el => el.classList.add('d-none'));
      document.getElementById(secao).classList.remove('d-none');

      document.querySelectorAll('.sidebar .nav-link').forEach(link => link.classList.remove('active'));
      document.querySelector('.sidebar .nav-link[href="#' + secao + '"]').classList.add('active');
    }
  </script>

  <script>
    let formInfo = document.getElementById("formInfo");

    function confirmarAlteracao() {
      new bootstrap.Modal(document.getElementById('confirmarSenhaModal')).show();
    }

    document.getElementById("formConfirmarSenha").addEventListener("submit", function(e) {
      e.preventDefault();

      const dadosForm = new FormData(formInfo);
      dadosForm.append("senha", this.senha.value);

      fetch("update-user.php", {
        method: "POST",
        body: dadosForm
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
          alert("Dados atualizados com sucesso!");
          document.getElementById('confirmarSenhaModal').querySelector('button.btn-close').click();
        } else {
          alert(data.mensagem || "Erro ao atualizar.");
        }
      })
      .catch(() => alert("Erro na requisição."));
    });
  </script>

  <script>
  function abrirChat(conversaId, nome) {
    let modalChat = null;

    document.getElementById("chatNome").innerText = nome;
    document.getElementById("chatConversaId").value = conversaId;

    fetch("load-messages.php?conversa_id=" + conversaId)
      .then(res => res.json())
      .then(mensagens => {
        const container = document.getElementById("chatMensagens");
        container.innerHTML = "";

        mensagens.forEach(msg => {
          const alinhamento = msg.eh_usuario_logado ? "text-end" : "text-start";
          const cor = msg.eh_usuario_logado ? "bg-primary text-white" : "bg-light";
          container.innerHTML += `
            <div class="mb-2 ${alinhamento}">
              <span class="d-inline-block p-2 rounded ${cor}" style="max-width: 75%;">
                ${msg.texto}
              </span>
            </div>
          `;
        });

        if (!modalChat) {
          modalChat = new bootstrap.Modal(document.getElementById('modalChat'));
        }
        modalChat.show();

      });
  }

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

  document.getElementById('modalChat').addEventListener('hidden.bs.modal', () => {
  document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
  document.body.classList.remove('modal-open');
  document.body.style.paddingRight = '';
});


  </script>

  <script>
    document.getElementById("formAnuncio").addEventListener("submit", function(e) {
      e.preventDefault();

      const dados = new FormData(this);

      fetch("create-announcement.php", {
        method: "POST",
        body: dados
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
          alert("Anúncio criado com sucesso!");
          this.reset();
        } else {
          alert(data.mensagem || "Erro ao criar anúncio.");
        }
      })
      .catch(() => alert("Erro na requisição."));
    });
  </script>


</body>
</html>
