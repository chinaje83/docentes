<?php


namespace Elastic;


use stdClass;
use ManejoErrores;
use FuncionesPHPLocal;

class APM implements InterfaceBase {

    use ManejoErrores;

    /** @var string */
    // private const INDEX = 'apm-' . APM_VER . '-transaction';
    private const INDEX = '.ds-traces-apm.rum-default-*';
    /** @var string */
    private const APP = APPLICATION . '-' . PROVINCIA;
    /** @var Conexion */
    private $cnx;

    /**
     * Accesos constructor.
     *
     * @param Conexion $cnx
     */
    public function __construct(Conexion $cnx) {
        $this->cnx =& $cnx;
    }

    /**
     * @inheritDoc
     */
    public static function Configuracion(stdClass &$jsonData): void {}

    /**
     * @inheritDoc
     */
    public static function Estructura(bool $devolverJson = true) {
        return false;
    }

    /**
     * @inheritDoc
     */
    public static function armarDatosElastic(array $datos, bool $encode = false) {
        return false;
    }

    /**
     * @inheritDoc
     */
    public static function obtenerId($datos) {
        return null;
    }

    /**
     * @inheritDoc
     */
    public static function getIndex(): string {
        return self::INDEX;
    }

    /**
     * Destructor de la clase
     */
    public function __destruct() {
        $this->error = [];
    }

    /**
     * @param array      $datos
     * @param array|null $resultado
     * @param int|null   $numfilas
     * @param int|null   $total
     *
     * @return bool
     * @noinspection t
     */
    public function buscar(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {
        $sort = true;
        $defaultSort = new stdClass();
        $defaultSort->{'@timestamp'} = new stdClass();
        $defaultSort->{'@timestamp'}->order = 'desc';
        $datosEnviar = new stdClass();
        $datosEnviar->size = $datos['size'] ?? PAGINAR;
        $datosEnviar->from = $datos['from'] ?? 0;

        $datosEnviar->query = new stdClass();
        $datosEnviar->query->bool = new stdClass();
        $filter = [new stdClass()];
        $ff = 0;

        $filter[$ff]->term = new stdClass();
        $filter[$ff]->term->{'service.name'} = new stdClass();
        $filter[$ff]->term->{'service.name'}->value = self::APP;

        $filter[++$ff] = new stdClass();
        $filter[$ff]->term = new stdClass();
        $filter[$ff]->term->{'processor.event'} = new stdClass();
        $filter[$ff]->term->{'processor.event'}->value = 'transaction';

        if (!FuncionesPHPLocal::isEmpty($datos['IdUsuario'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->term = new stdClass();
            $filter[$ff]->term->{'user.id'} = new stdClass();
            $filter[$ff]->term->{'user.id'}->value = (string)$datos['IdUsuario'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Rol'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->terms = new stdClass();
            $filter[$ff]->terms->{'labels.rol_id'} = is_array($datos['Rol']) ?
                $datos['Rol'] : explode(',', $datos['Rol']);
        }

        if (!FuncionesPHPLocal::isEmpty($datos['FechaDesde']) ||
            !FuncionesPHPLocal::isEmpty($datos['FechaHasta'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->range = new stdClass();
            $filter[$ff]->range->{'@timestamp'} = new stdClass();
            $filter[$ff]->range->{'@timestamp'}->gte = $datos['FechaDesde'] ?: '2020-01-01';
            $filter[$ff]->range->{'@timestamp'}->lte = $datos['FechaHasta'] ?: 'now';
            $filter[$ff]->range->{'@timestamp'}->format = 'dd/MM/yyyy';
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IgnorarUsuarios'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->must_not = [new stdClass()];
            $filter[$ff]->must_not[0]->terms = new stdClass();
            $filter[$ff]->must_not[0]->terms->{'user.id'} = is_array($datos['IgnorarUsuarios']) ? $datos['IgnorarUsuarios'] : explode(',', $datos['IgnorarUsuarios']);
        }

        # 1 ADMIN,  14818 JOSÉ , 14839 MARIAN,  14851 ALDI , 14852 FLOR
        // $datos['IgnorarUsuarios'] = [1, 14818, 14839, 14851, 14852];
        $must_not = [];
        $mn = 0;
        if (!FuncionesPHPLocal::isEmpty($datos['IgnorarUsuarios'])) {
            $must_not[$mn] = new stdClass();
            $must_not[$mn]->terms = new stdClass();
            $must_not[$mn]->terms->{'user.id'} = is_array($datos['IgnorarUsuarios']) ? $datos['IgnorarUsuarios'] : explode(',', $datos['IgnorarUsuarios']);
        }
        $datosEnviar->query->bool->must_not = $must_not;

        $datosEnviar->query->bool->filter = $filter;

        if ($sort) {
            $ss = -1;
            $datosEnviar->sort = [];

            if (isset($datos['sort']) && is_array($datos['sort']) && count($datos['sort']) > 0) {
                if (isset($datos['sort']['field'])) {
                    $datosEnviar->sort = new StdClass;
                    $datosEnviar->sort->{$datos['sort']['field']} = new StdClass;
                    $datosEnviar->sort->{$datos['sort']['field']}->order = $datos['sort']['order'];
                } else {
                    foreach ($datos['sort'] as $sort) {
                        $datosEnviar->sort[++$ss] = new StdClass;
                        $datosEnviar->sort[$ss]->{$sort['field']} = new StdClass;
                        $datosEnviar->sort[$ss]->{$sort['field']}->order = $sort['order'];
                    }
                }
            } else
                $datosEnviar->sort = $defaultSort;

        }

        $cuerpo = json_encode($datosEnviar);
        $this->cnx->setDebug(false);

        if (!$this->cnx->sendGet(self::INDEX, '_search', $data, $codigoRetorno, 'track_total_hits=true', $cuerpo)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (200 != $codigoRetorno || !isset($data['hits'])) {
            $this->setError(400, Funciones::DevolverError($data));
            return false;
        }

        $resultado = $data['hits']['hits'] ?? [];
        $numfilas = count($resultado);
        $total = (int)$data['hits']['total']['value'] ?? 0;

        return true;
    }
}
