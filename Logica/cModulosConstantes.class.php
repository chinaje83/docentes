<?php 
include(DIR_CLASES_DB."cModulosConstantes.db.php");

class cModulosConstantes extends cModulosConstantesdb
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
	
	
	public function BuscarxIdConstante($datos,&$resultado,&$numfilas)
	{		
		if (!parent::BuscarxIdConstante($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxIdConstanteVigenteDatosCompletos($datos,&$resultado,&$numfilas)
	{		
		if (!parent::BuscarxIdConstanteVigenteDatosCompletos($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarValidacionVigencia($datos,&$resultado,&$numfilas)
	{		
		if (!isset($datos['IdRegistro']))
			$datos['IdRegistro']="";
		$datos['xIdRegistro'] = 0;
		if (isset($datos['IdRegistro']) && $datos['IdRegistro']!="")
			$datos['xIdRegistro'] = 1;
		if (!isset($datos['VigenciaHasta']) || $datos['VigenciaHasta']=="")
			$datos['VigenciaHasta'] = "NULL";

		if (!parent::BuscarValidacionVigencia($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	
	
	
	public function BuscarModulosConstantesVigentes($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarModulosConstantesVigentes($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarModulosConstantesVigentesxIdConstante($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarModulosConstantesVigentesxIdConstante($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxIdModuloConstante($datos,&$resultado,&$numfilas)
	{		
		if (!parent::BuscarxIdModuloConstante($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxIdConstanteVigente($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['Anio']) && $datos['Anio']!="" && is_numeric($datos['Anio']))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) && $datos['Mes']!="" && is_numeric($datos['Mes']))
			$datos['Mes'] = date("m");

		$datos['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";
		
		
		if (!parent::BuscarxIdConstanteVigente($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BuscarAuditoriaRapidaxIdRegistro($datos,&$resultado,&$numfilas)
	{		
		if (!parent::BuscarAuditoriaRapidaxIdRegistro($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	
	
	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdModulo'=> 0,
			'IdModulo'=> '',
			'xIdConstante'=> 0,
			'IdConstante'=> '',
			'xConstante'=> 0,
			'Constante'=> '',
			'xDescripcion'=> 0,
			'Descripcion'=> '',
			'xValorConstante'=> 0,
			'ValorConstante'=> '',
			'xVigencia'=> 0,
			'Vigencia'=> '',
			'limit'=> '',
			'orderby'=> "IdRegistro DESC"
			);
		
		
		
		
		$sparam['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";
		$sparam['xVigencia']= 1;
		
		
		if(isset($datos['IdModulo']) && $datos['IdModulo']!="")
		{
			$sparam['IdModulo']= $datos['IdModulo'];
			$sparam['xIdModulo']= 1;
		}
		
		if(isset($datos['IdConstante']) && $datos['IdConstante']!="")
		{
			$sparam['IdConstante']= $datos['IdConstante'];
			$sparam['xIdConstante']= 1;
		}
		
		if(isset($datos['Constante']) && $datos['Constante']!="")
		{
			$sparam['Constante']= $datos['Constante'];
			$sparam['xConstante']= 1;
		}
		
		if(isset($datos['Descripcion']) && $datos['Descripcion']!="")
		{
			$sparam['Descripcion']= $datos['Descripcion'];
			$sparam['xDescripcion']= 1;
		}
		
		if(isset($datos['ValorConstante']) && $datos['ValorConstante']!="")
		{
			$sparam['ValorConstante']= $datos['ValorConstante'];
			$sparam['xValorConstante']= 1;
		}

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function Insertar($datos,&$codigoinsertado,&$IdConstante)
	{
		if (!cUsuariosPermisos::TienePermiso("001601")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para agregar un tipo de servicio.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		$datos['IdConstante'] = $this->ObtenerSiguienteIdConstante();
		$IdConstante = $datos['IdConstante'];
		$datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['AltaUsuario'] = $_SESSION['usuariocod'];
		$datos['AltaFecha'] = date("Y/m/d H:i:s");
		
		if (!parent::InsertarDB($datos,$codigoinsertado))
			return false;
			
		if(isset($datos['VigenciaHasta'])  && $datos['VigenciaHasta']=="NULL")	
			$datos['VigenciaHasta'] ="";
			
		$oAuditoriasModulosConstantes = new cAuditoriasModulosConstantes($this->conexion,$this->formato);
		$datos['IdRegistro'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasModulosConstantes->InsertarLog($datos,$codigoInsertado))
			return false;

		return true;
	}


	public function AgregarNuevaVigencia($datos,&$codigoinsertado,&$IdConstante)
	{
		if (!cUsuariosPermisos::TienePermiso("001602")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para agregar una nueva vigencia de un tipo de servicio.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarAgregarNuevaVigencia($datos,$datosRegistro))
			return false;

		$this->_SetearNull($datos);
		$IdConstante = $datos['IdConstante'];
		$datos['IdModulo'] = $datosRegistro['IdModulo'];
		$datos['Constante'] = $datosRegistro['Constante'];
		$datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['AltaUsuario'] = $_SESSION['usuariocod'];
		$datos['AltaFecha'] = date("Y/m/d H:i:s");
		if (!parent::InsertarDB($datos,$codigoinsertado))
			return false;
		
		$oAuditoriasModulosConstantes = new cAuditoriasModulosConstantes($this->conexion,$this->formato);
		$datos['IdRegistro'] = $codigoinsertado;
		$datos['Accion'] = INSERTARNUEVAVIGENCIA;
		if(!$oAuditoriasModulosConstantes->InsertarLog($datos,$codigoInsertado))
			return false;
			
		return true;
	}



	public function Modificar($datos)
	{
		if (!cUsuariosPermisos::TienePermiso("001602")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar los datos de un tipo de servicio.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$this->_SetearNull($datos);
		$datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

		if (!parent::Modificar($datos))
			return false;

		$oAuditoriasModulosConstantes = new cAuditoriasModulosConstantes($this->conexion,$this->formato);
		$datos['VigenciaDesde'] = $datosRegistro['VigenciaDesde'];
		$datos['VigenciaHasta'] = $datosRegistro['VigenciaHasta'];
		$datos['IdRegistro'] = $datosRegistro['IdRegistro'];
		$datos['Constante'] = $datosRegistro['Constante'];
		$datos['IdModulo'] = $datosRegistro['IdModulo'];
		$datos['Accion'] = MODIFICACION;
		$datos['AltaUsuario'] = $datosRegistro['AltaUsuario'];
		$datos['AltaFecha'] = $datosRegistro['AltaFecha'];
		if(!$oAuditoriasModulosConstantes->InsertarLog($datos,$codigoInsertado))
			return false;

		return true;
	}



	public function ModificarVigencia($datos)
	{
		if (!cUsuariosPermisos::TienePermiso("001602")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para modificar la vigencia de un tipo de servicio.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!$this->_ValidarModificarVigencia($datos,$datosRegistro))
			return false;

		$this->_SetearNull($datos);
		$datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

		if (!parent::ModificarVigencia($datos))
			return false;

		$oAuditoriasModulosConstantes = new cAuditoriasModulosConstantes($this->conexion,$this->formato);
		$datosRegistro['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
		$datosRegistro['UltimaModificacionUsuario'] = $datos['UltimaModificacionUsuario'];
		$datosLog = $datosRegistro;
		$datosLog['VigenciaDesde'] = $datos['VigenciaDesde'];
		$datosLog['VigenciaHasta'] = $datos['VigenciaHasta'];
		$datosLog['Accion'] = MODIFICARVIGENCIA;
		if(!$oAuditoriasModulosConstantes->InsertarLog($datosLog,$codigoInsertado))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		
		if (!cUsuariosPermisos::TienePermiso("001603")){
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, usted no tiene permisos para eliminar un tipo de servicio.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasModulosConstantes = new cAuditoriasModulosConstantes($this->conexion,$this->formato);
		$datosLog = $datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasModulosConstantes->InsertarLog($datosLog,$codigoInsertado))
			return false;
		
		if (!parent::Eliminar($datos))
			return false;

		return true;
	}


//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function ObtenerSiguienteIdConstante()
	{
		$idSiguiente = 0;
		if (!parent::BuscarUltimoIdConstante($resultado,$numfilas))
			return false;
		
		if ($numfilas>0)
		{
			$datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$idSiguiente = $datos['UltimoIdConstante'];	
			
		}	
		$idSiguiente = $idSiguiente+1;
			
		return $idSiguiente;	
	}




	private function _ValidarInsertar(&$datos)
	{
		
		if (!isset($datos['IdModulo']) || $datos['IdModulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un modulo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		
		if (!isset($datos['Constante']) || $datos['Constante']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una constante",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		if (!$this->_ValidarVigencia($datos))
			return false;

		return true;
	}



	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un codigo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		return true;
	}



	private function _ValidarModificarVigencia(&$datos,&$datosRegistro)
	{
			
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un codigo de empresa valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarVigencia($datos))
			return false;
			
		$datosBusqueda['IdConstante'] = $datosRegistro['IdConstante'];
		$datosBusqueda['IdRegistro'] = $datosRegistro['IdRegistro'];
		$datosBusqueda['VigenciaDesde'] = $datos['VigenciaDesde'];
		$datosBusqueda['VigenciaHasta'] = $datos['VigenciaHasta'];
		if (!$this->BuscarValidacionVigencia($datosBusqueda,$resultado,$numfilas))
			return false;
			
		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe una vigencia del mismo tipo de servicio.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}		

		return true;
	}

	private function _ValidarAgregarNuevaVigencia(&$datos,&$datosRegistro)
	{
		if (!isset($datos['IdConstante']) || $datos['IdConstante']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una empresa",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdConstante'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo de codigo Empresa.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		if (!$this->BuscarxIdConstante($datos,$resultado,$numfilas))
			return false;

		if ($numfilas==0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un codigo de empresa valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarVigencia($datos))
			return false;
			
		$datosBusqueda['IdConstante'] = $datosRegistro['IdConstante'];
		$datosBusqueda['VigenciaDesde'] = $datos['VigenciaDesde'];
		$datosBusqueda['VigenciaHasta'] = $datos['VigenciaHasta'];
		if (!$this->BuscarValidacionVigencia($datosBusqueda,$resultado,$numfilas))
			return false;
			
		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe una vigencia del mismo tipo de servicio.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un codigo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}



	private function _SetearNull(&$datos)
	{

		if (!isset($datos['IdConstante']) || $datos['IdConstante']=="")
			$datos['IdConstante']="NULL";

		if (!isset($datos['IdModulo']) || $datos['IdModulo']=="")
			$datos['IdModulo']="NULL";	
		
		if (!isset($datos['Constante']) || $datos['Constante']=="")
			$datos['Constante']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";
			
		if (!isset($datos['ValorConstante']) || $datos['ValorConstante']=="")
			$datos['ValorConstante']="NULL";
			
		if (!isset($datos['VigenciaHasta']) || $datos['VigenciaHasta']=="")
			$datos['VigenciaHasta']="NULL";

		if (!isset($datos['RegistroSeguridad']) || $datos['RegistroSeguridad']=="")
			$datos['RegistroSeguridad']="NULL";


		return true;
	}



	private function _ValidarDatosVacios($datos)
	{

	
		/*if (!isset($datos['IdModulo']) || $datos['IdModulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un modulo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (isset($datos['IdModulo']) && $datos['IdModulo']!="")
		{
			$oModulos = new cModulos($this->conexion,$this->formato);
			
			
			if(!$oMediosTransporte->Buscar($datos,$resultado,$numfilas))
				return false;
				
			if($numfilas!=1)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un medio de transporte valido",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}	
		}
		
		if (!isset($datos['Constante']) || $datos['Constante']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una constante",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		
		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripcion",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!isset($datos['ValorConstante']) || $datos['ValorConstante']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un valor",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (isset($datos['ValorConstante']) && $datos['ValorConstante']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un valor de la constante.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		
		
		
		return true;
	}




	private function _ValidarVigencia(&$datos)
	{

		if (!isset($datos['VigenciaDesde']) || $datos['VigenciaDesde']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una vigencia desde",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		$datos['VigenciaDesde'] = "01/".$datos['VigenciaDesde'];
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['VigenciaDesde'],"FechaDDMMAAAA"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo fecha (Desde) valido (mm/aaaa).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datos['VigenciaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['VigenciaDesde'],"dd/mm/aaaa","aaaammdd");

		if ($datos['VigenciaHasta']!="")
		{
			
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,"15/".$datos['VigenciaHasta'],"FechaDDMMAAAA"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo fecha (Hasta) valido (mm/aaaa).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (isset($datos['VigenciaHasta']) && $datos['VigenciaHasta']!="")
			{
				$dia = FuncionesPHPLocal::ObtenerUltimoDiaMes(substr($datos['VigenciaHasta'],3,4),substr($datos['VigenciaHasta'],0,2));
				$datos['VigenciaHasta'] = $dia."/".$datos['VigenciaHasta'];
			}
	
			$datos['VigenciaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['VigenciaHasta'],"dd/mm/aaaa","aaaammdd");
		}

		if (!isset($_SESSION['Anio']) || $_SESSION['Anio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe seleccionar un año (barra periodos).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!isset($_SESSION['Mes']) || $_SESSION['Mes']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe seleccionar un mes (barra periodos).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$anioMesInicio = $_SESSION['Anio'].str_pad($_SESSION['Mes'],2,"0")."01";
		$anioMesFin = $_SESSION['Anio'].str_pad($_SESSION['Mes'],2,"0").FuncionesPHPLocal::ObtenerUltimoDiaMes($_SESSION['Anio'],$_SESSION['Mes']);
		
		if ($datos['VigenciaHasta']!="")
		{
			if ($datos['VigenciaDesde']>$datos['VigenciaHasta'])
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, la vigencia (desde) debe ser menor que la vigencia (Hasta).",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if ($datos['VigenciaHasta']<$anioMesFin)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, la vigencia (hasta) debe encontrarse dentro del periodo seleccionado en la barra superior.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if ($datos['VigenciaDesde']>$anioMesInicio)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, la vigencia (desde) debe encontrarse dentro del periodo seleccionado en la barra superior.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		
		return true;
	}

}
?>