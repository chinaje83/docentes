<?php 
abstract class cInterfazPlazasDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cInterfazPlazasDB.
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
	 * Destructor de la clase cInterfazPlazasDB.
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
	
	protected function PlazasEjecucionesSP(?string &$spnombre, ?array &$sparam): void
	{
		$spnombre = 'sel_PlazasEjecuciones_combo_FechaEjecucion';
        $sparam = [
            'pBDInterfaz'=>BDINTERFACES
        ];
	}
	public abstract function PlazasEjecucionesSPResult( &$resultado, ?int &$numfilas): bool;
	


	protected function EscuelasPuestosSP(?string &$spnombre, ?array &$sparam): void
	{
		$spnombre = 'sel_EscuelasPuestos_combo_CodigoPuesto';
        $sparam = [
            'pBDInterfaz'=>BDINTERFACES
        ];
	}
	public abstract function EscuelasPuestosSPResult( &$resultado, ?int &$numfilas): bool;
	


	protected function EscuelasSP(?string &$spnombre, ?array &$sparam): void
	{
		$spnombre = 'sel_Escuelas_combo_Nombre';
        $sparam =[];
	}
	public abstract function EscuelasSPResult( &$resultado, ?int &$numfilas): bool;
	


	protected function Plazas_EstadosSP(?string &$spnombre, ?array &$sparam): void
	{
		$spnombre = 'sel_Plazas_Estados_combo_Nombre';
		$sparam = [
            'pBDInterfaz'=>BDINTERFACES
        ];
	}
	public abstract function Plazas_EstadosSPResult( &$resultado, ?int &$numfilas): bool;
	


	protected function BuscarxCodigo(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_Plazas_xIdRegistro";
		$sparam=array(
            'pBDInterfaz'=>BDINTERFACES,
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

    protected function BusquedaAvanzadaCantidad(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_Plazas_busqueda_avanzada_cantidad";
        $sparam=array(
            'pBDInterfaz'=>BDINTERFACES,
            'pxIdRegistro'=> $datos['xIdRegistro'],
            'pIdRegistro'=> $datos['IdRegistro'],
            'pxID'=> $datos['xID'],
            'pID'=> $datos['ID'],
            'pxCUPOF'=> $datos['xCUPOF'],
            'pCUPOF'=> $datos['CUPOF'],
            'pxidEscuelaAnexo'=> $datos['xidEscuelaAnexo'],
            'pidEscuelaAnexo'=> $datos['idEscuelaAnexo'],
            'pxIdEstado'=> $datos['xIdEstado'],
            'pIdEstado'=> $datos['IdEstado']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


    protected function BusquedaAvanzada(array $datos,  &$resultado, ?int &$numfilas): bool
	{
        $spnombre="sel_Plazas_busqueda_avanzada";
		$sparam=array(
            'pBDInterfaz'=>BDINTERFACES,
			'pxIdRegistro'=> $datos['xIdRegistro'],
			'pIdRegistro'=> $datos['IdRegistro'],
			'pxID'=> $datos['xID'],
			'pID'=> $datos['ID'],
			'pxCUPOF'=> $datos['xCUPOF'],
			'pCUPOF'=> $datos['CUPOF'],
			'pxidEscuelaAnexo'=> $datos['xidEscuelaAnexo'],
			'pidEscuelaAnexo'=> $datos['idEscuelaAnexo'],
			'pxIdEstado'=> $datos['xIdEstado'],
			'pIdEstado'=> $datos['IdEstado'],
            'pxBaja'=> $datos['xBaja'],
            'pBaja'=> explode(',',$datos['Baja']),
			'plimit'=> $datos['limit'],
			'porderby'=> $datos['orderby']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function Insertar(array $datos, ?int &$codigoInsertado): bool
	{
		$spnombre="ins_Plazas";
		$sparam=array(
            'pBDInterfaz'=>BDINTERFACES,
			'pIdEjecucion'=> $datos['IdEjecucion'],
			'pID'=> $datos['ID'],
			'pCUPOF'=> $datos['CUPOF'],
			'pidPuesto'=> $datos['idPuesto'],
			'pidEscuelaAnexo'=> $datos['idEscuelaAnexo'],
			'pidOrientacion'=> $datos['idOrientacion'],
			'pidPlanEstudio'=> $datos['idPlanEstudio'],
			'pidCargo'=> $datos['idCargo'],
			'pidMateria'=> $datos['idMateria'],
			'pTurno'=> $datos['Turno'],
			'pAnio'=> $datos['Anio'],
			'pSeccion'=> $datos['Seccion'],
			'pCantidadModulos'=> $datos['CantidadModulos'],
			'pCantidadHoras'=> $datos['CantidadHoras'],
			'pRegimenSuplencias'=> $datos['RegimenSuplencias'],
			'pTemporalidad'=> $datos['Temporalidad'],
			'pFechaActualizacion'=> $datos['FechaActualizacion'],
			'pFechaAltaRegistro'=> $datos['FechaAltaRegistro'],
			'pIdEstado'=> $datos['IdEstado'],
			'pObservaciones'=> $datos['Observaciones']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$codigoInsertado=$this->conexion->UltimoCodigoInsertado();
		return true;
	}


	protected function Modificar(array $datos): bool
	{
		$spnombre="upd_Plazas_xIdRegistro";
		$sparam=array(
            'pBDInterfaz'=>BDINTERFACES,
			'pFechaActualizacion'=> date("Y-m-d H:i:s"),
			'pIdEstado'=> $datos['IdEstado'],
			'pObservaciones'=> $datos['Observaciones'],
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function Eliminar(array $datos): bool
	{
		$spnombre="del_Plazas_xIdRegistro";
		$sparam=array(
            'pBDInterfaz'=>BDINTERFACES,
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}




}