<?php

use Bigtree\ExcepcionLogica;

include(DIR_CLASES_DB . 'cSolicitudesCoberturaPersona.db.php');

class cSolicitudesCoberturaPersona extends cSolicitudesCoberturaPersonaDB
{
    /**
     * @var Elastic\Conexion|null
     */
    private $conexionES;

    /**
     * Constructor de la clase cSolicitudesCoberturaPersona.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el mÃ©todo getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param Elastic\Conexion|null $conexionES
     * @param mixed $formato
     */
    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO, ?Elastic\Conexion $conexionES = NULL) {
        parent::__construct($conexion, $formato);
        $this->conexionES = $conexionES;
    }

    /**
     * Destructor de la clase cSolicitudesCobertura.
     */
    function __destruct() {
        parent::__destruct();
    }

    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarxCodigo($datos, $resultado, $numfilas);
    }

    public function buscarxSolicitudCobertura($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxSolicitudCobertura($datos, $resultado, $numfilas);
    }

    public function buscarAsignadosxSolicitudCobertura($datos, &$resultado, &$numfilas): bool {
        return parent::buscarAsignadosxSolicitudCobertura($datos, $resultado, $numfilas);
    }

    public function buscarxSolicitudCoberturaPersona($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxSolicitudCoberturaPersona($datos, $resultado, $numfilas);
    }

    public function buscarxSolicitudCoberturaDesempeno($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxSolicitudCoberturaDesempeno($datos, $resultado, $numfilas);
    }

    public function buscarxSolicitudCoberturaxPuesto($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxSolicitudCoberturaxPuesto($datos, $resultado, $numfilas);
    }

    public function buscarxSolicitudCoberturaxPuestoxPersona($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxSolicitudCoberturaxPuestoxPersona($datos, $resultado, $numfilas);
    }

    public function buscarxSolicitudCoberturaxPersona($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxSolicitudCoberturaxPersona($datos, $resultado, $numfilas);
    }

    public function buscarPersonasSobrantes($datos, &$resultado, &$numfilas): bool {
        return parent::buscarPersonasSobrantes($datos, $resultado, $numfilas);
    }

    public function buscarPersonasPuestosSobrantes($datos, &$resultado, &$numfilas): bool {
        return parent::buscarPersonasPuestosSobrantes($datos, $resultado, $numfilas);
    }

    public function buscarxSolicitudCoberturaNull($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxSolicitudCoberturaNull($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function buscarParaElastic($datos, &$resultado, &$numfilas): bool {
        return parent::buscarParaElastic($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function buscarParaElasticXSolicitud($datos, &$resultado, &$numfilas): bool {
        return parent::buscarParaElasticXSolicitud($datos, $resultado, $numfilas);
    }


    /**
     * ### Valida el estado del agente
     *
     * Verifica que el agente este en un estado activo
     * @todo
     *      - Persona licenciada: Ahora lo averiguamos porque quizás estatutariamente lo permite.
     *
     * @param array $datos
     *
     * @return bool
     */
    public function validarPersonaActiva(array $datos): bool {
        $oPersona = new Elastic\Personas($this->conexionES);
        $datos['incluirCampos'] = ['EstadoPersona.*'];
        if (!$oPersona->buscarxCodigo($datos, $datosPersona)) {
            $this->setError($oPersona->getError());
            return false;
        }
        $ret = $datosPersona['EstadoPersona']['Activo'] ?? false;
        if (!$ret)
            $this->setError(
                400,
                'Error, el agente no esta activo, su estado actual es ' . mb_strtolower($datosPersona['EstadoPersona']['Nombre'])
            );

        if (defined('VALIDAR_LICENCIADOS') && VALIDAR_LICENCIADOS && isset($datos['Fechas'])) {
            $oLicencias = new Elastic\Licencias($this->conexionES);
        }

        return $ret;
    }

    /**
     * @param array $datos
     * @return \Generator
     * @throws \Bigtree\ExcepcionLogica
     */
    public function buscarSubSolicitudes(array $datos): Generator {
        if (!parent::buscarParaElasticXSolicitud($datos, $resultado, $numfilas))
            throw new ExcepcionLogica($this->getError('error_description'));

        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            yield $fila;
    }


    public function Insertar($datos, &$codigoInsertado): bool {

        if (!self::_ValidarInsertar($datos))
            return false;

        self::_SetearNull($datos);
        self::_SetearFechas($datos);

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $oAuditoriasSolicitudesCoberturaPersona = new cAuditoriasSolicitudesCoberturaPersona($this->conexion, $this->formato);
        $datos['Id'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;

        if (!$oAuditoriasSolicitudesCoberturaPersona->InsertarLog($datos, $codigoInsertadolog))
            return false;

        if (!$this->_armarDatosElastic($datos, $datosRegistro, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_SOLICITUDCOBERTURA, $this->conexionES);
        if (!$oElastic->Insertar($datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }

    public function Modificar($datos): bool {

        if (!self::_ValidarModificar($datos, $datosRegistro))
            return false;

        self::_SetearNull($datos);
        self::_SetearFechas($datos);

        if (!parent::Modificar($datos))
            return false;

        $oAuditoriasSolicitudesCoberturaPersona = new cAuditoriasSolicitudesCoberturaPersona($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;

        if (!$oAuditoriasSolicitudesCoberturaPersona->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

//        return $this->actualizarElastic($datos);
        return true;
    }

    public function DesignarParaTodos($datos): bool {

        //print_r($datos);die;
        $oSolicitudCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion);
        $oSolicitudCoberturaPuestos = new cSolicitudesCoberturaPuesto($this->conexion);
        if ((int) $datos['Total'] > 1) {

            # SC multipuesto
            # Si asigno 1 persona para:

            if ($datos['IdPuesto'] == 'todos') {

                # Todos los puestos:
                # busco primer registro en scpersona y el resto guardo para borrarlos
                if (!$this->buscarxSolicitudCobertura($datos, $resultado, $numfilas))
                    return false;

                if ($numfilas == 0) {
                    $this->setError(400, 'No se encontraron registros. ');
                    return false;
                }

                $IdSCPersona = null;
                $borrarRegistrosPersonas = [];
                while ($filaPersonas = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                    if (is_null($IdSCPersona)) {
                        $IdSCPersona = $filaPersonas['Id'];
                    } else {
                        $borrarRegistrosPersonas[] = $filaPersonas['Id'];
                    }
                }

                # actualizo persona designada
                $datosModificar = [
                    'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
                    'IdPersonaDesignada' => $datos['IdPersonaDesignada'],
                    'InstrumentoLegal' => $datos['InstrumentoLegal'],
                    'Id' => $IdSCPersona
                ];
                if (!$this->Modificar($datosModificar))
                    return false;

                # actualizo idscpersona de scpuestos con el id del primer registro (upd_SolicitudesCoberturaPuesto_xIdSolicitudCobertura)
                $datosModificar = [
                    'IdSolicitudCoberturaPersona' => $IdSCPersona,
                    'Tildado' => $datos['Tildado'],
                    'IdSolicitudCobertura' => $datos['IdSolicitudCobertura']
                ];
                if (!$oSolicitudCoberturaPuestos->ModificarxSolicitudCobertura($datosModificar)) {
                    $this->setError($oSolicitudCoberturaPuestos->getError());
                    return false;
                }

                # busco en scpuestos los registros relacionados al idscpersona de la sc (sel_SolicitudesCoberturaPuesto_xIdSolicitudCoberturaPersona)
                $datosBuscar = [
                    'IdSolicitudCoberturaPersona' => $IdSCPersona
                ];
                if (!$oSolicitudCoberturaPuestos->BuscarxSolicitudCoberturaPersona($datosBuscar, $resultadoPuestos, $numfilasPuestos)) {
                    $this->setError($oSolicitudCoberturaPuestos->getError());
                    return false;
                }
                //var_dump($numfilasPuestos, $resultadoPuestos);echo PHP_EOL;//die;
                # PHP actualizo idpuesto en desempeño y elimino sobrante
                if ($numfilasPuestos > 0) {
                    $id = $idPuesto = false;
                    while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoPuestos)) {
                        # guardo el primer id-puesto
                        if ($idPuesto != $fila['IdPuesto']) {
                            $idPuesto = $fila['IdPuesto'];
                            $id = $fila['Id'];
                        }

                        if ($idPuesto == $fila['IdPuesto'] && $id != $fila['Id']) {
                            # actualizar en scDesempeño, idscpuesto repetido ($fila['Id']) por unico ($id)
                            $datosModificar = [
                                'IdSolicitudCoberturaPuestoNuevo' => $id,
                                'Tildado' => $datos['Tildado'],
                                'IdSolicitudCoberturaPuesto' => $fila['Id'],
                            ];
                            if (!$oSolicitudCoberturaDesempeno->ModificarxSolicitudCoberturaPuesto($datosModificar)) {
                                $this->setError($oSolicitudCoberturaDesempeno->getError());
                                return false;
                            }

                            # borrar de scPuesto, registro repetido ($fila['Id']);
                            $datosEliminar = [
                                'Id' => $fila['Id']
                            ];
                            if (!$oSolicitudCoberturaPuestos->Eliminar($datosEliminar)) {
                                $this->setError($oSolicitudCoberturaDesempeno->getError());
                                return false;
                            }
                        } else {
                            $datosModificar = [
                                'Tildado' => $datos['Tildado'],
                                'IdSolicitudCoberturaPuesto' => $fila['Id']
                            ];
                            if (!$oSolicitudCoberturaDesempeno->ModificarTildadoxSolicitudCoberturaPuesto($datosModificar)) {
                                $this->setError($oSolicitudCoberturaDesempeno->getError());
                                return false;
                            }
                        }
                    }
                }

                # elimino registros restantes scpersona según sc (93)
                $datosEliminar = [
                    'Ids' => $borrarRegistrosPersonas,
                ];
                if (!$this->EliminarVarios($datosEliminar))
                    return false;


            } else {
                # Todos los desempeños de un puesto:
                # Insertar en SolicitudesCoberturaPersona (SC, idPersonaDesignada)
                # Actualizar campo idSCPersona en tabla SolicitudesCoberturaPuesto (xidPuesto)

                $datosInsertar = [
                    'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
                    'IdPersonaDesignada' => $datos['IdPersonaDesignada'],
                ];
                if (!$this->Insertar($datosInsertar, $codigoInsertadoPersona))
                    return false;

                $datosModificar = [
                    'IdSolicitudCoberturaPersona' => $codigoInsertadoPersona,
                    'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
                    'Tildado' => $datos['Tildado'],
                    'IdPuesto' => $datos['IdPuesto']
                ];
                if (!$oSolicitudCoberturaPuestos->ModificarxPuestoxSolicitudCobertura($datosModificar)) {
                    $this->setError($oSolicitudCoberturaPuestos->getError());
                    return false;
                }
            }

        } else {

            # SC con único puesto - asignación de una persona a todos los desempeños
            # Obtener Id de 1er registro de SolicitudesCoberturaPersona, con ese buscar el id en SolicitudesCoberturaPuesto y con este Id actualizar en SolicitudesCoberturaDesempeno
            # Buscar registros sobrantes en SolicitudesCoberturaPersona y SolicitudesCoberturaPuesto, y eliminarlos

            if (!$this->ObtenerIdentificadores($datos, $asignaParaTodos, $borrarRegistros, $puestos))
                return false;

            # Actualizar primer registro de SolicitudesCoberturaPersonas con IdPersonaDesignada
            $datosModificarPersona = [
                'Tipo' => 'designacion',
                'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
                'IdPersonaDesignada' => $datos['IdPersonaDesignada'],
                'InstrumentoLegal' => $datos['InstrumentoLegal'],
                'Id' => $asignaParaTodos['IdSCPersona']
            ];
            if (!$this->Modificar($datosModificarPersona))
                return false;

            # Actualiza desempeños
            $datosModificarDesempeno = [
                'IdSolicitudCoberturaPuesto' => $asignaParaTodos['IdSCPuesto'],
                'Tildado' => $datos['Tildado'],
                'IdSCPuestos' => $puestos
            ];
            if (!$oSolicitudCoberturaDesempeno->ModificarxIdPuestos($datosModificarDesempeno)) {
                $this->setError($oSolicitudCoberturaDesempeno->getError());
                return false;
            }

            # Borrar registros
            if (!empty($borrarRegistros['Puestos'])) {
                $datosEliminar['Ids'] = $borrarRegistros['Puestos'];
                if (!$oSolicitudCoberturaPuestos->EliminarVarios($datosEliminar)) {
                    $this->setError($oSolicitudCoberturaPuestos->getError());
                    return false;
                }
            }

            if (!empty($borrarRegistros['Personas'])) {
                $datosEliminar['Ids'] = $borrarRegistros['Personas'];
                if (!self::EliminarVarios($datosEliminar))
                    return false;
            }
        }

        return true;
    }

    private function ObtenerIdentificadores($datos, &$asignaParaTodos, &$borrarRegistros, &$puestos): bool {

        $asignaParaTodos = [
            'IdSCPersona' => '',
            'IdSCPuesto' => '',
        ];
        $borrarRegistros = [
            'Personas' => [],
            'Puestos' => []
        ];
        $puestos = [];
        $primerRegistro = 0;

        if (!$this->buscarxSolicitudCoberturaxPuesto($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

                if ($primerRegistro == 0) {
                    $asignaParaTodos['IdSCPersona'] = $fila['IdSolicitudCoberturaPersona'];
                    $asignaParaTodos['IdSCPuesto'] = $fila['IdSolicitudCoberturaPuesto'];
                    $primerRegistro++;
                } else {
                    $borrarRegistros['Personas'][] = $fila['IdSolicitudCoberturaPersona'];
                    $borrarRegistros['Puestos'][] = $fila['IdSolicitudCoberturaPuesto'];
                }

                $puestos[] = $fila['IdSolicitudCoberturaPuesto'];
            }
        }

        return true;
    }


    public function DesignarParaUnDesempeno($datos): bool {

        # Si asigno directo en un desempeño del puesto

        $oSolicitudCoberturaPuesto = new cSolicitudesCoberturaPuesto($this->conexion, FMT_ARRAY);
        $oSolicitudCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);

        # Buscar persona en SolicitudesCoberturaPersonas si ya se encuentra asignada a un desempeño

        if (!$this->buscarxSolicitudCoberturaxPersona($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            # Si existe: actualizar idscpersona de SCPuesto
            $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $IdSCPersona = $fila['Id'];
            $datosModificar = [
                'IdSolicitudCoberturaPersona' => $IdSCPersona,
                'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
                'IdPuesto' => $datos['IdPuesto'],
                'IdDesempeno' => $datos['IdDesempeno']
            ];

            if (!$oSolicitudCoberturaPuesto->ModificarxSolicitudCoberturaxPuestoxDesempeno($datosModificar)) {
                $this->setError($oSolicitudCoberturaPuesto->getError());
                return false;
            }
        }
        else {
            # No existe:
            # INSERT SolicitudesCoberturaPersona: SC, IdPersonaDesignada
            # INSERT INTO SolicitudesCoberturaPuesto: SCPersona, IdPuesto
            # UPDATE SolicitudesCoberturaDesempeno -> SCPuesto

            $datosInsertar = [
                'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
                'IdPersonaDesignada' => $datos['IdPersonaDesignada'],
                'InstrumentoLegal' => $datos['InstrumentoLegal'],
            ];
            if (!$this->Insertar($datosInsertar, $codigoInsertadoPersona))
                return false;

            $datosInsertar = [
                'IdPuesto' => $datos['IdPuesto'],
                'IdSolicitudCoberturaPersona' => $codigoInsertadoPersona,
                'Tildado' => $datos['Tildado'],
            ];
            if (!$oSolicitudCoberturaPuesto->Insertar($datosInsertar, $codigoInsertadoPuesto)) {
                $this->setError($oSolicitudCoberturaPuesto->getError());
                return false;
            }
            $IdSCPuesto = $codigoInsertadoPuesto;

            # Actualiza desempeño
            $datosModificar = [
                'IdSolicitudCoberturaPuesto' => $IdSCPuesto,
                'Tildado' => $datos['Tildado'],
                'Id' => $datos['IdDesempeno']
            ];
            if (!$oSolicitudCoberturaDesempeno->Modificar($datosModificar)) {
                $this->setError($oSolicitudCoberturaDesempeno->getError());
                return false;
            }
        }

        return true;
    }

    public function EliminarDesignado($datos): bool {

        # si no existe un registro con idpersonadesignada en null, creo uno, sino uso el que existe (Id)
        if (!$this->buscarxSolicitudCoberturaNull($datos, $resultado, $numfilas))
            return false;

        if ($numfilas == 0) {
            if (!$this->Insertar($datos, $codigoInsertado))
                return false;

            $IdSCPersona = $codigoInsertado;
        } else {
            $IdSCPersona = $this->conexion->ObtenerSiguienteRegistro($resultado)['Id'];
        }

        # actualizo SCPUESTO con idscPersona
        $oSolicitudCoberturaPuesto = new cSolicitudesCoberturaPuesto($this->conexion, FMT_ARRAY);
        $oSolicitudCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);

        $datosModificar = [
            'IdSolicitudCoberturaPersona' => $IdSCPersona,
            'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'Tildado' => $datos['Tildado']
        ];

        if ($datos['IdPuesto'] == 'todos') {
            # por todos los puestos
            if (!$oSolicitudCoberturaPuesto->ModificarxSolicitudCobertura($datosModificar)) {
                $this->setError($oSolicitudCoberturaPuesto->getError());
                return false;
            }
        } else {
            $datosModificar['IdPuesto'] = $datos['IdPuesto'];
            if (!FuncionesPHPLocal::isEmpty($datos['IdDesempeno'])) {
                # por desempeño

                if (!$oSolicitudCoberturaPuesto->Insertar($datosModificar, $codigoInsertadoPuesto)) {
                    $this->setError($oSolicitudCoberturaPuesto->getError());
                    return false;
                }
                $IdSCPuesto = $codigoInsertadoPuesto;

                # Actualiza desempeño
                $datosModificar = [
                    'IdSolicitudCoberturaPuesto' => $IdSCPuesto,
                    'Tildado' => $datos['Tildado'],
                    'Id' => $datos['IdDesempeno']
                ];
                if (!$oSolicitudCoberturaDesempeno->Modificar($datosModificar)) {
                    $this->setError($oSolicitudCoberturaDesempeno->getError());
                    return false;
                }
            } else {
                # por puesto
                if (!$oSolicitudCoberturaPuesto->ModificarxPuestoxSolicitudCobertura($datosModificar)) {
                    $this->setError($oSolicitudCoberturaPuesto->getError());
                    return false;
                }
            }
        }

        return true;
    }

    public function Eliminar($datos): bool {

        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        $oAuditoriasSolicitudesCoberturaPersona = new cAuditoriasSolicitudesCoberturaPersona($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!$oAuditoriasSolicitudesCoberturaPersona->InsertarLog($datosLog, $codigoInsertadolog))
            return false;

        if (!parent::Eliminar($datos))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_SOLICITUDCOBERTURA, $this->conexionES);
        $datosRegistro['Tipo'] = [
            'name' => 'Sub-Solicitud',
            'parent' => (int)$datosRegistro['IdSolicitudCobertura']
        ];
        $datosRegistro['IdSolicitud'] = (int)$datosRegistro['IdSolicitudCobertura'];
        if (!$oElastic->Eliminar($datosRegistro)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }

    public function EliminarVarios($datos): bool {

        if (!$this->_ValidarEliminarVarios($datos))
            return false;

        if (empty($datos['Ids']))
            return true;

        return parent::EliminarVarios($datos);
    }

    private function _ValidarEliminarVarios(array &$datos):bool {

        $oSolicitudCoberturaPuesto = new cSolicitudesCoberturaPuesto($this->conexion, FMT_ARRAY);
        foreach ($datos['Ids'] as $key => $id) {
            $datosBuscar['IdSolicitudCoberturaPersona'] = $id;
            if (!$oSolicitudCoberturaPuesto->BuscarxSolicitudCoberturaPersona($datosBuscar, $resultado, $numfilas)) {
                $this->setError($oSolicitudCoberturaPuesto->getError());
                return false;
            }

            if ($numfilas > 0) {
                unset($datos['Ids'][$key]);
            }
        }

        return true;
    }

    public function actualizarNovedadRelacionada(array $datos): bool {

        if (!parent::actualizarNovedadRelacionada($datos))
            return false;

        /*if (!$this->_armarDatosElastic($datos, $datosRegistro, $datosElastic))
            return false;*/

        return true;
    }

    private function _ValidarInsertar($datos) {

        return self::_ValidarDatosVacios($datos);
    }

    private function _ValidarModificar($datos, &$datosRegistro) {

        if (!parent::buscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
            return false;
        }

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!self::_ValidarDatosVacios($datos))
            return false;

        return true;
    }


    private function _ValidarModificarxSC($datos, &$datosRegistro) {

        if (!parent::buscarxSolicitudCobertura($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
            return false;
        }

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!self::_ValidarDatosVacios($datos))
            return false;

        return true;
    }

    private function _ValidarEliminar($datos, &$datosRegistro) {

        if (!$this->buscarParaElastic($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }

    private function _SetearNull(&$datos): void {

        if (!isset($datos['IdNovedad']) || $datos['IdNovedad'] == '')
            $datos['IdNovedad'] = 'NULL';

        if (!isset($datos['InstrumentoLegal']) || $datos['InstrumentoLegal'] == '')
            $datos['InstrumentoLegal'] = 'NULL';

        if (!isset($datos['CantidadHorasModulos']) || $datos['CantidadHorasModulos'] == '')
            $datos['CantidadHorasModulos'] = 'NULL';

        if (!isset($datos['IdPersonaPropuesta']) || $datos['IdPersonaPropuesta'] == '')
            $datos['IdPersonaPropuesta'] = 'NULL';

        if (!isset($datos['IdPersonaDesignada']) || $datos['IdPersonaDesignada'] == '')
            $datos['IdPersonaDesignada'] = 'NULL';
    }

    private function _SetearFechas(&$datos): void {

        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
    }

    private function _ValidarDatosVacios($datos) {

        if (FuncionesPHPLocal::isEmpty($datos['IdSolicitudCobertura'])) {
            $this->setError(400, 'Debe ingresar una solicitud de cobertura');
            return false;
        }

        if (isset($datos['Tipo']) && $datos['Tipo'] == 'designacion') {
            if (FuncionesPHPLocal::isEmpty($datos['IdPersonaDesignada'])) {
                $this->setError(400, 'Debe asignar una persona');
                return false;
            }
        }

        return true;
    }

    private function _armarDatosElastic(array $datos, ?array &$datosRegistro, ?stdClass &$datosElastic): bool {

        if (empty($datosRegistro)) {
            if (!$this->buscarParaElastic($datos, $resultado, $numfilas))
                return false;

            if ($numfilas != 1) {
                $this->setError(400, "Debe ingresar código válido");
                return false;
            }
            $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        }

        $datosRegistro['Tipo'] = 'Sub-Solicitud';
        $datosElastic = Elastic\SolicitudCobertura::armarDatosElastic($datosRegistro);
        return true;
    }


    private function actualizarElastic(array $datos): bool {
        $oElastic = new Elastic\Modificacion(SUFFIX_SOLICITUDCOBERTURA, $this->conexionES ?? new Elastic\Conexion());
        if (!$this->_armarDatosElastic($datos, $datosRegistro, $datosElastic))
            return false;

        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }
        return true;
    }
}