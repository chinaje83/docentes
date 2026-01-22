<?php 

class cDocumentosPDF
{

	protected $conexion;
	protected $formato;
	protected $Campos;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		$this->Campos = array();
	}

	function __destruct(){}



	public function ArmarHTMLDocumento($datosDocumento,&$html)
	{
		$datosregistro['IdRegistroTipoDocumento'] = $datosDocumento['TipoDocumento']['IdRegistro'];
		$oDocumentosProcesar = new cDocumentosProcesar($this->conexion);
		$oDocumentosProcesar->GenerarFormulario(false);
		$oDocumentosProcesar->Procesar($datosregistro, $datosDocumento, $html);
		return true;
	}

}
?>