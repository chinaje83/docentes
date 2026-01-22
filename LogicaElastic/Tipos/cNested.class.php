<?php


namespace Elastic\Tipos;


use stdClass;

class Nested
{
	use Propiedades;
	public $type = 'nested';
	
	public function __construct() {
		$this->properties = new stdClass();
	}
}