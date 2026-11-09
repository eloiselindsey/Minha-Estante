<?php
require "conexao.php";
require "funcoes.php";
$titulo_pagina = "Minha Estante";
$filtro = (isset($_GET["status"]) ? $_GET["status"] : "Todos");
if ($filtro !== "Todos" && $filtro !== "Quero ler" && $filtro !== "Lendo" && $filtro !== "Lido") $filtro = "Todos";
$sql = "SELECT l.id_livro, l.titulo, l.autor, l.total_paginas, l.capa,
        le.status, le.paginas_lidas, le.nota
        FROM livro AS l INNER JOIN leitura AS le ON l.id_livro = le.id_livro
        WHERE le.id_usuario = 1";
// O filtro só usa palavras de uma lista fixa, nunca SQL escrito pelo visitante.
if ($filtro === "Quero ler") $sql .= " AND le.status = 'Quero ler'";
if ($filtro === "Lendo") $sql .= " AND le.status = 'Lendo'";
if ($filtro === "Lido") $sql .= " AND le.status = 'Lido'";
$sql .= " ORDER BY l.titulo ASC, l.id_livro ASC";
$resultado = mysqli_query($conexao, $sql);
include "cabecalho.php";
?>
<div class="topo-pagina"><div><span class="selo">ORGANIZE SUA LEITURA</span><h1>Minha Estante</h1><p class="subtitulo">Livros, metas e avaliações em um só lugar.</p></div><a class="botao" href="cadastrar.php">+ Adicionar livro</a></div>
<form class="filtro" action="estante.php" method="GET"><label for="status">Filtrar:</label>
    <select id="status" name="status">
    <?php foreach (["Todos", "Quero ler", "Lendo", "Lido"] as $opcao) { ?>
        <option value="<?php echo escapar($opcao); ?>" <?php if ($opcao === $filtro) echo 'selected'; ?>><?php echo escapar($opcao); ?></option>
    <?php } ?>
    </select><button class="botao" type="submit">Aplicar</button>
</form>
<div class="estante-grid" style="margin-top:24px">
<?php if ($resultado && mysqli_num_rows($resultado) > 0) { ?>
    <?php while ($livro = mysqli_fetch_assoc($resultado)) { ?>
    <article class="livro-card">
        <div class="capa-card">
            <?php if ($livro["capa"] !== "") { ?><img src="<?php echo escapar($livro["capa"]); ?>" alt="Capa ilustrativa de <?php echo escapar($livro["titulo"]); ?>">
            <?php } else { ?><div class="capa-vazia">📖 Sem capa</div><?php } ?>
        </div>
        <div class="corpo-card">
            <h3><?php echo escapar($livro["titulo"]); ?></h3><p class="autor"><?php echo escapar($livro["autor"]); ?></p>
            <span class="etiqueta"><?php echo escapar($livro["status"]); ?></span>
            <p class="numero-progresso"><?php echo (int) $livro["paginas_lidas"]; ?> de <?php echo (int) $livro["total_paginas"]; ?> páginas</p>
            <div class="progresso"><div class="progresso-preenchido" style="width:<?php echo progresso($livro); ?>%"></div></div>
            <p class="numero-progresso"><?php echo progresso($livro); ?>% lido</p>
            <p class="autor"><?php echo $livro["nota"] === null ? "Ainda sem nota" : "★ Nota: " . escapar($livro["nota"]) . "/10"; ?></p>
            <a class="botao" href="detalhes.php?id=<?php echo (int) $livro["id_livro"]; ?>">Ver detalhes</a>
        </div>
    </article>
    <?php } ?>
<?php } else { ?>
    <div class="vazio"><h3>Não há livros neste filtro</h3><p>Cadastre seu primeiro livro ou selecione outra categoria.</p><a class="botao" href="cadastrar.php">Cadastrar livro</a></div>
<?php } ?>
</div>
<?php if (isset($_GET["ok"])) { ?><p class="mensagem sucesso">Operação concluída com sucesso.</p><?php } ?>
<?php include "rodape.php"; ?>
