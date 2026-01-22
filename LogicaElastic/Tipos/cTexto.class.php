<?php


namespace Elastic\Tipos;


use stdClass;

class Texto
{
	use CamposDinamicos;
	use SubCampo;
	public $type = 'text';
	public $analyzer;
	
	public function __construct(string $analyzer = 'spanish') {
		$this->analyzer = $analyzer;
	}
}