<?php 
abstract class cUsuariosPermisosdb
{


	function __construct(){}

	function __destruct(){}

	
	
	protected function BuscarPermisosxUsuario($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_RolesModulosAccions_xIdRol";
		$sparam=array(
			'pIdRol'=> $datos['IdRol']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}







}
?>