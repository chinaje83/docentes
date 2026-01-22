<?php 
 
include(DIR_CLASES_DB."cUsuariosClientesEmpresas.db.php");

class cUsuariosClientesEmpresas extends cUsuariosClientesEmpresasdb
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

	public function TraerEmpresas($datos,&$resultado,&$numfilas)
	{
		$datos['IdCliente'] = $this->IdCliente;
		if (!parent::TraerEmpresas($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function SetIdCliente($IdCliente){$this->IdCliente = $IdCliente; return true;}

	
	public function ActualizarEmpresas($datos)
	{
		if(!$this->TraerEmpresas($datos,$resultado,$numfilas))
			return false;
		$insertar = false;
		if ($numfilas>0)
		{
			$datosDel['IdUsuario'] = $datos['IdUsuario'];
			$datosDel['IdCliente'] = $this->IdCliente;
			if(!parent::Eliminar($datosDel))
				return false;
				
			if ($datos['IdClienteEmpresa']!="")
				$insertar=true;
		}elseif ($datos['IdClienteEmpresa']!="")
			$insertar=true;

		if ($insertar)
		{
			$datosIns['IdClienteEmpresa'] = $datos['IdClienteEmpresa'];
			$datosIns['IdCliente'] = $this->IdCliente;
			$datosIns['IdUsuario'] = $datos['IdUsuario'];
			if (!$this->_ValidarActualizacionEmpresa($datosIns))
				return false;
			if(!parent::Insertar($datosIns))
				return false;
		}
		

		return true;	
	}
	
	
	public function InsertarEmpresas($datos)
	{
		$datosIns['IdClienteEmpresa'] = $datos['IdClienteEmpresa'];
		$datosIns['IdCliente'] = $this->IdCliente;
		$datosIns['IdUsuario'] = $datos['IdUsuario'];
		if (!$this->_ValidarActualizacionEmpresa($datosIns))
			return false;
		
		if(!$this->TraerEmpresas($datos,$resultado,$numfilas))
			return false;	
			
		$EmpresasCliente = array();
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
			$EmpresasCliente[$fila['IdClienteEmpresa']] = $fila['IdClienteEmpresa'];
		
		
		if(array_key_exists($datos['IdClienteEmpresa'],$EmpresasCliente))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, la empresa ya fue agregada.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
			
		}
			
		if(!parent::Insertar($datosIns))
			return false;
		
		return true;
	}
	
	private function _ValidarActualizacionEmpresa($datos)
	{
		
		$oClientesEmpresas = new cClientesEmpresas($this->conexion,$this->formato);
		
		if(!$oClientesEmpresas->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
			
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe seleccionar una empresa valida.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}	
			
		return true;	
		
	}
	
	
	public function EliminarEmpresas($datos)
	{
			
		$datos['IdClienteEmpresa'] = $datos['IdClienteEmpresa'];
		$datos['IdCliente'] = $this->IdCliente;
		$datos['IdUsuario'] = $datos['IdUsuario'];
		
		if (!$this->_ValidarActualizacionEmpresa($datos))
			return false;
		
		if(!parent::EliminarxIdClientexIdUsuarioxIdClienteEmpresa($datos))
			return false;
			
		return true;	
		
	}


//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------





}
?>