<?php

use Bigtree\Logica\Movimientos;

include(DIR_CLASES_DB . "cDocumentos.db.php");

class cDocumentos extends cDocumentosdb {
    const ALGO = OPENSSL_ALGO_SHA256;
    protected $conexion;
    protected $formato;
    protected $Campos;
    private $private;
    private $public;
    /**
     * @var Elastic\Conexion
     */
    private $conexionES;

    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO, ?Elastic\Conexion $conexionES = null) {
        parent::__construct($conexion, $formato);
        $this->conexionES = $conexionES;
        $this->conexion = &$conexion;
        $this->formato = &$formato;
        $this->Campos = [];
        $this->private = openssl_pkey_get_private('file://' . DIR_ROOT . '/certs/novedades');
        $this->public = openssl_pkey_get_public('file://' . DIR_ROOT . '/certs/novedades.pub');

    }

    static function DevolverEstadoPublico($fila, $arrayEstadosPublicos, $decode = true) {
        $Estado = "";
        if (isset($fila['Area']['Nombre']) && is_string($fila['Area']['Nombre'])) {
            if ($decode)
                $fila['Area']['Nombre'] = utf8_decode($fila['Area']['Nombre']);
            $Estado = FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Area']['Nombre'], ENT_QUOTES);
        }
        if (isset($fila['Estado']['Nombre']) && is_string($fila['Area']['Nombre'])) {
            if ($decode)
                $fila['Estado']['Nombre'] = utf8_decode($fila['Estado']['Nombre']);
            $Estado .= " - " . FuncionesPHPLocal::HtmlspecialcharsSistema($fila['Estado']['Nombre'], ENT_QUOTES);
        }
        /* if (in_array(ROLEQUIPOCONDUCCION,$_SESSION['rolcod']))
         {
             if (isset($arrayEstadosPublicos['Estados']) && array_key_exists($fila['Estado']['Id'],$arrayEstadosPublicos['Estados']))
                 $Estado = utf8_decode($arrayEstadosPublicos['Estados'][$fila['Estado']['Id']]);
         }/*elseif(in_array(ROLSAD,$_SESSION['rolcod'])){
             if (isset($arrayEstadosPublicos['EstadosSad']) && isset($fila['Estado']['Id']) && array_key_exists($fila['Estado']['Id'],$arrayEstadosPublicos['EstadosSad']))
                 $Estado = utf8_decode($arrayEstadosPublicos['EstadosSad'][$fila['Estado']['Id']]);
         }elseif (in_array(ROLCONSEJO,$_SESSION['rolcod'])){
             if (isset($arrayEstadosPublicos['EstadosConsejo']) && isset($fila['Estado']['Id']) && array_key_exists($fila['Estado']['Id'],$arrayEstadosPublicos['EstadosConsejo']))
                 $Estado = utf8_decode($arrayEstadosPublicos['EstadosConsejo'][$fila['Estado']['Id']]);
         }*/

        return $Estado;
    }

    static function DevolverComboEstados($arrayEstadosPublicos, $vecestadosHabilitados = []) {
        $arrayEstadosPublicosCombo = [];
        if (in_array(ROLEQUIPOCONDUCCION, $_SESSION['rolcod'])) {
            if (isset($arrayEstadosPublicos['EstadosPublicos']))
                $arrayEstadosPublicosCombo = $arrayEstadosPublicos['EstadosPublicos'];
        } elseif (in_array(ROLSAD, $_SESSION['rolcod'])) {
            if (isset($arrayEstadosPublicos['EstadosPublicosSad']))
                $arrayEstadosPublicosCombo = $arrayEstadosPublicos['EstadosPublicosSad'];
        } elseif (in_array(ROLCONSEJO, $_SESSION['rolcod'])) {
            if (isset($arrayEstadosPublicos['EstadosPublicosConsejo']))
                $arrayEstadosPublicosCombo = $arrayEstadosPublicos['EstadosPublicosConsejo'];
        }
        $ArrayEstadosBusqueda = [];
        foreach ($arrayEstadosPublicosCombo as $IdEstadoPublico => $dataPublica) {
            foreach ($dataPublica['Estados'] as $IdEstado => $Estados) {
                //if (in_array($IdEstado,$vecestadosHabilitados))
                {
                    $ArrayEstadosBusqueda[$IdEstadoPublico]['Nombre'] = $dataPublica['NombrePublico'];
                    $ArrayEstadosBusqueda[$IdEstadoPublico]['Estados'][$IdEstado] = $IdEstado;
                }

            }

        }

        return $ArrayEstadosBusqueda;
    }

    public static function ordenarDatosVerificacion(array $datos): array {
        return [
            'IdDocumento' => (!FuncionesPHPLocal::isEmpty($datos['IdDocumento']) ? $datos['IdDocumento'] : null),
            'IdEscuela' => (!FuncionesPHPLocal::isEmpty($datos['Escuela']['Id']) ? $datos['Escuela']['Id'] : (!FuncionesPHPLocal::isEmpty($datos['IdEscuela']) ? $datos['IdEscuela'] : null)),
            'IdEscuelaDestino' => (!FuncionesPHPLocal::isEmpty($datos['EscuelaDestino']['Id']) ? $datos['EscuelaDestino']['Id'] : (!FuncionesPHPLocal::isEmpty($datos['IdEscuelaDestino']) ? $datos['IdEscuelaDestino'] : null)),
            'IdTipoDocumento' => (!FuncionesPHPLocal::isEmpty($datos['TipoDocumento']['Id']) ? $datos['TipoDocumento']['Id'] : (!FuncionesPHPLocal::isEmpty($datos['IdTipoDocumento']) ? $datos['IdTipoDocumento'] : null)),
            'IdRegistroTipoDocumento' => (!FuncionesPHPLocal::isEmpty($datos['TipoDocumento']['IdRegistro']) ? $datos['TipoDocumento']['IdRegistro'] : (!FuncionesPHPLocal::isEmpty($datos['IdRegistroTipoDocumento']) ? $datos['IdRegistroTipoDocumento'] : null)),
            'IdPersona' => (!FuncionesPHPLocal::isEmpty($datos['Agente']['Id']) ? $datos['Agente']['Id'] : (!FuncionesPHPLocal::isEmpty($datos['IdPersona']) ? $datos['IdPersona'] : null)),
            'IdLicencia' => (!FuncionesPHPLocal::isEmpty($datos['Licencia']['Id']) ? $datos['Licencia']['Id'] : (!FuncionesPHPLocal::isEmpty($datos['IdLicencia']) ? $datos['IdLicencia'] : null)),
            'PeriodoFechaDesde' => (!FuncionesPHPLocal::isEmpty($datos['Periodo']['FechaDesde']) ? substr($datos['Periodo']['FechaDesde'], 0, 10) : (!FuncionesPHPLocal::isEmpty($datos['PeriodoFechaDesde']) ? substr($datos['PeriodoFechaDesde'], 0, 10) : null)),
            'PeriodoFechaHasta' => (!FuncionesPHPLocal::isEmpty($datos['Periodo']['FechaHasta']) ? substr($datos['Periodo']['FechaHasta'], 0, 10) : (!FuncionesPHPLocal::isEmpty($datos['PeriodoFechaHasta']) ? substr($datos['PeriodoFechaHasta'], 0, 10) : null)),
            'FechaEnvio' => (!FuncionesPHPLocal::isEmpty($datos['FechaEnvio']) ? substr($datos['FechaEnvio'], 0, 10) : null),
            'FechaTomaPosesion' => (!FuncionesPHPLocal::isEmpty($datos['FechaTomaPosesion']) ? substr($datos['FechaTomaPosesion'], 0, 10) : null),
            'FechaDesignacion' => (!FuncionesPHPLocal::isEmpty($datos['FechaDesignacion']) ? substr($datos['FechaDesignacion'], 0, 10) : null),
            //'NroResolucion' => (!FuncionesPHPLocal::isEmpty($datos['NroResolucion']) ? $datos['NroResolucion'] : NULL),
            //'Firma' => $datos['Firma'] ?? '',
        ];
    }

    function __destruct() {
        parent::__destruct();
    }

    public function BuscarxCodigo($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxCodigos($datos, &$resultado, &$numfilas) {
        if (!isset($datos['IdDocumento']) || $datos['IdDocumento'] == "")
            $datos['IdDocumento'] = "-1";


        if (!parent::BuscarxCodigos($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarUltimoDocumentoIngresado(&$IdDocumento, &$datosRegistro) {
        if (!parent::BuscarUltimoDocumentoIngresado($resultado, $numfilas))
            return false;
        if ($numfilas == 1) {
            $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $IdDocumento = intval($datosRegistro['IdDocumento']);

        } else {
            $datosRegistro = [];
            $IdDocumento = '';
        }
        return true;
    }

    public function BuscarxCodigoPadre($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarxCodigoPadre($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaAvanzadaxCodigoPadre($datos, &$resultado, &$numfilas) {

        $sparam = [
            'IdDocumentoPadre' => $datos['IdDocumentoPadre'],
            'xIdEstado' => 0,
            'IdEstado' => '-1',
            'xNotIdEstado' => 0,
            'NotIdEstado' => '-1',
            'limit' => '',
            'orderby' => "IdDocumento DESC",
        ];

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['NotIdEstado']) && $datos['NotIdEstado'] != "") {
            $sparam['NotIdEstado'] = $datos['NotIdEstado'];
            $sparam['xNotIdEstado'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzadaxCodigoPadre($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxCodigoFormatoElastic($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarxCodigoFormatoElastic($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarEscuelasCombo(&$resultado, &$numfilas): bool {
        if (!parent::BuscarEscuelasCombo($resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarDocumentosRaizVigentes($datos, &$resultado, &$numfilas) {
        if (!isset($datos['Vigencia']) || $datos['Vigencia'] == "")
            $datos['Vigencia'] = $_SESSION['Anio'] . $_SESSION['Mes'] . $_SESSION['Dia'];

        if (!isset($datos['IdUsuario']) || $datos['IdUsuario'] == "")
            $datos['IdUsuario'] = $_SESSION['usuariocod'];

        if (!parent::BuscarDocumentosRaizVigentes($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarDocumentoRaizxIdDocumento($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarDocumentoRaizxIdDocumento($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaAvanzadaDocumentosRaizVigentes($datos, &$resultado, &$numfilas) {
        if (!isset($datos['Vigencia']) || $datos['Vigencia'] == "")
            $datos['Vigencia'] = $_SESSION['Anio'] . $_SESSION['Mes'] . $_SESSION['Dia'];

        if (!isset($datos['IdCliente']) || $datos['IdCliente'] == "")
            $datos['IdCliente'] = $_SESSION['IdCliente'];


        if (isset($datos['IdArea']) && $datos['IdArea'] != "") {
            $datos['xIdArea'] = 1;
        } else {
            $datos['xIdArea'] = 0;
            $datos['IdArea'] = "";
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $datos['xIdTipoDocumento'] = 1;
        } else {
            $datos['xIdTipoDocumento'] = 0;
            $datos['IdTipoDocumento'] = "";
        }

        if (isset($datos['Cuit']) && $datos['Cuit'] != "") {
            $datos['xCuit'] = 1;
        } else {
            $datos['xCuit'] = 0;
            $datos['Cuit'] = "";
        }

        if (!parent::BusquedaAvanzadaDocumentosRaizVigentes($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaAvanzada($datos, &$resultado, &$numfilas) {
        $sparam = [
            'xIdDocumento' => 0,
            'IdDocumento' => "",
            'xIdArea' => 0,
            'IdArea' => "",
            'xIdTipoDocumento' => 0,
            'IdTipoDocumento' => "",
            'xIdEstado' => 0,
            'IdEstado' => "",
            'xIdCategoria' => 0,
            'IdCategoria' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "",
            'xFechaDesde' => "",
            'FechaDesde' => "",
            'xFechaHasta' => "",
            'FechaHasta' => "",
            'limit' => '',
            'orderby' => "IdDocumento DESC",
        ];

        if (isset($datos['IdDocumento']) && $datos['IdDocumento'] != "") {
            $sparam['IdDocumento'] = $datos['IdDocumento'];
            $sparam['xIdDocumento'] = 1;
        }

        if (isset($datos['IdArea']) && $datos['IdArea'] != "") {
            $sparam['IdArea'] = $datos['IdArea'];
            $sparam['xIdArea'] = 1;
        }


        if (isset($datos['TipoDocumento']) && $datos['TipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['TipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        } else {
            $sparam['IdTipoDocumento'] = [''];
            $sparam['xIdTipoDocumento'] = 0;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['IdCategoria']) && $datos['IdCategoria'] != "") {
            $sparam['IdCategoria'] = $datos['IdCategoria'];
            $sparam['xIdCategoria'] = 1;
        }


        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            $sparam['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd') . " 00:00:00";
            $sparam['xFechaDesde'] = 1;
        }

        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            $sparam['FechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], 'dd/mm/aaaa', 'aaaa-mm-dd') . " 23:59:59";
            $sparam['xFechaHasta'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarDocumentosRaizVigentesxIdClientexIdUsuario($datos, &$resultado, &$numfilas) {
        if (!isset($datos['Vigencia']) || $datos['Vigencia'] == "")
            $datos['Vigencia'] = $_SESSION['Anio'] . $_SESSION['Mes'] . $_SESSION['Dia'];

        if (!isset($datos['IdCliente']) || $datos['IdCliente'] == "")
            $datos['IdCliente'] = $_SESSION['IdCliente'];

        if (!isset($datos['IdUsuario']) || $datos['IdUsuario'] == "")
            $datos['IdUsuario'] = $_SESSION['IdUsuario'];

        if (!parent::BuscarDocumentosRaizVigentesxIdClientexIdUsuario($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function Insertar($datos, &$codigoinsertado, $NoValidarDatos = false) {

        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un tipo de documento.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        $oObjeto = new cDocumentosPermisos($this->conexion, $this->formato);
        $datosBusqueda = [];
        $datosBusqueda['IdTipoDocumento'] = $datos['IdTipoDocumento'];
        $datosBusqueda['IdArea'] = $_SESSION['IdArea'];
        if (!$oObjeto->PuedeAgregarDocumento($datosBusqueda, $resultado, $numfilas)) {
            return false;
        }

        if ($numfilas < 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, no tiene permisos para agregar el documento.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosTipoDocumento = $this->conexion->ObtenerSiguienteRegistro($resultado);

        $datos['IdRegistroTipoDocumento'] = $datosTipoDocumento['IdRegistro'];
        $datos['IdTipoDocumento'] = $datosTipoDocumento['IdTipoDocumento'];
        $datos['NombreTipoDocumento'] = $datosTipoDocumento['Nombre'];
        $datos['IdTipoDocumentoPadre'] = $datosTipoDocumento['IdTipoDocumentoPadre'];
        $datos['ModificaCargos'] = true;


        if (!$this->_ValidarInsertar($datos, $NoValidarDatos)) {
            return false;
        }


        if (isset($datosTipoDocumento['ClassEjecutar']) && class_exists($datosTipoDocumento['ClassEjecutar'])) {
            $class = new $datosTipoDocumento['ClassEjecutar']($this->conexion, $this->formato, $this->conexionES);
            if (method_exists($class, "ValidarInsertar")) {
                $Tipo = 1;
                if (!$class->ValidarInsertar($datos, $Tipo)) {
                    return false;
                }

            }
        }

        $this->_SetearNull($datos);


        $AltaEscuela = $datos['IdEscuela'] ?? 'NULL';;


        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        //$datos['AltaEscuela'] = $datos['UltimaModificacionEscuela'] = $_SESSION['IdEscuela'] ?? 'NULL';
        $datos['AltaEscuela'] = $datos['UltimaModificacionEscuela'] = $AltaEscuela;
        $datos['AltaRol'] = $datos['UltimaModificacionRol'] = implode(',', $_SESSION['rolcod']);
        $datos['IdArea'] = $datos['IdArea'] ?? $datosTipoDocumento['IdAreaInicial'];
        $datos['IdAreaInicial'] = $datos['IdArea'] ?? $datosTipoDocumento['IdAreaInicial'];
        $datos['IdEstado'] = $datosTipoDocumento['IdEstadoInicial'];
        $datos['IdEstadoInicial'] = $datosTipoDocumento['IdEstadoInicial'];

        if (!parent::Insertar($datos, $codigoinsertado)) {
            return false;
        }
        $datos['IdDocumento'] = $codigoinsertado;

        # Licencias
        if (isset($datos['Licencias']) && $datos['Licencias'] != 'lic_0') {

            $oDocumentosLicencias = new cDocumentosLicencias($this->conexion);
            foreach ($datos['Licencias'] as $r) {

                $datosLicencia = [
                    'IdDocumento' => $datos['IdDocumento'],
                    'IdLicencia' => $r,
                ];

                if (!$oDocumentosLicencias->Insertar($datosLicencia)) {
                    $this->setError($oDocumentosLicencias->getError());
                    return false;
                }
            }
        }

        $oDocumentosPuestos = new cDocumentosPuestos($this->conexion, $this->formato);
        if (!$oDocumentosPuestos->ActualizarDatos($datos, $codigosInsertados))
            return false;


        if (!FuncionesPHPLocal::isEmpty($datosTipoDocumento['ClassEjecutar']) && class_exists($datosTipoDocumento['ClassEjecutar'])) {
            /** @var iTiposDocumentosIns $object */
            $object = new $datosTipoDocumento['ClassEjecutar']($this->conexion, $this->formato, $this->conexionES);
            if (method_exists($object, 'insertar')) {
                if (!$object->insertar($datos)) {
                    if (FMT_ARRAY == $this->formato)
                        $this->setError($object->getError());
                    return false;
                }
            }
        }

        if (isset($datos['IdDocumentoRelacionado']) && count($datos['IdDocumentoRelacionado']) > 0) {
            $oDocumentosRelacionados = new cDocumentosRelacionados($this->conexion, $this->formato);
            foreach ($datos['IdDocumentoRelacionado'] as $IdDocumentoRelacionado) {
                $datos['IdDocumentoRelacionado'] = $IdDocumentoRelacionado;
                if (!$oDocumentosRelacionados->Insertar($datos)) {
                    return false;
                }
            }
        }

        if (!$this->ModificarHashDato($datos))
            return false;

        if (isset($datosTipoDocumento['ClassEjecutar']) && class_exists($datosTipoDocumento['ClassEjecutar'])) {
            $class = new $datosTipoDocumento['ClassEjecutar']($this->conexion, $this->formato);
            if (method_exists($class, "Insertar")) {
                if (!$class->Insertar($datos)) {
                    return false;
                }
            }
        }

        $oDocumentosArchivos = new cDocumentosArchivos($this->conexion, $this->formato);
        if (!$oDocumentosArchivos->ActualizarArchivos($datos, $datos, $arrayInsertados)) {
            return false;
        }


        $oAuditoriasDocumentos = new cAuditoriasDocumentos($this->conexion, $this->formato);
        $datos['IdDocumento'] = $codigoinsertado;
        $datos['Accion'] = INSERTAR;

        $datos['AltaEscuela'] = $_SESSION['IdEscuela'] ?? 'NULL';
        $datos['AltaRol'] = implode(',', $_SESSION['rolcod']);
        if (!$oAuditoriasDocumentos->InsertarLog($datos, $codigoInsertadolog)) {
            return false;
        }


        if (!$this->_armarDatosElastic($datos, $datosRegistroElastic, $datosElastic)) {
            return false;
        }


        $oElastic = new Elastic\Modificacion(SUFFIX_NOVEDADES, $this->conexionES);
        if (!$oElastic->Insertar($datosElastic)) {
            $this->setError($oElastic->getError());
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $this->getError()['error_description'], ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }


        $datos['Id'] = $codigoInsertadolog;
        $datos['AccionCambio'] = "Insertar";

        $datos['IdFilaLog'] = $codigoInsertadolog;

        $datos['Estado'] = [];

        $datos['Estado']['Inicial']['Id'] = $datosTipoDocumento['IdEstadoInicial'];
        $datos['Estado']['Inicial']['Nombre'] = $datosTipoDocumento['EstadoInicial'];
        $datos['Estado']['Final']['Id'] = $datosTipoDocumento['IdEstadoInicial'];
        $datos['Estado']['Final']['Nombre'] = $datosTipoDocumento['EstadoInicial'];
        $datos['Area']['Inicial']['Id'] = $datosTipoDocumento['IdAreaInicial'];
        $datos['Area']['Inicial']['Nombre'] = $datosTipoDocumento['AreaInicial'];
        $datos['Area']['Final']['Id'] = $datosTipoDocumento['IdAreaInicial'];
        $datos['Area']['Final']['Nombre'] = $datosTipoDocumento['AreaInicial'];
        if (!$this->_armarObjetoHistoricos($datos, $datosHistoricos)) {
            return false;
        }

        $oElastic = new Elastic\Modificacion(SUFFIX_NOVEDADES_HISTORICOS, $this->conexionES);
        if (!$oElastic->Insertar($datosHistoricos)) {
            $this->setError($oElastic->getError());
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $this->getError()['error_description'], ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }



    public function InsertarExtemporal($datos, &$codigoinsertado, $NoValidarDatos = false) {

        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un tipo de documento.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        $oObjeto = new cDocumentosPermisos($this->conexion, $this->formato);
        $datosBusqueda = [];
        $datosBusqueda['IdTipoDocumento'] = $datos['IdTipoDocumento'];
        $datosBusqueda['IdArea'] = $_SESSION['IdArea'];
        if (!$oObjeto->PuedeAgregarDocumento($datosBusqueda, $resultado, $numfilas)) {
            return false;
        }

        if ($numfilas < 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, no tiene permisos para agregar el documento.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosTipoDocumento = $this->conexion->ObtenerSiguienteRegistro($resultado);

        $datos['IdRegistroTipoDocumento'] = $datosTipoDocumento['IdRegistro'];
        $datos['IdTipoDocumento'] = $datosTipoDocumento['IdTipoDocumento'];
        $datos['NombreTipoDocumento'] = $datosTipoDocumento['Nombre'];
        $datos['IdTipoDocumentoPadre'] = $datosTipoDocumento['IdTipoDocumentoPadre'];
        $datos['ModificaCargos'] = true;


        if (!$this->_ValidarInsertar($datos, $NoValidarDatos)) {
            return false;
        }


        if (isset($datosTipoDocumento['ClassEjecutar']) && class_exists($datosTipoDocumento['ClassEjecutar'])) {

            $class = new $datosTipoDocumento['ClassEjecutar']($this->conexion, $this->formato, $this->conexionES);
            if (method_exists($class, "ValidarInsertar")) {
                $Tipo = 1;
                if (!$class->ValidarInsertar($datos, $Tipo)) {
                    return false;
                }

            }
        }

        $datos["PeriodoFechaDesde"] = $datos["FechaDesde"];
        $datos["PeriodoFechaHasta"] = $datos["FechaHasta"];

        $this->_SetearNull($datos);

        $AltaEscuela = $datos['IdEscuela'] ?? 'NULL';;

        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        //$datos['AltaEscuela'] = $datos['UltimaModificacionEscuela'] = $_SESSION['IdEscuela'] ?? 'NULL';
        $datos['AltaEscuela'] = $datos['UltimaModificacionEscuela'] = $AltaEscuela;
        $datos['AltaRol'] = $datos['UltimaModificacionRol'] = implode(',', $_SESSION['rolcod']);
        $datos['IdArea'] = $datos['IdArea'] ?? $datosTipoDocumento['IdAreaInicial'];
        $datos['IdAreaInicial'] = $datos['IdArea'] ?? $datosTipoDocumento['IdAreaInicial'];
        $datos['IdEstado'] = $datosTipoDocumento['IdEstadoInicial'];
        $datos['IdEstadoInicial'] = $datosTipoDocumento['IdEstadoInicial'];

        if (!parent::Insertar($datos, $codigoinsertado)) {
            return false;
        }
        $datos['IdDocumento'] = $codigoinsertado;

        $datos["GuardaRevista"] = true;

        $oDocumentosPuestos = new cDocumentosPuestos($this->conexion, $this->formato);
        if (!$oDocumentosPuestos->ActualizarDatos($datos, $codigosInsertados))
            return false;

        if (!FuncionesPHPLocal::isEmpty($datosTipoDocumento['ClassEjecutar']) && class_exists($datosTipoDocumento['ClassEjecutar'])) {
            /** @var iTiposDocumentosIns $object */
            $object = new $datosTipoDocumento['ClassEjecutar']($this->conexion, $this->formato, $this->conexionES);
            if (method_exists($object, 'insertar')) {
                if (!$object->insertar($datos)) {
                    if (FMT_ARRAY == $this->formato)
                        $this->setError($object->getError());
                    return false;
                }
            }
        }

        if (isset($datos['IdDocumentoRelacionado']) && count($datos['IdDocumentoRelacionado']) > 0) {
            $oDocumentosRelacionados = new cDocumentosRelacionados($this->conexion, $this->formato);
            foreach ($datos['IdDocumentoRelacionado'] as $IdDocumentoRelacionado) {
                $datos['IdDocumentoRelacionado'] = $IdDocumentoRelacionado;
                if (!$oDocumentosRelacionados->Insertar($datos)) {
                    return false;
                }
            }
        }

        if (!$this->ModificarHashDato($datos))
            return false;

        if (isset($datosTipoDocumento['ClassEjecutar']) && class_exists($datosTipoDocumento['ClassEjecutar'])) {
            $class = new $datosTipoDocumento['ClassEjecutar']($this->conexion, $this->formato);
            if (method_exists($class, "Insertar")) {
                if (!$class->Insertar($datos)) {
                    return false;
                }
            }
        }

        $oDocumentosArchivos = new cDocumentosArchivos($this->conexion, $this->formato);
        if (!$oDocumentosArchivos->ActualizarArchivos($datos, $datos, $arrayInsertados)) {
            return false;
        }

        $oAuditoriasDocumentos = new cAuditoriasDocumentos($this->conexion, $this->formato);
        $datos['IdDocumento'] = $codigoinsertado;
        $datos['Accion'] = INSERTAR;
        $datos['AltaEscuela'] = $_SESSION['IdEscuela'] ?? 'NULL';
        $datos['AltaRol'] = implode(',', $_SESSION['rolcod']);

        if (!$oAuditoriasDocumentos->InsertarLog($datos, $codigoInsertadolog)) {
            return false;
        }

        if (!$this->_armarDatosElastic($datos, $datosRegistroElastic, $datosElastic)) {
            return false;
        }


        $oElastic = new Elastic\Modificacion(SUFFIX_NOVEDADES, $this->conexionES);
        if (!$oElastic->Insertar($datosElastic)) {
            $this->setError($oElastic->getError());
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $this->getError()['error_description'], ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }


        $datos['Id'] = $codigoInsertadolog;
        $datos['AccionCambio'] = "Insertar";

        $datos['IdFilaLog'] = $codigoInsertadolog;

        $datos['Estado'] = [];

        $datos['Estado']['Inicial']['Id'] = $datosTipoDocumento['IdEstadoInicial'];
        $datos['Estado']['Inicial']['Nombre'] = $datosTipoDocumento['EstadoInicial'];
        $datos['Estado']['Final']['Id'] = $datosTipoDocumento['IdEstadoInicial'];
        $datos['Estado']['Final']['Nombre'] = $datosTipoDocumento['EstadoInicial'];
        $datos['Area']['Inicial']['Id'] = $datosTipoDocumento['IdAreaInicial'];
        $datos['Area']['Inicial']['Nombre'] = $datosTipoDocumento['AreaInicial'];
        $datos['Area']['Final']['Id'] = $datosTipoDocumento['IdAreaInicial'];
        $datos['Area']['Final']['Nombre'] = $datosTipoDocumento['AreaInicial'];
        if (!$this->_armarObjetoHistoricos($datos, $datosHistoricos)) {
            return false;
        }

        $oElastic = new Elastic\Modificacion(SUFFIX_NOVEDADES_HISTORICOS, $this->conexionES);
        if (!$oElastic->Insertar($datosHistoricos)) {
            $this->setError($oElastic->getError());
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $this->getError()['error_description'], ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }



    public function Modificar($datos) {

        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un tipo de documento.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['IdDocumento']) || $datos['IdDocumento'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un Id de documento.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        // Para solucionar un bug en extension de suplencia agrego este bloque porque perdia el idlicencia al guardar
        // solo cuando se agregaba la caja "extension_suplencia" con los datos de la licencia en la configuracion de la novedad
        if (!isset($datos['IdLicencia']) || $datos['IdLicencia'] == "")
            if (isset($datosRegistro['IdLicencia']) && $datosRegistro['IdLicencia'] != "")
                $datos['IdLicencia'] = $datosRegistro['IdLicencia'];


        $oObjeto = new cDocumentosPermisos($this->conexion, $this->formato);
        $datosBusqueda = [];
        $datosBusqueda['IdDocumento'] = $datos['IdDocumento'];
        $datosBusqueda['IdTipoDocumento'] = $datosRegistro['IdTipoDocumento'];
        $datosBusqueda['IdArea'] = $_SESSION['IdArea'];
        if (!$oObjeto->PuedeModificarDocumento($datosBusqueda, $resultado, $numfilas))
            return false;

        if ($numfilas == 0) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, no tiene permisos para modificar el documento.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosTipoDocumento = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!$oObjeto->PuedeModificarCargos($datosBusqueda, $resultadoCargos, $numfilasCargos))
            return false;

        $modificaCargos = false;

        if ($numfilasCargos === 1)
            $modificaCargos = true;

        $datos['IdPuesto'] = empty($datos['IdPuesto']) ? $datosRegistro['IdPuesto'] : $datos['IdPuesto'];

        $datos['ModificaCargos'] = $modificaCargos;
        $datos['IdDocumentoPadre'] = $datosRegistro['IdDocumentoPadre'];
        $datos['IdRegistroTipoDocumento'] = $datosTipoDocumento['IdRegistro'];
        $datos['IdTipoDocumento'] = $datosTipoDocumento['IdTipoDocumento'];
        $datos['NombreTipoDocumento'] = $datosTipoDocumento['Nombre'];
        $datos['IdTipoDocumentoPadre'] = $datosTipoDocumento['IdTipoDocumentoPadre'];

        if (!$this->_ValidarModificar($datos))
            return false;

        if (class_exists($datosTipoDocumento['ClassEjecutar'])) {
            $class = new $datosTipoDocumento['ClassEjecutar']($this->conexion, $this->formato);
            if (method_exists($class, "ValidarModificar")) {
                $Tipo = 1;
                if (!$class->ValidarModificar($datos, $datosRegistro))
                    return false;
            }

        }

        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionEscuela'] = $_SESSION['IdEscuelaSeleccionada'] ?: 'NULL';
        $datos['UltimaModificacionRol'] = implode(',', $_SESSION['rolcod']);

        $dmy = DateTime::createFromFormat('d/m/Y', $datos['FechaDesde']);
        if ($dmy && $dmy->format('d/m/Y') === $datos['FechaDesde']) {
            $datos['FechaDesde'] = $dmy->format('Y-m-d');
        }
        $datosJson = FuncionesPHPLocal::ConvertiraUtf8($datos);

        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;

        if ($modificaCargos === true) {
            $oDocumentosPuestos = new cDocumentosPuestos($this->conexion, $this->formato);
            if (!$oDocumentosPuestos->ActualizarDatos($datos, $codigosInsertados))
                return false;
        }
        $oDocumentosRelacionados = new cDocumentosRelacionados($this->conexion, $this->formato);

        if (!$oDocumentosRelacionados->EliminarxIdDocumento($datos))
            return false;

        if (isset($datos['IdDocumentoRelacionado']) && count($datos['IdDocumentoRelacionado']) > 0) {

            foreach ($datos['IdDocumentoRelacionado'] as $IdDocumentoRelacionado) {
                $datos['IdDocumentoRelacionado'] = $IdDocumentoRelacionado;
                if (!$oDocumentosRelacionados->Insertar($datos))
                    return false;
            }
        }

        if (!$this->ModificarHashDato($datos))
            return false;


        if (class_exists($datosTipoDocumento['ClassEjecutar'])) {
            $class = new $datosTipoDocumento['ClassEjecutar']($this->conexion, $this->formato);
            if (method_exists($class, "Modificar")) {
                if (!$class->Modificar($datos))
                    return false;

            }
        }

        $oDocumentosArchivos = new cDocumentosArchivos($this->conexion, $this->formato);
        if (!$oDocumentosArchivos->ActualizarArchivos($datos, $datosRegistro, $arrayInsertados))
            return false;


        $oAuditoriasDocumentos = new cAuditoriasDocumentos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasDocumentos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;



        if (!$this->_armarDatosElastic($datos, $datosRegistroElastic, $datosElastic))
            return false;


        $oElastic = new Elastic\Modificacion(SUFFIX_NOVEDADES, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $this->getError()['error_description'], ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    public function ModificarPuestoxIdDocumento($datos){

        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == '') {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error, debe ingresar un tipo de documento.', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }

        if (!isset($datos['IdDocumento']) || $datos['IdDocumento'] == '') {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error, debe ingresar un Id de documento.', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error debe ingresar un código valido.', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        if (!isset($datos['IdLicencia']) || $datos['IdLicencia'] == '')
            if (isset($datosRegistro['IdLicencia']) && $datosRegistro['IdLicencia'] != '')
                $datos['IdLicencia'] = $datosRegistro['IdLicencia'];

        $oObjeto = new cDocumentosPermisos($this->conexion, $this->formato);
        $datosBusqueda = [];
        $datosBusqueda['IdDocumento'] = $datos['IdDocumento'];
        $datosBusqueda['IdTipoDocumento'] = $datosRegistro['IdTipoDocumento'];
        $datosBusqueda['IdArea'] = $_SESSION['IdArea'];

        if (!$oObjeto->PuedeModificarDocumento($datosBusqueda, $resultado, $numfilas))
            return false;

        if ($numfilas == 0) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error, no tiene permisos para modificar el documento.', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }
        $datosTipoDocumento = $this->conexion->ObtenerSiguienteRegistro($resultado);


        $datos['IdDocumentoPadre'] = $datosRegistro['IdDocumentoPadre'];
        $datos['IdRegistroTipoDocumento'] = $datosTipoDocumento['IdRegistro'];
        $datos['IdTipoDocumento'] = $datosTipoDocumento['IdTipoDocumento'];
        $datos['NombreTipoDocumento'] = $datosTipoDocumento['Nombre'];
        $datos['IdTipoDocumentoPadre'] = $datosTipoDocumento['IdTipoDocumentoPadre'];

        if (!$oObjeto->PuedeModificarCargos($datosBusqueda, $resultadoCargos, $numfilasCargos))
            return false;

        $modificaCargos = false;

        if ($numfilasCargos === 1)
            $modificaCargos = true;

        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        $datos['UltimaModificacionEscuela'] = $_SESSION['IdEscuelaSeleccionada'] ?: 'NULL';
        $datos['UltimaModificacionRol'] = implode(',', $_SESSION['rolcod']);
        $datosJson = FuncionesPHPLocal::ConvertiraUtf8($datos);
        $datos['PuestosSeleccionados'][] = $datos;

        $this->_SetearNull($datos);

        if (!parent::Modificar($datos))
            return false;

        if($modificaCargos){
          $oDocumentosPuestos = new cDocumentosPuestos($this->conexion, $this->formato);
          if (!$oDocumentosPuestos->ActualizarDatosPlazaDestino($datos, $codigosInsertados))
              return false;
        }


        if (!$this->ModificarHashDato($datos))
            return false;


        $oAuditoriasDocumentos = new cAuditoriasDocumentos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasDocumentos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;


        if (!$this->_armarDatosElastic($datos, $datosRegistroElastic, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_NOVEDADES, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $this->getError()['error_description'], ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }

        return true;
    }

    public function Eliminar($datos) {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        /*$oArchivos = new cDocumentosArchivos($this->conexion,$this->formato);
        if (!$oArchivos->BuscarxIdDocumento($datos,$resultado_archivos,$numfilas_archivos))
            return false;

        if($numfilas_archivos>0)
        {
            while($filaArchivo = $this->conexion->ObtenerSiguienteRegistro($resultado_archivos))
            {
                if(!$oArchivos->EliminarxIdDocumentoArchivo($filaArchivo))
                    return false;
            }
        }*/

        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionEscuela'] = $_SESSION['IdEscuela'] ?: 'NULL';
        $datos['UltimaModificacionRol'] = implode(",", $_SESSION['rolcod']);
        $datos['IdEstado'] = 99; //ELIMINADO


        if (!parent::ModificarEstado($datos))
            return false;

        $oAuditoriasDocumentos = new cAuditoriasDocumentos($this->conexion, $this->formato);
        $datos['Accion'] = ELIMINAR;
        if (!$oAuditoriasDocumentos->InsertarLog($datos, $codigoInsertadolog))
            return false;


        if (!$this->_armarDatosElastic($datos, $datosRegistroElastic, $datosElastic))
            return false;


        $oElastic = new Elastic\Modificacion(SUFFIX_NOVEDADES, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }
        $datos['Id'] = $codigoInsertadolog;
        $datos['AccionCambio'] = "Eliminar";
        $datos['IdFilaLog'] = $datos['Id'];
        $datos['Estado']['Inicial']['Id'] = $datosRegistro['IdEstado'];
        $datos['Estado']['Inicial']['Nombre'] = $datosRegistro['NombreEstado'];
        $datos['Estado']['Final']['Id'] = $datosRegistro['IdEstado'];
        $datos['Estado']['Final']['Nombre'] = $datosRegistro['NombreEstado'];
        $datos['Area']['Inicial']['Id'] = $datosRegistro['IdArea'];
        $datos['Area']['Inicial']['Nombre'] = $datosRegistro['NombreArea'];
        $datos['Area']['Final']['Id'] = $datosRegistro['IdArea'];
        $datos['Area']['Final']['Nombre'] = $datosRegistro['NombreArea'];

        if (!$this->_armarObjetoHistoricos($datos, $datosHistoricos))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_NOVEDADES_HISTORICOS, $this->conexionES);
        if (!$oElastic->Insertar((array)$datosHistoricos)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }

    public function ModificarEstado($datos) {
        $datos['UltimaModificacionEscuela'] = $_SESSION['IdEscuela'] ?: 'NULL';
        $datos['UltimaModificacionRol'] = implode($_SESSION['rolcod'], ",");
        if (!parent::ModificarEstado($datos))
            return false;
        return true;
    }

    public function ModificarEstadoArea($datos) {

        $oElastic = new Elastic\Modificacion(SUFFIX_NOVEDADES, $this->conexionES);
        //		echo 'a';
        if (!$this->_ValidarModificarEstadoArea($datos))
            return false;
        //		echo 'b';

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        //		echo 'c';
        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        //		echo 'd';


        $oCircuitosWorkflow = new cCircuitosWorkflow($this->conexion, $this->formato);
        if (!$oCircuitosWorkflow->BuscarAccionesParticularesxIdWorkflow($datos, $resultado, $numfilas))
            return false;
        //		echo 'e';
        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe seleccionar un movimiento valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        //		echo 'f';
        $datosMovimiento = $this->conexion->ObtenerSiguienteRegistro($resultado);
        $impactaPofa = false;
        $desasignarSC = false;
        if ($datosMovimiento['NoValidaDatos'] == 0) {
            if (file_exists(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA . "documentos_tipos/documento_tipo_" . $datosRegistro['IdTipoDocumento'] . ".php"))
                include(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA . "documentos_tipos/documento_tipo_" . $datosRegistro['IdTipoDocumento'] . ".php");
            else {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Ha ocurrido un error al buscar el tipo de documento", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }

            $oValidaciones = new cValidacionesMovimiento($this->conexion, $this, $this->formato);
            if (!$oValidaciones->ValidarCajasDatos($ArrayValidacion, $datosRegistro))
                return false;


            $oDocumentosPermisos = new cDocumentosPermisos($this->conexion, $this->formato);
            if (!$oDocumentosPermisos->BuscarAccionesxIdWorkflowxRol($datos, $resultadoPermisos, $numfilas))
                return false;

            $arrayPermisos = [];
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoPermisos))
                $arrayPermisos[$fila['IdAccion']] = $fila['IdAccion'];

            $TieneObligatorioDocumento = false;
            if (isset($arrayPermisos['000010']))
                $TieneObligatorioDocumento = true;

            $impactaPofa = isset($arrayPermisos['000015']);

            $desasignarSC = isset($arrayPermisos['000017']);
            $reImpactaPofa = isset($arrayPermisos['000033']);

            $liquidarNovedad = isset($arrayPermisos['000030']);
            $QuitarliquidarNovedad = isset($arrayPermisos['000031']);
            $anularMovimiento = isset($arrayPermisos['000034']);



            if ($TieneObligatorioDocumento) {
                $oDocumentosArchivos = new cDocumentosArchivos ($this->conexion, $this->formato);
                if (!$oDocumentosArchivos->BuscarxIdDocumento($datosRegistro, $resultadoArchivos, $numfilasArchivos))
                    return false;

                if ($numfilasArchivos == 0) {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, Debe adjuntar una documentaci&oacute;n respaldatoria.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                    return false;
                }


            }

        }

        $modificarCircuito = false;

        if (isset($datosRegistro['ClassEjecutar']) && $datosRegistro['ClassEjecutar'] != "") {

            if (class_exists($datosRegistro['ClassEjecutar'])) {

                /** @var iTiposDocumentosPOFA|iTiposDocumentos $class */
                $class = new $datosRegistro['ClassEjecutar']($this->conexion, FMT_ARRAY, $this->conexionES);

                if (method_exists($class, "ModificarCircuito")) {
                    if (!$class->ModificarCircuito($datos, $datosRegistro, $filaWorkflow, $modificarCircuito))
                        return false;
                    //					echo 'm';

                    if ($modificarCircuito) {
                        $IdEstadoFinal = $filaWorkflow['IdEstadoFinal'];
                        $datos['IdArea'] = $filaWorkflow['IdAreaFinal'];
                    }

                }

                if ($impactaPofa && method_exists($class, 'impactaPofa')) {

                    $busquedaLicencias['IdDocumento'] = $datos['IdDocumento'];
                    $oDocumentosLicencias = new cDocumentosLicencias($this->conexion);
                    if (!$oDocumentosLicencias->BuscarxDocumento($busquedaLicencias, $resultadoLicencias, $numfilasLicencias)) {
                        return false;
                    }

                    $Licencias = [];

                    if ($numfilasLicencias > 0) {
                        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoLicencias)) {
                            $Licencias[] = $fila['IdLicencia'];
                        }
                    }


                    if (empty($datosRegistro['IdPuesto'])) {
                        $oDocumentosPuestos = new cDocumentosPuestos($this->conexion, $this->formato);
                        if (!$oDocumentosPuestos->BuscarxIdDocumento($datosRegistro, $resultadoPuestos, $numfilasPuestos)) {
                            if ($this->formato == FMT_ARRAY)
                                $this->setError($oDocumentosPuestos->getError());
                            return false;
                        }

                        if ($numfilasPuestos > 0) {
                            $cantidadNoAdmiteSuplente = 0;
                            while ($filaPuestos = $this->conexion->ObtenerSiguienteRegistro($resultadoPuestos)) {
                                $filaPuestos['IdPersona'] = $datosRegistro['IdPersona'];
                                $filaPuestos['FechaTomaPosesion'] = $datosRegistro['FechaTomaPosesion'] ?:
                                    ($datosRegistro['PeriodoFechaDesde'] ?: date('Y-m-d H:i:s'));

                                if (!empty($Licencias)) {
                                    $filaPuestos['IdLicencias'] = $Licencias;
                                }

                                $filaPuestos['IdSolicitudCobertura'] = $datosRegistro['IdSolicitudCobertura'] ?? '';

                                if (!$class->impactaPofa($datos, $filaPuestos)) {
                                    echo $class->getError()['error_description'];
                                    if (409 == $class->getError()['error'])
                                        http_response_code(409);
                                    return false;
                                }
                                $tmp = $class->getError('error_description');
                                if (!is_null($tmp))
                                    ++$cantidadNoAdmiteSuplente;
                                //								echo 'r';
                            }
                            if (method_exists($class, 'notificar')) {
                                if (!$class->notificar())
                                    die;

                            }
                            if ($cantidadNoAdmiteSuplente == $numfilasPuestos) {
                                $this->setError(400, 'Ninguno de los puestos asignados al docente admiten suplente');
                                return false;
                            }
                        } else {
                            $this->setError(400, 'El agente correspondiente al puesto padre se encuentra activo');
                            return false;
                        }
                    } elseif (!$class->impactaPofa($datos, $datosRegistro)) {
                        echo $class->getError()['error_description'];
                        if (409 == $class->getError()['error'])
                            http_response_code(409);
                        return false;
                    }
                    //					echo 'r';
                }
                #si tengo la accion de DesasignarSC y exsten el metodo en la clase del tipo documental
                if ($desasignarSC && method_exists($class, 'desasignarSC')) {

                    if (!$class->desasignarSC($datos, $datosRegistro)) {
                        return false;
                    }
                }


                #si tengo la accion de sacar anular movimiento y exsten el metodo en la clase
                if ($anularMovimiento && method_exists($class, 'anularMovimientoDocumento')) {

                    if (!$class->anularMovimientoDocumento($datos, $datosRegistro)) {
                        return false;
                    }
                }


                #si tengo la accion de reImpactaPofa y exsten el metodo en la clase
                if ($reImpactaPofa && method_exists($class, 'reImpactaPofa')) {

                    if (empty($datosRegistro['IdPuesto'])) {
                        $oDocumentosPuestos = new cDocumentosPuestos($this->conexion, $this->formato);
                        if (!$oDocumentosPuestos->BuscarxIdDocumento($datosRegistro, $resultadoPuestos, $numfilasPuestos)) {
                            if ($this->formato == FMT_ARRAY)
                                $this->setError($oDocumentosPuestos->getError());
                            return false;
                        }

                        if ($numfilasPuestos > 0) {
                            while ($filaPuestos = $this->conexion->ObtenerSiguienteRegistro($resultadoPuestos)) {
                                $filaPuestos['IdPersona'] = $datosRegistro['IdPersona'];
                                $filaPuestos['FechaTomaPosesion'] = $datosRegistro['FechaTomaPosesion'] ?:
                                    ($datosRegistro['PeriodoFechaDesde'] ?: date('Y-m-d H:i:s'));


                                if (!$class->reImpactaPofa($datos, $filaPuestos)) {
                                    echo $class->getError()['error_description'];
                                    if (409 == $class->getError()['error'])
                                        http_response_code(409);
                                    return false;
                                }

                            }

                        } else {
                            $this->setError(400, 'El agente correspondiente al puesto padre se encuentra activo');
                            return false;
                        }
                    } elseif (!$class->reImpactaPofa($datos, $datosRegistro)) {
                        echo $class->getError()['error_description'];
                        if (409 == $class->getError()['error'])
                            http_response_code(409);
                        return false;
                    }
                }

                #si tengo la accion de Liquidar
                if ($liquidarNovedad) {

                    if (isset($datosRegistro['ClassEjecutar']) && $datosRegistro['ClassEjecutar'] != "" && class_exists($datosRegistro['ClassEjecutar'])) {
                        $class = new $datosRegistro['ClassEjecutar']($this->conexion, $this->formato);

                        if (method_exists($class, "insertarMovimientoLiquidacion")) {
                            if (!$class->insertarMovimientoLiquidacion($datos, $datosRegistro))
                                return false;
                        } else {
                            $oMovimientos = new Movimientos($this->conexion, "");
                            if (!$oMovimientos->insertarMovimientoNovedad($datos, $datosRegistro)) {
                                $errorArray = $oMovimientos->getError();
                                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $errorArray["error_description"], ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                                return false;
                            }
                        }
                    } else {

                        $oMovimientos = new Movimientos($this->conexion, "");
                        if (!$oMovimientos->insertarMovimientoNovedad($datos, $datosRegistro)) {
                            $errorArray = $oMovimientos->getError();
                            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $errorArray["error_description"], ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                            return false;
                        }
                    }
                }

                #si tengo la accion QUITAR DE LQUIDACIONde Liquidar
                if ($QuitarliquidarNovedad) {
                    $oMovimientos = new Movimientos($this->conexion, "");
                    $datos["EstadoAnulado"] = 9;//anulado ya liquidado
                    if (!$oMovimientos->insertarMovimientoNovedad($datos, $datosRegistro)) {
                        $errorArray = $oMovimientos->getError();
                        FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $errorArray["error_description"], ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                        return false;
                    }
                }

            }
        }


        //		echo 's';
        if (!$modificarCircuito) {
            $oObjetoPermisos = new cDocumentosPermisos($this->conexion);
            //print_r($datos);
            if (!$oObjetoPermisos->BuscarAreasEnvioxIdWorkflowIdDocumentoxRol($datos, $resultado, $numfilas))
                return false;
            //			echo 't';
            if ($numfilas == 0) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, no tiene permisos para realizar dicha accion.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }
            //			echo 'u';


            if ($numfilas == 1) {
                $fila = $filaWorkflow = $this->conexion->ObtenerSiguienteRegistro($resultado);
                $IdEstadoFinal = $fila['IdEstadoFinal'];
                if ($fila['NodoGeneral'] == 1 && $fila['AreaOrigen'] == 0) {
                    // validar solo id area
                } else {
                    if ($fila['IdAreaFinal'] != $datos['IdArea']) {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe seleccionar un area valida.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                        return false;
                    }
                    //					echo 'v';
                }
            } else {
                $arrayAreas = [];
                $arrayDataWorkflow = [];
                while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                    $arrayDataWorkflow[$fila['IdAreaFinal']] = $fila;
                    $arrayAreas[$fila['IdAreaFinal']] = $fila['IdAreaFinal'];
                    $IdEstadoFinal = $fila['IdEstadoFinal'];
                }
                if (!array_key_exists($datos['IdArea'], $arrayAreas)) {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe seleccionar un area valida.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                    return false;
                }
                //				echo 'w';
                $filaWorkflow = $arrayDataWorkflow[$datos['IdArea']];
            }

        }


        //		echo 'x';
        //if(!$this->ValidarObligatoriosNodoDocumentoArea($datos,"AccionMetodoPrevio"))
        //return false;
        if ($datosMovimiento['NoValidaDatos'] == 0 && isset($datosRegistro['ClassEjecutar']) && $datosRegistro['ClassEjecutar'] != "") {
            if (class_exists($datosRegistro['ClassEjecutar'])) {
                /** @var iTiposDocumentosPOFA|iTiposDocumentos $class */
                $class = new $datosRegistro['ClassEjecutar']($this->conexion, $this->formato);

                if (method_exists($class, "Validar")) {
                    if (!$class->Validar($datos, $datosRegistro, $filaWorkflow))
                        return false;
                }
                //				echo 'y';
            }
        }

        $AltaEscuela = $datosRegistro['IdEscuela'] ?? 'NULL';

        $datosModificar['IdEstado'] = $IdEstadoFinal;
        $datosModificar['IdArea'] = $datos['IdArea'];
        $datosModificar['IdDocumento'] = $datos['IdDocumento'];
        $datosModificar['UltimaModificacionFecha'] = $datosModificar['MovimientoFecha'] = date("Y-m-d H:i:s");
        $datosModificar['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datosModificar['UltimaModificacionEscuela'] = $AltaEscuela;
        $datosModificar['UltimaModificacionRol'] = implode(",", $_SESSION['rolcod']);
        //$this->setError(400, 'debug');return false;
        if (!parent::ModificarEstadoArea($datosModificar))
            return false;
        //		echo 'z';

        $oAuditoriasDocumentos = new cAuditoriasDocumentos($this->conexion, $this->formato);
        $datosModificar['Accion'] = MODIFICACIONAREA;
        if (!$oAuditoriasDocumentos->InsertarLog($datosModificar, $codigoInsertadolog))
            return false;

        //		echo '-';
        //if(!$this->ValidarObligatoriosNodoDocumentoArea($datos,"ValidarAccionMetodoPosterior"))
        //return false;

        if (isset($filaWorkflow['Clase']) && $filaWorkflow['Clase'] != "") {
            if (class_exists($filaWorkflow['Clase'])) {
                $class = new $filaWorkflow['Clase']($this->conexion, $this->formato);

                if (method_exists($class, $filaWorkflow['Metodo'])) {
                    $metodo = $filaWorkflow['Metodo'];
                    if (!$class->$metodo($oElastic, $datos, $filaWorkflow))
                        return false;
                    //					echo '+';
                    $generoParticular = true;
                }
            }
        }
        //		echo '*';
        // Modifico el estado de la observacion;
        $oDocumentosObservaciones = new cDocumentosObservaciones($this->conexion, $this->formato);
        $datosObservacion['IdDocumento'] = $datos['IdDocumento'];
        $datosObservacion['IdEstado'] = DOCOBSERVACIONNUEVO;
        $datosObservacion['limit'] = "Limit 0,1";
        $datosObservacion['Orderby'] = "AltaFecha DESC";
        if (!$oDocumentosObservaciones->BusquedaAvanzada($datosObservacion, $resultadoObservacion, $numfilasObservacion))
            return false;
        //		echo '?';
        if ($numfilasObservacion == "1") {
            $filaObservacion = $this->conexion->ObtenerSiguienteRegistro($resultadoObservacion);
            $filaObservacion['IdEstado'] = DOCOBSERVACIONAPROBADO;
            if (!$oDocumentosObservaciones->ModificarIdEstado($filaObservacion))
                return false;
            //			echo '/';

        }


        if (!$this->_armarDatosElastic($datos, $datosRegistroElastic, $datosElastic))
            return false;
        //		echo '1';

        $oElastic = new Elastic\Modificacion(SUFFIX_NOVEDADES, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }
        //		echo '2';
        $datos['Id'] = $codigoInsertadolog;
        $datos['AccionCambio'] = MODIFICACION;
        $datos['IdFilaLog'] = $datos['Id'];
        $datos['Estado']['Inicial']['Id'] = $datosRegistro['IdEstado'];
        $datos['Estado']['Inicial']['Nombre'] = $datosRegistro['NombreEstado'];
        $datos['Estado']['Final']['Id'] = $filaWorkflow['IdEstadoFinal'];
        $datos['Estado']['Final']['Nombre'] = $filaWorkflow['NombreEstadoFinal'];
        $datos['Area']['Inicial']['Id'] = $datosRegistro['IdArea'];
        $datos['Area']['Inicial']['Nombre'] = $datosRegistro['NombreArea'];
        $datos['Area']['Final']['Id'] = $filaWorkflow['IdAreaFinal'];
        $datos['Area']['Final']['Nombre'] = $filaWorkflow['AreaNombre'];

        if (!$this->_armarObjetoHistoricos($datos, $datosHistoricos))
            return false;
        //		echo '3';
        $oElastic = new Elastic\Modificacion(SUFFIX_NOVEDADES_HISTORICOS, $this->conexionES);
        if (!$oElastic->Insertar($datosHistoricos)) {
            $this->setError($oElastic->getError());
            return false;
        }
        //		echo '4';

//        die;


        return true;
    }

    public function ActualizarEstadoArea($datos) {
        if (!$this->_ValidarModificarEstadoArea($datos))
            return false;

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        $oCircuitosWorkflow = new cCircuitosWorkflow($this->conexion, $this->formato);
        if (!$oCircuitosWorkflow->BuscarAccionesParticularesxIdWorkflow($datos, $resultado, $numfilas))
            return false;
        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe seleccionar un movimiento valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosMovimiento = $this->conexion->ObtenerSiguienteRegistro($resultado);
        /*if ($datosMovimiento['NoValidaDatos']==0)
        {
            if(file_exists(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA."documentos_tipos/documento_tipo_suna_".$datosRegistro['IdTipoDocumento'].".php"))
                include(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA."documentos_tipos/documento_tipo_suna_".$datosRegistro['IdTipoDocumento'].".php");
            else
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Ha ocurrido un error al buscar el tipo de documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
            $oValidaciones = new cValidacionesMovimiento($this->conexion,$this,$this->formato);
            if(!$oValidaciones->ValidarCajasDatos($ArrayValidacion,$datosRegistro))
                return false;
        }*/

        $modificarCircuito = false;

        if (isset($datosRegistro['ClassEjecutar']) && $datosRegistro['ClassEjecutar'] != "") {
            if (class_exists($datosRegistro['ClassEjecutar'])) {
                $class = new $datosRegistro['ClassEjecutar']($this->conexion, $this->formato);

                if (method_exists($class, "ModificarCircuito")) {
                    if (!$class->ModificarCircuito($datos, $datosRegistro, $filaWorkflow, $modificarCircuito))
                        return false;

                    if ($modificarCircuito) {
                        $IdEstadoFinal = $filaWorkflow['IdEstadoFinal'];
                        $datos['IdArea'] = $filaWorkflow['IdAreaFinal'];
                    }

                }
            }
        }

        /*if (!$modificarCircuito)
        {
            $oObjetoPermisos = new cDocumentosPermisos($this->conexion);
            //print_r($datos);
            if(!$oObjetoPermisos->BuscarAreasEnvioxIdWorkflowIdDocumentoxRol($datos,$resultado,$numfilas))
                return false;

            if ($numfilas==0)
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no tiene permisos para realizar dicha accion.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }


            if ($numfilas==1)
            {
                $fila = $filaWorkflow = $this->conexion->ObtenerSiguienteRegistro($resultado);
                $IdEstadoFinal=$fila['IdEstadoFinal'];
                if ($fila['NodoGeneral']==1 && $fila['AreaOrigen']==0)
                {
                    // validar solo id area
                }else
                {
                    if ($fila['IdAreaFinal']!=$datos['IdArea'])
                    {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe seleccionar un area valida.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                        return false;
                    }
                }
            }else
            {
                $arrayAreas = array();
                $arrayDataWorkflow = array();
                while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
                {
                    $arrayDataWorkflow[$fila['IdAreaFinal']] = $fila;
                    $arrayAreas[$fila['IdAreaFinal']] = $fila['IdAreaFinal'];
                    $IdEstadoFinal=$fila['IdEstadoFinal'];
                }
                if (!array_key_exists($datos['IdArea'],$arrayAreas))
                {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe seleccionar un area valida.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;
                }
                $filaWorkflow = $arrayDataWorkflow[$datos['IdArea']];
            }

        }*/

        //if(!$this->ValidarObligatoriosNodoDocumentoArea($datos,"AccionMetodoPrevio"))
        //return false;
        /*
        if ($datosMovimiento['NoValidaDatos']==0 && isset($datosRegistro['ClassEjecutar']) && $datosRegistro['ClassEjecutar']!="")
        {
            if (class_exists($datosRegistro['ClassEjecutar']))
            {
                $class = new $datosRegistro['ClassEjecutar']($this->conexion,$this->formato);

                if(method_exists($class,"Validar"))
                {
                    if(!$class->Validar($datos,$datosRegistro,$filaWorkflow))
                        return false;
                }
            }
        }*/

        $fila = $filaWorkflow = $this->conexion->ObtenerSiguienteRegistro($resultado);

        $datosModificar['IdEstado'] = $datos['IdEstadoFinal'];
        $datosModificar['IdArea'] = $datos['IdArea'];
        $datosModificar['IdDocumento'] = $datos['IdDocumento'];
        $datosModificar['UltimaModificacionFecha'] = $datosModificar['MovimientoFecha'] = date("Y-m-d H:i:s");
        $datosModificar['UltimaModificacionCuil'] = $_SESSION['Cuil'];
        $datosModificar['UltimaModificacionEscalafon'] = $_SESSION['IdEscalafon'];
        $datosModificar['UltimaModificacionClaveEscuela'] = $_SESSION['ClaveEscuela'];

        if (!parent::ActualizarEstadoArea($datosModificar))
            return false;


        $oAuditoriasDocumentos = new cAuditoriasDocumentos($this->conexion, $this->formato);
        $datosModificar['Accion'] = MODIFICACIONAREA;
        if (!$oAuditoriasDocumentos->InsertarLog($datosModificar, $codigoInsertadolog))
            return false;

        //if(!$this->ValidarObligatoriosNodoDocumentoArea($datos,"ValidarAccionMetodoPosterior"))
        //return false;


        $datosEnviar['Estado']['Id'] = $datos['IdEstadoFinal'];
        $datosEnviar['Estado']['Nombre'] = ($filaWorkflow['IdEstadoFinal'] == "") ? [] : (utf8_encode($filaWorkflow['NombreEstadoFinal']));
        $datosEnviar['Area']['Id'] = ($filaWorkflow['IdAreaFinal'] == "") ? [] : $filaWorkflow['IdAreaFinal'];
        $datosEnviar['Area']['Nombre'] = ($filaWorkflow['IdAreaFinal'] == "") ? [] : (utf8_encode($filaWorkflow['AreaNombre']));


        $oElastic = new cModifElastic(INDICESUNA);
        $datosEnviar['UltimaModificacion']['APP'] = APP;
        $datosEnviar['UltimaModificacion']['ClaveEscuela'] = $_SESSION['ClaveEscuela'];
        $datosEnviar['UltimaModificacion']['Escalafon'] = $_SESSION['IdEscalafon'];
        $datosEnviar['UltimaModificacion']['Cuil'] = $_SESSION['Cuil'];
        $datosEnviar['UltimaModificacion']['Fecha'] = date("Y-m-d H:i:s");

        if ($datosEnviar === false)
            return false;

        $datosRegistro['Tipo'] = TIPODOC;

        $dataEnvioAuditoria['MovimientoFecha'] = $datosEnviar['MovimientoFecha'] = $datosModificar['MovimientoFecha'];


        if (isset($filaWorkflow['Clase']) && $filaWorkflow['Clase'] != "") {
            if (class_exists($filaWorkflow['Clase'])) {
                $class = new $filaWorkflow['Clase']($this->conexion, $this->formato);

                if (method_exists($class, $filaWorkflow['Metodo'])) {
                    $metodo = $filaWorkflow['Metodo'];
                    if (!$class->$metodo($oElastic, $datos, $filaWorkflow))
                        return false;

                    $generoParticular = true;
                }
            }
        }

        if (!$oElastic->Actualizar($datosRegistro, $datosEnviar))
            return false;

        // Modifico el estado de la observacion;
        $oDocumentosObservaciones = new cDocumentosObservaciones($this->conexion, $this->formato);
        $datosObservacion['IdDocumento'] = $datos['IdDocumento'];
        $datosObservacion['IdEstado'] = DOCOBSERVACIONNUEVO;
        $datosObservacion['limit'] = "Limit 0,1";
        $datosObservacion['Orderby'] = "AltaFecha DESC";
        if (!$oDocumentosObservaciones->BusquedaAvanzada($datosObservacion, $resultadoObservacion, $numfilasObservacion))
            return false;

        if ($numfilasObservacion == "1") {
            $filaObservacion = $this->conexion->ObtenerSiguienteRegistro($resultadoObservacion);
            $filaObservacion['IdEstado'] = DOCOBSERVACIONAPROBADO;
            if (!$oDocumentosObservaciones->ModificarIdEstado($filaObservacion))
                return false;


        }


        $oAuditoriasElastic = new cModifElastic(INDICEAUDITORIA);
        $datosEnviar['IdDocumento'] = $datosRegistro['IdDocumento'];
        $datosEnviar['AccionCambio'] = "Movimiento";
        //$datosEnviar['IdFilaLog']=$codigoInsertadolog;
        $datosEnviar['Estado']['Inicial']['Id'] = $datosRegistro['IdEstado'];
        $datosEnviar['Estado']['Inicial']['Nombre'] = utf8_encode($datosRegistro['NombreEstado']);
        $datosEnviar['Estado']['Final']['Id'] = $datosEnviar['Estado']['Id'];
        $datosEnviar['Estado']['Final']['Nombre'] = $datosEnviar['Estado']['Nombre'];
        $datosEnviar['Area']['Inicial']['Id'] = $datosRegistro['IdArea'];
        $datosEnviar['Area']['Inicial']['Nombre'] = utf8_encode($datosRegistro['NombreArea']);
        $datosEnviar['Area']['Final']['Id'] = $datosEnviar['Area']['Id'];
        $datosEnviar['Area']['Final']['Nombre'] = $datosEnviar['Area']['Nombre'];
        $datosEnviar['Tipo'] = TIPODOC;
        unset($datosEnviar['Estado']['Id'], $datosEnviar['Estado']['Nombre'], $datosEnviar['Area']['Id'], $datosEnviar['Area']['Nombre']);

        if (!$oAuditoriasElastic->Insertar($datosEnviar))
            return false;

        return true;
    }

    public function ModificarHashDato(&$datos, ?bool $setearNULL = false): bool {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;


        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
            return false;
        }
        $result = self::ordenarDatosVerificacion($datos);
        $data = json_encode($result, JSON_NUMERIC_CHECK);
        if (!openssl_sign($data, $firma, $this->private, self::ALGO)) {
            $this->setError(500, 'Error al generar la firma');
            return false;
        }
        $datos['HashDato'] = hash('sha256', implode("", $result));
        $datos['Firma'] = base64_encode($firma);
        return parent::ModificarHashDato($datos);

    }

    public function verificarHashDatos(array $datos): bool {
        $result = self::ordenarDatosVerificacion($datos);
        $data = json_encode($result, JSON_NUMERIC_CHECK);
        $firma = base64_decode($datos['Firma']);
        switch (openssl_verify($data, $firma, $this->public, self::ALGO)) {
            case 1:
                return true;
            case 0:
                $this->setError(400, 'Error, firma incorrecta');
                return false;
                break;
            default:
                $this->setError(500, 'Error al verificar la firma');
                return false;
        }
    }

    public function DCargaEstadoPublico() {


        if (in_array(ROLEQUIPOCONDUCCION, $_SESSION['rolcod'])) {
            $arrayEstadosPublicosCombo = $arrayEstadosPublicos['EstadosPublicos'];
        } elseif (in_array(ROLSAD, $_SESSION['rolcod'])) {
            $arrayEstadosPublicosCombo = $arrayEstadosPublicos['EstadosPublicosSad'];
        } elseif (in_array(ROLCONSEJO, $_SESSION['rolcod'])) {
            $arrayEstadosPublicosCombo = $arrayEstadosPublicos['EstadosPublicosConsejo'];
        }
        return false;
    }

    public function Activar($datos) {
        $datosmodif['IdDocumento'] = $datos['IdDocumento'];
        $datosmodif['IdEstado'] = ACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        return true;
    }

    public function BuscarReintegrosIniciados($datos, &$resultado, &$numfilas) {
        return parent::BuscarReintegrosIniciados($datos, $resultado, $numfilas);
    }


    //-----------------------------------------------------------------------------------------
    //FUNCIONES PRIVADAS
    //-----------------------------------------------------------------------------------------

    public function DesActivar($datos) {
        $datosmodif['IdDocumento'] = $datos['IdDocumento'];
        $datosmodif['IdEstado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        return true;
    }

    /**
     * @deprecated
     *
     * @param bool  $encode
     * @param array $datos
     *
     * @return array|false|string
     */
    public function ArmarArrayDocumentosFormatoElastic_($datos, &$datosRegistroformatoelastic, &$numfilas) {

        if (!$this->BuscarxCodigoFormatoElastic($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, utf8_decode("Error debe ingresar un código valido."), ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        $oPersonas = new cServiciosPersonas($this->conexion);
        $datosPersona = $oPersonas->ObtenerPersonaxId($datosRegistro);

        if ($datosPersona != false) {
            $datosRegistroformatoelastic['Agente']['Id'] = $datosPersona['IdPersona'];
            $datosRegistroformatoelastic['Agente']['TipoDocumento'] = $datosPersona['IdTipoDocumento'];

            $oTiposDocumentos = new cServiciosTiposDocumentos($this->conexion);
            $datosBuscar['IdTipoDocumento'] = $datosPersona['IdTipoDocumento'];
            $datosTiposDocumentos = $oTiposDocumentos->ObtenerTiposDocumentosxId($datosBuscar);
            $datosRegistroformatoelastic['Agente']['TipoDocumentoNombre'] = $datosTiposDocumentos['Nombre'];
            $datosRegistroformatoelastic['Agente']['Dni'] = $datosPersona['DNI'];
            $datosRegistroformatoelastic['Agente']['Cuil'] = $datosPersona['CUIL'];
            $datosRegistroformatoelastic['Agente']['Sexo'] = $datosPersona['Sexo'];
            $datosRegistroformatoelastic['Agente']['Nombre'] = $datosPersona['Nombre'];
            $datosRegistroformatoelastic['Agente']['Apellido'] = $datosPersona['Apellido'];
            $datosRegistroformatoelastic['Agente']['NombreCompleto'] = $datosPersona['NombreCompleto'];
            $datosRegistroformatoelastic['Agente']['Email'] = $datosPersona['Email'];
            $datosRegistroformatoelastic['Agente']['Telefono'] = $datosPersona['Telefono'];
        }

        $datosRegistroformatoelastic['IdEscuelaDestino'] = $datosRegistro['IdEscuelaDestino'];
        $datosRegistroformatoelastic['IdEscuela'] = $datosRegistro['IdEscuela'];
        $datosRegistroformatoelastic['IdPuesto'] = $datosRegistro['IdPuesto'];
        $datosRegistroformatoelastic['Estado']['Id'] = ($datosRegistro['IdEstado'] == "") ? [] : $datosRegistro['IdEstado'];
        $datosRegistroformatoelastic['Estado']['Nombre'] = ($datosRegistro['IdEstado'] == "") ? [] : ($datosRegistro['NombreEstado']);
        $datosRegistroformatoelastic['Area']['Id'] = ($datosRegistro['IdArea'] == "") ? [] : $datosRegistro['IdArea'];
        $datosRegistroformatoelastic['Area']['Nombre'] = ($datosRegistro['IdArea'] == "") ? [] : ($datosRegistro['NombreArea']);
        $datosRegistroformatoelastic['Alta']['Fecha'] = $datosRegistro['AltaFecha'];
        $datosRegistroformatoelastic['UltimaModificacion']['Fecha'] = $datosRegistro['UltimaModificacionFecha'];

        $datosRegistroformatoelastic['TipoDocumento']['Id'] = $datosRegistro['IdTipoDocumento'];
        $datosRegistroformatoelastic['TipoDocumento']['IdRegistro'] = $datosRegistro['IdRegistroTipoDocumento'];
        $datosRegistroformatoelastic['TipoDocumento']['Nombre'] = ($datosRegistro['NombreTipoDocumento']);
        $datosRegistroformatoelastic['TipoDocumento']['Clasificacion']['Id'] = $datosRegistro['IdClasificacion'];
        $datosRegistroformatoelastic['TipoDocumento']['Clasificacion']['Nombre'] = utf8_encode($datosRegistro['ClasificacionNombre']);
        if (isset($datosRegistro['NombreCorto']) && $datosRegistro['NombreCorto'] != "")
            $datosRegistroformatoelastic['TipoDocumento']['NombreCorto'] = ($datosRegistro['NombreCorto']);
        if (isset($datosRegistro['IdCategoria']) && $datosRegistro['IdCategoria'] != "")
            $datosRegistroformatoelastic['TipoDocumento']['Categoria']['Id'] = $datosRegistro['IdCategoria'];
        if (isset($datosRegistro['CategoriaNombre']) && $datosRegistro['CategoriaNombre'] != "")
            $datosRegistroformatoelastic['TipoDocumento']['Categoria']['Nombre'] = utf8_encode($datosRegistro['CategoriaNombre']);
        $datosRegistroformatoelastic['IdDocumento'] = $datosRegistro['IdDocumento'];
        $datosRegistroformatoelastic['Tipo'] = TIPODOC;
        $datosRegistroformatoelastic['IdDocumentoPadre'] = $datosRegistro['IdDocumentoPadre'];

        if (isset($datosRegistro['FechaEnvio']) && $datosRegistro['FechaEnvio'] != "")
            $datosRegistroformatoelastic['FechaEnvio'] = $datosRegistro['FechaEnvio'];

        //PERIODO
        if (isset($datosRegistro['PeriodoFechaDesde']) && $datosRegistro['PeriodoFechaDesde'] != "" && $datosRegistro['PeriodoFechaDesde'] != "NULL")
            $datosRegistroformatoelastic['Periodo']['FechaDesde'] = $datosRegistro['PeriodoFechaDesde'];

        if (isset($datosRegistro['PeriodoFechaHasta']) && $datosRegistro['PeriodoFechaHasta'] != "" && $datosRegistro['PeriodoFechaHasta'] != "NULL")
            $datosRegistroformatoelastic['Periodo']['FechaHasta'] = $datosRegistro['PeriodoFechaHasta'];

        //OBSERVACIONES
        if (isset($datosRegistro['Observaciones']) && $datosRegistro['Observaciones'] != "" && $datosRegistro['Observaciones'] != "NULL")
            $datosRegistroformatoelastic['Observaciones'] = $datosRegistro['Observaciones'];

        //FECHA TOMA POSESION
        if (isset($datosRegistro['FechaTomaPosesion']) && $datosRegistro['FechaTomaPosesion'] != "" && $datosRegistro['FechaTomaPosesion'] != "NULL")
            $datosRegistroformatoelastic['FechaTomaPosesion'] = $datosRegistro['FechaTomaPosesion'];


        return true;
    }

    public function ArmarArrayDocumentosFormatoElastic($datos, &$datosRegistroformatoelastic, &$numfilas) {

        if (!$this->BuscarxCodigoFormatoElastic($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, utf8_decode("Error debe ingresar un código valido."), ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);


        if ($datosRegistro['IdPersona'] != "" && $datosRegistro['IdTipoDocumentoPersona'] != "") {
            $oTiposDocumentos = new cServiciosTiposDocumentos($this->conexion);
            $datosBuscar['IdTipoDocumento'] = $datosRegistro['IdTipoDocumentoPersona'];
            $datosTiposDocumentos = $oTiposDocumentos->ObtenerTiposDocumentosxId($datosBuscar);
            if (!$datosTiposDocumentos) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, utf8_decode($oTiposDocumentos->getError()['error_description']), ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }

            $datosRegistro['NombreTipoDocumentoPersona'] = $datosTiposDocumentos['Nombre'];

        }

        $oDocumentosPuestos = new cDocumentosPuestos($this->conexion, $this->formato);

        if (!$oDocumentosPuestos->BuscarxIdDocumento($datos, $resultadoCargo, $numfilasCargo))
            return false;


        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoCargo))
            $datosRegistro['Puestos'][] = $fila;


        $datosRegistroformatoelastic['DocumentosRelacionados'] = [];
        $oDocumentosRelacionados = new cDocumentosRelacionados($this->conexion, $this->formato);
        if (!$oDocumentosRelacionados->BuscarxIdDocumento($datos, $resultadoDocumentoRelacionado, $numfilasDocumentoRelacionado))
            return false;

        $i = 0;
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoDocumentoRelacionado)) {
            $datosRegistro['DocumentosRelacionados'][$i]['IdDocumento'] = $fila['IdDocumentoRelacionado'];
            $datosRegistro['DocumentosRelacionados'][$i]['IdTipoDocumento'] = $fila['IdTipoDocumento'];
            $datosRegistro['DocumentosRelacionados'][$i]['NombreTipoDocumento'] = $fila['NombreTipoDocumento'];

            $datosRegistro['DocumentosRelacionados'][$i]['Cuil'] = $fila['AgenteCuil'];
            $datosRegistro['DocumentosRelacionados'][$i]['Nombre'] = $fila['AgenteNombre'];
            $datosRegistro['DocumentosRelacionados'][$i]['Apellido'] = $fila['AgenteApellido'];
            $datosRegistro['DocumentosRelacionados'][$i]['ClaveEscuela'] = $fila['ClaveEscuela'];

            $i++;
        }
        $datosElastic = Elastic\Novedades::armarDatosElastic($datosRegistro, true);
        $datosRegistroformatoelastic = json_decode($datosElastic, true);

        return true;
    }

    private function _armarDatosElastic(array $datos, ?array &$datosRegistro, ?stdClass &$datosElastic): bool {

        if (empty($datosRegistro)) {
            if (!$this->BuscarxCodigoFormatoElastic($datos, $resultado, $numfilas))
                return false;

            if ($numfilas != 1) {
                $this->setError(400, "Debe ingresar código válido");
                return false;
            }

            $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

            $oDocumentosPuestos = new cDocumentosPuestos($this->conexion, $this->formato);


            if (!$oDocumentosPuestos->BuscarxIdDocumento($datos, $resultadoCargo, $numfilasCargo))
                return false;


            $datosPuestos['PuestosSeleccionados'] = [];
             while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoCargo)){
                $datosPuestos['PuestosSeleccionados'][] = $fila;
            }


            if (count($datosPuestos['PuestosSeleccionados']) > 0) {
                foreach ($datosPuestos['PuestosSeleccionados'] as $DataPuesto) {
                    $datosRegistroPuesto['IdPuesto'] = $DataPuesto['IdPuesto'];
                    $datosRegistroPuesto['CodigoPuesto'] = $DataPuesto['CodigoPuesto'];
                    $datosRegistroPuesto['IdEscuela'] = $DataPuesto['IdEscuela'];
                    $datosRegistroPuesto['IdNivelModalidad'] = $DataPuesto['IdNivelModalidad'];
                    $datosRegistroPuesto['IdNivel'] = $DataPuesto['IdNivel'] ?? null;
                    $datosRegistroPuesto['IdEscuelaTurno'] = $DataPuesto['IdEscuelaTurno'];
                    $datosRegistroPuesto['IdSeccion'] = $DataPuesto['IdSeccion'];
                    $datosRegistroPuesto['IdCargo'] = $DataPuesto['IdCargo'];
                    $datosRegistroPuesto['IdGrupo'] = $DataPuesto['IdGrupo'];
                    $datosRegistroPuesto['IdMateria'] = $DataPuesto['IdMateria'];
                    $datosRegistroPuesto['CantHoras'] = $DataPuesto['CantHoras'];
                    $datosRegistroPuesto['CantModulos'] = $DataPuesto['CantModulos'];
                    $datosRegistroPuesto['IdPersona'] = $DataPuesto['IdPersona'];
                    $datosRegistroPuesto['IdRevista'] = $DataPuesto['IdRevista'];
                    $datosRegistroPuesto['CodigoLiquidador'] = $DataPuesto['CodigoLiquidador'];
                    //$datosRegistroPuesto['FechaDesde'] =$DataPuesto['FechaDesde'];
                    $datosRegistroPuesto['FechaHasta'] = $DataPuesto['FechaHasta'];
                    $datosRegistroPuesto['FechaDesignacion'] = $DataPuesto['FechaDesignacion'] ?? null;
                    $datosRegistroPuesto['FechaTomaPosesion'] = $DataPuesto['FechaTomaPosesion'] ?? null;
                    $datosRegistroPuesto['IdEstado'] = $DataPuesto['IdEstado'];
                    $datosRegistroPuesto['NombreEstado'] = $DataPuesto['NombreEstado'];
                    $datosRegistroPuesto['CodigoMateria'] = $DataPuesto['CodigoMateria'];
                    $datosRegistroPuesto['NombreMateria'] = $DataPuesto['NombreMateria'];
                    $datosRegistroPuesto['CodigoCargo'] = $DataPuesto['CodigoCargo'];
                    $datosRegistroPuesto['NombreCargo'] = $DataPuesto['NombreCargo'];
                    $datosRegistroPuesto['Turno'] = $DataPuesto['Turno'];
                    $datosRegistroPuesto['NombreTurno'] = $DataPuesto['NombreTurno'];
                    $datosRegistroPuesto['IdTurno'] = $DataPuesto['IdTurno'] ?? null;
                    $datosRegistroPuesto['Seccion'] = $DataPuesto['Seccion'];
                    $datosRegistroPuesto['Grado'] = $DataPuesto['Grado'];
                    $datosRegistroPuesto['GradoNombreCorto'] = $DataPuesto['GradoNombreCorto'];

                    $datosRegistroPuesto['IdPofa'] = $DataPuesto['IdPofa'];

                    $datosRegistro['Puestos'][] = $datosRegistroPuesto;
                }
            }


        }

        $datosElastic = Elastic\Novedades::armarDatosElastic($datosRegistro);

        return true;
    }

    private function _armarObjetoHistoricos(array $datos, &$datosHistoricos): bool {

        if (!$this->BuscarxCodigoFormatoElastic($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(404, 'Error, no existe el registro');
            return false;
        }

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        $oDocumentosPuestos = new cDocumentosPuestos($this->conexion, $this->formato);
        if (!$oDocumentosPuestos->BuscarxIdDocumento($datos, $resultadoCargo, $numfilasCargo))
            return false;
        $datosRegistro['PuestosSeleccionados'] = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoCargo))
            $datosRegistro['CargosSeleccionados'][] = $fila;

        if (isset($datosRegistro['CargosSeleccionados']) && count($datosRegistro['CargosSeleccionados']) > 0) {
            foreach ($datosRegistro['CargosSeleccionados'] as $DataCargo) {
                $datosRegistroCargo['IdPuesto'] = $DataCargo['IdPuesto'];
                $datosRegistroCargo['CodigoPuesto'] = $DataCargo['CodigoPuesto'];
                $datosRegistroCargo['IdEscuela'] = $DataCargo['IdEscuela'];
                $datosRegistroCargo['IdNivelModalidad'] = $DataCargo['IdNivelModalidad'];
                $datosRegistroCargo['IdEscuelaTurno'] = $DataCargo['IdEscuelaTurno'];
                $datosRegistroCargo['IdSeccion'] = $DataCargo['IdSeccion'];
                $datosRegistroCargo['IdCargo'] = $DataCargo['IdCargo'];
                $datosRegistroCargo['IdGrupo'] = $DataCargo['IdGrupo'];
                $datosRegistroCargo['IdMateria'] = $DataCargo['IdMateria'];
                $datosRegistroCargo['CantHoras'] = $DataCargo['CantHoras'];
                $datosRegistroCargo['CantModulos'] = $DataCargo['CantModulos'];
                $datosRegistroCargo['IdPersona'] = $DataCargo['IdPersona'];
                $datosRegistroCargo['IdRevista'] = $DataCargo['IdRevista'];
                $datosRegistroCargo['CodigoLiquidador'] = $DataCargo['CodigoLiquidador'];
                //$datosRegistroCargo['FechaDesde'] =$DataCargo['FechaDesde'];
                $datosRegistroCargo['FechaHasta'] = $DataCargo['FechaHasta'];
                $datosRegistroCargo['FechaDesignacion'] = $DataCargo['FechaDesignacion'] ?? '';
                $datosRegistroCargo['FechaTomaPosesion'] = $DataCargo['FechaTomaPosesion'] ?? '';
                $datosRegistroCargo['IdEstado'] = $DataCargo['IdEstado'];
                $datosRegistroCargo['NombreEstado'] = $DataCargo['NombreEstado'];
                $datosRegistroCargo['CodigoMateria'] = $DataCargo['CodigoMateria'];
                $datosRegistroCargo['NombreMateria'] = $DataCargo['NombreMateria'];
                $datosRegistroCargo['CodigoCargo'] = $DataCargo['CodigoCargo'];
                $datosRegistroCargo['NombreCargo'] = $DataCargo['NombreCargo'];
                $datosRegistroCargo['Turno'] = $DataCargo['Turno'];
                $datosRegistroCargo['NombreTurno'] = $DataCargo['NombreTurno'];
                $datosRegistroCargo['Seccion'] = $DataCargo['Seccion'];
                $datosRegistroCargo['Grado'] = $DataCargo['Grado'];
                $datosRegistroCargo['GradoNombreCorto'] = $DataCargo['GradoNombreCorto'];


                $datosRegistro['Puestos'][] = $datosRegistroCargo;
            }
        }


        $datosRegistro['Id'] = $datos['Id'];
        $datosRegistro['AccionCambio'] = $datos['AccionCambio'];

        $datosRegistro['IdFilaLog'] = $datos['Id'];
        $datosRegistro['Estado']['Inicial']['Id'] = $datos['Estado']['Inicial']['Id'];
        $datosRegistro['Estado']['Inicial']['Nombre'] = $datos['Estado']['Inicial']['Nombre'];
        $datosRegistro['Estado']['Final']['Id'] = $datos['Estado']['Final']['Id'];
        $datosRegistro['Estado']['Final']['Nombre'] = $datos['Estado']['Final']['Nombre'];
        $datosRegistro['Area']['Inicial']['Id'] = $datos['Area']['Inicial']['Id'];
        $datosRegistro['Area']['Inicial']['Nombre'] = $datos['Area']['Inicial']['Nombre'];
        $datosRegistro['Area']['Final']['Id'] = $datos['Area']['Final']['Id'];
        $datosRegistro['Area']['Final']['Nombre'] = $datos['Area']['Final']['Nombre'];

        $datosHistoricos = Elastic\NovedadesHistoricos::armarDatosElastic($datosRegistro);
        return true;
    }

    private function _ValidarInsertar(&$datos, $NoGeneraDependiente = false) {

        if ($NoGeneraDependiente == false) {
            if (!$this->_ValidarDatosVacios($datos))
                return false;
        } else {
            if (!$this->_ValidarDatosVaciosGenerarDependiente($datos))
                return false;
        }

        return true;
    }

    private function _ValidarDatosVaciosGenerarDependiente(array $_): bool {
        return true;
    }

    private function _ValidarModificar(&$datos) {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        return true;
    }

    private function _ValidarModificarEstadoArea(&$datos) {


        if (!isset($datos['IdDocumento']) || $datos['IdDocumento'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe seleccionar un documento.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['IdWorkflow']) || $datos['IdWorkflow'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe seleccionar una accion.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['IdArea']) || $datos['IdArea'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe seleccionar un area.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }


        return true;
    }

    private function ValidarEstructuraDatos($datosRegistro, &$datos) {
        if (!file_exists(CARPETA_SERVIDOR_TIPOSDOCUMENTOS_FISICA . "documento_" . $datosRegistro['IdRegistroTipoDocumento'] . ".json"))
            return false;

        $jsonData = file_get_contents(CARPETA_SERVIDOR_TIPOSDOCUMENTOS_FISICA . "documento_" . $datosRegistro['IdRegistroTipoDocumento'] . ".json");

        $dataJsonArray = json_decode($jsonData, 1);
        if (isset($dataJsonArray['Campos']) && is_array($dataJsonArray['Campos'])) {
            $this->Campos = $dataJsonArray['Campos'];
            foreach ($dataJsonArray['Campos'] as $fila) {
                if ($fila['NombreCampo'] == "")
                    continue;
                if ($fila['CampoObligatorio'] == 1) {
                    $nombre = ($fila['LabelDefault'] == "" ? $fila['Nombre'] : $fila['LabelDefault']);
                    if (!isset($datos[$fila['NombreCampo']]) || (trim($datos[$fila['NombreCampo']]) == "")) {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe completar el campo (" . $nombre . ")", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                        return false;
                    }

                }
                if ($fila['CantidadMaximaCampo'] != "") {
                    $nombre = ($fila['LabelDefault'] == "" ? $fila['Nombre'] : $fila['LabelDefault']);
                    if (strlen(trim($datos[$fila['NombreCampo']])) > $fila['CantidadMaximaCampo']) {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error cantidad maxima de caracteres del campo (" . $nombre . ") es mayor a la definida.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                        return false;
                    }
                }
                switch ($fila['TipoCampoElastic']) {
                    case "date":
                        if (isset($datos[$fila['NombreCampo']]) && (trim($datos[$fila['NombreCampo']]) != "")) {
                            $nombre = ($fila['LabelDefault'] == "" ? $fila['Nombre'] : $fila['LabelDefault']);
                            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos[$fila['NombreCampo']], "FechaDDMMAAAA")) {
                                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo " . $nombre . " valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                                return false;
                            }

                            $datos[$fila['NombreCampo']] = FuncionesPHPLocal::ConvertirFecha($datos[$fila['NombreCampo']], "dd/mm/aaaa", "aaaa-mm-dd");
                        } else
                            $datos[$fila['NombreCampo']] = [];

                        break;

                    case "short":
                        $nombre = ($fila['LabelDefault'] == "" ? $fila['Nombre'] : $fila['LabelDefault']);
                        if (!FuncionesPHPLocal::validarNumerico($datos[$fila['NombreCampo']], 32767)) {
                            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un valor valido (" . $nombre . ").", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                            return false;
                        }
                        break;
                    case "integer":
                        $nombre = ($fila['LabelDefault'] == "" ? $fila['Nombre'] : $fila['LabelDefault']);
                        if (!FuncionesPHPLocal::validarNumerico($datos[$fila['NombreCampo']], 2147483647)) {
                            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un valor valido (" . $nombre . ").", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                            return false;
                        }
                        break;
                    case "long":
                        $nombre = ($fila['LabelDefault'] == "" ? $fila['Nombre'] : $fila['LabelDefault']);
                        if (!FuncionesPHPLocal::validarNumerico($datos[$fila['NombreCampo']], NULLDATE)) {
                            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un valor valido (" . $nombre . ").", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                            return false;
                        }
                        break;

                    case "boolean":
                        if (isset($datos[$fila['NombreCampo']]) && (trim($datos[$fila['NombreCampo']]) != "")) {
                            $nombre = ($fila['LabelDefault'] == "" ? $fila['Nombre'] : $fila['LabelDefault']);
                            $datos[$fila['NombreCampo']] = "true";

                        } else
                            $datos[$fila['NombreCampo']] = "false";
                        break;

                    case "text":
                        //
                        //break;

                    case "keyword":
                        //
                        //break;

                    default:
                        if (empty($datos[$fila['NombreCampo']]))
                            $datos[$fila['NombreCampo']] = null;

                }
            }
        }


        if (isset($dataJsonArray['Adjuntos']) && count($dataJsonArray['Adjuntos']) > 0) {

            foreach ($dataJsonArray['Adjuntos'] as $fila) {


                if ($fila['CampoObligatorio'] == "1") {
                    if ($fila['Cantidad'] < $datos['cant_archivos_' . $fila['IdDocumentoAdjunto']]) {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, la cantidad maxima de archivos a subir es de " . $fila['Cantidad'] . ".", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                        return false;
                    }

                    if ($fila['CantidadMaxObligatoria'] > $datos['cant_archivos_' . $fila['IdDocumentoAdjunto']]) {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe subir " . $fila['CantidadMaxObligatoria'] . " archivos obligatorios.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                        return false;
                    }


                } else {
                    if ($fila['Cantidad'] < $datos['cant_archivos_' . $fila['IdDocumentoAdjunto']]) {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, la cantidad maxima de archivos a subir es de " . $fila['Cantidad'] . ".", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                        return false;
                    }

                }

            }

        }


        $i = 0;
        $arrayCampos['campos'] = [];
        $camposMostrar = [];
        foreach ($dataJsonArray['Indices'] as $fila) {
            $j = 0;
            if (isset($fila['Campos']) && is_array($fila['Campos'])) {
                if ($fila['Unico'] == 1) {
                    foreach ($fila['Campos'] as $Campo) {
                        $arrayCampos['campos'][$i][$j][$Campo['NombreCampo']] = trim($datos[$Campo['NombreCampo']]);
                        $camposMostrar[] = utf8_decode($Campo['CampoDescripcion']);
                        $j++;
                    }
                }
            }
            $i++;
        }

        if (count($arrayCampos) > 0) {
            $arrayCampos['IdDocumento'] = $datos['IdDocumento'];
            $arrayCampos['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $oElastic = new cClientesElastic($this->conexion, $this->formato);
            if (!$oElastic->TraerCantidadDocumentosxIndices($arrayCampos, $resultJson))
                return false;

            if (isset($resultJson['count']) && $resultJson['count'] > 0) {
                $camposMostrarString = implode(", ", $camposMostrar);
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, ya existe un documento con alguno de los siguientes campos iguales (" . utf8_encode($camposMostrarString) . ").", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
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

    private function _SetearNull(&$datos) {

        if (!isset($datos['IdSolicitudCobertura']) || $datos['IdSolicitudCobertura'] == "")
            $datos['IdSolicitudCobertura'] = "NULL";

        if (!isset($datos['IdEscuela']) || $datos['IdEscuela'] == "")
            $datos['IdEscuela'] = "NULL";

        if (!isset($datos['IdSolicitudCobertura']) || $datos['IdSolicitudCobertura'] == "")
            $datos['IdSolicitudCobertura'] = "NULL";

        if (!isset($datos['IdEscuelaDestino']) || $datos['IdEscuelaDestino'] == "")
            $datos['IdEscuelaDestino'] = "NULL";

        if (!isset($datos['IdDocumentoPadre']) || $datos['IdDocumentoPadre'] == "")
            $datos['IdDocumentoPadre'] = "NULL";

        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == "")
            $datos['IdTipoDocumento'] = "NULL";

        if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento'] == "")
            $datos['IdRegistroTipoDocumento'] = "NULL";

        if (!isset($datos['IdPuesto']) || $datos['IdPuesto'] == "")
            $datos['IdPuesto'] = "NULL";

        if (!isset($datos['IdPersona']) || $datos['IdPersona'] == "")
            $datos['IdPersona'] = "NULL";

        if (!isset($datos['IdLicencia']) || $datos['IdLicencia'] == "")
            $datos['IdLicencia'] = "NULL";

        if (!isset($datos['PeriodoFechaDesde']) || $datos['PeriodoFechaDesde'] == "")
            $datos['PeriodoFechaDesde'] = "NULL";
        else
            $datos['PeriodoFechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['PeriodoFechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');

        if (!isset($datos['PeriodoFechaHasta']) || $datos['PeriodoFechaHasta'] == "")
            $datos['PeriodoFechaHasta'] = "NULL";
        else
            $datos['PeriodoFechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['PeriodoFechaHasta'], 'dd/mm/aaaa', 'aaaa-mm-dd');


        if (!isset($datos['Observaciones']) || $datos['Observaciones'] == "")
            $datos['Observaciones'] = "0";

        if (!isset($datos['IdArea']) || $datos['IdArea'] == "")
            $datos['IdArea'] = null;

        if (!isset($datos['IdEstado']) || $datos['IdEstado'] == "")
            $datos['IdEstado'] = "NULL";

        if (!isset($datos['IdAreaInicial']) || $datos['IdAreaInicial'] == "")
            $datos['IdAreaInicial'] = "NULL";

        if (!isset($datos['IdEstadoInicial']) || $datos['IdEstadoInicial'] == "")
            $datos['IdEstadoInicial'] = "NULL";

        if (!isset($datos['MovimientoFecha']) || $datos['MovimientoFecha'] == "")
            $datos['MovimientoFecha'] = "NULL";

        if (!isset($datos['FechaEnvio']) || $datos['FechaEnvio'] == "")
            $datos['FechaEnvio'] = "NULL";

        if (!isset($datos['FechaTomaPosesion']) || $datos['FechaTomaPosesion'] == "")
            $datos['FechaTomaPosesion'] = "NULL";
        else
            $datos['FechaTomaPosesion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaTomaPosesion'], 'dd/mm/aaaa', 'aaaa-mm-dd');

        /* if (!isset($datos['FechaDesde']) || $datos['FechaDesde']=="")
             $datos['FechaDesde'] = "NULL";
         else
             $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],'dd/mm/aaaa','aaaa-mm-dd');*/

        if (!isset($datos['FechaDesignacion']) || $datos['FechaDesignacion'] == "")
            $datos['FechaDesignacion'] = "NULL";
        else
            $datos['FechaDesignacion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesignacion'], 'dd/mm/aaaa', 'aaaa-mm-dd');

        if (!isset($datos['NroResolucion']) || $datos['NroResolucion'] == "")
            $datos['NroResolucion'] = "NULL";

        return true;
    }

    private function _ValidarDatosVacios(&$datos) {


        if (!isset($datos['IdEscuela']) || $datos['IdEscuela'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar una escuela", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        $oEscuelas = new cEscuelas($this->conexion, $this->formato);
        if (!$oEscuelas->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, escuela inexixtente", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosEscuela = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!isset($datos['NombreTipoDocumento']) || $datos['NombreTipoDocumento'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un nombre de tipo de documento", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (file_exists(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA . "documentos_tipos/documento_tipo_" . $datos['IdTipoDocumento'] . ".php"))
            include(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA . "documentos_tipos/documento_tipo_" . $datos['IdTipoDocumento'] . ".php");
        else {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Ha ocurrido un error al buscar el tipo de documento {$datos['IdTipoDocumento']}", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $oValidaciones = new cValidaciones($this->conexion, $this, $oEscuelas, $this->formato);


        if (!$oValidaciones->ValidarCajasDatos($ArrayValidacion, $datos)) {
            return false;
        }

        return true;
    }

    public function BajarLiquidacionDocumento($oElastic, $datos, $filaWorkflow = null) {
        if (!isset($datos['BajaLiquidacion']) || $datos['BajaLiquidacion'] == "")
            $datos['BajaLiquidacion'] = "1";
        if (!parent::BajarLiquidacionDocumentoDB($datos))
            return false;
        return true;
    }

    public function QuitarLiquidacionDocumento($datos, $filaWorkflow = null) {
        $datos['BajaLiquidacion'] = "0";
        if (!parent::BajarLiquidacionDocumentoDB($datos/*,$oElastic,$filaWorkflow*/))
            return false;
        return true;
    }

    public function BuscarDocumentosAuditoriaLiquidacion($datos, &$resultado, &$numfilas): bool {

        $sparam = [
            'xIdDocumento' => 0,
            'IdDocumento' => '-1',
            'xIdPlaza' => 0,
            'IdPlaza' => '',
            'xCuil' => 0,
            'Cuil' => '',
            'xIdTipoDocumento' => 0,
            'IdTipoDocumento' => '-1',
            'xFechaAlta' => 0,
            'FechaAlta' => '',
            'xFechaDesde' => 0,
            'FechaDesde' => '',
            'xFechaHasta' => 0,
            'FechaHasta' => '',
            'xIdEstado' => 0,
            'IdEstado' => '-1',
            'xIdEscuela' => 0,
            'IdEscuela' => '-1',
            'xIdCategoria' => 0,
            'IdCategoria' => '-1',
            'xIdNivel' => 0,
            'IdNivel' => '',
            'xIdRegion' => 0,
            'IdRegion' => '',
            'xIdRevista' => 0,
            'IdRevista' => '',
            'xExcluir_Escuela' => 0,
            'Excluir_Escuela' => '-1',
            'xEscuelas' => 0,
            'Escuelas' => '-1',

            'xCategoriaNovedad' => 0,
            'CategoriaNovedad' => '',

            'xIdCargo' => 0,
            'IdCargo' => '',

            'xIdMateria' => 0,
            'IdMateria' => '',

            'xIdTipoCargo' => 0,
            'IdTipoCargo' => '',

            'xIdAccionesDocAux' => 1,
            'IdAccionesDocAux' => '',

            'limit' => '',
            'orderby' => "IdDocumento DESC",


        ];

        if (isset($datos['IdDocumento']) && $datos['IdDocumento'] != "") {
            $sparam['IdDocumento'] = $datos['IdDocumento'];
            $sparam['xIdDocumento'] = 1;
        }

        if (isset($datos['IdPlaza']) && $datos['IdPlaza'] != "") {
            $sparam['IdPlaza'] = $datos['IdPlaza'];
            $sparam['xIdPlaza'] = 1;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (isset($datos['IdCategoria']) && $datos['IdCategoria'] != "") {
            $sparam['IdCategoria'] = $datos['IdCategoria'];
            $sparam['xIdCategoria'] = 1;
        }

        if (isset($datos['FechaAlta']) && $datos['FechaAlta'] != "") {
            $sparam['FechaAlta'] = $datos['FechaAlta'];
            $sparam['xFechaAlta'] = 1;
        }

        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            $sparam['FechaHasta'] = $datos['FechaHasta'];
            $sparam['xFechaHasta'] = 1;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }
        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }
        if (isset($datos['IdRevista']) && $datos['IdRevista'] != "") {
            $sparam['IdRevista'] = $datos['IdRevista'];
            $sparam['xIdRevista'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }
        if (isset($datos['Escuelas']) && $datos['Escuelas'] != "") {
            $sparam['Escuelas'] = $datos['Escuelas'];
            $sparam['xEscuelas'] = 1;
        }

        if (isset($datos['CategoriaNovedad']) && $datos['CategoriaNovedad'] != "") {
            $sparam['CategoriaNovedad'] = $datos['CategoriaNovedad'];
            $sparam['xCategoriaNovedad'] = 1;
        }

        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }

        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }

        if (isset($datos['IdTipoCargo']) && $datos['IdTipoCargo'] != "") {
            $sparam['IdTipoCargo'] = $datos['IdTipoCargo'];
            $sparam['xIdTipoCargo'] = 1;
        }


        if (isset($datos['IdAccionesDocAux']) && $datos['IdAccionesDocAux'] != "") {
            $sparam['IdAccionesDocAux'] = $datos['IdAccionesDocAux'];
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        return parent::BuscarDocumentosAuditoriaLiquidacion($sparam, $resultado, $numfilas);
    }

    public function BuscarDocumentosAuditoriaLiquidacionCantidad($datos, &$resultado, &$numfilas): bool {

        $sparam = [
            'xIdDocumento' => 0,
            'IdDocumento' => '-1',
            'xIdPlaza' => 0,
            'IdPlaza' => '',
            'xCuil' => 0,
            'Cuil' => '',
            'xIdTipoDocumento' => 0,
            'IdTipoDocumento' => '-1',
            'xFechaAlta' => 0,
            'FechaAlta' => '',
            'xFechaDesde' => 0,
            'FechaDesde' => '',
            'xFechaHasta' => 0,
            'FechaHasta' => '',
            'xIdEstado' => 0,
            'IdEstado' => '-1',
            'xIdEscuela' => 0,
            'IdEscuela' => '-1',
            'xIdCategoria' => 0,
            'IdCategoria' => '-1',
            'xIdNivel' => 0,
            'IdNivel' => '',
            'xIdRegion' => 0,
            'IdRegion' => '',
            'xIdRevista' => 0,
            'IdRevista' => '',
            'xExcluir_Escuela' => 0,
            'Excluir_Escuela' => '-1',
            'xEscuelas' => 0,
            'Escuelas' => '-1',

            'xCategoriaNovedad' => 0,
            'CategoriaNovedad' => '',

            'xIdCargo' => 0,
            'IdCargo' => '',

            'xIdMateria' => 0,
            'IdMateria' => '',

            'xIdTipoCargo' => 0,
            'IdTipoCargo' => '',

            'xIdAccionesDocAux' => 1,
            'IdAccionesDocAux' => '',

            'limit' => '',
            //	'orderby' => "IdDocumento DESC"


        ];

        if (isset($datos['IdDocumento']) && $datos['IdDocumento'] != "") {
            $sparam['IdDocumento'] = $datos['IdDocumento'];
            $sparam['xIdDocumento'] = 1;
        }

        if (isset($datos['IdPlaza']) && $datos['IdPlaza'] != "") {
            $sparam['IdPlaza'] = $datos['IdPlaza'];
            $sparam['xIdPlaza'] = 1;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (isset($datos['IdCategoria']) && $datos['IdCategoria'] != "") {
            $sparam['IdCategoria'] = $datos['IdCategoria'];
            $sparam['xIdCategoria'] = 1;
        }

        if (isset($datos['FechaAlta']) && $datos['FechaAlta'] != "") {
            $sparam['FechaAlta'] = $datos['FechaAlta'];
            $sparam['xFechaAlta'] = 1;
        }

        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            $sparam['FechaHasta'] = $datos['FechaHasta'];
            $sparam['xFechaHasta'] = 1;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }
        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }
        if (isset($datos['IdRevista']) && $datos['IdRevista'] != "") {
            $sparam['IdRevista'] = $datos['IdRevista'];
            $sparam['xIdRevista'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }
        if (isset($datos['Escuelas']) && $datos['Escuelas'] != "") {
            $sparam['Escuelas'] = $datos['Escuelas'];
            $sparam['xEscuelas'] = 1;
        }

        if (isset($datos['CategoriaNovedad']) && $datos['CategoriaNovedad'] != "") {
            $sparam['CategoriaNovedad'] = $datos['CategoriaNovedad'];
            $sparam['xCategoriaNovedad'] = 1;
        }

        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }

        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }

        if (isset($datos['IdTipoCargo']) && $datos['IdTipoCargo'] != "") {
            $sparam['IdTipoCargo'] = $datos['IdTipoCargo'];
            $sparam['xIdTipoCargo'] = 1;
        }


        if (isset($datos['IdAccionesDocAux']) && $datos['IdAccionesDocAux'] != "") {
            $sparam['IdAccionesDocAux'] = $datos['IdAccionesDocAux'];
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        return parent::BuscarDocumentosAuditoriaLiquidacionCantidad($sparam, $resultado, $numfilas);
    }

    public function BuscarDocumentosAuditoriaLiquidacionCSV($datos, &$resultado, &$numfilas): bool {

        $sparam = [
            'xIdDocumento' => 0,
            'IdDocumento' => '-1',
            'xIdPlaza' => 0,
            'IdPlaza' => '',
            'xCuil' => 0,
            'Cuil' => '',
            'xIdTipoDocumento' => 0,
            'IdTipoDocumento' => '-1',
            'xFechaAlta' => 0,
            'FechaAlta' => '',
            'xFechaDesde' => 0,
            'FechaDesde' => '',
            'xFechaHasta' => 0,
            'FechaHasta' => '',
            'xIdEstado' => 0,
            'IdEstado' => '-1',
            'xIdEscuela' => 0,
            'IdEscuela' => '-1',
            'xIdCategoria' => 0,
            'IdCategoria' => '-1',
            'xIdNivel' => 0,
            'IdNivel' => '',
            'xIdRegion' => 0,
            'IdRegion' => '',
            'xIdRevista' => 0,
            'IdRevista' => '',
            'xExcluir_Escuela' => 0,
            'Excluir_Escuela' => '-1',
            'xEscuelas' => 0,
            'Escuelas' => '-1',

            'xCategoriaNovedad' => 0,
            'CategoriaNovedad' => '',

            'xIdCargo' => 0,
            'IdCargo' => '',

            'xIdMateria' => 0,
            'IdMateria' => '',

            'xIdTipoCargo' => 0,
            'IdTipoCargo' => '',

            'xIdAccionesDocAux' => 1,
            'IdAccionesDocAux' => '',

            'limit' => '',
            'orderby' => "IdDocumento DESC",

        ];

        if (isset($datos['IdDocumento']) && $datos['IdDocumento'] != "") {
            $sparam['IdDocumento'] = $datos['IdDocumento'];
            $sparam['xIdDocumento'] = 1;
        }

        if (isset($datos['IdPlaza']) && $datos['IdPlaza'] != "") {
            $sparam['IdPlaza'] = $datos['IdPlaza'];
            $sparam['xIdPlaza'] = 1;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (isset($datos['IdCategoria']) && $datos['IdCategoria'] != "") {
            $sparam['IdCategoria'] = $datos['IdCategoria'];
            $sparam['xIdCategoria'] = 1;
        }

        if (isset($datos['FechaAlta']) && $datos['FechaAlta'] != "") {
            $sparam['FechaAlta'] = $datos['FechaAlta'];
            $sparam['xFechaAlta'] = 1;
        }

        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            $sparam['FechaHasta'] = $datos['FechaHasta'];
            $sparam['xFechaHasta'] = 1;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }
        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }
        if (isset($datos['IdRevista']) && $datos['IdRevista'] != "") {
            $sparam['IdRevista'] = $datos['IdRevista'];
            $sparam['xIdRevista'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }
        if (isset($datos['Escuelas']) && $datos['Escuelas'] != "") {
            $sparam['Escuelas'] = $datos['Escuelas'];
            $sparam['xEscuelas'] = 1;
        }

        if (isset($datos['CategoriaNovedad']) && $datos['CategoriaNovedad'] != "") {
            $sparam['CategoriaNovedad'] = $datos['CategoriaNovedad'];
            $sparam['xCategoriaNovedad'] = 1;
        }

        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }

        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }

        if (isset($datos['IdTipoCargo']) && $datos['IdTipoCargo'] != "") {
            $sparam['IdTipoCargo'] = $datos['IdTipoCargo'];
            $sparam['xIdTipoCargo'] = 1;
        }


        if (isset($datos['IdAccionesDocAux']) && $datos['IdAccionesDocAux'] != "") {
            $sparam['IdAccionesDocAux'] = $datos['IdAccionesDocAux'];
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        return parent::BuscarDocumentosAuditoriaLiquidacionCSV($sparam, $resultado, $numfilas);
    }


}

?>
