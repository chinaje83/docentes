<?php  
include(DIR_CLASES_DB."cArchivos.db.php");

class cArchivos extends cArchivosdb	
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
	public function Buscar ($ArregloDatos,&$numfilas,&$resultado)
	{
		if (!parent::Buscar ($ArregloDatos,$numfilas,$resultado))
			return false;
		return true;	
	}



	public function Insertar ($ArregloDatos,&$codigoarchivo)
	{
		if (!$this->Buscar ($ArregloDatos,$numfilas,$resultado))
			return false;
		
		if ($numfilas==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, El Archivo ya se encuentra insertado en la tabla de Archivos. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!parent::Insertar($ArregloDatos,$codigoarchivo))
			return false;
			
		return true;
	}

	public function Modificar ($ArregloDatos)
	{
		$datosbuscar['IdArchivo'] = $ArregloDatos['IdArchivo'];
		if (!$this->Buscar ($datosbuscar,$numfilas,$resultado))
			return false;
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Archivo Inexistente. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!parent::Modificar($ArregloDatos))
			return false;
		
		return true;
	}

	public function Eliminar ($ArregloDatos)
	{
		if (!$this->Buscar ($ArregloDatos,$numfilas,$resultado))
			return false;
		
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Archivo Inexistente. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!parent::Eliminar($ArregloDatos))
			return false;
		
		return true;
	}


	public function ObtenerEspacioEnDisco (&$ArregloDatos)
	{
		$bytes = disk_total_space(DOCUMENT_ROOT); 
		
		$si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
		$base = 1024;
		$class = min((int)log($bytes , $base) , count($si_prefix) - 1);
		
		$ArregloDatos["espaciototal"]=$bytes / pow($base,$class) . ' ' . $si_prefix[$class];
		
		$bytes = disk_free_space(DOCUMENT_ROOT); 
		$class = min((int)log($bytes , $base) , count($si_prefix) - 1);
		
		
		$ArregloDatos["espacioutilizado"]=$bytes / pow($base,$class) . ' ' . $si_prefix[$class];
		$ArregloDatos["porcentaje"]=100-(intval($ArregloDatos["espacioutilizado"])*100)/intval($ArregloDatos["espaciototal"]);
		//$ArregloDatos["porcentaje"]=10;
		$ArregloDatos["msje"]="#0F0";
		if($ArregloDatos["porcentaje"]>60 && $ArregloDatos["porcentaje"]<80){
			$ArregloDatos["msje"]="#FF3";	
		}
		if($ArregloDatos["porcentaje"]>80 && $ArregloDatos["porcentaje"]<=100){
			$ArregloDatos["msje"]="#F00";	
		}
				
				
				
		return true;
	}



}
?>