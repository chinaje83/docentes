<?php 
include(DIR_CLASES_DB."cRamas.db.php");

class cRamas extends cRamasdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdRama'=> 0,
			'IdRama'=> "",
			'xIdRamaExterno'=> 0,
			'IdRamaExterno'=> "",
			'xCodigo'=> 0,
			'Codigo'=> "",
			'xDescripcion'=> 0,
			'Descripcion'=> "",
			'xIdNivelExterno'=> 0,
			'IdNivelExterno'=> "",
			'xIdModalidadExterno'=> 0,
			'IdModalidadExterno'=> "",
			'xIdEnsenanzaExterno'=> 0,
			'IdEnsenanzaExterno'=> "",
			'xIdDependenciaFuncionalExterno'=> 0,
			'IdDependenciaFuncionalExterno'=> "",
			'xAuditoriaTitulo'=> 0,
			'AuditoriaTitulo'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdRama DESC"
		);

		if(isset($datos['IdRama']) && $datos['IdRama']!="")
		{
			$sparam['IdRama']= $datos['IdRama'];
			$sparam['xIdRama']= 1;
		}
		if(isset($datos['IdRamaExterno']) && $datos['IdRamaExterno']!="")
		{
			$sparam['IdRamaExterno']= $datos['IdRamaExterno'];
			$sparam['xIdRamaExterno']= 1;
		}
		if(isset($datos['Codigo']) && $datos['Codigo']!="")
		{
			$sparam['Codigo']= $datos['Codigo'];
			$sparam['xCodigo']= 1;
		}
		if(isset($datos['Descripcion']) && $datos['Descripcion']!="")
		{
			$sparam['Descripcion']= $datos['Descripcion'];
			$sparam['xDescripcion']= 1;
		}
		if(isset($datos['IdNivelExterno']) && $datos['IdNivelExterno']!="")
		{
			$sparam['IdNivelExterno']= $datos['IdNivelExterno'];
			$sparam['xIdNivelExterno']= 1;
		}
		if(isset($datos['IdModalidadExterno']) && $datos['IdModalidadExterno']!="")
		{
			$sparam['IdModalidadExterno']= $datos['IdModalidadExterno'];
			$sparam['xIdModalidadExterno']= 1;
		}
		if(isset($datos['IdEnsenanzaExterno']) && $datos['IdEnsenanzaExterno']!="")
		{
			$sparam['IdEnsenanzaExterno']= $datos['IdEnsenanzaExterno'];
			$sparam['xIdEnsenanzaExterno']= 1;
		}
		if(isset($datos['IdDependenciaFuncionalExterno']) && $datos['IdDependenciaFuncionalExterno']!="")
		{
			$sparam['IdDependenciaFuncionalExterno']= $datos['IdDependenciaFuncionalExterno'];
			$sparam['xIdDependenciaFuncionalExterno']= 1;
		}
		if(isset($datos['AuditoriaTitulo']) && $datos['AuditoriaTitulo']!="")
		{
			$sparam['AuditoriaTitulo']= $datos['AuditoriaTitulo'];
			$sparam['xAuditoriaTitulo']= 1;
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



	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		$oAuditoriasRamas = new cAuditoriasRamas($this->conexion,$this->formato);
		$datos['IdRama'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasRamas->InsertarLog($datos,$codigoInsertadolog))
			return false;

		$datos['IdRama'] =$codigoinsertado;
		if (!$this->Publicar($datos))
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		$oAuditoriasRamas = new cAuditoriasRamas($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasRamas->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		if (!$this->Publicar($datos))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasRamas = new cAuditoriasRamas($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasRamas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		$datosmodif['IdRama'] = $datos['IdRama'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function ModificarEstado($datos)
	{
		if (!parent::ModificarEstado($datos))
			return false;
		if (!$this->Publicar($datos))
			return false;

		return true;
	}



	public function Activar($datos)
	{
		$datosmodif['IdRama'] = $datos['IdRama'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdRama'] = $datos['IdRama'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function Publicar($datos)
	{
		if (!$this->PublicarListadoJson())
			return false;
		return true;
	}



	public function GuardarDatosJson($nombrearchivo,$carpeta,$array)
	{
		$datosJson = FuncionesPHPLocal::ConvertiraUtf8($array);
		$jsonData = json_encode($datosJson);
		if(!is_dir($carpeta)){
			@mkdir($carpeta);
		}
		if(!FuncionesPHPLocal::GuardarArchivo($carpeta,$jsonData,$nombrearchivo.".json"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_INF,"Error, al generar el archivo json. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



	public function EliminarDatosJson($nombrearchivo,$carpeta)
	{
		if(file_exists($carpeta.$nombrearchivo.".json"))
		{
			unlink($carpeta.$nombrearchivo.".json");
		}
		return true;
	}



	public function PublicarListadoJson()
	{
		$nombrearchivo = "ram_ramas";
		$carpeta = PUBLICA."json/";
		if(!$this->GerenarArrayDatosJsonListado($array))
			return false;
		if(count($array)>0)
		{
			if(!$this->GuardarDatosJson($nombrearchivo,$carpeta,$array))
				return false;
		}
		else
		{
			if(!$this->EliminarDatosJson($nombrearchivo,$carpeta))
				return false;
		}
		return true;
	}



	public function GerenarArrayDatosJsonListado(&$array)
	{
		$array = array();
		$datos['Estado'] = ACTIVO;
		if(!$this->BusquedaAvanzada($datos,$resultados,$numfilas))
			return false;
		if($numfilas>0)
		{
			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultados))
			{
				$array[$fila['IdRama']] = $fila;
			}
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
		
		$datosbuscar['IdRamaExterno'] = $datos['IdRamaExterno'];	
		if(!$this->BusquedaAvanzada($datosbuscar,$resultado,$numfilas))
			return false;	
			
		if($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el id rama.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}	
		return true;
	}



	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c�digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		
		$datosbuscar['IdRamaExterno'] = $datos['IdRamaExterno'];	
		if(!$this->BusquedaAvanzada($datosbuscar,$resultado,$numfilas))
			return false;	
			
		if($numfilas>0)
		{
			$fila = $this->conexion->ObtenerSiguienteRegistro($resultado);
			if($fila['IdRama']!=$datosRegistro['IdRama'])
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el id rama.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}	
		return true;
	}



	private function _ValidarEliminar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c�digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdRamaExterno']) || $datos['IdRamaExterno']=="")
			$datos['IdRamaExterno']="NULL";

		if (!isset($datos['Codigo']) || $datos['Codigo']=="")
			$datos['Codigo']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

		if (!isset($datos['IdNivelExterno']) || $datos['IdNivelExterno']=="")
			$datos['IdNivelExterno']="NULL";

		if (!isset($datos['IdModalidadExterno']) || $datos['IdModalidadExterno']=="")
			$datos['IdModalidadExterno']="NULL";

		if (!isset($datos['IdEnsenanzaExterno']) || $datos['IdEnsenanzaExterno']=="")
			$datos['IdEnsenanzaExterno']="NULL";

		if (!isset($datos['IdDependenciaFuncionalExterno']) || $datos['IdDependenciaFuncionalExterno']=="")
			$datos['IdDependenciaFuncionalExterno']="NULL";

		if (!isset($datos['AuditoriaTitulo']) || $datos['AuditoriaTitulo']=="")
			$datos['AuditoriaTitulo']="NULL";

		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdRamaExterno']) || $datos['IdRamaExterno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id rama externo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRamaExterno'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Codigo']) || $datos['Codigo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un c�digo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripci�n",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		/*if (!isset($datos['IdNivelExterno']) || $datos['IdNivelExterno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id nivel Externo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		if (!isset($datos['IdNivelExterno']) && $datos['IdNivelExterno']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdNivelExterno'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		/*if (!isset($datos['IdModalidadExterno']) || $datos['IdModalidadExterno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id modalidad Externo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		if (!isset($datos['IdModalidadExterno']) && $datos['IdModalidadExterno']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdModalidadExterno'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		/*if (!isset($datos['IdEnsenanzaExterno']) || $datos['IdEnsenanzaExterno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id ense�anza externo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		if (!isset($datos['IdEnsenanzaExterno']) && $datos['IdEnsenanzaExterno']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdEnsenanzaExterno'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		/*if (!isset($datos['IdDependenciaFuncionalExterno']) || $datos['IdDependenciaFuncionalExterno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id dependencia funcional externo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		if (!isset($datos['IdDependenciaFuncionalExterno']) && $datos['IdDependenciaFuncionalExterno']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdDependenciaFuncionalExterno'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		if (!isset($datos['AuditoriaTitulo']) || $datos['AuditoriaTitulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe  seleccionar si auditoria titulo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['AuditoriaTitulo'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}





}
?>