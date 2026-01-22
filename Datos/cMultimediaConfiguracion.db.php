<?php  
abstract class cMultimediaConfiguraciondb
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

// Parámetros de Entrada:
//		datos: arreglo de datos
//			formatocod = codigo del formato

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	protected function BucarConfiguracionxTipo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_configuracion_xconfigtipo";
		$sparam=array(
			'pconfigtipo'=> $datos['configtipo']
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la configuracion del multimedia por tipo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;	
	}
	

}
?>