<?php
require_once('AutomatoPDA.class.php');

/**
* Processa os dados passados, simulando os automatos e criando arquivos de saida
*/
class SimuladorPDA
{
	public $stringsTest = Array();
	public $stringsAutomatos = Array();
	public $automatos = Array();

	public function __construct($dados) {
		$this->stringsTest = $dados['stringsTest'];
		$this->stringsAutomatos = $dados['automatos'];
    	
    	$this->processaStringsAutomatos();
    }

    /**
    * Metodo processaStringsAutomatos()
	* Processa as strings lidas e as transforma em vetores
	* para se tornarem a entidade Automato
    */
    protected function processaStringsAutomatos(){
    	foreach ($this->stringsAutomatos as $arquivo => $automato) {
    		
            $alfabetoAF = [];
            $alfabetoPilha = [];
            
            // separa os elementos analisados nas transições nos alfabetos correspondentes
            foreach ($automato[0] as $value) {
                $value = explode('/', $value);
                if (!in_array($value[0], $alfabetoAF))
                    array_push($alfabetoAF, $value[0]);
                if (!in_array($value[1], $alfabetoPilha))
                    array_push($alfabetoPilha, $value[1]);
            }

    		$estados = [];
    		$transicoes = [];
    		$inicio = '';
    		$finais = [];

    		for ($i=1; $i < count($automato); $i++) {
    			$estado = $automato[$i][0];

    			// verifica se é estado final
    			$fnl = false;
    			if (strstr($estado, '*')) { 
    				$fnl = true;
    				$estado = str_replace('*', '', $estado);
    			}
    			// verifica se é o estado inicial
    			$ini = false;
    			if (strstr($estado, '>')) {
    				$ini = true;
    				$estado = str_replace('>', '', $estado);
    			}

    			if ($fnl) array_push($finais, $estado);
    			if ($ini) $inicio = $estado;

    			array_push($estados, $estado);
    			unset($automato[$i][0]); // destroi variavel

    			// combina os simbolos do alfabeto com as transicoes correspondentes
    			$transicoesEmString = array_combine($automato[0], $automato[$i]);
    			// transforma as strings de transicoes em array (mais facil processar)
    			foreach ($transicoesEmString as $simbolos => $transicao) {
                    // separa os simbolos da entrada e o top da pilha
                    $simbolos = explode('/', $simbolos);

					// apaga os caracteres {}
					$transicao = preg_replace('/({|})/u', '', $transicao);
					// separa a string em array a cada , encontrado e elimina as posições vazias
                    $acao = array_diff(explode(',', $transicao),['']);

                    // se nao tiver transicao, nao insere
                    if (count($acao) > 0)
					   $transicoes[$estado][$simbolos[0]][$simbolos[1]] = $acao;
    			}
    		}

    		// removendo o vazio do alfabeto
    		if (($indice = array_search('&', $alfabetoAF)) !== false) {
			    unset($alfabetoAF[$indice]);
			}

            if (($indice = array_search('&', $alfabetoPilha)) !== false) {
                unset($alfabetoPilha[$indice]);
            }

    		$this->automatos[$arquivo] = new AutomatoPDA($estados, $alfabetoAF, $alfabetoPilha, $transicoes, $inicio, $finais);
    	}
    }

    /**
    * Metodo simularTodos()
    * Percorre todos os arquivos de automato lidos e os simula.
    */
    public function simularTodos()
    {
    	foreach ($this->automatos as $arq => $automato) {
    		$this->simular($arq);
    	}
    }

    /**
    * Metodo simular()
    * Simula automato combinando com todos arquivos de entrada, 
    * gerando arquivo como saida
    * @param string $arquivo com o nome do automato que será simulado
    */
    public function simular($arquivo) 
    {
    	foreach ($this->stringsTest as $arq => $cadeias) {
    		// abre/cria o arquivo para escrita
			// $fp = fopen($arquivo . '-' . $arq . '.txt', "a");
			 
    		foreach ($cadeias as $cadeia) {
				// $escreve = fwrite($fp, ($this->automatos[$arquivo]->testar($cadeia))? 
				// 	'"' . $cadeia . '" aceita ! '."\n" : '"' . $cadeia . '" nao aceita! '."\n");

				echo (($this->automatos[$arquivo]->testar($cadeia))? 
					'"' . $cadeia . '" aceita ! '."\n" : '"' . $cadeia . '" nao aceita! '."\n");
    // var_dump(($this->automatos[$arquivo]->testar("0011")));
                // die();
			}
			
			// fclose($fp); // fecha o arquivo
			// echo 'Arquivo criado: ' . $arquivo . '-' . $arq . ".txt\n";
     	}
	}
}