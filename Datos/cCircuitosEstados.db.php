<?php

abstract class cCircuitosEstadosdb {


    function __construct() {}

    function __destruct() {}

    protected function BuscarxCodigo($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_CircuitosEstados_xIdEstado";
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function BuscarCombo(&$resultado, &$numfilas) {
        $spnombre = "sel_CircuitosEstados_combo";
        $sparam = [];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function BusquedaAvanzada($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_CircuitosEstados_busqueda_avanzada";
        $sparam = [
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'pxIdEstadoPublico' => $datos['xIdEstadoPublico'],
            'pIdEstadoPublico' => $datos['IdEstadoPublico'],
            'pxIdEstadoPublicoSad' => $datos['xIdEstadoPublicoSad'],
            'pIdEstadoPublicoSad' => $datos['IdEstadoPublicoSad'],
            'pxIdEstadoPublicoConsejo' => $datos['xIdEstadoPublicoConsejo'],
            'pIdEstadoPublicoConsejo' => $datos['IdEstadoPublicoConsejo'],
            'pxNombre' => $datos['xNombre'],
            'pNombre' => $datos['Nombre'],
            'pxNombrePublico' => $datos['xNombrePublico'],
            'pNombrePublico' => $datos['NombrePublico'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_CircuitosEstados_AuditoriaRapida";
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function BuscarEstadosFinales($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_CircuitosEstados_xEstadoFinal";
        $sparam = [
            'pEstadoFinal' => $datos['EstadoFinal'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar por codigo los estados finales. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function Insertar($datos, &$codigoinsertado) {
        $spnombre = "ins_CircuitosEstados";
        $sparam = [
            'pNombre' => $datos['Nombre'],
            'pIdEstadoPublico' => $datos['IdEstadoPublico'],
            'pIdEstadoPublicoSad' => $datos['IdEstadoPublicoSad'],
            'pIdEstadoPublicoConsejo' => $datos['IdEstadoPublicoConsejo'],
            'pDescripcion' => $datos['Descripcion'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        $codigoinsertado = $this->conexion->UltimoCodigoInsertado();

        return true;
    }


    protected function Modificar($datos) {
        $spnombre = "upd_CircuitosEstados_xIdEstado";
        $sparam = [
            'pNombre' => $datos['Nombre'],
            'pIdEstadoPublico' => $datos['IdEstadoPublico'],
            'pIdEstadoPublicoSad' => $datos['IdEstadoPublicoSad'],
            'pIdEstadoPublicoConsejo' => $datos['IdEstadoPublicoConsejo'],
            'pDescripcion' => $datos['Descripcion'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdEstado' => $datos['IdEstado'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function Eliminar($datos) {
        $spnombre = "del_CircuitosEstados_xIdEstado";
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function BusquedaRapida($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_CircuitosEstados_busqueda_rapida";
        $sparam = [
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEstado' => $datos['IdEstado'],
            'pxNombre' => $datos['xNombre'],
            'pNombre' => $datos['Nombre'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


}

?>
