<?php

namespace Bigtree\Logica;

include (DIR_CLASES_DB.'cMad.db.php');

use Elastic;
use Elastic\Consultas\Query;
use Validaciones;
use accesoBDLocal;
use ManejoErrores;
use Elastic\Consultas;
use Bigtree\Datos\MadDB;
use cServiciosLicencias;

class cMad extends MadDB {

    use ManejoErrores;
    use Validaciones;

    /** Constructor de la clase
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     * @var Elastic\Conexion
     */
    private $conexionES;


    function __construct(accesoBDLocal $conexion, ?Elastic\Conexion $conexionES = null, $formato = FMT_TEXTO) {
        $this->conexionES =& $conexionES;
        parent::__construct($conexion, $formato);
    }

    /**
     * Destructor de la clase
     */
    function __destruct() {
        parent::__destruct();
    }

    public function busquedaAvanzada($datos, &$resultado, &$numfilas): bool {


        $query = " ";
        $exists = false;
    /*
        if (!\FuncionesPHPLocal::isEmpty($datos['filtroNivelTurno'])) {

            foreach ($datos['filtroNivelTurno'] as $key => $r) {

                $query .= $key == 0 ? 'AND' : 'OR';

                # si existe nivel y turno
                if ((isset($r['Nivel']) && $r['Nivel'] <> 0) && (isset($r['Turno']) && $r['Turno'] <> 0)) {
                    $exists = true;
                    $query .= ' (N.IdNivel = '.$r['Nivel'] .' AND ET.IdTurno = '.$r['Turno'].') ';
                }

                # si existe nivel y no turno, o turno es "Todos"
                if ((isset($r['Nivel']) && $r['Nivel'] <> 0) && (!isset($r['Turno']) || ($r['Turno'] == 0))) {
                    $exists = true;
                    $query .= ' (N.IdNivel = '.$r['Nivel'] .') ';
                }
            }
        }*/


        $sparam = [
            'xId' => 0,
            'Id' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "",
            'xAnio' => 0,
            'Anio' => "",
            'xIdNivel' => 0,
            'IdNivel' => "",
            'xIdTipoCargo' => 0,
            'IdTipoCargo' => "",
            //'Query' => $exists ? $query : "",
            'limit' => '',
            'orderby' => "Id ASC"
        ];
        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['Anio']) && $datos['Anio'] != "") {
            $sparam['Anio'] = $datos['Anio'];
            $sparam['xAnio'] = 1;
        }

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }

        if (isset($datos['IdTipoCargo']) && $datos['IdTipoCargo'] != "") {
            $sparam['IdTipoCargo'] = $datos['IdTipoCargo'];
            $sparam['xIdTipoCargo'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        return parent::busquedaAvanzada($sparam, $resultado, $numfilas);
    }

    public function buscarCupofComboAprobado($datos, &$resultado, &$numfilas): bool {

        return parent::buscarCupofComboAprobado($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function busquedaAvanzadaTipoDocumental($datos, &$resultado, &$numfilas): bool {

        $sparam = [
            'xIdNivel' => 0,
            'IdNivel' => "",
            'xIdTipoCargo' => 0,
            'IdTipoCargo' => ""
        ];

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }

        if (isset($datos['IdTipo']) && $datos['IdTipo'] != "") {
            $sparam['IdTipoCargo'] = $datos['IdTipo'];
            $sparam['xIdTipoCargo'] = 1;
        }

        if (!parent::busquedaAvanzadaTipoDocumental($sparam, $resultado, $numfilas)) {
            return false;
        }

        return true;
    }


    public function insertarMad($datos, &$codigoInsertado): bool {

        /** BUSCA QUE NO HAYA UN MAD ACTIVO DE LA ESCUELA */
        $datosBuscar = [
            'IdEscuela' => $datos['IdEscuela'],
            'IdEstado' => [ESTADO_SC_FINALIZADA, ESTADO_SC_ELIMINADA] /** BUSCA SI EXISTEN REGISTROS QUE NO ESTEN FINALIZADOS NI ELIMINADOS */
        ];
        if (!$this->buscarMadActivoxEscuela($datosBuscar, $resultado, $numfilas))
            return false;

        if ($numfilas >= 1) {
            $this->setError(400, 'Ya existe un movimiento activo');
            return false;
        }

        /** BUSCA QUE HAYA CARGOS DE LA COMBINACIÓN DE NIVEL Y TIPO DE CARGO SELECCIONADA */
        $oEp = new \cEscuelasPuestos($this->conexion);
        if (!$oEp->buscarNoVacantesxEscuela($datos, $resultado, $numfilas)) {
            $this->setError($oEp->getError());
            return false;
        }

        if ($numfilas <= 0) {
            $this->setError(400, 'No se encontraron cargos para el nivel y tipo de puesto/plaza elegido.');
            return false;
        }

        /** BUSCA TIPO DOCUMENTAL SEGÚN NIVEL Y TIPO DE CARGO */

        if (!$this->busquedaAvanzadaTipoDocumental($datos, $resultado, $numfilas))
            return false;

        $tipoDocumental = 'ACTUALIZA_PLANTA';
        if ($numfilas == 1)
            $tipoDocumental = $this->conexion->ObtenerSiguienteRegistro($resultado)['ConstanteTipoDocumento'];

        self::_setearDatos($datos);
        self::_setearFechas($datos);

        if (\FuncionesPHPLocal::isEmpty($datos['IdNivel']))
            $datos['IdNivel'] = "NULL";

        if (\FuncionesPHPLocal::isEmpty($datos['IdTipoCargo']))
            $datos['IdTipoCargo'] = "NULL";

        $datos['IdTipoDocumento'] = constant('NOV_'.$tipoDocumental);
        $datos['IdRegistroTipoDocumento'] = constant('NOV_REGISTRO_'.$tipoDocumental);
        $datos['IdAreaInicial'] = $datos['IdArea'] = 1;
        $datos['IdEstado'] = $datos['IdEstadoInicial'] = CIRC_ESTADO_NUEVO_ESCUELA;
        $datos['Estado'] = ACTIVO;

        return parent::insertarMad($datos, $codigoInsertado);
    }

    public function insertarMadPuesto($datos, &$codigoInsertado, &$reloadPuesto): bool {

        if (!$this->_validarInsertar($datos))
            return false;

        self::_setearFechas($datos);
        self::_setearDatos($datos);

        if (!parent::insertarMadPuesto($datos, $codigoInsertado, $reloadPuesto))
            return false;

        $datosEnviar = [
            'IdPuestoDestino' => $datos['IdPuesto'],
            'IdMad' => $datos['IdMad'],
            'Vacante' => 1
        ];
        if (!$this->revisionVacante($datosEnviar, $reloadPuesto))
            return false;

        return true;
    }

    public function revisionVacante($datos, &$reloadPuesto): bool {

        if (!parent::buscarVacante($datos, $resultado, $numfilas))
            return false;

        $reloadPuesto = '';
        if ($numfilas == 1) {
            $reloadPuesto = $this->conexion->ObtenerSiguienteRegistro($resultado)['IdPuesto'];
            if (!$this->modificarVacante($datos))
                return false;
        }

        return true;
    }

    public function insertarMadPuestosPersonas($datos, &$codigoInsertado): bool {

        self::_setearFechas($datos);
        self::_setearDatos($datos);

        return parent::insertarMadPuestosPersonas($datos, $codigoInsertado);
    }

    public function actualizarMadPuesto($datos, &$reloadPuesto): bool {

        if (!parent::eliminarMadPuesto($datos, $reloadPuesto))
            return false;

        if (!$this->insertarMadPuesto($datos, $codigoInsertado, $reloadPuesto))
            return false;

        return true;
    }

    public function eliminarMadPuesto($datos, &$reloadPuesto): bool {

        $datosEnviar = [
            'IdPuestoDestino' => $datos['IdPuesto'],
            'IdMad' => $datos['IdMad'],
            'Vacante' => 0
        ];
        if (!$this->revisionVacante($datosEnviar, $reloadPuesto))
            return false;

        return parent::eliminarMadPuesto($datos, $reloadPuesto);
    }

    public function modificarVacante($datos): bool {

        self::_setearFechas($datos);
        return parent::modificarVacante($datos);
    }

    public function modificarEstado($datos): bool {

        self::_setearFechas($datos);
        return parent::modificarEstado($datos);
    }

    public function enviarWorkflow(array $datos): bool {

        $oDocumentosPermisos = new \cDocumentosPermisos($this->conexion, $this->formato);
        if (!$oDocumentosPermisos->BuscarMetodosAcciones($datos, $resultado, $numfilas))
            return false;

        $accionPosterior = [];
        if ($numfilas > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                if (!empty($fila['AccionClase']) && class_exists($fila['AccionClase'])) {

                    if (self::class == $fila['AccionClase'])
                        $oObjeto = $this;
                    else
                        $oObjeto = new $fila['AccionClase']($this->conexion, $this->formato);

                    if (!empty($fila['AccionMetodoPosterior']) && method_exists($oObjeto, $fila['AccionMetodoPosterior']))
                        $accionPosterior[] = ['objeto' => $oObjeto, 'metodo' => $fila['AccionMetodoPosterior']];

                    if (!empty($fila['AccionMetodoPrevio']) && method_exists($oObjeto, $fila['AccionMetodoPrevio'])) {
                        if (!$oObjeto->{$fila['AccionMetodoPrevio']}($datos)) {
                            $this->setError($oObjeto->getError());
                            return false;
                        }
                    }
                }
            }
        }

        if (!$oDocumentosPermisos->BuscarAreasEnvioxIdWorkflowMadxRol($datos, $resultado, $numfilas))
            return false;

        if ($numfilas < 1) {
            $this->setError(404, 'Error, accion no encontrada.');
            return false;
        }
        $filaWorkflow = $this->conexion->ObtenerSiguienteRegistro($resultado);
        # Circuito - Datos avanzados

        if (!empty($filaWorkflow['Clase']) && class_exists($filaWorkflow['Clase'])) {

            if (self::class == $filaWorkflow['Clase'])
                $oObjeto = $this;
            else
                $oObjeto = new $filaWorkflow['Clase']($this->conexion, $this->formato);
            if (!empty($filaWorkflow['Metodo']) && method_exists($oObjeto, $filaWorkflow['Metodo'])) {
                if (!$oObjeto->{$filaWorkflow['Metodo']}($datos)) {
                    $this->setError($oObjeto->getError());
                    return false;
                }
            }
        }

        $datos['IdMad'] = $datos['IdMad'];
        $datos['IdArea'] = $filaWorkflow['IdAreaFinal'];
        $datos['IdEstado'] = $filaWorkflow['IdEstadoFinal'];
        if (!$this->ModificarAreaEstado($datos))
            return false;

        if (!empty($accionPosterior)) {
            foreach ($accionPosterior as ['objeto' => $objeto, 'metodo' => $metodo]) {
                if (!$objeto->{$metodo}($datos)) {
                    $this->setError($objeto->getError());
                    return false;
                }
            }
        }

        return true;
    }


    public function ModificarAreaEstado($datos): bool {

        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['MovimientoFecha'] = date('Y-m-d H:i:s');
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        if (!parent::ModificarAreaEstado($datos))
            return false;
        return true;
    }

    public function eliminar($datos): bool {

        $datos['Estado'] = MAD_ELIMINADO;

        self::_setearFechas($datos);

        if (!parent::modificarEstadoMadPuestosPersonas($datos))
            return false;

        if (!parent::modificarEstadoMadPuesto($datos))
            return false;

        if (!parent::modificarEstado($datos))
            return false;

        return $this->desbloquearEscuela($datos);
    }


    public function validar($datos, &$error): bool {

        return $this->_validarPuesto($datos, $error);
    }

    public function rechazar($datos): bool {

        if (!$this->modificarEstado($datos))
            return false;

        return $this->desbloquearEscuela($datos);
    }

    public function validarMad($datos): bool {

        return $this->_validarMad($datos);
    }

    public function enviarARevision($datos): bool {

        if (!$this->bloquearEscuela($datos))
            return false;

        $datos['Id'] = $datos['IdMad'];
        if (!$this->_validarMad($datos))
            return false;

        /*self::_setearFechas($datos);
        return parent::modificarEstado($datos);*/
        return true;
    }

    public function aprobarMad($datos): bool {

        if (!$this->bloquearEscuela($datos))
            return false;

        if (!$this->_validarMad($datos))
            return false;

        /** Buscar puestos y personas del cargo origen */
        $datosBuscar['IdMad'] = $datos['Id'] = $datos['IdMad'];
        if (!$this->buscarPuestosPersonasMad($datosBuscar, $resultado_mad, $numfilas_mad))
            return false;

        if ($numfilas_mad <= 0) {
            $this->setError(400, 'No se encontraron servicios en los puestos/plazas seleccionadas');
            return false;
        }

        /** INICIO BLOQUE GUARDAR DATOS EN TABLAS MAD **/

        /** Guarda los puestos destino para dar de baja los datos */
        $puestosDestino  = [];
        /** Guarda los puestos origen con su destino correspondiente para insertar nuevos datos */
        $puestosInsertar = [];

        //x cada pofa que recorro!
        while ($r = $this->conexion->ObtenerSiguienteRegistro($resultado_mad)) {

            $puestosDestino[] = $r['IdPuestoDestino'];
            $puestosInsertar[$r['IdPuestoDestino']][] = $r;

            if (!empty($r['IdPuestoPadre'])) {
                /** Solo si el puesto NO es raiz inserto tambien en MadPuesto*/
                $datosInsertar = [
                    'IdMad' => $datos['Id'],
                    'IdPuestoPadre' => $r['IdPuestoPadre'],
                    'IdPuesto' => $r['IdPuesto'],
                    'IdPuestoDestino' => $r['IdPuestoDestino'],
                    'Vacante' => $r['Vacante']
                ];
                if (!$this->insertarMadPuesto($datosInsertar, $codigoInsertadoPuesto, $reloadPuesto))
                    return false;
            }

            /** Inserta persona de puesto origen */
            $datosInsertar = [
                'IdMad' => $datos['Id'],
                'IdMadPuestos' =>  (empty($r['IdPuestoPadre']) ? $r['IdMadPuesto'] : $codigoInsertadoPuesto),
                'IdPofa' => $r['IdPofa'],
                'IdPuesto' => $r['IdPuesto'],
                'IdPersona' => $r['IdPersona'],
            ];
            if (!$this->insertarMadPuestosPersonas($datosInsertar, $codigoInsertado))
                return false;
        }
        $puestosDestino = array_unique($puestosDestino);
        /** FIN BLOQUE GUARDAR DATOS EN TABLAS MAD **/

        $oEp  = new \cEscuelasPuestos($this->conexion, $this->conexionES);
        $oEpp = new \cEscuelasPuestosPersonas($this->conexion, $this->conexionES);
        $oEpd = new \cEscuelasPuestosDesempeno($this->conexion, $this->conexionES);

        /** Guarda datos del registro de puesto destino para copiar */
        $detallePuestoDestino = [];

        /* Array de novedades de Baja */
        $vecNovedadesBajas = array();

        /** INICIO BLOQUE BAJA DESTINO */
        foreach ($puestosDestino as $rp) {

            /** Busco datos de puesto */
            $datosBuscar['IdPuesto'] = $rp;
            if (!$this->buscarPuestosPersonasDestinoMad($datosBuscar, $resultado_dest, $numfilas_dest))
                return false;

            if ($numfilas_dest <= 0) {
                $this->setError(400, 'No se encontro el puesto de destino con ID '.$datosBuscar['IdPuesto']);
                return false;
            }

            while ($filaDestino = $this->conexion->ObtenerSiguienteRegistro($resultado_dest)) {

                /** Elimina persona */
                if ($filaDestino['EstadoPersona'] == ACTIVO) {
                    $ayer= date("Y-m-d 23:59:59", strtotime("-1 day"));
                    $datosModificar = [
                            'IdPofa' => $filaDestino['IdPofa'],
                            'FechaHasta' => $ayer,//AYER!
                            'Estado' => NOACTIVO//ELIMINADO,
                    ];
                    #PASO EL ESTADO DEL PUESTO PERSONA A CESADO-NOACTIVO
                    if (!$oEpp->ModificarEstadoFechaHasta($datosModificar)) {
                        $this->setError($oEpp->getError());
                        return false;
                    }


                    //armo novedad de cese!!
                    $vecNovedadesBajas[]=$datosModificar;
                }

                /** Elimina hijo y desempe�os de puesto padre destino */
                if (!empty($filaDestino['IdPuestoPadre'])) {
                    //elimno el puesto hijo
                    $datosModificar = [
                        'IdPuesto' => $filaDestino['IdPuesto'],
                        'Estado' => ELIMINADO,
                    ];
                    //elimino el EscuelaPuesto si es hijo -> le paso e estado a 30
                    if (!$oEp->ModificarEstadoBD($datosModificar)) {
                        $this->setError($oEp->getError());
                        return false;
                    }
                    //elimino desempeños del IdPuestoHijo
                    if (!$oEpd->EliminarxIdPuesto($filaDestino)) {
                        $this->setError($oEpd->getError());
                        return false;
                    }
                } else {
                    //si no es padre , paso toda la info del puesto-persona a un array $detallePuestoDestino
                    $detallePuestoDestino[$filaDestino['IdPuesto']] = $filaDestino;
                }
            }
        }
        /** FIN BLOQUE BAJA DESTINO */

        /** INICIO BLOQUE BAJA ORIGEN Y MOVIMIENTOS */
        /** Guarda puesto a dar de baja y alta*/
        $puestosLicencias = []; $i = 0;

        //array de novedades de alta
        $vecNovedadesAltas = array();

        foreach ($puestosInsertar as $key_idPuestoDestino => $puestosIns) {

            $datosBuscar['IdPuesto'] = $key_idPuestoDestino;

            /** busco datos de desempe�o */
            if (!$oEpd->BuscarxCodigo($datosBuscar, $resultado_desemp, $numfilas_desemp)) {
                $this->setError($oEp->getError());
                return false;
            }
            $puestoDesempenoDestino = [];
            if ($numfilas_desemp > 0) {
                while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado_desemp))
                    $puestoDesempenoDestino[] = $fila;
            }

            $puestoDestino = $detallePuestoDestino[$key_idPuestoDestino];



            foreach ($puestosIns as $p) {

                /** INICIO BLOQUE BAJA ORIGEN */
                /** Eliminar persona */
                $ayer= date("Y-m-d 23:59:59", strtotime("-1 day"));
                $datosModificar = [
                    'IdPofa' => $p['IdPofa'],
                    'FechaHasta' => $ayer,//AYER!
                    'Estado' => NOACTIVO//ELIMINADO,
                ];
                if (!$oEpp->ModificarEstadoBD($datosModificar)) {
                    $this->setError($oEpp->getError());
                    return false;
                }


                /** Elimina hijo y desempe�os de puesto padre origen */
                if (!empty($p['IdPuestoPadre'])) {
                    //elimino puedo hijo que ya no se usa
                    $datosModificar = [
                        'IdPuesto' => $p['IdPuesto'],
                        'Estado' => ELIMINADO,
                    ];
                    if (!$oEp->ModificarEstadoBD($datosModificar)) {
                        $this->setError($oEp->getError());
                        return false;
                    }

                    /** Eliminar desempe�os de puestos hijos */
                    $datosEliminar['IdPuesto'] = $p['IdPuesto'];
                    if (!$oEpd->EliminarxIdPuesto($datosEliminar)) {
                        $this->setError($oEpd->getError());
                        return false;
                    }
                }


                /** FIN BLOQUE BAJA ORIGEN */

                /** INICIO CREAR PUESTOS EN DESTINO */
                /** Si soy hijo, creo puesto y desempe�os */
                if (!empty($p['IdPuestoPadre'])) {
                    $datosInsertar = $puestoDestino;
                    $datosInsertar['IdPuestoPadre'] = $key_idPuestoDestino;
                    $datosInsertar['FechaDesde'] = date('Y-m-d');
                    $datosInsertar['Estado'] = ACTIVO;
                    self::_setearFechas($datosInsertar);
                    if (!$oEp->InsertarDB($datosInsertar, $codigoInsertadoHijo)) {
                        $this->setError($oEp->getError());
                        return false;
                    }

                    $puestoDestino['IdPuesto'] = $codigoInsertadoHijo;

                    if (!empty($puestoDesempenoDestino)) {

                        foreach ($puestoDesempenoDestino as $fila) {
                            $datosIns = $fila;
                            $datosIns['IdPuesto'] = $codigoInsertadoHijo;
                            $datosIns['IdDesempeno'] = 'NULL';
                            if (!$oEpd->InsertarDB($datosIns, $codigoInsertadoDesempeno)) {
                                $this->setError($oEpd->getError());
                                return false;
                            }
                        }
                    }
                }

                /** Inserta personas */
                $datosInsertar = [
                    'IdPuesto' => $puestoDestino['IdPuesto'],
                    'IdPersona' => $p['IdPersona'],
                    'IdRevista' => $p['IdRevista'],
                    'CodigoLiquidador' => $p['CodigoLiquidador'],
                    'FechaDesde' => date('d/m/Y'), //hoy arranca de nuevo en el nuevo puesto
                    'FechaDesignacion' => date('d/m/Y'),//hoy arranca de nuevo en el nuevo puesto
                    'FechaTomaPosesion' => date('d/m/Y'),//hoy arranca de nuevo en el nuevo puesto
                    'Orden' => 1,
                    'InstrumentoLegal' => NULL, //esto deberia venir del MAD completo
                    'IdPofaMigracion' => NULL, ///no se traslada
                    'IgnorarConflictos' => true,
                ];
                self::_setearFechas($datosInsertar);

                if (!$oEpp->InsertarDB($datosInsertar, $codigoInsertadoPersona)) {
                    $this->setError($oEpp->getError());
                    return false;
                }
                //agrego el puesto persona al array de novedades de alta que hay que crear
                $vecNovedadesAltas[]=$datosInsertar;

                /** Guarda nuevo id puesto para modificarlo en LicenciasCargos */
                $puestosLicencias[$i]['IdPersona'] = $p['IdPersona'];
                $puestosLicencias[$i]['IdRevista'] = $p['IdRevista'];
                $puestosLicencias[$i]['IdPuesto'] = $p['IdPuesto'];
                $puestosLicencias[$i]['IdPofaDestino'] = $codigoInsertadoPersona;
                $puestosLicencias[$i++]['IdPuestoDestino'] = $puestoDestino['IdPuesto'];

                /** FIN CREAR PUESTOS EN DESTINO */
            }
        }
        /** FIN BLOQUE BAJA ORIGEN Y MOVIMIENTOS */

        /** DESBLOQUEA */
        if (!$this->desbloquearEscuela($datos))
            return false;

        /** ENV�A A ESTADO 30 LOS CARGOS AFECTADOS DE LICENCIAS ACTIVAS
         *  E INSERTA NUEVO CARGO
         */
        /* pasar a BD*/
        $oLicencias = new cServiciosLicencias($this->conexion);
        if (!$oLicencias->modificarCargosMad($puestosLicencias)) {
            $this->setError($oLicencias->getError());
            return false;
        }

        return $this->modificarElastic($datos);
    }

    protected function bloquearEscuela($datos): bool {

        $datosBloquear = [
            'PofEditable' => 1,
            'PofaEditable' => 1,
            'PofaAdminEditable' => 1,
            'PofEcEditable' => 1,
            'PermiteCargaDesempeno' => 0,
            'IdEscuela' => $datos['IdEscuela']
        ];

        $oE = new \cEscuelasPOF($this->conexion);
        if (!$oE->modificarxIdEscuela($datosBloquear)) {
            $this->setError($oE->getError());
            return false;
        }

        return true;
    }

    /** DESBLOQUEA POF Y PON DE ADMIN/MESA PERO DE DIRECTOR NO */
    protected function desbloquearEscuela($datos): bool {

        $datosBloquear = [
            'PofEditable' => 0,
            'PofaEditable' => 1,
            'PofaAdminEditable' => 0,
            'PofEcEditable' => 1,
            'PermiteCargaDesempeno' => 0,
            'IdEscuela' => $datos['IdEscuela']
        ];

        $oE = new \cEscuelasPOF($this->conexion);
        if (!$oE->modificarxIdEscuela($datosBloquear)) {
            $this->setError($oE->getError());
            return false;
        }

        return true;
    }

    /** Se usa al seleccionar el cargo en el combo. Todav�a no se encuentran los datos insertados en BD */
    protected function _validarPuesto($datos, &$error): bool {

        /** INICIO BLOQUE VALIDACIONES **/
        $error = false;

        /** Guarda todos los puestos no vacantes */
        $noVacantes = [];
        if (!$this->_validarMovimiento($datos, $vacante))
            return false;

        if (!$vacante) {
            $error = true;
            $noVacantes['IdPuesto'] = $datos['IdPuestoDestino'];
        }

        /** Si destino no es vacante */
        if (!empty($noVacantes)) {

            /** Guarda id de puestos origen */
            $puestosOrigen = [];
            /** Busca todos los puestos origen */
            if (!$this->buscarxId($datos, $resultado, $numfilas))
                return false;

            if ($numfilas > 0) {
                while ($r = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                    if (!\FuncionesPHPLocal::isEmpty($r['IdPuesto']))
                        $puestosOrigen[] = $r['IdPuesto'];
                }
                /** Valida que los no vacantes se hayan movido a otro cargo */
                if (!empty($puestosOrigen)) {
                    if (in_array($noVacantes['IdPuesto'], $puestosOrigen))
                        $error = false;
                }
            }
        }
        /** FIN BLOQUE VALIDACIONES **/

        return true;
    }

    protected function _validarMad($datos): bool {


        $datos['Id'] = $datos['IdMad'];

        /** INICIO BLOQUE VALIDACIONES **/
        /** Busca todos los movimientos guardados de Mad */
        if (!$this->buscarxId($datos, $resultado, $numfilas))
            return false;

        /** Guarda todos los puestos no vacantes */
        $noVacantes = [];
        /** Guarda id de puestos origen */
        $puestosOrigen = [];
        $i = 0;

        if ($numfilas <= 0) {
            $this->setError(400, 'No se encontraron datos correspondientes al MAD');
            return false;
        }

        while ($r = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

            if (!\FuncionesPHPLocal::isEmpty($r['IdPuesto'])) {
                $puestosOrigen[] = $r['IdPuesto'];
                $datosBuscar = [
                    'IdPuesto'  => $r['IdPuesto'],
                    'IdPuestoDestino' => $r['IdPuestoDestino'],
                    'IdMad'  =>  $datos['IdMad']
                ];
                if (!$this->_validarMovimiento($datosBuscar, $vacante))
                    return false;

                if (!$vacante) {
                    $noVacantes[$i]['IdPuesto'] = $r['IdPuestoDestino'];
                    $noVacantes[$i++]['Codigo'] = $r['CodigoPuestoDestino'];
                }
            }
        }

        if (empty($puestosOrigen)) {
            $this->setError(400, 'No se realizaron movimientos');
            return false;
        }

        /** Valida que los no vacantes se hayan movido a otro cargo */
        $error = '';
        foreach ($noVacantes as $nv) {
            if (!in_array($nv['IdPuesto'], $puestosOrigen))
                $error .= $nv['Codigo'].' <br>';
        }

        /** Error si fallan validaciones */
        if (!empty($error)) {
            $this->setError(400, 'No es posible validar la acci&oacute;n.
                                  Los puestos seleccionados: '. $error. ' tienen agentes asignados.
                                  Los puestos de destino deben estar vacantes para poder finalizar.');

            return false;
        }
        /** FIN BLOQUE VALIDACIONES **/

        return true;
    }

    public function modificarElastic(array $datos): bool {

        if (!$this->vaciarElastic($datos))
            return false;

        if (!$this->impactarElastic($datos)) {
            $this->vaciarElastic($datos);
            return false;
        }

        #todo cambiar
        return true; #false;
    }

    public function vaciarElastic(array $datos): bool {

        if (empty($datos['IdEscuela'])) {
            $this->setError(400, 'La escuela es obligatoria');
            return false;
        }

        $query = Consultas\Base::nueva(null,null)
        ->setQuery(
            Query::bool()
                ->addShould(Query::term('Escuela.Id', (int)$datos['IdEscuela']))
                ->addShould(Query::has_parent('Puesto', Query::term('Escuela.Id', (int)$datos['IdEscuela'])))
        );

        $this->conexionES->setDebug(false);
        if (!$this->conexionES->sendPost(Elastic\Puestos::getIndex(), '_delete_by_query', $query->toJson(), $resultado, $codigoRetorno)) {
            $this->setError($this->conexionES->getError());
            return false;
        }

        return true;
    }

    public function impactarElastic(array $datos): bool {
        $page_size = 1000;
        $oPuestos    = new \cEscuelasPuestos($this->conexion, $this->conexionES, FMT_ARRAY);
        $oPofa       = new \cEscuelasPuestosPersonas($this->conexion, $this->conexionES, FMT_ARRAY);
        $oDesempenos = new \cEscuelasPuestosDesempeno($this->conexion, $this->conexionES, FMT_ARRAY);

        $oConexionLicencias = new accesoBDLocal(SERVIDORBD, USUARIOBD, CLAVEBD);
        $oConexionLicencias->SeleccionBD(BASEDATOS);
        $oPofaLicencia = new \cEscuelasPuestosPersonas($oConexionLicencias, $this->conexionES, FMT_ARRAY);


        if (!$oPuestos->buscarParaElasticxEscuela($datos, $resultado_puesto, $numfilas_puesto)) {
            $this->setError($oPuestos->getError());
            return false;
        }

        if (!$oPofa->buscarParaElasticxEscuela($datos, $resultado_pofa, $numfilas_pofa)) {
            $this->setError($oPofa->getError());
            return false;
        }

        if (!$oDesempenos->buscarParaElasticxEscuela($datos, $resultado_desempeno, $numfilas_desempeno)) {
            $this->setError($oDesempenos->getError());
            return false;
        }

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);

        $stringBulk = "";
        $i = 0;
        if ($numfilas_puesto > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado_puesto)) {
                $fila['Id']     = $fila['IdPuesto'];
                $fila['Codigo'] = str_pad($fila['IdPuesto'], "8", "0", STR_PAD_LEFT);

                if (!$oPuestos->_armarObjetoElastic($fila, $fila, $datosElastic)) {
                    $this->setError($oPuestos->getError());
                    return false;
                }
                /*
                $jsonSave['update'] = array();
                $jsonSave['update']['_id'] = $fila['IdPuesto'];
                $jsonSave['update']['_index'] = INDEXPREFIX.SUFFIX_PUESTOS;
                */
                $jsonSave['index'] = array();
                $jsonSave['index']['_id'] = $fila['IdPuesto'];
                $jsonSave['index']['_index'] = INDEXPREFIX.SUFFIX_PUESTOS;
                $jsonSaveData = $datosElastic;
                //$jsonSaveData['doc_as_upsert'] = true;

                $stringBulk .= json_encode($jsonSave)."\n";
                $stringBulk .= json_encode($jsonSaveData)."\n";
                $i++;
                if ($i == $page_size) {
                    if (!$oElastic->ActualizarBulkIndex($stringBulk)) {
                        $this->setError($oElastic->getError());
                        return false;
                    }
                    $stringBulk = "";
                    $i = 0;
                }
            }
        }

        if ($numfilas_pofa > 0) {

            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado_pofa)) {

                $datosRegistro = $fila;
                $datosRegistro['Id'] = $fila['IdPofa'];
                $datosRegistro['CodigoRevista'] = $fila['RevistaCodigo'];
                $datosRegistro['DescripcionRevista'] = $fila['RevistaDescripcion'];

                if (isset($datosPersona['Sexo'])) {
                    $datosRegistro['IdSexo'] = $fila['Sexo'];
                    if ($fila['Sexo'] == "X")
                        $datosRegistro['DescripcionSexo']="No Binario";
                    elseif ($fila['Sexo'] == "M")
                        $datosRegistro['DescripcionSexo']="Masculino";
                    else
                        $datosRegistro['DescripcionSexo']="Femenino";
                   // $datosRegistro['DescripcionSexo'] = ($fila['Sexo'] == "F" ) ? "Femenino":"Masculino";
                }

                if (!$oPofa->_armarObjetoElastic($datosRegistro, $datosRegistro, $datosElastic)) {
                    $this->setError($oPofa->getError());
                    return false;
                }

                /*BLANQUEO EL ARRAY DE LICENCIAS DEL PUESTO*/
                $datosElastic->EstadoPersona->Licencia = [];
                $datosElastic->EstadoPersona->Id = 1;
                $datosElastic->EstadoPersona->Descripcion = 'Activo';
                $datosElastic->EstadoPersona->Codigo = 'ACT';

                /** BUSCO SI TIENE LICENCIA ACTIVA */

                if (!in_array($fila['EstadoPuesto'], [ELIMINADO, NOACTIVO]) && $fila['Estado'] == ACTIVO) {

                    $datosBuscar['IdPofa'] = $fila['IdPofa'];
                    if (!$oPofaLicencia->buscarLicenciasxPofa($datosBuscar, $resultado_lic, $numfilas_lic)) {
                        $this->setError($oPofaLicencia->getError());
                        return false;
                    }

                    if ($numfilas_lic > 0) {

                        $b = [];
                        while ($rs = $oConexionLicencias->ObtenerSiguienteRegistro($resultado_lic)) {

                            $b[] = [
                                'Prioridad' => $rs['Prioridad'],
                                'Articulo' => [
                                    'Id' => $rs['IdArticulo'],
                                    'Codigo' => isset($rs['ArticuloCodigo'])?utf8_encode($rs['ArticuloCodigo']):"NULL",
                                    'Descripcion' => isset($rs['ArticuloDescripcion'])?utf8_encode($rs['ArticuloDescripcion']):"NULL"
                                ],
                                'Fechas' => [
                                    'gte' => substr($rs['Inicio'], 0, 10),
                                    'lte' => substr($rs['Fin'],0, 10),
                                ],
                                'FechaFinAbierta' => (bool) $rs["FechaFinAbierta"],
                                'Motivo' => utf8_encode($rs['MotivoDescripcion']),
                                'IdMotivo' => $rs['IdMotivo'],
                                'Pendiente' => NULL,
                                'Id' => $rs['IdLicencia'],
                                'FechaHastaEstimada' => substr($rs['FechaHastaEstimada'], 0, 10),
                                'Estado' => $rs['IdEstado']
                            ];
                        }
                        if(isset($this->licencia['FechaReintegro']) && $this->licencia['FechaReintegro']!="")
                            $b["FechaReintegro"]= $this->licencia['FechaReintegro'];

                        $datosElastic->EstadoPersona = [
                            'Id' => 2,
                            'Descripcion' => 'Licenciado',
                            'Codigo' => 'LIC',
                            'Licencia' => $b,
                        ];

                    }
                }


//                print_r($datosElastic);


                /*
                $jsonSave['update'] = array();
                $jsonSave['update']['_id'] = 'per-'.$datosRegistro['IdPuesto'].'-'.$fila['IdPofa'];
                $jsonSave['update']['_index'] = INDEXPREFIX.SUFFIX_PUESTOS;
                $jsonSave['update']['routing'] = $datosRegistro['IdPuesto'];

                $jsonSaveData['doc'] = $datosElastic;
                $jsonSaveData['doc_as_upsert'] = true;
                */
                $jsonSave['index'] = array();
                $jsonSave['index']['_id'] = 'per-'.$datosRegistro['IdPuesto'].'-'.$fila['IdPofa'];
                $jsonSave['index']['_index'] = INDEXPREFIX.SUFFIX_PUESTOS;
                $jsonSave['index']['routing'] = $datosRegistro['IdPuesto'];

                $jsonSaveData = $datosElastic;
                //$jsonSaveData['doc_as_upsert'] = true;
                $stringBulk .= json_encode($jsonSave)."\n";
                $stringBulk .= json_encode($jsonSaveData)."\n";
                $i++;
                if ($i == $page_size) {
                    if (!$oElastic->ActualizarBulkIndex($stringBulk)) {
                        $this->setError($oElastic->getError());
                        return false;
                    }

                    $stringBulk = "";
                    $i = 0;
                }
            }
        }

        if ($numfilas_desempeno > 0) {

            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado_desempeno)) {

                $dias = \FuncionesPHPLocal::ObtenerDiasSemanaNumerico();
                $datosRegistro = $fila;
                $datosRegistro['Id'] = $fila['IdDesempeno'];
                $datosRegistro['Tipo'] = "Desempeno";
                $datosRegistro['DiaNumero'] = $fila['Dia'];
                $datosRegistro['DiaDescripcion'] = $dias[$fila['Dia']];
                $datosRegistro['HorarioDesde'] = substr($fila['HoraInicio'],0,5);
                $datosRegistro['HorarioHasta'] = substr($fila['HoraFin'],0,5);

                if (!$oDesempenos->_armarObjetoElastic($datosRegistro, $datosRegistro, $datosElastic)) {
                    $this->setError($oDesempenos->getError());
                    return false;
                }

                /*$jsonSave['update'] = array();
                $jsonSave['update']['_id'] = 'des-'.$datosRegistro['IdPuesto'].'-'.$fila['IdDesempeno'];
                $jsonSave['update']['_index'] = INDEXPREFIX.SUFFIX_PUESTOS;
                $jsonSave['update']['routing'] = $datosRegistro['IdPuesto'];
                $jsonSaveData['doc'] = $datosElastic;
                $jsonSaveData['doc_as_upsert'] = true;
                */
                $jsonSave['index'] = array();
                $jsonSave['index']['_id'] = 'des-'.$datosRegistro['IdPuesto'].'-'.$fila['IdDesempeno'];
                $jsonSave['index']['_index'] = INDEXPREFIX.SUFFIX_PUESTOS;
                $jsonSave['index']['routing'] = $datosRegistro['IdPuesto'];
                $jsonSaveData = $datosElastic;

                $stringBulk .= json_encode($jsonSave)."\n";
                $stringBulk .= json_encode($jsonSaveData)."\n";
                $i++;
                if ($i == $page_size) {
                    if (!$oElastic->ActualizarBulkIndex($stringBulk)) {
                        $this->setError($oElastic->getError());
                        return false;
                    }

                    $stringBulk = "";
                    $i = 0;
                }
            }
        }

        if ($stringBulk != "") {
            if (!$oElastic->ActualizarBulkIndex($stringBulk)) {
                $this->setError($oElastic->getError());
                return false;
            }
        }

        return true;
    }

    public function buscarDatosxId($datos, &$resultado, &$numfilas):bool {
        return parent::buscarDatosxId($datos, $resultado, $numfilas);
    }

    public function buscarxId($datos, &$resultado, &$numfilas):bool {
        return parent::buscarxId($datos, $resultado, $numfilas);
    }

    public function buscarEstadoxIdMad($datos, &$resultado, &$numfilas):bool {
        return parent::buscarEstadoxIdMad($datos, $resultado, $numfilas);
    }

    public function buscarMadActivoxEscuela($datos, &$resultado, &$numfilas):bool {
        return parent::buscarMadActivoxEscuela($datos, $resultado, $numfilas);
    }

    public function buscarPuestosPersonasMad($datos, &$resultado, &$numfilas):bool {
        return parent::buscarPuestosPersonasMad($datos, $resultado, $numfilas);
    }

    public function buscarMadFinalizado($datos, &$resultado, &$numfilas):bool {


        $sparam = [
            'IdEscuela' => $datos['IdEscuela'],
            'xIdPersona' => 0,
            'IdPersona' => "",
            'xCodigoPuesto' => 0,
            'CodigoPuesto' => "",
            'xIdCargo' => 0,
            'IdCargo' => "",
            'xIdMateria' => 0,
            'IdMateria' => "",
            'IdMad'=> $datos['Id'],
            'xDNI'=> 0,
            'DNI'=>"",
            //'Query' => $exists ? $query : "",
        ];
        if (isset($datos['IdEscuelaAnexo']) && $datos['IdEscuelaAnexo'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuelaAnexo'];
        }
        if (isset($datos['IdPersona']) && $datos['IdPersona'] != "") {
            $sparam['IdPersona'] = $datos['IdPersona'];
            $sparam['xIdPersona'] = 1;
        }
        if (isset($datos['DNI']) && $datos['DNI'] != "") {
            $sparam['DNI'] = trim($datos['DNI']);
            $sparam['xDNI'] = 1;
        }

        if (isset($datos['CodigoPuesto']) && $datos['CodigoPuesto'] != "") {
            $sparam['CodigoPuesto'] = $datos['CodigoPuesto'];
            $sparam['xCodigoPuesto'] = 1;
        }

        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }

        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }

        return parent::buscarMadFinalizado($sparam, $resultado, $numfilas);
    }

    public function buscarMadPuestoxIdPuestoxIdMad($datos, &$resultado, &$numfilas):bool {
        return parent::buscarMadPuestoxIdPuestoxIdMad($datos, $resultado, $numfilas);
    }

    public function buscarPuestosPersonasDestinoMad($datos, &$resultado, &$numfilas):bool {
        return parent::buscarPuestosPersonasDestinoMad($datos, $resultado, $numfilas);
    }

    public function buscarVacante($datos, &$resultado, &$numfilas):bool {
        return parent::buscarVacante($datos, $resultado, $numfilas);
    }

    protected function _validarInsertar(&$datos): bool {

        if (!$this->_validarDatosVacios($datos))
            return false;

        if (!$this->_validarMovimiento($datos, $vacante))
            return false;

        return true;
    }

    protected function _validarMovimiento(array $datos, &$vacante): bool {

        /** Busco si el cargo destino es vacante */
        $datosBuscar['IdPuesto'] = $datos['IdPuestoDestino'];
        $oEpp = new \cEscuelasPuestosPersonas($this->conexion);
        if (!$oEpp->buscarxPuesto($datosBuscar, $resultado, $numfilas)) {
            $this->setError($oEpp->getError());
            return false;
        }

        $vacante = true;
        if ($numfilas > 0) {
            $vacante = false;
            while ($r = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                /** Valido que no sea el mismo puesto origen */
                if (isset($datos['IdPuesto']) && $r['IdPuesto'] == $datos['IdPuesto']) {
                    $this->setError(400, 'Esta seleccionando el mismo puesto/plaza origen y destino');
                    return false;
                }
            }
        }
        /** Valido que el puesto no este ya asociado como destino de otro puesto de origen en el mismo MAD */
        /*
        $datosBuscar['IdPuesto'] = $datos['IdPuestoDestino'];
        $datosBuscar['IdMad'] = $datos['IdMad'];
        if (!$this->buscarMadPuestoxIdPuestoDestinoxIdMad($datosBuscar, $resultadoExistePuesto, $numfilasxistePuesto)) {
            $this->setError($oEpp->getError());
            return false;
        }
        if ($numfilasxistePuesto > 0) {
            $this->setError(400, 'El puesto seleccionado ya se encuentra designado como destino en este mismo proceso');
            return false;
        }
        */
        return true;
    }

    protected function _validarDatosVacios(array $datos): bool {

        if (\FuncionesPHPLocal::isEmpty($datos['IdPuesto'])) {
            $this->setError(400, 'Debe seleccionar un puesto/plaza del listado de origen');
            return false;
        }

        if (\FuncionesPHPLocal::isEmpty($datos['IdPuestoDestino'])) {
            $this->setError(400, 'Debe seleccionar un puesto/plaza destino');
            return false;
        }

        return true;
    }

    private static function _setearFechas(&$datos): void {

        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
    }

    private static function _setearDatos(&$datos): void {

        $datos['Anio'] = date('Y');
        $datos['Estado'] = $datos['Estado'] ?? 1;
        $datos['IdPuestoPadre'] = $datos['IdPuestoPadre'] ?? 'NULL';
    }

    public function buscarMadPuestoxIdPuestoDestinoxIdMad($datos, &$resultado, &$numfilas):bool {
        return parent::buscarMadPuestoxIdPuestoDestinoxIdMad($datos, $resultado, $numfilas);
    }
}

