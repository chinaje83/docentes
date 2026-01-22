<?php


namespace Elastic\Consultas;

/**
 * Class Booleano
 *
 * @property array      $filter
 * @property array      $must
 * @property array      $should
 * @property array      $must_not
 * @property int|string $minimum_should_match
 * @property float      $boost
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html
 */
class Booleano implements iConsulta {
	use CamposDinamicos;
	
	/**
	 * @param Query $filtro
	 * @return $this
	 */
	public function addFilter(Query $filtro): self {
		if (empty($this->filter))
			$this->filter = [$filtro];
		else
			$this->filter[] =& $filtro;
		return $this;
	}
	
	/**
	 * @param Query $must
	 * @return $this
	 */
	public function addMust(Query $must): self {
		if (empty($this->must))
			$this->must = [$must];
		else
			$this->must[] =& $must;
		return $this;
	}
	
	/**
	 * @param Query $should
	 * @return $this
	 */
	public function addShould(Query $should): self {
		if (empty($this->should))
			$this->should = [$should];
		else
			$this->should[] =& $should;
		return $this;
	}
	
	/**
	 * @param Query $must_not
	 * @return $this
	 */
	public function addMustNot(Query $must_not): self {
		if (empty($this->must_not))
			$this->must_not = [$must_not];
		else
			$this->must_not[] =& $must_not;
		return $this;
	}
	
	/**
	 * @param float $boost
	 * @return $this
	 */
	public function setBoost(float $boost): self {
		$this->boost = $boost;
		return $this;
	}
	
	/**
	 * @param int|string $minimum_should_match
	 * @return $this
	 */
	public function setMinimumShouldMatch($minimum_should_match = 1): self {
		$this->minimum_should_match = $minimum_should_match;
		return $this;
	}

	public function countFilter(): int {
	    return $this->countElements('filter');
    }

    public function countShould(): int {
        return $this->countElements('should');
    }

    public function countMust(): int {
        return $this->countElements('must');
    }

    public function countMustNot(): int {
        return $this->countElements('must_not');
    }

	private function countElements(string $tipo): int {
	    if (!isset($this->{$tipo}))
	        return 0;

	    if (!is_array($this->{$tipo}))
	        return 1;

	    return count($this->{$tipo});
    }
}