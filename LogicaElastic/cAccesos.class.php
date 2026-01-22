<?php


namespace Elastic;


use DateTime;
use stdClass;
use Exception;
use ManejoErrores;
use FuncionesPHPLocal;
use Bigtree\Elastic\Tipos;
use Bigtree\Elastic\Conexion;
use Bigtree\Elastic\Tipos\Mapping;
use Bigtree\Elastic\Configuracion\Settings;

class Accesos implements \Bigtree\Elastic\InterfaceBase {
    use ManejoErrores;

    /** @var Conexion */
    private $cnx;
    /** @var string */
    private const INDEX = INDEXPREFIX . ACCESOS;

    /**
     * Accesos constructor.
     *
     * @param Conexion $cnx
     */
    public function __construct(Conexion $cnx) {
        $this->cnx =& $cnx;
    }

    /**
     * Destructor de la clase
     */
    public function __destruct() {
        $this->error = [];
    }


    /**
     * @inheritDoc
     */
    public static function Configuracion(int $shards = CANTIDAD_SHARDS, int $replicas = CANTIDAD_REPLICAS): Settings {
        return Settings::crear(
            $shards,
            $replicas,
        )->addFilter(
            'spanish_stop',
            'stop',
            ['stopwords' => '_spanish_']
        )->addFilter(
            'spanish_stemmer',
            'stemmer',
            ['language' => 'light_spanish']
        )->addAnalyzer(
            'custom_es',
            'custom',
            [
                'tokenizer' => 'standard',
                'filter' => ['lowercase', 'asciifolding', 'spanish_stop', 'spanish_stemmer'],
            ]
        );

    }

    /**
     * @inheritDoc
     */
    public static function Estructura(): Mapping {
        return Mapping::crear()
            ->setProperty('Id', Tipos\Keyword::crear())
            ->setProperty(
                'Usuario',
                Tipos\Objeto::crear()
                    ->setProperty('Id', Tipos\Entero::crear())
                    ->setProperty('Nombre', Tipos\Texto::crear()
                        ->addField('prefix', Tipos\Autocompletar::crear('standard'))
                    )
                    ->setProperty('Documento', Tipos\Texto::crear('pattern')
                        ->addField('prefix', Tipos\Autocompletar::crear('custom_es'))
                    )
                    ->setProperty('CUIL', Tipos\Texto::crear('pattern')
                        ->addField('prefix', Tipos\Autocompletar::crear('custom_es'))
                    )

            )
            ->setProperty(
                'Aplicacion',
                Tipos\Objeto::crear()
                    ->setProperty('Id', Tipos\Entero::crear())
                    ->setProperty('Codigo', Tipos\Keyword::crear())
                    ->setProperty('Nombre', Tipos\Texto::crear())
            )
            ->setProperty(
                'Roles',
                Tipos\Nested::crear()
                    ->setProperty('Codigo', Tipos\Keyword::crear())
                    ->setProperty('Nombre', Tipos\Texto::crear())
            )
            ->setProperty(
                'RolActivo',
                Tipos\Objeto::crear()
                    ->setProperty('Codigo', Tipos\Keyword::crear())
                    ->setProperty('Nombre', Tipos\Texto::crear())
            )
            ->setProperty(
                'EscuelaSeleccionada',
                Tipos\Objeto::crear()
                    ->setProperty('Id', Tipos\Entero::crear())
                    ->setProperty('Codigo', Tipos\Keyword::crear())
                    ->setProperty('Nombre', Tipos\Texto::crear())
            )
            ->setProperty(
                'RegionSeleccionada',
                Tipos\Objeto::crear()
                    ->setProperty('Id', Tipos\Entero::crear())
                    ->setProperty('Codigo', Tipos\Keyword::crear())
                    ->setProperty('Nombre', Tipos\Texto::crear())
            )
            ->setProperty(
                'Tipo',
                Tipos\Objeto::crear()
                    ->setProperty('Id', Tipos\Entero::crear())
                    ->setProperty('Nombre', Tipos\Texto::crear())
            )
            ->setProperty('IP', Tipos\Ip::crear())
            ->setProperty('SistemaOperativo', Tipos\Keyword::crear())
            ->setProperty('Navegador', Tipos\Keyword::crear())
            ->setProperty('FechaMovimiento', Tipos\Fecha::crear('yyyy-MM-dd HH:mm:ss||strict_date_optional_time||epoch_millis'));

    }

