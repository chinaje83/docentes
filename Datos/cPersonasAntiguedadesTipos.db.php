<?php

use Bigtree\ExcepcionDB;

abstract class cPersonasAntiguedadesTiposDB {
    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    function __construct(accesoBDLocal $conexion, $formato) {
        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    function __destruct() {}

    public abstract function getError(): array;

    protected function setError($error, $error_description = ''): void {
        $this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
    }

    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_PersonasAntiguedadesTipos_xIdAntiguedadTipo";
        $sparam = [
            'pIdAntiguedadTipo' => $datos['IdAntiguedadTipo'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar por codigo.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_PersonasAntiguedadesTipos_busqueda_avanzada";
        $sparam = [
            'pxIdAntiguedadTipo' => $datos['xIdAntiguedadTipo'],
            'pIdAntiguedadTipo' => $datos['IdAntiguedadTipo'],
            'pxNombre' => $datos['xNombre'],
            'pNombre' => $datos['Nombre'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'pxAltaFecha' => $datos['xAltaFecha'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pxAltaUsuario' => $datos['xAltaUsuario'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pxUltimaModificacionesFecha' => $datos['xUltimaModificacionesFecha'],
            'pUltimaModificacionesFecha' => $datos['UltimaModificacionesFecha'],
            'pxUltimaModificacionUsuario' => $datos['xUltimaModificacionUsuario'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pxSoloLiquidacion' => $datos['xSoloLiquidacion'],
            'pSoloLiquidacion' => $datos['SoloLiquidacion'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la busqueda avanzada.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function Insertar(array $datos, ?int &$codigoInsertado): bool {
        $spnombre = "ins_PersonasAntiguedadesTipos";
        $sparam = [
            'pNombre' => $datos['Nombre'],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionesFecha' => $datos['UltimaModificacionesFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pSoloLiquidacion' => $datos['SoloLiquidacion'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }

    protected function Modificar(array $datos): bool {
        $spnombre = "upd_PersonasAntiguedadesTipos_xIdAntiguedadTipo";
        $sparam = [
            'pNombre' => $datos['Nombre'],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionesFecha' => $datos['UltimaModificacionesFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pSoloLiquidacion' => $datos['SoloLiquidacion'],
            'pIdAntiguedadTipo' => $datos['IdAntiguedadTipo'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function Eliminar(array $datos): bool {
        $spnombre = "del_PersonasAntiguedadesTipos_xIdAntiguedadTipo";
        $sparam = [
            'pIdAntiguedadTipo' => $datos['IdAntiguedadTipo'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }
    protected function ModificarEstado(array $datos): bool {
        $spnombre = "upd_PersonasAntiguedadesTipos_Estado_xIdAntiguedadTipo";
        $sparam = [
            'pEstado' => $datos['Estado'],
            'pIdAntiguedadTipo' => $datos['IdAntiguedadTipo'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }
}
