<?php
require "conexao.php";
require "funcoes.php";
$livro = buscar_livro($conexao, (isset($_GET["id"]) ? $_GET["id"] : 0));
if (!$livro) { http_response_code(404); die("Livro não encontrado. <a href='estante.php'>Voltar</a>"); }
$titulo_pagina = "Editar livro";
$destino_formulario = "atualizar.php";
include "cabecalho.php";
?>
<div class="caixa"><span class="selo">EDITAR LEITURA</span><h1>Editar livro</h1><p class="subtitulo">Atualize páginas, status, nota ou comentário.</p>
<?php include "form_livro.php"; ?></div>
<?php include "rodape.php"; ?>
