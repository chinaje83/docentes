<?php


namespace Elastic\Tipos;


use stdClass;

class Autocompletar
{
	use CamposDinamicos;
	use SubCampo;
	public $type = 'search_as_you_type';
	public $analyzer;
	
	public function __construct(string $analyzer = 'spanish') {
		$this->analyzer = $analyzer;
	}
}