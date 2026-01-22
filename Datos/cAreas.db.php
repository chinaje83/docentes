<?php 
abstract class cAreasdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxIdRegistro($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_xIdRegistro";
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
	
	
	protected function BuscarxCodigosActivos($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_xIdsAreasActivas";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pVigencia'=> $datos['Vigencia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_xIdArea";
		$sparam=array(
			'pIdArea'=> $datos['IdArea']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarValidacionVigencia($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_ValidacionVigencia";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pxIdRegistro'=> $datos['xIdRegistro'],
			'pIdRegistro'=> $datos['IdRegistro'],
			'pVigenciaDesde'=> $datos['VigenciaDesde'],
			'pVigenciaHasta'=> $datos['VigenciaHasta']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la vigencia del tipo de documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarxIdAreaVigente($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_xIdArea_Vigente";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pIdArea'=> $datos['IdArea']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo de tipo de documento vigente. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarAreasxAreaSuperior($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_xIdAreaSuperior";
		$sparam=array(
			'pIdAreaSuperior'=> $datos['IdAreaSuperior'],
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener la areas por area superior. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	
	protected function BuscaAreasRaiz($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_xIdAreaSuperiorNull";
		$sparam=array(
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado']
			);


		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener la categoria por categoria superior. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	protected function BuscarAreasxAreaSuperiorVigente($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_xIdAreaSuperior_Vigente";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pIdAreaSuperior'=> $datos['IdAreaSuperior'],
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener la areas por area superior. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	protected function BuscaAreasRaizVigente($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_xIdAreaSuperiorNull_Vigente";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado']
			);


		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener la categoria por categoria superior. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}



	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_busqueda_avanzada";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pxIdRegistro'=> $datos['xIdRegistro'],
			'pIdRegistro'=> $datos['IdRegistro'],
			'pxIdArea'=> $datos['xIdArea'],
			'pIdArea'=> $datos['IdArea'],
			'pxIdTipo'=> $datos['xIdTipo'],
			'pIdTipo'=> $datos['IdTipo'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado'],
			'pxIdAreaSuperior'=> $datos['xIdAreaSuperior'],
			'pIdAreaSuperior'=> $datos['IdAreaSuperior'],
			'pxRecepcionAutomatica'=> $datos['xRecepcionAutomatica'],
			'pRecepcionAutomatica'=> $datos['RecepcionAutomatica'],
			'pxTieneBandejaEntrada'=> $datos['xTieneBandejaEntrada'],
			'pTieneBandejaEntrada'=> $datos['TieneBandejaEntrada'],
			'pxTieneBandejaSalida'=> $datos['xTieneBandejaSalida'],
			'pTieneBandejaSalida'=> $datos['TieneBandejaSalida'],
			'pxModificaCircuito'=> $datos['xModificaCircuito'],
			'pModificaCircuito'=> $datos['ModificaCircuito'],
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



	protected function BusquedaAvanzadaVigenciaHastaNull($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_busqueda_avanzada_xVigenciaHastaNull";
		$sparam=array(
			'pxIdRegistro'=> $datos['xIdRegistro'],
			'pIdRegistro'=> $datos['IdRegistro'],
			'pxIdArea'=> $datos['xIdArea'],
			'pIdArea'=> $datos['IdArea'],
			'pxIdTipo'=> $datos['xIdTipo'],
			'pIdTipo'=> $datos['IdTipo'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxIdAreaSuperior'=> $datos['xIdAreaSuperior'],
			'pIdAreaSuperior'=> $datos['IdAreaSuperior'],
			'pxRecepcionAutomatica'=> $datos['xRecepcionAutomatica'],
			'pRecepcionAutomatica'=> $datos['RecepcionAutomatica'],
			'pxTieneBandejaEntrada'=> $datos['xTieneBandejaEntrada'],
			'pTieneBandejaEntrada'=> $datos['TieneBandejaEntrada'],
			'pxTieneBandejaSalida'=> $datos['xTieneBandejaSalida'],
			'pTieneBandejaSalida'=> $datos['TieneBandejaSalida'],
			'pxModificaCircuito'=> $datos['xModificaCircuito'],
			'pModificaCircuito'=> $datos['ModificaCircuito'],
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
		$spnombre="sel_Areas_AuditoriaRapida";
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
	
	
	protected function BuscarUltimoIdArea(&$resultado,&$numfilas)
	{
		$spnombre="sel_Areas_proximo_IdArea";
		$sparam=array(
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el ultimo id de la empresa. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function InsertarDB($datos,&$codigoinsertado)
	{
		$spnombre="ins_Areas";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pIdTipo'=> $datos['IdTipo'],
			'pNombre'=> $datos['Nombre'],
			'pDescripcion'=> $datos['Descripcion'],
			'pIdAreaSuperior'=> $datos['IdAreaSuperior'],
			'pIdAreaRaiz'=> $datos['IdAreaRaiz'],
			'pDeriva'=> $datos['Deriva'],
			'pRecepcionAutomatica'=> $datos['RecepcionAutomatica'],
			'pTieneBandejaEntrada'=> $datos['TieneBandejaEntrada'],
			'pTieneBandejaSalida'=> $datos['TieneBandejaSalida'],
			'pModificaCircuito'=> $datos['ModificaCircuito'],
			'pVigenciaDesde'=> $datos['VigenciaDesde'],
			'pVigenciaHasta'=> $datos['VigenciaHasta'],
			'pJsonPadres'=> $datos['JsonPadres'],
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
		$spnombre="upd_Areas_xIdRegistro";
		$sparam=array(
			'pIdTipo'=> $datos['IdTipo'],
			'pNombre'=> $datos['Nombre'],
			'pDescripcion'=> $datos['Descripcion'],
			'pIdAreaSuperior'=> $datos['IdAreaSuperior'],
			'pIdAreaRaiz'=> $datos['IdAreaRaiz'],
			'pDeriva'=> $datos['Deriva'],
			'pRecepcionAutomatica'=> $datos['RecepcionAutomatica'],
			'pTieneBandejaEntrada'=> $datos['TieneBandejaEntrada'],
			'pTieneBandejaSalida'=> $datos['TieneBandejaSalida'],
			'pModificaCircuito'=> $datos['ModificaCircuito'],
			'pJsonPadres'=> $datos['JsonPadres'],
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
	
	
	
	protected function ModificarAreaRaiz($datos)
	{
		$spnombre="upd_Areas_IdAreaRaiz_xIdRegistro";
		$sparam=array(
			'pIdAreaRaiz'=> $datos['IdAreaRaiz'],
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
	
	protected function ModificarVigencia($datos)
	{
		$spnombre="upd_Areas_Vigencia_xIdRegistro";
		$sparam=array(
			'pVigenciaDesde'=> $datos['VigenciaDesde'],
			'pVigenciaHasta'=> $datos['VigenciaHasta'],
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

	
	protected function ModificarVigenciaHasta($datos)
	{
		$spnombre="upd_Areas_VigenciaHasta_xIdRegistro";
		$sparam=array(
			'pVigenciaHasta'=> $datos['VigenciaHasta'],
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
		$spnombre="del_Areas_xIdRegistro";
		$sparam=array(
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			echo "aca";die;
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function ModificarEstado($datos)
	{
		$spnombre="upd_Areas_Estado_xIdRegistro";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'], 
			'pUltimaModificacionFecha'=>  date("Y-m-d H:i:s"),
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function ModificarBandejaEntrada($datos)
	{
		$spnombre="upd_Areas_TieneBandejaEntrada_xIdRegistro";
		$sparam=array(
			'pTieneBandejaEntrada'=> $datos['TieneBandejaEntrada'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=>  date("Y-m-d H:i:s"),
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function ModificarBandejaSalida($datos)
	{
		$spnombre="upd_Areas_TieneBandejaSalida_xIdRegistro";
		$sparam=array(
			'pTieneBandejaSalida'=> $datos['TieneBandejaSalida'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=>  date("Y-m-d H:i:s"),
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function ModificarRecepcionAutomatica($datos)
	{
		$spnombre="upd_Areas_RecepcionAutomatica_xIdRegistro";
		$sparam=array(
			'pRecepcionAutomatica'=> $datos['RecepcionAutomatica'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=>  date("Y-m-d H:i:s"),
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function ModificarModificaCircuito($datos)
	{
		$spnombre="upd_Areas_ModificaCircuito_xIdRegistro";
		$sparam=array(
			'pModificaCircuito'=> $datos['ModificaCircuito'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=>  date("Y-m-d H:i:s"),
			'pIdRegistro'=> $datos['IdRegistro']
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