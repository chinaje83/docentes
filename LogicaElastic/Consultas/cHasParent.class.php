<?php


namespace Elastic\Consultas;


use Bigtree\ExcepcionLogica;
use stdClass;

/**
 * Class HasParent
 *
 * @property bool $ignore_unmapped
 * @property bool $score
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-has-parent-query.html
 */
class HasParent implements iConsulta {
	use CamposDinamicos;
	
	public $query;
	public $parent_type;
	
	/**
	 * HasParent constructor.
	 *
	 * @param string $parent_type
	 * @param Query  $query
	 */
	public function __construct(string $parent_type, Query $query) {
		$this->parent_type = $parent_type;
		$this->query = $query;
	}
	
	/**
	 * @param bool $score
	 * @return self
	 */
	public function setScoreMode(bool $score = false): self {
		$this->score = $score;
		return $this;
	}
	
	/**
	 * @param bool $ignore_unmapped
	 * @return self
	 */
	public function setIgnoreUnmapped(bool $ignore_unmapped = false): self {
		$this->ignore_unmapped = $ignore_unmapped;
		return $this;
	}
	
}