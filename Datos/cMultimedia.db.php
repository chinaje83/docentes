<?php  
abstract class cMultimediadb
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

	protected function BuscarMultimediaxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_xmultimediacod";
		$sparam=array(
			'pmultimediacod'=> $datos['multimediacod']
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}



	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{

		$spnombre="sel_mul_multimedia_busqueda_avanzada";
		$sparam=array(
			'pestadomulcatcod'=> $datos['estadomulcatcod'],
			'pcatcod'=> $datos['catcod'],		
			'pestadomultimediadesc'=> $datos['estadomultimediadesc'],
			'pmultimediadesc'=> $datos['multimediadesc'],
			'pmultimedianombre'=> $datos['multimedianombre'],
			'pestadomultimedianombre'=> $datos['estadomultimedianombre'],
			'pmultimediatipoarchivo'=> $datos['multimediatipoarchivo'],
			'pestadomultimediatipoarchivo'=> $datos['estadomultimediatipoarchivo'],			
			'pestadomultimediaestadocod'=> $datos['estadomultimediaestadocod'],
			'pmultimediaestadocod'=> $datos['multimediaestadocod'],
			'pestadomultimediacatcod'=> $datos['estadomultimediacatcod'],
			'pmultimediacatcod'=> $datos['multimediacatcod'],
			'pestadomultimediaconjuntocod'=> $datos['estadomultimediaconjuntocod'],
			'pmultimediaconjuntocod'=> $datos['multimediaconjuntocod'],
			'pxmultimediaidexterno'=> $datos['xmultimediaidexterno'],
			'pmultimediaidexterno'=> $datos['multimediaidexterno'],
			'pmultimediatitulo'=> $datos['multimediatitulo'],
			'pxmultimediatitulo'=> $datos['xmultimediatitulo'],
			'porderby'=> $datos['orderby'],
			'plimit'=> $datos['limit']
			);
	

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los archivos multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
protected function BusquedaPopup($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_busqueda_popup";
		$sparam=array(
			'pcriteriobusqueda'=> $datos['criteriobusqueda'],
			'pestadomultimediaconjuntocod'=> $datos['estadomultimediaconjuntocod'],
			'pmultimediaconjuntocod'=> $datos['multimediaconjuntocod'],
			'pmultimediaestadocod'=> $datos['multimediaestadocod'],
			'plimit'=> $datos['limit'],
			'porderby'=> $datos['orderby']
			);
	
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los archivos multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	protected function ModificarEstadoMultimedia($datos)
	{
		
		$spnombre="upd_mul_multimedia_modif_estado_xmultimediacod";
		$sparam=array(
			'pmultimediaestadocod'=> $datos['multimediaestadocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pmultimediacod'=> $datos['multimediacod']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado del multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;	
	}

	protected function BuscarMultimediasRelacionados($datos,&$resultado,&$numfilas)
	{

		$spnombre="sel_mul_multimedia_cantidad_relacionadas";
		$sparam=array(
			'pmultimediacod'=> $datos['multimediacod']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la cantidad de multimedia relacionado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





	protected function Insertar($datos,&$codigoinsertado)
	{


		$spnombre="ins_mul_multimedia";
		$sparam=array(
			'pcatcod'=> $datos['catcod'],
			'pmultimediacatcod'=> $datos['multimediacatcod'],
			'pmultimediatitulo'=> $datos['multimediatitulo'],
			'pmultimediadesc'=> $datos['multimediadesc'],
			'pmultimedianombre'=> $datos['multimedianombre'],
			'pmultimediaubic'=> $datos['multimediaubic'],
			'pmultimediaidexterno'=> $datos['multimediaidexterno'],
			'pmultimediatipocod'=> $datos['multimediatipocod'],
			'pmultimediaestadocod'=> $datos['multimediaestadocod'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s")
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) && $numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el archivo multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}
	
	
	protected function Eliminar($datos)
	{

		$spnombre="del_mul_multimedia_xmultimediacod";
		$sparam=array(
			'pmultimediacod'=> $datos['multimediacod']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar el archivo multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	


	protected function ModificarDescripcion($datos)
	{

		$spnombre="upd_mul_multimedia_descripcion_xmultimediacod";
		$sparam=array(
			'pmultimediadesc'=> $datos['multimediadesc'],
			'pcatcod'=> $datos['catcod'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pmultimediacod'=> $datos['multimediacod']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar la descripcion del archivo multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	

	protected function ModificarTitulo($datos)
	{

		$spnombre="upd_mul_multimedia_titulo_xmultimediacod";
		$sparam=array(
			'pmultimediatitulo'=> $datos['multimediatitulo'],
			'pcatcod'=> $datos['catcod'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pmultimediacod'=> $datos['multimediacod']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar la descripcion del archivo multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
		


	protected function ModificarPreview($datos)
	{

		$spnombre="upd_mul_multimedia_preview_xmultimediacod";
		$sparam=array(
			'pmultimediapreview'=> $datos['multimediapreview'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pmultimediacod'=> $datos['multimediacod']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el preview del archivo multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
		




}
?>