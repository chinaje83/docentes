<?php 
include(DIR_CLASES_DB."cUsuariosMacrosDashboardZonas.db.php");

class cUsuariosMacrosDashboardZonas extends cUsuariosMacrosDashboardZonasdb
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

	public function BuscarZonasxMacros($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarZonasxMacros($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdZona'=> 0,
			'IdZona'=> "",
			'xIdUsuarioMacro'=> 0,
			'IdUsuarioMacro'=> "",
			'xIdEstructura'=> 0,
			'IdEstructura'=> "",
			'xIdMacro'=> 0,
			'IdMacro'=> "",
			'xIdUsuario'=> 0,
			'IdUsuario'=> "",
			'limit'=> '',
			'orderby'=> "IdZona DESC"
		);

		if(isset($datos['IdZona']) && $datos['IdZona']!="")
		{
			$sparam['IdZona']= $datos['IdZona'];
			$sparam['xIdZona']= 1;
		}
		if(isset($datos['IdUsuarioMacro']) && $datos['IdUsuarioMacro']!="")
		{
			$sparam['IdUsuarioMacro']= $datos['IdUsuarioMacro'];
			$sparam['xIdUsuarioMacro']= 1;
		}
		if(isset($datos['IdEstructura']) && $datos['IdEstructura']!="")
		{
			$sparam['IdEstructura']= $datos['IdEstructura'];
			$sparam['xIdEstructura']= 1;
		}
		if(isset($datos['IdMacro']) && $datos['IdMacro']!="")
		{
			$sparam['IdMacro']= $datos['IdMacro'];
			$sparam['xIdMacro']= 1;
		}
		if(isset($datos['IdUsuario']) && $datos['IdUsuario']!="")
		{
			$sparam['IdUsuario']= $datos['IdUsuario'];
			$sparam['xIdUsuario']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function UsuariosMacrosDashboardSP(&$spnombre,&$sparam)
	{
		if (!parent::UsuariosMacrosDashboardSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function UsuariosMacrosDashboardSPResult(&$resultado,&$numfilas)
	{
		if (!$this->UsuariosMacrosDashboardSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function MacrosDashboardEstructurasSP(&$spnombre,&$sparam)
	{
		if (!parent::MacrosDashboardEstructurasSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function MacrosDashboardEstructurasSPResult(&$resultado,&$numfilas)
	{
		if (!$this->MacrosDashboardEstructurasSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function MacrosDashboardSP(&$spnombre,&$sparam)
	{
		if (!parent::MacrosDashboardSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function MacrosDashboardSPResult(&$resultado,&$numfilas)
	{
		if (!$this->MacrosDashboardSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function UsuariosSP(&$spnombre,&$sparam)
	{
		if (!parent::UsuariosSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function UsuariosSPResult(&$resultado,&$numfilas)
	{
		if (!$this->UsuariosSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos))
			return false;

		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos))
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

		return true;
	}



	private function _ValidarModificar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		return true;
	}



	private function _ValidarEliminar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdUsuarioMacro']) || $datos['IdUsuarioMacro']=="")
			$datos['IdUsuarioMacro']="NULL";

		if (!isset($datos['IdEstructura']) || $datos['IdEstructura']=="")
			$datos['IdEstructura']="NULL";

		if (!isset($datos['IdMacro']) || $datos['IdMacro']=="")
			$datos['IdMacro']="NULL";

		if (!isset($datos['IdUsuario']) || $datos['IdUsuario']=="")
			$datos['IdUsuario']="NULL";

		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdUsuarioMacro']) || $datos['IdUsuarioMacro']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un codigo de usuario-macro",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdUsuarioMacro'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdEstructura']) || $datos['IdEstructura']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una estructura",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdEstructura'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdMacro']) || $datos['IdMacro']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un macro",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdMacro'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->conexion->TraerCampo('UsuariosMacrosDashboard','IdUsuarioMacro',array('IdUsuarioMacro='.$datos['IdUsuarioMacro']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->conexion->TraerCampo('MacrosDashboardEstructuras','IdEstructura',array('IdEstructura='.$datos['IdEstructura']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->conexion->TraerCampo('MacrosDashboard','IdMacro',array('IdMacro='.$datos['IdMacro']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>