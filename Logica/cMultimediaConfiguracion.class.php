<?php 
/*
CLASE LOGICA PARA EL MANEJO DE LOS ARCHIVOS MULTIMEDIA.
*/
include(DIR_CLASES_DB."cMultimediaConfiguracion.db.php");

class cMultimediaConfiguracion extends cMultimediaConfiguraciondb
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

// Parámetros de Entrada:
//		datos: arreglo de datos
//			formatocod = codigo del formato

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function BucarConfiguracionxTipo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BucarConfiguracionxTipo ($datos,$resultado,$numfilas))
			return false;
		return true;	
	}
	


}//fin clase	

?>