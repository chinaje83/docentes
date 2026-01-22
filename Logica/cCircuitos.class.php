<?php 
include(DIR_CLASES_DB."cCircuitos.db.php");

class cCircuitos extends cCircuitosdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xNombre'=> 0,
			'Nombre'=> "",
			'limit'=> '',
			'orderby'=> "IdCircuito DESC"
		);

		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}


	public function Insertar($datos)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$datos['FechaAlta']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		$this->_SetearNull($datos);
		if (!parent::Insertar($datos))
			return false;
		
		
		$oAuditoriasCircuitos = new cAuditoriasCircuitos($this->conexion,$this->formato);
		$datos['IdCircuito'] = $datos['IdCircuito'];
		$datos['AltaUsuario'] = $_SESSION['usuariocod'];
		$datos['FechaAlta'] = $datos['FechaAlta'];
		$datos['Estado'] = $datos['Estado'];
		$datos['Accion'] = INSERTAR;
		$datos['Nombre'] = $datos['Nombre'];
		$datos['NombreCorto'] = $datos['NombreCorto'];
		$datos['Descripcion'] = $datos['Descripcion'];
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		if(!$oAuditoriasCircuitos->InsertarLog($datos,$codigoLogInsertado))
			return false;
		
		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		$oAuditoriasCircuitos = new cAuditoriasCircuitos($this->conexion,$this->formato);
		$datos['IdCircuito'] = $datosRegistro['IdCircuito'];
		$datos['AltaUsuario'] = $datosRegistro['AltaUsuario'];
		$datos['FechaAlta'] = $datosRegistro['FechaAlta'];
		$datos['Estado'] = $datosRegistro['Estado'];
		$datos['Accion'] = MODIFICACION;
		$datos['Nombre'] = $datos['Nombre'];
		$datos['NombreCorto'] = $datos['NombreCorto'];
		$datos['Descripcion'] = $datos['Descripcion'];
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		if(!$oAuditoriasCircuitos->InsertarLog($datos,$codigoInsertado))
			return false;
		
		
		
		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$datosmodif['IdCircuito'] = $datos['IdCircuito'];
		$datosmodif['Estado'] = ELIMINADO;
		$datosmodif['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		if (!$this->ModificarEstado($datosmodif))
			return false;


		$oAuditoriasCircuitos = new cAuditoriasCircuitos($this->conexion,$this->formato);
		$datos = $datosRegistro;
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = $datosmodif['UltimaModificacionFecha'];
		$datos['Accion'] = ELIMINAR;
		if(!$oAuditoriasCircuitos->InsertarLog($datos,$codigoLogInsertado))
			return false;


		return true;
	}



	public function ModificarEstado($datos)
	{
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}



	public function Activar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

		$datosmodif['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datosmodif['IdCircuito'] = $datos['IdCircuito'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		
		
		$oAuditoriasCircuitos = new cAuditoriasCircuitos($this->conexion,$this->formato);
		$datos = $datosRegistro;
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = $datosmodif['UltimaModificacionFecha'];
		$datos['Accion'] = ACTIVAR;
		if(!$oAuditoriasCircuitos->InsertarLog($datos,$codigoLogInsertado))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);


		$datosmodif['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datosmodif['IdCircuito'] = $datos['IdCircuito'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;

		$oAuditoriasCircuitos = new cAuditoriasCircuitos($this->conexion,$this->formato);
		$datos = $datosRegistro;
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = $datosmodif['UltimaModificacionFecha'];
		$datos['Accion'] = DESACTIVAR;
		if(!$oAuditoriasCircuitos->InsertarLog($datos,$codigoLogInsertado))
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



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['NombreCorto']) || $datos['NombreCorto']=="")
			$datos['NombreCorto']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";


		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['NombreCorto']) || $datos['NombreCorto']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre corto",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>