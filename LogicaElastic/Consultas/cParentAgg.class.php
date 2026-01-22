<?php


namespace Elastic\Consultas;


use stdClass;

/**
 * Class ParentAgg
 *
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-parent-aggregation.html
 */
class ParentAgg extends Agg {
	use CamposDinamicos;
	
	public $parent;
	
	public function __construct(string $tipo) {
		$this->parent = new stdClass();
		$this->parent->type = $tipo;
	}
}