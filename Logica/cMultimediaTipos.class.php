<?php 
/*
CLASE LOGICA PARA EL MANEJO DE LOS ARCHIVOS MULTIMEDIA.
*/
include(DIR_CLASES_DB."cMultimediaTipos.db.php");

class cMultimediaTipos extends cMultimediaTiposdb
{
	protected $conexion;
	protected $formato;
	
	
	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		parent::__construct(); 
    } 
	
	// Destructor de la clase
	function __destruct() {	
		parent::__destruct(); 
    } 	


//-----------------------------------------------------------------------------------------
//							 PUBLICAS	
//----------------------------------------------------------------------------------------- 

	public function SpMultimediaTipos(&$spnombre,&$sparam)
	{
		if (!parent::SpMultimediaTipos($spnombre,$sparam))
			return false;

		return true;
	}


	public function SpMultimediaTiposxTipo($datos,&$spnombre,&$sparam)
	{
		if (!parent::SpMultimediaTiposxTipo($datos,$spnombre,$sparam))
			return false;

		return true;
	}

//----------------------------------------------------------------------------------------- 
// Retorna una consulta los tipos de multimedia por tipo de archivo

// Parámetros de Entrada:
//		datos: arreglo de datos
//			multimediatipocod = codigo del tipo de multimedia

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function BuscarMultimediaTiposxTipoArchivo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarMultimediaTiposxTipoArchivo($datos,$resultado,$numfilas))
			return false;

		return true;
	}

	
	
}//fin clase	

?>