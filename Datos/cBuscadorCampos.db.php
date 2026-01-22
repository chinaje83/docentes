<?php 
abstract class cBuscadorCamposdb
{


	function __construct(){}

	function __destruct(){}
	
	
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_BuscadorCampos_xIdBuscador_xIdCampo";
		$sparam=array(
			'pIdBuscador'=> $datos['IdBuscador'],
			'pIdCampo'=> $datos['IdCampo'],
			
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarxIdBuscador($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_BuscadorCampos_xIdBuscador";
		$sparam=array(
			'pIdBuscador'=> $datos['IdBuscador']
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
		$spnombre="sel_BuscadorCampos_busqueda_avanzada";
		$sparam=array(
			'pxIdBuscador'=> $datos['xIdBuscador'],
			'pIdBuscador'=> $datos['IdBuscador'],
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
		$spnombre="sel_BuscadorCampos_max_orden_xIdBuscador";
		$sparam=array(
			'pIdBuscador'=> $datos['IdBuscador']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el maximo orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function Insertar($datos)
	{
		$spnombre="ins_BuscadorCampos";
		$sparam=array(
			'pIdBuscador'=> $datos['IdBuscador'],
			'pIdCampo'=> $datos['IdCampo'],
			'pOrden'=> $datos['Orden'],
			'pDataJson'=> $datos['DataJson'],
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

		return true;
	}
	
	protected function Eliminar($datos)
	{
		$spnombre="del_BuscadorCampos_xIdBuscador_xIdCampo";
		$sparam=array(
			'pIdBuscador'=> $datos['IdBuscador'],
			'pIdCampo'=> $datos['IdCampo']
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
		$spnombre="upd_BuscadorCampos_Orden_xIdBuscador_xIdCampo";
		$sparam=array(
			'pOrden'=> $datos['Orden'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdBuscador'=> $datos['IdBuscador'],
			'pIdCampo'=> $datos['IdCampo']
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
		$spnombre="upd_BuscadorCampos_DataJson_xIdBuscador_xIdCampo";
		$sparam=array(
			'pDataJson'=> $datos['DataJson'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdBuscador'=> $datos['IdBuscador'],
			'pIdCampo'=> $datos['IdCampo']
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