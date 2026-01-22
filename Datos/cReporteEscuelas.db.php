<?php

namespace Bigtree\Datos;

use accesoBDLocal;
use ManejoErrores;

abstract class ReporteEscuelasDB
{
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    /**
     * Constructor de la clase
     * @param accesoBDLocal $conexion
     * @param mixed $formato
     */
    function __construct(accesoBDLocal $conexion, $formato)
    {
        $this->conexion = &$conexion;
        $this->formato = $formato;
    }

    /**
     * Destructor de la clase
     */
    function __destruct() {}

    protected function InsertarLog(array $datos, ?int &$codigoInsertado): bool {

        $spnombre = 'ins_LogReporteEscuelasEjecucion';
        $sparam  = [
            'Id' => $datos['Id'],
            'IdUsuario' => $datos['IdUsuario'],
            'FechaEjecucion' => $datos['FechaEjecucion'],
            'IdEstado' => $datos['IdEstado']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,'Error al insertar log de ejecucion por reporte de escuelas');
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }

    protected function InsertarLogReporte(array $datos, ?int &$codigoInsertado): bool {

        $spnombre = 'ins_LogReporteEscuelas';
        $sparam  = [
            'IdEscuela' => $datos['IdEscuela'],
            'Periodo' => $datos['Periodo'],
            'CUE' => $datos['CUE'],
            'CUISE' => $datos['CUISE'],
            'Nombre' => $datos['Nombre'],
            'Ambiente' => $datos['Ambiente'],
            'Nivel' => $datos['Nivel'],
            'CantPuestos' => $datos['CantPuestos'],
            'CantAgentes' => $datos['CantAgentes'],
            'CantDocentes' => $datos['CantDocentes'],
            'CantAuxiliares' => $datos['CantAuxiliares'],
            'CantOtrosCargos' => $datos['CantOtrosCargos'],
            'CantDesempenios' => $datos['CantDesempenios'],
            'PorcentajePuestosConDesempenio' => $datos['PorcentajePuestosConDesempenio'],
            'CantSecciones' => $datos['CantSecciones'],
            'CantMedicas' => $datos['CantMedicas'],
            'CantAdmin' => $datos['CantAdmin'],
            'CantArt' => $datos['CantArt'],
            'CantMat' => $datos['CantMat'],
            'CantIn' => $datos['CantIn'],
            'CantAltaSuplenteDoc' => $datos['CantAltaSuplenteDoc'],
            'CantAltaSuplenteAux' => $datos['CantAltaSuplenteAux'],
            'CantCeseSuplenteDoc' => $datos['CantCeseSuplenteDoc'],
            'CantCeseSuplenteAux' => $datos['CantCeseSuplenteAux'],
            'CantAltaManual' => $datos['CantAltaManual'],
            'CantCeseManul' => $datos['CantCeseManul'],
            'CantSCDirector' => $datos['CantSCDirector'],
            'CantSCUrgente' => $datos['CantSCUrgente'],
            'CantSCAux' => $datos['CantSCAux'],
            'CantSCDocente' => $datos['CantSCDocente'],
            'Otros' => $datos['Otros'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,'Error al insertar log de reporte por escuelas');
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }
}