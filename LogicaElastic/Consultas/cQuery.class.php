<?php


namespace Elastic\Consultas;


use Bigtree\ExcepcionLogica;

/**
 * Class Query
 *
 * @package Elastic\Consultas
 * @method setFormat(string $formato)
 * @method setDefaultOperator(string $string)
 */
class Query {
    use CamposDinamicos;

    private $activo;

    public function __construct(string $tipo, iConsulta $consulta) {
        $this->activo = $tipo;
        $this->{$tipo} = $consulta;
    }

    /**
     * @return static
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-all-query.html
     */
    public static function match_all(): self {
        return new static('match_all', new vacio());
    }

    public static function bool($consulta = NULL): self {
        return new static('bool', $consulta ?? new Booleano());
    }

    /**
     * @param string $campo
     * @param mixed  $valor
     *
     * @return static
     */
    public static function term(string $campo, $valor): self {
        return new static('term', new Term($campo, $valor));
    }

    /**
     * @param string $tipo
     * @param mixed  $id
     *
     * @return static
     */
    public static function parent_id(string $tipo, $id): self {
        return new static('parent_id', new ParentId($tipo, $id));
    }

    /**
     * @param string $campo
     * @param string $valor
     *
     * @return static
     */
    public static function match(string $campo, string $valor): self {
        return new static('match', new cMatch($campo, $valor));
    }

    /**
     * @param string      $query
     * @param array       $fields
     * @param string|null $type
     *
     * @return static
     */
    public static function multi_match(string $query, array $fields, ?string $type = NULL): self {
        return new static('multi_match', new MultiMatch($query, $fields, $type));
    }

    /**
     * Genera el caso particular de multi_match utilizado para autocompletar sobre campos Tipos\Autocompletar
     *
     * No hacen falta los grams, ya los inserta autom�ticamente
     *
     * @param string $query
     * @param array  $campos
     *
     * @return static
     */
    public static function autocompletar(string $query, array $campos): self {
        $fields = [];
        foreach ($campos as $campo) {
            $fields[] = $campo;
            $fields[] = $campo . '._2gram';
            $fields[] = $campo . '._3gram';
        }

        return self::multi_match($query, $fields, MultiMatch::BOOL);
    }

    /**
     * @param string $campo
     * @param array  $valor
     *
     * @return static
     * @throws ExcepcionLogica
     */
    public static function range(string $campo, array $valor): self {
        return new static('range', new Range($campo, $valor));
    }

    /**
     * @param string|string[] $campo
     * @param string          $query
     *
     * @return static
     */
    public static function query_string($campo, string $query): self {
        return new static('query_string', new QueryString($campo, $query));
    }

    /**
     * @param string $campo
     *
     * @return static
     */
    public static function exists(string $campo): self {
        return new static('exists', new Exists($campo));
    }

    /**
     * @param string $campo
     * @param array  $valor
     *
     * @return static
     */
    public static function terms(string $campo, array $valor): self {
        return new static('terms', new Terms($campo, $valor));
    }

    /**
     * @param string $path
     * @param Query  $query
     *
     * @return static
     */
    public static function nested(string $path, Query $query): self {
        return new static('nested', new Nested($path, $query));
    }

    /**
     * @param string $type
     * @param Query  $query
     *
     * @return static
     */
    public static function has_child(string $type, Query $query): self {
        return new static('has_child', new HasChild($type, $query));
    }

    /**
     * @param string $parent_type
     * @param Query  $query
     *
     * @return static
     */
    public static function has_parent(string $parent_type, Query $query): self {
        return new static('has_parent', new HasParent($parent_type, $query));
    }

    /**
     * @return iConsulta
     */
    public function getActivo(): iConsulta {
        return $this->{$this->activo};
    }


    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     * @throws ExcepcionLogica
     */
    public function __call($name, $arguments) {
        if (!empty($this->activo) && method_exists($this->getActivo(), $name)) {
            $this->getActivo()->{$name}(...$arguments);
            return $this;
        }
        throw new ExcepcionLogica('Error, método no implementado', 405);
    }


    public function addFilter(Query $filtro): self {
        $this->getActivo()->addFilter($filtro);
        return $this;
    }

    /**
     * @return int
     */
    public function countFilter(): int {
        if (method_exists($this->getActivo(), 'countFilter'))
            return $this->getActivo()->countFilter();
        return -1;
    }

    public function addMust(Query $must): self {
        $this->getActivo()->addMust($must);
        return $this;
    }

    /**
     * @return int
     */
    public function countMust(): int {
        if (method_exists($this->getActivo(), 'countMust'))
            return $this->getActivo()->countMust();
        return -1;
    }


    public function addShould(Query $should): self {
        $this->getActivo()->addShould($should);
        return $this;
    }

    /**
     * @return int
     */
    public function countShould(): int {
        if (method_exists($this->getActivo(), 'countShould'))
            return $this->getActivo()->countShould();
        return -1;
    }


    public function addMustNot(Query $must_not): self {
        $this->getActivo()->addMustNot($must_not);
        return $this;
    }

    /**
     * @return int
     */
    public function countMustNot(): int {
        if (method_exists($this->getActivo(), 'countMustNot'))
            return $this->getActivo()->countMustNot();
        return -1;
    }

}