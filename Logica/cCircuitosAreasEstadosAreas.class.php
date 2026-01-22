<?php 
include(DIR_CLASES_DB."cCircuitosAreasEstadosAreas.db.php");

class cCircuitosAreasEstadosAreas extends cCircuitosAreasEstadosAreasdb
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

	public function BuscarxCodigoNodo($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['Anio']) || $datos['Anio']=="" || !is_numeric($datos['Anio']))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) || $datos['Mes']=="" || !is_numeric($datos['Mes']))
			$datos['Mes'] = date("m");

		$datos['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";
		
		if (!parent::BuscarxCodigoNodo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarNombresAreasxCodigoNodo($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['Anio']) || $datos['Anio']=="" || !is_numeric($datos['Anio']))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) || $datos['Mes']=="" || !is_numeric($datos['Mes']))
			$datos['Mes'] = date("m");

		$datos['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";
		if (!parent::BuscarNombresAreasxCodigoNodo($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarxCodigoNodoxArea($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigoNodoxArea($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos,$datosNodo))
			return false;

		$this->_SetearNull($datos);
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['FechaAlta']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		$oAuditoriasCircuitosAreasEstadosAreas = new cAuditoriasCircuitosAreasEstadosAreas($this->conexion,$this->formato);
		$datos['IdNodoWorkflowArea'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['FechaAlta'] = $datos['FechaAlta'];
		if(!$oAuditoriasCircuitosAreasEstadosAreas->InsertarLog($datos,$codigoInsertadolog))
			return false;

		if ($datosNodo['NodoGeneral']==1)
		{
			$oCircuitosAreasEstados = new cCircuitosAreasEstados($this->conexion,$datosNodo['IdCircuito'],$this->formato);
			$datosModificar['IdNodoWorkflow'] = $datosNodo['IdNodoWorkflow'];
			$datosModificar['NodoGeneral'] = 0;
			if(!$oCircuitosAreasEstados->ModificarNodoGeneral($datosModificar))
				return false;
		}
		
		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		$oAuditoriasCircuitosAreasEstadosAreas = new cAuditoriasCircuitosAreasEstadosAreas($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasCircuitosAreasEstadosAreas->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		$oAuditoriasCircuitosAreasEstadosAreas = new cAuditoriasCircuitosAreasEstadosAreas($this->conexion,$this->formato);
		if (!$this->BuscarxCodigoNodo($datos,$resultado,$numfilas))
			return false;
			
		while($datosLog = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{	
			$datosLog['Accion'] = ELIMINAR;
			if(!$oAuditoriasCircuitosAreasEstadosAreas->InsertarLog($datosLog,$codigoInsertadolog))
				return false;
		}
		if (!parent::EliminarxNodo($datos))
			return false;

		return true;
	}




	public function EliminarxCodigo($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$datosLog = $datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		$oAuditoriasCircuitosAreasEstadosAreas = new cAuditoriasCircuitosAreasEstadosAreas($this->conexion,$this->formato);
		if(!$oAuditoriasCircuitosAreasEstadosAreas->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		
		
		if (!parent::Eliminar($datos))
			return false;

		return true;
	}




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar($datos,&$datosNodo)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		if (!$this->BuscarxCodigoNodoxArea($datos,$resultado,$numfilas))
			return false;
		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el area ya existe.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$oCircuitosAreasEstados = new cCircuitosAreasEstados($this->conexion,$datos['IdCircuito'],$this->formato);
		if(!$oCircuitosAreasEstados->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		$datosNodo = $this->conexion->ObtenerSiguienteRegistro($resultado);

		/*
		$datosBusqueda['IdArea'] = $datos['IdArea'];
		$datosBusqueda['IdEstado'] = $datosNodo['IdEstado'];
		if(!$oCircuitosAreasEstados->BuscarxAreaxEstadoxCircuito($datosBusqueda,$resultado,$numfilas))
			return false;
		
		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya se encuentra un area y estado dentro del circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		*/
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
		
		$datosRegistro['Anio'] = $datos['Anio'];
		$datosRegistro['Mes'] = $datos['Mes'];
		if(!$this->BuscarxCodigoNodo($datosRegistro,$resultado,$numfilas))
			return false;
			
		if ($numfilas<2)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe existir al menos un area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdNodoWorkflow']) || $datos['IdNodoWorkflow']=="")
			$datos['IdNodoWorkflow']="NULL";

		if (!isset($datos['IdArea']) || $datos['IdArea']=="")
			$datos['IdArea']="NULL";

		if (!isset($datos['NodoGeneral']) || $datos['NodoGeneral']=="")
			$datos['NodoGeneral']="0";

		if (!isset($datos['FechaAlta']) || $datos['FechaAlta']=="")
			$datos['FechaAlta']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdNodoWorkflow']) || $datos['IdNodoWorkflow']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nodo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdNodoWorkflow'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['NodoGeneral']) && $datos['NodoGeneral']==0)
		{
			if (!isset($datos['IdArea']) || $datos['IdArea']=="")
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un area",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdArea'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		return true;
	}





}
?>