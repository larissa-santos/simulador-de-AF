<?php
/**
* Funções de pilha
*/
class Pilha
{
	public $elementos = array();

	public function __construct() { 
		array_push($this->elementos, 'z');
    }

    /* retira o elemento do topo da pilha */
    public function pop() {
    	return array_pop($this->elementos);
    }

    /* insere o elemento no topo da pilha */
    public function push($elem) {
    	array_push($this->elementos, $elem);
    }
}