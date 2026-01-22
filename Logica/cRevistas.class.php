<?php 
include(DIR_CLASES_DB."cRevistas.db.php");
class cRevistas extends cRevistasdb
{
	/**
	 * Constructor de la clase cRevistas.
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
	 * Destructor de la clase cRevistas.
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
			'xIdRevista'=> 0,
			'IdRevista'=> "",
			'xIdRevistaExterno'=> 0,
			'IdRevistaExterno'=> "",
			'xCodigo'=> 0,
			'Codigo'=> "",
			'xDescripcion'=> 0,
			'Descripcion'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdRevista DESC"
		);
		if(isset($datos['IdRevista']) && $datos['IdRevista']!="")
		{
			$sparam['IdRevista']= $datos['IdRevista'];
			$sparam['xIdRevista']= 1;
		}
		if(isset($datos['IdRevistaExterno']) && $datos['IdRevistaExterno']!="")
		{
			$sparam['IdRevistaExterno']= $datos['IdRevistaExterno'];
			$sparam['xIdRevistaExterno']= 1;
		}
		if(isset($datos['Codigo']) && $datos['Codigo']!="")
		{
			$sparam['Codigo']= $datos['Codigo'];
			$sparam['xCodigo']= 1;
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
		$oAuditoriasRevistas = new cAuditoriasRevistas($this->conexion,$this->formato);
		$datos['IdRevista'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasRevistas->InsertarLog($datos,$codigoInsertadolog))
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
		$oAuditoriasRevistas = new cAuditoriasRevistas($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasRevistas->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasRevistas = new cAuditoriasRevistas($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasRevistas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdRevista'] = $datos['IdRevista'];
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
		$datosmodif['IdRevista'] = $datos['IdRevista'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasRevistas = new cAuditoriasRevistas($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasRevistas->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdRevista'] = $datos['IdRevista'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasRevistas = new cAuditoriasRevistas($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasRevistas->InsertarLog($datosRegistro,$codigoInsertadolog))
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


		if (!isset($datos['IdRevistaExterno']) || $datos['IdRevistaExterno']=="")
			$datos['IdRevistaExterno']="NULL";

		if (!isset($datos['Codigo']) || $datos['Codigo']=="")
			$datos['Codigo']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		
	}


	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['Codigo']) || $datos['Codigo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un c�digo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un descripcion",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

       /* if (!isset($datos['IdRevistaExterno']) || $datos['IdRevistaExterno']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id tabla externa",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

       /* if (isset($datos['IdRevistaExterno']) && $datos['IdRevistaExterno']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRevistaExterno'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }*/
		return true;
	}




}