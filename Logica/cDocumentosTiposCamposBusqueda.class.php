<?php 
include(DIR_CLASES_DB."cDocumentosTiposCamposBusqueda.db.php");

class cDocumentosTiposCamposBusqueda extends cDocumentosTiposCamposBusquedadb
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

	public function BuscarxIdRegistroTipoDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdCampoBusqueda'=> 0,
			'IdCampoBusqueda'=> "",
			'xIdRegistroTipoDocumento'=> 0,
			'IdRegistroTipoDocumento'=> "",
			'xIdCampo'=> 0,
			'IdCampo'=> "",
			'xEnBuscador'=> 0,
			'EnBuscador'=> "",
			'xEnListado'=> 0,
			'EnListado'=> "",
			'limit'=> '',
			'orderby'=> "Orden ASC"
		);
		
		if(isset($datos['IdCampoBusqueda']) && $datos['IdCampoBusqueda']!="")
		{
			$sparam['IdCampoBusqueda']= $datos['IdCampoBusqueda'];
			$sparam['xIdCampoBusqueda']= 1;
		}
		
		if(isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento']!="")
		{
			$sparam['IdRegistroTipoDocumento']= $datos['IdRegistroTipoDocumento'];
			$sparam['xIdRegistroTipoDocumento']= 1;
		}
		
		if(isset($datos['IdCampo']) && $datos['IdCampo']!="")
		{
			$sparam['IdCampo']= $datos['IdCampo'];
			$sparam['xIdCampo']= 1;
		}
		
		if(isset($datos['EnBuscador']) && $datos['EnBuscador']!="")
		{
			$sparam['EnBuscador']= $datos['EnBuscador'];
			$sparam['xEnBuscador']= 1;
		}
		
		if(isset($datos['EnListado']) && $datos['EnListado']!="")
		{
			$sparam['EnListado']= $datos['EnListado'];
			$sparam['xEnListado']= 1;
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
		
		$this->ObtenerProximoOrden($datos,$proxorden);	
		$datos['Orden']= $proxorden;
		
		$this->_SetearNull($datos);
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datos['AltaFecha'] = date("Y-m-d H:i:s");
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
			
		$oAuditoriasDocumentosTiposCamposBusqueda = new cAuditoriasDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		$datos['IdCampoBusqueda'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasDocumentosTiposCamposBusqueda->InsertarLog($datos,$codigoInsertadolog))
			return false;
			
			

		return true;
	}
	
	
	public function Eliminar($datos)
	{
		
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasDocumentosTiposCamposBusqueda = new cAuditoriasDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosTiposCamposBusqueda->InsertarLog($datosLog,$codigoInsertadolog))
			return false;	
			

		if (!parent::Eliminar($datos))
			return false;
			

		return true;
	}
	
	
	public function EliminarxIdRegistroTipoDocumento($datos)
	{
		if(!$this->BuscarxIdRegistroTipoDocumento($datos,$resultado,$numfilas))
			return false;

		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			if (!$this->Eliminar($fila))
				return false;
		}
		return true;
	}
	
	
	public function ModificarDataJson($datos)
	{
		
		if (!$this->_ValidarModificarDataJson($datos,$datosRegistro))
			return false;
		$datos['DataJson'] = json_encode($datos);
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		if (!parent::ModificarDataJson($datos))
			return false;

		/*$oAuditoriasDocumentosTipos = new cAuditoriasDocumentosTipos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTipos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;*/
			
		$oAuditoriasDocumentosTiposCamposBusqueda = new cAuditoriasDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposCamposBusqueda->InsertarLog($datosLog,$codigoInsertadolog))
			return false;		
			

		return true;
	}
	
	
	
	
	
//----------------------------------------------------------------------------------------- 
//Retorna true o false si pudo cambiar el orden de las categorias

