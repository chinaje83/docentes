<?php 
abstract class cParosDocentesdb
{


	function __construct(){}

	function __destruct(){}

	
	
	protected function BuscarxTaskIdxFechaModificacion($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Licencias_TaskId_FechaModificacion";
		$sparam=array(
			'pBaseLicencias'=> BASEDATOSLICENCIAS,
			'pTaskId'=> $datos['TaskId'],
			'pFechaModificacion'=> $datos['FechaModificacion']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
	
	
		return true;
	}
	
	
	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Licencias_busqueda_avanzada";
		$sparam=array(
			'pBaseLicencias'=> BASEDATOSLICENCIAS,
			'pTaskId'=> $datos['TaskId'],
			'pxTaskId'=> $datos['xTaskId'],
			'plimit'=> $datos['limit'],
			'porderby'=> $datos['orderby']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	
	protected function ModificarIdEstadoProcesoxTaskIdxFechaModificacion($datos)
	{
		$spnombre="upd_Licencias_IdEstadoProceso_xTaskId_FechaModificacion";
		$sparam=array(
			'pBaseLicencias'=> BASEDATOSLICENCIAS,
			'pIdEstadoProceso'=> $datos['IdEstadoProceso'],
			'pTaskId'=> $datos['TaskId'],
			'pFechaModificacion'=> $datos['FechaModificacion']
		);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado del proceso. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
	
	
		return true;
	}


}
?>