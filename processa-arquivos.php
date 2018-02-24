<?php
require_once('class/ManipulaArquivos.class.php');
require_once('class/SimuladorAF.class.php');
require_once('class/Automato.class.php');

if (!isset($argv[1])) {
	die('Nenhum argumento com arquivo de entrada encontrado.');		
}

$conteudoZip = ManipulaArquivos::leituraDeArquivoZip($argv[1]);

$simulador = new SimuladorAF($conteudoZip);
$simulador->simularTodos();