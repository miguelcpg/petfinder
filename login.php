<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cadastro - PetFinder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Header -->
  <?php session_start(); ?>
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
  <main class="container flex-grow-1 d-flex flex-column align-items-center justify-content-center">

    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
      <h3 class="mb-4 text-center">Logue-se</h3>

      <form id="formLogin">
        <div class="mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input type="email" name="email" class="form-control" id="email" required>
        </div>

        <div class="mb-3">
          <label for="senha" class="form-label">Senha</label>
          <input type="password" class="form-control" name="senha" id="senha" required>
        </div>

        Ainda não possui um login?<a href="/petfinder/cadastro.php"> Cadastre-se</a>
        <a href="/petfinder/dashboard.php">teste</a>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
      </form>
    </div>

  </main>

  <!-- Footer -->
  <footer class="bg-success text-center text-muted py-3 mt-4 w-100">
    <small class="text-white">© 2025 PetFinder. Todos os direitos reservados.</small>
  </footer>

  <script>
    document.getElementById("formLogin").addEventListener("submit", function(event) {
      event.preventDefault();

      const formData = new FormData(this);

      fetch("login-process.php", {
        method: "POST",
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === "ok") {
          window.location.href = "/petfinder/dashboard.php"; // redireciona após login
        } else {
          alert(data.mensagem); // exibe erro (você pode substituir por um alerta bonito)
        }
      })
      .catch(error => {
        console.error("Erro no login:", error);
        alert("Erro ao tentar login.");
      });
    });
</script>


</body>
</html>
