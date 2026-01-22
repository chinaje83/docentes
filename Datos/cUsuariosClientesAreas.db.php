<?php 
abstract class cUsuariosClientesAreasdb
{


	function __construct(){}

	function __destruct(){}

	
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_usuarios_Areas_xIdUsuario_xIdCliente_xIdArea";
		$sparam=array(
			'pIdCliente'=> $datos['IdCliente'],
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdArea'=> $datos['IdArea']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	
	protected function TraerAreas($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_usuarios_Areas_Proyectos_xIdUsuario_xIdCliente";
		$sparam=array(
			'pIdCliente'=> $datos['IdCliente'],
			'pIdUsuario'=> $datos['IdUsuario']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function Insertar($datos)
	{
		$spnombre="ins_UsuariosAreas";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pIdCliente'=> $datos['IdCliente'],
			'pIdUsuario'=> $datos['IdUsuario'],
			'pAltaFecha'=> date("Y-m-d H:i:s"),
			'pAltaUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod']
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
		$spnombre="del_UsuariosAreas_xIdCliente_xIdUsuario";
		$sparam=array(
			'pIdCliente'=> $datos['IdCliente'],
			'pIdUsuario'=> $datos['IdUsuario']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function EliminarxIdClientexIdUsuarioxIdArea($datos)
	{
		$spnombre="del_UsuariosAreas_xIdCliente_xIdUsuario_xIdArea";
		$sparam=array(
			'pIdCliente'=> $datos['IdCliente'],
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdArea'=> $datos['IdArea']
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