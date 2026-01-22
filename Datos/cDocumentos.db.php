<?php

abstract class cDocumentosdb {
	use ManejoErrores;

	/** @var accesoBDLocal */
	protected $conexion;
	/** @var mixed */
	protected $formato;
	/** @var array */
	protected $error;

	/**
	 * Constructor de la clase cEscuelasPuestosPersonasDB.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion, $formato) {

		$this->conexion = &$conexion;
		$this->formato = &$formato;
	}


	function __destruct() {
	}

	public function TotalCoberturasSolicitadas($datos, &$resultado, &$numfilas) {
		$datos['size'] = NULL;

		$sql = $this->GenerarSqlCoberturasSolicitadas($datos);

		$sql = "SELECT COUNT(*) cantidad FROM ($sql) tabla";

		if (!$this->conexion->_EjecutarQuery($sql, 'Error al ejecutar la consulta en la base de datos', $resultado, $errno))
			return false;

		return true;
	}

	public function BusquedaAvanzada($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Documentos_busqueda_avanzada";
		$sparam = array(
			'pxIdDocumento' => $datos['xIdDocumento'],
			'pIdDocumento' => $datos['IdDocumento'],
			'pxIdArea' => $datos['xIdArea'],
			'pIdArea' => $datos['IdArea'],
			'pxIdTipoDocumento' => $datos['xIdTipoDocumento'],
			'pIdTipoDocumento' => $datos['IdTipoDocumento'],
			'pxIdEstado' => $datos['xIdEstado'],
			'pIdEstado' => $datos['IdEstado'],
			'pxFechaDesde' => $datos['xFechaDesde'],
			'pFechaDesde' => $datos['FechaDesde'],
			'pxFechaHasta' => $datos['xFechaHasta'],
			'pFechaHasta' => $datos['FechaHasta'],
			'pxIdCategoria' => $datos['xIdCategoria'],
			'pIdCategoria' => $datos['IdCategoria'],
			'pxIdEscuela' => $datos['xIdEscuela'],
			'pIdEscuela' => $datos['IdEscuela'],
			'plimit' => $datos['limit'],
			'porderby' => $datos['orderby']
		);


		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;

	}

	protected function BuscarxCodigo($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Documentos_xIdDocumento";
		$sparam = array(
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}


		return true;
	}

	protected function BuscarxCodigos($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Documentos_xIdDocumentos";
		$sparam = array(
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}


		return true;
	}

	protected function BuscarUltimoDocumentoIngresado(&$resultado, &$numfilas) {
		$spnombre = "sel_Documentos_UltimoIngresado";
		$sparam = array();
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar el �ltimo documento ingresado. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarxCodigoPadre($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Documentos_xIdDocumentoPadre";
		$sparam = array(
			'pIdDocumentoPadre' => $datos['IdDocumentoPadre']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}

	protected function BusquedaAvanzadaxCodigoPadre($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Documentos_busqueda_avanzada_xIdDocumentoPadre";
		$sparam = array(
			'pIdDocumentoPadre' => $datos['IdDocumentoPadre'],
			'pxIdEstado' => $datos['xIdEstado'],
			'pIdEstado' => $datos['IdEstado'],
			'pxNotIdEstado' => $datos['xNotIdEstado'],
			'pNotIdEstado' => $datos['NotIdEstado'],
			'plimit' => $datos['limit'],
			'porderby' => $datos['orderby']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarxCodigoFormatoElastic($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Documentos_formato_elastic_xIdDocumento";
		$sparam = array(
			'pBase' => BASEDATOS,
			'pBasePersonas' => BASEDATOS_PERSONAS,
			'pBaseLicencias' => BASEDATOSLICENCIAS,
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarDocumentosRaizVigentes($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Documentos_xVigencia";
		$sparam = array(
			'pVigencia' => $datos['Vigencia']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarDocumentoRaizxIdDocumento($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Documento_Raiz_xIdDocumeto";
		$sparam = array(
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}

	protected function BusquedaAvanzadaDocumentosRaizVigentes($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Documentos_xVigencia_busqueda_avanzada";
		$sparam = array(
			'pVigencia' => $datos['Vigencia'],
			'pIdCliente' => $datos['IdCliente'],
			'pxIdArea' => $datos['xIdArea'],
			'pIdArea' => $datos['IdArea'],
			'pxIdTipoDocumento' => $datos['xIdTipoDocumento'],
			'pIdTipoDocumento' => $datos['IdTipoDocumento'],
			'pxCuit' => $datos['xCuit'],
			'pCuit' => $datos['Cuit']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarDocumentosRaizVigentesxIdClientexIdUsuario($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Documentos_xVigencia_IdCliente_IdUsuario";
		$sparam = array(
			'pVigencia' => $datos['Vigencia'],
			'pIdCliente' => $datos['IdCliente'],
			'pIdUsuario' => $datos['IdUsuario']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}


	/*protected function BuscarInasistenciaRelacionadoxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Documentos_InasistenciaRelacionado__xIdDocumento_IdCategoria";
		$sparam=array(
			'pIdDocumento'=> $datos['IdDocumento'],
			'pIdCategoria'=> $datos['IdCategoria']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		return true;
	}*/


