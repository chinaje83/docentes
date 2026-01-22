<?php


namespace Elastic\Consultas;


use stdClass;

/**
 * Class Exists
 *
 * @property string $field
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html
 */
class Exists implements iConsulta {
	use CamposDinamicos;
	
	public function __construct(string $campo) {
		$this->field = $campo;
	}
	
}