<?php

/**
* Processa e cria arquivos
*/
class ManipulaArquivos
{

	/**
	* Metodo leituraDeArquivoZip()
	* Abre o arquivo .zip e processa arquivo por arquivo, 
	* quebrando em array cada quebra de linha
	* @param string $arq Caminho do arquivo .zip que será processado
	* @return array $retorno é o conjunto de arquivos contidos em $arq, 
	* processados e separados em automatos ('AF') e entradas ('entradas')
	*/
    public static function leituraDeArquivoZip($arq) {
    	$retorno = false;

		if ($zip = zip_open($arq)) {
			$index = 0;
			$retorno = array();

			// leitura de arquivos txt
		    while ($zip_entry = zip_read($zip)) {

		        preg_match('/([a-zA-Z0-9_-])*.(\w{3})$/',zip_entry_name($zip_entry), $match);
		        $retorno[$index]['nome'] = $match[0];

		        // classifica o arquivo do pacote em entradas de teste ou tabela de transicao do AF
		        if ( substr_count($retorno[$index]['nome'], 'entrada') > 0 || 
		        	substr_count($retorno[$index]['nome'], 'string') > 0 ) {
		        	$retorno[$index]['tipo'] = 'entradas';
		        } else {
		        	$retorno[$index]['tipo'] = 'AF';
		        }

		        // abre arquivo para leitura
		        if (zip_entry_open($zip, $zip_entry, "r")) {
		            $retorno[$index]['conteudo'] = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
		            
		            zip_entry_close($zip_entry);
		        }

		        $index++;
		    }
   
		    zip_close($zip);
		    $retorno = self::processaStrings($retorno);
		}

		return $retorno;
	}

	/**
	* Metodo processaStrings()
	* 
	* @param string $dados 
	* @return array $retorno 
	*/
	protected static function processaStrings($dados) {
		$retorno = Array();

		foreach ($dados as $arquivo) {
       		if ($arquivo['tipo'] == 'entradas') {
       			$retorno['stringsTest'][substr($arquivo['nome'], 0, -4)] = $arquivo['conteudo'];
       		} else {
       			$retorno['automatos'][substr($arquivo['nome'], 0, -4)] = $arquivo['conteudo'];
       		}
       	}

		// processa strings que serão usadas nos testes
		foreach ($retorno['stringsTest'] as $arquivo => $conteudo) {
			$retorno['stringsTest'][$arquivo] = self::removeQuebraDeLinha($conteudo);
		}
		
		// processa strings que configuram o automato
		foreach ($retorno['automatos'] as $arquivo => $conteudo) {
			$automato = self::removeQuebraDeLinha($conteudo);

			$aux = [];
			foreach ($automato as $key => $string) {
    			// substitui os espaços em branco por -
    			$string = preg_replace('/(\s)+/', '-', $string);
    			// espara a string em array a cada - encontrado e elimina as posições vazias
    			$aux[$key] = array_diff(explode('-', $string),['']);
    		}

    		$retorno['automatos'][$arquivo] = $aux;
		}	

		return $retorno;	
	}

	/** 
	* Metodo removeQuebraDeLinha()
	* Substitui as quebras de linha por <br/>, transforma a string em array onde tem <br/>
	* e remove as posicoes vazias do array gerado
	* @param string $string
	* @return array com as linhas do arquivo em cada posição
	*/
	protected static function removeQuebraDeLinha($string) {
		return array_diff(explode('<br/>', preg_replace('/\R/u', '<br/>', $string)), ['']);
	}
}