<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con el acceso a base de datos para el manejo de las constantes generales
abstract class cConstantesGralesdb
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


	function BuscarDatosOrdenados ($ArregloDatos,&$numfilas,&$resultado)
	{
		
		$spnombre="sel_constantes_grales_orden";
		$sparam=array(
			'porderby'=> "Codigo ASC"
			);
		if (isset ($ArregloDatos['orderby']))
		{
			if ($ArregloDatos['orderby']!="")
				$sparam['porderby']= $ArregloDatos['orderby'];
		}	

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar Constante Generales.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


//----------------------------------------------------------------------------------------- 
// Retorna una consulta con todos los usuarios que cumplan con las condiciones

// Parámetros de Entrada:
//		ArregloDatos: array asociativo con los filtros. Claves: usuarionombre, usuarioapellido, usuariocuit, usuarioemail

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no



	function Buscar ($ArregloDatos,&$numfilas,&$resultado)
	{
		
		$spnombre="sel_constantes_grales";
		$sparam=array(
			'pestadocod'=> 0,
			'pCodigo'=> "",
			'pestadotipo'=> 0,
			'pTipo'=> "",
			'pestadonom'=> 0,
			'pNombre'=> "",
			'pestadosis'=> 0,
			'pNombreSistema'=> "",
			'pestadodesc'=> 0,
			'pDescripcion'=> "",
			'porderby'=> "Codigo ASC"
			);

		if (isset ($ArregloDatos['Codigo']))
		{
			if ($ArregloDatos['Codigo']!="")
			{	
				$sparam['pCodigo']= $ArregloDatos['Codigo'];
				$sparam['pestadocod']= 1;
			}
		}
		
		if (isset ($ArregloDatos['Tipo']))
		{
			if ($ArregloDatos['Tipo']!="")
			{	
				$sparam['pTipo']= $ArregloDatos['Tipo'];
				$sparam['pestadotipo']= 1;
			}
		}	
		if (isset ($ArregloDatos['Nombre']))
		{
			if ($ArregloDatos['Nombre']!="")
			{	
				$sparam['pNombre']= $ArregloDatos['Nombre'];
				$sparam['pestadonom']= 1;
			}
		}	
		
		if (isset ($ArregloDatos['NombreSistema']))
		{
			if ($ArregloDatos['NombreSistema']!="")
			{	
				$sparam['pNombreSistema']= $ArregloDatos['NombreSistema'];
				$sparam['pestadosis']= 1;
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

		if (isset ($ArregloDatos['orderby']))
		{
			if ($ArregloDatos['orderby']!="")
				$sparam['porderby']= $ArregloDatos['orderby'];
		}	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar Constante Generales.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	function Insertar ($ArregloDatos, &$codigoinsertado)
	{
		
		$spnombre="ins_constantes_grales";
		$sparam=array(
			'pNombreSistema'=> $ArregloDatos['NombreSistema'],
			'pTipo'=> $ArregloDatos['Tipo'],
			'pCodigo'=> $ArregloDatos['Codigo'],
			'pNombre'=> $ArregloDatos['Nombre'],
			'pDescripcion'=> $ArregloDatos['Descripcion'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s")
			);
	
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar Constante Generales.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();
		return true;
	}
	


	function Modificar ($ArregloDatos)
	{
		$spnombre="upd_constantes_grales_xconstantetipo_constantecod";
		$sparam=array(
			'pNombreSistema'=> $ArregloDatos['NombreSistema'],
			'pNombre'=> $ArregloDatos['Nombre'],
			'pDescripcion'=> $ArregloDatos['Descripcion'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
			'pTipoMod'=> $ArregloDatos['TipoMod'],
			'pCodigoMod'=> $ArregloDatos['CodigoMod'],
			'pTipo'=> $ArregloDatos['Tipo'],
			'pCodigo'=> $ArregloDatos['Codigo']
			);	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar Constante Generales.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	function Eliminar ($ArregloDatos)
	{
		$spnombre="del_constantes_grales_xconstantetipo_constantecod";
		$sparam=array(
			'pTipo'=> $ArregloDatos['Tipo'] ,
			'pCodigo'=> $ArregloDatos['Codigo'] 
			);
		
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al Eliminar Constante Generales.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

		
		
}//FIN CLASE

?>