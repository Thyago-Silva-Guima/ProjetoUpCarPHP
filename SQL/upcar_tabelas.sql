
CREATE DATABASE upcar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE upcar;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(11) UNIQUE NOT NULL,
    data_nascimento DATE NOT NULL,
    tipo_usuario ENUM('passageiro', 'motorista') NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE caronas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motorista_id INT NOT NULL,
    origem VARCHAR(100) NOT NULL,
    destino VARCHAR(100) NOT NULL,
    data_hora DATETIME NOT NULL,
    vagas_totais INT NOT NULL CHECK (vagas_totais >= 1),
    vagas_disponiveis INT NOT NULL,
    FOREIGN KEY (motorista_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carona_id INT NOT NULL,
    passageiro_id INT NOT NULL,
    status ENUM('solicitado', 'aceito', 'recusado') DEFAULT 'solicitado',
    FOREIGN KEY (carona_id) REFERENCES caronas(id) ON DELETE CASCADE,
    FOREIGN KEY (passageiro_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


CREATE TABLE relatorios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    denunciante_id INT NOT NULL,
    denunciado_id INT NOT NULL,
    motivo TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (denunciante_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (denunciado_id) REFERENCES usuarios(id) ON DELETE CASCADE
);