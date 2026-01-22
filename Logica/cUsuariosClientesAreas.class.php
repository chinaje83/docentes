<?php 
 
include(DIR_CLASES_DB."cUsuariosClientesAreas.db.php");

class cUsuariosClientesAreas extends cUsuariosClientesAreasdb
{
	/**
	 * Conexion a la base de datos.
	 * @var objeto conexion
	 */
	protected $conexion;
	/**
	 * Formato de errores. Formato en que se muestran los errores.
	 * @var string
	 */
 	protected $formato;
 	protected $IdCliente;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		if (isset($_SESSION['IdCliente']))
			$this->IdCliente = $_SESSION['IdCliente'];
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}


/**
 * Retorna datos de areas y proyectos de usuario por codigo.
 *
 * @param array $datos['IdUsuario'], array con clave de codigo de usuario
 *
 * @return  Query con los datos del usuario de areas y proyectos del usuario
 * @todo    Retorna falso en caso de que exista un problema con el store procedure.
 *
 * @since   2017-08-02
 * @author  Alejandro Precioso <aprecioso@gmail.com>
 *
 */
 
 

	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$datos['IdCliente'] = $this->IdCliente;
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function TraerAreas($datos,&$resultado,&$numfilas)
	{
		$datos['IdCliente'] = $this->IdCliente;
		if (!parent::TraerAreas($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function SetIdCliente($IdCliente){$this->IdCliente = $IdCliente; return true;}

	
	public function ActualizarAreas($datos)
	{
		if(!$this->TraerAreas($datos,$resultado,$numfilas))
			return false;
		$insertar = false;
		if ($numfilas>0)
		{
			$datosDel['IdUsuario'] = $datos['IdUsuario'];
			$datosDel['IdCliente'] = $this->IdCliente;
			if(!parent::Eliminar($datosDel))
				return false;
				
			if ($datos['IdArea']!="")
				$insertar=true;
		}elseif ($datos['IdArea']!="")
			$insertar=true;

		if ($insertar)
		{
			$datosIns['IdArea'] = $datos['IdArea'];
			$datosIns['IdCliente'] = $this->IdCliente;
			$datosIns['IdUsuario'] = $datos['IdUsuario'];
			if (!$this->_ValidarActualizacionArea($datosIns))
				return false;
			if(!parent::Insertar($datosIns))
				return false;
		}
		

		return true;	
	}
	
	
	public function InsertarAreas($datos)
	{
		$datosIns['IdArea'] = $datos['IdArea'];
		$datosIns['IdCliente'] = $this->IdCliente;
		$datosIns['IdUsuario'] = $datos['IdUsuario'];
		if (!$this->_ValidarActualizacionArea($datosIns))
			return false;
		
		if(!$this->TraerAreas($datos,$resultado,$numfilas))
			return false;	
			
		$AreasCliente = array();
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
			$AreasCliente[$fila['IdArea']] = $fila['IdArea'];
		
		
		if(array_key_exists($datos['IdArea'],$AreasCliente))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el area ya fue agregada.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
			
		}
			
		if(!parent::Insertar($datosIns))
			return false;
		
		return true;
	}
	
	private function _ValidarActualizacionArea($datos)
	{
		
		$oAreas = new cAreas($this->conexion,$this->formato);
		$oAreas->SetIdCliente($this->IdCliente);
		if(!$oAreas->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe seleccionar un area valida.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}	
			
		return true;	
		
	}
	
	
	public function EliminarAreas($datos)
	{
			
		$datos['IdArea'] = $datos['IdArea'];
		$datos['IdCliente'] = $this->IdCliente;
		$datos['IdUsuario'] = $datos['IdUsuario'];
		
		if (!$this->_ValidarActualizacionArea($datos))
			return false;
		
		$oUsuariosRoles = new cUsuariosRoles($this->conexion,$this->formato);
		if(!$oUsuariosRoles->BajaUsuarioAreaCliente($datos))
			return false;
		
		if(!parent::EliminarxIdClientexIdUsuarioxIdArea($datos))
			return false;
			
		return true;	
		
	}


//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------





}
?>