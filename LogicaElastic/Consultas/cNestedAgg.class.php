<?php

namespace Elastic\Consultas;

use stdClass;

class NestedAgg extends Agg {
    public $nested;

    public function __construct(string $path) {
        $this->nested = new stdClass();
        $this->nested->path = $path;
    }
}