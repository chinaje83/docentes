<?php


namespace Elastic\Consultas;


use stdClass;

/**
 * Class ChildrenAgg
 *
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-children-aggregation.html
 */
class ChildrenAgg extends Agg {
	use CamposDinamicos;
	
	public $children;
	
	public function __construct(string $tipo) {
		$this->children = new stdClass();
		$this->children->type = $tipo;
	}
}