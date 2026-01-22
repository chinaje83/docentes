<?php 
include(DIR_CLASES_DB."cAlertasVisualizaciones.db.php");

class cAlertasVisualizaciones extends cAlertasVisualizacionesdb
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
			'xIdAlerta'=> 0,
			'IdAlerta'=> "",
			'xIdUsuario'=> 0,
			'IdUsuario'=> "",
			'xFechaLectura'=> 0,
			'FechaLectura'=> "",
			'limit'=> '',
			'orderby'=> "IdAlertaVisualizacion DESC"
		);

		if(isset($datos['IdAlerta']) && $datos['IdAlerta']!="")
		{
			$sparam['IdAlerta']= $datos['IdAlerta'];
			$sparam['xIdAlerta']= 1;
		}
		if(isset($datos['IdUsuario']) && $datos['IdUsuario']!="")
		{
			$sparam['IdUsuario']= $datos['IdUsuario'];
			$sparam['xIdUsuario']= 1;
		}
		if(isset($datos['FechaLectura']) && $datos['FechaLectura']!="")
		{
			$sparam['FechaLectura']=FuncionesPHPLocal::ConvertirFecha( $datos['FechaLectura'],'dd/mm/aaaa','aaaa-mm-dd');
			$sparam['xFechaLectura']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function AlertasSP(&$spnombre,&$sparam)
	{
		if (!parent::AlertasSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function AlertasSPResult(&$resultado,&$numfilas)
	{
		if (!$this->AlertasSP($spnombre,$sparam))
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
		$Alertas = explode(",",$datos['IdAlerta']);		
		$user_agent = FuncionesPHPLocal::getBrowserSo($_SERVER['HTTP_USER_AGENT']);

		$datosIns['FechaLectura']= date("Y-m-d H:i:s");
		$datosIns['IP'] = FuncionesPHPLocal::HtmlspecialcharsSistema($_SERVER['REMOTE_ADDR'],ENT_QUOTES);
		$datosIns['Navegador'] = FuncionesPHPLocal::HtmlspecialcharsSistema("{$user_agent['name']} {$user_agent['version']}",ENT_QUOTES);
		$datosIns['SO'] = FuncionesPHPLocal::HtmlspecialcharsSistema($user_agent['platform'],ENT_QUOTES);
		$datosIns['IdUsuario'] = $_SESSION['usuariocod'];
		/*if (!$this->_ValidarInsertar($datos))
			return false;*/
		foreach($Alertas as $IdAlerta)
		{
			$datosIns['IdAlerta'] = $IdAlerta;
			$this->_SetearNull($datosIns);
			if (!parent::Insertar($datosIns,$codigoinsertado))
				return false;
		}
		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos))
			return false;

		$datos['FechaLectura']=FuncionesPHPLocal::ConvertirFecha( $datos['FechaLectura'],'dd/mm/aaaa','aaaa-mm-dd');
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


		if (!isset($datos['IdAlerta']) || $datos['IdAlerta']=="")
			$datos['IdAlerta']="NULL";

		if (!isset($datos['IdUsuario']) || $datos['IdUsuario']=="")
			$datos['IdUsuario']="NULL";

		if (!isset($datos['FechaLectura']) || $datos['FechaLectura']=="")
			$datos['FechaLectura']="NULL";

		if (!isset($datos['IP']) || $datos['IP']=="")
			$datos['IP']="NULL";

		if (!isset($datos['Navegador']) || $datos['Navegador']=="")
			$datos['Navegador']="NULL";

		if (!isset($datos['SO']) || $datos['SO']=="")
			$datos['SO']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdAlerta']) || $datos['IdAlerta']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una alerta",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdAlerta'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdUsuario']) || $datos['IdUsuario']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un usuario",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdUsuario'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['FechaLectura']) || $datos['FechaLectura']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una fecha de lectura",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		

		if (!isset($datos['IP']) || $datos['IP']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un IP",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Navegador']) || $datos['Navegador']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un navegador",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['SO']) || $datos['SO']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un sistema operativo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->conexion->TraerCampo('Alertas','IdAlerta',array('IdAlerta='.$datos['IdAlerta']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->conexion->TraerCampo('Usuarios','IdUsuario',array('IdUsuario='.$datos['IdUsuario']),$dato,$numfilas,$errno))
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