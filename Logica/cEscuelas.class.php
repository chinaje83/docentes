<?php
include(DIR_CLASES_DB . "cEscuelas.db.php");

class cEscuelas extends cEscuelasdb {
    /**
     * Constructor de la clase cEscuelas.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO) {
        parent::__construct($conexion, $formato);
    }

    public static function preprocesarDatosElastic(array $datos): array {
        $datos['Tabla'] = 'Escuelas';
        $datos['Identificadores'] = [$datos['CodigoEscuela'], $datos['ClaveUnicaEscuela']];

        if (!FuncionesPHPLocal::isEmpty($datos['Latitud']) && !FuncionesPHPLocal::isEmpty($datos['Longitud'])) {
            $datos['Coordenadas'] = new stdClass();
            $datos['Coordenadas']->lat = $datos['Latitud'];
            $datos['Coordenadas']->lon = $datos['Longitud'];
        }

        return $datos;
    }

    /**
     * Destructor de la clase cEscuelas.
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

    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxIdEscuelaAnexoActivas($datos, &$resultado, &$numfilas): bool {

        $sparam['IdEscuelaAnexo'] = "-1";
        if (isset($datos['IdEscuelaAnexo']) && $datos['IdEscuelaAnexo'] != "")
            $sparam['IdEscuelaAnexo'] = $datos['IdEscuelaAnexo'];

        if (!parent::BuscarxIdEscuelaAnexoActivas($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxBloqueo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxBloqueo($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarParaElastic($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarParaElastic($datos, $resultado, $numfilas);
    }

    public function BuscarDatosCompletosxCodigo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarDatosCompletosxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarEscuelasRepublicar($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarEscuelasRepublicar($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xNombre' => 0,
            'Nombre' => "",
            'xCodigoEscuela' => 0,
            'CodigoEscuela' => "",
            'xClaveUnicaEscuela' => 0,
            'ClaveUnicaEscuela' => "",
            'xIdDepartamento' => 0,
            'IdDepartamento' => "",
            'xIdLocalidad' => 0,
            'IdLocalidad' => "",
            'xIdDistrito' => 0,
            'IdDistrito' => "",
            'xIdRegion' => 0,
            'IdRegion' => "-1",
            'xIdNivel' => 0,
            'IdNivel' => "-1",
            'xEsAnexo' => 0,
            'EsAnexo' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdEscuelaExcluir' => 0,
            'IdEscuelaExcluir' => "-1",
            'xHabilitada' => 0,
            'Habilitada' => "",
            'xBloqueo' => 0,
            'Bloqueo' => "",
            'xTest' => 0,
            'Test' => "",
            'xIdEnsenanza' => 0,
            'IdEnsenanza' => "",
            'xIdTipoOrganizacion' => 0,
            'IdTipoOrganizacion' => "",
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "IdEscuela ASC",
        ];

        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            $sparam['Nombre'] = utf8_decode($datos['Nombre']);
            $sparam['xNombre'] = 1;
        }
        if (isset($datos['CodigoEscuela']) && $datos['CodigoEscuela'] != "") {
            $sparam['CodigoEscuela'] = $datos['CodigoEscuela'];
            $sparam['xCodigoEscuela'] = 1;
        }
        if (isset($datos['ClaveUnicaEscuela']) && $datos['ClaveUnicaEscuela'] != "") {
            $sparam['ClaveUnicaEscuela'] = $datos['ClaveUnicaEscuela'];
            $sparam['xClaveUnicaEscuela'] = 1;
        }
        if (isset($datos['IdDepartamento']) && $datos['IdDepartamento'] != "") {
            $sparam['IdDepartamento'] = $datos['IdDepartamento'];
            $sparam['xIdDepartamento'] = 1;
        }
        if (isset($datos['IdLocalidad']) && $datos['IdLocalidad'] != "") {
            $sparam['IdLocalidad'] = $datos['IdLocalidad'];
            $sparam['xIdLocalidad'] = 1;
        }
        if (isset($datos['IdDistrito']) && $datos['IdDistrito'] != "") {
            $sparam['IdDistrito'] = $datos['IdDistrito'];
            $sparam['xIdDistrito'] = 1;
        }
        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }
        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }
        if (isset($datos['EsAnexo']) && $datos['EsAnexo'] != "") {
            $sparam['EsAnexo'] = $datos['EsAnexo'];
            $sparam['xEsAnexo'] = 1;
        }
        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdEscuelaExcluir']) && $datos['IdEscuelaExcluir'] != "") {
            $sparam['IdEscuelaExcluir'] = $datos['IdEscuelaExcluir'];
            $sparam['xIdEscuelaExcluir'] = 1;
        }

        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }

        if (isset($datos['Habilitada']) && $datos['Habilitada'] != "") {
            $sparam['Habilitada'] = $datos['Habilitada'];
            $sparam['xHabilitada'] = 1;
        }

        if (isset($datos['Bloqueo']) && $datos['Bloqueo'] != "") {
            $sparam['Bloqueo'] = $datos['Bloqueo'];
            $sparam['xBloqueo'] = 1;
        }

        if (isset($datos['Test']) && $datos['Test'] != "") {
            $sparam['Test'] = $datos['Test'];
            $sparam['xTest'] = 1;
        }

        if (isset($datos['IdEnsenanza']) && $datos['IdEnsenanza'] != "") {
            $sparam['IdEnsenanza'] = $datos['IdEnsenanza'];
            $sparam['xIdEnsenanza'] = 1;
        }

        if (isset($datos['IdTipoOrganizacion']) && $datos['IdTipoOrganizacion'] != "") {
            $sparam['IdTipoOrganizacion'] = $datos['IdTipoOrganizacion'];
            $sparam['xIdTipoOrganizacion'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaAvanzadaCSV($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xNombre' => 0,
            'Nombre' => "",
            'xCodigoEscuela' => 0,
            'CodigoEscuela' => "",
            'xClaveUnicaEscuela' => 0,
            'ClaveUnicaEscuela' => "",
            'xIdDepartamento' => 0,
            'IdDepartamento' => "",
            'xIdLocalidad' => 0,
            'IdLocalidad' => "",
            'xIdDistrito' => 0,
            'IdDistrito' => "",
            'xIdRegion' => 0,
            'IdRegion' => "-1",
            'xIdNivel' => 0,
            'IdNivel' => "-1",
            'xEsAnexo' => 0,
            'EsAnexo' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdEscuelaExcluir' => 0,
            'IdEscuelaExcluir' => "-1",
            'xHabilitada' => 0,
            'Habilitada' => "",
            'xBloqueo' => 0,
            'Bloqueo' => "",
            'xTest' => 0,
            'Test' => "",
            'xIdEnsenanza' => 0,
            'IdEnsenanza' => "",
            'xIdTipoOrganizacion' => 0,
            'IdTipoOrganizacion' => "",
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "IdEscuela ASC",
        ];

        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            $sparam['Nombre'] = utf8_decode($datos['Nombre']);
            $sparam['xNombre'] = 1;
        }
        if (isset($datos['CodigoEscuela']) && $datos['CodigoEscuela'] != "") {
            $sparam['CodigoEscuela'] = $datos['CodigoEscuela'];
            $sparam['xCodigoEscuela'] = 1;
        }
        if (isset($datos['ClaveUnicaEscuela']) && $datos['ClaveUnicaEscuela'] != "") {
            $sparam['ClaveUnicaEscuela'] = $datos['ClaveUnicaEscuela'];
            $sparam['xClaveUnicaEscuela'] = 1;
        }
        if (isset($datos['IdDepartamento']) && $datos['IdDepartamento'] != "") {
            $sparam['IdDepartamento'] = $datos['IdDepartamento'];
            $sparam['xIdDepartamento'] = 1;
        }
        if (isset($datos['IdLocalidad']) && $datos['IdLocalidad'] != "") {
            $sparam['IdLocalidad'] = $datos['IdLocalidad'];
            $sparam['xIdLocalidad'] = 1;
        }
        if (isset($datos['IdDistrito']) && $datos['IdDistrito'] != "") {
            $sparam['IdDistrito'] = $datos['IdDistrito'];
            $sparam['xIdDistrito'] = 1;
        }
        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }
        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }
        if (isset($datos['EsAnexo']) && $datos['EsAnexo'] != "") {
            $sparam['EsAnexo'] = $datos['EsAnexo'];
            $sparam['xEsAnexo'] = 1;
        }
        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdEscuelaExcluir']) && $datos['IdEscuelaExcluir'] != "") {
            $sparam['IdEscuelaExcluir'] = $datos['IdEscuelaExcluir'];
            $sparam['xIdEscuelaExcluir'] = 1;
        }

        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }

        if (isset($datos['Habilitada']) && $datos['Habilitada'] != "") {
            $sparam['Habilitada'] = $datos['Habilitada'];
            $sparam['xHabilitada'] = 1;
        }

        if (isset($datos['Bloqueo']) && $datos['Bloqueo'] != "") {
            $sparam['Bloqueo'] = $datos['Bloqueo'];
            $sparam['xBloqueo'] = 1;
        }

        if (isset($datos['Test']) && $datos['Test'] != "") {
            $sparam['Test'] = $datos['Test'];
            $sparam['xTest'] = 1;
        }

        if (isset($datos['IdEnsenanza']) && $datos['IdEnsenanza'] != "") {
            $sparam['IdEnsenanza'] = $datos['IdEnsenanza'];
            $sparam['xIdEnsenanza'] = 1;
        }

        if (isset($datos['IdTipoOrganizacion']) && $datos['IdTipoOrganizacion'] != "") {
            $sparam['IdTipoOrganizacion'] = $datos['IdTipoOrganizacion'];
            $sparam['xIdTipoOrganizacion'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzadaCSV($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarEscuelasHabilitadas($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xNombre' => 0,
            'Nombre' => "",
            'xCodigoEscuela' => 0,
            'CodigoEscuela' => "",
            'xClaveUnicaEscuela' => 0,
            'ClaveUnicaEscuela' => "",
            'xIdRegion' => 0,
            'IdRegion' => "-1",
            'limit' => '',
            'orderby' => "a.IdEscuela ASC",
        ];

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != '') {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != '') {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != '')
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != '')
            $sparam['limit'] = $datos['limit'];

        if (!parent::BuscarEscuelasHabilitadas($sparam, $resultado, $numfilas))
            return false;

        return true;
    }

    public function BuscarEscuelasHabilitadasTotal($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdRegion' => 0,
            'IdRegion' => "-1",
        ];

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != '') {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != '') {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (!parent::BuscarEscuelasHabilitadasTotal($sparam, $resultado, $numfilas))
            return false;

        return true;
    }

    public function BuscarEscuelasHabilitadasBloqueadasTotal($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdRegion' => 0,
            'IdRegion' => "-1",
        ];

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != '') {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != '') {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (!parent::BuscarEscuelasHabilitadasBloqueadasTotal($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    /**
     * Búsqueda rápida de escuelas habilitadas para informar importaciones (datos_importacion.php)
     */
    public function buscarHabilitadas($datos, &$resultado, &$numfilas): bool {

        $sparam = [
            'xCodigoEscuela' => 0,
            'CodigoEscuela' => '',
            'xNombre' => 0,
            'Nombre' => '',
            'xClaveUnicaEscuela' => 0,
            'ClaveUnicaEscuela' => '',
            'limit' => '',
            'orderby' => 'CodigoEscuela ASC',
        ];

        if (isset($datos['CodigoEscuela']) && $datos['CodigoEscuela'] != "") {
            $sparam['CodigoEscuela'] = $datos['CodigoEscuela'];
            $sparam['xCodigoEscuela'] = 1;

        }
        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            $sparam['Nombre'] = utf8_decode($datos['Nombre']);
            $sparam['xNombre'] = 1;
        }
        if (isset($datos['ClaveUnicaEscuela']) && $datos['ClaveUnicaEscuela'] != "") {
            $sparam['ClaveUnicaEscuela'] = $datos['ClaveUnicaEscuela'];
            $sparam['xClaveUnicaEscuela'] = 1;

        }
        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::buscarHabilitadas($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarHabilitacionxIdEscuela($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarHabilitacionxIdEscuela($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarEscuelasCombo(&$resultado, &$numfilas): bool {
        if (!parent::BuscarEscuelasCombo($resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarEscuelasComboxIdEscuela($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarEscuelasComboxIdEscuela($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarNivelesModalidadxId($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarNivelesModalidadxId($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxRegion($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxRegion($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxPersona($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxPersona($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function busquedaAvanzadaParaNovedad($datos, &$resultado, &$numfilas): bool {

        $sparam = [
            'xIdEscuela' => 0,
            'IdEscuela' => '-1',
            'xIdRegion' => 0,
            'IdRegion' => '-1',
            'xIdPersona' => 0,
            'IdPersona' => '-1',
            'xIdNivel' => 0,
            'IdNivel' => '-1',
        ];

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != '') {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != '') {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdPersona']) && $datos['IdPersona'] != '') {
            $sparam['IdPersona'] = $datos['IdPersona'];
            $sparam['xIdPersona'] = 1;
        }

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != '') {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }

        if (!parent::busquedaAvanzadaParaNovedad($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function autoCompletar($datos, &$resultado, &$numfilas): bool {

        if (!parent::autoCompletar($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function SectorSP(&$spnombre, &$sparam): void {
        parent::SectorSP($spnombre, $sparam);
    }

    public function DependenciasSP(&$spnombre, &$sparam): void {
        parent::DependenciasSP($spnombre, $sparam);
    }

    public function CategoriasSP(&$spnombre, &$sparam): void {
        parent::CategoriasSP($spnombre, $sparam);
    }

    public function PeriodosSP(&$spnombre, &$sparam): void {
        parent::PeriodosSP($spnombre, $sparam);
    }

    public function AmbitosSP(&$spnombre, &$sparam): void {
        parent::AmbitosSP($spnombre, $sparam);
    }

    public function AranceladoSP(&$spnombre, &$sparam): void {
        parent::AranceladoSP($spnombre, $sparam);
    }

    public function CooperadoraSP(&$spnombre, &$sparam): void {
        parent::CooperadoraSP($spnombre, $sparam);
    }

    public function PermanenciaSP(&$spnombre, &$sparam): void {
        parent::PermanenciaSP($spnombre, $sparam);
    }

    public function AlternanciaSP(&$spnombre, &$sparam): void {
        parent::AlternanciaSP($spnombre, $sparam);
    }

    public function DepartamentosSP(&$spnombre, &$sparam): void {
        parent::DepartamentosSP($spnombre, $sparam);
    }

    public function LocalidadesSP(&$spnombre, &$sparam): void {
        parent::LocalidadesSP($spnombre, $sparam);
    }

    public function RegionSP(&$spnombre, &$sparam): void {
        parent::RegionSP($spnombre, $sparam);
    }

    public function DistritosSP(&$spnombre, &$sparam): void {
        parent::DistritosSP($spnombre, $sparam);
    }

    public function Insertar($datos, &$codigoInsertado): bool {
        if (!$this->_ValidarInsertar($datos))
            return false;

        $this->_SetearNull($datos);
        $this->ObtenerProximoOrden($datos, $proxorden);
        $datos['IdEscuela'] = $proxorden;
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;
        if (!parent::Insertar($datos, $codigoInsertado))
            return false;
        $oAuditoriasEscuelas = new cAuditoriasEscuelas($this->conexion, $this->formato);
        $datos['IdEscuela'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasEscuelas->InsertarLog($datos, $codigoInsertadolog))
            return false;

        $oEscuelasPof = new cEscuelasPOF($this->conexion);
        if (!$oEscuelasPof->Insertar($datos, $codigoInsertadoPof)) {
            $error = $oEscuelasPof->getError();
            $this->setError($error['error'], $error['error_description']);
            return false;
        }

        return $this->actualizarElastic($datos);
    }

    public function Modificar($datos): bool {

        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $this->_SetearNull($datos);

        if (!parent::Modificar($datos))
            return false;
        $oAuditoriasEscuelas = new cAuditoriasEscuelas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        if (!$this->actualizarElastic($datos))
            return false;

        $conexionES = new Elastic\Conexion();

        # ACTUALIZACIÓN DE PUESTOS DE LA ESCUELA

        $oModificacionPuesto = new Elastic\Modificacion(SUFFIX_PUESTOS, $conexionES);

        $query = new stdClass();
        $query->bool = new stdClass();
        $query->bool->filter = new stdClass();
        $query->bool->filter->term = new stdClass();
        $query->bool->filter->term->{'Escuela.Id'} = new stdClass();
        $query->bool->filter->term->{'Escuela.Id'}->value = $datos['IdEscuela'];

        $script = 'ctx._source.Escuela=params.Escuela';
        $params = [];
        $params['Escuela']['Id'] = (int)$datos['IdEscuela'];
        $params['Escuela']['Nombre'] = utf8_encode($datos['Nombre']);
        $params['Escuela']['Codigo'] = $datos['CodigoEscuela'];
        $params['Escuela']['CUE'] = $datos['ClaveUnicaEscuela'];
        $params['Escuela']['Anexo'] = (bool)$datos['EsAnexo'];;
        $params['Escuela']['Region']['Id'] = $datos['IdRegion'];
        $params['Escuela']['Region']['Nombre'] = utf8_encode($datos['NombreRegion']);
        $lang = 'painless';

        if (!$oModificacionPuesto->actualizarPorConsulta($query, $script, $resultado, $lang, $params)) {
            $this->setError($oModificacionPuesto->getError());
            return false;
        }

        # ACTUALIZA REGION EN NOVEDADES

        $oModificacionNovedad = new Elastic\Modificacion(SUFFIX_NOVEDADES, $conexionES);

        $script = 'ctx._source.Escuela.Region=params.Escuela.Region';
        $params = [];
        $params['Escuela']['Region']['Id'] = $datos['IdRegion'];
        $params['Escuela']['Region']['Nombre'] = utf8_encode($datos['NombreRegion']);
        $lang = 'painless';

        if (!$oModificacionNovedad->actualizarPorConsulta($query, $script, $resultado, $lang, $params)) {
            $this->setError($oModificacionNovedad->getError());
            return false;
        }

        # ACTUALIZA REGION EN LICENCIAS

        $oLicenciasCargos = new cLicenciasCargos($this->conexion);

        if (!$oLicenciasCargos->ModificarxEscuela($datos)) {
            $this->setError($oLicenciasCargos->getError());
            return false;
        }

        return true;
    }

    public function ModificarAnexo($datos): bool {
        if (!$this->_ValidarModificarAnexo($datos, $datosRegistro))
            return false;

        if ($datos['EsAnexo'] == 0)
            $datos['IdEscuelaAnexo'] = "NULL";

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

        if (!parent::ModificarAnexo($datos))
            return false;
        $oAuditoriasEscuelas = new cAuditoriasEscuelas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }

    public function ModificarHabilitacion($datos): bool {
        if (!$this->_ValidarModificarHabilitacion($datos, $datosRegistro))
            return false;

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

        if (!parent::ModificarHabilitacion($datos))
            return false;
        $oAuditoriasEscuelas = new cAuditoriasEscuelas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }

    public function Eliminar($datos): bool {
        $oEscuelaNivelModalidad = new cEscuelasNivelModalidad($this->conexion, $this->formato);

        if (!$oEscuelaNivelModalidad->BuscarxIdEscuela($datos, $resultado, $numfilas)) {
            $error = $oEscuelaNivelModalidad->getError();
            $this->setError($error['error'], $error['error_description']);
            return false;
        }

        if ($numfilas > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                $datosEliminar['Id'] = $fila['Id'];
                if (!$oEscuelaNivelModalidad->EliminarxId($datosEliminar)) {
                    $error = $oEscuelaNivelModalidad->getError();
                    $this->setError($error['error'], $error['error_description']);
                    return false;
                }
            }
        }

        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasEscuelas = new cAuditoriasEscuelas($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!$oAuditoriasEscuelas->InsertarLog($datosLog, $codigoInsertadolog))
            return false;
        $datosmodif['IdEscuela'] = $datos['IdEscuela'];
        $datosmodif['Estado'] = ELIMINADO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        return true;
    }

    public function ModificarEstado($datos): bool {
        if (!parent::ModificarEstado($datos))
            return false;

        return $this->actualizarElastic($datos);
    }

    public function ValidarEscuela($datos, &$datosEscuela, &$tipoAcceso) {
        $tipoAcceso = $_SESSION['TipoAcceso'];
        switch ($tipoAcceso) {
            case 2:
                #si no es la escuela
                if ($_SESSION['IdEscuelaSeleccionada'] != $datos['IdEscuela']) {
                    $vecEscuelas = explode(",", $datos['IdEscuela']);
                    if (is_array($vecEscuelas) && !in_array($_SESSION['IdEscuelaSeleccionada'], $vecEscuelas))
                        return false;
                }
        }

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1)
            return false;

        $datosEscuela = $this->conexion->ObtenerSiguienteRegistro($resultado);

        return true;

    }

    public function getEscuelas(&$Escuelas) {
        if (isset($_SESSION['IdsRegion']) && !empty($_SESSION['IdsRegion'])) {
            $datosBusqueda['IdRegion'] = implode(",", $_SESSION['IdsRegion']);
        } else {
            $oUsuariosRolesDistritos = new cUsuariosRolesDistritos($this->conexion, $this->formato);
            $datos['IdUsuario'] = $_SESSION['usuariocod'];
            $datos['IdRol'] = current($_SESSION['rolcod']);
            if (!$oUsuariosRolesDistritos->BuscarxIdUsuarioxIdRol($datos, $resultado, $numfilas))
                return false;
            if ($numfilas > 0) {
                $arrayRegion = $arrayEscuelas = [];
                while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                    if ($fila['IdRegion'] != "")
                        $arrayRegion[$fila['IdRegion']] = $fila['IdRegion'];
                    if ($fila['IdEscuela'] != "")
                        $arrayEscuelas[$fila['IdEscuela']] = $fila['IdEscuela'];
                }
                if (count($arrayRegion) > 0)
                    $datosBusqueda['IdRegion'] = implode(",", $arrayRegion);
                if (count($arrayEscuelas) > 0)
                    $datosBusqueda['IdEscuela'] = implode(",", $arrayEscuelas);
            }

        };
        if (isset($_SESSION['IdEscuela']) && $_SESSION['IdEscuela'] != "" && is_array($_SESSION['IdEscuela'])) {

            $datosBusqueda['IdEscuela'] = implode(',', $_SESSION['IdEscuela']);

        }

        $datosBusqueda['Estado'] = ACTIVO;
        $datosBusqueda['orderby'] = "Nombre ASC";
        if (!$this->BusquedaAvanzada($datosBusqueda, $resultadoComboEscuela, $numfilasComboEscuela))
            return false;

        $Escuelas = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoComboEscuela))
            $Escuelas[] = $fila;

        return true;
    }

    public function SectorSPResult(&$resultado, &$numfilas): bool {
        $this->SectorSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function DependenciaSPResult(&$resultado, &$numfilas): bool {
        $this->DependenciasSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function CategoriasSPResult(&$resultado, &$numfilas): bool {
        $this->CategoriasSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function PeriodosSPResult(&$resultado, &$numfilas): bool {
        $this->PeriodosSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function AmbitosSPResult(&$resultado, &$numfilas): bool {
        $this->AmbitosSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function AranceladoSPResult(&$resultado, &$numfilas): bool {
        $this->AranceladoSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function CooperadoraSPResult(&$resultado, &$numfilas): bool {
        $this->CooperadoraSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function PermanenciaSPResult(&$resultado, &$numfilas): bool {
        $this->PermanenciaSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function AlternanciaSPResult(&$resultado, &$numfilas): bool {
        $this->AlternanciaSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function DepartamentosSPResult(&$resultado, &$numfilas): bool {
        $this->DepartamentosSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function LocalidadesSPResult(&$resultado, &$numfilas): bool {
        $this->LocalidadesSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function RegionesSPResult(&$resultado, &$numfilas): bool {
        $this->RegionSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function DistritosSPResult(&$resultado, &$numfilas): bool {
        $this->DistritosSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }


    public function EnsenanzasSPResult(&$resultado, &$numfilas): bool {

        $this->EnsenanzasSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar ensenanzas.");
            return false;
        }
        return true;
    }

    public function TiposOrganizacionSPResult(&$resultado, &$numfilas): bool {
        $this->TiposOrganizacionSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar tipos de organizacion. ");
            return false;
        }
        return true;
    }


    public function EscuelasZonasSPResult(&$resultado, &$numfilas): bool {
        $this->EscuelasZonasSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar las zonas de la escuela.");
            return false;
        }
        return true;
    }

    public function Activar(array $datos): bool {
        $datosmodif['IdEscuela'] = $datos['IdEscuela'];
        $datosmodif['Estado'] = ACTIVO;

        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        if (!$this->ModificarEstado($datosmodif))
            return false;

        $oAuditoriasEscuelas = new cAuditoriasEscuelas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }

    public function DesActivar(array $datos): bool {
        $datosmodif['IdEscuela'] = $datos['IdEscuela'];
        $datosmodif['Estado'] = NOACTIVO;

        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        if (!$this->ModificarEstado($datosmodif))
            return false;

        $oAuditoriasEscuelas = new cAuditoriasEscuelas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }

    public function ModificarOrdenCompleto($datos): bool {
        $datosmodif['IdEscuela'] = 1;
        $arregloOrden = explode(",", $datos['orden']);
        foreach ($arregloOrden as $IdEscuela) {
            $datosmodif['IdEscuela'] = $IdEscuela;
            if (!parent::ModificarOrden($datosmodif))
                return false;
            $datosmodif['IdEscuela']++;
        }
        return true;
    }

    public function InsertarNivelModalidad($datos) {
        $datos['Estado'] = ACTIVO;
        if (!$this->BuscarNivelesModalidadxId($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            $this->setError(400, "Actualmente ya existe el nivel y modalidad asociados en la escuela.");
            return false;
        }

        $oEscuelaNivelModalidad = new cEscuelasNivelModalidad($this->conexion);
        if (!$oEscuelaNivelModalidad->Insertar($datos, $codigoinsertado)) {
            $error = $oEscuelaNivelModalidad->getError();
            $this->setError($error['error'], $error['error_description']);
            return false;
        }

        return true;
    }


    public function EliminarNivelModalidad($datos) {
        $datosBusqueda['IdNivelModalidad'] = $datos['Id'];
        $oPlan = new cEscuelasNivelPlanes($this->conexion, $this->formato);
        if (!$oPlan->BusquedaAvanzada($datosBusqueda, $resultado, $numfilas)) {
            $error = $oPlan->getError();
            $this->setError($error['error'], $error['error_description']);
            return false;
        }

        if ($numfilas > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                $datosBusqueda['Id'] = $fila['Id'];
                $datosBusqueda['IdEscuela'] = $datos['IdEscuela'];
                if (!$oPlan->EliminarFisico($datosBusqueda)) {
                    $error = $oPlan->getError();
                    $this->setError($error['error'], $error['error_description']);
                    return false;
                }
            }
        }

        $oEscuelasNivelModalidad = new cEscuelasNivelModalidad($this->conexion, $this->formato);
        if (!$oEscuelasNivelModalidad->Eliminar($datos)) {
            $error = $oEscuelasNivelModalidad->getError();
            $this->setError($error['error'], $error['error_description']);
            return false;
        }

        return true;
    }


    public function armarObjetoElastic(array $datos, ?array &$datosRegistro, ?object &$datosElastic): bool {
        if (empty($datosRegistro)) {
            if (!$this->BuscarParaElastic($datos, $resultado, $numfilas))
                return false;

            if ($numfilas != 1) {
                $this->setError(400, 'No existe el registro');
                return false;
            }
            $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        }


        try {
            $datosElastic = Elastic\Tablas::armarDatosElastic(
                self::preprocesarDatosElastic($datosRegistro)
            );
        } catch (Bigtree\ExcepcionBase $e) {
            $this->setError($e->getError());
            return false;
        }

        return true;
    }

    private function ObtenerProximoOrden(array $datos, ?int &$proxorden): bool {
        $proxorden = 0;
        if (!parent::BuscarUltimoOrden($datos, $resultado, $numfilas))
            return false;
        if ($numfilas != 0) {
            $datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $proxorden = $datos['maximo'] + 1;
        }
        return true;
    }

    private function actualizarElastic(array $datos): bool {
        if (!$this->armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_TABLAS, new Elastic\Conexion());

        if (!$oElastic->Actualizar(self::preprocesarDatosElastic($datos), $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }


        return true;
    }

    public function ModificarEscuelaPrueba($datos): bool {

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, 'Error debe ingresar un código valido.');
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

        if (!parent::ModificarEscuelaPrueba($datos))
            return false;
        $oAuditoriasEscuelas = new cAuditoriasEscuelas($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelas->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;


        return true;
    }



    //-----------------------------------------------------------------------------------------
    //FUNCIONES PRIVADAS
    //-----------------------------------------------------------------------------------------

    private function _ValidarInsertar($datos) {


        if (isset($datos['Numero']) && $datos['Numero'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Numero'], "NumericoEntero")) {
                $this->setError(400, "Por favor, asegurese de ingresar un valor numerico valido en el campo Nro. de Calle.");
                return false;
            }
        }

        if (isset($datos['Piso']) && $datos['Piso'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Piso'], "NumericoEntero")) {
                $this->setError(400, "Por favor, asegurese de ingresar un valor numerico valido en el campo Piso .");
                return false;
            }
        }

        if (isset($datos['CodigoPostal']) && $datos['CodigoPostal'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['CodigoPostal'], "NumericoEntero")) {
                $this->setError(400, "Por favor, asegurese de ingresar un valor numerico valido en el campo Codigo Postal .");
                return false;
            }
        }


        if (isset($datos['NroRed']) && $datos['NroRed'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['NroRed'], "NumericoEntero")) {
                $this->setError(400, "Por favor, asegurese de ingresar un valor numerico valido en el campo Nro. de Red .");
                return false;
            }
        }

        if (!$this->_ValidarDatosVacios($datos))
            return false;
        return true;
    }


    private function _ValidarModificar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
            return false;
        }

        if (isset($datos['Numero']) && $datos['Numero'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Numero'], "NumericoEntero")) {
                $this->setError(400, "Por favor, asegurese de ingresar un valor numerico valido en el campo Numero de Calle.");
                return false;
            }
        }

        if (isset($datos['Piso']) && $datos['Piso'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Piso'], "NumericoEntero")) {

                $this->setError(400, "Por favor, asegurese de ingresar un valor numerico valido en el campo Piso .");
                return false;
            }
        }

        if (isset($datos['CodigoPostal']) && $datos['CodigoPostal'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['CodigoPostal'], "NumericoEntero")) {
                $this->setError(400, "Por favor, asegurese de ingresar un valor numerico valido en el campo Codigo Postal .");
                return false;
            }
        }


        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!$this->_ValidarDatosVacios($datos))
            return false;
        return true;
    }


    private function _ValidarModificarAnexo($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        if (!$this->_ValidarDatosVaciosAnexo($datos))
            return false;
        return true;
    }

    private function _ValidarModificarHabilitacion($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!isset($datos['Habilitada']) || $datos['Habilitada'] == "") {
            $this->setError(400, utf8_encode("Debe ingresar una opción"));
            return false;
        }

        return true;
    }

    private function _ValidarEliminar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, utf8_encode("Error debe ingresar un código valido."));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }


    private function _SetearNull(&$datos): void {
        if (!isset($datos['IdSector']) || $datos['IdSector'] == "")
            $datos['IdSector'] = "NULL";

        if (!isset($datos['EstadoLocalizacion']) || $datos['EstadoLocalizacion'] == "")
            $datos['EstadoLocalizacion'] = "NULL";

        if (!isset($datos['IdDependencia']) || $datos['IdDependencia'] == "")
            $datos['IdDependencia'] = "NULL";

        if (!isset($datos['IdCategoria']) || $datos['IdCategoria'] == "")
            $datos['IdCategoria'] = "NULL";

        if (!isset($datos['IdPeriodo']) || $datos['IdPeriodo'] == "")
            $datos['IdPeriodo'] = "NULL";

        if (!isset($datos['IdAmbito']) || $datos['IdAmbito'] == "")
            $datos['IdAmbito'] = "NULL";

        if (!isset($datos['IdArancelado']) || $datos['IdArancelado'] == "")
            $datos['IdArancelado'] = "NULL";

        if (!isset($datos['IdCooperadora']) || $datos['IdCooperadora'] == "")
            $datos['IdCooperadora'] = "NULL";

        if (!isset($datos['IdPermanencia']) || $datos['IdPermanencia'] == "")
            $datos['IdPermanencia'] = "NULL";

        if (!isset($datos['IdAlternancia']) || $datos['IdAlternancia'] == "")
            $datos['IdAlternancia'] = "NULL";

        if (!isset($datos['Nombre']) || $datos['Nombre'] == "")
            $datos['Nombre'] = "NULL";

        if (!isset($datos['CodigoEscuela']) || $datos['CodigoEscuela'] == "")
            $datos['CodigoEscuela'] = "NULL";

        if (!isset($datos['ClaveUnicaEscuela']) || $datos['ClaveUnicaEscuela'] == "")
            $datos['ClaveUnicaEscuela'] = "NULL";

        if (!isset($datos['IdProvincia']) || $datos['IdProvincia'] == "")
            $datos['IdProvincia'] = "NULL";

        if (!isset($datos['IdDepartamento']) || $datos['IdDepartamento'] == "")
            $datos['IdDepartamento'] = "NULL";

        if (!isset($datos['IdLocalidad']) || $datos['IdLocalidad'] == "")
            $datos['IdLocalidad'] = "NULL";

        if (!isset($datos['IdRegion']) || $datos['IdRegion'] == "")
            $datos['IdRegion'] = "NULL";

        if (!isset($datos['IdDistrito']) || $datos['IdDistrito'] == "")
            $datos['IdDistrito'] = "NULL";

        if (!isset($datos['Direccion']) || $datos['Direccion'] == "")
            $datos['Direccion'] = "NULL";

        if (!isset($datos['Numero']) || $datos['Numero'] == "")
            $datos['Numero'] = "NULL";

        if (!isset($datos['Piso']) || $datos['Piso'] == "")
            $datos['Piso'] = "NULL";

        if (!isset($datos['CodigoPostal']) || $datos['CodigoPostal'] == "")
            $datos['CodigoPostal'] = "NULL";

        if (!isset($datos['Longitud']) || $datos['Longitud'] == "")
            $datos['Longitud'] = "NULL";

        if (!isset($datos['Latitud']) || $datos['Latitud'] == "")
            $datos['Latitud'] = "NULL";

        if (!isset($datos['EsAnexo']) || $datos['EsAnexo'] == "")
            $datos['EsAnexo'] = 0;

        if (!isset($datos['TelefonoCodArea']) || $datos['TelefonoCodArea'] == "")
            $datos['TelefonoCodArea'] = "NULL";

        if (!isset($datos['TelefonoInstCargadoRA']) || $datos['TelefonoInstCargadoRA'] == "")
            $datos['TelefonoInstCargadoRA'] = "NULL";

        if (!isset($datos['TelefonoPadron']) || $datos['TelefonoPadron'] == "")
            $datos['TelefonoPadron'] = "NULL";

        if (!isset($datos['TelefonoInst']) || $datos['TelefonoInst'] == "")
            $datos['TelefonoInst'] = "NULL";

        if (!isset($datos['Email']) || $datos['Email'] == "")
            $datos['Email'] = "NULL";

        if (!isset($datos['SitioWeb']) || $datos['SitioWeb'] == "")
            $datos['SitioWeb'] = "NULL";

        if (!isset($datos['NroRed']) || $datos['NroRed'] == "")
            $datos['NroRed'] = "NULL";

        if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "")
            $datos['Descripcion'] = "NULL";

        if (!isset($datos['FechaDesde']) || $datos['FechaDesde'] == "")
            $datos['FechaDesde'] = "NULL";
        else
            $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], "dd/mm/aaaa", "aaaammdd");

        if (!isset($datos['FechaHasta']) || $datos['FechaHasta'] == "")
            $datos['FechaHasta'] = "NULL";
        else
            $datos['FechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], "dd/mm/aaaa", "aaaammdd");

        if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
            $datos['UltimaModificacionFecha'] = "NULL";

        if (!isset($datos['Habilitada']) || $datos['Habilitada'] == "")
            $datos['Habilitada'] = 0;

        if (!isset($datos['IdEnsenanza']) || $datos['IdEnsenanza'] == "")
            $datos['IdEnsenanza'] = 0;

        if (!isset($datos['IdTipoOrganizacion']) || $datos['IdTipoOrganizacion'] == "")
            $datos['IdTipoOrganizacion'] = 0;
    }


    private function _ValidarDatosVaciosAnexo($datos) {
        if (!isset($datos['EsAnexo']) || $datos['EsAnexo'] == "") {
            $this->setError(400, "Debe ingresar un anexo");
            return false;
        }


        if (!isset($datos['IdEscuela']) || $datos['IdEscuela'] == "") {
            $this->setError(400, "Debe seleccionar escuela");
            return false;
        }

        if ($datos['EsAnexo'] == 1) {
            if (!isset($datos['IdEscuelaAnexo']) || $datos['IdEscuelaAnexo'] == "") {
                $this->setError(400, "Debe seleccionar escuela");
                return false;
            }

            if ($datos['IdEscuelaAnexo'] == $datos['IdEscuela']) {
                $this->setError(400, "Error, no puede seleccionar la misma escuela como anexo");
                return false;
            }
        }

        return true;
    }

    private function _ValidarDatosVacios($datos) {
        if (!isset($datos['IdSector']) || $datos['IdSector'] == "") {
            $this->setError(400, "Debe ingresar un sector");
            return false;
        }

        if (isset($datos['IdSector']) && $datos['IdSector'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdSector'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo Sector.");
                return false;
            }
            if (strlen($datos['IdSector']) > 11) {
                $this->setError(400, "Error, el campo Sector no puede ser mayor a 11.");
                return false;
            }
        }

        if (!isset($datos['EstadoLocalizacion']) || $datos['EstadoLocalizacion'] == "") {
            $this->setError(400, utf8_encode("Debe ingresar un estado de localización"));
            return false;
        }

        if (isset($datos['EstadoLocalizacion']) && $datos['EstadoLocalizacion'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['EstadoLocalizacion'], "NumericoEntero")) {
                $this->setError(400, utf8_encode("Debe ingresar un campo numérico para el campo Estado de Localización."));
                return false;
            }
            if (strlen($datos['EstadoLocalizacion']) > 11) {
                $this->setError(400, utf8_encode("El campo Estado de Localización no puede ser mayor a 11."));
                return false;
            }
        }

        if (!isset($datos['IdDependencia']) || $datos['IdDependencia'] == "") {
            $this->setError(400, "Debe ingresar una dependencia");
            return false;
        }

        if (isset($datos['IdDependencia']) && $datos['IdDependencia'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdDependencia'], "NumericoEntero")) {
                $this->setError(400, utf8_encode("Debe ingresar un campo numérico para el campo Dependencia."));
                return false;
            }
            if (strlen($datos['IdDependencia']) > 11) {
                $this->setError(400, "El campo Dependencia no puede ser mayor a 11.");
                return false;
            }
        }

        if (!isset($datos['IdCategoria']) || $datos['IdCategoria'] == "") {
            $this->setError(400, utf8_encode("Debe ingresar una categoría"));
            return false;
        }

        if (isset($datos['IdCategoria']) && $datos['IdCategoria'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdCategoria'], "NumericoEntero")) {
                $this->setError(400, utf8_encode("Debe ingresar un campo numérico para el campo Categoría."));
                return false;
            }
            if (strlen($datos['IdCategoria']) > 11) {
                $this->setError(400, utf8_encode("El campo Categoría no puede ser mayor a 11."));
                return false;
            }
        }

        if (!isset($datos['IdPeriodo']) || $datos['IdPeriodo'] == "") {
            $this->setError(400, utf8_encode("Debe ingresar un periódo"));
            return false;
        }

        if (isset($datos['IdPeriodo']) && $datos['IdPeriodo'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdPeriodo'], "NumericoEntero")) {
                $this->setError(400, utf8_encode("Debe ingresar un campo numérico para el campo Periódo."));
                return false;
            }
            if (strlen($datos['IdPeriodo']) > 11) {
                $this->setError(400, utf8_encode("El campo Periódo no puede ser mayor a 11."));
                return false;
            }
        }

        if (!isset($datos['IdAmbito']) || $datos['IdAmbito'] == "") {
            $this->setError(400, utf8_encode("Debe ingresar un ámbito"));
            return false;
        }

        if (isset($datos['IdAmbito']) && $datos['IdAmbito'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdAmbito'], "NumericoEntero")) {
                $this->setError(400, utf8_encode("Debe ingresar un campo numérico para el campo ámbito."));
                return false;
            }
            if (strlen($datos['IdAmbito']) > 11) {
                $this->setError(400, utf8_encode("El campo Ámbito no puede ser mayor a 11."));
                return false;
            }
        }

        if (!isset($datos['IdArancelado']) || $datos['IdArancelado'] == "") {
            $this->setError(400, "Debe ingresar un arancelado");
            return false;
        }

        if (isset($datos['IdArancelado']) && $datos['IdArancelado'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdArancelado'], "NumericoEntero")) {
                $this->setError(400, utf8_encode("Debe ingresar un campo numérico para el campo Arancelado."));
                return false;
            }
            if (strlen($datos['IdArancelado']) > 11) {
                $this->setError(400, "El campo Arancelado no puede ser mayor a 11 .");
                return false;
            }
        }

        if (!isset($datos['IdCooperadora']) || $datos['IdCooperadora'] == "") {
            $this->setError(400, "Debe ingresar cooperadora");
            return false;
        }

        if (isset($datos['IdCooperadora']) && $datos['IdCooperadora'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdCooperadora'], "NumericoEntero")) {
                $this->setError(400, utf8_encode("Debe ingresar un campo numérico para el campo Cooperadora."));
                return false;
            }
            if (strlen($datos['IdCooperadora']) > 11) {
                $this->setError(400, "El campo Cooperadora no puede ser mayor a 11.");
                return false;
            }
        }

        if (!isset($datos['IdPermanencia']) || $datos['IdPermanencia'] == "") {
            $this->setError(400, "Debe ingresar una permanencia");
            return false;
        }

        if (isset($datos['IdPermanencia']) && $datos['IdPermanencia'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdPermanencia'], "NumericoEntero")) {
                $this->setError(400, utf8_encode("Error debe ingresar un campo numérico para el campo Permanencia."));
                return false;
            }
            if (strlen($datos['IdPermanencia']) > 11) {
                $this->setError(400, "Error, el campo Permanencia no puede ser mayor a 11.");
                return false;
            }
        }

        if (!isset($datos['IdAlternancia']) || $datos['IdAlternancia'] == "") {
            $this->setError(400, "Debe ingresar una alternancia");
            return false;
        }

        if (isset($datos['IdAlternancia']) && $datos['IdAlternancia'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdAlternancia'], "NumericoEntero")) {
                $this->setError(400, utf8_encode("Debe ingresar un campo numérico para el campo Alternancia."));
                return false;
            }
            if (strlen($datos['IdAlternancia']) > 11) {
                $this->setError(400, "El campo Alternancia no puede ser mayor a 11.");
                return false;
            }
        }

        if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
            $this->setError(400, "Debe ingresar un nombre");
            return false;
        }

        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            if (strlen($datos['Nombre']) > 255) {
                $this->setError(400, "Error, el campo Nombre no puede ser mayor a 255 .");
                return false;
            }
        }

        if (!isset($datos['CodigoEscuela']) || $datos['CodigoEscuela'] == "") {
            $this->setError(400, utf8_encode("Debe ingresar un código de escuela"));
            return false;
        }

        if (isset($datos['CodigoEscuela']) && $datos['CodigoEscuela'] != "") {
            if (strlen($datos['CodigoEscuela']) > 20) {
                $this->setError(400, utf8_encode("El campo Código Escuela no puede ser mayor a 20."));
                return false;
            }
        }

        if (!isset($datos['ClaveUnicaEscuela']) || $datos['ClaveUnicaEscuela'] == "") {
            $this->setError(400, "Debe ingresar un CUE");
            return false;
        }

        if (isset($datos['ClaveUnicaEscuela']) && $datos['ClaveUnicaEscuela'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['ClaveUnicaEscuela'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo CUE.");
                return false;
            }
            if (strlen($datos['ClaveUnicaEscuela']) > 9) {
                $this->setError(400, "Error, el campo CUE no puede ser mayor a 9 .");
                return false;
            }
        }


        if (!isset($datos['IdDepartamento']) || $datos['IdDepartamento'] == "") {
            $this->setError(400, "Debe ingresar un departamento");
            return false;
        }

        if (isset($datos['IdDepartamento']) && $datos['IdDepartamento'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdDepartamento'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo Departamento.");
                return false;
            }
            if (strlen($datos['IdDepartamento']) > 10) {
                $this->setError(400, "Error, el campo Departamento no puede ser mayor a 10 .");
                return false;
            }
        }

        if (!isset($datos['IdMunicipio']) || $datos['IdMunicipio'] == "") {
            $this->setError(400, "Debe ingresar un municipio");
            return false;
        }

        if (isset($datos['IdMunicipio']) && $datos['IdMunicipio'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdMunicipio'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo Municipio.");
                return false;
            }
            if (strlen($datos['IdMunicipio']) > 10) {
                $this->setError(400, "Error, el campo Municipio no puede ser mayor a 10 .");
                return false;
            }
        }

        if (!isset($datos['IdLocalidad']) || $datos['IdLocalidad'] == "") {
            $this->setError(400, "Debe ingresar una localidad");
            return false;
        }

        if (isset($datos['IdLocalidad']) && $datos['IdLocalidad'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdLocalidad'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo Localidad.");
                return false;
            }
            if (strlen($datos['IdLocalidad']) > 10) {
                $this->setError(400, "Error, el campo Localidad no puede ser mayor a 10 .");
                return false;
            }
        }

        if (!isset($datos['IdRegion']) || $datos['IdRegion'] == "") {
            $this->setError(400, utf8_encode("Debe ingresar una región"));
            return false;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdRegion'], "NumericoEntero")) {
                $this->setError(400, utf8_encode("Error debe ingresar un campo numérico para el campo Región."));
                return false;
            }
            if (strlen($datos['IdRegion']) > 11) {
                $this->setError(400, utf8_encode("Error, el campo Región no puede ser mayor a 11."));
                return false;
            }
        }

        /*if (!isset($datos['IdDistrito']) || $datos['IdDistrito']=="")
        {
            $this->setError(400,"Debe ingresar un distrito");
            return false;
        }*/

        if (isset($datos['IdDistrito']) && $datos['IdDistrito'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdDistrito'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo Distrito.");
                return false;
            }
            if (strlen($datos['IdDistrito']) > 10) {
                $this->setError(400, "Error, el campo Distrito no puede ser mayor a 10 .");
                return false;
            }
        }

        if (!isset($datos['Direccion']) || $datos['Direccion'] == "") {
            $this->setError(400, utf8_encode("Debe ingresar una dirección"));
            return false;
        }

        if (isset($datos['Direccion']) && $datos['Direccion'] != "") {
            if (strlen($datos['Direccion']) > 100) {
                $this->setError(400, utf8_encode("Error, el campo Dirección no puede ser mayor a 100."));
                return false;
            }
        }

        if (!isset($datos['Numero']) || $datos['Numero'] == "") {
            $this->setError(400, "Debe ingresar un nro. de calle");
            return false;
        }

        if (isset($datos['Numero']) && $datos['Numero'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Numero'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo Nro. Calle.");
                return false;
            }
            if (strlen($datos['Numero']) > 5) {
                $this->setError(400, "Error, el campo Nro. Calle no puede ser mayor a 5 .");
                return false;
            }
        }

        if (isset($datos['Piso']) && $datos['Piso'] != "") {
            if (strlen($datos['Piso']) > 5) {
                $this->setError(400, "Error, el campo  no puede ser mayor a 5 .");
                return false;
            }
        }

        if (!isset($datos['CodigoPostal']) || $datos['CodigoPostal'] == "") {
            $this->setError(400, utf8_encode("Debe ingresar un código postal"));
            return false;
        }

        if (isset($datos['CodigoPostal']) && $datos['CodigoPostal'] != "") {
            if (strlen($datos['CodigoPostal']) > 10) {
                $this->setError(400, utf8_encode("Error, el campo Código Postal no puede ser mayor a 10."));
                return false;
            }
        }

        /*		if (!isset($datos['EsAnexo']) || $datos['EsAnexo']=="")
                {
                    $this->setError(400,"Debe ingresar un anexo");
                    return false;
                }*/

        /*		if (!isset($datos['TelefonoCodArea']) || $datos['TelefonoCodArea']=="")
                {
                    $this->setError(400,utf8_encode("Debe ingresar un Teléfono de Cód. área"));
                    return false;
                }*/

        if (isset($datos['TelefonoCodArea']) && $datos['TelefonoCodArea'] != "") {
            if (strlen($datos['TelefonoCodArea']) > 20) {
                $this->setError(400, utf8_encode("Error, el campo Teléfono de Cód. área no puede ser mayor a 20."));
                return false;
            }
        }

        /*if (!isset($datos['TelefonoInstCargadoRA']) || $datos['TelefonoInstCargadoRA']=="")
        {
            $this->setError(400,utf8_encode("Debe ingresar un Teléfono Inst. Cargado RA"));
            return false;
        }*/

        if (isset($datos['TelefonoInstCargadoRA']) && $datos['TelefonoInstCargadoRA'] != "") {
            if (strlen($datos['TelefonoInstCargadoRA']) > 20) {
                $this->setError(400, utf8_encode("Error, el campo Teléfono Inst. Cargado RA no puede ser mayor a 20."));
                return false;
            }
        }

        /*if (!isset($datos['TelefonoPadron']) || $datos['TelefonoPadron']=="")
        {
            $this->setError(400,utf8_encode("Debe ingresar un Teléfono Padrón"));
            return false;
        }*/

        if (isset($datos['TelefonoPadron']) && $datos['TelefonoPadron'] != "") {
            if (strlen($datos['TelefonoPadron']) > 20) {
                $this->setError(400, utf8_encode("Error, el campo Teléfono Padrón no puede ser mayor a 20."));
                return false;
            }
        }

        /*	if (!isset($datos['TelefonoInst']) || $datos['TelefonoInst']=="")
            {
                $this->setError(400,utf8_encode("Debe ingresar un Teléfono Inst"));
                return false;
            }*/

        if (isset($datos['TelefonoInst']) && $datos['TelefonoInst'] != "") {
            if (strlen($datos['TelefonoInst']) > 20) {
                $this->setError(400, utf8_encode("Error, el campo Teléfono Inst no puede ser mayor a 20."));
                return false;
            }
        }

        /*		if (!isset($datos['Email']) || $datos['Email']=="")
                {
                    $this->setError(400,"Debe ingresar un email");
                    return false;
                }*/

        if (isset($datos['Email']) && $datos['Email'] != "") {
            if (strlen($datos['Email']) > 60) {
                $this->setError(400, "Error, el campo Email no puede ser mayor a 60 .");
                return false;
            }
        }

        /*		if (!isset($datos['SitioWeb']) || $datos['SitioWeb']=="")
                {
                    $this->setError(400,"Debe ingresar un sitio web");
                    return false;
                }*/

        if (isset($datos['SitioWeb']) && $datos['SitioWeb'] != "") {
            if (strlen($datos['SitioWeb']) > 60) {
                $this->setError(400, "Error, el campo Sitio Web no puede ser mayor a 60 .");
                return false;
            }
        }

        if (!isset($datos['NroRed']) || $datos['NroRed'] == "") {
            $this->setError(400, "Debe ingresar un nro. red");
            return false;
        }

        if (isset($datos['NroRed']) && $datos['NroRed'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['NroRed'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo Nro. Red.");
                return false;
            }
            if (strlen($datos['NroRed']) > 4) {
                $this->setError(400, "Error, el campo Nro. Red no puede ser mayor a 4 .");
                return false;
            }
        }


        if (!empty($datos['FechaDesde']) && !empty($datos['FechaHasta'])) {

            $desde = DateTime::createFromFormat('d/m/Y', $datos['FechaDesde']);
            $hasta = DateTime::createFromFormat('d/m/Y', $datos['FechaHasta']);

            if ($desde && $hasta && $desde > $hasta) {
                $this->setError(400, "Fecha Desde no puede ser mayor a Fecha Hasta");
                return false;
            }
        }


        if ((isset($datos['FechaHasta']) && $datos['FechaHasta'] != "" && $datos['FechaHasta'] != "NULL") && ((!isset($datos['FechaDesde']) || $datos['FechaDesde'] == "" || $datos['FechaDesde'] == "NULL"))) {
            $this->setError(400, "Debe ingresar Fecha Desde");
            return false;
        }

        return true;
    }


}
