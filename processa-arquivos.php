<?php
/* C:\xampp\php\php.exe C:\xampp\htdocs\simulador-de-AF\processa-arquivos.php C:\xampp\htdocs\simulador-de-AF\src\entradas.zip */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('class/ManipulaArquivos.class.php');
require_once('class/SimuladorAF.class.php');
require_once('class/Automato.class.php');

if (!isset($argv[1])) {
	die('Nenhum argumento com arquivo de entrada encontrado.');		
}

$conteudoZip = ManipulaArquivos::leituraDeArquivoZip($argv[1]);

$simulador = new SimuladorAF($conteudoZip);
$simulador->simularTodos();

// $simulador->simular('ex_dfa01');
// $simulador->simular('ex_nfa01');
// $simulador->simular('ex_epsilon_nfa');
// var_dump($simulador);