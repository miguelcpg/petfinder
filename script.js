function chamarAnunciante() {
    alert("Abrindo WhatsApp para falar com o anunciante...");
    // Para abrir WhatsApp real:
    window.open("https://wa.me/5517996745361", "_blank");
  }
  
  function revelarLocalizacao() {
    const card = document.getElementById('cardInner');
    card.classList.toggle('flipped');
  }
  
  function gerarLink() {
    const link = window.location.href + "?pet=12345"; // Exemplo de link
    navigator.clipboard.writeText(link).then(function() {
      alert("Link copiado: " + link);
    }, function(err) {
      alert("Erro ao copiar o link");
    });
  }

  // Dados de exemplo de pets
const pets = [
  {
    imagem: "/images/bc.jpg",
    nome: "Bolinha",
    descricao: "Raça: Border Collie | Cor: Preto e Branco | Última vez visto em: Rua das Flores, 123 - Bairro Jardim."
  },
  {
    imagem: "/images/golden.jpg",
    nome: "Rex",
    descricao: "Raça: Golden | Cor: Dourado | Última vez visto em: Av. Central, 456 - Bairro Lagoa."
  },
  {
    imagem: "/images/akita.jpg",
    nome: "Mimi",
    descricao: "Raça: Akita | Cor: Branco | Última vez visto em: Praça dos Sonhos, 789."
  }
];

let petAtual = 0;

function proximoPet() {
  petAtual = (petAtual + 1) % pets.length; // Volta para o início ao final
  atualizarPet();
}

function anteriorPet() {
  petAtual = (petAtual - 1) % pets.length; // Volta para o início ao final
  atualizarPet();
}

function atualizarPet() {
  const pet = pets[petAtual];
  document.getElementById("petImage").src = pet.imagem;
  document.getElementById("petName").textContent = "Nome: " + pet.nome;
  document.getElementById("petDescription").textContent = pet.descricao;
}
