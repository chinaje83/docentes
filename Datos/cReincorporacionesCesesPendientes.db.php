<?php

abstract class cReincorporacionesCesesPendientesdb {


    function __construct() {}

    function __destruct() {}


    protected function busquedaAvanzada($datos, &$numfilas, &$resultado) {

        $spnombre = 'sel_ReincorporacionesCesesPendientes_busqueda_avanzada';
        $sparam = [
            'pxIdPuesto' => $datos['xIdPuesto'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pxIdPofa' => $datos['xIdPofa'],
            'pIdPofa' => $datos['IdPofa'],
            'pxNombreSuplente' => $datos['xNombreSuplente'],
            'pNombreSuplente' => $datos['NombreSuplente'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxNombreSuplido' => $datos['xNombreSuplido'],
            'pNombreSuplido' => $datos['NombreSuplido'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error al realizar la búsqueda avanzada. ', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }

        return true;
    }

    protected function buscarXIdEscuela($datos, &$numfilas, &$resultado) {

        $spnombre = 'sel_ReincorporacionesCesesPendientes_xIdEscuela';
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error al realizar la busqueda. ', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }

        return true;
    }

    protected function CambiarEstadoxIdPofa($datos) {

        $spnombre = 'upd_ReincorporacionesCesesPendientes_xIdPofa';
        $sparam = [
            'pIdPofa' => $datos['IdPofa'],
            'pEstado' => $datos['Estado'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date('Y-m-d H:i:s'),
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error al cambiar de estado. ', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }

        return true;
    }

    protected function Insertar($datos, &$codigoinsertado) {
        $spnombre = 'ins_ReincorporacionesCesesPendientes';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdPofa' => $datos['IdPofa'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdDocumento' => $datos['IdDocumento'],
            'pIdPersonaReincorporada' => $datos['IdPersonaReincorporada'],
            'pEstado' => $datos['Estado'],
            'pAltaUsuario' => $_SESSION['usuariocod'],
            'pAltaFecha' => date('Y-m-d H:i:s'),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date('Y-m-d H:i:s'),
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error al insertar. ', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }

        $codigoinsertado = $this->conexion->UltimoCodigoInsertado();

        return true;
    }

}

?>
