<?php 
include(DIR_CLASES_DB."cDocumentosRelacionados.db.php");

class cDocumentosRelacionados extends cDocumentosRelacionadosdb
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
	
	public function BuscarxIdDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	

	public function Insertar($datos)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
        $datos['AltaUsuario']= $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] =date("Y-m-d H:i:s");
        $datos['AltaApp'] = $datos['UltimaModificacionApp'] = APP;

		if (!parent::Insertar($datos))
			return false;

		$oAuditoriasDocumentosRelacionados = new cAuditoriasDocumentosRelacionados($this->conexion,$this->formato);
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasDocumentosRelacionados->InsertarLog($datos,$codigoInsertadolog))
			return false;


		return true;
	}

    public function Eliminar($datos)
    {
        if (!$this->_ValidarEliminar($datos,$datosRegistro))
            return false;

        $oAuditoriasDocumentosRelacionados = new cAuditoriasDocumentosRelacionados($this->conexion,$this->formato);
        $datosLog =$datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if(!$oAuditoriasDocumentosRelacionados->InsertarLog($datosLog,$codigoInsertadolog))
           return false;

        if (!parent::Eliminar($datos))
            return false;




        return true;
    }

	

	public function EliminarxIdDocumento($datos)
	{
		if (!$this->BuscarxIdDocumento($datos,$resultado,$numfilas))
			return false;


		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
        {
            if(!$this->Eliminar($fila))
                return false;

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
	
	private function _ValidarDatosVacios($datos)
	{
        if (!isset($datos['IdDocumento']) || $datos['IdDocumento']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (!isset($datos['IdDocumentoRelacionado']) || $datos['IdDocumentoRelacionado']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una Documento Relacionado",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
		return true;
	}
	
	private function _SetearNull(&$datos)
	{
		if (!isset($datos['IdDocumento']) || $datos['IdDocumento']=="")
			$datos['IdDocumento']="NULL";
			
		if (!isset($datos['IdDocumentoRelacionado']) || $datos['IdDocumentoRelacionado']=="")
			$datos['IdDocumentoRelacionado']="NULL";

			
		return true;
	}




}
?>