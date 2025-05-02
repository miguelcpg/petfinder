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
    imagem: "/petfinder/images/bc.jpg",
    nome: "Bolinha",
    anunciante: "Miguel",
    tipo: "Encontrado",
    raca: "Border Collie",
    cor: "Preto e Branco",
    ultimaloc: "Rua das Flores, 123 - Bairro Jardim"
  },
  {
    imagem: "/petfinder/images/golden.jpg",
    nome: "Rex",
    anunciante: "Pedro",
    tipo: "Desaparecido",
    raca: "Golden",
    cor: "Dourado",
    ultimaloc: "Rua Central, 456 - Bairro Lagoa."
  },
  {
    imagem: "/petfinder/images/akita.jpg",
    nome: "Mimi",
    anunciante: "Thiago",
    tipo: "Encontrado",
    raca: "Akita",
    cor: "Branco",
    ultimaloc: "Praça dos Sonhos, 789."
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
  document.getElementById("anunciante").textContent = "Anunciante: " + pet.anunciante;
  document.getElementById("tipo").textContent = "Tipo: " + pet.tipo;
  document.getElementById("raca").textContent = "Raça: " + pet.raca;
  document.getElementById("cor").textContent = "Cor: " + pet.cor;
  document.getElementById("ultimaloc").textContent = "Última vez visto em: " + pet.ultimaloc;
}
