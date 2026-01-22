<?php


namespace Elastic\Consultas;


use stdClass;

/**
 * Class Term
 *
 * @package Elastic\Consultas
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html
 */
class Term implements iConsulta {
	use CamposDinamicos;
	
	private $campo;
	
	public function __construct(string $campo, $valor) {
		$this->campo = $campo;
		$this->{$campo} = new stdClass();
		$this->{$campo}->value = $valor;
	}
	
	public function setBoost(float $boost): self {
		$this->{$this->campo}->boost = $boost;
		return $this;
	}
	
}