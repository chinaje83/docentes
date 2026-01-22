<?php

abstract class cRegionesDB {
	/** @var accesoBDLocal */
	protected $conexion;
	/** @var mixed */
	protected $formato;
	/** @var array */
	protected $error;
	
	/**
	 * Constructor de la clase cRegionesDB.
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
	 * Destructor de la clase cRegionesDB.
	 */
	function __destruct() {
	}
	
	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public abstract function getError(): array;
	
	
	/**
	 * Guarda un mensaje de error
	 *
	 * @param string|array $error
	 * @param string       $error_description
	 */
	protected function setError($error, $error_description = ''): void {
		$this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
	}
	
	protected function BuscarxCodigo($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Regiones_xIdRegion";
		$sparam = array(
			'pIdRegion' => $datos['IdRegion']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	protected function BuscarParaElastic($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Regiones_es_xIdRegion";
		$sparam = array(
			'pIdRegion' => $datos['IdRegion']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	protected function BuscarCombo(&$resultado, &$numfilas) {
		$spnombre = "sel_Regiones_xEstado";
		$sparam = array();
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	protected function BuscarComboxRegion($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Regiones_combo_xRegion";
		$sparam = array(
			"pIdRegion" => $datos['IdRegion']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	protected function BusquedaAvanzada($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Regiones_busqueda_avanzada";
		$sparam = array(
			'pxIdRegion' => $datos['xIdRegion'],
			'pIdRegion' => $datos['IdRegion'],
			'pxNombre' => $datos['xNombre'],
			'pNombre' => $datos['Nombre'],
			'pxNumeroRegion' => $datos['xNumeroRegion'],
			'pNumeroRegion' => $datos['NumeroRegion'],
			'pxEstado' => $datos['xEstado'],
			'pEstado' => $datos['Estado'],
			'plimit' => $datos['limit'],
			'porderby' => $datos['orderby']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	
	protected function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas) {
		$spnombre = "sel_Regiones_AuditoriaRapida";
		$sparam = array(
			'pIdRegion' => $datos['IdRegion']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	
	protected function Insertar($datos, &$codigoinsertado) {
		$spnombre = "ins_Regiones";
		$sparam = array(
			'pNombre' => $datos['Nombre'],
			'pNumeroRegion' => $datos['NumeroRegion'],
			'pIdRegionExterno' => $datos['IdRegionExterno'],
			'pEstado' => $datos['Estado'],
			'pAltaFecha' => $datos['AltaFecha'],
			'pAltaUsuario' => $datos['AltaUsuario'],
			'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		$codigoinsertado = $this->conexion->UltimoCodigoInsertado();
		return true;
	}
	
	
	protected function Modificar($datos) {
		$spnombre = "upd_Regiones_xIdRegion";
		$sparam = array(
			'pNombre' => $datos['Nombre'],
			'pNumeroRegion' => $datos['NumeroRegion'],
			'pIdRegionExterno' => $datos['IdRegionExterno'],
			'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
			'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
			'pIdRegion' => $datos['IdRegion']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	
	protected function Eliminar($datos) {
		$spnombre = "del_Regiones_xIdRegion";
		$sparam = array(
			'pIdRegion' => $datos['IdRegion']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	
	protected function ModificarEstado($datos) {
		$spnombre = "upd_Regiones_Estado_xIdRegion";
		$sparam = array(
			'pEstado' => $datos['Estado'],
			'pIdRegion' => $datos['IdRegion']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		return true;
	}
	
	
}