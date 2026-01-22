<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con el acceso a base de datos para el manejo de los abm de stored procedures
abstract class cStoreddb
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
		$sparam+=array('pestadonombre' =>0);
		$sparam+=array('pestadoopert' =>0);
		$sparam+=array('pestadotabla' =>0);
		$sparam+=array('pestadosql' =>0);
		$sparam+=array('pestadoobs' =>0);
		
	
		$sparam+=array('pspcod' =>"");
		$sparam+=array('pspnombre' =>"");
		$sparam+=array('pspoperacion' =>"");
		$sparam+=array('psptabla' =>"");
		$sparam+=array('pspsqlstring' =>"");
		$sparam+=array('pspobserv' =>"");

		$sparam+=array('porderby' =>"spcod");

		if (isset ($ArregloDatos['spcod']))
		{
			if ($ArregloDatos['spcod']!="")
			{	
				$sparam['pspcod']= $ArregloDatos['spcod'];
				$sparam['pestadocod']= 1;
			}
		}
		
		if (isset ($ArregloDatos['spnombre']))
		{
			if ($ArregloDatos['spnombre']!="")
			{	
				$sparam['pspnombre']= $ArregloDatos['spnombre'];
				$sparam['pestadonombre']= 1;
			}
		}	
		
		if (isset ($ArregloDatos['spoperacion']))
		{
			if ($ArregloDatos['spoperacion']!="")
			{	
				$sparam['pspoperacion']= $ArregloDatos['spoperacion'];
				$sparam['pestadoopert']= 1;
			}
		}	
		
		if (isset ($ArregloDatos['sptabla']))
		{
			if ($ArregloDatos['sptabla']!="")
			{	
				$sparam['psptabla']= $ArregloDatos['sptabla'];
				$sparam['pestadotabla']= 1;
			}
		}	
		
		if (isset ($ArregloDatos['spsqlstring']))
		{
			if ($ArregloDatos['spsqlstring']!="")
			{	
				$sparam['pspsqlstring']= $ArregloDatos['spsqlstring'];
				$sparam['pestadosql']= 1;
			}
		}	
		
		if (isset ($ArregloDatos['spobserv']))
		{
			if ($ArregloDatos['spobserv']!="")
			{	
				$sparam['pspobserv']= $ArregloDatos['spobserv'];
				$sparam['pestadoobs']= 1;
			}
		}	
		
		
		if (isset ($ArregloDatos['orderby']))
		{
			if ($ArregloDatos['orderby']!="")
				$sparam['porderby']= $ArregloDatos['orderby'];
		}	
		
		$spnombre="sel_stored";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el Store Procedure.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Insertar ($ArregloDatos, &$codigoinsertado)
	{
		$sparam =array("pspnombre"=>$ArregloDatos['spnombre']);
		$sparam+=array("pspoperacion"=>$ArregloDatos['spoperacion']);
		$sparam+=array("psptabla"=>$ArregloDatos['sptabla']);
		$sparam+=array("pspsqlstring"=>$ArregloDatos['spsqlstring']);
		$sparam+=array("pspobserv"=>$ArregloDatos['spobserv']);
		$sparam+=array("pultmodusuario"=>$_SESSION['usuariocod']);
		$sparam+=array("pultmodfecha"=>date("Y/m/d H:i:s"));
		$spnombre="ins_stored";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el Store Procedure.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();
		return true;
	}


	protected function Modificar ($ArregloDatos)
	{
		
		$sparam =array("pspnombre"=>$ArregloDatos['spnombre']);
		$sparam+=array("pspoperacion"=>$ArregloDatos['spoperacion']);
		$sparam+=array("psptabla"=>$ArregloDatos['sptabla']);
		$sparam+=array("pspsqlstring"=>$ArregloDatos['spsqlstring']);
		$sparam+=array("pspobserv"=>$ArregloDatos['spobserv']);
		$sparam+=array("pultmodusuario"=>$_SESSION['usuariocod']);
		$sparam+=array("pultmodfecha"=>date("Y/m/d H:i:s"));
		
		$sparam+=array("pspcod"=>$ArregloDatos['spcod']);
	
	
		$spnombre="upd_stored";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el Store Procedure.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	function Eliminar ($ArregloDatos)
	{
		
		$sparam =array("pspcod"=>$ArregloDatos['spcod']);
		$spnombre="del_stored";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al Eliminar el Store Procedure.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}
		
		
	protected function TraerTablas($bd,&$resultado) 
	{
		$sparam =array("pbd"=>$bd);
		$spnombre="show_tables";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al Buscar Todas las Tablas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}	
	
	
	protected function TraerCampos($tabla,&$resultado) 
	{
		$sparam =array("tabla"=>$tabla);
		$spnombre="buscar_campos";	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$error="Error al Buscar Todos los campos de la Tabla '".$tabla."'";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$error,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}	
		
		
}//FIN CLASE

?>