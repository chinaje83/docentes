<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
include(DIR_CLASES_DB."cMultimediaConjuntos.db.php");

class cMultimediaConjuntos extends cMultimediaConjuntosdb	
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

//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 

// Trae los datos de la tabla mul_multimedia_conjuntos 

// Retorna una consulta con los datos de los tipos multimedia

// Parámetros de Entrada:
//		datos: arreglo de datos

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	function BusquedaMultimediaConjuntosSP(&$spnombre,&$sparam)
	{
		if (!parent::BusquedaMultimediaConjuntosSP ($spnombre,$sparam))
			return false;
		return true;			
	}

}//fin clase	

?>
