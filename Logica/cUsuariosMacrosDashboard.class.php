<?php 
include(DIR_CLASES_DB."cUsuariosMacrosDashboard.db.php");

class cUsuariosMacrosDashboard extends cUsuariosMacrosDashboarddb
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

	public function BuscarMacros($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarMacros($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdUsuarioMacro'=> 0,
			'IdUsuarioMacro'=> "",
			'xIdUsuario'=> 0,
			'IdUsuario'=> "",
			'xIdMacro'=> 0,
			'IdMacro'=> "",
			'xOrden'=> 0,
			'Orden'=> "",
			'limit'=> '',
			'orderby'=> "Orden ASC"
		);

		if(isset($datos['IdUsuarioMacro']) && $datos['IdUsuarioMacro']!="")
		{
			$sparam['IdUsuarioMacro']= $datos['IdUsuarioMacro'];
			$sparam['xIdUsuarioMacro']= 1;
		}
		if(isset($datos['IdUsuario']) && $datos['IdUsuario']!="")
		{
			$sparam['IdUsuario']= $datos['IdUsuario'];
			$sparam['xIdUsuario']= 1;
		}
		if(isset($datos['IdMacro']) && $datos['IdMacro']!="")
		{
			$sparam['IdMacro']= $datos['IdMacro'];
			$sparam['xIdMacro']= 1;
		}
		if(isset($datos['Orden']) && $datos['Orden']!="")
		{
			$sparam['Orden']= $datos['Orden'];
			$sparam['xOrden']= 1;
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



	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->ObtenerProximoOrden($datos,$proxorden);
		$datos['Orden'] = $proxorden;
		$this->_SetearNull($datos);
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		return true;
	}


	public function AgregarMacro($datos,&$codigoinsertado)
	{
		
		if(!$this->Insertar($datos,$codigoinsertado))
			return false;
		
		$datosBusqueda['IdMacro'] = $datos['IdMacro'];
		$oObjeto = new cMacrosDashboardEstructuras($this->conexion,$this->formato);	
		if(!$oObjeto->BuscarEstructurasxMacro($datosBusqueda,$resultado,$numfilas))
			return false;
			

		$datosInsertar['IdUsuarioMacro'] = $codigoinsertado;
		$datosInsertar['IdMacro'] = $datos['IdMacro'];
		$oFormulariosPasosMacrosZonas = new cUsuariosMacrosDashboardZonas($this->conexion,$this->formato);	

		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{

			$datosInsertar['IdEstructura'] = $fila['IdEstructura'];
			if(!$oFormulariosPasosMacrosZonas->Insertar($datosInsertar,$codigoinsertado))
				return false;
				
		}	

		
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
		$arregloOrden = explode(",",$datos['orden']);
		foreach ($arregloOrden as $IdUsuarioMacro){
			$datosmodif['IdUsuarioMacro'] = $IdUsuarioMacro;
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

		if (!isset($datos['IdMacro']) || $datos['IdMacro']=="")
			$datos['IdMacro']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{



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