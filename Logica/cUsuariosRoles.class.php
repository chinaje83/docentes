<?php  
include(DIR_CLASES_DB."cUsuariosRoles.db.php");

class cUsuariosRoles extends cUsuariosRolesdb
{
	protected $conexion;
	protected $formato;
	
	
	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		parent::__construct(); 
    } 
	
	// Destructor de la clase
	function __destruct() {	
		parent::__destruct(); 
    } 	



//-----------------------------------------------------------------------------------------
//							 PUBLICAS	
//----------------------------------------------------------------------------------------- 

//----------------------------------------------------------------------------------------- 

	
	public function BuscarxActiveDirectory($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxActiveDirectory($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	

	public function AltaUsuarioRol($datos)
	{
		if (!$this->_ValidarDatosAltaUsuarioRol($datos))
			return false;
		
		$datos['sitiocod']="0";
		if (!parent::AltaUsuarioRol($datos))
			return false;
		
		return true;

	}
	
	public function AltaUsuarioRolExt($datos)
	{
		$datos['sitiocod']="0";
		if (!parent::AltaUsuarioRol($datos))
			return false;
		
		return true;

	}


	public function BajaUsuarioRol($datos)
	{
		
		if (!$this->_ValidarDatosBajaUsuarioRol($datos))
			return false;
	
		if (!parent::BajaUsuarioRol($datos))
			return false;
		
		return true;

	}
	
	public function BajaUsuarioRolAreaCliente($datos)
	{
		
		if (!$this->_ValidarDatosBajaUsuarioRolAreaCliente($datos))
			return false;
	
		if (!parent::BajaUsuarioRolAreaCliente($datos))
			return false;
		
		return true;

	}
	
	
	public function BajaUsuarioAreaCliente($datos)
	{
		
		if (!$this->_ValidarDatosBajaUsuarioAreaCliente($datos))
			return false;
	
		if (!parent::BajaUsuarioAreaCliente($datos))
			return false;
		
		return true;

	}


	public function BajaUsuarioRolesxIdRol($datos)
	{
		
		if (!$this->_ValidarDatosBajaUsuarioRolesxIdRol($datos))
			return false;
	
		if (!parent::BajaUsuarioRolesxIdRol($datos))
			return false;
		
		return true;

	}


	/*public function ObtenerDatosCheckRoles($datos,&$arrayfinal)
	{
		
		$arrayfinal=array();
		foreach ($datos as $nombre_var => $valor_var) {
			if (empty($valor_var)) {
				$vacio[$nombre_var] = $valor_var;
			} else {
				
				$post[$nombre_var] = $valor_var;
				$opcion = substr($nombre_var,0,6);
				if ($opcion=="IdRol_")
				{
					$arrayfinal[] = $valor_var;
				}
			}
		}
		return true;
	}*/
	
	public function ObtenerDatosCheckRoles($datos,&$arrayfinal)
	{
		
		$arrayfinal=array();
		foreach ($datos as $nombre_var => $valor_var) {
			if (empty($valor_var)) {
				$vacio[$nombre_var] = $valor_var;
			} else {
				
				$post[$nombre_var] = $valor_var;
				$opcion = substr($nombre_var,0,6);
				if ($opcion=="IdRol_")
				{
					$arrayvalores = explode("_",substr($nombre_var,6));
					$arrayfinal[$arrayvalores[0]][$arrayvalores[1]][$arrayvalores[2]] = $valor_var;
				}
			}
		}
		return true;
	}



	/*public function ActualizarRolesUsuario($datos)
	{
		//array de roles a asignar
		if (!$this->ObtenerDatosCheckRoles($datos,$arrayfinal))
			return false;
		
		$oRoles=new cRoles($this->conexion);
		if (!$oRoles->RolesDeUnUsuario($datos['IdUsuario'],$numfilas,$resultadoroles))
			return false;
				
		if (!$oRoles->TraerRolesActualizar($_SESSION,$resultado,$numfilas))
			return false;
			
		$arregloroles = array();
		while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
			$arregloroles[] = $fila['IdRolActualizado'];
		
		$arrayinicial = array();
		while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoroles))
		{	
			if (in_array($fila['IdRol'],$arregloroles))
				$arrayinicial[] = $fila['IdRol'];
		}
		
		$arraysacar = array_diff($arrayinicial,$arrayfinal);
		$arrayponer = array_diff($arrayfinal,$arrayinicial);

		$datosinsertar['IdUsuario'] = $datos['IdUsuario'];
		foreach($arrayponer as $rolcod)
		{
			$datosinsertar['IdRol'] = $rolcod;
			if (!$this->AltaUsuarioRol($datosinsertar))
				return false;
		}
		
		$datoseliminar['IdUsuario'] = $datos['IdUsuario'];
		foreach($arraysacar as $rolcod)
		{
			$datoseliminar['IdRol'] = $rolcod;
			if (!$this->BajaUsuarioRol($datoseliminar))
				return false;
		}

		return true;
	}*/
	
	
	public function ActualizarRolesUsuario($datos)
	{
		
		//array de roles a asignar
		if (!$this->ObtenerDatosCheckRoles($datos,$arrayfinal))
			return false;
			
		
		
		$oRoles=new cRoles($this->conexion);
		if (!$oRoles->RolesClientesAreasDeUnUsuario($datos['IdUsuario'],$numfilas,$resultadoroles))
			return false;
				
		if (!$oRoles->TraerRolesActualizar($_SESSION,$resultado,$numfilas))
			return false;
			
		$arregloroles = array();
		while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
			$arregloroles[] = $fila['IdRolActualizado'];
		
		$arrayinicial = array();
		while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoroles))
		{	
			
			if (in_array($fila['IdRol'],$arregloroles))
				$arrayinicial[$fila['IdCliente']][$fila['IdArea']][$fila['IdRol']] = $fila['IdRol'];
		}
		
		$arraysacar = $this->array_diff_assoc_recursive($arrayinicial,$arrayfinal);
		$arrayponer = $this->array_diff_assoc_recursive($arrayfinal,$arrayinicial);
		
		if(count($arrayponer)>0)
		{
			
			$datosinsertar['IdUsuario'] = $datos['IdUsuario'];
			foreach($arrayponer as $IdCliente=>$DatosClienteRol)
			{
				$datosinsertar['IdCliente'] = $IdCliente;
				
				foreach($DatosClienteRol as $IdArea=>$DatosAreaRol)
				{
					$datosinsertar['IdArea'] = $IdArea;
					foreach($DatosAreaRol as $IdRol)
					{
						$datosinsertar['IdRol'] = $IdRol;
						
						if (!$this->AltaUsuarioRol($datosinsertar))
							return false;
					}
				}
			}
		}
		if(count($arraysacar)>0)
		{
			$oUsuariosRolesDistritos = new cUsuariosRolesDistritos($this->conexion,$this->formato);
			
			$datoseliminar['IdUsuario'] = $datos['IdUsuario'];
			foreach($arraysacar as $IdCliente=>$DatosClienteRol)
			{
				$datoseliminar['IdCliente'] = $IdCliente;
				foreach($DatosClienteRol as $IdArea=>$DatosAreaRol)
				{
					$datoseliminar['IdArea'] = $IdArea;
					foreach($DatosAreaRol as $IdRol)
					{
						$datoseliminar['IdRol'] = $IdRol;
						if (!$this->BajaUsuarioRolAreaCliente($datoseliminar))
							return false;
						
						
						if (!$oUsuariosRolesDistritos->EliminarxIdUsuarioxIdClientexIdAreaxIdRol($datoseliminar))
							return false;
						
					}
				}
			}
		}
		return true;
	}
	
	
	
	


