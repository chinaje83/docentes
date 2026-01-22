<?php 
abstract class cReglasCargaNovedadesdb
{


	function __construct(){}

	function __destruct(){}

	protected function TiposOrganizacionSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_TiposOrganizacion_combo_Descripcion";
		$sparam=array(
		);
		return true;
	}



	protected function CargosSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_Cargos_combo_Descripcion";
		$sparam=array(
		);
		return true;
	}



	protected function NivelEnsenanzasSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_NivelEnsenanzas_combo_Descripcion";
		$sparam=array(
		);
		return true;
	}



	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasCargaNovedades_xTipoOrganizacion";
		$sparam=array(
			'pIdReglasCargaNovedades'=> $datos['IdReglasCargaNovedades']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	
	protected function BuscarxTipoOrganizacionxAnio($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasCargaNovedades_xTipoOrganizacion_Anio";
		$sparam=array(
			'pTipoOrganizacion'=> $datos['TipoOrganizacion'],
			'pAnio'=> $datos['Anio']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxTipoOrganizacionxAnioxCargo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasCargaNovedades_xTipoOrganizacion_Anio_Cargo";
		$sparam=array(
			'pTipoOrganizacion'=> $datos['TipoOrganizacion'],
			'pAnio'=> $datos['Anio'],
			'pCargo'=> $datos['Cargo']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxTipoOrganizacionxAnioxCargoxNivelEnsenanza($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasCargaNovedades_xTipoOrganizacion_Anio_Cargo_NivelEnsenanza";
		$sparam=array(
			'pTipoOrganizacion'=> $datos['TipoOrganizacion'],
			'pAnio'=> $datos['Anio'],
			'pCargo'=> $datos['Cargo'],
			'pNivelEnsenanza'=> $datos['NivelEnsenanza'],
			'pxNivelEnsenanza'=> $datos['xNivelEnsenanza'],
			'pxNivelEnsenanzaNull'=> $datos['xNivelEnsenanzaNull']
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
		$spnombre="sel_ReglasCargaNovedades_busqueda_avanzada";
		$sparam=array(
			'pxIdReglasCargaNovedades'=> $datos['xIdReglasCargaNovedades'],
			'pIdReglasCargaNovedades'=> $datos['IdReglasCargaNovedades'],
			'pxTipoOrganizacion'=> $datos['xTipoOrganizacion'],
			'pTipoOrganizacion'=> $datos['TipoOrganizacion'],
			'pxCargo'=> $datos['xCargo'],
			'pCargo'=> $datos['Cargo'],
			'pxAnio'=> $datos['xAnio'],
			'pAnio'=> $datos['Anio'],
			'pxNivelEnsenanza'=> $datos['xNivelEnsenanza'],
			'pNivelEnsenanza'=> $datos['NivelEnsenanza'],
			'pxNivelEnsenanzaNull'=> $datos['xNivelEnsenanzaNull'],
			'pxModalidadCarrera'=> $datos['xModalidadCarrera'],
			'pModalidadCarrera'=> $datos['ModalidadCarrera'],
			'pxAsignatura'=> $datos['xAsignatura'],
			'pAsignatura'=> $datos['Asignatura'],
			'pxArea'=> $datos['xArea'],
			'pArea'=> $datos['Area'],
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado'],
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



	protected function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasCargaNovedades_AuditoriaRapida";
		$sparam=array(
			'pIdReglasCargaNovedades'=> $datos['IdReglasCargaNovedades']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_ReglasCargaNovedades";
		$sparam=array(
			'pTipoOrganizacion'=> $datos['TipoOrganizacion'],
			'pCargo'=> $datos['Cargo'],
			'pAnio'=> $datos['Anio'],
			'pNivelEnsenanza'=> $datos['NivelEnsenanza'],
			'pModalidadCarrera'=> $datos['ModalidadCarrera'],
			'pAsignatura'=> $datos['Asignatura'],
			'pArea'=> $datos['Area'],
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
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
		$spnombre="upd_ReglasCargaNovedades_xTipoOrganizacion";
		$sparam=array(
			'pTipoOrganizacion'=> $datos['TipoOrganizacion'],
			'pCargo'=> $datos['Cargo'],
			'pAnio'=> $datos['Anio'],
			'pNivelEnsenanza'=> $datos['NivelEnsenanza'],
			'pModalidadCarrera'=> $datos['ModalidadCarrera'],
			'pAsignatura'=> $datos['Asignatura'],
			'pArea'=> $datos['Area'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdReglasCargaNovedades'=> $datos['IdReglasCargaNovedades']
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
		$spnombre="del_ReglasCargaNovedades_xTipoOrganizacion";
		$sparam=array(
			'pIdReglasCargaNovedades'=> $datos['IdReglasCargaNovedades']
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
		$spnombre="upd_ReglasCargaNovedades_Estado_xTipoOrganizacion";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdReglasCargaNovedades'=> $datos['IdReglasCargaNovedades']
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