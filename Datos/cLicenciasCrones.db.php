<?php 
abstract class cLicenciasCronesdb
{


	function __construct(){}

	function __destruct(){}

	
	
	protected function BuscarUltimoCron(&$resultado,&$numfilas)
	{
		$spnombre="sel_LicenciasCrones_Ultimo";
		$sparam=array(
			'pBaseLicencias'=> BASEDATOSLICENCIAS,
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_LicenciasCrones";
		$sparam=array(
			'pBaseLicencias'=> BASEDATOSLICENCIAS,
			'pdateStart'=> $datos['dateStart'],
			'pdateEnd'=> $datos['dateEnd'],
			'pdateEndBase'=> $datos['dateEndBase'],
			'pFechaInicio'=> $datos['FechaInicio'],
			'pFechaFin'=> $datos['FechaFin'],
			'pTotal'=> $datos['Total'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}



}
?>