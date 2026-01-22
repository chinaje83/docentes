<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con la l�gica para el manejo de roles
// Clase con el acceso a base de datos para el manejo de modulos archivos
abstract class cRolesModulosdb
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

// Par�metros de Entrada:
//		ArregloDatos: array asociativo con los filtros. Claves: usuarionombre, usuarioapellido, usuariocuit, usuarioemail

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	protected function Buscar ($ArregloDatos,&$numfilas,&$resultado)
	{
		$sparam=array('pestadomodcod' =>0);
		$sparam+=array('pestadorol' =>0);
	
		$sparam+=array('pIdModulo' =>"");
		$sparam+=array('pIdRol' =>"");

		$sparam+=array('porderby' =>"IdRol");

		if (isset ($ArregloDatos['IdModulo']))
		{
			if ($ArregloDatos['IdModulo']!="")
			{	
				$sparam['pIdModulo']= $ArregloDatos['IdModulo'];
				$sparam['pestadomodcod']= 1;
			}
		}
		
		if (isset ($ArregloDatos['IdRol']))
		{
			if ($ArregloDatos['IdRol']!="")
			{	
				$sparam['pIdRol']= $ArregloDatos['IdRol'];
				$sparam['pestadorol']= 1;
			}
		}	
		
		if (isset ($ArregloDatos['orderby']))
		{
			if ($ArregloDatos['orderby']!="")
				$sparam['porderby']= $ArregloDatos['orderby'];
		}	
		
		$spnombre="sel_roles_modulos";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el Rol-M�dulo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	
	protected function BuscarModulosHabilitados($datos,&$numfilas,&$resultado)
	{
		$spnombre="sel_modulos_xrolcod_habilitados";
		$sparam=array(
			'pxIdRol'=> $datos['xIdRol'],
			'pIdRol'=> $datos['IdRol']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al busqueda de modulos habilitados. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	

	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_roles_modulos_busqueda_avanzada";
		$sparam=array(
			'pxIdModulo'=> $datos['xIdModulo'],
			'pIdModulo'=> $datos['IdModulo'],
			'pxIdRol'=> $datos['xIdRol'],
			'pIdRol'=> $datos['IdRol'],
			'pxEsDefault'=> $datos['xEsDefault'],
			'pEsDefault'=> $datos['EsDefault'],
			'porderby'=> $datos['orderby'],
			'plimit'=> $datos['limit']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la b�squeda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	

	protected function Insertar ($ArregloDatos)
	{
		
		$sparam =array("pIdModulo"=>$ArregloDatos['IdModulo']);
		$sparam+=array("pIdRol"=>$ArregloDatos['IdRol']);
		$sparam+=array("pUltimaModificacionUsuario"=>$_SESSION['usuariocod']);
		$sparam+=array("pUltimaModificacionFecha"=>date("Y/m/d H:i:s"));
		
		$spnombre="ins_roles_modulos";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el Rol-M�dulo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function Eliminar ($ArregloDatos)
	{
		$sparam =array("pIdModulo"=>$ArregloDatos['IdModulo']);
		$sparam +=array("pIdRol"=>$ArregloDatos['IdRol']);
		$spnombre="del_roles_modulos_xrolcod_xmodulocod";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al Eliminar el M�dulo-Archivo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}
	
	
	protected function EliminarxRol ($datos)
	{
		$spnombre="del_roles_modulos_xrolcod";
		$sparam=array(
			'pIdRol'=> $datos['IdRol']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al Eliminar por Rol.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function InsertarModulosDefault($datos)
	{
		$spnombre="ins_roles_modulos_default";
		$sparam=array(
			'pIdRol'=> $datos['IdRol'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s")
			);


		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar los modulos default.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

}

?>