<?php 
abstract class cLocalidadesDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cLocalidadesDB.
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
	 * Destructor de la clase cLocalidadesDB.
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
	
	protected function ProvinciasSP(?string &$spnombre, ?array &$sparam): void
	{
		$spnombre = 'sel_Provincias_combo_Nombre';
		$sparam = [];
	}
	public abstract function ProvinciasSPResult( &$resultado, ?int &$numfilas): bool;
	


	protected function DepartamentosSP(?string &$spnombre, ?array &$sparam): void
	{
		$spnombre = 'sel_Departamentos_combo_Nombre';
		$sparam = [];
	}
	public abstract function DepartamentosSPResult( &$resultado, ?int &$numfilas): bool;
	


	protected function BuscarxCodigo(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_Localidades_xIdLocalidad";
		$sparam=array(
			'pIdLocalidad'=> $datos['IdLocalidad']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

    protected function BuscarxIdDepartamento(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_Localidades_xIdDepartamento";
        $sparam=array(
            'pIdDepartamento'=> $datos['IdDepartamento'],
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


	protected function BusquedaAvanzada(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_Localidades_busqueda_avanzada";
		$sparam=array(
			'pxIdLocalidad'=> $datos['xIdLocalidad'],
			'pIdLocalidad'=> $datos['IdLocalidad'],
			'pxIdProvincia'=> $datos['xIdProvincia'],
			'pIdProvincia'=> $datos['IdProvincia'],
			'pxIdDepartamento'=> $datos['xIdDepartamento'],
			'pIdDepartamento'=> $datos['IdDepartamento'],
			'pxIdMunicipio'=> $datos['xIdMunicipio'],
			'pIdMunicipio'=> $datos['IdMunicipio'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
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
		$spnombre="sel_Localidades_AuditoriaRapida";
		$sparam=array(
			'pIdLocalidad'=> $datos['IdLocalidad']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

    protected function BuscarxMunicipio(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_Localidades_xIdMunicipio";
        $sparam=array(
            'pIdMunicipio'=> $datos['IdMunicipio']
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
		$spnombre="ins_Localidades";
		$sparam=array(
			'pIdProvincia'=> $datos['IdProvincia'],
			'pIdDepartamento'=> $datos['IdDepartamento'],
			'pIdMunicipio' => $datos['IdMunicipio'],
			'pNombre'=> $datos['Nombre'],
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
		$spnombre="upd_Localidades_xIdLocalidad";
		$sparam=array(
			'pIdProvincia'=> $datos['IdProvincia'],
			'pIdDepartamento'=> $datos['IdDepartamento'],
			'pIdMunicipio' => $datos['IdMunicipio'],
			'pNombre'=> $datos['Nombre'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdLocalidad'=> $datos['IdLocalidad']
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
		$spnombre="del_Localidades_xIdLocalidad";
		$sparam=array(
			'pIdLocalidad'=> $datos['IdLocalidad']
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
		$spnombre="upd_Localidades_Estado_xIdLocalidad";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pIdLocalidad'=> $datos['IdLocalidad']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}




}