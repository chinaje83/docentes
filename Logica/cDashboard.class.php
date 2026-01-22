<?php 
include(DIR_CLASES_DB."cDashboard.db.php");

class cDashboard extends cDashboarddb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}


	public function BuscarCantidadTrabajandoxArea($datos,&$resultado,&$numfilas)
	{

		if (!parent::BuscarCantidadTrabajandoxArea($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarCantidadContralores($datos,&$resultado,&$numfilas)
	{

		if (!parent::BuscarCantidadContralores($datos,$resultado,$numfilas))
			return false;
		return true;
	}



}
?>