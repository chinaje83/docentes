<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con la lógica para el manejo de roles
include(DIR_CLASES_DB."cRolesAbmRoles.db.php");

class cRolesAbmRoles extends cRolesAbmRolesdb	
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, El Rol-ABM-Rol ya se encuentra insertado en la tabla de Roles-ABM-Roles.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!parent::Insertar($ArregloDatos))
			return false;
		return true;
	}


	public function Eliminar ($ArregloDatos)
	{
		if (!isset ($ArregloDatos['IdRolActualiza']) || ($ArregloDatos['IdRolActualiza']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Rol que actualiza Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!isset ($ArregloDatos['IdRolActualizado']) || ($ArregloDatos['IdRolActualizado']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Rol Actualizado Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->Buscar ($ArregloDatos,$numfilas,$resultado))
			return false;
		
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Rol-ABM-Rol Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!parent::Eliminar($ArregloDatos))
			return false;
		return true;
	}


	public function EliminarxIdRol ($ArregloDatos)
	{
		if (!isset ($ArregloDatos['IdRol']) || ($ArregloDatos['IdRol']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error Código de Rol Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		
		if (!parent::EliminarxIdRol($ArregloDatos))
			return false;
		return true;
	}
	
	
	


}

?>