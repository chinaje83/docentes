<?php
include(DIR_CLASES_DB . "cSolicitudesCoberturaTipoValidaciones.db.php");

class cSolicitudesCoberturaTipoValidaciones extends cSolicitudesCoberturaTipoValidacionesdb {
	/**
	 * Constructor de la clase cSolicitudesCoberturaTipoValidaciones.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el mÃ©todo getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO) {
		parent::__construct($conexion, $formato);
	}
	
	/**
	 * Destructor de la clase cSolicitudesCoberturaTipoValidaciones.
	 */
	function __destruct() {
		parent::__destruct();
	}
	
	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
		if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
			return false;
		return true;
	}
	
	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public function BuscarxTipoNivel($datos, &$resultado, &$numfilas): bool {
		$sparam = [
			'IdTipo' => $datos['IdTipo'],
			'IdNivel' => $datos['IdNivel'],
			'xIdRevista' => 0,
			'IdRevista' => '',
		];
		
		if (!FuncionesPHPLocal::isEmpty($datos['IdRevista'])) {
			$sparam['IdRevista'] = $datos['IdRevista'];
			$sparam['xIdRevista'] = 1;
		}
		
		if (!parent::BuscarxTipoNivel($sparam, $resultado, $numfilas))
			return false;
		return true;
	}
	
	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
		$sparam = array(
			'xId' => 0,
			'Id' => "",
			'xIdTipo' => 0,
			'IdTipo' => "",
			'xIdNivel' => 0,
			'IdNivel' => "",
			'xIdRevista' => 0,
			'IdRevista' => "",
			'xEstado' => 0,
			'Estado' => "-1",
			'limit' => '',
			'orderby' => "Id DESC"
		);
		if (isset($datos['Id']) && $datos['Id'] != "") {
			$sparam['Id'] = $datos['Id'];
			$sparam['xId'] = 1;
		}
		if (isset($datos['IdTipo']) && $datos['IdTipo'] != "") {
			$sparam['IdTipo'] = $datos['IdTipo'];
			$sparam['xIdTipo'] = 1;
		}
		if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
			$sparam['IdNivel'] = $datos['IdNivel'];
			$sparam['xIdNivel'] = 1;
		}
		if (isset($datos['IdRevista']) && $datos['IdRevista'] != "") {
			$sparam['IdRevista'] = $datos['IdRevista'];
			$sparam['xIdRevista'] = 1;
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
	
	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas): bool {
		if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
			return false;
		return true;
	}
	
	/**
	 * @param string|null $spnombre
	 * @param array|null  $sparam
	 */
	public function DocumentosTiposSP(&$spnombre, &$sparam): void {
		parent::DocumentosTiposSP($spnombre, $sparam);
	}
	
	/**
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public function DocumentosTiposSPResult(&$resultado, &$numfilas): bool {
		$this->DocumentosTiposSP($spnombre, $sparam);
		
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al buscar por codigo. ");
			return false;
		}
		return true;
	}
	
	/**
	 * @param string|null $spnombre
	 * @param array|null  $sparam
	 */
	public function NivelesSP(&$spnombre, &$sparam): void {
		parent::NivelesSP($spnombre, $sparam);
	}
	
	/**
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public function NivelesSPResult(&$resultado, &$numfilas): bool {
		$this->NivelesSP($spnombre, $sparam);
		
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al buscar por codigo. ");
			return false;
		}
		return true;
	}
	
	/**
	 * @param string|null $spnombre
	 * @param array|null  $sparam
	 */
	public function RevistasSP(&$spnombre, &$sparam): void {
		parent::RevistasSP($spnombre, $sparam);
	}
	
	/**
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public function RevistasSPResult(&$resultado, &$numfilas): bool {
		$this->RevistasSP($spnombre, $sparam);
		
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al buscar por codigo. ");
			return false;
		}
		return true;
	}
	
	/**
	 * @param array    $datos
	 * @param int|null $codigoInsertado
	 * @return bool
	 */
	public function Insertar($datos, &$codigoInsertado): bool {
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);
		$datos['AltaFecha'] = date("Y-m-d H:i:s");
		$datos['AltaUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos, $codigoInsertado))
			return false;
		$oAuditoriasSolicitudesCoberturaTipoValidaciones = new cAuditoriasSolicitudesCoberturaTipoValidaciones($this->conexion, $this->formato);
		$datos['Id'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		if (!$oAuditoriasSolicitudesCoberturaTipoValidaciones->InsertarLog($datos, $codigoInsertadolog)) {
			$this->setError($oAuditoriasSolicitudesCoberturaTipoValidaciones->getError());
			return false;
		}
		return true;
	}
	
	/**
	 * @param array $datos
	 * @return bool
	 */
	public function Modificar($datos): bool {
		if (!$this->_ValidarModificar($datos, $datosRegistro))
			return false;
		$datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasSolicitudesCoberturaTipoValidaciones = new cAuditoriasSolicitudesCoberturaTipoValidaciones($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasSolicitudesCoberturaTipoValidaciones->InsertarLog($datosRegistro, $codigoInsertadolog)) {
			$this->setError($oAuditoriasSolicitudesCoberturaTipoValidaciones->getError());
			return false;
		}
		return true;
	}
	
	/**
	 * @param array $datos
	 * @return bool
	 */
	public function Eliminar($datos): bool {
		$datosModif['Id'] = $datos['Id'];
		$datosModif['Estado'] = ELIMINADO;
		return $this->ModificarEstado($datosModif, ELIMINAR);
	}
	
	/**
	 * @param array  $datos
	 * @param string $accion
	 * @return bool
	 */
	public function ModificarEstado($datos, string $accion = MODIFICACION): bool {
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		if (!parent::ModificarEstado($datos))
			return false;
		$oAuditoriasSolicitudesCoberturaTipoValidaciones = new cAuditoriasSolicitudesCoberturaTipoValidaciones($this->conexion, $this->formato);
		$datosRegistro['Accion'] = $accion;
		if (!$oAuditoriasSolicitudesCoberturaTipoValidaciones->InsertarLog($datosRegistro, $codigoInsertadolog)) {
			$this->setError($oAuditoriasSolicitudesCoberturaTipoValidaciones->getError());
			return false;
		}
		return true;
	}
	
	/**
	 * @param array $datos
	 * @return bool
	 */
	public function Activar(array $datos): bool {
		$datosModif['Id'] = $datos['Id'];
		$datosModif['Estado'] = ACTIVO;
		return $this->ModificarEstado($datosModif);
	}
	
	/**
	 * @param array $datos
	 * @return bool
	 */
	public function DesActivar(array $datos): bool {
		$datosModif['Id'] = $datos['Id'];
		$datosModif['Estado'] = NOACTIVO;
		return $this->ModificarEstado($datosModif);
	}
	
	
	
	
	//-----------------------------------------------------------------------------------------
	//FUNCIONES PRIVADAS
	//-----------------------------------------------------------------------------------------
	/**
	 * @param $datos
	 * @return bool
	 */
	private function _ValidarInsertar($datos) {
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}
	
	/**
	 * @param $datos
	 * @param $datosRegistro
	 * @return bool
	 */
	private function _ValidarModificar($datos, &$datosRegistro) {
		if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
			return false;
		
		if ($numfilas != 1) {
			$this->setError(400, "Error debe ingresar un código valido.");
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}
	
	/**
	 * @param $datos
	 * @param $datosRegistro
	 * @return bool
	 */
	private function _ValidarEliminar($datos, &$datosRegistro) {
		if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
			return false;
		
		if ($numfilas != 1) {
			$this->setError(400, "Error debe ingresar un código valido.");
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}
	
	/**
	 * @param $datos
	 */
	private function _SetearNull(&$datos): void {
		if (!isset($datos['IdTipo']) || $datos['IdTipo'] == "")
			$datos['IdTipo'] = "NULL";
		
		if (!isset($datos['IdNivel']) || $datos['IdNivel'] == "")
			$datos['IdNivel'] = "NULL";
		
		if (!isset($datos['IdRevista']) || $datos['IdRevista'] == "")
			$datos['IdRevista'] = "NULL";
		
		if (!isset($datos['RepeticionesMax']) || $datos['RepeticionesMax'] == "")
			$datos['RepeticionesMax'] = "NULL";
		
		if (!isset($datos['DuracionDesde']) || $datos['DuracionDesde'] == "")
			$datos['DuracionDesde'] = "NULL";
		
		if (!isset($datos['DuracionHasta']) || $datos['DuracionHasta'] == "")
			$datos['DuracionHasta'] = "NULL";
		
		if (!isset($datos['Habiles']) || $datos['Habiles'] == "")
			$datos['Habiles'] = 0;
		
		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
			$datos['UltimaModificacionFecha'] = "NULL";
		
	}
	
	/**
	 * @param $datos
	 * @return bool
	 */
	private function _ValidarDatosVacios($datos) {
		if (!isset($datos['IdTipo']) || $datos['IdTipo'] == "") {
			$this->setError(400, "Debe ingresar un tipo de solicitud");
			return false;
		} else {
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdTipo'], "NumericoEntero")) {
				$this->setError(400, "Error debe ingresar un campo numérico para el campo Tipo de solicitud.");
				return false;
			}
			if (strlen($datos['IdTipo']) > 11) {
				$this->setError(400, "Error, el campo Tipo de solicitud no puede ser mayor a 11 .");
				return false;
			}
			
			if (!$this->conexion->TraerCampo('DocumentosTipos', 'IdTipoDocumento', array('IdTipoDocumento=' . $datos['IdTipo']), $dato, $numfilas, $errno))
				return false;
			
			
			if ($numfilas != 1) {
				$this->setError(400, "Error debe ingresar un tipo de solicitud valido.");
				return false;
			}
		}
		
		if (!isset($datos['IdNivel']) || $datos['IdNivel'] == "") {
			$this->setError(400, "Debe ingresar un nivel");
			return false;
		} else {
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdNivel'], "NumericoEntero")) {
				$this->setError(400, "Error debe ingresar un campo numérico para el campo Nivel.");
				return false;
			}
			if (strlen($datos['IdNivel']) > 2) {
				$this->setError(400, "Error, el campo Nivel no puede ser mayor a 2 .");
				return false;
			}
			
			if (!$this->conexion->TraerCampo('Niveles', 'IdNivel', array('IdNivel=' . $datos['IdNivel']), $dato, $numfilas, $errno))
				return false;
			
			
			if ($numfilas != 1) {
				$this->setError(400, "Error debe ingresar un nivel valido.");
				return false;
			}
		}
		
		/*if (!isset($datos['IdRevista']) || $datos['IdRevista']=="")
		{
			$this->setError(400,"Debe ingresar un revista");
			return false;
		}*/
		
		if (isset($datos['IdRevista']) && $datos['IdRevista'] != "") {
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdRevista'], "NumericoEntero")) {
				$this->setError(400, "Error debe ingresar un campo numérico para el campo Revista.");
				return false;
			}
			if (strlen($datos['IdRevista']) > 2) {
				$this->setError(400, "Error, el campo Revista no puede ser mayor a 2 .");
				return false;
			}
			
			if (!$this->conexion->TraerCampo('Revistas', 'IdRevista', array('IdRevista=' . $datos['IdRevista']), $dato, $numfilas, $errno))
				return false;
			
			
			if ($numfilas != 1) {
				$this->setError(400, "Error debe ingresar una revista valida.");
				return false;
			}
		}
		
		if (!isset($datos['DuracionDesde']) || $datos['DuracionDesde'] == "") {
			$this->setError(400, "Debe ingresar una duración mínima");
			return false;
		} else {
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['DuracionDesde'], "NumericoEntero")) {
				$this->setError(400, "Error debe ingresar un campo numérico para el campo duración mínima.");
				return false;
			}
		}
		
		/*if (!isset($datos['DuracionHasta']) || $datos['DuracionHasta'] == "") {
			$this->setError(400,"Debe ingresar una duración máxima");
			return false;
		}*/
		
		if (isset($datos['DuracionHasta']) && $datos['DuracionHasta'] != "") {
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['DuracionHasta'], "NumericoEntero")) {
				$this->setError(400, "Error debe ingresar un campo numérico para el campo duración máxima.");
				return false;
			}
		}
		
		if (!isset($datos['Habiles']) || $datos['Habiles'] == "") {
			$this->setError(400, "Debe ingresar si la duración es días hábiles o corridos");
			return false;
		} else {
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Habiles'], "NumericoEntero") ) {
				$this->setError(400, "Error debe ingresar un campo numérico para el campo días hábiles o corridos.");
				return false;
			}
			if (!in_array( $datos['Habiles'], ['0','1']) ) {
				$this->setError(400, "Error debe ingresar un campo válido para el campo días hábiles o corridos.");
				return false;
			}
		}
		
		return true;
	}
	
	
}