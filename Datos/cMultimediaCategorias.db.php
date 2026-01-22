<?php  
abstract class cMultimediaCategoriasdb
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

//----------------------------------------------------------------------------------------- 
// Retorna el SP y los parametros para cargar los roles del sistema

// Parámetros de Entrada:

// Retorna:
//		spnombre,spparam
//		la función retorna true o false si se pudo ejecutar con éxito o no
	/*protected function CategoriasSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_categorias";
		$sparam=array(
			);
		return true;
	}*/
	

	protected function BuscarAvanzadaxCategoriaSuperior($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_categorias_busqueda_xcatsupnull";
		$sparam=array(
			'pxcatsuperior'=> $datos['xcatsuperior'],
			'pxcatsuperior1'=> $datos['xcatsuperior1'],
			'pcatsuperior1'=> $datos['catsuperior1'],
			'porderby'=> $datos['orderby'],
			'plimit'=> $datos['limit']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener la categoria por categoria superior. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}


	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_categorias_multimedia_xcatcod";
		$sparam=array(
			'pcatcod'=> $datos['catcod']
			);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener la categoria. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	
	}


	protected function BuscarCategoriasMultimediaxCatcod($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_categorias_multimedia";
		$sparam=array(
			'pcatcod'=> $datos['catcod']
			);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener la categoria del multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	
	}
	
	protected function BuscaCategoriasxEstado($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_categorias_xcatestado";
		$sparam=array(
			'pcatestado'=> $datos["catestado"]
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar categorias por código de estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	protected function BuscarCategoriasxCategoriaSuperior($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_categorias_multimedia_xcatsuperior";
		$sparam=array(
			'pcatsuperior'=> $datos['catsuperior']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener la categoria por categoria superior. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	

	
	protected function BuscaCategoriasRaiz(&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_categorias_multimedia_xcatsupnull";
		$sparam=array(
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener la categoria por categoria superior. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	
	protected function BuscaCategoriasNombreRaiz($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_categorias_multimedia_xcatnom_xcatsupnull";
		$sparam=array(
			'pcatnom'=> $datos['catnom']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la categoria por nombre. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	protected function BuscaCategoriasNombrexCategoriaSuperior($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_categorias_multimedia_xcatnom_xsuperior";
		$sparam=array(
			'pcatnom'=> $datos['catnom'],
			'pcatsuperior'=> $datos['catsuperior']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la categoria por nombre. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	
	protected function Insertar($datos,&$codigoinsertado)
	{

	$spnombre="ins_mul_multimedia_categorias_multimedia";
	$sparam=array(
		'pcatnom'=> $datos['catnom'],
		'pcatdominio'=> $datos['catdominio'],
		'pcatdesc'=> $datos['catdesc'],
		'pcatsuperior'=> $datos['catsuperior'],
		'pcatorden'=> $datos['catorden'],
		'pcatestado'=> $datos['catestado'],
		'pultmodusuario'=> $_SESSION['usuariocod'],
		'pultmodfecha'=> date("Y/m/d H:i:s")
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno) || $numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar la categoria. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}
	
	
	
	protected function Modificar($datos)
	{

		$spnombre="upd_mul_multimedia_categorias_multimedia_xcatcod";
		$sparam=array(
			'pcatnom'=> $datos['catnom'],
			'pcatdominio'=> $datos['catdominio'],
			'pcatdesc'=> $datos['catdesc'],
			'pcatsuperior'=> $datos['catsuperior'],
			'pcatorden'=> $datos['catorden'],
			'pcatestado'=> $datos['catestado'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pcatcod'=> $datos['catcod']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar la categoria. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function ModificarEstadoCategoria($datos)
	{
		$spnombre="upd_mul_multimedia_categorias_multimedia_xcatestado";
		$sparam=array(
			'pcatestado'=> $datos['catestado'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pcatcod'=> $datos['catcod']
			);		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado de la categoria. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;	
	}


	
	protected function Eliminar($datos)
	{

		$spnombre="del_mul_multimedia_categorias_multimedia_xcatcod";
		$sparam=array(
			'pcatcod'=> $datos['catcod']
			);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar la categoria. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function BuscarMultimediaUltimoOrden($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_categorias_multimedia_maxorden_xcatsuperior";
		$sparam=array(
			'pcatsuperior'=> $datos['catsuperior']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el proximo orden.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;	
	}
	
	protected function ModificarOrden($datos)
	{
		$spnombre="upd_mul_multimedia_categorias_multimedia_orden_xcatcod";
		$sparam=array(
			'pcatorden'=> $datos['catorden'],
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pcatcod'=> $datos['catcod']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden de los multimedias. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}
		
		return true;	
	}

	protected function BuscarMultimediaCategoriaxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_categorias_xmultimediacatcod";
		$sparam=array(
			'pmultimediacatcod'=> $datos['multimediacatcod']
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la categoria del multimedia por codigo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}



	protected function BuscarMultimediaCategorias(&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_categorias";
		$sparam=array(
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la categoria del multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}




}
?>