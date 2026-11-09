<?php
require "conexao.php";
require "funcoes.php";
$livro = buscar_livro($conexao, (isset($_GET["id"]) ? $_GET["id"] : 0));
if (!$livro) { http_response_code(404); die("Livro não encontrado. <a href='estante.php'>Voltar</a>"); }
$titulo_pagina = "Detalhes";
include "cabecalho.php";
?>
<div class="topo-pagina"><div><span class="selo">SUA LEITURA</span><h1>Detalhes do livro</h1></div><a href="estante.php" class="botao secundario">Voltar à estante</a></div>
<article class="detalhes">
    <div class="detalhes-capa">
    <?php if ($livro["capa"] !== "") { ?><img src="<?php echo escapar($livro["capa"]); ?>" alt="Capa ilustrativa">
    <?php } else { ?><div class="capa-vazia">📖 Sem capa</div><?php } ?>
    </div>
    <div class="detalhes-conteudo"><span class="selo"><?php echo escapar($livro["status"]); ?></span>
    <h1><?php echo escapar($livro["titulo"]); ?></h1><p class="autor">Por <?php echo escapar($livro["autor"]); ?></p>
    <h3 class="divisor">Progresso de leitura</h3>
    <p><?php echo (int) $livro["paginas_lidas"]; ?> de <?php echo (int) $livro["total_paginas"]; ?> páginas concluídas</p>
    <div class="progresso"><div class="progresso-preenchido" style="width:<?php echo progresso($livro); ?>%"></div></div><p><?php echo progresso($livro); ?>% de progresso</p>
    <h3 class="divisor">Minha avaliação</h3><p><?php echo $livro["nota"] === null ? "Ainda não avaliado" : "★ " . escapar($livro["nota"]) . " de 10"; ?></p>
    <h3 class="divisor">Comentário</h3><p><?php echo $livro["comentario"] === "" ? "Ainda não foi registrado nenhum comentário." : escapar($livro["comentario"]); ?></p>
    <div class="linha-botoes"><a class="botao" href="editar.php?id=<?php echo (int) $livro["id_livro"]; ?>">Editar livro</a>
    <form method="POST" action="excluir.php" onsubmit="return confirm('Deseja realmente excluir este livro?');">
    <input type="hidden" name="id_livro" value="<?php echo (int) $livro["id_livro"]; ?>"><button type="submit" class="botao perigo">Excluir livro</button>
    </form></div></div>
</article>
<?php include "rodape.php"; ?>
