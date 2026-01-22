<?php

abstract class cFeriadosDB {
	use ManejoErrores;

	/** @var accesoBDLocal */
	protected $conexion;
	/** @var mixed */
	protected $formato;

	/**
	 * Constructor de la clase cFeriadosABMDB.
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
	 * Destructor de la clase cFeriadosABMDB.
	 */
	function __destruct() {
	}

	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = "sel_Feriados_xIdFeriado";
		$sparam = array(
			'pIdFeriado' => $datos['IdFeriado']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al buscar al buscar por codigo. ");
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
	protected function buscarxRangoEstado(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = "sel_Feriados_xFechas_Estado";
		$sparam = array(
			'pInicio' => $datos['Inicio'],
			'pFin' => $datos['Fin'],
			'pEstado' => $datos['Estado'],
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al buscar al buscar por codigo. ");
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
		$spnombre = "sel_Feriados_busqueda_avanzada";
		$sparam = array(
			'pxIdFeriado' => $datos['xIdFeriado'],
			'pIdFeriado' => $datos['IdFeriado'],
			'pxDia' => $datos['xDia'],
			'pDia' => $datos['Dia'],
			'pxDescripcion' => $datos['xDescripcion'],
			'pDescripcion' => $datos['Descripcion'],
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

	/**
	 * @param array    $datos
	 * @param int|null $codigoInsertado
	 * @return bool
	 */
	protected function Insertar(array $datos, ?int &$codigoInsertado): bool {
		$spnombre = "ins_Feriados";
		$sparam = array(
			'pDia' => $datos['Dia'],
			'pDescripcion' => $datos['Descripcion'],
			'pEstado' => $datos['Estado'],
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

	/**
	 * @param array $datos
	 * @return bool
	 */
	protected function Modificar(array $datos): bool {
		$spnombre = "upd_Feriados_xIdFeriado";
		$sparam = array(
			'pDia' => $datos['Dia'],
			'pDescripcion' => $datos['Descripcion'],
			'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
			'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
			'pIdFeriado' => $datos['IdFeriado']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al modificar. ");
			return false;
		}
		return true;
	}

	/**
	 * @param array $datos
	 * @return bool
	 */
	protected function Eliminar(array $datos): bool {
		$spnombre = "del_Feriados_xIdFeriado";
		$sparam = array(
			'pIdFeriado' => $datos['IdFeriado']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al eliminar por codigo. ");
			return false;
		}
		return true;
	}

	/**
	 * @param array $datos
	 * @return bool
	 */
	protected function ModificarEstado(array $datos): bool {
		$spnombre = "upd_Feriados_Estado_xIdFeriado";
		$sparam = array(
			'pEstado' => $datos['Estado'],
			'pIdFeriado' => $datos['IdFeriado']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al modificar el estado. ");
			return false;
		}
		return true;
	}

	/**
	 * @param array $datos
	 * @return bool
	 */
	protected function BuscarFeriados(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = "sel_Feriados_xEstado_xDia";
		$sparam = array(
			'pInicio' => $datos['Inicio']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al modificar el estado. ");
			return false;
		}
		return true;
	}



	/**
	 * @param array $datos
	 * @return bool
	 */
	protected function BuscarFeriadosActivos(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = "sel_Feriados_xEstado";
		$sparam = array(
			'pEstado' => $datos['Estado']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400,"Error al buscar feriados por estado.");
			return false;
		}
		return true;
	}


}
