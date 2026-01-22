<?php


namespace Elastic\Consultas;


use Bigtree\ExcepcionLogica;
use stdClass;

/**
 * Class QueryString
 *
 * @property string[] $fields
 * @property string   $default_field
 * @property float    $boost
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html
 */
class QueryString implements iConsulta {
	use CamposDinamicos;
	
	public $query;
	public $default_operator = 'OR';
	
	/**
	 * QueryString constructor.
	 *
	 * @param string|string[] $campo
	 * @param string          $query
	 */
	public function __construct($campo, string $query) {
		if (is_array($campo))
			$this->fields = $campo;
		elseif (is_string($campo) && '' !== $campo)
			$this->default_field = $campo;
		$this->query = $query;
	}
	
	/**
	 * @param string $operator
	 * @return $this
	 * @throws ExcepcionLogica
	 */
	public function setDefaultOperator(string $operator): self {
		if (!in_array($operator, ['OR', 'AND']))
			throw new ExcepcionLogica('Error, valor incorrecto', 400);
		$this->default_operator = $operator;
		return $this;
	}
	
	public function setBoost(float $boost): self {
		$this->boost = $boost;
		return $this;
	}
	
}