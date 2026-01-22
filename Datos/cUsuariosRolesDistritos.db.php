<?php
abstract class cUsuariosRolesDistritosdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosRolesDistritos_xIdUsuarioRolDistrito";
		$sparam=array(
			'pIdUsuarioRolDistrito'=> $datos['IdUsuarioRolDistrito']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarxIdUsuarioxIdRolxIdClientexIdAreaxTieneDistrito($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosRolesDistritos_xIdUsuario_IdRol_IdCliente_IdArea_TieneDistrito";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdRol'=> $datos['IdRol'],
			'pIdCliente'=> $datos['IdCliente'],
			'pIdArea'=> $datos['IdArea'],
			'pTieneDistrito'=> $datos['TieneDistrito']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarxIdUsuarioxIdRol($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosRolesDistritos_xIdUsuario_IdRol";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdRol'=> $datos['IdRol']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarxIdUsuario($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosRolesDistritos_xIdUsuario";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


    protected function BuscarUsuarioLocal($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_UsuariosLocal_xIdUsuario";
        $sparam=array(
            'pIdUsuario' => $datos['IdUsuario']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    protected function InsertarUsuarioLocal($datos)
    {
        $spnombre="ins_UsuariosLocal";
        $sparam=array(
            'pIdUsuario'=> $datos['IdUsuario'],
            'pNombre' => $datos['Nombre'],
            'pApellido' => $datos['Apellido'],
            'pCuil' => $datos['Cuil'],
            'pDni' => $datos['Dni'],
            'pAltaFecha' => $datos['AltaFecha']
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
        $spnombre="sel_UsuariosRolesDistritos_busqueda_avanzada";
        $sparam=array(
            'pxIdUsuarioRolDistrito'=> $datos['xIdUsuarioRolDistrito'],
            'pIdUsuarioRolDistrito'=> $datos['IdUsuarioRolDistrito'],
            'pxIdUsuario'=> $datos['xIdUsuario'],
            'pIdUsuario'=> $datos['IdUsuario'],
            'pxIdRol'=> $datos['xIdRol'],
            'pIdRol'=> $datos['IdRol'],
            'pxIdRegion'=> $datos['xIdRegion'],
            'pIdRegion'=> $datos['IdRegion'],
            'pxIdDistrito'=> $datos['xIdDistrito'],
            'pIdDistrito'=> $datos['IdDistrito'],
            'pxIdEscuela'=> $datos['xIdEscuela'],
            'pIdEscuela'=> $datos['IdEscuela'],
            'pxIdNivel'=> $datos['xIdNivel'],
            'pIdNivel'=> $datos['IdNivel'],
            'pxIdTurno'=> $datos['xIdTurno'],
            'pIdTurno'=> $datos['IdTurno'],
            'porderby'=> $datos['orderby'],
            'plimit'=> $datos['limit']
        );

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


    protected function BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelNullxIdTurnoNull($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_UsuariosRolesDistritos_xIdRol_IdRegion_IdEscuela_IdNivel_Null_IdTurno_Null";
        $sparam=array(
            'pxIdUsuario'=> $datos['xIdUsuario'],
            'pIdUsuario'=> $datos['IdUsuario'],
            'pxIdRol'=> $datos['xIdRol'],
            'pIdRol'=> $datos['IdRol'],
            'pxIdRegion'=> $datos['xIdRegion'],
            'pIdRegion'=> $datos['IdRegion'],
            'pxIdDistrito'=> $datos['xIdDistrito'],
            'pIdDistrito'=> $datos['IdDistrito'],
            'pxIdEscuela'=> $datos['xIdEscuela'],
            'pIdEscuela'=> $datos['IdEscuela'],
            'porderby'=> $datos['orderby'],
            'plimit'=> $datos['limit']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


    protected function BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelxIdTurno($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_UsuariosRolesDistritos_xIdRol_IdRegion_IdEscuela_IdNivel_IdTurno";
        $sparam=array(
            'pxIdUsuario'=> $datos['xIdUsuario'],
            'pIdUsuario'=> $datos['IdUsuario'],
            'pxIdRol'=> $datos['xIdRol'],
            'pIdRol'=> $datos['IdRol'],
            'pxIdRegion'=> $datos['xIdRegion'],
            'pIdRegion'=> $datos['IdRegion'],
            'pxIdDistrito'=> $datos['xIdDistrito'],
            'pIdDistrito'=> $datos['IdDistrito'],
            'pxIdEscuela'=> $datos['xIdEscuela'],
            'pIdEscuela'=> $datos['IdEscuela'],
            'pxIdNivel'=> $datos['xIdNivel'],
            'pIdNivel'=> $datos['IdNivel'],
            'pxIdNivelNull'=> $datos['xIdNivelNull'],
            'pxIdTurno'=> $datos['xIdTurno'],
            'pIdTurno'=> $datos['IdTurno'],
            'pxIdTurnoNull'=> $datos['xIdTurnoNull'],
            'porderby'=> $datos['orderby'],
            'plimit'=> $datos['limit']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    protected function BuscarxIdUsuarioxIdRolxIdEscuelaNull($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_UsuariosRolesDistritos_xIdUsuario_IdRol_IdEscuela_Null";
        $sparam=array(
            'pIdUsuario'=> $datos['IdUsuario'],
            'pIdRol'=> $datos['IdRol']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    protected function BuscarxIdUsuarioxIdRolxIdNivelxIdEscuela($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_UsuariosRolesDistritos_xIdUsuario_IdRol_IdNivel_IdEscuela";
        $sparam=array(
            'pIdUsuario'=> $datos['IdUsuario'],
            'pIdRol'=> $datos['IdRol'],
            'pIdNivel'=> $datos['IdNivel'],
            'pIdEscuela'=> $datos['IdEscuela']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    protected function BuscarxIdUsuarioxIdRolxIdNivel($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_UsuariosRolesDistritos_xIdUsuario_IdRol_IdNivel";
        $sparam=array(
            'pIdUsuario'=> $datos['IdUsuario'],
            'pIdRol'=> $datos['IdRol'],
            'pIdNivel'=> $datos['IdNivel']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }
    protected function Insertar($datos,&$codigoinsertado)
    {
        $spnombre="ins_UsuariosRolesDistritos";
        $sparam=array(
            'pIdUsuario'=> $datos['IdUsuario'],
            'pIdRol'=> $datos['IdRol'],
            'pIdRegion'=> $datos['IdRegion'],
            'pIdDistrito'=> $datos['IdDistrito'],
            'pIdEscuela'=> $datos['IdEscuela'],
            'pIdNivel'=> $datos['IdNivel'],
            'pIdTurno'=> $datos['IdTurno'],
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
		$spnombre="upd_UsuariosRolesDistritos_xIdUsuario";
		$sparam=array(
			'pIdRol'=> $datos['IdRol'],
			'pIdCliente'=> $datos['IdCliente'],
			'pIdArea'=> $datos['IdArea'],
			'pIdDistritoExterno'=> $datos['IdDistritoExterno'],
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdUsuario'=> $datos['IdUsuario']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function EliminarxIdUsuarioxIdRolxIdClientexIdAreaxIdDistritoExternoxClaveEscuela($datos)
	{
		$spnombre="del_UsuariosRolesDistritos_xIdUsuario_IdRol_IdCliente_IdArea_IdDistritoExterno_ClaveEscuela";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdRol'=> $datos['IdRol'],
			'pIdCliente'=> $datos['IdCliente'],
			'pIdArea'=> $datos['IdArea'],
			'pIdDistritoExterno'=> $datos['IdDistritoExterno'],
			'pClaveEscuela'=> strtoupper($datos['ClaveEscuela'])
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function Eliminar($datos)
	{
		$spnombre="del_UsuariosRolesDistritos_xIdUsuarioRolDistrito";
		$sparam=array(
			'pIdUsuarioRolDistrito'=> $datos['IdUsuarioRolDistrito']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function EliminarxIdUsuarioxIdClientexIdAreaxIdRol($datos)
	{
		$spnombre="del_UsuariosRolesDistritos_xIdUsuario_IdRol_IdCliente_IdArea";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario'],
			'pIdRol'=> $datos['IdRol'],
			'pIdCliente'=> $datos['IdCliente'],
			'pIdArea'=> $datos['IdArea']

		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    protected function EliminarxIdUsuarioxIdRol($datos)
    {
        $spnombre="del_UsuariosRolesDistritos_xIdUsuario_IdRol";
        $sparam=array(
            'pIdUsuario'=> $datos['IdUsuario'],
            'pIdRol'=> $datos['IdRol']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar todos los datos. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }




}
?>
