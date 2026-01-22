<?php


namespace Elastic\Consultas;


class FilterAgg extends Agg {
    public $filter;

    public function __construct(Query $query) {
        $this->filter = &$query;
    }
}