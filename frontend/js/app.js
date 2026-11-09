// MinhaEstante: DOM, Fetch API, JSON, eventos e tratamento de erros (DWS).
// GitHub Pages publica HTML/CSS/JS, mas não executa a API Flask ou PHP.
const PUBLICADO_PAGES = window.location.hostname.endsWith("github.io");
const API_URL = "http://127.0.0.1:5000/api";

if (PUBLICADO_PAGES) {
    const main = document.querySelector("main");
    if (main) {
        const aviso = document.createElement("p");
        aviso.className = "aviso";
        aviso.textContent = "Prévia do GitHub Pages: esta página mostra a interface. Para cadastrar, editar e salvar dados, execute a versão PHP/MySQL no XAMPP ou a versão Python/Flask localmente. O GitHub Pages não executa bancos de dados.";
        main.insertBefore(aviso, main.firstChild);
    }
}

function mostrarMensagem(id, texto, erro) {
    const elemento = document.getElementById(id);
    if (!elemento) return;
    elemento.className = erro ? "mensagem erro" : "mensagem sucesso";
    elemento.textContent = texto;
}

async function requisicao(caminho, opcoes) {
    const resposta = await fetch(API_URL + caminho, opcoes);
    const dados = await resposta.json();
    if (!resposta.ok) throw new Error(dados.erro || "Não foi possível completar a operação.");
    return dados;
}

function criarElemento(tag, classe, texto) {
    const elemento = document.createElement(tag);
    if (classe) elemento.className = classe;
    if (texto !== undefined) elemento.textContent = texto;
    return elemento;
}

function criarCapa(capa, classe) {
    const caixa = criarElemento("div", classe);
    if (capa) {
        const imagem = document.createElement("img");
        imagem.src = capa;
        imagem.alt = "Capa ilustrativa selecionada para o livro";
        caixa.appendChild(imagem);
    } else {
        caixa.appendChild(criarElemento("div", "capa-vazia", "📖 Sem capa"));
    }
    return caixa;
}

async function carregarResumo() {
    if (PUBLICADO_PAGES) return;
    try {
        const resumo = await requisicao("/resumo");
        document.getElementById("numero-quero").textContent = resumo["Quero ler"];
        document.getElementById("numero-lendo").textContent = resumo.Lendo;
        document.getElementById("numero-lido").textContent = resumo.Lido;
    } catch (erro) {
        mostrarMensagem("mensagem-inicio", "Não foi possível carregar os números. Verifique se a API Flask está ligada.", true);
    }
}

function construirCard(livro) {
    const card = criarElemento("article", "livro-card");
    card.appendChild(criarCapa(livro.capa, "capa-card"));
    const corpo = criarElemento("div", "corpo-card");
    corpo.appendChild(criarElemento("h3", "", livro.titulo));
    corpo.appendChild(criarElemento("p", "autor", livro.autor));
    corpo.appendChild(criarElemento("span", "etiqueta", livro.status));
    corpo.appendChild(criarElemento("p", "numero-progresso", livro.paginas_lidas + " de " + livro.total_paginas + " páginas"));
    const barra = criarElemento("div", "progresso");
    const parte = criarElemento("div", "progresso-preenchido");
    parte.style.width = livro.progresso + "%";
    barra.appendChild(parte);
    corpo.appendChild(barra);
    corpo.appendChild(criarElemento("p", "numero-progresso", livro.progresso + "% lido"));
    corpo.appendChild(criarElemento("p", "autor", livro.nota === null ? "Ainda sem nota" : "★ Nota: " + livro.nota + "/10"));
    const link = criarElemento("a", "botao", "Ver detalhes");
    link.href = "detalhes.html?id=" + livro.id_livro;
    corpo.appendChild(link);
    card.appendChild(corpo);
    return card;
}

