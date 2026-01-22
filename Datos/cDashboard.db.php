<?php 
abstract class cDashboarddb
{


	function __construct(){}

	function __destruct(){}



	protected function BuscarCantidadTrabajandoxArea($MisAreas,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosLibros_Dashboard_xIdArea";
		$sparam=array(
			'pIdArea'=> $MisAreas
		);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la cantidad de libros que se trabajan en el area. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarCantidadContralores($MisAreas,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosLibros_Dashboard_xIdArea";
		$sparam=array(
			'pIdArea'=> $MisAreas
		);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la cantidad de libros que se trabajan en el area. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}	




}
?>