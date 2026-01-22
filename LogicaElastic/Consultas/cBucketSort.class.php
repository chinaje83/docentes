<?php


namespace Elastic\Consultas;


class BucketSort extends Agg {
    public $bucket_sort;


    public function __construct (int $size = PAGINAR, int $from = 0) {
        $this->bucket_sort = new \StdClass();
        $this->bucket_sort->size = $size;
        $this->bucket_sort->from = $from;
    }
}
