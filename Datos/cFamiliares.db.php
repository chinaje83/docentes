<?php

abstract class cFamiliaresdb {

    function __construct() {}

    function __destruct() {}

    protected function BuscarxCodigo($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_Familiares_xId";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function BuscarxCUIL($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_Familiares_xCUIL";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pCUIL' => $datos['CUIL'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar el CUIL. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function BuscarAgenteRelacionado($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_Familiares_xIdFamiliarAgente";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al agente relacionado. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function BusquedaAvanzada($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_Familiares_busqueda_avanzada";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pxId' => $datos['xId'],
            'pId' => $datos['Id'],
            'pxIdFamiliarAgente' => $datos['xIdFamiliarAgente'],
            'pIdFamiliarAgente' => $datos['IdFamiliarAgente'],
            'pxNombre' => $datos['xNombre'],
            'pNombre' => $datos['Nombre'],
            'pxApellido' => $datos['xApellido'],
            'pApellido' => $datos['Apellido'],
            'pxDni' => $datos['xDni'],
            'pDni' => $datos['Dni'],
            'pxCUIL' => $datos['xCUIL'],
            'pCUIL' => $datos['CUIL'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdAccionesDocAuxSesion' => $datos['xIdAccionesDocAuxSesion'],
            'pIdAccionesDocAuxSesion' => $datos['IdAccionesDocAuxSesion'],
            'pxIdPersona' => $datos["xIdPersona"],
            'pIdPersona' => $datos["IdPersona"],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function Insertar($datos, &$codigoinsertado) {
        $spnombre = "ins_Personas_Familiar";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pCUIL' => $datos['CUIL'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pDNI' => $datos['Dni'],
            'pSexo' => $datos['Sexo'],
            'pNombre' => $datos['Nombre'],
            'pApellido' => $datos['Apellido'],
            'pNombreCompleto' => $datos['NombreCompleto'],
            'pFechaNacimiento' => $datos['FechaNacimiento'],
            'pEsFamiliar' => $datos['EsFamiliar'],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar el familiar en personas. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        $codigoinsertado = $this->conexion->UltimoCodigoInsertado();

        return true;
    }


    protected function Modificar($datos) {
        $spnombre = "upd_Familiares_xId";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdEstado' => $datos['IdEstado'],
            'pIdFamiliarPersona' => $datos['IdFamiliarPersona'],
            'pId' => $datos['Id'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modifica al familiar en familiares. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function DenegarFamiliar($datos) {
        $spnombre = "upd_Familiares_Estado_xId";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdEstado' => $datos['IdEstado'],
            'pId' => $datos['Id'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modifica al familiar en familiares. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

}

?>
