<?php


namespace Elastic\Consultas;


use stdClass;

/**
 * Class Match
 *
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html
 */
class cMatch implements iConsulta {
	use CamposDinamicos;
	
	public function __construct(string $campo, string $valor) {
		$this->{$campo} = new stdClass();
		$this->{$campo}->query = $valor;
	}
	
}