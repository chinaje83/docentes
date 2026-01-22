<?php

include(DIR_CLASES_DB."cEscuelasAGSecciones.db.php");
class cEscuelasAGSecciones extends cEscuelasAGSeccionesdb
{
	/**
	 * Constructor de la clase cEscuelasAGSecciones.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion,$formato=FMT_TEXTO){
		parent::__construct($conexion,$formato);
	}
	/**
	 * Destructor de la clase cEscuelasAGSecciones.
	 */
	function __destruct(){
		parent::__destruct();
	}
	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public function getError(): array {
		return $this->error;
	}

    public function getGrados(&$resultado,&$numfilas): bool
    {
        if (!parent::getGrados($resultado,$numfilas))
            return false;
        return true;
    }
	public function BuscarxCodigo($datos, &$resultado,&$numfilas): bool
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

    public function BuscarxIdEscuelaTurnoAnioGrado($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaTurnoAnioGrado($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxSeccionesxIdEscuelaTurnoAnioGrado($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxSeccionesxIdEscuelaTurnoAnioGrado($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarxIdEscuelaxIdCicloLectivo($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaxIdCicloLectivo($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdEscuelaxIdCicloLectivoxIdNivelModalidadxIdTurnoxIdGradoAnio($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaxIdCicloLectivoxIdNivelModalidadxIdTurnoxIdGradoAnio($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarxIdEscuelaTurnoAnioGradoxNombreSeccionxEstado($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaTurnoAnioGradoxNombreSeccionxEstado($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarDatosCompletosxIdSeccion($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarDatosCompletosxIdSeccion($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarMateriasxGrado($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarMateriasxGrado($datos,$resultado,$numfilas))
            return false;

        return true;
    }


    public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xIdSeccion'=> 0,
			'IdSeccion'=> "",
			'xIdEscuelaTurnoAnioGrado'=> 0,
			'IdEscuelaTurnoAnioGrado'=> "",
			'xNombreSeccion'=> 0,
			'NombreSeccion'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdSeccion DESC"
		);
		if(isset($datos['IdSeccion']) && $datos['IdSeccion']!="")
		{
			$sparam['IdSeccion']= $datos['IdSeccion'];
			$sparam['xIdSeccion']= 1;
		}
		if(isset($datos['IdEscuelaTurnoAnioGrado']) && $datos['IdEscuelaTurnoAnioGrado']!="")
		{
			$sparam['IdEscuelaTurnoAnioGrado']= $datos['IdEscuelaTurnoAnioGrado'];
			$sparam['xIdEscuelaTurnoAnioGrado']= 1;
		}
		if(isset($datos['NombreSeccion']) && $datos['NombreSeccion']!="")
		{
			$sparam['NombreSeccion']= $datos['NombreSeccion'];
			$sparam['xNombreSeccion']= 1;
		}
		if(isset($datos['Estado']) && $datos['Estado']!="")
		{
			$sparam['Estado']= $datos['Estado'];
			$sparam['xEstado']= 1;
		}

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];
		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas): bool
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}


    public function Insertar($datos,&$codigoInsertado): bool
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$oEscuelaTurnos = new cEscuelasTurnos($this->conexion,$this->formato);
        if(!$oEscuelaTurnos->BuscarxCodigo($datos,$resultTurnos, $numfilasTurnos))
            return false;
        if ($numfilasTurnos!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, al buscar el turno asociado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosTurno = $this->conexion->ObtenerSiguienteRegistro($resultTurnos);
		$this->_SetearNull($datos);
        $datos['AltaFecha']=date("Y-m-d H:i:s");
        $datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoInsertado))
			return false;
		$oAuditoriasEscuelasAGSecciones = new cAuditoriasEscuelasAGSecciones($this->conexion,$this->formato);
		$datos['IdSeccion'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		if(!$oAuditoriasEscuelasAGSecciones->InsertarLog($datos,$codigoInsertadolog))
			return false;

        $datos['IdEscuela']=$datosTurno['IdEscuela'];
        $datos['IdNivelModalidad']=$datosTurno['IdNivelModalidad'];
        $conexionES = new Elastic\Conexion();
		$oEscuelaPuestos = new cEscuelasPuestos($this->conexion,$conexionES, $this->formato);
        if (!$this->BuscarMateriasxGrado($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            if (!$oEscuelaPuestos->InsertarMateria($datos, $codigoInsertado))
                return false;
        }

		return true;
	}


	public function Modificar($datos): bool
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasEscuelasAGSecciones = new cAuditoriasEscuelasAGSecciones($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasAGSecciones->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasAGSecciones = new cAuditoriasEscuelasAGSecciones($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
        if (!isset($datos['CargaManual']))
            $datosLog['CargaManual'] = 0;
		if(!$oAuditoriasEscuelasAGSecciones->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdSeccion'] = $datos['IdSeccion'];
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
		$datosmodif['IdSeccion'] = $datos['IdSeccion'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasAGSecciones = new cAuditoriasEscuelasAGSecciones($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasAGSecciones->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdSeccion'] = $datos['IdSeccion'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasAGSecciones = new cAuditoriasEscuelasAGSecciones($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasAGSecciones->InsertarLog($datosRegistro,$codigoInsertadolog))
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
        $datosBuscar['IdEscuelaTurnoAnioGrado'] = $datos['IdEscuelaTurnoAnioGrado'];
        $datosBuscar['NombreSeccion'] = $datos['NombreSeccion'];
        $datosBuscar['Estado'] = ACTIVO;
		if(!$this->BuscarxIdEscuelaTurnoAnioGradoxNombreSeccionxEstado($datosBuscar,$resultado,$numfilas))
		    return false;

		if ($numfilas > 0) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE, utf8_decode("Error, ya existe la división para el año seleccionado.") ,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

		return true;
	}


	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;


        $datosBuscar['IdEscuelaTurnoAnioGrado'] = $datos['IdEscuelaTurnoAnioGrado'];
        $datosBuscar['NombreSeccion'] = $datos['NombreSeccion'];
        $datosBuscar['Estado'] = ACTIVO;
        if(!$this->BuscarxIdEscuelaTurnoAnioGradoxNombreSeccionxEstado($datosBuscar,$resultado,$numfilas))
            return false;

        if($numfilas>0)
        {
            $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);
            if($fila['IdSeccion']!=$datos['IdSeccion'])
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe la Sección para el Grado/Año seleccionado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }

        }

		return true;
	}


	private function _ValidarEliminar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        $oPuestosPersonas = new cEscuelasPuestosPersonas($this->conexion);
        $datos['IdEstado']=ACTIVO;
        if (!$oPuestosPersonas->BuscarEscuelaPOFA($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Error al eliminar, la sección se encuentra asociada a un puesto en ".PLANTA_ANALITICA_ALIAS),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

		$conexionES = new Elastic\Conexion();
        $oPuestos   = new cEscuelasPuestos($this->conexion,$conexionES, "");

        if (!$oPuestos->BuscarEscuelaPOF($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0)
        {
            while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            {
                $datosEliminar['IdPuesto'] = $fila['IdPuesto'];
                if (!$oPuestos->EliminarCargoPof($datosEliminar))
                    return false;
            }
        }

        return true;
	}


	private function _SetearNull(&$datos): void
	{
		if (!isset($datos['IdEscuelaTurnoAnioGrado']) || $datos['IdEscuelaTurnoAnioGrado']=="")
			$datos['IdEscuelaTurnoAnioGrado']="NULL";

		if (!isset($datos['NombreSeccion']) || $datos['NombreSeccion']=="")
			$datos['NombreSeccion']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		
	}


	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdEscuelaTurnoAnioGrado']) || $datos['IdEscuelaTurnoAnioGrado']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id turno año grado",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdEscuelaTurnoAnioGrado']) && $datos['IdEscuelaTurnoAnioGrado']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdEscuelaTurnoAnioGrado'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo Id Turno Año Grado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdEscuelaTurnoAnioGrado'])>10)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Id Turno Año Grado no puede ser mayor a 10 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['NombreSeccion']) || $datos['NombreSeccion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre sección",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['NombreSeccion']) && $datos['NombreSeccion']!="")
		{

			if (strlen($datos['NombreSeccion'])>10)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Nombre Sección no puede ser mayor a 10 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		return true;
	}




}