	protected function BuscarEscuelasCombo(&$resultado, ?int &$numfilas): bool {
		$spnombre = "sel_Escuelas_Combo";
		$sparam = array();
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}


	protected function Insertar($datos, &$codigoinsertado) {
		$spnombre = "ins_Documentos";
		$sparam = array(
			'pIdEscuela' => $datos['IdEscuela'],
			'pIdEscuelaDestino' => $datos['IdEscuelaDestino'],
			'pIdDocumentoPadre' => $datos['IdDocumentoPadre'],
			'pIdTipoDocumento' => $datos['IdTipoDocumento'],
			'pIdRegistroTipoDocumento' => $datos['IdRegistroTipoDocumento'],
			'pIdPuesto' => $datos['IdPuesto'],
			'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
			'pIdPersona' => $datos['IdPersona'],
			'pIdLicencia' => $datos['IdLicencia'],
			'pPeriodoFechaDesde' => $datos['PeriodoFechaDesde'],
			'pPeriodoFechaHasta' => $datos['PeriodoFechaHasta'],
			'pObservaciones' => $datos['Observaciones'],
			'pIdArea' => $datos['IdArea'],
			'pIdEstado' => $datos['IdEstado'],
			'pIdAreaInicial' => $datos['IdAreaInicial'],
			'pIdEstadoInicial' => $datos['IdEstadoInicial'],
			'pFechaEnvio' => $datos['FechaEnvio'],
			'pFechaTomaPosesion' => $datos['FechaTomaPosesion'],
			'pFechaDesignacion' => $datos['FechaDesignacion'],
			'pNroResolucion' => $datos['NroResolucion'],
			'pAltaUsuario' => $datos['AltaUsuario'],
			'pAltaFecha' => $datos['AltaFecha'],
			'pAltaEscuela' => $datos['AltaEscuela'],
			'pAltaRol' => $datos['AltaRol'],
			'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionEscuela' => $datos['UltimaModificacionEscuela'],
			'pUltimaModificacionRol' => $datos['UltimaModificacionRol'],
		);


		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		$codigoinsertado = $this->conexion->UltimoCodigoInsertado();

		return true;
	}


