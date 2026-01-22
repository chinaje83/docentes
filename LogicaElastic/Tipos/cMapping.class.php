<?php


namespace Elastic\Tipos;


use stdClass;

class Mapping
{
	use Propiedades;
	public $dynamic;
	
	public function __construct(string $dynamic = 'strict') {
		$this->dynamic = $dynamic;
		$this->properties = new stdClass();
	}
	
}