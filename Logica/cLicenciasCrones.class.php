<?php 
include(DIR_CLASES_DB."cLicenciasCrones.db.php");

class cLicenciasCrones extends cLicenciasCronesdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	
	public function BuscarUltimoCron(&$resultado,&$numfilas)
	{
		if (!parent::BuscarUltimoCron($resultado,$numfilas))
			return false;
		return true;
	}
	
	public function Insertar($datos,&$codigoinsertado)
	{

		$this->_SetearNull($datos);
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		return true;
	}
	
	
	private function _SetearNull(&$datos)
	{


		if (!isset($datos['dateEnd']) || $datos['dateEnd']=="")
			$datos['dateEnd']="NULL";
		
		if (!isset($datos['Total']) || $datos['Total']=="")
			$datos['Total']="0";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

		return true;
	}


}
?>