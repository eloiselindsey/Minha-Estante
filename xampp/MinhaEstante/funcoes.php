<?php
// Funções simples com PHP, validação dos formulários e JOIN (DWS e BD).
function campo_post($nome) {
    if (isset($_POST[$nome])) return $_POST[$nome];
    return "";
}
function campo_livro($livro, $nome, $valor_padrao = "") {
    if (isset($livro[$nome])) return $livro[$nome];
    return $valor_padrao;
}
function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}

function buscar_livro($conexao, $id_livro) {
    $id_livro = (int) $id_livro;
    if ($id_livro <= 0) return null;
    $sql = "SELECT l.id_livro, l.titulo, l.autor, l.total_paginas, l.capa,
            le.id_leitura, le.status, le.paginas_lidas, le.nota, le.comentario
            FROM livro AS l INNER JOIN leitura AS le
            ON l.id_livro = le.id_livro
            WHERE le.id_usuario = 1 AND l.id_livro = $id_livro";
    $resultado = mysqli_query($conexao, $sql);
    if (!$resultado) return null;
    $livro = mysqli_fetch_assoc($resultado);
    return $livro ? $livro : null;
}

function progresso($livro) {
    if ($livro["total_paginas"] <= 0) return 0;
    return round($livro["paginas_lidas"] * 100 / $livro["total_paginas"], 1);
}

function receber_formulario() {
    $dados = [
        "titulo" => trim(campo_post("titulo")),
        "autor" => trim(campo_post("autor")),
        "total_paginas" => campo_post("total_paginas"),
        "paginas_lidas" => campo_post("paginas_lidas"),
        "status" => campo_post("status"),
        "capa" => campo_post("capa"),
        "nota" => campo_post("nota"),
        "comentario" => trim(campo_post("comentario"))
    ];
    if ($dados["titulo"] === "" || $dados["autor"] === "") return [null, "Informe título e autor."];
    if (strlen($dados["titulo"]) > 150 || strlen($dados["autor"]) > 100) return [null, "Título ou autor muito longo."];
    $total = filter_var($dados["total_paginas"], FILTER_VALIDATE_INT);
    $lidas = filter_var($dados["paginas_lidas"], FILTER_VALIDATE_INT);
    if ($total === false || $lidas === false || $total <= 0 || $lidas < 0 || $lidas > $total)
        return [null, "Páginas inválidas: informe números inteiros entre zero e o total (maior que zero)."];
    if ($dados["status"] !== "Quero ler" && $dados["status"] !== "Lendo" && $dados["status"] !== "Lido")
        return [null, "Selecione o status."];
    if ($dados["status"] === "Quero ler" && $lidas !== 0)
        return [null, "No status Quero ler, informe 0 páginas lidas."];
    if ($dados["status"] === "Lendo" && ($lidas <= 0 || $lidas >= $total))
        return [null, "No status Lendo, informe um número entre 1 e total - 1."];
    if ($dados["status"] === "Lido" && $lidas !== $total)
        return [null, "No status Lido, as páginas lidas devem ser iguais ao total."];
    if ($dados["capa"] !== "" && $dados["capa"] !== "img/capas/classica.png" && $dados["capa"] !== "img/capas/lua.png" && $dados["capa"] !== "img/capas/botanica.png")
        return [null, "Escolha uma capa disponível."];
    if (strlen($dados["comentario"]) > 800)
        return [null, "O comentário deve ter até 800 caracteres."];
    if ($dados["nota"] === "") {
        $dados["nota"] = null;  // NULL não significa nota zero.
    } else {
        $nota = filter_var($dados["nota"], FILTER_VALIDATE_FLOAT);
        if ($nota === false || $nota < 0 || $nota > 10)
            return [null, "A nota precisa ser um número entre 0 e 10."];
        $dados["nota"] = $nota;
    }
    $dados["total_paginas"] = $total;
    $dados["paginas_lidas"] = $lidas;
    return [$dados, null];
}
?>
