<?php


namespace Elastic\Consultas;


use Bigtree\ExcepcionLogica;
use stdClass;

/**
 * Class Nested
 *
 * @property bool   $ignore_unmapped
 * @property string $score_mode
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-nested-query.html
 */
class Nested implements iConsulta {
	use CamposDinamicos;
	
	public $query;
	public $path;
	
	/**
	 * Nested constructor.
	 *
	 * @param string $path
	 * @param Query  $query
	 */
	public function __construct(string $path, Query $query) {
		$this->path = $path;
		$this->query = $query;
	}
	
	/**
	 * @param string $score_mode
	 * @return $this
	 * @throws ExcepcionLogica
	 */
	public function setScoreMode(string $score_mode = 'avg'): self {
		if (!in_array($score_mode, ['avg', 'max', 'min', 'none', 'sum']))
			throw new ExcepcionLogica('Error, valor incorrecto', 400);
		$this->score_mode = $score_mode;
		return $this;
	}
	
	public function setIgnoreUnmapped(bool $ignore_unmapped = false): self {
		$this->ignore_unmapped = $ignore_unmapped;
		return $this;
	}
	
}