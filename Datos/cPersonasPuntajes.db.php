<?php 
abstract class cPersonasPuntajesDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cPersonasPuntajesDB.
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
	 * Destructor de la clase cPersonasPuntajesDB.
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
	
	protected function BuscarxCodigo(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_PersonasPuntajes_xId";
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
	
	protected function BuscarxPersona(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_PersonasPuntajes_xIdPersona";
		$sparam=array(
			'pIdPersona'=> $datos['IdPersona']
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
		$spnombre="sel_PersonasPuntajes_busqueda_avanzada";
		$sparam=array(
			'pBase' => BASEDATOS_PERSONAS,
			'pxId'=> $datos['xId'],
			'pId'=> $datos['Id'],
			'pxIdPersona'=> $datos['xIdPersona'],
			'pIdPersona'=> $datos['IdPersona'],
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
		$spnombre="sel_PersonasPuntajes_AuditoriaRapida";
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


	protected function Insertar(array $datos, ?int &$codigoInsertado): bool
	{
		$spnombre="ins_PersonasPuntajes";
		$sparam=array(
			'pIdPersona'=> $datos['IdPersona'],
			'pTitulo'=> $datos['Titulo'],
			'pAnioEgreso'=> $datos['AnioEgreso'],
			'pAntiguedadRama'=> $datos['AntiguedadRama'],
			'pDesfavorabilidadRama'=> $datos['DesfavorabilidadRama'],
			'pAntiguedadCargo'=> $datos['AntiguedadCargo'],
			'pDesfavorabilidadCargo'=> $datos['DesfavorabilidadCargo'],
			'pCalificaciones'=> $datos['Calificaciones'],
			'pBonificantes'=> $datos['Bonificantes'],
			'pTitularidad'=> $datos['Titularidad'],
			'pResidencia'=> $datos['Residencia'],
			'pTotal'=> $datos['Total'],
			'pEstado'=> $datos['Estado'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
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
		$spnombre="upd_PersonasPuntajes_xId";
		$sparam=array(
			'pIdPersona'=> $datos['IdPersona'],
			'pTitulo'=> $datos['Titulo'],
			'pAnioEgreso'=> $datos['AnioEgreso'],
			'pAntiguedadRama'=> $datos['AntiguedadRama'],
			'pDesfavorabilidadRama'=> $datos['DesfavorabilidadRama'],
			'pAntiguedadCargo'=> $datos['AntiguedadCargo'],
			'pDesfavorabilidadCargo'=> $datos['DesfavorabilidadCargo'],
			'pCalificaciones'=> $datos['Calificaciones'],
			'pBonificantes'=> $datos['Bonificantes'],
			'pTitularidad'=> $datos['Titularidad'],
			'pResidencia'=> $datos['Residencia'],
			'pTotal'=> $datos['Total'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
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
		$spnombre="del_PersonasPuntajes_xId";
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


	protected function ModificarEstado(array $datos): bool
	{
		$spnombre="upd_PersonasPuntajes_Estado_xId";
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