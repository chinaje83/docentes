<?php


namespace Elastic\Tipos;


use stdClass;

/**
 * @property string   $type
 * @property stdClass $fields
 * @property stdClass $properties
 * @property bool     $index
 * @property integer  $depth_limit
 * @property bool     $dynamic
 * @property bool     $enabled
 */
class Json {
    use CamposDinamicos;
    use SubCampo;

    public $type = 'flattened';

    /**
     * Json constructor.
     *
     * Recibe el json completo y lo almacena aplanado en un campo similar al keyword
     * sirve para almacenar datos que no se requieren para la búsqueda, pero pueden ser necesarios para mostrar
     *
     * @param bool     $index
     * @param int|null $depth_limit por defecto elastic le asigna un valor de 20, este valor se puede actualizar dinámicamente
     */
    public function __construct(bool $index = false, ?int $depth_limit = null) {
        if (defined('USE_OPENSEARCH') && constant('USE_OPENSEARCH')) {
            $this->type = 'object';
            $this->dynamic = true;
            $this->enabled = !$index;
        } else {
            $this->index = $index;
            if (!empty($depth_limit))
                $this->depth_limit = $depth_limit;
        }
    }
}