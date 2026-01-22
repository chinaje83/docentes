<?php 
include(DIR_CLASES_DB."cBuscadorTipos.db.php");

class cBuscadorTipos extends cBuscadorTiposdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarBuscadotxClientexBuscador($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarBuscadotxClientexBuscador($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	

}
?>