<?php

namespace Elastic;

use DateTime;
use stdClass;
use ManejoErrores;
use FuncionesPHPLocal;
use Bigtree\ExcepcionLogica;
use Elastic\Consultas\Query;
use Elastic\Consultas\Base as Consulta;

class Incompatibilidades {
    use ManejoErrores;

    /** @var Conexion */
    private $cnx;

    public function __construct(Conexion $cnx) {
        $this->cnx =& $cnx;
    }

    public function __destruct() {}


    /**
     * @param array      $datos
     * @param array|null $resumen
     * @param array|null $puestoNuevo
     *
     * @return bool
     * @throws \Exception
     */
    public function validarSuperposicionHoraria(array $datos, ?array &$resumen, ?array $puestoNuevo = null): bool {
        $haySuperposicion = array_fill(1, 6, false);
        $colisiones = $horas = $temp = array_fill(1, 6, []);
        $resumen = [
            'hay_conflictos' => false,
            'conflictos_dias' => $haySuperposicion,
            'colisiones' => $colisiones,
        ];
        $jsonData = new stdClass();
        $jsonData->size = 1000;
        $jsonData->_source = new stdClass();
        $jsonData->_source->includes = [
            'Dia.*',
            'Horario',
            'HoraDesde',
            'HoraHasta',
        ];

        $jsonData->query = new stdClass();
        $jsonData->query->bool = new stdClass();
        $filter = [];
        $ff = -1;

        $filter[++$ff] = new stdClass();
        $filter[$ff]->term = new stdClass();
        $filter[$ff]->term->Tipo = new stdClass();
        $filter[$ff]->term->Tipo->value = 'Desempeno';

        $filter[++$ff] = new stdClass();
        $filter[$ff]->term = new stdClass();
        $filter[$ff]->term->Estado = new stdClass();
        $filter[$ff]->term->Estado->value = ACTIVO;

        $filter[++$ff] = new stdClass();
        $filter[$ff]->has_parent = new stdClass();
        $filter[$ff]->has_parent->parent_type = 'Puesto';
        $filter[$ff]->has_parent->query = new stdClass();
        $filter[$ff]->has_parent->query->has_child = new stdClass();
        $filter[$ff]->has_parent->query->has_child->type = 'Persona';
        $filter[$ff]->has_parent->query->has_child->query = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool->filter = [];
        $filter[$ff]->has_parent->query->has_child->query->bool->filter[0] = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool->filter[0]->term = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool->filter[0]->term->IdPersona = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool->filter[0]->term->IdPersona->value = (int)$datos['IdPersona'];

        if (!FuncionesPHPLocal::isEmpty($datos['POFA']) && $datos['POFA']) {
            $filter[$ff]->has_parent->query->has_child->query->bool->filter[1] = new stdClass();
            $filter[$ff]->has_parent->query->has_child->query->bool->filter[1]->term = new stdClass();
            $filter[$ff]->has_parent->query->has_child->query->bool->filter[1]->term->{'EstadoPersona.Id'} = new stdClass();
            $filter[$ff]->has_parent->query->has_child->query->bool->filter[1]->term->{'EstadoPersona.Id'}->value = 1;
        }

        $filter[$ff]->has_parent->query->has_child->query->bool->must_not = [new stdClass(), new stdClass()];
        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[0]->nested = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[0]->nested->path = 'EstadoPersona.Licencia';
        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[0]->nested->query = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[0]->nested->query->bool = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[0]->nested->query->bool->must = [new stdClass(), new stdClass()];
        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[0]->nested->query->bool->must[0]->term = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[0]->nested->query->bool->must[0]->term->{'EstadoPersona.Licencia.Fechas'} = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[0]->nested->query->bool->must[0]->term->{'EstadoPersona.Licencia.Fechas'}->value = 'now';
        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[0]->nested->query->bool->must[1]->terms = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[0]->nested->query->bool->must[1]->terms->{'EstadoPersona.Licencia.Articulo.Id'} = ARTICULOS_IGNORAN_CARGOS;


        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[1]->terms = new stdClass();
        $filter[$ff]->has_parent->query->has_child->query->bool->must_not[1]->terms->{"Estado"} = [ELIMINADO, NOACTIVO];


        $filter[++$ff] = new stdClass();
        $filter[$ff]->has_parent = new stdClass();
        $filter[$ff]->has_parent->parent_type = 'Puesto';
        $filter[$ff]->has_parent->query = new stdClass();
        $filter[$ff]->has_parent->query->bool = new stdClass();
        $filter[$ff]->has_parent->query->bool->must_not = [new stdClass()];
        $filter[$ff]->has_parent->query->bool->must_not[0]->term = new stdClass();
        $filter[$ff]->has_parent->query->bool->must_not[0]->term->Estado = new stdClass();
        $filter[$ff]->has_parent->query->bool->must_not[0]->term->Estado->value = ELIMINADO;

        if (!FuncionesPHPLocal::isEmpty($datos['PuestosIgnorar'])) {
            $filter[$ff]->has_parent->query->bool->must_not[1] = new stdClass();
            $filter[$ff]->has_parent->query->bool->must_not[1]->terms = new stdClass();
            $filter[$ff]->has_parent->query->bool->must_not[1]->terms->{'Id'} = $datos['PuestosIgnorar'];
        }

        if (!FuncionesPHPLocal::isEmpty($filter))
            $jsonData->query->bool->filter = $filter;

        $jsonData->aggs = new stdClass();
        $jsonData->aggs->Parent = new stdClass();
        $jsonData->aggs->Parent->parent = new stdClass();
        $jsonData->aggs->Parent->parent->type = 'Desempeno';
        $jsonData->aggs->Parent->aggs = new stdClass();
        $jsonData->aggs->Parent->aggs->Puestos = new stdClass();
        $jsonData->aggs->Parent->aggs->Puestos->terms = new stdClass();
        $jsonData->aggs->Parent->aggs->Puestos->terms->field = 'Id';
        $jsonData->aggs->Parent->aggs->Puestos->terms->size = 1000;
        $jsonData->aggs->Parent->aggs->Puestos->aggs = new stdClass();
        $jsonData->aggs->Parent->aggs->Puestos->aggs->Datos = new stdClass();
        $jsonData->aggs->Parent->aggs->Puestos->aggs->Datos->top_hits = new stdClass();
        $jsonData->aggs->Parent->aggs->Puestos->aggs->Datos->top_hits->size = 1;
        $jsonData->aggs->Parent->aggs->Puestos->aggs->Datos->top_hits->_source = new stdClass();
        $jsonData->aggs->Parent->aggs->Puestos->aggs->Datos->top_hits->_source->excludes = ['Id', 'UltimaModificacion.*', 'Alta.*',
            'Tipo'];
        $jsonData->aggs->Parent->aggs->Puestos->aggs->Datos->top_hits->sort = new stdClass();
        $jsonData->aggs->Parent->aggs->Puestos->aggs->Datos->top_hits->sort->{'UltimaModificacion.Fecha'} = new stdClass();
        $jsonData->aggs->Parent->aggs->Puestos->aggs->Datos->top_hits->sort->{'UltimaModificacion.Fecha'}->order = 'desc';

        $this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(Puestos::getIndex(), '_search', json_encode($jsonData), $resultado, $codigoRetorno, 'track_total_hits=true')) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($resultado['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado));
            return false;
        }

        $puestos = [];
        foreach ($resultado['aggregations']['Parent']['Puestos']['buckets'] as $item)
            $puestos[$item['Datos']['hits']['hits'][0]['_id'] . 'p'] = $item['Datos']['hits']['hits'][0]['_source'];


        foreach ($resultado['hits']['hits'] as ['_routing' => $idPuesto, '_source' => $source]) {
            $idPuesto = $idPuesto . 'p';
            $dia = (int)$source['Dia']['Numero'];
            $horario = new stdClass();
            $horario->gte = new DateTime($source['Horario']['gte']);
            $horario->lte = new DateTime($source['Horario']['lte']);
            $temp[$dia][$idPuesto] = [
                'id' => (int)$idPuesto,
                'dia' => $source['Dia']['Descripcion'],
                'horario' => (object)$source['Horario'],
                'desde' => $source['HoraDesde'],
                'hasta' => $source['HoraHasta'],
                'puesto' => $puestos[$idPuesto],
            ];
            $horas[$dia][$idPuesto] = $horario;
        }

        /*print_r($horas);
        if(!empty($puestoNuevo)) {
            $temp = array_merge_recursive($temp, $puestoNuevo['desempeno']);
            $horas = array_merge_recursive($horas, $puestoNuevo['horas']);
        }
        print_r($horas);die;*/

        foreach ($horas as $dia => $horas_del_dia) {

            if (!empty($puestoNuevo['desempeno'][$dia]))
                $temp[$dia] = array_merge($temp[$dia], $puestoNuevo['desempeno'][$dia]);
            if (!empty($puestoNuevo['horas'][$dia]))
                $horas_del_dia = array_merge($horas_del_dia, $puestoNuevo['horas'][$dia]);

            $idPuesto = array_key_first($horas_del_dia);
            $primero = array_shift($horas_del_dia);


            while (count($horas_del_dia) >= 1) {

                foreach ($horas_del_dia as $ip_puesto => $hora) {
                    if (FuncionesPHPLocal::areDateRangesOverlapping($primero, $hora)) {
                        $colisiones[$dia][] = [$temp[$dia][$idPuesto], $temp[$dia][$ip_puesto]];
                        $haySuperposicion[$dia] = true;
                    }
                }
                $idPuesto = array_key_first($horas_del_dia);
                $primero = array_shift($horas_del_dia);
            }
        }

        $resumen['hay_conflictos'] = array_reduce($haySuperposicion, function ($carry, $item) {
            return $carry || $item;
        }, false);
        $resumen['conflictos_dias'] = $haySuperposicion;
        $resumen['colisiones'] = $colisiones;

        return true;
    }