    /**
     * @inheritDoc
     */
    public static function armarDatosElastic(array $datos, bool $encode = false): bool|string|stdClass {
        $jsonData = new stdClass();
        $jsonData->Id = self::obtenerId($datos);
        if (!empty($datos['usuario'])) {
            $jsonData->Usuario = (object)$datos['usuario'];
        }
        if (!empty($datos['aplicacion'])) {
            $jsonData->Aplicacion = (object)$datos['aplicacion'];
        }
        if (!empty($datos['roles'])) {
            $jsonData->Roles = array_map(fn ($rol) => (object)$rol, $datos['roles']);
        }
        if (!empty($datos['rolActivo'])) {
            $jsonData->RolActivo = (object)$datos['rolActivo'];
        }
        if (!empty($datos['escuelaSeleccionada'])) {
            $jsonData->EscuelaSeleccionada = (object)$datos['escuelaSeleccionada'];
        }
        if (!empty($datos['regionSeleccionada'])) {
            $jsonData->RegionSeleccionada = (object)$datos['regionSeleccionada'];
        }
        if (!empty($datos['tipo'])) {
            $jsonData->Tipo = (object)$datos['tipo'];
        }
        if (!empty($datos['Ip'])) {
            $jsonData->IP = $datos['Ip'];
        }
        if (!empty($datos['SistemaOperativo'])) {
            $jsonData->SistemaOperativo = $datos['SistemaOperativo'];
        }
        if (!empty($datos['Navegador'])) {
            $datosUserAgent = FuncionesPHPLocal::getBrowserSo($datos['Navegador']);
            $jsonData->Navegador = $datosUserAgent['name'] . ' ' . $datosUserAgent['version'];
        }
        if (!empty($datos['FechaMovimiento'])) {
            $jsonData->FechaMovimiento = $datos['FechaMovimiento'];
        }
        $jsonData = FuncionesPHPLocal::ConvertiraUtf8($jsonData);
        return $encode ? json_encode($jsonData) : $jsonData;
    }

    /**
     * @inheritDoc
     */
    public static function obtenerId($datos) {
        if (is_array($datos) && isset($datos['IdUsuario']) && isset($datos['FechaMovimiento'])) {
            try {
                $fecha = new DateTime($datos['FechaMovimiento']);
            } catch (Exception $e) {
                return null;
            }

            return "{$datos['IdUsuario']}@{$fecha->format('c')}";
        }
        if (is_object($datos) && isset($datos->Id)) {
            return $datos->Id;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public static function getIndex(): string {
        return self::INDEX;
    }


    public function busquedaAvanzada(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {
        $SortField = 'FechaMovimiento';
        $SortOrder = 'desc';
        $datosEnviar = new stdClass();
        $datosEnviar->from = $datos['from'] ?? 0;
        $datosEnviar->size = $datos['size'] ?? PAGINAR;
        $datosEnviar->query = new stdClass();
        $datosEnviar->query->bool = new stdClass();
        $datosEnviar->query->bool->filter = [];
        $datosEnviar->query->bool->must = [];
        $sort = true;

        $ff = $mm = $ss = -1;

        if (!FuncionesPHPLocal::isEmpty($datos['IdUsuario'])) {
            $datosEnviar->query->bool->filter[++$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Usuario.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Usuario.Id'}->value = (int)$datos['IdUsuario'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Roles'])) {
            $datosEnviar->query->bool->filter[++$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->nested = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->nested->path = 'Roles';
            $datosEnviar->query->bool->filter[$ff]->nested->query = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->nested->query->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->nested->query->term->{'Roles.Codigo'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->nested->query->term->{'Roles.Codigo'}->value = $datos['Roles'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Aplicacion'])) {
            $datosEnviar->query->bool->filter[++$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Aplicacion.Codigo'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Aplicacion.Codigo'}->value = $datos['Aplicacion'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['FechaDesde']) || !FuncionesPHPLocal::isEmpty($datos['FechaHasta'])) {
            try {
                if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != '')
                    $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],
                            'dd/mm/aaaa', 'aaaa-mm-dd') . ' 00:00:00';

                if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != '')
                    $datos['FechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],
                            'dd/mm/aaaa', 'aaaa-mm-dd') . ' 23:59:59';

                $fechaDesde = new DateTime($datos['FechaDesde'] ?? '@0');
                $fechaHasta = new DateTime($datos['FechaHasta'] ?? 'tomorrow');
            } catch (Exception $e) {
                $this->setError(500, $e->getMessage());
                return false;
            }
            $datosEnviar->query->bool->filter[++$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->range = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->range->{'FechaMovimiento'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->range->{'FechaMovimiento'}->gte = $fechaDesde->format('c');
            $datosEnviar->query->bool->filter[$ff]->range->{'FechaMovimiento'}->lte = $fechaHasta->format('c');
        }

        if ($sort) {
            $datosEnviar->sort = [];

            if (isset($datos['sort']) && is_array($datos['sort']) && count($datos['sort']) > 0) {
                foreach ($datos['sort'] as $sort) {
                    $datosEnviar->sort[++$ss] = new StdClass;
                    $datosEnviar->sort[$ss]->{$sort['field']} = new StdClass;
                    $datosEnviar->sort[$ss]->{$sort['field']}->order = $sort['order'];
                }
            } else {
                $datosEnviar->sort[++$ss] = new StdClass;
                $datosEnviar->sort[$ss]->{$SortField} = new StdClass;
                $datosEnviar->sort[$ss]->{$SortField}->order = $SortOrder;
            }
        }

        $cuerpo = json_encode($datosEnviar);
        $this->cnx->setDebug(false);

        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $data, $codigoRetorno, 'track_total_hits=true')) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($data['hits'])) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }
        $resultado = $data['hits']['hits'] ?? [];
        $numfilas = count($resultado);
        $total = (int)$data['hits']['total']['value'] ?? 0;

