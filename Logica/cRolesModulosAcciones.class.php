<?php 
include(DIR_CLASES_DB."cRolesModulosAcciones.db.php");

class cRolesModulosAcciones extends cRolesModulosAccionesdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarAccionesxRol($datos)
	{
		if (!parent::BuscarAccionesxRolDB($datos,$resultado,$numfilas))
			return false;
	
		$arrayRolesAcciones = array();
		while($filaRolesAcciones =$this->conexion->ObtenerSiguienteRegistro($resultado))
				$arrayRolesAcciones[$filaRolesAcciones['IdModulo']][] = $filaRolesAcciones['IdAccion'];
					
			
		return $arrayRolesAcciones;
	}



	public function Insertar($datos)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		if (!parent::Insertar($datos))
			return false;
		return true;
	}


	public function ActualizarAccionesxRol($datos,$codigosModulosSeleccionados)
	{
		if (isset($datos['IdAccion']))
		{
			foreach($datos['IdAccion'] as $IdModulo=>$acciones)
			{
				if (in_array($IdModulo, $codigosModulosSeleccionados))
				{
					foreach ($acciones as $IdAccion=>$data)
					{
						$datosInsertar['IdModulo'] = $IdModulo;
						$datosInsertar['IdAccion'] = $IdAccion;
						$datosInsertar['IdRol'] = $datos['IdRol'];
						if(!$this->Insertar($datosInsertar))
							return false;
					}	
				}
				
			}
		}
		return true;
	}



	public function EliminarxRol($datos)
	{
		if (!parent::EliminarxRol($datos))
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




	private function _SetearNull(&$datos)
	{
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{

		if (!isset($datos['IdRol']) || $datos['IdRol']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un rol",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRol'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdModulo']) || $datos['IdModulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un modulo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdModulo'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdAccion']) || $datos['IdAccion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una accion",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		/*if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdAccion'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		return true;
	}





}
?>