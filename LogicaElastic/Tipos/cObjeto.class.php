<?php


namespace Elastic\Tipos;


use stdClass;

class Objeto
{
	use Propiedades;
	public $type = 'object';
	
	public function __construct() {
		$this->properties = new stdClass();
	}
	
}