<?php 
abstract class cEstructuraObjetosTiposdb
{


	function __construct(){}

	function __destruct(){}


	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_EstructuraObjetosTipos_xIdObjeto_IdTipoCampo";
		$sparam=array(
			'pIdObjeto'=> $datos['IdObjeto'],
			'pIdTipoCampo'=> $datos['IdTipoCampo']
			);


		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los tipos de campo asociados al objeto. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	
	
	protected function BuscarxIdObjeto($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_EstructuraObjetosTipos_xIdObjeto";
		$sparam=array(
			'pIdObjeto'=> $datos['IdObjeto']
			);


		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los tipos de campo asociados al objeto. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarxIdObjetoActivos($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_EstructuraObjetosTipos_xIdObjeto_Activos";
		$sparam=array(
			'pIdObjeto'=> $datos['IdObjeto']
			);


		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los tipos de campo asociados al objeto. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BuscarTiposCampos($datos,&$resultado,&$numfilas)
	{

		$spnombre="sel_EstructuraCamposTipos_Activos";
		$sparam=array(
			);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los tipos de campos activos. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function Insertar($datos)
	{
		$spnombre="ins_EstructuraObjetosTipos";
		$sparam=array(
			'pIdObjeto'=> $datos['IdObjeto'],
			'pIdTipoCampo'=> $datos['IdTipoCampo'],
			'pTipoCampoElastic'=> $datos['TipoCampoElastic'],
			'pFechaAlta'=> $datos['FechaAlta'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
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
		$spnombre="del_EstructuraObjetosTipos_xIdObjeto_IdTipoCampo";
		$sparam=array(
			'pIdObjeto'=> $datos['IdObjeto'],
			'pIdTipoCampo'=> $datos['IdTipoCampo']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}






}
?>