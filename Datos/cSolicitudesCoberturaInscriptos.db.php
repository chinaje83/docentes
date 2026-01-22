<?php 
abstract class cSolicitudesCoberturaInscriptosDB
{
	use ManejoErrores;
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cSolicitudesCoberturaInscriptosDB.
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
	 * Destructor de la clase cSolicitudesCoberturaInscriptosDB.
	 */
	function __destruct(){}
	
	protected function SolicitudesCoberturaSP(?string &$spnombre, ?array &$sparam): void
	{
		$spnombre = 'sel_SolicitudesCobertura_combo_IdEscuela';
		$sparam = [];
	}
	public abstract function SolicitudesCoberturaSPResult( &$resultado, ?int &$numfilas): bool;
	


	protected function PersonasPuntajesSP(?string &$spnombre, ?array &$sparam): void
	{
		$spnombre = 'sel_PersonasPuntajes_combo_IdPersona';
		$sparam = [];
	}
	public abstract function PersonasPuntajesSPResult( &$resultado, ?int &$numfilas): bool;
	


	protected function BuscarxCodigo(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_SolicitudesCoberturaInscriptos_xId";
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
	
	
	protected function buscarxSolicitud(array $datos, &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_SolicitudesCoberturaInscriptos_xIdSolicitudCobertura";
		$sparam=array(
			'pBasePersonas' => BASEDATOS_PERSONAS,
			'pIdSolicitudCobertura'=> $datos['IdSolicitudCobertura']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al buscar por solicitud. ");
			return false;
		}
		return true;
	}


	protected function BusquedaAvanzada(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_SolicitudesCoberturaInscriptos_busqueda_avanzada";
		$sparam=array(
			'pBase' => BASEDATOS_PERSONAS,
			'pxId'=> $datos['xId'],
			'pId'=> $datos['Id'],
			'pxIdSolicitudCobertura'=> $datos['xIdSolicitudCobertura'],
			'pIdSolicitudCobertura'=> $datos['IdSolicitudCobertura'],
			'pxIdActoPublico'=> $datos['xIdActoPublico'],
			'pIdActoPublico'=> $datos['IdActoPublico'],
			'pxIdPersonaPuntaje'=> $datos['xIdPersonaPuntaje'],
			'pIdPersonaPuntaje'=> $datos['IdPersonaPuntaje'],
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
		$spnombre="sel_SolicitudesCoberturaInscriptos_AuditoriaRapida";
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
		$spnombre="ins_SolicitudesCoberturaInscriptos";
		$sparam=array(
			'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
			'pIdActoPublico' => $datos['IdActoPublico'],
			'pIdPersonaPuntaje' => $datos['IdPersonaPuntaje'],
			'pIdEstado' => $datos['IdEstado'],
			'pAltaUsuario' => $datos['AltaUsuario'],
			'pAltaFecha' => $datos['AltaFecha'],
			'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al insertar. ");
			return false;
		}
		$codigoInsertado=$this->conexion->UltimoCodigoInsertado();
		return true;
	}
	
	
	protected function insertarInscriptosSimulados(array $datos): bool
	{
		$spnombre="ins_SolicitudesCoberturaInscriptos_rnd";
		$sparam=array(
			'pIdSolicitudCobertura'=> $datos['IdSolicitudCobertura'],
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
		
		return true;
	}


	protected function Modificar(array $datos): bool
	{
		$spnombre="upd_SolicitudesCoberturaInscriptos_xId";
		$sparam=array(
			'pIdSolicitudCobertura'=> $datos['IdSolicitudCobertura'],
			'pIdPersonaPuntaje'=> $datos['IdPersonaPuntaje'],
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
		$spnombre="del_SolicitudesCoberturaInscriptos_xId";
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
		$spnombre="upd_SolicitudesCoberturaInscriptos_Estado_xId";
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
	
	
	protected function eliminarxSolicitud(array $datos): bool
	{
		$spnombre="del_SolicitudesCoberturaDesempeno_xIdSolicitudCobertura";
		$sparam=array(
			'pIdSolicitudCobertura'=> $datos['IdSolicitudCobertura']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al eliminar por codigo. ");
			return false;
		}
		return true;
	}




}