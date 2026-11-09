-- 1. INNER JOIN: estante completa do usuário 1.
SELECT u.nome, l.titulo, le.status, le.paginas_lidas
FROM usuario AS u
INNER JOIN leitura AS le ON u.id_usuario = le.id_usuario
INNER JOIN livro AS l ON le.id_livro = l.id_livro
WHERE u.id_usuario = 1 ORDER BY l.titulo ASC;

-- 2. LEFT JOIN: usuários que ainda não cadastraram leituras.
SELECT u.id_usuario, u.nome FROM usuario AS u
LEFT JOIN leitura AS le ON u.id_usuario = le.id_usuario
WHERE le.id_leitura IS NULL;

-- 3. WHERE + ORDER BY: livros em andamento.
SELECT l.titulo, le.paginas_lidas FROM livro AS l
INNER JOIN leitura AS le ON le.id_livro = l.id_livro
WHERE le.status = 'Lendo' ORDER BY l.titulo;

-- 4. AVG: média das avaliações realmente cadastradas (NULL não vira zero).
SELECT AVG(nota) AS media_das_notas FROM leitura WHERE nota IS NOT NULL;

-- 5. GROUP BY: quantidade de livros por status.
SELECT status, COUNT(*) AS quantidade FROM leitura GROUP BY status;

-- 6. Subconsulta IN: livros que receberam nota 9 ou maior.
SELECT titulo FROM livro WHERE id_livro IN
(SELECT id_livro FROM leitura WHERE nota >= 9);

-- 7. EXISTS: usuários com pelo menos uma leitura em andamento.
SELECT u.nome FROM usuario AS u WHERE EXISTS
(SELECT 1 FROM leitura AS le WHERE le.id_usuario = u.id_usuario AND le.status = 'Lendo');

-- 8. Paginação estável: segunda página, 5 livros por página.
SELECT id_livro, titulo FROM livro ORDER BY titulo ASC, id_livro ASC LIMIT 5 OFFSET 5;
