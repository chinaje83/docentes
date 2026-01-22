<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con la lógica para el manejo de grupo de modulos

include(DIR_CLASES_DB."cGruposModulos.db.php");

class cGruposModulos extends cGruposModulosdb	
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



	public function Insertar ($ArregloDatos, &$codigoinsertado)
	{
		if (!$this->Buscar ($ArregloDatos,$numfilas,$resultado))
			return false;
		
		if ($numfilas==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, El Grupo de Modulo ya se encuentra insertado en la tabla de Grupos Módulos.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!parent::Insertar($ArregloDatos,$codigoinsertado))
			return false;
			
		$datosMenu = array();
		$oMenu = new cMenuAdministrador($this->conexion,$this->formato);
		if(!$oMenu->PublicarMenu($datosMenu))
			return false;			

		return true;
	}

	public function Modificar ($ArregloDatos)
	{
		if (!parent::Modificar($ArregloDatos))
			return false;
			
		$datosMenu = array();
		$oMenu = new cMenuAdministrador($this->conexion,$this->formato);
		if(!$oMenu->PublicarMenu($datosMenu))
			return false;			

		return true;
	}

	public function Eliminar ($ArregloDatos)
	{
		if (!isset ($ArregloDatos['IdGrupoMod']) || ($ArregloDatos['IdGrupoMod']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Grupo Módulo Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!$this->Buscar ($ArregloDatos,$numfilas,$resultado))
			return false;
		
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Grupo Módulo Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!parent::Eliminar($ArregloDatos))
			return false;
			
		$datosMenu = array();
		$oMenu = new cMenuAdministrador($this->conexion,$this->formato);
		if(!$oMenu->PublicarMenu($datosMenu))
			return false;
						
		return true;
	}


}

?>