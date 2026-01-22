<?php


namespace Elastic\Consultas;


use stdClass;

/**
 * Class Terms
 *
 * @package Elastic\Consultas
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html
 */
class Terms implements iConsulta {
	use CamposDinamicos;
	
	private $campo;
	
	public function __construct(string $campo, array $valor) {
		$this->campo = $campo;
		$this->{$campo} = $valor;
	}
	
	public function setBoost(float $boost): self {
		$this->{$this->campo}->boost = $boost;
		return $this;
	}
	
}