//-----------------------------------------------------------------------------------------
//							 PRIVADAS	
//----------------------------------------------------------------------------------------- 
	
	private function array_diff_assoc_recursive($array1, $array2)
	{
		foreach($array1 as $key => $value)
		{
			if(is_array($value))
			{
				if(!isset($array2[$key]))
				{
					$difference[$key] = $value;
				}
				elseif(!is_array($array2[$key]))
				{
					$difference[$key] = $value;
				}
				else
				{
					$new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
					if($new_diff != FALSE)
					{
						$difference[$key] = $new_diff;
					}
				}
			}
			elseif(!isset($array2[$key]) || $array2[$key] != $value)
			{
				$difference[$key] = $value;
			}
		}
		return !isset($difference) ? array() : $difference;
	}
	
	
	
	
	private function _ValidarDatosUsuarioRol($datos)
	{
		
		
		$oRoles=new cRoles($this->conexion);
		if (!$oRoles->RolesPosiblesAsignarxClientexArea($_SESSION['rolcod'],$datos['IdUsuario'],$datos['IdCliente'],$datos['IdArea'],$numfilas,$roles_sin_asignar))			
			return false;

		if (!in_array($datos['IdRol'],$roles_sin_asignar))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, no puede asignar dicho rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		$oUsuariosClientesAreas = new  cUsuariosClientesAreas($this->conexion,$this->formato);
		$oUsuariosClientesAreas->setIdCliente($datos['IdCliente']);
		if(!$oUsuariosClientesAreas->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		
		
		if($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, no puede asignar dicho rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
			
		}	
	
	
		return true;
	}
	
	
	private function _ValidarDatosAltaUsuarioRol($datos)
	{
		if (!$this->_ValidarDatosUsuarioRol($datos))
			return false;
	
	
		return true;
	}

	private function _ValidarDatosBajaUsuarioRol($datos)
	{


		return true;
	}
	
	private function _ValidarDatosBajaUsuarioRolAreaCliente($datos)
	{


		return true;
	}
	
	private function _ValidarDatosBajaUsuarioAreaCliente($datos)
	{


		return true;
	}
	
	

	private function _ValidarDatosBajaUsuarioRolesxIdRol($datos)
	{


		return true;
	}




}


?>