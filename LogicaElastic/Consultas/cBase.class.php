<?php


namespace Elastic\Consultas;


use stdClass;

/**
 * Class Base
 *
 * @property Source      _source
 * @property Sort|Sort[] sort
 * @property iScript     script
 * @property stdClass    aggs
 * @property int         size
 * @property int         from
 * @package Elastic\Consultas
 */
class Base {
    use CamposDinamicos;

    /** @var Query */
    public $query;

    public function __construct(?int $size = PAGINAR, ?int $from = 0) {
        if (!is_null($size))
            $this->size = $size;
        if (!is_null($from))
            $this->from = $from;
    }

    public function getQuery(): iConsulta {
        return $this->query->getActivo();
    }

    /**
     * @param Query $query
     *
     * @return Base
     */
    public function setQuery(Query $query): self {
        $this->query = $query;
        return $this;
    }

    public function setSource(Source $source): self {
        $this->_source = $source;
        return $this;
    }

    public function getSource(): Source {
        return $this->_source;
    }

    public function setScript(iScript $script): self {
        $this->script = $script;
        return $this;
    }

    public function getScript(): iScript {
        return $this->script;
    }

    public function setAgg(string $nombre, Agg $aggs): self {
        if (empty($this->aggs))
            $this->aggs = new stdClass();
        $this->aggs->{$nombre} = $aggs;
        return $this;
    }

    public function getAgg(string $nombre): Agg {
        return $this->aggs->{$nombre};
    }

    /**
     * @param int $size
     *
     * @return self
     */
    public function setSize(int $size): self {
        $this->size = $size;
        return $this;
    }

    /**
     * @param int $from
     *
     * @return self
     */
    public function setFrom(int $from): self {
        $this->from = $from;
        return $this;
    }

    /**
     * @param Sort $sort
     *
     * @return Base
     */
    public function setSort(Sort $sort): self {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @param Sort $sort
     *
     * @return Base
     */
    public function addSort(Sort $sort): self {
        if (empty($this->sort))
            $this->sort = $sort;
        elseif (is_array($this->sort))
            $this->sort[] = $sort;
        else
            $this->sort = [$this->sort, $sort];


        return $this;
    }

    /**
     * Convierte a json el objeto de consulta
     *
     * @param bool $pretty_print (false) Sí llega en verdadero expande el json con espacios y nueva línea, en caso contrario usa el json normal
     *
     * @return string
     */
    public function toJson(bool $pretty_print = false): string {
        return json_encode($this, $pretty_print ? JSON_PRETTY_PRINT : 0);
    }

    /**
     * @param int|null $size
     * @param int|null $from
     *
     * @return static
     */
    public static function nueva(?int $size = PAGINAR, ?int $from = 0): self {
        return new static($size, $from);
    }

}