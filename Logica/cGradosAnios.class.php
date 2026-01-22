<?php 
include(DIR_CLASES_DB."cGradosAnios.db.php");
class cGradosAnios extends cGradosAniosdb

{
	/**
	 * Constructor de la clase cGradosAnios.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed $formato
	 */
	function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO)
	{
		parent::__construct($conexion, $formato);
	}

	/**
	 * Destructor de la clase cGradosAnios.
	 */
	function __destruct()
	{
		parent::__destruct();
	}

	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public function getError(): array
	{
		return $this->error;
	}

	public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool
	{
		if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
			return false;
		return true;
	}


	public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool
	{
		$sparam = array(
			'xIdGradoAnio' => 0,
			'IdGradoAnio' => "",
			'xNombre' => 0,
			'Nombre' => "",
			'xNombreCorto' => 0,
			'NombreCorto' => "",
			'xIdGradoAnioExterno' => 0,
			'IdGradoAnioExterno' => "",
			'xCodigo' => 0,
			'Codigo' => "",
			'xOrden' => 0,
			'Orden' => "",
			'xEstado' => 0,
			'Estado' => "-1",
			'limit' => '',
			'orderby' => "IdGradoAnio ASC"
		);


		if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
			$sparam['Nombre'] = $datos['Nombre'];
			$sparam['xNombre'] = 1;
		}
		if (isset($datos['NombreCorto']) && $datos['NombreCorto'] != "") {
			$sparam['NombreCorto'] = $datos['NombreCorto'];
			$sparam['xNombreCorto'] = 1;
		}
		if (isset($datos['IdGradoAnioExterno']) && $datos['IdGradoAnioExterno'] != "") {
			$sparam['IdGradoAnioExterno'] = $datos['IdGradoAnioExterno'];
			$sparam['xIdGradoAnioExterno'] = 1;
		}
		if (isset($datos['Codigo']) && $datos['Codigo'] != "") {
			$sparam['Codigo'] = $datos['Codigo'];
			$sparam['xCodigo'] = 1;
		}
		if (isset($datos['Orden']) && $datos['Orden'] != "") {
			$sparam['Orden'] = $datos['Orden'];
			$sparam['xOrden'] = 1;
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


	public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas): bool
	{
		if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
			return false;
		return true;
	}


	public function Insertar($datos, &$codigoInsertado): bool
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);
		$datos['AltaFecha'] = date("Y-m-d H:i:s");
		$datos['AltaUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos, $codigoInsertado))
			return false;
		$oAuditoriasGradosAnios = new cAuditoriasGradosAnios($this->conexion, $this->formato);
		$datos['IdGradoAnio'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if (!$oAuditoriasGradosAnios->InsertarLog($datos, $codigoInsertadolog))
			return false;
		return true;
	}


	public function Modificar($datos): bool
	{
		/*var_dump($datos);die;*/
		if (!$this->_ValidarModificar($datos, $datosRegistro))
			return false;
		$datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasGradosAnios = new cAuditoriasGradosAnios($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasGradosAnios->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{

		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasGradosAnios = new cAuditoriasGradosAnios($this->conexion, $this->formato);
		$datosLog = $datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if (!$oAuditoriasGradosAnios->InsertarLog($datosLog, $codigoInsertadolog))
			return false;
		$datosmodif['IdGradoAnio'] = $datos['IdGradoAnio'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}


	public function ModificarEstado($datos): bool
	{
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}


	public function Activar(array $datos): bool
	{
		$datosmodif['IdGradoAnio'] = $datos['IdGradoAnio'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasGradosAnios = new cAuditoriasGradosAnios($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasGradosAnios->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdGradoAnio'] = $datos['IdGradoAnio'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasGradosAnios = new cAuditoriasGradosAnios($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasGradosAnios->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}


	private function _ValidarModificar($datos, &$datosRegistro)
	{
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


	private function _ValidarEliminar($datos, &$datosRegistro)
	{

		if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
			return false;

		if ($numfilas != 1) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}


	private function _SetearNull(&$datos): void
	{


		if (!isset($datos['Nombre']) || $datos['Nombre'] == "")
			$datos['Nombre'] = "NULL";

		if (!isset($datos['IdGradoAnioExterno']) || $datos['IdGradoAnioExterno'] == "")
			$datos['IdGradoAnioExterno'] = "NULL";

		if (!isset($datos['Codigo']) || $datos['Codigo'] == "")
			$datos['Codigo'] = "NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
			$datos['UltimaModificacionFecha'] = "NULL";

	}


	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un nombre.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		if (!isset($datos['Orden']) || $datos['Orden'] !== "") {
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Orden'], "NumericoEntero")) {
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un nombre.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
					return false;
				}
			}
			return true;
		}
	}
}