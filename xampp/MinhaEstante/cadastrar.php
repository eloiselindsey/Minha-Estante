<?php
require "funcoes.php";
$titulo_pagina = "Cadastrar livro";
$destino_formulario = "salvar.php";
$livro = [];
include "cabecalho.php";
?>
<div class="caixa"><span class="selo">NOVA HISTÓRIA</span><h1>Cadastrar livro</h1><p class="subtitulo">Preencha os campos para organizar sua leitura.</p>
<?php include "form_livro.php"; ?></div>
<?php include "rodape.php"; ?>
