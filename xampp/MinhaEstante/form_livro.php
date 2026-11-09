<form action="<?php echo escapar($destino_formulario); ?>" method="POST" class="form-grid" id="form-livro">
<?php if (isset($livro["id_livro"])) { ?><input type="hidden" name="id_livro" value="<?php echo (int) $livro["id_livro"]; ?>"><?php } ?>
<div class="campo"><label for="titulo">Título *</label><input id="titulo" name="titulo" type="text" maxlength="150" required value="<?php echo escapar(campo_livro($livro, "titulo")); ?>"></div>
<div class="campo"><label for="autor">Autor *</label><input id="autor" name="autor" type="text" maxlength="100" required value="<?php echo escapar(campo_livro($livro, "autor")); ?>"></div>
<div class="campo"><label for="total_paginas">Total de páginas *</label><input id="total_paginas" name="total_paginas" type="number" min="1" step="1" required value="<?php echo escapar(campo_livro($livro, "total_paginas")); ?>"></div>
<div class="campo"><label for="paginas_lidas">Páginas lidas *</label><input id="paginas_lidas" name="paginas_lidas" type="number" min="0" step="1" required value="<?php echo escapar(campo_livro($livro, "paginas_lidas", "0")); ?>"><span class="campo-ajuda">Quero ler = 0; Lendo = entre 1 e total - 1; Lido = total.</span></div>
<div class="campo"><label for="status">Status *</label><select id="status" name="status" required>
  <?php foreach (["Quero ler", "Lendo", "Lido"] as $status) { ?>
    <option value="<?php echo escapar($status); ?>" <?php if ((campo_livro($livro, "status", "Quero ler")) === $status) echo "selected"; ?>><?php echo escapar($status); ?></option>
  <?php } ?>
</select></div>
<div class="campo"><label for="capa">Capa ilustrativa</label><select id="capa" name="capa">
  <?php foreach (["" => "Sem capa", "img/capas/classica.png" => "Clássica", "img/capas/lua.png" => "Luar", "img/capas/botanica.png" => "Botânica"] as $url => $nome) { ?>
  <option value="<?php echo escapar($url); ?>" <?php if ((campo_livro($livro, "capa")) === $url) echo "selected"; ?>><?php echo escapar($nome); ?></option>
  <?php } ?>
</select></div>
<div class="campo"><label for="nota">Nota (0 a 10, opcional)</label><input id="nota" name="nota" type="number" min="0" max="10" step="0.1" value="<?php echo escapar(campo_livro($livro, "nota")); ?>"><span class="campo-ajuda">Deixe vazio se ainda não avaliou. Zero é uma nota válida.</span></div>
<div class="campo largura-toda"><label for="comentario">Comentário (opcional)</label><textarea id="comentario" name="comentario" maxlength="800"><?php echo escapar(campo_livro($livro, "comentario")); ?></textarea></div>
<p class="mensagem" id="aviso-validacao" aria-live="polite"></p><div class="linha-botoes"><button class="botao" type="submit">Salvar livro</button><a class="botao secundario" href="estante.php">Cancelar</a></div>
</form>
<script src="js/formulario.js"></script>
