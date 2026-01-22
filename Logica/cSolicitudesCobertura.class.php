<?php
include(DIR_CLASES_DB . "cSolicitudesCobertura.db.php");

use Bigtree\ExcepcionLogica;
use cFeriados as Feriados;
use Elastic\Puestos;

class cSolicitudesCobertura extends cSolicitudesCoberturadb {
    /**
     * @var Elastic\Conexion|null
     */
    private $conexionES;

    /** @var array */
    private $datosSolicitud = [];

    /** @var cArchivosDocumentacion */
    private $oArchivosDocumentacion;

    /**
     * Constructor de la clase cSolicitudesCobertura.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal         $conexion
     * @param Elastic\Conexion|null $conexionES
     * @param mixed                 $formato
     */
    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO, ?Elastic\Conexion $conexionES = null) {
        parent::__construct($conexion, $formato);
        $this->conexionES = $conexionES;

        require_once(DIR_CLASES_LOGICA . "cArchivosDocumentacion.class.php");

        $this->oArchivosDocumentacion = new cArchivosDocumentacion(
            $conexion,
            CARPETA_CONFIGURACION_SOLICITUD_COBERTURA,
            $formato
        );
    }

    /**
     * Destructor de la clase cSolicitudesCobertura.
     */
    function __destruct() {
        parent::__destruct();
    }

    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarxCodigoLog($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigoLog($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function buscarParElastic($datos, &$resultado, &$numfilas): bool {
        return parent::buscarParElastic($datos, $resultado, $numfilas);
    }


    /**
     * @inheritDoc
     */
    public function buscarxPuestoLicencia(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarxPuestoLicencia($datos, $resultado, $numfilas);
    }


    /**
     * @inheritDoc
     */
    public function buscarxPuestoNovedad(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarxPuestoNovedad($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function buscarxNovedad(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarxNovedad($datos, $resultado, $numfilas);
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    public function buscarAnexos(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarAnexos($datos, $resultado, $numfilas);
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    public function buscarxLicencia(array $datos, &$resultado, ?int &$numfilas): bool {
        $datosParam["IdLicencia"] = $datos["IdLicencia"];
        $datosParam["IdPuesto"] = $datos["IdPuesto"];
        $datosParam["xIdEstado"] = "0";
        $datosParam["IdEstado"] = "-1";
        if (isset($datos["IdEstado"]) && $datos["IdEstado"] != "") {
            $datosParam["xIdEstado"] = "1";
            $datosParam["IdEstado"] = $datos["IdEstado"];
        }
        $datosParam["xEstado"] = "1";
        $datosParam["Estado"] = ACTIVO;
        return parent::buscarxLicencia($datosParam, $resultado, $numfilas);
    }


    /**
     * @inheritDoc
     */
    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xId' => 0,
            'Id' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdTipoDocumento' => 0,
            'IdTipoDocumento' => "",
            'xDni' => 0,
            'Dni' => "",
            'xIdLicencia' => 0,
            'IdLicencia' => "",
            'xIdPersonaSaliente' => 0,
            'IdPersonaSaliente' => "",
            'xFechaDesde' => 0,
            'FechaDesde' => "",
            'xIdArea' => 0,
            'IdArea' => "-1",
            'xIdEstado' => 0,
            'IdEstado' => "-1",
            'xIdCargo' => 0,
            'IdCargo' => "-1",
            'xIdMateria' => 0,
            'IdMateria' => "-1",
            'xIdRegion' => 0,
            'IdRegion' => "-1",
            'xIdAreaIgnorar' => 0,
            'IdAreaIgnorar' => "-1",
            'xIdEstadoIgnorar' => 0,
            'IdEstadoIgnorar' => "-1",
            'xFiltros' => 0,
            'Filtros' => 'TRUE',
            'xIdsNivel' => 0,
            'IdsNivel' => '-1',
            'xIdPersonaEntrante' => 0,
            'IdPersonaEntrante' => '',
            "xIdCategoria" => 0,
            "IdCategoria" => "",
            "xEstado" => 1,
            "Estado" => ACTIVO,
            'limit' => '',
            'orderby' => "Id DESC",
        ];
        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }
        if (isset($datos['IdEscuela']) && is_array($datos['IdEscuela'])) {
            $sparam['IdEscuela'] = implode(",", $datos['IdEscuela']);
            $sparam['xIdEscuela'] = 1;
        } elseif (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            $sparam['Dni'] = $datos['Dni'];
            $sparam['xDni'] = 1;
        }

        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != "") {
            $sparam['IdLicencia'] = $datos['IdLicencia'];
            $sparam['xIdLicencia'] = 1;
        }
        if (isset($datos['IdPersonaSaliente']) && $datos['IdPersonaSaliente'] != "") {
            $sparam['IdPersonaSaliente'] = $datos['IdPersonaSaliente'];
            $sparam['xIdPersonaSaliente'] = 1;
        }
        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            $sparam['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xFechaDesde'] = 1;
        }
        if (isset($datos['IdArea']) && $datos['IdArea'] != "") {
            $sparam['IdArea'] = $datos['IdArea'];
            $sparam['xIdArea'] = 1;
        }
        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }

        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdAreaIgnorar']) && $datos['IdAreaIgnorar'] != "") {
            $sparam['IdAreaIgnorar'] = $datos['IdAreaIgnorar'];
            $sparam['xIdAreaIgnorar'] = 1;
        }

        if (isset($datos['IdEstadoIgnorar']) && $datos['IdEstadoIgnorar'] != "") {
            $sparam['IdEstadoIgnorar'] = $datos['IdEstadoIgnorar'];
            $sparam['xIdEstadoIgnorar'] = 1;
        }

        if (isset($datos['IdPersonaEntrante']) && $datos['IdPersonaEntrante'] != "") {
            $sparam['xIdPersonaEntrante'] = 1;
            $sparam['IdPersonaEntrante'] = $datos['IdPersonaEntrante'];
        }

        if (isset($datos['IdCategoria']) && $datos['IdCategoria'] != "") {
            $sparam['xIdCategoria'] = 1;
            $sparam['IdCategoria'] = $datos['IdCategoria'];
        }

        $arrayFiltros = [];
        $ii = 0;


        if (!FuncionesPHPLocal::isEmpty($datos['filtarxRegionxNivelxTurno'])) {
            foreach ($datos['filtarxRegionxNivelxTurno'] as $filtro) {
                $arrayFiltros[$ii] = 'E.IdRegion =' . $filtro['Region'];
                if (0 != $filtro['Nivel'])
                    $arrayFiltros[$ii] .= ' AND SC.IdNivel = ' . $filtro['Nivel'];
                ++$ii;
            }

        } elseif (!FuncionesPHPLocal::isEmpty($datos['IdNivel']) && !is_array($datos['IdNivel'])) {

            $arrayFiltros[0] = sprintf('SC.IdNivel = %d', $datos['IdNivel']);
            if (!FuncionesPHPLocal::isEmpty($datos['IdsEscuela']))
                $arrayFiltros[0] .= sprintf(' AND SC.IdEscuela IN (%s)', implode(',', $datos['IdsEscuela']));

        }


        if (!FuncionesPHPLocal::isEmpty($arrayFiltros)) {
            $sparam['Filtros'] = sprintf('(%s)', implode(') OR (', $arrayFiltros));
            $sparam['xFiltros'] = 1;
        }


        if (!FuncionesPHPLocal::isEmpty($datos['filtarxEscuelaxNivelxTurno'])) {
            $filtroNivel = [];
            foreach ($datos['filtarxEscuelaxNivelxTurno'] as $filtro) {
                if (0 != $filtro['Nivel'])
                    $filtroNivel[] = $filtro['Nivel'];
            }
            if (!empty($filtroNivel)) {
                $sparam['xIdsNivel'] = 1;
                $sparam['IdsNivel'] = implode(',', $filtroNivel);
            }
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdNivel']) && is_array($datos['IdNivel'])) {

            $sparam['xIdsNivel'] = 1;
            $sparam['IdsNivel'] = implode(',', $datos['IdNivel']);
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Estado'])) {
            $sparam['xEstado'] = 1;
            $sparam['Estado'] = $datos['Estado'];
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaListado($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xId' => 0,
            'Id' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdTipoDocumento' => 0,
            'IdTipoDocumento' => "",
            'xDni' => 0,
            'Dni' => "",
            'xIdLicencia' => 0,
            'IdLicencia' => "",
            'xIdPersonaSaliente' => 0,
            'IdPersonaSaliente' => "",
            'xFechaDesde' => 0,
            'FechaDesde' => "",
            'xIdArea' => 0,
            'IdArea' => "-1",
            'xIdEstado' => 0,
            'IdEstado' => "-1",
            'xIdCargo' => 0,
            'IdCargo' => "-1",
            'xIdMateria' => 0,
            'IdMateria' => "-1",
            'xIdRegion' => 0,
            'IdRegion' => "-1",
            'xIdAreaIgnorar' => 0,
            'IdAreaIgnorar' => "-1",
            'xIdEstadoIgnorar' => 0,
            'IdEstadoIgnorar' => "-1",
            'xFiltros' => 0,
            'Filtros' => 'TRUE',
            'xIdsNivel' => 0,
            'IdsNivel' => '-1',
            "xIdCategoria" => 0,
            "IdCategoria" => "",
            "xIdTurno" => 0,
            "IdTurno" => "",
            "xIdsFiltradosPuestos" => 0,
            "IdsFiltradosPuestos" => "-1",
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "SC.Id DESC",
        ];
        if (isset($datos['IdsFiltradosPuestos']) && $datos['IdsFiltradosPuestos'] != "") {
            $sparam['IdsFiltradosPuestos'] = $datos['IdsFiltradosPuestos'];
            $sparam['xIdsFiltradosPuestos'] = 1;
        }
        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }
        if (isset($datos['IdEscuela']) && is_array($datos['IdEscuela'])) {
            $sparam['IdEscuela'] = implode(",", $datos['IdEscuela']);
            $sparam['xIdEscuela'] = 1;
        } elseif (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            $sparam['Dni'] = $datos['Dni'];
            $sparam['xDni'] = 1;
        }

        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != "") {
            $sparam['IdLicencia'] = $datos['IdLicencia'];
            $sparam['xIdLicencia'] = 1;
        }
        if (isset($datos['IdPersonaSaliente']) && $datos['IdPersonaSaliente'] != "") {
            $sparam['IdPersonaSaliente'] = $datos['IdPersonaSaliente'];
            $sparam['xIdPersonaSaliente'] = 1;
        }
        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            $sparam['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xFechaDesde'] = 1;
        }
        if (isset($datos['IdArea']) && $datos['IdArea'] != "") {
            $sparam['IdArea'] = $datos['IdArea'];
            $sparam['xIdArea'] = 1;
        }
        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }

        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdAreaIgnorar']) && $datos['IdAreaIgnorar'] != "") {
            $sparam['IdAreaIgnorar'] = $datos['IdAreaIgnorar'];
            $sparam['xIdAreaIgnorar'] = 1;
        }

        if (isset($datos['IdEstadoIgnorar']) && $datos['IdEstadoIgnorar'] != "") {
            $sparam['IdEstadoIgnorar'] = $datos['IdEstadoIgnorar'];
            $sparam['xIdEstadoIgnorar'] = 1;
        }


        if (isset($datos['IdCategoria']) && $datos['IdCategoria'] != "") {
            $sparam['xIdCategoria'] = 1;
            $sparam['IdCategoria'] = $datos['IdCategoria'];
        }


        if (isset($datos['IdTurno']) && $datos['IdTurno'] != "") {
            $sparam['xIdTurno'] = 1;
            $sparam['IdTurno'] = $datos['IdTurno'];
        }

        $arrayFiltros = [];
        $ii = 0;


        if (!FuncionesPHPLocal::isEmpty($datos['filtarxRegionxNivelxTurno'])) {
            foreach ($datos['filtarxRegionxNivelxTurno'] as $filtro) {
                $arrayFiltros[$ii] = 'E.IdRegion =' . $filtro['Region'];
                if (0 != $filtro['Nivel'])
                    $arrayFiltros[$ii] .= ' AND SC.IdNivel = ' . $filtro['Nivel'];
                ++$ii;
            }

        } elseif (!FuncionesPHPLocal::isEmpty($datos['IdNivel']) && !is_array($datos['IdNivel'])) {

            $arrayFiltros[0] = sprintf('SC.IdNivel = %d', $datos['IdNivel']);
            if (!FuncionesPHPLocal::isEmpty($datos['IdsEscuela']))
                $arrayFiltros[0] .= sprintf(' AND SC.IdEscuela IN (%s)', implode(',', $datos['IdsEscuela']));

        }


        if (!FuncionesPHPLocal::isEmpty($arrayFiltros)) {
            $sparam['Filtros'] = sprintf('(%s)', implode(') OR (', $arrayFiltros));
            $sparam['xFiltros'] = 1;
        }


        if (!FuncionesPHPLocal::isEmpty($datos['filtarxEscuelaxNivelxTurno'])) {
            $filtroNivel = [];
            foreach ($datos['filtarxEscuelaxNivelxTurno'] as $filtro) {
                if (0 != $filtro['Nivel'])
                    $filtroNivel[] = $filtro['Nivel'];
            }
            if (!empty($filtroNivel)) {
                $sparam['xIdsNivel'] = 1;
                $sparam['IdsNivel'] = implode(',', $filtroNivel);
            }
        }

        // IdNivel puede venir en el post como filtro, pero puede estar sobreescrito en algun rol
        if (!FuncionesPHPLocal::isEmpty($datos['IdNivel'])) {

            if (is_array($datos['IdNivel'])) { // si es array esta sobreescrito por rol

                // si viene sobreescrito por rol pero selecciona filtro por nivel
                if (!FuncionesPHPLocal::isEmpty($datos['IdNivelSeleccionado']) && in_array($datos['IdNivelSeleccionado'], $datos['IdNivel'])) {
                    $sparam['IdsNivel'] = $datos['IdNivelSeleccionado'];
                } else { // si no filtra nivel y tiene nivel por rol, devuelve solo esos niveles
                    $sparam['IdsNivel'] = implode(',', $datos['IdNivel']);
                }

            } else { // rol sin nivel

                $sparam['IdsNivel'] = $datos['IdNivel'];
            }
            $sparam['xIdsNivel'] = 1;
        }

        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaListado($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaListadoCantidad($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xId' => 0,
            'Id' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdTipoDocumento' => 0,
            'IdTipoDocumento' => "",
            'xDni' => 0,
            'Dni' => "",
            'xIdLicencia' => 0,
            'IdLicencia' => "",
            'xIdPersonaSaliente' => 0,
            'IdPersonaSaliente' => "",
            'xFechaDesde' => 0,
            'FechaDesde' => "",
            'xIdArea' => 0,
            'IdArea' => "-1",
            'xIdEstado' => 0,
            'IdEstado' => "-1",
            'xIdCargo' => 0,
            'IdCargo' => "-1",
            'xIdMateria' => 0,
            'IdMateria' => "-1",
            'xIdRegion' => 0,
            'IdRegion' => "-1",
            'xIdAreaIgnorar' => 0,
            'IdAreaIgnorar' => "-1",
            'xIdEstadoIgnorar' => 0,
            'IdEstadoIgnorar' => "-1",
            'xFiltros' => 0,
            'Filtros' => 'TRUE',
            'xIdsNivel' => 0,
            'IdsNivel' => '-1',
            "xIdCategoria" => 0,
            "IdCategoria" => "",
            "xIdTurno" => 0,
            "IdTurno" => "",
            "xIdsFiltradosPuestos" => 0,
            "IdsFiltradosPuestos" => "-1",
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "SC.Id DESC",
        ];
        if (isset($datos['IdsFiltradosPuestos']) && $datos['IdsFiltradosPuestos'] != "") {
            $sparam['IdsFiltradosPuestos'] = $datos['IdsFiltradosPuestos'];
            $sparam['xIdsFiltradosPuestos'] = 1;
        }
        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }
        if (isset($datos['IdEscuela']) && is_array($datos['IdEscuela'])) {
            $sparam['IdEscuela'] = implode(",", $datos['IdEscuela']);
            $sparam['xIdEscuela'] = 1;
        } elseif (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            $sparam['Dni'] = $datos['Dni'];
            $sparam['xDni'] = 1;
        }

        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != "") {
            $sparam['IdLicencia'] = $datos['IdLicencia'];
            $sparam['xIdLicencia'] = 1;
        }
        if (isset($datos['IdPersonaSaliente']) && $datos['IdPersonaSaliente'] != "") {
            $sparam['IdPersonaSaliente'] = $datos['IdPersonaSaliente'];
            $sparam['xIdPersonaSaliente'] = 1;
        }
        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            $sparam['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xFechaDesde'] = 1;
        }
        if (isset($datos['IdArea']) && $datos['IdArea'] != "") {
            $sparam['IdArea'] = $datos['IdArea'];
            $sparam['xIdArea'] = 1;
        }
        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }

        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdAreaIgnorar']) && $datos['IdAreaIgnorar'] != "") {
            $sparam['IdAreaIgnorar'] = $datos['IdAreaIgnorar'];
            $sparam['xIdAreaIgnorar'] = 1;
        }

        if (isset($datos['IdEstadoIgnorar']) && $datos['IdEstadoIgnorar'] != "") {
            $sparam['IdEstadoIgnorar'] = $datos['IdEstadoIgnorar'];
            $sparam['xIdEstadoIgnorar'] = 1;
        }


        if (isset($datos['IdCategoria']) && $datos['IdCategoria'] != "") {
            $sparam['xIdCategoria'] = 1;
            $sparam['IdCategoria'] = $datos['IdCategoria'];
        }


        if (isset($datos['IdTurno']) && $datos['IdTurno'] != "") {
            $sparam['xIdTurno'] = 1;
            $sparam['IdTurno'] = $datos['IdTurno'];
        }

        $arrayFiltros = [];
        $ii = 0;


        if (!FuncionesPHPLocal::isEmpty($datos['filtarxRegionxNivelxTurno'])) {
            foreach ($datos['filtarxRegionxNivelxTurno'] as $filtro) {
                $arrayFiltros[$ii] = 'E.IdRegion =' . $filtro['Region'];
                if (0 != $filtro['Nivel'])
                    $arrayFiltros[$ii] .= ' AND SC.IdNivel = ' . $filtro['Nivel'];
                ++$ii;
            }

        } elseif (!FuncionesPHPLocal::isEmpty($datos['IdNivel']) && !is_array($datos['IdNivel'])) {

            $arrayFiltros[0] = sprintf('SC.IdNivel = %d', $datos['IdNivel']);
            if (!FuncionesPHPLocal::isEmpty($datos['IdsEscuela']))
                $arrayFiltros[0] .= sprintf(' AND SC.IdEscuela IN (%s)', implode(',', $datos['IdsEscuela']));

        }


        if (!FuncionesPHPLocal::isEmpty($arrayFiltros)) {
            $sparam['Filtros'] = sprintf('(%s)', implode(') OR (', $arrayFiltros));
            $sparam['xFiltros'] = 1;
        }


        if (!FuncionesPHPLocal::isEmpty($datos['filtarxEscuelaxNivelxTurno'])) {
            $filtroNivel = [];
            foreach ($datos['filtarxEscuelaxNivelxTurno'] as $filtro) {
                if (0 != $filtro['Nivel'])
                    $filtroNivel[] = $filtro['Nivel'];
            }
            if (!empty($filtroNivel)) {
                $sparam['xIdsNivel'] = 1;
                $sparam['IdsNivel'] = implode(',', $filtroNivel);
            }
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdNivel'])) {
            if (is_array($datos['IdNivel'])) {
                if (!FuncionesPHPLocal::isEmpty($datos['IdNivelSeleccionado']) && in_array($datos['IdNivelSeleccionado'], $datos['IdNivel'])) {
                    $sparam['IdsNivel'] = $datos['IdNivelSeleccionado'];
                } else {
                    $sparam['IdsNivel'] = implode(',', $datos['IdNivel']);
                }
            } else {
                $sparam['IdsNivel'] = $datos['IdNivel'];
            }
            $sparam['xIdsNivel'] = 1;
        }

        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaListadoCantidad($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function ValidarAltaPorSC($datos, &$AltaPendiente): bool {

        $IdSC = $datos['IdSolicitudCobertura'];

        // traigo solo novedades de alta que hayan generado un idpofa
        $sqlDocumentos = "
            SELECT
            d.IdDocumento,
            dp.IdPuesto,
            dp.IdPofa,
            ce.EstadoFinal
            FROM Documentos d
            INNER JOIN DocumentosPuestos dp ON dp.IdDocumento = d.IdDocumento
            INNER JOIN CircuitosEstados ce ON ce.IdEstado = d.IdEstado
            WHERE d.IdSolicitudCobertura = $IdSC
            AND ce.EstadoFinal = 0
            GROUP BY IdDocumento
        ";

        if (!$this->conexion->ejecutarSQL($sqlDocumentos, "SEL", $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda de puestos por documento.');
            return false;
        }

        $AltaPendiente = $numfilas > 0;
        return true;
    }


    public function ValidarAltaPorSCV($datos, &$SolicitudPendiente, &$IdSCV): bool {

        $IdPuesto = $datos['IdPuesto'];
        $IdTipoDocumento = $datos["IdTipoDocumento"];

        // traigo solo novedades de alta que hayan generado un idpofa
        $sqlDocumentos = "
            SELECT
            a.*
            FROM SolicitudesCobertura a
            LEFT JOIN SolicitudesCoberturaPuesto b ON a.`Id`=b.`IdSolicitudCobertura`
            INNER JOIN CircuitosEstados cesol ON cesol.IdEstado = a.IdEstado
            LEFT JOIN Documentos d ON d.`IdSolicitudCobertura`=a.`Id`
            LEFT JOIN CircuitosEstados cedoc ON cedoc.IdEstado = d.`IdEstado`
            WHERE a.Estado = 10
            AND a.`IdTipoDocumento` = $IdTipoDocumento
            AND b.`IdPuesto` = $IdPuesto
            AND (cesol.EstadoFinal = 0 OR cedoc.`EstadoFinal` = 0)
            GROUP BY a.`Id`
        ";

        if (!$this->conexion->ejecutarSQL($sqlDocumentos, "SEL", $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda de puestos por documento.');
            return false;
        }

        $IdSCV = "";
        $SolicitudPendiente = $numfilas > 0;

        if($SolicitudPendiente){
            while($fila=$this->conexion->ObtenerSiguienteRegistro($resultado)){
                $IdSCV = $fila['Id'];
                break;
            }
        }

        return true;
    }

    public function validadSCExistente($datos, &$resultado, &$numfilas): bool {

        /*
        $oCircuitosEstados = new cCircuitosEstados($this->conexion);

        $datos['EstadoFinal'] = 1;
        if (!$oCircuitosEstados->BuscarEstadosFinales($datos, $resultadoEstados, $numfilasEstados)) {
            $this->setError(500, 'Error al buscar los estados finales.');
            return false;
        }

        if ($numfilasEstados > 0) {
            while ($filaEstados = $this->conexion->ObtenerSiguienteRegistro($resultadoEstados)) {
                $arrayEstados[$filaEstados['IdEstado']] = $filaEstados['IdEstado'];
            }
        }
        */

        //$datos['IdEstado'] = implode(',', $arrayEstados);

        $sparam['IdEstado'] = "-1";
        $sparam["xIdEstado"] = "0";

        if (!FuncionesPHPLocal::isEmpty($datos['IdEstado'])){
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam["xIdEstado"] = "1";
        }

        if (!parent::buscarxLicencia($sparam, $resultado, $numfilas)) {
            $this->setError(500, 'Error al buscar solicitudes ya iniciadas.');
            return false;
        }

        return true;
    }

    public function encuadreSC(array $datos): string {

        if (!$this->busquedaAvanzadaTipo($resultado, $numfilas))
            return false;

        $arbol = [];

        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

            $urgente = (int)($fila['Urgente'] ?: 0);
            $vacante = (int)($fila['Vacante'] ?: 0);
            $idNivel = (int)($fila['IdNivel'] ?: 0);
            $idTipoCargo = (int)($fila['IdTipoCargo'] ?: 0);
            $constanteTipoDocumento = ($fila['ConstanteTipoDocumento']);


            $arbol[$urgente][$vacante][$idNivel][$idTipoCargo] = $constanteTipoDocumento;

        }


        $urgente = (int)($datos['esUrgente'] ?? 0);
        $vacante = (int)($datos['Vacante'] ?? 0);
        $idNivel = (int)($datos['IdNivel'] ?? 0);
        $idTipoCargo = (int)($datos['IdTipo'] ?? 0);

        if (!isset($arbol[$urgente]))
            $urgente = 0;
        if (!isset($arbol[$urgente][$vacante]))
            $vacante = 0;
        if (!isset($arbol[$urgente][$vacante][$idNivel]))
            $idNivel = 0;
        if (!isset($arbol[$urgente][$vacante][$idNivel][$idTipoCargo]))
            $idTipoCargo = 0;


        return $arbol[$urgente][$vacante][$idNivel][$idTipoCargo];


    }


    /**
     * @inheritDoc
     */
    public function busquedaAvanzadaTipo(&$resultado, &$numfilas): bool {


        return parent::busquedaAvanzadaTipo($resultado, $numfilas);
    }

    public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function EscuelasSP(&$spnombre, &$sparam): void {
        parent::EscuelasSP($spnombre, $sparam);
    }


    public function EscuelasSPResult(&$resultado, &$numfilas): bool {
        $this->EscuelasSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }


    public function DocumentosTiposSP(&$spnombre, &$sparam): void {
        parent::DocumentosTiposSP($spnombre, $sparam);
    }


    public function DocumentosTiposSPResult(&$resultado, &$numfilas): bool {
        $this->DocumentosTiposSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }


    public function EscuelasPuestosSP(&$spnombre, &$sparam): void {
        parent::EscuelasPuestosSP($spnombre, $sparam);
    }


    public function EscuelasPuestosSPResult(&$resultado, &$numfilas): bool {
        $this->EscuelasPuestosSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }


    public function SP(&$spnombre, &$sparam): void {
        parent::SP($spnombre, $sparam);
    }


    public function SPResult(&$resultado, &$numfilas): bool {
        $this->SP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    /**
     * Recorre las sub-solicitudes y los puestos asignados a estas
     *
     * Devuelve un array de personas y puestos que permite completar el listado en patalla de Puestos de la solicitud y
     * los agentes asignados a c/u
     *
     * @param array      $datos
     * @param array|null $resultado
     * @param bool       $ordenar_bajo_persona
     *
     * @return bool
     */
    public function buscarDatosAnexos(array $datos, ?array &$resultado, bool $ordenar_bajo_persona = false): bool {
        $resultado = ['Personas' => [], 'Puestos' => []];
        $oPersona = new cSolicitudesCoberturaPersona($this->conexion, FMT_ARRAY);
        $oPuesto = new cSolicitudesCoberturaPuesto($this->conexion, FMT_ARRAY);
        $oDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);

        if (!$oPersona->buscarParaElasticXSolicitud($datos, $resultado_persona, $numfilas)) {
            $this->setError($oPersona->getError());
            return false;
        }

        if ($numfilas == 0) {
            $this->setError(400, 'Error, no se encontraron datos. ');
            return false;
        }

        while ($filaPersona = $this->conexion->ObtenerSiguienteRegistro($resultado_persona)) {

            $filaPersona['Tipo'] = 'Sub-Solicitud';
            $resultado['Personas'][$filaPersona['Id']] = json_decode(Elastic\SolicitudCobertura::armarDatosElastic($filaPersona, true), true);

            $resultado['Personas'][$filaPersona['Id']]['PersonaDesignada']['Estado']['Nombre'] = $filaPersona['NombreEstadoPersona'] ?? '';
            $resultado['Personas'][$filaPersona['Id']]['PersonaDesignada']['Estado']['Id'] = $filaPersona['IdEstadoPersona'] ?? '';
            $resultado['Personas'][$filaPersona['Id']]['PersonaDesignada']['FallecidoFecha'] = $filaPersona['FallecidoFecha'] ?? '';
            $activa = false;

            $datos['IdSolicitudCoberturaPersona'] = $filaPersona['Id'];
            if (!$oPuesto->buscarParaElasticXSubSolicitud($datos, $resultado_puesto, $numfilas)) {
                $this->setError($oPuesto->getError());
                return false;
            }
            while ($filaPuesto = $this->conexion->ObtenerSiguienteRegistro($resultado_puesto)) {
                $filaPuesto['Tipo'] = 'Puesto';
                if (empty($filaPuesto['Tildado']))
                    $filaPuesto['Tildado'] = 0;
                $datos['IdSolicitudCoberturaPuesto'] = $filaPuesto['Id'];
                if (!$oDesempeno->buscarxPuesto($datos, $resultado_desempeno, $numfilas)) {
                    $this->setError($oPuesto->getError());
                    return false;
                }

                $filaPuesto['Desempenos'] = [];
                $horasDesempenos = 0;
                while ($filaDesempeno = $this->conexion->ObtenerSiguienteRegistro($resultado_desempeno)) {
                    $filaPuesto['Desempenos'][] = [
                        'Id' => $filaDesempeno['Id'],
                        'TipoCantidad' => $filaDesempeno['TipoCantidad'],
                        'CantidadHorasModulos' => $filaDesempeno['CantidadHorasModulos'],
                        'Dia' => $filaDesempeno['Dia'],
                        'Hora' => (object)[
                            'gte' => $filaDesempeno['HoraInicio'],
                            'lte' => $filaDesempeno['HoraFin'],
                        ],
                        'Tildado' => $filaDesempeno['Tildado'],
                    ];
                    $horasDesempenos += $filaDesempeno['CantidadHorasModulos'];
                    if ($ordenar_bajo_persona) {
                        /**
                         * Esta linea es efectivamente una disyunci�n:
                         *   - 1 or 1 = 1: (1+1)%2 = 0, pero 1+1-(1*1) = 1
                         *   - 1 or 0 = 1: 1+0-(1*0) = 1
                         *   - 0 or 1 = 1: 0+1-(0*1) = 1
                         *   - 0 or 0 = 0: 0+0-(0*0) = 0
                         * en las �ltimas tres el resultado de la disyunci�n es igual al de la disyunci�n
                         * exclusiva que se obtiene del modulo 2 de la suma 1 xor 1 = 0 y (1+1)%2 = 0.
                         * El objetivo es que si al menos un desempe�o est� seleccionado, el puesto se considera seleccionado
                         */
                        $filaPuesto['Tildado'] = $filaPuesto['Tildado'] + $filaDesempeno['Tildado'] - ($filaPuesto['Tildado'] * $filaDesempeno['Tildado']);
                    }
                }
                $filaPuesto['CantidadHorasModulos'] = $horasDesempenos;
                $datosPuesto = json_decode(Elastic\SolicitudCobertura::armarDatosElastic($filaPuesto, true), true);
                if ($ordenar_bajo_persona) {
                    $activa = $activa || $datosPuesto['Activo'];
                    $resultado['Personas'][$filaPersona['Id']]['Puestos'][] = $datosPuesto;
                } else
                    $resultado['Puestos'][] = $datosPuesto;
            }
            if ($ordenar_bajo_persona)
                $resultado['Personas'][$filaPersona['Id']]['Activa'] = $activa;
        }


        return true;
    }


    /**
     * Recorre las sub-solicitudes y los puestos asignados a estas
     *
     * Devuelve un array de personas y puestos que permite completar el listado en patalla de Puestos de la solicitud y
     * los agentes asignados a c/u
     *
     * @param array      $datos
     * @param array|null $datosRegistro
     * @param bool       $ordenar_bajo_persona
     *
     * @return bool
     */
    public function armarDatosAnexos(array $datos, ?array &$datosRegistro, bool $ordenar_bajo_persona = false): bool {

        if (!$this->buscarAnexos($datos, $resultado, $numfilas))
            return false;

        if ($numfilas == 0) {
            $this->setError(404, 'No se encontraron resultados');
            return false;
        }

        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

            $id_sc = $fila['Id'];
            $id_sc_puesto = $fila['IdSolicitudCoberturaPuesto'];
            $id_sc_desempeno = $fila['IdSolicitudCoberturaDesempeno'];

            $datosRegistro[$id_sc]['IdEscuela'] = $fila['IdEscuela'];
            $datosRegistro[$id_sc]['IdTipoDocumento'] = $fila['IdTipoDocumento'];
            $datosRegistro[$id_sc]['NombreTipoDocumento'] = $fila['NombreTipoDocumento'];
            $datosRegistro[$id_sc]['IdRegistroTipoDocumento'] = $fila['IdRegistroTipoDocumento'];
            $datosRegistro[$id_sc]['IdLicencia'] = $fila['IdLicencia'];
            $datosRegistro[$id_sc]['IdPersonaSaliente'] = $fila['IdPersonaSaliente'];
            $datosRegistro[$id_sc]['FechaDesde'] = $fila['FechaDesde'];
            $datosRegistro[$id_sc]['FechaHasta'] = $fila['FechaHasta'];
            $datosRegistro[$id_sc]['Observaciones'] = $fila['Observaciones'];
            $datosRegistro[$id_sc]['EsAuxiliar'] = $fila['EsAuxiliar'];
            $datosRegistro[$id_sc]['IdNivel'] = $fila['IdNivel'];
            $datosRegistro[$id_sc]['Nivel'] = $fila['Nivel'];
            $datosRegistro[$id_sc]['IdArea'] = $fila['IdArea'];
            $datosRegistro[$id_sc]['Area'] = $fila['Area'];
            $datosRegistro[$id_sc]['IdEstado'] = $fila['IdEstado'];
            $datosRegistro[$id_sc]['EstadoCircuito'] = $fila['EstadoCircuito'];
            $datosRegistro[$id_sc]['IdAreaInicial'] = $fila['IdAreaInicial'];
            $datosRegistro[$id_sc]['IdEstadoInicial'] = $fila['IdEstadoInicial'];
            $datosRegistro[$id_sc]['MovimientoFecha'] = $fila['MovimientoFecha'];
            $datosRegistro[$id_sc]['FechaEnvio'] = $fila['FechaEnvio'];
            $datosRegistro[$id_sc]['Desglosado'] = $fila['Desglosado'];
            $datosRegistro[$id_sc]['Estado'] = $fila['Estado'];

            $datosRegistro[$id_sc]['Todos'] = $fila['DesignadoEnTodos'];
            if ($fila['DesignadoEnTodos']) {
                $datosRegistro[$id_sc]['IdPersonaDesignada'] = $fila['IdPersonaDesignada'];
                $datosRegistro[$id_sc]['InstrumentoLegal'] = $fila['InstrumentoLegal'];
                $datosRegistro[$id_sc]['FechaDesignacion'] = $fila['FechaDesignacion'];
                $datosRegistro[$id_sc]['IdNovedad'] = $fila['IdNovedad'];
                $datosRegistro[$id_sc]['IdEstadoPersona'] = $fila['IdEstadoPersona'];
            }

            # Si existe por lo menos una designaci�n confirmada (con novedad de alta) o finalizada (alta aprobada
            if (in_array($fila['IdEstadoPersona'], [CONFIRMADO, FINALIZADO])) {
                $datosRegistro[$id_sc]['BloquearTodos'] = $fila['BloquearTodos'] = true;
                $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['BloquearPuesto'] = true;
            }

            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['IdPuesto'] = $fila['IdPuesto'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['TipoCantidad'] = $fila['TipoCantidad'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['CantidadHorasModulos'] = $fila['CantidadHorasModulos'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['PuedeDesglosar'] = (bool)$fila['PuedeDesglosar'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desglosado'] = $fila['DesglosadoPuesto'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['TipoCantidad'] = $fila['TipoCantidadDesempeno'];


            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desempenos'][$id_sc_desempeno]['IdPersonaDesignada'] = $fila['IdPersonaDesignada'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desempenos'][$id_sc_desempeno]['IdNovedad'] = $fila['IdNovedad'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desempenos'][$id_sc_desempeno]['IdEstadoPersona'] = $fila['IdEstadoPersona'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desempenos'][$id_sc_desempeno]['TipoCantidadDesempeno'] = $fila['TipoCantidadDesempeno'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desempenos'][$id_sc_desempeno]['CantidadHorasModulosDesempeno'] = $fila['CantidadHorasModulosDesempeno'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desempenos'][$id_sc_desempeno]['Dia'] = $fila['Dia'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desempenos'][$id_sc_desempeno]['HoraInicio'] = $fila['HoraInicio'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desempenos'][$id_sc_desempeno]['HoraFin'] = $fila['HoraFin'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desempenos'][$id_sc_desempeno]['InstrumentoLegal'] = $fila['InstrumentoLegal'];
            $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desempenos'][$id_sc_desempeno]['FechaDesignacion'] = $fila['FechaDesignacion'];

            if (!FuncionesPHPLocal::isEmpty($fila['IdExcepcionTipo'])) {
                $datosRegistro[$id_sc]['Puestos'][$id_sc_puesto]['Desempenos'][$id_sc_desempeno]['IdExcepcionTipo'] = $fila['IdExcepcionTipo'];
            }
        }

        return true;
    }


    public function Insertar($datos, &$codigoInsertado): bool {

        if (!$this->devolverTipoDocumentoSC($datos))
            return false;

        if (!$this->obtenerDatosxLicencia($datos))
            return false;

        if (!$this->_ValidarInsertar($datos))
            return false;

        if (isset($datos["multipuesto"]) && $datos["multipuesto"] == "0" && count($datos["Puestos"]) > 1) {
            $idPuestoActual = $datos["IdPuesto"];
            $datosPuesto = array_filter($datos["Puestos"], function ($item) use ($idPuestoActual) {
                return $item['IdPuesto'] == $idPuestoActual;
            });
            $datos["Puestos"] = $datosPuesto;
        }

        $this->_SetearFechas($datos);
        $this->_SetearNull($datos);

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $oAuditoriasSolicitudesCobertura = new cAuditoriasSolicitudesCobertura($this->conexion, $this->formato);
        $datos['IdSolicitudCobertura'] = $datos['Id'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasSolicitudesCobertura->InsertarLog($datos, $codigoInsertadolog)) {
            $this->setError(400, 'Error interno en auditoria');
            return false;
        }

        $oSolicitudesCoberturaPuesto = new cSolicitudesCoberturaPuesto($this->conexion, FMT_ARRAY);
        $oSolicitudesCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);
        foreach ($datos['Puestos'] as $key_puesto => $puestos) {

            $datosInsertar = $puestos;
            $datosInsertar['IdSolicitudCobertura'] = $codigoInsertado;
            if (!$oSolicitudesCoberturaPuesto->Insertar($datosInsertar, $codigoInsertadoPuesto)) {
                $this->setError($oSolicitudesCoberturaPuesto->getError());
                return false;
            }

            foreach ($puestos['Desempenos'] as $key_des => $desempeno) {
                $datosInsertar = $desempeno;
                $datosInsertar['IdSolicitudCobertura'] = $codigoInsertado;
                $datosInsertar['IdSolicitudCoberturaPuesto'] = $codigoInsertadoPuesto;
                if (!$oSolicitudesCoberturaDesempeno->Insertar($datosInsertar, $codigoInsertadoDesempeno)) {
                    $this->setError($oSolicitudesCoberturaDesempeno->getError());
                    return false;
                }
            }
        }

        return true;
    }


    public function InsertarVacante($datos, &$codigoInsertado): bool {
        $datos['Vacante'] = true;

        if (!$this->devolverTipoDocumentoSC($datos))
            return false;

        if (!$this->obtenerDatos($datos))
            return false;

        if (!$this->_ValidarInsertarVacante($datos))
            return false;

        $this->_SetearFechas($datos);
        $this->_SetearNull($datos);

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $oAuditoriasSolicitudesCobertura = new cAuditoriasSolicitudesCobertura($this->conexion, $this->formato);
        $datos['IdSolicitudCobertura'] = $datos['Id'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasSolicitudesCobertura->InsertarLog($datos, $codigoInsertadolog)) {
            $this->setError(400, 'Error al insertar auditoria');
            return false;
        }

        $oSolicitudesCoberturaPuesto = new cSolicitudesCoberturaPuesto($this->conexion, FMT_ARRAY);
        $oSolicitudesCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);
        foreach ($datos['Puestos'] as $key_puesto => $puestos) {

            $datosInsertar = $puestos;
            $datosInsertar['IdSolicitudCobertura'] = $codigoInsertado;
            if (!$oSolicitudesCoberturaPuesto->Insertar($datosInsertar, $codigoInsertadoPuesto)) {
                $this->setError($oSolicitudesCoberturaPuesto->getError());
                return false;
            }

            foreach ($puestos['Desempenos'] as $key_des => $desempeno) {
                $datosInsertar = $desempeno;
                $datosInsertar['IdSolicitudCobertura'] = $codigoInsertado;
                $datosInsertar['IdSolicitudCoberturaPuesto'] = $codigoInsertadoPuesto;
                if (!$oSolicitudesCoberturaDesempeno->Insertar($datosInsertar, $codigoInsertadoDesempeno)) {
                    $this->setError($oSolicitudesCoberturaDesempeno->getError());
                    return false;
                }
            }
        }

        return true;
    }

    public function devolverTipoDocumentoSC(&$datos): bool {


        $constante = $this->encuadreSC($datos);
        if (!$constante)
            return false;

        $datos['IdTipoDocumento'] = constant('NOV_' . $constante);
        $datos['IdRegistroTipoDocumento'] = constant('NOV_REGISTRO_' . $constante);

        return true;
    }

    /**
     * ### Busca los cargos afectados por la licencia
     * Filtra por escuela, nivel y tipo de cargo
     * Arma estructura puesto-desempe�o
     *
     * @param $datos
     *
     * @return bool
     */
    public function obtenerDatosxLicencia(&$datos): bool {
        if (!FuncionesPHPLocal::isEmpty($datos['IdLicencia'])) {

            $oLicencias = new cServiciosLicencias($this->conexion);
            $oDesempeno = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES, FMT_ARRAY);
            $oPuestosPersonas = new cEscuelasPuestosPersonas($this->conexion, $this->conexionES, FMT_ARRAY);
            $datosBuscar['Id'] = $datos['IdLicencia'];
            $datosLicencia = $oLicencias->ObtenerLicenciaxId($datosBuscar);

            if (false === $datosLicencia) {
                $this->setError($oLicencias->getError());
                return false;
            }
            $fechaDesde = date_create_immutable($datosLicencia['Fechas']['gte'] ?? null);
            $fechaHasta = empty($datosLicencia['Fechas']['lte']) ? null : date_create_immutable($datosLicencia['Fechas']['lte']);

            $date_diff = is_null($fechaHasta) ? 9999 : $fechaHasta->diff($fechaDesde)->days;
            $dia_semana_inicio = $fechaDesde->format('w');
            $dia_semana_fin = is_null($fechaHasta) ? 8 : $fechaHasta->format('w');

            $datos['FechaDesde'] = $fechaDesde->format('d/m/Y');
            $datos['FechaHasta'] = empty($datosLicencia['Fechas']['lte']) ? null : $fechaHasta->format('d/m/Y');
            $puestosAfectados = [];
            $mensaje = '';

            $estadosNegativos = $this->_obtenerEstadosFinalesNegativos();

            foreach ($datosLicencia['Cargos'] as $key => $rc) {

                if ($rc['Puesto']['Estado'] <> ACTIVO)
                    continue;

                # Filtra cargos por escuela y nivel
                if ($datos['IdEscuela'] != $rc['Escuela']['Id'] || (isset($rc['Escuela']['Niveles']['Id']) && $datos['IdNivel'] != $rc['Escuela']['Niveles']['Id']))
                    continue;

                // busco si existe algun puesto que ya se solicito primero por separado para evitar duplicar la solicitud
                if (isset($datos["multipuesto"]) && $datos["multipuesto"] == "1") {

                    $datosBuscarExistente = [
                        'IdLicencia' => $datos["IdLicencia"],
                        'IdPuesto' => $rc['Puesto']['Id'],
                        "Estado" => ACTIVO,
                        "xEstado" => 1,

                        // excluye los estados finales negativos de la busqueda para permitir crear si se anulo la solicitud previa
                        "IdEstado" => implode(",", $estadosNegativos)
                    ];

                    if (!$this->validadSCExistente($datosBuscarExistente, $resultadoExistente, $numfilasExistente)) {
                        return false;
                    }

                    if ($numfilasExistente != 0)
                        continue;
                }

                switch ($datos['IdTipoDocumento']) {
                    case (defined('NOV_SC_DIR_PRIMARIO') ? NOV_SC_DIR_PRIMARIO : -1):
                    case (defined('NOV_SC_DIR_SECUNDARIO') ? NOV_SC_DIR_SECUNDARIO : -2):
                        if (!isset($rc['Puesto']['IdTipoCargo']) || !in_array($rc['Puesto']['IdTipoCargo'], TIPOS_DIRECTIVOS)) {
                            $mensaje = 'El tipo de cargo no es directivo';
                            continue 2;
                        }
                        break;
                    case (defined('NOV_SC_AUX_PRIMARIO') ? NOV_SC_AUX_PRIMARIO : -3):
                    case (defined('NOV_SC_AUX_SECUNDARIO') ? NOV_SC_AUX_SECUNDARIO : -4):
                        if (!isset($rc['Puesto']['IdTipoCargo']) || !in_array($rc['Puesto']['IdTipoCargo'], TIPOS_AUXILIARES)) {
                            $mensaje = 'El tipo de cargo no es auxiliar';
                            continue 2;
                        }
                        break;

                    default:
                        break;

                        /*
                    case (defined('NOV_SC_DOC_PRIMARIO') ? NOV_SC_DOC_PRIMARIO : -5):
                    case (defined('NOV_SC_DOC_URGENTE_PRIMARIO') ? NOV_SC_DOC_URGENTE_PRIMARIO : -6):
                    case (defined('NOV_SC_DOC_SECUNDARIO') ? NOV_SC_DOC_SECUNDARIO : -7):
                    case (defined('NOV_SC_INICIAL') ? NOV_SC_INICIAL : -8):
                    case (defined('NOV_SC_DEFAULT_PRIMARIO') ? NOV_SC_DEFAULT_PRIMARIO : -9):
                    case (defined('NOV_SC_DEFAULT_SECUNDARIO') ? NOV_SC_DEFAULT_SECUNDARIO : -10):
                    case (defined('NOV_SC_DEFAULT') ? NOV_SC_DEFAULT : -11):
                    default:
                        # SI ES DIRECTOR O AUXILIAR Y NO EXISTEN LAS SC DE ESTOS TIPOS, DEJO QUE LA SC COMÚN TOME EL CARGO
                        if (isset($rc['Puesto']['IdTipoCargo'])) {
                            if (in_array($rc['Puesto']['IdTipoCargo'], TIPOS_DIRECTIVOS) &&
                                ($datos['IdNivel'] == NIVEL_PRIMARIO && defined('NOV_SC_DIR_PRIMARIO')
                                    || $datos['IdNivel'] == NIVEL_SECUNDARIO && defined('NOV_SC_DIR_SECUNDARIO'))) {
                                $mensaje = 'El tipo de cargo no es directivo';
                                continue 2;
                            }

                            if (in_array($rc['Puesto']['IdTipoCargo'], TIPOS_AUXILIARES) &&
                                ($datos['IdNivel'] == NIVEL_PRIMARIO && defined('NOV_SC_AUX_PRIMARIO')
                                    || $datos['IdNivel'] == NIVEL_SECUNDARIO && defined('NOV_SC_AUX_SECUNDARIO'))) {
                                $mensaje = 'El tipo de cargo no es auxiliar';
                                continue 2;
                            }
                        }
                        break;
                        */
                }

                $datosBuscar = [
                    'IdPersona' => $datos['IdPersonaSaliente'],
                    'IdPuesto' => $rc['Puesto']['Id'],
                ];

                if (!$oPuestosPersonas->buscarCargoxIdPuestoxIdPersona($datosBuscar, $resultado, $numfilas)) {
                    $this->setError($oPuestosPersonas->getError());
                    return false;
                }

                if ($numfilas != 1) {
                    $mensaje = 'No se encuentra la persona en el cargo';
                    continue;
                }

                $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);

                if ($fila['IdEstado'] == REU) {
                    $mensaje = 'Cargo en disponibilidad';
                    continue;
                }

                if ($fila['PuestoAdmiteSuplente'] != '' && $fila['PuestoAdmiteSuplente'] == 0) {
                    $mensaje = 'El cargo no admite suplente';
                    continue;
                }

                if ($fila['AdmiteSuplente'] != '' && $fila['AdmiteSuplente'] == 0) {
                    $mensaje = 'El cargo/materia no admite suplente';
                    continue;
                }

                $puedeDesglosar = $fila['SCParcial'];
                $datosBuscar['IdPuesto'] = $rc['Puesto']['Id'];
                if (!$oDesempeno->BuscarxCodigo($datosBuscar, $resultado_desempenos, $numfilas_desempenos)) {
                    $this->setError($oDesempeno->getError());
                    return false;
                }

                if ($numfilas_desempenos == 0) {
                    $this->setError(400, 'El cargo no tiene desempe&ntilde;os');
                    return false;
                }

                $desempenos = [];
                /**
                 * ### Total de horas/m�dulos del puesto aproximados al siguiente cent�simo
                 * El valor se almacena escalado en dos decimales
                 * 1. Sumo el valor a la cantidad de horas/m�dulos del desempa�o al total del puesto
                 */
                $Total = 0;

                while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado_desempenos)) {
                    # Si la diferencia de fechas es al menos una semana, contamos todos los d�as, si no aplican condiciones
                    if ($date_diff < 7) {
                        switch ($dia_semana_inicio <=> $dia_semana_fin) {
                            /**
                             * Si inicio es anterior al fin, e.g. lunes a mi�rcoles, el desempe�o debe estar entre esos d�as
                             *
                             * martes a jueves ($dia_semana_inicio = 2, $dia_semana_fin = 4)
                             * lunes:       2 > 1 => true   ||  4 < 1 => false : no toma el d�a
                             * martes:      2 > 2 => false  ||  4 < 2 => false : toma el d�a
                             * mi�rcoles:   2 > 3 => false  ||  4 < 3 => false : toma el d�a
                             * jueves:      2 > 4 => false  ||  4 < 4 => false : toma el d�a
                             * viernes:     2 > 5 => false  ||  4 < 5 => true  : no toma el d�a
                             */
                            case -1:
                                if ($dia_semana_inicio > $fila['Dia'] || $dia_semana_fin < $fila['Dia'])
                                    continue 2;
                                break;

                            /**
                             * Si inicio es igual al fin, el desempe�o debe ser ese mismo d�a
                             *
                             * mi�rcoles solo ($dia_semana_inicio = $dia_semana_fin = 3)
                             * lunes:       3 != 1 => true  : no toma el d�a
                             * martes:      3 != 2 => true  : no toma el d�a
                             * mi�rcoles:   3 != 3 => false : toma el d�a
                             * jueves:      3 != 4 => true  : no toma el d�a
                             * viernes:     3 != 5 => true  : no toma el d�a
                             */
                            case 0:
                                if ($dia_semana_inicio != $fila['Dia'])
                                    continue 2;
                                break;

                            /**
                             * Si el inicio es posterior al fin, e.g. jueves a martes, el desempe�o debe estar por fuera del intervalo definido por esos d�as
                             *
                             * jueves a martes ($dia_semana_inicio = 4, $dia_semana_fin = 2)
                             * lunes:       4 > 1 => true   &&  2 < 1 => false : toma el d�a
                             * martes:      4 > 2 => true   &&  2 < 2 => false : toma el d�a
                             * mi�rcoles:   4 > 3 => true   &&  2 < 3 => true  : no toma el d�a
                             * jueves:      4 > 4 => false  &&  2 < 2 => true  : toma el d�a
                             * viernes:     4 > 5 => false  &&  2 < 5 => true  : toma el d�a
                             */
                            case 1:
                                if ($dia_semana_inicio > $fila['Dia'] && $dia_semana_fin < $fila['Dia'])
                                    continue 2;
                                break;

                        }
                    }

                    try {
                        $hora_ini = new DateTime($fila['HoraInicio']);
                        $hora_fin = new DateTime($fila['HoraFin']);
                    } catch (Exception $e) {
                        continue;
                    }
                    $dif = $hora_fin->diff($hora_ini, true);

                    /**
                     * ### Total de horas/m�dulos del desempe�o aproximados al siguiente cent�simo
                     * El valor se almacena escalado en dos decimales
                     * 1. Calcula el total de minutos afectados pro el desempe�o
                     *    1440 minutos por d�a + 60 minutos por hora + minutos
                     *
                     * 2. Calcula la cantidad de horas o de m�dulos correspondientes al nro de minutos
                     *
                     * 3. Multiplico por un factor de FACTOR_ESCALA y aproximo al siguiente valor entero
                     */
                    $TotalDesempeno = (1440 * $dif->days) + (60 * $dif->h) + $dif->i;

                    /** paso 2 */
                    $TotalDesempeno /= $rc['Puesto']['Catedra']['Unidad']['Nombre'] == 'horas'
                        ? CANT_HORAS_PUESTO : CANT_MODULOS_PUESTO;

                    /** paso 3 */
                    $TotalDesempeno = ceil(FACTOR_ESCALA * $TotalDesempeno);

                    /** paso 1 de $Total */
                    $Total += $TotalDesempeno;

                    $desempenos[] = [
                        'IdSolicitudCobertura' => null,
                        'IdPuesto' => $datos['IdPuesto'],
                        'TipoCantidad' => $rc['Puesto']['Catedra']['Unidad']['Nombre'] == 'horas' ? 1 : 2,
                        'CantidadHorasModulos' => $TotalDesempeno,
                        'Dia' => $fila['Dia'],
                        'HoraInicio' => $fila['HoraInicio'],
                        'HoraFin' => $fila['HoraFin'],
                    ];
                }

                if (empty($desempenos))
                    continue;

                $puestosAfectados[] = [
                    'IdSolicitudCobertura' => null,
                    'IdPuesto' => $rc['Puesto']['Id'],
                    'TipoCantidad' => $rc['Puesto']['Catedra']['Unidad']['Nombre'] == 'horas' ? 1 : 2,
                    'CantidadHorasModulos' => $Total,
                    'PuedeDesglosar' => $puedeDesglosar,
                    'Desglosado' => 0,
                    'Desempenos' => $desempenos,
                ];
            }

            if (count($puestosAfectados) <= 0) {

                if (empty($mensaje))
                    $mensaje = 'La solicitud no tiene puestos afectados.';

                $this->setError(400, $mensaje);
                return false;
            }

            $datos['Puestos'] = $puestosAfectados;
        }

        return true;
    }


    /**
     *
     * @param $datos *
     *
     * @return bool
     */
    public function obtenerDatos(&$datos): bool {

        $datos['FechaDesde'] = date('d/m/Y');
        $datos['FechaHasta'] = null;
        $datosBusqueda['Id'] = $datos['IdPuesto'];

        $oPuestos = new Elastic\Puestos($this->conexionES);
        if (!$oPuestos->buscarxCodigo($datosBusqueda, $resultado_puesto)) {
            $this->setError($oPuestos->getError());
            return false;
        }

        $oDesempeno = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES, FMT_ARRAY);
        if (!$oDesempeno->BuscarxCodigo($datos, $resultado_desempenos, $numfilas_desempenos)) {
            $this->setError($oDesempeno->getError());
            return false;
        }

        if ($numfilas_desempenos == 0) {
            $this->setError(400, 'El cargo no tiene desempe&ntilde;os');
            return false;
        }

        $desempenos = [];
        $Total = 0;
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado_desempenos)) {

            try {
                $hora_ini = new DateTime($fila['HoraInicio']);
                $hora_fin = new DateTime($fila['HoraFin']);
            } catch (Exception $e) {
                continue;
            }
            $dif = $hora_fin->diff($hora_ini, true);
            $TotalDesempeno = (1440 * $dif->days) + (60 * $dif->h) + $dif->i;
            $TotalDesempeno /= !empty($resultado_puesto['Horas']) ? CANT_HORAS_PUESTO : CANT_MODULOS_PUESTO;
            $TotalDesempeno = ceil(FACTOR_ESCALA * $TotalDesempeno);
            $Total += $TotalDesempeno;

            $desempenos[] = [
                'IdSolicitudCobertura' => null,
                'IdPuesto' => $datos['IdPuesto'],
                'TipoCantidad' => (isset($resultado_puesto['IdTipo']) && $resultado_puesto['IdTipo'] == 2) ? 2 : 1,
                'CantidadHorasModulos' => $TotalDesempeno,
                'Dia' => $fila['Dia'],
                'HoraInicio' => $fila['HoraInicio'],
                'HoraFin' => $fila['HoraFin'],
            ];
        }

        $puestosAfectados[] = [
            'IdSolicitudCobertura' => null,
            'IdPuesto' => $datos['IdPuesto'],
            'TipoCantidad' => (!empty($resultado_puesto['Horas']) ? 1 : 2),
            'CantidadHorasModulos' => $Total,
            'PuedeDesglosar' => 0,
            'Desglosado' => 0,
            'Desempenos' => $desempenos,
        ];

        $datos['Puestos'] = $puestosAfectados;

        return true;
    }

    public function Modificar($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro, true))
            return false;

        //print_r($this->datosSolicitud); echo PHP_EOL; //die;

        $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');
        $datos['FechaHasta'] = empty($datos['FechaHasta']) ? null : FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], 'dd/mm/aaaa', 'aaaa-mm-dd');
        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;
        $oAuditoriasSolicitudesCobertura = new cAuditoriasSolicitudesCobertura($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasSolicitudesCobertura->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        if (!$this->_armarDatosElastic($datos, $datosRegistro, $datosElastic))
            return false;
        $datosBuscar['IdSolicitudCobertura'] = $datos['IdSolicitudCobertura'] = $datos['Id'];
        $datosBuscar['Tipo'] = 'Designada';
        $datosBuscar['TraerNull'] = 1;
        $oSolicitudesCoberturaPersona = new cSolicitudesCoberturaPersona($this->conexion, FMT_ARRAY, $this->conexionES);
        if (isset($datos['seleccionaTodos']) && isset($datos['IdPersonaDesignada']['todos'])) {
            $tmp = $datos['IdPersonaDesignada']['todos'];
            foreach ($datos['chkPuesto'] as $ii => $_)
                $datos['IdPersonaDesignada'][$ii] = $tmp;
        }

        unset($datos['IdPersonaDesignada']['todos']);
        //print_r($datos['IdPersonaDesignada']); echo PHP_EOL;die;
        if (!empty($datos['IdPersonaDesignada'])) {
            foreach ($datos['IdPersonaDesignada'] as $ii => $personaPuesto) {
                /*if (empty($datos['chkPuesto'][$ii]))
                    continue;*/
                //print_r($personaPuesto); echo PHP_EOL;
                if (empty($personaPuesto['valor'])) {

                    unset($personaPuesto['valor']);
                    foreach ($personaPuesto as $jj => $personaDesempeno) {
                        if (empty($datos['chkPuestoDesempeno'][$ii][$jj]))
                            continue;
                        $datosBuscar['IdPersona'] = $personaDesempeno['valor'];
                        if (!$this->verificarPersonaDesignada($datosBuscar, $idSCPersona, $oSolicitudesCoberturaPersona))
                            return false;
                        $datos['Puestos'][$ii]['Desempenos'][$jj]['IdSolicitudCoberturaPersona'] = $idSCPersona;
                    }
                } else {
                    $datosBuscar['IdPersona'] = $personaPuesto['valor'];
                    if (!$this->verificarPersonaDesignada($datosBuscar, $idSCPersona, $oSolicitudesCoberturaPersona))
                        return false;
                    $datos['Puestos'][$ii]['IdSolicitudCoberturaPersona'] = $idSCPersona;

                }
            }
        }
        //print_r($datos['Puestos']); echo PHP_EOL; die;

        $oSolicitudesCoberturaPuesto = new cSolicitudesCoberturaPuesto($this->conexion, FMT_ARRAY, $this->conexionES);
        $oSolicitudesCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);


        foreach ($datos['Puestos'] ?? [] as $puesto) {
            //print '<pre>'; print_r($puesto); print '</pre>';
            if (!$this->verificarPuesto($puesto, $idSCPuesto, $oSolicitudesCoberturaPuesto, $oSolicitudesCoberturaDesempeno))
                return false;
        }
        //die;
        if (!$this->limpiarRegistrosRedundantes($oSolicitudesCoberturaPersona, $oSolicitudesCoberturaPuesto, $oSolicitudesCoberturaDesempeno))
            return false;


        $oElastic = new Elastic\Modificacion(SUFFIX_SOLICITUDCOBERTURA, $this->conexionES);
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        /*if (!$this->actualizarElasticCompleto($datos, $oSolicitudesCoberturaPersona, $oSolicitudesCoberturaPuesto))
            return false;*/

        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarDesglosePorSolicitud(array $datos): bool {
        self::_SetearFechas($datos);
        return parent::modificarDesglosePorSolicitud($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarDesignadoEnTodos(array $datos): bool {
        self::_SetearFechas($datos);
        return parent::modificarDesignadoEnTodos($datos);
    }


    /**
     * @param array                        $datos
     * @param int|null                     $idSCPersona
     * @param cSolicitudesCoberturaPersona $oSolicitudesCoberturaPersona
     *
     * @return bool
     */
    private function verificarPersonaDesignada(array                        $datos, ?int &$idSCPersona,
                                               cSolicitudesCoberturaPersona $oSolicitudesCoberturaPersona): bool {
        $idPersona = (int)$datos['IdPersona'];
        if (array_key_exists('null', $this->datosSolicitud['designadas'])) {
            $idSCPersona = $this->datosSolicitud['designadas']['null'];
            $this->datosSolicitud['designadas'][$idPersona] = $idSCPersona;
            $this->datosSolicitud['subSolicitudes'][$idSCPersona]['IdPersonaDesignada'] = $idPersona;
            if (!$oSolicitudesCoberturaPersona->Modificar($this->datosSolicitud['subSolicitudes'][$idSCPersona])) {
                $this->setError($oSolicitudesCoberturaPersona->getError());
                return false;
            }
            unset($this->datosSolicitud['designadas']['null']);
        } elseif (array_key_exists($idPersona, $this->datosSolicitud['designadas'])) {
            $idSCPersona = $this->datosSolicitud['designadas'][$idPersona];
        } else {
            $datosIns = [
                'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
                'CantidadHorasModulos' => 0,
                'IdPersonaDesignada' => $datos['IdPersona'],
            ];
            if (!$oSolicitudesCoberturaPersona->Insertar($datosIns, $idSCPersona)) {
                $this->setError($oSolicitudesCoberturaPersona->getError());
                return false;
            }
            $this->datosSolicitud['designadas'][$idPersona] = $idSCPersona;
            $datosBuscar['Id'] = $idSCPersona;
            if (!$oSolicitudesCoberturaPersona->BuscarxCodigo($datosBuscar, $resultado, $numfilas)) {
                $this->setError($oSolicitudesCoberturaPersona->getError());
                return false;
            }
            $this->datosSolicitud['subSolicitudes'][$idSCPersona] = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos'] = [];
        }

        $this->datosSolicitud['activas']['personas'][] = $idSCPersona;

        return true;
    }


    private function verificarPuesto(array                          $datos, ?int &$idSCPuesto,
                                     cSolicitudesCoberturaPuesto    $oSolicitudesCoberturaPuesto,
                                     cSolicitudesCoberturaDesempeno $oSolicitudesCoberturaDesempeno): bool {

        $idSCPuesto = empty($datos['Id']) ? null : (int)$datos['Id'];
        $idPuesto = (int)$datos['IdPuesto'];

        if (empty($this->datosSolicitud['puestos'][$idPuesto]))
            $this->datosSolicitud['puestos'][$idPuesto] = [];


        if (isset($datos['IdSolicitudCoberturaPersona'])) {
            $idSCPersona = (int)$datos['IdSolicitudCoberturaPersona'];

            if (!$this->impactaPuesto($datos, $idSCPersona, $idPuesto, $idSCPuesto, $datosPuesto, $oSolicitudesCoberturaPuesto))
                return false;

            //$this->datosSolicitud['activas']['puestos'][$idSCPuesto][] = $idSCPersona;
            //$this->datosSolicitud['activas']['puestos'][$idSCPersona][] = $idSCPuesto;
            $this->datosSolicitud['activas']['puestos'][] = $idSCPuesto;


            foreach ($datos['Desempenos'] as $desempeno) {
                if (!$this->impactaDesempeno($datos, $desempeno, $idSCPersona,
                    $idPuesto, $idSCPuesto, $oSolicitudesCoberturaPuesto, $oSolicitudesCoberturaDesempeno))
                    return false;
            }
            //print_r($this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos']);die;

        } else {
            //echo '1' . PHP_EOL;
            if (!empty($datos['Desempenos']) && is_array($datos['Desempenos'])) {
                //echo '2' . PHP_EOL;
                //print_r($datos['Desempenos']);die;
                foreach ($datos['Desempenos'] as $desempeno) {
                    if (FuncionesPHPLocal::isEmpty($desempeno['IdSolicitudCoberturaPersona'])) {
                        $this->setError(400, 'Error, debe designar agentes para todos los desempe&ntilde;os');
                        return false;
                    }
                    $idSCPersona = (int)$desempeno['IdSolicitudCoberturaPersona'];
                    if (!$this->impactaDesempeno($datos, $desempeno, $idSCPersona, $idPuesto, $idSCPuesto,
                        $oSolicitudesCoberturaPuesto, $oSolicitudesCoberturaDesempeno))
                        return false;
                }

            }
            //echo 'fin';
        }
        //die;
        return true;
    }

    /**
     * ### Actualiza o inserta en la tabla de SCPuestos
     *
     * Chequea si el puesto existe y est� asignado a la persona, en tal caso simplemente extrae los datos
     * y devuelve verdadero. Si el puesto existe, pero est� asignado a otra persona y  no activo, lo mueve.
     * Finalmente si el puesto ya est� activo, o no existe lo inserta.
     *
     * @param array                       $datos
     * @param int                         $idSCPersona
     * @param int                         $idPuesto
     * @param int|null                    $idSCPuesto
     * @param array|null                  $datosPuesto
     * @param cSolicitudesCoberturaPuesto $oSolicitudesCoberturaPuesto
     *
     * @return bool
     */
    private function impactaPuesto(array                       $datos, int $idSCPersona, int $idPuesto, ?int &$idSCPuesto, ?array &$datosPuesto,
                                   cSolicitudesCoberturaPuesto $oSolicitudesCoberturaPuesto): bool {
        if (empty($this->datosSolicitud['activas']['puestos']))
            $this->datosSolicitud['activas']['puestos'] = [];
        if (empty($this->datosSolicitud['puestos'][$idPuesto]))
            $this->datosSolicitud['puestos'][$idPuesto] = [];

        /**
         * Bloque de detecci�n de noop
         *
         * Si el puesto ya est� asignado a la persona no hay necesidad de realizar ninguna operaci�n,
         * por lo tanto interrumpe el proceso, marca el puesto como activo, extrae los datos de este
         * y devuelve operaci�n exitosa.
         */
        if (
            !empty($this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos']) &&
            array_key_exists(
                $idSCPuesto,
                $this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos']
            )
        ) {
            $this->datosSolicitud['activas']['puestos'][] = $idSCPuesto;
            $datosPuesto = $this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos'][$idSCPuesto];
            return true;
        }


        /**
         * Bloque de proceso de puesto
         *
         * Si el puesto existe, no est� activo y est� asignado a otra persona,
         * lo reasigna a la persona. En caso contrario lo inserta.
         */
        if (isset($this->datosSolicitud['puestosSC'][$idSCPuesto]) &&
            !in_array($idSCPuesto, $this->datosSolicitud['activas']['puestos'])) {
            /*echo print_r($this->datosSolicitud['puestosSC'][$idSCPuesto], true) . PHP_EOL
                . print_r($this->datosSolicitud['activas']['puestos'], true) . PHP_EOL
                . $idSCPersona . PHP_EOL;*/
            $idSCPersonaTmp = $this->datosSolicitud['puestosSC'][$idSCPuesto];
            $datosPuesto = $this->datosSolicitud['subSolicitudes'][$idSCPersonaTmp]['puestos'][$idSCPuesto];
            $datosPuesto['IdSolicitudCoberturaPersona'] = $idSCPersona;
            $datosPuesto['desempenos'] = [];
            //echo print_r($datosPuesto, true) . PHP_EOL;
            unset($this->datosSolicitud['puestos'][$idPuesto][$idSCPersonaTmp]);
            if (!$oSolicitudesCoberturaPuesto->Modificar($datosPuesto)) {
                $this->setError($oSolicitudesCoberturaPuesto->getError());
                return false;
            }
        } else {
            $datos['IdSolicitudCoberturaPersona'] = $idSCPersona;
            if (!$oSolicitudesCoberturaPuesto->Insertar($datos, $idSCPuesto)) {
                $this->setError($oSolicitudesCoberturaPuesto->getError());
                return false;
            }
            $datosBuscar['Id'] = $idSCPuesto;
            $this->datosSolicitud['puestos'][$idPuesto][$idSCPersona] = $idSCPuesto;
            $this->datosSolicitud['puestosSC'][$idSCPuesto] = $idSCPersona;
            if (!$oSolicitudesCoberturaPuesto->BuscarxCodigo($datosBuscar, $resultado, $numfilas)) {
                $this->setError($oSolicitudesCoberturaPuesto->getError());
                return false;
            }
            $datosPuesto = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $datosPuesto['desempenos'] = [];
        }
        $this->datosSolicitud['puestos'][$idPuesto][$idSCPersona] = $idSCPuesto;
        $this->datosSolicitud['puestosSC'][$idSCPuesto] = $idSCPersona;
        $this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos'][$idSCPuesto] = $datosPuesto;
        $this->datosSolicitud['activas']['puestos'][] = $idSCPuesto;

        return true;
    }

    /**
     * ### Actualiza o inserta en la tabla d SCDesempenos
     *
     * Chequea si el desempe�o existe y est� asignado a la persona, en tal caso simplemente y devuelve verdadero.
     * Si el desempe�o existe, pero est� asignado a otra persona, lo mueve. Finalmente si el desempe�o no existe lo inserta.
     *
     * @param array                           $datos
     * @param array                           $desempeno
     * @param int                             $idSCPersona
     * @param int                             $idPuesto
     * @param int|null                        $idSCPuesto
     * @param \cSolicitudesCoberturaPuesto    $oSolicitudesCoberturaPuesto
     * @param \cSolicitudesCoberturaDesempeno $oSolicitudesCoberturaDesempeno
     *
     * @return bool
     */
    private function impactaDesempeno(array                          $datos, array &$desempeno, int $idSCPersona,
                                      int                            $idPuesto, ?int &$idSCPuesto,
                                      cSolicitudesCoberturaPuesto    $oSolicitudesCoberturaPuesto,
                                      cSolicitudesCoberturaDesempeno $oSolicitudesCoberturaDesempeno): bool {

        if (empty($this->datosSolicitud['activas']['desempenos']))
            $this->datosSolicitud['activas']['desempenos'] = [];

        $idSCDesempeno = $desempeno['Id'] = (int)$desempeno['IdDesempeno'];

        /**
         * Bloque de operaci�n de puesto
         *
         * Si el puesto est� asignado a la persona, toma ese puesto como valor activo,
         * en caso contrario ejecuta el m�todo de inserci�n/modificaci�n de puestos.
         */
        if (!empty($this->datosSolicitud['puestos'][$idPuesto][$idSCPersona])) {
            $idSCPuesto = $this->datosSolicitud['puestos'][$idPuesto][$idSCPersona];
            if (empty($this->datosSolicitud['activas']['puestos']))
                $this->datosSolicitud['activas']['puestos'] = [];
            $this->datosSolicitud['activas']['puestos'][] = $idSCPuesto;
            $datosPuesto = $this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos'][$idSCPuesto];
        } else {
            unset ($idSCPuesto);
            if (!$this->impactaPuesto($datos, $idSCPersona, $idPuesto, $idSCPuesto, $datosPuesto, $oSolicitudesCoberturaPuesto))
                return false;

        }

        /**
         * Bloque de detecci�n de noop
         *
         * Si el desempe�o ya est� asignado al puesto no hay necesidad de realizar ninguna operaci�n,
         * por lo tanto interrumpe el proceso, lo marca como activo y devuelve operaci�n exitosa.
         */
        if (
            !empty($this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos'][$idSCPuesto]['desempenos']) &&
            array_key_exists(
                $idSCDesempeno,
                $this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos'][$idSCPuesto]['desempenos']
            )
        ) {
            $this->datosSolicitud['activas']['desempenos'][] = $idSCDesempeno;
            return true;
        }

        /**
         * Bloque de operaci�n de desempe�o
         *
         * Si el desempe�o existe, pero est� asignado a otra persona, lo reasigna. En caso contrario,
         * inserta el desempe�o
         */
        if (array_key_exists($idSCDesempeno, $this->datosSolicitud['desempenos'])) {
            $index = $this->datosSolicitud['desempenos'][$idSCDesempeno];

            $datosDesempeno = $this->datosSolicitud['subSolicitudes'][$index['idSCPersona']]['puestos'][$index['idSCPuesto']]['desempenos'][$idSCDesempeno];

            $datosDesempeno['IdSolicitudCoberturaPuesto'] = $idSCPuesto;
            $datosDesempeno['IdSolicitudCoberturaPersona'] = $idSCPersona;
            $datosPuesto['desempenos'][$idSCDesempeno] = $datosDesempeno;


            unset($this->datosSolicitud['subSolicitudes'][$index['idSCPersona']]['puestos'][$index['idSCPuesto']]['desempenos'][$idSCDesempeno]);
            if (!$oSolicitudesCoberturaDesempeno->Modificar($datosDesempeno)) {
                $this->setError($oSolicitudesCoberturaDesempeno->getError());
                return false;
            }

        } else {

            $desempeno['IdSolicitudCoberturaPuesto'] = $idSCPuesto;
            $desempeno['IdSolicitudCoberturaPersona'] = $idSCPersona;
            unset($idSCDesempeno);
            if (isset($desempeno['IdsDesempeno']))
                $res = $oSolicitudesCoberturaDesempeno->Insertar($desempeno, $idSCDesempeno);
            else
                $res = $oSolicitudesCoberturaDesempeno->insertarValores($desempeno, $idSCDesempeno);

            if (!$res) {
                $this->setError($oSolicitudesCoberturaDesempeno->getError());
                return false;
            }
            $desempeno['Id'] = $idSCDesempeno;
            $datosDesempeno = $desempeno;
        }

        $this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos'][$idSCPuesto]['desempenos'][$idSCDesempeno] = $datosDesempeno;
        $this->datosSolicitud['desempenos'][$idSCDesempeno] = ['idSCPersona' => $idSCPersona, 'idSCPuesto' => $idSCPuesto];
        $this->datosSolicitud['activas']['desempenos'][] = $idSCDesempeno;
        return true;
    }

    /**
     * Elimina los registros inactivos para limpiar la base de basura
     *
     * @param cSolicitudesCoberturaPersona   $oSolicitudesCoberturaPersona
     * @param cSolicitudesCoberturaPuesto    $oSolicitudesCoberturaPuesto
     * @param cSolicitudesCoberturaDesempeno $oSolicitudesCoberturaDesempeno
     *
     * @return bool
     */
    private function limpiarRegistrosRedundantes(cSolicitudesCoberturaPersona   $oSolicitudesCoberturaPersona,
                                                 cSolicitudesCoberturaPuesto    $oSolicitudesCoberturaPuesto,
                                                 cSolicitudesCoberturaDesempeno $oSolicitudesCoberturaDesempeno): bool {

        $desempenosEliminar = array_diff(array_keys($this->datosSolicitud['desempenos']), $this->datosSolicitud['activas']['desempenos']);
        //var_dump(array_keys($this->datosSolicitud['desempenos']), $this->datosSolicitud['activas']['desempenos'], $desempenosEliminar);
        //echo PHP_EOL;
        if (!empty($desempenosEliminar)) {
            foreach ($desempenosEliminar as $id) {
                if (!$oSolicitudesCoberturaDesempeno->Eliminar(['Id' => $id])) {
                    $this->setError($oSolicitudesCoberturaDesempeno->getError());
                    return false;
                }

            }
        }
        $puestosEliminar = array_diff(array_keys($this->datosSolicitud['puestosSC']), $this->datosSolicitud['activas']['puestos']);
        //var_dump(array_keys($this->datosSolicitud['puestosSC']), $this->datosSolicitud['activas']['puestos'], $puestosEliminar);
        //echo PHP_EOL;
        if (!empty($puestosEliminar)) {
            foreach ($puestosEliminar as $id) {
                if (!$oSolicitudesCoberturaPuesto->Eliminar(['Id' => $id])) {
                    $this->setError($oSolicitudesCoberturaPuesto->getError());
                    return false;
                }

            }
        }
        $personasEliminar = array_diff(array_keys($this->datosSolicitud['subSolicitudes']), $this->datosSolicitud['activas']['personas']);
        //var_dump($this->datosSolicitud['activas']['personas'], array_keys($this->datosSolicitud['subSolicitudes']), $personasEliminar);die;

        //print_r($personasEliminar);
        //die;

        if (!empty($personasEliminar)) {
            foreach ($personasEliminar as $id) {
                if (!$oSolicitudesCoberturaPersona->Eliminar(['Id' => $id])) {
                    $this->setError($oSolicitudesCoberturaPersona->getError());
                    return false;
                }

            }
        }

        return true;
    }


    private function actualizarElasticCompleto(array                        $datos,
                                               cSolicitudesCoberturaPersona $oSolicitudesCoberturaPersona,
                                               cSolicitudesCoberturaPuesto  $oSolicitudesCoberturaPuesto): bool {

        $idSolicitud = (int)$datos['Id'];

        $bulkData = '';
        $Action_and_MetaData = new StdClass;
        $Action_and_MetaData->index = new StdClass;
        $Action_and_MetaData->index->_index = INDEXPREFIX . SUFFIX_SOLICITUDCOBERTURA;

        try {
            $generadorPersonas = $oSolicitudesCoberturaPersona->buscarSubSolicitudes(['IdSolicitudCobertura' => $idSolicitud]);
        } catch (ExcepcionLogica $e) {
            $this->setError($e->getError());
            return false;
        }
        foreach ($generadorPersonas as $subSolicitud) {
            $subSolicitud['Tipo'] = 'Sub-Solicitud';
            $idSubSolicitud = Elastic\SolicitudCobertura::obtenerId(
                [
                    'Tipo' => [
                        'name' => $subSolicitud['Tipo'],
                        'parent' => $idSolicitud,
                    ],
                    'Id' => $subSolicitud['Id'],
                ]
            );
            $Action_and_MetaData->index->_id = $idSubSolicitud;
            $Action_and_MetaData->index->routing = $idSolicitud;
            $bulkData .= json_encode($Action_and_MetaData) . "\n";
            $bulkData .= Elastic\SolicitudCobertura::armarDatosElastic($subSolicitud, true) . "\n";
            try {
                $generadorPuestos = $oSolicitudesCoberturaPuesto->buscarPuestos(['IdSolicitudCoberturaPersona' => $subSolicitud['Id']]);
            } catch (ExcepcionLogica $e) {
                $this->setError($e->getError());
                return false;
            }
            foreach ($generadorPuestos as $puesto) {
                $puesto['Tipo'] = 'Puesto';
                $idPuesto = Elastic\SolicitudCobertura::obtenerId(
                    [
                        'Tipo' => [
                            'name' => $puesto['Tipo'],
                            'parent' => $idSubSolicitud,
                        ],
                        'Id' => $puesto['Id'],
                    ]
                );
                $Action_and_MetaData->index->_id = $idPuesto;
                $Action_and_MetaData->index->routing = $idSolicitud;
                $bulkData .= json_encode($Action_and_MetaData) . "\n";
                $bulkData .= Elastic\SolicitudCobertura::armarDatosElastic($puesto, true) . "\n";
            }

        }

        if (!empty($bulkData)) {
            $oObjeto = new Elastic\Modificacion('', $this->conexionES);
            if (!$oObjeto->ActualizarBulk($bulkData)) {
                $this->setError($oObjeto->getError());
                return false;
            }
        }


        return true;
    }

    /*
    public function Eliminar($datos): bool {

        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        $datosEliminar['IdSolicitudCobertura'] = $datos['Id'];
        $oDesempenos = new cSolicitudesCoberturaDesempeno($this->conexion, $this->formato);
        if (!$oDesempenos->eliminarxSolicitud($datosEliminar)) {
            $this->setError($oDesempenos->getError());
            return false;
        }

        $oPuestos = new cSolicitudesCoberturaPuesto($this->conexion, $this->formato);
        if (!$oPuestos->eliminarxSolicitud($datosEliminar)) {
            $this->setError($oPuestos->getError());
            return false;
        }

        $oAuditoriasSolicitudesCobertura = new cAuditoriasSolicitudesCobertura($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!$oAuditoriasSolicitudesCobertura->InsertarLog($datosLog, $codigoInsertadolog))
            return false;

        if (!parent::Eliminar($datos))
            return false;

//        if (!$this->cerrarSolicitud($datosRegistro))
//            return false;

        return true;
    }*/

    // la comento y la duplico para reemplazar la eliminacion fisica por eliminacion logica

    public function Eliminar($datos): bool {

        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        $datosEliminar['UltimaModificacionUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datosEliminar['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datosEliminar['Estado'] = $datos['Estado'] = ELIMINADO;

        $datosEliminar['IdSolicitudCobertura'] = $datos['Id'];

        $oDesempenos = new cSolicitudesCoberturaDesempeno($this->conexion, $this->formato);
        if (!$oDesempenos->eliminarxSolicitud($datosEliminar)) {
            $this->setError($oDesempenos->getError());
            return false;
        }

        $oPuestos = new cSolicitudesCoberturaPuesto($this->conexion, $this->formato);
        if (!$oPuestos->eliminarxSolicitud($datosEliminar)) {
            $this->setError($oPuestos->getError());
            return false;
        }

        $oAuditoriasSolicitudesCobertura = new cAuditoriasSolicitudesCobertura($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!$oAuditoriasSolicitudesCobertura->InsertarLog($datosLog, $codigoInsertadolog))
            return false;

        if (!parent::Eliminar($datos))
            return false;

//        if (!$this->cerrarSolicitud($datosRegistro))
//            return false;

        return true;
    }

    public function ModificarEstado($datos): bool {

        if (!parent::ModificarEstado($datos))
            return false;

        /*$oElastic = new Elastic\Modificacion(SUFFIX_SOLICITUDCOBERTURA, $this->conexionES ?? new Elastic\Conexion());
        if (!$this->_armarDatosElastic($datos, $datosRegistro, $datosElastic))
            return false;

        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }*/

        return true;
    }

    public function ModificarAreaEstado($datos): bool {
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['MovimientoFecha'] = date('Y-m-d H:i:s');
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        if (!parent::ModificarAreaEstado($datos))
            return false;
        return true;
    }

    public function CantidadSolicitudes(array $datos, &$resultado, $filtrarArea = true): bool {
        if ($filtrarArea)
            $datos['IdArea'] = implode(',', empty($_SESSION['IdArea']) ? ['-1'] : $_SESSION['IdArea']);

        $sparam = [
            'xId' => 0,
            'Id' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdTipoDocumento' => 0,
            'IdTipoDocumento' => "",
            'xDni' => 0,
            'Dni' => "",
            'xIdLicencia' => 0,
            'IdLicencia' => "",
            'xIdPersonaSaliente' => 0,
            'IdPersonaSaliente' => "",
            'xFechaDesde' => 0,
            'FechaDesde' => "",
            'xIdArea' => 0,
            'IdArea' => "-1",
            'xIdEstado' => 0,
            'IdEstado' => "-1",
            'xIdAreaIgnorar' => 0,
            'IdAreaIgnorar' => "-1",
            'xIdEstadoIgnorar' => 0,
            'IdEstadoIgnorar' => "-1",
            'xFiltros' => 0,
            'Filtros' => 'TRUE',
            'xIdsNivel' => 0,
            'IdsNivel' => '-1',
        ];
        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }
        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }
        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            $sparam['Dni'] = $datos['Dni'];
            $sparam['xDni'] = 1;
        }

        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != "") {
            $sparam['IdLicencia'] = $datos['IdLicencia'];
            $sparam['xIdLicencia'] = 1;
        }
        if (isset($datos['IdPersonaSaliente']) && $datos['IdPersonaSaliente'] != "") {
            $sparam['IdPersonaSaliente'] = $datos['IdPersonaSaliente'];
            $sparam['xIdPersonaSaliente'] = 1;
        }
        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            $sparam['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xFechaDesde'] = 1;
        }
        if (isset($datos['IdArea']) && $datos['IdArea'] != "") {
            $sparam['IdArea'] = $datos['IdArea'];
            $sparam['xIdArea'] = 1;
        }
        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['IdAreaIgnorar']) && $datos['IdAreaIgnorar'] != "") {
            $sparam['IdAreaIgnorar'] = $datos['IdAreaIgnorar'];
            $sparam['xIdAreaIgnorar'] = 1;
        }

        if (isset($datos['IdEstadoIgnorar']) && $datos['IdEstadoIgnorar'] != "") {
            $sparam['IdEstadoIgnorar'] = $datos['IdEstadoIgnorar'];
            $sparam['xIdEstadoIgnorar'] = 1;
        }

        $arrayFiltros = [];
        $ii = 0;

        if (!FuncionesPHPLocal::isEmpty($datos['filtarxRegionxNivelxTurno'])) {
            foreach ($datos['filtarxRegionxNivelxTurno'] as $filtro) {
                $arrayFiltros[$ii] = 'E.IdRegion =' . $filtro['Region'];
                if (0 != $filtro['Nivel'])
                    $arrayFiltros[$ii] .= ' AND SC.IdNivel = ' . $filtro['Nivel'];
                ++$ii;
            }

        } elseif (!FuncionesPHPLocal::isEmpty($datos['IdNivel'])) {
            $arrayFiltros[0] = sprintf('SC.IdNivel = %d', $datos['IdNivel']);
            if (!FuncionesPHPLocal::isEmpty($datos['IdsEscuela']))
                $arrayFiltros[0] .= sprintf(' AND SC.IdEscuela IN (%s)', implode(',', $datos['IdsEscuela']));

        }

        if (!FuncionesPHPLocal::isEmpty($arrayFiltros)) {
            $sparam['Filtros'] = sprintf('(%s)', implode(') OR (', $arrayFiltros));
            $sparam['xFiltros'] = 1;
        }

        if (!FuncionesPHPLocal::isEmpty($datos['filtarxEscuelaxNivelxTurno'])) {
            $filtroNivel = [];
            foreach ($datos['filtarxEscuelaxNivelxTurno'] as $filtro) {
                if (0 != $filtro['Nivel'])
                    $filtroNivel[] = $filtro['Nivel'];
            }
            if (!empty($filtroNivel)) {
                $sparam['xIdsNivel'] = 1;
                $sparam['IdsNivel'] = implode(',', $filtroNivel);
            }
        }
        return parent::CantidadSolicitudes($sparam, $resultado);
    }

    public function CantidadSolicitudesSupTecnica(array $datos, &$resultado, $filtrarArea = true): bool {
        if ($filtrarArea)
            $datos['IdArea'] = implode(',', empty($_SESSION['IdArea']) ? ['-1'] : $_SESSION['IdArea']);

        $sparam = [
            'xId' => 0,
            'Id' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdTipoDocumento' => 0,
            'IdTipoDocumento' => "",
            'xDni' => 0,
            'Dni' => "",
            'xIdLicencia' => 0,
            'IdLicencia' => "",
            'xIdPersonaSaliente' => 0,
            'IdPersonaSaliente' => "",
            'xFechaDesde' => 0,
            'FechaDesde' => "",
            'xIdArea' => 0,
            'IdArea' => "-1",
            'xIdRegion' => 0,
            'IdRegion' => "-1",
            'xIdEstado' => 0,
            'IdEstado' => "-1",
            'xIdAreaIgnorar' => 0,
            'IdAreaIgnorar' => "-1",
            'xIdEstadoIgnorar' => 0,
            'IdEstadoIgnorar' => "-1",
            'xFiltros' => 0,
            'Filtros' => 'TRUE',
            'xIdsNivel' => 0,
            'IdsNivel' => '-1',
        ];
        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }
        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }
        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            $sparam['Dni'] = $datos['Dni'];
            $sparam['xDni'] = 1;
        }

        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != "") {
            $sparam['IdLicencia'] = $datos['IdLicencia'];
            $sparam['xIdLicencia'] = 1;
        }
        if (isset($datos['IdPersonaSaliente']) && $datos['IdPersonaSaliente'] != "") {
            $sparam['IdPersonaSaliente'] = $datos['IdPersonaSaliente'];
            $sparam['xIdPersonaSaliente'] = 1;
        }
        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            $sparam['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xFechaDesde'] = 1;
        }
        if (isset($datos['IdArea']) && $datos['IdArea'] != "") {
            $sparam['IdArea'] = $datos['IdArea'];
            $sparam['xIdArea'] = 1;
        }
        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['IdAreaIgnorar']) && $datos['IdAreaIgnorar'] != "") {
            $sparam['IdAreaIgnorar'] = $datos['IdAreaIgnorar'];
            $sparam['xIdAreaIgnorar'] = 1;
        }

        if (isset($datos['IdEstadoIgnorar']) && $datos['IdEstadoIgnorar'] != "") {
            $sparam['IdEstadoIgnorar'] = $datos['IdEstadoIgnorar'];
            $sparam['xIdEstadoIgnorar'] = 1;
        }

        $arrayFiltros = [];
        $ii = 0;

        if (!FuncionesPHPLocal::isEmpty($datos['filtarxRegionxNivelxTurno'])) {
            foreach ($datos['filtarxRegionxNivelxTurno'] as $filtro) {
                $arrayFiltros[$ii] = 'E.IdRegion =' . $filtro['Region'];
                if (0 != $filtro['Nivel'])
                    $arrayFiltros[$ii] .= ' AND SC.IdNivel = ' . $filtro['Nivel'];
                ++$ii;
            }

        } elseif (!FuncionesPHPLocal::isEmpty($datos['IdNivel'])) {
            $arrayFiltros[0] = sprintf('SC.IdNivel = %d', $datos['IdNivel']);
            if (!FuncionesPHPLocal::isEmpty($datos['IdsEscuela']))
                $arrayFiltros[0] .= sprintf(' AND SC.IdEscuela IN (%s)', implode(',', $datos['IdsEscuela']));

        }

        if (!FuncionesPHPLocal::isEmpty($arrayFiltros)) {
            $sparam['Filtros'] = sprintf('(%s)', implode(') OR (', $arrayFiltros));
            $sparam['xFiltros'] = 1;
        }

        if (!FuncionesPHPLocal::isEmpty($datos['filtarxEscuelaxNivelxTurno'])) {
            $filtroNivel = [];
            foreach ($datos['filtarxEscuelaxNivelxTurno'] as $filtro) {
                if (0 != $filtro['Nivel'])
                    $filtroNivel[] = $filtro['Nivel'];
            }
            if (!empty($filtroNivel)) {
                $sparam['xIdsNivel'] = 1;
                $sparam['IdsNivel'] = implode(',', $filtroNivel);
            }
        }
        return parent::CantidadSolicitudes($sparam, $resultado);
    }


    public function ModificarFechas($datos): bool {
        self::_SetearFechas($datos);
        return parent::ModificarFechas($datos);
    }

    public function ModificarObservacion($datos): bool {

        self::_SetearNull($datos);
        self::_SetearFechas($datos);
        return parent::ModificarObservacion($datos);
    }


    public function actualizarFechaEnvio(array $datos, ...$_): bool {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        $datos['FechaEnvio'] = $datosRegistro['FechaEnvio'] ?: date('Y-m-d H:i:s');


        if (!parent::actualizarFechaEnvio($datos))
            return false;
        /*$oElastic = new Elastic\Modificacion(SUFFIX_SOLICITUDCOBERTURA, $this->conexionES ?? new Elastic\Conexion());
        if (!$this->_armarDatosElastic($datos, $datosRegistro, $datosElastic))
            return false;

        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }*/
        return true;
    }

    public function actualizarPersonaDesignada(array $datos): bool {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        $datosRegistro['IdPersonaDesignada'] = $datos['IdPersonaDesignada'];

        if (!parent::actualizarPersonaDesignada($datos))
            return false;
        $datosRegistro = [];
        $oElastic = new Elastic\Modificacion(SUFFIX_SOLICITUDCOBERTURA, $this->conexionES);
        if (!$this->_armarDatosElastic($datos, $datosRegistro, $datosElastic))
            return false;
        file_put_contents(PUBLICA . 'pd.json', json_encode($datosRegistro));
        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }
        return true;
    }

    /*protected function actualizarNovedadRelacionada(array $datos): bool {
        if (!parent::actualizarNovedadRelacionada($datos))
            return false;

        if (!$this->_armarDatosElastic($datos, $datosRegistro, $datosElastic))
            return false;

        return true;
    }*/


    public function validarExisteRectificado(array $datos): bool {

        $datosBuscar = [
            'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'IdEstado' => RECTIFICADO,
        ];
        $oSolicitudCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion);
        if (!$oSolicitudCoberturaDesempeno->buscarxSolicitud($datosBuscar, $resultado, $numfilas)) {
            $this->setError($oSolicitudCoberturaDesempeno->getError());
            return false;
        }

        if ($numfilas == 0) {
            $this->setError(400, 'Para operar esta accion debe rectificar designaciones');
            return false;
        }

        return true;
    }


    public function validarReabrir(array $datos): bool {
        $datosBuscar = [
            'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'IdEstado' => [NUEVO, CANCELADO, RECTIFICADO],
        ];
        $oSolicitudCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion);
        if (!$oSolicitudCoberturaDesempeno->buscarxSolicitud($datosBuscar, $resultado, $numfilas)) {
            $this->setError($oSolicitudCoberturaDesempeno->getError());
            return false;
        }

        if ($numfilas == 0) {
            $this->setError(400, 'No cumple con las condiciones para reabrir');
            return false;
        }

        return true;
        /*while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            if (in_array($fila['IdEstado'], [NUEVO, CANCELADO, RECTIFICADO]))
                return true;*/


    }


    public function validarCerrarReabierta(array $datos): bool {
        $datosBuscar = [
            'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'IdEstado' => DESIGNADO,
        ];
        $oSolicitudCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion);
        if (!$oSolicitudCoberturaDesempeno->buscarxSolicitud($datosBuscar, $resultado, $numfilas)) {
            $this->setError($oSolicitudCoberturaDesempeno->getError());
            return false;
        }

        if ($numfilas > 0) {
            $this->setError(400, 'No cumple con las condiciones para volver a cerrar.');
            return false;
        }
        return true;
    }

    public function enviarWorkflow(array $datos): bool
    {
        $datos['Id'] = $datos['IdSolicitudCobertura'];
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        $oDocumentosPermisos = new cDocumentosPermisos($this->conexion, $this->formato);
        if (!$oDocumentosPermisos->BuscarMetodosAcciones($datos, $resultado, $numfilas))
            return false;

        $accionPosterior = [];
        if ($numfilas > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

                if (!empty($fila['AccionClase']) && class_exists($fila['AccionClase'])) {
                    if (self::class == $fila['AccionClase'])
                        $oObjeto = $this;
                    else
                        $oObjeto = new $fila['AccionClase']($this->conexion, $this->formato);

                    if (!empty($fila['AccionMetodoPosterior']) && method_exists($oObjeto, $fila['AccionMetodoPosterior']))
                        $accionPosterior[] = ['objeto' => $oObjeto, 'metodo' => $fila['AccionMetodoPosterior']];

                    if (!empty($fila['AccionMetodoPrevio']) && method_exists($oObjeto, $fila['AccionMetodoPrevio'])) {
                        if (!$oObjeto->{$fila['AccionMetodoPrevio']}($datos)) {
                            $this->setError($oObjeto->getError());
                            return false;
                        }
                    }
                }
            }
        }

        if (!$oDocumentosPermisos->BuscarAreasEnvioxIdWorkflowIdSolicitudCoberturaxRol($datos, $resultado, $numfilas))
            return false;
        if ($numfilas < 1) {
            $this->setError(404, 'Error, accion no encontrada.');
            return false;
        }

        $filaWorkflow = $this->conexion->ObtenerSiguienteRegistro($resultado);
        # Circuito - Datos avanzados

        if (!empty($filaWorkflow['Clase']) && class_exists($filaWorkflow['Clase'])) {

            if (self::class == $filaWorkflow['Clase'])
                $oObjeto = $this;
            else
                $oObjeto = new $filaWorkflow['Clase']($this->conexion, $this->formato);
            if (!empty($filaWorkflow['Metodo']) && method_exists($oObjeto, $filaWorkflow['Metodo'])) {
                if (!$oObjeto->{$filaWorkflow['Metodo']}($datos)) {
                    $this->setError($oObjeto->getError());
                    return false;
                }
            }
        }

        $datos['Id'] = $datos['IdSolicitudCobertura'];
        $datos['IdArea'] = $filaWorkflow['IdAreaFinal'];
        $datos['IdEstado'] = $filaWorkflow['IdEstadoFinal'];
        if (!$this->ModificarAreaEstado($datos))
            return false;


        if (!empty($accionPosterior)) {
            foreach ($accionPosterior as ['objeto' => $objeto, 'metodo' => $metodo]) {
                if (!$objeto->{$metodo}($datos)) {
                    $this->setError($objeto->getError());
                    return false;
                }
            }
        }

        $oAuditoriasSolicitudesCobertura = new cAuditoriasSolicitudesCobertura($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        $datosRegistro['Id'] = $datos['IdSolicitudCobertura'];
        if (!$oAuditoriasSolicitudesCobertura->InsertarLog($datosRegistro, $codigoInsertadolog)) {
            $this->setError($oAuditoriasSolicitudesCobertura->getError('error_description'));

            return false;
        }

        return true;

    }

    public function Activar(array $datos): bool {
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['IdEstado'] = ACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasSolicitudesCobertura = new cAuditoriasSolicitudesCobertura($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasSolicitudesCobertura->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }

    public function DesActivar(array $datos): bool {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['IdEstado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        $oAuditoriasSolicitudesCobertura = new cAuditoriasSolicitudesCobertura($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;

        if (!$oAuditoriasSolicitudesCobertura->InsertarLog($datosRegistro, $codigoInsertadolog)) {
            return false;
        }
        return true;
    }

    public function abrirSolicitud($datos, ...$_): bool {
        require_once DIR_ROOT . 'config/include_elastic.php';
        $conexionES = new Elastic\Conexion();

        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;


        $datosEnviar = new stdClass();

        $datosEnviar->Id = $datosRegistro['IdPuesto'];
        $datosEnviar->Tipo = 'Puesto';
        $datosEnviar->SolicitudAbierta = new stdClass();
        $datosEnviar->SolicitudAbierta->Id = (int)$datos['Id'];
        $datosEnviar->SolicitudAbierta->Area = new stdClass();
        $datosEnviar->SolicitudAbierta->Area->Id = (int)$datosRegistro['IdArea'];
        $datosEnviar->SolicitudAbierta->Area->Nombre = $datosRegistro['NombreArea'];
        $datosEnviar->SolicitudAbierta->Estado = new stdClass();
        $datosEnviar->SolicitudAbierta->Estado->Id = (int)$datosRegistro['IdEstado'];
        $datosEnviar->SolicitudAbierta->Estado->Nombre = $datosRegistro['NombreEstado'];
        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $conexionES);
        if (!$oElastic->Actualizar((array)$datosEnviar, $datosEnviar)) {
            $this->setError($oElastic->getError());
            return false;
        }
        return true;
    }

    public function simularInscripciones(array $datos, ...$_): bool {
        $datos['IdSolicitudCobertura'] = $datos['Id'];
        $oInscriptos = new cSolicitudesCoberturaInscriptos($this->conexion, $this->formato, $this->conexionES);
        if (!$oInscriptos->insertarInscriptosSimulados($datos)) {
            $this->setError($oInscriptos->getError());
            return false;
        }

        return true;
    }

    /**
     * @param array     $datos
     * @param bool|null $soloValidar
     * @param mixed     ...$_
     *
     * @return bool
     * @throws Exception
     */
    public function validarHayInscriptos(array $datos, ?bool $soloValidar = false, ...$_): bool {

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas == 0) {
            $this->setError(400, 'Error, no existe la solicitud');
            return false;
        }

        $datosSolicitud = $this->conexion->ObtenerSiguienteRegistro($resultado);

        # BUSCO SI EXISTEN RECTIFICADOS
        $datosBuscar = [
            'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'IdEstado' => RECTIFICADO,
        ];
        $oSolicitudCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion);
        if (!$oSolicitudCoberturaDesempeno->buscarxSolicitud($datosBuscar, $resultado_rectificado, $numfilas_rectificado)) {
            $this->setError($oSolicitudCoberturaDesempeno->getError());
            return false;
        }

        # SI EXISTEN, FRENO PARA CORREGIRLOS
        if ($numfilas_rectificado > 0) {
            $this->setError(400, 'Se encuentran pendientes de corregir designaciones rectificadas');
            return false;
        }

        /**
         * Almacena el rango de fechas de la solicitud, este valor se utilizar� si es necesario validar
         * si la persona designado est� licenciada
         */
        $fechasSolicitud = ['gte' => $datosSolicitud['FechaDesde'], 'lte' => $datosSolicitud['FechaHasta']];

        if (!$this->buscarDesignados($datos, $resultado_anexos))
            return false;

        /** Almacena las personas designadas y sus cargos */
        $personas = [];
        /** Cuenta las sub-solicitudes activas sin persona designada */
        $sin_persona_designada = 0;

        /**
         * Recorre las sub-solicitudes y guarda los datos de las personas designadas en un array,
         * tambi�n considera los puestos sin persona designada
         */
        foreach ($resultado_anexos as $persona) {

//            print_r($persona); die;
            if (FuncionesPHPLocal::isEmpty($persona['IdPersonaDesignada'])) {
                /** Si no hay persona designada incremento el contador. */
                ++$sin_persona_designada;
            } else {
                /** Si hay persona designada guardo los datos en el array correspondiente. */
                $personas[] = [
                    'IdPersonaDesignada' => $persona['IdPersonaDesignada'],
                    'Puestos' => $persona['Puestos'],
                    'aceptarConConflictos' => $persona['ExisteInconsistencia'],
                ];
            }
        }

        if (count($personas) == 0) {
            $this->setError(400, 'Error, debe haber al menos un agente designado.');
            return false;
        }

        if (!isset($datos['conConflicto']) || 0 == $datos['conConflicto']) {
            # COMIENZO VALIDACI�N SOBRE PERSONA
            /**
             * Recorro los datos de personas designadas y las verifico una por una.
             */
            foreach ($personas as $fila) {

                $fila['FechasSolicitud'] = $fechasSolicitud;
                $fila['IdTipoDocumento'] = $datosSolicitud['IdTipoDocumento'];
                $fila['IdPersonaSaliente'] = $datosSolicitud['IdPersonaSaliente'];
                $fila['aceptarConConflictos'] = $fila['aceptarConConflictos'] && SC_PERMITIR_SALTEAR_VALIDACION;
                if (!$this->validarDesingacion($fila))
                    return false;
            }
        }

        return true;
    }

    public function buscarDesignados(array $datos, ?array &$personas): bool {

        $oSolicitudesCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);
        $oSolicitudesCoberturaPuesto = new cSolicitudesCoberturaPuesto($this->conexion, FMT_ARRAY);

        if (!$oSolicitudesCoberturaPuesto->buscarxSolicitudCobertura($datos, $resultado, $numfilas)) {
            $this->setError($oSolicitudesCoberturaPuesto->getError());
            return false;
        }

        if ($numfilas < 1) {
            $this->setError(400, 'No hay puestos');
            return false;
        }

        $puestos = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $fila['Desempenos'] = [];
            $puestos[$fila['Id']] = $fila;
        }

        $datos['IdEstado'] = [NUEVO, DESIGNADO, CANCELADO, FINALIZADO];
        if (!$oSolicitudesCoberturaDesempeno->buscarxSolicitud($datos, $resultado, $numfilas)) {
            $this->setError($oSolicitudesCoberturaDesempeno->getError());
            return false;
        }

        if ($numfilas < 1) {
            $this->setError(400, 'No hay desempenos');
            return false;
        }

        # Array de personas con su puesto y desempe�os del mismo
        $personas = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

            if (empty($personas[$fila['IdPersonaDesignada']]))
                $personas[$fila['IdPersonaDesignada']] = [
                    'IdPersonaDesignada' => $fila['IdPersonaDesignada'],
                    'Puestos' => [],
                ];

            $existeInconsistencia = (bool)$fila['ExisteInconsistencia'];

            $personas[$fila['IdPersonaDesignada']]['ExisteInconsistencia'] = empty($personas[$fila['IdPersonaDesignada']]['ExisteInconsistencia'])
                ? $existeInconsistencia
                : ($existeInconsistencia || $personas[$fila['IdPersonaDesignada']]['ExisteInconsistencia']);

            if (empty($personas[$fila['IdPersonaDesignada']]['Puestos'][$fila['IdSolicitudCoberturaPuesto']]))
                $personas[$fila['IdPersonaDesignada']]['Puestos'][$fila['IdSolicitudCoberturaPuesto']] = $puestos[$fila['IdSolicitudCoberturaPuesto']];

            $personas[$fila['IdPersonaDesignada']]['Puestos'][$fila['IdSolicitudCoberturaPuesto']]['Desempenos'][] = $fila;

        }

        return true;
    }

    public function validarDesingacion(array $fila): bool {
        $tieneConflictoPrevio = false;
        $oPuestos = new Elastic\Puestos($this->conexionES);
        $oIncompatibilidades = new Elastic\Incompatibilidades($this->conexionES);
        $oIncompatibilidadesHoras = new cInconsistenciasHoras($this->conexion, FMT_ARRAY, $this->conexionES);

        if ($fila['IdPersonaSaliente'] == $fila['IdPersonaDesignada']) {
            $this->setError(400, 'Error, no se puede designar al mismo agente que est&aacute; siendo suplido');
            return false;
        }

        $datosBuscar['IdPersona'] = $fila['IdPersonaDesignada'];
        $datosBuscar['Fechas'] = $fila['FechasSolicitud'];
        if (!$this->validarPersonaActiva($datosBuscar))
            return false;

        /**
         * Array que se env�a a los m�todos de validaci�n de superposici�n e incompatibilidades
         */
        $datosNuevo = [
            'desempeno' => [],
            'horas' => [],
            'cargos' => [
                'cargosJerarquicos' => null,
                'cargosNoJerarquicos' => null,
                'cargosAdministrativos' => null,
                'cargosBase' => null,
                'cargoSupervisor' => null,
                'horasCatedra' => null,
                'horasCatedraItinerantes' => null,
            ],
        ];

        /** Horas afectadas del puesto */
        $horas = 0;
        foreach ($fila['Puestos'] as $puesto) {

            $datosBuscar['Id'] = $puesto['IdPuesto'];
            $datosBuscar['excluirCampos'] = ['Id', 'UltimaModificacion.', 'Alta.', 'Tipo'];

            if (!$oPuestos->buscarxCodigo($datosBuscar, $datosPuesto)) {
                $this->setError($oPuestos->getError());
                return false;
            }

            switch ($fila['IdTipoDocumento'] ?? null) {
                case (defined('NOV_SC_DIR_PRIMARIO') ? NOV_SC_DIR_PRIMARIO : -1):
                    /**
                     * Caso de cargos jer�rquicos, si la variable no fue seteada a�n, la seteo en uno,
                     * sino incremento el valor. esta variable se utiliza pata la validaci�n de incompatibilidades
                     */
                    if (is_null($datosNuevo['cargos']['cargosJerarquicos']))
                        $datosNuevo['cargos']['cargosJerarquicos'] = 1;
                    else
                        ++$datosNuevo['cargos']['cargosJerarquicos'];

                    $sumarDesempeno = false;
                    break;
                case (defined('NOV_SC_AUX_PRIMARIO') ? NOV_SC_AUX_PRIMARIO : -2):
                    /**
                     * Caso de cargos auxiliares, idem jer�rquicos
                     */
                    if (is_null($datosNuevo['cargos']['cargosNoJerarquicos']))
                        $datosNuevo['cargos']['cargosNoJerarquicos'] = 1;
                    else
                        ++$datosNuevo['cargos']['cargosNoJerarquicos'];

                    $sumarDesempeno = false;
                    break;
                case (defined('NOV_SC_DOC_SECUNDARIO') ? NOV_SC_DOC_SECUNDARIO : -3):
                case (defined('NOV_SC_DOC_PRIMARIO') ? NOV_SC_DOC_PRIMARIO : -4):
                case (defined('NOV_SC_DOC_URGENTE_PRIMARIO') ? NOV_SC_DOC_URGENTE_PRIMARIO : -5):
                case (defined('NOV_SC_INICIAL') ? NOV_SC_INICIAL : -6):
                default:
                    /**
                     * Caso de horas c�tedra, si no est�n seteadas las seteo en cero,
                     * al recorrer los desempe�os se incrementa una variable temporal
                     * la cual se insertar� en esta variable redondeada a la pr�xima
                     * hora entera, e.g. de 1:01 a 1:59 se redondea a dos horas
                     */
                    if (is_null($datosNuevo['cargos']['horasCatedra'])) {
                        $datosNuevo['cargos']['horasCatedra'] = 0;
                        $datosNuevo['cargos']['horasCatedraItinerantes'] = 0;
                    }

                    $sumarDesempeno = true;
                    if (empty($datosPuesto['Materia']['Id'])) {
                        /**
                         * Caso de cargos base, idem auxiliares
                         */
                        if (is_null($datosNuevo['cargos']['cargosNoJerarquicos']))
                            $datosNuevo['cargos']['cargosNoJerarquicos'] = 1;
                        else
                            ++$datosNuevo['cargos']['cargosNoJerarquicos'];

                        $sumarDesempeno = false;
                    }
            }

            foreach ($puesto['Desempenos'] as $filaDesempeno) {

                # EXCLUIR DESIGNACIONES YA APROBADAS O PENDIENTES DE APROBACI�N
                if (!in_array($filaDesempeno['IdEstado'], [FINALIZADO, CONFIRMADO])) {
                    $idPuesto = $puesto['IdPuesto'] . 'p';
                    $dia = (int)$filaDesempeno['Dia'];
                    $horario = new stdClass();
                    $horario->gte = new DateTime($filaDesempeno['HoraInicio']);
                    $horario->lte = new DateTime($filaDesempeno['HoraFin']);
                    /** Componente del array de datos que se utiliza para la superposici�n horaria */
                    $datosNuevo['desempeno'][$dia][$idPuesto] = [
                        'id' => (int)$puesto['IdPuesto'],
                        'dia' => $filaDesempeno['Dia'],
                        'horario' => (object)['gte' => $filaDesempeno['HoraInicio'], 'lte' => $filaDesempeno['HoraFin']],
                        'desde' => substr($filaDesempeno['HoraInicio'], 0, 5),
                        'hasta' => substr($filaDesempeno['HoraFin'], 0, 5),
                        'puesto' => $datosPuesto,
                    ];
                    $datosNuevo['horas'][$dia][$idPuesto] = $horario;

                    if (!$sumarDesempeno)
                        continue;

                    $diff = $horario->lte->diff($horario->gte);
                    $horas += MULTIPLICAR_HORAS ?
                        $filaDesempeno['CantidadHorasModulos'] / FACTOR_ESCALA :
                        $diff->h + ($diff->i / 60);
                }
            }
        }
        if (!is_null($datosNuevo['cargos']['horasCatedra']))
            $datosNuevo['cargos']['horasCatedra'] = ceil($horas);

        $datos['IdPersona'] = $fila['IdPersonaDesignada'];

        if (!$oIncompatibilidades->validarSuperposicionHoraria($datos, $resumen, $datosNuevo)) {
            $this->setError($oIncompatibilidades->getError());
            return false;
        }

        if ($resumen['hay_conflictos']) {
            $this->setError(409, json_encode($resumen['colisiones']));
            $tieneConflictoPrevio = true;
            if (empty($fila['aceptarConConflictos']))
                return false;
        }

        if (!$oIncompatibilidadesHoras->validarPersona($datos, $registro, $resumen, $datosNuevo['cargos'])) {
            $this->setError($oIncompatibilidades->getError());
            return false;
        }
        unset($registro);
        if ($resumen['tiene_conflictos']) {
            if (empty($fila['aceptarConConflictos'])) {
                $this->setError(400, $resumen['error_msg']);
                return false;
            } else {
                if (!$tieneConflictoPrevio)
                    $this->setError(409, $resumen['error_msg']);
                else {
                    $conflictos = new stdClass();
                    $conflictos->Horario = json_decode($this->getError('error_description'));
                    $conflictos->Reglas = $resumen['error_msg'];
                    $this->setError(409, json_encode($conflictos));
                }
            }
        }

        return true;
    }

    public function validarPreDesignacion($datos): bool {

        $datosBuscar['Id'] = $datos['IdSolicitudCobertura'];
        if (!$this->BuscarxCodigo($datosBuscar, $resultado, $numfilas))
            return false;


        $solicitud = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!$this->buscarDesignados($datos, $personas))
            return false;

        $oPofa = new cEscuelasPuestosPersonas($this->conexion);

        foreach ($personas as $p => $datosSolicitud) {
            foreach ($datosSolicitud['Puestos'] as $k => $datosPuesto) {
                $datosValidarPersona = [
                    'IdPuesto' => $datosPuesto['IdPuesto'],
                    'IdPersona' => $datos['IdPersonaDesignada']
                ];

                if(!$oPofa->ValidarPersonaYaPresenteEnPuesto($datosValidarPersona, $resultadoRepetida, $numfilasRepetida)) {
                    $this->setError(400, $oPofa->getError()['error_description']);
                    return false;
                }
            }
        }

        # DATOS SEG�N TIPO DE DESIGNACI�N
        # $datosValidar SOLO CON LOS DATOS DEL PUESTO/DESEMPE�O NECESARIOS
        $datosValidar = [];
        foreach ($personas as $key => $p) {

            if ($key != '')
                continue;

            switch ($datos['TipoDesignacion']) {
                case 'puesto':
                    foreach ($p['Puestos'] as $puesto) {
                        if ($puesto['Id'] == $datos['IdSolicitudCoberturaPuesto'])
                            $datosValidar['Puestos'][] = $puesto;
                    }
                    break;
                case 'desempeno':
                    foreach ($p['Puestos'] as $puesto) {
                        foreach ($puesto['Desempenos'] as $desempeno) {
                            if ($desempeno['Id'] == (int)$datos['Id']) {
                                unset($puesto['Desempenos']);
                                $datosValidar['Puestos'][0] = $puesto;
                                $datosValidar['Puestos'][0]['Desempenos'][] = $desempeno;
                            }
                        }
                    }
                    break;
                case 'todos':
                    $datosValidar = $p;
                    break;
            }

            $datosValidar['FechasSolicitud'] = (object)['gte' => $solicitud['FechaDesde'], 'lte' => $solicitud['FechaHasta']];
            $datosValidar['IdPersonaDesignada'] = $datos['IdPersonaDesignada'];
            $datosValidar['IdPersonaSaliente'] = $solicitud['IdPersonaSaliente'];
            $datosValidar['aceptarConConflictos'] = $datos['conConflicto'] ?? false;

            if (!$this->validarDesingacion($datosValidar))
                return false;
        }

        return true;
    }


    /**
     * ### Valida el estado del agente
     *
     * Verifica que el agente este en un estado activo
     *
     * @param array $datos
     *
     * @return bool
     * @todo
     *      - Persona licenciada: Ahora lo averiguamos porque quiz�s estatutariamente lo permite.
     *
     */
    public function validarPersonaActiva(array $datos): bool {
        $oPersona = new Elastic\Personas($this->conexionES);
        $datos['incluirCampos'] = ['EstadoPersona.*'];
        if (!$oPersona->buscarxCodigo($datos, $datosPersona)) {
            $this->setError($oPersona->getError());
            return false;
        }
        $ret = $datosPersona['EstadoPersona']['Activo'] ?? false;
        if (!$ret)
            $this->setError(
                400,
                'Error, el agente no esta activo, su estado actual es ' . mb_strtolower($datosPersona['EstadoPersona']['Nombre'])
            );

        if (defined('VALIDAR_LICENCIADOS') && VALIDAR_LICENCIADOS && isset($datos['Fechas'])) {
            $oLicencias = new Elastic\Licencias($this->conexionES);
        }

        return $ret;
    }

    public function validarDesempenos(array $datos, &$resultado = null): bool {
        $oDesempenos = new cSolicitudesCoberturaDesempeno($this->conexion, $this->formato);
        $datos['IdSolicitudCobertura'] = $datos['IdSolicitudCobertura'] ?? $datos['Id'];
        if (!$oDesempenos->buscarxSolicitud($datos, $resultado, $numfilas)) {
            $this->setError($oDesempenos->getError());
            return false;
        }
        if ($numfilas == 0) {
            $this->setError(400, 'Error, el cargo no tiene desempe&ntilde;o');
            return false;
        }
        return true;
    }

    public function validarDesempenosxPersona(array $datos, &$resultado = null): bool {
        $oDesempenos = new cSolicitudesCoberturaDesempeno($this->conexion, $this->formato);
        $datos['IdSolicitudCobertura'] = $datos['IdSolicitudCobertura'] ?? $datos['Id'];
        if (!$oDesempenos->buscarxSolicitudxPersona($datos, $resultado, $numfilas)) {
            $this->setError($oDesempenos->getError());
            return false;
        }
        if ($numfilas == 0) {
            $this->setError(400, 'Error, el cargo no tiene desempe&ntilde;o');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     * @param mixed ...$_
     *
     * @return bool
     */
    public function crearDesignacion(array $datos, ...$_): bool {

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if (1 != $numfilas) {
            $this->setError(404, 'Error, no existe la solicitud');
            return false;
        }
        $datosSolicitud = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!$this->crearPuestos($datos))
            return false;

        $oNovedad = new cDocumentos($this->conexion, FMT_ARRAY, $this->conexionES ?? new Elastic\Conexion());
        $this->obtenerTipoAltaxNivelTipoCargo($datosSolicitud);

        $oSolicitudCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);
        $datosBuscar = [
            'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'IdEstado' => DESIGNADO,
        ];

        if (!$oSolicitudCoberturaDesempeno->buscarxSolicitud($datosBuscar, $resultado_persona_puesto, $numfilas_persona_puesto)) {
            $this->setError($oSolicitudCoberturaDesempeno->getError());
            return false;
        }

        if ($numfilas_persona_puesto == 0) {
            $this->setError(404, 'Error, no existen personas asignadas');
            return false;
        }

        $persona = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado_persona_puesto)) {
            if (empty($fila['IdPersonaDesignada']))
                continue;
            $persona[$fila['IdPersonaDesignada']][$fila['IdPuesto']] = 1;
            $persona[$fila['IdPersonaDesignada']]['InstrumentoLegal'] = $fila['InstrumentoLegal'];
            $persona[$fila['IdPersonaDesignada']]['FechaDesignacion'] = $fila['FechaDesignacion'];
        }


        $datosModificar = [
            'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'IdEstado' => DESIGNADO,
            'IdEstadoNuevo' => CONFIRMADO,
        ];
        if (!$oSolicitudCoberturaDesempeno->modificarEstadoxSolicitudxEstado($datosModificar)) {
            $this->setError($oSolicitudCoberturaDesempeno->getError());
            return false;
        }

        $datosNovedad = $datosSolicitud;
        if (strtotime($datosSolicitud['FechaDesde']) > strtotime($datosSolicitud['FechaHasta'])) {
            $this->setError(400, 'Error la fecha hasta es anterior a la fecha desde');
            return false;
        }
        $datosNovedad['IdSolicitudCobertura'] = $datos['IdSolicitudCobertura'];
        $datosNovedad['PeriodoFechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datosSolicitud['FechaDesde'], 'aaaa-mm-dd', 'dd/mm/aaaa');
        $datosNovedad['PeriodoFechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datosSolicitud['FechaHasta'], 'aaaa-mm-dd', 'dd/mm/aaaa');
        $datosNovedad['FechaDesde'] = null;
        $datosNovedad['IdArea'] = 1;

        //Novedad por persona designada
        foreach ($persona as $key_persona => $p) {

            $datosNovedadIns = $datosNovedad;
            $datosNovedadIns['IdPersona'] = $key_persona;
            $datosNovedadIns['NroResolucion'] = $p['InstrumentoLegal'];
            $datosNovedadIns['FechaDesignacion'] = FuncionesPHPLocal::ConvertirFecha($p['FechaDesignacion'], 'aaaa-mm-dd', 'dd/mm/aaaa');;
            foreach ($p as $key_puesto => $puestos) {
                $datosNovedadIns['Puesto_' . $key_puesto] = 1;
            }

            unset($datosNovedadIns['Puesto_InstrumentoLegal'], $datosNovedadIns['Puesto_FechaDesignacion']);
            if (!$oNovedad->Insertar($datosNovedadIns, $codigoinsertado)) {
                $this->setError(400, 'error al insertar.');
                return false;
            }

            $datosModificar = [
                'IdNovedad' => $codigoinsertado,
                'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
                'IdPersonaDesignada' => $key_persona,
            ];
            if (!$oSolicitudCoberturaDesempeno->modificarNovedadxPersonaxSolicitud($datosModificar)) {
                $this->setError($oSolicitudCoberturaDesempeno->getError());
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $datos
     * @param mixed ...$_
     *
     * @return bool
     */
    public function crearDesignacionVacante(array $datos, ...$_): bool {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if (1 != $numfilas) {
            $this->setError(404, 'Error, no existe la solicitud');
            return false;
        }
        $datosSolicitud = $this->conexion->ObtenerSiguienteRegistro($resultado);
        $datosSolicitud['Vacante'] = 1;
        $oNovedad = new cDocumentos($this->conexion, FMT_ARRAY, $this->conexionES ?? new Elastic\Conexion());
        $this->obtenerTipoAltaxNivelTipoCargo($datosSolicitud);

        $oSolicitudCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);
        $datosBuscar = [
            'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'IdEstado' => DESIGNADO,
        ];
        if (!$oSolicitudCoberturaDesempeno->buscarxSolicitud($datosBuscar, $resultado_persona_puesto, $numfilas_persona_puesto)) {
            $this->setError($oSolicitudCoberturaDesempeno->getError());
            return false;
        }

        if ($numfilas_persona_puesto == 0) {
            $this->setError(404, 'Error, no existen personas asignadas');
            return false;
        }

        $persona = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado_persona_puesto)) {
            if (empty($fila['IdPersonaDesignada']))
                continue;
            $persona[$fila['IdPersonaDesignada']][$fila['IdPuesto']] = 1;
            $persona[$fila['IdPersonaDesignada']]['InstrumentoLegal'] = $fila['InstrumentoLegal'];
            $persona[$fila['IdPersonaDesignada']]['FechaDesignacion'] = $fila['FechaDesignacion'];
        }

        $datosModificar = [
            'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'IdEstado' => DESIGNADO,
            'IdEstadoNuevo' => CONFIRMADO,
        ];
        if (!$oSolicitudCoberturaDesempeno->modificarEstadoxSolicitudxEstado($datosModificar)) {
            $this->setError($oSolicitudCoberturaDesempeno->getError());
            return false;
        }

        $datosNovedad = $datosSolicitud;
        $datosNovedad['IdSolicitudCobertura'] = $datos['IdSolicitudCobertura'];
        $datosNovedad['PeriodoFechaDesde'] = $datosNovedad['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datosSolicitud['FechaDesde'], 'aaaa-mm-dd', 'dd/mm/aaaa');
        $datosNovedad['IdArea'] = 1;

        $key = [];
        foreach ($persona as $key_persona => $p) {
            $datosNovedadIns = $datosNovedad;
            $datosNovedadIns['IdPersona'] = $key_persona;
            $datosNovedadIns['NroResolucion'] = $p['InstrumentoLegal'];
            $datosNovedadIns['FechaDesignacion'] = FuncionesPHPLocal::ConvertirFecha($p['FechaDesignacion'], 'aaaa-mm-dd', 'dd/mm/aaaa');;
            foreach ($p as $key_puesto => $puestos) {
                $key[] = $key_puesto;
                $datosNovedadIns['Puesto_' . $key_puesto] = 1;
            }
            $datosNovedadIns['IdPuesto'] = $key[0];
            unset($datosNovedadIns['Puesto_InstrumentoLegal'], $datosNovedadIns['Puesto_FechaDesignacion']);

            if (!$oNovedad->Insertar($datosNovedadIns, $codigoinsertado)) {
                $this->setError(400, 'error al insertar novedad vacante.');
                return false;
            }

            $datosModificar = [
                'IdNovedad' => $codigoinsertado,
                'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
                'IdPersonaDesignada' => $key_persona,
            ];
            if (!$oSolicitudCoberturaDesempeno->modificarNovedadxPersonaxSolicitud($datosModificar)) {
                $this->setError($oSolicitudCoberturaDesempeno->getError());
                return false;
            }
        }

        return true;
    }


    public function crearPuestos($datos): bool {

        if (is_null($this->conexionES))
            $this->conexionES = new Elastic\Conexion();

        $oEscuelasPuestos = new cEscuelasPuestos($this->conexion, $this->conexionES, FMT_ARRAY);
        $oEscuelasPuestosDesempeno = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES, FMT_ARRAY);
        $oSolicitudCoberturaPuesto = new cSolicitudesCoberturaPuesto($this->conexion, FMT_ARRAY);
        $oSolicitudesCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);

        # Busca los datos de los puestos que tengan una persona designada en la solicitud
        if (!$oSolicitudCoberturaPuesto->buscarEscuelasPuestosxSolicitud($datos, $resultado, $numfilas)) {
            $this->setError($oSolicitudCoberturaPuesto->getError());
            return false;
        }

        if ($numfilas == 0) {
            $this->setError(400, 'Error, no se encuentran puestos asociados');
            return false;
        }

        $puesto = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $puesto[$fila['IdPuesto']][$fila['IdPersonaDesignada']][] = $fila;
        }


        //Busco cant de desempenos por SC
        $datosDesempenoSC['IdEstado'] = 2;
        $datosDesempenoSC['IdSolicitudCobertura'] = $datos['IdSolicitudCobertura'];
        if (!$oSolicitudesCoberturaDesempeno->buscarxSolicitud($datosDesempenoSC, $resultadoDesempenoSC, $numfilasDesempenoSC))
            return false;

        foreach ($puesto as $key_puesto => $p) {
            $datosInsertarPuesto = [];
            foreach ($p as $key_persona => $subpuesto) {

                $datosInsertarDesempeno = [];
                $IdsDesempenos = [];
                $horasModulos = 0;
                foreach ($subpuesto as $key => $r) {
                    # CREAR PUESTO AGRUPADO POR PERSONA DESIGNADA CON DESEMPE�OS CORRESPONDIENTES
                    $datosInsertarPuesto = $r;
                    $datosInsertarPuesto['IdPuestoPadre'] = $r['IdPuesto'];
                    $datosInsertarPuesto['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($r['FechaDesde'], 'aaaa-mm-dd', 'dd/mm/aaaa');
                    $datosInsertarPuesto['CargaManual'] = 0;


                    //Busco cant de desempenos por puesto

                    $datosEPDesempeno['IdPuesto'] = $r['IdPuesto'];
                    if (!$oEscuelasPuestosDesempeno->BuscarxCodigo($datosEPDesempeno, $resultadoEPDesempeno, $numfilasEPDesempeno))
                        return false;

                    # SUMAR HORAS MODULOS DEL PUESTO
                    // si hay mas de una persona y es plaza partida inserto en cantidad el calculo basado en el desempeño o
                    // si es diferente a la cant de desempeños del puesto padre
                    if (count($p) > 1 || (count($p) == 1 && $numfilasEPDesempeno != $numfilasDesempenoSC)) {

                        if (!$oSolicitudesCoberturaDesempeno->BuscarxCodigo(["Id" => $r["IdSolicitudCoberturaDesempeno"]], $res_desempeno, $numfilas_desempeno)) {
                            $this->setError(400, 'Error al buscar los horarios del puesto');
                            return false;
                        }

                        if ($numfilas_desempeno != 1) {
                            $this->setError(400, 'Error al buscar los horarios del puesto');
                            return false;
                        }

                        $dato_desempeno = $this->conexion->ObtenerSiguienteRegistro($res_desempeno);

                        $horasModulos += $dato_desempeno['CantidadHorasModulos'] / FACTOR_ESCALA;

                        if ($r['TipoCantidad'] == 1) {
                            # horas
                            $datosInsertarPuesto['CantHoras'] = $horasModulos;
                            $datosInsertarPuesto['CantModulos'] = 'NULL';
                        } else {
                            # m�dulos
                            $datosInsertarPuesto['CantModulos'] = $horasModulos;
                            $datosInsertarPuesto['CantHoras'] = 'NULL';
                        }

                    } else {

                        // Si no, toma cant semanal del titular
                        if ($r['TipoCantidad'] == 1) {
                            # horas
                            $datosInsertarPuesto['CantHoras'] = $r["CantHoras"];
                            $datosInsertarPuesto['CantModulos'] = 'NULL';
                        } else {
                            # m�dulos
                            $datosInsertarPuesto['CantModulos'] = $r["CantModulos"];
                            $datosInsertarPuesto['CantHoras'] = 'NULL';
                        }
                    }
                    $datosInsertarDesempeno[$key_puesto][$key_persona][$key]['Dia'] = $r['Dia'];
                    $datosInsertarDesempeno[$key_puesto][$key_persona][$key]['HoraInicio'] = $r['HoraInicio'];
                    $datosInsertarDesempeno[$key_puesto][$key_persona][$key]['HoraFin'] = $r['HoraFin'];

                    $IdsDesempenos[$key_puesto][$key_persona][$key] = $r['IdSolicitudCoberturaDesempeno'];
                }

                # CREAR NUEVO PUESTO HIJO
                if (!$oEscuelasPuestos->Insertar($datosInsertarPuesto, $codigoInsertado)) {
                    $this->setError($oEscuelasPuestos->getError());
                    return false;
                }

                # ACTUALIZAR EN SC DESEMPENO -> IDPUESTOS
                foreach ($IdsDesempenos as $idpersona) {
                    foreach ($idpersona as $iddesempeno) {
                        foreach ($iddesempeno as $id) {
                            $datosModificar = [
                                'Id' => $id,
                                'IdPuesto' => $codigoInsertado,
                            ];
                            if (!$oSolicitudesCoberturaDesempeno->modificarPuesto($datosModificar)) {
                                $this->setError($oSolicitudesCoberturaDesempeno->getError());
                                return false;
                            }
                        }
                    }
                }

                # CREAR DESEMPENOS ASOCIADOS A PUESTO HIJO
                foreach ($datosInsertarDesempeno as $idpersona) {
                    foreach ($idpersona as $iddesempeno) {
                        foreach ($iddesempeno as $fila) {
                            $datosInsertar = [
                                'IdPuesto' => $codigoInsertado,
                                'Dia' => $fila['Dia'],
                                'HoraInicio' => $fila['HoraInicio'],
                                'HoraFin' => $fila['HoraFin'],
                                'Estado' => ACTIVO,
                            ];
                            if (!$oEscuelasPuestosDesempeno->Insertar($datosInsertar, $desempenoInsertado)) {
                                $this->setError($oEscuelasPuestosDesempeno->getError());
                                return false;
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    //-----------------------------------------------------------------------------------------
    //FUNCIONES PRIVADAS
    //-----------------------------------------------------------------------------------------

    public function cerrarSolicitud($datos, ...$_): bool {
        // print_r($datos);die;
        require_once DIR_ROOT . 'config/include_elastic.php';
        $conexionES = new Elastic\Conexion();
        $datosEnviar = new stdClass();

        if (empty($datos['IdPuesto'])) {
            if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
                return false;
            $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $datos['IdPuesto'] = $fila['IdPuesto'];
        }

        $datosEnviar->Id = $datos['IdPuesto'];
        $datosEnviar->Tipo = 'Puesto';
        $datosEnviar->SolicitudAbierta = null;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $conexionES);
        if (!$oElastic->Actualizar((array)$datosEnviar, $datosEnviar)) {
            $this->setError($oElastic->getError());
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     * @param       $constante
     *
     * @return bool
     */
    public function obtenerTipoSolicitudxNivelTipoCargo(array $datos, &$constante): bool {}

    /**
     * @param array $datos
     *
     * @return string
     */
    public function definirSolicitudDefault(array $datos): string {

        switch ($datos['IdNivel']) {
            case NIVEL_INICIAL:
            case NIVEL_PRIMARIO:
                $constante = 'SC_DEFAULT_PRIMARIO';
                break;
            case NIVEL_SECUNDARIO:
                $constante = 'SC_DEFAULT_SECUNDARIO';
                break;
            default:
                $constante = 'SC_DEFAULT';
                break;
        }
        return $constante;
    }

    /**
     * @param array $datos
     *
     * @return void
     */
    public function obtenerTipoAltaxNivelTipoCargo(array &$datos): void {

        if (isset($datos['Vacante']) && $datos['Vacante']) {
            $datos['IdTipoDocumento'] = NOV_ALTA_AGENTE_VACANTE;
            $datos['IdRegistroTipoDocumento'] = NOV_REGISTRO_ALTA_AGENTE_VACANTE;
        } else {
            if (!empty($datos['IdTipoDocumento']) && in_array($datos['IdTipoDocumento'], [NOV_SC_AUX_PRIMARIO, NOV_SC_AUX_SECUNDARIO])) {
                $datos['IdTipoDocumento'] = NOV_ALTA_SUPLENTE_NO_DOC;
                $datos['IdRegistroTipoDocumento'] = NOV_REGISTRO_ALTA_SUPLENTE_NO_DOC;
            } else {

                $datos['IdTipoDocumento'] = NOV_ALTA_SUPLENTE_DOC;
                $datos['IdRegistroTipoDocumento'] = NOV_REGISTRO_ALTA_SUPLENTE_DOC;
            }
        }
    }

    public function eliminarSobrantes(array $datos): bool {

        $oSolicitudCoberturaPuesto = new cSolicitudesCoberturaPuesto($this->conexion);
        $oSolicitudCoberturaPersona = new cSolicitudesCoberturaPersona($this->conexion);
        $datosEliminarPuesto = $datosEliminarPersona = [];

        # busco por puesto-persona y elimino
        if (!$oSolicitudCoberturaPersona->buscarPersonasPuestosSobrantes($datos, $resultadoPuestoPersona, $numfilasPuestoPersona)) {
            $this->setError($oSolicitudCoberturaPersona->getError());
            return false;
        }
        if ($numfilasPuestoPersona > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoPuestoPersona)) {
                $datosEliminarPersona['Ids'][] = $fila['IdSCPersona'] ?? '';
                $datosEliminarPuesto['Ids'][] = $fila['IdSCPuesto'] ?? '';
            }

            if (!empty($datosEliminarPuesto)) {
                if (!$oSolicitudCoberturaPuesto->EliminarVarios($datosEliminarPuesto)) {
                    $this->setError($oSolicitudCoberturaPuesto->getError());
                    return false;
                }
            }

            if (!empty($datosEliminarPersona)) {
                if (!$oSolicitudCoberturaPersona->EliminarVarios($datosEliminarPersona)) {
                    $this->setError($oSolicitudCoberturaPersona->getError());
                    return false;
                }
            }
        }

        # busco por puesto
        if (!$oSolicitudCoberturaPuesto->buscarPuestosSobrantes($datos, $resultadoPuesto, $numfilasPuesto)) {
            $this->setError($oSolicitudCoberturaPuesto->getError());
            return false;
        }

        if ($numfilasPuesto > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoPuesto)) {
                if (is_null($fila['IdSCDesempeno'])) {
                    $datosEliminarPuesto['Ids'][] = $fila['IdSCPuesto'];
                }
            }

            if (!empty($datosEliminarPuesto)) {
                if (!$oSolicitudCoberturaPuesto->EliminarVarios($datosEliminarPuesto)) {
                    $this->setError($oSolicitudCoberturaPuesto->getError());
                    return false;
                }
            }
        }

        # busco por persona
        if (!$oSolicitudCoberturaPersona->buscarPersonasSobrantes($datos, $resultadoPersona, $numfilasPersona)) {
            $this->setError($oSolicitudCoberturaPersona->getError());
            return false;
        }

        if ($numfilasPersona > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoPersona)) {
                if (is_null($fila['IdSCPuesto'])) {
                    $datosEliminarPersona['Ids'][] = $fila['IdSCPersona'];
                }
            }

            if (!empty($datosEliminarPersona)) {
                if (!$oSolicitudCoberturaPersona->EliminarVarios($datosEliminarPersona)) {
                    $this->setError($oSolicitudCoberturaPersona->getError());
                    return false;
                }
            }
        }

        return true;
    }

    private function _validarFechasLicencia(array $datos): bool {

        if (!FuncionesPHPLocal::isEmpty($datos['IdLicencia'])) {
            $oLicencias = new Elastic\Licencias($this->conexionES);
            $datos['size'] = 0;
            $datos['from'] = 0;
            $datos['dateFormat'] = 'dd/MM/yyyy';
            if (!$oLicencias->buscarxIdFechas($datos, $resultado, $numfilas, $total)) {
                $this->setError($oLicencias->getError());
                return false;
            }

            /*if ($total < 1) {
                $this->setError(400, 'Error, las fechas de la solicitud no se encuentran dentro del rango de la licencia');
                return false;
            }*/
        }

        return true;
    }

    private function _ValidarInsertar($datos) {

        if (!$this->_ValidarDatosVacios($datos))
            return false;

        if (!$this->_validarSolicitud($datos))
            return false;

        return $this->_validarFechasLicencia($datos);
    }

    private function _ValidarInsertarVacante($datos) {

        if (!$this->_ValidarDatosVacios($datos))
            return false;

        // validacion por IdPuesto , IdTipoDocumento , Estado, IdEstado pendientes
        if(!$this->ValidarAltaPorSCV($datos, $SolicitudPendiente, $IdScv)){
            $this->setError(500, "Error al buscar solicitudes pendientes");
            return false;
        }

        if($SolicitudPendiente) {
            $this->setError(400, "Se encontró una Solicitud de Cobertura Vacante sin finalizar con ID ". $IdScv);
            return false;
        }

        return true;
    }

    /**
     * @param array      $datos
     * @param array|null $datosRegistro
     * @param false      $cargarDatos
     *
     * @return bool
     */
    private function _ValidarModificar(array $datos, ?array &$datosRegistro, $cargarDatos = false): bool {

        /*if (!$this->validarDesempenos($datos))
            return false;*/

        if (!$this->buscarParElastic($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un codigo valido.");
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        if (!$this->_validarSolicitud($datos))
            return false;


        if (!$this->_validarFechasLicencia($datos))
            return false;

        return !$cargarDatos || $this->cargarDatos($datosRegistro);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    private function cargarDatos(array $datos): bool {
        $this->datosSolicitud = $datos;

        $oSolicitudesCoberturaPersona = new cSolicitudesCoberturaPersona($this->conexion, FMT_ARRAY);
        $oSolicitudesCoberturaPuesto = new cSolicitudesCoberturaPuesto($this->conexion, FMT_ARRAY);
        $oSolicitudesCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);

        $datos['IdSolicitudCobertura'] = $datos['Id'];
        if (!$oSolicitudesCoberturaPersona->buscarxSolicitudCobertura($datos, $resultado, $numfilas)) {
            $this->setError($oSolicitudesCoberturaPersona->getError());
            return false;
        }

        $this->datosSolicitud['designadas'] = [];
        $this->datosSolicitud['subSolicitudes'] = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $id = (int)$fila['Id'];
            $idPersonaDesignada = empty($fila['IdPersonaDesignada']) ? 'null' : ((int)$fila['IdPersonaDesignada']);
            $this->datosSolicitud['designadas'][$idPersonaDesignada] = $id;
            $this->datosSolicitud['subSolicitudes'][$id] = $fila;
            $this->datosSolicitud['subSolicitudes'][$id]['puestos'] = [];
        }

        if (!$oSolicitudesCoberturaPuesto->BuscarxSolicitudCobertura($datos, $resultado, $numfilas)) {
            $this->setError($oSolicitudesCoberturaPuesto->getError());
            return false;
        }

        $this->datosSolicitud['puestos'] = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $idSCPersona = (int)$fila['IdSolicitudCoberturaPersona'];
            $id = (int)$fila['Id'];
            $idPuesto = (int)$fila['IdPuesto'];
            if (empty($this->datosSolicitud['puestos'][$idPuesto]))
                $this->datosSolicitud['puestos'][$idPuesto] = [];
            $this->datosSolicitud['puestos'][$idPuesto][$idSCPersona] = $id;
            $this->datosSolicitud['puestosSC'][$id] = $idSCPersona;
            $this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos'][$id] = $fila;
            $this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos'][$id]['desempenos'] = [];
        }

        if (!$oSolicitudesCoberturaDesempeno->buscarxSolicitud($datos, $resultado, $numfilas)) {
            $this->setError($oSolicitudesCoberturaDesempeno->getError());
            return false;
        }

        $this->datosSolicitud['desempenos'] = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $idSCPersona = (int)$fila['IdSolicitudCoberturaPersona'];
            $idSCPuesto = (int)$fila['IdSolicitudCoberturaPuesto'];
            $id = (int)$fila['Id'];
            $this->datosSolicitud['subSolicitudes'][$idSCPersona]['puestos'][$idSCPuesto]['desempenos'][$id] = $fila;
            $this->datosSolicitud['desempenos'][$id] = [
                'idSCPersona' => $idSCPersona,
                'idSCPuesto' => $idSCPuesto,
            ];
        }
        //print_r($this->datosSolicitud);die;
        return true;
    }

    private function _ValidarEliminar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un codigo valido.");
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }


    private function _SetearFechas(&$datos): void {

        if (isset($datos['FechaDesde']))
            $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');

        if (isset($datos['FechaHasta']) && !is_null($datos['FechaHasta']))
            $datos['FechaHasta'] = empty($datos['FechaHasta']) ? null : FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], 'dd/mm/aaaa', 'aaaa-mm-dd');

        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
    }

    private function _SetearNull(&$datos): void {


        if (!isset($datos['IdEscuela']) || $datos['IdEscuela'] == "")
            $datos['IdEscuela'] = "NULL";

        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == "")
            $datos['IdTipoDocumento'] = "NULL";

        if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento'] == "")
            $datos['IdRegistroTipoDocumento'] = "NULL";

        if ((isset($datos['Vacante']) && $datos['Vacante']) && (!isset($datos['IdLicencia']) || $datos['IdLicencia'] == ""))
            $datos['IdLicencia'] = "NULL";

        if (!isset($datos['IdPersonaSaliente']) || $datos['IdPersonaSaliente'] == "")
            $datos['IdPersonaSaliente'] = "NULL";

        /*if (!isset($datos['IdPersonaPropuesta']) || $datos['IdPersonaPropuesta'] == "")
            $datos['IdPersonaPropuesta'] = "NULL";

        if (!isset($datos['IdPersonaDesignada']) || $datos['IdPersonaDesignada'] == "")
            $datos['IdPersonaDesignada'] = "NULL";*/

        if (!isset($datos['FechaDesde']) || $datos['FechaDesde'] == "")
            $datos['FechaDesde'] = "NULL";

        if (!isset($datos['FechaHasta']) || $datos['FechaHasta'] == "")
            $datos['FechaHasta'] = "NULL";

        if (!isset($datos['Observaciones']) || $datos['Observaciones'] == "")
            $datos['Observaciones'] = "NULL";

        if (!isset($datos['IdArea']) || $datos['IdArea'] == "")
            $datos['IdArea'] = "NULL";

        if (!isset($datos['IdAreaInicial']) || $datos['IdAreaInicial'] == "")
            $datos['IdAreaInicial'] = "NULL";

        if (!isset($datos['IdEstadoInicial']) || $datos['IdEstadoInicial'] == "")
            $datos['IdEstadoInicial'] = "NULL";

        if (!isset($datos['MovimientoFecha']) || $datos['MovimientoFecha'] == "")
            $datos['MovimientoFecha'] = "NULL";

        if (!isset($datos['FechaEnvio']) || $datos['FechaEnvio'] == "")
            $datos['FechaEnvio'] = "NULL";

        if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
            $datos['UltimaModificacionFecha'] = "NULL";

        if (!isset($datos['HashDato']) || $datos['HashDato'] == "")
            $datos['HashDato'] = "NULL";

        if (!isset($datos['EsAuxiliar']) || '' == $datos['EsAuxiliar'])
            $datos['EsAuxiliar'] = 0;

    }

    private function _ValidarDatosVacios($datos) {


        if (!isset($datos['IdEscuela']) || $datos['IdEscuela'] == "") {
            $this->setError(400, "Debe ingresar un escuela");
            return false;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdEscuela'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numerico para el campo Escuela.");
                return false;
            }
            if (strlen($datos['IdEscuela']) > 10) {
                $this->setError(400, "Error, el campo Escuela no puede ser mayor a 10 .");
                return false;
            }
        }

        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == "") {
            $this->setError(400, "Debe ingresar un tipo de documento");
            return false;
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdTipoDocumento'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo num&eacute;rico para el campo Tipo de documento.");
                return false;
            }
            if (strlen($datos['IdTipoDocumento']) > 11) {
                $this->setError(400, "Error, el campo Tipo de documento no puede ser mayor a 11 caracteres.");
                return false;
            }
        }

        if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento'] == "") {
            $this->setError(400, "Debe ingresar un tipo de documento vigente");
            return false;
        }

        if (isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdRegistroTipoDocumento'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numerico para el campo Tipo de documento.");
                return false;
            }
            if (strlen($datos['IdRegistroTipoDocumento']) > 11) {
                $this->setError(400, "Error, el campo Tipo de documento no puede ser mayor a 11 .");
                return false;
            }
        }

        if (isset($datos['IdPersonaSaliente']) && $datos['IdPersonaSaliente'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdPersonaSaliente'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numerico para el campo Persona saliente.");
                return false;
            }
            if (strlen($datos['IdPersonaSaliente']) > 11) {
                $this->setError(400, "Error, el campo Persona saliente no puede ser mayor a 11 .");
                return false;
            }
        }

        if (!isset($datos['FechaDesde']) || $datos['FechaDesde'] == "") {
            $this->setError(400, "Debe ingresar una fecha desde");
            return false;
        }

        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaDesde'], "FechaDDMMAAAA")) {
                $this->setError(400, "Error debe ingresar una fecha valida para el campo Fecha desde.");
                return false;
            }
        }

        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaHasta'], "FechaDDMMAAAA")) {
                $this->setError(400, "Error debe ingresar una fecha valida para el campo FechaHasta.");
                return false;
            }
        }

        if (isset($datos['MovimientoFecha']) && $datos['MovimientoFecha'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['MovimientoFecha'], "FechaAAAAMMDD")) {
                $this->setError(400, "Error debe ingresar una fecha valida para el campo MovimientoFecha.");
                return false;
            }
        }

        if (isset($datos['FechaEnvio']) && $datos['FechaEnvio'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaEnvio'], "FechaAAAAMMDD")) {
                $this->setError(400, "Error debe ingresar una fecha valida para el campo FechaEnvio.");
                return false;
            }
        }

        if (!$this->conexion->TraerCampo('Escuelas', 'IdEscuela', ['IdEscuela=' . $datos['IdEscuela']], $dato, $numfilas, $errno))
            return false;


        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un campo valido.");
            return false;
        }

        if (!$this->conexion->TraerCampo('DocumentosTipos', 'IdRegistro', ['IdRegistro=' . $datos['IdRegistroTipoDocumento']], $dato, $numfilas, $errno))
            return false;


        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un campo valido.");
            return false;
        }

        return true;
    }

    private function _armarDatosElastic(array $datos, ?array &$datosRegistro, ?stdClass &$datosElastic): bool {

        if (empty($datosRegistro)) {
            if (!$this->buscarParElastic($datos, $resultado, $numfilas))
                return false;

            if ($numfilas != 1) {
                $this->setError(400, "Debe ingresar codigo valido");
                return false;
            }

            $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        }


        $datosElastic = Elastic\SolicitudCobertura::armarDatosElastic($datosRegistro);

        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    private function _validarSolicitud(array $datos): bool {

        try {
            $desde = new DateTime(str_replace('/', '.', $datos['FechaDesde']));
            $hasta = new DateTime(str_replace('/', '.', $datos['FechaHasta']));
            $diff = $hasta->diff($desde);
        } catch (Exception $e) {
            $this->setError(500, $e->getMessage());
            return false;
        }
        $oPuestos = new cEscuelasPuestosPersonas($this->conexion, $this->conexionES, FMT_ARRAY);

        foreach ($datos['Puestos'] as $r) {

            $datosBuscar = [
                'IdPuesto' => $r['IdPuesto'],
                'IdPersona' => $datos['IdPersonaSaliente'],
            ];

            if (!$oPuestos->BuscarxIdPuestoxIdPersona($datosBuscar, $resultado_puesto, $numfilas_puesto)) {
                $this->setError($oPuestos->getError());
                return false;
            }

            if ($numfilas_puesto == 1) {

                $filaPuesto = $this->conexion->ObtenerSiguienteRegistro($resultado_puesto);

                # SI EL PUESTO NO ADMITE SUPLENTE, LO SALTEA
                if (!$filaPuesto['AdmiteSuplente'])
                    continue;

                $datosBuscar = [
                    'IdTipo' => $datos['IdTipoDocumento'],
                    'IdNivel' => $filaPuesto['IdNivel'],
                    'IdRevista' => $filaPuesto['IdRevista'],
                ];

                $oValidar = new cSolicitudesCoberturaTipoValidaciones($this->conexion, FMT_ARRAY);
                //Ver como trae el IdNivel esta funcion
                if (!$oValidar->BuscarxTipoNivel($datosBuscar, $resultado, $numfilas)) {
                    $this->setError($oValidar->getError());
                    return false;
                }

                if ($numfilas == 1) {
                    /*$this->setError(400, 'Error, no existe el registro');
                    return false;
                }*/
                    $filaValidacion = $this->conexion->ObtenerSiguienteRegistro($resultado);

                    if (!FuncionesPHPLocal::isEmpty($filaValidacion['RepeticionesMax']) && !FuncionesPHPLocal::isEmpty($datos['IdPersonaSaliente'])) {
                        try {
                            $desde_ = new DateTime(($filaValidacion['RepeticionesMax'] + 1) . ' days ago');
                        } catch (Exception $e) {
                            $this->setError(500, $e->getMessage());
                            return false;
                        }
                        $datosBuscar = [
                            'IdTipo' => $datos['IdTipoDocumento'],
                            'IdPersonaSaliente' => $datos['IdPersonaSaliente'],
                            'FechaDesde' => $desde_->format('Y-m-d'),
                            'IdPuesto' => $datos['IdPuesto'],
                        ];
                        if (!$this->buscarRepeticiones($datosBuscar, $resultado_repeticiones, $numfilas_repeticiones))
                            return false;

                        if ($numfilas_repeticiones >= $filaValidacion['RepeticionesMax']) {
                            $this->setError(400, "No puede haber m&aacute;s de {$filaValidacion['RepeticionesMax']} repeticiones consecutivas.");
                            return false;
                        }
                    }

                    if (1 == $filaValidacion['Habiles']) {
                        $oFeriados = new Feriados($this->conexion, FMT_ARRAY);
                        $datosBuscar = [
                            'Inicio' => $desde->format('Y-m-d'),
                            'Fin' => $hasta->format('Y-m-d'),
                            'Estado' => ACTIVO,
                        ];
                        if (!$oFeriados->buscarxRangoEstado($datosBuscar, $resultado_feriados, $numfilas_feriados)) {
                            $this->setError($oFeriados->getError());
                            return false;
                        }
                        $feriados = [];
                        while ($filaFeriados = $this->conexion->ObtenerSiguienteRegistro($resultado_feriados))
                            $feriados[] = $filaFeriados['Dia'];

                        $diff = (object)['days' => Feriados::contarDiasHabiles($desde, $hasta, $feriados)];
                    }

                    //Se convierte el valor de DuracionDesde a INT para la comparacion
                    $DuracionDesde = (int)($filaValidacion['DuracionDesde'] ?: 0);
                    if ($diff->days < ($DuracionDesde)) {
                        $this->setError(400, "La solicitud no puede ser por un periodo menor a {$filaValidacion['DuracionDesde']} d&iacute;as");
                        return false;
                    } elseif ($diff->days > ($filaValidacion['DuracionHasta'] ?: PHP_INT_MAX)) {
                        $this->setError(400, "La solicitud no puede ser por un periodo mayor a {$filaValidacion['DuracionHasta']} d&iacute;as");
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function _obtenerEstadosFinalesNegativos(): array
    {
        // Lista única de constantes de estados finalizados negativamente segun la tabla CircuitosEstados
        $constantes = [
            'NOV_ESTADO_ELIMINADO',
            'NOV_ESTADO_RECHAZADO_SUBDIREC',
            'NOV_ESTADO_NO_AUTORIZADO',
            'NOV_ESTADO_ANULADO',
            'NOV_ESTADO_DESESTIMADA_CONTRALOR',
            'NOV_ESTADO_DESESTIMADA_SAD',
            'NOV_ESTADO_DESIGNACION_NO_REALIZADA',
            'NOV_ESTADO_DENEGADO',
            'NOV_ESTADO_RECHAZADO',
            'NOV_ESTADO_NO_APROBADO',
            'NOV_ESTADO_DESESTIMADO',
            'NOV_ESTADO_SIN_DESIGNAR'
        ];

        $resultado = [];

        foreach ($constantes as $constante) {
            if (defined($constante)) {
                $resultado[] = constant($constante);
            }
        }

        return $resultado;
    }


    public function BuscarxIdSolicutudEnDocumento($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxIdSolicutudEnDocumento($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarxCodigoEnDocumento($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigoEnDocumento($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function AdjuntarDocumento($datos)
    {
        if (!$this->ValidarGuardarDocumento($datos)){
            return false;
        }

        return true;
    }



    /*
    public function GuardarDocumento($datos)
    {
        if (!parent::GuardarDocumento($datos))
            return false;

        return true;
    }
    */


    public function EliminarDocumento($datos)
    {
        if (!parent::EliminarDocumento($datos))
            return false;

        return true;
    }


    public function ValidarGuardarDocumento($datos)
    {
        if ($datos['Id'] <= 0 ) {
            return false;
        }

        if(!isset($datos['ArchivoNombre']) || empty($datos['ArchivoNombre'])) {
            return false;
        }

        $datosBuscar['Id'] = $datos['Id'];

        if (!$this->BuscarxCodigo($datosBuscar, $resultado, $numfilas))
            return false;

        $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);
        $archivosDatos = [];

        $archivosDatos['IdSolicitudCobertura']  = $datos['Id'];
        $archivosDatos['IdDocumentoAdjunto'] = 1; //En todos siempre esta este valor
        $archivosDatos['IdRegistroTipoDocumento'] = $fila['IdRegistroTipoDocumento'];
        $archivosDatos['ArchivoUbicacion']  = $datos['Id']."_".$datos['ArchivoUbicacion'];
        $archivosDatos['ArchivoNombre']  = $datos['ArchivoNombre'];
        $archivosDatos['ArchivoSize']  = $datos['ArchivoSize'];
        $archivosDatos['ArchivoHash']  = $datos['ArchivoHash'];

        if (!parent::GuardarDocumento($archivosDatos))
            return false;

        $archivosDatos['nombrearchivotmp'] = $datos['nombrearchivotmp'];

        if (!$this->oArchivosDocumentacion->InsertarArchivo($archivosDatos))
            return false;

        return true;
    }

}
