<?php

require_once('Pilha.class.php');

/**
* Processar automato
*/
class AutomatoPDA
{
	public $estados = array();
	public $alfabeto = array();
    public $alfabetoPilha = array();
	public $transicoes = array();
	public $inicio = '';
	public $finais = array();
    public $pilha;
	public $isENFA = false;
	public $isNFA = false;

	/**
	* @param array $estados (Q) é um conjunto finito de estados.
	* @param array $alfabeto (Σ) é um conjunto finito de símbolos, chamado de alfabeto do autômato.
    * @param array $alfabetoPilha (Γ) é um conjunto finito de símbolos, chamado de alfabeto da pilha.
	* @param array $transicoes (δ ou g) é a função de transição, isto é, δ: Q x Σ → Q.
	* @param string $inicio (q0) é o estado inicial, isto é, o estado do autômato antes de qualquer entrada ser processada.
	* @param array $finais (F) é um conjunto de estados de Q chamado de estados de aceitação.
	*/
	public function __construct($estados, $alfabeto, $alfabetoPilha, $transicoes, $inicio, $finais) { 
		$this->estados = $estados;
		$this->alfabeto = $alfabeto;
        $this->alfabetoPilha = $alfabetoPilha;
		$this->transicoes = $transicoes;
		$this->inicio = $inicio;
		$this->finais = $finais;
    }

