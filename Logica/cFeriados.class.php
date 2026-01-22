<?php
include(DIR_CLASES_DB . "cFeriados.db.php");

class cFeriados extends cFeriadosDB {
	/**
	 * Constructor de la clase cFeriadosABM.
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

	/**
	 * Cuanta la cantidad de d�as h�biles en el intervalo, considerando feriados, s�bados y domingos
	 *
	 * @param DateTime $inicio
	 * @param DateTime $fin
	 * @param string[] $feriados
	 * @return int
	 */
	public static function contarDiasHabiles(DateTime $inicio, DateTime $fin, array $feriados): int {
		$interval = new DateInterval('P1D');
		$dia = clone $inicio;
		$cant = 0;
		while ($dia <= $fin) {
			if (self::diaEsHabil($dia, $feriados))
				++$cant;
			$dia->add($interval);
		}

		return $cant;
	}

	/**
	 * Obtiene el d�a en�simo d�a h�bil, considerando feriados, s�bados y domingos
	 *
	 * @param DateTime $fechaInicio
	 * @param int      $N
	 * @param string[] $diasFeriados
	 * @return DateTime
	 */
	public static function obtenerEnesimoDiaHabil(DateTime $fechaInicio, int $N, array $diasFeriados = []): DateTime {
		$dia = clone $fechaInicio;
		$interval = new DateInterval('P1D');
		$ii = self::diaEsHabil($dia, $diasFeriados) ? 1 : 0;
		while ($ii <= $N) {
			$dia->add($interval);
			if (self::diaEsHabil($dia, $diasFeriados))
				++$ii;

		}
		return $dia;
	}

	/**
	 * Verifica si una fecha dada es h�bil
	 *
	 * @param DateTime $dia
	 * @param string[] $diasFeriados
	 * @return bool
	 */
	public static function diaEsHabil(DateTime $dia, array $diasFeriados = []): bool {
		return !(
			in_array($dia->format('N'), [6, 7])
			|| in_array($dia->format('Y-m-d'), $diasFeriados)
		);
	}

	/**
	 * Destructor de la clase cFeriadosABM.
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
		return parent::BuscarxCodigo($datos, $resultado, $numfilas);
	}

	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public function BuscarFeriadosActivos($datos, &$resultado, &$numfilas): bool {

        if (!isset($datos['Estado']) || $datos['Estado'] == "") {
            $datos['Estado'] = ACTIVO;
        }

		return parent::BuscarFeriadosActivos($datos, $resultado, $numfilas);
	}

	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public function buscarxRangoEstado($datos, &$resultado, &$numfilas): bool {
		return parent::buscarxRangoEstado($datos, $resultado, $numfilas);
	}

	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
		$sparam = array(
			'xIdFeriado' => 0,
			'IdFeriado' => "",
			'xDia' => 0,
			'Dia' => "",
			'xDescripcion' => 0,
			'Descripcion' => "",
			'xEstado' => 0,
			'Estado' => "-1",
			'limit' => '',
			'orderby' => "IdFeriado DESC"
		);
		if (isset($datos['IdFeriado']) && $datos['IdFeriado'] != "") {
			$sparam['IdFeriado'] = $datos['IdFeriado'];
			$sparam['xIdFeriado'] = 1;
		}
		if (isset($datos['Dia']) && $datos['Dia'] != "") {
			$sparam['Dia'] = FuncionesPHPLocal::ConvertirFecha($datos['Dia'], 'dd/mm/aaaa', 'aaaa-mm-dd');
			$sparam['xDia'] = 1;
		}
		if (isset($datos['Descripcion']) && $datos['Descripcion'] != "") {
			$sparam['Descripcion'] = $datos['Descripcion'];
			$sparam['xDescripcion'] = 1;
		}
		if (isset($datos['Estado']) && $datos['Estado'] != "") {
			$sparam['Estado'] = $datos['Estado'];
			$sparam['xEstado'] = 1;
		}

		if (isset($datos['orderby']) && $datos['orderby'] != "")
			$sparam['orderby'] = $datos['orderby'];
		if (isset($datos['limit']) && $datos['limit'] != "")
			$sparam['limit'] = $datos['limit'];
		return parent::BusquedaAvanzada($sparam, $resultado, $numfilas);
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

		$datos['Dia'] = FuncionesPHPLocal::ConvertirFecha($datos['Dia'], 'dd/mm/aaaa', 'aaaa-mm-dd');
		$datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos, $codigoInsertado))
			return false;
		return true;
	}

	/**
	 * @param array $datos
	 * @return bool
	 */
	public function Modificar($datos): bool {
		if (!$this->_ValidarModificar($datos, $datosRegistro))
			return false;

		//$datos['Descripcion'] = utf8_decode($datos['Descripcion']);
		$datos['Dia'] = FuncionesPHPLocal::ConvertirFecha($datos['Dia'], 'dd/mm/aaaa', 'aaaa-mm-dd');
		$datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		return true;
	}

	/**
	 * @param array $datos
	 * @return bool
	 */
	public function Eliminar($datos): bool {
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$datosModif['IdFeriado'] = $datos['IdFeriado'];
		$datosModif['Estado'] = ELIMINADO;
		return $this->ModificarEstado($datosModif);
	}

	public function ModificarEstado($datos): bool {
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}

	/**
	 * @param array $datos
	 * @return bool
	 */
	public function Activar(array $datos): bool {
		$datosModif['IdFeriado'] = $datos['IdFeriado'];
		$datosModif['Estado'] = ACTIVO;
		return $this->ModificarEstado($datosModif);
	}

	/**
	 * @param array $datos
	 * @return bool
	 */
	public function DesActivar(array $datos): bool {
		$datosModif['IdFeriado'] = $datos['IdFeriado'];
		$datosModif['Estado'] = NOACTIVO;
		return $this->ModificarEstado($datosModif);
	}


    public function BuscarFeriados($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarFeriados($datos, $resultado, $numfilas);
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
			$this->setError(400, "Error debe ingresar un c�digo valido.");
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
			$this->setError(400, "Error debe ingresar un c�digo valido.");
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}

	/**
	 * @param $datos
	 */
	private function _SetearNull(&$datos): void {


		if (!isset($datos['Dia']) || $datos['Dia'] == "")
			$datos['Dia'] = "NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "")
			$datos['Descripcion'] = "NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
			$datos['UltimaModificacionFecha'] = "NULL";

	}

	/**
	 * @param $datos
	 * @return bool
	 */
	private function _ValidarDatosVacios($datos) {


		if (!isset($datos['Dia']) || $datos['Dia'] == "") {
			$this->setError(400, "Debe ingresar una fecha");
			return false;
		}

		if (isset($datos['Dia']) && $datos['Dia'] != "") {
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Dia'], "FechaDDMMAAAA")) {
				$this->setError(400, "Error debe ingresar una fecha valida para el campo Fecha.");
				return false;
			}
		}

		if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "") {
			$this->setError(400, "Debe ingresar una descripci�n");
			return false;
		}

		if (isset($datos['Descripcion']) && $datos['Descripcion'] != "") {
			if (strlen($datos['Descripcion']) > 150) {
				$this->setError(400, "Error, el campo Descripci�n no puede ser mayor a 150 .");
				return false;
			}
		}
		return true;
	}


}
