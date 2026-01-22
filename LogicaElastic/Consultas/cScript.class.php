<?php


namespace Elastic\Consultas;

/**
 * Class Script
 *
 * @package Elastic\Consultas
 *
 * @property string $params
 */
class Script implements iScript {
    use CamposDinamicos;

    public $source;
    public $lang;

    public function __construct(string $source, string $language = 'painless') {
        $this->source = $source;
        $this->lang = $language;
    }

    public function setParams(object $params): self {
        $this->params = $params;
        return $this;
    }
}