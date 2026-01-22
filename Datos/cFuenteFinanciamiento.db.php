<?php

abstract class cFuenteFinanciamientodb {
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



	protected function BuscarxCodigo($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_FuentesFinanciamiento";
		$sparam = array(
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar las fuentes de financiamiento. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}


		return true;
	}


	protected function BusquedaFuentesFinanciamiento(&$resultado, &$numfilas) {
		$spnombre = "sel_FuentesFinanciamiento";
		$sparam = array(
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar las fuentes de financiamiento. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}


		return true;
	}


}

?>
