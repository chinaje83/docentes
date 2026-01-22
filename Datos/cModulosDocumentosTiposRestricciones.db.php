<?php 
abstract class cModulosDocumentosTiposRestriccionesdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosDocumentosTiposRestricciones_xIdRegistro";
		$sparam=array(
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxIdDocumentoTipoModuloxIdDocumentoTipoModuloRestriccion($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosDocumentosTiposRestricciones_xIdDocumentoTipoModulo_IdDocumentoTipoModuloRestriccion";
		$sparam=array(
			'pIdDocumentoTipoModulo'=> $datos['IdDocumentoTipoModulo'],
			'pIdDocumentoTipoModuloRestriccion'=> $datos['IdDocumentoTipoModuloRestriccion']
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
		$spnombre="sel_ModulosDocumentosTiposRestricciones_busqueda_avanzada";
		$sparam=array(
			'pxIdRegistro'=> $datos['xIdRegistro'],
			'pIdRegistro'=> $datos['IdRegistro'],
			'pxIdDocumentoTipoModulo'=> $datos['xIdDocumentoTipoModulo'],
			'pIdDocumentoTipoModulo'=> $datos['IdDocumentoTipoModulo'],
			'pxIdDocumentoTipoModuloRestriccion'=> $datos['xIdDocumentoTipoModuloRestriccion'],
			'pIdDocumentoTipoModuloRestriccion'=> $datos['IdDocumentoTipoModuloRestriccion'],
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
	
	protected function BusquedaAvanzadaxIdDocumentoTipoModulo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosDocumentosTiposRestricciones_busqueda_avanzada_xIdDocumentoTipoModulo";
		$sparam=array(
			'pIdDocumentoTipoModulo'=> $datos['IdDocumentoTipoModulo'],
			'pIdDocumentoTipoModuloRestriccion'=> $datos['IdDocumentoTipoModuloRestriccion'],
			'pxIdRegistro'=> $datos['xIdRegistro'],
			'pIdRegistro'=> $datos['IdRegistro'],
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



	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_ModulosDocumentosTiposRestricciones";
		$sparam=array(
			'pIdDocumentoTipoModulo'=> $datos['IdDocumentoTipoModulo'],
			'pIdDocumentoTipoModuloRestriccion'=> $datos['IdDocumentoTipoModuloRestriccion'],
			'pDescripcion'=> $datos['Descripcion'],
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
		$spnombre="upd_ModulosDocumentosTiposRestricciones_xIdRegistro";
		$sparam=array(
			'pIdDocumentoTipoModulo'=> $datos['IdDocumentoTipoModulo'],
			'pIdDocumentoTipoModuloRestriccion'=> $datos['IdDocumentoTipoModuloRestriccion'],
			'pDescripcion'=> $datos['Descripcion'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function ModificarDescripcion($datos)
	{
		$spnombre="upd_ModulosDocumentosTiposRestricciones_Descripcion_xIdRegistro";
		$sparam=array(
			'pDescripcion'=> $datos['Descripcion'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdRegistro'=> $datos['IdRegistro']
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
		$spnombre="del_ModulosDocumentosTiposRestricciones_xIdRegistro";
		$sparam=array(
			'pIdRegistro'=> $datos['IdRegistro']
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