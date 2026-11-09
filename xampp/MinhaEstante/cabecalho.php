<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo escapar((isset($titulo_pagina) ? $titulo_pagina : "MinhaEstante")); ?> | MinhaEstante</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
<a class="logo" href="index.php">📚 MinhaEstante <span>· sua biblioteca pessoal</span></a>
<nav><a href="index.php">Início</a><a href="estante.php">Minha Estante</a><a href="sobre.php">Sobre</a><a href="contato.php">Contato</a><a class="destaque-menu" href="cadastrar.php">+ Novo livro</a></nav>
</header>
<main>
