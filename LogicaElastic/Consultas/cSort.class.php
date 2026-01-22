<?php


namespace Elastic\Consultas;


use stdClass;

class Sort {
	use CamposDinamicos;
	
	public function __construct(string $campo, string $orden) {
		$this->{$campo} = new stdClass();
		$this->{$campo}->order = $orden;
	}
	
}