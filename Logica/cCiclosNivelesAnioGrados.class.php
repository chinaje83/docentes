<?php 
include(DIR_CLASES_DB."cCiclosNivelesAnioGrados.db.php");
class cCiclosNivelesAnioGrados extends cCiclosNivelesAnioGradosdb
{
	/**
	 * Constructor de la clase cCiclosNivelesAnioGrados.
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
	 * Destructor de la clase cCiclosNivelesAnioGrados.
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

    public function BuscarxIdEscuela($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdEscuela($datos,$resultado,$numfilas))
            return false;
        return true;
    }


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xIdNivelGradoAnio'=> 0,
			'IdNivelGradoAnio'=> "",
			'xIdCiclo'=> 0,
			'IdCiclo'=> "",
			'xIdEstructura'=> 0,
			'IdEstructura'=> "",
			'xIdNivel' => 0,
			'IdNivel' => "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdCicloNivelAnioGrado DESC"
		);
		if(isset($datos['IdNivelGradoAnio']) && $datos['IdNivelGradoAnio']!="")
		{
			$sparam['IdNivelGradoAnio']= $datos['IdNivelGradoAnio'];
			$sparam['xIdNivelGradoAnio']= 1;
		}
		if(isset($datos['IdCiclo']) && $datos['IdCiclo']!="")
		{
			$sparam['IdCiclo']= $datos['IdCiclo'];
			$sparam['xIdCiclo']= 1;
		}
		if(isset($datos['IdEstructura']) && $datos['IdEstructura']!="")
		{
			$sparam['IdEstructura']= $datos['IdEstructura'];
			$sparam['xIdEstructura']= 1;
		}
        if(isset($datos['IdNivel']) && $datos['IdNivel']!="")
        {
            $sparam['IdNivel']= $datos['IdNivel'];
            $sparam['xIdNivel']= 1;
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
		$oAuditoriasCiclosNivelesAnioGrados = new cAuditoriasCiclosNivelesAnioGrados($this->conexion,$this->formato);
		$datos['IdCicloNivelAnioGrado'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasCiclosNivelesAnioGrados->InsertarLog($datos,$codigoInsertadolog))
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
		$oAuditoriasCiclosNivelesAnioGrados = new cAuditoriasCiclosNivelesAnioGrados($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasCiclosNivelesAnioGrados->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasCiclosNivelesAnioGrados = new cAuditoriasCiclosNivelesAnioGrados($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasCiclosNivelesAnioGrados->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdCicloNivelAnioGrado'] = $datos['IdCicloNivelAnioGrado'];
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
		$datosmodif['IdCicloNivelAnioGrado'] = $datos['IdCicloNivelAnioGrado'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasCiclosNivelesAnioGrados = new cAuditoriasCiclosNivelesAnioGrados($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasCiclosNivelesAnioGrados->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdCicloNivelAnioGrado'] = $datos['IdCicloNivelAnioGrado'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasCiclosNivelesAnioGrados = new cAuditoriasCiclosNivelesAnioGrados($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasCiclosNivelesAnioGrados->InsertarLog($datosRegistro,$codigoInsertadolog))
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Error debe ingresar un código valido."),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}


	private function _SetearNull(&$datos): void
	{


		if (!isset($datos['IdNivelGradoAnio']) || $datos['IdNivelGradoAnio']=="")
			$datos['IdNivelGradoAnio']="NULL";

		if (!isset($datos['IdCiclo']) || $datos['IdCiclo']=="")
			$datos['IdCiclo']="NULL";

		if (!isset($datos['IdEstructura']) || $datos['IdEstructura']=="")
			$datos['IdEstructura']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		
	}


	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdNivelGradoAnio']) || $datos['IdNivelGradoAnio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Debe ingresar un nivel - grado - año"),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdCiclo']) || $datos['IdCiclo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un ciclo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdEstructura']) || $datos['IdEstructura']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un estructura",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}




}