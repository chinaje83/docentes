<?php 
include(DIR_CLASES_DB."cModulosAlertasModulosRoles.db.php");

class cModulosAlertasModulosRoles extends cModulosAlertasModulosRolesdb
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
			'xIdModuloInicial'=> 0,
			'IdModuloInicial'=> "",
			'xIdModuloFinal'=> 0,
			'IdModuloFinal'=> "-1",
			'xIdRol'=> 0,
			'IdRol'=> "-1",
			'xEnviaMail'=> 0,
			'EnviaMail'=> "",
			'xEsObligatorio'=> 0,
			'EsObligatorio'=> "",
			'limit'=> '',
			'orderby'=> "IdModuloInicial DESC"
		);

		if(isset($datos['IdModuloInicial']) && $datos['IdModuloInicial']!="")
		{
			$sparam['IdModuloInicial']= $datos['IdModuloInicial'];
			$sparam['xIdModuloInicial']= 1;
		}
		if(isset($datos['IdModuloFinal']) && $datos['IdModuloFinal']!="")
		{
			$sparam['IdModuloFinal']= $datos['IdModuloFinal'];
			$sparam['xIdModuloFinal']= 1;
		}
		if(isset($datos['IdRol']) && $datos['IdRol']!="")
		{
			$sparam['IdRol']= $datos['IdRol'];
			$sparam['xIdRol']= 1;
		}
		if(isset($datos['EnviaMail']) && $datos['EnviaMail']!="")
		{
			$sparam['EnviaMail']= $datos['EnviaMail'];
			$sparam['xEnviaMail']= 1;
		}
		if(isset($datos['EsObligatorio']) && $datos['EsObligatorio']!="")
		{
			$sparam['EsObligatorio']= $datos['EsObligatorio'];
			$sparam['xEsObligatorio']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function ModulosSP(&$spnombre,&$sparam)
	{
		if (!parent::ModulosSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function ModulosSPResult(&$resultado,&$numfilas)
	{
		if (!$this->ModulosSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function RolesSP(&$spnombre,&$sparam)
	{
		if (!parent::RolesSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function RolesSPResult(&$resultado,&$numfilas)
	{
		if (!$this->RolesSP($spnombre,$sparam))
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
		
		$datosins['IdRegistro'] = $codigoinsertado;
		foreach($datos['IdModuloFinal'] as $modulo)
		{
			$datosins['IdModulo'] = $modulo;
			if(!$this->InsertarModulosFinal($datosins))
				return false;
		}
		unset($datosins['IdModulo']);
		foreach($datos['IdRol'] as $rol)
		{
			$datosins['IdRol'] = $rol;
			if(!$this->InsertarRoles($datosins))
				return false;
		}
		
		return true;
	}



	public function InsertarModulosFinales($datos)
	{
		if(!$this->_ValidarInsertarModulosFinales($datos))
			return false;
		if (!parent::InsertarModulosFinales($datos))
			return false;	
		
		return true;
	}



	public function InsertarRoles($datos)
	{
		if(!$this->_ValidarInsertarRoles($datos))
			return false;
		if (!parent::InsertarRoles($datos))
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
		
		$datosins['IdRegistro'] = $datos['IdRegistro'];
		
		if (!$this->EliminarModulosFinales($datos))
			return false;
		
		if (!$this->EliminarRoles($datos))
			return false;
		
		
		foreach($datos['IdModuloFinal'] as $modulo)
		{
			$datosins['IdModulo'] = $modulo;
			if(!$this->InsertarModulosFinal($datosins))
				return false;
		}
		unset($datosins['IdModulo']);foreach($datos['IdRol'] as $rol)
		{
			$datosins['IdRol'] = $rol;
			if(!$this->InsertarRoles($datosins))
				return false;
		}

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


	public function EliminarModulosFinales($datos)
	{

		if (!parent::EliminarModulosFinales($datos))
			return false;

		return true;
	}


	public function EliminarRoles($datos)
	{

		if (!parent::EliminarRoles($datos))
			return false;

		return true;
	}
	
	
	public function BuscarRepetidos($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'IdModuloInicial'=> $datos['IdModuloInicial'],
			'xIdRegistro'=> 0,
			'IdRegistro'=> ""
		);


		if(isset($datos['IdRegistro']) && $datos['IdRegistro']!="")
		{
			$sparam['IdRegistro']= $datos['IdRegistro'];
			$sparam['xIdRegistro']= 1;
		}
		
		if (!parent::BuscarRepetidos($sparam,$resultado,$numfilas))
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
		
		if(!$this->_ValidarRepetidos($datos))
			return false;

		return true;
	}



	private function _ValidarInsertarModulosFinales($datos)
	{
		
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdModuloFinal'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->conexion->TraerCampo('Modulos','IdModulo',array('IdModulo='.$datos['IdModuloFinal']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



	private function _ValidarInsertarRoles($datos)
	{
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRol'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
	
		if (!$this->conexion->TraerCampo('Roles','IdRol',array('IdRol='.$datos['IdRol']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

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
		
		if(!$this->_ValidarRepetidos($datos))
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


		if (!isset($datos['IdModuloInicial']) || $datos['IdModuloInicial']=="")
			$datos['IdModuloInicial']="NULL";

		/*if (!isset($datos['IdModuloFinal']) || $datos['IdModuloFinal']=="")
			$datos['IdModuloFinal']="NULL";

		if (!isset($datos['IdRol']) || $datos['IdRol']=="")
			$datos['IdRol']="NULL";*/

		if (!isset($datos['EnviaMail']) || $datos['EnviaMail']=="")
			$datos['EnviaMail']="NULL";

		if (!isset($datos['EsObligatorio']) || $datos['EsObligatorio']=="")
			$datos['EsObligatorio']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdModuloInicial']) || $datos['IdModuloInicial']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un modulo inicial",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdModuloInicial'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if ((!isset($datos['IdModuloFinal']) || count($datos['IdModuloFinal'])==0) && (!isset($datos['IdRol']) || count($datos['IdRol'])==0))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un modulo o un rol final",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}



		if (!isset($datos['EnviaMail']) || $datos['EnviaMail']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si env�a mail",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['EsObligatorio']) || $datos['EsObligatorio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar es obligatorio",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		if (!$this->conexion->TraerCampo('Modulos','IdModulo',array('IdModulo='.$datos['IdModuloInicial']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	
	private function _ValidarRepetidos($datos)
	{
		$datosBuscar['IdModuloInicial'] = $datos['IdModuloInicial'];
		
		if(isset($datos['IdRegistro']))
			$datosBuscar['IdRegistro'] = $datos['IdRegistro'];
			
		if(!$this->BuscarRepetidos($datosBuscar,$resultado,$numfilas))
			return false;
		
		if($numfilas != 0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error ya existe una configuracion de alerta a ese modulo inicial.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}





}
?>