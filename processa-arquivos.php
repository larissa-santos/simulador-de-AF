<?php
/* C:\xampp\php\php.exe C:\xampp\htdocs\simulador-de-AF\processa-arquivos.php C:\xampp\htdocs\simulador-de-AF\src\entradas.zip */

// $arqEntrada = __DIR__ . '\src\entradas.zip';
require_once('class/ManipulaArquivos.class.php');
require_once('class/SimuladorAF.class.php');
require_once('class/Automato.class.php');

if (!isset($argv[1])) {
	die('Nenhum argumento com arquivo de entrada encontrado.');		
}

$conteudoZip = ManipulaArquivos::leituraDeArquivoZip($argv[1]);
// var_dump($conteudoZip);
// die();
$simulador = new SimuladorAF($conteudoZip);
// var_dump($simulador);
// $simulador->simularEmTodos('ex_dfa01');
$simulador->simularEmTodos('ex_nfa01');
// $simulador->simularEmTodos('ex_epsilon_nfa');
die();

// /** reconhece cadeias com numero par de zero **/
// $afd = new Automato(
// 	['S0','S1'], // $estados, 
// 	['0','1'], // $alfabeto, 
// 	array( //$transicoes
// 		'S0' => array('0' => ['S1'], '1' => ['S0']),
// 		'S1' => array('0' => ['S0'], '1' => ['S1'])
// 	), 
// 	'S0', // $inicio, 
// 	['S0'] // $finais
// ); 

// $cadeias = ['0101','10100','111','010','000','011','1', ''];
// foreach ($cadeias as $cadeia) {
// 	echo ($afd->testarAFD($cadeia))? '"' . $cadeia . '" aceita ! ' : '"' . $cadeia . '" não aceita! ';
// }

// /** determina se uma entrada contém um número par de 0s ou um número par de 1s  **/
// $afn = new Automato(
// 	['S0','S1','S2','S3','S4'], // $estados 
// 	['0','1'], // $alfabeto
// 	array( // $transicoes
// 		'S0' => array('vazia' => ['S1','S3']),
// 		'S1' => array('0' => ['S2'], '1' => ['S1']),
// 		'S2' => array('0' => ['S1'], '1' => ['S2']),
// 		'S3' => array('0' => ['S3'], '1' => ['S4']),
// 		'S4' => array('0' => ['S4'], '1' => ['S3'])
// 	), 
// 	'S0', // $inicio
// 	['S1', 'S3'] // $finais
// ); 

// $cadeias = ['0101','10100','111','010','000','011','1', ''];
// $cadeias = ['10100'];
// foreach ($cadeias as $cadeia) {
// 	// echo ($afn->testar($cadeia))? '"' . $cadeia . '" aceita ! ' : '"' . $cadeia . '" não aceita! ';
// }

// var_dump($afn->identificaAutomato());
