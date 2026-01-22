<?php
include(DIR_CLASES_DB."cEscuelasTurnos.db.php");
class cEscuelasTurnos extends cEscuelasTurnosdb
{
	/**
	 * Constructor de la clase cEscuelasTurnos.
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
	 * Destructor de la clase cEscuelasTurnos.
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

    public function getTurnos(&$resultado,&$numfilas): bool
    {
        if (!parent::getTurnos($resultado,$numfilas))
            return false;
        return true;
    }

	public function BuscarxCodigo($datos, &$resultado,&$numfilas): bool
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

    public function BuscarxIdEscuelaxIdNivelModalidadxIdTurnoxIdCicloLectivo($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaxIdNivelModalidadxIdTurnoxIdCicloLectivo($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdEscuelaxIdNivelModalidadxIdCicloLectivo($datos, &$resultado,&$numfilas): bool
    {
        if (is_array($datos['IdEscuela'])){
            $datos['IdEscuela']= implode(",",$datos['IdEscuela']);
        }

        if (!parent::BuscarxIdEscuelaxIdNivelModalidadxIdCicloLectivo($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdEscuelaTurno($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaTurno($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarDatosxIdEscuelaTurno($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarDatosxIdEscuelaTurno($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarTurno($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarTurno($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarMaterias($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarMaterias($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdEscuela($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuela($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdEscuelaxIdTurno($datos, &$resultado,&$numfilas): bool {
        return parent::BuscarxIdEscuelaxIdTurno($datos,$resultado,$numfilas);
    }

    public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xIdEscuelaTurno'=> 0,
			'IdEscuelaTurno'=> "",
			'xIdEscuela'=> 0,
			'IdEscuela'=> "-1",
			'xIdNivelModalidad'=> 0,
			'IdNivelModalidad'=> "",
			'xIdTurno'=> 0,
			'IdTurno'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdEscuelaTurno DESC"
		);
		if(isset($datos['IdEscuelaTurno']) && $datos['IdEscuelaTurno']!="")
		{
			$sparam['IdEscuelaTurno']= $datos['IdEscuelaTurno'];
			$sparam['xIdEscuelaTurno']= 1;
		}
		if(isset($datos['IdEscuela']) && $datos['IdEscuela']!="")
		{
			$sparam['IdEscuela']= $datos['IdEscuela'];
			$sparam['xIdEscuela']= 1;
		}
		if(isset($datos['IdNivelModalidad']) && $datos['IdNivelModalidad']!="")
		{
			$sparam['IdNivelModalidad']= $datos['IdNivelModalidad'];
			$sparam['xIdNivelModalidad']= 1;
		}
		if(isset($datos['IdTurno']) && $datos['IdTurno']!="")
		{
			$sparam['IdTurno']= $datos['IdTurno'];
			$sparam['xIdTurno']= 1;
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
		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoInsertado))
			return false;
		$oAuditoriasEscuelasTurnos = new cAuditoriasEscuelasTurnos($this->conexion,$this->formato);
		$datos['IdEscuelaTurno'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasEscuelasTurnos->InsertarLog($datos,$codigoInsertadolog))
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
		$oAuditoriasEscuelasTurnos = new cAuditoriasEscuelasTurnos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasTurnos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasTurnos = new cAuditoriasEscuelasTurnos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasEscuelasTurnos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdEscuelaTurno'] = $datos['IdEscuelaTurno'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}

    public function EliminarCascada($datos): bool
    {
        $oEscuelasCiclos = new cEscuelasCiclos($this->conexion,$this->formato);
        $datos['Estado'] = ACTIVO;
        if(!$oEscuelasCiclos->BuscarxIdEscuelaTurno($datos, $resultado,$numfilas))
            return false;

        if ($numfilas > 0) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar. El turno se encuentra con uno o varios ciclos asociados",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        /*while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
        {
            if (!$oEscuelasCiclos->EliminarCascada($fila))
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
		$oAuditoriasEscuelasTurnos = new cAuditoriasEscuelasTurnos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasTurnos->InsertarLog($datosRegistro,$codigoInsertadolog))
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
		$oAuditoriasEscuelasTurnos = new cAuditoriasEscuelasTurnos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasTurnos->InsertarLog($datosRegistro,$codigoInsertadolog))
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

        if (!$this->BuscarxIdEscuelaxIdNivelModalidadxIdTurnoxIdCicloLectivo($datos,  $resultado, $numfilas))
            return false;

        if($numfilas>0)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el turno.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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


		if (!isset($datos['IdEscuela']) || $datos['IdEscuela']=="")
			$datos['IdEscuela']="NULL";

		if (!isset($datos['IdNivelModalidad']) || $datos['IdNivelModalidad']=="")
			$datos['IdNivelModalidad']="NULL";

        if (!isset($datos['IdCicloLectivo']) || $datos['IdCicloLectivo']=="")
            $datos['IdCicloLectivo']="NULL";

		if (!isset($datos['IdTurno']) || $datos['IdTurno']=="")
			$datos['IdTurno']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

	}


	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdEscuela']) || $datos['IdEscuela']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un escuela",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdEscuela']) && $datos['IdEscuela']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdEscuela'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo Escuela.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdEscuela'])>10)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Escuela no puede ser mayor a 10 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['IdNivelModalidad']) || $datos['IdNivelModalidad']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nivel modalidad",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdNivelModalidad']) && $datos['IdNivelModalidad']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdNivelModalidad'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo Nivel Modalidad.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdNivelModalidad'])>11)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Nivel Modalidad no puede ser mayor a 11 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}


		if (!isset($datos['IdTurno']) || $datos['IdTurno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un turno",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdTurno']) && $datos['IdTurno']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTurno'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo Turno.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdTurno'])>11)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Turno no puede ser mayor a 11 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		return true;
	}




}
