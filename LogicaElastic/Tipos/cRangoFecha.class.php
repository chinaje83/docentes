<?php


namespace Elastic\Tipos;


class RangoFecha
{
	use CamposDinamicos;
	public $type = 'date_range';
	public $format;
	
	public function __construct(string $format = 'yyyy-MM-dd HH:mm:ss||strict_date||epoch_millis') {
		$this->format = $format;
	}
}