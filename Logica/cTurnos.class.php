<?php 
include(DIR_CLASES_DB."cTurnos.db.php");
class cTurnos extends cTurnosdb
{
	/**
	 * Constructor de la clase cTurnos.
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
	 * Destructor de la clase cTurnos.
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
			'xIdTurno'=> 0,
			'IdTurno'=> "",
			'xIdTurnoExterno'=> 0,
			'IdTurnoExterno'=> "",
			'xTurno'=> 0,
			'Turno'=> "",
			'xDescripcion'=> 0,
			'Descripcion'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "Orden ASC"
		);
		if(isset($datos['IdTurno']) && $datos['IdTurno']!="")
		{
			$sparam['IdTurno']= $datos['IdTurno'];
			$sparam['xIdTurno']= 1;
		}
		if(isset($datos['IdTurnoExterno']) && $datos['IdTurnoExterno']!="")
		{
			$sparam['IdTurnoExterno']= $datos['IdTurnoExterno'];
			$sparam['xIdTurnoExterno']= 1;
		}
		if(isset($datos['Turno']) && $datos['Turno']!="")
		{
			$sparam['Turno']= $datos['Turno'];
			$sparam['xTurno']= 1;
		}
		if(isset($datos['Descripcion']) && $datos['Descripcion']!="")
		{
			$sparam['Descripcion']= $datos['Descripcion'];
			$sparam['xDescripcion']= 1;
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
		$this->ObtenerProximoOrden($datos,$proxorden);
		$datos['Orden'] = $proxorden;
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoInsertado))
			return false;
		$oAuditoriasTurnos = new cAuditoriasTurnos($this->conexion,$this->formato);
		$datos['IdTurno'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasTurnos->InsertarLog($datos,$codigoInsertadolog))
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
		$oAuditoriasTurnos = new cAuditoriasTurnos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasTurnos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasTurnos = new cAuditoriasTurnos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasTurnos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdTurno'] = $datos['IdTurno'];
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
		$datosmodif['IdTurno'] = $datos['IdTurno'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasTurnos = new cAuditoriasTurnos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasTurnos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdTurno'] = $datos['IdTurno'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasTurnos = new cAuditoriasTurnos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasTurnos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function ModificarOrdenCompleto($datos): bool
	{
		$datosmodif['Orden'] = 1;
		$arregloOrden = explode(",",$datos['orden']);
		foreach ($arregloOrden as $IdTurno){
			$datosmodif['IdTurno'] = $IdTurno;
			if (!parent::ModificarOrden($datosmodif))
				return false;
			$datosmodif['Orden']++;
		}
		return true;
	}


	private function ObtenerProximoOrden(array $datos, ?int &$proxorden): bool
	{
		$proxorden = 0;
		if (!parent::BuscarUltimoOrden($datos,$resultado,$numfilas))
			return false;
		if ($numfilas!=0){
			$datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$proxorden = $datos['maximo'] + 1;
		}
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


		if (!isset($datos['IdTurnoExterno']) || $datos['IdTurnoExterno']=="")
			$datos['IdTurnoExterno']="NULL";

		if (!isset($datos['Turno']) || $datos['Turno']=="")
			$datos['Turno']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

		if (!isset($datos['Vigh']) || $datos['Vigh']=="")
			$datos['Vigh']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		
	}


	private function _ValidarDatosVacios($datos)
	{
		/*if (!isset($datos['Turno']) || $datos['Turno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un turno",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un descripcion",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

        /*if (!isset($datos['IdTurnoExterno']) || $datos['IdTurnoExterno']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Id de Tabla Externa",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

        if (isset($datos['IdTurnoExterno']) && $datos['IdTurnoExterno']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTurnoExterno'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }

		/*if (!isset($datos['Vigh']) || $datos['Vigh']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una vigencia",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		return true;
	}




}