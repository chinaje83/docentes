<?php


namespace Elastic\Consultas;


use Bigtree\ExcepcionLogica;
use stdClass;

/**
 * Class Range
 *
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html
 */
class Range implements iConsulta {
	use CamposDinamicos;
	/** @var string La intersección entre los rangos no es vacía */
	public const I = 'INTERSECTS';
	/** @var string El rango almacenado contiene al rango de búsqueda */
	public const C = 'CONTAINS';
	/** @var string El rango almacenado es contenido dentro del rango de búsqueda */
	public const D = 'WITHIN';
	
	private $campo;
	
	/**
	 * Range constructor.
	 *
	 * @param string $campo
	 * @param array  $valor
	 * @throws ExcepcionLogica
	 */
	public function __construct(string $campo, array $valor) {
		$this->campo = $campo;
		$this->{$campo} = new stdClass();
		
		if (!empty($valor['gt']))
			$this->{$campo}->gt = $valor['gt'];
		elseif (!empty($valor['gte']))
			$this->{$campo}->gte = $valor['gte'];
		
		if (!empty($valor['lt']))
			$this->{$campo}->lt = $valor['lt'];
		elseif (!empty($valor['lte']))
			$this->{$campo}->lte = $valor['lte'];
		
		if (empty($this->{$campo}))
			throw new ExcepcionLogica('Error, Debe elegir al menos un campo', 400);
	}
	
	public function setBoost(float $boost): self {
		$this->{$this->campo}->boost = $boost;
		return $this;
	}
	
	public function setFormat(string $format): self {
		$this->{$this->campo}->format = $format;
		return $this;
	}
	
	/**
	 * @param string $relation
	 * @return $this
	 * @throws ExcepcionLogica
	 * @todo por ahí conviene usar constantes para los valores, si estas no me rompen el json
	 */
	public function setRelation(string $relation = self::I): self {
		if (!in_array($relation, [self::I,  //Matches documents with a range field value that intersects the query’s range.
		                          self::C,  //Matches documents with a range field value that entirely contains the query’s range.
		                          self::D   //Matches documents with a range field value entirely within the query’s range.
		]))
			throw new ExcepcionLogica('Error, valor incorrecto', 400);
		$this->{$this->campo}->relation = $relation;
		return $this;
	}
	
	public function setTimeZone(string $time_zone): self {
		$this->{$this->campo}->time_zone = $time_zone;
		return $this;
	}
	
}