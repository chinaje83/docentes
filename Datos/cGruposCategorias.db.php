<?php 
abstract class cGruposCategoriasdb
{


	function __construct(){}

	function __destruct(){}

	protected function grupos_categoriasSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_GruposCategorias_combo_Nombre";
		$sparam=array(
		);
		return true;
	}



	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_GruposCategorias_xIdGrupoCategoria";
		$sparam=array(
			'pIdGrupoCategoria'=> $datos['IdGrupoCategoria']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_GruposCategorias_busqueda_avanzada";
		$sparam=array(
			'pxIdGrupoCategoria'=> $datos['xIdGrupoCategoria'],
			'pIdGrupoCategoria'=> $datos['IdGrupoCategoria'],
			'pxIdGrupoCategoriaSuperior'=> $datos['xIdGrupoCategoriaSuperior'],
			'pIdGrupoCategoriaSuperior'=> $datos['IdGrupoCategoriaSuperior'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'plimit'=> $datos['limit'],
			'porderby'=> $datos['orderby']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarGruposCategoriasxGrupoCategoriaSuperior($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_GruposCategorias_xIdGrupoCategoriaSuperior";
		$sparam=array(
			'pIdGrupoCategoriaSuperior'=> $datos['IdGrupoCategoriaSuperior']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener el grupo categoría por grupo categoría superior. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	protected function BuscarGrupoCategoriaArbolxRol($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_GruposCategorias_xRol";
		$sparam=array(
			'pxIdRol'=> $datos['xIdRol'],
			'pIdRol'=> $datos['IdRol']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener el grupo categoría por grupo categoría superior. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;	
	}
	
	
	protected function BuscarAvanzadaxGrupoCategoriaSuperior($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_GruposCategorias_busqueda_xIdGrupoCategoriaSuperiorNull";
		$sparam=array(
			'pxIdGrupoCategoriaSuperior'=> $datos['xIdGrupoCategoriaSuperior'],
			'pxIdGrupoCategoriaSuperior1'=> $datos['xIdGrupoCategoriaSuperior1'],
			'pIdGrupoCategoriaSuperior1'=> $datos['IdGrupoCategoriaSuperior1'],
			'porderby'=> $datos['orderby'],
			'plimit'=> $datos['limit']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener el grupo categoría por grupo categoría superior. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	
	protected function BuscaGrupoCategoriaRaiz($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_GruposCategorias_xIdGrupoCategoriaSuperiorNull";
		$sparam=array(
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al obtener el grupo categoría por grupo categoría superior. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}



	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_GruposCategorias";
		$sparam=array(
			'pIdGrupoCategoriaSuperior'=> $datos['IdGrupoCategoriaSuperior'],
			'pNombre'=> $datos['Nombre'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}



	protected function Modificar($datos)
	{
		$spnombre="upd_GruposCategorias_xIdGrupoCategoria";
		$sparam=array(
			'pIdGrupoCategoriaSuperior'=> $datos['IdGrupoCategoriaSuperior'],
			'pNombre'=> $datos['Nombre'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdGrupoCategoria'=> $datos['IdGrupoCategoria']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Eliminar($datos)
	{
		$spnombre="del_GruposCategorias_xIdGrupoCategoria";
		$sparam=array(
			'pIdGrupoCategoria'=> $datos['IdGrupoCategoria']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>