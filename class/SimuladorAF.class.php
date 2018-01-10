<?php

require_once('Automato.class.php');

/**
* Processa os dados passados, simulando os automatos
*/
class SimuladorAF
{
	public $stringsTest = Array();
	public $stringsAutomatos = Array();
	public $automatos = Array();

	public $arquivoSaida = '';

	public function __construct($dados) {
		$this->stringsTest = $dados['stringsTest'];
		$this->stringsAutomatos = $dados['automatos'];
    	
    	$this->processaStringsAutomatos();
    }

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

    			// nao deu certo
    			$transicoes[$estado] = array_combine($alfabeto, $automato[$i]);
    		}

    		$this->automatos[$arquivo] = new Automato($estados, $alfabeto, $transicoes, $inicio, $finais);
    	}
    }

    public function simularEmTodos($arquivo) {

    	foreach ($this->stringsTest as $arq => $cadeias) {
    		foreach ($cadeias as $cadeia) {
				echo ($this->automatos[$arquivo]->testarNFA($cadeia))? '"' . $cadeia . '" aceita ! '."\n" : '"' . $cadeia . '" nao aceita! '."\n";
			}
    	}
	}

}