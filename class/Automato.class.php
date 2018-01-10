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
	public $isENFA = false;
	public $isNFA = false;

	/**
	* @param array $estados (Q) é um conjunto finito de estados.
	* @param array $alfabeto (Σ) é um conjunto finito de símbolos, chamado de alfabeto do autômato.
	* @param array $transicoes (δ ou g) é a função de transição, isto é, δ: Q x Σ → Q.
	* @param string $inicio (q0) é o estado inicial, isto é, o estado do autômato antes de qualquer entrada ser processada.
	* @param array $finais (F) é um conjunto de estados de Q chamado de estados de aceitação.
	*/
	public function __construct($estados, $alfabeto, $transicoes, $inicio, $finais) { 
		$this->estados = $estados;
		$this->alfabeto = $alfabeto;
		$this->transicoes = $transicoes;
		$this->inicio = $inicio;
		$this->finais = $finais;
    }

    function transicaoDeEstado($estado, $valor) {
    	// verifica se o caracter pertence ao alfabeto do automato
		if (!in_array($valor, $this->alfabeto) && ($valor != '&')) {
		    throw new Exception("O '$valor' não pertence ao alfabeto do automato", 1);
		}

    	if (isset($this->transicoes[$estado][$valor])) {
	   		return $this->transicoes[$estado][$valor];
	   	} else {
	   		return [];
	   	}
    }

    /**
    * Testa string no automato finito deterministico
    * @param string $cadeia
    */
    function testarDFA($cadeia) {

    	$estado_atual = $this->inicio;

        for ( $i=0; $i < strlen($cadeia) ; $i++ ) {
        	// verifica se o caracter pertence ao alfabeto do automato
		    if (in_array($cadeia[$i], $this->alfabeto)) {
		    	// verifica se existe uma transicao do estado atual ativada pelo caracter lido
			    if (isset($this->transicoes[$estado_atual][$cadeia[$i]])) {
			   		$estado_atual = $this->transicoes[$estado_atual][$cadeia[$i]][0];
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

    	if ($ENFA) {
     		return $this->converteENFAparaDFA();
     	}

     	if ($NFA) {
     		return $this->testarNFA();
     	}

     	return $this->testarDFA();
     	
    	// estado => posicao atual na cadeia
    	$estadoAtual = array($this->inicio => 0);
    	$processa = true;

    	try
    	{

       		while ($processa) {

				$proximosEstados = [];

				foreach ($estadoAtual as $estado => $indice) {

					// verifica se existe uma transicao do estado atual ativada pelo caracter lido
					$proximosEstadosElemento = [];
					foreach ($this->transicaoDeEstado($estado, $cadeia[$indice]) as $proximoEstado) {
						$proximosEstadosElemento[$proximoEstado] = $indice + 1;
					}

					// verifica se existe uma transicao do estado atual ativada pela string vazia
					$proximosEstadosVazia = [];
					foreach ($this->transicaoDeEstado($estado, '&') as $proximoEstado) {
						$proximosEstadosVazia[$proximoEstado] = $indice;
					}

					// nenhum proximo estado encontrado
					if (count($proximosEstadosElemento) == 0 && count($proximosEstadosVazia) == 0) {
						//$proximosEstados = $proximosEstados;
						// ---- remove o estado atual do array 
					} else {
					

						// ---- verificar modo de fazer isso com as keys
						// $proximosEstados = array_unique( array_merge(
						// 	$proximosEstados, 
						// 	$proximosEstadosVazia, 
						// 	$proximosEstadosElemento
						// ));	
					}	   						
				}
				$processa = false;
				$estadoAtual = $proximosEstados;
				var_dump($estadoAtual);
			}
		}
		catch (Exception $e)
		{
			var_dump($e);
			return false; 	
		} 

		// verifica se é um estado de aceitação
		if (count(array_intersect($estadoAtual,$this->finais)) > 0 ) {
			return true;
		}

		return false;
    } 


    function testarNFA($cadeia) {

    	$estadoAtual = [$this->inicio];

        for ( $i = 0; $i < strlen($cadeia) ; $i++ ) {
        	// verifica se o caracter pertence ao alfabeto do automato
		    if (in_array($cadeia[$i], $this->alfabeto)) {

		    	$proximosEstados = [];

				foreach ($estadoAtual as $estado) { 
					// verifica se existe uma transicao do estado atual ativada pelo caracter lido
					$proximosEstadosElemento = $this->transicaoDeEstado($estado, $cadeia[$i]);

					// verifica se existe uma transicao do estado atual ativada pela string vazia
					$proximosEstadosVazia = $this->transicaoDeEstado($estado, '&');

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

    public function identificaAutomato()
    {	
    	foreach ($this->estados as $estado) {
    		// verifica se a transicao possui mais de um estado
    		foreach ($this->alfabeto as $simbolo) {
    			if ( count($this->transicoes[$estado][$simbolo]) > 1) {
		     		$this->isNFA = true;
		     	}
    		}
    		// verifica se existe a transicao para vazio
    		if ( isset($this->transicoes[$estado]["&"]) ) {
	     		$this->isENFA = true;
	     	}
    	}
    }

    public function converteENFAparaDFA()
    {
    	$processa = function($Dstates) {
    		foreach ($Dstates as $indice => $estado) {
    			if (!$estado['marcado']) return true;
    		}

    		return false;
    	};
    	
    	$eClosure = function($estados) {
    		$estadosProximos = array();
    		foreach ($estados as $estado) {
    			$estadosProximos = array_unique( array_merge(
					$estadosProximos, 
					(isset($this->transicoes[$estado]['&']))? $this->transicoes[$estado]['&'] : []
				));	
    		}

    		return $estadosProximos;
    	};

    	$move = function($estados, $simbolo) {
    		$estadosProximos = array();
    		foreach ($estados as $estado) {
    			$estadosProximos = array_unique( array_merge(
					$estadosProximos, 
					(isset($this->transicoes[$estado][$simbolo]))? $this->transicoes[$estado][$simbolo] : []
				));	
    		}

    		return $estadosProximos;
    	};

    	// agrupar todos os estado alcancados pela transicao de vazio a partir do estado selecionado
    	$Dstates = $Dtransicoes = array();
    	$i = 0;
    	array_push($Dstates, array( 'nome' => 'Q0', 'marcado' => false, 'estados' => $eClosure([$this->inicio])));
    	
    	
    	while ($processa($Dstates)) {

    		// marcando estados
    		foreach ($Dstates as $indice => $info) {
    			if ( !$Dstates[$indice]['marcado']) {
    				$Dstates[$indice]['marcado'] = true;
    				$t = $Dstates[$indice];
    				break;
    			}
    		}

    		foreach ($this->alfabeto as $simbolo) {
	    		$u = $eClosure($move($t['estados'], $simbolo));
	    		// var_dump($move($t, $simbolo));
	    		
	    		if (count($u) > 0) {
		    		// verifica se é um novo estado descoberto
		    		$novoEstado = true;
		    		foreach ($Dstates as $estado) {	
			    		if (count($estado['estados']) === count(array_intersect($estado['estados'], $u))) {
			    			$novoEstado = false;
			    		}
			    	}
			    	
			    	if ($novoEstado) {
			    		$i++;
			    		array_push($Dstates, array( 'nome' => 'Q'.$i , 'marcado' => false, 'estados' => $u));
			    	}

		    		$Dtransicoes[$t['nome']][$simbolo] = $u;
		    		// var_dump($Dtransicoes);
		    	}
	    	}	
    	}
    	
    	// $Dstates = array_keys($Dstates);
    	var_dump($Dtransicoes);
    	var_dump($Dstates);
    }


}
