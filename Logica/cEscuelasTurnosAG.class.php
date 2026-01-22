<?php
include(DIR_CLASES_DB."cEscuelasTurnosAG.db.php");
class cEscuelasTurnosAG extends cEscuelasTurnosAGdb
{
	/**
	 * Constructor de la clase cEscuelasTurnosAG.
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
	 * Destructor de la clase cEscuelasTurnosAG.
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

    public function BuscarxIdEscuelaCiclo($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaCiclo($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdEscuelaCicloxIdGradoAnio($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaCicloxIdGradoAnio($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarxIdEscuelaxIdCicloLectivoxIdNivelModalidadxIdTurno($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaxIdCicloLectivoxIdNivelModalidadxIdTurno($datos,$resultado,$numfilas))
            return false;
        return true;
    }




	public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xIdEscuelaTurnoAnioGrado'=> 0,
			'IdEscuelaTurnoAnioGrado'=> "",
			'xIdEscuelaCiclo'=> 0,
			'IdEscuelaCiclo'=> "",
			'xIdGradoAnio'=> 0,
			'IdGradoAnio'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
            'xIdEscuela'=> 0,
            'IdEscuela'=> "-1",
			'limit'=> '',
			'orderby'=> "IdEscuelaTurnoAnioGrado DESC"
		);
		if(isset($datos['IdEscuelaTurnoAnioGrado']) && $datos['IdEscuelaTurnoAnioGrado']!="")
		{
			$sparam['IdEscuelaTurnoAnioGrado']= $datos['IdEscuelaTurnoAnioGrado'];
			$sparam['xIdEscuelaTurnoAnioGrado']= 1;
		}
		if(isset($datos['IdEscuelaCiclo']) && $datos['IdEscuelaCiclo']!="")
		{
			$sparam['IdEscuelaCiclo']= $datos['IdEscuelaCiclo'];
			$sparam['xIdEscuelaCiclo']= 1;
		}
		if(isset($datos['IdGradoAnio']) && $datos['IdGradoAnio']!="")
		{
			$sparam['IdGradoAnio']= $datos['IdGradoAnio'];
			$sparam['xIdGradoAnio']= 1;
		}
		if(isset($datos['Estado']) && $datos['Estado']!="")
		{
			$sparam['Estado']= $datos['Estado'];
			$sparam['xEstado']= 1;
		}
        if(isset($datos['IdEscuela']) && $datos['IdEscuela']!="")
        {
            $sparam['IdEscuela']= $datos['IdEscuela'];
            $sparam['xIdEscuela']= 1;
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
		$oAuditoriasEscuelasTurnosAG = new cAuditoriasEscuelasTurnosAG($this->conexion,$this->formato);
		$datos['IdEscuelaTurnoAnioGrado'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasEscuelasTurnosAG->InsertarLog($datos,$codigoInsertadolog))
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
		$oAuditoriasEscuelasTurnosAG = new cAuditoriasEscuelasTurnosAG($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasTurnosAG->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasTurnosAG = new cAuditoriasEscuelasTurnosAG($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasEscuelasTurnosAG->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdEscuelaTurnoAnioGrado'] = $datos['IdEscuelaTurnoAnioGrado'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}

    public function EliminarCascada($datos): bool
    {
        $oEscuelasAGSecciones = new cEscuelasAGSecciones($this->conexion,$this->formato);
        $datos['Estado'] = ACTIVO;
        if(!$oEscuelasAGSecciones->BuscarxIdEscuelaTurnoAnioGrado($datos, $resultado,$numfilas))
            return false;

        if ($numfilas > 0) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Error al eliminar. El grado se encuentra con divisiones asociadas"),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        /*while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
        {
            if (!$oEscuelasAGSecciones->Eliminar($fila))
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
		$datosmodif['IdEscuelaTurnoAnioGrado'] = $datos['IdEscuelaTurnoAnioGrado'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasTurnosAG = new cAuditoriasEscuelasTurnosAG($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasTurnosAG->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdEscuelaTurnoAnioGrado'] = $datos['IdEscuelaTurnoAnioGrado'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasTurnosAG = new cAuditoriasEscuelasTurnosAG($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasTurnosAG->InsertarLog($datosRegistro,$codigoInsertadolog))
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

        if (!$this->BuscarxIdEscuelaCicloxIdGradoAnio($datos, $resultado,$numfilas))
            return false;


        if($numfilas>0)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el Grado/A�o",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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

		if (!isset($datos['IdGradoAnio']) || $datos['IdGradoAnio']=="")
			$datos['IdGradoAnio']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

	}


	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdEscuelaCiclo']) || $datos['IdEscuelaCiclo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id escuela ciclo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdEscuelaCiclo']) && $datos['IdEscuelaCiclo']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdEscuelaCiclo'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo Id Escuela Ciclo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdEscuelaCiclo'])>10)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Id Escuela Ciclo no puede ser mayor a 10 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['IdGradoAnio']) || $datos['IdGradoAnio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Debe ingresar un grado/año"),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdGradoAnio']) && $datos['IdGradoAnio']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdGradoAnio'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo Grado A�o.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdGradoAnio'])>11)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Grado A�o no puede ser mayor a 11 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		return true;
	}




}
