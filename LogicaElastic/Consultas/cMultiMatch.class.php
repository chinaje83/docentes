<?php


namespace Elastic\Consultas;


use stdClass;

/**
 * Class Match
 *
 * @property string type Tipo de comparación con los campos, el valor por defecto es <em>best_fields</em>
 *
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html
 */
class MultiMatch implements iConsulta {
	use CamposDinamicos;
	
	/** @var string (default) Finds documents which match any field, but uses the _score from the best field. See best_fields. */
	const DEFAULT = 'best_fields';
	/** @var string Finds documents which match any field and combines the _score from each field. See most_fields. */
	const MOST = 'most_fields';
	/** @var string Treats fields with the same analyzer as though they were one big field. Looks for each word in any field. See cross_fields. */
	const CROSS = 'cross_fields';
	/** @var string Runs a match_phrase query on each field and uses the _score from the best field. See phrase and phrase_prefix. */
	const PHRASE = 'phrase';
	/** @var string Runs a match_phrase_prefix query on each field and uses the _score from the best field. See phrase and phrase_prefix. */
	const PREFIX = 'phrase_prefix';
	/** @var string Creates a match_bool_prefix query on each field and combines the _score from each field. See bool_prefix. */
	const BOOL = 'bool_prefix';

	/** @var string  El string contra el que se quiere comparar*/
	public $query;
	/** @var array Los campos sobre los que se quiere buscar */
	public $fields;
	
	/**
	 * MultiMatch constructor.
	 *
	 * Recibe dos parámetros
	 *
	 * @param string      $query  El string contra el que se quiere comparar
	 * @param array       $fields Los campos sobre los que se quiere buscar
	 * @param string|null $type tipo de consulta
	 */
	public function __construct(string $query, array $fields, ?string $type = NULL) {
		$this->query = $query;
		$this->fields = $fields;
		if(!empty($type))
			$this->setType($type);
	}
	
	/**
	 * Configura el tipo de consulta
	 *
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setType(string $type = self::DEFAULT): self {
		if(in_array($type, [self::DEFAULT, self::MOST, self::CROSS, self::PHRASE, self::PREFIX, self::BOOL]))
		    $this->type = $type;
		return $this;
	}
	
}