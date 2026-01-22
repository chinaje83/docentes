<?php  
include(DIR_CLASES_DB."cFilesConfig.db.php");

class cFilesConfig extends cFilesConfigdb	
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
// Busqueda de un archivo config por codigo

// Parámetros de Entrada:
//		datos: arreglo de datos
//			filecod = codigo del archivo de configuracion

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo ($datos,$resultado,$numfilas))
			return false;
		return true;	
	}
	
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Busqueda avanzada de archivos config

// Parámetros de Entrada:
//		datos: arreglo de datos

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		if (!parent::BusquedaAvanzada ($datos,$resultado,$numfilas))
			return false;
		return true;	
	}
	
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 

// Modificar un  archivo

// Parámetros de Entrada:
//		datos: arreglo de datos
//			filecod: codigo del archivo config
//			filedetalle = Detalle del archivo

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosFile))
			return false;

		if (!parent::Modificar ($datos))
			return false;
		
		if (!$this->RegenerarFileConfig($datosFile,$datos['filedetalle']))
			return false;
			
		return true;	
	}	
	
	
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 

// Modificar un  archivo

// Parámetros de Entrada:
//		datos: arreglo de datos
//			fileubic: codigo del archivo config
//			filedetalle = Detalle del archivo

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function RegenerarFileConfig($datosFile,$filedetalle)
	{
		if (file_exists($datosFile['fileubic']))
		{
			if(!file_put_contents($datosFile['fileubic'], $filedetalle))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error al regenerar el archivo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		return true;	
	}	
	
	 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 

// Function que valida los datos al momento de modificar un archivo

// Parámetros de Entrada:
//		datos: array asociativo con los datos a validar
//			filecod: codigo del archivo config
//			filedetalle = Detalle del archivo

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no
//		la función retorna los datos del archivo en un array asociativo 
//			$datosFile = array asociativo con los datos del archivo.
	
	private function _ValidarModificar($datos,&$datosFile)
	{
		
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		if(!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Archivo inexistente. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosFile = $this->conexion->ObtenerSiguienteRegistro($resultado);
	
		return true;
	}

//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 

// Function que valida los datos al momento de modificar un archivo

// Parámetros de Entrada:
//		datos: array asociativo con los datos a validar
//			filedetalle = Detalle del archivo

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no	

	private function _ValidarDatosVacios($datos)
	{

		return true;
	}


}
?>