<?php

/**
 * Class de Usuarios
 * Instancia de usuarios.
 *
 * El constructor la clase de usuarios maneja la persistencia a la base de datos
 *
 * @category  MyLibrary
 * @example   usuarios.php
 * @example   <br />
 *    $oUser = new cUsuarios($conexion);<br />
 *  $oUser->BuscarxCodigo($datos,$resultado,$numfilas);<br />
 *  $datosUsuario = $conexion->ObtenerSiguienteRegistro($resultado);<br />
 *  print_r($datosUsuario);<br />
 * @version   0.01
 * @since     2017-08-02
 * @author    Alejandro Precioso <aprecioso@gmail.com>
 */
use Bigtree\OAuth\User;

include(DIR_CLASES_DB . "cUsuarios.db.php");

class cUsuarios extends cUsuariosdb {
    use ManejoErrores;

    /**
     * Conexion a la base de datos.
     *
     * @var accesoBDLocal conexion
     */
    protected $conexion;
    /**
     * Formato de errores. Formato en que se muestran los errores.
     *
     * @var string
     */
    protected $formato;

    function __construct($conexion, $formato = FMT_TEXTO) {
        $this->conexion = &$conexion;
        $this->formato = &$formato;
        $this->error = [];
        parent::__construct();
    }

    function __destruct() {
        parent::__destruct();
    }

