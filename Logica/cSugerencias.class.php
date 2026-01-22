<?php 
include(DIR_CLASES_DB."cSugerencias.db.php");

class cSugerencias extends cSugerenciasdb
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
			'xIdSugerencia'=> 0,
			'IdSugerencia'=> "",
			'xIdSugerenciaTipocod'=> 0,
			'IdSugerenciaTipocod'=> "",
			'xIdDocumento'=> 0,
			'IdDocumento'=> "",
			'xIdTipoDocumento'=> 0,
			'IdTipoDocumento'=> "",
			'xDescripcion'=> 0,
			'Descripcion'=> "",
			'xClaveEscuela'=> 0,
			'ClaveEscuela'=> "",
			'xIdDistrito'=> 0,
			'IdDistrito'=> "",
			'xIdTipoOrganismo'=> 0,
			'IdTipoOrganismo'=> "",
			'xFechaDesde'=> "",
			'FechaDesde'=> "",
			'xFechaHasta'=> "",
			'FechaHasta'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdSugerencia DESC"
		);

		if(isset($datos['IdSugerencia']) && $datos['IdSugerencia']!="")
		{
			$sparam['IdSugerencia']= $datos['IdSugerencia'];
			$sparam['xIdSugerencia']= 1;
		}
		if(isset($datos['IdSugerenciaTipocod']) && $datos['IdSugerenciaTipocod']!="")
		{
			$sparam['IdSugerenciaTipocod']= $datos['IdSugerenciaTipocod'];
			$sparam['xIdSugerenciaTipocod']= 1;
		}
		if(isset($datos['Descripcion']) && $datos['Descripcion']!="")
		{
			$sparam['Descripcion']= $datos['Descripcion'];
			$sparam['xDescripcion']= 1;
		}
		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			$sparam['IdDocumento']= $datos['IdDocumento'];
			$sparam['xIdDocumento']= 1;
		}
		
		if(isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento']!="")
		{
			$sparam['IdTipoDocumento']= $datos['IdTipoDocumento'];
			$sparam['xIdTipoDocumento']= 1;
		}
		
		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$sparam['ClaveEscuela']= $datos['ClaveEscuela'];
			$sparam['xClaveEscuela']= 1;
		}
		
		if(isset($datos['IdDistrito']) && $datos['IdDistrito']!="")
		{
			$sparam['IdDistrito']= $datos['IdDistrito'];
			$sparam['xIdDistrito']= 1;
		}
		
		if(isset($datos['IdTipoOrganismo']) && $datos['IdTipoOrganismo']!="")
		{
			$sparam['IdTipoOrganismo']= $datos['IdTipoOrganismo'];
			$sparam['xIdTipoOrganismo']= 1;
		}
		
		
		if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			$sparam['FechaDesde']= FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],'dd/mm/aaaa','aaaa-mm-dd')." 00:00:00";
			$sparam['xFechaDesde']= 1;
		}


		if(isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
		{
			$sparam['FechaHasta']= FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],'dd/mm/aaaa','aaaa-mm-dd')." 23:59:59";
			$sparam['xFechaHasta']= 1;
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
		
		if(isset($_SESSION['ClaveEscuela']) && $_SESSION['ClaveEscuela']!="")
			$datos['ClaveEscuela'] = $_SESSION['ClaveEscuela'];
		
		if(isset($_SESSION['NombreEscuela']) && $_SESSION['NombreEscuela']!="")
			$datos['NombreEscuela'] = $_SESSION['NombreEscuela'];
		
		if(isset($_SESSION['IdDistrito']) && $_SESSION['IdDistrito']!="")
			$datos['IdDistrito'] = $_SESSION['IdDistrito'];
		
		if(isset($_SESSION['IdTipo']) && $_SESSION['IdTipo']!="")
			$datos['IdTipoOrganismo'] = $_SESSION['IdTipo'];

		$this->_SetearNull($datos);
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['Estado'] = ACTIVO;
		
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$datosmodif['IdSugerencia'] = $datos['IdSugerencia'];
		$datosmodif['Estado'] = 30;
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



	public function Procesar($datos)
	{
		$datosmodif['IdSugerencia'] = $datos['IdSugerencia'];
		$datosmodif['Estado'] = 20;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function Pendiente($datos)
	{
		$datosmodif['IdSugerencia'] = $datos['IdSugerencia'];
		$datosmodif['Estado'] = 10;
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


		if (!isset($datos['IdSugerenciaTipocod']) || $datos['IdSugerenciaTipocod']=="")
			$datos['IdSugerenciaTipocod']="NULL";

		if (!isset($datos['IdDocumento']) || $datos['IdDocumento']=="")
			$datos['IdDocumento']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";
		
		if (!isset($datos['ClaveEscuela']) || $datos['ClaveEscuela']=="")
			$datos['ClaveEscuela']="NULL";
		
		if (!isset($datos['NombreEscuela']) || $datos['NombreEscuela']=="")
			$datos['NombreEscuela']="NULL";
		
		if (!isset($datos['IdDistrito']) || $datos['IdDistrito']=="")
			$datos['IdDistrito']="NULL";
		
		if (!isset($datos['IdTipoOrganismo']) || $datos['IdTipoOrganismo']=="")
			$datos['IdTipoOrganismo']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdSugerenciaTipocod']) || $datos['IdSugerenciaTipocod']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo de sugerencias",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdSugerenciaTipocod']) && $datos['IdSugerenciaTipocod']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdSugerenciaTipocod'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}


		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripcion",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}





}
?>