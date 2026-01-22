<?php 
abstract class cEscuelasTurnosDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cEscuelasTurnosDB.
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
	 * Destructor de la clase cEscuelasTurnosDB.
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

    protected function getTurnos( &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_Turnos";
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
		$spnombre="sel_EscuelasTurnos_xIdEscuelaTurno";
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

    protected function BuscarxIdEscuelaxIdNivelModalidadxIdTurnoxIdCicloLectivo(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasTurnos_xIdEscuela_IdNivelModalidad_IdTurno_IdCicloLectivo";
        $sparam=array(
            'pIdEscuela'=> $datos['IdEscuela'],
            'pIdNivelModalidad'=> $datos['IdNivelModalidad'],
            'pIdTurno'=> $datos['IdTurno']
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
        $spnombre="sel_EscuelasTurnos_xIdEscuela_IdNivelModalidad_IdCicloLectivo";
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

    protected function BuscarxIdEscuelaTurno(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasTurnos_Seccion_Grado_Ciclo_xIdEscuelaTurno";
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

    protected function BuscarDatosxIdEscuelaTurno(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasTurnos_Datos_xIdEscuelaTurno";
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

    protected function BuscarTurno(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasTurnos_Turno";
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

    protected function BuscarMaterias(array $datos,  &$resultado, ?int &$numfilas): bool
    {

        $spnombre="sel_EscuelasTurnos_Materias";
        $sparam=array(
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pIdEscuelaCiclo' => $datos['IdEscuelaCiclo'],
            'pIdEscuelaTurnoAnioGrado' => $datos['IdEscuelaTurnoAnioGrado'],
            'pIdPlanEducativo' => $datos['IdPlanEducativo']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function BuscarxIdEscuela(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasTurnos_xIdEscuela";
        $sparam=array(
            'pIdEscuela'=> $datos['IdEscuela']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function BuscarxIdEscuelaxIdTurno(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre = 'sel_EscuelasTurnos_xIdEscuela_xIdTurno';
        $sparam = [
            'pIdEscuela'=> $datos['IdEscuela'],
            'pIdTurno'=> $datos['IdTurno'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function BusquedaAvanzada(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasTurnos_busqueda_avanzada";
		$sparam=array(
			'pxIdEscuelaTurno'=> $datos['xIdEscuelaTurno'],
			'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
			'pxIdEscuela'=> $datos['xIdEscuela'],
			'pIdEscuela'=> $datos['IdEscuela'],
			'pxIdNivelModalidad'=> $datos['xIdNivelModalidad'],
			'pIdNivelModalidad'=> $datos['IdNivelModalidad'],
			'pxIdTurno'=> $datos['xIdTurno'],
			'pIdTurno'=> $datos['IdTurno'],
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
		$spnombre="sel_EscuelasTurnos_AuditoriaRapida";
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


	protected function Insertar(array $datos, ?int &$codigoInsertado): bool
	{
		$spnombre="ins_EscuelasTurnos";
		$sparam=array(
			'pIdEscuela'=> $datos['IdEscuela'],
			'pIdNivelModalidad'=> $datos['IdNivelModalidad'],
			'pIdTurno'=> $datos['IdTurno'],
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
		$spnombre="upd_EscuelasTurnos_xIdEscuelaTurno";
		$sparam=array(
			'pIdEscuela'=> $datos['IdEscuela'],
			'pIdNivelModalidad'=> $datos['IdNivelModalidad'],
			'pIdTurno'=> $datos['IdTurno'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdEscuelaTurno'=> $datos['IdEscuelaTurno']
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
		$spnombre="del_EscuelasTurnos_xIdEscuelaTurno";
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
		$spnombre="upd_EscuelasTurnos_Estado_xIdEscuelaTurno";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pIdEscuelaTurno'=> $datos['IdEscuelaTurno']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}




}