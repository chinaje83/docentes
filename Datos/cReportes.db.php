<?php

namespace Bigtree\Datos;

use accesoBDLocal;
use ManejoErrores;
use FuncionesPHPLocal;

abstract class Reportes {
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var int */
    protected $formato;

    /**
     * @param \accesoBDLocal $conexion
     * @param int            $formato
     */
    protected function __construct(accesoBDLocal $conexion, int $formato) {
        $this->conexion =& $conexion;
        $this->formato = $formato;
    }

    /**
     *
     */
    protected function __destruct() {
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */

    protected function BusquedaAvanzadaCantidad(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_Registro_Usuario_Cantidad";
        $sparam = [
            'pBase' => BASEDATOS,
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pBaseLicencias' => BASEDATOSLICENCIAS,
            'pBaseAuditorias' => BASEDATOSAUDITORIAS,
            'pxDni'=> $datos['xDni'],
            'pDni'=> $datos['Dni'],
            'pxIdRol'=> $datos['xIdRol'],
            'pIdRol'=> $datos['IdRol'],
            'pxEscuela'=> $datos['xEscuela'],
            'pEscuela'=> $datos['Escuela'],
            'pxEstado'=> $datos['xEstado'],
            'pEstado'=> $datos['Estado'],
            'pxIdUsuario'=> $datos['xIdUsuario'],
            'pIdUsuario'=> $datos['IdUsuario'],
            'pxFechaRango'=> $datos['xFechaRango'],
            'pFechaDesde'=> $datos['FechaDesde'],
            'pFechaHasta'=> $datos['FechaHasta']
        ];
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }




    protected function BuscarRegistroxUsuario($datos,&$resultado,&$numfilas)
    {
        $spnombre = 'sel_Registro_Usuario_xId';
        $sparam = [
            'pBase' => BASEDATOS,
            'pBaseLicencias' => BASEDATOSLICENCIAS,
            'pBaseAuditorias' => BASEDATOSAUDITORIAS,
            'pxDni'=> $datos['xDni'],
			'pDni'=> $datos['Dni'],
            'pxIdRol'=> $datos['xIdRol'],
            'pIdRol'=> $datos['IdRol'],
            'pxEscuela'=> $datos['xEscuela'],
            'pEscuela'=> $datos['Escuela'],
            'pxEstado'=> $datos['xEstado'],
            'pEstado'=> $datos['Estado'],
            'pxIdUsuario'=> $datos['xIdUsuario'],
			'pIdUsuario'=> $datos['IdUsuario'],
            'pxFechaRango'=> $datos['xFechaRango'],
            'pFechaDesde'=> $datos['FechaDesde'],
            'pFechaHasta'=> $datos['FechaHasta'],
            'plimit'=> $datos['limit'],
			'porderby'=> $datos['orderby']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar reporte. ');
            return false;
        }
        return true;
    }






    protected function buscarConflictosSolicitudesCobertura(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_SolicitudesCoberturaDesempenoss_conflictos';
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pEscuelasIgnorar' => implode(',', ESCUELAS_DE_PRUEBA ?: [-1]),
            'pLimit' => $datos['limit'] ?? '',
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar reporte. ');
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function buscarConflictosEscuelasPuestos(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_EscuelasPuestosPersonas_conflictos';
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pEscuelasIgnorar' => implode(',', ESCUELAS_DE_PRUEBA ?: [-1]),
            'pLimit' => $datos['limit'] ?? '',
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar reporte. ');
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function BuscarTotalNovedadesxAnios(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_reportes_novedades_cantidadesmesanio';
        $sparam = [
            'pBasePersonasLic' => BASEDATOSLICENCIAS,
            'pEscuelasIgnorar' => implode(',', ESCUELAS_DE_PRUEBA ?: [-1]),
        ];


        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar reporte de novedades. ');
            return false;
        }
        return true;
    }
    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function BuscarTotalEventosPorDia(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_Reporte_movimientos_dia';
        $sparam = [
			'pBasePersonasLic' => BASEDATOSLICENCIAS,
            'pEscuelasIgnorar' => implode(',', ESCUELAS_DE_PRUEBA ?: [-1]),
            'pxAnio' => $datos["xAnio"],
            'pAnio' => $datos["Anio"],
            'pxMes' => $datos["xMes"],
            'pMes' => $datos["Mes"]
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar reporte de novedades. ');
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function BuscarTotalEventosPorDiaProcesados(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_Reporte_licencias_novedades_dia';
        $sparam = [
            'pxAnio' => $datos["xAnio"],
            'pAnio' => $datos["Anio"],
            'pxMes' => $datos["xMes"],
            'pMes' => $datos["Mes"]
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar reporte de novedades. ');
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function BuscarTotalLicenciasPorDia(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_Reporte_licencias_dia';
        $sparam = [
            'pBasePersonasLic' => BASEDATOSLICENCIAS,
            'pEscuelasIgnorar' => implode(',', ESCUELAS_DE_PRUEBA ?: [-1]),
            'pxAnio' => $datos["xAnio"],
            'pAnio' => $datos["Anio"],
            'pxMes' => $datos["xMes"],
            'pMes' => $datos["Mes"]
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar reporte de novedades. ');
            return false;
        }
        return true;
    }


    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function AsistenciaPerfecta(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_Reporte_asistencia_perfecta';
        $sparam = [
			'pBasePersonasLic' => BASEDATOSLICENCIAS,
            'pBase' => BASEDATOS,
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar el reporte de asistencia perfecta. ');
            return false;
        }
        return true;
    }

}
