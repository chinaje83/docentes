<?php  
abstract class cUsuariosRolesdb
{
	
	// Constructor de la clase
	function __construct(){


    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	

	
//-----------------------------------------------------------------------------------------
//							 PUBLICAS	
//----------------------------------------------------------------------------------------- 

	protected function BuscarxActiveDirectory($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosRoles_xUsuarioAd";
		$sparam=array(
			'pUsuarioAd'=> $datos['UsuarioAd']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function AltaUsuarioRol($datos)
	{

		$spnombre="ins_usuariosroles";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdRol'=> $datos['IdRol'],
			'pIdCliente'=> $datos['IdCliente'],
			'pIdArea'=> $datos['IdArea'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s")
			);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) || $numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"No se pudieron insertar el usuario al rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}
		
		return true;

	}


	protected function BajaUsuarioRol($datos)
	{
		
		$spnombre="del_usuariosroles_sitios_xIdUsuario_rolcod";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdRol'=> $datos['IdRol'],
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"No se pudieron dar de baja el usuario al rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}
		
		return true;

	}
	
	protected function BajaUsuarioRolAreaCliente($datos)
	{
		
		$spnombre="del_usuariosroles_sitios_xIdUsuario_IdRol_IdCliente_IdArea";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdRol'=> $datos['IdRol'],
			'pIdCliente'=> $datos['IdCliente'],
			'pIdArea'=> $datos['IdArea']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) || $numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"No se pudieron dar de baja el usuario al rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}
		
		return true;

	}
	
	protected function BajaUsuarioAreaCliente($datos)
	{
		
		$spnombre="del_usuariosroles_sitios_xIdUsuario_IdCliente_IdArea";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdCliente'=> $datos['IdCliente'],
			'pIdArea'=> $datos['IdArea']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"No se pudieron dar de baja el usuario al rol 3. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}
		
		return true;

	}


	protected function BajaUsuarioRolesxIdRol($datos)
	{
		
		$spnombre="del_UsuariosRoles_xIdRol";
		$sparam=array(
			'pIdRol'=> $datos['IdRol']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"No se pudieron dar de baja el rol asignado a usuarios. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}
		
		return true;

	}


}


?>