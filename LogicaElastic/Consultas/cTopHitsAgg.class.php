<?php


namespace Elastic\Consultas;

use stdClass;

/**
 * Class TopHitsAgg
 *
 * @package Elastic\Consultas
 * @see     https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-top-hits-aggregation.html
 */
class TopHitsAgg extends Agg {
    use CamposDinamicos;

    public $top_hits;

    /**
     * TopHitsAgg constructor.
     *
     * @param int      $size
     * @param int|null $from
     */
    public function __construct (int $size, ?int $from = null) {
        $this->top_hits = new stdClass();
        $this->top_hits->size = $size;
        if (!is_null($from))
            $this->top_hits->from = $from;
    }

    public function setSource (Source $source): self {
        $this->top_hits->_source = $source;
        return $this;
    }

    public function getSource (): Source {
        return $this->top_hits->_source;
    }

    /**
     * @param Sort $sort
     *
     * @return self
     */
    public function setSort (Sort $sort): self {
        $this->top_hits->sort = $sort;
        return $this;
    }

    /**
     * @param Sort $sort
     *
     * @return self
     */
    public function addSort (Sort $sort): self {
        if (empty($this->top_hits->sort))
            $this->top_hits->sort = [$sort];
        elseif (is_array($this->top_hits->sort))
            $this->top_hits->sort[] = $sort;
        else
            $this->top_hits->sort = [$this->sort, $sort];


        return $this;
    }
}
