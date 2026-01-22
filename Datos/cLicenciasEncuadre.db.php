<?php 
abstract class cLicenciasEncuadredb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_LicenciasEncuadre_xIdLicenciaEncuadre";
		$sparam=array(
			'pIdLicenciaEncuadre'=> $datos['IdLicenciaEncuadre']
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
		$spnombre="sel_LicenciasEncuadre_busqueda_avanzada";
		$sparam=array(
			'pxIdLicenciaEncuadre'=> $datos['xIdLicenciaEncuadre'],
			'pIdLicenciaEncuadre'=> $datos['IdLicenciaEncuadre'],
			'pxCodigoEncuadre'=> $datos['xCodigoEncuadre'],
			'pCodigoEncuadre'=> $datos['CodigoEncuadre'],
			'pxCodigoRevista'=> $datos['xCodigoRevista'],
			'pCodigoRevista'=> $datos['CodigoRevista'],
			'pxSexo'=> $datos['xSexo'],
			'pSexo'=> $datos['Sexo'],
			'pxDescripcionEncuadre'=> $datos['xDescripcionEncuadre'],
			'pDescripcionEncuadre'=> $datos['DescripcionEncuadre'],
			'pxCodigoEncuadreHost'=> $datos['xCodigoEncuadreHost'],
			'pCodigoEncuadreHost'=> $datos['CodigoEncuadreHost'],
			
			'pxAntiguedadDesde'=> $datos['xpxAntiguedadDesde'],
			'pAntiguedadDesde'=> $datos['AntiguedadDesde'],
			
			'pxAntiguedadHasta'=> $datos['xAntiguedadHasta'],
			'pAntiguedadHasta'=> $datos['AntiguedadHasta'],
			
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
		$spnombre="sel_LicenciasEncuadre_AuditoriaRapida";
		$sparam=array(
			'pIdLicenciaEncuadre'=> $datos['IdLicenciaEncuadre']
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
		$spnombre="ins_LicenciasEncuadre";
		$sparam=array(
			'pCodigoEncuadre'=> $datos['CodigoEncuadre'],
			'pCodigoRevista'=> $datos['CodigoRevista'],
			'pSexo'=> $datos['Sexo'],
			'pAntiguedadDesde'=> $datos['AntiguedadDesde'],
			'pAntiguedadHasta'=> $datos['AntiguedadHasta'],
			'pDescripcionEncuadre'=> $datos['DescripcionEncuadre'],
			'pParticularidades'=> $datos['Particularidades'],
			'pCodigoEncuadreHost'=> $datos['CodigoEncuadreHost'],
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
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
		$spnombre="upd_LicenciasEncuadre_xIdLicenciaEncuadre";
		$sparam=array(
			'pCodigoEncuadre'=> $datos['CodigoEncuadre'],
			'pCodigoRevista'=> $datos['CodigoRevista'],
			'pSexo'=> $datos['Sexo'],
			'pAntiguedadDesde'=> $datos['AntiguedadDesde'],
			'pAntiguedadHasta'=> $datos['AntiguedadHasta'],
			'pDescripcionEncuadre'=> $datos['DescripcionEncuadre'],
			'pParticularidades'=> $datos['Particularidades'],
			'pCodigoEncuadreHost'=> $datos['CodigoEncuadreHost'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdLicenciaEncuadre'=> $datos['IdLicenciaEncuadre']
		);
		//print_r($sparam);die;
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Eliminar($datos)
	{
		$spnombre="del_LicenciasEncuadre_xIdLicenciaEncuadre";
		$sparam=array(
			'pIdLicenciaEncuadre'=> $datos['IdLicenciaEncuadre']
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
		$spnombre="upd_LicenciasEncuadre_Estado_xIdLicenciaEncuadre";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pIdLicenciaEncuadre'=> $datos['IdLicenciaEncuadre']
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