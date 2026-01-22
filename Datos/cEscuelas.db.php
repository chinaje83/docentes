<?php

abstract class cEscuelasDB {
    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    /**
     * Constructor de la clase cEscuelasDB.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion, $formato) {

        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    /**
     * Destructor de la clase cEscuelasDB.
     */
    function __destruct() {}

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public abstract function getError(): array;


    /**
     * Guarda un mensaje de error
     *
     * @param string|array $error
     * @param string       $error_description
     */
    protected function setError($error, $error_description = ''): void {
        $this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
    }

    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_Escuelas_xIdEscuela";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarxIdEscuelaAnexoActivas(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_Escuelas_xIdEscuelaAnexo_Activos";
        $sparam = [
            'pIdEscuelaAnexo' => $datos['IdEscuelaAnexo'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarxBloqueo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPOF_xBloqueo";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por bloqueo. ");
            return false;
        }
        return true;
    }


    protected function BuscarParaElastic(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Escuelas_es_xIdEscuela";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar para elastic. ");
            return false;
        }
        return true;
    }

    protected function BuscarDatosCompletosxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_Escuelas_xIdEscuela_DatosCompletos";
        $sparam = [
            'pIdEscuela' => FuncionesPHPLocal::DevolverIdEscuela($datos['IdEscuela']),
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarEscuelasRepublicar(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasRepublicar_xIdEstado";
        $sparam = [
            'pBDInterfaz' => BDINTERFACES,
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Escuelas_busqueda_avanzada";
        $sparam = [
            'pxNombre' => $datos['xNombre'],
            'pNombre' => $datos['Nombre'],
            'pxCodigoEscuela' => $datos['xCodigoEscuela'],
            'pCodigoEscuela' => $datos['CodigoEscuela'],
            'pxClaveUnicaEscuela' => $datos['xClaveUnicaEscuela'],
            'pClaveUnicaEscuela' => $datos['ClaveUnicaEscuela'],
            'pxIdDepartamento' => $datos['xIdDepartamento'],
            'pIdDepartamento' => $datos['IdDepartamento'],
            'pxIdLocalidad' => $datos['xIdLocalidad'],
            'pIdLocalidad' => $datos['IdLocalidad'],
            'pxIdDistrito' => $datos['xIdDistrito'],
            'pIdDistrito' => $datos['IdDistrito'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRegion' => $datos['IdRegion'],
            'pxIdNivel' => $datos['xIdNivel'],
            'pIdNivel' => $datos['IdNivel'],
            'pxEsAnexo' => $datos['xEsAnexo'],
            'pEsAnexo' => $datos['EsAnexo'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdEscuelaExcluir' => $datos['xIdEscuelaExcluir'],
            'pIdEscuelaExcluir' => $datos['IdEscuelaExcluir'],
            'pxHabilitada' => $datos['xHabilitada'],
            'pHabilitada' => $datos['Habilitada'],
            'pxBloqueo' => $datos['xBloqueo'],
            'pBloqueo' => $datos['Bloqueo'],
            'pxTest' => $datos['xTest'],
            'pTest' => $datos['Test'],
            'pxIdEnsenanza' => $datos['xIdEnsenanza'],
            'pIdEnsenanza' => $datos['IdEnsenanza'],
            'pxIdTipoOrganizacion' => $datos['xIdTipoOrganizacion'],
            'pIdTipoOrganizacion' => $datos['IdTipoOrganizacion'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby']
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al realizar la búsqueda avanzada. ");
            return false;
        }
        return true;
    }

    protected function BusquedaAvanzadaCSV(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Escuelas_busqueda_avanzada_csv";
        $sparam = [
            'pxNombre' => $datos['xNombre'],
            'pNombre' => $datos['Nombre'],
            'pxCodigoEscuela' => $datos['xCodigoEscuela'],
            'pCodigoEscuela' => $datos['CodigoEscuela'],
            'pxClaveUnicaEscuela' => $datos['xClaveUnicaEscuela'],
            'pClaveUnicaEscuela' => $datos['ClaveUnicaEscuela'],
            'pxIdDepartamento' => $datos['xIdDepartamento'],
            'pIdDepartamento' => $datos['IdDepartamento'],
            'pxIdLocalidad' => $datos['xIdLocalidad'],
            'pIdLocalidad' => $datos['IdLocalidad'],
            'pxIdDistrito' => $datos['xIdDistrito'],
            'pIdDistrito' => $datos['IdDistrito'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRegion' => $datos['IdRegion'],
            'pxIdNivel' => $datos['xIdNivel'],
            'pIdNivel' => $datos['IdNivel'],
            'pxEsAnexo' => $datos['xEsAnexo'],
            'pEsAnexo' => $datos['EsAnexo'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdEscuelaExcluir' => $datos['xIdEscuelaExcluir'],
            'pIdEscuelaExcluir' => $datos['IdEscuelaExcluir'],
            'pxHabilitada' => $datos['xHabilitada'],
            'pHabilitada' => $datos['Habilitada'],
            'pxBloqueo' => $datos['xBloqueo'],
            'pBloqueo' => $datos['Bloqueo'],
            'pxTest' => $datos['xTest'],
            'pTest' => $datos['Test'],
            'pxIdEnsenanza' => $datos['xIdEnsenanza'],
            'pIdEnsenanza' => $datos['IdEnsenanza'],
            'pxIdTipoOrganizacion' => $datos['xIdTipoOrganizacion'],
            'pIdTipoOrganizacion' => $datos['IdTipoOrganizacion'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby']
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al realizar la búsqueda avanzada. ");
            return false;
        }
        return true;
    }


    protected function busquedaAvanzadaParaNovedad(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_Escuelas_busqueda_avanzada_novedad';
        $sparam = [
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRegion' => $datos['IdRegion'],
            'pxIdPersona' => $datos['xIdPersona'],
            'pIdPersona' => $datos['IdPersona'],
            'pxIdNivel' => $datos['xIdNivel'],
            'pIdNivel' => $datos['IdNivel'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda avanzada para novedad');
            return false;
        }
        return true;
    }


    protected function BuscarAuditoriaRapida(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Escuelas_AuditoriaRapida";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarNivelesModalidadxId(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasNivelModalidad_xIds";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdNivel' => $datos['IdNivel'],
            'pIdModalidad' => $datos['IdModalidad'],
            'pEstado' => $datos['Estado'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarEscuelasCombo(&$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Escuelas_Combo";
        $sparam = [];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarEscuelasComboxIdEscuela(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Escuelas_xIdEscuela_Combo";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarxRegion(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Escuelas_xIdRegion";
        $sparam = [
            'pIdsRegion' => $datos['IdsRegion'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarxPersona(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Escuelas_xIdPersona";
        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function autoCompletar(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Escuelas_autocompletar";
        $sparam = [
            'pCadena' => $datos['Cadena'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar autocompletado. ");
            return false;
        }
        return true;
    }


    protected function buscarHabilitadas(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_Escuelas_habilitadas_importacion';
        $sparam = [
            'pIdEscuelaExcluir' => ESCUELAS_DE_PRUEBA,
            'pxCodigoEscuela' => $datos['xCodigoEscuela'],
            'pCodigoEscuela' => $datos['CodigoEscuela'],
            'pxNombre' => $datos['xNombre'],
            'pNombre' => $datos['Nombre'],
            'pxClaveUnicaEscuela' => $datos['xClaveUnicaEscuela'],
            'pClaveUnicaEscuela' => $datos['ClaveUnicaEscuela'],
            'porderby' => $datos['orderby'],
            'plimit' => $datos['limit'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar escuelas habilitadas.');
            return false;
        }
        return true;
    }


    protected function BuscarHabilitacionxIdEscuela(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Escuelas_Habilitada_xIdEscuela";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarEscuelasHabilitadas(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_Escuelas_Habilitada";
        $sparam = [
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRegion' => $datos['IdRegion'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar escuelas habilitadas. ");
            return false;
        }

        return true;
    }

    protected function BuscarEscuelasHabilitadasTotal(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_Escuelas_Habilitada_cantidad";
        $sparam = [
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRegion' => $datos['IdRegion'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar escuelas habilitadas. ");
            return false;
        }

        return true;
    }


    protected function BuscarEscuelasHabilitadasBloqueadasTotal($datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_Escuelas_Habilitadas_Bloqueadas_total";
        $sparam = [
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRegion' => $datos['IdRegion'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar total de escuelas bloqueadas. ");
            return false;
        }

        return true;
    }

    protected function SectorSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Sectores_xEstado';
        $sparam = [];
    }

    protected function DependenciasSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Dependencia_xEstado';
        $sparam = [];
    }

    protected function CategoriasSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Categoria_xEstado';
        $sparam = [];
    }

    protected function PeriodosSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Periodo_xEstado';
        $sparam = [];
    }

    protected function AmbitosSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Ambito_xEstado';
        $sparam = [];
    }

    protected function AranceladoSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Arancelado_xEstado';
        $sparam = [];
    }

    protected function CooperadoraSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Cooperadora_xEstado';
        $sparam = [];
    }

    protected function PermanenciaSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Permanencia_xEstado';
        $sparam = [];
    }

    protected function AlternanciaSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Alternancia_xEstado';
        $sparam = [];
    }

    protected function DepartamentosSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Departamentos_xEstado';
        $sparam = [];
    }

    protected function LocalidadesSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Localidades_xEstado';
        $sparam = [];
    }

    protected function RegionSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Regiones_xEstado';
        $sparam = [];
    }

    protected function DistritosSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Distritos_xEstado';
        $sparam = [];
    }

    protected function EnsenanzasSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Ensenanzas_xEstado';
        $sparam = [];
    }

    protected function TiposOrganizacionSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_TiposOrganizacion_xEstado';
        $sparam = [];
    }

    protected function EscuelasZonasSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_EscuelasZonas_xEstado';
        $sparam = [];
    }

    protected function Insertar(array $datos, ?int &$codigoInsertado): bool {
        $spnombre = "ins_Escuelas";
        $sparam = [
            'pIdSector' => $datos['IdSector'],
            'pEstadoLocalizacion' => $datos['EstadoLocalizacion'],
            'pIdDependencia' => $datos['IdDependencia'],
            'pIdCategoria' => $datos['IdCategoria'],
            'pIdPeriodo' => $datos['IdPeriodo'],
            'pIdAmbito' => $datos['IdAmbito'],
            'pIdArancelado' => $datos['IdArancelado'],
            'pIdCooperadora' => $datos['IdCooperadora'],
            'pIdPermanencia' => $datos['IdPermanencia'],
            'pIdAlternancia' => $datos['IdAlternancia'],
            'pNombre' => $datos['Nombre'],
            'pCodigoEscuela' => $datos['CodigoEscuela'],
            'pClaveUnicaEscuela' => $datos['ClaveUnicaEscuela'],
            'pIdProvincia' => $datos['IdProvincia'],
            'pIdDepartamento' => $datos['IdDepartamento'],
            'pIdMunicipio' => $datos['IdMunicipio'],
            'pIdLocalidad' => $datos['IdLocalidad'],
            'pIdRegion' => $datos['IdRegion'],
            'pIdDistrito' => $datos['IdDistrito'],
            'pDireccion' => $datos['Direccion'],
            'pNumero' => $datos['Numero'],
            'pPiso' => $datos['Piso'],
            'pCodigoPostal' => $datos['CodigoPostal'],
            'pLongitud' => $datos['Longitud'],
            'pLatitud' => $datos['Latitud'],
            'pEsAnexo' => $datos['EsAnexo'],
            'pTelefonoCodArea' => $datos['TelefonoCodArea'],
            'pTelefonoInstCargadoRA' => $datos['TelefonoInstCargadoRA'],
            'pTelefonoPadron' => $datos['TelefonoPadron'],
            'pTelefonoInst' => $datos['TelefonoInst'],
            'pEmail' => $datos['Email'],
            'pSitioWeb' => $datos['SitioWeb'],
            'pNroRed' => $datos['NroRed'],
            'pDescripcion' => $datos['Descripcion'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pIdEnsenanza' => $datos['IdEnsenanza'],
            'pIdTipoOrganizacion' => $datos['IdTipoOrganizacion'],
            'pIdEscuelaZona' => $datos['IdEscuelaZona']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al insertar.");
            return false;
        }
        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }

    protected function Modificar(array $datos): bool {
        $spnombre = "upd_Escuelas_xIdEscuela";
        $sparam = [
            'pIdSector' => $datos['IdSector'],
            'pEstadoLocalizacion' => $datos['EstadoLocalizacion'],
            'pIdDependencia' => $datos['IdDependencia'],
            'pIdCategoria' => $datos['IdCategoria'],
            'pIdPeriodo' => $datos['IdPeriodo'],
            'pIdAmbito' => $datos['IdAmbito'],
            'pIdArancelado' => $datos['IdArancelado'],
            'pIdCooperadora' => $datos['IdCooperadora'],
            'pIdPermanencia' => $datos['IdPermanencia'],
            'pIdAlternancia' => $datos['IdAlternancia'],
            'pNombre' => $datos['Nombre'],
            'pCodigoEscuela' => $datos['CodigoEscuela'],
            'pClaveUnicaEscuela' => $datos['ClaveUnicaEscuela'],
            'pIdProvincia' => $datos['IdProvincia'],
            'pIdDepartamento' => $datos['IdDepartamento'],
            'pIdMunicipio' => $datos['IdMunicipio'],
            'pIdLocalidad' => $datos['IdLocalidad'],
            'pIdRegion' => $datos['IdRegion'],
            'pIdDistrito' => $datos['IdDistrito'],
            'pDireccion' => $datos['Direccion'],
            'pNumero' => $datos['Numero'],
            'pPiso' => $datos['Piso'],
            'pCodigoPostal' => $datos['CodigoPostal'],
            'pLongitud' => $datos['Longitud'],
            'pLatitud' => $datos['Latitud'],
            'pTelefonoCodArea' => $datos['TelefonoCodArea'],
            'pTelefonoInstCargadoRA' => $datos['TelefonoInstCargadoRA'],
            'pTelefonoPadron' => $datos['TelefonoPadron'],
            'pTelefonoInst' => $datos['TelefonoInst'],
            'pEmail' => $datos['Email'],
            'pSitioWeb' => $datos['SitioWeb'],
            'pNroRed' => $datos['NroRed'],
            'pDescripcion' => $datos['Descripcion'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdEnsenanza' => $datos['IdEnsenanza'],
            'pIdTipoOrganizacion' => $datos['IdTipoOrganizacion'],
            'pIdEscuelaZona' => $datos['IdEscuelaZona']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar.");
            return false;
        }
        return true;
    }

    protected function ModificarAnexo(array $datos): bool {
        $spnombre = "upd_Escuelas_xIdEscuelaAnexo";
        $sparam = [
            'pEsAnexo' => $datos['EsAnexo'],
            'pIdEscuelaAnexo' => $datos['IdEscuelaAnexo'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdEscuela' => $datos['IdEscuela'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar.");
            return false;
        }
        return true;
    }

    protected function ModificarHabilitacion(array $datos): bool {
        $spnombre = "upd_Escuelas_Habilitada_xIdEscuela";
        $sparam = [
            'pHabilitada' => $datos['Habilitada'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdEscuela' => $datos['IdEscuela'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar.");
            return false;
        }
        return true;
    }

    protected function Eliminar(array $datos): bool {
        $spnombre = "del_Escuelas_xIdEscuela";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al eliminar escuela.");
            return false;
        }
        return true;
    }

    protected function BuscarUltimoOrden(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Escuelas_max_orden";
        $sparam = [];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar el maximo orden.");
            return false;
        }
        return true;
    }

    protected function ModificarOrden(array $datos): bool {
        $spnombre = "upd_Escuelas_IdEscuela_xIdEscuela";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al cambiar el orden.");
            return false;
        }
        return true;
    }

    protected function ModificarEstado(array $datos): bool {
        $spnombre = "upd_Escuelas_Estado_xIdEscuela";
        $sparam = [
            'pEstado' => $datos['Estado'],
            'pIdEscuela' => $datos['IdEscuela'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar el estado.");
            return false;
        }
        return true;
    }

    protected function ModificarEscuelaPrueba(array $datos): bool {
        $spnombre = "upd_Escuelas_Test_xIdEscuela";
        $sparam = [
            'pTest' => $datos['Test'],
            'pUltimaModificacionFecha' => date('Y/m/d H:i:s'),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdEscuela' => $datos['IdEscuela'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar el estado.");
            return false;
        }
        return true;
    }
}
