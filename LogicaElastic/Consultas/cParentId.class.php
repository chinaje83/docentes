<?php


namespace Elastic\Consultas;


use stdClass;

/**
 * Class Term
 *
 * @package Elastic\Consultas
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html
 *
 * @property bool ignore_unmapped
 */
class ParentId implements iConsulta {
	use CamposDinamicos;
	
	public $type;
	public $id;

	public function __construct(string $type, $id) {
		$this->type = $type;
		$this->id = $id;
	}
	
	public function setIgnoreUnmapped(bool $valor=true): self {
		$this->ignore_unmapped = $valor;
		return $this;
	}
	
}