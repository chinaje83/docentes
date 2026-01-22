<?php 
include(DIR_CLASES_DB."cCiclosLectivos.db.php");
class cCiclosLectivos extends cCiclosLectivosdb
{
	/**
	 * Constructor de la clase cCiclosLectivos.
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
	 * Destructor de la clase cCiclosLectivos.
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


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xIdCicloLectivo'=> 0,
			'IdCicloLectivo'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdCicloLectivo DESC"
		);
		if(isset($datos['IdCicloLectivo']) && $datos['IdCicloLectivo']!="")
		{
			$sparam['IdCicloLectivo']= $datos['IdCicloLectivo'];
			$sparam['xIdCicloLectivo']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
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

    public function BuscarCombo(&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarCombo($resultado,$numfilas))
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
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoInsertado))
			return false;
		$oAuditoriasCiclosLectivos = new cAuditoriasCiclosLectivos($this->conexion,$this->formato);
		$datos['IdCicloLectivo'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasCiclosLectivos->InsertarLog($datos,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Modificar($datos): bool
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasCiclosLectivos = new cAuditoriasCiclosLectivos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasCiclosLectivos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasCiclosLectivos = new cAuditoriasCiclosLectivos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasCiclosLectivos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdCicloLectivo'] = $datos['IdCicloLectivo'];
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
		$datosmodif['IdCicloLectivo'] = $datos['IdCicloLectivo'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasCiclosLectivos = new cAuditoriasCiclosLectivos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasCiclosLectivos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdCicloLectivo'] = $datos['IdCicloLectivo'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasCiclosLectivos = new cAuditoriasCiclosLectivos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasCiclosLectivos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


    public function AsignarDefault(array $datos): bool
    {
        $oAuditoriasCiclosLectivos = new cAuditoriasCiclosLectivos($this->conexion,$this->formato);

        if (!$this->BuscarDefault($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            $datosAuditoria = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $datosAuditoria['Accion'] = MODIFICACION;
            if(!$oAuditoriasCiclosLectivos->InsertarLog($datosAuditoria,$codigoInsertadolog))
                return false;
        }

        $datosRegistro['IdCicloLectivo'] = $datos['IdCicloLectivo'];
        $datosRegistro['Accion'] = MODIFICACION;
        if(!$oAuditoriasCiclosLectivos->InsertarLog($datosRegistro,$codigoInsertadolog))
            return false;

        if (!parent::AsignarDefault($datos))
            return false;

        return true;
    }


    public function DesasignarDefault(array $datos): bool
    {
        $oAuditoriasCiclosLectivos = new cAuditoriasCiclosLectivos($this->conexion,$this->formato);
        $datosRegistro['IdCicloLectivo'] = $datos['IdCicloLectivo'];
        $datosRegistro['Accion'] = MODIFICACION;
        if(!$oAuditoriasCiclosLectivos->InsertarLog($datosRegistro,$codigoInsertadolog))
            return false;

        $datosmodif['IdCicloLectivo'] = $datos['IdCicloLectivo'];
        if (!parent::DesasignarDefault($datos))
            return false;

        return true;
    }


    public function BuscarDefault($datos, &$resultado, &$numfilas)
    {
        if (!parent::BuscarDefault($datos,$resultado, $numfilas))
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


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		
	}


	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			if (strlen($datos['Nombre'])>50)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Nombre no puede ser mayor a 50 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		return true;
	}




}