<?php
//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------
// Clase genérica con la lógica para el mapeo de índices en elastic
namespace Elastic;


use Bigtree\ExcepcionLogica;
use Generator;
use ManejoErrores;
use stdClass;

/**
 * Class Indices
 *
 * @package Elastic
 *
 *
 * @author  José R. Méndez <jmendez@bigtree.com.ar>
 */
class Indices {
    use ManejoErrores;

    /** @var string */
    protected $indexSuffix;
    /** @var string */
    protected $index;
    /** @var Conexion */
    public $conexionES;


    /**
     * Constructor de la clase.
     *
     * @param string   $indexSuffix
     * @param Conexion $conexionES
     */
    public function __construct(string $indexSuffix, Conexion $conexionES) {
        $this->indexSuffix = $indexSuffix;
        $this->index = INDEXPREFIX . $indexSuffix;
        $this->conexionES =& $conexionES;
    }

    /**
     * Destructor de la clase
     */
    public function __destruct() {
        $this->error = [];
    }


    /**
     * @param string $nombre
     *
     * @return bool
     */
    public function CrearTemplate(string $nombre): bool {
        $indice = '';
        $endPoint = '_template';

        if (!$this->conexionES->sendHead($indice, $endPoint, $returnCode, $nombre))
            return false;
        if ($returnCode == 200)
            return true;
        else {
            $datosEnviar = self::_estructuraTemplate();
            //$this->conexionES->setDebug(true);
            if (!$this->conexionES->sendPut($indice, $endPoint, $datosEnviar, $data, $codigoRetorno, $nombre))
                return false;

            if (!isset($data['acknowledged']) || $data['acknowledged'] === false) {
                $this->setError('400', Funciones::DevolverError($data));
                return false;
            }

            return true;
        }
    }


    /**
     * @param int $shards
     * @param int $replicas
     *
     * @return bool
     */
    public function Crear(int $shards = CANTIDAD_SHARDS, int $replicas = CANTIDAD_REPLICAS): bool {
        $endPoint = '';
        if (!$this->conexionES->sendHead($this->index, $endPoint, $returnCode))
            return false;

        if ($returnCode === 200) {
            return true;
        }
        $jsonData = new stdClass();
        $jsonData->settings = new stdClass();
        $jsonData->settings->number_of_shards = $shards;
        $jsonData->settings->number_of_replicas = $replicas;

        if (defined("CLASES") && isset(CLASES[$this->indexSuffix])) {
            /** @var InterfaceBase $clase */
            $clase = 'Elastic\\' . CLASES[$this->indexSuffix];
            if (class_exists($clase) && method_exists($clase, 'Configuracion'))
                $clase::Configuracion($jsonData);

        }

        $datosEnviar = json_encode($jsonData);
        if (!$this->conexionES->sendPut($this->index, $endPoint, $datosEnviar, $data, $codigoRetorno))
            return false;

        if (!isset($data['acknowledged']) || $data['acknowledged'] === false) {
            $this->setError('400', Funciones::DevolverError($data));
            return false;
        }

        return true;

    }


