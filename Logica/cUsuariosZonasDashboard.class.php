<?php 
include(DIR_CLASES_DB."cUsuariosZonasDashboard.db.php");

class cUsuariosZonasDashboard extends cUsuariosZonasDashboarddb
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
			'xIdUsuario'=> 0,
			'IdUsuario'=> "",
			'xIdZona'=> 0,
			'IdZona'=> "",
			'xIdModulosDashboard'=> 0,
			'IdModulosDashboard'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'limit'=> '',
			'orderby'=> "Orden ASC"
		);

		if(isset($datos['IdUsuario']) && $datos['IdUsuario']!="")
		{
			$sparam['IdUsuario']= $datos['IdUsuario'];
			$sparam['xIdUsuario']= 1;
		}
		if(isset($datos['IdZona']) && $datos['IdZona']!="")
		{
			$sparam['IdZona']= $datos['IdZona'];
			$sparam['xIdZona']= 1;
		}
		if(isset($datos['IdModulosDashboard']) && $datos['IdModulosDashboard']!="")
		{
			$sparam['IdModulosDashboard']= $datos['IdModulosDashboard'];
			$sparam['xIdModulosDashboard']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
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



	public function UsuariosMacrosDashboardZonasSP(&$spnombre,&$sparam)
	{
		if (!parent::UsuariosMacrosDashboardZonasSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function UsuariosMacrosDashboardZonasSPResult(&$resultado,&$numfilas)
	{
		if (!$this->UsuariosMacrosDashboardZonasSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function ModulosDashboardSP(&$spnombre,&$sparam)
	{
		if (!parent::ModulosDashboardSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function ModulosDashboardSPResult(&$resultado,&$numfilas)
	{
		if (!$this->ModulosDashboardSP($spnombre,$sparam))
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

		$this->ObtenerProximoOrden($datos,$proxorden);
		$datos['Orden'] = $proxorden;
		$datos['UltimaModificacionFecha']=FuncionesPHPLocal::ConvertirFecha( $datos['UltimaModificacionFecha'],'dd/mm/aaaa','aaaa-mm-dd');
		$this->_SetearNull($datos);
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos))
			return false;

		$datos['UltimaModificacionFecha']=FuncionesPHPLocal::ConvertirFecha( $datos['UltimaModificacionFecha'],'dd/mm/aaaa','aaaa-mm-dd');
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



	public function ModificarOrdenCompleto($datos)
	{
		$datosmodif['Orden'] = 1;
		$datosmodif['IdZona'] = $datos['IdZona'];
		$datosmodif['IdUsuario'] = $_SESSION['usuariocod'];
		$arregloOrden = $datos['module'];
		foreach ($arregloOrden as $IdUsuarioDashboard){
			$datosmodif['IdUsuarioDashboard'] = $IdUsuarioDashboard;
			if (!parent::ModificarOrden($datosmodif))
				return false;
			$datosmodif['Orden']++;
		}
		return true;
	}



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



	public function InsertarModuloEnZona($datos,&$codigoinsertado)
	{
		
		$datosBusqueda['IdUsuario'] = $_SESSION['usuariocod'];
		$datosBusqueda['IdZona'] = $datos['IdZona'];
		if(!$this->BusquedaAvanzada($datosBusqueda,$resultadoModulos,$numfilasModulos))
			return false;
			
		$datosmodif['IdUsuario'] = $_SESSION['usuariocod'];
		$datosmodif['IdZona'] = $datos['IdZona'];
		$datosmodif['Orden'] = 1;
		
		while($filaDatos = $this->conexion->ObtenerSiguienteRegistro($resultadoModulos))
		{
			if ($datosmodif['Orden']==$datos['orden'])
				$datosmodif['Orden']++;

			$datosmodif['IdUsuarioDashboard'] = $filaDatos['IdUsuarioDashboard'];
			if (!parent::ModificarOrden ($datosmodif))
				return false;
			$datosmodif['Orden']++;
		}

		$datosinsertar['IdZona']=$datos['IdZona'];
		$datosinsertar['Orden']=$datos['orden'];
		$datosinsertar['IdModulosDashboard']=$datos['IdModulosDashboard'];
		$datosinsertar['Json']=json_encode(array());
		if (!$this->Insertar($datosinsertar,$codigoinsertado))
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


		if (!isset($datos['IdUsuario']) || $datos['IdUsuario']=="")
			$datos['IdUsuario']="NULL";

		if (!isset($datos['IdZona']) || $datos['IdZona']=="")
			$datos['IdZona']="NULL";

		if (!isset($datos['IdModulosDashboard']) || $datos['IdModulosDashboard']=="")
			$datos['IdModulosDashboard']="NULL";

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['Json']) || $datos['Json']=="")
			$datos['Json']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdZona']) || $datos['IdZona']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una zona",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdZona'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdModulosDashboard']) || $datos['IdModulosDashboard']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un modulo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdModulosDashboard'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
/*
		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
*/
		if (!isset($datos['Json']) || $datos['Json']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un json",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->conexion->TraerCampo('UsuariosMacrosDashboardZonas','IdZona',array('IdZona='.$datos['IdZona']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->conexion->TraerCampo('ModulosDashboard','IdModulosDashboard',array('IdModulosDashboard='.$datos['IdModulosDashboard']),$dato,$numfilas,$errno))
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