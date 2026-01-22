<?php 
abstract class cMovimientosTmpDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cTiposLiquidacionDB.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion,$formato){

		$this->conexion = &$conexion;
		$this->formato = &$formato;
	}

	/**
	 * Destructor de la clase cTiposLiquidacionDB.
	 */
	function __destruct(){}

	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public abstract function getError(): array;
	
	
	/**
	 * Guarda un mensaje de error
	 *
	 * @param string|array  $error
	 * @param string        $error_description
	 */
	protected function setError($error,$error_description=''): void {
		$this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
	}

    protected  function BuscarxIdLogMovimientos (array $datos, &$resultado, ?int &$numfilas){

        $spnombre = 'sel_LogMovimientosNovedadesTmp_en_orden_xIdLogMovimientos';
        $sparam  = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda de datos txt. ');
            return false;
        }

        return true;
    }

    protected function InsertarLogNovedadTmp(array $datos, ?int &$codigoInsertado): bool {

        $spnombre = 'ins_LogMovimientosNovedadesTmp';
        $sparam  = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
            'pOrden' => $datos['Orden'],
            'pPeriodo' => $datos['Periodo'],
            'pCuil' => $datos['Agente'],
            'pIdMovimiento' => $datos['idMovimiento'],
            'pIdPlaza' => $datos['idPlaza'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdServicioTGE' => $datos['idServicioTGE'],
            'pIdSubServicioNovTGE' => $datos['idSubServicioNovTGE'],
            'pCargoSalarial' => $datos['CargoSalarial'],
            'pIdSituacionRevista' => $datos['idSituacionRevista'],
            'pFechaAlta' => $datos['FechaAlta'],
            'pFechaBaja' => $datos['FechaBaja'],
            'pHoras' => $datos['Horas'],
            'pIdLicencia' => $datos['idLicencia'],
            'pIdServicioTGEQueSuple' => $datos['idServicioTGEQueSuple'],
            'pIdServicioTGERelacionado' => $datos['idServicioTGERelacionado'],
            'pCausaAlta' => $datos['CausaAlta'],
            'pCausaBaja' => $datos['CausaBaja'],
            'pFechaCarga' => $datos['FechaCarga'],
            'pUsuarioCarga' => $datos['UsuarioCarga'],
            'pIdRevistaAntigua' => $datos['IdRevistaAntigua'],
            'pIdRevistaNueva' => $datos['IdRevistaNueva'],
            'pIdSubServicioLicTGE' => $datos['idSubServicioLicTGE'],
            'pFechaMovimiento' => $datos['FechaMovimiento'],
            'pIdEstado' => $datos['IdEstado'], //$datos['IdEstado'],
            'pErrorCod' => 1, //$datos['ErrorCod'],

            'pIdPersona' => $datos['IdPersona'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pIdArticulo' => $datos['IdArticulo'],
            'pBajaLiquidacion' => $datos['BajaLiquidacion'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdPuestoDestino' => $datos['IdPuestoDestino'],
            'pFechaReintegro' => $datos['FechaReintegro'],

            'pIdEstadoNovedad' => $datos['IdEstadoNovedad'],
            'pEstadoNovedad' => $datos['EstadoNovedad'],
            'pFechaLiquidacion' => $datos['FechaLiquidacion']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,'Error al insertar movimiento novedad');
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }

    protected function EliminarxIdLogMovimientos($datos): bool {

        $spnombre="del_LogMovimientosNovedadesTmp_xIdLogMovimientos";
        $sparam  = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al eliminar el tmp. ');
            return false;
        }

        return true;
    }



}