// Parámetros de Entrada:
//		catorden = orden de las categorias.

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no		
	public function ModificarOrdenCompleto($datos)
	{
				
		$datosmodif['Orden'] = 1;
		$datosmodif['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datosmodif['UltimaModificacionFecha']=  date("Y-m-d H:i:s");
		
		if (isset($datos['IdCampoBusqueda']) && count($datos['IdCampoBusqueda'])>0)
		{
			foreach ($datos['IdCampoBusqueda'] as $key=>$IdCampoBusqueda)
			{
				
				$datosmodif['IdCampoBusqueda'] = $IdCampoBusqueda;
				if (!$this->ModificarOrden($datosmodif))
						return false;
				$datosmodif['Orden']++;
			}
		}
		
		
		return true;
	}
	
	public function ActivarEnBuscador($datos)
	{
		$datosmodif['EnBuscador'] = 1;
		$datosmodif['IdCampoBusqueda'] = $datos['IdCampoBusqueda'];
		$datosmodif['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datosmodif['UltimaModificacionFecha']=  date("Y-m-d H:i:s");
		if (!parent::ModificarEnBuscador($datosmodif))
			return false;
			
			
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);	
			
			
		$oAuditoriasDocumentosTiposCamposBusqueda = new cAuditoriasDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposCamposBusqueda->InsertarLog($datosLog,$codigoInsertadolog))
			return false;		
		
		return true;	
	}
	
	
	public function DesactivarEnBuscador($datos)
	{
		
		$datosmodif['EnBuscador'] = 0;
		$datosmodif['IdCampoBusqueda'] = $datos['IdCampoBusqueda'];
		$datosmodif['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datosmodif['UltimaModificacionFecha']=  date("Y-m-d H:i:s");
		if (!parent::ModificarEnBuscador($datosmodif))
			return false;
			
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);	
			
			
		$oAuditoriasDocumentosTiposCamposBusqueda = new cAuditoriasDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposCamposBusqueda->InsertarLog($datosLog,$codigoInsertadolog))
			return false;		
			
		
		return true;	
	}
	
	
	public function ActivarEnListado($datos)
	{
		
		$datosmodif['EnListado'] = 1;
		$datosmodif['IdCampoBusqueda'] = $datos['IdCampoBusqueda'];
		$datosmodif['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datosmodif['UltimaModificacionFecha']=  date("Y-m-d H:i:s");
		if (!parent::ModificarEnListado($datosmodif))
			return false;
			
			
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);	
			
			
		$oAuditoriasDocumentosTiposCamposBusqueda = new cAuditoriasDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposCamposBusqueda->InsertarLog($datosLog,$codigoInsertadolog))
			return false;	
			
		
		return true;	
		
	}
	
	
	public function DesactivarEnListado($datos)
	{
		$datosmodif['EnListado'] = 0;
		$datosmodif['IdCampoBusqueda'] = $datos['IdCampoBusqueda'];
		$datosmodif['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datosmodif['UltimaModificacionFecha']=  date("Y-m-d H:i:s");
		if (!parent::ModificarEnListado($datosmodif))
			return false;
			
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);	
			
			
		$oAuditoriasDocumentosTiposCamposBusqueda = new cAuditoriasDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposCamposBusqueda->InsertarLog($datosLog,$codigoInsertadolog))
			return false;	
			
		
		return true;	
	}
	
	
	public function ActivarEnEncabezado($datos)
	{
		
		$datosmodif['EnEncabezado'] = 1;
		$datosmodif['IdCampoBusqueda'] = $datos['IdCampoBusqueda'];
		$datosmodif['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datosmodif['UltimaModificacionFecha']=  date("Y-m-d H:i:s");
		if (!parent::ModificarEnEncabezado($datosmodif))
			return false;
			
			
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);	
			
			
		$oAuditoriasDocumentosTiposCamposBusqueda = new cAuditoriasDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposCamposBusqueda->InsertarLog($datosLog,$codigoInsertadolog))
			return false;	
			
		
		return true;	
		
	}
	
	
	public function DesactivarEnEncabezado($datos)
	{
		$datosmodif['EnEncabezado'] = 0;
		$datosmodif['IdCampoBusqueda'] = $datos['IdCampoBusqueda'];
		$datosmodif['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datosmodif['UltimaModificacionFecha']=  date("Y-m-d H:i:s");
		if (!parent::ModificarEnEncabezado($datosmodif))
			return false;
			
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);	
			
			
		$oAuditoriasDocumentosTiposCamposBusqueda = new cAuditoriasDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposCamposBusqueda->InsertarLog($datosLog,$codigoInsertadolog))
			return false;	
			
		
		return true;	
	}
	
	


	public function ModificarOrden($datos)
	{
		$datosmodif['Orden'] = $datos['Orden'];
		$datosmodif['IdCampoBusqueda'] = $datos['IdCampoBusqueda'];
		$datosmodif['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datosmodif['UltimaModificacionFecha']=  date("Y-m-d H:i:s");
		if (!parent::ModificarOrden($datosmodif))
			return false;
			
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);	
			
			
		$oAuditoriasDocumentosTiposCamposBusqueda = new cAuditoriasDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposCamposBusqueda->InsertarLog($datosLog,$codigoInsertadolog))
			return false;	
			
		
		return true;	
	}
	
	
	
	
	
	
	
