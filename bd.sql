CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  telefone VARCHAR(20),
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100),
  raca VARCHAR(100),
  cor VARCHAR(50),
  descricao VARCHAR(100) NOT NULL,
  ultima_localizacao VARCHAR(100),
  imagem_url VARCHAR(100)
);

CREATE TABLE anuncios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  pet_id INT NOT NULL,
  ultima_localizacao VARCHAR(255),
  descricao TEXT,
  tipo VARCHAR(255),
  imagem_url VARCHAR(255),
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE
);

CREATE TABLE conversas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_origem_id INT NOT NULL,
  usuario_destino_id INT NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_origem_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_destino_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE mensagens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  conversa_id INT NOT NULL,
  usuario_id INT NOT NULL,
  texto TEXT NOT NULL,
  enviada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conversa_id) REFERENCES conversas(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

INSERT INTO usuarios (nome, email, senha_hash, telefone) VALUES
('Maria Silva', 'maria@example.com', 'hash123', '11999998888'),
('João Souza', 'joao@example.com', 'hash456', '11888887777');

INSERT INTO pets (nome, raca, cor, descricao, ultima_localizacao, imagem_url)
VALUES
  ('Bolinha', 'Border Collie', 'Preto e Branco', 'Cachorro lindo', 'Rua das Flores, 123 - Bairro Jardim.', '/petfinder/images/bc.jpg'),
  ('Rex', 'Golden', 'Dourado', 'Cachorro feio', 'Av. Central, 456 - Bairro Lagoa.', '/petfinder/images/golden.jpg'),
  ('Mimi', 'Akita', 'Branco', 'Dog', 'Praça dos Sonhos, 789.', '/petfinder/images/akita.jpg');

INSERT INTO anuncios (usuario_id, pet_id, ultima_localizacao, descricao, tipo, imagem_url) VALUES
(1, 1, 'Rua das Flores, 123 - Bairro Jardim', 'Raça: Border Collie | Cor: Preto e Branco | Última vez visto em: Rua das Flores, 123 - Bairro Jardim.', 'Encontrado', '/petfinder/images/bc.jpg'),
(2, 2, 'Av. Central, 456 - Bairro Lagoa', 'Raça: Golden | Cor: Dourado | Última vez visto em: Av. Central, 456 - Bairro Lagoa.', 'Desaparecido' '/petfinder/images/golden.jpg'),
(1, 3, 'Praça dos Sonhos, 789', 'Raça: Akita | Cor: Branco | Última vez visto em: Praça dos Sonhos, 789.', 'Encontrado', '/petfinder//images/akita.jpg');

INSERT INTO conversas (usuario_origem_id, usuario_destino_id) VALUES
(1, 2);

INSERT INTO mensagens (conversa_id, usuario_id, texto) VALUES
(1, 1, 'Olá, você viu meu cachorro recentemente?'),
(1, 2, 'Oi, acho que vi sim! Onde ele sumiu?');