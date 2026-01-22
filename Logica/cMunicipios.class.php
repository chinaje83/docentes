<?php 
include(DIR_CLASES_DB."cMunicipios.db.php");
class cMunicipios extends cMunicipiosdb
{
	/**
	 * Constructor de la clase cMunicipios.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion,$formato=FMT_TEXTO){
		parent::__construct($conexion,$formato);
	}
	/**
	 * Destructor de la clase cMunicipios.
	 */
	function __destruct(){
		parent::__destruct();
	}
	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public function getError(): array {
		return $this->error;
	}
	
	public function BuscarxCodigo($datos, &$resultado,&$numfilas): bool
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xIdProvincia'=> 0,
			'IdProvincia'=> "",
			'xIdDepartamento'=> 0,
			'IdDepartamento'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdMunicipio ASC"
		);
		if(isset($datos['IdProvincia']) && $datos['IdProvincia']!="")
		{
			$sparam['IdProvincia']= $datos['IdProvincia'];
			$sparam['xIdProvincia']= 1;
		}
		if(isset($datos['IdDepartamento']) && $datos['IdDepartamento']!="")
		{
			$sparam['IdDepartamento']= $datos['IdDepartamento'];
			$sparam['xIdDepartamento']= 1;
		}
		if(isset($datos['Estado']) && $datos['Estado']!="")
		{
			$sparam['Estado']= $datos['Estado'];
			$sparam['xEstado']= 1;
		}

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];
		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas): bool
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}


    public function BuscarxIdProvincia($datos,&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdProvincia($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarCombo(&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarCombo($resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxDepartamento($datos,&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxDepartamento($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function Insertar($datos,&$codigoInsertado): bool
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);
		$datos['UltimaModificacionUsuario'] =  $datos['AltaUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = $datos['AltaFecha'] = date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoInsertado))
			return false;
		$oAuditoriasMunicipios = new cAuditoriasMunicipios($this->conexion,$this->formato);
		$datos['IdMunicipio'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		if(!$oAuditoriasMunicipios->InsertarLog($datos,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Modificar($datos): bool
	{
        if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasMunicipios = new cAuditoriasMunicipios($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasMunicipios->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasMunicipios = new cAuditoriasMunicipios($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasMunicipios->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdMunicipio'] = $datos['IdMunicipio'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}


	public function ModificarEstado($datos): bool
	{
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}


	public function Activar(array $datos): bool
	{
		$datosmodif['IdMunicipio'] = $datos['IdMunicipio'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasMunicipios = new cAuditoriasMunicipios($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasMunicipios->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdMunicipio'] = $datos['IdMunicipio'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasMunicipios = new cAuditoriasMunicipios($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasMunicipios->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function ModificarOrdenCompleto($datos): bool
	{
		$datosmodif['IdMunicipio'] = 1;
		$arregloOrden = explode(",",$datos['orden']);
		foreach ($arregloOrden as $IdMunicipio){
			$datosmodif['IdMunicipio'] = $IdMunicipio;
			if (!parent::ModificarOrden($datosmodif))
				return false;
			$datosmodif['IdMunicipio']++;
		}
		return true;
	}


	private function ObtenerProximoOrden(array $datos, ?int &$proxorden): bool
	{
		$proxorden = 0;
		if (!parent::BuscarUltimoOrden($datos,$resultado,$numfilas))
			return false;
		if ($numfilas!=0){
			$datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$proxorden = $datos['maximo'] + 1;
		}
		return true;
	}




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}


	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}


	private function _ValidarEliminar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}


	private function _SetearNull(&$datos): void
	{
		if (!isset($datos['IdProvincia']) || $datos['IdProvincia']=="")
			$datos['IdProvincia']="NULL";

		if (!isset($datos['IdDepartamento']) || $datos['IdDepartamento']=="")
			$datos['IdDepartamento']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

		if (!isset($datos['esImportante']) || $datos['esImportante']=="")
			$datos['esImportante']=0;

		if (!isset($datos['CampoEditable']) || $datos['CampoEditable']=="")
			$datos['CampoEditable']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
	}


	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdProvincia']) || $datos['IdProvincia']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar una provincia",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdProvincia']) && $datos['IdProvincia']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdProvincia'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo Provincia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdProvincia'])>10)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Provincia no puede ser mayor a 10 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

	/*	if (!isset($datos['IdDepartamento']) || $datos['IdDepartamento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar un departamento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		if (isset($datos['IdDepartamento']) && $datos['IdDepartamento']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdDepartamento'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo Departamento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdDepartamento'])>10)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Departamento no puede ser mayor a 10 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		/*
		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripci�n",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		*/
		if (isset($datos['Descripcion']) && $datos['Descripcion']!="")
		{
			if (strlen($datos['Descripcion'])>100)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Descripci�n no puede ser mayor a 100 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		/*
		if (!isset($datos['esImportante']) || $datos['esImportante']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un valor a esImportante",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		*/
		if (isset($datos['esImportante']) && $datos['esImportante']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['esImportante'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['esImportante'])>10)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo  no puede ser mayor a 10 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		/*
		if (!isset($datos['CampoEditable']) || $datos['CampoEditable']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un valor a CampoEditable",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		*/
		if (isset($datos['CampoEditable']) && $datos['CampoEditable']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['CampoEditable'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['CampoEditable'])>2)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo  no puede ser mayor a 2 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		return true;
	}




}