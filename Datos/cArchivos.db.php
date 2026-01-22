<?php  
abstract class cArchivosdb
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
		$sparam=array('pestadonom' =>0);
		$sparam+=array('pestadocod' =>0);
		
		$sparam+=array('pNombre' =>"");
		$sparam+=array('pIdArchivo' =>"");
		
		$sparam+=array('porderby' =>"Nombre");

		if (isset ($ArregloDatos['Nombre']))
		{
			if ($ArregloDatos['Nombre']!="")
			{	
				$sparam['pNombre']= $ArregloDatos['Nombre'];
				$sparam['pestadonom']= 1;
			}
		}
		if (isset ($ArregloDatos['IdArchivo']))
		{
			if ($ArregloDatos['IdArchivo']!="")
			{	
				$sparam['pIdArchivo']= $ArregloDatos['IdArchivo'];
				$sparam['pestadocod']= 1;
			}
		}	
		
		if (isset ($ArregloDatos['orderby']))
		{
			if ($ArregloDatos['orderby']!="")
				$sparam['porderby']= $ArregloDatos['orderby'];
		}	

		$spnombre="sel_archivos";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el Archivo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Insertar ($ArregloDatos, &$codigoarchivo)
	{
		$sparam =array("pNombre"=>$ArregloDatos['Nombre']);
		$sparam+=array("pUltimaModificacionUsuario"=>$_SESSION['usuariocod']);
		$sparam+=array("pUltimaModificacionFecha"=>date("Y/m/d H:i:s"));
		$spnombre="ins_archivos";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el Archivo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$codigoarchivo=$this->conexion->UltimoCodigoInsertado();
		
		
		return true;
	}




	protected function Modificar ($ArregloDatos)
	{
		$sparam =array("pIdArchivo"=>$ArregloDatos['IdArchivo']);
		$sparam+=array("pNombre"=>$ArregloDatos['Nombre']);
		$sparam+=array("pUltimaModificacionUsuario"=>$_SESSION['usuariocod']);
		$sparam+=array("pUltimaModificacionFecha"=>date("Y/m/d H:i:s"));
		$spnombre="upd_archivos_xIdArchivo";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el Archivo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}
	
	

	protected function Eliminar ($ArregloDatos)
	{
		$sparam =array("pIdArchivo"=>$ArregloDatos['IdArchivo']);
		$spnombre="del_archivos_xIdArchivo";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al Eliminar el Archivo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


}


?>