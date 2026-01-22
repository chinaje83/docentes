<?php 
abstract class cClientesEmpresasdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ClientesEmpresas_xIdClienteEmpresa";
		$sparam=array(
			'pIdClienteEmpresa'=> $datos['IdClienteEmpresa']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxIdClienteActivos($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ClientesEmpresas_xIdCliente_Activos";
		$sparam=array(
			'pIdCliente'=> $datos['IdCliente']
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
		$spnombre="sel_ClientesEmpresas_busqueda_avanzada";
		$sparam=array(
			'pxIdClienteEmpresa'=> $datos['xIdClienteEmpresa'],
			'pIdClienteEmpresa'=> $datos['IdClienteEmpresa'],
			'pxIdCliente'=> $datos['xIdCliente'],
			'pIdCliente'=> $datos['IdCliente'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxNombreCorto'=> $datos['xNombreCorto'],
			'pNombreCorto'=> $datos['NombreCorto'],
			'pxCuit'=> $datos['xCuit'],
			'pCuit'=> $datos['Cuit'],
			'pxRazonSocial'=> $datos['xRazonSocial'],
			'pRazonSocial'=> $datos['RazonSocial'],
			'pxEmail'=> $datos['xEmail'],
			'pEmail'=> $datos['Email'],
			'pxTelefono'=> $datos['xTelefono'],
			'pTelefono'=> $datos['Telefono'],
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado'],
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



	protected function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ClientesEmpresas_AuditoriaRapida";
		$sparam=array(
			'pIdClienteEmpresa'=> $datos['IdClienteEmpresa']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_ClientesEmpresas";
		$sparam=array(
			'pIdCliente'=> $datos['IdCliente'],
			'pClienteNombre'=> $datos['ClienteNombre'],
			'pNombre'=> $datos['Nombre'],
			'pNombreCorto'=> $datos['NombreCorto'],
			'pCuit'=> $datos['Cuit'],
			'pRazonSocial'=> $datos['RazonSocial'],
			'pEmail'=> $datos['Email'],
			'pTelefono'=> $datos['Telefono'],
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
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
		$spnombre="upd_ClientesEmpresas_xIdClienteEmpresa";
		$sparam=array(
			'pIdCliente'=> $datos['IdCliente'],
			'pClienteNombre'=> $datos['ClienteNombre'],
			'pNombre'=> $datos['Nombre'],
			'pNombreCorto'=> $datos['NombreCorto'],
			'pCuit'=> $datos['Cuit'],
			'pRazonSocial'=> $datos['RazonSocial'],
			'pEmail'=> $datos['Email'],
			'pTelefono'=> $datos['Telefono'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdClienteEmpresa'=> $datos['IdClienteEmpresa']
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
		$spnombre="del_ClientesEmpresas_xIdClienteEmpresa";
		$sparam=array(
			'pIdClienteEmpresa'=> $datos['IdClienteEmpresa']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function ModificarEstado($datos)
	{
		$spnombre="upd_ClientesEmpresas_Estado_xIdClienteEmpresa";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdClienteEmpresa'=> $datos['IdClienteEmpresa']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>