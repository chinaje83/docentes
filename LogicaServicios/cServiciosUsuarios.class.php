<?php

use Bigtree\ExcepcionLogica;

class cServiciosUsuarios
{
    use ManejoErrores;
    protected $oCurl;
    protected $conexion;
    protected $error;
    protected $Utf8;

    protected $MemCache;

    const MemCacheExpire = 86400;// 1 dia

    function __construct($conexion){
        $this->conexion = &$conexion;
        $this->error = array();
        $this->oCurl = new CurlBigtree();
        $this->Utf8 = false;

    }

    public function __destruct() {
        $this->oCurl->CloseCurl();
        unset($this->oCurl);
    }

    public function getCurl()
    {
        return 	$this->oCurl;
    }

    public function CodificarUtf8()
    {
        $this->Utf8 = true;
    }


    public function ObtenerUsuarios($datos)
    {
        $oUsuarios = new cUsuarios($this->conexion,"");

        if(!$oUsuarios->ObtenerUsuariosCantidad($datos,$resultadoCant,$numfilasCant))
            return false;

        $fila = $this->conexion->ObtenerSiguienteRegistro($resultadoCant);
        $total = $fila['Total'];




        $size =  $datos['rows']??10;
        $page =  $datos['page']??1;


        $start = $size*$page - $size;
        if( $start<0 )
            $start = 0;

        $datos['limit'] = "LIMIT ".$start." , ".$size;

        $sidx = "U.IdUsuario";
        $sord = "ASC";
        if (isset($datos['sidx']))
            $sidx = $datos['sidx'];
        else
            $sidx = 'Id';

        if (isset($datos['sord']))
            $sord = $datos['sord'];
        else
            $sord = 'ASC';


        $datos['orderby'] = $sidx." ".$sord;
        if(!$oUsuarios->ObtenerUsuarios($datos,$resultado,$numfilas))
            return false;

        $usuarios = array();
        while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)){
            $usuarios[] = $fila;
        }

        $array = [
            'total' => $total,
            'size' => $size,
            'from' => ($size * ($page - 1)),
            'filas' => $usuarios
        ];


        return $array;
    }

    /**
     * @param int $id
     *
     * @return string
     * @throws \Bigtree\ExcepcionLogica
     */
    public function obtenerThumbnailUsuarioxId(int $id): string {

        $url = sprintf("v1/usuarios/%d/thumbnail", $id);

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . '-' . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult))
            throw new ExcepcionLogica('Ocurrió un error al buscar el usuario por código');

        return $dataResult['Avatar'];
    }

    public function ObtenerUsuarioxId($datos)
    {
       /* $url = "v1/usuarios/".$datos['Id'];

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Ocurrió un error al buscar el usuario por código");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;*/
        $oUsuarios = new cUsuarios($this->conexion,"");
        $datos['IdUsuario'] = $datos['Id'];
        if(!$oUsuarios->ObtenerUsuarioxIdUsuario($datos,$resultado,$numfilas))
            return false;

         if($numfilas!=1)
             throw new ExcepcionLogica('Ocurrió un error al buscar el usuario por código');

         $array = $this->conexion->ObtenerSiguienteRegistro($resultado);
         $array['Avatar'] = "";

        if (!empty($array['UbicacionAvatar']) && file_exists(PATH_STORAGE.CARPETA_SERVIDOR_MULTIMEDIA_AVATAR.CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_L.$array['UbicacionAvatar'])) {
            $dataFile = file_get_contents(PATH_STORAGE.CARPETA_SERVIDOR_MULTIMEDIA_AVATAR.CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_L.$array['UbicacionAvatar']);
            $array['Avatar'] = base64_encode($dataFile);
        }

          $array['Roles'] = [];

         $oUsuariosRolesDistritos = new cUsuariosRolesDistritos($this->conexion,"");

         if(!$oUsuariosRolesDistritos->BuscarxIdUsuario($datos,$resultadoRoles,$numfilasRoles))
             return false;

         while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoRoles)){
             $fila['RolAsignado'] = true;
             $array['Roles'][] = $fila;
         }


        return $array;
    }

    public function CrearUsuario($datos, &$codigoInsertado)
    {

        $oUsuarios = new cUsuarios($this->conexion,"");


        if(!$oUsuarios->InsertarUsuario($datos))


        if (!$this->_ValidarDatosVacios($datos))
            return false;




        /*$url = "v1/usuarios";

        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);

        $arrayHeader = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");

        $this->oCurl->setHeader($arrayHeader);

        $dataEnviar = new stdClass();
        $dataEnviar->DNI = $datos['Dni'];
        $dataEnviar->CUIL = $datos['Cuil'];
        $dataEnviar->Sexo = $datos['Sexo'];
        $dataEnviar->Nombre = utf8_encode($datos['Nombre']);
        $dataEnviar->Apellido = utf8_encode($datos['Apellido']);
        $dataEnviar->NombreCompleto = utf8_encode($datos['Nombre'].' '.$datos['Apellido']);
        $dataEnviar->Email = $datos['Email'];
        $dataEnviar->Telefono = $datos['Telefono'];
        $dataEnviar->IdTipoDocumento = 1;
        if (isset($datos['Roles']) && is_array($datos['Roles']))
            $dataEnviar->Roles = $datos['Roles'];

        $dataEnviar->Roles =  array();

        $cuerpo = json_encode($dataEnviar);

        $this->oCurl->setDebug(false);

        if(!$this->oCurl->sendPost($cuerpo,$dataResult))
        {
            $this->setError($dataResult['error'],$dataResult['error_description']);
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error']))
        {
            $this->setError($array['error'],$array['error_description']);
            return false;
        }*/

        $codigoInsertado = $array['IdUsuario'];
        return $array;
    }


    public function ModificarUsuario($datos)
    {

        $dataEnviar = new stdClass();
        $dataEnviar->Roles = $datos['Roles'];

        if (isset($datos['PassAnterior']) && $datos['PassAnterior'] != "")
            $dataEnviar->PassAnterior = $datos['PassAnterior'];

        if (isset($datos['Password']) && $datos['Password'] != "")
            $dataEnviar->Password = $datos['Password'];

        if (isset($datos['PassAnterior']) && $datos['PassAnterior'] != "" || isset($datos['Password']) && $datos['Password'] != "")
            if (!$this->_ValidarPasswords($datos))
                return false;

        $cuerpo = json_encode($dataEnviar);

        $url = "v1/usuarios/".$datos['Id'];

        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $arrayHeader = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);

        if(!$this->oCurl->sendPut($cuerpo,$dataResult))
        {
            $this->setError("Error","Ocurrió un error al modificar usuario");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'],$array['error_description']);
            return false;
        }

        return $array;
    }

    public function ModificarContrasenia($datos)
    {
        $datos['Roles'] = $this->ObtenerRolesUsuario($datos);

        $dataEnviar = new stdClass();
        $dataEnviar->Roles = $datos['Roles'];

        if (isset($datos['Password']) && $datos['Password'] != "")
            $dataEnviar->Password = $datos['Password'];

        if (isset($datos['PassAnterior']) && $datos['PassAnterior'] != "" || isset($datos['Password']) && $datos['Password'] != "")
            if (!$this->_ValidarPasswords($datos))
                return false;

        $cuerpo = json_encode($dataEnviar);

        $url = "v1/usuarios/".$datos['Id'];

        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $arrayHeader = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);

        if(!$this->oCurl->sendPut($cuerpo,$dataResult))
        {
            $this->setError($dataResult['error'],$dataResult['error_description']);
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'],$array['error_description']);
            return false;
        }

        return $array;
    }



    public function ActivarUsuario($datos)
    {
        $url = "v1/usuarios/".$datos['Id']."/activar";

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
       // $this->oCurl->setDebug(true);
        if(!$this->oCurl->sendPatch($url,$dataResult))
        {
            $this->setError($dataResult['error'],$dataResult['error_description']);
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    public function DesactivarUsuario($datos)
    {
        $url = "v1/usuarios/".$datos['Id']."/desactivar";

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
       // $this->oCurl->setDebug(true);
        if(!$this->oCurl->sendPatch($url,$dataResult))
        {
            $this->setError($dataResult['error'],$dataResult['error_description']);
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    public function ModificarNivel($datos)
    {
        $url = "v1/usuarios/".$datos['Id']."/modificar-nivel";

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
       // $this->oCurl->setDebug(true);
        if(!$this->oCurl->sendPatch($datos,$dataResult))
        {
            $this->setError($dataResult['error'],$dataResult['error_description']);
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }


    public function reenviarMail($datos): bool {

        $url = "v1/mail/reenvio";

        $dataEnviar = new stdClass();
        $dataEnviar->IdPersona = $datos['IdPersona'];
        $dataEnviar->IdUsuario = $datos['IdUsuario'];
        $dataEnviar->Tipo = $datos['Tipo'];
        $cuerpo = json_encode($dataEnviar);

        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendPost($cuerpo,$dataResult)) {
            $this->setError($dataResult['error'],$dataResult['error_description']);
            return false;
        }

        return true;
    }

    public static function preprocesarDatosElastic(array $datos): array {
        $datos['Tabla'] = 'Usuarios';
        $datos['Identificadores'] = [$datos['Cuil'], $datos['Dni']];
        return $datos;
    }

    private function ObtenerRolesUsuario($datos)
    {
        $ArrayUsuarios = $this->ObtenerUsuarioxId($datos);
        $RolesDatos = array();
        $RolesArray = array();
        $Roles      = array();

        if (isset($datos['Roles']))
            foreach ($datos['Roles'] as $res)
                $RolesDatos[] = $res;

        if (isset($ArrayUsuarios['Roles']))
            foreach ($ArrayUsuarios['Roles'] as $res)
                if ($res['RolAsignado'] != "")
                    $RolesArray[] = $res['IdRol'];

        $Roles = array_unique(array_merge($RolesArray, $RolesDatos));

        return $Roles;
    }

    private function _ValidarPasswords($datos)
    {
        if ($datos['Password'] != $datos['PassConfirmed']) {
	        $this->setError(400,'Contraseña de confirmación debe ser idéntica.');
            return false;
        }

        return true;
    }


    private function _ValidarDatosVacios($datos)
    {
        if (!isset($datos['Dni']) || $datos['Dni'] == "") {
            $this->setError(400,'Debe ingresar Dni');
            return false;
        }

        if (!isset($datos['Cuil']) || $datos['Cuil'] == "") {
                $this->setError(400, 'Debe ingresar Cuil');
            return false;
        }

        if (!isset($datos['Sexo']) || $datos['Sexo'] == "") {
            $this->setError(400, 'Falta seleccionar un sexo');
            return false;
        }

        if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
            $this->setError(400, 'Debe ingresar nombre');
            return false;
        }

        if (!isset($datos['Apellido']) || $datos['Apellido'] == "") {
            $this->setError(400, 'Debe ingresar apellido');
            return false;
        }

        if (!isset($datos['Email']) || $datos['Email'] == "") {
            $this->setError(400, 'Debe ingresar un email');
            return false;
        }

        if (!isset($datos['Telefono']) || $datos['Telefono'] == "") {
            $this->setError(400, utf8_encode('Debe ingresar un teléfono'));
            return false;
        }

        return true;
    }
}
