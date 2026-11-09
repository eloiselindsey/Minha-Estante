<?php
require "conexao.php";
require "funcoes.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: estante.php"); exit; }
$id_livro = (int) ((isset($_POST["id_livro"]) ? $_POST["id_livro"] : 0));
if (!buscar_livro($conexao, $id_livro)) { http_response_code(404); die("Livro não encontrado."); }
$comando = mysqli_prepare($conexao, "DELETE FROM leitura WHERE id_usuario = 1 AND id_livro = ?");
mysqli_stmt_bind_param($comando, "i", $id_livro);
if (!mysqli_stmt_execute($comando)) die("Não foi possível excluir a leitura.");
mysqli_stmt_close($comando);
$comando = mysqli_prepare($conexao, "DELETE FROM livro WHERE id_livro = ?");
mysqli_stmt_bind_param($comando, "i", $id_livro);
if (!mysqli_stmt_execute($comando)) die("Não foi possível excluir o livro.");
mysqli_stmt_close($comando);
header("Location: estante.php?ok=1");
exit;
?>
