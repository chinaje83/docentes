<?php 
abstract class cEscuelasNivelPlanesDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cEscuelasNivelPlanesDB.
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
	 * Destructor de la clase cEscuelasNivelPlanesDB.
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
    protected function getPlanes(&$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_PlanesEducativos";
        $sparam=array(

        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }
	protected function BuscarxCodigo(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasNivelPlanes_xId";
		$sparam=array(
			'pId'=> $datos['Id']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			$this->setError(400,"Error al buscar al buscar por codigo. ");
			return false;
		}
		return true;
	}


	protected function BusquedaAvanzada(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasNivelPlanes_busqueda_avanzada";
		$sparam=array(
			'pxIdPlanEducativo'=> $datos['xIdPlanEducativo'],
			'pIdPlanEducativo'=> $datos['IdPlanEducativo'],
			'pxIdNivelModalidad'=> $datos['xIdNivelModalidad'],
			'pIdNivelModalidad'=> $datos['IdNivelModalidad'],
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado'],
			'plimit'=> $datos['limit'],
			'porderby'=> $datos['orderby']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			$this->setError(400,"Error al realizar la búsqueda avanzada. ");
			return false;
		}
		return true;
	}


	protected function BuscarAuditoriaRapida(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasNivelPlanes_AuditoriaRapida";
		$sparam=array(
			'pId'=> $datos['Id']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			$this->setError(400,"Error al buscar al buscar por codigo. ");
			return false;
		}
		return true;
	}

    protected function BuscarExistente(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasNivelPlanes_xIdPlanEducativo_xIdNivelModalidad";
        $sparam=array(
            'pIdPlanEducativo'=> $datos['IdPlanEducativo'],
            'pIdNivelModalidad'=> $datos['IdNivelModalidad']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarxIdNivelModalidad(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasNivelPlanes_xIdNivelModalidad";
        $sparam=array(
            'pIdNivelModalidad'=> $datos['IdNivelModalidad'],
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarxIdEscuelaTurno(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_PlanesEducativos_xIdEscuelaTurno";
        $sparam=array(
            'pIdEscuelaTurno'=> $datos['IdEscuelaTurno']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


	protected function Insertar(array $datos, ?int &$codigoInsertado): bool
	{
		$spnombre="ins_EscuelasNivelPlanes";
		$sparam=array(
			'pIdPlanEducativo'=> $datos['IdPlanEducativo'],
			'pIdNivelModalidad'=> $datos['IdNivelModalidad'],
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al insertar. ");
			return false;
		}
		$codigoInsertado=$this->conexion->UltimoCodigoInsertado();
		return true;
	}


	protected function Modificar(array $datos): bool
	{
		$spnombre="upd_EscuelasNivelPlanes_xId";
		$sparam=array(
			'pIdPlanEducativo'=> $datos['IdPlanEducativo'],
			'pIdNivelModalidad'=> $datos['IdNivelModalidad'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pId'=> $datos['Id']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al modificar. ");
			return false;
		}
		return true;
	}


	protected function Eliminar(array $datos): bool
	{
		$spnombre="del_EscuelasNivelPlanes_xId";
		$sparam=array(
			'pId'=> $datos['Id']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al eliminar por codigo. ");
			return false;
		}
		return true;
	}


	protected function BuscarUltimoOrden(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasNivelPlanes_max_orden";
		$sparam=array();
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			$this->setError(400,"Error al buscar el maximo orden. ");
			return false;
		}
		return true;
	}


	protected function ModificarOrden(array $datos): bool
	{
		$spnombre="upd_EscuelasNivelPlanes_Id_xId";
		$sparam=array(
			'pId'=> $datos['Id'],
			'pId'=> $datos['Id']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al modificar el orden. ");
			return false;
		}
		return true;
	}


	protected function ModificarEstado(array $datos): bool
	{
		$spnombre="upd_EscuelasNivelPlanes_Estado_xId";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pId'=> $datos['Id']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al modificar el estado. ");
			return false;
		}
		return true;
	}




}