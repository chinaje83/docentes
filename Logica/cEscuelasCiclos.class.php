<?php
include(DIR_CLASES_DB."cEscuelasCiclos.db.php");
class cEscuelasCiclos extends cEscuelasCiclosdb
{
	/**
	 * Constructor de la clase cEscuelasCiclos.
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
	 * Destructor de la clase cEscuelasCiclos.
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

	public function BuscarxCodigo($datos, &$resultado,&$numfilas): bool
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

    public function BuscarxIdEscuelaTurnoxIdCiclo($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaTurnoxIdCiclo($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdEscuelaTurnoxIdCicloxIdOrientacion($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaTurnoxIdCicloxIdOrientacion($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarxIdEscuelaTurno($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaTurno($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdEscuelaxIdNivelModalidadxIdCicloLectivo($datos, &$resultado,&$numfilas): bool
    {
        if (is_array($datos['IdEscuela']))
            $datos['IdEscuela']= implode(",",$datos['IdEscuela']);

        if (!parent::BuscarxIdEscuelaxIdNivelModalidadxIdCicloLectivo($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarCiclosxIdEscuelaTurno($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarCiclosxIdEscuelaTurno($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarOrientacionesxIdEscuelaTurno($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarOrientacionesxIdEscuelaTurno($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarGradosAniosxIdEscuelaTurno($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarGradosAniosxIdEscuelaTurno($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarSeccionesxIdEscuelaTurnoxIdGradoAnio($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarSeccionesxIdEscuelaTurnoxIdGradoAnio($datos,$resultado,$numfilas))
            return false;
        return true;
    }


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xIdEscuelaCiclo'=> 0,
			'IdEscuelaCiclo'=> "",
			'xIdEscuelaTurno'=> 0,
			'IdEscuelaTurno'=> "",
			'xIdCiclo'=> 0,
			'IdCiclo'=> "",
			'xIdCicloLectivo'=> 0,
			'IdCicloLectivo'=> "",
			'xIdOrientacion'=> 0,
			'IdOrientacion'=> "",
			'xOrientacion'=> 0,
			'Orientacion'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdEscuelaTurno DESC"
		);
		if(isset($datos['IdEscuelaCiclo']) && $datos['IdEscuelaCiclo']!="")
		{
			$sparam['IdEscuelaCiclo']= $datos['IdEscuelaCiclo'];
			$sparam['xIdEscuelaCiclo']= 1;
		}
		if(isset($datos['IdEscuelaTurno']) && $datos['IdEscuelaTurno']!="")
		{
			$sparam['IdEscuelaTurno']= $datos['IdEscuelaTurno'];
			$sparam['xIdEscuelaTurno']= 1;
		}
		if(isset($datos['IdCiclo']) && $datos['IdCiclo']!="")
		{
			$sparam['IdCiclo']= $datos['IdCiclo'];
			$sparam['xIdCiclo']= 1;
		}
		if(isset($datos['IdCicloLectivo']) && $datos['IdCicloLectivo']!="")
		{
			$sparam['IdCicloLectivo']= $datos['IdCicloLectivo'];
			$sparam['xIdCicloLectivo']= 1;
		}
		if(isset($datos['IdOrientacion']) && $datos['IdOrientacion']!="")
		{
			$sparam['IdOrientacion']= $datos['IdOrientacion'];
			$sparam['xIdOrientacion']= 1;
		}
		if(isset($datos['Orientacion']) && $datos['Orientacion']!="")
		{
			$sparam['Orientacion']= $datos['Orientacion'];
			$sparam['xOrientacion']= 1;
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


    public function BuscarExistente($datos,&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarExistente($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdPlanEducativoxIdEscuela($datos,&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdPlanEducativoxIdEscuela($datos,$resultado,$numfilas))
            return false;
        return true;
    }

	public function Insertar($datos,&$codigoInsertado): bool
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoInsertado))
			return false;
		$oAuditoriasEscuelasCiclos = new cAuditoriasEscuelasCiclos($this->conexion,$this->formato);
		$datos['IdEscuelaCiclo'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		if(!$oAuditoriasEscuelasCiclos->InsertarLog($datos,$codigoInsertadolog))
			return false;
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
		$oAuditoriasEscuelasCiclos = new cAuditoriasEscuelasCiclos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasCiclos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasCiclos = new cAuditoriasEscuelasCiclos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasEscuelasCiclos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdEscuelaCiclo'] = $datos['IdEscuelaCiclo'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}

    public function EliminarCascada($datos): bool
    {
        $oEscuelasTurnosAG = new cEscuelasTurnosAG($this->conexion,$this->formato);
        $datos['Estado'] = ACTIVO;
        if(!$oEscuelasTurnosAG->BuscarxIdEscuelaCiclo($datos, $resultado,$numfilas))
            return false;

        if ($numfilas > 0) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar. El ciclo se encuentra con grados/años asociados",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        /*while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
        {
            if (!$oEscuelasTurnosAG->EliminarCascada($fila))
                return false;
        }*/

        if (!$this->Eliminar($datos))
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
		$datosmodif['IdEscuelaTurno'] = $datos['IdEscuelaTurno'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasCiclos = new cAuditoriasEscuelasCiclos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasCiclos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdEscuelaTurno'] = $datos['IdEscuelaTurno'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasCiclos = new cAuditoriasEscuelasCiclos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasCiclos->InsertarLog($datosRegistro,$codigoInsertadolog))
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

        if (!$this->BuscarExistente($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe la relación del ciclo y el plan seleccionado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
		return true;
	}


	private function _SetearNull(&$datos): void
	{


		if (!isset($datos['IdEscuelaCiclo']) || $datos['IdEscuelaCiclo']=="")
			$datos['IdEscuelaCiclo']="NULL";

		if (!isset($datos['IdCiclo']) || $datos['IdCiclo']=="")
			$datos['IdCiclo']="NULL";

		if (!isset($datos['IdOrientacion']) || $datos['IdOrientacion']=="")
			$datos['IdOrientacion']="NULL";

		if (!isset($datos['Orientacion']) || $datos['Orientacion']=="")
			$datos['Orientacion']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

	}


	private function _ValidarDatosVacios($datos)
	{

		if (!isset($datos['IdCiclo']) || $datos['IdCiclo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un ciclo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdCiclo']) && $datos['IdCiclo']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdCiclo'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Error debe ingresar un campo numérico para el campo Ciclo."),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdCiclo'])>11)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Ciclo no puede ser mayor a 11 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

        if (!isset($datos['IdPlanEducativo']) || $datos['IdPlanEducativo']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar un plan en el nivel del establecimiento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (isset($datos['IdPlanEducativo']) && $datos['IdPlanEducativo']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdPlanEducativo'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo Id PlanEducativo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
            if (strlen($datos['IdPlanEducativo'])>11)
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Id PlanEducativo no puede ser mayor a 11 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }

		return true;
	}




}
