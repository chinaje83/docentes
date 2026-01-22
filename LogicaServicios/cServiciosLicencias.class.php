<?php

use Elastic\Personas;

class cServiciosLicencias {
    use ManejoErrores;

    protected $oCurl;
    protected $conexion;
    protected $error;
    protected $Utf8;
    protected $MemCache;// 1 dia

    function __construct($conexion) {
        $this->conexion = &$conexion;
        $this->error = [];
        $this->oCurl = new CurlBigtree();
        $this->Utf8 = false;
    }

    static function getUrlDominio($tipo) {
        $url = "";
        switch ($tipo) {
            case 1:
                $url = 'medicas';
                break;
            case 2:
                $url = 'administrativas';
                break;
            case 3:
                $url = 'art';
                break;
            case 4:
                $url = 'maternidad';
                break;
            case 5:
                $url = 'inasistencias';
                break;
        }
        return $url;
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

    public function ObtenerLicencias($datos) {
        $url = "licencias";

        $urlAnexa = "";

        if (isset($datos['IdPersona']) && $datos['IdPersona'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "IdPersona=" . $datos['IdPersona'];
        }

        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "Nombre=" . $datos['Nombre'];
        }

        if (isset($datos['Inicio']) && $datos['Inicio'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "Inicio=" . $datos['Inicio'];
        }

        if (isset($datos['Fin']) && $datos['Fin'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "Fin=" . $datos['Fin'];
        }

        if (isset($datos['IdMotivo']) && $datos['IdMotivo'] != "") {
            if ($urlAnexa == "")
                $urlAnexa = "?";
            else
                $urlAnexa .= "&";

            $urlAnexa .= "IdMotivo=" . $datos['IdMotivo'];
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
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar licencias");
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    public function CrearLicencia($datos, &$codigoInsertado) {

        if (!$this->_ValidarInsertar($datos))
            return false;

        $this->_SetearNull($datos);

        $url = (isset($datos['enviar']) && $datos['enviar'] == 0 ? "licencias" : "licencias/enviar");
        $dataEnviar = new stdClass();
        $dataEnviar->AltaLicencia = $datos['AltaLicencia'];
        $dataEnviar->IdTipo = $datos['IdTipo'];
        $dataEnviar->IdPersona = $datos['IdPersona'];
        $dataEnviar->IdEstado = 1;
        $dataEnviar->FechaFinAbierta = $datos['FechaFinAbierta'];
        $dataEnviar->Inicio = $Inicio = FuncionesPHPLocal::ConvertirFecha($datos['Inicio'], "dd/mm/aaaa", "aaaa-mm-dd");

        if (isset($datos['Horas']) && $datos['Horas'] != "" && $datos['Horas'] != "0") {
            $dataEnviar->Duracion = $Dias = $datos['Horas'] / 24;
            $dataEnviar->Unidad = 0;

        } else {
            $dataEnviar->Duracion = $Dias = $datos['Duracion'];
            $dataEnviar->Unidad = $datos['Unidad'];
        }

        $dataEnviar->HorasAfectadas = $datos['HorasAfectadas'] ?? null;
        $dataEnviar->IdMotivo = $datos['IdMotivo'];
        $dataEnviar->IdMotivoDetalle = $datos['IdMotivoDetalle'] ?? null;
        $dataEnviar->IdDiagnostico = $datos['IdDiagnostico'] ?? null;
        $dataEnviar->IdDiagnosticoDetalle = $datos['IdDiagnosticoDetalle'] ?? null;
        $dataEnviar->Descripcion = $datos['Descripcion'] ?? null;

        if (isset($datos['Familiar']) && $datos['Familiar'] == 1) {
            $dataEnviar->Familiar = $datos['Familiar'];
            $dataEnviar->DatosFamiliar = new stdClass();
            $dataEnviar->DatosFamiliar->Id = $datos['IdFamiliar'];
            $dataEnviar->DatosFamiliar->IdParentesco = $datos['IdParentesco'];
        }

        $dataEnviar->IdEspecialidad = $datos['IdEspecialidad'] ?? null;
        $dataEnviar->IdArticuloSeleccionado = $datos['IdArticuloSeleccionado'] ?? null;
        $dataEnviar->IdAutorizante = $datos['IdAutorizante'] ?? null;
        $dataEnviar->Matricula = $datos['Matricula'] ?? null;
        $dataEnviar->Nombre = $datos['NombreAutorizante'] ?? null;
        $dataEnviar->Apellido = $datos['ApellidoAutorizante'] ?? null;
        $dataEnviar->EncontroAutorizante = $datos['EncontroAutorizante'] ?? 0;
        $dataEnviar->LicenciaAprobada = $datos['enviar'];
        $dataEnviar->rolActivo = (int)current($_SESSION['rolcod']);
        $dataEnviar->Estado = ACTIVO;
        $dataEnviar->Certificados = [];
        $i = 0;
        if (isset($datos['nombrearchivo']) && !empty($datos['nombrearchivo'])) {
            while ($i < count($datos['nombrearchivo'])) {
                $dataEnviar->Certificados[$i] = new stdClass();
                $dataEnviar->Certificados[$i]->Nombre = $datos['nombrearchivo'][$i];
                $dataEnviar->Certificados[$i]->Contenido = base64_encode(file_get_contents(PATH_STORAGE . 'tmp/' . $datos['nombrearchivotmp'][$i]));
                $i++;
            }
        }

        if (!empty($datos['IdPuestoAfectado']))
            $dataEnviar->IdPuesto = $datos['IdPuestoAfectado'];


        $dataEnviar->seleccionaCargos = $datos['seleccionaCargos'] ?? [];
        $dataEnviar->IdEscuelaSeleccionada = $datos['IdEscuelaSeleccionada'] ?? null;
        $dataEnviar->RolActivo = current($_SESSION['rolcod']);
        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPost($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], utf8_decode($dataResult['error_description']));
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

        $codigoInsertado = $array['Id'];
        return $array;
    }

    public function ModificarLicencia($datos) {

        if (!$this->_ValidarModificar($datos))
            return false;

        $this->_SetearNull($datos);

        $url = (isset($datos['enviar']) && $datos['enviar'] == 0 ? "licencias/" . $datos['Id'] : "licencias/" . $datos['Id'] . "/enviar");

        $dataEnviar = new stdClass();
        $dataEnviar->AltaLicencia = $datos['AltaLicencia'];
        $dataEnviar->Id = $datos['Id'];
        $dataEnviar->IdTipo = $datos['IdTipo'];
        $dataEnviar->IdPersona = $datos['IdPersona'];
        $dataEnviar->IdEstado = empty($datos['IdEstado']) ? 1 : $datos['IdEstado'];
        $dataEnviar->FechaFinAbierta = $datos['FechaFinAbierta'];
        $dataEnviar->Inicio = $Inicio = FuncionesPHPLocal::ConvertirFecha($datos['Inicio'], "dd/mm/aaaa", "aaaa-mm-dd");

        if ($datos['Horas'] != "0") {
            $dataEnviar->Duracion = $Dias = $datos['Horas'] / 24;;
            $dataEnviar->Unidad = 0;

        } else {
            $dataEnviar->Duracion = $Dias = $datos['Duracion'];
            $dataEnviar->Unidad = $datos['Unidad'];
        }
        $dataEnviar->DuracionHabiles = $datos['DuracionHabiles'] ?? null;
        $dataEnviar->IdMotivo = $datos['IdMotivo'];
        $dataEnviar->IdMotivoDetalle = $datos['IdMotivoDetalle'] ?? null;
        $dataEnviar->IdArticuloSeleccionado = $datos['IdArticuloSeleccionado'];
        $dataEnviar->ArticuloDescripcion = $datos['ArticuloDescripcion'] ?? null;
        $dataEnviar->IdDiagnostico = $datos['IdDiagnostico'] ?? null;
        $dataEnviar->IdDiagnosticoDetalle = $datos['IdDiagnosticoDetalle'] ?? null;
        $dataEnviar->Descripcion = $datos['Descripcion'] ?? null;

        if (isset($datos['Familiar']) && $datos['Familiar']) {
            $dataEnviar->Familiar = $datos['Familiar'];
            $dataEnviar->DatosFamiliar = new stdClass();
            $dataEnviar->DatosFamiliar->Id = $datos['IdFamiliar'];
            $dataEnviar->DatosFamiliar->IdParentesco = $datos['IdParentesco'];
        }

        if (isset($datos['NroResolucion']) && $datos['NroResolucion']) {
            $dataEnviar->NroResolucion = $datos['NroResolucion'];
        }

        $dataEnviar->IdEspecialidad = $datos['IdEspecialidad'] ?? null;
        $dataEnviar->IdAutorizante = $datos['IdAutorizante'] ?? null;
        $dataEnviar->EncontroAutorizante = $datos['EncontroAutorizante'] ?? 0;
        $dataEnviar->Matricula = $datos['Matricula'] ?? null;
        $dataEnviar->Nombre = $datos['NombreAutorizante'] ?? null;
        $dataEnviar->Apellido = $datos['ApellidoAutorizante'] ?? null;
        $dataEnviar->Estado = ACTIVO;
        $dataEnviar->rolActivo = (int)current($_SESSION['rolcod']);

        if (!empty($datos['IdPuestoAfectado'])) {
            $dataEnviar->IdPuesto = $datos['IdPuestoAfectado'];
            $dataEnviar->IdArticuloPuesto = $datos['IdArticuloPuesto'] ?? [];
        }

        $dataEnviar->seleccionaCargos = $datos['seleccionaCargos'];

        if (isset($datos['TareaPasiva']))
            $dataEnviar->TareaPasiva = $datos['TareaPasiva'];

        if (isset($datos['AptoFisico']))
            $dataEnviar->AptoFisico = $datos['AptoFisico'];


        $dataEnviar->Certificados = [];
        $i = 0;
        if (isset($datos['nombrearchivo']) && !empty($datos['nombrearchivo'])) {
            while ($i < count($datos['nombrearchivo'])) {
                $dataEnviar->Certificados[$i] = new stdClass();
                $dataEnviar->Certificados[$i]->Nombre = $datos['nombrearchivo'][$i];
                $dataEnviar->Certificados[$i]->Contenido = base64_encode(file_get_contents(PATH_STORAGE . 'tmp/' . $datos['nombrearchivotmp'][$i]));
                $i++;
            }
        }

        $i = 0;
        if (isset($datos['IdCertificados']) && !empty($datos['IdCertificados'])) {
            while ($i < count($datos['IdCertificados'])) {
                $dataEnviar->CertificadosEliminar[$i] = new stdClass();
                $dataEnviar->CertificadosEliminar[$i]->Id = $datos['IdCertificados'][$i];
                $i++;
            }
        }
        $dataEnviar->RolActivo = current($_SESSION['rolcod']);

        $dataEnviar->EsEnviar = (isset($datos['enviar']) && $datos['enviar'] == 1);

        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendPut($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'] ?? '--', utf8_decode($dataResult['error_description'] ?? 'error'));
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

    public function Eliminar($datos) {

        if (!$this->_validarEliminar($datos))
            return false;

        $url = "licencias/" . $datos['Id'];
        $dataEnviar = new stdClass();
        $dataEnviar->Id = $datos['Id'];
        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendDelete($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'] ?? '--', utf8_decode($dataResult['error_description'] ?? 'error'));
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

    public function ModificarLicenciaxReintegro($datos): bool {
        $url = 'licencias/reintegro';

        $dataEnviar = new stdClass();
        $dataEnviar->Licencias = $datos['Licencias'];
        $dataEnviar->FechaReintegro = $datos['FechaReintegro'];
        $fechaFin = DateTime::createFromFormat('Y-m-d', $datos['FechaReintegro']);

        if ($fechaFin !== false && $datos['FechaFinAbierta'] == 1) {
            $fechaFin->modify('-1 day');
            $dataEnviar->Fin = $fechaFin->format('Y-m-d');

            $dataEnviar->FechaFinAbierta = 0;
        } else {
            $dataEnviar->Fin = $datos['Fin'];
            $dataEnviar->FechaFinAbierta = $datos['FechaFinAbierta'];
        }

        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendPut($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], utf8_decode($dataResult['error_description']));
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

        if (!isset($array['success']))
            return false;

        return $array['success'];
    }

    public function ReincorporarLicencias($datos): bool {
        $url = 'licencias/reincorporar';

        $dataEnviar = new stdClass();
        $dataEnviar->Id = $datos['Id'];
        //$dataEnviar->x_token = $datos['x_token'];
        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$datos['x_token']}"]);
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(true);
        if (!$this->oCurl->sendPost($cuerpo, $dataResult)) {
            var_dump($dataResult);
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

        if (!isset($array['success']))
            return false;

        return $array['success'];
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function cambiarEstado(array &$datos): bool {

        $url = "licencias/{$datos['Id']}/workflow";

        $dataEnviar = new stdClass();
        $dataEnviar->Id = (int)$datos['Id'];
        if (isset($datos['IdEstado']))
            $dataEnviar->IdEstado = (int)$datos['IdEstado'];
        if (isset($datos['IdWorkflow']))
            $dataEnviar->IdWorkflow = (int)$datos['IdWorkflow'];
        $dataEnviar->rolActivo = (int)current($_SESSION['rolcod']);
        $cuerpo = json_encode($dataEnviar);

        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);


        if (!$this->oCurl->sendPost($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], $dataResult['error_description']);
            return false;
        }


        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $datos["bajaLiquidacion"] = $array["bajaLiquidacion"];
        $datos["anularLiquidacion"] = $array["anularLiquidacion"];
        $datos["liquidarSIGA"] = $array["liquidarSIGA"];

        if (isset($array['error'])) {
            $this->setError($array['error'], $array['error_description']);
            return false;
        }


        return true;
    }

    public function buscarJuntasxLicencias(array $datos, ?array &$resultado, ?int &$numfilas): bool {
        $url = "licencias/{$datos['IdLicencia']}/juntas";
        $resultado = [];
        $numfilas = 0;
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet('', $dataResult)) {
            $this->setError($dataResult['error'], $dataResult['error_description']);
            return false;
        }
        $array = $this->Utf8 ? $dataResult : FuncionesPHPLocal::DecodificarUtf8($dataResult);
        if (isset($array['error'])) {
            $this->setError($array['error'], $array['error_description']);
            return false;
        }
        $resultado = $array['filas'];
        $numfilas = $array['total'];

        return true;
    }

    public function buscarJuntaxCodigo(array $datos, ?array &$resultado, ?int &$numfilas): bool {

        $url = "juntas/{$datos['IdJunta']}";
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet('', $dataResult)) {
            $this->setError($dataResult['error'], $dataResult['error_description']);
            return false;
        }

        if (isset($dataResult['error'])) {
            $this->setError($dataResult['error'], $dataResult['error_description']);
            return false;
        }

        $resultado = $dataResult['filas'];
        $numfilas = $dataResult['total'];

        return true;
    }

    public function buscarCantidadxEstado(array $datos, ?array &$resultado, ?int &$numfilas): bool {

        $url = "licencias/cantidad/estado/{$datos['IdEstado']}";

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet('', $dataResult)) {
            $this->setError($dataResult['error'], $dataResult['error_description']);
            return false;

        }


        $array = $this->Utf8 ? $dataResult : FuncionesPHPLocal::DecodificarUtf8($dataResult);

        if (isset($array['error'])) {
            $this->setError($array['error'], $array['error_description']);
            return false;
        }
        //array(3) { ["request"]=> array(1) { ["IdEstado"]=> string(2) "16" } ["total"]=> int(1) ["Cantidad"]=> string(1) "2" }
        $resultado = $array['Cantidad'] ?? [];
        $numfilas = $array['total'] ?? 0;

        return true;
    }


    public function InsertarObservacion($datos, &$codigoInsertado) {
        if (!$this->_ValidarInsertarObservacion($datos))
            return false;

        $url = "observaciones";

        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setHeader($arrayHeader);

        $dataEnviar = new stdClass();
        $dataEnviar->IdLicencia = $datos['Id'];
        $dataEnviar->IdUsuario = $_SESSION['usuariocod'];
        $dataEnviar->Observacion = $datos['Observacion'];
        $dataEnviar->Fecha = date('Y-m-d H:i:s');

        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

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

        $codigoInsertado = $datos['Id'];

        return true;
    }

    public function ObtenerObservacionesxIdLicencia($datos, &$resultado, &$numfilas) {
        $url = "observaciones/" . $datos['Id'];

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar licencia por código");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $resultado = $array;
        $numfilas = count($array);

        return $array;
    }

    public function ObtenerLicenciaxId($datos, $esControl = false) {

        $url = "licencias/{$datos['Id']}";
        if ($esControl)
            $url .= '/completo';

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar licencia por código");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    public function ObtenerAutorizantes() {
        $url = "combos/autorizantes";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar autorizantes");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;
    }

    public function ObtenerEspecialidades() {
        $url = "combos/especialidades";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar especialidades");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;
    }

    public function ObtenerMotivos(int $idTipo = null) {
        $url = is_null($idTipo) ? 'combos/motivos' : "combos/motivos/$idTipo";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar motivos");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;
    }

    public function ObtenerMotivoxId(int $IdMotivo) {
        $url = "combos/motivos/id/" . $IdMotivo;

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar motivos");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;
    }

    public function ObtenerMotivoDetalle($datos, &$resultado, &$numfilas) {
        $url = "/combos/motivos/detalle/" . $datos['Id'];

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar motivos");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $resultado = $array;

        return $array;
    }

    public function ObtenerDiagnosticos(int $idTipo) {
        $url = "combos/diagnosticos/$idTipo";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar diagnósticos");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;
    }

    public function ObtenerDiagnosticoDetalle($datos, &$resultado, &$numfilas) {
        $url = "/combos/diagnosticos/detalle/" . $datos['Id'];

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar diagnosticos");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $resultado = $array;

        return $array;
    }

    public function ObtenerParentescos() {
        $url = "combos/parentescos";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar parentescos");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;
    }

    public function ObtenerEstados() {
        $url = "combos/estados";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar estados");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;
    }

    public function ObtenerEstadosPublicos() {
        $url = "licencias-estados";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        $urlAnexa = "?Estado=10&rows=100";
        $url .= $urlAnexa;

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar estados");
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    public function ObtenerAptoFisico() {

        $url = "combos/aptofisico";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar apto fisico");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;
    }

    public function BuscarPersona($datos, &$resultado, &$numfilas) {
        $conexionES = new Elastic\Conexion();
        $oPersonas = new Elastic\Personas($conexionES);

        $datosBuscar['Dni'] = $datos['DniBusqueda'] ?? $datos['DniBusquedaPersona'];
        if (!$oPersonas->buscarxDni($datosBuscar, $resultado, $numfilas))
            return false;

        return true;
    }

    public function BuscarAutorizantexId($datos, &$resultado, &$numfilas) {

        $url = "autorizantes/id/" . $datos['Id'];

        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader(["Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar licencia por código");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $resultado = $array;

        return $array;
    }


    public function BuscarAutorizantexMatricula($datos, &$resultado, &$numfilas) {
        $url = "autorizantes/" . $datos['Matricula'] . "?";

        $dataEnviar = new stdClass();
        $dataEnviar->Estado = ACTIVO;
        if (isset($datos["TipoAutorizante"]) && $datos["TipoAutorizante"] != "")
            $dataEnviar->TipoAutorizante = $datos["TipoAutorizante"];

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet(http_build_query($dataEnviar), $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar licencia por código");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $resultado = $array;

        return $array;
    }

    public function BuscarFamiliarxDni($datos, &$resultado, &$numfilas) {
        $url = "familiares/" . $datos['Dni'];

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar licencia por código");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $resultado = $array;

        return $array;
    }

    public function BuscarTiposLicencias() {
        $url = "combos/tipos_licencias";
        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar tipos de licencias");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $resultado = $array;

        return $array;
    }

    public function BuscarTipoLicenciasxCodigo(array $datos, ?array &$resultado): bool {
        $url = "combos/tipos_licencias/detalle/{$datos['IdTipo']}";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar tipos de licencias por código");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $resultado = current($array['filas']);

        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     */
    public function agregarCargo(array $datos): bool {

        $url = "licencias/{$datos['IdLicencia']}/cargos";
        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($datos));

        $arrayHeader = ['Content-Type: application/json', "Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPost($cuerpo, $dataResult)) {
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


        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     */
    public function eliminarCargo(array $datos): bool {

        $url = "licencias/{$datos['IdLicencia']}/cargos";
        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($datos));

        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);

        $arrayHeader = ['Content-Type: application/json', "Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setHeader($arrayHeader);

        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendDelete($cuerpo, $dataResult)) {
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


        return true;
    }


    /**
     * @param bool $Utf8
     *
     * @return cServiciosLicencias
     */
    public function setUtf8(bool $Utf8 = false): cServiciosLicencias {
        $this->Utf8 = $Utf8;
        return $this;
    }

    public function insertarJunta(array $datos, ?int &$codigoInsertado): bool {

        $datos['Id'] = $datos['IdLicencia'];
        if (!$this->obtenerAcciones($datos, $acciones)) {
            $this->setError(400, 'Error al buscar acciones');
            return false;
        }
        if (empty($acciones['accionesABM'])) {
            $this->setError(400, 'No tiene acciones');
            return false;
        }

        if (!isset($acciones['accionesABM']['000004'])) {
            $this->setError(400, 'No tiene permisos para realizar dicha accion');
            return false;
        }

        $url = "licencias/{$datos['IdLicencia']}/juntas";
        $cuerpo = json_encode($datos);
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendPost($cuerpo, $dataResult)) {
            $this->setError($dataResult);
            return false;
        }

        if (!empty($dataResult['error'])) {
            $this->setError($dataResult);
            return false;
        }

        $codigoInsertado = $dataResult['Id'];

        return true;
    }

    public function modificarJunta(array $datos): bool {

        $datos['IdJunta'] = $datos['Id'];
        $datos['Id'] = $datos['IdLicencia'];
        if (!$this->obtenerAcciones($datos, $acciones)) {
            $this->setError(400, 'Error al buscar acciones');
            return false;
        }
        if (empty($acciones['accionesABM'])) {
            $this->setError(400, 'No tiene acciones');
            return false;
        }

        if (!isset($acciones['accionesABM']['000005'])) {
            $this->setError(400, 'No tiene permisos para realizar dicha accion');
            return false;
        }

        $url = "licencias/{$datos['IdLicencia']}/juntas";

        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($datos));
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setDebug(false);

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setHttpBuildPost(false);
        if (!$this->oCurl->sendPut($cuerpo, $dataResult)) {
            $this->setError($dataResult);
            return false;
        }

        if (!empty($dataResult['error'])) {
            $this->setError($dataResult);
            return false;
        }

        return true;
    }

    public function cambiarEstadoJunta(array $datos): bool {

        $datos['IdJunta'] = $datos['Id'];
        $datos['Id'] = $datos['IdLicencia'];
        if (!$this->obtenerAcciones($datos, $acciones)) {
            $this->setError(400, 'Error al buscar acciones');
            return false;
        }
        if (empty($acciones['accionesABM'])) {
            $this->setError(400, 'No tiene acciones');
            return false;
        }

        if (!isset($acciones['accionesABM']['000005'])) {
            $this->setError(400, 'No tiene permisos para realizar dicha accion');
            return false;
        }

        $url = "licencias/{$datos['IdLicencia']}/juntas";

        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($datos));

        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setDebug(false);

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setHttpBuildPost(false);
        if (!$this->oCurl->sendPatch($cuerpo, $dataResult)) {
            $this->setError($dataResult);
            return false;
        }

        if (!empty($dataResult['error'])) {
            $this->setError($dataResult);
            return false;
        }

        return true;
    }

    public function obtenerAcciones(array $datos, ?array &$acciones): bool {


        $array = [];
        $array['acciones'] = [];
        $url = "licencias/{$datos['Id']}/workflow";

        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setHeader($arrayHeader);
        $data = new stdClass();
        $data->rolActivo = (int)current($_SESSION['rolcod']);
        $cuerpo = json_encode($data);

        if (!$this->oCurl->sendGet('', $dataResult, $cuerpo)) {
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

        if (isset($array['acciones'])) {
            // Trabaja con $array['acciones']
            $acciones = $array['acciones'];
        } else {
            // Maneja el caso cuando no existe la clave
            $acciones = [];
        }


        return true;
    }

    public function obtenerAccionesNodoInicial(array $datos, ?array &$acciones): bool {

        $url = "licencias/workflow/inicial";
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);
        $data = new stdClass();
        $data->IdMotivo = (int)$datos['IdMotivo'];
        $data->IdRol = (int)current($_SESSION['rolcod']);
        $cuerpo = json_encode($data);

        if (!$this->oCurl->sendGet('', $dataResult, $cuerpo)) {
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

        $acciones = $array['acciones'];
        return true;
    }

    public function buscarCargosAfectados(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {

        $url = 'cargos-afectados?';

        if (empty($datos['Inicio'])) {
            $this->setError(400, 'Debe seleccionar la fecha de inicio.');
            return false;
        }
        $Inicio = $datos['Inicio'];
        $datos['Inicio'] = FuncionesPHPLocal::ConvertirFecha($datos['Inicio'], 'dd/mm/aaaa', 'aaaa-mm-dd');

        if (isset($datos['Fin']) && empty($datos['Fin'])) {
            $this->setError(400, 'Debe seleccionar la fecha de fin.');
            return false;
        } elseif (isset($datos['Fin'])) {
            $Fin = $datos['Fin'];
            $datos['Fin'] = FuncionesPHPLocal::ConvertirFecha($datos['Fin'], 'dd/mm/aaaa', 'aaaa-mm-dd');
        }

        if (isset($datos['Fin'])) {

            if (strtotime($datos['Fin']) < strtotime($datos['Inicio'])) {
                $this->setError(400, 'La fecha de finalizaci&oacute;n no puede ser anterior a la de inicio.');
                return false;
            }

            $start = new DateTime($datos['Inicio']);
            $end = new DateTime($datos['Fin']);
            $diff = $start->diff($end);
            $datos['Duracion'] = $diff->days + 1;
            $datos['Unidad'] = 1;
        }

        if (empty($datos['Duracion']) && $datos["FechaFinAbierta"] == "0") {
            $this->setError(400, 'Debe seleccionar la duraci&oacute;n. ');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['Unidad'])) {
            $this->setError(400, 'Debe seleccionar la unidad. ');
            return false;
        }

        $query_data['IdPersona'] = $datos['IdPersona'];
        $query_data['Inicio'] = $datos['Inicio'];
        $query_data['Duracion'] = $datos['Duracion'];
        $query_data['Unidad'] = $datos['Unidad'];
        $query_data['FechaFinAbierta'] = "0";
        if (!FuncionesPHPLocal::isEmpty($datos['FechaFinAbierta'])) {
            $query_data['FechaFinAbierta'] = $datos['FechaFinAbierta'];
        }
        if (!FuncionesPHPLocal::isEmpty($_SESSION['IdEscuela']) && is_array($_SESSION['IdEscuela']))
            $query_data['IdEscuela'] = implode(",", $_SESSION['IdEscuela']);

        if (!FuncionesPHPLocal::isEmpty($datos['Id'])) {
            $query_data['id_licencia'] = $datos['Id'];
            $query_data['rolActivo'] = (int)current($_SESSION['rolcod']);
        }

        $query_data['seleccionaCargos'] = $datos['seleccionaCargos'] ?? [];
        $query_data['IdEscuelaSeleccionada'] = $datos['IdEscuelaSeleccionada'] ?? null;

        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader(["Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet(http_build_query($query_data), $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar cargos afectados");
            return false;
        }

        $resultado = $dataResult['filas'];
        $numfilas = $dataResult['size'];
        $total = $dataResult['total'];
        return true;
    }


    public function calcularFechaFin($datos, &$resultado): bool {

        $url = "calcular-licencia";

        $dataEnviar = new stdClass();
        $dataEnviar->Inicio = FuncionesPHPLocal::ConvertirFecha($datos['Inicio'], 'dd/mm/aaaa', 'aaaa-mm-dd');
        $dataEnviar->Duracion = $datos['Duracion'];
        $dataEnviar->Unidad = $datos['Unidad'];
        $dataEnviar->Horas = $datos['Horas'];
        $cuerpo = json_encode($dataEnviar);
        $header = ["Authorization: Bearer {$_SESSION['token']}", "Content-Type: application/json"];
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet('', $dataResult, $cuerpo)) {
            $this->setError("Error", "Ocurrió un error al recalcular fechas");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $resultado = $array;

        return true;
    }


    private function _ValidarInsertar(&$datos) {

        if (empty($datos['Duracion']) && !empty($datos['Horas'])) {
            $datos['Duracion'] = $datos['Horas'];
            $datos['Unidad'] = '0';
        }

        if (!$this->obtenerAccionesNodoInicial($datos, $acciones)) {
            $this->setError(400, 'Error al buscar acciones');
            return false;
        }

        $datos['Acciones'] = $acciones;
        $datos['esInsertar'] = true;
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        if (!$this->buscarCargosAfectados($datos, $resultado, $numfilas, $total))
            return false;

        if ($total < 1) {
            $this->setError(404, 'La licencia no tiene cargos asociados');
            return false;
        }

        return true;
    }

    private function _ValidarModificar(&$datos) {

        if (!$this->obtenerAcciones($datos, $acciones)) {
            $this->setError(400, 'Error al buscar acciones al modificar');
            return false;
        }

        if (empty($acciones['accionesABM'])) {
            $this->setError(400, 'No tiene acciones');
            return false;
        }

        if (!isset($acciones['accionesABM']['000003']) && !isset($acciones['accionesABM']['000006'])) {
            $this->setError(400, 'No tiene permisos para realizar dicha accion');
            return false;
        }

        //TODO: Pasar a acción (validar artículos)
        /*if (isset($datos['Estado'])  &&  in_array($datos['Estado'], [2, 10, 11, 16, 18, 19])) {
            if (FuncionesPHPLocal::isEmpty($datos['IdArticuloPuesto']) || count($datos['IdArticuloPuesto']) != count($datos['IdPuestoAfectado'])) {
                $this->setError(400, 'Falta seleccionar los articulos de los cargos afectadosss');
                return false;
            }
        }*/

        $datos['Acciones'] = $acciones['accionesABM'];
        $datos['esInsertar'] = false;

        if (!$this->_ValidarDatosVacios($datos))
            return false;

        return true;
    }

    private function _validarEliminar(&$datos) {

        if (!$this->obtenerAcciones($datos, $acciones)) {
            $this->setError(400, 'Error al buscar acciones');
            return false;
        }
        if (empty($acciones['accionesABM'])) {
            $this->setError(400, 'No tiene acciones');
            return false;
        }

        if (!isset($acciones['accionesABM']['000017'])) {
            $this->setError(400, 'No tiene permisos para realizar dicha accion');
            return false;
        }

        return true;
    }


    //----------------------------------------
    // Juntas
    //----------------------------------------

    private function _ValidarInsertarObservacion($datos) {
        if (!$this->obtenerAcciones($datos, $acciones)) {
            $this->setError(400, 'Error al buscar acciones');
            return false;
        }
        if (empty($acciones['accionesABM'])) {
            $this->setError(400, 'No tiene acciones');
            return false;
        }

        if (!isset($acciones['accionesABM']['000005'])) {
            $this->setError(400, 'No tiene permisos para realizar dicha accion');
            return false;
        }

        if (!$this->_ValidarDatosVaciosObservacion($datos))
            return false;

        return true;
    }

    private function _ValidarDatosVacios(&$datos) {

        if (!isset($datos['IdPersona']) || $datos['IdPersona'] == "") {
            $this->setError(400, 'Falta seleccionar persona');
            return false;
        }

        if (!isset($datos['Inicio']) || $datos['Inicio'] == "") {
            $this->setError(400, 'Falta ingresar fecha de inicio');
            return false;
        }

        if (isset($datos['Fin']) && $datos['Fin'] == '') {
            $this->setError(400, 'Falta ingresar fecha de fin.');
            return false;
        }

        if (isset($datos['Fin'])) {

            $Inicio = FuncionesPHPLocal::ConvertirFecha($datos['Inicio'], "dd/mm/aaaa", "aaaa-mm-dd");
            $Fin = FuncionesPHPLocal::ConvertirFecha($datos['Fin'], "dd/mm/aaaa", "aaaa-mm-dd");

            if (strtotime($Fin) < strtotime($Inicio)) {
                $this->setError(400, 'Error, la fecha de fin no puede ser menor a la de inicio.');
                return false;
            }

            $start = new DateTime($Inicio);
            $end = new DateTime($Fin);
            $diff = $start->diff($end);
            $datos['Duracion'] = $diff->days + 1;

            $datos['Unidad'] = 1;
            $datos['Horas'] = '0';
        }


        if (isset($datos['Duracion']) && $datos['Duracion'] != '' && $datos['Duracion'] == 0 && $datos['Horas'] == 0 && $datos["FechaFinAbierta"] == "0") {
            $this->setError(400, utf8_decode('La duración no puede ser menor a 1 día'));
            return false;
        }

        if ((!isset($datos['Horas']) || $datos['Horas'] === "") && $datos["FechaFinAbierta"] == "0") {
            $this->setError(400, utf8_decode('Falta ingresar duración de la licencia'));
            return false;
        }

        if ($datos['Horas'] == 0 && (!isset($datos['Unidad']) || $datos['Unidad'] == "")) {
            $this->setError(400, utf8_decode('Falta ingresar duración de la licencia'));
            return false;
        }

        if (!isset($datos['IdMotivo']) || $datos['IdMotivo'] == "") {
            $this->setError(400, utf8_decode('Falta seleccionar motivo de la licencia'));
            return false;
        }

        if (isset($datos['Acciones']["000203"]) && $datos['Acciones']["000203"]) {

            if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "") {
                if (in_array($datos['IdTipo'], [1, 3])) {
                    $this->setError(400, utf8_decode('Debe ingresar una descripción de los síntomas manifestados'));
                    return false;
                } else {
                    $this->setError(400, utf8_decode('Debe ingresar una descripción/observación'));
                    return false;
                }
            }

            if (strlen($datos['Descripcion']) > 255) {
                $this->setError(400, utf8_decode('Longitud de descripción demasiado larga'));
                return false;
            }
        }


        if (isset($datos['Acciones']['000193']) && $datos['Acciones']['000193']) {

            if (isset($datos['NroResolucion']) && $datos['NroResolucion'] == "") {
                $this->setError(400, utf8_decode('Debe ingresar Numero de resolución'));
                return false;
            }
        }

        if (isset($datos['Acciones']['000193']) && $datos['Acciones']['000193']) {

            if (isset($datos['NroResolucion']) && $datos['NroResolucion'] == "") {
                $this->setError(400, utf8_decode('Debe ingresar Numero de resolución'));
                return false;
            }
        }

        if (isset($datos['Acciones']['000231']) && $datos['Acciones']['000231']) {

            if (isset($datos['Certificados']) && $datos['Certificados'] == "") {
                $this->setError(400, utf8_decode('Debe ingresar un Certificado'));
                return false;
            }
        }


        if (isset($datos['Certificados']) && count($datos['Certificados']) >= 1 && $datos['esInsertar']) {
            if (isset($datos['Acciones']['000021']) && $datos['Acciones']['000021']) {
                if ((!isset($datos['IdEspecialidad']) || $datos['IdEspecialidad'] == "") || (!isset($datos['Matricula']) || $datos['Matricula'] == "")) {
                    $this->setError(400, utf8_decode('Es necesario completar los datos del médico tratante'));
                    return false;
                }
            }
        }


        if (isset($datos['Familiar']) && $datos['Familiar'] == 1) {
            if (!isset($datos['IdFamiliar']) || $datos['IdFamiliar'] == '') {
                $this->setError(400, utf8_decode('Debe seleccionar el familiar.'));
                return false;
            }

            /*	if (!isset($datos['IdParentesco']) || $datos['IdParentesco'] == "") {
                    $this->setError(400, utf8_decode('Debe seleccionar relación con el familiar.'));
                    return false;
                } */
        }

        if (isset($datos['Acciones']['000292']) && $datos['Acciones']['000292']) {

            if (isset($datos['IdDiagnosticoDetalle']) && $datos['IdDiagnosticoDetalle'] == "") {
                $this->setError(400, utf8_decode('Debe ingresar un diagnostico'));
                return false;
            }
        }

        if (isset($datos['Acciones']['000001']) && $datos['Acciones']['000001']) {
            $i = 0;
            if (isset($datos['nombrearchivo']) && !empty($datos['nombrearchivo'])) {
                while ($i < count($datos['nombrearchivo'])) {
                    if (strlen($datos['nombrearchivo'][$i]) > 255) {
                        $this->setError(400, utf8_decode('El nombre del archivo supera la cantidad máxima permitida de caracteres'));
                        return false;
                    }
                    $i++;
                }
            }
        }

        if (isset($datos['Acciones']['000283']) && $datos['Acciones']['000283']) {

            if (isset($datos['TareaPasiva']) && $datos['TareaPasiva'] == "") {
                $this->setError(400, utf8_decode('Debe seleccionar si deriva a Tarea Pasiva o cambio de Función'));
                return false;
            }
        }

        if (isset($datos['Acciones']['000263']) && $datos['Acciones']['000263']) {
            if (isset($datos['AptoFisico']) && $datos['AptoFisico'] == "") {
                $this->setError(400, utf8_decode('Debe seleccionar si presentó el Apto Físico'));
                return false;
            }
        }


        return true;
    }

    private function _ValidarDatosVaciosObservacion($datos) {
        if (!isset($datos['Id']) || $datos['Id'] == "") {
            $this->setError(400, 'Error, debe ingresar una licencia');
            return false;
        }

        if (!isset($datos['Observacion']) || $datos['Observacion'] == "") {
            $this->setError(400, 'Error, debe ingresar un comentario');
            return false;
        }

        if (isset($datos['Observacion']) && $datos['Observacion'] != "") {
            if (strlen($datos['Observacion']) > 1000) {
                $this->setError(400, utf8_decode('Longitud del comentario demasiado largo'));
                return false;
            }
        }

        return true;
    }


    private function _SetearNull(&$datos): void {
        if (!isset($datos['IdAutorizante']) || $datos['IdAutorizante'] == "")
            $datos['IdAutorizante'] = null;

        if (!isset($datos['IdFamiliar']) || $datos['IdFamiliar'] == "")
            $datos['IdFamiliar'] = null;

        if (!isset($datos['IdMotivoDetalle']) || $datos['IdMotivoDetalle'] == "")
            $datos['IdMotivoDetalle'] = null;

        if (!isset($datos['IdArticuloSeleccionado']) || $datos['IdArticuloSeleccionado'] == "")
            $datos['IdArticuloSeleccionado'] = null;

        if (!isset($datos['FechaFinAbierta']) || $datos['FechaFinAbierta'] == "")
            $datos['FechaFinAbierta'] = null;
    }

    public function ModificarLicenciaReabierta($datos) {

        if (!$this->_ValidarModificar($datos))
            return false;

        $this->_SetearNull($datos);

        $url = 'licencias/' . $datos['Id'] . '/reabierta';

        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . '-' . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);

        $arrayHeader = ['Content-Type: application/json', "Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setHeader($arrayHeader);

        $dataEnviar = new stdClass();

        $dataEnviar->AltaLicencia = $datos['AltaLicencia'];

        $dataEnviar->Id = $datos['Id'];
        $dataEnviar->IdTipo = $datos['IdTipo'];
        $dataEnviar->IdPersona = $datos['IdPersona'];
        $dataEnviar->IdEstado = empty($datos['IdEstado']) ? 1 : $datos['IdEstado'];
        $dataEnviar->Inicio = FuncionesPHPLocal::ConvertirFecha($datos['Inicio'], 'dd/mm/aaaa', 'aaaa-mm-dd');

        if ($datos['Horas'] != '0') {
            $dataEnviar->Duracion = $datos['Horas'] / 24;;
            $dataEnviar->Unidad = 0;

        } else {
            $dataEnviar->Duracion = $datos['Duracion'];
            $dataEnviar->Unidad = $datos['Unidad'];
        }

        if (isset($datos['NroResolucion']) && $datos['NroResolucion']) {
            $dataEnviar->NroResolucion = $datos['NroResolucion'];
        }

        $dataEnviar->IdMotivo = $datos['IdMotivo'];
        $dataEnviar->IdMotivoDetalle = $datos['IdMotivoDetalle'] ?? null;
        $dataEnviar->IdArticuloSeleccionado = $datos['IdArticuloSeleccionado'] ?? null;
        $dataEnviar->IdDiagnostico = $datos['IdDiagnostico'] ?? null;
        $dataEnviar->IdDiagnosticoDetalle = $datos['IdDiagnosticoDetalle'] ?? null;
        $dataEnviar->Descripcion = $datos['Descripcion'] ?? null;

        $dataEnviar->FechaFinAbierta = $datos['FechaFinAbierta'] ?? null;

        if (isset($datos['Familiar']) && $datos['Familiar']) {
            $dataEnviar->Familiar = $datos['Familiar'];
            $dataEnviar->DatosFamiliar = new stdClass();
            $dataEnviar->DatosFamiliar->Id = $datos['IdFamiliar'];
            $dataEnviar->DatosFamiliar->IdParentesco = $datos['IdParentesco'];
        }
        $dataEnviar->DuracionHabiles = $datos['DuracionHabiles'] ?? null;
        $dataEnviar->IdEspecialidad = $datos['IdEspecialidad'] ?? null;
        $dataEnviar->IdAutorizante = $datos['IdAutorizante'] ?? null;
        $dataEnviar->EncontroAutorizante = $datos['EncontroAutorizante'] ?? 0;
        $dataEnviar->Matricula = $datos['Matricula'] ?? null;
        $dataEnviar->Nombre = $datos['NombreAutorizante'] ?? null;
        $dataEnviar->Apellido = $datos['ApellidoAutorizante'] ?? null;
        $dataEnviar->Estado = ACTIVO;

        $dataEnviar->rolActivo = (int)current($_SESSION['rolcod']);

        if (!empty($datos['IdPuestoAfectado'])) {
            $dataEnviar->IdPuesto = $datos['IdPuestoAfectado'];
            $dataEnviar->IdArticuloPuesto = $datos['IdArticuloPuesto'] ?? [];
        }

        $dataEnviar->Certificados = [];
        $i = 0;
        if (isset($datos['nombrearchivo']) && !empty($datos['nombrearchivo'])) {
            while ($i < count($datos['nombrearchivo'])) {
                $dataEnviar->Certificados[$i] = new stdClass();
                $dataEnviar->Certificados[$i]->Nombre = $datos['nombrearchivo'][$i];
                $dataEnviar->Certificados[$i]->Contenido = base64_encode(file_get_contents(PATH_STORAGE . 'tmp/' . $datos['nombrearchivotmp'][$i]));
                $i++;
            }
        }

        $i = 0;
        if (isset($datos['IdCertificados']) && !empty($datos['IdCertificados'])) {
            while ($i < count($datos['IdCertificados'])) {
                $dataEnviar->CertificadosEliminar[$i] = new stdClass();
                $dataEnviar->CertificadosEliminar[$i]->Id = $datos['IdCertificados'][$i];
                $i++;
            }
        }
        $dataEnviar->RolActivo = current($_SESSION['rolcod']);

        if (isset($datos['TareaPasiva']))
            $dataEnviar->TareaPasiva = $datos['TareaPasiva'];

        if (isset($datos['AptoFisico']))
            $dataEnviar->AptoFisico = $datos['AptoFisico'];

        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPut($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'] ?? '--', utf8_decode($dataResult['error_description'] ?? 'error'));
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


    public function modificarCargosMad($datos): bool {

        $url = 'licencias/mad';
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . '-' . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();
        foreach ($datos as $key => $r) {
            $dataEnviar->Puestos[$key] = new stdClass();
            $dataEnviar->Puestos[$key]->IdPersona = $r['IdPersona'];
            $dataEnviar->Puestos[$key]->IdPuesto = $r['IdPuesto'];
            $dataEnviar->Puestos[$key]->IdRevista = $r['IdRevista'];
            $dataEnviar->Puestos[$key]->IdPofaDestino = $r['IdPofaDestino'];
            $dataEnviar->Puestos[$key]->IdPuestoDestino = $r['IdPuestoDestino'];
        }

        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));
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

        return true;
    }


    public static function armarComboHorasParo(string $fechaInicio, array $desempenos, array $datos): string {

//        $stream = fopen(DIR_ROOT.'/error_logs/inasistencias.log', 'a+');
//        $logger = new Monolog\Logger('Agentes', [new Monolog\Handler\StreamHandler($stream)]);

        $fechaInicio = new DateTime($fechaInicio);
        $diaInicio = $fechaInicio->format('N');


        if (isset($datos['IdTipo']) && $datos['IdTipo'] == 2) {
            $valorMostrar = 'Hora/s C&aacute;tedra/s';

            $options = '';
            $arrayOpcionesDesempenio = [];
            foreach ($desempenos as $key => $desempeno) {

                if ($desempeno['Dia']['Numero'] == $diaInicio/*date('N')*/) {
                    $diferenciaHoras = ceil(FuncionesPHPLocal::calcularDiferenciaModulos($desempeno['Horario']['gte'], $desempeno['Horario']['lte']));

                    for ($i = $diferenciaHoras; $i >= 1; $i--) {
                        $arrayOpcionesDesempenio[$key][] = $i;
                    }
                }
            }
        } else {
            $valorMostrar = 'Hora/s';

            $options = '';
            $arrayOpcionesDesempenio = [];
            foreach ($desempenos as $key => $desempeno) {

                if ($desempeno['Dia']['Numero'] == $diaInicio/*date('N')*/) {
                    $diferenciaHoras = ceil(FuncionesPHPLocal::calcularDiferenciaHoras($desempeno['Horario']['gte'], $desempeno['Horario']['lte']));

                    for ($i = $diferenciaHoras; $i >= 1; $i--) {
                        $arrayOpcionesDesempenio[$key][] = $i;
                    }
                }
            }
        }
        /*
                $valorMostrar = '';
                if (array_key_exists('Horas', $datos) && !empty($datos['Horas'])) {

                    $valorMostrar = 'Hora/s';

                    $options = '';
                    $arrayOpcionesDesempenio = [];
                    foreach ($desempenos as $key => $desempeno) {

                        if ($desempeno['Dia']['Numero'] == date('N')) {
                            $diferenciaHoras = ceil(FuncionesPHPLocal::calcularDiferenciaHoras($desempeno['Horario']['gte'], $desempeno['Horario']['lte']));

                            for ($i = $diferenciaHoras; $i >= 1; $i--) {
                                $arrayOpcionesDesempenio[$key][] = $i;
                            }
                        }
                    }


                } else {

                    $valorMostrar = 'Hora/s C&aacute;tedra/s';

                    $options = '';
                    $arrayOpcionesDesempenio = [];
                    foreach ($desempenos as $key => $desempeno) {

                        if ($desempeno['Dia']['Numero'] == date('N')) {
                            $diferenciaHoras = ceil(FuncionesPHPLocal::calcularDiferenciaModulos($desempeno['Horario']['gte'], $desempeno['Horario']['lte']));

                            for ($i = $diferenciaHoras; $i >= 1; $i--) {
                                $arrayOpcionesDesempenio[$key][] = $i;
                            }
                        }
                    }
                }
        */

        $arrayOpciones = array_shift($arrayOpcionesDesempenio);
        if (count($arrayOpcionesDesempenio) > 0) {
            foreach ($arrayOpcionesDesempenio as $k => $opcionesDesempenio) {
                $opcionesDesempenio_ = $opcionesDesempenio;
                foreach ($arrayOpciones as $opt1) {
                    foreach ($opcionesDesempenio_ as $opt2) {
                        $opcionesDesempenio[] = $opt1 + $opt2;
                    }
                }
                $arrayOpciones = array_merge($arrayOpciones, $opcionesDesempenio);
            }
            $arrayOpciones = array_unique($arrayOpciones);
        }

        if (!empty($arrayOpciones)) {
            rsort($arrayOpciones);
            $maxValor = reset($arrayOpciones);
        } else
            $maxValor = 1;


        $options .= '<option value="' . $maxValor . '">Todas</option>';

        if (!empty($arrayOpciones)) {
            foreach ($arrayOpciones as $i) {
                if ($i !== $maxValor) {
                    $options .= '<option value="' . $i . '">' . $i . ' ' . $valorMostrar . '</option>';
                }
            }
        }


        return $options;
    }

    public function RepublicarLicenciaElastic($datos): bool {
        $url = 'licencias/republicar/' . $datos["IdLicencia"];
        if (isset($_SESSION['token']) && $_SESSION['token'] != "")
            $token = $_SESSION['token'];
        else
            $token = $datos['x_token'];
        $dataEnviar = new stdClass();
        $dataEnviar->IdLicencia = $datos['IdLicencia'];
        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$token}"]);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPost($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], utf8_decode($dataResult['error_description']));
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
        return true;
    }


    public function ObtenerLicenciasPorImpactar() {

        $url = "licencias-indicador-impactar-pon";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar licencias");
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

}
