<?php 
abstract class cLicenciasEncuadreAdministrativasdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_LicenciasEncuadreAdministrativas_xIdEncuadre";
		$sparam=array(
			'pIdEncuadre'=> $datos['IdEncuadre']
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
		$spnombre="sel_LicenciasEncuadreAdministrativas_busqueda_avanzada";
		$sparam=array(
			'pxIdEncuadre'=> $datos['xIdEncuadre'],
			'pIdEncuadre'=> $datos['IdEncuadre'],

			'pxCategoria'=> $datos['xCategoria'],
			'pCategoria'=> $datos['Categoria'],

			'pxTipo'=> $datos['xTipo'],
			'pTipo'=> $datos['Tipo'],

			'pxTope'=> $datos['xTope'],
			'pTope'=> $datos['Tope'],

			'pxEncuadre'=> $datos['xEncuadre'],
			'pEncuadre'=> $datos['Encuadre'],
			
			'pxRegimenCodigo'=> $datos['xRegimenCodigo'],
			'pRegimenCodigo'=> $datos['RegimenCodigo'],

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
		$spnombre="sel_LicenciasEncuadreAdministrativas_AuditoriaRapida";
		$sparam=array(
			'pIdEncuadre'=> $datos['IdEncuadre']
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
		$spnombre="ins_LicenciasEncuadreAdministrativas";
		$sparam=array(
			'pCategoria'=> $datos['Categoria'],
			'pTipo'=> $datos['Tipo'],
			'pTipoDetalle'=> $datos['TipoDetalle'],
			'pGrupo'=> $datos['Grupo'],
			'pLeyendaEstatuto'=> $datos['LeyendaEstatuto'],
			'pLeyendaDecreto'=> $datos['LeyendaDecreto'],
			'pTope'=> $datos['Tope'],
			'pEncuadre'=> $datos['Encuadre'],
			'pRegimenCodigo'=> $datos['RegimenCodigo'],
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
		$spnombre="upd_LicenciasEncuadreAdministrativas_xIdEncuadre";
			$sparam=array(
			'pCategoria'=> $datos['Categoria'],
			'pTipo'=> $datos['Tipo'],
			'pTipoDetalle'=> $datos['TipoDetalle'],
			'pGrupo'=> $datos['Grupo'],
			'pLeyendaEstatuto'=> $datos['LeyendaEstatuto'],
			'pLeyendaDecreto'=> $datos['LeyendaDecreto'],
			'pTope'=> $datos['Tope'],
			'pEncuadre'=> $datos['Encuadre'],
			'pRegimenCodigo'=> $datos['RegimenCodigo'],
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdEncuadre'=> $datos['IdEncuadre']
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
		$spnombre="del_LicenciasEncuadreAdministrativas_xIdEncuadre";
		$sparam=array(
			'pIdEncuadre'=> $datos['IdEncuadre']
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
		$spnombre="upd_LicenciasEncuadreAdministrativas_Estado_xIdEncuadre";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pIdEncuadre'=> $datos['IdEncuadre']
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