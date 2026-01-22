<?php

namespace Elastic\Consultas;

use stdClass;

class ReverseNestedAgg extends Agg {
    public $reverse_nested;

    public function __construct(?string $path) {
        $this->reverse_nested = new stdClass();
        if (!empty($path))
            $this->reverse_nested->path = $path;
    }
}