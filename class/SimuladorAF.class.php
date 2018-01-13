<?php
require_once('Automato.class.php');

/**
* Processa os dados passados, simulando os automatos e criando arquivos de saida
*/
class SimuladorAF
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
    		$alfabeto = $automato[0];
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
    			unset($automato[$i][0]);

    			// combina os simbolos do alfabeto com as transicoes correspondentes
    			$transicoesEmString = array_combine($alfabeto, $automato[$i]);
    			// transforma as strings de transicoes em array (mais facil processar)
    			foreach ($transicoesEmString as $simbolo => $transicao) {
					// apaga os caracteres {}
					$transicao = preg_replace('/({|})/u', '', $transicao);
					// espara a string em array a cada , encontrado e elimina as posições vazias
					$transicoes[$estado][$simbolo] = array_diff(explode(',', $transicao),['']);
    			}
    		}

    		// removendo o vazio do alfabeto
    		if (($indice = array_search('&', $alfabeto)) !== false) {
			    unset($alfabeto[$indice]);
			}

    		$this->automatos[$arquivo] = new Automato($estados, $alfabeto, $transicoes, $inicio, $finais);
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
			$fp = fopen($arquivo . '-' . $arq . '.txt', "a");
			 
    		foreach ($cadeias as $cadeia) {
				$escreve = fwrite($fp, ($this->automatos[$arquivo]->testar($cadeia))? 
					'"' . $cadeia . '" aceita ! '."\n" : '"' . $cadeia . '" nao aceita! '."\n");

				// echo (($this->automatos[$arquivo]->testar($cadeia))? 
				// 	'"' . $cadeia . '" aceita ! '."\n" : '"' . $cadeia . '" nao aceita! '."\n");
			}
			
			fclose($fp); // fecha o arquivo
			echo 'Arquivo criado: ' . $arquivo . '-' . $arq . ".txt\n";
    	}
	}
}