<?php 

/**
* Processar automato
*/
class Automato
{
	public $estados = array();
	public $alfabeto = array();
	public $transicoes = array();
	public $inicio = '';
	public $finais = array();

	/**
	* @param array $estados (Q) é um conjunto finito de estados.
	* @param array $alfabeto (Σ) é um conjunto finito de símbolos, chamado de alfabeto do autômato.
	* @param array $transicoes (δ ou g) é a função de transição, isto é, δ: Q x Σ → Q.
	* @param string $inicio (q0) é o estado inicial, isto é, o estado do autômato antes de qualquer entrada ser processada.
	* @param array $finais (F) é um conjunto de estados de Q chamado de estados de aceitação.
	*/
	function __construct($estados, $alfabeto, $transicoes, $inicio, $finais) { 
		$this->estados = $estados;
		$this->alfabeto = $alfabeto;
		$this->transicoes = $transicoes;
		$this->inicio = $inicio;
		$this->finais = $finais;
    }

    function transicaoDeEstado($estado, $valor) {
    	if (isset($this->transicoes[$estado][$valor])) {
	   		return $this->transicoes[$estado][$valor];
	   	} else {
	   		return [];
	   	}
    }

    /**
    * Testa string no automato
    * @param string $cadeia
    */
    function testarAFD($cadeia) {

    	$estado_atual = $this->inicio;

        for ( $i=0; $i < strlen($cadeia) ; $i++ ) {
        	// verifica se o caracter pertence ao alfabeto do automato
		    if (in_array($cadeia[$i], $this->alfabeto)) {
		    	// verifica se existe uma transicao do estado atual ativada pelo caracter lido
			    if (isset($this->transicoes[$estado_atual][$cadeia[$i]])) {
			   		$estado_atual = $this->transicoes[$estado_atual][$cadeia[$i]];
			   	} else {
			   		return false;
			   	}
			} else {
				return false;
			}
		} 

		// verifica se é um estado de aceitação
		if (in_array($estado_atual, $this->finais)) {
			return true;
		} else {
			return false;
		}
    }

    /**
    * Testa string no automato nao-deterministico
    * @param string $cadeia
    */
    function testar($cadeia) {

    	$estadoAtual = [$this->inicio];

        for ( $i = 0; $i < strlen($cadeia) ; $i++ ) {
        	// verifica se o caracter pertence ao alfabeto do automato
		    if (in_array($cadeia[$i], $this->alfabeto)) {

		    	$proximosEstados = [];

				foreach ($estadoAtual as $estado) { 
					// verifica se existe uma transicao do estado atual ativada pelo caracter lido
					$proximosEstadosElemento = $this->transicaoDeEstado($estado, $cadeia[$i]);

					// verifica se existe uma transicao do estado atual ativada pela string vazia
					$proximosEstadosVazia = $this->transicaoDeEstado($estado, 'vazia');

					// nenhum proximo estado encontrado
					if (count($proximosEstadosElemento) == 0 && count($proximosEstadosVazia) == 0) {
						return false;
					}
					
					$proximosEstados = array_unique( array_merge(
						$proximosEstados, 
						$proximosEstadosVazia, 
						$proximosEstadosElemento
					));		   						
				}
				var_dump($cadeia[$i]);
				$estadoAtual = $proximosEstados;
				var_dump($estadoAtual);
			} else {
				return false;
			}
		} 

		// verifica se é um estado de aceitação
		if (count(array_intersect($estadoAtual,$this->finais)) > 0 ) {
			return true;
		}

		return false;
    } 
} 

/** reconhece cadeias com numero par de zero **/
$afd = new Automato(
	['S0','S1'], // $estados, 
	['0','1'], // $alfabeto, 
	array( //$transicoes
		'S0' => array('0' => ['S1'], '1' => ['S0']),
		'S1' => array('0' => ['S0'], '1' => ['S1'])
	), 
	'S0', // $inicio, 
	['S0'] // $finais
); 

$cadeias = ['0101','10100','111','010','000','011','1', ''];
foreach ($cadeias as $cadeia) {
	// echo ($afd->testar($cadeia))? '"' . $cadeia . '" aceita ! ' : '"' . $cadeia . '" não aceita! ';
}

/** determina se uma entrada contém um número par de 0s ou um número par de 1s  **/
$afn = new Automato(
	['S0','S1','S2','S3','S4'], // $estados 
	['0','1'], // $alfabeto
	array( // $transicoes
		'S0' => array('vazia' => ['S1','S3']),
		'S1' => array('0' => ['S2'], '1' => ['S1']),
		'S2' => array('0' => ['S1'], '1' => ['S2']),
		'S3' => array('0' => ['S3'], '1' => ['S4']),
		'S4' => array('0' => ['S4'], '1' => ['S3'])
	), 
	'S0', // $inicio
	['S1', 'S3'] // $finais
); 

$cadeias = ['0101','10100','111','010','000','011','1', ''];
$cadeias = ['10100'];
foreach ($cadeias as $cadeia) {
	echo ($afn->testar($cadeia))? '"' . $cadeia . '" aceita ! ' : '"' . $cadeia . '" não aceita! ';
}

