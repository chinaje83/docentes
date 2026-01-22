<?php
include(DIR_CLASES_DB.'cDocumentosLicencias.db.php');

class cDocumentosLicencias extends cDocumentosLicenciasDB
{
    /**
     * Constructor de la clase cDocumentosLicencias.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion,$formato=FMT_TEXTO) {
        parent::__construct($conexion,$formato);
    }
    /**
     * Destructor de la clase cDocumentosLicencias.
     */
    function __destruct() {
        parent::__destruct();
    }
    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public function getError(): array {
        return $this->error;
    }

    public function BuscarxCodigo($datos, &$resultado,&$numfilas): bool {

        if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        return true;
    }

    public function BuscarxDocumento($datos, &$resultado,&$numfilas): bool {

        if (!parent::BuscarxDocumento($datos,$resultado,$numfilas))
            return false;

        return true;
    }

    public function Insertar($datos): bool {

        if (!self::_ValidarInsertar($datos))
            return false;

        self::_SetearAltaModif($datos);

        if (!parent::Insertar($datos))
            return false;

        if (!self::ModificarHashDato($datos)) {
            return false;
        }

        return true;
    }

    public function ModificarHashDato($datos): bool {

        if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c�digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        $arrayhash = [
            'IdDocumento' =>'',
            'IdPuesto' =>''
        ];

        $result = array_intersect_key($datos, $arrayhash);
        $datos['HashDato'] = md5 (implode('', $result));
        if (!parent::ModificarHashDato($datos))
            return false;

        return true;
    }

    private function _ValidarInsertar($datos) {

        if (!self::_ValidarDatosVacios($datos))
            return false;

        return true;
    }

    private function _ValidarDatosVacios($datos) {

        if (FuncionesPHPLocal::isEmpty($datos['IdDocumento'])) {
            self::setError(400, 'No existe documento');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdLicencia'])) {
            self::setError(400, 'No se encuentran licencias');
            return false;
        }

        return true;
    }

    private function _SetearAltaModif(&$datos) {

        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        if (isset($_SESSION['IdEscuelaSeleccionada']) && $_SESSION['IdEscuelaSeleccionada'] != ""){
            $datos['AltaEscuela'] = $datos['UltimaModificacionEscuela'] = $_SESSION['IdEscuelaSeleccionada']??'NULL';
        }else{
            $datos['AltaEscuela'] = $datos['UltimaModificacionEscuela'] = $_SESSION['IdEscuela']??'NULL';
        }

        $datos['AltaRol'] = $datos['UltimaModificacionRol'] = implode(',', $_SESSION['rolcod']);
        $datos['HashDato'] = '';
    }
}
