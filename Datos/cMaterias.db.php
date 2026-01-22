<?php
abstract class cMateriasDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cMateriasDB.
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
	 * Destructor de la clase cMateriasDB.
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
		$spnombre="sel_Materias_xIdMateria";
		$sparam=array(
			'pIdMateria'=> $datos['IdMateria']
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
		$spnombre="sel_Materias_busqueda_avanzada";
		$sparam=array(
			'pxSCParcial'=> $datos['xSCParcial'],
			'pSCParcial'=> $datos['SCParcial'],
			'pxPermiteSimultaneo'=> $datos['xPermiteSimultaneo'],
			'pPermiteSimultaneo'=> $datos['PermiteSimultaneo'],
			'pxIdMateria'=> $datos['xIdMateria'],
			'pIdMateria'=> $datos['IdMateria'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
            'pxCodigo'=> $datos['xCodigo'],
            'pCodigo'=> $datos['Codigo'],
            'pxIdMateriaPadre' => $datos["xIdMateriaPadre"],
            'pIdMateriaPadre' => $datos["IdMateriaPadre"],
            'pxIdModalidad' => $datos["xIdModalidad"],
            'pIdModalidad' => $datos["IdModalidad"],
            'pxTienePadre' => $datos["xTienePadre"],
            'pTienePadre' => $datos["TienePadre"],
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
		$spnombre="sel_Materias_AuditoriaRapida";
		$sparam=array(
			'pIdMateria'=> $datos['IdMateria']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


    public function BuscarCombo(&$resultado,&$numfilas): bool
    {
        $spnombre = "sel_Materias_combo";
        $sparam = array();
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    public function BuscarComboPadres(&$resultado,&$numfilas): bool
    {
        $spnombre = "sel_Materias_combo_padres";
        $sparam = array();
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

	protected function Insertar(array $datos, ?int &$codigoInsertado): bool
	{
		$spnombre="ins_Materias";
		$sparam=array(
			'pNombre'=> $datos['Nombre'],
			'pCodigo'=> $datos['Codigo'],
            'pIdExterno'=> $datos['IdExterno'],
            'pPermiteSimultaneo'=> $datos['PermiteSimultaneo'],
            'pSCParcial' => $datos['SCParcial'],
			'pEstado'=> $datos['Estado'],
            'pIdMateriaPadre' => $datos["IdMateriaPadre"],
            'pIdModalidad' => $datos["IdModalidad"],
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
		$spnombre="upd_Materias_xIdMateria";
		$sparam=array(
			'pNombre'=> $datos['Nombre'],
			'pCodigo'=> $datos['Codigo'],
            'pIdExterno'=> $datos['IdExterno'],
            'pPermiteSimultaneo'=> $datos['PermiteSimultaneo'],
            'pSCParcial' => $datos['SCParcial'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdMateria'=> $datos['IdMateria'],
            'pIdMateriaPadre' => $datos["IdMateriaPadre"],
            'pIdModalidad' => $datos["IdModalidad"]
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
		$spnombre="del_Materias_xIdMateria";
		$sparam=array(
			'pIdMateria'=> $datos['IdMateria']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function BuscarUltimoOrden(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_Materias_max_orden";
		$sparam=array();
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el maximo orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function ModificarOrden(array $datos): bool
	{
		$spnombre="upd_Materias_IdMateria_xIdMateria";
		$sparam=array(
			'pIdMateria'=> $datos['IdMateria'],
			'pIdMateria'=> $datos['IdMateria']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function ModificarEstado(array $datos): bool
	{
		$spnombre="upd_Materias_Estado_xIdMateria";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pIdMateria'=> $datos['IdMateria']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}




}