    /**
     * Genera las pipelines de ingest para los indices, se debe llamar una ves por cada pipeline
     *
     * ejemplo de processors:
     * ['if (ctx.IdCliente == null) { throw new Exception("El identificador de cliente es obligatorio") }']
     *
     *
     * @param string $name        Nombre del pipeline, debe ser ASCII, sin espacios y en minúscula
     * @param string $description Descripción del pipeline
     * @param array  $processors  Procesadores del pipeline
     *
     * @return bool
     */
    public function GenerarPipelines(string $name, string $description, array $processors): bool {

        $jsonData = new stdClass();
        $jsonData->description = utf8_encode($description);
        $jsonData->processors = [];
        foreach ($processors as $ii => $processor) {
            $jsonData->processors[$ii] = new stdClass();
            $jsonData->processors[$ii]->script = new stdClass();
            $jsonData->processors[$ii]->script->lang = 'painless';
            $jsonData->processors[$ii]->script->inline = utf8_encode($processor);

        }


        $datosEnviar = json_encode($jsonData);

        $endPoint = '_ingest';
        $param = "pipeline/$name";

        if (!$this->conexionES->sendPut($this->index, $endPoint, $datosEnviar, $data, $codigoRetorno, $param))
            if (!isset($data['acknowledged']) || $data['acknowledged'] === false) {
                $this->setError('400', Funciones::DevolverError($data));
                return false;
            }


        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     */
    public function GenerarAnalizadores(array $datos): bool {
        $jsonData = new stdClass();
        Funciones::CerrarIndice($this->index, $this->conexionES);

        if (!empty($datos['filter'])) {
            $jsonData->settings = new stdClass();
            $jsonData->settings->analysis = new stdClass();
            $jsonData->settings->analysis->filter = new stdClass();
            foreach ($datos['filter'] as $key => $filter) {
                $jsonData->settings->analysis->filter->{$filter} = new stdClass();
                $jsonData->settings->analysis->filter->{$filter}->type = $datos['filter_type'][$key];
                $jsonData->settings->analysis->filter->{$filter}->stopwords = $datos['filter_stopwords'][$key];
            }
        }


        if (!empty($datos['analyzer'])) {
            $jsonData->analysis = new stdClass();
            $jsonData->analysis->analyzer = new stdClass();
            foreach ($datos['analyzer'] as $key => $analyzer) {
                $jsonData->analysis->analyzer->{$analyzer} = new stdClass();
                $jsonData->analysis->analyzer->{$analyzer}->tokenizer = $datos['analyzer_tokenizer'][$key];
                $jsonData->analysis->analyzer->{$analyzer}->filter = $datos['analyzer_filter'][$key];
            }
        }


        if (!empty($datos['normalizer'])) {
            if (!isset($jsonData->analysis))
                $jsonData->analysis = new stdClass();
            $jsonData->analysis->normalizer = new stdClass();
            foreach ($datos['normalizer'] as $key => $normalizer) {
                $jsonData->analysis->normalizer->{$normalizer} = new stdClass();
                $jsonData->analysis->normalizer->{$normalizer}->type = $datos['normalizer_type'][$key];
                $jsonData->analysis->normalizer->{$normalizer}->char_filter = $datos['normalizer_char_filter'][$key];
                $jsonData->analysis->normalizer->{$normalizer}->filter = $datos['normalizer_filter'][$key];
            }
        }


        $datosEnviar = json_encode($jsonData);
        if (!$this->conexionES->sendPut($this->index, '_settings', $datosEnviar, $data, $codigoRetorno))
            return false;


        Funciones::AbrirIndice($this->index, $this->conexionES);
        if (!isset($data['acknowledged']) || $data['acknowledged'] === false) {
            $this->setError('400', Funciones::DevolverError($data));
            return false;
        }


        return true;

    }


    /**
     * @param string     $alias
     * @param array|null $data
     *
     * @return bool
     */
    public function GenerarAlias(string $alias, ?array &$data): bool {
        $ii = 0;
        $datosAlias = new stdClass();
        $datosAlias->actions = array();
        $datosAlias->actions[$ii] = new stdClass();
        $datosAlias->actions[$ii]->add = new stdClass();
        $datosAlias->actions[$ii]->add->index = INDEXPREFIX . $this->indexSuffix;
        $datosAlias->actions[$ii]->add->alias = $alias;
        $datosEnviar = json_encode($datosAlias);
        if (!$this->conexionES->sendPost($this->index, '_aliases', $datosEnviar, $data, $codigoRetorno))
            return false;

        if (!isset($data['acknowledged']) || $data['acknowledged'] === false) {
            $this->setError('400', Funciones::DevolverError($data));
            return false;
        }


        return true;
    }

    /**
     * @param string     $alias
     * @param array|null $data
     *
     * @return bool
     */
    public function EliminarAlias(string $alias, ?array &$data): bool {
        $ii = 0;
        $datosAlias = new stdClass();
        $datosAlias->actions = array();
        $datosAlias->actions[$ii] = new stdClass();
        $datosAlias->actions[$ii]->remove = new stdClass();
        $datosAlias->actions[$ii]->remove->index = INDEXPREFIX . $this->indexSuffix;;
        $datosAlias->actions[$ii]->remove->alias = $alias;
        $datosEnviar = json_encode($datosAlias);

        if (!$this->conexionES->sendPost($this->index, '_aliases', $datosEnviar, $data, $codigoRetorno))
            return false;
        if (!isset($data['acknowledged']) || $data['acknowledged'] === false) {
            $this->setError('400', Funciones::DevolverError($data));
            return false;
        }


        return true;
    }


    /**
     * @return bool
     */
    public function Mapear(): bool {
        $datosEnviar = $this->_ObtenerMapping();
        $this->conexionES->setDebug(false);
        if (!$this->conexionES->sendPut($this->index, '_mapping', $datosEnviar, $data, $codigoRetorno))
            return false;

        if (!isset($data['acknowledged']) || $data['acknowledged'] === false) {
            $this->setError('400', Funciones::DevolverError($data));
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function Eliminar(): bool {
        if (!$this->conexionES->sendDelete($this->index, '', $data, $codigoRetorno))
            return false;

        if (!isset($data['acknowledged']) || $data['acknowledged'] === false) {
            $this->setError('400', Funciones::DevolverError($data));
            return false;
        }

        return true;
    }

    /**
     * @param string|null $campo
     *
     * @return array
     * @throws ExcepcionLogica
     */
    public function getMapping(?string $campo = null): array {
        $param = '';
        if (!is_null($campo))
            $param = "field/$campo";

        $this->conexionES->setDebug(false);
        if (!$this->conexionES->sendGet($this->index, '_mapping', $resultado, $codigoRetorno, $param))
            throw new ExcepcionLogica($this->conexionES->getError('error_description'));
        if (is_null($campo))
            return $resultado[$this->index]['mappings'];

        return $resultado[$this->index]['mappings'][$campo]['mapping'][$campo] ?? [];
    }

    /**
     * @param array  $datosIndice
     * @param string $campoBase
     *
     * @return Generator
     */
    public static function recorrerCampos(array $datosIndice, string $campoBase=''): Generator {
        foreach ($datosIndice['properties'] as $campo => $propiedades) {
            yield $campoBase.$campo;
            if (isset($propiedades['properties']) && !isset($propiedades['type']))
                foreach(self::recorrerCampos($propiedades, "$campoBase$campo.") as $subCampo)
                    yield $subCampo;
        }
    }

    /**
     * @return false|stdClass|string
     */
    private function _ObtenerMapping() {
        $claseExiste = false;
        $jsonData = '{"properties":{"Texto":{"type":"text"}}}';

        if (defined("CLASES") && isset(CLASES[$this->indexSuffix])) {
            /** @var InterfaceBase $clase */
            $clase = 'Elastic\\' . CLASES[$this->indexSuffix];
            if (class_exists($clase) && method_exists($clase, 'Estructura')) {
                $jsonData = $clase::Estructura();
                $claseExiste = true;
            }
        }

        if (!$claseExiste && file_exists(PUBLICA . "mappings/{$this->indexSuffix}.json"))
            $jsonData = file_get_contents(PUBLICA . "mappings/{$this->indexSuffix}.json");

        return $jsonData;
    }


    /**
     * PLANTILLAS
     *
     *
     * @return false|string
     */
    private static function _estructuraTemplate() {
        $jsonData = new stdClass();
        $jsonData->index_patterns = [INDEXPREFIX . '*'];
        $jsonData->settings = new stdClass();
        $jsonData->settings->number_of_shards = CANTIDAD_SHARDS;
        $jsonData->settings->number_of_replicas = CANTIDAD_REPLICAS;
        $jsonData->mappings = new stdClass();
        $jsonData->mappings->dynamic = 'strict';

        return json_encode($jsonData);

    }


    /*
    Numéricos:
      Enteros:
      $jsonData['properties']['BBBBBBBBBBBBBBBBB']['type'] = 'byte';      (8 bits)

      $jsonData['properties']['SSSSSSSSSSSSSSSSS']['type'] = 'short';    (16 bits)

      $jsonData['properties']['iiiiiiiiiiiiiiiii']['type'] = 'integer';  (32 bits)

      $jsonData['properties']['lllllllllllllllll']['type'] = 'long';     (64 bits)


      Decimales:
      $jsonData['properties']['sssssssssssssssss']['type'] = 'scaled_float'; (recomendada)
      $jsonData['properties']['sssssssssssssssss']['scaling_factor'] = 100;

      $jsonData['properties']['hhhhhhhhhhhhhhhhh']['type'] = 'half_float';

      $jsonData['properties']['fffffffffffffffff']['type'] = 'float';

      $jsonData['properties']['ddddddddddddddddd']['type'] = 'double';

    Strings:

      $jsonData['properties']['kkkkkkkkkkkkkkkkk']['type'] = 'keyword';

      $jsonData['properties']['ttttttttttttttttt']['type'] = 'text';
      $jsonData['properties']['ttttttttttttttttt']['analyzer'] = 'spanish';
      $jsonData['properties']['ttttttttttttttttt']['fields']['raw']['type'] = 'keyword';

    Fecha:
      $jsonData['properties']['yyyyyyyyyyyyyyyyy']['type'] = 'date';
      $jsonData['properties']['yyyyyyyyyyyyyyyyy']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
      $jsonData['properties']['yyyyyyyyyyyyyyyyy']['format'] = 'basic_date'; (equivalente a yyyyMMdd)

    Otros:
      $jsonData['properties']['bbbbbbbbbbbbbbbbb']['type'] = 'boolean';

      $jsonData['properties']['IIIIIIIIIIIIIIIII']['type'] = 'ip';           (IPv4 o IPv6)

      $jsonData['properties']['ggggggggggggggggg']['type'] = 'geo_point';    (latitud y longitud)

        $jsonData['properties']['nnnnnnnnnnnnnnnnn']['type'] = 'nested';        (anidados)



    $jsonData['properties']['CAMPO']['null_value'] = Contenido del misto tipo que el CAMPO;
    */

}
