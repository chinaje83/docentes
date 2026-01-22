<?php


namespace Elastic\Consultas;


use stdClass;

/**
 * Class TermsAgg
 *
 * @property stdClass order
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-terms-aggregation.html
 */
class TermsAgg extends Agg {
	use CamposDinamicos;
	
	/**
	 * @var stdClass
	 */
	public $terms;
	
	public function __construct(string $campo, ?int $cantidad = 10) {
		$this->terms = new stdClass();
		$this->terms->field = $campo;
		$this->terms->size = $cantidad;
	}

    /**
     * @param string $metrica
     * @param string $orden
     *
     * @return $this
     */
	public function setOrder(string $metrica, string $orden): self {
	    $this->order = new stdClass();
        $this->order->{$metrica} = $orden;
	    return $this;
    }
	
	
}