async function carregarEstante() {
    if (PUBLICADO_PAGES) {
        const lista = document.getElementById("lista-livros");
        lista.innerHTML = "";
        lista.appendChild(criarElemento("p", "aviso", "A estante dinâmica depende de uma API e de um banco de dados. Na demonstração do GitHub Pages, execute a versão para XAMPP conforme o README do repositório."));
        return;
    }
    const lista = document.getElementById("lista-livros");
    lista.innerHTML = "";
    const status = document.getElementById("filtro").value;
    try {
        const livros = await requisicao("/livros?status=" + encodeURIComponent(status));
        if (livros.length === 0) {
            const vazio = criarElemento("div", "vazio");
            vazio.appendChild(criarElemento("h3", "", "Nenhum livro por aqui..."));
            vazio.appendChild(criarElemento("p", "", "Cadastre um livro ou escolha outro filtro."));
            const link = criarElemento("a", "botao", "Adicionar meu primeiro livro");
            link.href = "formulario.html";
            vazio.appendChild(link);
            lista.appendChild(vazio);
            return;
        }
        livros.forEach(function (livro) { lista.appendChild(construirCard(livro)); });
    } catch (erro) {
        mostrarMensagem("mensagem-estante", "Falha ao consultar a API. Inicie backend/app.py e atualize esta página.", true);
    }
}

function idDaPagina() {
    const numero = parseInt(window.location.search.replace("?id=", ""));
    return numero > 0 ? numero : null;
}

function dadosDoFormulario() {
    return {
        titulo: document.getElementById("titulo").value.trim(),
        autor: document.getElementById("autor").value.trim(),
        total_paginas: Number(document.getElementById("total_paginas").value),
        paginas_lidas: Number(document.getElementById("paginas_lidas").value),
        status: document.getElementById("status").value,
        capa: document.getElementById("capa").value,
        nota: document.getElementById("nota").value,
        comentario: document.getElementById("comentario").value.trim()
    };
}

function validarFormulario(dados) {
    if (dados.total_paginas <= 0 || dados.paginas_lidas < 0 ||
        !Number.isInteger(dados.total_paginas) || !Number.isInteger(dados.paginas_lidas) ||
        dados.paginas_lidas > dados.total_paginas) {
        return "Confira as páginas: números inteiros, de 0 até o total (que deve ser positivo).";
    }
    if (dados.status === "Quero ler" && dados.paginas_lidas !== 0) return "Em 'Quero ler', informe zero páginas.";
    if (dados.status === "Lendo" && (dados.paginas_lidas === 0 || dados.paginas_lidas === dados.total_paginas)) return "Em 'Lendo', informe páginas entre 1 e total - 1.";
    if (dados.status === "Lido" && dados.paginas_lidas !== dados.total_paginas) return "Em 'Lido', as páginas lidas devem ser iguais ao total.";
    if (dados.nota !== "" && (Number(dados.nota) < 0 || Number(dados.nota) > 10)) return "A nota deve ficar entre 0 e 10.";
    return "";
}

async function prepararFormulario() {
    if (PUBLICADO_PAGES) {
        document.getElementById("form-livro").addEventListener("submit", function (evento) {
            evento.preventDefault();
            mostrarMensagem("mensagem-formulario", "O GitHub Pages não salva livros. Execute a versão XAMPP ou Flask para cadastrar no banco.", true);
        });
        return;
    }
    const id = idDaPagina();
    if (id) {
        document.getElementById("titulo-formulario").textContent = "Editar livro";
        try {
            const livro = await requisicao("/livros/" + id);
            ["titulo", "autor", "total_paginas", "paginas_lidas", "status", "capa", "comentario"].forEach(function (campo) {
                document.getElementById(campo).value = livro[campo];
            });
            document.getElementById("nota").value = livro.nota === null ? "" : livro.nota;
        } catch (erro) { mostrarMensagem("mensagem-formulario", erro.message, true); }
    }
    document.getElementById("form-livro").addEventListener("submit", async function (evento) {
        evento.preventDefault();
        const dados = dadosDoFormulario();
        const aviso = validarFormulario(dados);
        if (aviso) { mostrarMensagem("mensagem-formulario", aviso, true); return; }
        const botao = document.getElementById("botao-salvar");
        botao.disabled = true;
        botao.textContent = "Salvando...";
        try {
            const caminho = id ? "/livros/" + id : "/livros";
            const metodo = id ? "PUT" : "POST";
            const resposta = await requisicao(caminho, {
                method: metodo,
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(dados)
            });
            mostrarMensagem("mensagem-formulario", resposta.mensagem + " Abra a estante para conferir.", false);
            if (!id) document.getElementById("form-livro").reset();
        } catch (erro) { mostrarMensagem("mensagem-formulario", erro.message, true); }
        botao.disabled = false;
        botao.textContent = "Salvar na estante";
    });
}

