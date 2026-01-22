<?php 
abstract class cDocumentosTiposCamposBusquedaGeneraldb
{


	function __construct(){}

	function __destruct(){}
	
	
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposCamposBusquedaGeneral_xIdCampoBusquedaGeneral";
		$sparam=array(
			'pIdCampoBusquedaGeneral'=> $datos['IdCampoBusquedaGeneral']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarxIdRegistroTipoDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposCamposBusquedaGeneral_xIRegistroTipoDocumento";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por Id del tipo de documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposCamposBusquedaGeneral_busqueda_avanzada";
		$sparam=array(
			'pxIdCampoBusquedaGeneral'=> $datos['xIdCampoBusquedaGeneral'],
			'pIdCampoBusquedaGeneral'=> $datos['IdCampoBusquedaGeneral'],
			'pxIdRegistroTipoDocumento'=> $datos['xIdRegistroTipoDocumento'],
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pxIdCampo'=> $datos['xIdCampo'],
			'pIdCampo'=> $datos['IdCampo'],
			'porderby'=> $datos['orderby'],
			'plimit'=> $datos['limit']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por Id del tipo de documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarUltimoOrden($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposCamposBusquedaGeneral_max_orden_xIdRegistroTipoDocumento";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el maximo orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_DocumentosTiposCamposBusquedaGeneral";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdCampo'=> $datos['IdCampo'],
			'pOrden'=> $datos['Orden'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaApp'=>  APP,
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=>  APP 
			);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}
	
	protected function Eliminar($datos)
	{
		$spnombre="del_DocumentosTiposCamposBusquedaGeneral_xIdCampoBusquedaGeneral";
		$sparam=array(
			'pIdCampoBusquedaGeneral'=> $datos['IdCampoBusquedaGeneral']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function ModificarOrden($datos)
	{
		$spnombre="upd_DocumentosTiposCamposBusquedaGeneral_Orden_xIdCampoBusquedaGeneral";
		$sparam=array(
			'pOrden'=> $datos['Orden'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdCampoBusquedaGeneral'=> $datos['IdCampoBusquedaGeneral']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function ModificarDataJson($datos)
	{
		$spnombre="upd_DocumentosTiposCamposBusquedaGeneral_DataJson_xIdCampoBusquedaGeneral";
		$sparam=array(
			'pDataJson'=> $datos['DataJson'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdCampoBusquedaGeneral'=> $datos['IdCampoBusquedaGeneral']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}
	

}
?>