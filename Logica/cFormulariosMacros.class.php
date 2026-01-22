<?php 
include(DIR_CLASES_DB."cFormulariosMacros.db.php");

class cFormulariosMacros extends cFormulariosMacrosdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarMacros($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['orderby']))
			$datos['orderby'] = "IdMacro ASC";
		if (!parent::BuscarMacros($datos,$resultado,$numfilas))
			return false;
			
		return true;
	}





}
?>