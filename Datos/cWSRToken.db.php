<?php 
abstract class cWSRTokendb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_wsr_token_xwscod";
		$sparam=array(
			'pwscod'=> $datos['wscod']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarxTipo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_wsr_token_xwstipocod";
		$sparam=array(
			'pwstipocod'=> $datos['wstipocod']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por tipo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_wsr_token";
		$sparam=array(
			'pwstipocod'=> $datos['wstipocod'],
			'pwstoken'=> $datos['wstoken'],
			'pwsrefreshtoken'=> $datos['wsrefreshtoken'],
			'pwsestado'=> $datos['wsestado'],
			'pwsdatetime'=> $datos['wsdatetime'],
			'pwsdatetimestamp'=> $datos['wsdatetimestamp'],
			'pwsexpire'=> $datos['wsexpire'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y-m-d H:i:s")
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}



	protected function Modificar($datos)
	{
		$spnombre="upd_wsr_token_xwscod";
		$sparam=array(
			'pwstoken'=> $datos['wstoken'],
			'pwsrefreshtoken'=> $datos['wsrefreshtoken'],
			'pwsestado'=> $datos['wsestado'],
			'pwsdatetime'=> $datos['wsdatetime'],
			'pwsdatetimestamp'=> $datos['wsdatetimestamp'],
			'pwsexpire'=> $datos['wsexpire'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y-m-d H:i:s"),
			'pwscod'=> $datos['wscod']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Eliminar($datos)
	{
		$spnombre="del_wsr_token_xwscod";
		$sparam=array(
			'pwscod'=> $datos['wscod']
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