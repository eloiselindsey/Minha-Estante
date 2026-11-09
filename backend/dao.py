"""DAO: concentra o SQL, como nos capítulos 2 e 5 da apostila POO."""
import sqlite3
from models import Livro, Leitura


class BibliotecaDAO:
    def __init__(self, caminho_banco="banco/minhaestante.db"):
        self.caminho_banco = caminho_banco

    def inicializar_banco(self):
        # O script SQL é o mesmo documento usado no modelo físico.
        with open("banco/schema.sql", "r", encoding="utf-8") as arquivo:
            instrucoes = arquivo.read()
        with sqlite3.connect(self.caminho_banco) as conexao:
            cursor = conexao.cursor()
            for comando in instrucoes.split(";"):
                if comando.strip():
                    cursor.execute(comando)
            cursor.execute("SELECT id_usuario FROM usuario WHERE id_usuario = ?", (1,))
            if cursor.fetchone() is None:
                cursor.execute(
                    "INSERT INTO usuario (id_usuario, nome, email) VALUES (?, ?, ?)",
                    (1, "Leitor de demonstração", "demo@minhaestante.local"),
                )
            conexao.commit()

    def _converter(self, linha):
        # A consulta JOIN retorna as colunas na ordem definida em _consulta_base.
        livro = Livro(linha[0], linha[1], linha[2], linha[3], linha[4])
        leitura = Leitura(linha[5], linha[6], linha[0], linha[7], linha[8], linha[9], linha[10])
        dados = livro.to_dict()
        dados.update(leitura.to_dict())
        dados["progresso"] = round(leitura.paginas_lidas * 100 / livro.total_paginas, 1)
        return dados

    def _consulta_base(self):
        return """SELECT l.id_livro, l.titulo, l.autor, l.total_paginas, l.capa,
                  le.id_leitura, le.id_usuario, le.status, le.paginas_lidas,
                  le.nota, le.comentario
                  FROM livro AS l
                  INNER JOIN leitura AS le ON l.id_livro = le.id_livro"""

    def buscar_todos(self, id_usuario, status="Todos"):
        sql = self._consulta_base() + " WHERE le.id_usuario = ?"
        valores = [id_usuario]
        if status != "Todos":
            sql += " AND le.status = ?"
            valores.append(status)
        sql += " ORDER BY l.titulo ASC, l.id_livro ASC"
        with sqlite3.connect(self.caminho_banco) as conexao:
            linhas = conexao.cursor().execute(sql, valores).fetchall()
        return [self._converter(linha) for linha in linhas]

    def buscar_por_id(self, id_usuario, id_livro):
        sql = self._consulta_base() + " WHERE le.id_usuario = ? AND l.id_livro = ?"
        with sqlite3.connect(self.caminho_banco) as conexao:
            linha = conexao.cursor().execute(sql, (id_usuario, id_livro)).fetchone()
        if linha is None:
            return None
        return self._converter(linha)

    def inserir(self, livro, leitura):
        with sqlite3.connect(self.caminho_banco) as conexao:
            cursor = conexao.cursor()
            cursor.execute(
                "INSERT INTO livro (titulo, autor, total_paginas, capa) VALUES (?, ?, ?, ?)",
                (livro.titulo, livro.autor, livro.total_paginas, livro.capa),
            )
            id_livro = cursor.lastrowid
            cursor.execute(
                """INSERT INTO leitura
                   (id_usuario, id_livro, status, paginas_lidas, nota, comentario)
                   VALUES (?, ?, ?, ?, ?, ?)""",
                (leitura.id_usuario, id_livro, leitura.status,
                 leitura.paginas_lidas, leitura.nota, leitura.comentario),
            )
            conexao.commit()
        return id_livro

    def atualizar(self, id_usuario, id_livro, livro, leitura):
        if self.buscar_por_id(id_usuario, id_livro) is None:
            return False
        with sqlite3.connect(self.caminho_banco) as conexao:
            cursor = conexao.cursor()
            cursor.execute(
                """UPDATE livro SET titulo=?, autor=?, total_paginas=?, capa=?
                   WHERE id_livro=?""",
                (livro.titulo, livro.autor, livro.total_paginas, livro.capa, id_livro),
            )
            cursor.execute(
                """UPDATE leitura SET status=?, paginas_lidas=?, nota=?, comentario=?
                   WHERE id_usuario=? AND id_livro=?""",
                (leitura.status, leitura.paginas_lidas, leitura.nota,
                 leitura.comentario, id_usuario, id_livro),
            )
            conexao.commit()
        return True

    def remover(self, id_usuario, id_livro):
        if self.buscar_por_id(id_usuario, id_livro) is None:
            return False
        with sqlite3.connect(self.caminho_banco) as conexao:
            cursor = conexao.cursor()
            cursor.execute(
                "DELETE FROM leitura WHERE id_usuario = ? AND id_livro = ?",
                (id_usuario, id_livro),
            )
            cursor.execute("DELETE FROM livro WHERE id_livro = ?", (id_livro,))
            conexao.commit()
        return True

    def contar_por_status(self, id_usuario):
        # LEFT JOIN conserva o usuário mesmo se a estante estiver vazia.
        with sqlite3.connect(self.caminho_banco) as conexao:
            linhas = conexao.cursor().execute(
                """SELECT le.status, COUNT(le.id_leitura)
                   FROM usuario AS u
                   LEFT JOIN leitura AS le ON u.id_usuario = le.id_usuario
                   WHERE u.id_usuario = ?
                   GROUP BY le.status""",
                (id_usuario,),
            ).fetchall()
        resumo = {"Quero ler": 0, "Lendo": 0, "Lido": 0}
        for linha in linhas:
            if linha[0] is not None:
                resumo[linha[0]] = linha[1]
        return resumo
