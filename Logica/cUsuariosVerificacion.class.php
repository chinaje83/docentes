<?php 
include(DIR_CLASES_DB."cUsuariosVerificacion.db.php");

class cUsuariosVerificacion extends cUsuariosVerificaciondb
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

	public function BuscarxCodigoClaveEscuelaCuilIdRol($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigoClaveEscuelaCuilIdRol($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdVerificacion'=> 0,
			'IdVerificacion'=> "",
			'xCuil'=> 0,
			'Cuil'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xApellido'=> 0,
			'Apellido'=> "",
			'xClaveEscuela'=> 0,
			'ClaveEscuela'=> "",
			'limit'=> '',
			'orderby'=> "IdVerificacion DESC"
		);

		if(isset($datos['IdVerificacion']) && $datos['IdVerificacion']!="")
		{
			$sparam['IdVerificacion']= $datos['IdVerificacion'];
			$sparam['xIdVerificacion']= 1;
		}
		if(isset($datos['Cuil']) && $datos['Cuil']!="")
		{
			$sparam['Cuil']= $datos['Cuil'];
			$sparam['xCuil']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['Apellido']) && $datos['Apellido']!="")
		{
			$sparam['Apellido']= $datos['Apellido'];
			$sparam['xApellido']= 1;
		}
		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$sparam['ClaveEscuela']= $datos['ClaveEscuela'];
			$sparam['xClaveEscuela']= 1;
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
		
		
		$datos['Metapuestos'] = implode("||",$datos['metapuesto']);
		
		$this->_SetearNull($datos);
		$datos['UltimaModificacionUsuario']="NULL";
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;
		
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

		if (!parent::Eliminar($datos))
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
		$datosmodif['IdVerificacion'] = $datos['IdVerificacion'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdVerificacion'] = $datos['IdVerificacion'];
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
		if (!$this->BuscarxCodigoClaveEscuelaCuilIdRol($datos,$resultado,$numfilas))
			return false;
		
		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Ya existe una solicitud para su usuario en dicha escuela con el puesto seleccionado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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


		if (!isset($datos['Cuil']) || $datos['Cuil']=="")
			$datos['Cuil']="NULL";

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['Apellido']) || $datos['Apellido']=="")
			$datos['Apellido']="NULL";

		if (!isset($datos['Email']) || $datos['Email']=="")
			$datos['Email']="NULL";

		if (!isset($datos['ClaveEscuela']) || $datos['ClaveEscuela']=="")
			$datos['ClaveEscuela']="NULL";

		if (!isset($datos['NombreEscuela']) || $datos['NombreEscuela']=="")
			$datos['NombreEscuela']="NULL";

		if (!isset($datos['Distrito']) || $datos['Distrito']=="")
			$datos['Distrito']="NULL";

		if (!isset($datos['TipoOrganizacion']) || $datos['TipoOrganizacion']=="")
			$datos['TipoOrganizacion']="NULL";
		
		if (!isset($datos['Metapuestos']) || $datos['Metapuestos']=="")
			$datos['Metapuestos']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['Cuil']) || $datos['Cuil']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un cuil",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Apellido']) || $datos['Apellido']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un apellido",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Email']) || $datos['Email']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un email",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['ClaveEscuela']) || $datos['ClaveEscuela']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una clave escuela",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['NombreEscuela']) || $datos['NombreEscuela']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre de la escuela",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Distrito']) || $datos['Distrito']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un distrito",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['TipoOrganizacion']) || $datos['TipoOrganizacion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo de organizacion",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdRol']) || $datos['IdRol']=="" || !is_numeric($datos['IdRol']))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un puesto",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->conexion->TraerCampo('Roles','IdRol',array('IdRol='.$datos['IdRol']),$dato,$numfilas,$errno))
			return false;
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un puesto valido",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}





}
?>