<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con la l�gica para el manejo de roles modulos
include(DIR_CLASES_DB."cRolesModulos.db.php");

class cRolesModulos extends cRolesModulosdb	
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

// Par�metros de Entrada:
//		ArregloDatos: array asociativo con los filtros. Claves: usuarionombre, usuarioapellido, usuariocuit, usuarioemail

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	function BuscarDatos ($ArregloDatos,&$numfilas,&$resultado)
	{
		if (!parent::Buscar($ArregloDatos,$numfilas,$resultado))
			return false;

		return true;
	}
	
	function BuscarModulosHabilitados ($datos,&$numfilas,&$resultado)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos['IdRol']=$roles;
		$datos['xIdRol']=1;
		$oRoles = new cRoles($this->conexion,$this->formato);

		if ($oRoles->RolAdministrador($_SESSION['rolcod']))
			$datos['xIdRol']=0;
        //print_r($datos);
		if (!parent::BuscarModulosHabilitados($datos,$numfilas,$resultado))
			return false;

		return true;
	}
	
	
	
	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdModulo'=> 0,
			'IdModulo'=> "",
			'xIdRol'=> 0,
			'IdRol'=> "",
			'xEsDefault'=> 0,
			'EsDefault'=> "",
			'limit'=> '',
			'orderby'=> "c.Descripcion ASC"
		);

		if (isset ($datos['IdRol']) && $datos['IdRol']!="")
		{
			$sparam['IdRol']= $datos['IdRol'];
			$sparam['xIdRol']= 1;
		}

		if (isset ($datos['IdModulo']) && $datos['IdModulo']!="")
		{
			$sparam['IdModulo']= $datos['IdModulo'];
			$sparam['xIdModulo'] = 1;
		}

		if (isset ($datos['EsDefault']) && $datos['EsDefault']!=="")
		{
			$sparam['EsDefault']= $datos['EsDefault'];
			$sparam['xEsDefault'] = 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}

	
	



	public function Insertar ($ArregloDatos)
	{
		
		if (!$this->BuscarDatos ($ArregloDatos,$numfilas,$resultado))
			return false;
		
		if ($numfilas==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, El Rol-M�dulo ya se encuentra insertado en la tabla de roles_modulos.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!parent::Insertar($ArregloDatos))
			return false;
		
		
		$datosMenu['IdRol'] = $ArregloDatos['IdRol'];
		$oMenu = new cMenuAdministrador($this->conexion,$this->formato);
		if(!$oMenu->PublicarMenu($datosMenu))
			return false;
		
		return true;
	}


	public function InsertarModulosDefault ($ArregloDatos)
	{
		if (!parent::InsertarModulosDefault($ArregloDatos))
			return false;
		
		
		$datosMenu['IdRol'] = $ArregloDatos['IdRol'];
		$oMenu = new cMenuAdministrador($this->conexion,$this->formato);
		if(!$oMenu->PublicarMenu($datosMenu))
			return false;
		
		return true;
	}



	public function Eliminar ($ArregloDatos)
	{
		if (!isset ($ArregloDatos['IdModulo']) || ($ArregloDatos['IdModulo']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error C�digo de M�dulo Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!isset ($ArregloDatos['IdRol']) || ($ArregloDatos['IdRol']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error C�digo de Rol Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->BuscarDatos ($ArregloDatos,$numfilas,$resultado))
			return false;
		
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error C�digo de Rol-M�dulo Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!parent::Eliminar($ArregloDatos))
			return false;
		
		
		$datosMenu['IdRol'] = $ArregloDatos['IdRol'];
		$oMenu = new cMenuAdministrador($this->conexion,$this->formato);
		if(!$oMenu->PublicarMenu($datosMenu))
			return false;
		
		return true;
	}
	
	public function EliminarxRol ($datos)
	{
		
		if (!isset ($datos['IdRol']) || ($datos['IdRol']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error C�digo de Rol Inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		$oRolesModulosAcciones = new cRolesModulosAcciones($this->conexion,$this->formato);
		if(!$oRolesModulosAcciones->EliminarxRol($datos))
			return false;


		if (!parent::EliminarxRol($datos))
			return false;
			
			
			
		return true;
	}


}

?>