<?php
include(DIR_CLASES_DB . "cEscuelasPuestosPersonas.db.php");

use Bigtree\Logica\Movimientos;

require_once DIR_CLASES_AUDITORIAS_LOGICA . 'cPuestosHistoricos.class.php';
require_once DIR_CLASES_LOGICA . 'cExtensionSuplenciaTipo.class.php';
require_once DIR_CLASES_LOGICA . 'cCargos.class.php';

class cEscuelasPuestosPersonas extends cEscuelasPuestosPersonasdb {
    /**
     * @var Elastic\Conexion
     */
    private $conexionES;

    /**
     * Constructor de la clase cEscuelasPuestosPersonas.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el m�todo getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal         $conexion
     * @param Elastic\Conexion|null $conexionES
     * @param mixed                 $formato
     */
    public function __construct(accesoBDLocal $conexion, ?Elastic\Conexion $conexionES = null, $formato = FMT_ARRAY) {
        $this->conexionES =& $conexionES;
        parent::__construct($conexion, $formato);
        $this->setError(0, '');
    }

    /**
     * Destructor de la clase cEscuelasPuestosPersonas.
     */
    function __destruct() {
        parent::__destruct();
    }

    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        return true;
    }

    public function buscarxPuesto($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxPuesto($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function BuscarxPuestoEstado($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarxPuestoEstado($datos, $resultado, $numfilas);
    }

    public function BuscarPersonaxIdPuesto($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarPersonaxIdPuesto($datos, $resultado, $numfilas);
    }

    public function BuscarxPuestoxIdPersona(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::BuscarxPuestoxIdPersona($datos, $resultado, $numfilas);
    }

    public function BuscarxIdPuestoxIdPersona($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxIdPuestoxIdPersona($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxIdPuestoxIdPersonaxFechas($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxIdPuestoxIdPersonaxFechas($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxIdPofaxIdPersona($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxIdPofaxIdPersona($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxIdPuestoRaiz($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxIdPuestoRaiz($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function buscarParaElastic($datos, &$resultado, &$numfilas): bool {
        if (!parent::buscarParaElastic($datos, $resultado, $numfilas))
            return false;

        return true;
    }

    public function buscarParaElasticxEscuela($datos, &$resultado, &$numfilas): bool {
        return parent::buscarParaElasticxEscuela($datos, $resultado, $numfilas);
    }

    public function buscarParaHistoricos($datos, &$resultado, &$numfilas): bool {
        if (!parent::buscarParaHistoricos($datos, $resultado, $numfilas))
            return false;

        return true;
    }

    public function buscarHistorico($datos, &$resultado, &$numfilas): bool {
        return parent::buscarHistorico($datos, $resultado, $numfilas);
    }

    public function buscarCargoxIdPuestoxIdPersona($datos, &$resultado, &$numfilas) {
        return parent::buscarCargoxIdPuestoxIdPersona($datos, $resultado, $numfilas);
    }

    public function buscarTotalAgentesxEscuelas(&$resultado, &$numfilas) {
        return parent::buscarTotalAgentesxEscuelas($resultado, $numfilas);
    }

    public function buscarLicenciasxPuesto($datos, &$resultado, &$numfilas): bool {
        return parent::buscarLicenciasxPuesto($datos, $resultado, $numfilas);
    }

    public function buscarSuplenciasVencidasEscuela($datos, &$resultado, &$numfilas): bool {
        return parent::buscarSuplenciasVencidasEscuela($datos, $resultado, $numfilas);
    }

    public function buscarSuplenciasVencidas(&$resultado, &$numfilas): bool {
        return parent::buscarSuplenciasVencidas($resultado, $numfilas);
    }

    public function buscarSuplencias(&$resultado, &$numfilas): bool {
        return parent::buscarSuplencias($resultado, $numfilas);
    }

    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdPofa' => 0,
            'IdPofa' => "",
            'xIdPuesto' => 0,
            'IdPuesto' => "",
            'xIdPersona' => 0,
            'IdPersona' => "",
            'xIdPofaSuperior' => 0,
            'IdPofaSuperior' => "",
            'xIdRevista' => 0,
            'IdRevista' => "",
            'xCodigoLiquidador' => 0,
            'CodigoLiquidador' => "",
            'xFechaDesde' => 0,
            'FechaDesde' => "",
            'xFechaHasta' => 0,
            'FechaHasta' => "",
            'xFechaDesignacion' => 0,
            'FechaDesignacion' => "",
            'xFechaTomaPosesion' => 0,
            'FechaTomaPosesion' => "",
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "IdPersona DESC",
        ];
        if (isset($datos['IdPofa']) && $datos['IdPofa'] != "") {
            $sparam['IdPofa'] = $datos['IdPofa'];
            $sparam['xIdPofa'] = 1;
        }
        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != "") {
            $sparam['IdPuesto'] = $datos['IdPuesto'];
            $sparam['xIdPuesto'] = 1;
        }
        if (isset($datos['IdPersona']) && $datos['IdPersona'] != "") {
            $sparam['IdPersona'] = $datos['IdPersona'];
            $sparam['xIdPersona'] = 1;
        }
        if (isset($datos['IdPofaSuperior']) && $datos['IdPofaSuperior'] != "") {
            $sparam['IdPofaSuperior'] = $datos['IdPofaSuperior'];
            $sparam['xIdPofaSuperior'] = 1;
        }
        if (isset($datos['IdRevista']) && $datos['IdRevista'] != "") {
            $sparam['IdRevista'] = $datos['IdRevista'];
            $sparam['xIdRevista'] = 1;
        }
        if (isset($datos['CodigoLiquidador']) && $datos['CodigoLiquidador'] != "") {
            $sparam['CodigoLiquidador'] = $datos['CodigoLiquidador'];
            $sparam['xCodigoLiquidador'] = 1;
        }
        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            $sparam['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xFechaDesde'] = 1;
        }
        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            $sparam['FechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xFechaHasta'] = 1;
        }
        if (isset($datos['FechaDesignacion']) && $datos['FechaDesignacion'] != "") {
            $sparam['FechaDesignacion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesignacion'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xFechaDesignacion'] = 1;
        }
        if (isset($datos['FechaTomaPosesion']) && $datos['FechaTomaPosesion'] != "") {
            $sparam['FechaTomaPosesion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaTomaPosesion'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xFechaTomaPosesion'] = 1;
        }
        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];
        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function buscarSuplentesDeLaPersona2($datos, &$resultados, &$numfilas): bool {
        return parent::buscarSuplentesDeLaPersona2($datos, $resultados, $numfilas);
    }

    public function buscarPorPadre($datos, &$resultados, &$numfilas): bool {
        return parent::buscarPorPadre($datos, $resultados, $numfilas);
    }

    public function arrayBusquedaSuplentesDeLaPersona(array $datos, &$arrayHijos): bool {
        $datos['IdPuesto'] = array_unique($datos['IdPuesto']);

        if (empty($arrayHijos))
            $arrayHijos = [];

        foreach ($datos['IdPuesto'] as $IdPuesto) {
            $datosEnviar['IdPuesto'] = $IdPuesto;

            if (!$this->buscarSuplentesDeLaPersona($datosEnviar, $arrayHijos))
                return false;
        }

        return true;
    }

    public function buscarSuplentesDeLaPersona(array $datos, &$arrayHijos): bool {


        if (!$this->buscarPorPadre($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {

            while ($filaHijos = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                $arrayHijos[] = $filaHijos;

                if (!$this->buscarSuplentesDeLaPersona($filaHijos, $arrayHijos))
                    return false;
            }

        }

        return true;
    }

    public function BuscarEscuelaPOFA($datos, &$resultado, &$numfilas): bool {
        $datos['xIdEstado'] = "-1";
        if (isset($datos["IdEstado"]) && $datos["IdEstado"] != "") {
            $datos['xIdEstado'] = "1";
            $datos['IdEstado'] = $datos["IdEstado"];
        }

        if (!parent::BuscarEscuelaPOFA($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function InsertarDB($datos, &$codigoInsertado): bool {

        if (!$this->_ValidarInsertar($datos))
            return false;


        if (!$this->_definirPuesto($datos))
            return false;


        $this->ObtenerProximoOrden($datos, $proxorden);
        $datos['Orden'] = $proxorden;

        $this->_SetearNull($datos);
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;
        $datos['IdEstado'] = $datos['IdEstado'] ?? 1;

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        return true;

    }


    public function InsertarDBSinDefinirPuesto($datos, &$codigoInsertado): bool {

        if (!$this->_ValidarInsertar($datos))
            return false;

        $this->ObtenerProximoOrden($datos, $proxorden);
        $datos['Orden'] = $proxorden - 1; // mantengo el orden original

        $this->_SetearNull($datos);
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;
        $datos['IdEstado'] = $datos['IdEstado'] ?? 1;

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        return true;

    }


    public function Insertar($datos, &$codigoInsertado): bool {

        if (!$this->_ValidarInsertar($datos))
            return false;

        if (!$this->_definirPuesto($datos))
            return false;

        $this->ObtenerProximoOrden($datos, $proxorden);
        $datos['Orden'] = $proxorden;

        $this->_SetearNull($datos);
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;
        $datos['IdEstado'] = $datos['IdEstado'] ?? 1;

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $oPersonas = new cPersonas($this->conexion);

        $datosActualizarPersona['IdPersona'] = $datos['IdPersona'];
        if (!$oPersonas->actualizarEsFamiliar($datosActualizarPersona))
            return false;

        $datos['IdPofa'] = $datos['Id'] = $codigoInsertado;
        $datosRegistro = [];
        if (!$this->_armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;

        $oAuditoriasEscuelasPuestosPersonas = new cAuditoriasEscuelasPuestosPersonas($this->conexion, $this->formato);
        $datos['IdPofa'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasEscuelasPuestosPersonas->InsertarLog($datos, $codigoInsertadolog))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);

        $datos['FechaHasta'] = null;
        $datos['Razon'] = $datos['Razon'] ?? 'Alta';
        if (!$this->_actualizarHistoricos($datos))
            return false;

        if (!empty($datos['IgnorarConflictos'])) {
            if (!$this->_validarConflictos($datos, true, $errores))
                return false;

            $datosGuardar = [
                'IdPofa' => $codigoInsertado,
                'TipoConflicto' => $errores->Tipo,
                'Conflictos' => self::cargarConflictos($errores),
            ];

            if (!$this->ModificarExisteInconsistencia($datosGuardar))
                return false;
        }

        if (!FuncionesPHPLocal::isEmpty($datos['asociaPon']) && $datos['asociaPon']) {

            $datosNovedad['IdTipoDocumento'] = NOV_ALTA_MANUAL;
            $datosNovedad['IdRegistroTipoDocumento'] = NOV_REGISTRO_ALTA_MANUAL;
            $datosNovedad['IdPersona'] = $datos['IdPersona'];
            $datosNovedad['IdPuesto'] = $datos['IdPuesto'];
            $datosNovedad['Puesto_' . $datos['IdPuesto']] = 1;
            $datosNovedad['IdEscuela'] = (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") ? $datos['IdEscuela'] : $_SESSION["IdEscuela"];
            $datosNovedad['FechaDesignacion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesignacion'], 'aaaa-mm-dd', 'dd/mm/aaaa');
            $datosNovedad['FechaTomaPosesion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaTomaPosesion'], 'aaaa-mm-dd', 'dd/mm/aaaa');
            $datosNovedad['FechaHastaPosesion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHastaPosesion'], 'aaaa-mm-dd', 'dd/mm/aaaa');
            $datosNovedad['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesignacion'], 'aaaa-mm-dd', 'dd/mm/aaaa');
            //$datosLiquidacion['BajaLiquidacion'] = 1;
            $datosLiquidacion['BajaLiquidacion'] = 0; //el alta automatica no baja a liquidacion

            $oNovedad = new cDocumentos($this->conexion, $this->formato, $this->conexionES ?? new Elastic\Conexion());
            if (!$oNovedad->Insertar($datosNovedad, $codigoinsertado)) {
                $this->setError(400, 'error al insertar.');
                return false;
            }
            /*
            $datosLiquidacion['IdDocumento'] = $codigoinsertado;
            $oLiquidacion = new cDocumentos($this->conexion, $this->formato, $this->conexionES ?? new Elastic\Conexion());
            if (!$oLiquidacion->BajarLiquidacionDocumento(null, $datosLiquidacion)) {
                $this->setError(400, 'error al bajar liquidacion si / no .');
                return false;
            }*/


        }

        if (!$oElastic->Insertar($datosElastic)) {
            $this->setError($oElastic->getError());
            $error = $oElastic->getError();
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $error['error_description'], ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    public function InsertarPersonaPlazaDestino($datos, &$codigoInsertado): bool {

        if (!$this->_ValidarInsertar($datos))
            return false;

        if (!$this->_definirPuesto($datos))
            return false;

        $this->ObtenerProximoOrden($datos, $proxorden);
        $datos['Orden'] = $proxorden;

        $this->_SetearNull($datos);
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;
        $datos['IdEstado'] = $datos['IdEstado'] ?? 1;

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $oPersonas = new cPersonas($this->conexion);

        $datosActualizarPersona['IdPersona'] = $datos['IdPersona'];
        if (!$oPersonas->actualizarEsFamiliar($datosActualizarPersona))
            return false;

        $datos['IdPofa'] = $datos['Id'] = $codigoInsertado;
        $datosRegistro = [];
        if (!$this->_armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;

        $oAuditoriasEscuelasPuestosPersonas = new cAuditoriasEscuelasPuestosPersonas($this->conexion, $this->formato);
        $datos['IdPofa'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasEscuelasPuestosPersonas->InsertarLog($datos, $codigoInsertadolog))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);

        $datos['FechaHasta'] = null;
        $datos['Razon'] = $datos['Razon'] ?? 'Alta';
        if (!$this->_actualizarHistoricos($datos))
            return false;


        if (!$oElastic->Insertar($datosElastic)) {
            $this->setError($oElastic->getError());
            $error = $oElastic->getError();
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $error['error_description'], ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    public function ModificarOrigen($datos): bool{

        $datosPuestoOrigen['IdPuesto'] = $datos['IdPuestoOrigen'];
        if (!$this->BuscarPersonaxIdPuesto($datosPuestoOrigen, $resultadoPuestoOrigen, $numfilasPuestoOrigen))
            return false;

        if ($numfilasPuestoOrigen==1){
            $filaPuestoOrigen = $this->conexion->ObtenerSiguienteRegistro($resultadoPuestoOrigen);
        }

        $filaPuestoOrigen['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        $filaPuestoOrigen['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $filaPuestoOrigen['IdPuesto'] = $datos['IdPuestoOrigen'];
        $filaPuestoOrigen['IdEstado'] = REB; #reubicado

        if (!parent::ModificarPuestoOrigen($filaPuestoOrigen))
            return false;


        $oAuditoriasEscuelasPuestosPersonas = new cAuditoriasEscuelasPuestosPersonas($this->conexion, $this->formato);
        $filaPuestoOrigen['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestosPersonas->InsertarLog($filaPuestoOrigen, $codigoInsertadolog))
            return false;

        $datosBusqueda = [];
        if (!$this->_armarObjetoElastic($filaPuestoOrigen, $datosBusqueda, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }


        return true;
    }

    public function InsertarExtemporal($datos, &$codigoInsertado): bool {

        if (!$this->_ValidarInsertar($datos))
            return false;

        if (!$this->_ValidarInsertarExtemporal($datos))
            return false;

        if (!$this->_definirPuesto($datos))
            return false;

        $this->ObtenerProximoOrden($datos, $proxorden);
        $datos['Orden'] = $proxorden;

        $this->_SetearNull($datos);
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['Estado'] = NOACTIVO;
        $datos['IdEstado'] = $datos['IdEstado'] ?? 1;

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $oPersonas = new cPersonas($this->conexion);
        $datosActualizarPersona['IdPersona'] = $datos['IdPersona'];
        if (!$oPersonas->actualizarEsFamiliar($datosActualizarPersona))
            return false;

        $datos['IdPofa'] = $datos['Id'] = $codigoInsertado;

        /*
        $datosRegistro = [];
        if (!$this->_armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;
        */

        $oAuditoriasEscuelasPuestosPersonas = new cAuditoriasEscuelasPuestosPersonas($this->conexion, $this->formato);
        $datos['IdPofa'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasEscuelasPuestosPersonas->InsertarLog($datos, $codigoInsertadolog))
            return false;


        //$datos['FechaHasta'] = null;
        $datos['Razon'] = $datos['Razon'] ?? 'Alta';
        if (!$this->_actualizarHistoricos($datos))
            return false;

        if (!empty($datos['IgnorarConflictos'])) {
            if (!$this->_validarConflictos($datos, true, $errores))
                return false;

            $datosGuardar = [
                'IdPofa' => $codigoInsertado,
                'TipoConflicto' => $errores->Tipo,
                'Conflictos' => self::cargarConflictos($errores),
            ];

            if (!$this->ModificarExisteInconsistencia($datosGuardar))
                return false;
        }

        return true;
    }


    public function altaExtemporal($datos, &$codigoInsertado): bool {

        if(!$this->ValidarPersonaYaPresenteEnPuesto($datos, $resultadoRepetida,$numfilasRepetida)) {
            return false;
        }

        $datos['asociaPon'] = 0;

        $oNovedad = new cDocumentos($this->conexion, $this->formato, $this->conexionES ?? new Elastic\Conexion());

        $datosNovedad['IdTipoDocumento'] = NOV_ALTA_POFA_EXTEMPORAL;
        $datosNovedad['IdRegistroTipoDocumento'] = NOV_REGISTRO_ALTA_EXTEMPORAL;
        $datosNovedad['IdPersona'] = $datos['IdPersona'];
        $datosNovedad['IdPuesto'] = $datos['IdPuesto'];
        $datosNovedad['Puesto_' . $datos['IdPuesto']] = 1;
        $datosNovedad['IdEscuela'] = (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") ? $datos['IdEscuela'] : $_SESSION["IdEscuela"];
        $datosNovedad['FechaDesignacion'] = $datos['FechaDesignacion'];
        $datosNovedad['FechaTomaPosesion'] = $datos['FechaTomaPosesion'];
        $datosNovedad['FechaHasta'] = $datos['FechaHasta'];
        $datosNovedad['FechaDesde'] = $datos['FechaDesignacion'];
        if (isset($datos['IdExcepcionTipo']))
            $datosNovedad['IdExcepcionTipo'] = $datos['IdExcepcionTipo'];
        $datosNovedad['IdRevista'] = $datos['IdRevista'];

        if (!$oNovedad->InsertarExtemporal($datosNovedad, $codigoInsertado)) {
            if (FMT_ARRAY == $this->formato)
                $this->setError(400, 'error al insertar.');
            return false;
        }

        return true;
    }

    public function Modificar($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        if (!$this->_ValidarDatosVacios($datos))
            return false;

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;
        $oAuditoriasEscuelasPuestosPersonas = new cAuditoriasEscuelasPuestosPersonas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestosPersonas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        $datosBusqueda = [];
        if (!$this->_armarObjetoElastic($datos, $datosBusqueda, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        /*$datos['FechaHasta'] = $datos['FechaHasta'] ?? null;
        $datos['Razon'] = $datos['Razon'] ?? 'Cambio';
        if (!$this->_actualizarHistoricos($datos))
            return false;
        */

        //liquido movimientos por IdPofa
        $oMovimientos = new Movimientos($this->conexion, $this->formato);

        $datosLiqui["IdMovimiento"] = 1;#ALTA
        $datosLiqui["IdPofa"] = $datos["IdPofa"];#ALTA
        //$datosLiqui["IdDocumento"]=$datosRegistro["IdDocumento"];
        $datosLiqui["FechaDesde"] = $datos["FechaTomaPosesion"];
        $datosLiqui["FechaHasta"] = $datos["FechaHasta"];
        //  $datosLiqui["OmitirAnulacion"] = true;
        if (!$oMovimientos->insertarMovimientoPofa($datosLiqui, $IdLogNovedad)) {
            $errormsg = $oMovimientos->getError();
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $errormsg["error_description"], ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        /*
        $datosLiqui["IdMovimiento"]=2;#BAJA
        $datosLiqui["IdPofa"]=$datos["IdPofa"];
        $datosLiqui["FechaHasta"]=$datosRegistro["FechaHasta"];
        if (!$oMovimientos->insertarMovimientoPofa($datosLiqui,$IdLogNovedad))
        {
            $errormsg=$oMovimientos->getError();
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $errormsg["error_description"], array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }*/
        return true;
    }

    /**
     * modificar solo la fechaHasta
     *
     */
    public function ModificarFechaHasta($datos): bool {

        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];


        $this->_SetearNull($datos);

        if (!parent::ModificarFechaHasta($datos))
            return false;

        //TODO: REVISAR QUE FUNCIONE AUDITORIA
        $oAuditoriasEscuelasPuestosPersonas = new cAuditoriasEscuelasPuestosPersonas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestosPersonas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        $datosBusqueda = [];
        if (!$this->_armarObjetoElastic($datos, $datosBusqueda, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        /*$datos['FechaHasta'] = $datos['FechaHasta'] ?? NULL;
        $datos['Razon'] = $datos['Razon'] ?? 'Cambio';
        if (!$this->_actualizarHistoricos($datos))
            return false;
        */
        return true;
    }

    public function ExtensionFechaHasta($datos): bool {

        if (!$this->_ValidarExtensionFechaHasta($datos, $datosRegistro))
            return false;

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");

        if (!isset($datos['UltimaModificacionUsuario']) || $datos['UltimaModificacionUsuario'] == "") {
            $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        } else {
            $datosRegistro['UltimaModificacionUsuario'] = $datos['UltimaModificacionUsuario'];
        }


        $this->_SetearNull($datos);

        if (!parent::ExtensionFechaHasta($datos))
            return false;

        //TODO: REVISAR QUE FUNCIONE AUDITORIA
        $oAuditoriasEscuelasPuestosPersonas = new cAuditoriasEscuelasPuestosPersonas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestosPersonas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        $datosBusqueda = [];
        if (!$this->_armarObjetoElastic($datos, $datosBusqueda, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }
        /*
        $datos['FechaHasta'] = $datos['FechaHasta'] ?? null;
        $datos['Razon'] = $datos['Razon'] ?? 'Cambio';
        if (!$this->_actualizarHistoricos($datos))
            return false;
        */
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function ModificarExisteInconsistencia(array $datos): bool {
        $datos['ExisteInconsistencia'] = empty($datos['TipoConflicto']) ? 0 : 1;
        $datos['JsonInconsistencia'] = empty($datos['TipoConflicto']) ? 'NULL' : json_encode([
            'IdUsuario' => $_SESSION['usuariocod'],
            'Fecha' => date('Y-m-d H:i:s'),
            'TipoConflicto' => $datos['TipoConflicto'],
            'Conflictos' => $datos['Conflictos'] ?? null,
        ]);
        return parent::ModificarExisteInconsistencia($datos);
    }


    /**
     * Modifica el orden a uno menos en puestos personas
     */
    public function ModificarOrden($datos): bool {

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

        if (!parent::ModificarOrden($datos))
            return false;

        $datosBusqueda = [];
        if (!$this->_armarObjetoElastic($datos, $datosBusqueda, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }


    public function ReacomodarPuesto($datos): bool {

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

        if (!parent::ReacomodarPuesto($datos))
            return false;


        return true;
    }


    public function Eliminar($datos, bool $actualizoElatic = true): bool {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;


        //if (!$this->buscarxPuesto($datos, $resultado_puesto, $numfilas_puesto))
        //  return false;

        if (!$this->BuscarxCodigo($datos, $resultado_puesto, $numfilas_puesto))
            return false;

        $oAuditoriasEscuelasPuestosPersonas = new cAuditoriasEscuelasPuestosPersonas($this->conexion, FMT_ARRAY);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = "eliminar";
        if (!$oAuditoriasEscuelasPuestosPersonas->InsertarLog($datosLog, $codigoInsertadolog)) {
            return false;
        }


        if (isset($datos['cesemanual']) && $datos['cesemanual'] == 1) {

            $datosNovedad['IdTipoDocumento'] = NOV_CESE_MANUAL;
            $datosNovedad['IdRegistroTipoDocumento'] = NOV_REGISTRO_CESE_MANUAL;
            $datosNovedad['IdPersona'] = $datos['IdPersona'];
            $datosNovedad['IdPuesto'] = $datos['IdPuesto'];
            $datosNovedad['Puesto_' . $datos['IdPuesto']] = 1;
            $datosNovedad['IdEscuela'] = (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") ? $datos['IdEscuela'] : $_SESSION["IdEscuela"];
            $datosNovedad['PeriodoFechaDesde'] = date('d/m/Y');
            $datosLiquidacion['BajaLiquidacion'] = 0;//no baja el cese automatico


            $oNovedad = new cDocumentos($this->conexion, $this->formato, $this->conexionES ?? new Elastic\Conexion());
            if (!$oNovedad->Insertar($datosNovedad, $codigoinsertado)) {
                $this->setError(400, 'error al insertar.');
                return false;
            }
            /*var_dump($datosNovedad);die;*/
            /*
            $datosLiquidacion['IdDocumento'] = $codigoinsertado;
            $oLiquidacion = new cDocumentos($this->conexion, $this->formato, $this->conexionES ?? new Elastic\Conexion());
            if (!$oLiquidacion->BajarLiquidacionDocumento(null, $datosLiquidacion)) {
                $this->setError(400, 'error al bajar liquidacion si / no .');
                return false;
            }*/

        }


        $oPofa = new cEscuelasPuestosPersonas($this->conexion, $this->conexionES, FMT_ARRAY);
        $datosBusqueda = [
            //'IdPuesto' => $datosRegistro['IdPuesto'],
            'IdPofa' => $datosRegistro['IdPofa'],
            'IdPersona' => $datosRegistro['IdPersona'],
        ];


        $datosBusqueda['Estado'] = [ACTIVO, ELIMINADO];
        // if (!$oPofa->BuscarxIdPuestoxIdPersona($datosBusqueda, $resultado, $numfilas)) {
        if (!$oPofa->BuscarxIdPofaxIdPersona($datosBusqueda, $resultado, $numfilas)) {
            $this->setError($oPofa->getError());
            return false;
        }


        $datosPuestoPersona = $this->conexion->ObtenerSiguienteRegistro($resultado);
        if (isset($datos['cesemanual']) && $datos['cesemanual'] == 1) {

            $datosModif = [
                'IdPofa' => $datos['IdPofa'],
                'FechaHasta' => $datos['FechaHasta'] ?? date('Y-m-d'),
                'Estado' => ELIMINADO,
            ];
        } else {

            $datosModif = [
                'IdPofa' => $datos['IdPofa'],
                'FechaHasta' => $datos['FechaHasta'] ?? date('Y-m-d'),
                'Estado' => NOACTIVO,
            ];

        }

        # MODIFICADO A ELIMINACIÓN LÓGICA PARA POSTERIORMENTE PODER CONSULTAR LOS DATOS EN BAJADA DE MOVIMIENTOS
        if (!parent::ModificarEstado($datosModif))
            return false;


        if (!parent::ModificarFechaHasta($datosModif))
            return false;

        // seteo el actualizar para elastic
        $actualizar = false;
        if ($numfilas_puesto > 0) {
            $actualizar = true;
            $fila = $this->conexion->ObtenerSiguienteRegistro($resultado_puesto);
            //subo el puesto hijo a donde estaba el puesto padre

            $oPuestos = new cEscuelasPuestos($this->conexion, $this->conexionES);


            /*
            if (!empty($fila['IdPuestoPadre'])) {

                $datosRegistro['NroResolucion'] = $fila["NroResolucion"];
                if (!$oPuestos->ReacomodarPuesto($datos, $datosRegistro)) {
                    $this->setError($oPofa->getError());
                    return false;
                }


                if (!$oPuestos->EliminarCargo($datos)) {
                    $this->setError($oPuestos->getError());
                    return true;
                }


            }
            */

            // Eliminacion logica de puestos suplente cuando no son el raiz no tinen puesto hijo
            if ($fila['IdRevista'] == REVISTA_SUPLENTE) { // si es suplente

                // TODO posible solucion hacer el reacomodar puesto, a confirmar
                /*
                $datosRegistro['NroResolucion'] = $fila["NroResolucion"];
                if (!$oPuestos->ReacomodarPuesto($datos, $datosRegistro)) {
                    $this->setError($oPofa->getError());
                    return false;
                }
                */

                // solucion provisoria

                if (!$oPuestos->BuscarxCodigo(["IdPuesto" => $fila["IdPuesto"]], $resultado_EscuelaPuesto, $numfilas_EscuelaPuesto)) {
                    $this->setError($oPuestos->getError());
                    return true;
                }

                $EscuelaPuesto = [];
                if ($numfilas_EscuelaPuesto == 1) {
                    $EscuelaPuesto = $this->conexion->ObtenerSiguienteRegistro($resultado_EscuelaPuesto);
                }

                if (!$oPuestos->BuscarHijos($datos, $hijos)) {
                    $this->setError($oPuestos->getError());
                    return true;
                }

                if (
                    !empty($EscuelaPuesto) && $EscuelaPuesto["IdPuestoRaiz"] != $EscuelaPuesto["IdPuesto"] // si no es puesto raiz
                    && $hijos == [] // si no tiene hijos pasa a estado 30 EscuelaPuesto
                ) {
                    if (!$oPuestos->EliminarCargo($datos)) {
                        $this->setError($oPuestos->getError());
                        return true;
                    }
                }
            }

        }


        /*$datos['Razon'] = $datos['Razon'] ?? 'Baja';
        $datos['FechaHasta'] = $datos['FechaHasta'] ?? date('Y-m-d');
        if (!$this->_actualizarHistoricos($datos))
            return false;
        */

        if ($actualizoElatic) {
            $datosEliminar['Id'] = $datos['IdPofa'];
            $datosEliminar['Tipo']['parent'] = $datosRegistro['IdPuesto'];
            $datosEliminar['Tipo']['name'] = "Persona";
            $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
            if (!$oElastic->Eliminar($datosEliminar)) {
                $this->setError(400, 'Error al eliminar el puesto de la persona.');
                return false;
            }
        }


        if ($actualizar && $actualizoElatic) {
            $datosBusqueda = [

            ];


            $oMad = new \Bigtree\Logica\cMad($this->conexion, $this->conexionES);

            //$datos['IdEscuela']= $fila["IdEscuela"];
            $datos['IdEscuela'] = $datosPuestoPersona["IdEscuela"];

            if (!$oMad->modificarElastic($datos)) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error, republicacion de Elastic de POF/POFA con errores', ['archivo' => __FILE__, 'funcion' => '', 'linea' => __LINE__], ['formato' => FMT_TEXTO]);
                die;
            }

            if (!$this->_armarObjetoElastic($datosPuestoPersona, $datosBusqueda, $datosElastic)) {
                return false;
            }


            $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
            if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
                $this->setError($oElastic->getError());
                return false;
            }

        }

        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);

        if (!isset($datosRegistro['CargaManual']))
            $datosRegistro['CargaManual'] = 0;
        $datosRegistro['Accion'] = ELIMINAR;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        if (REINCORPORASINCESE) {
            $oReincorporacionCesesPendientes = new \Bigtree\Logica\cReincorporacionesCesesPendientes($this->conexion, $this->conexionES, $this->formato);

            $datosReinc['IdPofa'] = $datosRegistro['IdPofa'];
            if (!$oReincorporacionCesesPendientes->CambiarEstadoxIdPofa($datosReinc))
                return false;
        }

        return true;
    }

    public function ModificarRevista($datos): bool {

        if (!parent::ModificarRevista($datos))
            return false;

        if (!$this->_armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }


    public function ModificarEstadoBD($datos): bool {

        if (!parent::ModificarEstado($datos))
            return false;

        return true;
    }

    public function ModificarEstado($datos): bool {

        if (!parent::ModificarEstado($datos))
            return false;

        if (!$this->_armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }


        return true;
    }


    public function ModificarIdEstado($datos): bool {
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        if (!parent::ModificarIdEstado($datos))
            return false;

        if (!$this->_armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }
        /*
        $datos['Razon'] = $datos['Razon'] ?? 'Cambio';
        $datos['FechaHasta'] = $datos['FechaHasta'] ?? null;
        if (!$this->_actualizarHistoricos($datos))
            return false;
        */
        /** Elimina desempeño de una plaza inconsistente */
        if ($datos['IdEstado'] == PI) {
            $oDesempeno = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES);
            $datosEliminar['IdPuesto'] = $datos['IdPuesto'];
            if (!$oDesempeno->EliminarxIdPuesto($datosEliminar)) {
                $this->setError($oDesempeno->getError());
                return false;
            }
        }

        return true;
    }


    public function Activar(array $datos): bool {
        $datosmodif['IdPersona'] = $datos['IdPersona'];
        $datosmodif['Estado'] = ACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasEscuelasPuestosPersonas = new cAuditoriasEscuelasPuestosPersonas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestosPersonas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function DesActivar(array $datos): bool {
        $datosmodif['IdPersona'] = $datos['IdPersona'];
        $datosmodif['Estado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasEscuelasPuestosPersonas = new cAuditoriasEscuelasPuestosPersonas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestosPersonas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function AsociarLicencia(array $datos): bool {

        if (FuncionesPHPLocal::isEmpty($datos['IdArticulo'])) {
            $this->setError(400, utf8_decode('Debe seleccionar un artículo asociado al cargo'));
            return false;
        }

        $oElastic = new Elastic\Puestos($this->conexionES);
        $oLicencias = new Elastic\Licencias($this->conexionES);

        $datosBuscarPuesto = [
            'Id' => $datos['IdPuesto'],
            'Tipo' => 'Puesto',
        ];
        if (!$oElastic->buscarxCodigo($datosBuscarPuesto, $datosPuesto)) {
            $this->setError($oElastic->getError());
            return false;
        }

        $datosBuscarPuesto = [
            'Id' => $datos['IdPofa'],
            'Tipo' => [
                'name' => 'Persona',
                'parent' => $datos['IdPuesto'],
            ],
        ];
        if (!$oElastic->buscarxCodigo($datosBuscarPuesto, $datosPuestoPersona)) {
            $this->setError($oElastic->getError());
            return false;
        }

        $a = array_filter($datosPuestoPersona['EstadoPersona']['Licencia'] ?? [],
            function ($item) use ($datos) {
                return $item['Id'] == $datos['IdLicencia'];
            });

        $actualizarPofa = (0 == count($a));
        $datosBuscarLicencia = ['Id' => $datos['IdLicencia']];
        if (!$oLicencias->buscarxCodigo($datosBuscarLicencia, $datosLicencia)) {
            $this->setError($oLicencias->getError());
            return false;
        }

        $a = array_filter($datosLicencia['Cargos'] ?? [],
            function ($item) use ($datos) {
                return $item['Puesto']['Id'] == $datos['IdPuesto'];
            });
        $actualizarLicencia = (0 == count($a));


        if ($actualizarPofa) {

            $oPuestosHistoricos = new cPuestosHistoricos($this->conexion, $this->conexionES, $this->formato);

            $datosHistorico = [
                'IdPuesto' => $datos['IdPuesto'],
                'IdPofa' => $datos['IdPofa'],
                'Orden' => $datosPuestoPersona['Orden'],
                'IdLicencia' => $datos['IdLicencia'],
                'IdRevista' => $datosPuestoPersona['Revista']['Id'],
                'IdPersona' => $datosPuestoPersona['IdPersona'],
                'FechaDesde' => (new DateTime($datosLicencia['Inicio']))->format('Y-m-d'),
                'FechaHasta' => (new DateTime($datosLicencia['Fin'] ?? date('Y-12-31')))->add(new DateInterval('PT1S'))->format('Y-m-d'),
                'FechaDesignacion' => $datosPuestoPersona['FechaDesignacion'] ?? '',
                'FechaTomaPosesion' => $datosPuestoPersona['FechaTomaPosesion'] ?? '',
                'Razon' => 'Licencia',
            ];

            if (!$oPuestosHistoricos->insertar($datosHistorico)) {
                $this->setError($oPuestosHistoricos->getError());
                return false;
            }

            $datosPofa = [
                'IdPofa' => $datos['IdPofa'],
                'UltimaModificacionUsuario' => $_SESSION['usuariocod'],
                'UltimaModificacionFecha' => date('Y-m-d H:i:s'),
                'FechaHasta' => $datosLicencia['Inicio'],
                'IdEstado' => 2,
            ];
            if (!$this->ModificarIdEstado($datosPofa))
                return false;

            $oModif = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
            $licencias = empty($datosPuestoPersona['EstadoPersona']['Licencia']) ? [] : $datosPuestoPersona['EstadoPersona']['Licencia'];
            if (!isset($licencias[0]) && isset($licencias['Id']))
                $licencias = [$licencias];

            $fechas = $datosLicencia['Fechas'];

            try {
                $fechaFin = new DateTime(empty($datosLicencia['Fin']) ? date('Y-12-31') : $datosLicencia['Fin'] . '+1 second');
            } catch (Exception $e) {
                $this->setError(400, $e->getMessage());
                return false;
            }

            if (empty($fechas)) {
                try {
                    $desde = new DateTime($datosLicencia['Inicio']);
                } catch (Exception $e) {
                    $this->setError(400, $e->getMessage());
                    return false;
                }
                $fechas = [
                    'gte' => $desde->format('Y-m-d'),
                    'lte' => $fechaFin->format('Y-m-d'),
                ];
            }
            //print_r($licencias);
            $licencias[] = [
                'Id' => $datosLicencia['Id'],
                'Fechas' => $fechas,
                'FechaHastaEstimada' => $fechaFin->format('Y-m-d'),
                'Motivo' => $datosLicencia['Motivo']['Descripcion'],
                'Prioridad' => $datosLicencia['Tipo']['Prioridad'],
            ];
            //print_r($licencias);

            $b = array_filter($licencias, function ($item) {
                $lte = isset($item['Fechas']['lte']) ? new DateTime($item['Fechas']['lte']) : new DateTime($item['FechaHastaEstimada']);
                $lte->add(new DateInterval('P1D'));
                $now = new DateTime();
                return $lte > $now;
            });

            usort($b, function ($f, $s) {
                return ($f['Prioridad'] ?? 5) <=> ($s['Prioridad'] ?? 5);
            });

            $jsonData = new stdClass();
            $jsonData->EstadoPersona = new stdClass();
            $jsonData->EstadoPersona->Id = 2;
            $jsonData->EstadoPersona->Descripcion = 'Licenciado';
            $jsonData->EstadoPersona->Codigo = 'LIC';
            $jsonData->EstadoPersona->Licencia = $b;
            if (!$oModif->Actualizar($datosBuscarPuesto, $jsonData)) {
                $this->setError($oModif->getError());
                return false;
            }

        }

        if ($actualizarLicencia) {
            $oLicencias = new cServiciosLicencias($this->conexion);

            $datosIns['IdLicencia'] = (int)$datos['IdLicencia'];
            $datosIns['IdPuesto'] = (int)$datos['IdPuesto'];
            $datosIns['IdEscuela'] = (int)$datosPuesto['Escuela']['Id'];
            $datosIns['IdEscuelaTurno'] = (int)$datosPuesto['IdEscuelaTurno'];
            $datosIns['IdRegion'] = (int)$datosPuesto['Escuela']['Region']['Id'];
            $datosIns['IdRevista'] = (int)$datosPuestoPersona['Revista']['Id'];
            $datosIns['Cargo'] = $datosPuesto['Materia']['Descripcion'] ?? $datosPuesto['Cargo']['Descripcion'];
            $datosIns['Escuela'] = $datosPuesto['Escuela']['Nombre'];
            $datosIns['IdArticulo'] = (int)$datos['IdArticulo'];
            $datosIns['IdTipoCargo'] = $datos['IdTipoCargo'] == '-1' ? null : (int)$datos['IdTipoCargo'];
            $datosIns['Inicio'] = $datosLicencia['Inicio'];
            $datosIns['Fin'] = $datosLicencia['Fin'];
            $datosIns['Horas'] = $datosPuesto['Horas'];
            $datosIns['Modulos'] = $datosPuesto['Modulos'];

            if (!$oLicencias->agregarCargo($datosIns)) {
                $this->setError($oLicencias->getError());
                return false;
            }
        }

        return true;
    }

    public function desAsociarLicencia(array $datos): bool {
        $oElastic = new Elastic\Puestos($this->conexionES);
        $oLicencias = new Elastic\Licencias($this->conexionES);
        $oHistoricos = new cPuestosHistoricos($this->conexion, $this->conexionES, FMT_ARRAY);

        if (!$oHistoricos->busquedaAvanzada($datos, $resultado, $numfilas)) {
            $this->setError($oHistoricos->getError());
            return false;
        }

        if ($numfilas != 0) {
            $filaHistorico = $this->conexion->ObtenerSiguienteRegistro($resultado);

            $filaHistorico['FechaHasta'] = date('Y-m-d');

            if (!$oHistoricos->modificar($filaHistorico)) {
                $this->setError($oHistoricos->getError());
                return false;
            }
        } else {
            if (!$this->buscarParaHistoricos($datos, $resultado, $numfilas))
                return false;

            $filaHistorico = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $filaHistorico['Razon'] = 'Desasociar licencia';
            $filaHistorico['FechaHasta'] = date('Y-m-d');
            if (!$oHistoricos->insertar($filaHistorico)) {
                $this->setError($oHistoricos->getError());
                return false;
            }
        }


        $datosBuscarPuesto = [
            'Id' => $datos['IdPofa'],
            'Tipo' => [
                'name' => 'Persona',
                'parent' => $datos['IdPuesto'],
            ],
        ];
        if (!$oElastic->buscarxCodigo($datosBuscarPuesto, $datosPuestoPersona)) {
            $this->setError($oElastic->getError());
            return false;
        }

        $now = new DateTime();
        //print_r($datosPuestoPersona);die;
        $actualizarPofa = array_filter($datosPuestoPersona['EstadoPersona']['Licencia'] ?? [],
            function ($item) use ($datos, $now) {
                $lte = isset($item['Fechas']['lte']) ? new DateTime($item['Fechas']['lte']) : new DateTime($item['FechaHastaEstimada']);
                $lte->add(new DateInterval('P1D'));
                return $item['Id'] != $datos['IdLicencia'] && $lte > $now;
            });
        //print_r($a);
        $datosBuscarLicencia = ['Id' => $datos['IdLicencia']];
        if (!$oLicencias->buscarxCodigo($datosBuscarLicencia, $datosLicencia)) {
            $this->setError($oLicencias->getError());
            return false;
        }

        if (is_array($actualizarPofa)) {
            $datosPofa = [
                'IdPofa' => $datos['IdPofa'],
                'UltimaModificacionUsuario' => $_SESSION['usuariocod'],
                'UltimaModificacionFecha' => date('Y-m-d H:i:s'),
                'FechaHasta' => $datosLicencia['Inicio'],
                'IdEstado' => count($actualizarPofa) > 0 ? 2 : 1,
            ];
            if (!$this->ModificarIdEstado($datosPofa))
                return false;

            usort($actualizarPofa, function ($f, $s) {
                return ($f['Prioridad'] ?? 5) <=> ($s['Prioridad'] ?? 5);
            });

            $oModif = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
            $jsonData = new stdClass();
            $jsonData->EstadoPersona = new stdClass();
            $jsonData->EstadoPersona->Id = count($actualizarPofa) > 0 ? 2 : 1;
            $jsonData->EstadoPersona->Descripcion = count($actualizarPofa) > 0 ? 'Licenciado' : 'Activo';
            $jsonData->EstadoPersona->Codigo = count($actualizarPofa) > 0 ? 'LIC' : 'ACT';
            $jsonData->EstadoPersona->Licencia = $actualizarPofa;
            if (!$oModif->Actualizar($datosBuscarPuesto, $jsonData)) {
                $this->setError($oModif->getError());
                return false;
            }
        }

        $actualizarLicencia = array_filter($datosLicencia['Cargos'] ?? [],
            function ($item) use ($datos) {
                return $item['Puesto']['Id'] != $datos['IdPuesto'];
            });
        //print_r($a);die;

        if (is_array($actualizarLicencia)) {
            $oLicencias = new cServiciosLicencias($this->conexion);

            $datosDel['IdLicencia'] = (int)$datos['IdLicencia'];
            $datosDel['IdPuesto'] = (int)$datos['IdPuesto'];

            if (!$oLicencias->eliminarCargo($datosDel)) {
                $this->setError($oLicencias->getError());
                return false;
            }

        }


        return true;
    }


    public function crearExtension(array $datos, array $datosLicencia, &$codigoinsertado): bool {

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(404, 'Error, no existe el puesto');
            return false;
        }


        $datosNivel['Id'] = $datos['IdNivel'];
        $oEscuelasNivelModalidad = new cEscuelasNivelModalidad($this->conexion);
        if (!$oEscuelasNivelModalidad->BuscarxCodigo($datosNivel, $resultadoNivel, $numfilasNivel)) {
            $this->setError($oEscuelasNivelModalidad->getError());
            return false;
        }


        if ($numfilasNivel > 0) {
            $Nivel = $this->conexion->ObtenerSiguienteRegistro($resultadoNivel)['IdNivel'];

            $oPuestos = new cEscuelasPuestos($this->conexion, $this->conexionES, $this->formato);
            if (!$oPuestos->BuscarCargoxIdPuesto($datos, $resultado_puesto, $numfilas_puesto)) {
                $this->setError($oPuestos->getError());
                return false;
            }


            if ($numfilas_puesto == 1) {
                while ($filaPuesto = $this->conexion->ObtenerSiguienteRegistro($resultado_puesto)) {
                    $datosBuscarExtensionTipo['IdTipoCargo'] = $filaPuesto['IdTipoCargo'];
                }
            } else {
                $datosBuscarExtensionTipo['IdTipoCargo'] = 0;
            }

            $datosBuscarExtensionTipo['IdNivel'] = $Nivel;

            if (!$this->devolverExtensionTipoDocumento($datosBuscarExtensionTipo, $datosExtensionTipo)) {
                return false;
            }

            /*          if (EXTENSION_SUPLENCIA_DIVIDIDO_NIVEL) {
                          switch ($Nivel) {
                              case NIVEL_INICIAL:
                              case NIVEL_PRIMARIO:
                                  $datosNovedad['IdTipoDocumento'] = NOVEDAD_EXTENSION_LICENCIA_PRIMARIA;
                                  $datosNovedad['IdRegistroTipoDocumento'] = REGISTRO_NOVEDAD_EXTENSION_LICENCIA_PRIMARIA;
                                  break;
                              case NIVEL_SECUNDARIO:
                                  $datosNovedad['IdTipoDocumento'] = NOVEDAD_EXTENSION_LICENCIA_SECUNDARIA;
                                  $datosNovedad['IdRegistroTipoDocumento'] = REGISTRO_NOVEDAD_EXTENSION_LICENCIA_SECUNDARIA;
                                  break;
                          }

                      } else {

                          $datosNovedad['IdTipoDocumento'] = NOVEDAD_EXTENSION_LICENCIA_SECUNDARIA;
                          $datosNovedad['IdRegistroTipoDocumento'] = REGISTRO_NOVEDAD_EXTENSION_LICENCIA_SECUNDARIA;
                      }*/

            $datosNovedad['IdTipoDocumento'] = $datosExtensionTipo['IdTipoDocumento'];
            $datosNovedad['IdRegistroTipoDocumento'] = $datosExtensionTipo['IdRegistroTipoDocumento'];

            $datosNovedad['IdPersona'] = $datos['IdPersonaSuplente'];
            $datosNovedad['IdLicencia'] = $datos['IdLicencia'];
            $datosNovedad['IdPuesto'] = $datos['IdPuesto'];
            $datosNovedad['Puesto_' . $datos['IdPuesto']] = 1;
            $datosNovedad['IdEscuela'] = $datos['IdEscuela'];
            $datosNovedad['PeriodoFechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datosLicencia['Fechas']['gte'], 'aaaa-mm-dd', 'dd/mm/aaaa');
            $datosNovedad['PeriodoFechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datosLicencia['Fechas']['lte'], 'aaaa-mm-dd', 'dd/mm/aaaa');
            $datosNovedad['FechaDesde'] = null;

            $oNovedad = new cDocumentos($this->conexion, $this->formato, $this->conexionES ?? new Elastic\Conexion());
            if (!$oNovedad->Insertar($datosNovedad, $codigoinsertado)) {
                $this->setError(400, 'error al insertar.');
                return false;
            }
        } else {
            $this->setError(400, 'Error al insertar. No se encuentra nivel');
            return false;
        }

        return true;
    }

    public function devolverExtensionTipoDocumento($datos, &$datosExtensionTipo): bool {

        $oExtensionSuplenciaTipo = new cExtensionSuplenciaTipo($this->conexion);

        if (!$oExtensionSuplenciaTipo->BusquedaxNivelxTipoCargo($datos, $resultado, $numfilas)) {
            return false;
        }

        if ($numfilas > 0) {
            $datosTipoDocumento = $this->conexion->ObtenerSiguienteRegistro($resultado);

            $datosExtensionTipo['IdTipoDocumento'] = $datosTipoDocumento['IdTipoDocumento'];
            $datosExtensionTipo['IdRegistroTipoDocumento'] = $datosTipoDocumento['IdRegistroTipoDocumento'];
        }


        return true;
    }


    /**
     * Crea una novedad de reintegro del suplido
     *
     * @param array $datos
     * @param       $codigoInsertado
     *
     * @return bool
     */
    public function reintegrarSuplido(array $datos, &$codigoInsertado): bool {

        $conexionES = new Elastic\Conexion();
        $oLicenciasES = new Elastic\Licencias($conexionES);
        $oNovedad = new cDocumentos($this->conexion, $this->formato, $this->conexionES ?? new Elastic\Conexion());
        $datosSolicitud['IdTipoDocumento'] = NOVEDAD_REINTEGRO;
        $datosSolicitud['IdRegistroTipoDocumento'] = REGISTRO_NOVEDAD_REINTEGRO;
        $datosSolicitud['IdPersona'] = $datos['IdPersona'];
        //	    $datosSolicitud['FechaDesde'] = date('d/m/Y');
        $datosSolicitud['PeriodoFechaDesde'] = date('d/m/Y');
        $datosSolicitud['IdEscuela'] = $datos['IdEscuela'];
        $datosSolicitud['FechaDesignacion'] = null;
        $datosSolicitud['FechaTomaPosesion'] = null;
        $datosSolicitud['Licencias'] = explode(',', $datos['IdLicencias']);

        if ($datos['IdLicencias'] == 'lic_0') {
            $datos['IdLicencias'] = '';
            $datosSolicitud['Licencias'] = 'lic_0';
        }

        $datosBuscar = [
            'IdPersona' => $datos['IdPersona'],
            'IdPuesto' => $datos['IdPuesto'],
            'IdLicencias' => $datos['IdLicencias'],
        ];


        if (!$this->BuscarxPuestoxIdPersona($datosBuscar, $resultadoPuestos, $numfilasPuestos))
            return false;

        while ($filaPuestos = $this->conexion->ObtenerSiguienteRegistro($resultadoPuestos))
            $arrayPuestosPersonas[$filaPuestos['IdPuesto']] = $filaPuestos['IdPuesto'];


        if (!$oLicenciasES->buscarxPersonaxRangoFecha($datosBuscar, $resultado, $numfilas, $total))
            return false;

        if ($numfilas > 0) {
            foreach ($resultado as $r) {
                $rs = $r['_source'];
                foreach ($rs['Cargos'] as $rc) {

                    if (!in_array($rc['Puesto']['Id'], $arrayPuestosPersonas))
                        continue;

                    if ($rc['Puesto']['Estado'] <> ACTIVO)
                        continue;

                    $datosSolicitud['Puesto_' . $rc['Puesto']['Id']] = 1;
                }
            }
        } else {
            if (in_array($datos['IdPuesto'], $arrayPuestosPersonas)) {
                $datosSolicitud['Puesto_' . $datos['IdPuesto']] = 1;
            }
        }


        if (!$oNovedad->Insertar($datosSolicitud, $codigoInsertado)) {
            if (FMT_ARRAY == $this->formato)
                $this->setError(400, 'error al insertar.');
            return false;
        }

        return true;
    }

    public function crearPlazaDestino(array $datos, &$codigoInsertado): bool {

        $oNovedad = new cDocumentos(
            $this->conexion,
            $this->formato,
            $this->conexionES ?? new Elastic\Conexion()
        );

        if (empty($datos['IdLicencias'])) {
            $this->setError(400, 'Debe indicar una licencia.');
            return false;
        }

        if (empty($datos['IdPuesto'])) {
            $this->setError(400, 'Puesto inválido.');
            return false;
        } else {
            #Traigo datos del puesto para pre-setear la plaza destino
            $oEscuelasPuestos = new cEscuelasPuestos($this->conexion);

            if (!$oEscuelasPuestos->BuscarxCodigo($datos, $resultado, $numfilas)) {
                $this->setError(400, 'Error al buscar puesto origen.');
                return false;
            }
            $filaPuestos = $this->conexion->ObtenerSiguienteRegistro($resultado);

            if(!$this->BuscarxIdPuestoxIdPersona($datos, $resultadoPofa, $numfilasPofa)){
                $this->setError(400, 'Error al buscar los datos de la persona en el puesto origen.');
                return false;
            }

            $filaPofa = $this->conexion->ObtenerSiguienteRegistro($resultadoPofa);
        }

        $horas = '';
        if ($filaPuestos['IdTipo'] == 1) {
            $horas = $filaPuestos['CantHoras'];
        } elseif ($filaPuestos['IdTipo'] == 2) {
            $horas = $filaPuestos['CantModulos'];
        }

        $datosSolicitud = [
            'IdTipoDocumento' => NOVEDAD_PLAZA_DESTINO,
            'IdRegistroTipoDocumento' => REGISTRO_NOVEDAD_PLAZA_DESTINO,
            'IdEscuela' => $datos['IdEscuela'] ?? null,
            'FechaDesignacion' => null,
            'FechaTomaPosesion' => null,
            'IdPofaOrigen' => $filaPofa['IdPofa'], #Pofa origen del nuevo pofa
            'IdPuestoOrigen' => $datos['IdPuesto'], #El puesto de donde se parte pasa a ser puesto origen del nuevo puesto
            'IdPuesto' => 0, #El puesto nuevo se setea en 0 hasta su creacion - dummy
            'IdLicencia' => $datos['IdLicencias'],
            'IdPersona' => $datos['IdPersona'],
            'IdNivelModalidad' => $filaPuestos['IdNivelModalidad'],
            'IdEscuelaTurno' => $filaPuestos['IdEscuelaTurno'],
            'IdPlanEducativo' => $filaPuestos['IdPlanEducativo'],
            'IdEscuelaTurnoAnioGrado' => $filaPuestos['IdEscuelaTurnoAnioGrado'],
            'IdSeccion' => $filaPuestos['IdSeccion'],
            'IdCargo' => $filaPuestos['IdCargo'],
            'IdMateria' => $filaPuestos['IdMateria'],
            'IdTipo' => $filaPuestos['IdTipo'],
            'CantHorasModulos' => $horas,
            'IdTemporalidadPuesto' => $filaPuestos['IdTemporalidadPuesto'],
            'IdFuncionCargo' => $filaPuestos['IdFuncionCargo'],
            'FechaDesde' => $filaPuestos['FechaDesde'],
            'IdRevista' => $filaPofa['IdRevista'],
            'AltaPlaza' => 1,
        ];


        $datosSolicitud['Puesto_' . $datos['IdPuesto']] = 1;


        if (!$oNovedad->Insertar($datosSolicitud, $codigoInsertado)) {
            if (FMT_ARRAY === $this->formato) {
                $this->setError(400, 'Error al insertar Plaza Destino.');
            }
            return false;
        }

        return true;
    }


    /**
     * Crea una novedad de designación urgente primaria
     *
     * @param array $datos
     * @param       $codigoInsertado
     *
     * @return bool
     */
    public function designacionUrgentePrimaria(array $datos, &$codigoInsertado): bool {

        if(!$this->ValidarPersonaYaPresenteEnPuesto($datos, $resultadoRepetida, $numfilasRepetida)) {
            return false;
        }

        $conexionES = new Elastic\Conexion();
        $oLicenciasES = new Elastic\Licencias($conexionES);
        $oNovedad = new cDocumentos($this->conexion, $this->formato, $this->conexionES ?? new Elastic\Conexion());
        $datosSolicitud['IdTipoDocumento'] = NOVEDAD_DESIGNACION_URGENTE_PRIMARIA;
        $datosSolicitud['IdRegistroTipoDocumento'] = REGISTRO_NOVEDAD_DESIGNACION_URGENTE_PRIMARIA;
        $datosSolicitud['IdPersona'] = $datos['IdPersona'];
        $datosSolicitud['PeriodoFechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['PeriodoFechaDesde'], 'aaaa-mm-dd', 'dd/mm/aaaa');
        $datosSolicitud['PeriodoFechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['PeriodoFechaHasta'], 'aaaa-mm-dd', 'dd/mm/aaaa');
        $datosSolicitud['IdEscuela'] = $datos['IdEscuela'];
        $datosSolicitud['FechaDesignacion'] = date('d/m/Y');
        $datosSolicitud['FechaTomaPosesion'] = null;
        $datosSolicitud['Licencias'] = $datos['IdLicencia'];
        $datosSolicitud['Puesto_' . $datos['IdPuesto']] = 1;


        if (!$oNovedad->Insertar($datosSolicitud, $codigoInsertado)) {
            if (FMT_ARRAY == $this->formato)
                $this->setError(400, 'error al insertar.');
            return false;
        }

        return true;
    }

    /**
     * Crea una novedad de cese de suplente
     *
     * @param array $datos
     * @param       $codigoInsertado
     *
     * @return bool
     */
    public function cesarSuplente(array $datos, &$codigoInsertado): bool {

        $oNovedad = new cDocumentos($this->conexion, $this->formato, $this->conexionES ?? new Elastic\Conexion());
        $datosSolicitud['IdTipoDocumento'] = NOVEDAD_CESE_SUPLENTE_DOC;
        $datosSolicitud['IdRegistroTipoDocumento'] = REGISTRO_NOVEDAD_CESE_SUPLENTE_DOC;
        $datosSolicitud['IdEscuela'] = $datos['IdEscuela'];
        $datosSolicitud['IdPersona'] = $datos['IdPersona'];
        $datosSolicitud['IdPuesto'] = $datos['IdPuesto'];
        $datosSolicitud['Puesto_' . $datos['IdPuesto']] = 1;
        $datosSolicitud['PeriodoFechaDesde'] = date('d/m/Y');
        $datosSolicitud['FechaDesignacion'] = null;
        $datosSolicitud['FechaTomaPosesion'] = null;

        if (!$oNovedad->Insertar($datosSolicitud, $codigoInsertado)) {
            if (FMT_ARRAY == $this->formato)
                $this->setError(400, 'error al insertar.');
            return false;
        }

        return true;
    }

    /**
     *  Crea una novedad de alta de docente manual con baja liquidacion
     *
     * @param array $datos
     * @param       $codigoInsertado
     *
     * @return bool
     *
     */
    public function altaManualConLiquidacion(array $datos, &$codigoInsertado): bool {

        if(!$this->ValidarPersonaYaPresenteEnPuesto($datos, $resultadoRepetida,$numfilasRepetida)) {
            return false;
        }

        if (!$this->_definirPuesto($datos))
            return false;

        $oNovedad = new cDocumentos($this->conexion, $this->formato, $this->conexionES ?? new Elastic\Conexion());
        $datosSolicitud['IdTipoDocumento'] = NOVEDAD_ALTA_MANUAL_VACANTE_CON_LIQUIDACION;
        $datosSolicitud['IdRegistroTipoDocumento'] = REGISTRO_NOVEDAD_ALTA_MANUAL_VACANTE_CON_LIQUIDACION;
        $datosSolicitud['IdEscuela'] = $datos['IdEscuela'];
        $datosSolicitud['IdPersona'] = $datos['IdPersona'];
        $datosSolicitud['IdPuesto'] = $datos['IdPuesto'];
        $datosSolicitud['Puesto_' . $datos['IdPuesto']] = 1;
        $datosSolicitud['PeriodoFechaDesde'] = date('d/m/Y');
        $datosSolicitud['FechaDesignacion'] = null;
        $datosSolicitud['FechaTomaPosesion'] = null;

        // para las altas manuales con liquidacion desde pofa viene el alta con seleccion de revista
        $datosSolicitud['GuardaRevista'] = false;
        if (isset($datos['IdRevista']) && $datos['IdRevista'] != "") {
            $datosSolicitud['IdRevista'] = $datos['IdRevista'];
            $datosSolicitud['GuardaRevista'] = true;
        }

        $datosSolicitud["IdExcepcionTipo"] = "";
        if (!FuncionesPHPLocal::isEmpty($datos["IdExcepcionTipo"]))
            $datosSolicitud["IdExcepcionTipo"] = $datos["IdExcepcionTipo"];

        if (!$oNovedad->Insertar($datosSolicitud, $codigoInsertado)) {
            if (FMT_ARRAY == $this->formato)
                $this->setError(400, 'error al insertar.');
            return false;
        }

        return true;
    }

    public function BuscarExcepcionesTipo($datos, &$resultado, &$numfilas): bool {

        if (!isset($datos['IdEstado']) || $datos['IdEstado'] == "")
            $datos['IdEstado'] = ACTIVO;

        return parent::BuscarExcepcionesTipo($datos, $resultado, $numfilas);
    }


    public function BuscarExcepcionesTipoxId($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarExcepcionesTipoxId($datos, $resultado, $numfilas);
    }

    public function BuscarPorEscuelaPorPersona(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::BuscarPorEscuelaPorPersona($datos, $resultado, $numfilas);
    }


    public function ValidarPersonaYaPresenteEnPuesto($datos, &$resultado, &$numfilas):bool {

        $base = BASEDATOS;
        $IdPuesto = $datos['IdPuesto'];
        $IdPersona = $datos['IdPersona'];
        $sql = "
            SELECT epp.*
            FROM $base.`EscuelasPuestos` ep
            INNER JOIN $base.`EscuelasPuestosPersonas` epp
            ON ep.`IdPuesto`=epp.`IdPuesto`
            WHERE (ep.IdPuestoRaiz = (
                SELECT IdPuestoRaiz
                FROM EscuelasPuestos
                WHERE IdPuesto = $IdPuesto
            ) OR ep.IdPuesto = $IdPuesto)
            AND epp.IdPersona = $IdPersona
            AND epp.Estado = 10
        ";

        if (!$this->conexion->ejecutarSQL($sql, "SEL", $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda de persona ya presente en el puesto.');
            return false;
        }

        if ($numfilas > 0) {
            $this->setError(400, "Error, la persona ya se encuentra activa en el puesto.");
            return false;
        }

        return true;
    }

    //-----------------------------------------------------------------------------------------
    //FUNCIONES PRIVADAS
    //-----------------------------------------------------------------------------------------


    public function _armarObjetoElastic(array $datos, ?array &$datosRegistro, &$datosElastic): bool {
        if (empty($datosRegistro)) {

            if (!$this->buscarParaElastic($datos, $resultado, $numfilas))
                return false;

            if ($numfilas != 1) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar el puesto de la persona.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }

            $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

            $datos['incluirCampos'] = ["Documento.*", "Nombre", "NombreCompleto", "Apellido", "Sexo.*", "CUIL"];
            $datos['IdPersona'] = $datosRegistro['IdPersona'];

            if ($this->conexionES === null) {
                $this->conexionES = new Elastic\Conexion();
            }
            $oPersona = new Elastic\Personas($this->conexionES);
            if (!$oPersona->buscarxCodigo($datos, $datosPersona)) {
                $this->setError($oPersona->getError());
                return false;
            }

            $datosRegistro['IdPersona'] = $datos['IdPersona'];
            $datosRegistro['Nombre'] = utf8_decode($datosPersona['Nombre']);
            $datosRegistro['Apellido'] = "";
            if (isset($datosPersona['Apellido']))
                $datosRegistro['Apellido'] = utf8_decode($datosPersona['Apellido']);

            $datosRegistro['Id'] = $datosRegistro['IdPofa'];
            $datosRegistro['IdTipoDocumento'] = $datosPersona['Documento']['Tipo']['Id'];
            $datosRegistro['DescripcionTipoDocumento'] = utf8_decode($datosPersona['Documento']['Tipo']['Nombre']);
            $datosRegistro['DNI'] = $datosPersona['Documento']['Numero'];

            if (isset($datosPersona['Sexo'])) {
                $datosRegistro['IdSexo'] = $datosPersona['Sexo']['Id'];
                $datosRegistro['DescripcionSexo'] = utf8_decode($datosPersona['Sexo']['Nombre']);
            }

            $datosRegistro['CodigoRevista'] = $datosRegistro['RevistaCodigo'];
            $datosRegistro['DescripcionRevista'] = $datosRegistro['RevistaDescripcion'];

            if (!FuncionesPHPLocal::isEmpty($datos['ExtiendeSuplencia']))
                $datosRegistro['ExtiendeSuplencia'] = $datos['ExtiendeSuplencia'];

            if (!FuncionesPHPLocal::isEmpty($datos['IdExcepcionTipo']))
                $datosRegistro['IdExcepcionTipo'] = $datos['IdExcepcionTipo'];

            if (!FuncionesPHPLocal::isEmpty($datos['ExcepcionNombre']))
                $datosRegistro['ExcepcionNombre'] = $datos['ExcepcionNombre'];
        }

        try {
            $datosElastic = Elastic\Puestos::armarDatosElastic($datosRegistro);
        } catch (Exception $e) {
            $this->setError($e->getCode(), $e->getMessage());
            return false;
        }


        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     */
    private function _actualizarHistoricos(array $datos): bool {

        if (!$this->buscarParaHistoricos($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(404, 'Error, no existe el registro');
            return false;
        }

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        $datosRegistro['IdLicencia'] = $datos['IdLicencia'] ?? null;
        $datosRegistro['IdNovedad'] = $datos['IdNovedad'] ?? null;
        $datosRegistro['Razon'] = $datos['Razon'] ?? null;
        $datosRegistro['FechaDesde'] = $datos['FechaDesde'] ?? $datosRegistro['FechaDesde'];
        $datosRegistro['FechaHasta'] = $datos['FechaHasta'] ?? $datosRegistro['FechaHasta'];

        $oHistoricos = new cPuestosHistoricos($this->conexion, $this->conexionES, $this->formato);
        if (!$oHistoricos->insertar($datosRegistro)) {
            $this->setError($oHistoricos->getError());
            return false;
        }

        return true;
    }


    private function ObtenerProximoOrden($datos, &$proxorden) {
        $proxorden = 0;
        if (!parent::BuscarUltimoOrden($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 0) {
            $datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $proxorden = $datos['maximo'] + 1;
        }
        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     * @throws Exception
     */
    private function _ValidarInsertar(array $datos): bool {
        if (!$this->_ValidarDatosVacios($datos)) {
            return false;
        }

        $validaPersonaRepetida = $datos['ValidaPersonaRepetida'] ?? true;
        if ($validaPersonaRepetida){
            if (!$this->ValidarPersonaYaPresenteEnPuesto($datos, $resultadoRepetida, $numfilasRepetido)) {
                return false;
            }
        }

        if (!$this->BuscarxIdPuestoxIdPersona($datos, $resultado, $numfilas)) {
            return false;
        }

        if (!$this->_ValidarInsertarIdExterno($datos)) {
            return false;
        }

        /*
        if ($numfilas > 0) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, el docente ya fue asociado.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }*/
        # anulo validacion de misma persona en mismo cupof
        /*
        $oPuestos = new cEscuelasPuestos($this->conexion, $this->conexionES, $this->formato);
        if (!$oPuestos->BuscarxCodigo($datos, $resultado_pof, $numfilas_pof)) {
            $this->setError($oPuestos->getError());
            return false;
        }

        if ($numfilas_pof == 1) {
            $filaPof = $this->conexion->ObtenerSiguienteRegistro($resultado_pof);
            $codPuesto = $filaPof['CodigoPuesto'];
        }

        $datosBuscar = [
            'CodigoPuesto' => $codPuesto ?? '',
            'Estado' => ACTIVO
        ];
        if (!$oPuestos->buscarxCodigoPuesto($datosBuscar, $resultado_puesto, $numfilas_puesto)) {
            $this->setError($oPuestos->getError());
            return false;
        }

        if ($numfilas_puesto > 0) {
            while ($r =  $this->conexion->ObtenerSiguienteRegistro($resultado_puesto)) {
                $datosBuscarPersona = [
                    'IdPuesto' => $r['IdPuesto'],
                    'IdPersona' => $datos['IdPersona']
                ];
                if (!$this->BuscarxIdPuestoxIdPersona($datosBuscarPersona, $resultado_persona, $numfilas_persona)) {
                    $this->setError($this->getError());
                    return false;
                }

                if ($numfilas_persona > 0) {
                    $this->setError(400,utf8_decode('El agente ya se encuentra activo en el cargo seleccionado. <br>Realice la reincorporación para poder finalizar el alta.'));
                    return false;
                }

            }
        }*/


        $datos['IdEstado'] = 1;
        if (!$this->BuscarxPuestoEstado($datos, $resultado, $numfilas)) {

            return false;
        }


        if ((!defined('PERMITE_MULTIPLES_ACTIVOS') || !PERMITE_MULTIPLES_ACTIVOS) && $numfilas > 0) {
            $this->setError(400, 'Error, hay docentes activos en el puesto');

            return false;
        }

        if (empty($datos['IgnorarConflictos'])) {
            return $this->_validarConflictos($datos);
        }


        return true;
    }


    private function _validarConflictos(array $datos, bool $devolverErrores = false, ?object &$errores = null): bool {

        $errores = new stdClass();
        $errores->Tipo = null;
        $oIncompatibilidades = new Elastic\Incompatibilidades($this->conexionES);
        $oTabla = new cInconsistenciasHoras($this->conexion);
        $oPuestos = new Elastic\Puestos($this->conexionES);
        $oDesempenos = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES, $this->formato);
        $oEscuelasPuestos = new cEscuelasPuestos($this->conexion);

        $datosBuscarPuesto['IdPuesto'] = $datos['IdPuesto'];
        if (!$oEscuelasPuestos->BuscarxCodigo($datosBuscarPuesto, $resultadoPuestos, $numfilasPuestos))
            return false;

        $datosNuevo = [];
        if ($numfilasPuestos > 0) {

            $fila = $this->conexion->ObtenerSiguienteRegistro($resultadoPuestos);

            $total = MULTIPLICAR_HORAS ?
                (int)($fila['CantModulos'] * CANT_MODULOS_PUESTO / CANT_HORAS_PUESTO) :
                (int)$fila['CantModulos'];
            $total += (int)$fila['CantHoras'];


            $datosNuevo = [
                'cargosJerarquicos' => $fila['Jerarquico'] ? 1 : 0,
                'cargosNoJerarquicos' => !$fila['IdMateria'] && !$fila['Jerarquico'] ? 1 : 0,  // suma de admin, base y supervisor
                'cargosAdministrativos' => null, // CHANGEME: estos tiene que ser reemplazados por los valores desagregados
                'cargosBase' => null, // CHANGEME: estos tiene que ser reemplazados por los valores desagregados
                'cargoSupervisor' => null, // CHANGEME: estos tiene que ser reemplazados por los valores desagregados
                'horasCatedra' => 0,
                'horasCatedraItinerantes' => null, // CHANGEME: estos tiene que ser reemplazados por los valores desagregados
            ];

            if (!$datosNuevo['cargosJerarquicos'] && !$datosNuevo['cargosNoJerarquicos']) {
                $datosNuevo['horasCatedra'] = $total;
            }
        }

        $datosBuscarPersona['IdPersona'] = $datos['IdPersona'];
        if (!$oTabla->validarPersona($datosBuscarPersona, $registro, $resumen, $datosNuevo)) {
            if (FMT_ARRAY == $this->formato)
                $this->setError($oTabla->getError());
            return false;
        }

        if ($resumen['tiene_conflictos']) {
            if ($devolverErrores) {
                $errores->Tipo = cSolicitudesCoberturaDesempeno::getTipoConflicto('R');
                $errores->Reglas = $resumen['error_msg'];
            } else {
                $this->setError(400, utf8_decode($resumen['error_msg']));
                return false;
            }
        }


        $datosBuscar['Id'] = $datos['IdPuesto'];
        $datosBuscar['excluirCampos'] = ['Id', 'UltimaModificacion.*', 'Alta.*', 'Tipo'];
        if (!$oPuestos->buscarxCodigo($datosBuscar, $datosPuesto)) {
            if (FMT_ARRAY == $this->formato)
                $this->setError($oPuestos->getError());
            return false;
        }


        if (!$oDesempenos->BuscarxCodigo($datos, $resultado, $numfilas)) {
            if (FMT_ARRAY == $this->formato)
                $this->setError($oDesempenos->getError());
            return false;
        }

        if ($numfilas > 0) {
            $datosNuevo = ['desempeno' => [], 'horas' => []];
            while ($filaDesempeno = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                $idPuesto = $filaDesempeno['IdPuesto'] . 'p';
                $dia = (int)$filaDesempeno['Dia'];
                $horario = new stdClass();
                $horario->gte = new DateTime($filaDesempeno['HoraInicio']);
                $filaDesempeno['HoraInicio'] = substr($filaDesempeno['HoraInicio'], 0, 5);
                $horario->lte = new DateTime($filaDesempeno['HoraFin']);
                $filaDesempeno['HoraFin'] = substr($filaDesempeno['HoraFin'], 0, 5);
                $datosNuevo['desempeno'][$dia][$idPuesto] = [
                    'id' => (int)$filaDesempeno['IdPuesto'],
                    'dia' => $filaDesempeno['Dia'],
                    'horario' => (object)['gte' => $filaDesempeno['HoraInicio'], 'lte' => $filaDesempeno['HoraFin']],
                    'desde' => substr($filaDesempeno['HoraInicio'], 0, 5),
                    'hasta' => substr($filaDesempeno['HoraFin'], 0, 5),
                    'puesto' => $datosPuesto,
                ];
                $datosNuevo['horas'][$dia][$idPuesto] = $horario;
            }

            if (!$oIncompatibilidades->validarSuperposicionHoraria($datos, $resumen, $datosNuevo)) {
                $this->setError($oIncompatibilidades->getError());
                return false;
            }

            if ($resumen['hay_conflictos']) {
                if ($devolverErrores) {
                    $errores->Tipo = cSolicitudesCoberturaDesempeno::getTipoConflicto(is_null($errores->Tipo) ? 'H' : 'A');
                    $errores->Horario = $resumen['colisiones'];
                } else {
                    $this->setError(409, json_encode($resumen['colisiones']));
                    return false;
                }
            }
        }
        return true;
    }


    private function _ValidarModificar($datos, &$datosRegistro) {

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un codigo valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!$this->_ValidarModificarIdExterno($datos))
            return false;

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);


        return true;
    }

    private function _ValidarExtensionFechaHasta($datos, &$datosRegistro) {

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un c�digo valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);


        return true;
    }


    private function _ValidarModificarIdExterno($datos) {

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un c�digo valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);


        if ($datos['IdPofaMigracion'] != $datosRegistro['IdPofaMigracion']) {

            if (cUsuariosPermisos::TienePermiso("009944")) {
                if (isset($datos['IdPofaMigracion']) && $datos['IdPofaMigracion'] != "") {

                    if (!$this->BuscarIdPofaMigracionxIdPersona($datos, $resultadosIdPodaMigracion, $numfilasIdPofaMigracion))
                        return false;

                    if ($numfilasIdPofaMigracion != 0) {

                        FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "El IdServicioExterno ya existe.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                        return false;

                    }
                }

            } else {

                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "No tiene permisos para editar el IdServicioExterno.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;

            }


        }


        return true;

    }


    private function _ValidarInsertarIdExterno($datos) {

        if (cUsuariosPermisos::TienePermiso("009944")) {
            if (isset($datos['IdPofaMigracion']) && $datos['IdPofaMigracion'] != "") {
                if (!$this->BuscarIdPofaMigracionxIdPersona($datos, $resultadosIdPodaMigracion, $numfilasIdPofaMigracion))
                    return false;

                if ($numfilasIdPofaMigracion != 0) {

                    FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "El IdServicioExterno ya existe.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                    return false;

                }
            }

        }


        return true;

    }


    private function _ValidarEliminar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;


        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }


    /**
     * Busco si existe una persona en el cargo. En caso de que sí, creo un registro en EscuelasPuestos asignando IdPuestoPadre, y con los mismos datos del padre
     * Busco los desempeños del puesto padre, si es que tiene, se los copio al puesto hijo
     *
     *
     * @param array $datos
     *
     * @return bool
     * @throws Exception
     */
    private function _definirPuesto(array &$datos): bool {

        $datosBuscar['IdPuesto'] = $datos['IdPuesto'];

        if (!$this->buscarxPuesto($datosBuscar, $resultado, $numfilas))
            return false;

        if ($numfilas != 1)
            return true;

        $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);

        $datosIns = $fila;
        $datosIns['IdPuestoPadre'] = $datos['IdPuesto'];
        $oPuestos = new cEscuelasPuestos($this->conexion, $this->conexionES);
        if (!$oPuestos->Insertar($datosIns, $codigoInsertado)) {
            $this->setError($oPuestos->getError());
            return false;
        }
        $datos['IdPuesto'] = $codigoInsertado;

        $oDesempenos = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES);
        if (!$oDesempenos->BuscarxCodigo($datosBuscar, $resultado, $numfilas)) {
            $this->setError($oDesempenos->getError());
            return false;
        }

        if ($numfilas > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

                $datosIns = [
                    'IdPuesto' => $datos['IdPuesto'],
                    'Dia' => $fila['Dia'],
                    'HoraInicio' => $fila['HoraInicio'],
                    'HoraFin' => $fila['HoraFin'],
                ];

                if (!$oDesempenos->Insertar($datosIns, $codigoInsertado)) {
                    $this->setError($oDesempenos->getError());
                    return false;
                }
            }
        }

        # si tiene puesto padre:
        # crear registro en escuelas puestos (enviar dato IdPuestoPadre)
        # crear registro con nuevo idpuesto en esc. puestos personas
        # copiar desempeño


        return true;
    }


    private function _SetearNull(&$datos): void {

        if (!isset($datos['IdPofa']) || $datos['IdPofa'] == "")
            $datos['IdPofa'] = "NULL";

        if (!isset($datos['IdPuesto']) || $datos['IdPuesto'] == "")
            $datos['IdPuesto'] = "NULL";

        if (!isset($datos['InstrumentoLegal']) || $datos['InstrumentoLegal'] == "")
            $datos['InstrumentoLegal'] = "NULL";

        if (!isset($datos['IdPersona']) || $datos['IdPersona'] == "")
            $datos['IdPersona'] = "NULL";

        if (!isset($datos['Orden']) || $datos['Orden'] == "")
            $datos['Orden'] = "0";

        if (!isset($datos['IdPofaSuperior']) || $datos['IdPofaSuperior'] == "")
            $datos['IdPofaSuperior'] = "NULL";

        if (!isset($datos['IdRevista']) || $datos['IdRevista'] == "")
            $datos['IdRevista'] = "NULL";

        if (!isset($datos['CodigoLiquidador']) || $datos['CodigoLiquidador'] == "")
            $datos['CodigoLiquidador'] = "NULL";

        if (!isset($datos['FechaDesde']) || $datos['FechaDesde'] == "")
            $datos['FechaDesde'] = "NULL";
        else
            $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], "dd/mm/aaaa", "aaaa-mm-dd");

        if (!isset($datos['FechaHasta']) || $datos['FechaHasta'] == "")
            $datos['FechaHasta'] = "NULL";
        else
            $datos['FechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], "dd/mm/aaaa", "aaaa-mm-dd");

        if (!isset($datos['FechaDesignacion']) || $datos['FechaDesignacion'] == "")
            $datos['FechaDesignacion'] = "NULL";
        else
            $datos['FechaDesignacion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesignacion'], "dd/mm/aaaa", "aaaa-mm-dd");

        if (!isset($datos['FechaTomaPosesion']) || $datos['FechaTomaPosesion'] == "")
            $datos['FechaTomaPosesion'] = "NULL";
        else
            $datos['FechaTomaPosesion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaTomaPosesion'], "dd/mm/aaaa", "aaaa-mm-dd");

        if (!isset($datos['FechaHastaPosesion']) || $datos['FechaHastaPosesion'] == "")
            $datos['FechaHastaPosesion'] = "NULL";
        else
            $datos['FechaHastaPosesion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHastaPosesion'], "dd/mm/aaaa", "aaaa-mm-dd");

        if (!isset($datos['IdPofaMigracion']) || $datos['IdPofaMigracion'] == "")
            $datos['IdPofaMigracion'] = "NULL";

        if (!isset($datos['IdExcepcionTipo']) || $datos['IdExcepcionTipo'] == "")
            $datos['IdExcepcionTipo'] = 1;

        if (!isset($datos['IdPofaOrigen']) || $datos['IdPofaOrigen'] == "")
            $datos['IdPofaOrigen'] = 'NULL';
    }


    private function _ValidarDatosVacios($datos) {

        if (!isset($datos['IdPuesto']) || $datos['IdPuesto'] == "") {

            $this->setError(400, "Debe ingresar un id puesto");
            return false;
        }

        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdPuesto'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo num&eacute;rico para el campo Id Puesto.");
                return false;
            }
            if (strlen($datos['IdPuesto']) > 11) {
                $this->setError(400, "Error, el campo Id Puesto no puede ser mayor a 11 .");
                return false;
            }
        }

        if (!isset($datos['IdPersona']) || $datos['IdPersona'] == "") {
            $this->setError(400, "Debe selecionar un docente");
            return false;
        }

        if (isset($datos['IdPersona']) && $datos['IdPersona'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdPersona'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo num&eacute;rico para el campo Id Persona.");
                return false;
            }
            if (strlen($datos['IdPersona']) > 11) {
                $this->setError(400, "Error, el campo Id Persona no puede ser mayor a 11 .");
                return false;
            }
        }

        if (!isset($datos['IdRevista']) || $datos['IdRevista'] == "") {
            $this->setError(400, "Debe ingresar un id revista");
            return false;
        }

        if (isset($datos['IdRevista']) && $datos['IdRevista'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdRevista'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo num&eacute;rico para el campo Id Revista.");
                return false;
            }
            if (strlen($datos['IdRevista']) > 2) {
                $this->setError(400, "Error, el campo Id Revista no puede ser mayor a 2 .");
                return false;
            }
        }

        if (!isset($datos['FechaDesignacion']) || $datos['FechaDesignacion'] == "") {
            $this->setError(400, "Debe ingresar una fecha designaci&oacute;n");
            return false;
        }

        if (isset($datos['FechaDesignacion']) && $datos['FechaDesignacion'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaDesignacion'], "FechaDDMMAAAA")) {
                $this->setError(400, "Error debe ingresar una fecha valida para el campo Fecha Designación.");
                return false;
            }
        }

        if (!isset($datos['FechaTomaPosesion']) || $datos['FechaTomaPosesion'] == "") {
            $this->setError(400, "Debe ingresar una fecha toma posesión");
            return false;
        }

        if (isset($datos['FechaTomaPosesion']) && $datos['FechaTomaPosesion'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaTomaPosesion'], "FechaDDMMAAAA")) {
                $this->setError(400, "Error debe ingresar una fecha valida para el campo Fecha Toma Posesión.");
                return false;
            }
        }


        $FechaHastaPosesion = "";
        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaHasta'], "FechaDDMMAAAA")) {
                $this->setError(400, "Error debe ingresar una fecha valida para el campo Fecha Hasta.");
                return false;
            }
            $FechaHastaPosesion = new DateTime(FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], "dd/mm/aaaa", "aaaa-mm-dd"));
        }

        $FechaDesignacion = new DateTime(FuncionesPHPLocal::ConvertirFecha($datos['FechaDesignacion'], "dd/mm/aaaa", "aaaa-mm-dd"));
        $FechaTomaPosesion = new DateTime(FuncionesPHPLocal::ConvertirFecha($datos['FechaTomaPosesion'], "dd/mm/aaaa", "aaaa-mm-dd"));

        if ($FechaTomaPosesion < $FechaDesignacion) {
            $this->setError(400, utf8_decode("La fecha toma de posesión no puede ser menor a la de designación."));
            return false;
        }

        if ($FechaHastaPosesion instanceof DateTime) {
            if ($FechaTomaPosesion > $FechaHastaPosesion) {
                $this->setError(400, utf8_decode("La fecha toma de posesión no puede ser mayor a la fecha hasta."));
                return false;
            }
            if ($FechaDesignacion > $FechaHastaPosesion) {
                $this->setError(400, utf8_decode("La fecha hasta no puede ser mayor a la fecha designación."));
                return false;
            }
        }

        return true;
    }

    /**
     * @param object $conflictos
     *
     * @return object|string|null
     */
    private static function cargarConflictos(object $conflictos) {
        switch ($conflictos->Tipo) {
            case cSolicitudesCoberturaDesempenoDB::CONFLICTO_AMBOS:
                unset($conflictos->Tipo);
                return $conflictos;
            case cSolicitudesCoberturaDesempenoDB::CONFLICTO_HORARIO:
                return $conflictos->Horario;
            case cSolicitudesCoberturaDesempenoDB::CONFLICTO_REGLAS:
                return $conflictos->Reglas;
        }
        return null;
    }

    public function buscarLicenciasxPofa($datos, &$resultado, &$numfilas): bool {
        return parent::buscarLicenciasxPofa($datos, $resultado, $numfilas);
    }

    public function ModificarEstadoFechaHasta($datos): bool {

        if (!parent::ModificarEstadoFechaHasta($datos))
            return false;

        return true;
    }

    /**
     * modificar solo el codigoLiquidador
     *
     */
    public function ModificarCodigoLiquidador($datos): bool {

        #valido que puedo modificar, buscando en la BD todos los datos de la POFA
        if (!$this->_ValidarModificarCodLiq($datos, $datosRegistro))
            return false;

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];


        $this->_SetearNull($datos);

        //if (!parent::ModificarFechaHasta($datos))
        //  return false;
        if (!parent::ModificarCodigoLiquidador($datos))
            return false;


        //TODO: REVISAR QUE FUNCIONE AUDITORIA
        $oAuditoriasEscuelasPuestosPersonas = new cAuditoriasEscuelasPuestosPersonas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestosPersonas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        $datosBusqueda = [];
        if (!$this->_armarObjetoElastic($datos, $datosBusqueda, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        /*$datos['FechaHasta'] = $datos['FechaHasta'] ?? NULL;
        $datos['Razon'] = $datos['Razon'] ?? 'Cambio';
        if (!$this->_actualizarHistoricos($datos))
            return false;
        */
        return true;
    }

    private function _ValidarModificarCodLiq($datos, &$datosRegistro) {

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un c�digo valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        //if (!$this->_ValidarModificarIdExterno($datos))
        //  return false;

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);


        return true;
    }

    public function InsertarRepublicarElastic($datos): bool {

        if (!parent::InsertarRepublicarElastic($datos))
            return false;

        return true;
    }

    private function _ValidarInsertarExtemporal($datos): bool {
        //validaciones propias de la extermporal

        if (!isset($datos['FechaDesignacion']) || $datos['FechaDesignacion'] == "") {
            $this->setError(400, "Error, debe ingresar una fecha designaci&oacute;n");
            return false;
        }

        if (isset($datos['FechaDesignacion']) && $datos['FechaDesignacion'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaDesignacion'], "FechaDDMMAAAA")) {
                $this->setError(400, "Error, debe ingresar una fecha v&aacute;lida para el campo Fecha Designaci&oacute;n.");
                return false;
            }
        }

        if (!isset($datos['FechaTomaPosesion']) || $datos['FechaTomaPosesion'] == "") {
            $this->setError(400, "Error, debe ingresar una fecha toma posesi&oacute;n");
            return false;
        }

        if (isset($datos['FechaTomaPosesion']) && $datos['FechaTomaPosesion'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaTomaPosesion'], "FechaDDMMAAAA")) {
                $this->setError(400, "Error, debe ingresar una fecha v&aacute;lida para el campo Fecha Toma Posesi&oacute;n.");
                return false;
            }
        }


        $FechaHastaPosesion = "";
        if (!isset($datos['FechaHasta']) || $datos['FechaHasta'] == "") {
            $this->setError(400, "Error, debe ingresar una Fecha Hasta.");
            return false;
        } else {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaHasta'], "FechaDDMMAAAA")) {
                $this->setError(400, "Error, debe ingresar una fecha v&aacute;lida para el campo Fecha Hasta.");
                return false;
            }
            $FechaHastaPosesion = new DateTime(FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], "dd/mm/aaaa", "aaaa-mm-dd"));

            $ahora = new DateTime();

            // la fecha hasta debe ser anterior a hoy
            if ($FechaHastaPosesion->format('Y-m-d') >= $ahora->format('Y-m-d')) {
                $this->setError(400, "Error, debe ingresar una Fecha Hasta anterior a la fecha actual.");
                return false;
            }
        }

        return true;
    }
}
