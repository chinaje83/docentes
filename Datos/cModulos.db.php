<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con la lógica para el manejo de modulos
abstract class cModulosdb
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
		$sparam=array('pestadocod' =>0);
		$sparam+=array('pestadodesc' =>0);
		$sparam+=array('pestadotext' =>0);
		$sparam+=array('pestadoarchcod' =>0);
		$sparam+=array('pestadosec' =>0);
		$sparam+=array('pestadomostrar' =>0);
		$sparam+=array('pestadocategoria' =>0);
		
	
		$sparam+=array('pIdModulo' =>"");
		$sparam+=array('pDescripcion' =>"");
		$sparam+=array('pTextoMenu' =>"");
		$sparam+=array('pIdArchivo' =>"");
		$sparam+=array('pSecuencia' =>"");
		$sparam+=array('pMostrar' =>"");
		$sparam+=array('pIdGrupoCategoria' =>"");

		$sparam+=array('porderby' =>"IdModulo");

		if (isset ($ArregloDatos['IdModulo']))
		{
			if ($ArregloDatos['IdModulo']!="")
			{	
				$sparam['pIdModulo']= $ArregloDatos['IdModulo'];
				$sparam['pestadocod']= 1;
			}
		}
		
		if (isset ($ArregloDatos['Descripcion']))
		{
			if ($ArregloDatos['Descripcion']!="")
			{	
				$sparam['pDescripcion']= $ArregloDatos['Descripcion'];
				$sparam['pestadodesc']= 1;
			}
		}	
		
		if (isset ($ArregloDatos['TextoMenu']))
		{
			if ($ArregloDatos['TextoMenu']!="")
			{	
				$sparam['pTextoMenu']= $ArregloDatos['TextoMenu'];
				$sparam['pestadotext']= 1;
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
		
		if (isset ($ArregloDatos['Secuencia']))
		{
			if ($ArregloDatos['Secuencia']!="")
			{	
				$sparam['pSecuencia']= $ArregloDatos['Secuencia'];
				$sparam['pestadosec']= 1;
			}
		}	
		
		if (isset ($ArregloDatos['Mostrar']))
		{
			if ($ArregloDatos['Mostrar']!="")
			{	
				$sparam['pMostrar']= $ArregloDatos['Mostrar'];
				$sparam['pestadomostrar']= 1;
			}
		}	
		
		
		if (isset ($ArregloDatos['IdGrupoCategoria']))
		{
			if ($ArregloDatos['IdGrupoCategoria']!="")
			{	
				$sparam['pIdGrupoCategoria']= $ArregloDatos['IdGrupoCategoria'];
				$sparam['pestadocategoria']= 1;
			}
		}	
		
		
		if (isset ($ArregloDatos['orderby']))
		{
			if ($ArregloDatos['orderby']!="")
				$sparam['porderby']= $ArregloDatos['orderby'];
		}	
		
		$spnombre="sel_modulos";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el Módulo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Insertar ($ArregloDatos, &$codigoinsertado)
	{
		
		$sparam =array("pIdModulo"=>$ArregloDatos['IdModulo']);
		$sparam+=array("pDescripcion"=>$ArregloDatos['Descripcion']);
		$sparam+=array("pTextoMenu"=>$ArregloDatos['TextoMenu']);
		$sparam+=array("pIdArchivo"=>$ArregloDatos['IdArchivo']);
		$sparam+=array("pSecuencia"=>$ArregloDatos['Secuencia']);
		$sparam+=array("pMostrar"=>$ArregloDatos['Mostrar']);
		$sparam+=array("pUbicacionImagen"=>$ArregloDatos['UbicacionImagen']);
		$sparam+=array("pDashboard"=>$ArregloDatos['Dashboard']);
		$sparam+=array("pEsDefault"=>$ArregloDatos['EsDefault']);
		$sparam+=array("pIdGrupoCategoria"=>$ArregloDatos['IdGrupoCategoria']);
		$sparam+=array("pUrl"=>$ArregloDatos['Url']);
        $sparam+=array("pMuestraMenuSuperior"=>$ArregloDatos['MuestraMenuSuperior']);
        $sparam+=array("pTextoMenuSuperior"=>$ArregloDatos['TextoMenuSuperior']);
		$sparam+=array("pUltimaModificacionUsuario"=>$_SESSION['usuariocod']);
		$sparam+=array("pUltimaModificacionFecha"=>date("Y/m/d H:i:s"));
		

		
		$spnombre="ins_modulos";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el Módulo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();
		return true;
	}



	protected function Modificar ($ArregloDatos)
	{
		
		$sparam =array("pIdModulonuevo"=>$ArregloDatos['IdModulonuevo']);
		$sparam+=array("pIdModuloviejo"=>$ArregloDatos['IdModulo']);
		$sparam+=array("pDescripcion"=>$ArregloDatos['Descripcion']);
		$sparam+=array("pTextoMenu"=>$ArregloDatos['TextoMenu']);
		$sparam+=array("pIdArchivo"=>$ArregloDatos['IdArchivo']);
		$sparam+=array("pSecuencia"=>$ArregloDatos['Secuencia']);
		$sparam+=array("pMostrar"=>$ArregloDatos['Mostrar']);
		$sparam+=array("pUbicacionImagen"=>$ArregloDatos['UbicacionImagen']);
		$sparam+=array("pDashboard"=>$ArregloDatos['Dashboard']);		
		$sparam+=array("pEsDefault"=>$ArregloDatos['EsDefault']);
		$sparam+=array("pIdGrupoCategoria"=>$ArregloDatos['IdGrupoCategoria']);
        $sparam+=array("pUrl"=>$ArregloDatos['Url']);
        $sparam+=array("pMuestraMenuSuperior"=>$ArregloDatos['MuestraMenuSuperior']);
        $sparam+=array("pTextoMenuSuperior"=>$ArregloDatos['TextoMenuSuperior']);
		$sparam+=array("pUltimaModificacionUsuario"=>$_SESSION['usuariocod']);
		$sparam+=array("pUltimaModificacionFecha"=>date("Y/m/d H:i:s"));

		$spnombre="upd_modulos_xIdModulo";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el Módulo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



	protected function Eliminar ($ArregloDatos)
	{
		
		$sparam =array("pIdModulo"=>$ArregloDatos['IdModulo']);
		$spnombre="del_modulos_xIdModulo";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al Eliminar el Módulo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}
	
	
	protected function ModificarIdGrupoCategoria($datos)
	{
		$spnombre="upd_Modulos_IdGrupoCategoria_xIdModulo";
		$sparam=array(
			'pIdGrupoCategoria'=> $datos['IdGrupoCategoria'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdModulo'=> $datos['IdModulo']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al actualizar la categoria.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


}

?>