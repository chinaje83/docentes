<?php
include(DIR_CLASES_DB . "cRegiones.db.php");

class cRegiones extends cRegionesdb {
	/**
	 * Constructor de la clase cRegiones.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO) {
		parent::__construct($conexion, $formato);
	}
	
	public static function preprocesarDatosElastic(array $datos): array {
		$datos['Tabla'] = 'Regiones';
		$datos['Identificadores'] = [];
		if (!empty($datos['NumeroRegion']))
			$datos['Identificadores'][] = $datos['NumeroRegion'];
		if (!empty($datos['Codigo']))
			$datos['Identificadores'][] = $datos['Codigo'];
		return $datos;
	}
	
	/**
	 * Destructor de la clase cRegiones.
	 */
	function __destruct() {
		parent::__destruct();
	}
	
	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public function getError(): array {
		return $this->error;
	}
	
	public function BuscarxCodigo($datos, &$resultado, &$numfilas) {
		if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
			return false;
		return true;
	}
	
	public function BuscarParaElastic($datos, &$resultado, &$numfilas) {
		return parent::BuscarParaElastic($datos, $resultado, $numfilas);
	}
	
	public function BuscarCombo(&$resultado, &$numfilas) {
		if (!parent::BuscarCombo($resultado, $numfilas))
			return false;
		return true;
	}
	
	public function BuscarComboxRegion($datos, &$resultado, &$numfilas) {
		if (!parent::BuscarComboxRegion($datos, $resultado, $numfilas))
			return false;
		return true;
	}
	
	public function BusquedaAvanzada($datos, &$resultado, &$numfilas) {
		$sparam = array(
			'xIdRegion' => 0,
			'IdRegion' => "",
			'xNombre' => 0,
			'Nombre' => "",
			'xNumeroRegion' => 0,
			'NumeroRegion' => "",
			'xEstado' => 0,
			'Estado' => "-1",
			'limit' => '',
			'orderby' => "IdRegion DESC"
		);
		if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
			$sparam['IdRegion'] = $datos['IdRegion'];
			$sparam['xIdRegion'] = 1;
		}
		if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
			$sparam['Nombre'] = $datos['Nombre'];
			$sparam['xNombre'] = 1;
		}
		if (isset($datos['NumeroRegion']) && $datos['NumeroRegion'] != "") {
			$sparam['NumeroRegion'] = $datos['NumeroRegion'];
			$sparam['xNumeroRegion'] = 1;
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
	
	public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas) {
		if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
			return false;
		return true;
	}
	
	public function Insertar($datos, &$codigoinsertado) {
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);
		$datos['AltaFecha'] = date("Y-m-d H:i:s");
		$datos['AltaUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos, $codigoinsertado))
			return false;
		$oAuditoriasRegiones = new cAuditoriasRegiones($this->conexion, $this->formato);
		$datos['IdRegion'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		
		if (!$oAuditoriasRegiones->InsertarLog($datos, $codigoInsertadolog))
			return false;
		
		return $this->actualizarElastic($datos);
	}
	
	public function Modificar($datos) {
		if (!$this->_ValidarModificar($datos, $datosRegistro))
			return false;
		$datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasRegiones = new cAuditoriasRegiones($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasRegiones->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		
		return $this->actualizarElastic($datos);
	}
	
	public function Eliminar($datos) {
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasRegiones = new cAuditoriasRegiones($this->conexion, $this->formato);
		$datosLog = $datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if (!$oAuditoriasRegiones->InsertarLog($datosLog, $codigoInsertadolog))
			return false;
		$datosmodif['IdRegion'] = $datos['IdRegion'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}
	
	public function ModificarEstado($datos) {
		if (!parent::ModificarEstado($datos))
			return false;
		
		return $this->actualizarElastic($datos);
	}
	
	public function armarObjetoElastic(array $datos, ?array &$datosRegistro, ?object &$datosElastic): bool {
		if (empty($datosRegistro)) {
			if (!$this->BuscarParaElastic($datos, $resultado, $numfilas))
				return false;
			
			if ($numfilas != 1) {
				$this->setError(400, 'No existe el registro');
				return false;
			}
			$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		}
		
		
		try {
			$datosElastic = Elastic\Tablas::armarDatosElastic(
				self::preprocesarDatosElastic($datosRegistro)
			);
		} catch (Bigtree\ExcepcionBase $e) {
			$this->setError($e->getError());
			return false;
		}
		
		return true;
	}
	
	public function Activar($datos) {
		$datosmodif['IdRegion'] = $datos['IdRegion'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasRegiones = new cAuditoriasRegiones($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasRegiones->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}
	
	
	public function DesActivar($datos) {
		$datosmodif['IdRegion'] = $datos['IdRegion'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasRegiones = new cAuditoriasRegiones($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasRegiones->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}
	
	private function actualizarElastic(array $datos): bool {
		if (!$this->armarObjetoElastic($datos, $datosRegistro, $datosElastic))
			return false;
		
		$oElastic = new Elastic\Modificacion(SUFFIX_TABLAS, new Elastic\Conexion());
		
		if (!$oElastic->Actualizar(self::preprocesarDatosElastic($datos), $datosElastic)) {
			$this->setError($oElastic->getError());
			return false;
		}
		
		
		return true;
	}
	
	
	
	//-----------------------------------------------------------------------------------------
	//FUNCIONES PRIVADAS
	//-----------------------------------------------------------------------------------------
	
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
	
	
	private function _SetearNull(&$datos) {
		
		
		if (!isset($datos['Nombre']) || $datos['Nombre'] == "")
			$datos['Nombre'] = "NULL";
		
		if (!isset($datos['NumeroRegion']) || $datos['NumeroRegion'] == "")
			$datos['NumeroRegion'] = "NULL";
		
		if (!isset($datos['IdRegionExterno']) || $datos['IdRegionExterno'] == "")
			$datos['IdRegionExterno'] = "NULL";
		
		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
			$datos['UltimaModificacionFecha'] = "NULL";
		return true;
	}
	
	
	private function _ValidarDatosVacios($datos) {
		
		
		if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un nombre", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		
		if (!isset($datos['NumeroRegion']) || $datos['NumeroRegion'] == "") {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un numero", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		
		if (isset($datos['IdRegionExterno']) && $datos['IdRegionExterno'] != "") {
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdRegionExterno'], "NumericoEntero")) {
				FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo numérico.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
				return false;
			}
		}
		return true;
	}
	
	
}