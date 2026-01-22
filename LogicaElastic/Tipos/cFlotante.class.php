<?php


namespace Elastic\Tipos;


class Flotante
{
	use CamposDinamicos;
	public $type = 'scaled_float';
	public $scaling_factor;
	
	public function __construct(int $scaling_factor = 100) {
		$this->scaling_factor = $scaling_factor;
	}
}