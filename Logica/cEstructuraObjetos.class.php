<?php 
include(DIR_CLASES_DB."cEstructuraObjetos.db.php");

class cEstructuraObjetos extends cEstructuraObjetosdb
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


	public function BuscarTiposObjetosCampos($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarTiposObjetosCampos($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdObjeto'=> 0,
			'IdObjeto'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xClase'=> 0,
			'Clase'=> "",
			'xMetodo'=> 0,
			'Metodo'=> "",
			'xTipoCampoEditable'=> 0,
			'TipoCampoEditable'=> "",
			'xTieneValores'=> 0,
			'TieneValores'=> "",
			'xArchivo'=> 0,
			'Archivo'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdObjeto DESC"
		);

		if(isset($datos['IdObjeto']) && $datos['IdObjeto']!="")
		{
			$sparam['IdObjeto']= $datos['IdObjeto'];
			$sparam['xIdObjeto']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['Clase']) && $datos['Clase']!="")
		{
			$sparam['Clase']= $datos['Clase'];
			$sparam['xClase']= 1;
		}
		if(isset($datos['Metodo']) && $datos['Metodo']!="")
		{
			$sparam['Metodo']= $datos['Metodo'];
			$sparam['xMetodo']= 1;
		}
		if(isset($datos['TipoCampoEditable']) && $datos['TipoCampoEditable']!="")
		{
			$sparam['TipoCampoEditable']= $datos['TipoCampoEditable'];
			$sparam['xTipoCampoEditable']= 1;
		}
		if(isset($datos['TieneValores']) && $datos['TieneValores']!="")
		{
			$sparam['TieneValores']= $datos['TieneValores'];
			$sparam['xTieneValores']= 1;
		}
		if(isset($datos['Archivo']) && $datos['Archivo']!="")
		{
			$sparam['Archivo']= $datos['Archivo'];
			$sparam['xArchivo']= 1;
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



	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datos['FechaAlta'] = date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		/*$oAuditoriasEstructuraObjetos = new cAuditoriasEstructuraObjetos($this->conexion,$this->formato);
		$datos['IdObjeto'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasEstructuraObjetos->InsertarLog($datos,$codigoInsertadolog))
			return false;*/

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

		/*$oAuditoriasEstructuraObjetos = new cAuditoriasEstructuraObjetos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEstructuraObjetos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;*/

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		/*$oAuditoriasEstructuraObjetos = new cAuditoriasEstructuraObjetos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasEstructuraObjetos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;*/

		$datosmodif['IdObjeto'] = $datos['IdObjeto'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function ModificarEstado($datos)
	{
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}



	public function Activar($datos)
	{
		$datosmodif['IdObjeto'] = $datos['IdObjeto'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdObjeto'] = $datos['IdObjeto'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
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



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['Clase']) || $datos['Clase']=="")
			$datos['Clase']="NULL";

		if (!isset($datos['Metodo']) || $datos['Metodo']=="")
			$datos['Metodo']="NULL";

		if (!isset($datos['TipoCampoEditable']) || $datos['TipoCampoEditable']=="")
			$datos['TipoCampoEditable']="NULL";

		if (!isset($datos['TieneValores']) || $datos['TieneValores']=="")
			$datos['TieneValores']="NULL";

		if (!isset($datos['Archivo']) || $datos['Archivo']=="")
			$datos['Archivo']="NULL";

		
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		/*if (!isset($datos['Clase']) || $datos['Clase']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una clase",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Metodo']) || $datos['Metodo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un metodo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
*/
		if (!isset($datos['TipoCampoEditable']) || $datos['TipoCampoEditable']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo campo editable",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['TieneValores']) || $datos['TieneValores']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tiene valores",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Archivo']) || $datos['Archivo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un archivo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		
		return true;
	}





}
?>