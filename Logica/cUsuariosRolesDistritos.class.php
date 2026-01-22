<?php
include(DIR_CLASES_DB."cUsuariosRolesDistritos.db.php");

class cUsuariosRolesDistritos extends cUsuariosRolesDistritosdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}


	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarxIdUsuarioxIdRolxIdClientexIdAreaxTieneDistrito($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdUsuarioxIdRolxIdClientexIdAreaxTieneDistrito($datos,$resultado,$numfilas))
			return false;
		return true;
	}

    public function BuscarxIdUsuarioxIdRol($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxIdUsuarioxIdRol($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdUsuario($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxIdUsuario($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarUsuarioLocal($datos, &$resultado, &$numfilas)
    {
        if (!parent::BuscarUsuarioLocal($datos,$resultado,$numfilas))
            return false;

        return true;
    }

    public function InsertarUsuarioLocal($datos)
    {
        $this->_SetearNullLocal($datos);
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        if (!parent::InsertarUsuarioLocal($datos))
            return false;

        return true;
    }

    public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{

        $sparam=array(
            'xIdUsuarioRolDistrito'=> 0,
            'IdUsuarioRolDistrito'=> "",
            'xIdUsuario'=> 0,
            'IdUsuario'=> "",
            'xIdRol'=> 0,
            'IdRol'=> "",
            'xIdRegion'=> 0,
            'IdRegion'=> "",
            'xIdDistrito'=> 0,
            'IdDistrito'=> "",
            'xIdEscuela'=> 0,
            'IdEscuela'=> "",
            'xIdNivel'=> 0,
            'IdNivel'=> "",
            'xIdTurno'=> 0,
            'IdTurno'=> "",
            'limit'=> "",
            'orderby'=> "IdUsuarioRolDistrito DESC"
        );

        if(isset($datos['IdUsuarioRolDistrito']) && $datos['IdUsuarioRolDistrito']!="")
        {
            $sparam['IdUsuarioRolDistrito']= $datos['IdUsuarioRolDistrito'];
            $sparam['xIdUsuarioRolDistrito']= 1;
        }

		if(isset($datos['IdUsuario']) && $datos['IdUsuario']!="")
		{
			$sparam['IdUsuario']= $datos['IdUsuario'];
			$sparam['xIdUsuario']= 1;
		}
		if(isset($datos['IdRol']) && $datos['IdRol']!="")
		{
			$sparam['IdRol']= $datos['IdRol'];
			$sparam['xIdRol']= 1;
		}
		if(isset($datos['IdRegion']) && $datos['IdRegion']!="")
		{
			$sparam['IdRegion']= $datos['IdRegion'];
			$sparam['xIdRegion']= 1;
		}
		if(isset($datos['IdDistrito']) && $datos['IdDistrito']!="")
		{
			$sparam['IdDistrito']= $datos['IdDistrito'];
			$sparam['xIdDistrito']= 1;
		}
		if(isset($datos['IdEscuela']) && $datos['IdEscuela']!="")
		{
			$sparam['IdEscuela']= $datos['IdEscuela'];
			$sparam['xIdEscuela']= 1;
		}

        if(isset($datos['IdNivel']) && $datos['IdNivel']!="")
        {
            $sparam['IdNivel']= $datos['IdNivel'];
            $sparam['xIdNivel']= 1;
        }

        if(isset($datos['IdTurno']) && $datos['IdTurno']!="")
        {
            $sparam['IdTurno']= $datos['IdTurno'];
            $sparam['xIdTurno']= 1;
        }

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}

    public function BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelNullxIdTurnoNull($datos,&$resultado,&$numfilas)
    {
        $sparam=array(
            'xIdUsuario'=> 1,
            'IdUsuario'=> $datos['IdUsuario'],
            'xIdRol'=> 1,
            'IdRol'=> $datos['IdRol'],
            'xIdRegion'=> 0,
            'IdRegion'=> "",
            'xIdDistrito'=> 0,
            'IdDistrito'=> "",
            'xIdEscuela'=> 0,
            'IdEscuela'=> "",
            'limit'=> "",
            'orderby'=> "IdUsuarioRolDistrito DESC"
        );

        if(isset($datos['TieneDistrito']) && $datos['TieneDistrito']=="1")
        {
            $sparam['IdRegion']= $datos['IdRegion'];
            $sparam['xIdRegion']= 1;
        }

        if(isset($datos['TieneDistrito']) && $datos['TieneDistrito']=="2")
        {
            $sparam['IdRegion']= $datos['IdRegion'];
            $sparam['xIdRegion']= 1;
            $sparam['IdEscuela']= $datos['IdEscuela'];
            $sparam['xIdEscuela']= 1;
        }

        if(isset($datos['orderby']) && $datos['orderby']!="")
            $sparam['orderby']= $datos['orderby'];

        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];

        if (!parent::BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelNullxIdTurnoNull($sparam,$resultado,$numfilas))
            return false;

        return true;
    }


    public function BuscarxIdUsuarioxIdRolxIdRegionxIdEscuela($datos,&$resultado,&$numfilas)
    {

        $sparam=array(
            'xIdUsuarioRolDistrito'=> 0,
            'IdUsuarioRolDistrito'=> "",
            'xIdUsuario'=> 1,
            'IdUsuario'=> $datos['IdUsuario'],
            'xIdRol'=> 1,
            'IdRol'=> $datos['IdRol'],
            'xIdRegion'=> 0,
            'IdRegion'=> "",
            'xIdDistrito'=> 0,
            'IdDistrito'=> "",
            'xIdEscuela'=> 0,
            'IdEscuela'=> "",
            'xIdNivel'=> 0,
            'IdNivel'=> "",
            'xIdTurno'=> 0,
            'IdTurno'=> "",
            'limit'=> "",
            'orderby'=> "IdUsuarioRolDistrito DESC"
        );

        if(isset($datos['TieneDistrito']) && $datos['TieneDistrito']=="1")
        {
            $sparam['IdRegion']= $datos['IdRegion'];
            $sparam['xIdRegion']= 1;
        }

        if(isset($datos['TieneDistrito']) && $datos['TieneDistrito']=="2")
        {
            $sparam['IdRegion']= $datos['IdRegion'];
            $sparam['xIdRegion']= 1;
            $sparam['IdEscuela']= $datos['IdEscuela'];
            $sparam['xIdEscuela']= 1;
        }



        if(isset($datos['orderby']) && $datos['orderby']!="")
            $sparam['orderby']= $datos['orderby'];

        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
            return false;
        return true;
    }



    public function BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelxIdTurno($datos,&$resultado,&$numfilas)
    {

        $sparam=array(
            'xIdUsuarioRolDistrito'=> 0,
            'IdUsuarioRolDistrito'=> "",
            'xIdUsuario'=> 1,
            'IdUsuario'=> $datos['IdUsuario'],
            'xIdRol'=> 1,
            'IdRol'=> $datos['IdRol'],
            'xIdRegion'=> 0,
            'IdRegion'=> "",
            'xIdDistrito'=> 0,
            'IdDistrito'=> "",
            'xIdEscuela'=> 0,
            'IdEscuela'=> "",
            'xIdNivel'=> 0,
            'IdNivel'=> "",
            'xIdNivelNull'=> 0,
            'xIdTurno'=> 0,
            'IdTurno'=> "",
            'xIdTurnoNull'=> 0,
            'limit'=> "",
            'orderby'=> "IdUsuarioRolDistrito DESC"
        );

        if(isset($datos['TieneDistrito']) && $datos['TieneDistrito']=="1")
        {
            $sparam['IdRegion']= $datos['IdRegion'];
            $sparam['xIdRegion']= 1;
        }

        if(isset($datos['TieneDistrito']) && $datos['TieneDistrito']=="2")
        {
            $sparam['IdRegion']= $datos['IdRegion'];
            $sparam['xIdRegion']= 1;
            $sparam['IdEscuela']= $datos['IdEscuela'];
            $sparam['xIdEscuela']= 1;
        }

        if(isset($datos['IdNivel']) && $datos['IdNivel']!="")
        {
            $sparam['IdNivel']= $datos['IdNivel'];
            $sparam['xIdNivel']= 1;
        }
        else
            $sparam['xIdNivelNull']= 1;

        if(isset($datos['IdTurno']) && $datos['IdTurno']!="")
        {
            $sparam['IdTurno']= $datos['IdTurno'];
            $sparam['xIdTurno']= 1;
        }
        else
            $sparam['xIdTurnoNull']= 1;



        if(isset($datos['orderby']) && $datos['orderby']!="")
            $sparam['orderby']= $datos['orderby'];

        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];

        if (!parent::BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelxIdTurno($sparam,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdUsuarioxIdRolxIdEscuelaNull($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxIdUsuarioxIdRolxIdEscuelaNull($datos,$resultado,$numfilas))
            return false;
        return true;

    }

    public function BuscarxIdUsuarioxIdRolxIdNivelxIdEscuela($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxIdUsuarioxIdRolxIdNivelxIdEscuela($datos,$resultado,$numfilas))
            return false;
        return true;

    }

    public function BuscarxIdUsuarioxIdRolxIdNivel($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxIdUsuarioxIdRolxIdNivel($datos,$resultado,$numfilas))
            return false;
        return true;

    }





    public function BuscarBloqueo(): bool
    {
        switch (array_key_first($_SESSION['rolcod'])) {
            # Director
            case ROL_EQUIPO_CONDUCCION:
                $datos['IdEscuela'] = $_SESSION['IdEscuelaSeleccionada'];

                $oPofa = new cEscuelasPOF($this->conexion);
                if (!$oPofa->BuscarPOFAEditable($datos, $resultado, $numfilas))
                    return false;

                if ($numfilas > 0) {

                    $r = $this->conexion->ObtenerSiguienteRegistro($resultado);

                    if ($r['PofaEditable'] == 1)
                        return true; # Bloquea módulos
                    else
                        return false;
                }
                break;
            default:
                break;
        }
        return false;
    }


	public function Insertar($datos,&$codigoinsertado)
	{

		if (!$this->_ValidarInsertar($datos))
			return false;


		$oRoles = new cRoles($this->conexion,$this->formato);
		if(!$oRoles->BuscarxCodigo($datos,$resultadoRoles,$numfilasRoles))
			return false;


		if($numfilasRoles!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un rol valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$filaRoles = $this->conexion->ObtenerSiguienteRegistro($resultadoRoles);

		$datos['TieneDistrito']= $filaRoles['TieneDistrito'];


		if($datos['TieneDistrito']=="1")
        {
            if($datos['IdNivel']=="" && $datos['IdTurno']=="")
            {
                if(!$this->BuscarxIdUsuarioxIdRolxIdRegionxIdEscuela($datos,$resultado,$numfilas))
                    return false;

                if($numfilas>0)
                {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar la region con todos los niveles y todos los turnos",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;
                }

            }

            if(!$this->BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelNullxIdTurnoNull($datos,$resultado,$numfilas))
                return false;

            if($numfilas>0)
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar la region - nivel - turno seleccionado 2.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
            else
            {
                //busco con nivel null
                $datosBuscar['IdUsuario']= $datos['IdUsuario'];
                $datosBuscar['IdRol']= $datos['IdRol'];
                $datosBuscar['IdEscuela']= $datos['IdEscuela'];
                $datosBuscar['IdRegion']= $datos['IdRegion'];
                $datosBuscar['IdTurno']= $datos['IdTurno'];
                $datosBuscar['IdNivel']="";
                $datosBuscar['TieneDistrito']=$filaRoles['TieneDistrito'];
                if(!$this->BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelxIdTurno($datosBuscar,$resultado,$numfilas))
                    return false;

                if($numfilas>0)
                {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar la region - nivel - turno seleccionado 1.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;
                }


                if($datos['IdNivel']!="" && $datos['IdTurno']=="")
                {
                    //busco con turno null
                    $datosBuscar['IdUsuario']= $datos['IdUsuario'];
                    $datosBuscar['IdRol']= $datos['IdRol'];
                    $datosBuscar['IdEscuela']= $datos['IdEscuela'];
                    $datosBuscar['IdNivel']=$datos['IdNivel'];
                    if(!$this->BusquedaAvanzada($datosBuscar,$resultado,$numfilas))
                        return false;

                    if($numfilas>0)
                    {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar la region - nivel - turno seleccionado 2.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                        return false;
                    }
                }

                if(!$this->BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelxIdTurno($datos,$resultado,$numfilas))
                    return false;

                if($numfilas>0)
                {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar la region - nivel - turno seleccionado 3.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;
                }
            }
        }



        if ($datos['TieneDistrito']=="2") {
            $id_escuelas = explode(",", $datos["IdEscuela"]);
            $this->_SetearNull($datos);
            $oEscuelas = new cEscuelas($this->conexion);
            foreach ($id_escuelas as $IdEscuela) {
                $datos["IdEscuela"] = $IdEscuela;
                if (!$oEscuelas->BuscarxCodigo(["IdEscuela" => $IdEscuela], $resultadoEscuela, $numfilaEscuela))
                    return false;
                if ($numfilaEscuela != 1) {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, escuela inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;
                }
                $fila_escuela = $this->conexion->ObtenerSiguienteRegistro($resultadoEscuela);
                $datos["IdRegion"] = $fila_escuela["IdRegion"];

                if($datos['IdNivel']=="" && $datos['IdTurno']=="")
                {
                    if(!$this->BuscarxIdUsuarioxIdRolxIdRegionxIdEscuela($datos,$resultado,$numfilas))
                        return false;

                    if($numfilas>0)
                    {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar la escuela con todos los niveles y todos los turnos.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                        return false;
                    }

                }

                if(!$this->BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelNullxIdTurnoNull($datos,$resultado,$numfilas))
                    return false;

                if($numfilas>0)
                {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar la escuela - nivel - turno seleccionado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;
                }
                else
                {
                    //busco con nivel null
                    $datosBuscar['IdUsuario']= $datos['IdUsuario'];
                    $datosBuscar['IdRol']= $datos['IdRol'];
                    $datosBuscar['IdEscuela']= $datos['IdEscuela'];
                    $datosBuscar['IdRegion']= $datos['IdRegion'];
                    $datosBuscar['IdTurno']= $datos['IdTurno'];
                    $datosBuscar['IdNivel']="";
                    $datosBuscar['TieneDistrito']=$filaRoles['TieneDistrito'];
                    if(!$this->BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelxIdTurno($datosBuscar,$resultado,$numfilas))
                        return false;

                    if($numfilas>0)
                    {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar la escuela - nivel - turno seleccionado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                        return false;
                    }

                    if($datos['IdNivel']!="" && $datos['IdTurno']=="")
                    {
                        //busco con turno null
                        $datosBuscar['IdUsuario']= $datos['IdUsuario'];
                        $datosBuscar['IdRol']= $datos['IdRol'];
                        $datosBuscar['IdEscuela']= $datos['IdEscuela'];
                        $datosBuscar['IdNivel']=$datos['IdNivel'];
                        if(!$this->BusquedaAvanzada($datosBuscar,$resultado,$numfilas))
                            return false;

                        if($numfilas>0)
                        {
                            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar la escuela - nivel - turno seleccionado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                            return false;
                        }
                    }

                    if(!$this->BuscarxIdUsuarioxIdRolxIdRegionxIdEscuelaxIdNivelxIdTurno($datos,$resultado,$numfilas))
                        return false;

                    if($numfilas>0)
                    {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar la escuela - nivel - turno seleccionado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                        return false;
                    }
                }

                $datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
                $datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
                if (!parent::Insertar($datos,$codigoinsertado))
                    return false;
            }
        }



        if($datos['TieneDistrito']=="3")
        {

            //busco si existe el usuario, rol, nivel, escuela
            $datosBuscar['IdUsuario']= $datos['IdUsuario'];
            $datosBuscar['IdRol']= $datos['IdRol'];
            $datosBuscar['IdNivel']= $datos['IdNivel'];
            $datosBuscar['IdEscuela']= $datos['IdEscuela'];
            if(!$this->BuscarxIdUsuarioxIdRolxIdNivel($datosBuscar,$resultado,$numfilas))
                return false;

            if($numfilas>0)
            {
                //si existe es (Error) no se puede insertar el mismo nivel y escuela
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar - nivel - escuela.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }

        }
        if ($datos['TieneDistrito']=="5") {
            $id_escuelas = explode(",", $datos["IdEscuela"]);
            $this->_SetearNull($datos);
            $oEscuelas = new cEscuelas($this->conexion);
            foreach ($id_escuelas as $IdEscuela) {
                $datos["IdEscuela"] = $IdEscuela;
                if (!$oEscuelas->BuscarxCodigo(["IdEscuela" => $IdEscuela], $resultadoEscuela, $numfilaEscuela))
                    return false;
                if ($numfilaEscuela != 1) {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, escuela inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;
                }
                $fila_escuela = $this->conexion->ObtenerSiguienteRegistro($resultadoEscuela);
                $datos["IdRegion"] = $fila_escuela["IdRegion"];
                /*
                if(!$this->BuscarxIdUsuarioxIdRolxIdNivelxIdEscuela($datos,$resultadoTieneEscuela,$numfilasTieneEscuela))
                    return false;
                if($numfilasTieneEscuela>0)  // si ya tiene la escuela
                {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede agregar escuela.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;
                }
                */
                $datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
                $datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
                if (!parent::Insertar($datos,$codigoinsertado))
                    return false;

            }
            return true;
        }

        if ($datos['TieneDistrito']!="2") {

            $this->_SetearNull($datos);
            $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
            $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
            if (!parent::Insertar($datos, $codigoinsertado))
                return false;
        }

        return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		return true;
	}

    public function ActualizarRolesDistritos($datos)
    {

        $oObjeto = new cServiciosUsuarios($this->conexion);

        $datosregistro = $oObjeto->ObtenerUsuarioxId($datos);
        $Roles = $datosregistro["Roles"];




        //print_r($Roles);die;
        $arrayinicial = array();
        if(count($Roles)>0)
        {
            foreach ($Roles as $key =>$DatosRoles) {
                $arrayinicial[$DatosRoles['IdRol']] = $DatosRoles['IdRol'];
            }

        }
        $arrayfinal = array();
        if(isset($datos['Roles']) && count($datos['Roles'])>0)
        {
            foreach($datos['Roles'] as $IdRol)
            {
                $arrayfinal[$IdRol] = $IdRol;
            }
        }

        $arraysacar = array_diff($arrayinicial,$arrayfinal);
        $arrayponer = array_diff($arrayfinal,$arrayinicial);


        $datosinsertar['IdUsuario'] = $datos['Id'];
        foreach($arrayponer as $IdRol)
        {
            $datosinsertar['IdRol'] = $IdRol;
            $this->_SetearNull($datosinsertar);
            $datosinsertar['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
            $datosinsertar['UltimaModificacionFecha']=date("Y-m-d H:i:s");
            if (!parent::Insertar($datosinsertar,$codigoinsertado))
                return false;
        }


        $datoseliminar['IdUsuario'] = $datos['Id'];
        foreach($arraysacar as $IdRol)
        {

            $datoseliminar['IdRol'] = $IdRol;
            if (!parent::EliminarxIdUsuarioxIdRol($datoseliminar))
                return false;
        }


       /* $oObjeto = new cServiciosUsuarios($this->conexion);

        $datosEnviar['Id'] = $datos['Id'];
        $datosEnviar['Roles'] = $datos['Roles'];
        if(!$oObjeto->ModificarUsuario($datosEnviar)) {
        	FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, $oObjeto->getError()['error_description'], ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
	        return false;
        }*/

        return true;

    }



	public function Eliminar($datos)
	{

		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		if (!parent::Eliminar($datos))
			return false;

		return true;
	}



	public function EliminarxIdUsuarioxIdClientexIdAreaxIdRol($datos)
	{

		if (!parent::EliminarxIdUsuarioxIdClientexIdAreaxIdRol($datos))
			return false;

		return true;
	}

    public function EliminarxIdUsuarioxIdRol($datos)
    {

        if (!parent::EliminarxIdUsuarioxIdRol($datos))
            return false;

        return true;
    }




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		return true;
	}



	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		return true;
	}



	private function _ValidarEliminar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}



	private function _SetearNull(&$datos)
	{
		if (!isset($datos['IdUsuario']) || $datos['IdUsuario']=="")
			$datos['IdUsuario']="NULL";

		if (!isset($datos['IdRol']) || $datos['IdRol']=="")
			$datos['IdRol']="NULL";

		if (!isset($datos['IdRegion']) || $datos['IdRegion']=="")
			$datos['IdRegion']="NULL";

		if (!isset($datos['IdDistrito']) || $datos['IdDistrito']=="")
			$datos['IdDistrito']="NULL";

		if (!isset($datos['IdEscuela']) || $datos['IdEscuela']=="")
			$datos['IdEscuela']="NULL";

        if (!isset($datos['IdNivel']) || $datos['IdNivel']=="")
            $datos['IdNivel']="NULL";

        if (!isset($datos['IdTurno']) || $datos['IdTurno']=="")
            $datos['IdTurno']="NULL";

        return true;
	}


	private function _SetearNullLocal(&$datos)
    {
        if (!isset($datos['Dni']) || $datos['Dni'] == "")
            $datos['Dni'] = "NULL";

        if (!isset($datos['Cuil']) || $datos['Cuil'] == "")
            $datos['Cuil'] = "NULL";

        return true;
    }


	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdUsuario']) || $datos['IdUsuario']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un usuario",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
        if (isset($datos['IdUsuario']) && $datos['IdUsuario']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdUsuario'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un usuario como numero.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }

        if (!isset($datos['IdRol']) || $datos['IdRol']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un rol",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        if (isset($datos['IdRol']) && $datos['IdRol']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRol'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un rol como numero.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }
        if($datos['TieneDistrito']=="1")
        {
            if (!isset($datos['IdRegion']) || $datos['IdRegion']=="")
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una region",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }

        }

        if (isset($datos['IdRegion']) && $datos['IdRegion']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRegion'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una region como numero.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }

        /*if (!isset($datos['IdDistrito']) || $datos['IdDistrito']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una distrito",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/
        if (isset($datos['IdDistrito']) && $datos['IdDistrito']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdDistrito'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un distrito como numero.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }
        if($datos['TieneDistrito']=="2")
        {
            if (!isset($datos['IdEscuela']) || $datos['IdEscuela']=="")
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una escuela",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }

        }


        if($datos['TieneDistrito']=="3")
        {
            if (!isset($datos['IdNivel']) || $datos['IdNivel']=="")
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Nivel",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }

        }


        if (isset($datos['IdEscuela']) && !empty($datos['IdEscuela']))
        {
             if($datos['TieneDistrito']!="5" && $datos['TieneDistrito']!="2") {
                 if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdEscuela'],"NumericoEntero"))
                 {
                     FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una escuela como numero.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                     return false;
                 }
             } else {
                 $id_escuelas = explode(",", $datos["IdEscuela"]);
                 foreach ($id_escuelas as $IdEscuela) {
                     if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$IdEscuela,"NumericoEntero"))
                     {
                         FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una escuela como numero.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                         return false;
                     }
                 }
             }
        }


        if (isset($datos['IdNivel']) && $datos['IdNivel']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdNivel'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un nivel como numero.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }

        if (isset($datos['IdTurno']) && $datos['IdTurno']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTurno'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un turno como numero.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }


		return true;
	}





}
?>
