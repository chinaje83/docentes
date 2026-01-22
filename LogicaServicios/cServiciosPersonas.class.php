<?php
require_once DIR_ROOT . '/config/include_elastic.php';

use Elastic\Puestos;
use Bigtree\OAuth\User;


class cServiciosPersonas {
    use ManejoErrores;

    protected $oCurl;
    protected $conexion;
    protected $error;
    protected $Utf8;
    protected $formato;
    protected $MemCache;

    const MemCacheExpire = 86400;// 1 dia

    function __construct($conexion) {
        $this->conexion = &$conexion;
        $this->error = [];
        $this->oCurl = new CurlBigtree();
        $this->Utf8 = false;

    }

    public function __destruct() {
        $this->oCurl->CloseCurl();
        unset($this->oCurl);
    }

    public function getCurl() {
        return $this->oCurl;
    }

    public function CodificarUtf8() {
        $this->Utf8 = true;
    }

    public function ObtenerPersona($datos) {
        $url = "v1/personas";

        $urlAnexa = "";

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "CUIL=" . $datos['Cuil'];
        }

        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "DNI=" . $datos['Dni'];
        }

        if (isset($datos['NombreCompleto']) && $datos['NombreCompleto'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "NombreCompleto=" . $datos['NombreCompleto'];
        }

        if (isset($datos['IdPersona']) && $datos['IdPersona'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "IdPersona=" . $datos['IdPersona'];
        }

        if (isset($datos['page']) && $datos['page'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "page=" . $datos['page'];
        }

        if (isset($datos['rows']) && $datos['rows'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "rows=" . $datos['rows'];
        }

        if (isset($datos['sidx']) && $datos['sidx'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "sidx=" . $datos['sidx'];
        }

        if (isset($datos['sord']) && $datos['sord'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "sord=" . $datos['sord'];
        }

        $url .= $urlAnexa;
        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar usuarios");
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    public function obtenerAgentes($datos, ?array &$resultado) {
        $url = "v1/personas/agentes";

        $params = [];

        if (!empty($datos['Estado'])) {
            $params['Estado'] = $datos['Estado'];
        }

        if (!empty($datos['Cuil'])) {
            $params['CUIL'] = $datos['Cuil'];
        }

        if (!empty($datos['Dni'])) {
            $params['DNI'] = $datos['Dni'];
        }

        if (!empty($datos['NombreCompleto'])) {
            $params['NombreCompleto'] = $datos['NombreCompleto'];
        }

        if (!empty($datos['Sexo'])) {
            $params['Sexo'] = $datos['Sexo'];
        }

        if (!empty($datos['Email'])) {
            $params['Email'] = $datos['Email'];
        }

        if (!empty($datos['Id'])) {
            $params['IdPersona'] = $datos['Id'];
        }

        if (!empty($datos['page'])) {
            $params['page'] = $datos['page'];
        }

        if (!empty($datos['rows'])) {
            $params['rows'] = $datos['rows'];
        }

        if (!empty($datos['sidx'])) {
            $params['sidx'] = $datos['sidx'];
        }

        if (!empty($datos['sord'])) {
            $params['sord'] = $datos['sord'];
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar personas con puestos");
            return false;
        }

        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;

        return true;

    }

    public function ObtenerPersonaxId($datos) {
        /*$url = "v1/personas/" . $datos['IdPersona'];

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar el usuario por código");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;*/

        $oPersonas = new cPersonas($this->conexion, "");

        if (!$oPersonas->buscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(404, 'Not Found', 'Persona inexistente');
            return false;
        }

        $datosPersona = $this->conexion->ObtenerSiguienteRegistro($resultado);


        $avatar = file_get_contents(PATH_STORAGE . 'avatars/avatar-l/default.png');
        //print_r($datosPersona);die;
        $ubicacionAvatar = PATH_STORAGE . "avatars/avatar-l/" . $datosPersona['UbicacionAvatar'];
        if (!FuncionesPHPLocal::isEmpty($datosPersona['UbicacionAvatar']) && file_exists($ubicacionAvatar)) {
            $avatar = file_get_contents($ubicacionAvatar);
        }
        unset($datosPersona['Password'], $datosPersona['UbicacionAvatar'], $datosPersona['RegistroSeguridad']);
        //$datos['IdAplicacion'] = $_SESSION['IdAplicacion'];

        $datosPersona['Avatar'] = base64_encode($avatar);

        return $datosPersona;

    }


    public function ObtenerEstados(&$resultado) {
        $url = 'v1/combos/personas_estados';

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar el usuario por código");
            return false;
        }

        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;

        return true;
    }


    public function CrearPersona($datos, &$codigoInsertado) {

        if (!$this->_ValidarDatosVacios($datos))
            return false;

        $url = "v1/personas";

        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setHeader($arrayHeader);

        $dataEnviar = new stdClass();
        $dataEnviar->IdEstadoPersona = $datos['IdEstadoPersona'] ?? 1;
        $dataEnviar->IdTipoDocumento = utf8_encode($datos['IdTipoDocumento']);
        $dataEnviar->DNI = utf8_encode($datos['DNI']);
        $dataEnviar->CUIL = utf8_encode($datos['CUIL']);
        $dataEnviar->Sexo = utf8_encode($datos['Sexo']);
        $dataEnviar->Nombre = utf8_encode($datos['Nombre']);
        $dataEnviar->Apellido = utf8_encode($datos['Apellido']);
        $dataEnviar->NombreCompleto = utf8_encode($datos['Nombre'] . ' ' . $datos['Apellido']);
        $dataEnviar->Email = utf8_encode($datos['Email']);
        $dataEnviar->Telefono = $datos['Telefono'];
        $dataEnviar->Calle = $datos['Calle'];
        $dataEnviar->NumeroPuerta = $datos['NumeroPuerta'];
        $dataEnviar->Piso = $datos['Piso'];
        $dataEnviar->Depto = $datos['Depto'];
        $dataEnviar->CodigoPostal = $datos['CodigoPostal'];
        $dataEnviar->IdDepartamento = $datos['IdDepartamento'];
        $dataEnviar->IdProvincia = $datos['IdProvincia'];
        $dataEnviar->IdLocalidad = $datos['IdLocalidad'];
        $dataEnviar->IdRegion = $datos['IdRegion'];
        // if(isset($datos['FallecidoFecha']) && $datos['FallecidoFecha']!="")
        //    $dataEnviar->FallecidoFecha = FuncionesPHPLocal::ConvertirFecha($datos['FallecidoFecha'],'dd/mm/aaaa','aaaa-mm-dd');
        if (isset($datos['FechaNacimiento']) && $datos['FechaNacimiento'] != "")
            $dataEnviar->FechaNacimiento = utf8_encode($datos['FechaNacimiento']);
        if (isset($datos['FechaIngreso']) && $datos['FechaIngreso'] != "")
            $dataEnviar->FechaIngreso = utf8_encode($datos['FechaIngreso']);

        if (isset($datos['FallecidoFecha']) && $datos['FallecidoFecha'] != "")
            $dataEnviar->FallecidoFecha = utf8_encode($datos['FallecidoFecha']);
        $dataEnviar->Size = $datos['Size'];
        $dataEnviar->Name = $datos['Name'];
        $dataEnviar->File = $datos['File'];

        if ($datos['File'] != "") {
            $dataEnviar->FileTmp = base64_encode(file_get_contents(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA . $datos['File']));
        }

        $cuerpo = json_encode($dataEnviar);

        if (!$this->oCurl->sendPost($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], $dataResult['error_description']);
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'], $array['error_description']);
            return false;
        }

        $codigoInsertado = $array['IdPersona'];
        return $array;
    }

    public function ActualizarPersonaFamiliar($datos) {

        $url = "v1/familiares-persona";

        $dataEnviar = new stdClass();
        $dataEnviar->IdPersona = $datos['IdPersona'];

        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(true);

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setHeader($arrayHeader);
        $cuerpo = json_encode($dataEnviar);

        if (!$this->oCurl->sendPost($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], $dataResult['error_description']);
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'], $array['error_description']);
            return false;
        }

        return $array;
    }


    public function getAvatarxIdpersona($datos) {
        $url = "v1/avatar/" . $datos['IdPersona'];

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar usuario.");
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }


    public function ModificarPersona($datos, $modificar_usuario_sso = false) {


        if (isset($datos['FileTmp']) && $datos['FileTmp'] != "")
            file_put_contents(PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_TMP . $datos['File'], base64_decode($datos['FileTmp']));


        $datosPersona = $this->ObtenerPersonaxId($datos);
        if (false === $datosPersona)
            return false;

        $oPersonas = new cPersonas($this->conexion, FMT_ARRAY);
        if (!$oPersonas->ModificarPersona($datos)) {
            $error = $oPersonas->getError();
            $this->setError(400, $error['error_description']);
            return false;
        }

        if ($modificar_usuario_sso) {
            $oUsuarios = new cUsuarios($this->conexion);
            $datosBuscarUsuario['IdPersona'] = $datos['IdPersona'];
            if (!$oUsuarios->ObtenerUsuarios($datosBuscarUsuario, $resultadoUsuario, $numfilasUsuario))
                return false;

            if ($numfilasUsuario == 1) {

                $filaUsuario = $this->conexion->ObtenerSiguienteRegistro($resultadoUsuario);
                $datosBuscarUsuario['IdUsuario'] = $filaUsuario['Id'];

                if (!$oUsuarios->BuscarxCodigo($datosBuscarUsuario, $resultadoUsuario, $numfilasUsuario))
                    return false;

                $filaUsuario = $this->conexion->ObtenerSiguienteRegistro($resultadoUsuario);
                $datosSSo = $datos;
                $datosSSo['id'] = $filaUsuario['Subject'];
                $datosSSo["Cuil"] = $datos["CUIL"];
                $datosSSo["Dni"] = $datos["DNI"];

                $oUser = new User();
                try {
                    $oUser->modificarxCodigo($datosSSo);
                } catch (\Bigtree\ExcepcionLogica $e) {
                    $error = $e->getMessage();
                    $this->setError(400, $error);
                    //$error = "Error al modifcar el usuario en el sso";
                    FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRSOSP, $error, ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                    return false;
                }
            }
        }

        $oTiposDocumentos = new cServiciosTiposDocumentos($this->conexion);
        $oTiposDocumentos->CodificarUtf8();
        $datosBuscar = [];
        $datosTiposDocumentos = $oTiposDocumentos->ObtenerTiposDocumentos($datosBuscar);
        $tiposDocumento = [];
        if (false !== $datosTiposDocumentos) {
            foreach ($datosTiposDocumentos['filas'] as ['Id' => $id, 'Nombre' => $nombre])
                $tiposDocumento[(int)$id] = $nombre;
        }
        $datosBuscar['IdPersona'] = $datos['IdPersona'];
        $datosBuscar['excluirCampos'] = ['*'];
        $conexionES = new Elastic\Conexion();
        $oPofa = new Puestos($conexionES);
        if (!$oPofa->buscarPuestosPersonasxIdPersona($datosBuscar, $resultadoBusqueda, $cantidadResultados, $totalResultados)) {
            $this->setError($oPofa->getError());
            return false;
        }

        $bulkData = '';
        $Action_and_MetaData = new StdClass;
        $Action_and_MetaData->update = new StdClass;
        $Action_and_MetaData->update->_index = Puestos::getIndex();
        $source = new stdClass();
        $source->doc = new stdClass();
        if ($cantidadResultados > 0) {
            foreach ($resultadoBusqueda as ['_id' => $id, '_routing' => $routing]) {
                $Action_and_MetaData->update->_id = $id;
                $Action_and_MetaData->update->routing = $routing;
                $source->doc->Nombre = mb_convert_encoding($datos['Nombre'], 'UTF-8', 'ISO-8859-1');
                $source->doc->Apellido = mb_convert_encoding($datos['Apellido'], 'UTF-8', 'ISO-8859-1');
                if (!BLOQUEA_DNI) {
                    $source->doc->Documento = new stdClass();
                    $source->doc->Documento->Tipo = new stdClass();
                    $source->doc->Documento->Tipo->Id = $datos['IdTipoDocumento'];
                    $source->doc->Documento->Tipo->Descripcion = $tiposDocumento[$datos['IdTipoDocumento']] ?? '';
                    $source->doc->Documento->Numero = $datos['DNI'];
                }
                if (!BLOQUEA_CUIL)
                    $source->doc->CUIL = $datos['CUIL'];

                $bulkData .= json_encode($Action_and_MetaData) . "\n";
                $bulkData .= json_encode($source) . "\n";
            }
        }

        //var_dump($bulkData);die;

        if (!empty($bulkData)) {
            $oElastic = new Elastic\Modificacion('', $conexionES);
            if (!$oElastic->ActualizarBulk($bulkData)) {
                $this->setError($oElastic->getError());
                return false;
            }
        }


        $respuesta = [
            'IdPersona' => $datos['IdPersona'],
            'success' => true,
        ];
        return $respuesta;

    }

    public function modificarEstado($datos) {

        $url = "v1/personas/" . $datos['IdPersona'];

        $dataEnviar = new stdClass();
        $dataEnviar->ModificaEstado = true;
        $dataEnviar->IdPersona = $datos['IdPersona'];
        $dataEnviar->IdEstadoPersona = $datos['IdEstadoPersona'];
        $dataEnviar->BajaFecha = $datos['BajaFecha'] ?? 'NULL';
        $dataEnviar->FallecidoFecha = $datos['FallecidoFecha'] ?? 'NULL';
        $cuerpo = json_encode($dataEnviar);

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPut($cuerpo, $dataResult)) {
            $this->setError($dataResult);
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'], $array['error_description']);
            return false;
        }

        return $array;
    }


    public function modificarPerfil($datos) {

        $url = "v1/mi-perfil/";

        $dataEnviar = new stdClass();
        $dataEnviar->IdPersona = $datos['IdPersona'];
        $dataEnviar->ModificaEstado = true;
        $dataEnviar->Email = $datos['Email'];
        $dataEnviar->Telefono = $datos['Telefono'];
        if (isset($datos['FechaNacimiento']) && $datos['FechaNacimiento'] != "")
            $dataEnviar->FechaNacimiento = FuncionesPHPLocal::ConvertirFecha($datos['FechaNacimiento'], "dd/mm/aaaa", "aaaa-mm-dd");
        $dataEnviar->Calle = $datos['Calle'];
        $dataEnviar->NumeroPuerta = $datos['NumeroPuerta'];
        $dataEnviar->Piso = $datos['Piso'];
        $dataEnviar->Depto = $datos['Depto'];
        $dataEnviar->CodigoPostal = $datos['CodigoPostal'];
        $dataEnviar->IdDepartamento = $datos['IdDepartamento'];
        $dataEnviar->IdProvincia = $datos['IdProvincia'];
        $dataEnviar->IdLocalidad = $datos['IdLocalidad'];
        $dataEnviar->IdRegion = $datos['IdRegion'];
        $dataEnviar->FileTmp = $datos['File'];
        $dataEnviar->File = $datos['File'];
        $dataEnviar->Name = $datos['Name'];
        $dataEnviar->Size = $datos['Size'];
        if ($datos['File'] != "") {
            $dataEnviar->Avatar = base64_encode(file_get_contents(DIR_ROOT . '/multimedia/tmp/' . $datos['File']));
        }

        $cuerpo = json_encode($dataEnviar);

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPut($cuerpo, $dataResult)) {
            $this->setError($dataResult);
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'], $array['error_description']);
            return false;
        }

        return $array;
    }

    public function ActivarPersona($datos) {
        $url = "v1/personas/" . $datos['IdPersona'] . "/activar";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        // $this->oCurl->setDebug(true);
        if (!$this->oCurl->sendPatch($url, $dataResult)) {
            $this->setError($dataResult['error'], $dataResult['error_description']);
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    public function DesactivarPersona($datos) {


        if (!$this->_ValidarPuestoPersona($datos))
            return false;


        $url = "v1/personas/" . $datos['IdPersona'] . "/desactivar";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        // $this->oCurl->setDebug(true);
        if (!$this->oCurl->sendPatch($url, $dataResult)) {
            $this->setError($dataResult['error'], $dataResult['error_description']);
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    public function EliminarAvatar($datos) {
        $url = "v1/personas/" . $datos['IdPersona'] . "/eliminaravatar";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendPatch($url, $dataResult)) {
            $this->setError($dataResult['error'], $dataResult['error_description']);
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    /*private function _ValidarCUILxDni($datos){

            if(isset($datos['DNI']) && strlen($datos['DNI']) > 8){
                $this->setError(400, 'Debe ingresar un DNI valido');
                return false;
            }

        $dni = $datos['DNI'];
        $cuil = $datos['CUIL'];


        $cuilDigitosMedio = substr($cuil, 2, 8);


        if (strcmp($dni, $cuilDigitosMedio) === 0) {

        } else {
            $this->setError(400, 'El DNI no coincide con el CUIL');
            return false;
        }

    }*/

    private function _ValidarPuestoPersona($datos) {
        // valido que no tenga un usuario asociado activo
        $oUsuarios = new cUsuarios($this->conexion, $this->formato);
        if (!$oUsuarios->BuscarxIdPersona($datos, $resultadoPersona, $numfilasPersona)) {
            return false;
        }

        if ($numfilasPersona > 0) {
            $this->setError(400, 'El agente tiene asociado un usuario activo.');
            return false;
        }

        $oPuestosPersonas = new cEscuelasPuestosPersonas($this->conexion, $this->formato);

        if (!$oPuestosPersonas->BuscarxPuestoxIdPersona($datos, $resultado, $numfilas)) {
            return false;
        }

        if ($numfilas > 0) {
            $this->setError(400, 'El agente tiene puestos asociados, debe cesarlo de los mismos para poder eliminar');
            return false;
        }

        return true;
    }


    private function _ValidarDatosVacios($datos) {

        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == "") {
            $this->setError(400, 'Debe ingresar Tipo Documento');
            return false;
        }

        if (!isset($datos['DNI']) || $datos['DNI'] == "") {
            $this->setError(400, 'Debe ingresar DNI');
            return false;
        }


        if (!isset($datos['CUIL']) || $datos['CUIL'] == "") {
            $this->setError(400, 'Debe ingresar CUIL');
            return false;
        }

        if (!isset($datos['Sexo']) || $datos['Sexo'] == "") {
            $this->setError(400, 'Falta seleccionar un sexo');
            return false;
        }

        if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
            $this->setError(400, 'Debe ingresar Nombre');
            return false;
        }

        if (!isset($datos['Apellido']) || $datos['Apellido'] == "") {
            $this->setError(400, 'Debe ingresar Apellido');
            return false;
        }

        if (!isset($datos['IdEstadoPersona']) || $datos['IdEstadoPersona'] == "") {
            $this->setError(400, 'Debe seleccionar un estado');
            return false;
        }

        /*
        if (!isset($datos['Email']) || $datos['Email'] == "") {
            $this->setError(400, 'Debe ingresar Email');
            return false;
        }


        if (!isset($datos['Telefono']) || $datos['Telefono'] == "") {
            $this->setError(400, 'Debe ingresar Teléfono');
            return false;
        }*/

        if (isset($datos['FallecidoFecha']) && $datos['FallecidoFecha'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FallecidoFecha'], "FechaDDMMAAAA")) {
                $this->setError(400, 'Debe ingresar una Fecha Fallecido Valida');
                return false;
            }

        }

        return true;
    }


    private function _ValidarDatosVaciosFamiliar($datos) {

        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == "") {
            $this->setError(400, 'Debe ingresar Tipo Documento');
            return false;
        }

        if (!isset($datos['Dni']) || $datos['Dni'] == "") {
            $this->setError(400, 'Debe ingresar DNI');
            return false;
        }


        if (!isset($datos['CUIL']) || $datos['CUIL'] == "") {
            $this->setError(400, 'Debe ingresar CUIL');
            return false;
        }

        if (!isset($datos['Sexo']) || $datos['Sexo'] == "") {
            $this->setError(400, 'Falta seleccionar un sexo');
            return false;
        }

        if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
            $this->setError(400, 'Debe ingresar Nombre');
            return false;
        }

        if (!isset($datos['Apellido']) || $datos['Apellido'] == "") {
            $this->setError(400, 'Debe ingresar Apellido');
            return false;
        }


        return true;
    }


    public function obtenerSexoLetra(?string $letra): ?string {
        return match (strtoupper($letra)) {
            'M', '1' => 'M',
            'F', '2' => 'F',
            'X', '3' => 'X',
            default  => null,
        };

    }

}
