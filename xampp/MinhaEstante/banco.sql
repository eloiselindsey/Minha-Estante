-- Importar pelo phpMyAdmin (XAMPP) UMA VEZ.
-- Modelo relacional (BD): USUARIO 1:N LEITURA N:1 LIVRO; 3FN.
CREATE DATABASE IF NOT EXISTS minhaestante CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE minhaestante;
CREATE TABLE IF NOT EXISTS usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS livro (
    id_livro INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    total_paginas INT NOT NULL,
    capa VARCHAR(100) DEFAULT ''
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS leitura (
    id_leitura INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_livro INT NOT NULL,
    status VARCHAR(15) NOT NULL DEFAULT 'Quero ler',
    paginas_lidas INT NOT NULL DEFAULT 0,
    nota DECIMAL(3,1) NULL,
    comentario TEXT,
    UNIQUE (id_usuario, id_livro),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_livro) REFERENCES livro(id_livro)
) ENGINE=InnoDB;
INSERT INTO usuario (id_usuario, nome, email) VALUES
    (1, 'Leitor de demonstração', 'demo@minhaestante.local');
