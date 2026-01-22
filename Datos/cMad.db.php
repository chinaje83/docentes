<?php

namespace Bigtree\Datos;

use accesoBDLocal;
use ManejoErrores;

abstract class MadDB
{
    use ManejoErrores;

    /** @var accesoBDLocal  */
    protected $conexion;
    /** @var mixed  */
    protected $formato;
    /** @var array  */
    protected $error;
    /**
     * Constructor de la clase
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion, $formato) {
        $this->conexion = &$conexion;
        $this->formato = $formato;
    }

    /**
     * Destructor de la clase
     */
    function __destruct() {}

    protected function busquedaAvanzada(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre = 'sel_Mad_busqueda_avanzada';
        $sparam = [
            'pxId'=> $datos['xId'],
            'pId'=> $datos['Id'],
            'pxIdEscuela'=> $datos['xIdEscuela'],
            'pIdEscuela'=> $datos['IdEscuela'],
            'pxAnio'=> $datos['xAnio'],
            'pAnio'=> $datos['Anio'],
            'pxIdNivel'=> $datos['xIdNivel'],
            'pIdNivel'=> $datos['IdNivel'],
            'pxIdTipoCargo'=> $datos['xIdTipoCargo'],
            'pIdTipoCargo'=> $datos['IdTipoCargo'],
           // 'pQuery' => $datos['Query'],
            'plimit'=> $datos['limit'],
            'porderby'=> $datos['orderby']
        ];


        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400, "Error al realizar la b�squeda avanzada. ");
            return false;
        }

        return true;
    }


    protected function buscarCupofComboAprobado(array $datos,  &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_MadPuestos_aprobado_combo';
        $sparam = [
            'pIdMad'=> $datos['IdMad'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400, "Error al buscar combo.");
            return false;
        }

        return true;
    }
    protected function insertarMad(array $datos, ?int &$codigoInsertado): bool {

        $spnombre = 'ins_Mad';
        $sparam  = [
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pIdRegistroTipoDocumento' => $datos['IdRegistroTipoDocumento'],
            'pIdEstado' => $datos['IdEstado'],
            'pIdArea' => $datos['IdArea'],
            'pIdAreaInicial' => $datos['IdAreaInicial'],
            'pIdEstadoInicial' => $datos['IdEstadoInicial'],
            'pIdNivel' => $datos['IdNivel'],
            'pIdTipoCargo' => $datos['IdTipoCargo'],
            'pAnio' => $datos['Anio'],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,'Error al insertar mad');
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }

    protected function insertarMadPuesto(array $datos, ?int &$codigoInsertado, &$reloadPuesto): bool {

        $spnombre = 'ins_MadPuestos';
        $sparam  = [
            'pIdMad' => $datos['IdMad'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdPuestoPadre' => $datos['IdPuestoPadre'],
            'pIdPuestoDestino' => $datos['IdPuestoDestino'],
            'pVacante' => $datos['Vacante'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,'Error al insertar mad puestos');
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }

    protected function insertarMadPuestosPersonas(array $datos, ?int &$codigoInsertado): bool {

        $spnombre = 'ins_MadPuestosPersonas';
        $sparam  = [
            'pIdMad' => $datos['IdMad'],
            'pIdMadPuestos' => $datos['IdMadPuestos'],
            'pIdPofa' => $datos['IdPofa'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdPersona' => $datos['IdPersona'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,'Error al insertar mad puestos personas');
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }

    protected function eliminarMadPuesto(array $datos, &$reloadPuesto): bool {

        $spnombre = 'del_MadPuestos_xIdMad_xIdPuesto';
        $sparam  = [
            'pIdMad' => $datos['IdMad'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,'Error al eliminar mad puestos');
            return false;
        }

        return true;
    }

    protected function modificarVacante(array $datos): bool {

        $spnombre = 'upd_MadPuestos_Vacante_xIdMad';
        $sparam  = [
            'pVacante'=> $datos['Vacante'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pIdMad' => $datos['IdMad'],
            'pIdPuestoDestino' => $datos['IdPuestoDestino'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,'Error al actualizar vacante');
            return false;
        }

        return true;
    }
    protected function modificarEstado(array $datos): bool {

        $spnombre = 'upd_Mad_Estado_xId';
        $sparam  = [
            'pEstado'=> $datos['Estado'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pId' => $datos['IdMad'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,'Error al actualizar estado del MAD');
            return false;
        }

        return true;
    }

    protected function modificarEstadoMadPuesto(array $datos): bool {

        $spnombre = 'upd_MadPuestos_Estado_xIdMad';
        $sparam  = [
            'pEstado'=> $datos['Estado'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pIdMad' => $datos['IdMad'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,'Error al actualizar estado del MAD');
            return false;
        }

        return true;
    }

    protected function modificarEstadoMadPuestosPersonas(array $datos): bool {

        $spnombre = 'upd_MadPuestosPersonas_Estado_xIdMad';
        $sparam  = [
            'pEstado'=> $datos['Estado'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pIdMad' => $datos['IdMad'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,'Error al actualizar estado del MAD');
            return false;
        }

        return true;
    }

    protected function ModificarAreaEstado(array $datos): bool
    {
        $spnombre="upd_Mad_IdArea_IdEstado_xId";
        $sparam=array(
            'pIdArea' => $datos['IdArea'],
            'pIdEstado'=> $datos['IdEstado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdMad'=> $datos['IdMad']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            $this->setError(400,"Error al modificar el area y estado. ");
            return false;
        }
        return true;
    }

    protected function buscarDatosxId(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_Mad_Datos_xId';
        $sparam = [
            'pId'=> $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por id.");
            return false;
        }
        return true;
    }

    protected function buscarxId(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_Mad_xId';
        $sparam = [
            'pId'=> $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo.");
            return false;
        }
        return true;
    }
    protected function buscarEstadoxIdMad(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_MadEstado_xIdMad';
        $sparam = [
            'pId'=> $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo.");
            return false;
        }
        return true;
    }

    protected function buscarMadActivoxEscuela(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_Mad_Estado_xIdEscuela';
        $sparam = [
            'pIdEscuela'=> $datos['IdEscuela'],
            'pIdEstado'=> $datos['IdEstado'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar activos.");
            return false;
        }
        return true;
    }

    protected function buscarPuestosPersonasMad(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_buscarPuestosPersonasMad';
        $sparam = [
            'pIdMad'=> $datos['IdMad'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar agentes por puesto.");
            return false;
        }

        return true;
    }

    protected function buscarMadFinalizado(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_Mad_Final_xIdMad';
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdPersona'=> $datos['xIdPersona'],
            'pIdPersona'=> $datos['IdPersona'],
            'pxCodigoPuesto'=> $datos['xCodigoPuesto'],
            'pCodigoPuesto'=> $datos['CodigoPuesto'],
            'pxIdCargo'=> $datos['xIdCargo'],
            'pIdCargo'=> $datos['IdCargo'],
            'pxIdMateria'=> $datos['xIdMateria'],
            'pIdMateria'=> $datos['IdMateria'],
            //'pQuery' => $datos['Query'],
            'pIdMad'=> $datos['IdMad'],
            'pxDNI'=> $datos['xDNI'],
            'pDNI'=> $datos['DNI'],

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar documento finalizado.");
            return false;
        }

        return true;
    }


    protected function buscarMadPuestoxIdPuestoxIdMad(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_MadPuestos_xIdPuesto_xIdMad';
        $sparam = [
            'pIdEscuela'=> $datos['IdEscuela'],
            'pCodigoPuesto'=> $datos['CodigoPuesto'],
            'pIdPuesto'=> $datos['IdPuesto'],
            'pIdMad'=> $datos['IdMad'],
            'pBasePersonas' => BASEDATOS_PERSONAS
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar datos del cargo.");
            return false;
        }

        return true;
    }

    protected function buscarPuestosPersonasDestinoMad(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_buscarPuestosDestinoMad';
        $sparam = [
            'pIdPuesto'=> $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo puesto.");
            return false;
        }

        return true;
    }

    protected function buscarVacante(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_MadPuestos_Vacante_xIdMad';
        $sparam = [
            'pIdMad'=> $datos['IdMad'],
            'pIdPuestoDestino'=> $datos['IdPuestoDestino'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar vacante.");
            return false;
        }

        return true;
    }


    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */
    protected function busquedaAvanzadaTipoDocumental(array $datos,  &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_MadTipoDocumental_constante";
        $sparam = [
            'pxIdNivel'=> $datos['xIdNivel'],
            'pIdNivel'=> $datos['IdNivel'],
            'pxIdTipoCargo'=> $datos['xIdTipoCargo'],
            'pIdTipoCargo'=> $datos['IdTipoCargo'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al buscar tipo de documento");
            return false;
        }
        return true;
    }

    protected function buscarMadPuestoxIdPuestoDestinoxIdMad(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_MadPuestos_xIdMad_xIdPuestoDestino';
        $sparam = [
            'pIdPuesto'=> $datos['IdPuesto'],
            'pIdMad'=> $datos['IdMad']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar datos del puesto y mad seleccionado.");
            return false;
        }

        return true;
    }

}