    function BuscarTiposDocumento($datos, &$resultado, &$numfilas) {
        if (!isset($datos['Estado']) || $datos['Estado'] == "")
            $datos['Estado'] = "10";
        if (!parent::BuscarTiposDocumento($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    function ObtenerUsuarios($datos, &$resultado, &$numfilas) {
        $sparam = array(
            'xIdPersona' => 0,
            'IdPersona' => "",
            'xIdUsuario' => 0,
            'IdUsuario' => "",
            'xCuil' => 0,
            'Cuil' => "",
            'xDni' => 0,
            'Dni' => "",
            'xIdRol' => 0,
            'IdRol' => "",
            'xNombreCompleto' => 0,
            'NombreCompleto' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "",
            'limit' => '',
            'orderby' => "U.IdUsuario ASC",
        );

        if (isset($datos['IdPersona']) && $datos['IdPersona'] != "") {
            $sparam['IdPersona'] = $datos['IdPersona'];
            $sparam['xIdPersona'] = 1;
        }
        if (isset($datos['IdUsuario']) && $datos['IdUsuario'] != "") {
            $sparam['IdUsuario'] = $datos['IdUsuario'];
            $sparam['xIdUsuario'] = 1;
        }
        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }
        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            $sparam['Dni'] = $datos['Dni'];
            $sparam['xDni'] = 1;
        }
        if (isset($datos['IdRol']) && $datos['IdRol'] != "") {
            $sparam['IdRol'] = $datos['IdRol'];
            $sparam['xIdRol'] = 1;
        }
        if (isset($datos['NombreCompleto']) && $datos['NombreCompleto'] != "") {
            $sparam['NombreCompleto'] = $datos['NombreCompleto'];
            $sparam['xNombreCompleto'] = 1;
        }
        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::ObtenerUsuarios($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    function ObtenerUsuariosCantidad($datos, &$resultado, &$numfilas) {
        $sparam = array(
            'xIdPersona' => 0,
            'IdPersona' => "",
            'xIdUsuario' => 0,
            'IdUsuario' => "",
            'xCuil' => 0,
            'Cuil' => "",
            'xDni' => 0,
            'Dni' => "",
            'xIdRol' => 0,
            'IdRol' => "",
            'xNombreCompleto' => 0,
            'NombreCompleto' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "",
        );

        if (isset($datos['IdPersona']) && $datos['IdPersona'] != "") {
            $sparam['IdPersona'] = $datos['IdPersona'];
            $sparam['xIdPersona'] = 1;
        }
        if (isset($datos['IdUsuario']) && $datos['IdUsuario'] != "") {
            $sparam['IdUsuario'] = $datos['IdUsuario'];
            $sparam['xIdUsuario'] = 1;
        }
        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }
        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            $sparam['Dni'] = $datos['Dni'];
            $sparam['xDni'] = 1;
        }
        if (isset($datos['IdRol']) && $datos['IdRol'] != "") {
            $sparam['IdRol'] = $datos['IdRol'];
            $sparam['xIdRol'] = 1;
        }
        if (isset($datos['NombreCompleto']) && $datos['NombreCompleto'] != "") {
            $sparam['NombreCompleto'] = $datos['NombreCompleto'];
            $sparam['xNombreCompleto'] = 1;
        }
        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (!parent::ObtenerUsuariosCantidad($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    function ObtenerUsuarioxIdUsuario($datos, &$resultado, &$numfilas) {

        if (!parent::ObtenerUsuarioxIdUsuario($datos, $resultado, $numfilas))
            return false;
        return true;
    }




    /**
     * Retorna datos de usuario por codigo.
     *
     * @param array $datos ['IdUsuario'], array con clave de codigo de usuario
     *
     * @return  Query con los datos del usuario
     * @todo    Retorna falso en caso de que exista un problema con el store procedure.
     *
     * @since   2017-08-02
     * @author  Alejandro Precioso <aprecioso@gmail.com>
     *
     */

    public function BuscarxCodigo($datos, &$resultado, &$numfilas) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];

        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    /**
     * Retorna datos de usuario por cuil.
     *
     * @param array $datos ['Cuil'], array con clave de codigo de usuario
     *
     * @return  Query con los datos del usuario
     * @todo    Retorna falso en caso de que exista un problema con el store procedure.
     *
     * @since   2017-08-02
     * @author  Alejandro Precioso <aprecioso@gmail.com>
     *
     */

    public function BuscarxSubject($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarxSubject($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarxIdPersona($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarxIdPersona($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    /**
     * Retorna datos de areas y proyectos de usuario por codigo.
     *
     * @param array $datos ['IdUsuario'], array con clave de codigo de usuario
     *
     * @return  Query con los datos del usuario de areas y proyectos del usuario
     * @todo    Retorna falso en caso de que exista un problema con el store procedure.
     *
     * @since   2017-08-02
     * @author  Alejandro Precioso <aprecioso@gmail.com>
     *
     */

    public function TraerAreasyProyectos($datos, &$resultado, &$numfilas) {

        if (!parent::TraerAreasyProyectos($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    /**
     * Retorna datos de usuario por codigo.
     *
     * @param array $datos ['IdUsuario'], array con clave de codigo de usuario
     *
     * @return  Query con los datos del usuario
     * @todo    Retorna falso en caso de que exista un problema con el store procedure.
     *
     * @since   2017-08-02
     * @author  Alejandro Precioso <aprecioso@gmail.com>
     *
     */

    public function BuscarxActiveDirectory($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarxActiveDirectory($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    /**
     * Retorna datos de usuario por documento.
     *
     * @param array $datos ['DNI'], array con DNI de usuario
     *
     * @return  Query con los datos del usuario
     * @todo    Retorna falso en caso de que exista un problema con el store procedure.
     *
     * @since   2017-08-02
     * @author  Alejandro Precioso <aprecioso@gmail.com>
     *
     */

    public function BuscarxDocumento($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarxDocumento($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxCuil($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarxCuil($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    /**
     * Retorna datos de usuario por codigo.
     *
     * @param array $datos ['IdUsuario'], array con clave de codigo de usuario
     *
     * @return  Query con los datos del usuario
     * @todo    Retorna falso en caso de que exista un problema con el store procedure.
     *
     * @since   2017-08-02
     * @author  Alejandro Precioso <aprecioso@gmail.com>
     *
     */

    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];
        $sparam = array(
            'xNombre' => 0,
            'Nombre' => "",
            'xApellido' => 0,
            'Apellido' => "",
            'xCuil' => 0,
            'Cuil' => "",
            'limit' => '',
            'orderby' => "IdUsuario DESC",
        );

        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            $sparam['Nombre'] = $datos['Nombre'];
            $sparam['xNombre'] = 1;
        }

        if (isset($datos['Apellido']) && $datos['Apellido'] != "") {
            $sparam['Apellido'] = $datos['Apellido'];
            $sparam['xApellido'] = 1;
        }
        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }
        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;

        return true;
    }

    public function BusquedaAvanzadaUsuariosRoles($datos, &$resultado, &$numfilas): bool {

        $sparam = array(
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdRegion' => 0,
            'IdRegion' => "-1",
            'xCuil' => 0,
            'Cuil' => "",
            'limit' => '',
            'orderby' => "a.IdUsuario ASC",
        );

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != '') {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != '') {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != '') {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != '')
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != '')
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzadaUsuariosRoles($sparam, $resultado, $numfilas))
            return false;

        return true;
    }

    public function BuscarUsuariosAlertas($datos, &$resultado, &$numfilas) {
        $sparam = array(
            'xIdRol' => 0,
            'IdRol' => "",
            'xIdModulo' => 0,
            'IdModulo' => "",
        );

        if (isset($datos['IdRol']) && $datos['IdRol'] != "") {
            $sparam['IdRol'] = $datos['IdRol'];
            $sparam['xIdRol'] = 1;
        }
        if (isset($datos['IdModulo']) && $datos['IdModulo'] != "") {
            $sparam['IdModulo'] = $datos['IdModulo'];
            $sparam['xIdModulo'] = 1;
        }

        if (!parent::BuscarUsuariosAlertas($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function autoCompletar($datos, &$resultado, &$numfilas): bool {

        if (!parent::autoCompletar($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function Insertar($datos, &$codigoinsertado) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];


        if (!$this->_ValidarInsertar($datos))
            return false;

        $datos['Estado'] = 30;
        $this->_SetearNull($datos);
        if (!parent::Insertar($datos, $codigoinsertado))
            return false;

        return true;
    }

    public function ModificarDatosUsuario ($datos) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un codigo valido.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }
        $datosUsuario = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (false === $datosUsuario)
            return false;

        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;


        return true;
    }

    public function Modificar($datos) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un codigo valido.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }
        $datosUsuario = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (false === $datosUsuario)
            return false;


        $oUsuarios_Roles = new cUsuariosRoles($this->conexion);
        if (!$this->_ValidarModificar($datos))
            return false;

        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;

        if (isset($datos['Password']) && $datos['Password'] != "") {
            if (!$this->CambiarPwdUsuario($datos))
                return false;
        }


        //if (!$oUsuarios_Roles->ActualizarRolesUsuario($datos))
        //	return false;

        return true;
    }

    public function modificarPerfil($datos) {

        $oPersonas = new cServiciosPersonas($this->conexion);
        if (!$oPersonas->modificarPerfil($datos)) {
            $error = $oPersonas->getError();
            echo($error['error_description']);
            return false;
        }

        return true;
    }


    public function CrearUsuario($datos,&$codigoInsertado) {


        // voy a crear al sso, si no existe lo crea, la fucnion siempre me devuelve el subject de usuario
        $oUser = new User();

        try {
            $Subject = $oUser->CrearUsuario($datos);
        }catch (\Bigtree\ExcepcionLogica $e)
        {
            $error = $e->getMessage();
            //$error = "Error al crear el usuario en el sso";
            $this->setError(400,$error);
            return false;
        }

        try {
            $IdPersona = $this->CrearModificarPersona($datos);
        }catch (\Bigtree\ExcepcionLogica $e)
        {
            //$error = $e->getMessage();
            $error = "Error al crear o modificar la persona";
            $this->setError(400,$error);
            return false;
        }

        if($IdPersona == false)
        {
            $error = $this->getError()['error_description'];
            $this->setError(400,$error);
            return false;
        }


        $oUsuarios = new cUsuarios($this->conexion,$this->formato);
        $datos['IdPersona'] = $IdPersona;
        $datos['Subject'] = $Subject;
        if(!$oUsuarios->Insertar($datos,$codigoInsertado))
        {
            $error = "Error al insertar el usuario";
            $this->setError(400,$error);
            return false;
        }

        return true;
    }




    public function ModificarUsuario($datos) {

        $datosBuscarUsuario['IdUsuario'] = $datos['Id'];
        if(!$this->BuscarxCodigo($datosBuscarUsuario, $resultadoUsuario, $numfilasUsuario))
            return false;


        if($numfilasUsuario != 1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRSOSP, "Error, No existe el usuario", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        $filaUsuario = $this->conexion->ObtenerSiguienteRegistro($resultadoUsuario);


        $oPersonas = new cServiciosPersonas($this->conexion);
        $datosPersona = $oPersonas->ObtenerPersonaxId($datos);

        if (false === $datosPersona) {
            $this->setError($oPersonas->getError());
            return false;
        }

        $datos['IdEstadoPersona'] = $datosPersona['IdEstadoPersona'];

        if (BLOQUEA_DNI)
            $datos['Dni'] = $datosPersona['DNI'];
        if (BLOQUEA_CUIL)
            $datos['Cuil'] = $datosPersona['CUIL'];

        if (!$this->_ValidarModificar($datos))
            return false;

        $datosModif = $datos;

        $this->_SetearNull($datosModif);


        $datosModif['IdUsuario'] = $datos['IdUsuario'] = $datos['Id'];
        if (!parent::Modificar($datosModif))
            return false;


        $datosSSo = $datos;
        $datosSSo['id'] = $filaUsuario['Subject'];


        $oUser = new User();
        try {
            $oUser->modificarxCodigo($datosSSo);
        }catch (\Bigtree\ExcepcionLogica $e)
        {
            $error = $e->getMessage();
            //$error = "Error al modifcar el usuario en el sso";
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRSOSP, $error, array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        $datos['IdTipoDocumento'] = 1;
        $datos['DNI'] = $datos['Dni'];
        $datos['CUIL'] = $datos['Cuil'];

        $datos["Calle"] = $datosPersona["Calle"];
        $datos["NumeroPuerta"] = $datosPersona["NumeroPuerta"];
        $datos["Piso"] = $datosPersona["Piso"];
        $datos["Depto"] = $datosPersona["Depto"];
        $datos["CodigoPostal"] = $datosPersona["CodigoPostal"];
        $datos["IdDepartamento"] = $datosPersona["IdDepartamento"];
        $datos["IdProvincia"] = $datosPersona["IdProvincia"];
        $datos["IdLocalidad"] = $datosPersona["IdLocalidad"];
        $datos["IdRegion"] = $datosPersona["IdRegion"];
        $datos["FechaIngreso"] = FuncionesPHPLocal::ConvertirFecha($datosPersona["FechaIngreso"], "aaaa-mm-dd", "dd/mm/aaaa");
        $datos["FallecidoFecha"] = FuncionesPHPLocal::ConvertirFecha($datosPersona["FallecidoFecha"], "aaaa-mm-dd", "dd/mm/aaaa");


        if (!$oPersonas->ModificarPersona($datos)) {
            $error = $oPersonas->getError();
            echo($error['error_description']);
            return false;
        }

        return true;
    }

    public function HabilitarUsuario($datos,&$codigoInsertado) {

        // voy a crear al sso, si no existe lo crea, la fucnion siempre me devuelve el subject de usuario
        $oUser = new User();
        try {
            $Subject = $oUser->CrearUsuario($datos);
        }catch (\Bigtree\ExcepcionLogica $e)
        {
            $error = $e->getMessage();
            //$error = "Error al crear el usuario en el sso";
            $this->setError(400,$error);
            return false;
        }

       try {
            $IdPersona = $this->CrearModificarPersona($datos);
        }catch (\Bigtree\ExcepcionLogica $e)
        {
            //$error = $e->getMessage();
            $error = "Error al crear o modificar la persona";
            $this->setError(400,$error);
            return false;
        }

        if($IdPersona == false)
        {
            $error = $this->getError()['error_description'];
            $this->setError(400,$error);
            return false;
        }


        $oUsuarios = new cUsuarios($this->conexion,$this->formato);
        $datos['IdPersona'] = $IdPersona;
        $datos['Subject'] = $Subject;
        if(!$oUsuarios->Insertar($datos,$codigoInsertado))
        {
            $error = "Error al insertar el usuario";
            $this->setError(400,$error);
            return false;
        }


        $oUsuariosRolesDistritos = new cUsuariosRolesDistritos($this->conexion,$this->formato);

        $datosRoles['IdUsuario'] = $codigoInsertado;
        $datosRoles['TieneDistrito']=4;
        $datosRoles['IdRol'] = ROL_AGENTE;
        if(!$oUsuariosRolesDistritos->Insertar($datosRoles,$codigoinsertado ))
        {
            $error = "Error al insertar el rol Agente";
            $this->setError(400,$error);
            return false;
        }

        return true;
    }

    public function CrearUsuarioRolAgente($datos,&$codigoInsertado) {

        $datos['Nivel']=2;
        if(!$this->_ValidarDatosVaciosUsuarioRolAgente($datos))
            return false;

        if(!$this->CrearUsuario($datos,$codigoInsertado))
            return false;

        $oUsuariosRolesDistritos = new cUsuariosRolesDistritos($this->conexion,$this->formato);

        $datosRoles['IdUsuario'] = $codigoInsertado;
        $datosRoles['TieneDistrito']=4;
        $datosRoles['IdRol'] = ROL_AGENTES;
        if(!$oUsuariosRolesDistritos->Insertar($datosRoles,$codigoinsertado ))
        {
            $error = "Error al insertar el rol Agente";
            $this->setError(400,$error);
            return false;
        }

        return true;
    }


    public function ModificarNivel($datos)
    {

        $oUsuarios = new cServiciosUsuarios($this->conexion);
        $datosNivel['Nivel'] = $datos['Nivel'];
        $datosNivel['Id'] = $datos['Id'];
        if(!$oUsuarios->ModificarNivel($datosNivel))
        {
            $error = $oUsuarios->getError();
            return false;
        }

        return true;
    }


    public function ModificarDatosPersonales($datos) {
        $ArregloDatos['IdUsuario'] = $_SESSION["usuariocod"];
        if (!$this->BuscarxCodigo($ArregloDatos, $resultadousuarios, $numfilas) || $numfilas != 1)
            return false;

        $filausuario = $this->conexion->ObtenerSiguienteRegistro($resultadousuarios);
        $datos['IdUsuario'] = $filausuario["IdUsuario"];
        $datos['IdTipo'] = $filausuario["IdTipo"];
        $datos['UsuarioAd'] = $filausuario["UsuarioAd"];
        $datos['DNI'] = $filausuario["DNI"];
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;

        return true;
    }

    public function CambiarPwd($IdUsuario, $claveactual, $clavenueva, $claveconf) {
        $ArregloDatos['IdUsuario'] = $IdUsuario;
        if (!$this->BuscarxCodigo($ArregloDatos, $resultadousuarios, $numfilas) || $numfilas != 1)
            return false;

        $filausuario = $this->conexion->ObtenerSiguienteRegistro($resultadousuarios);

        if (md5($claveactual) != $filausuario["Password"] || $clavenueva != $claveconf) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRSOSP, "Los datos ingresados son err&oacute;neos. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        if (!FuncionesPHPLocal::ValidarPassword($clavenueva, $claveactual, $filausuario["Email"], 8)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRSOSP, "La nueva contrase&ntilde;a no es v&aacute;lida. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        if (!parent::CambiarPassword($filausuario, $clavenueva))
            return false;

        return true;
    }

    private function CambiarPwdUsuario($datos) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];
        $ArregloDatos['IdUsuario'] = $datos['IdUsuario'];
        if (!$this->BuscarxCodigo($ArregloDatos, $resultadousuarios, $numfilas) || $numfilas != 1)
            return false;

        $filausuario = $this->conexion->ObtenerSiguienteRegistro($resultadousuarios);
        //print_r($filausuario);die;
        if ($datos['Password'] != $datos['Passwordconfirm']) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRSOSP, "La confirmación de la contraseña es diferente a la misma. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        if (!FuncionesPHPLocal::ValidarPassword($datos['Password'], $filausuario["Password"], $filausuario["Email"], 8)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRSOSP, "La nueva contraseña no es válida. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }
        if (!parent::CambiarPassword($filausuario, $datos['Password']))
            return false;

        /*$datosModif['IdUsuario'] = $filausuario['IdUsuario'];
        $datosModif['IdEstado'] = USUARIONUEVO;
        if (!$this->ModificarEstado($datosModif))
            return false;

        if (!$this->ResetearIntentosLogin($datosModif))
            die();*/

        return true;
    }


    public function Eliminar($datos) {

        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];

        if (!$this->_ValidarEliminar($datos))
            return false;

        $datosmodif['IdUsuario'] = $datos['IdUsuario'];
        $datosmodif['IdEstado'] = ELIMINADO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        return true;
    }


    public function ModificarEstado($datos) {
        if (!parent::ModificarEstado($datos))
            return false;
        return true;
    }


    public function Activar($datos) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];
        $datosmodif['IdUsuario'] = $datos['IdUsuario'];
        $datosmodif['IdEstado'] = USUARIOACT;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        return true;
    }


    public function DesActivar($datos) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];
        $datosmodif['IdUsuario'] = $datos['IdUsuario'];
        $datosmodif['IdEstado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        return true;
    }


    public function GuardarImagen($datos) {

        if (isset($datos['size']) && $datos['size'] != "" && isset($datos['name']) && $datos['name'] != "" && isset($datos['file']) && $datos['file'] != "") {
            if (!$this->InsertarImgDesdeTemporal($datos))
                return false;


            if (!$this->ModificarFotoUsuario($datos))
                return false;

            if ($datos["IdUsuario"] == $_SESSION['usuariocod'])
                $_SESSION['avatar'] = $datos["UbicacionAvatar"];
            //print_r($datos);die;
        }
        //echo 'acá';//die;
        return true;
    }

    public function InsertarImgDesdeTemporal(&$datos) {


        $pathinfo = pathinfo($datos['name']);
        $extension = strtolower($pathinfo['extension']);

        switch ($extension) {
            case "jpg":
            case "gif":
            case "png":
                break;
            default:
                FuncionesPHPLocal::MostrarMensaje($this->conexion, "Formato de archivo no permitido.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
                return false;
                break;
        }

        if (!is_dir(PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR)) {
            @mkdir(PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR);
        }

        //Subir imagenes
        $nombrearchivo = "usuario_" . $datos['IdUsuario'] . ".jpg";//.$extension;
        $carpetaorigen = PATH_STORAGE . "/" . "tmp/" . $datos['file'];

        $ancho = TAMANIOAVATARL;
        $calidad = 100;
        $forma = "T";
        $image_new = FuncionesPHPLocal::Guardafoto($carpetaorigen, $ancho, $calidad, $forma);
        $savePath = PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_L;
        if (!imagejpeg($image_new, $savePath . "usuario_" . $datos['IdUsuario'] . '.jpg', $calidad)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error guardar la imagen. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        $ancho = TAMANIOAVATARM;
        $calidad = 100;
        $forma = "T";
        $image_new = FuncionesPHPLocal::Guardafoto($carpetaorigen, $ancho, $calidad, $forma);
        $savePath = PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_M;
        if (!imagejpeg($image_new, $savePath . "usuario_" . $datos['IdUsuario'] . '.jpg', $calidad)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error guardar la imagen. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        $ancho = TAMANIOAVATARS;
        $calidad = 100;
        $forma = "T";
        $image_new = FuncionesPHPLocal::Guardafoto($carpetaorigen, $ancho, $calidad, $forma);
        $savePath = PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_S;
        if (!imagejpeg($image_new, $savePath . "usuario_" . $datos['IdUsuario'] . '.jpg', $calidad)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error guardar la imagen. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        @unlink($carpetaorigen);

        $datos['UbicacionAvatar'] = $nombrearchivo;
        return true;

    }

    public function ModificarFotoUsuario($datos) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];
        if (!parent::ModificarFotoUsuario($datos))
            return false;

        return true;
    }

    public function EliminarFotoUsuario($datos) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];
        if (!$this->BorrarFotoUsuario($datos))
            return false;

        if (!$this->ModificarFotoUsuario($datos))
            return false;

        return true;

    }

    function BorrarFotoUsuario(&$datos) {

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar el usuario.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }
        $datosusuario = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (isset($datosusuario['UbicacionAvatar']) && $datosusuario['UbicacionAvatar'] != "") {
            $savePath = PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_L . "usuario_" . $datos['IdUsuario'] . '.jpg';
            unlink($savePath);

            $savePath = PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_M . "usuario_" . $datos['IdUsuario'] . '.jpg';
            unlink($savePath);

            $savePath = PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_S . "usuario_" . $datos['IdUsuario'] . '.jpg';
            unlink($savePath);
        }

        if ($datos["IdUsuario"] == $_SESSION['IdUsuario'])
            $_SESSION['avatar'] = "/default.png";

        $datos['UbicacionAvatar'] = "/default.png";
        return true;
    }


    public function BloqueaUsuario($datos) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];
        $ArregloDatos['IdUsuario'] = $datos['IdUsuario'];
        if (!$this->BuscarxCodigo($ArregloDatos, $resultadousuarios, $numfilas) || $numfilas != 1)
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRSOSP, "Error, usuario inexistente. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => FMT_TEXTO));
            return false;
        }

        $filausuario = $this->conexion->ObtenerSiguienteRegistro($resultadousuarios);

        $filausuario["IdEstado"] = USUARIOBAJA;
        $filausuario['BajaFecha'] = date("Y/m/d H:i:s");
        if (!parent::ModificarEstadoUsuario($filausuario))
            return false;

        return true;
    }

