<?php 
abstract class cUsuariosClientesEmpresasdb
{


	function __construct(){}

	function __destruct(){}

	
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosClientesEmpresas_xIdUsuario_xIdCliente_xIdClienteEmpresa";
		$sparam=array(
			'pIdCliente'=> $datos['IdCliente'],
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdClienteEmpresa'=> $datos['IdClienteEmpresa']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	
	protected function TraerEmpresas($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosClientesEmpresas_xIdUsuario_xIdCliente";
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
		$spnombre="ins_UsuariosClientesEmpresas";
		$sparam=array(
			'pIdClienteEmpresa'=> $datos['IdClienteEmpresa'],
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
		$spnombre="del_UsuariosClientesEmpresas_xIdCliente_xIdUsuario";
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
	
	protected function EliminarxIdClientexIdUsuarioxIdClienteEmpresa($datos)
	{
		$spnombre="del_UsuariosClientesEmpresas_xIdCliente_xIdUsuario_xIdClienteEmpresa";
		$sparam=array(
			'pIdCliente'=> $datos['IdCliente'],
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdClienteEmpresa'=> $datos['IdClienteEmpresa']
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