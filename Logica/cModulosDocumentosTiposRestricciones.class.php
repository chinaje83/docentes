<?php 
include(DIR_CLASES_DB."cModulosDocumentosTiposRestricciones.db.php");

class cModulosDocumentosTiposRestricciones extends cModulosDocumentosTiposRestriccionesdb
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
	
	
	public function BuscarxIdDocumentoTipoModuloxIdDocumentoTipoModuloRestriccion($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdDocumentoTipoModuloxIdDocumentoTipoModuloRestriccion($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdRegistro'=> 0,
			'IdRegistro'=> "",
			'xIdDocumentoTipoModulo'=> 0,
			'IdDocumentoTipoModulo'=> "",
			'xIdDocumentoTipoModuloRestriccion'=> 0,
			'IdDocumentoTipoModuloRestriccion'=> "",
			'limit'=> '',
			'orderby'=> "IdRegistro DESC"
		);

		if(isset($datos['IdRegistro']) && $datos['IdRegistro']!="")
		{
			$sparam['IdRegistro']= $datos['IdRegistro'];
			$sparam['xIdRegistro']= 1;
		}
		if(isset($datos['IdDocumentoTipoModulo']) && $datos['IdDocumentoTipoModulo']!="")
		{
			$sparam['IdDocumentoTipoModulo']= $datos['IdDocumentoTipoModulo'];
			$sparam['xIdDocumentoTipoModulo']= 1;
		}
		if(isset($datos['IdDocumentoTipoModuloRestriccion']) && $datos['IdDocumentoTipoModuloRestriccion']!="")
		{
			$sparam['IdDocumentoTipoModuloRestriccion']= $datos['IdDocumentoTipoModuloRestriccion'];
			$sparam['xIdDocumentoTipoModuloRestriccion']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BusquedaAvanzadaxIdDocumentoTipoModulo($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'IdDocumentoTipoModulo'=> $datos['IdDocumentoTipoModulo'],
			'IdDocumentoTipoModuloRestriccion'=> $datos['IdDocumentoTipoModulo'],
			'xIdRegistro'=> 0,
			'IdRegistro'=> "",
			'limit'=> '',
			'orderby'=> "IdRegistro DESC"
		);

		if(isset($datos['IdRegistro']) && $datos['IdRegistro']!="")
		{
			$sparam['IdRegistro']= $datos['IdRegistro'];
			$sparam['xIdRegistro']= 1;
		}

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzadaxIdDocumentoTipoModulo($sparam,$resultado,$numfilas))
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
		if (!parent::Insertar($datos,$codigoinsertado))
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

		return true;
	}
	
	public function ModificarDescripcion($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una Descripción",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::ModificarDescripcion($datos))
			return false;

		return true;
	}
	



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		if (!parent::Eliminar($datos))
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
		
		if (!$this->BuscarxIdDocumentoTipoModuloxIdDocumentoTipoModuloRestriccion($datos,$resultado,$numfilas))
			return false;

		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el modulo ya fue agregado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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


		if (!isset($datos['IdDocumentoTipoModulo']) || $datos['IdDocumentoTipoModulo']=="")
			$datos['IdDocumentoTipoModulo']="NULL";

		if (!isset($datos['IdDocumentoTipoModuloRestriccion']) || $datos['IdDocumentoTipoModuloRestriccion']=="")
			$datos['IdDocumentoTipoModuloRestriccion']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

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


		if (!isset($datos['IdDocumentoTipoModulo']) || $datos['IdDocumentoTipoModulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Id Modulo Suna",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdDocumentoTipoModulo']) && $datos['IdDocumentoTipoModulo']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdDocumentoTipoModulo'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['IdDocumentoTipoModuloRestriccion']) || $datos['IdDocumentoTipoModuloRestriccion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Id Modulo Suna Restricción",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdDocumentoTipoModuloRestriccion']) && $datos['IdDocumentoTipoModuloRestriccion']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdDocumentoTipoModuloRestriccion'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una Descripción",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}





}
?>