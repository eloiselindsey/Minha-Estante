<?php
require "conexao.php";
require "funcoes.php";
$titulo_pagina = "Início";
$contagens = ["Quero ler" => 0, "Lendo" => 0, "Lido" => 0];
$resultado = mysqli_query($conexao,
    "SELECT status, COUNT(*) AS quantidade FROM leitura WHERE id_usuario = 1 GROUP BY status");
if ($resultado) {
    while ($linha = mysqli_fetch_assoc($resultado)) $contagens[$linha["status"]] = $linha["quantidade"];
}
include "cabecalho.php";
?>
<section class="hero"><div class="hero-conteudo">
    <span class="selo">UM LUGAR PARA SUAS HISTÓRIAS</span>
    <h1>Cada leitura merece um lugar especial.</h1>
    <p class="subtitulo">Organize os livros que deseja ler, acompanhe suas páginas, avalie suas histórias preferidas e construa uma estante do seu jeito.</p>
    <div class="linha-botoes"><a class="botao" href="estante.php">Explorar minha estante</a><a class="botao secundario" href="cadastrar.php">Cadastrar livro</a></div>
    <p class="aviso">Versão acadêmica local com MySQL. Existe apenas uma estante de demonstração, sem login e sem dados individuais privados.</p>
</div><div class="hero-ilustra" aria-label="Estante decorativa"><div class="prateleira"><i class="livro-decorativo vinho"></i><i class="livro-decorativo baixo ocre"></i><i class="livro-decorativo alto oliva"></i><i class="livro-decorativo creme"></i><i class="livro-decorativo azul"></i></div><div class="prateleira"><i class="livro-decorativo alto azul"></i><i class="livro-decorativo creme"></i><i class="livro-decorativo baixo oliva"></i><i class="livro-decorativo ocre"></i><i class="livro-decorativo vinho"></i></div></div></section>
<section class="secao"><h2>Seu cantinho de leitura</h2><div class="cartoes">
    <article class="cartao-info"><h3>01 · Cadastre</h3><p>Guarde títulos e autores das próximas histórias.</p></article>
    <article class="cartao-info"><h3>02 · Acompanhe</h3><p>Veja o percentual e as páginas lidas.</p></article>
    <article class="cartao-info"><h3>03 · Avalie</h3><p>Dê notas de 0 a 10 e escreva comentários.</p></article>
</div></section>
<section class="secao"><h2>Minha leitura em números</h2><div class="cartoes">
    <article class="cartao-info"><div class="numero"><?php echo (int) $contagens["Quero ler"]; ?></div><p>Quero ler</p></article>
    <article class="cartao-info"><div class="numero"><?php echo (int) $contagens["Lendo"]; ?></div><p>Lendo</p></article>
    <article class="cartao-info"><div class="numero"><?php echo (int) $contagens["Lido"]; ?></div><p>Lido</p></article>
</div></section>
<?php include "rodape.php"; ?>
