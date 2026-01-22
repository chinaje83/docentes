<?php 
include(DIR_CLASES_DB."cCamposReservados.db.php");

class cCamposReservados extends cCamposReservadosdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarCamposReservados($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarCamposReservados($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarCamposReservadosxNombreCampo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarCamposReservadosxNombreCampo($datos,$resultado,$numfilas))
			return false;
		return true;
	}




}
?>