<?php 
abstract class cUsuariosDocumentosdb
{


	function __construct(){}

	function __destruct(){}

	
	
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosDocumentos_xIdUsuario_xIdDocumento";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdDocumento'=> $datos['IdDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxIdDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosDocumentos_xIdDocumento";
		$sparam=array(
			'pIdDocumento'=> $datos['IdDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarDocumentosAgrupadoxIdTipoDocumentoxIdUsuario($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosDocumentos_AgurpadoxIdTipoDocumento_xIdUsuario";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario']
			
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxUsuarioxTipoDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosDocumentos_xIdUsuario_xIdTipoDocumento";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por tipo de documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarxUsuario($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosDocumentos_xIdUsuario";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarUsuariosSinDocumentos($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosSinDocumentos";
		$sparam=array(
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxApellido'=> $datos['xApellido'],
			'pApellido'=> $datos['Apellido'],
			'pxTipoEvitar'=> $datos['xTipoEvitar'],
			'pTipoEvitar'=> $datos['TipoEvitar'],
			'pxIdEstado'=> $datos['xIdEstado'],
			'pIdEstado'=> $datos['IdEstado'],
			'plimit'=> $datos['limit'],
			'porderby'=> $datos['orderby']
	);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los usuarios sin documentos. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	
	protected function Insertar($datos)
	{
		$spnombre="ins_UsuariosDocumentos";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdDocumento'=> $datos['IdDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pBajaFecha'=> $datos['BajaFecha'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pRegistroSeguridad'=> $datos['RegistroSeguridad']
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
		$spnombre="del_UsuariosDocumentos_xIdUsuario_xIdDocumento";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdDocumento'=> $datos['IdDocumento']
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