"""API REST local do MinhaEstante: Flask + SQLite + DAO."""
from flask import Flask, request, jsonify
from flask_cors import CORS
from dao import BibliotecaDAO
from models import Livro, Leitura

app = Flask(__name__)
CORS(app)  # Apostila DWS: integração entre origens durante desenvolvimento local.
dao = BibliotecaDAO()
USUARIO_DEMO = 1  # Não é login. Todos utilizam a mesma estante local de demonstração.


def validar_dados(dados):
    """Verifica os valores antes de enviá-los ao banco de dados."""
    if not isinstance(dados, dict):
        return "Envie um objeto JSON válido."
    titulo = str(dados.get("titulo") or "").strip()
    autor = str(dados.get("autor") or "").strip()
    if not titulo or not autor:
        return "Título e autor são obrigatórios."
    if len(titulo) > 150 or len(autor) > 100:
        return "Título ou autor muito longo."
    # JSON numérico inteiro: não aceitar 12.5 como se fossem 12 páginas.
    if not isinstance(dados.get("total_paginas"), int) or isinstance(dados.get("total_paginas"), bool):
        return "Informe valores inteiros para as páginas."
    if not isinstance(dados.get("paginas_lidas"), int) or isinstance(dados.get("paginas_lidas"), bool):
        return "Informe valores inteiros para as páginas."
    total = dados["total_paginas"]
    lidas = dados["paginas_lidas"]
    if total <= 0 or lidas < 0 or lidas > total:
        return "As páginas lidas devem ficar entre zero e o total de páginas (maior que zero)."
    status = dados.get("status")
    if status not in ["Quero ler", "Lendo", "Lido"]:
        return "Escolha um status de leitura válido."
    if status == "Quero ler" and lidas != 0:
        return "Para 'Quero ler', informe zero páginas lidas."
    if status == "Lendo" and (lidas == 0 or lidas == total):
        return "Para 'Lendo', informe um valor entre 1 e total - 1."
    if status == "Lido" and lidas != total:
        return "Para 'Lido', as páginas lidas devem ser iguais ao total."
    nota = dados.get("nota")
    if nota is not None and nota != "":
        try:
            valor = float(nota)
        except (TypeError, ValueError):
            return "A nota deve ser um número entre 0 e 10."
        if not (0 <= valor <= 10):
            return "A nota deve estar entre 0 e 10."
    capa = str(dados.get("capa") or "")
    if capa not in ["", "img/capas/classica.png", "img/capas/lua.png", "img/capas/botanica.png"]:
        return "Selecione uma das capas locais disponíveis."
    if len(str(dados.get("comentario") or "")) > 800:
        return "O comentário deve ter até 800 caracteres."
    return None


def montar_objetos(dados):
    livro = Livro(None, str(dados["titulo"]).strip(), str(dados["autor"]).strip(),
                  int(dados["total_paginas"]), str(dados.get("capa") or ""))
    nota = dados.get("nota")
    nota = None if nota is None or nota == "" else float(nota)
    leitura = Leitura(None, USUARIO_DEMO, None, dados["status"],
                      int(dados["paginas_lidas"]), nota,
                      str(dados.get("comentario") or "").strip())
    return livro, leitura


@app.route("/api", methods=["GET"])
def inicio_api():
    return jsonify({"mensagem": "MinhaEstante: API local de demonstração", "login": False})


@app.route("/api/livros", methods=["GET"])
def listar_livros():
    status = request.args.get("status", "Todos")
    if status not in ["Todos", "Quero ler", "Lendo", "Lido"]:
        return jsonify({"erro": "Filtro inválido."}), 400
    return jsonify(dao.buscar_todos(USUARIO_DEMO, status))


@app.route("/api/livros/<int:id_livro>", methods=["GET"])
def detalhar_livro(id_livro):
    livro = dao.buscar_por_id(USUARIO_DEMO, id_livro)
    if livro is None:
        return jsonify({"erro": "Livro não encontrado."}), 404
    return jsonify(livro)


@app.route("/api/livros", methods=["POST"])
def cadastrar_livro():
    dados = request.json
    erro = validar_dados(dados)
    if erro:
        return jsonify({"erro": erro}), 400
    livro, leitura = montar_objetos(dados)
    id_livro = dao.inserir(livro, leitura)
    return jsonify({"mensagem": "Livro cadastrado com sucesso!", "id_livro": id_livro}), 201


@app.route("/api/livros/<int:id_livro>", methods=["PUT"])
def editar_livro(id_livro):
    dados = request.json
    erro = validar_dados(dados)
    if erro:
        return jsonify({"erro": erro}), 400
    livro, leitura = montar_objetos(dados)
    if not dao.atualizar(USUARIO_DEMO, id_livro, livro, leitura):
        return jsonify({"erro": "Livro não encontrado."}), 404
    return jsonify({"mensagem": "Livro atualizado com sucesso!"})


@app.route("/api/livros/<int:id_livro>", methods=["DELETE"])
def excluir_livro(id_livro):
    if not dao.remover(USUARIO_DEMO, id_livro):
        return jsonify({"erro": "Livro não encontrado."}), 404
    return jsonify({"mensagem": "Livro excluído com sucesso!"})


@app.route("/api/resumo", methods=["GET"])
def resumo():
    return jsonify(dao.contar_por_status(USUARIO_DEMO))


if __name__ == "__main__":
    dao.inicializar_banco()
    app.run(debug=True)
