<?php 
abstract class cEscuelasCiclosDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cEscuelasCiclosDB.
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
	 * Destructor de la clase cEscuelasCiclosDB.
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
		$spnombre="sel_EscuelasCiclos_xIdEscuelaCiclo";
		$sparam=array(
			'pIdEscuelaCiclo'=> $datos['IdEscuelaCiclo']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

    protected function BuscarxIdEscuelaTurno(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasCiclos_xIdEscuelaTurno";
        $sparam=array(
            'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
            'pEstado' => $datos['Estado']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function BuscarxIdEscuelaTurnoxIdCiclo(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasCiclos_xIdEscuelaTurno_IdCiclo";
        $sparam=array(
            'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
            'pIdCiclo'=> $datos['IdCiclo'],
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function BuscarxIdEscuelaTurnoxIdCicloxIdOrientacion(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasCiclos_xIdEscuelaTurno_IdCiclo_IdOrientacion";
        $sparam=array(
            'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
            'pIdCiclo'=> $datos['IdCiclo'],
            'pIdOrientacion'=> $datos['IdOrientacion'],
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function BuscarxIdEscuelaxIdNivelModalidadxIdCicloLectivo(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasCiclos_xIdEscuela_IdNivelModalidad_IdCicloLectivo";
        $sparam=array(
            'pIdEscuela'=> $datos['IdEscuela'],
            'pIdNivelModalidad'=> $datos['IdNivelModalidad']

        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


    protected function BuscarCiclosxIdEscuelaTurno(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasCiclos_Ciclos_xIdEscuelaTurno";
        $sparam=array(
            'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


    protected function BuscarOrientacionesxIdEscuelaTurno(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasCiclos_Orientaciones_xIdEscuelaTurno";
        $sparam=array(
            'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


    protected function BuscarGradosAniosxIdEscuelaTurno(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasCiclos_GradosAnios_xIdEscuelaTurno";
        $sparam=array(
            'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }



    protected function BuscarSeccionesxIdEscuelaTurnoxIdGradoAnio(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasCiclos_Secciones_xIdEscuelaTurno_xIdGradoAnio";
        $sparam=array(
            'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
            'pIdGradoAnio' => $datos['IdGradoAnio']
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
		$spnombre="sel_EscuelasCiclos_busqueda_avanzada";
		$sparam=array(
			'pxIdEscuelaCiclo'=> $datos['xIdEscuelaCiclo'],
			'pIdEscuelaCiclo'=> $datos['IdEscuelaCiclo'],
			'pxIdEscuelaTurno'=> $datos['xIdEscuelaTurno'],
			'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
			'pxIdCiclo'=> $datos['xIdCiclo'],
			'pIdCiclo'=> $datos['IdCiclo'],
			'pxIdOrientacion'=> $datos['xIdOrientacion'],
			'pIdOrientacion'=> $datos['IdOrientacion'],
			'pxOrientacion'=> $datos['xOrientacion'],
			'pOrientacion'=> $datos['Orientacion'],
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
		$spnombre="sel_EscuelasCiclos_AuditoriaRapida";
		$sparam=array(
			'pIdEscuelaTurno'=> $datos['IdEscuelaTurno']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

    protected function BuscarExistente(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasCiclos_xIdEscuelaTurno_xIdCiclo_xIdPlanEducativo";
        $sparam=array(
            'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
            'pIdCiclo' => $datos['IdCiclo'],
            'pIdPlanEducativo' => $datos['IdPlanEducativo']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function BuscarxIdPlanEducativoxIdEscuela(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasCiclos_xIdPlanEducativo_xIdEscuela";
        $sparam=array(
            'pIdPlanEducativo' => $datos['IdPlanEducativo'],
            'pIdEscuela'=> $datos['IdEscuela']
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
		$spnombre="ins_EscuelasCiclos";
		$sparam=array(
			'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
			'pIdCiclo'=> $datos['IdCiclo'],
			'pIdOrientacion'=> $datos['IdOrientacion'],
			'pOrientacion'=> $datos['Orientacion'],
            'pIdPlanEducativo'=> $datos['IdPlanEducativo'],
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
		$spnombre="upd_EscuelasCiclos_xIdEscuelaTurno";
		$sparam=array(
            'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
			'pIdCiclo'=> $datos['IdCiclo'],
			'pIdOrientacion'=> $datos['IdOrientacion'],
			'pOrientacion'=> $datos['Orientacion'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pIdEscuelaCiclo'=> $datos['IdEscuelaCiclo']

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
		$spnombre="del_EscuelasCiclos_xIdEscuelaTurno";
		$sparam=array(
			'pIdEscuelaTurno'=> $datos['IdEscuelaTurno']
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
		$spnombre="upd_EscuelasCiclos_Estado_xIdEscuelaCiclo";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pIdEscuelaCiclo'=> $datos['IdEscuelaCiclo']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}




}