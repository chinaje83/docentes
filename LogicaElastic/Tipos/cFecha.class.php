<?php


namespace Elastic\Tipos;


use stdClass;

class Fecha
{
	use CamposDinamicos;
	use SubCampo;
	public $type = 'date';
	public $format;
	
	public function __construct(string $format = 'yyyy-MM-dd HH:mm:ss||strict_date||epoch_millis') {
		$this->format = $format;
	}
}