-- OPCIONAL: execute somente em uma base de testes, após iniciar a API.
-- A conta 1 é o perfil local de demonstração criado ao iniciar o sistema.
INSERT INTO livro (titulo, autor, total_paginas, capa)
VALUES ('Dom Casmurro', 'Machado de Assis', 256, 'img/capas/classica.png');
INSERT INTO leitura (id_usuario, id_livro, status, paginas_lidas, nota, comentario)
VALUES (1, (SELECT MAX(id_livro) FROM livro), 'Lido', 256, 9, 'Narrativa que permite diferentes interpretações.');
INSERT INTO livro (titulo, autor, total_paginas, capa)
VALUES ('Vidas Secas', 'Graciliano Ramos', 176, 'img/capas/botanica.png');
INSERT INTO leitura (id_usuario, id_livro, status, paginas_lidas, nota, comentario)
VALUES (1, (SELECT MAX(id_livro) FROM livro), 'Lendo', 44, NULL, 'Estou acompanhando os capítulos.');
INSERT INTO livro (titulo, autor, total_paginas, capa)
VALUES ('O Cortiço', 'Aluísio Azevedo', 304, 'img/capas/lua.png');
INSERT INTO leitura (id_usuario, id_livro, status, paginas_lidas, nota, comentario)
VALUES (1, (SELECT MAX(id_livro) FROM livro), 'Quero ler', 0, NULL, '');