        return true;
    }


    /**
     * @param array      $datos
     * @param array|null $dataResult
     *
     * @return bool
     */
    public function autoCompletar(array $datos, ?array &$dataResult): bool {
        $jsonData = new stdClass();
        $jsonData->size = 0;
        $jsonData->_source = ['Usuario.Id', 'Usuario.Nombre', 'Usuario.Documento', 'Usuario.CUIL',];
        $jsonData->query = new stdClass();
        $jsonData->query->multi_match = new stdClass();
        $jsonData->query->multi_match->query = $datos['Nombre'];
        $jsonData->query->multi_match->type = 'bool_prefix';
        $jsonData->query->multi_match->fields = [
            'Usuario.Nombre.prefix',
            'Usuario.Nombre.prefix._2gram',
            'Usuario.Nombre.prefix._3gram',
            'Usuario.Documento.prefix',
            'Usuario.Documento.prefix._2gram',
            'Usuario.Documento.prefix._3gram',
            'Usuario.CUIL.Numero.prefix',
            'Usuario.CUIL.Numero.prefix._2gram',
            'Usuario.CUIL.Numero.prefix._3gram',
        ];

        $jsonData->aggs = new stdClass();
        $jsonData->aggs->Usuarios = new stdClass();
        $jsonData->aggs->Usuarios->terms = new stdClass();
        $jsonData->aggs->Usuarios->terms->field = 'Usuario.Id';
        $jsonData->aggs->Usuarios->terms->size = 10;
        $jsonData->aggs->Usuarios->aggs = new stdClass();
        $jsonData->aggs->Usuarios->aggs->Datos = new stdClass();
        $jsonData->aggs->Usuarios->aggs->Datos->top_hits = new stdClass();
        $jsonData->aggs->Usuarios->aggs->Datos->top_hits->size = 1;
        $jsonData->aggs->Usuarios->aggs->Datos->top_hits->_source = 'Usuario.*';

        $cuerpo = json_encode($jsonData);
        $this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado, $codigoRetorno)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($resultado['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado));
            return false;
        }

        if ($resultado['hits']['total'] < 1) {
            $this->setError(404, 'No se encuentra');
            return false;
        }
        $dataResult = $resultado['aggregations']['Usuarios']['buckets'];

        return true;
    }

    public static function getIndexSuffix(): string {
        return ACCESOS;
    }
}