	protected function Modificar($datos) {
		$spnombre = "upd_Documentos_xIdDocumento";
		$sparam = array(
			'pIdEscuela' => $datos['IdEscuela'],
			'pIdEscuelaDestino' => $datos['IdEscuelaDestino'],
			'pIdDocumentoPadre' => $datos['IdDocumentoPadre'],
			'pIdTipoDocumento' => $datos['IdTipoDocumento'],
			'pIdRegistroTipoDocumento' => $datos['IdRegistroTipoDocumento'],
			'pIdPuesto' => $datos['IdPuesto'],
			'pIdPersona' => $datos['IdPersona'],
			'pIdLicencia' => $datos['IdLicencia'],
			'pPeriodoFechaDesde' => $datos['PeriodoFechaDesde'],
			'pPeriodoFechaHasta' => $datos['PeriodoFechaHasta'],
			'pObservaciones' => $datos['Observaciones'],
			'pFechaEnvio' => $datos['FechaEnvio'],
			'pFechaTomaPosesion' => $datos['FechaTomaPosesion'],
			'pFechaDesignacion' => $datos['FechaDesignacion'],
			'pNroResolucion' => $datos['NroResolucion'],
			'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionEscuela' => $datos['UltimaModificacionEscuela'],
			'pUltimaModificacionRol' => $datos['UltimaModificacionRol'],
			'pIdDocumento' => $datos['IdDocumento'],
		);


		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}


