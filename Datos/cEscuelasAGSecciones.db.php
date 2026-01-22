<?php 
abstract class cEscuelasAGSeccionesDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cEscuelasAGSeccionesDB.
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
	 * Destructor de la clase cEscuelasAGSeccionesDB.
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

    protected function getGrados(&$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_Grados";
        $sparam=array(
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }
	
	protected function BuscarxCodigo(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasAGSecciones_xIdSeccion";
		$sparam=array(
			'pIdSeccion'=> $datos['IdSeccion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

    protected function BuscarxIdEscuelaTurnoAnioGrado(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasAGSecciones_xIdEscuelaTurnoAnioGrado";
        $sparam=array(
            'pIdEscuelaTurnoAnioGrado'=> $datos['IdEscuelaTurnoAnioGrado'],
            'pEstado' => $datos['Estado']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


    protected function BuscarxSeccionesxIdEscuelaTurnoAnioGrado(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_Secciones_xIdPlanEducativo_xIdEscuelaTurnoAnioGrado_xIdEscuelaTurno";
        $sparam=array(
            'pIdEscuelaTurnoAnioGrado'=> $datos['IdEscuelaTurnoAnioGrado'],
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pIdPlanEducativo' => $datos['IdPlanEducativo']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function BuscarDatosCompletosxIdSeccion(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasAGSecciones_DatosCompletosxIdSeccion";
        $sparam=array(
            'pIdSeccion'=> $datos['IdSeccion']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


    protected function BuscarMateriasxGrado(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_PlanesEducativosAniosGrados_PlanesEducativosMaterias";
        $sparam=array(
            'pIdGradoAnio'=> $datos['IdGradoAnio'],
            'pIdPlanEducativo' => $datos['IdPlanEducativo']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


    protected function BuscarxIdEscuelaxIdCicloLectivo(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasAGSecciones_xIdEscuela_IdCicloLectivo";
        $sparam=array(
            'pIdEscuela'=> $datos['IdEscuela'],
            'pIdCicloLectivo'=> $datos['IdCicloLectivo'],
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function BuscarxIdEscuelaxIdCicloLectivoxIdNivelModalidadxIdTurnoxIdGradoAnio(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasAGSecciones_xIdEscuela_IdCicloLectivo_IdNivelModalidad_IdTurno_IdGradoAnio";
        $sparam=array(
            'pIdEscuela'=> $datos['IdEscuela'],
            'pIdNivelModalidad'=> $datos['IdNivelModalidad'],
            'pIdTurno'=> $datos['IdTurno'],
            'pIdGradoAnio'=> $datos['IdGradoAnio']

        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


    protected function BuscarxIdEscuelaTurnoAnioGradoxNombreSeccionxEstado(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasAGSecciones_xIdEscuelaTurnoAnioGrado_NombreSeccion_Estado";
        $sparam=array(
            'pIdEscuelaTurnoAnioGrado'=> $datos['IdEscuelaTurnoAnioGrado'],
            'pNombreSeccion'=> $datos['NombreSeccion'],
            'pEstado'=> $datos['Estado'],
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }




	protected function BusquedaAvanzada(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasAGSecciones_busqueda_avanzada";
		$sparam=array(
			'pxIdSeccion'=> $datos['xIdSeccion'],
			'pIdSeccion'=> $datos['IdSeccion'],
			'pxIdEscuelaTurnoAnioGrado'=> $datos['xIdEscuelaTurnoAnioGrado'],
			'pIdEscuelaTurnoAnioGrado'=> $datos['IdEscuelaTurnoAnioGrado'],
			'pxNombreSeccion'=> $datos['xNombreSeccion'],
			'pNombreSeccion'=> $datos['NombreSeccion'],
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado'],
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


	protected function BuscarAuditoriaRapida(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasAGSecciones_AuditoriaRapida";
		$sparam=array(
			'pIdSeccion'=> $datos['IdSeccion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function Insertar(array $datos, ?int &$codigoInsertado): bool
	{
		$spnombre="ins_EscuelasAGSecciones";
		$sparam=array(
			'pIdEscuelaTurnoAnioGrado'=> $datos['IdEscuelaTurnoAnioGrado'],
			'pNombreSeccion'=> $datos['NombreSeccion'],
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario']
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
		$spnombre="upd_EscuelasAGSecciones_xIdSeccion";
		$sparam=array(
			'pIdEscuelaTurnoAnioGrado'=> $datos['IdEscuelaTurnoAnioGrado'],
			'pNombreSeccion'=> $datos['NombreSeccion'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdSeccion'=> $datos['IdSeccion']
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
		$spnombre="del_EscuelasAGSecciones_xIdSeccion";
		$sparam=array(
			'pIdSeccion'=> $datos['IdSeccion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function ModificarEstado(array $datos): bool
	{
		$spnombre="upd_EscuelasAGSecciones_Estado_xIdSeccion";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pIdSeccion'=> $datos['IdSeccion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}




}