<?php

/**
 * Class cEscuelasPuestosPersonasEstadosDB
 */
abstract class cEscuelasPuestosPersonasEstadosDB {
	use ManejoErrores;
	/** @var accesoBDLocal */
	protected $conexion;
	/** @var mixed */
	protected $formato;
	
	/**
	 * Constructor de la clase cEscuelasPuestosPersonasEstadosDB.
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
	
	/**
	 * Destructor de la clase cEscuelasPuestosPersonasEstadosDB.
	 */
	function __destruct() {
	}
	
	protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = "sel_EscuelasPuestosPersonasEstados_xId";
		$sparam = array(
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al buscar al buscar por codigo. ");
			return false;
		}
		return true;
	}
	
	/**
	 * @param mysqli_result $resultado
	 * @param int|null      $numfilas
	 * @return bool
	 */
	protected function buscarActivos(&$resultado, ?int &$numfilas): bool {
		$spnombre = "sel_EscuelasPuestosPersonasEstados_xEstado";
		$sparam = array(
			'pEstado' => ACTIVO
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al buscar al buscar por estado. ");
			return false;
		}
		return true;
	}
	
	
	protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = "sel_EscuelasPuestosPersonasEstados_busqueda_avanzada";
		$sparam = array(
			'pxId' => $datos['xId'],
			'pId' => $datos['Id'],
			'pxCodigo' => $datos['xCodigo'],
			'pCodigo' => $datos['Codigo'],
			'pxNombre' => $datos['xNombre'],
			'pNombre' => $datos['Nombre'],
			'pxEstado' => $datos['xEstado'],
			'pEstado' => $datos['Estado'],
			'plimit' => $datos['limit'],
			'porderby' => $datos['orderby']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al realizar la búsqueda avanzada. ");
			return false;
		}
		return true;
	}
	
	
	protected function BuscarAuditoriaRapida(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = "sel_EscuelasPuestosPersonasEstados_AuditoriaRapida";
		$sparam = array(
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al buscar al buscar por codigo. ");
			return false;
		}
		return true;
	}
	
	
	protected function Insertar(array $datos, ?int &$codigoInsertado): bool {
		$spnombre = "ins_EscuelasPuestosPersonasEstados";
		$sparam = array(
			'pCodigo' => $datos['Codigo'],
			'pNombre' => $datos['Nombre'],
			'pDescripcion' => $datos['Descripcion'],
			'pEstado' => $datos['Estado'],
			'pConstanteSistema' => $datos['ConstanteSistema'],
			'pAltaFecha' => $datos['AltaFecha'],
			'pAltaUsuario' => $datos['AltaUsuario'],
			'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al insertar. ");
			return false;
		}
		$codigoInsertado = $this->conexion->UltimoCodigoInsertado();
		return true;
	}
	
	
	protected function Modificar(array $datos): bool {
		$spnombre = "upd_EscuelasPuestosPersonasEstados_xId";
		$sparam = array(
			'pCodigo' => $datos['Codigo'],
			'pNombre' => $datos['Nombre'],
			'pDescripcion' => $datos['Descripcion'],
			'pConstanteSistema' => $datos['ConstanteSistema'],
			'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
			'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al modificar. ");
			return false;
		}
		return true;
	}
	
	
	protected function Eliminar(array $datos): bool {
		$spnombre = "del_EscuelasPuestosPersonasEstados_xId";
		$sparam = array(
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al eliminar por codigo. ");
			return false;
		}
		return true;
	}
	
	
	protected function ModificarEstado(array $datos): bool {
		$spnombre = "upd_EscuelasPuestosPersonasEstados_Estado_xId";
		$sparam = array(
			'pEstado' => $datos['Estado'],
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al modificar el estado. ");
			return false;
		}
		return true;
	}
	
	
}