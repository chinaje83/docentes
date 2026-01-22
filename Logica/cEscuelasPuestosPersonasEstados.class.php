<?php
include(DIR_CLASES_DB . "cEscuelasPuestosPersonasEstados.db.php");

/**
 * Class cEscuelasPuestosPersonasEstados
 */
class cEscuelasPuestosPersonasEstados extends cEscuelasPuestosPersonasEstadosdb {
	/**
	 * Constructor de la clase cEscuelasPuestosPersonasEstados.
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
	 * Destructor de la clase cEscuelasPuestosPersonasEstados.
	 */
	function __destruct() {
		parent::__destruct();
	}

	
	public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
		if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
			return false;
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public function buscarActivos(&$resultado, ?int &$numfilas): bool {
		return parent::buscarActivos($resultado, $numfilas);
	}
	
	
	public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
		$sparam = array(
			'xId' => 0,
			'Id' => "",
			'xCodigo' => 0,
			'Codigo' => "",
			'xNombre' => 0,
			'Nombre' => "",
			'xEstado' => 0,
			'Estado' => "-1",
			'limit' => '',
			'orderby' => "Id DESC"
		);
		if (isset($datos['Id']) && $datos['Id'] != "") {
			$sparam['Id'] = $datos['Id'];
			$sparam['xId'] = 1;
		}
		if (isset($datos['Codigo']) && $datos['Codigo'] != "") {
			$sparam['Codigo'] = $datos['Codigo'];
			$sparam['xCodigo'] = 1;
		}
		if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
			$sparam['Nombre'] = $datos['Nombre'];
			$sparam['xNombre'] = 1;
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
		$oAuditoriasEscuelasPuestosPersonasEstados = new cAuditoriasEscuelasPuestosPersonasEstados($this->conexion, $this->formato);
		$datos['Id'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		if (!$oAuditoriasEscuelasPuestosPersonasEstados->InsertarLog($datos, $codigoInsertadolog))
			return false;
		return true;
	}
	
	
	public function Modificar($datos): bool {
		if (!$this->_ValidarModificar($datos, $datosRegistro))
			return false;
		$datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasEscuelasPuestosPersonasEstados = new cAuditoriasEscuelasPuestosPersonasEstados($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasEscuelasPuestosPersonasEstados->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}
	
	
	public function Eliminar($datos): bool {
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasEscuelasPuestosPersonasEstados = new cAuditoriasEscuelasPuestosPersonasEstados($this->conexion, $this->formato);
		$datosLog = $datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if (!$oAuditoriasEscuelasPuestosPersonasEstados->InsertarLog($datosLog, $codigoInsertadolog))
			return false;
		$datosmodif['Id'] = $datos['Id'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}
	
	
	public function ModificarEstado($datos): bool {
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}
	
	
	public function Activar(array $datos): bool {
		$datosmodif['Id'] = $datos['Id'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasEscuelasPuestosPersonasEstados = new cAuditoriasEscuelasPuestosPersonasEstados($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasEscuelasPuestosPersonasEstados->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}
	
	
	public function DesActivar(array $datos): bool {
		$datosmodif['Id'] = $datos['Id'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasEscuelasPuestosPersonasEstados = new cAuditoriasEscuelasPuestosPersonasEstados($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasEscuelasPuestosPersonasEstados->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
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
			$this->setError(400,"Error debe ingresar un código valido.");
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
			$this->setError(400,"Error debe ingresar un código valido.");
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}
	
	
	private function _SetearNull(&$datos): void {
		
		
		if (!isset($datos['Codigo']) || $datos['Codigo'] == "")
			$datos['Codigo'] = "NULL";
		
		if (!isset($datos['Nombre']) || $datos['Nombre'] == "")
			$datos['Nombre'] = "NULL";
		
		if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "")
			$datos['Descripcion'] = "NULL";
		
		if (!isset($datos['ConstanteSistema']) || $datos['ConstanteSistema'] == "")
			$datos['ConstanteSistema'] = "NULL";
		
		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
			$datos['UltimaModificacionFecha'] = "NULL";
		
	}
	
	
	private function _ValidarDatosVacios($datos) {
		
		
		if (!isset($datos['Codigo']) || $datos['Codigo'] == "") {
			$this->setError(400,"Debe ingresar un código");
			return false;
		}
		
		if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
			$this->setError(400,"Debe ingresar un nombre");
			return false;
		}
		
		if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
			if (strlen($datos['Nombre']) > 50) {
				$this->setError(400,"Error, el campo Nombre no puede ser mayor a 50 .");
				return false;
			}
		}
		
		if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "") {
			$this->setError(400,"Debe ingresar una descripción");
			return false;
		}
		
		if (!isset($datos['ConstanteSistema']) || $datos['ConstanteSistema'] == "") {
			$this->setError(400,"Debe ingresar una constante");
			return false;
		}
		return true;
	}
	
	
}