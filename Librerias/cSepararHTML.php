<?php 
class cSepararHTML
{
	

	// Constructor de la clase
	function __construct(){

    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	

//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 


	static function ProcesarParametros($parte,&$componentes,$posicion=0)
	{
		if($posicion==0)
		{
			$componentes=array();
			if(!preg_match("/^([A-Z0-9]+='[^']+')( [A-Z0-9]+='[^']+')*$/i",$parte))
				return false;
		}

		$componentelocal=array();
		if(!preg_match("/^([A-Z0-9]+='[^']+')/i",$parte,$componentelocal))
			return false;
		else
		{
			$posigual=strpos($componentelocal[0],"=");
			$componentes[substr($componentelocal[0],0,$posigual)]=substr($componentelocal[0],$posigual+2,strlen(substr($componentelocal[0],$posigual+2))-1);
			$parte=substr($parte,strlen($componentelocal[0])+1);
			if(strlen($parte)>0)
				return cSepararHTML::ProcesarParametros($parte,$componentes,++$posicion);
		}
		
		return true;
	}
	
//----------------------------------------------------------------------------------------- 
// Verifica si lo que llega en $parte es un comando.
// Si es asi, lo procesa, sino retorna false
	
	static function ProcesarComando($parte,&$componentes)
	{
		if(substr($parte,0,5)=="Tipo=")
		{
			if(!preg_match("/^([A-Z0-9]+='[^']+')( [A-Z0-9]+='[^']+')*$/i",$parte))
				return false;
		}
		else
			return false;
	
		return cSepararHTML::ProcesarParametros($parte,$componentes);
	}
	
//----------------------------------------------------------------------------------------- 
// Separa el codigo html en un array.
// Cada posición en partes puede ser:
//      codigo HTML puro, para enviar directamente
//      un array, indicando que es un comando que hay que interpretar
	
	static function ProcesarHTML($html,&$partes)
	{
		$partes=explode("$$",$html);
		
		for($i=0;$i<count($partes);$i++)
		{
			$componentes=array();
			if(cSepararHTML::ProcesarComando($partes[$i],$componentes))
				$partes[$i]=$componentes;
		}
		
		return true;
	}

//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 

	
}//FIN CLASE

?>