    /**
    * Metodo transicaoDeEstado()
    * Verifica se existe uma transição do estado $estado com o 
    * símbolo lido $valor 
    * @param string $estado com o estado de transição
    * @param string $valor com o símbolo de transição
    * @return array com os estados para onde o símbolo leva o automato
    */
    function transicaoDeEstado($estado, $valor) {
    	// verifica se o símbolo pertence ao alfabeto do automato
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
    * Metodo testarDFA()
    * Testa string no automato finito deterministico
    * @param string $cadeia que será testada no automato
    * @return boolean com o resultado do teste
    */
    function testarDFA($cadeia) {

    	$estadoAtual = $this->inicio;
    	// percorre todos os simbolos da string ou para em caso de rejeição
        for ( $i=0; $i < strlen($cadeia) ; $i++ ) {
        	// verifica se o caracter pertence ao alfabeto do automato
		    if (in_array($cadeia[$i], $this->alfabeto)) {
		    	// verifica se existe uma transicao do estado atual ativada pelo caracter lido
			    if (isset($this->transicoes[$estadoAtual][$cadeia[$i]])) {
			   		$estadoAtual = $this->transicoes[$estadoAtual][$cadeia[$i]][0];
			   	} else {
			   		return false;
			   	}
			} else {
				return false;
			}
		} 

		// verifica se o estado atual é um estado de aceitação
		if (in_array($estadoAtual, $this->finais)) {
			return true;
		} else {
			return false;
		}
    }

    function manipulaPilha($acao) {
        for ( $i = strlen($acao)-1 ; $i >= 0 ; $i-- ) {
            if ($acao[$i] !== '&') {
                $this->pilha->push($acao[$i]);
            }
        }
    }

    /**
    * Metodo testarPDA()
    * Testa string no automato finito de pilha
    * @param string $cadeia que será testada no automato
    * @return boolean com o resultado do teste
    */
    function testarPDA($cadeia) {
        $this->pilha = new Pilha();
        $estadoAtual = $this->inicio;
        $topoDaPilha = '';
        // percorre todos os simbolos da string ou para em caso de rejeição
        for ( $i=0; $i < strlen($cadeia) ; $i++ ) {
            // verifica se o caracter pertence ao alfabeto do automato
            if (in_array($cadeia[$i], $this->alfabeto)) {
                // verifica se existe uma transicao do estado atual ativada pelo caracter lido da cadeia e da pilha

                $topoDaPilha = $this->pilha->pop();
                if (isset($this->transicoes[$estadoAtual][$cadeia[$i]][$topoDaPilha])) {

                    // acaoAtual = estadoAtual/InserirNaPilha
                    $acaoAtual = $this->transicoes[$estadoAtual][$cadeia[$i]][$topoDaPilha][0];

                    $acaoAtual = explode('/', $acaoAtual);

                    $estadoAtual = $acaoAtual[0];

                    $this->manipulaPilha($acaoAtual[1]);
                    
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } 

        // verifica se o estado atual é um estado de aceitação
        if (in_array($estadoAtual, $this->finais)) {
            return true;
        } else {
            // verifica se existe uma transição quando não tem mais caracteres
            $topoDaPilha = $this->pilha->pop();
            if (isset($this->transicoes[$estadoAtual]['&'][$topoDaPilha])) {
                // acaoAtual = estadoAtual/InserirNaPilha
                $acaoAtual = $this->transicoes[$estadoAtual]['&'][$topoDaPilha][0];
                $acaoAtual = explode('/', $acaoAtual);
                $estadoAtual = $acaoAtual[0];

                $this->manipulaPilha($acaoAtual[1]);
                
                // verifica se o estado atual é um estado de aceitação
                if (in_array($estadoAtual, $this->finais)) {
                    return true;
                } else {
                    return false;
                }
            }

            return false;
        }
    }

    /** 
    * Metodo testar()
    * Identifica o tipo de automato, executa as ações necessarias de conversão para DFA
    * e por fim testa se a string $cadeia é aceita pela automato
    * @param string $cadeia
    * @return boolean com o resultado de aceitãção retornado do automato para a string
    */
    function testar($cadeia) 
    {
    	try 
    	{
	    	$this->identificaAutomato();
	    	
	    	// if ($this->isENFA) {
	     // 		$this->converteENFAparaDFA();
	     // 	} else if($this->isNFA) {
	     // 		$this->converteNFAparaDFA();
	     // 	}

	     	return $this->testarPDA($cadeia);
	    }
		catch (Exception $e)
		{
			var_dump($e);
			return false; 	
		}
	} 

	/**
	* Metodo identificaAutomato()
	* Verifica nos estados de transição do automato que tipo de 
	* automato finito é, e o classifica.
	*/
    public function identificaAutomato()
    {	
    	$this->isENFA = false;
		$this->isNFA = false;

    	foreach ($this->estados as $estado) {
    		// verifica se a transicao possui mais de um estado
    		foreach ($this->alfabeto as $simbolo) {
                foreach ($this->alfabetoPilha as $simboloPilha) {
        			if ( isset($this->transicoes[$estado][$simbolo][$simboloPilha]) &&
                            count($this->transicoes[$estado][$simbolo][$simboloPilha]) > 1) {
    		     		
                        $this->isNFA = true;
    		     	}
                }
    		}
    		// verifica se existe a transicao para vazio
    		if ( isset($this->transicoes[$estado]["&"]) ) {
	     		$this->isENFA = true;
	     	}
    	}
    }

    /**
    * Metodo converteNFAparaDFA()
    * Converte os estados nao deterministicos ate que nao haja mais nenhum
    * caminho nao deterministico no automato
    */
    public function converteNFAparaDFA()
    {
    	$converter = true;
    	while ($converter) {
    		$converter = !($this->converteEstadosNaoDeterministicos());
    	}
    }

    /**
    * Metodo converteEstadosNaoDeterministicos()
    * Procura pelos estados com mais de uma possibilidade de caminho
    * Transformando um estado nao deterministico em deterministico
    * @return boolean =FALSE caso o processo de conversao deva ser repetido
    */
    private function converteEstadosNaoDeterministicos()
    {
    	$todosEstadosConvertidos = true;
    	$novosEstados = [];
    	$copiaTransicoes = $this->transicoes;

    	// procura os estados nao deterministicos
    	foreach ($this->estados as $estado) {
    		// verifica se a transicao possui mais de um estado
    		foreach ($this->alfabeto as $simbolo) {
    			if ( count($copiaTransicoes[$estado][$simbolo]) > 1) {
    				sort($copiaTransicoes[$estado][$simbolo]); //ordena valores
    				// une o array em uma string e adiciona nas variaveis necessarias
    				$novoEstado = implode(",", $copiaTransicoes[$estado][$simbolo]); 
		     		$copiaTransicoes[$estado][$simbolo] = [$novoEstado]; // marca processamento do estado
			     	array_push($novosEstados, $novoEstado);
		     	}
    		}
    	}

    	// varrendo todos os estados novos
    	foreach ($novosEstados as $novoEstado) {
    		// verifica se o estado já está entre os estados do automato
    		if (!in_array($novoEstado, $this->estados)) {
     			array_push($this->estados, $novoEstado);
     			$auxEstados = explode(',', $novoEstado); // transforma a string em array

     			// verifica estados atingiveis a partir do novo 
     			foreach ($this->alfabeto as $simbolo) {
     				$novaTransicao = [];
     				foreach ($auxEstados as $estado) {
     					if ($x = $this->transicaoDeEstado($estado, $simbolo)) {
     						$y = [];
     						// une todos os estados encontrados em um array
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

    /**
    * Metodo converteENFAparaDFA()
    * Converte o automato NFA-e para um PDA deterministico
    */
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
    }

    /**
    * Metodo atualizaFinais()
    * Verifica se o novo estado se encaixa no quadro de estado final
    * da nova versão do automato
    * @param array $novosFinais com os estados finais já encontrados
    * @param array $estados estados da primeira parte do novo estado
    * @param string $novoEstado estado que esta sendo processado
    * @return Array
    */
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

    /**
    * Metodo juntaAtingiveisPorVazio()
    * Junta todos os estados alcançaveis pela transição vazia a partir dos estados $processarEstados
    * @param array $processarEstados estados a serem verificados
    * @param array $atingiveis matriz com o conjunto de estados atingiveis pela
    * transicao vazia de todos os estados iniciais do automato
    * @return Array com o conjunto de estados atingiveis pela transicao vazia
    */
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

    /**
    * Metodo atingiveisPorTransicaoVazia()
    * Busca por todos os estados alcançaveis recursivamente
    * pela transição vazia a partir dos estados passados.
    * @param Array $processarEstados estados a serem analizados
    * @return Array com os estados atingiveis pela transição vazia
    */
    private function atingiveisPorTransicaoVazia($processarEstados)
    {	
    	// busca todos estados atingiveis pela transicao vazia
    	$conjunto = [];
    	foreach ( $processarEstados as $estado) {

            // if ( isset($this->transicaoDeEstado($estado, '&')) ) {
        		$conjunto = array_unique(
        							array_merge(
        								$conjunto, 
        								$this->transicaoDeEstado($estado, '&')
        						));
            // }
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
    	sort($conjunto); // ordena vetor
    	return $conjunto;
    }

    /**
    * Metodo atingiveisPorTransicao()
    * Avalia os estados usados para montar o novo estado e busca os estados atingiveis
    * por esses estados com o simbolo do alfabeto avaliado.
    * @param string $estadoAtual com todos os estados que devem ser avaliados
    * @param string $simbolo simbolo de transicao avalido nas transições de estado
    * @return Array com os valores de estados encontrados com as transições possiveis
    */
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

    /**
    * Metodo concatenaArray()
    * Concatena os valores de um array em uma string com separação de um caracter especificado
    * @param array $array com valores a serem concatenados
    * @param string $delimitador com o caracter que ficará entre os valores do array
	* @return string formada pela concatenação dos valores
    */
    public function concatenaArray($array, $delimitador = ',')
    {
    	sort($array); // ordena o array antes de concatenar
    	return implode(",", array_unique($array));
    }
}
