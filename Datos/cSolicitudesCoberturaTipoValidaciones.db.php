<?php

abstract class cSolicitudesCoberturaTipoValidacionesDB {
	use ManejoErrores;
	
	/** @var accesoBDLocal */
	protected $conexion;
	/** @var mixed */
	protected $formato;
	
	/**
	 * Constructor de la clase cSolicitudesCoberturaTipoValidacionesDB.
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
	 * Destructor de la clase cSolicitudesCoberturaTipoValidacionesDB.
	 */
	function __destruct() {
	}
	
	/**
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public abstract function DocumentosTiposSPResult(&$resultado, ?int &$numfilas): bool;
	
	/**
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public abstract function NivelesSPResult(&$resultado, ?int &$numfilas): bool;
	
	/**
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	public abstract function RevistasSPResult(&$resultado, ?int &$numfilas): bool;
	
	/**
	 * @param string|null $spnombre
	 * @param array|null  $sparam
	 */
	protected function DocumentosTiposSP(?string &$spnombre, ?array &$sparam): void {
		$spnombre = 'sel_DocumentosTipos_sc_combo_Nombre';
		$sparam = [];
	}
	
	/**
	 * @param string|null $spnombre
	 * @param array|null  $sparam
	 */
	protected function NivelesSP(?string &$spnombre, ?array &$sparam): void {
		$spnombre = 'sel_Niveles_combo_Nombre';
		$sparam = [];
	}
	
	/**
	 * @param string|null $spnombre
	 * @param array|null  $sparam
	 */
	protected function RevistasSP(?string &$spnombre, ?array &$sparam): void {
		$spnombre = 'sel_Revistas_combo_Descripcion';
		$sparam = [];
	}
	
	/**
	 * @param array    $datos
	 * @param          $resultado
	 * @param int|null $numfilas
	 * @return bool
	 */
	protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = "sel_SolicitudesCoberturaTipoValidaciones_xId";
		$sparam = array(
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al buscar al buscar por codigo. ");
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
	protected function BuscarxTipoNivel(array $datos, &$resultado, ?int &$numfilas): bool {
		$spnombre = "sel_SolicitudesCoberturaTipoValidaciones_xIdTipo_IdNivel";
		$sparam = array(
			'pIdTipo' => $datos['IdTipo'],
			'pIdNivel' => $datos['IdNivel'],
			'pxIdRevista' => $datos['xIdRevista'],
			'pIdRevista' => $datos['IdRevista'],
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al buscar al buscar por codigo. ");
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
		$spnombre = "sel_SolicitudesCoberturaTipoValidaciones_busqueda_avanzada";
		$sparam = array(
			'pxId' => $datos['xId'],
			'pId' => $datos['Id'],
			'pxIdTipo' => $datos['xIdTipo'],
			'pIdTipo' => $datos['IdTipo'],
			'pxIdNivel' => $datos['xIdNivel'],
			'pIdNivel' => $datos['IdNivel'],
			'pxIdRevista' => $datos['xIdRevista'],
			'pIdRevista' => $datos['IdRevista'],
			'pxEstado' => $datos['xEstado'],
			'pEstado' => $datos['Estado'],
			'plimit' => $datos['limit'],
			'porderby' => $datos['orderby']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al realizar la búsqueda avanzada. ");
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
		$spnombre = "sel_SolicitudesCoberturaTipoValidaciones_AuditoriaRapida";
		$sparam = array(
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al buscar al buscar por codigo. ");
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
		$spnombre = "ins_SolicitudesCoberturaTipoValidaciones";
		$sparam = array(
			'pIdTipo' => $datos['IdTipo'],
			'pIdNivel' => $datos['IdNivel'],
			'pIdRevista' => $datos['IdRevista'],
			'pRepeticionesMax' => $datos['RepeticionesMax'],
			'pDuracionDesde' => $datos['DuracionDesde'],
			'pDuracionHasta' => $datos['DuracionHasta'],
			'pHabiles' => $datos['Habiles'],
			'pEstado' => $datos['Estado'],
			'pAltaFecha' => $datos['AltaFecha'],
			'pAltaUsuario' => $datos['AltaUsuario'],
			'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al insertar. ");
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
		$spnombre = "upd_SolicitudesCoberturaTipoValidaciones_xId";
		$sparam = array(
			'pIdTipo' => $datos['IdTipo'],
			'pIdNivel' => $datos['IdNivel'],
			'pIdRevista' => $datos['IdRevista'],
			'pRepeticionesMax' => $datos['RepeticionesMax'],
			'pDuracionDesde' => $datos['DuracionDesde'],
			'pDuracionHasta' => $datos['DuracionHasta'],
			'pHabiles' => $datos['Habiles'],
			'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
			'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al modificar. ");
			return false;
		}
		return true;
	}
	
	/**
	 * @param array $datos
	 * @return bool
	 */
	protected function Eliminar(array $datos): bool {
		$spnombre = "del_SolicitudesCoberturaTipoValidaciones_xId";
		$sparam = array(
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al eliminar por codigo. ");
			return false;
		}
		return true;
	}
	
	/**
	 * @param array $datos
	 * @return bool
	 */
	protected function ModificarEstado(array $datos): bool {
		$spnombre = "upd_SolicitudesCoberturaTipoValidaciones_Estado_xId";
		$sparam = array(
			'pEstado' => $datos['Estado'],
			'pId' => $datos['Id']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			$this->setError(400, "Error al modificar el estado. ");
			return false;
		}
		return true;
	}
	
	
}