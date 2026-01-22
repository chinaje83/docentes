<?php 
abstract class cDocumentacionTiposdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentacionTipos_xIdDocumentoTipo";
		$sparam=array(
			'pIdDocumentoTipo'=> $datos['IdDocumentoTipo']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentacionTipos_busqueda_avanzada";
		$sparam=array(
			'pxIdDocumentoTipo'=> $datos['xIdDocumentoTipo'],
			'pIdDocumentoTipo'=> $datos['IdDocumentoTipo'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxExtension'=> $datos['xExtension'],
			'pExtension'=> $datos['Extension'],
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

}
?>