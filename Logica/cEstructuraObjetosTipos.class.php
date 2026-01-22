<?php 
include(DIR_CLASES_DB."cEstructuraObjetosTipos.db.php");

class cEstructuraObjetosTipos extends cEstructuraObjetosTiposdb
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
	
	
	public function BuscarxIdObjeto($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdObjeto($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxIdObjetoActivos($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdObjetoActivos($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	


	public function BuscarTiposCampos($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarTiposCampos($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function Insertar($datos)
	{
		if (!$this->_ValidarInsertar($datos,$datosTipo))
			return false;
			
		$datos['TipoCampoElastic'] = $datosTipo['TipoCampoElasticSearch'];
		$this->_SetearNull($datos);
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datos['FechaAlta'] = date("Y-m-d H:i:s");
		if (!parent::Insertar($datos))
			return false;
			

		/*$oAuditoriasDocumentosTiposDependientes = new cAuditoriasDocumentosTiposDependientes($this->conexion,$this->formato);
		$datos['IdTipoDocumentoDependiente'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasDocumentosTiposDependientes->InsertarLog($datos,$codigoInsertadolog))
			return false; 
		*/
		return true;
	}
	
	public function Eliminar($datos)
	{
		
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		/*$oAuditoriasDocumentosTiposCamposBusqueda = new cAuditoriasDocumentosTiposCamposBusqueda($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosTiposCamposBusqueda->InsertarLog($datosLog,$codigoInsertadolog))
			return false;	*/
			

		if (!parent::Eliminar($datos))
			return false;
			

		return true;
	}

//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar($datos,&$datostipo)
	{
		if (!$this->_ValidarDatosVacios($datos,$datostipo))
			return false;
		
		$datosbuscar['IdObjeto'] = $datos['IdObjeto'];
		$datosbuscar['IdTipoCampo'] = $datos['IdTipoCampo'];
		
		
		if(!$this->BuscarxCodigo($datosbuscar,$resultado,$numfilas))
			return false;
			
		if($numfilas==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el tipo de campo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
			
		}	
		return true;
	}
	
	private function _ValidarEliminar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un cÃ³digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdObjeto']) || $datos['IdObjeto']=="")
			$datos['IdObjeto']="NULL";

		if (!isset($datos['IdTipoCampo']) || $datos['IdTipoCampo']=="")
			$datos['IdTipoCampo']="NULL";

		if (!isset($datos['TipoCampoElastic']) || $datos['TipoCampoElastic']=="")
			$datos['TipoCampoElastic']="NULL";

		return true;
	}



	private function _ValidarDatosVacios($datos,&$datostipo)
	{


		if (!isset($datos['IdObjeto']) || $datos['IdObjeto']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un código  de objeto",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdObjeto'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdTipoCampo']) || $datos['IdTipoCampo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo de campo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTipoCampo'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		$oEstructuraCamposTipos = new cEstructuraCamposTipos($this->conexion,$this->formato);
		
		if(!$oEstructuraCamposTipos->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
			
		$datostipo = $this->conexion->ObtenerSiguienteRegistro($resultado);
		
		return true;
	}




}
?>