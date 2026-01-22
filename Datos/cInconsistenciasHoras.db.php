<?php

/**
 * Class cInconsistenciasHorasDB
 */
abstract class cInconsistenciasHorasDB {
	use ManejoErrores;

	/** @var accesoBDLocal */
	protected $conexion;
	/** @var mixed */
	protected $formato;

	/**
	 * Constructor de la clase cInconsistenciasHorasDB.
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
	 * Destructor de la clase cInconsistenciasHorasDB.
	 */
	function __destruct() {
	}

	/**
	 * @param array $datos
	 * @return bool
	 */
	public abstract function Activar(array $datos): bool;

	/**
	 * @param array $datos
	 * @return bool
	 */
	public abstract function DesActivar(array $datos): bool;

	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = 'sel_InconsistenciasHoras_xId';
		$sparam = array(
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, 'Error al buscar al buscar por codigo. ');
			return false;
		}
		return true;
	}

	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = 'sel_InconsistenciasHoras_busqueda_avanzada';
		$sparam = array(
			'pxId' => $datos['xId'],
			'pId' => $datos['Id'],
			'pxEstado' => $datos['xEstado'],
			'pEstado' => $datos['Estado'],
			'plimit' => $datos['limit'],
			'porderby' => $datos['orderby']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, 'Error al realizar la búsqueda avanzada. ');
			return false;
		}
		return true;
	}

	/**
	 * @param mysqli_result|null $resultado
	 * @param int|null           $numfilas
	 * @return bool
	 */
	protected function buscarActivos(&$resultado, ?int &$numfilas): bool {
		$spnombre = 'sel_InconsistenciasHoras_xEstado';
		$sparam = array(
			'pEstado' => ACTIVO
		);
		try {
			$this->conexion->getParent()->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno);
		} catch (Bigtree\ExcepcionDB $e) {
			//$this->setError($e->getError());
			$this->setError(400, 'Error al buscar el listado');
			return false;
		}
		return true;
	}

	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	protected function BuscarAuditoriaRapida(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = 'sel_InconsistenciasHoras_AuditoriaRapida';
		$sparam = array(
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, 'Error al buscar al buscar por codigo. ');
			return false;
		}
		return true;
	}

	/**
	 * @param array    $datos
	 * @param int|null $codigoInsertado
	 * @return bool
	 */
	protected function Insertar(array $datos, ?int &$codigoInsertado): bool {
		$spnombre = 'ins_InconsistenciasHoras';
		$sparam = array(
			'pCargosJerarquicos' => $datos['CargosJerarquicos'],
			'pCargosAdministrativos' => $datos['CargosAdministrativos'],
			'pCargosBase' => $datos['CargosBase'],
			'pCargoSupervisor' => $datos['CargoSupervisor'],
			'pHorasCatedra' => $datos['HorasCatedra'],
			'pHorasCatedraItinerantes' => $datos['HorasCatedraItinerantes'],
			'pObservaciones' => $datos['Observaciones'],
            'pReglaVerbal' => $datos['ReglaVerbal'],
            'pIdJornada' => $datos['IdJornada'],
			'pEstado' => $datos['Estado'],
			'pAltaUsuario' => $datos['AltaUsuario'],
			'pAltaFecha' => $datos['AltaFecha'],
			'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, 'Error al insertar. ');
			return false;
		}
		$codigoInsertado = $this->conexion->UltimoCodigoInsertado();
		return true;
	}

	/**
	 * @param array $datos
	 * @return bool
	 */
	protected function Modificar(array $datos): bool {
		$spnombre = 'upd_InconsistenciasHoras_xId';
		$sparam = array(
			'pCargosJerarquicos' => $datos['CargosJerarquicos'],
			'pCargosAdministrativos' => $datos['CargosAdministrativos'],
			'pCargosBase' => $datos['CargosBase'],
			'pCargoSupervisor' => $datos['CargoSupervisor'],
			'pHorasCatedra' => $datos['HorasCatedra'],
			'pHorasCatedraItinerantes' => $datos['HorasCatedraItinerantes'],
			'pObservaciones' => $datos['Observaciones'],
			'pReglaVerbal' => $datos['ReglaVerbal'],
			'pIdJornada' => $datos['IdJornada'],
			'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
			'pUltimaModificacionFecha' => date('Y/m/d H:i:s'),
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, 'Error al modificar. ');
			return false;
		}
		return true;
	}

	/**
	 * @param array $datos
	 * @return bool
	 */
	protected function Eliminar(array $datos): bool {
		$spnombre = 'del_InconsistenciasHoras_xId';
		$sparam = array(
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, 'Error al eliminar por codigo. ');
			return false;
		}
		return true;
	}

	/**
	 * @param array $datos
	 * @return bool
	 */
	protected function ModificarEstado(array $datos): bool {
		$spnombre = 'upd_InconsistenciasHoras_Estado_xId';
		$sparam = array(
			'pEstado' => $datos['Estado'],
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, 'Error al modificar el estado. ');
			return false;
		}
		return true;
	}

	/**
	 * @param array      $datos
	 * @param array|null $registro
	 * @param array|null $resumen
	 * @return bool
	 */
	public abstract function validarPersona(array $datos, ?array &$registro, ?array &$resumen): bool;

	/**
	 * @param array      $resumen
	 * @param array|null $error
	 * @return bool
	 */
	public abstract function evaluarRegistro(array $resumen, ?array &$error): bool;

    /**
     * @param array $error
     * @param array $datosPersona
     *
     * @return string
     */
	public abstract static function analizarError(array $error, array $datosPersona): string;

	/**
	 * @param $datos
	 * @return bool
	 */
	protected abstract function _ValidarInsertar(array $datos): bool;

	/**
	 * @param $datos
	 * @param $datosRegistro
	 * @return bool
	 */
	protected abstract function _ValidarModificar(array $datos, &$datosRegistro): bool;

	/**
	 * @param $datos
	 * @param $datosRegistro
	 * @return bool
	 */
	protected abstract function _ValidarEliminar(array $datos, &$datosRegistro): bool;

	/**
	 * @param $datos
	 */
	protected abstract function _SetearNull(?array &$datos): void;

	/**
	 * @param $datos
	 * @return bool
	 */
	protected abstract function _ValidarDatosVacios(array $datos): bool;

	/**
	 * @param $datos
	 * @return bool
	 */
	protected abstract function _insertarLog(array $datos): bool;

	/**
	 * @param int|null $valor
	 * @param array    $valores
	 * @return int|null
	 */
	protected static abstract function primerMayor(?int $valor, array $valores): ?int;

	/**
	 * @param array       $resumen
	 * @param string      $nivel
	 * @param array       $arbol
	 * @param array|null  $path
	 * @param array|null $error
	 * @return bool
	 */
	protected static abstract function recorrerArbol(array $resumen, string $nivel, array $arbol, ?array &$path, ?array &$error): bool;

	/**
	 * @return bool
	 */
	protected abstract function cargarArbol(): bool;
}
