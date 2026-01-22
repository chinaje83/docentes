<?php

include(DIR_CLASES_DB . "cDeclaracionJurada.db.php");

class cDeclaracionJurada extends cDeclaracionJuradaDB
{
    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO)
    {
        parent::__construct($conexion, $formato);
    }

    function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public function getError(): array
    {
        return $this->error;
    }

    public function InsertarDeclaracion($datos, &$puestos, &$personas): bool {

        $oDeclaracionJuradaPdf = new cDeclaracionJuradaPDF($this->conexion);
        $fileExists = $oDeclaracionJuradaPdf->getPdfExists($datos['IdEscuela']);

        if ($fileExists) {
            $this->setError('200', 'Actualmente el archivo ya se encuentra firmado.');
            return false;
        }

        if (!$this->ValidarPuestos($datos, $puestos,  $personas)) {
            $this->setError($this->getError());
            return false;
        }

        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $datos['IdUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['RegistroSeguridad'] = md5($datos['Texto']);
        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $oPofa = new cEscuelasPOF($this->conexion);
        if (!$oPofa->BuscarPOFAEditable($datos, $resultadoPofa, $numfilasPofa)) {
            $this->setError($oPofa->getError());
            return false;
        }

        if ($numfilasPofa > 0) {
            $datosPofa = $this->conexion->ObtenerSiguienteRegistro($resultadoPofa);
            $datosPofa['PofaEditable'] = 0;
            if (!$oPofa->Modificar($datosPofa)) {
                $this->setError($oPofa->getError());
                return false;
            }
        }

        $oDeclaracionJuradaPdf = new cDeclaracionJuradaPDF($this->conexion);
        if(!$oDeclaracionJuradaPdf->generarPDF($datos,"F")) {
            $this->setError("Ha ocurrido un error al generar la declaracion jurada");
            return false;
        }

        if (defined('FIRMARPDF') && FIRMARPDF) {
            $data = $oDeclaracionJuradaPdf->getPdfBase64File();
            $logoFirma = file_get_contents(DIR_ROOT . "/assets/provincia/" . PROVINCIA . "/images/logo.png");
            $dataEnviar["pdf_signer"] = $data;
            $dataEnviar["imgFirma"] = base64_encode($logoFirma);
            $dataEnviar["x"] = 15;
            $dataEnviar["y"] = 50;
            $dataEnviar["reason"] = "DDJJ";
            $dataEnviar["location"] = PROVINCIA_NOMBRE;
            $dataEnviar["contact"] = $_SESSION['usuarionombre'] . " " . $_SESSION['usuarioapellido'];
            $dataEnviar["visualizarFirma"] = 1;
            $dataEnviar['page'] = $oDeclaracionJuradaPdf->getCountPage();
            //$dataEnviar['page'] = 2;
            $oCurl = new CurlBigtree();
            $url = API_FIRMA . "sign-pdf";
            $oCurl->setUrl($url);
            if (!$oCurl->sendPost($dataEnviar, $dataResult)) {
                $this->setError("Error", "Error, debe ingresar un token");
                return false;
            }
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
            if (isset($array['error'])) {
                $this->setError($array['error'], $array['error_description']);
                return false;
            }
            if (!$oDeclaracionJuradaPdf->saveSignerPdf($array['pdf'])) {
                $this->setError("Error", "Error, al guardar el archivo firmado");
                return false;
            }
        }
        $_SESSION['Bloqueo'] = false;

        return true;
    }


    public function ValidarPuestos($datos, &$puestos, &$personas): bool {

        $oEscuelasPuestos = new cEscuelasPuestos($this->conexion);
        $error = false;

        if (!$oEscuelasPuestos->buscarExistentexEscuela($datos, $resultado, $numfilas)) {
            $this->setError($oEscuelasPuestos->getError());
            return false;
        }

        $puestos_existentes = $this->conexion->ObtenerSiguienteRegistro($resultado)['Cantidad'];

        if ($puestos_existentes == 0) {
            $this->setError("Error", 'No existen cargos en la escuela');
            return false;
        }

        unset($resultado, $numfilas);
        $datos['EnDisponibilidad'] = EXCLUIR_ESTADO_EN_DISPONIBILIDAD;
        $datos['AdmiteSuplente'] = EXCLUIR_ADMITE_SUPLENTE;
        if (!$oEscuelasPuestos->BuscarDesempenosFaltantesxEscuela($datos, $resultado, $numfilas)) {
            $this->setError($oEscuelasPuestos->getError());
            return false;
        }

        $puestos = $this->conexion->ObtenerSiguienteRegistro($resultado)['Cantidad'];
        if ($puestos > 0)
            $error = true;

        unset($resultado, $numfilas);

        if (!$oEscuelasPuestos->BuscarPersonasFaltantesxEscuela($datos, $resultado, $numfilas)) {
            $this->setError($oEscuelasPuestos->getError());
            return false;
        }

        $personas = $this->conexion->ObtenerSiguienteRegistro($resultado)['Cantidad'];
        if ($personas > 0)
            $error = true;


        if ($error) {
            $this->setError("Error", utf8_encode("Actualmente los puestos no cumplen con los requisitos necesarios."));
            return false;
        }

        return true;
    }
}

