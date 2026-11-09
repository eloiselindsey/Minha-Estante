"""Entidades simples: atributos e conversão para dicionário (apostila POO)."""


class Usuario:
    def __init__(self, id_usuario, nome, email):
        self.id_usuario = id_usuario
        self.nome = nome
        self.email = email

    def to_dict(self):
        return {"id_usuario": self.id_usuario, "nome": self.nome, "email": self.email}


class Livro:
    def __init__(self, id_livro, titulo, autor, total_paginas, capa):
        self.id_livro = id_livro
        self.titulo = titulo
        self.autor = autor
        self.total_paginas = total_paginas
        self.capa = capa

    def to_dict(self):
        return {
            "id_livro": self.id_livro,
            "titulo": self.titulo,
            "autor": self.autor,
            "total_paginas": self.total_paginas,
            "capa": self.capa,
        }


class Leitura:
    def __init__(self, id_leitura, id_usuario, id_livro, status, paginas_lidas, nota, comentario):
        self.id_leitura = id_leitura
        self.id_usuario = id_usuario
        self.id_livro = id_livro
        self.status = status
        self.paginas_lidas = paginas_lidas
        self.nota = nota
        self.comentario = comentario

    def to_dict(self):
        return {
            "id_leitura": self.id_leitura,
            "id_usuario": self.id_usuario,
            "id_livro": self.id_livro,
            "status": self.status,
            "paginas_lidas": self.paginas_lidas,
            "nota": self.nota,
            "comentario": self.comentario,
        }
