<?php 
abstract class cBuscadorTiposdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarBuscadotxClientexBuscador($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ClientesBuscadoresTipos_xIdCliente_xIdTipoBuscador";
		$sparam=array(
			'pIdCliente'=> $datos['IdCliente'],
			'pIdTipoBuscador'=> $datos['IdTipoBuscador']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



}
?>