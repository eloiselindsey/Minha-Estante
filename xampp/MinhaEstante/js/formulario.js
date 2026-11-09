// DOM e addEventListener (DWS): auxílio visual; a validação definitiva está no PHP.
const statusLivro = document.getElementById("status");
const totalPaginas = document.getElementById("total_paginas");
const paginasLidas = document.getElementById("paginas_lidas");
statusLivro.addEventListener("change", function () {
    if (statusLivro.value === "Quero ler") paginasLidas.value = 0;
    if (statusLivro.value === "Lido" && Number(totalPaginas.value) > 0)
        paginasLidas.value = totalPaginas.value;
});
document.getElementById("form-livro").addEventListener("submit", function (evento) {
    const total = Number(totalPaginas.value);
    const lidas = Number(paginasLidas.value);
    const status = statusLivro.value;
    let mensagem = "";
    if (total <= 0 || lidas < 0 || lidas > total) mensagem = "Confira o total de páginas e as páginas lidas.";
    if (status === "Quero ler" && lidas !== 0) mensagem = "Quero ler deve ter zero páginas lidas.";
    if (status === "Lendo" && (lidas <= 0 || lidas >= total)) mensagem = "Lendo exige páginas entre 1 e total - 1.";
    if (status === "Lido" && lidas !== total) mensagem = "Lido exige páginas lidas iguais ao total.";
    if (mensagem) {
        evento.preventDefault();
        const aviso = document.getElementById("aviso-validacao");
        if (aviso) aviso.textContent = mensagem;
    }
});
