<?php

abstract class cMotivosdb {


    function __construct() {}

    function __destruct() {}

    protected function BuscarxCodigo($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_Motivos_xId";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar el motivo por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function BuscarLicenciasTiposActivas($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_LicenciasTipos_activas";
        $sparam = [
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar los tipos de licencias activos. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function BusquedaAvanzada($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_Motivos_busqueda_avanzada";
        $sparam = [
            'pxId' => $datos['xId'],
            'pId' => $datos['Id'],
            'pxNombre' => $datos['xNombre'],
            'pNombre' => $datos['Nombre'],
            'pxDescripcion' => $datos['xDescripcion'],
            'pDescripcion' => $datos['Descripcion'],
            'pxIdCircuito' => $datos['xIdCircuito'],
            'pIdCircuito' => $datos['IdCircuito'],
            'pxIdTipoLicencia' => $datos['xIdTipoLicencia'],
            'pIdTipoLicencia' => $datos['IdTipoLicencia'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'pxReincorporacionAutomatico' => $datos['xReincorporacionAutomatico'],
            'pReincorporacionAutomatico' => $datos['ReincorporacionAutomatico'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function InactivaCargos(&$resultado, &$numfilas) {
        $spnombre = "sel_motivos_inactiva_cargos";
        $sparam = [

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar el listado de motivos. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function Insertar($datos, &$codigoinsertado) {
        $spnombre = "ins_Motivos";
        $sparam = [
            'pIdTipoLicencia' => $datos['IdTipoLicencia'],
            'pNombre' => $datos['Nombre'],
            'pDescripcion' => $datos['Descripcion'],
            'pPermiteLicenciaFamiliar' => $datos['PermiteLicenciaFamiliar'],
            'pPermiteSuperposicion' => $datos['PermiteSuperposicion'],
            'pPermiteAgentes' => $datos['PermiteAgentes'],
            'pSeleccionaCargos' => $datos['SeleccionaCargos'],
            'pIdCircuito' => $datos['IdCircuito'],
            'pDiasHabilesAnticipacion' => $datos['DiasHabilesAnticipacion'],
            'pDiasAnticipacionDesde' => $datos['DiasAnticipacionDesde'],
            'pDiasAnticipacionHasta' => $datos['DiasAnticipacionHasta'],
            'pDiasHabilesDuracion' => $datos['DiasHabilesDuracion'],
            'pFechaFinOptativa' => $datos['FechaFinOptativa'],
            'pMinimoDiasDuracion' => $datos['MinimoDiasDuracion'],
            'pMaximoDiasDuracion' => $datos['MaximoDiasDuracion'],
            'pInactivaCargos' => $datos["InactivaCargos"],
            'pReincorporacionAutomatico' => $datos["ReincorporacionAutomatico"],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $_SESSION['usuariocod'],
            'pAltaApp' => APP,
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionApp' => APP,
            'pAdmiteSuplente' => $datos['AdmiteSuplente'],
            'pSeleccionaEscuela' => $datos['SeleccionaEscuela'],
            'pIdRegimenSalarial' => $datos['IdRegimenSalarial'],
            'pPermiteInasistenciaDiaria' => $datos['PermiteInasistenciaDiaria'],
            'pPermitePlazaDestino' => $datos['PermitePlazaDestino'],
            'pSeleccionaDuracion' => $datos['SeleccionaDuracion'],
            'pEligeDiagnostico' => $datos['EligeDiagnostico'],
            'pConHistoriaClinica' => $datos['ConHistoriaClinica'],
            'pEligeArticulo' => $datos['EligeArticulo'],
            'pEmpiezaHoy' => $datos['EmpiezaHoy']
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $codigoinsertado = $this->conexion->UltimoCodigoInsertado();

        return true;
    }


    protected function Modificar($datos) {
        $spnombre = "upd_Motivos_xId";
        $sparam = [
            'pIdTipoLicencia' => $datos['IdTipoLicencia'],
            'pNombre' => $datos['Nombre'],
            'pDescripcion' => $datos['Descripcion'],
            'pPermiteLicenciaFamiliar' => $datos['PermiteLicenciaFamiliar'],
            'pPermiteSuperposicion' => $datos['PermiteSuperposicion'],
            'pPermiteAgentes' => $datos['PermiteAgentes'],
            'pSeleccionaCargos' => $datos['SeleccionaCargos'],
            'pIdCircuito' => $datos['IdCircuito'],
            'pDiasHabilesAnticipacion' => $datos['DiasHabilesAnticipacion'],
            'pDiasAnticipacionDesde' => $datos['DiasAnticipacionDesde'],
            'pDiasAnticipacionHasta' => $datos['DiasAnticipacionHasta'],
            'pDiasHabilesDuracion' => $datos['DiasHabilesDuracion'],
            'pFechaFinOptativa' => $datos['FechaFinOptativa'],
            'pMinimoDiasDuracion' => $datos['MinimoDiasDuracion'],
            'pMaximoDiasDuracion' => $datos['MaximoDiasDuracion'],
            'pInactivaCargos' => $datos["InactivaCargos"],
            'pReincorporacionAutomatico' => $datos["ReincorporacionAutomatico"],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionApp' => APP,
            'pAdmiteSuplente' => $datos['AdmiteSuplente'],
            'pSeleccionaEscuela' => $datos['SeleccionaEscuela'],
            'pId' => $datos['Id'],
            'pIdRegimenSalarial' => $datos['IdRegimenSalarial'],
            'pPermiteInasistenciaDiaria' => $datos['PermiteInasistenciaDiaria'],
            'pPermitePlazaDestino' => $datos['PermitePlazaDestino'],
            'pSeleccionaDuracion' => $datos['SeleccionaDuracion'],
            'pEligeDiagnostico' => $datos['EligeDiagnostico'],
            'pConHistoriaClinica' => $datos['ConHistoriaClinica'],
            'pEligeArticulo' => $datos['EligeArticulo'],
            'pEmpiezaHoy' => $datos['EmpiezaHoy']
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function Eliminar($datos) {
        $spnombre = "del_Motivos_xId";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function ModificarEstado($datos) {
        $spnombre = "upd_Motivos_Estado_xId";
        $sparam = [
            'pEstado' => $datos['Estado'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionApp' => APP,
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


}

?>
