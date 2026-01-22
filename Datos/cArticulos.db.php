<?php

abstract class cArticulosTabladb {
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var int */
    protected $formato;

    /**
     * @param accesoBDLocal $conexion
     * @param               $formato
     */
    protected function __construct(accesoBDLocal $conexion, $formato) {
        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    protected function __destruct() {
    }

    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas) {
        $spnombre = "sel_Articulos_xId";
        $sparam = array(
            'pIdArticulo' => $datos['IdArticulo']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar el motivo por codigo. ');
            return false;
        }

        return true;
    }

    protected function buscarListado(&$resultado, ?int &$numfilas) {
        $spnombre = "sel_Articulos";
        $sparam = [];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar los articulos. ');
            return false;
        }

        return true;
    }


    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Articulos_BusquedaAvanzada";
        $sparam = array(
            'pxIdArticulo' => $datos['xIdArticulo'],
            'pIdArticulo' => $datos['IdArticulo'],
            'pxDescripcion' => $datos['xDescripcion'],
            'pDescripcion' => $datos['Descripcion'],
            'pxCodigo' => $datos['xCodigo'],
            'pCodigo' => $datos['Codigo'],
            'pxIdMotivo' => $datos['xIdMotivo'],
            'pIdMotivo' => $datos['IdMotivo'],
            'pxIdTipoLicencia' => $datos['xIdTipoLicencia'],
            'pIdTipoLicencia' => $datos['IdTipoLicencia'],
            'pxCantidadMaximaDias' => $datos['xCantidadMaximaDias'],
            'pCantidadMaximaDias' => $datos['CantidadMaximaDias'],
            'pxConGoceSueldo' => $datos['xConGoceSueldo'],
            'pConGoceSueldo' => $datos['ConGoceSueldo'],
            'pxPermiteOtroOrganismo' => $datos['xPermiteOtroOrganismo'],
            'pPermiteOtroOrganismo' => $datos['PermiteOtroOrganismo'],
            'pxEsAnual' => $datos['xEsAnual'],
            'pEsAnual' => $datos['EsAnual'],
            'pxEsAuxiliar' => $datos['xEsAuxiliar'],
            'pEsAuxiliar' => $datos['EsAuxiliar'],
			'pxIdExterno' => $datos['xIdExterno'],
			'pIdExterno' => $datos['IdExterno'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby']
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda avanzada. ');
            return false;
        }

        return true;
    }


    protected function Insertar(array $datos, ?int &$codigoinsertado): bool {
        $spnombre = "ins_Articulos";
        $sparam = array(
            'pIdMotivo' => $datos['IdMotivo'],
            'pCodigo' => $datos['Codigo'],
            'pDescripcion' => $datos['Descripcion'],
            'pCantidadMaximaDias' => $datos['CantidadMaximaDias'],
            'pCantidadMaximaDiasMes' => $datos['CantidadMaximaDiasMes'],
            'pConGoceSueldo' => $datos['ConGoceSueldo'],
            'pPermiteOtroOrganismo' => $datos['PermiteOtroOrganismo'],
            'pEsAnual' => $datos['EsAnual'],
            'pEsAuxiliar' => $datos['EsAuxiliar'],
			'pIdExterno' => $datos['IdExterno'],
            'pBajaLiquidacion'=> $datos['BajaLiquidacion'],
            'pObservaciones' => $datos['Observaciones'],
            'pIgnoraCargos' => $datos['IgnoraCargos'],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pDiasHabiles' => $datos['DiasHabiles'],
            'pIdRegimenSalarial' => $datos['IdRegimenSalarial']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al insertar. ');
            return false;
        }
        $codigoinsertado = $this->conexion->UltimoCodigoInsertado();

        return true;
    }


    protected function Modificar(array $datos): bool {
        $spnombre = "upd_Articulos_xIdArticulo";
        $sparam = array(
            'pIdArticulo' => $datos['IdArticulo'],
            'pIdMotivo' => $datos['IdMotivo'],
            'pCodigo' => $datos['Codigo'],
            'pDescripcion' => $datos['Descripcion'],
            'pCantidadMaximaDias' => $datos['CantidadMaximaDias'],
            'pCantidadMaximaDiasMes' => $datos['CantidadMaximaDiasMes'],
            'pConGoceSueldo' => $datos['ConGoceSueldo'],
            'pPermiteOtroOrganismo' => $datos['PermiteOtroOrganismo'],
            'pEsAnual' => $datos['EsAnual'],
            'pEsAuxiliar' => $datos['EsAuxiliar'],
			'pIdExterno' => $datos['IdExterno'],
            'pDiasHabiles' => $datos['DiasHabiles'],
            'pBajaLiquidacion' => $datos['BajaLiquidacion'],
            'pObservaciones' => $datos['Observaciones'],
            'pIgnoraCargos' => $datos['IgnoraCargos'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pDiasHabiles' => $datos['DiasHabiles'],
            'pIdRegimenSalarial' => $datos['IdRegimenSalarial']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar. ');
            return false;
        }

        return true;
    }


    protected function ModificarEstado(array $datos): bool {
        $spnombre = "upd_Articulos_Estado_xId";
        $sparam = array(
            'pEstado' => $datos['Estado'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdArticulo' => $datos['IdArticulo']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar el estado. ');
            return false;
        }

        return true;
    }


}
