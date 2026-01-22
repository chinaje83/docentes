<?php 
abstract class cModulosConstantesdb
{


	function __construct(){}

	function __destruct(){}


	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosConstantes_xIdRegistro";
		$sparam=array(
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	

	protected function BuscarxIdConstante($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosConstantes_xIdConstante";
		$sparam=array(
			'pIdConstante'=> $datos['IdConstante']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo de empresa. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarxIdConstanteVigenteDatosCompletos($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosConstantes_xIdConstante_Vigente_DatosCompletos";
		$sparam=array(
			'pIdConstante'=> $datos['IdConstante'],
			'pPeriodoDesde'=> $datos['PeriodoDesde'],
			'pPeriodoHasta'=> $datos['PeriodoHasta']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los datos completos por Id de Empresa. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarValidacionVigencia($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosConstantes_ValidacionVigencia";
		$sparam=array(
			'pIdConstante'=> $datos['IdConstante'],
			'pxIdRegistro'=> $datos['xIdRegistro'],
			'pIdRegistro'=> $datos['IdRegistro'],
			'pVigenciaDesde'=> $datos['VigenciaDesde'],
			'pVigenciaHasta'=> $datos['VigenciaHasta']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la vigencia de la camara de transporte. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarModulosConstantesVigentes($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosConstantes_Vigentes";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las camaras vigentes. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarModulosConstantesVigentesxIdConstante($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosConstantes_Vigentes_xIdConstante";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pIdConstante'=> $datos['IdConstante']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las camaras vigentes por IdConstante. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxIdModuloConstante($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosConstantes_xIdModulo_Constante";
		$sparam=array(
			'pIdModulo'=> $datos['IdModulo'], 
			'pConstante'=> $datos['Constante']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la empresa por cámara. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarxIdConstanteVigente($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosConstantes_xIdConstante_Vigente";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pIdConstante'=> $datos['IdConstante']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo de empresa vigente. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarUltimoIdConstante(&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosConstantes_proximo_IdConstante";
		$sparam=array(
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el ultimo id de la empresa. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarAuditoriaRapidaxIdRegistro($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosConstantes_AuditoriaRapida";
		$sparam=array(
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	
	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosConstantes_busqueda_avanzada";
		$sparam=array(
			'pxIdModulo'=> $datos['xIdModulo'],
			'pIdModulo'=> $datos['IdModulo'],
			'pxIdConstante'=> $datos['xIdConstante'],
			'pIdConstante'=> $datos['IdConstante'],
			'pxConstante'=> $datos['xConstante'],
			'pConstante'=> $datos['Constante'],
			'pxDescripcion'=> $datos['xDescripcion'],
			'pDescripcion'=> $datos['Descripcion'],
			'pxValorConstante'=> $datos['xValorConstante'],
			'pValorConstante'=> $datos['ValorConstante'],
			'pxVigencia'=> $datos['xVigencia'],
			'pVigencia'=> $datos['Vigencia'],
			'pVigencia'=> $datos['Vigencia'],
			'porderby'=> $datos['orderby'],
			'plimit'=> $datos['limit']
			);
				
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function InsertarDB($datos,&$codigoinsertado)
	{
		$spnombre="ins_ModulosConstantes";
		$sparam=array(
			'pIdModulo'=> $datos['IdModulo'],
			'pIdConstante'=> $datos['IdConstante'],
			'pConstante'=> $datos['Constante'],
			'pDescripcion'=> $datos['Descripcion'],
			'pValorConstante'=> $datos['ValorConstante'],
			'pVigenciaDesde'=> $datos['VigenciaDesde'],
			'pVigenciaHasta'=> $datos['VigenciaHasta'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$this->conexion->TextoError(),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}



	protected function Modificar($datos)
	{
		$spnombre="upd_ModulosConstantes_xIdRegistro";
		$sparam=array(
			'pDescripcion'=> $datos['Descripcion'],
			'pValorConstante'=> $datos['ValorConstante'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$this->conexion->TextoError(),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function ModificarVigencia($datos)
	{
		$spnombre="upd_ModulosConstantes_Vigencia_xIdRegistro";
		$sparam=array(
			'pVigenciaDesde'=> $datos['VigenciaDesde'],
			'pVigenciaHasta'=> ($datos['VigenciaHasta']==""?"NULL":$datos['VigenciaHasta']),
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$this->conexion->TextoError(),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Eliminar($datos)
	{
		$spnombre="del_ModulosConstantes_xIdRegistro";
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