async function carregarDetalhes() {
    if (PUBLICADO_PAGES) {
        mostrarMensagem("mensagem-detalhes", "A consulta de detalhes requer banco de dados e não está disponível no GitHub Pages.", true);
        return;
    }
    const id = idDaPagina();
    if (!id) { mostrarMensagem("mensagem-detalhes", "Identificação do livro inválida.", true); return; }
    try {
        const livro = await requisicao("/livros/" + id);
        const painel = document.getElementById("painel-detalhes");
        painel.innerHTML = "";
        painel.appendChild(criarCapa(livro.capa, "detalhes-capa"));
        const conteudo = criarElemento("div", "detalhes-conteudo");
        conteudo.appendChild(criarElemento("span", "selo", livro.status));
        conteudo.appendChild(criarElemento("h1", "", livro.titulo));
        conteudo.appendChild(criarElemento("p", "autor", "Por " + livro.autor));
        conteudo.appendChild(criarElemento("h3", "divisor", "Progresso de leitura"));
        conteudo.appendChild(criarElemento("p", "", livro.paginas_lidas + " de " + livro.total_paginas + " páginas concluídas"));
        const barra = criarElemento("div", "progresso");
        const parte = criarElemento("div", "progresso-preenchido");
        parte.style.width = livro.progresso + "%";
        barra.appendChild(parte);
        conteudo.appendChild(barra);
        conteudo.appendChild(criarElemento("p", "numero-progresso", livro.progresso + "% de progresso"));
        conteudo.appendChild(criarElemento("h3", "divisor", "Minha avaliação"));
        conteudo.appendChild(criarElemento("p", "", livro.nota === null ? "Ainda não avaliado" : "★ " + livro.nota + " de 10"));
        conteudo.appendChild(criarElemento("h3", "divisor", "Comentário"));
        conteudo.appendChild(criarElemento("p", "", livro.comentario || "Ainda não foi registrado nenhum comentário."));
        const acoes = criarElemento("div", "linha-botoes");
        const editar = criarElemento("a", "botao", "Editar livro");
        editar.href = "formulario.html?id=" + id;
        acoes.appendChild(editar);
        const excluir = criarElemento("button", "botao perigo", "Excluir livro");
        excluir.addEventListener("click", async function () {
            if (!window.confirm("Deseja realmente excluir este livro e sua leitura?")) return;
            try {
                await requisicao("/livros/" + id, { method: "DELETE" });
                window.location.href = "estante.html";
            } catch (erro) { mostrarMensagem("mensagem-detalhes", erro.message, true); }
        });
        acoes.appendChild(excluir);
        conteudo.appendChild(acoes);
        painel.appendChild(conteudo);
    } catch (erro) { mostrarMensagem("mensagem-detalhes", erro.message, true); }
}

function prepararContato() {
    document.getElementById("form-contato").addEventListener("submit", function (evento) {
        evento.preventDefault();
        mostrarMensagem("mensagem-contato", "Campos preenchidos corretamente. Este formulário é apenas ilustrativo e não envia mensagens.", false);
    });
}

// Cada página usa apenas as funções de que realmente precisa.
const pagina = document.body.getAttribute("data-pagina");
if (pagina === "inicio") carregarResumo();
if (pagina === "estante") {
    document.getElementById("filtro").addEventListener("change", carregarEstante);
    carregarEstante();
}
if (pagina === "formulario") prepararFormulario();
if (pagina === "detalhes") carregarDetalhes();
if (pagina === "contato") prepararContato();
