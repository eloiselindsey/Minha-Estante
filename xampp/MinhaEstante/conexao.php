<?php
// Conteúdo DWS: conexão PHP/MySQL via mysqli no XAMPP.
// Se o professor configurar senha/porta diferente, altere somente este arquivo.
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "minhaestante";
$conexao = mysqli_connect($servidor, $usuario, $senha, $banco);
if (!$conexao) {
    die("Banco indisponível. Inicie Apache/MySQL no XAMPP e importe banco.sql pelo phpMyAdmin.");
}
mysqli_set_charset($conexao, "utf8mb4");
?>
