<?php


namespace Elastic\Consultas;


use stdClass;

class SumAgg extends Agg {
    public $sum;

    public function __construct(string $campo) {
        $this->sum = new stdClass();
        $this->sum->field = $campo;
    }
}