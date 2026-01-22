<?php

abstract class cDocumentosPuestosDB {
    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    /**
     * Constructor de la clase cDocumentosPuestosDB.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion, $formato) {

        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    /**
     * Destructor de la clase cDocumentosPuestosDB.
     */
    function __destruct() {}

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public abstract function getError(): array;


    /**
     * Guarda un mensaje de error
     *
     * @param string|array $error
     * @param string       $error_description
     */
    protected function setError($error, $error_description = ''): void {
        $this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
    }

    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_DocumentosPuestos_xIdDocumento_IdPuesto";
        $sparam = [
            'pIdDocumento' => $datos['IdDocumento'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    public function BuscarDocumentoxIdPofa(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_DocumentosPuestos_Documento_xIdPofa";
        $sparam = [
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BuscarxIdDocumento(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_DocumentosPuestos_xIdDocumento";
        $sparam = [
            'pIdDocumento' => $datos['IdDocumento'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BuscarDocumentos(&$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_DocumentosPuestos";
        $sparam = [];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_DocumentosPuestos_busqueda_avanzada";
        $sparam = [
            'pxIdDocumento' => $datos['xIdDocumento'],
            'pIdDocumento' => $datos['IdDocumento'],
            'pxIdPuesto' => $datos['xIdPuesto'],
            'pIdPuesto' => $datos['IdPuesto'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function BuscarxIdPofa($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_DocumentosPuestos_xIdPuesto";
        $sparam = [
            'pIdPofa' => $datos['IdPofa'],
            'porderby' => $datos['orderby'],
            'plimit' => $datos['limit'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar novedades por puesto. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function BuscarAuditoriaRapida(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_DocumentosPuestos_AuditoriaRapida";
        $sparam = [
            'pIdDocumento' => $datos['IdDocumento'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function BuscarDatosJson(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_DocumentosPuestos_DatosJson_xIdDocumento";
        $sparam = [
            'pIdDocumento' => $datos['IdDocumento']
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function Insertar(array $datos): bool {
        $spnombre = "ins_DocumentosPuestos";
        $sparam = [
            'pIdDocumento' => $datos['IdDocumento'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdRevista' => $datos['IdRevista'],
            'pCodigoLiquidador' => $datos['CodigoLiquidador'],
            'pIdEstado' => $datos['IdEstado'],
            'pDatosJson' => $datos['DatosJson'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaEscuela' => $datos['AltaEscuela'],
            'pAltaRol' => $datos['AltaRol'],
            'pIdPofa' => $datos['IdPofa'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionEscuela' => $datos['UltimaModificacionEscuela'],
            'pUltimaModificacionRol' => $datos['UltimaModificacionRol'],
            'pHashDato' => $datos['HashDato'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function Eliminar(array $datos): bool {
        $spnombre = "del_DocumentosPuestos_xIdDocumento_IdPuesto";
        $sparam = [
            'pIdDocumento' => $datos['IdDocumento'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function ActualizarIdPofaxPuesto(array $datos): bool {
        $spnombre = "upd_DocumentosPuestos_xIdDocumento_IdPofa";
        $sparam = [
            'pIdDocumento' => $datos['IdDocumento'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdPofa' => $datos['IdPofa'],
            'pDatosJson' => $datos['DatosJson'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function EliminarxIdDocumento(array $datos): bool {
        $spnombre = "del_DocumentosPuestos_xIdDocumento";
        $sparam = [
            'pIdDocumento' => $datos['IdDocumento'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function ModificarHashDato($datos) {
        $spnombre = "upd_DocumentosPuestos_HashDato_xIdDocumento_IdPuesto";
        $sparam = [
            'pHashDato' => $datos['HashDato'],
            'pIdDocumento' => $datos['IdDocumento'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


}
