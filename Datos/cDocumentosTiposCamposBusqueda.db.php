<?php 
abstract class cDocumentosTiposCamposBusquedadb
{


	function __construct(){}

	function __destruct(){}
	
	
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposCamposBusqueda_xIdCampoBusqueda";
		$sparam=array(
			'pIdCampoBusqueda'=> $datos['IdCampoBusqueda']
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
		$spnombre="sel_DocumentosTiposCamposBusqueda_xIRegistroTipoDocumento";
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
		$spnombre="sel_DocumentosTiposCamposBusqueda_busqueda_avanzada";
		$sparam=array(
			'pxIdCampoBusqueda'=> $datos['xIdCampoBusqueda'],
			'pIdCampoBusqueda'=> $datos['IdCampoBusqueda'],
			'pxIdRegistroTipoDocumento'=> $datos['xIdRegistroTipoDocumento'],
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pxIdCampo'=> $datos['xIdCampo'],
			'pIdCampo'=> $datos['IdCampo'],
			'pxEnBuscador'=> $datos['xEnBuscador'],
			'pEnBuscador'=> $datos['EnBuscador'],
			'pxEnListado'=> $datos['xEnListado'],
			'pEnListado'=> $datos['EnListado'],
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
		$spnombre="sel_DocumentosTiposCamposBusqueda_max_orden_xIdRegistroTipoDocumento";
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
		$spnombre="ins_DocumentosTiposCamposBusqueda";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdCampo'=> $datos['IdCampo'],
			'pOrden'=> $datos['Orden'],
			'pDataJson'=> $datos['DataJson'],
			'pEnBuscador'=> $datos['EnBuscador'],
			'pEnListado'=> $datos['EnListado'],
			'pEnEncabezado'=> $datos['EnEncabezado'],
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
		$spnombre="del_DocumentosTiposCamposBusqueda_xIdCampoBusqueda";
		$sparam=array(
			'pIdCampoBusqueda'=> $datos['IdCampoBusqueda']
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
		$spnombre="upd_DocumentosTiposCamposBusqueda_Orden_xIdCampoBusqueda";
		$sparam=array(
			'pOrden'=> $datos['Orden'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdCampoBusqueda'=> $datos['IdCampoBusqueda']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function ModificarEnBuscador($datos)
	{
		$spnombre="upd_DocumentosTiposCamposBusqueda_EnBuscador_xIdCampoBusqueda";
		$sparam=array(
			'pEnBuscador'=> $datos['EnBuscador'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdCampoBusqueda'=> $datos['IdCampoBusqueda']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function ModificarEnListado($datos)
	{
		$spnombre="upd_DocumentosTiposCamposBusqueda_EnListado_xIdCampoBusqueda";
		$sparam=array(
			'pEnListado'=> $datos['EnListado'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdCampoBusqueda'=> $datos['IdCampoBusqueda']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function ModificarEnEncabezado($datos)
	{
		$spnombre="upd_DocumentosTiposCamposBusqueda_EnEncabezado_xIdCampoBusqueda";
		$sparam=array(
			'pEnEncabezado'=> $datos['EnEncabezado'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdCampoBusqueda'=> $datos['IdCampoBusqueda']
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
		$spnombre="upd_DocumentosTiposCamposBusqueda_DataJson_xIdCampoBusqueda";
		$sparam=array(
			'pDataJson'=> $datos['DataJson'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdCampoBusqueda'=> $datos['IdCampoBusqueda']
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