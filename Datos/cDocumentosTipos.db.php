<?php
abstract class cDocumentosTiposdb
{


	function __construct(){}

	function __destruct(){}



	protected function relacionTipsCategoriasSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_DocumentosTiposCategorias_combo_Nombre";
		$sparam=array(
		);
		return true;
	}

	protected function cantidadEscuelasSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_Escuelas_cantidad";
		$sparam=array(
		);
		return true;
	}



	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_xIdRegistro";
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



	protected function BuscarAreasEstadosxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_Areas_Estados_xIdTipoDocumento";
		$sparam=array(
			'pIdTipoDocumento'=> $datos['IdTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarxIdTipoDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_xIdTipoDocumento";
		$sparam=array(
			'pIdTipoDocumento'=> $datos['IdTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarxIdCircuito($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_xIdCircuito";
		$sparam=array(
			'pIdCircuito'=> $datos['IdCircuito']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarCamposxIdRegistroTipoDocumentoValidacionDatos($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposCampos_xIdRegistroTipoDocumento_Validacion";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarxIdTipoDocumentoVigente($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_xIdTipoDocumento_Vigente";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo de tipo de documento vigente. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarTiposDocumentosxIdxIdAreaVigente($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposEstructura_xIdEstructura_IdArea_Vigente";
		$sparam=array(
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			"pVigencia"=>$datos['Vigencia'],
			"pIdArea"=>$datos['IdArea']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la estructura vigente por id y area. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





	protected function BuscarCamposRelacionadosxIdRegistroTipoDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_EstructuraCampos_Busqueda_xIdRegistroTipoDocumento";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo de Tipo de Documento (registro). ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BuscarValidacionVigencia($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_ValidacionVigencia";
		$sparam=array(
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
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

	protected function BuscarUltimoIdTipoDocumento(&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_proximo_IdTipoDocumento";
		$sparam=array(
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el ultimo id de la empresa. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}




	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_busqueda_avanzada";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pxIdNivel'=> $datos['xIdNivel'],
			'pIdNivel'=> $datos['IdNivel'],
			'pxEstadoFiltro'=> $datos['xEstadoFiltro'],
			'pEstadoFiltro'=> $datos['EstadoFiltro'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxNombreCorto'=> $datos['xNombreCorto'],
			'pNombreCorto'=> $datos['NombreCorto'],
			'pxIdClasificacion'=> $datos['xIdClasificacion'],
			'pIdClasificacion'=> $datos['IdClasificacion'],
            'pxIdCategoria'=> $datos['xIdCategoria'],
            'pIdCategoria'=> $datos['IdCategoria'],
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


	protected function BuscarTipoDocumentoxTipoDocumentoPadre($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_xIdTipoDocumentoPadre";
		$sparam=array(
			'pIdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
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


	protected function BuscarTipoDocumentoxTipoDocumentoPadreVigente($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_xIdTipoDocumentoPadre_Vigente";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pIdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
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


	protected function BuscarTipoDocumentoRaizVigente($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_xIdTipoDocumentoPadreNull_Vigente";
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



	protected function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_AuditoriaRapida";
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



	protected function InsertarDB($datos,&$codigoinsertado)
	{
		$spnombre="ins_DocumentosTipos";
		$sparam=array(
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
			'pIdCategoria'=> $datos['IdCategoria'],
			'pIdNivel'=> $datos['IdNivel'],
			'pNombre'=> $datos['Nombre'],
			'pNombreCorto'=> $datos['NombreCorto'],
			'pDescripcionCorta'=> $datos['DescripcionCorta'],
			'pDescripcionLarga'=> $datos['DescripcionLarga'],
			'pIdClasificacion'=> $datos['IdClasificacion'],
			'pDocumentoProgramado'=> $datos['DocumentoProgramado'],
			'pTieneAdjunto'=> $datos['TieneAdjunto'],
			'pClase'=> $datos['Clase'],
			'pMetodo'=> $datos['Metodo'],
			'pDocumentoAgrupador' =>$datos['DocumentoAgrupador'],
			'pClass' => $datos['Class'],
			'pCargaPopup' => $datos['CargaPopup'],
			'pClassEjecutar' => $datos['ClassEjecutar'],
			'pAltaProgramada' => $datos['AltaProgramada'],
			'pUrlAltaProgramada' => $datos['UrlAltaProgramada'],
			'pDocumentoExterno' => $datos['DocumentoExterno'],
			'pDocumentosIlimitados' => $datos['DocumentosIlimitados'],
			'pCantidadDocumentos' => $datos['CantidadDocumentos'],
			'pMuestraTitulo' => $datos['MuestraTitulo'],
			'pMostrarAlta' => $datos['MostrarAlta'],
            'pMostrarAltaPON' => $datos['MostrarAltaPON'],
			'pBajaLiquidacion' => $datos['BajaLiquidacion'],
			'pTituloAgrupadorDependientes' => $datos['TituloAgrupadorDependientes'],
			'pCodigoHost' => $datos['CodigoHost'],
			'pEstado'=> $datos['Estado'],
			'pVigenciaDesde'=> $datos['VigenciaDesde'],
			'pVigenciaHasta'=> $datos['VigenciaHasta'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaApp'=> APP,
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pConstante'=> $datos['Constante'],
			'pUltimaModificacionApp'=> APP
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
        $spnombre="upd_DocumentosTipos_xIdRegistro";
		$sparam=array(
			'pIdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
			'pIdCategoria'=> $datos['IdCategoria'],
			'pIdNivel'=> $datos['IdNivel'],
			'pAreaRecepcion'=> $datos['AreaRecepcion'],
			'pEstadoRecepcion'=> $datos['EstadoRecepcion'],
			'pNombre'=> $datos['Nombre'],
			'pNombreCorto'=> $datos['NombreCorto'],
			'pDescripcionCorta'=> $datos['DescripcionCorta'],
			'pDescripcionLarga'=> $datos['DescripcionLarga'],
			'pIdClasificacion'=> $datos['IdClasificacion'],
			'pDocumentoProgramado'=> $datos['DocumentoProgramado'],
			'pTieneCircuito'=> $datos['TieneCircuito'],
			'pTieneAdjunto'=> $datos['TieneAdjunto'],
			'pClase'=> $datos['Clase'],
			'pMetodo'=> $datos['Metodo'],
			'pDocumentoAgrupador' =>$datos['DocumentoAgrupador'],
			'pClass' => $datos['Class'],
			'pCargaPopup' => $datos['CargaPopup'],
			'pClassEjecutar' => $datos['ClassEjecutar'],
			'pAltaProgramada' => $datos['AltaProgramada'],
			'pUrlAltaProgramada' => $datos['UrlAltaProgramada'],
			'pDocumentoExterno' => $datos['DocumentoExterno'],
			'pDocumentosIlimitados' => $datos['DocumentosIlimitados'],
			'pCantidadDocumentos' => $datos['CantidadDocumentos'],
			'pMuestraTitulo' => $datos['MuestraTitulo'],
			'pMostrarAlta' => $datos['MostrarAlta'],
            'pMostrarAltaPON' => $datos['MostrarAltaPON'],
            'pBajaLiquidacion' => $datos['BajaLiquidacion'],
			'pTituloAgrupadorDependientes' => $datos['TituloAgrupadorDependientes'],
			'pCodigoHost' => $datos['CodigoHost'],
			'pEstadoPadreDependiente' => $datos['EstadoPadreDependiente'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pConstante'=> $datos['Constante'],
			'pIdRegistro'=> $datos['IdRegistro'],
			'pUltimaModificacionApp'=> APP
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function ModificarEvento($datos)
	{
		$spnombre="upd_DocumentosTipos_Evento_xIdRegistro";
		$sparam=array(
			'pLoadTipoDocumento' => $datos['LoadTipoDocumento'],
			'pUnLoadTipoDocumento' => $datos['UnLoadTipoDocumento'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdRegistro'=> $datos['IdRegistro'],
			'pUltimaModificacionApp'=> APP
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function ModificarDatosCamposJson($datos)
	{
		$spnombre="upd_DocumentosTipos_DatosCamposJson_xIdRegistro";
		$sparam=array(
			'pDatosCamposJson' => $datos['DatosCamposJson'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdRegistro'=> $datos['IdRegistro'],
			'pUltimaModificacionApp'=> APP
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function ModificarCircuitoDB($datos)
	{
		$spnombre="upd_DocumentosTipos_Circuito_xIdRegistro";
		$sparam=array(
			'pTieneCircuito'=> "1",
			'pIdCircuito'=> $datos['IdCircuito'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
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
		$spnombre="upd_DocumentosTipos_Vigencia_xIdRegistro";
		$sparam=array(
			'pVigenciaDesde'=> $datos['VigenciaDesde'],
			'pVigenciaHasta'=> $datos['VigenciaHasta'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
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
		$spnombre="del_DocumentosTipos_xIdRegistro";
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
		$spnombre="upd_DocumentosTipos_Estado_xIdRegistro";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


    protected function BuscarTiposDocumentoPofa(array $datos, &$resultado, ?int &$numfilas) {

        $sql="SELECT * FROM DocumentosTipos WHERE IdTipoDocumento IN(".implode(",",NOV_ALTA_POFA).")";

        if (!$this->conexion->ejecutarSQL($sql, "SEL", $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda avanzada cantidad. ');
            return false;
        }

        return true;
    }


	protected function BusquedaConstante($datos, &$resultado, &$numfilas)
	{
		$spnombre = "sel_DocumentosTipos_existe_Constante";
		$sparam = array(
			'ptable'=> BASEDATOS,
			'pConstante'   => $datos['Constante'],
			'pxIdTipoDocumento'   => $datos['xIdTipoDocumento'],
			'pIdTipoDocumento'   => $datos['IdTipoDocumento']
		);

		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje(
				$this->conexion,
				MSG_ERRGRAVE,
				"Error al buscar la constante.",
				array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__),
				array("formato" => $this->formato)
			);
			return false;
		}

		return true;
	}

}
?>
