<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con el acceso a base de datos para el manejo de modulos archivos
abstract class cModulosArchivosdb
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
		$sparam=array('pestadomodcod' =>0);
		$sparam+=array('pestadoarchcod' =>0);
	
		$sparam+=array('pIdModulo' =>"");
		$sparam+=array('pIdArchivo' =>"");

		$sparam+=array('porderby' =>"IdModulo");

		if (isset ($ArregloDatos['IdModulo']))
		{
			if ($ArregloDatos['IdModulo']!="")
			{	
				$sparam['pIdModulo']= $ArregloDatos['IdModulo'];
				$sparam['pestadomodcod']= 1;
			}
		}
		
		if (isset ($ArregloDatos['IdArchivo']))
		{
			if ($ArregloDatos['IdArchivo']!="")
			{	
				$sparam['pIdArchivo']= $ArregloDatos['IdArchivo'];
				$sparam['pestadoarchcod']= 1;
			}
		}	
		
		if (isset ($ArregloDatos['orderby']))
		{
			if ($ArregloDatos['orderby']!="")
				$sparam['porderby']= $ArregloDatos['orderby'];
		}	
		
		$spnombre="sel_modulosarchivos";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el Archivo-Módulo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Insertar ($ArregloDatos)
	{
		
		$sparam =array("pIdModulo"=>$ArregloDatos['IdModulo']);
		$sparam+=array("pIdArchivo"=>$ArregloDatos['IdArchivo']);
		$sparam+=array("pUltimaModificacionUsuario"=>$_SESSION['usuariocod']);
		$sparam+=array("pUltimaModificacionFecha"=>date("Y/m/d H:i:s"));
		
		$spnombre="ins_modulosarchivos";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el Archivo-Módulo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	protected function Eliminar ($ArregloDatos)
	{
		$sparam =array("pIdModulo"=>$ArregloDatos['IdModulo']);
		$sparam +=array("pIdArchivo"=>$ArregloDatos['IdArchivo']);
		$spnombre="del_modulosarchivos_xgrupocod_xIdArchivo";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar el Archivo-Módulo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


}

?>