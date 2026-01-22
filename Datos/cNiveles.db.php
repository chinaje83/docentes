<?php 
abstract class cNivelesDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cNivelesDB.
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
	 * Destructor de la clase cNivelesDB.
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

    /**
     * @param string|null $spnombre
     * @param array|null $sparam
     * @return bool
     */
    protected function buscarCombo(&$resultado, &$numfilas): bool {
        $spnombre = 'sel_Niveles_combo_Nombre';
        $sparam = [];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) ) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Niveles_xIdNivel";
		$sparam=array(
			'pIdNivel'=> $datos['IdNivel']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Niveles_busqueda_avanzada";
		$sparam=array(
			'pxIdNivel'=> $datos['xIdNivel'],
			'pIdNivel'=> $datos['IdNivel'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxIdNivelExterno'=> $datos['xIdNivelExterno'],
			'pIdNivelExterno'=> $datos['IdNivelExterno'],
			'pxCodigo'=> $datos['xCodigo'],
			'pCodigo'=> $datos['Codigo'],
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


	protected function CiclosCamposSP(&$spnombre,&$sparam)
    {
        $spnombre="sel_Niveles_Ciclos_nombre";
        $sparam=array(
        );
        return true;
    }


    protected function EstructurasCamposSP(&$spnombre,&$sparam)
    {
        $spnombre="sel_Niveles_Estructuras_nombre";
        $sparam=array(
        );
        return true;
    }


    protected function GradosAniosCamposSP(&$spnombre,&$sparam)
    {
        $spnombre="sel_Niveles_GradosAnios_nombre";
        $sparam=array(
        );
        return true;
    }


	protected function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Niveles_AuditoriaRapida";
		$sparam=array(
			'pIdNivel'=> $datos['IdNivel']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


    protected function BuscarEstructurasxId($datos, &$resultado,&$numfilas)
    {
        $spnombre="sel_Niveles_Estructuras_xId";
        $sparam=array(
            'pIdNivel' => $datos['IdNivel'],
            'pIdCiclo' => $datos['IdCiclo'],
            'pIdEstructura' => $datos['IdEstructura'],
            'pIdGradoAnio' => $datos['IdGradoAnio'],
            'pEstado' => $datos['Estado']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE, "Error al buscar estructuras.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function BuscarEstructurasxIdNivel($datos, &$resultado,&$numfilas)
    {
        $spnombre="sel_Niveles_Estructuras_xIdNivel";
        $sparam=array(
            'pIdNivel' => $datos['IdNivel']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE, "Error al buscar estructuras.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_Niveles";
		$sparam=array(
			'pNombre'=> $datos['Nombre'],
			'pIdNivelExterno'=> $datos['IdNivelExterno'],
			'pCodigo'=> $datos['Codigo'],
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();
		return true;
	}


	protected function Modificar($datos)
	{
		$spnombre="upd_Niveles_xIdNivel";
		$sparam=array(
			'pNombre'=> $datos['Nombre'],
			'pIdNivelExterno'=> $datos['IdNivelExterno'],
			'pCodigo'=> $datos['Codigo'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
			'pIdNivel'=> $datos['IdNivel']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function Eliminar($datos)
	{
		$spnombre="del_Niveles_xIdNivel";
		$sparam=array(
			'pIdNivel'=> $datos['IdNivel']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function ModificarEstado($datos)
	{
		$spnombre="upd_Niveles_Estado_xIdNivel";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pIdNivel'=> $datos['IdNivel']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


    protected function EliminarEstructuraxIdNivel($datos)
    {
        $spnombre="del_Niveles_Estructuras_xIdNivel";
        $sparam=array(
            'pIdNivel' => $datos['IdNivel']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Error al eliminar estructura por código."),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


    protected function ModificarEstadoEstructura($datos)
    {
        $spnombre="upd_Niveles_Estructuras_xId";
        $sparam=array(
            'pEstado' => $datos['Estado'],
            'pIdNivel' => $datos['IdNivel'],
            'pIdCicloNivelAnioGrado' => $datos['IdCicloNivelAnioGrado']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Error al eliminar estructura por código."),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

}
