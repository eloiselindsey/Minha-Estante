<?php
require "conexao.php";
require "funcoes.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: estante.php"); exit; }
$id_livro = (int) ((isset($_POST["id_livro"]) ? $_POST["id_livro"] : 0));
if (!buscar_livro($conexao, $id_livro)) { http_response_code(404); die("Livro não encontrado."); }
$recebidos = receber_formulario();
    $dados = $recebidos[0];
    $erro = $recebidos[1];
if ($erro) { http_response_code(400); die(escapar($erro) . " <a href='editar.php?id=$id_livro'>Voltar</a>"); }

$comando = mysqli_prepare($conexao, "UPDATE livro SET titulo=?, autor=?, total_paginas=?, capa=? WHERE id_livro=?");
mysqli_stmt_bind_param($comando, "ssisi", $dados["titulo"], $dados["autor"], $dados["total_paginas"], $dados["capa"], $id_livro);
if (!mysqli_stmt_execute($comando)) die("Não foi possível atualizar o livro.");
mysqli_stmt_close($comando);

$comando = mysqli_prepare($conexao, "UPDATE leitura SET status=?, paginas_lidas=?, nota=?, comentario=? WHERE id_usuario=1 AND id_livro=?");
mysqli_stmt_bind_param($comando, "sidsi", $dados["status"], $dados["paginas_lidas"], $dados["nota"], $dados["comentario"], $id_livro);
if (!mysqli_stmt_execute($comando)) die("Não foi possível atualizar a leitura.");
mysqli_stmt_close($comando);
header("Location: detalhes.php?id=$id_livro");
exit;
?>
