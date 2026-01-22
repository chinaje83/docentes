<?php 
abstract class cCamposReservadosdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarCamposReservadosxNombreCampo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CamposReservados_xNombreCampo";
		$sparam=array(
			'pNombreCampo'=> $datos['NombreCampo']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por nombre del campo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarCamposReservados($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CamposReservados";
		$sparam=array(
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los campos reservados. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>