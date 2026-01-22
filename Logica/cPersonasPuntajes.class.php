<?php
include(DIR_CLASES_DB."cPersonasPuntajes.db.php");

class cPersonasPuntajes extends cPersonasPuntajesdb
{
	/**
	 * Constructor de la clase cPersonasPuntajes.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO)
	{
		parent::__construct($conexion, $formato);
	}
	
	/**
	 * Destructor de la clase cPersonasPuntajes.
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
	
	public function BuscarxPersona($datos, &$resultado, &$numfilas): bool
	{
		if (!parent::BuscarxPersona($datos, $resultado, $numfilas))
			return false;
		return true;
	}
	
	public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool
	{
		$sparam = array(
			'xId' => 0,
			'Id' => "",
			'xIdPersona' => 0,
			'IdPersona' => "",
			'xEstado' => 0,
			'Estado' => "-1",
			'limit' => '',
			'orderby' => "Id DESC"
		);
		if (isset($datos['Id']) && $datos['Id'] != "")
		{
			$sparam['Id'] = $datos['Id'];
			$sparam['xId'] = 1;
		}
		if (isset($datos['IdPersona']) && $datos['IdPersona'] != "")
		{
			$sparam['IdPersona'] = $datos['IdPersona'];
			$sparam['xIdPersona'] = 1;
		}
		if (isset($datos['Estado']) && $datos['Estado'] != "")
		{
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
		$datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos, $codigoInsertado))
			return false;
		$oAuditoriasPersonasPuntajes = new cAuditoriasPersonasPuntajes($this->conexion, $this->formato);
		$datos['Id'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		
		if (!$oAuditoriasPersonasPuntajes->InsertarLog($datos, $codigoInsertadolog))
			return false;
		return true;
	}
	
	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}
	
	private function _ValidarDatosVacios($datos)
	{
		
		
		if (!isset($datos['IdPersona']) || $datos['IdPersona'] == "")
		{
			$this->setError(400,"Debe ingresar un persona");
			return false;
		}
		
		if (isset($datos['IdPersona']) && $datos['IdPersona'] != "")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdPersona'], "NumericoEntero"))
			{
				$this->setError(400,"Error debe ingresar un campo numérico para el campo Persona.");
				return false;
			}
		}
		
		if (!isset($datos['Titulo']) || $datos['Titulo'] === "")
		{
			$this->setError(400,"Debe ingresar un puntaje de título");
			return false;
		}
		
		if (isset($datos['Titulo']) && $datos['Titulo'] !== "")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Titulo'], "Numerico2Decimales"))
			{
				$this->setError(400,"Error debe ingresar un campo numérico para el campo Puntaje de título.");
				return false;
			}
		}
		
		if (!isset($datos['AnioEgreso']) || $datos['AnioEgreso'] === "")
		{
			$this->setError(400,"Debe ingresar un puntaje de año de egreso");
			return false;
		}
		
		if (isset($datos['AnioEgreso']) && $datos['AnioEgreso'] !== "")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['AnioEgreso'], "Numerico2Decimales"))
			{
				$this->setError(400,"Error debe ingresar un campo numérico para el campo Puntaje de año de agreso.");
				return false;
			}
		}
		
		if (!isset($datos['AntiguedadRama']) || $datos['AntiguedadRama'] === "")
		{
			$this->setError(400,"Debe ingresar un puntaje de antigüedad en la rama");
			return false;
		}
		
		if (isset($datos['AntiguedadRama']) && $datos['AntiguedadRama'] !== "")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['AntiguedadRama'], "Numerico2Decimales"))
			{
				$this->setError(400,"Error debe ingresar un campo numérico para el campo Puntaje de antigüedad en la rama.");
				return false;
			}
		}
		
		if (!isset($datos['DesfavorabilidadRama']) || $datos['DesfavorabilidadRama'] === "")
		{
			$this->setError(400,"Debe ingresar un puntaje de desfavorabilidad de la rama");
			return false;
		}
		
		if (isset($datos['DesfavorabilidadRama']) && $datos['DesfavorabilidadRama'] !== "")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['DesfavorabilidadRama'], "Numerico2Decimales"))
			{
				$this->setError(400,"Error debe ingresar un campo numérico para el campo Puntaje de desfavorabilidad en la rama.");
				return false;
			}
		}
		
		if (!isset($datos['AntiguedadCargo']) || $datos['AntiguedadCargo'] === "")
		{
			$this->setError(400,"Debe ingresar un puntaje de antigüedad en  el cargo");
			return false;
		}
		
		if (isset($datos['AntiguedadCargo']) && $datos['AntiguedadCargo'] !== "")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['AntiguedadCargo'], "Numerico2Decimales"))
			{
				$this->setError(400,"Error debe ingresar un campo numérico para el campo Puntaje de antigüedad en el cargo.");
				return false;
			}
		}
		
		if (!isset($datos['DesfavorabilidadCargo']) || $datos['DesfavorabilidadCargo'] === "")
		{
			$this->setError(400,"Debe ingresar un puntaje de  desfavorabilidad del cargo");
			return false;
		}
		
		if (isset($datos['DesfavorabilidadCargo']) && $datos['DesfavorabilidadCargo'] !== "")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['DesfavorabilidadCargo'], "Numerico2Decimales"))
			{
				$this->setError(400,"Error debe ingresar un campo numérico para el campo Puntaje de desfavorabilidad en el cargo.");
				return false;
			}
		}
		
		if (!isset($datos['Calificaciones']) || $datos['Calificaciones'] === "")
		{
			$this->setError(400,"Debe ingresar un puntaje de  calificaciones obtenidas");
			return false;
		}
		
		if (isset($datos['Calificaciones']) && $datos['Calificaciones'] !== "")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Calificaciones'], "Numerico2Decimales"))
			{
				$this->setError(400,"Error debe ingresar un campo numérico para el campo Puntaje de calificaciones.");
				return false;
			}
		}
		
		if (!isset($datos['Bonificantes']) || $datos['Bonificantes'] === "")
		{
			$this->setError(400,"Debe ingresar un puntaje de bonificantes");
			return false;
		}
		
		if (isset($datos['Bonificantes']) && $datos['Bonificantes'] !== "")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Bonificantes'], "Numerico2Decimales"))
			{
				$this->setError(400,"Error debe ingresar un campo numérico para el campo Puntaje de bonificantes.");
				return false;
			}
		}
		
		if (!isset($datos['Titularidad']) || $datos['Titularidad'] === "")
		{
			$this->setError(400,"Debe ingresar si califica para puntaje de no titularidad");
			return false;
		}
		
		if (isset($datos['Titularidad']) && $datos['Titularidad'] !== "")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Titularidad'], "Numerico2Decimales"))
			{
				$this->setError(400,"Error debe ingresar un campo numérico para el campo Califica para no titularidad.");
				return false;
			}
		}
		
		if (!isset($datos['Residencia']) || $datos['Residencia'] === "")
		{
			$this->setError(400,"Debe ingresar si califica para puntaje de residencia");
			return false;
		}
		
		if (isset($datos['Residencia']) && $datos['Residencia'] !== "")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Residencia'], "Numerico2Decimales"))
			{
				$this->setError(400,"Error debe ingresar un campo numérico para el campo Califica para residencia.");
				return false;
			}
		}
		/*
		if (!isset($datos['Total']) || $datos['Total']=="")
		{
			$this->setError(400,"Debe ingresar un total");
			return false;
		}
		*/
		return true;
	}
	
	private function _SetearNull(&$datos): void
	{
		
		
		if (!isset($datos['IdPersona']) || $datos['IdPersona'] == "")
			$datos['IdPersona'] = "NULL";
		
		if (!isset($datos['Titulo']) || $datos['Titulo'] == "")
			$datos['Titulo'] = 100;
		else
			$datos['Titulo'] = (int)($datos['Titulo'] * 100);
		
		if (!isset($datos['AnioEgreso']) || $datos['AnioEgreso'] == "")
			$datos['AnioEgreso'] = 25;
		else
			$datos['AnioEgreso'] = (int)($datos['AnioEgreso'] * 100);
		
		if (!isset($datos['AntiguedadRama']) || $datos['AntiguedadRama'] == "")
			$datos['AntiguedadRama'] = 0;
		else
			$datos['AntiguedadRama'] = (int)($datos['AntiguedadRama'] * 100);
		
		if (!isset($datos['DesfavorabilidadRama']) || $datos['DesfavorabilidadRama'] == "")
			$datos['DesfavorabilidadRama'] = 0;
		else
			$datos['DesfavorabilidadRama'] = (int)($datos['DesfavorabilidadRama'] * 100);
		
		if (!isset($datos['AntiguedadCargo']) || $datos['AntiguedadCargo'] == "")
			$datos['AntiguedadCargo'] = 0;
		else
			$datos['AntiguedadCargo'] = (int)($datos['AntiguedadCargo'] * 100);
		
		if (!isset($datos['DesfavorabilidadCargo']) || $datos['DesfavorabilidadCargo'] == "")
			$datos['DesfavorabilidadCargo'] = 0;
		else
			$datos['DesfavorabilidadCargo'] = (int)($datos['DesfavorabilidadCargo'] * 100);
		
		if (!isset($datos['Calificaciones']) || $datos['Calificaciones'] == "")
			$datos['Calificaciones'] = 0;
		else
			$datos['Calificaciones'] = (int)($datos['Calificaciones'] * 100);
		
		if (!isset($datos['Bonificantes']) || $datos['Bonificantes'] == "")
			$datos['Bonificantes'] = 0;
		else
			$datos['Bonificantes'] = (int)($datos['Bonificantes'] * 100);
		
		if (!isset($datos['Titularidad']) || $datos['Titularidad'] == "")
			$datos['Titularidad'] = 0;
		
		if (!isset($datos['Residencia']) || $datos['Residencia'] == "")
			$datos['Residencia'] = 0;
		
		$datos['Total'] = $datos['Titulo'] + $datos['AnioEgreso'] + $datos['AntiguedadRama'] + $datos['DesfavorabilidadRama'] +
			$datos['AntiguedadCargo'] + $datos['DesfavorabilidadCargo'] + $datos['Calificaciones'] + $datos['Bonificantes'] +
			$datos['Titularidad'] + $datos['Residencia'];
		
		
	}
	
	public function Modificar($datos): bool
	{
		if (!$this->_ValidarModificar($datos, $datosRegistro))
			return false;
		$datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasPersonasPuntajes = new cAuditoriasPersonasPuntajes($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasPersonasPuntajes->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}
	
	private function _ValidarModificar($datos, &$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
			return false;
		
		if ($numfilas != 1)
		{
			$this->setError(400,"Error debe ingresar un código valido.");
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------
	
	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasPersonasPuntajes = new cAuditoriasPersonasPuntajes($this->conexion, $this->formato);
		$datosLog = $datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if (!$oAuditoriasPersonasPuntajes->InsertarLog($datosLog, $codigoInsertadolog))
			return false;
		$datosmodif['Id'] = $datos['Id'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}
	
	private function _ValidarEliminar($datos, &$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
			return false;
		
		if ($numfilas != 1)
		{
			$this->setError(400,"Error debe ingresar un código valido.");
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
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
		$datosmodif['Id'] = $datos['Id'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasPersonasPuntajes = new cAuditoriasPersonasPuntajes($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasPersonasPuntajes->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}
	
	public function DesActivar(array $datos): bool
	{
		$datosmodif['Id'] = $datos['Id'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos, $datosRegistro))
			return false;
		$oAuditoriasPersonasPuntajes = new cAuditoriasPersonasPuntajes($this->conexion, $this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if (!$oAuditoriasPersonasPuntajes->InsertarLog($datosRegistro, $codigoInsertadolog))
			return false;
		return true;
	}
	
	
}