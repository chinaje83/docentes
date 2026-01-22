<?php 
abstract class cUsuariosVerificaciondb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosVerificacion_xIdVerificacion";
		$sparam=array(
			'pIdVerificacion'=> $datos['IdVerificacion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarxCodigoClaveEscuelaCuilIdRol($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosVerificacion_xCuil_ClaveEscuela_IdRol";
		$sparam=array(
			'pCuil'=> $datos['Cuil'],
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pIdRol'=> $datos['IdRol']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por cuil, clave escuela y puesto. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosVerificacion_busqueda_avanzada";
		$sparam=array(
			'pxIdVerificacion'=> $datos['xIdVerificacion'],
			'pIdVerificacion'=> $datos['IdVerificacion'],
			'pxCuil'=> $datos['xCuil'],
			'pCuil'=> $datos['Cuil'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxApellido'=> $datos['xApellido'],
			'pApellido'=> $datos['Apellido'],
			'pxClaveEscuela'=> $datos['xClaveEscuela'],
			'pClaveEscuela'=> $datos['ClaveEscuela'],
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



	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_UsuariosVerificacion";
		$sparam=array(
			'pCuil'=> $datos['Cuil'],
			'pNombre'=> $datos['Nombre'],
			'pApellido'=> $datos['Apellido'],
			'pEmail'=> $datos['Email'],
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pNombreEscuela'=> $datos['NombreEscuela'],
			'pDistrito'=> $datos['Distrito'],
			'pTipoOrganizacion'=> $datos['TipoOrganizacion'],
			'pMetapuestos'=> $datos['Metapuestos'],
			'pIdRol'=> $datos['IdRol'],
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			echo "aaaa";die;
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}



	protected function Modificar($datos)
	{
		$spnombre="upd_UsuariosVerificacion_xIdVerificacion";
		$sparam=array(
			'pCuil'=> $datos['Cuil'],
			'pNombre'=> $datos['Nombre'],
			'pApellido'=> $datos['Apellido'],
			'pEmail'=> $datos['Email'],
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pNombreEscuela'=> $datos['NombreEscuela'],
			'pDistrito'=> $datos['Distrito'],
			'pTipoOrganizacion'=> $datos['TipoOrganizacion'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdVerificacion'=> $datos['IdVerificacion']
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
		$spnombre="del_UsuariosVerificacion_xIdVerificacion";
		$sparam=array(
			'pIdVerificacion'=> $datos['IdVerificacion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function ModificarEstado($datos)
	{
		$spnombre="upd_UsuariosVerificacion_Estado_xIdVerificacion";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdVerificacion'=> $datos['IdVerificacion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>