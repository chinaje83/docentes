<?php 
include(DIR_CLASES_DB."cClientesEmpresas.db.php");

class cClientesEmpresas extends cClientesEmpresasdb
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
	
	
	
	public function BuscarxIdClienteActivos($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdClienteActivos($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdClienteEmpresa'=> 0,
			'IdClienteEmpresa'=> "",
			'xIdCliente'=> 0,
			'IdCliente'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xNombreCorto'=> 0,
			'NombreCorto'=> "",
			'xCuit'=> 0,
			'Cuit'=> "",
			'xRazonSocial'=> 0,
			'RazonSocial'=> "",
			'xEmail'=> 0,
			'Email'=> "",
			'xTelefono'=> 0,
			'Telefono'=> "",
			'Estado' => "",
			'xEstado' => 0,
			'limit'=> '',
			'orderby'=> "IdClienteEmpresa DESC"
		);

		if(isset($datos['IdClienteEmpresa']) && $datos['IdClienteEmpresa']!="")
		{
			$sparam['IdClienteEmpresa']= $datos['IdClienteEmpresa'];
			$sparam['xIdClienteEmpresa']= 1;
		}
		if(isset($datos['IdCliente']) && $datos['IdCliente']!="")
		{
			$sparam['IdCliente']= $datos['IdCliente'];
			$sparam['xIdCliente']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['NombreCorto']) && $datos['NombreCorto']!="")
		{
			$sparam['NombreCorto']= $datos['NombreCorto'];
			$sparam['xNombreCorto']= 1;
		}
		if(isset($datos['Cuit']) && $datos['Cuit']!="")
		{
			$sparam['Cuit']= $datos['Cuit'];
			$sparam['xCuit']= 1;
		}
		if(isset($datos['RazonSocial']) && $datos['RazonSocial']!="")
		{
			$sparam['RazonSocial']= $datos['RazonSocial'];
			$sparam['xRazonSocial']= 1;
		}
		if(isset($datos['Email']) && $datos['Email']!="")
		{
			$sparam['Email']= $datos['Email'];
			$sparam['xEmail']= 1;
		}
		if(isset($datos['Telefono']) && $datos['Telefono']!="")
		{
			$sparam['Telefono']= $datos['Telefono'];
			$sparam['xTelefono']= 1;
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
			
			
		$oConexionWS = new cConexionWS($this->conexion);
		$oClientesWS = new cClientesWS($this->conexion, $oConexionWS->getCurl(), $oConexionWS->getToken());
		
		$datosBusqueda['IdCliente']=$datos['IdCliente'];
		$objClientes = $oClientesWS->BuscarClientesxCodigo ($datosBusqueda);
		
		$datos['ClienteNombre'] = $objClientes['Nombre'];	

		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		$oAuditoriasClientesEmpresas = new cAuditoriasClientesEmpresas($this->conexion,$this->formato);
		$datos['IdClienteEmpresa'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasClientesEmpresas->InsertarLog($datos,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;
			
		$oConexionWS = new cConexionWS($this->conexion);
		$oClientesWS = new cClientesWS($this->conexion, $oConexionWS->getCurl(), $oConexionWS->getToken());
		
		$datosBusqueda['IdCliente']=$datos['IdCliente'];
		$objClientes = $oClientesWS->BuscarClientesxCodigo ($datosBusqueda);
		
		$datos['ClienteNombre'] = $objClientes['Nombre'];		

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		$oAuditoriasClientesEmpresas = new cAuditoriasClientesEmpresas($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasClientesEmpresas->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasClientesEmpresas = new cAuditoriasClientesEmpresas($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasClientesEmpresas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		$datosmodif['IdClienteEmpresa'] = $datos['IdClienteEmpresa'];
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
		$datosmodif['IdClienteEmpresa'] = $datos['IdClienteEmpresa'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdClienteEmpresa'] = $datos['IdClienteEmpresa'];
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
		
		$datosbuscar['IdCliente'] = $datos['IdCliente'];
		$datosbuscar['Cuit'] = $datos['Cuit'];
		$datosbuscar['Estado'] = ACTIVO.",".NOACTIVO;
		if(!$this->BusquedaAvanzada($datosbuscar,$resultado,$numfilas))
			return false;
			
		if($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el cuit ya fue dado de alta",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
			
		$datosbuscar['IdCliente'] = $datos['IdCliente'];
		$datosbuscar['Cuit'] = $datos['Cuit'];
		$datosbuscar['Estado'] = ACTIVO.",".NOACTIVO;
		if(!$this->BusquedaAvanzada($datosbuscar,$resultado,$numfilas))
			return false;
			
		if($numfilas>0)
		{
			$fila = $this->conexion->ObtenerSiguienteRegistro($resultado);
			
			if($fila['IdClienteEmpresa']!=$datos['IdClienteEmpresa'])
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el cuit ya fue dado de alta",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdCliente']) || $datos['IdCliente']=="")
			$datos['IdCliente']="NULL";
			
		if (!isset($datos['ClienteNombre']) || $datos['ClienteNombre']=="")
			$datos['ClienteNombre']="NULL";	
			
		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['NombreCorto']) || $datos['NombreCorto']=="")
			$datos['NombreCorto']="NULL";

		if (!isset($datos['Cuit']) || $datos['Cuit']=="")
			$datos['Cuit']="NULL";

		if (!isset($datos['RazonSocial']) || $datos['RazonSocial']=="")
			$datos['RazonSocial']="NULL";

		if (!isset($datos['Email']) || $datos['Email']=="")
			$datos['Email']="NULL";

		if (!isset($datos['Telefono']) || $datos['Telefono']=="")
			$datos['Telefono']="NULL";

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


		if (!isset($datos['IdCliente']) || $datos['IdCliente']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un cliente",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdCliente'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['NombreCorto']) || $datos['NombreCorto']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre corto",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Cuit']) || $datos['Cuit']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un cuit",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (isset($datos['Cuit']) && $datos['Cuit']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Cuit'],"CUIT"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un cuit válido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['RazonSocial']) || $datos['RazonSocial']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una razón social",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		/*if (!isset($datos['Email']) || $datos['Email']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un email",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		
		if (isset($datos['Email']) && $datos['Email']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Email'],"Email"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un email válido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		/*if (!isset($datos['Telefono']) || $datos['Telefono']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un telefono",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		
		
		return true;
	}





}
?>