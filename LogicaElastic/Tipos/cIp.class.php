<?php


namespace Elastic\Tipos;


use stdClass;

/**
 * @property stdClass fields
 */
class Ip
{
	use CamposDinamicos;
	public $type = 'ip';
	
	public function addField(string $nombre, $valor): self
	{
		$this->fields = new stdClass();
		$this->fields->{$nombre} = $valor;
		return $this;
	}
}