    /**
     * @param array      $datos
     * @param array|null $resultado
     * @param int|null   $numfilas
     * @param int|null   $total
     *
     * @return bool
     */
    public
    function buscarPersonasxEscuela(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {
        $sort = true;
        $SortField = 'IdPersona';
        $SortOrder = 'asc';
        $size = $datos['size'] ?? 10;
        $from = $datos['from'] ?? 0;

        $sort = [];
        $ss = -1;
        $sort[++$ss] = new StdClass;
        $sort[$ss]->sorter = new StdClass;
        $sort[$ss]->sorter->order = $SortOrder;

        $jsonData = new stdClass();
        $jsonData->size = 0;
        $jsonData->query = new stdClass();
        $jsonData->query->bool = new stdClass();
        $filter = [];
        $ff = 0;
        $filter[$ff] = new stdClass();
        $filter[$ff]->term = new stdClass();
        $filter[$ff]->term->Tipo = new stdClass();
        $filter[$ff]->term->Tipo->value = 'Persona';

        $filter[++$ff] = new stdClass();
        $filter[$ff]->bool = new stdClass();
        $filter[$ff]->bool->must_not = [new stdClass()];
        $filter[$ff]->bool->must_not[0]->term = new stdClass();
        $filter[$ff]->bool->must_not[0]->term->Estado = new stdClass();
        $filter[$ff]->bool->must_not[0]->term->Estado->value = ELIMINADO;

        $filter[++$ff] = new stdClass();
        $filter[$ff]->has_parent = new stdClass();
        $filter[$ff]->has_parent->parent_type = 'Puesto';
        $filter[$ff]->has_parent->query = new stdClass();
        $filter[$ff]->has_parent->query->bool = new stdClass();
        $filter[$ff]->has_parent->query->bool->filter = [new stdClass()];
        $filter[$ff]->has_parent->query->bool->filter[0]->bool = new stdClass();
        $filter[$ff]->has_parent->query->bool->filter[0]->bool->must_not = [new stdClass()];
        $filter[$ff]->has_parent->query->bool->filter[0]->bool->must_not[0]->term = new stdClass();
        $filter[$ff]->has_parent->query->bool->filter[0]->bool->must_not[0]->term->Estado = new stdClass();
        $filter[$ff]->has_parent->query->bool->filter[0]->bool->must_not[0]->term->Estado->value = ELIMINADO;

        if (!FuncionesPHPLocal::isEmpty($datos['IdEscuela'])) {
            $filter[$ff]->has_parent->query->bool->filter[1] = new stdClass();
            $filter[$ff]->has_parent->query->bool->filter[1]->term = new stdClass();
            $filter[$ff]->has_parent->query->bool->filter[1]->term->{'Escuela.Id'} = new stdClass();
            $filter[$ff]->has_parent->query->bool->filter[1]->term->{'Escuela.Id'}->value = (int)$datos['IdEscuela'];
        }


        if (!FuncionesPHPLocal::isEmpty($datos['IdPersona'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->term = new stdClass();
            $filter[$ff]->term->IdPersona = new stdClass();
            $filter[$ff]->term->IdPersona->value = (int)$datos['IdPersona'];
        }

        if (isset($datos['TieneConflictos']) && '' !== $datos['TieneConflictos']) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->term = new stdClass();
            $filter[$ff]->term->TieneConflictos = new stdClass();
            $filter[$ff]->term->TieneConflictos->value = (bool)$datos['TieneConflictos'];
        }

        $jsonData->query->bool->filter = $filter;

        $jsonData->aggs = new stdClass();
        $jsonData->aggs->Total = new stdClass();
        $jsonData->aggs->Total->cardinality = new stdClass();
        $jsonData->aggs->Total->cardinality->field = 'IdPersona';
        $jsonData->aggs->Personas = new stdClass();
        $jsonData->aggs->Personas->terms = new stdClass();
        $jsonData->aggs->Personas->terms->field = 'IdPersona';
        $jsonData->aggs->Personas->terms->size = 10000;
        $jsonData->aggs->Personas->aggs = new stdClass();
        $jsonData->aggs->Personas->aggs->Datos = new stdClass();
        $jsonData->aggs->Personas->aggs->Datos->top_hits = new stdClass();
        $jsonData->aggs->Personas->aggs->Datos->top_hits->size = 1;
        $jsonData->aggs->Personas->aggs->Datos->top_hits->_source = ['Nombre', 'Apellido'];
        $jsonData->aggs->Personas->aggs->sorter = new stdClass();
        $jsonData->aggs->Personas->aggs->sorter->avg = new stdClass();
        $jsonData->aggs->Personas->aggs->sorter->avg->field = $SortField;
        $jsonData->aggs->Personas->aggs->orden = new stdClass();
        $jsonData->aggs->Personas->aggs->orden->bucket_sort = new stdClass();
        $jsonData->aggs->Personas->aggs->orden->bucket_sort->sort = $sort;
        $jsonData->aggs->Personas->aggs->orden->bucket_sort->size = $size;
        $jsonData->aggs->Personas->aggs->orden->bucket_sort->from = $from;


        $cuerpo = json_encode($jsonData);
        $this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(Puestos::getIndex(), '_search', $cuerpo, $resultado_consulta, $codigoRetorno, 'track_total_hits=true')) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (empty($resultado_consulta['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado_consulta));
            return false;
        }
        $this->setError('', '');

        $resultado = $resultado_consulta['aggregations']['Personas']['buckets'];
        $total = (int)$resultado_consulta['aggregations']['Total']['value'];
        $numfilas = (int)count($resultado);

        return true;
    }

    /**
     * @param array      $datos
     * @param array|null $resumen
     * @param int|null   $total
     * @param array|null $cargos_computables
     *
     * @return bool
     */
    public function validarModulosHorasAtendidos(array $datos, ?array &$resumen, ?int &$total = null, ?array $cargos_computables = []): bool {
        $sortField = $datos['sidx'] ?? 'Horas';
        $sortOrder = $datos['sord'] ?? 'desc';
        $jsonData = new stdClass();
        $size = (int)($datos['size'] ?? 10);
        $from = (int)($datos['from'] ?? 0);
        $jsonData->query = new stdClass();
        $jsonData->size = 0;
        $jsonData->query->bool = new stdClass();
        $filter = [];
        $ff = -1;
        $must_not = [];
        $mn = -1;
        $filter[++$ff] = new stdClass();
        $filter[$ff]->term = new stdClass();
        $filter[$ff]->term->Tipo = new stdClass();
        $filter[$ff]->term->Tipo->value = 'Persona';


        $filter[++$ff] = new stdClass();
        $filter[$ff]->bool = new stdClass();
        $filter[$ff]->bool->must_not = [new stdClass()];
        $filter[$ff]->bool->must_not[0]->terms = new stdClass();
        $filter[$ff]->bool->must_not[0]->terms->Estado = [ELIMINADO, NOACTIVO];


        if (!FuncionesPHPLocal::isEmpty($datos['IdPersona'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->term = new stdClass();
            $filter[$ff]->term->IdPersona = (int)$datos['IdPersona'];
        }

        $filter[++$ff] = new stdClass();
        $filter[$ff]->has_parent = new stdClass();
        $filter[$ff]->has_parent->parent_type = 'Puesto';
        $filter[$ff]->has_parent->query = new stdClass();
        $filter[$ff]->has_parent->query->bool = new stdClass();
        $filter[$ff]->has_parent->query->bool->filter = [new stdClass()];
        $filter[$ff]->has_parent->query->bool->filter[0]->bool = new stdClass();
        $filter[$ff]->has_parent->query->bool->filter[0]->bool->must_not = [new stdClass()];
        $filter[$ff]->has_parent->query->bool->filter[0]->bool->must_not[0]->term = new stdClass();
        $filter[$ff]->has_parent->query->bool->filter[0]->bool->must_not[0]->term->Estado = new stdClass();
        $filter[$ff]->has_parent->query->bool->filter[0]->bool->must_not[0]->term->Estado->value = ELIMINADO;

        if (!FuncionesPHPLocal::isEmpty($datos['IdEscuela'])) {
            $filter[$ff]->has_parent->query->bool->filter[1] = new stdClass();
            $filter[$ff]->has_parent->query->bool->filter[1]->term = new stdClass();
            $filter[$ff]->has_parent->query->bool->filter[1]->term->{'Escuela.Id'} = new stdClass();
            $filter[$ff]->has_parent->query->bool->filter[1]->term->{'Escuela.Id'}->value = (int)$datos['IdEscuela'];
        }


        $must_not[++$mn] = new stdClass();
        $must_not[$mn]->nested = new stdClass();
        $must_not[$mn]->nested->path = 'EstadoPersona.Licencia';
        $must_not[$mn]->nested->query = new stdClass();
        $must_not[$mn]->nested->query->bool = new stdClass();
        $must_not[$mn]->nested->query->bool->must = [new stdClass(), new stdClass()];
        $must_not[$mn]->nested->query->bool->must[0]->term = new stdClass();
        $must_not[$mn]->nested->query->bool->must[0]->term->{'EstadoPersona.Licencia.Fechas'} = new stdClass();
        $must_not[$mn]->nested->query->bool->must[0]->term->{'EstadoPersona.Licencia.Fechas'}->value = 'now';
        $must_not[$mn]->nested->query->bool->must[1]->terms = new stdClass();
        $must_not[$mn]->nested->query->bool->must[1]->terms->{'EstadoPersona.Licencia.Articulo.Id'} = ARTICULOS_IGNORAN_CARGOS;


        $jsonData->query->bool->filter = $filter;
        $jsonData->query->bool->must_not = $must_not;

        $jsonData->aggs = new stdClass();
        $jsonData->aggs->Personas = new stdClass();
        $jsonData->aggs->Personas->terms = new stdClass();
        $jsonData->aggs->Personas->terms->field = 'IdPersona';
        $jsonData->aggs->Personas->terms->size = 10000;
        $jsonData->aggs->Personas->aggs = new stdClass();
        $jsonData->aggs->Personas->aggs->Persona = new stdClass();
        $jsonData->aggs->Personas->aggs->Persona->top_hits = new stdClass();
        $jsonData->aggs->Personas->aggs->Persona->top_hits->size = 1;
        $jsonData->aggs->Personas->aggs->Persona->top_hits->_source = ['Nombre', 'Apellido', 'IdPersona'];
        $jsonData->aggs->Personas->aggs->Resumen = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->parent = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->parent->type = 'Persona';
        $jsonData->aggs->Personas->aggs->Resumen->aggs = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Materias = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Materias->filter = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Materias->filter->exists = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Materias->filter->exists->field = 'Materia.Id';
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Materias->aggs = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Materias->aggs->Horas = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Materias->aggs->Horas->sum = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Materias->aggs->Horas->sum->field = 'Horas';
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Materias->aggs->Modulos = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Materias->aggs->Modulos->sum = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Materias->aggs->Modulos->sum->field = 'Modulos';
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Horas = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Horas->sum = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Horas->sum->field = 'Horas';
        /*$jsonData->aggs->Personas->aggs->Resumen->aggs->Modulos = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Modulos->sum = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->Modulos->sum->field = 'Modulos';*/
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos->filter = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos->filter->bool = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos->filter->bool->filter = [new stdClass, new stdClass];
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos->filter->bool->filter[0]->term = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos->filter->bool->filter[0]->term->{'Cargo.Jerarquico'} = true;
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos->filter->bool->filter[1]->terms = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos->filter->bool->filter[1]->terms->{'Cargo.Id'} = $cargos_computables;
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos->aggs = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos->aggs->Cardinality = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos->aggs->Cardinality->cardinality = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosJerarquicos->aggs->Cardinality->cardinality->field = 'Cargo.Id';

        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos->filter = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos->filter->bool = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos->filter->bool->filter = [new stdClass, new stdClass];
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos->filter->bool->filter[0]->term = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos->filter->bool->filter[0]->term->{'Cargo.Jerarquico'} = false;
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos->filter->bool->filter[1]->terms = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos->filter->bool->filter[1]->terms->{'Cargo.Id'} = $cargos_computables;
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos->aggs = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos->aggs->Cardinality = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos->aggs->Cardinality->cardinality = new stdClass();
        $jsonData->aggs->Personas->aggs->Resumen->aggs->CargosNoJerarquicos->aggs->Cardinality->cardinality->field = 'Cargo.Id';

        $jsonData->aggs->Personas->aggs->sort = new stdClass();
        $jsonData->aggs->Personas->aggs->sort->bucket_sort = new stdClass();
        $jsonData->aggs->Personas->aggs->sort->bucket_sort->sort = [];
        $jsonData->aggs->Personas->aggs->sort->bucket_sort->sort[0] = new stdClass();
        $jsonData->aggs->Personas->aggs->sort->bucket_sort->sort[0]->{'Resumen.' . $sortField} = new stdClass();
        $jsonData->aggs->Personas->aggs->sort->bucket_sort->sort[0]->{'Resumen.' . $sortField}->order = $sortOrder;
        $jsonData->aggs->Personas->aggs->sort->bucket_sort->size = $size;
        $jsonData->aggs->Personas->aggs->sort->bucket_sort->from = $from;


        $jsonData->aggs->Total = new stdClass();
        $jsonData->aggs->Total->cardinality = new stdClass();
        $jsonData->aggs->Total->cardinality->field = 'IdPersona';


        $this->cnx->setDebug(false);
        $cuerpo = json_encode($jsonData);
        if (!$this->cnx->sendPost(Puestos::getIndex(), '_search', $cuerpo, $resultado, $codigoRetorno, 'track_total_hits=true')) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($resultado['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado));
            return false;
        }

        $resumen = $resultado['aggregations']['Personas']['buckets'];
        $total = $resultado['aggregations']['Total']['value'];

        return true;
    }

    /**
     * Valida que las licencias de la persona no sean mayores que el maximo por articulo
     *
     *
     * @param array      $datos
     * @param array|null $resumen
     *
     * @return bool
     * @todo arma un script para las licencias infinitas
     */
    public
    function validarLongitudLicencias(array $datos, ?array &$resumen): bool {
        $resumen = [
            'cantidad_licencias' => 0,
            'cantidad_dias' => 0,
            'articulos' => [],
        ];
        $jsonData = new stdClass();
        $jsonData->size = 0;
        $jsonData->query = new stdClass();
        $jsonData->query->bool = new stdClass();
        $filter = [];
        $ff = -1;

        $filter[++$ff] = new stdClass();
        $filter[$ff]->term = new stdClass();
        $filter[$ff]->term->{'Persona.Id'} = new stdClass();
        $filter[$ff]->term->{'Persona.Id'}->value = (int)$datos['IdPersona'];

        if (!FuncionesPHPLocal::isEmpty($filter))
            $jsonData->query->bool->filter = $filter;

        $jsonData->aggs = new stdClass();
        $jsonData->aggs->Articulos = new stdClass();
        $jsonData->aggs->Articulos->terms = new stdClass();
        $jsonData->aggs->Articulos->terms->field = 'Articulo.Id';
        $jsonData->aggs->Articulos->terms->size = 100;
        $jsonData->aggs->Articulos->aggs = new stdClass();
        $jsonData->aggs->Articulos->aggs->datosArticulo = new stdClass();
        $jsonData->aggs->Articulos->aggs->datosArticulo->top_hits = new stdClass();
        $jsonData->aggs->Articulos->aggs->datosArticulo->top_hits->size = 1;
        $jsonData->aggs->Articulos->aggs->datosArticulo->top_hits->_source = ['Articulo.Descripcion', 'Articulo.CantidadMaximaDias'];
        $jsonData->aggs->Articulos->aggs->Longitud = new stdClass();
        $jsonData->aggs->Articulos->aggs->Longitud->sum = new stdClass();
        $jsonData->aggs->Articulos->aggs->Longitud->sum->field = 'Duracion';
        $jsonData->aggs->Longitud = new stdClass();
        $jsonData->aggs->Longitud->sum = new stdClass();
        $jsonData->aggs->Longitud->sum->field = 'Duracion';

        $cuerpo = json_encode($jsonData);

        if (!$this->cnx->sendPost(Licencias::getIndex(), '_search', $cuerpo, $resultado, $codigoRetorno, 'track_total_hits=true')) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($resultado['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado));
            return false;
        }

        $resumen['cantidad_licencias'] = (int)$resultado['hits']['total']['value'];
        $resumen['cantidad_dias'] = (int)$resultado['aggregations']['Longitud']['value'];
        foreach ($resultado['aggregations']['Articulos']['buckets'] as $bucket) {
            $resumen['articulos'][] = [
                'id' => (int)$bucket['key'],
                'nombre' => $bucket['datosArticulo']['hits']['hits'][0]['_source']['Articulo']['Descripcion'],
                'cantidad_maxima_dias' => (int)$bucket['datosArticulo']['hits']['hits'][0]['_source']['Articulo']['CantidadMaximaDias'],
                'cantidad_licencias' => (int)$bucket['doc_count'],
                'cantidad_dias' => (int)$bucket['Longitud']['value'],
                'porcentaje_maximo' => (float)round(100 * $bucket['Longitud']['value'] / $bucket['datosArticulo']['hits']['hits'][0]['_source']['Articulo']['CantidadMaximaDias'], 2),
            ];
        }


        return true;
    }


    /**
     * Verifica si se debe ignorar el agente a la hora de la verificaci?n de incompatibilidades
     *
     * Busca si el agente tiene una licencia que implique que no est? cubriendo el puesto, e.g. _Licencia por cargo de mayor jerarqu?a_,
     *
     * @param array $datos
     *
     * @return bool
     * @throws ExcepcionLogica
     * @todo definir que pasa en otras situaciones similares como cargo en disponibilidad, reubicado, etc.
     *
     */
    public
    function verificarAgenteIgnorable(array $datos): bool {
        return $this->buscarAgenteIgnorable($datos) > 0;
    }

    /**
     * @param array $datos
     *
     * @return int
     * @throws ExcepcionLogica
     */
    private
    function buscarAgenteIgnorable(array $datos): int {
        $cuerpo = Consulta::nueva(null, null)
            ->setQuery(
                Query::bool()
                    ->addFilter(Query::term('Tipo', 'Persona'))
                    ->addFilter(Query::bool()->addMustNot(Query::term('Estado', ELIMINADO)))
                    ->addFilter(Query::term('IdPersona', (int)$datos['IdPersona']))
                    ->addFilter(
                        Query::has_parent(
                            'Puesto',
                            Query::bool()
                                ->addFilter(Query::term('Id', (int)$datos['IdPuesto']))
                                ->addFilter(Query::bool()->addMustNot(Query::term('Estado', ELIMINADO)))
                        )
                    )
                    ->addFilter(
                        Query::bool()
                            ->addShould(
                                Query::nested(
                                    'EstadoPersona.Licencia',
                                    Query::bool()
                                        ->addFilter(Query::terms('EstadoPersona.Licencia.Articulo.Id', ARTICULOS_IGNORAN_CARGOS))
                                        ->addFilter(Query::term('EstadoPersona.Licencia.Fechas', 'now'))

                                ))
//                            ->addShould(Query::bool()->addMustNot(Query::terms('EstadoPersona.Id', [ALT, LIC])))
                    )

            )
            ->toJson();
        $this->cnx->setDebug(false);
        if (!$this->cnx->sendGet(Puestos::getIndex(), '_count', $resultado, $codigoRetorno, '', $cuerpo))
            throw new ExcepcionLogica($this->cnx->getError('error_description'));
        return (int)$resultado['count'] ?? 0;
    }

}
