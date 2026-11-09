<?php
require "conexao.php";
require "funcoes.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: cadastrar.php"); exit; }
$recebidos = receber_formulario();
    $dados = $recebidos[0];
    $erro = $recebidos[1];
if ($erro) { http_response_code(400); die(escapar($erro) . " <a href='cadastrar.php'>Voltar</a>"); }

// Prepared statements: mencionados no quadro 'Segurança' da apostila DWS.
$sql = "INSERT INTO livro (titulo, autor, total_paginas, capa) VALUES (?, ?, ?, ?)";
$comando = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($comando, "ssis", $dados["titulo"], $dados["autor"], $dados["total_paginas"], $dados["capa"]);
if (!mysqli_stmt_execute($comando)) die("Não foi possível cadastrar o livro.");
$id_livro = mysqli_insert_id($conexao);
mysqli_stmt_close($comando);

$comando = mysqli_prepare($conexao, "INSERT INTO leitura (id_usuario, id_livro, status, paginas_lidas, nota, comentario) VALUES (1, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($comando, "isids", $id_livro, $dados["status"], $dados["paginas_lidas"], $dados["nota"], $dados["comentario"]);
if (!mysqli_stmt_execute($comando)) {
    $id_livro = (int) $id_livro;
    mysqli_query($conexao, "DELETE FROM livro WHERE id_livro = $id_livro");
    die("Não foi possível salvar a leitura.");
}
mysqli_stmt_close($comando);
header("Location: estante.php?ok=1");
exit;
?>
