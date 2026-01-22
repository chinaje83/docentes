<?php
include(DIR_CLASES_DB . "cSolicitudesCoberturaInscriptos.db.php");

class cSolicitudesCoberturaInscriptos extends cSolicitudesCoberturaInscriptosdb {
	/**
	 * @var Elastic\Conexion|null
	 */
	private $conexionES;
	
	/**
	 * Constructor de la clase cSolicitudesCoberturaInscriptos.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal         $conexion
	 * @param mixed                 $formato
	 * @param Elastic\Conexion|null $conexionES
	 */
	function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO, ?Elastic\Conexion $conexionES = NULL) {
		parent::__construct($conexion, $formato);
		$this->conexionES = $conexionES;
	}
	
	/**
	 * Destructor de la clase cSolicitudesCoberturaInscriptos.
	 */
	function __destruct() {
		parent::__destruct();
	}
	
	public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
		if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
			return false;
		return true;
	}
	
	public function buscarxSolicitud($datos, &$resultado, &$numfilas): bool {
		return parent::buscarxSolicitud($datos, $resultado, $numfilas);
	}
	
	
	public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
		$sparam = array(
			'xId' => 0,
			'Id' => "",
			'xIdSolicitudCobertura' => 0,
			'IdSolicitudCobertura' => "",
			'xIdActoPublico' => 0,
			'IdActoPublico' => "",
			'xIdPersonaPuntaje' => 0,
			'IdPersonaPuntaje' => "",
			'xEstado' => 0,
			'Estado' => "-1",
			'limit' => '',
			'orderby' => "Id DESC"
		);
		if (isset($datos['Id']) && $datos['Id'] != "") {
			$sparam['Id'] = $datos['Id'];
			$sparam['xId'] = 1;
		}
		if (isset($datos['IdSolicitudCobertura']) && $datos['IdSolicitudCobertura'] != "") {
			$sparam['IdSolicitudCobertura'] = $datos['IdSolicitudCobertura'];
			$sparam['xIdSolicitudCobertura'] = 1;
		}
		if (isset($datos['IdActoPublico']) && $datos['IdActoPublico'] != "") {
			$sparam['IdActoPublico'] = $datos['IdActoPublico'];
			$sparam['xIdActoPublico'] = 1;
		}
		if (isset($datos['IdPersonaPuntaje']) && $datos['IdPersonaPuntaje'] != "") {
			$sparam['IdPersonaPuntaje'] = $datos['IdPersonaPuntaje'];
			$sparam['xIdPersonaPuntaje'] = 1;
		}
		if (isset($datos['Estado']) && $datos['Estado'] != "") {
			$sparam['Estado'] = $datos['Estado'];
			$sparam['xEstado'] = 1;
		}
		
		if (isset($datos['orderby']) && $datos['orderby'] != "")
			$sparam['orderby'] = $datos['orderby'];
		if (isset($datos['limit']) && $datos['limit'] != "")
			$sparam['limit'] = $datos['limit'];
		if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
			return false;
		return true;
	}
	
	
	public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas): bool {
		if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
			return false;
		return true;
	}
	
	
	public function SolicitudesCoberturaSP(&$spnombre, &$sparam): void {
		parent::SolicitudesCoberturaSP($spnombre, $sparam);
	}
	
	
	public function SolicitudesCoberturaSPResult(&$resultado, &$numfilas): bool {
		$this->SolicitudesCoberturaSP($spnombre, $sparam);
		
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	
	public function PersonasPuntajesSP(&$spnombre, &$sparam): void {
		parent::PersonasPuntajesSP($spnombre, $sparam);
	}
	
	
	public function PersonasPuntajesSPResult(&$resultado, &$numfilas): bool {
		$this->PersonasPuntajesSP($spnombre, $sparam);
		
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	
	public function Insertar($datos, &$codigoInsertado): bool {
		if (!$this->_ValidarInsertar($datos))
			return false;
		
		$this->_SetearNull($datos);
		$datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		
		
		if (!parent::Insertar($datos, $codigoInsertado))
			return false;
		
		/*$oAuditoriasSolicitudesCoberturaInscriptos = new cAuditoriasSolicitudesCoberturaInscriptos($this->conexion,$this->formato);
		$datos['Id'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		if(!$oAuditoriasSolicitudesCoberturaInscriptos->InsertarLog($datos,$codigoInsertadolog))
			return false;*/
		return true;
	}
	
	public function insertarInscriptosSimulados($datos): bool {
		$datos['IdPersonaPuntaje'] = 1;
		if (!$this->_ValidarInsertar($datos))
			return false;
		$datos['IdPersonaPuntaje'] = NULL;
		$this->_SetearNull($datos);
		$datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
		$datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		return parent::insertarInscriptosSimulados($datos);
	}
	
	public function Modificar($datos): bool {
		if (!$this->_ValidarModificar($datos, $datosRegistro))
			return false;
		$datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasSolicitudesCoberturaInscriptos = new cAuditoriasSolicitudesCoberturaInscriptos($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasSolicitudesCoberturaInscriptos->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}
	
	public function Eliminar($datos): bool {
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		/*$oAuditoriasSolicitudesCoberturaInscriptos = new cAuditoriasSolicitudesCoberturaInscriptos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasSolicitudesCoberturaInscriptos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;*/
		if (!parent::Eliminar($datos))
			return false;
		return true;
	}
	
	public function ModificarEstado($datos): bool {
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}
	
	public function eliminarxSolicitud($datos): bool {
		if (!$this->_validarEliminarxSolicitud($datos, $datosRegistros))
			return false;
		
		if (empty($datosRegistros))
			return true;
		
		$oAuditoriasSolicitudesCoberturaDesempeno = new cAuditoriasSolicitudesCoberturaDesempeno($this->conexion, $this->formato);
		foreach ($datosRegistros as $datosRegistro) {
			$datosLog = $datosRegistro;
			$datosLog['Accion'] = ELIMINAR;
			if (!$oAuditoriasSolicitudesCoberturaDesempeno->InsertarLog($datosLog, $codigoInsertadolog))
				return false;
		}
		
		return parent::eliminarxSolicitud($datos);
	}
	
	public function insertarDesignadoSinActo(array $datos, ?int &$codigoInsertado): bool {
		if (!$this->_validarPersonaActiva($datos))
			return false;
		$oPersonasPuntajes = new cPersonasPuntajes($this->conexion, $this->formato);
		if (!$oPersonasPuntajes->BuscarxPersona($datos, $resultado, $numfilas)) {
			$this->setError($oPersonasPuntajes->getError());
			return false;
		}
		if ($numfilas > 0) {
			$filaPersona = $this->conexion->ObtenerSiguienteRegistro($resultado);
		} else {
			try {
				$filaPersona = [
					'Id' => NULL,
					'IdPersona' => $datos['IdPersona'],
					'Titulo' => 0, //random_int(100, 2500) / 100,
					'AnioEgreso' => 0, //random_int(25, 250) / 100,
					'AntiguedadRama' => 0, //random_int(0, 500) / 100,
					'DesfavorabilidadRama' => 0, //random_int(0, 250) / 100,
					'AntiguedadCargo' => 0, //random_int(0, 250) / 100,
					'DesfavorabilidadCargo' => 0, //random_int(0, 100) / 100,
					'Calificaciones' => 0, //random_int(0, 200) / 100,
					'Bonificantes' => 0, //random_int(0, 1000) / 100,
					'Titularidad' => 0, //random_int(0, 100) >= 20 ? 1000 : 0,
					'Residencia' => 0, //random_int(0, 100) >= 50 ? 500 : 0,
				];
			} catch (Exception $e) {
				$this->setError(500, $e->getMessage());
				return false;
			}
			if (!$oPersonasPuntajes->Insertar($filaPersona, $codigoInsertadoPP)) {
				$this->setError($oPersonasPuntajes->getError());
				return false;
			}
			
			$filaPersona['Id'] = $codigoInsertadoPP;
			
		}
		$datos['IdPersonaPuntaje'] = $filaPersona['Id'];
		$datos['IdEstado'] = 2;

		$oSCPersona = new cSolicitudesCoberturaPersona($this->conexion, FMT_ARRAY, $this->conexionES);
		if(!$oSCPersona->buscarxSolicitudCoberturaPersona($datos, $resultado, $numfilas)) {
		    $this->setError($oSCPersona->getError());
		    return false;
        }
		
		if (!$this->Insertar($datos, $codigoInsertado)) {
			return false;
		}

		$oSolicitudCobertura = new cSolicitudesCobertura($this->conexion, $this->formato, $this->conexionES);
		$datos['Id'] = $datos['IdSolicitudCobertura'];
		try {
			if (!$oSolicitudCobertura->validarHayInscriptos($datos, true)) {
				$this->setError($oSolicitudCobertura->getError());
				return false;
			}
		} catch (Exception $e) {
			$this->setError(500, $e->getMessage());
			return false;
		}
		/*$datos['IdPersonaDesignada'] = $datos['IdPersona'];
		if (!$oSolicitudCobertura->actualizarPersonaDesignada($datos)) {
			$this->setError($oSolicitudCobertura->getError());
			return false;
		}*/
		
		return true;
	}
	
	public function Activar(array $datos): bool {
		$datosModif = ['Id' => $datos['Id'], 'Estado' => ACTIVO];
		if (!$this->ModificarEstado($datosModif))
			return false;
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasSolicitudesCoberturaInscriptos = new cAuditoriasSolicitudesCoberturaInscriptos($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasSolicitudesCoberturaInscriptos->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}
	
	public function DesActivar(array $datos): bool {
		$datosModif = ['Id' => $datos['Id'], 'Estado' => NOACTIVO];
		if (!$this->ModificarEstado($datosModif))
			return false;
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasSolicitudesCoberturaInscriptos = new cAuditoriasSolicitudesCoberturaInscriptos($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasSolicitudesCoberturaInscriptos->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}
	
	
	
	
	//-----------------------------------------------------------------------------------------
	//FUNCIONES PRIVADAS
	//-----------------------------------------------------------------------------------------
	
	public function _validarPersonaActiva(array $datos): bool {
		$oPersona = new Elastic\Personas($this->conexionES);
		$datos['incluirCampos'] = ['EstadoPersona.*'];
		if (!$oPersona->buscarxCodigo($datos, $datosPersona)) {
			$this->setError($oPersona->getError());
			return false;
		}
		$ret = $datosPersona['EstadoPersona']['Activo'] ?? false;
		if (!$ret)
			$this->setError(400, 'Error, la persona no esta activa, su estado actual es ' . mb_strtolower($datosPersona['EstadoPersona']['Nombre']));
		return $ret;
	}
	
	private function _ValidarInsertar($datos) {
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}
	
	private function _ValidarModificar($datos, &$datosRegistro) {
		if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
			return false;
		
		if ($numfilas != 1) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}
	
	private function _ValidarEliminar($datos, &$datosRegistro) {
		if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
			return false;
		
		if ($numfilas != 1) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}
	
	private function _validarEliminarxSolicitud(array $datos, ?array &$datosRegistros): bool {
		$datosRegistros = [];
		if (!$this->buscarxSolicitud($datos, $resultado, $numfilas))
			return false;
		
		if ($numfilas < 1)
			return true;
		
		while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
			$datosRegistros[] = $fila;
		
		
		return true;
	}
	
	private function _SetearNull(&$datos): void {
		
		
		if (!isset($datos['IdSolicitudCobertura']) || $datos['IdSolicitudCobertura'] == "")
			$datos['IdSolicitudCobertura'] = "NULL";
		
		if (!isset($datos['IdActoPublico']) || $datos['IdActoPublico'] == "")
			$datos['IdActoPublico'] = "NULL";
		
		if (!isset($datos['IdPersonaPuntaje']) || $datos['IdPersonaPuntaje'] == "")
			$datos['IdPersonaPuntaje'] = "NULL";
		
		if (!isset($datos['IdEstado']) || $datos['IdEstado'] == "")
			$datos['IdEstado'] = "NULL";
		
	}
	
	private function _ValidarDatosVacios($datos) {
		
		
		if (!isset($datos['IdSolicitudCobertura']) || $datos['IdSolicitudCobertura'] == "") {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un solicitud de cobertura", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		
		if (!isset($datos['IdPersonaPuntaje']) || $datos['IdPersonaPuntaje'] == "") {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar una persona", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		
		if (isset($datos['IdPersonaPuntaje']) && $datos['IdPersonaPuntaje'] != "") {
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdPersonaPuntaje'], "NumericoEntero")) {
				FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo numérico para el campo Persona.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
				return false;
			}
			if (strlen($datos['IdPersonaPuntaje']) > 11) {
				FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, el campo Persona no puede ser mayor a 11 .", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
				return false;
			}
		}
		
		if (!$this->conexion->TraerCampo('SolicitudesCobertura', 'Id', array('Id=' . $datos['IdSolicitudCobertura']), $dato, $numfilas, $errno))
			return false;
		
		
		if ($numfilas != 1) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo valido.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		
		if (!$this->conexion->TraerCampo('PersonasPuntajes', 'Id', array('Id=' . $datos['IdPersonaPuntaje']), $dato, $numfilas, $errno))
			return false;
		
		
		if ($numfilas != 1) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo valido.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	
}