<?php


namespace Elastic\Tipos;


use stdClass;

class Join
{
	public $type = 'join';
	public $relations;
	
	public function __construct(string $padre, array $hijos) {
		$this->relations = new stdClass();
		$this->relations->{$padre} = $hijos;
		
	}
	
	public function addRelacion(string $padre, array $hijos): self {
		$this->relations->{$padre} = $hijos;
		return $this;
	}
	
}