<?php
// Executar fora do navegador: php testes/testar_validacoes_php.php
// Valida as regras de negócio de XAMPP sem precisar ligar o banco.
require __DIR__ . "/../xampp/MinhaEstante/funcoes.php";
$dados_basicos = [
    "titulo" => "Meu livro",
    "autor" => "Autora",
    "total_paginas" => "400",
    "paginas_lidas" => "100",
    "status" => "Lendo",
    "capa" => "img/capas/classica.png",
    "nota" => "0",
    "comentario" => "Gostei"
];
function testar($nome, $alteracoes, $espera_erro, $espera_nota = null) {
    global $dados_basicos;
    $_POST = array_merge($dados_basicos, $alteracoes);
    $resposta = receber_formulario();
    $erro = $resposta[1];
    $ok = $espera_erro ? $erro !== null : $erro === null;
    if (!$espera_erro && $espera_nota !== null) $ok = $ok && (float) $resposta[0]["nota"] === (float) $espera_nota;
    if (!$espera_erro && array_key_exists("nota", $alteracoes) && $alteracoes["nota"] === "") $ok = $ok && $resposta[0]["nota"] === null;
    echo ($ok ? "OK" : "FALHOU") . " - $nome\n";
    if (!$ok) exit(1);
}
testar("Livro em leitura e nota zero", [], false, 0);
testar("Sem nota é NULL", ["nota" => ""], false);
testar("Nota máxima 10", ["nota" => "10"], false, 10);
testar("Nota fora do intervalo", ["nota" => "11"], true);
testar("Páginas negativas", ["paginas_lidas" => "-5"], true);
testar("Mais páginas lidas que total", ["paginas_lidas" => "401"], true);
testar("Status Lido com progresso incompleto", ["status" => "Lido"], true);
testar("Status Lido com 400/400", ["status" => "Lido", "paginas_lidas" => "400"], false);
testar("Quero ler com 0/400", ["status" => "Quero ler", "paginas_lidas" => "0"], false);
testar("Quero ler com páginas já lidas", ["status" => "Quero ler"], true);
testar("Título ausente", ["titulo" => ""], true);
testar("Tentativa de capa externa", ["capa" => "https://site.com/malware.png"], true);
if (escapar('<script>alert(1)</script>') !== '&lt;script&gt;alert(1)&lt;/script&gt;') exit(1);
echo "OK - htmlspecialchars protege a exibição de HTML\n";
