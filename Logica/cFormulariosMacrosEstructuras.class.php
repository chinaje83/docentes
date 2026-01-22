<?php 
include(DIR_CLASES_DB."cFormulariosMacrosEstructuras.db.php");

class cFormulariosMacrosEstructuras extends cFormulariosMacrosEstructurasdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarEstructurasxIdMacro($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['orderby']))
			$datos['orderby'] = "Orden ASC";
		if (!parent::BuscarEstructurasxIdMacro($datos,$resultado,$numfilas))
			return false;
			
		return true;
	}





}
?>