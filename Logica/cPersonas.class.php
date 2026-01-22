<?php

include(DIR_CLASES_DB . "cPersonas.db.php");

class cPersonas extends cPersonasdb {

    protected $conexion;
    protected $formato;
    /**
     * @var Elastic\Conexion
     */
    protected $conexionES;

    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO, ?Elastic\Conexion $conexionES = null) {
        parent::__construct($conexion, $formato);
        $conexionES = $conexionES ?? new Elastic\Conexion();
        $this->conexionES =& $conexionES;
    }

    /**
     * Destructor de la clase cNiveles.
     */
    function __destruct() {
        parent::__destruct();
    }


    public function getError(): array {
        return $this->error;
    }

    public function BuscarxCodigo($datos, &$resultado, &$numfilas) {
        $sparam = [
            'IdPersona' => $datos['IdPersona'],
            'xEstado' => 0,
            'Estado' => "",
        ];


        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }


        if (!parent::BuscarxCodigo($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function buscarxCuil($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxCuil($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     *
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    public function buscarxDni($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxDni($datos, $resultado, $numfilas);
    }

    public function buscarParaElastic(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarParaElastic($datos, $resultado, $numfilas);
    }

    public function InsertarPersona($datos, &$codigoInsertado) {


        if (!$this->Insertar($datos, $codigoInsertado))
            return false;

        $datos['IdPersona'] = $codigoInsertado;
        if (isset($datos['Size']) && $datos['Size'] != "" && isset($datos['Name']) && $datos['Name'] != "" && isset($datos['File']) && $datos['File'] != "") {

            if (!$this->InsertarImgDesdeTemporal($datos))
                return false;

            if (!$this->ModificarFotoPersona($datos))
                return false;
        }

        //$oAuditoriasPersonas = new cAuditoriasPersonas($this->conexion,$this->formato);
        $datos['IdPersona'] = $codigoInsertado;
        if (!isset($datos['NombreCompleto']) || $datos['NombreCompleto'] == "")
            $datos['NombreCompleto'] = trim($datos['Nombre'] . " " . $datos['Apellido']);

        /* $datos['Accion'] = INSERTAR;
         $datos['Estado'] = ACTIVO;
         $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
         $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
         if (!$oAuditoriasPersonas->InsertarLog($datos,$codigoInsertadolog))
             return false;*/

        if (!$this->_armarArrayElastic($datos, $datosElastic))
            return false;
        $oElastic = new Elastic\Modificacion(PERSONAS, $this->conexionES);
        if (!$oElastic->Insertar($datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }

    public function ModificarPersona($datos) {


        if (!$this->Modificar($datos, $datosRegistro))
            return false;


        if (isset($datos['Size']) && $datos['Size'] != "" && isset($datos['Name']) && $datos['Name'] != "" && isset($datos['File']) && $datos['File'] != "") {

            if (!$this->InsertarImgDesdeTemporal($datos))
                return false;

            if (!$this->ModificarFotoPersona($datos))
                return false;
        }

        /*$oAuditoriasPersonas = new cAuditoriasPersonas($this->conexion,$this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasPersonas->InsertarLog($datosRegistro,$codigoInsertadolog))
            return false;*/


        if (!$this->_armarArrayElastic($datos, $datosElastic))
            return false;


        $oElastic = new Elastic\Modificacion(PERSONAS, $this->conexionES);
        if (!$oElastic->Actualizar($datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }

    public function Insertar($datos, &$codigoInsertado) {

        if (!$this->_validarInsertar($datos))
            return false;


        if (!$this->_ValidarCUILxDni($datos))
            return false;


        $datos['Sexo'] = $this->obtenerSexoLetra($datos['Sexo']);

        $this->_setearNull($datos);
        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        if (!parent::InsertarDB($datos, $codigoInsertado))
            return false;


        return true;
    }

    public function Modificar($datos, &$datosRegistro) {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['Sexo'] = $this->obtenerSexoLetra($datos['Sexo']);

        $this->_SetearNull($datos);
        if (!parent::ModificarDB($datos))
            return false;


        return true;
    }

    public function ModificarFotoPersona(array $datos): bool {
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        if (!parent::ModificarFotoPersona($datos))
            return false;

        return true;
    }


    public function actualizarEsFamiliar($datos) {

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        $persona = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if ($persona['EsFamiliar'] == 1) {

            $datos['EsFamiliar'] = 0;
            if (!$this->actualizarPersonaFamiliar($datos))
                return false;
        }

        return true;
    }


    public function actualizarPersonaFamiliar($datos): bool {

        if (!parent::actualizarPersonaFamiliar($datos))
            return false;

        return true;
    }

    public function InsertarImgDesdeTemporal(&$datos) {


        $pathinfo = pathinfo($datos['Name']);
        $extension = strtolower($pathinfo['extension']);

        switch ($extension) {
            case "jpg":
            case "gif":
            case "png":
                break;
            default:
                $this->setError(400, "Formato de archivo no permitido.");
                return false;
                break;
        }

        if (!is_dir(PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR)) {
            @mkdir(PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR);
        }

        //Subir imagenes
        $nombrearchivo = "persona_" . $datos['IdPersona'] . "." . $extension;//.$extension;
        $carpetaorigen = PATH_STORAGE . "tmp/" . $datos['File'];

        $ancho = TAMANIOAVATARL;
        $calidad = 100;
        $forma = "T";
        $image_new = FuncionesPHPLocal::Guardafoto($carpetaorigen, $ancho, $calidad, $forma);
        $savePath = PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_L;

        if (!is_dir($savePath)) {
            @mkdir($savePath);
        }

        if (!imagejpeg($image_new, $savePath . "persona_" . $datos['IdPersona'] . '.' . $extension, $calidad)) {
            $this->setError(400, "Error guardar la imagen. ");
            return false;
        }


        $ancho = TAMANIOAVATARM;
        $calidad = 100;
        $forma = "T";
        $image_new = FuncionesPHPLocal::Guardafoto($carpetaorigen, $ancho, $calidad, $forma);
        $savePath = PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_M;
        if (!is_dir($savePath)) {
            @mkdir($savePath);
        }

        if (!imagejpeg($image_new, $savePath . "persona_" . $datos['IdPersona'] . '.' . $extension, $calidad)) {
            $this->setError(400, "Error guardar la imagen. ");
            return false;
        }

        $ancho = TAMANIOAVATARS;
        $calidad = 100;
        $forma = "T";
        $image_new = FuncionesPHPLocal::Guardafoto($carpetaorigen, $ancho, $calidad, $forma);
        $savePath = PATH_STORAGE . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR . CARPETA_SERVIDOR_MULTIMEDIA_AVATAR_S;
        if (!is_dir($savePath)) {
            @mkdir($savePath);
        }
        if (!imagejpeg($image_new, $savePath . "persona_" . $datos['IdPersona'] . '.jpg', $calidad)) {
            $this->setError(400, "Error guardar la imagen. ");
            return false;
        }

        @unlink($carpetaorigen);

        $datos['UbicacionAvatar'] = $nombrearchivo;
        return true;


    }






//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//

    protected function _validarInsertar(array $datos): bool {
        if (!empty($datos['Cuil'])) {
            if (!$this->buscarxCuil($datos, $resultado, $numfilas))
                return false;
            if ($numfilas > 0) {
                $this->setError('400', 'Error, el Cuil ya se encuentra en uso. ');
                return false;
            }
        }

        if (!$this->buscarxDni($datos, $resultado, $numfilas))
            return false;


        if ($numfilas > 0) {
            $this->setError('400', 'Error, el Dni ya se encuentra en uso. ');
            return false;
        }

        if (!$this->_validarDatosVacios($datos))
            return false;
        return true;
    }

    private function _ValidarModificar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        if (!$this->_ValidarDatosVacios($datos))
            return false;
        return true;
    }

    private function _ValidarDatosVacios($datos) {

        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == "") {
            $this->setError(400, "Debe ingresar Tipo Documento");
            return false;
        }

        if (!isset($datos['CUIL']) || $datos['CUIL'] == "") {
            $this->setError(400, "Debe ingresar CUIL");
            return false;
        }

        if (!isset($datos['DNI']) || $datos['DNI'] == "") {
            $this->setError(400, "Debe ingresar DNI");
            return false;
        }

        if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
            $this->setError(400, "Falta seleccionar un Nombre");
            return false;
        }

        if (!isset($datos['Apellido']) || $datos['Apellido'] == "") {
            $this->setError(400, "Debe ingresar Apellido");
            return false;
        }


        if (!isset($datos['Sexo']) || $datos['Sexo'] == "") {
            $this->setError(400, "Falta seleccionar un sexo");
            return false;
        }

        if (!isset($datos['IdEstadoPersona']) || $datos['IdEstadoPersona'] == "") {
            $this->setError(400, "Debe seleccionar un estado");
            return false;
        }

        if (isset($datos['FallecidoFecha']) && $datos['FallecidoFecha'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FallecidoFecha'], "FechaDDMMAAAA")) {
                $this->setError(400, "Debe ingresar una Fecha Fallecido Valida");
                return false;
            }

        }

        return true;
    }

    private function _ValidarCUILxDni($datos) {

        if (isset($datos['Dni']) && strlen($datos['Dni']) > 8) {
            $this->setError(400, 'Debe ingresar un DNI valido');
            return false;
        }

        $dni = $datos['Dni'];
        $cuil = $datos['Cuil'];


        $cuilDigitosMedio = substr($cuil, 2, 8);


        if (strcmp($dni, $cuilDigitosMedio) !== 0) {
            $this->setError(400, 'El DNI no coincide con el CUIL ingresado');
            return false;
        }

        return true;
    }

    protected function _armarArrayElastic($datos, &$datosElastic): bool {
        #busco datos basicos de elastic
        if (!$this->buscarParaElastic($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError('400', 'Error, no existe la persona');
            return false;
        }
        $datosPersona=$this->conexion->ObtenerSiguienteRegistro($resultado);
        $datosPersona["Antiguedades"]=array();
        #busco antiguedad par elastic
        if (!$this->buscarAntiguedadParaElastic($datos, $resultadoAnt, $numfilasAnt))
            return false;
        if ($numfilasAnt >0) {
            while($filaAntiguedad=$this->conexion->ObtenerSiguienteRegistro($resultadoAnt)){
                $Antiguedad=array(
                  "Id"=>$filaAntiguedad["IdAntiguedad"],
                    "FechaDesde"=>$filaAntiguedad["Fecha"],
                    "IdAntiguedadTipo"=>$filaAntiguedad["IdAntiguedadTipo"],
                    "Dias"=>$filaAntiguedad["Dias"],
                    "Estado"=>$filaAntiguedad["Estado"],
                    "Importada"=>$filaAntiguedad["Importada"]
                );
                $datosPersona["Antiguedades"][]=$Antiguedad;
            }
        }

        $datosElastic = Elastic\Personas::armarDatosElastic($datosPersona);

        return true;
    }

    private function _SetearNull(&$datos) {

        if (!isset($datos['IdExterno']) || $datos['IdExterno'] == "")
            $datos['IdExterno'] = "NULL";

        if (!isset($datos['CUIL']) || $datos['CUIL'] == "")
            $datos['CUIL'] = "NULL";

        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == "")
            $datos['IdTipoDocumento'] = 1;

        if (!isset($datos['DNI']) || $datos['DNI'] == "")
            $datos['DNI'] = "NULL";

        if (!isset($datos['Sexo']) || $datos['Sexo'] == "")
            $datos['Sexo'] = "NULL";

        if (!isset($datos['Nombre']) || $datos['Nombre'] == "")
            $datos['Nombre'] = "NULL";

        if (!isset($datos['Apellido']) || $datos['Apellido'] == "")
            $datos['Apellido'] = "NULL";

        if (!isset($datos['NombreCompleto']) || $datos['NombreCompleto'] == "")
            $datos['NombreCompleto'] = trim($datos['Nombre'] . " " . $datos['Apellido']);

        if (!isset($datos['Email']) || $datos['Email'] == "")
            $datos['Email'] = "NULL";

        if (!isset($datos['Telefono']) || $datos['Telefono'] == "")
            $datos['Telefono'] = "NULL";

        if (!isset($datos['UbicacionAvatar']) || $datos['UbicacionAvatar'] == "")
            $datos['UbicacionAvatar'] = "NULL";

        if (!isset($datos['FechaNacimiento']) || $datos['FechaNacimiento'] == "")
            $datos['FechaNacimiento'] = "NULL";
        else
            $datos['FechaNacimiento'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaNacimiento'], "dd/mm/aaaa", "aaaa-mm-dd");

        if (!isset($datos['FechaIngreso']) || $datos['FechaIngreso'] == "")
            $datos['FechaIngreso'] = "NULL";
        else
            $datos['FechaIngreso'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaIngreso'], "dd/mm/aaaa", "aaaa-mm-dd");


        if (!isset($datos['FechaAntiguedadDocente']) || $datos['FechaAntiguedadDocente'] == "")
            $datos['FechaAntiguedadDocente'] = "NULL";
        else
            $datos['FechaAntiguedadDocente'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaAntiguedadDocente'], "dd/mm/aaaa", "aaaa-mm-dd");

        if (!isset($datos['FechaAntiguedadAdministrativo']) || $datos['FechaAntiguedadAdministrativo'] == "")
            $datos['FechaAntiguedadAdministrativo'] = "NULL";
        else
            $datos['FechaAntiguedadAdministrativo'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaAntiguedadAdministrativo'], "dd/mm/aaaa", "aaaa-mm-dd");

        if (!isset($datos['TipoEstudio']) || $datos['TipoEstudio'] == "")
            $datos['TipoEstudio'] = "NULL";

        if (!isset($datos['FallecidoFecha']) || $datos['FallecidoFecha'] == "")
            $datos['FallecidoFecha'] = "NULL";
        else
            $datos['FallecidoFecha'] = FuncionesPHPLocal::ConvertirFecha($datos['FallecidoFecha'], "dd/mm/aaaa", "aaaa-mm-dd");

        if (!isset($datos['Estado']) || $datos['Estado'] == "")
            $datos['Estado'] = "NULL";

        if (!isset($datos['CausaBaja']) || $datos['CausaBaja'] == "")
            $datos['CausaBaja'] = "NULL";

        if (!isset($datos['BajaFecha']) || $datos['BajaFecha'] == "")
            $datos['BajaFecha'] = "NULL";

        if (!isset($datos['IdEstado']) || $datos['IdEstado'] == "")
            $datos['IdEstado'] = "NULL";

        if (!isset($datos['IdEstadoPersona']) || $datos['IdEstadoPersona'] == "")
            $datos['IdEstadoPersona'] = "NULL";

        if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario'] == "")
            $datos['AltaUsuario'] = "NULL";

        if (!isset($datos['AltaFecha']) || $datos['AltaFecha'] == "")
            $datos['AltaFecha'] = "NULL";

        if (!isset($datos['UltimaModificacionUsuario']) || $datos['UltimaModificacionUsuario'] == "")
            $datos['UltimaModificacionUsuario'] = "NULL";

        if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
            $datos['UltimaModificacionFecha'] = "NULL";

        if (!isset($datos['RegistroSeguridad']) || $datos['RegistroSeguridad'] == "")
            $datos['RegistroSeguridad'] = "NULL";

        if (!isset($datos['Calle']) || $datos['Calle'] == "")
            $datos['Calle'] = "NULL";

        if (!isset($datos['NumeroPuerta']) || $datos['NumeroPuerta'] == "")
            $datos['NumeroPuerta'] = "NULL";

        if (!isset($datos['Piso']) || $datos['Piso'] == "")
            $datos['Piso'] = "NULL";

        if (!isset($datos['Depto']) || $datos['Depto'] == "")
            $datos['Depto'] = "NULL";

        if (!isset($datos['CodigoPostal']) || $datos['CodigoPostal'] == "")
            $datos['CodigoPostal'] = "NULL";

        if (!isset($datos['IdDepartamento']) || $datos['IdDepartamento'] == "")
            $datos['IdDepartamento'] = "NULL";

        if (!isset($datos['IdProvincia']) || $datos['IdProvincia'] == "")
            $datos['IdProvincia'] = "NULL";

        if (!isset($datos['IdLocalidad']) || $datos['IdLocalidad'] == "")
            $datos['IdLocalidad'] = "NULL";

        if (!isset($datos['IdRegion']) || $datos['IdRegion'] == "")
            $datos['IdRegion'] = "NULL";

        if (!isset($datos['IdEstadoCivil']) || $datos['IdEstadoCivil'] == "")
            $datos['IdEstadoCivil'] = "NULL";

        if (!isset($datos['IdPaisNacionalidad']) || $datos['IdPaisNacionalidad'] == "")
            $datos['IdPaisNacionalidad'] = "NULL";

        if (!isset($datos['IdPaisNacimiento']) || $datos['IdPaisNacimiento'] == "")
            $datos['IdPaisNacimiento'] = "NULL";

        if (!isset($datos['IdProvinciaNacimiento']) || $datos['IdProvinciaNacimiento'] == "")
            $datos['IdProvinciaNacimiento'] = "NULL";

        if (!isset($datos['IdTipoDiscapacidad']) || $datos['IdTipoDiscapacidad'] == "")
            $datos['IdTipoDiscapacidad'] = "NULL";

        if (!isset($datos['GrupoSanguineo']) || $datos['GrupoSanguineo'] == "")
            $datos['GrupoSanguineo'] = "NULL";

        if (!isset($datos['Antiguedades']) || is_empty($datos['Antiguedades']))
            $datos['Antiguedades'] = array();

        return true;
    }

    public function obtenerSexoLetra(?string $letra): ?string {
        return match (strtoupper($letra)) {
            'F', '1' => 'F',
            'M', '2' => 'M',
            'X', '3' => 'X',
            default  => null,
        };
    }

    public function buscarAntiguedadParaElastic(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarAntiguedadParaElastic($datos, $resultado, $numfilas);
    }
}

?>
