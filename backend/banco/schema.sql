-- Modelo físico SQLite: USUARIO 1:N LEITURA N:1 LIVRO.
CREATE TABLE IF NOT EXISTS usuario (
    id_usuario INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS livro (
    id_livro INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo TEXT NOT NULL,
    autor TEXT NOT NULL,
    total_paginas INTEGER NOT NULL CHECK (total_paginas > 0),
    capa TEXT DEFAULT ''
);

CREATE TABLE IF NOT EXISTS leitura (
    id_leitura INTEGER PRIMARY KEY AUTOINCREMENT,
    id_usuario INTEGER NOT NULL,
    id_livro INTEGER NOT NULL,
    status TEXT NOT NULL DEFAULT 'Quero ler'
        CHECK (status IN ('Quero ler', 'Lendo', 'Lido')),
    paginas_lidas INTEGER NOT NULL DEFAULT 0 CHECK (paginas_lidas >= 0),
    nota REAL CHECK (nota IS NULL OR (nota >= 0 AND nota <= 10)),
    comentario TEXT DEFAULT '',
    UNIQUE (id_usuario, id_livro),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_livro) REFERENCES livro(id_livro)
);
