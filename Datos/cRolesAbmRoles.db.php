<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con el acceso a base de datos para el manejo de los abm de roles
abstract class cRolesAbmRolesdb
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
// Retorna una consulta con todos los usuarios que cumplan con las condiciones

// Parámetros de Entrada:
//		ArregloDatos: array asociativo con los filtros. Claves: usuarionombre, usuarioapellido, usuariocuit, usuarioemail

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	protected function Buscar ($ArregloDatos,&$numfilas,&$resultado)
	{
		$sparam=array('pestadoactualizado' =>0);
		$sparam+=array('pestadoactualiza' =>0);
	
		$sparam+=array('pIdRolActualizado' =>"");
		$sparam+=array('pIdRolActualiza' =>"");

		$sparam+=array('porderby' =>"IdRolActualizado");

		if (isset ($ArregloDatos['IdRolActualizado']))
		{
			if ($ArregloDatos['IdRolActualizado']!="")
			{	
				$sparam['pIdRolActualizado']= $ArregloDatos['IdRolActualizado'];
				$sparam['pestadoactualizado']= 1;
			}
		}
		
		if (isset ($ArregloDatos['IdRolActualiza']))
		{
			if ($ArregloDatos['IdRolActualiza']!="")
			{	
				$sparam['pIdRolActualiza']= $ArregloDatos['IdRolActualiza'];
				$sparam['pestadoactualiza']= 1;
			}
		}	
		
		if (isset ($ArregloDatos['orderby']))
		{
			if ($ArregloDatos['orderby']!="")
				$sparam['porderby']= $ArregloDatos['orderby'];
		}	
		
		$spnombre="sel_roles_abm_roles";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el Roles-ABM-Roles.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		

		return true;
	}



	protected function Insertar ($ArregloDatos)
	{
		
		$sparam =array("pIdRolActualiza"=>$ArregloDatos['IdRolActualiza']);
		$sparam+=array("pIdRolActualizado"=>$ArregloDatos['IdRolActualizado']);
		$sparam+=array("pUltimaModificacionUsuario"=>$_SESSION['usuariocod']);
		$sparam+=array("pUltimaModificacionFecha"=>date("Y/m/d H:i:s"));
		$spnombre="ins_rolesabmroles";
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el Roles-ABM-Roles.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function Eliminar ($ArregloDatos)
	{
		$sparam =array("pIdRolActualiza"=>$ArregloDatos['IdRolActualiza']);
		$sparam +=array("pIdRolActualizado"=>$ArregloDatos['IdRolActualizado']);
		$spnombre="del_roles_abm_roles_xIdRolActualiza_IdRolActualizado";	
		//print_r($sparam);die;
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar el Roles-ABM-Roles.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function EliminarxIdRol ($ArregloDatos)
	{
		$sparam =array("pIdRol"=>$ArregloDatos['IdRol']);
		$spnombre="del_roles_abm_roles_xIdRol";	
		//print_r($sparam);die;
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar el Roles-ABM-Roles.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


}

?>