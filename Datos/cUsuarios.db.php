<?php

abstract class cUsuariosdb {


    function __construct() {}

    function __destruct() {}


    protected function BuscarTiposDocumento($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_tiposdocumento";
        $sparam = [
            'pEstado' => $datos['Estado'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function ObtenerUsuarios($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_ObtenerUsuarios";
        $sparam = [
            'pxIdPersona' => $datos['xIdPersona'],
            'pIdPersona' => $datos['IdPersona'],
            'pxIdUsuario' => $datos['xIdUsuario'],
            'pIdUsuario' => $datos['IdUsuario'],
            'pxCuil' => $datos['xCuil'],
            'pCuil' => $datos['Cuil'],
            'pxDni' => $datos['xDni'],
            'pDni' => $datos['Dni'],
            'pxIdRol' => $datos['xIdRol'],
            'pIdRol' => $datos['IdRol'],
            'pxNombreCompleto' => $datos['xNombreCompleto'],
            'pNombreCompleto' => $datos['NombreCompleto'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar los usuarios. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function ObtenerUsuariosCantidad($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_ObtenerUsuarios_cantidad";
        $sparam = [
            'pxIdPersona' => $datos['xIdPersona'],
            'pIdPersona' => $datos['IdPersona'],
            'pxIdUsuario' => $datos['xIdUsuario'],
            'pIdUsuario' => $datos['IdUsuario'],
            'pxCuil' => $datos['xCuil'],
            'pCuil' => $datos['Cuil'],
            'pxDni' => $datos['xDni'],
            'pDni' => $datos['Dni'],
            'pxIdRol' => $datos['xIdRol'],
            'pIdRol' => $datos['IdRol'],
            'pxNombreCompleto' => $datos['xNombreCompleto'],
            'pNombreCompleto' => $datos['NombreCompleto'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar los usuarios. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function ObtenerUsuarioxIdUsuario($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_ObtenerUsuario_xIdUsuario";
        $sparam = [
            'pIdUsuario' => $datos['IdUsuario'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function BuscarxCodigo($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_UsuariosLocal_xIdUsuario";
        $sparam = [
            'pIdUsuario' => $datos['IdUsuario'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function TraerAreasyProyectos($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_usuarios_Areas_Proyectos_xIdUsuario";
        $sparam = [
            'pIdUsuario' => $datos['IdUsuario'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function BuscarxActiveDirectory($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_usuarios_UsuarioAd";
        $sparam = [
            'pUsuarioAd' => $datos['UsuarioAd'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function BuscarxSubject($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_usuarios_Subject";
        $sparam = [
            'pSubject' => $datos['Subject'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function BuscarxIdPersona($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_usuario_activo_xIdPersona";
        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function BuscarxDocumento($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_usuarios_DNI";
        $sparam = [
            'pDNI' => $datos['DNI'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function BuscarxCuil($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_usuarios_Cuil";
        $sparam = [
            'pCuil' => $datos['Cuil'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function BusquedaAvanzada($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_usuarios_busqueda_avanzada";
        $sparam = [
            'pxNombre' => $datos['xNombre'],
            'pNombre' => $datos['Nombre'],
            'pxApellido' => $datos['xApellido'],
            'pApellido' => $datos['Apellido'],
            'pxCuil' => $datos['xCuil'],
            'pCuil' => $datos['Cuil'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function BusquedaAvanzadaUsuariosRoles($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_Usuarios_Roles_busqueda_avanzada";
        $sparam = [
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRegion' => $datos['IdRegion'],
            'pxCuil' => $datos['xCuil'],
            'pCuil' => $datos['Cuil'],
            'pBase' => BASEDATOS_PERSONAS,
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar usuarios de escuelas. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function BuscarUsuariosAlertas($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_Usuarios_otros_xIdRol_xIdModulo";
        $sparam = [
            'pxIdRol' => $datos['xIdRol'],
            'pIdRol' => $datos['IdRol'],
            'pxIdModulo' => $datos['xIdModulo'],
            'pIdModulo' => $datos['IdModulo'],
            'pIdUsuario' => $_SESSION['usuariocod'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function autoCompletar(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_Usuarios_autocompletar';
        $sparam = [
            'pCadena' => $datos['Cadena'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar autocompletado. ");
            return false;
        }
        return true;
    }

    protected function Insertar($datos, &$codigoinsertado) {
        $spnombre = "ins_usuarios";
        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
            'pNombre' => $datos['Nombre'],
            'pApellido' => $datos['Apellido'],
            'pCuil' => $datos['Cuil'],
            'pDni' => $datos['Dni'],
            'pSubject' => $datos['Subject'],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => date("Y-m-d H:i:s"),

        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        $codigoinsertado = $this->conexion->UltimoCodigoInsertado();

        /*$datosModif['RegistroSeguridad'] = md5($codigoinsertado.CLAVEENCRIPTACION.md5($datos['Password']));
        $datosModif["IdUsuario"]=$codigoinsertado;
        if (!$this->ModificarCodigoSeguridad($datosModif))
            return false;*/

        return true;
    }


    protected function Modificar($datos) {
        $spnombre = "upd_usuarios_xIdUsuario";
        $sparam = [
            'pNombre' => $datos['Nombre'],
            'pApellido' => $datos['Apellido'],
            'pCuil' => $datos['Cuil'],
            'pDni' => $datos['Dni'],
            'pIdUsuario' => $datos['IdUsuario'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function ModificarSSO($datos) {
        $spnombre = "upd_usuarios_sso_xId";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pCuil' => $datos['Cuil'],
            'pDni' => $datos['Dni'],
            'pSexo' => $datos['Sexo'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionApp' => APP,
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function CambiarPassword($datosusuario, $clavenueva) {

        $spnombre = "upd_Password_xIdUsuario";
        $sparam = [
            'pFechaCambioPassword' => date('Y-m-d H:i:s'),
            'pPassword' => md5($clavenueva),
            'pIdEstado' => $datosusuario['IdEstado'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date('Y-m-d H:i:s'),
            'pRegistroSeguridad' => md5($datosusuario['IdUsuario'] . CLAVEENCRIPTACION . md5($clavenueva)),
            'pIdUsuario' => $datosusuario['IdUsuario'],
        ];
        //print_r($sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $nousar, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al actualizar la contraseña del usuario. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function Eliminar($datos) {
        $spnombre = "del_usuarios_xIdUsuario";
        $sparam = [
            'pIdUsuario' => $datos['IdUsuario'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function ModificarIntentosLoginxUsuario($datos) {
        $spnombre = "upd_usuarios_intentologin_xIdUsuario";
        $sparam = [
            'pIntentosLogin' => $datos['IntentosLogin'],
            'pIdUsuario' => $datos['IdUsuario'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al sumar un intento al login. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function ModificarEstado($datos) {
        $spnombre = "upd_usuarios_IdEstado_xIdUsuario";
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'pIdUsuario' => $datos['IdUsuario'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function ModificarFotoUsuario($datos) {

        $spnombre = "upd_usuarios_foto_xIdUsuario";
        $sparam = [
            'pUbicacionAvatar' => $datos['UbicacionAvatar'],
            'pIdUsuario' => $datos['IdUsuario'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar la foto del usuario. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => FMT_TEXTO]);
            return false;
        }

        return true;
    }


    protected function ModificarEstadoUsuario($datos) {

        $spnombre = "upd_usuarios_IdEstado_BajaFecha_xIdUsuario";
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pBajaFecha' => $datos['BajaFecha'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'pIdUsuario' => $datos['IdUsuario'],
        ];


        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno) || $numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado del usuario. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => FMT_TEXTO]);
            return false;
        }

        return true;
    }


    protected function ModificarCodigoSeguridad($datos) {

        $spnombre = "upd_usuarios_RegistroSeguridad_xIdUsuario";
        $sparam = [
            'pRegistroSeguridad' => $datos['RegistroSeguridad'],
            'pIdUsuario' => $datos['IdUsuario'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno) || $numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el registro de seguridad. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => FMT_TEXTO]);
            return false;
        }

        return true;
    }


    protected function InsertarProyectoxUsuario($datos) {
        $spnombre = "ins_UsuariosProyectos";
        $sparam = [
            'pIdProyecto' => $datos['IdProyecto'],
            'pIdUsuario' => $datos['IdUsuario'],
            'pAltaFecha' => date("Y-m-d H:i:s"),
            'pAltaUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function EliminarProyectosxUsuario($datos) {
        $spnombre = "del_UsuariosProyectos_xIdProyecto_xIdUsuario";
        $sparam = [
            'pIdProyecto' => $datos['IdProyecto'],
            'pIdUsuario' => $datos['IdUsuario'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


// Retorna una consulta con todos los usuarios que cumplan con las condiciones

// Parámetros de Entrada:
//		datosbuscar: array asociativo con los filtros. Claves: usuarionombre, usuarioapellido, usuariocuit, usuarioemail

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no


    protected function BuscarUsuarios($ArregloDatos, &$resultado, &$numfilas) {


        $sparam = ['pestadocod' => 0];
        $sparam += ['pestadopass' => 0];
        $sparam += ['pestadonom' => 0];
        $sparam += ['pestadoape' => 0];
        $sparam += ['pestadoemail' => 0];
        $sparam += ['pestadoestado' => 0];

        $sparam += ['pIdUsuario' => ""];
        $sparam += ['pPassword' => ""];
        $sparam += ['pNombre' => ""];
        $sparam += ['pApellido' => ""];
        $sparam += ['pEmail' => ""];
        $sparam += ['pIdEstado' => ""];


        if (isset ($ArregloDatos['pIdUsuario'])) {
            if ($ArregloDatos['pIdUsuario'] != "") {
                $sparam['pIdUsuario'] = $ArregloDatos['pIdUsuario'];
                $sparam['pestadocod'] = 1;
            }
        }
        if (isset ($ArregloDatos['pPassword'])) {
            if ($ArregloDatos['pPassword'] != "") {
                $sparam['pPassword'] = $ArregloDatos['pPassword'];
                $sparam['pestadopass'] = 1;
            }
        }
        if (isset ($ArregloDatos['pNombre'])) {
            if ($ArregloDatos['pNombre'] != "") {
                $sparam['pNombre'] = $ArregloDatos['pNombre'];
                $sparam['pestadonom'] = 1;
            }
        }
        if (isset ($ArregloDatos['pApellido'])) {
            if ($ArregloDatos['pApellido'] != "") {
                $sparam['pApellido'] = $ArregloDatos['pApellido'];
                $sparam['pestadoape'] = 1;
            }
        }
        if (isset ($ArregloDatos['pEmail'])) {
            if ($ArregloDatos['pEmail'] != "") {
                $sparam['pEmail'] = $ArregloDatos['pEmail'];
                $sparam['pestadoemail'] = 1;
            }
        }
        if (isset ($ArregloDatos['pIdEstado'])) {
            if ($ArregloDatos['pIdEstado'] != "") {
                $sparam['pIdEstado'] = $ArregloDatos['pIdEstado'];
                $sparam['pestadoestado'] = 1;
            }
        }


        $spnombre = "sel_usuarios";
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar una busqueda de usuario. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function buscarxUsuarioxRolxEscuela(array $datos, &$resultado, ?int &$numfilas): bool {
        $sp_nombre = 'sel_Usuarios_xIdPersona';
        $sp_param = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdRol' => $datos['IdRol'],

        ];
        try {
            $this->conexion->ejecutarStoredProcedure($sp_nombre, $sp_param, $resultado, $numfilas, $errno);
        } catch (Bigtree\ExcepcionDB $e) {
            $this->setError(400, 'Error al buscar por persona. ');
            return false;
        }

        return true;
    }


    protected function ObtenerUsuariosSubject($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_usuarios_Nombre_Apellido_Subject";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al traer listado de usuarios. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


}

?>
