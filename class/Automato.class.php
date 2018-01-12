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

    	$this->identificaAutomato();
    	
    	if ($this->isENFA) {
     		$this->converteENFAparaDFA();
     	} else if($this->isNFA) {
     		$this->converteNFAparaDFA();
     	}

     	return $this->testarDFA($cadeia);
     	
  		// try
		// {
		// }
		// catch (Exception $e)
		// {
		// 	var_dump($e);
		// 	return false; 	
		// } 

	} 

    public function identificaAutomato()
    {	
    	$this->isENFA = false;
		$this->isNFA = false;

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

    public function converteNFAparaDFA()
    {
    	$converter = true;
    	while ($converter) {
    		$converter = !($this->converteEstadosNaoDeterministicos());
    	}
    }
    /**
    * Procura pelos estados com mais de uma possibilidade de caminho
    * Transformando um estado nao deterministico em deterministico
    */
    private function converteEstadosNaoDeterministicos(){

    	$todosEstadosConvertidos = true;
    	$novosEstados = [];
    	$copiaTransicoes = $this->transicoes;
    	foreach ($this->estados as $estado) {
    		// verifica se a transicao possui mais de um estado
    		foreach ($this->alfabeto as $simbolo) {
    			if ( count($copiaTransicoes[$estado][$simbolo]) > 1) {
    				sort($copiaTransicoes[$estado][$simbolo]); //ordena valores
    				// une o array em uma string e adiciona nas variaveis necessarias
    				$novoEstado = implode(",", $copiaTransicoes[$estado][$simbolo]); 
		     		array_push($novosEstados, $novoEstado);
		     		$copiaTransicoes[$estado][$simbolo] = array($novoEstado); // marca processamento do estado
		     	}
    		}
    	}

    	foreach ($novosEstados as $novoEstado) {
    		if (!in_array($novoEstado, $this->estados)) {
     			array_push($this->estados, $novoEstado);
     			$auxEstados = explode(',', $novoEstado);

     			foreach ($this->alfabeto as $simbolo) {
     				$novaTransicao = [];
     				foreach ($auxEstados as $estado) {
     					if ($x = $this->transicaoDeEstado($estado, $simbolo)) {
     						$y = [];
     						foreach ($x as $valor) {
 	    						$y = array_merge($y, explode(',', $valor));
     						}
     						$novaTransicao = array_unique(array_merge($novaTransicao,$y));
     						sort($novaTransicao);
     					}

     					if (!in_array($novoEstado, $this->finais) &&
     							in_array($estado, $this->finais) ) {
     						array_push($this->finais, $novoEstado);
     					}
     				}

     				$copiaTransicoes[$novoEstado][$simbolo] = $novaTransicao;
     				if (count($novaTransicao) > 1) $todosEstadosConvertidos = false;
     			}
     		}
    	}
    	$this->transicoes = $copiaTransicoes;

    	return $todosEstadosConvertidos;
    }

    public function converteENFAparaDFA()
    {
    	// busca estado atingiveis pela transicao vazia
    	foreach ($this->estados as $estado) {
    		$atingiveisPorVazio[$estado] = $this->atingiveisPorTransicaoVazia([$estado]);
    	}
    	
    	$novoInicio = $this->inicio . '-' . $this->concatenaArray($atingiveisPorVazio[$this->inicio]);
	   	$novosFinais = $this->atualizaFinais([], [$this->inicio], $novoInicio);
	   	$novosEstados = [];
	   	array_push($novosEstados, $novoInicio);

    	$indice = 0;
    	while(count($novosEstados) > $indice ) {
	    	foreach ($this->alfabeto as $simbolo) {
	    		// busca estados atingiveis diretamente pelo novo estado
	    		$x = $this->atingiveisPorTransicao($novosEstados[$indice],$simbolo);
		    	// busca estados atingiveis pela transicao vazia atraves dos valores de $x
		    	$y = $this->juntaAtingiveisPorVazio($x, $atingiveisPorVazio);

		    	$novoEstado = $this->concatenaArray($x) . '-' . $this->concatenaArray($y);
		    	$novasTransicoes[$novosEstados[$indice]][$simbolo] = [$novoEstado];

		    	if (!in_array($novoEstado, $novosEstados)) {
		    		array_push($novosEstados, $novoEstado);

		    		$novosFinais = $this->atualizaFinais($novosFinais, $x, $novoEstado);
		    	}
	    	}
	    	$indice++;
	    }
		
		$this->inicio = $novoInicio;
		$this->estados = $novosEstados;
    	$this->transicoes = $novasTransicoes;
    	$this->finais = $novosFinais;	
    	// var_dump($this->estados);
    	// var_dump($this->transicoes);
    	// var_dump($this->finais);
		// die();
    }

    private function atualizaFinais ($novosFinais, $estados, $novoEstado) 
    {
    	// verifica se o novo estado deve ser um estado final
    	foreach ($estados as $estado) {
    		if (in_array($estado, $this->finais)) {
    			array_push($novosFinais, $novoEstado);
    		}
    	}
    	return $novosFinais;
    }

    private function juntaAtingiveisPorVazio($processarEstados, $atingiveis) 
    {
    	$conjunto = [];
    	foreach ($processarEstados as $estado) {
    		$conjunto = array_unique(
    							array_merge(
    								$conjunto, 
    								$atingiveis[$estado]
    						));
    	}
    	sort($conjunto);
    	return $conjunto;
    }

    private function atingiveisPorTransicaoVazia($processarEstados)
    {	
    	// busca todos estados atingiveis pela transicao vazia
    	$conjunto = [];
    	foreach ( $processarEstados as $estado) {
    		$conjunto = array_unique(
    							array_merge(
    								$conjunto, 
    								$this->transicaoDeEstado($estado, '&')
    						));
    	}
    	
    	// retira oq tem no $conjunto e nao tem no $processarEstados. Ou seja, os nao processados
    	$naoProcessados = array_diff($conjunto, $processarEstados);
    	if (count($naoProcessados) > 0) {
    		$conjunto = array_unique(
    							array_merge(
    								$conjunto, 
    								$this->atingiveisPorTransicaoVazia($naoProcessados)
    						));
    	}
    	sort($conjunto);
    	return $conjunto;
    }

    private function atingiveisPorTransicao($estadoAtual,$simbolo)
    {
    	
    	$string = str_replace('-', ',', $estadoAtual);
    	$processarEstados = explode(',',$string); 

    	// busca todos estados atingiveis pela transicao vazia
    	$conjunto = [];
    	foreach ( $processarEstados as $estado) {
    		$conjunto = array_unique(
    							array_merge(
    								$conjunto, 
    								$this->transicaoDeEstado($estado, $simbolo)
    						));
    	}
    	
    	return $conjunto;
    }

    public function concatenaArray($array, $delimitador = ',')
    {
    	sort($array);
    	return implode(",", array_unique($array));
    	
    }

}
