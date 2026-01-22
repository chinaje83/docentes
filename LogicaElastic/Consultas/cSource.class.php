<?php


namespace Elastic\Consultas;

/**
 * Class Source
 *
 * @property array includes
 * @property array excludes
 * @package Elastic\Consultas
 */
class Source {
    use CamposDinamicos;

    private $used = false;

    /**
     * @param string ...$campos
     *
     * @return $this
     */
    public function addIncludes(string ...$campos): self {
        if (empty($this->includes))
            $this->includes = [];
        $this->includes = array_values(
            array_unique(
                array_merge($this->includes, $campos)
            )
        );

        return $this->setUsed();
    }

    public function addExcludes(string ...$campos): self {
        if (empty($this->excludes))
            $this->excludes = [];
        $this->excludes = array_values(
            array_unique(
                array_merge($this->excludes, $campos)
            )
        );

        return $this->setUsed();
    }

    /**
     * @return bool
     */
    public function isUsed(): bool {
        return $this->used;
    }

    /**
     * @param bool $used
     *
     * @return Source
     */
    public function setUsed(bool $used = true): Source {
        $this->used = $used;
        return $this;
    }

    public static function includes(string ...$campos): self {
        $obj = new static();
        return $obj->addIncludes(...$campos);
    }

    public static function excludes(string ...$campos): self {
        $obj = new static();
        return $obj->addExcludes(...$campos);
    }
}