//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function ObtenerProximoOrden($datos,&$proxorden)
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

	
	
	
	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
			
		$datosbuscar['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
		$datosbuscar['IdCampo'] = $datos['IdCampo'];
		if(!$this->BusquedaAvanzada($datosbuscar,$resultado,$numfilas))
			return false;
		
		if($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el campo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
	
	
	private function _ValidarModificarDataJson($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		
		if(!isset($datos['CantidadColumnas']) || $datos['CantidadColumnas']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una cantidad de columnas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
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


		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
			$datos['IdRegistroTipoDocumento']="NULL";

		if (!isset($datos['IdCampo']) || $datos['IdCampo']=="")
			$datos['IdCampo']="NULL";
			
		
		if (!isset($datos['Orden']) || $datos['Orden']=="")
			$datos['Orden']="NULL";
			
		if (!isset($datos['DataJson']) || $datos['DataJson']=="")
			$datos['DataJson']="NULL";		
		
		if (!isset($datos['EnBuscador']) || $datos['EnBuscador']=="")
			$datos['EnBuscador']="1";
			
		if (!isset($datos['EnListado']) || $datos['EnListado']=="")
			$datos['EnListado']="1";		
			
		if (!isset($datos['EnEncabezado']) || $datos['EnEncabezado']=="")
			$datos['EnEncabezado']="1";		
			
		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['AltaApp']) || $datos['AltaApp']=="")
			$datos['AltaApp']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

		if (!isset($datos['UltimaModificacionApp']) || $datos['UltimaModificacionApp']=="")
			$datos['UltimaModificacionApp']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo de documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRegistroTipoDocumento'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdCampo']) || $datos['IdCampo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un campo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		

		if (!isset($datos['EnEncabezado']) || $datos['EnEncabezado']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe selecionar si se encuentra en el encabezado",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (intval($datos['EnEncabezado'])!==0 && intval($datos['EnEncabezado'])!==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe selecionar si se encuentra en el encabezado",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		

		if (!isset($datos['EnBuscador']) || $datos['EnBuscador']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe selecionar si se encuentra en el buscador",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (intval($datos['EnBuscador'])!==0 && intval($datos['EnBuscador'])!==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe selecionar si se encuentra en el buscador",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!isset($datos['EnListado']) || $datos['EnListado']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe selecionar si se encuentra en el listado",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (intval($datos['EnListado'])!==0 && intval($datos['EnListado'])!==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe selecionar si se encuentra en el listado",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		
		return true;
	}	


}
?>