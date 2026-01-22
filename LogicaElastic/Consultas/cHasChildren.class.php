<?php


namespace Elastic\Consultas;


use Bigtree\ExcepcionLogica;
use stdClass;

/**
 * Class HasChildren
 *
 * @property bool   $ignore_unmapped
 * @property string $score_mode
 * @property int    $max_children
 * @property int    $min_children
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-has-child-query.html
 */
class HasChildren implements iConsulta {
	use CamposDinamicos;
	
	public $query;
	public $type;
	
	/**
	 * HasChildren constructor.
	 *
	 * @param string $type
	 * @param Query  $query
	 */
	public function __construct(string $type, Query $query) {
		$this->type = $type;
		$this->query = $query;
	}
	
	/**
	 * @param string $score_mode
	 * @return $this
	 * @throws ExcepcionLogica
	 */
	public function setScoreMode(string $score_mode = 'none'): self {
		if (!in_array($score_mode, ['avg', 'max', 'min', 'none', 'sum']))
			throw new ExcepcionLogica('Error, valor incorrecto', 400);
		$this->score_mode = $score_mode;
		return $this;
	}
	
	public function setIgnoreUnmapped(bool $ignore_unmapped = false): self {
		$this->ignore_unmapped = $ignore_unmapped;
		return $this;
	}
	
	/**
	 * @param int $max_children
	 * @return self
	 */
	public function setMaxChildren(int $max_children): self {
		$this->max_children = $max_children;
		return $this;
	}
	
	/**
	 * @param int $min_children
	 * @return self
	 */
	public function setMinChildren(int $min_children): self {
		$this->min_children = $min_children;
		return $this;
	}
	
}