	protected function ModificarDatosLicenciaAutomatica($datos) {
		$spnombre = "upd_Documentos_LicenciaAutomatica_xIdDocumento";
		$sparam = array(
			'pPeriodoFechaDesde' => $datos['PeriodoFechaDesde'],
			'pPeriodoFechaHasta' => $datos['PeriodoFechaHasta'],
			'pLicenciaEncuadreArticulo' => $datos['LicenciaEncuadreArticulo'],
			'pDatosJsonLicencia' => $datos['DatosJsonLicencia'],
			'pDatosJsonDocumento' => $datos['DatosJsonDocumento'],
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}


	protected function ModificarHashDato(array &$datos): bool {
		$spnombre = "upd_Documentos_HashDato_xIdDocumento";
		$sparam = array(
			'pHashDato' => $datos['HashDato'],
			'pFirma' => $datos['Firma'],
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al modificar hash y firma. ");
			return false;
		}

		return true;
	}


	protected function ModificarTaskId($datos) {
		$spnombre = "upd_Documentos_TaskId_xIdDocumento";
		$sparam = array(
			'pTaskId' => $datos['TaskId'],
			'pDatosJsonLicencia' => $datos['DatosJsonLicencia'],
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}


	protected function Eliminar($datos) {
		$spnombre = "del_Documentos_xIdDocumento";
		$sparam = array(
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}


	protected function ModificarEstado($datos) {
		$spnombre = "upd_Documentos_IdEstado_xIdDocumento";
		$sparam = array(
			'pIdEstado' => $datos['IdEstado'],
			'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
			'pUltimaModificacionEscuela' => $datos['UltimaModificacionEscuela'],
			'pUltimaModificacionRol' => $datos['UltimaModificacionRol'],
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}


	protected function ModificarEstadoArea($datos) {
		$spnombre = "upd_Documentos_IdEstado_IdArea_xIdDocumento";
		$sparam = array(
			'pIdEstado' => $datos['IdEstado'],
			'pIdArea' => $datos['IdArea'],
			'pMovimientoFecha' => $datos['MovimientoFecha'],
			'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
			'pUltimaModificacionEscuela' => $datos['UltimaModificacionEscuela'],
			'pUltimaModificacionRol' => $datos['UltimaModificacionRol'],
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado y area. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}

	protected function ActualizarEstadoArea($datos) {
		$spnombre = "upd_Documentos_IdEstado_IdArea_xIdDocumento";
		$sparam = array(
			'pIdEstado' => $datos['IdEstado'],
			'pIdArea' => $datos['IdArea'],
			'pMovimientoFecha' => $datos['MovimientoFecha'],
			'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado y area. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}

	protected function ModificarEncuadreAntiguedad($datos) {
		$spnombre = "upd_Documentos_Encuadre_xIdDocumento";
		$sparam = array(
			'pLicenciaEncuadreArticulo' => $datos['LicenciaEncuadreArticulo'],
			'pInasistenciaAntiguedadDoc' => $datos['InasistenciaAntiguedadDoc'],
			'pInasistenciaAntiguedadAdm' => $datos['InasistenciaAntiguedadAdm'],
			'pUltimaModificacionCuil' => $datos['UltimaModificacionCuil'],
			'pUltimaModificacionEscalafon' => $datos['UltimaModificacionEscalafon'],
			'pUltimaModificacionClaveEscuela' => $datos['UltimaModificacionClaveEscuela'],
			'pUltimaModificacionApp' => APP,
			'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
			'pIdDocumento' => $datos['IdDocumento']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado y area. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}

    protected function BajarLiquidacionDocumentoDB($datos) {
        $spnombre = "upd_Documentos_BajaLiquidacion_xIdDocumento";
        $sparam = array(
            'pBajaLiquidacion' => $datos['BajaLiquidacion'],
            'pIdDocumento' => $datos['IdDocumento']
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado y area. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        return true;
    }

    protected function BuscarDocumentosAuditoriaLiquidacion(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_MovimientosNovedades_Pendientes_busqueda_avanzada";
        $sparam =  array (
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdDocumento' => $datos['IdDocumento'],
            'pxIdDocumento' => $datos['xIdDocumento'],
            'pIdPlaza' => $datos['IdPlaza'],
            'pxIdPlaza' => $datos['xIdPlaza'],
            'pCuil' => $datos['Cuil'],
            'pxCuil' => $datos['xCuil'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pxIdTipoDocumento' => $datos['xIdTipoDocumento'],
            'pFechaAlta' => $datos['FechaAlta'],
            'pxFechaAlta' => $datos['xFechaAlta'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pxFechaDesde' => $datos['xFechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pxFechaHasta' => $datos['xFechaHasta'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdNivel' => $datos['IdNivel'],
            'pxIdNivel' => $datos['xIdNivel'],
            'pIdRegion' => $datos['IdRegion'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRevista' => $datos['IdRevista'],
            'pxIdRevista' => $datos['xIdRevista'],
            'pEscuelas' => $datos['Escuelas'],
            'pxEscuelas' => $datos['xEscuelas'],
            'pIdCategoria' => $datos['IdCategoria'],
            'pxIdCategoria' => $datos['xIdCategoria'],
            'pCategoriaNovedad' => $datos['CategoriaNovedad'],
            'pxCategoriaNovedad' => $datos['xCategoriaNovedad'],
            'pIdCargo' => $datos['IdCargo'],
            'pxIdCargo' => $datos['xIdCargo'],
            'pIdMateria' => $datos['IdMateria'],
            'pxIdMateria' => $datos['xIdMateria'],
            'pIdTipoCargo' => $datos['IdTipoCargo'],
            'pxIdTipoCargo' => $datos['xIdTipoCargo'],
            'pIdAccionesDocAux' => $datos['IdAccionesDocAux'],
            'pxIdAccionesDocAux' => $datos['xIdAccionesDocAux'],
            'pExcluir_Escuela' => $datos['Excluir_Escuela'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby']
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar el movimiento novedad pendiente. ');
            return false;
        }

        return true;
    }

    protected function BuscarDocumentosAuditoriaLiquidacionCantidad(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_MovimientosNovedades_Pendientes_cantidad_busqueda_avanzada";
        $sparam =  array (
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdDocumento' => $datos['IdDocumento'],
            'pxIdDocumento' => $datos['xIdDocumento'],
            'pIdPlaza' => $datos['IdPlaza'],
            'pxIdPlaza' => $datos['xIdPlaza'],
            'pCuil' => $datos['Cuil'],
            'pxCuil' => $datos['xCuil'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pxIdTipoDocumento' => $datos['xIdTipoDocumento'],
            'pFechaAlta' => $datos['FechaAlta'],
            'pxFechaAlta' => $datos['xFechaAlta'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pxFechaDesde' => $datos['xFechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pxFechaHasta' => $datos['xFechaHasta'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdNivel' => $datos['IdNivel'],
            'pxIdNivel' => $datos['xIdNivel'],
            'pIdRegion' => $datos['IdRegion'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRevista' => $datos['IdRevista'],
            'pxIdRevista' => $datos['xIdRevista'],
            'pEscuelas' => $datos['Escuelas'],
            'pxEscuelas' => $datos['xEscuelas'],
            'pIdCategoria' => $datos['IdCategoria'],
            'pxIdCategoria' => $datos['xIdCategoria'],
            'pCategoriaNovedad' => $datos['CategoriaNovedad'],
            'pxCategoriaNovedad' => $datos['xCategoriaNovedad'],
            'pIdCargo' => $datos['IdCargo'],
            'pxIdCargo' => $datos['xIdCargo'],
            'pIdMateria' => $datos['IdMateria'],
            'pxIdMateria' => $datos['xIdMateria'],
            'pIdTipoCargo' => $datos['IdTipoCargo'],
            'pxIdTipoCargo' => $datos['xIdTipoCargo'],
            'pIdAccionesDocAux' => $datos['IdAccionesDocAux'],
            'pxIdAccionesDocAux' => $datos['xIdAccionesDocAux'],
            'pExcluir_Escuela' => $datos['Excluir_Escuela'],
            'plimit' => $datos['limit'],
        //    'porderby' => $datos['orderby']
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar la cantidad de movimiento novedad pendiente. ');
            return false;
        }

        return true;
    }


    protected function BuscarDocumentosAuditoriaLiquidacionCSV(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_MovimientosNovedades_Pendientes_csv_busqueda_avanzada";
        $sparam =  array (
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdDocumento' => $datos['IdDocumento'],
            'pxIdDocumento' => $datos['xIdDocumento'],
            'pIdPlaza' => $datos['IdPlaza'],
            'pxIdPlaza' => $datos['xIdPlaza'],
            'pCuil' => $datos['Cuil'],
            'pxCuil' => $datos['xCuil'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pxIdTipoDocumento' => $datos['xIdTipoDocumento'],
            'pFechaAlta' => $datos['FechaAlta'],
            'pxFechaAlta' => $datos['xFechaAlta'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pxFechaDesde' => $datos['xFechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pxFechaHasta' => $datos['xFechaHasta'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdNivel' => $datos['IdNivel'],
            'pxIdNivel' => $datos['xIdNivel'],
            'pIdRegion' => $datos['IdRegion'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRevista' => $datos['IdRevista'],
            'pxIdRevista' => $datos['xIdRevista'],
            'pEscuelas' => $datos['Escuelas'],
            'pxEscuelas' => $datos['xEscuelas'],
            'pIdCategoria' => $datos['IdCategoria'],
            'pxIdCategoria' => $datos['xIdCategoria'],
            'pCategoriaNovedad' => $datos['CategoriaNovedad'],
            'pxCategoriaNovedad' => $datos['xCategoriaNovedad'],
            'pIdCargo' => $datos['IdCargo'],
            'pxIdCargo' => $datos['xIdCargo'],
            'pIdMateria' => $datos['IdMateria'],
            'pxIdMateria' => $datos['xIdMateria'],
            'pIdTipoCargo' => $datos['IdTipoCargo'],
            'pxIdTipoCargo' => $datos['xIdTipoCargo'],
            'pIdAccionesDocAux' => $datos['IdAccionesDocAux'],
            'pxIdAccionesDocAux' => $datos['xIdAccionesDocAux'],
            'pExcluir_Escuela' => $datos['Excluir_Escuela'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby']
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar el movimiento novedad pendiente. ');
            return false;
        }

        return true;
    }

    protected function BuscarReintegrosIniciados($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_Documentos_ReintegrosIniciados_xIdPuesto_xIdLicencia";
        $sparam =  array (
            'pIdTipoDocumento' => REINCORPORACION_TIPO_DOCUMENTO,
            'pIdPersona' => $datos["IdPersona"],
            'pIdPuesto' => $datos["IdPuesto"],
            "pIdLicencias" => $datos["IdLicencias"]
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar los reintegros ya iniciados. ');
            return false;
        }

        return true;
    }
}

?>
