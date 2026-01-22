<?php  
abstract class cMultimediaConjuntosdb
{
	
	// Constructor de la clase
	function __construct(){


    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	

	
//-----------------------------------------------------------------------------------------
//							 PUBLICAS	
//----------------------------------------------------------------------------------------- 

//----------------------------------------------------------------------------------------- 
	function BusquedaMultimediaConjuntosSP (&$spnombre,&$sparam)
	{
		
		$spnombre="sel_mul_multimedia_conjuntos_xorderby";
		$sparam=array(
			'porderby'=> "multimediaconjuntocod ASC"
			);

		return true;
	}


}//fin clase	

?>
