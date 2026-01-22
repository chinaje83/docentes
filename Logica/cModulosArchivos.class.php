<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con la lógica para el manejo de roles

include(DIR_CLASES_DB."cModulosArchivos.db.php");

class cModulosArchivos extends cModulosArchivosdb	
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
// Retorna una consulta con todos los usuarios que cumplan con las condiciones

// Parámetros de Entrada:
//		ArregloDatos: array asociativo con los filtros. Claves: usuarionombre, usuarioapellido, usuariocuit, usuarioemail

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function Buscar ($ArregloDatos,&$numfilas,&$resultado)
	{
		if (!parent::Buscar($ArregloDatos,$numfilas,$resultado))
			return false;

		return true;
	}



	public function Insertar ($ArregloDatos)
	{
		
		if (!$this->Buscar ($ArregloDatos,$numfilas,$resultado))
			return false;
		
		if ($numfilas==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, El Módulo-Archivo ya se encuentra insertado en la tabla de Módulos.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!parent::Insertar($ArregloDatos))
			return false;
		return true;
	}


	public function Eliminar ($ArregloDatos)
	{
		if (!isset ($ArregloDatos['IdModulo']) || ($ArregloDatos['IdModulo']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Módulo Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!isset ($ArregloDatos['IdArchivo']) || ($ArregloDatos['IdArchivo']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Archivo Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->Buscar ($ArregloDatos,$numfilas,$resultado))
			return false;
		
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Módulo-Archivo Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!parent::Eliminar($ArregloDatos))
			return false;
		return true;
	}


}

?>