    public function RehabilitaUsuario($datos) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];

        $ArregloDatos['IdUsuario'] = $datos['IdUsuario'];
        if (!$this->BuscarxCodigo($ArregloDatos, $resultadousuarios, $numfilas) || $numfilas != 1)
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocalPortal::MostrarMensaje($this->conexion, MSG_ERRSOSP, "Error, usuario inexistente. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => FMT_TEXTO));
            return false;
        }

        $filausuario = $this->conexion->ObtenerSiguienteRegistro($resultadousuarios);

        if ($filausuario["IdEstado"] != USUARIOBAJA) {
            FuncionesPHPLocalPortal::MostrarMensaje($this->conexion, MSG_ERRSOSP, "El usuario no se encuentra para rehabilitar.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => FMT_TEXTO));
            return false;
        } else { // se puede generar la nueva contraseña
            $filausuario["IdEstado"] = USUARIOACT;
            $filausuario['BajaFecha'] = "NULL";
            if (!parent::ModificarEstadoUsuario($filausuario))
                return false;
        }

        return true;
    }


    public function ResetearIntentosLogin($datos) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];
        $datosmodif['IdUsuario'] = $datos['IdUsuario'];
        $datosmodif['IntentosLogin'] = 0;
        if (!parent::ModificarIntentosLoginxUsuario($datosmodif))
            return false;
        return true;
    }


    public function SumarIntentosLoginxUsuario($datos) {
        if (!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
            $datos['IdUsuario'] = $datos['usuariocod'];
        $ArregloDatos['IdUsuario'] = $datos['IdUsuario'];
        if (!$this->BuscarxCodigo($ArregloDatos, $resultadousuarios, $numfilas) || $numfilas != 1)
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocalPortal::MostrarMensaje($this->conexion, MSG_ERRSOSP, "Error, usuario inexistente. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => FMT_TEXTO));
            return false;
        }

        $filausuario = $this->conexion->ObtenerSiguienteRegistro($resultadousuarios);

        $datosmodif['IdUsuario'] = $datos['IdUsuario'];
        $datosmodif['IntentosLogin'] = $filausuario['IntentosLogin'] + 1;
        if (!parent::ModificarIntentosLoginxUsuario($datosmodif))
            return false;

        if ($filausuario['IntentosLogin'] >= CANTINTENTOSBLOQUEO) {
            //bloqueo
            $datosModif["IdEstado"] = USUARIOPASSBLOQUEADO;
            $datosModif['IdUsuario'] = $datos['IdUsuario'];
            if (!parent::ModificarEstado($datosModif))
                return false;

        }
        return true;

    }



    //-----------------------------------------------------------------------------------------
    // Retorna si el rol elegido es válido

    // Parámetros de Entrada:
    //		IdUsuario
    //		rolcod

    // Retorna:
    //		datosvalidados: retorna el rol asignado
    //		la función retorna true o false si se pudo ejecutar con éxito o no


    public function ValidarDatosElegirRol($UsuarioAd, $IdRol, $IdArea, &$datosvalidados) {

        $oUsuariosRoles = new cUsuariosRoles($this->conexion);
        $datosBuscar['UsuarioAd'] = $UsuarioAd;

        if (!$oUsuariosRoles->BuscarxActiveDirectory($datosBuscar, $resultado, $numfilas))
            return false;

        $arrayRoles = array();
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            $arrayRoles[$fila['IdArea']][$fila['IdRol']] = $fila;


        if (!array_key_exists($IdRol, $arrayRoles[$IdArea])) {
            FuncionesPHPLocalPortal::MostrarMensaje($this->conexion, MSG_ERRSOSP, "Error filas en selección de roles. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        $datosvalidados["rolcod"] = $IdRol;
        $datosvalidados["IdArea"] = $IdArea;


        /*$roles=new cRoles($this->conexion);



        if (!$oRoles->RolesPosiblesAsignarxClientexArea($_SESSION['rolcod'],$datos['IdUsuario'],$datos['IdCliente'],$datos['IdArea'],$numfilas,$roles_sin_asignar))
            return false;

        if (!in_array($datos['IdRol'],$roles_sin_asignar))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error, no puede asignar dicho rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/


        /*$roles->RolesDeUnUsuarioSP($IdUsuario,$spnombre,$spparam);
        $arraybusq=array("IdRol"=>$IdRol);

        if(!$this->conexion->BuscarRegistroxClave($spnombre,$spparam,$arraybusq,$query,$filaret,$numfilasmatcheo,$errno))
        {
            FuncionesPHPLocalPortal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error BD en selección de roles. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        elseif($numfilasmatcheo!=1)
        {
            FuncionesPHPLocalPortal::MostrarMensaje($this->conexion,MSG_ERRSOSP,"Error filas en selección de roles. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/


        return true;
    }




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------


    private function _ValidarInsertar($datos) {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        /*if (!$this->BuscarxActiveDirectory($datos, $resultado, $numfilas))
            return false;

        if ($numfilas == 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error ya existe el usuario Active Directory ingresado1.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }*/

        return true;
    }


    private function _ValidarModificar($datos) {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        $datosBuscar['Cuil'] = $datos['Cuil'];
        $oPersonas = new cServiciosPersonas($this->conexion);
        $result = $oPersonas->ObtenerPersona($datosBuscar);


        if (false === $result) {
            $this->setError($oPersonas->getError());
            return false;
        }

        /*if (empty($result['filas'][0])) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"No se encuentra la persona",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

        if (!empty($result['filas']) && !empty($result['filas'][0]) && $result['filas'][0]['IdPersona'] != $datos['IdPersona']&& $result['Estado']==10) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error ya existe otra persona con ese cuil.', array('archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__), array('formato' => $this->formato));
            return false;
        }


        $datosBuscar2['Dni'] = $datos['Dni'];
        $oPersonas = new cServiciosPersonas($this->conexion);
        $result = $oPersonas->ObtenerPersona($datosBuscar2);
        if (false === $result) {
            $this->setError($oPersonas->getError());
            return false;
        }

        if (!empty($result['filas']) && !empty($result['filas'][0]) && $result['filas'][0]['IdPersona'] != $datos['IdPersona'] && $result['Estado']==10) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error ya existe otra persona con ese dni.', array('archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__), array('formato' => $this->formato));
            return false;
        }

        return true;
    }


    private function _ValidarEliminar($datos) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un c&oacute;digo valido.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }
        return true;
    }


    private function _SetearNull(&$datos) {

        if (!isset($datos['IdPersona']) || $datos['IdPersona'] == "")
            $datos['IdPersona'] = "NULL";

        if (!isset($datos['Nombre']) || $datos['Nombre'] == "")
            $datos['Nombre'] = "NULL";

        if (!isset($datos['Apellido']) || $datos['Apellido'] == "")
            $datos['Apellido'] = "NULL";

        if (!isset($datos['Cuil']) || $datos['Cuil'] == "")
            $datos['Cuil'] = "NULL";

        if (!isset($datos['Dni']) || $datos['Dni'] == "")
            $datos['Dni'] = "NULL";

        if (isset($datos['Subject']) && $datos['Subject'] == "")
            $datos['Dni'] = "Subject";



        return true;
    }


    private function _ValidarDatosVacios($datos) {


        if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un nombre", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        if (!isset($datos['Apellido']) || $datos['Apellido'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un apellido", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        if (!isset($datos['Cuil']) || $datos['Cuil'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un cuil", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Cuil'], "CUIT")) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, utf8_decode("Error debe ingresar un cuit valido."), array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
                return false;
            }

        }
        /*if (!isset($datos['Dni']) || $datos['Dni']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un dni",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/
        if (isset($datos['Email']) && $datos['Email'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Email'], "Email")) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, utf8_decode("Error debe ingresar un Email valido."), array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
                return false;
            }

        }


        return true;
    }

    private function _ValidarDatosVaciosUsuarioRolAgente($datos)
    {
        if (!isset($datos['Cuil']) || $datos['Cuil'] == "") {
            $error = "Debe ingresar un cuil";
            $this->setError(400,$error);
            return false;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Cuil'], "CUIT")) {
                $error = "Error debe ingresar un cuit valido.";
                $this->setError(400,$error);
                return false;
            }

        }

        if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
            $error = "Debe ingresar un nombre";
            $this->setError(400,$error);
            return false;
        }

        if (!isset($datos['Apellido']) || $datos['Apellido'] == "") {
            $error = "Debe ingresar un apellido";
            $this->setError(400,$error);
            return false;
        }

        if (!isset($datos['Sexo']) || $datos['Sexo'] == "") {
            $error = "Debe ingresar un sexo";
            $this->setError(400,$error);
            return false;
        }


        /*if (!isset($datos['Dni']) || $datos['Dni']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un dni",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/
        if (isset($datos['Email']) && $datos['Email'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Email'], "Email")) {
                $error = "Error debe ingresar un Email valido.";
                $this->setError(400,$error);
                return false;
            }

        }

        if (!isset($datos['password']) || $datos['password'] == "") {
            $error = "Debe ingresar una contraseña";
            $this->setError(400,$error);
            return false;
        }

        if (!isset($datos['password_confirmation']) || $datos['password_confirmation'] == "") {
            $error = "Debe ingresar la confirmacion de la contraseña";
            $this->setError(400,$error);
            return false;
        }


        return true;
    }

    function _ValidarRoles($datos) {


        //recojo los roles de los check de roles
        $oUsuarios_Roles = new cUsuariosRoles($this->conexion);
        if (!$oUsuarios_Roles->ObtenerDatosCheckRoles($datos, $arrayfinal))
            return false;

        //si no hay al menos un rol muestro error
        if (count($arrayfinal) < 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRSOSP, "Debe seleccionar al menos un rol. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }
        return true;
    }
    //-----------------------------------------------------------------------------------------
// Genera una password nueva para ser enviada al usuario

// Parámetros de Entrada:
//		usuariocuit/usuariomail: datos a buscar en la base

// Retorna:
//		nuevapwd: la clave nueva generada por el sistema
//		la función retorna true o false si se pudo ejecutar con éxito o no

    function ReenviarContrasenia($datos) {
        $ArregloDatos['pEmail'] = $datos['pEmail'];
        if (!$this->BuscarUsuarios($ArregloDatos, $resultadousuarios, $numfilas))
            return false;

        if ($numfilas != 1) {
            return "Error al buscar el usuario.";
        }

        $usuario = $this->conexion->ObtenerSiguienteRegistro($resultadousuarios);
        if ($usuario["IdEstado"] > USUARIOACT) {
            return "No se pudo enviar una contraseña nueva. Su usuario no se encuentra activo ";
        } else {

            $nuevapwd = FuncionesPHPLocal::GenerarPassword(8);
            $datos['Password'] = $nuevapwd;
            $datos['IdEstado'] = USUARIONUEVO;//$usuario['usuarioestado'];
            $datos['IdUsuario'] = $usuario['IdUsuario'];

            if (!parent::CambiarPassword($datos, $nuevapwd))
                return false;
            $datos = $usuario;
            $datos['Password'] = $nuevapwd;
            $oMails = new cMails($this->conexion, $this->formato);
            if (!$oMails->MailReenvioContrasenia($datos))
                return false;
        }


        return true;

    }

    public function BuscarUsuarios($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarUsuarios($datos, $resultado, $numfilas))
            return false;
        return true;
    }




    public function buscarxUsuarioxRolxEscuela($datos, &$resultado, &$numfilas): bool {

        return parent::buscarxUsuarioxRolxEscuela($datos, $resultado, $numfilas);
    }

    protected function CrearModificarPersona ($datos) {

        $cuilEncontrado = false;
        $oPersonas = new cPersonas($this->conexion,$this->formato);
        if (!FuncionesPHPLocal::isEmpty($datos['Cuil'])) {

            if (!$oPersonas->buscarxCuil($datos, $resultado, $numfilas)) {
                $this->setError(400,"Error al buscar la persona por Cuil");
                return false;
            }
            if ($numfilas >= 1)
                $cuilEncontrado = true;
        }


        if (!$cuilEncontrado) {
            if (!$oPersonas->buscarxDni($datos, $resultado, $numfilas)) {
                $this->setError(400,"Error al buscar la persona por DNI");
                return false;
            }
        }

        if ($numfilas == 1) {

            $datosPersona = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $datos['IdPersona'] = (int)$datosPersona['IdPersona'];
            if (empty($datos['IdEstadoPersona']))
                $datos['IdEstadoPersona'] = (int)$datosPersona['IdEstadoPersona'];

            if (empty($datos['IdTipoDocumento']))
                $datos['IdTipoDocumento'] = (int)$datosPersona['IdTipoDocumento'];

            $datos['DNI'] = $datos['Dni'];
            $datos['CUIL'] = $datos['Cuil'];

            $datos["Calle"] = $datosPersona["Calle"];
            $datos["NumeroPuerta"] = $datosPersona["NumeroPuerta"];
            $datos["Piso"] = $datosPersona["Piso"];
            $datos["Depto"] = $datosPersona["Depto"];
            $datos["CodigoPostal"] = $datosPersona["CodigoPostal"];
            $datos["IdDepartamento"] = $datosPersona["IdDepartamento"];
            $datos["IdProvincia"] = $datosPersona["IdProvincia"];
            $datos["IdLocalidad"] = $datosPersona["IdLocalidad"];
            $datos["IdRegion"] = $datosPersona["IdRegion"];
            $datos["FechaIngreso"] = FuncionesPHPLocal::ConvertirFecha($datosPersona["FechaIngreso"], "aaaa-mm-dd", "dd/mm/aaaa");
            $datos["FallecidoFecha"] = FuncionesPHPLocal::ConvertirFecha($datosPersona["FallecidoFecha"], "aaaa-mm-dd", "dd/mm/aaaa");

            //actualizar
            if (!$oPersonas->ModificarPersona($datos)) {
                $error = $oPersonas->getError()["error_description"];
                $this->setError(400,$error);
                return false;
            }

        } else {

            $datos['IdEstadoPersona'] = 1;
            $datos['IdTipoDocumento'] = 1;
            $datos['DNI'] = $datos['Dni'];
            $datos['CUIL'] = $datos['Cuil'];
            if (!$oPersonas->InsertarPersona($datos, $codigoInsertado)) {
                $error = $oPersonas->getError()["error_description"];
                $this->setError(400,$error);
                return false;
            }
            $datos['IdPersona'] = $codigoInsertado;

        }

        return $datos['IdPersona'];
    }


    public function ObtenerUsuariosSubject($datos, &$resultado, &$numfilas) {
        if (!parent::ObtenerUsuariosSubject($datos, $resultado, $numfilas))
            return false;
        return true;
    }

}

?>
