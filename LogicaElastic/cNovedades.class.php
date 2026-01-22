<?php


namespace Elastic;


use Bigtree\ExcepcionLogica;
use Elastic\Consultas\Agg;
use Elastic\Consultas\Query;
use Elastic\Consultas\Sort;
use Exception;
use FuncionesPHPLocal;
use ManejoErrores;
use stdClass;

class Novedades implements InterfaceBase {
    use ManejoErrores;

    /** @var string */
    private const INDEX = INDEXPREFIX . SUFFIX_NOVEDADES;
    /** @var Conexion */
    private $cnx;

    protected const CAMPO_REGION = 'Escuela.Region.Id';
    protected const CAMPO_NIVEL = 'Puestos.Nivel.Id';
    protected const CAMPO_TURNO = 'Puestos.Turno.Id';
    protected const CAMPO_ESCUELA = 'Escuela.Id';

    /**
     * Novedades constructor.
     *
     * @param Conexion $cnx
     */
    public function __construct(Conexion $cnx) {
        $this->cnx =& $cnx;
    }

    /**
     * @inheritDoc
     */
    public static function Configuracion(&$jsonData): void {

        if (empty($jsonData->settings))
            $jsonData->settings = new stdClass();

        if (empty($jsonData->settings->analysis))
            $jsonData->settings->analysis = new stdClass();

        if (empty($jsonData->settings->filter->analyzer))
            $jsonData->settings->analysis->filter = new stdClass();

        $jsonData->settings->analysis->filter->spanish_stop = new stdClass();
        $jsonData->settings->analysis->filter->spanish_stop->type = 'stop';
        $jsonData->settings->analysis->filter->spanish_stop->stopwords = '_spanish_';
        $jsonData->settings->analysis->filter->spanish_stemmer = new stdClass();
        $jsonData->settings->analysis->filter->spanish_stemmer->type = 'stemmer';
        $jsonData->settings->analysis->filter->spanish_stemmer->language = 'light_spanish';

        if (empty($jsonData->settings->analysis->analyzer))
            $jsonData->settings->analysis->analyzer = new stdClass();

        $jsonData->settings->analysis->analyzer->custom_es = new stdClass();
        $jsonData->settings->analysis->analyzer->custom_es->type = 'custom';
        $jsonData->settings->analysis->analyzer->custom_es->tokenizer = 'standard';
        $jsonData->settings->analysis->analyzer->custom_es->filter = ['lowercase', 'asciifolding', 'spanish_stop', 'spanish_stemmer'];


    }

    /**
     * @inheritDoc
     */
    public static function Estructura(bool $devolverJson = true) {
        $jsonData = new Tipos\Mapping();

        $jsonData->Id = new Tipos\EnteroLargo();

        $jsonData->IdEscuela = new Tipos\Entero();
        $jsonData->IdEscuelaDestino = new Tipos\Entero();
        $jsonData->IdDocumento = new Tipos\EnteroLargo();
        $jsonData->IdPuesto = new Tipos\EnteroLargo();
        $jsonData->IdSolicitudCobertura = new Tipos\EnteroLargo();
        $jsonData->IdDocumentoPadre = new Tipos\EnteroLargo();
        $jsonData->Tipo = new Tipos\Keyword();
        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Escuela                                      *
         *                                                                              *
         * **************************************************************************** */
        $jsonData->Escuela = new Tipos\Objeto();
        $jsonData->Escuela->Id = new Tipos\Entero();
        $jsonData->Escuela->Nombre = new Tipos\Texto('spanish');
        $jsonData->Escuela->Nombre->addField('raw', new Tipos\Keyword());
        $jsonData->Escuela->Region = new Tipos\Objeto();
        $jsonData->Escuela->Region->Id = new Tipos\Entero();
        $jsonData->Escuela->Region->Nombre = new Tipos\Texto('spanish');
        $jsonData->Escuela->Region->Nombre->addField('raw', new Tipos\Keyword());
        $jsonData->Escuela->Distrito = new Tipos\Objeto();
        $jsonData->Escuela->Distrito->Id = new Tipos\Entero();
        $jsonData->Escuela->Distrito->Nombre = new Tipos\Texto('spanish');
        $jsonData->Escuela->Distrito->Nombre->addField('raw', new Tipos\Keyword());

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Escuela Destino                              *
         *                                                                              *
         * **************************************************************************** */
        $jsonData->EscuelaDestino = new Tipos\Objeto();
        $jsonData->EscuelaDestino->Id = new Tipos\Entero();
        $jsonData->EscuelaDestino->Nombre = new Tipos\Texto('spanish');
        $jsonData->EscuelaDestino->Nombre->addField('raw', new Tipos\Keyword());
        $jsonData->EscuelaDestino->Region = new Tipos\Objeto();
        $jsonData->EscuelaDestino->Region->Id = new Tipos\Entero();
        $jsonData->EscuelaDestino->Region->Nombre = new Tipos\Texto('spanish');
        $jsonData->EscuelaDestino->Region->Nombre->addField('raw', new Tipos\Keyword());
        $jsonData->EscuelaDestino->Distrito = new Tipos\Objeto();
        $jsonData->EscuelaDestino->Distrito->Id = new Tipos\Entero();
        $jsonData->EscuelaDestino->Distrito->Nombre = new Tipos\Texto('spanish');
        $jsonData->EscuelaDestino->Distrito->Nombre->addField('raw', new Tipos\Keyword());

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Tipo Documento                               *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->TipoDocumento = new Tipos\Objeto();
        $jsonData->TipoDocumento->Id = new Tipos\Entero();
        $jsonData->TipoDocumento->IdRegistro = new Tipos\Entero();
        $jsonData->TipoDocumento->Nombre = new Tipos\Texto('spanish');
        $jsonData->TipoDocumento->Nombre->addField('raw', new Tipos\Keyword());
        $jsonData->TipoDocumento->NombreCorto = new Tipos\Texto('spanish');
        $jsonData->TipoDocumento->NombreCorto->addField('raw', new Tipos\Keyword());
        $jsonData->TipoDocumento->Categoria = new Tipos\Objeto();
        $jsonData->TipoDocumento->Categoria->Id = new Tipos\Entero();
        $jsonData->TipoDocumento->Categoria->Nombre = new Tipos\Texto('spanish');
        $jsonData->TipoDocumento->Categoria->Nombre->addField('raw', new Tipos\Keyword());
        $jsonData->TipoDocumento->Clasificacion = new Tipos\Objeto();
        $jsonData->TipoDocumento->Clasificacion->Id = new Tipos\Entero();
        $jsonData->TipoDocumento->Clasificacion->Nombre = new Tipos\Texto('spanish');
        $jsonData->TipoDocumento->Clasificacion->Nombre->addField('raw', new Tipos\Keyword());


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Agente                                       *
         *                                                                              *
         * **************************************************************************** */


        $jsonData->Agente = new Tipos\Objeto();
        $jsonData->Agente->Id = new Tipos\Entero();
        $jsonData->Agente->Cuil = new Tipos\Keyword();
        $jsonData->Agente->Dni = new Tipos\Keyword();
        $jsonData->Agente->Dni->addField('auto', new Tipos\Autocompletar('pattern'));
        $jsonData->Agente->TipoDocumento = new Tipos\Entero();
        $jsonData->Agente->TipoDocumentoNombre = new Tipos\Keyword();

        $jsonData->Agente->Nombre = new Tipos\Texto('spanish');
        $jsonData->Agente->Nombre->addField('raw', new Tipos\Keyword())
            ->addField('auto', new Tipos\Autocompletar('custom_es'));
        $jsonData->Agente->Apellido = new Tipos\Texto('spanish');
        $jsonData->Agente->Apellido->addField('raw', new Tipos\Keyword())
            ->addField('auto', new Tipos\Autocompletar('custom_es'));
        $jsonData->Agente->NombreCompleto = new Tipos\Texto('spanish');
        $jsonData->Agente->NombreCompleto->addField('raw', new Tipos\Keyword())
            ->addField('auto', new Tipos\Autocompletar('custom_es'));
        $jsonData->Agente->Sexo = new Tipos\Keyword();

        $jsonData->Agente->Email = new Tipos\Texto('spanish');
        $jsonData->Agente->Email->addField('raw', new Tipos\Keyword());

        $jsonData->Agente->Telefono = new Tipos\Texto('spanish');
        $jsonData->Agente->Telefono->addField('raw', new Tipos\Keyword());

        $jsonData->Agente->FechaFallecido = new Tipos\Fecha('strict_date||epoch_millis');

        $jsonData->Agente->FechaBaja = new Tipos\Fecha('strict_date||epoch_millis');


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Periodo                                      *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->Periodo = new Tipos\Objeto();
        $jsonData->Periodo->FechaDesde = new Tipos\Fecha('strict_date||epoch_millis');
        $jsonData->Periodo->FechaHasta = new Tipos\Fecha('strict_date||epoch_millis');

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Licencias                                    *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->Licencia = new Tipos\Objeto();
        $jsonData->Licencia->Id = new Tipos\Entero();
        $jsonData->Licencia->Tipo = new Tipos\Objeto();
        $jsonData->Licencia->Tipo->Id = new Tipos\Entero();
        $jsonData->Licencia->Tipo->Nombre = new Tipos\Keyword();
        $jsonData->Licencia->Nombre = new Tipos\Texto('spanish');

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Puestos                                      *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->Puestos = new Tipos\Nested();
        $jsonData->Puestos->Id = new Tipos\EnteroLargo();
        $jsonData->Puestos->IdPuestoRaiz = new Tipos\EnteroLargo();
        $jsonData->Puestos->IdPofa = new Tipos\EnteroLargo();
        $jsonData->Puestos->Codigo = new Tipos\Keyword();
        $jsonData->Puestos->Escuela = new Tipos\Objeto();
        $jsonData->Puestos->Escuela->Id = new Tipos\Entero();
        $jsonData->Puestos->NivelModalidad = new Tipos\Objeto();
        $jsonData->Puestos->NivelModalidad->Id = new Tipos\Entero();
        $jsonData->Puestos->Turno = new Tipos\Objeto();
        $jsonData->Puestos->Turno->Id = new Tipos\Entero();
        $jsonData->Puestos->Turno->Nombre = new Tipos\Keyword();
        $jsonData->Puestos->Turno->NombreCorto = new Tipos\Keyword();
        $jsonData->Puestos->Turno->IdTurno = new Tipos\Entero();


        $jsonData->Puestos->Nivel = new Tipos\Objeto();
        $jsonData->Puestos->Nivel->Id = new Tipos\Entero();

        $jsonData->Puestos->Cargo = new Tipos\Objeto();
        $jsonData->Puestos->Cargo->Id = new Tipos\Entero();
        $jsonData->Puestos->Grupo = new Tipos\Objeto();
        $jsonData->Puestos->Grupo->Id = new Tipos\Entero();
        $jsonData->Puestos->Materia = new Tipos\Objeto();
        $jsonData->Puestos->Materia->Id = new Tipos\Entero();
        $jsonData->Puestos->Materia->CantidadHoras = new Tipos\Entero();
        $jsonData->Puestos->Materia->CantidadModulos = new Tipos\Entero();
        $jsonData->Puestos->Revista = new Tipos\Objeto();
        $jsonData->Puestos->Revista->Id = new Tipos\Entero();
        $jsonData->Puestos->CodigoLiquidador = new Tipos\Keyword();


        $jsonData->Puestos->Materia->Codigo = new Tipos\Keyword();
        $jsonData->Puestos->Materia->Nombre = new Tipos\Keyword();
        $jsonData->Puestos->Cargo->Codigo = new Tipos\Keyword();
        $jsonData->Puestos->Cargo->Nombre = new Tipos\Keyword();


        $jsonData->Puestos->Estado = new Tipos\Objeto();
        $jsonData->Puestos->Estado->Id = new Tipos\Entero();
        $jsonData->Puestos->Estado->Nombre = new Tipos\Keyword();

        $jsonData->Puestos->Seccion = new Tipos\Objeto();
        $jsonData->Puestos->Seccion->Id = new Tipos\Entero();
        $jsonData->Puestos->Seccion->Nombre = new Tipos\Keyword();
        $jsonData->Puestos->Grado = new Tipos\Objeto();
        $jsonData->Puestos->Grado->Id = new Tipos\Entero();
        $jsonData->Puestos->Grado->Nombre = new Tipos\Keyword();
        $jsonData->Puestos->Grado->NombreCorto = new Tipos\Keyword();

        $jsonData->Puestos->Fechas = new Tipos\Objeto();
        $jsonData->Puestos->Fechas->Entre = new Tipos\RangoFecha('strict_date||epoch_millis');
        $jsonData->Puestos->Fechas->Designacion = new Tipos\Fecha('strict_date||epoch_millis');
        $jsonData->Puestos->Fechas->TomaPosesion = new Tipos\Fecha('strict_date||epoch_millis');


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque FechaDesignacion                             *
         *                                                                              *
         * **************************************************************************** */

        // $jsonData->FechaDesignacion = new Tipos\Fecha();

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque FechaTomaPosesion                            *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->FechaTomaPosesion = new Tipos\Fecha();

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque FechaDesde                                   *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->FechaDesde = new Tipos\Fecha();

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque FechaDesignacion                             *
         *                                                                              *
        * **************************************************************************** */

        $jsonData->FechaDesignacion = new Tipos\Fecha();

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque MovimientoFecha                              *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->MovimientoFecha = new Tipos\Fecha();


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque FechaEnvio                                   *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->FechaEnvio = new Tipos\Fecha();

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Observaciones                                *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->Observaciones = new Tipos\Texto('spanish');


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Estado                                       *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->Estado = new Tipos\Objeto();
        $jsonData->Estado->Id = new Tipos\Entero();
        $jsonData->Estado->Nombre = new Tipos\Texto('spanish');
        $jsonData->Estado->Nombre->addField('raw', new Tipos\Keyword());

        /* **************************************************************************** *
         *                                                                              *
         *                            Bloque Area                                       *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->Area = new Tipos\Objeto();
        $jsonData->Area->Id = new Tipos\Entero();
        $jsonData->Area->Nombre = new Tipos\Texto('spanish');
        $jsonData->Area->Nombre->addField('raw', new Tipos\Keyword());


        /* **************************************************************************** *
         *                                                                              *
         *                            Bloque Nro. Resolución                            *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->NroResolucion = new Tipos\Keyword();


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Alta  Modificacion                           *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->Alta = new Tipos\Objeto();
        $jsonData->Alta->Fecha = new Tipos\Fecha();
        $jsonData->Alta->Usuario = new Tipos\Objeto();
        $jsonData->Alta->Usuario->Id = new Tipos\Entero();
        $jsonData->Alta->Usuario->Nombre = new Tipos\Keyword();;
        $jsonData->Alta->Escuela = new Tipos\Objeto();
        $jsonData->Alta->Escuela->Id = new Tipos\Entero();
        $jsonData->Alta->Escuela->Nombre = new Tipos\Keyword();
        $jsonData->Alta->Rol = new Tipos\Objeto();
        $jsonData->Alta->Rol->Id = new Tipos\Entero();
        $jsonData->Alta->Rol->Nombre = new Tipos\Keyword();


        $jsonData->UltimaModificacion = new Tipos\Objeto();
        $jsonData->UltimaModificacion->Fecha = new Tipos\Fecha();
        $jsonData->UltimaModificacion->Usuario = new Tipos\Objeto();
        $jsonData->UltimaModificacion->Usuario->Id = new Tipos\Entero();
        $jsonData->UltimaModificacion->Usuario->Nombre = new Tipos\Keyword();;
        $jsonData->UltimaModificacion->Escuela = new Tipos\Objeto();
        $jsonData->UltimaModificacion->Escuela->Id = new Tipos\Entero();
        $jsonData->UltimaModificacion->Escuela->Nombre = new Tipos\Keyword();
        $jsonData->UltimaModificacion->Rol = new Tipos\Objeto();
        $jsonData->UltimaModificacion->Rol->Id = new Tipos\Entero();
        $jsonData->UltimaModificacion->Rol->Nombre = new Tipos\Keyword();

        $jsonData->Firma = new Tipos\Binario();


        return $devolverJson ? json_encode($jsonData) : $jsonData;

    }

    /**
     * @inheritDoc
     */
    public static function armarDatosElastic(array $datos, $encode = false) {
        $jsonData = new stdClass();

        $jsonData->Id = $datos['IdDocumento'];

        $jsonData->Tipo = 'Documento';

        $jsonData->IdEscuela = $datos['IdEscuela'] ?? null;

        $jsonData->IdEscuelaDestino = $datos['IdEscuelaDestino'] ?? null;

        $jsonData->IdDocumento = $datos['IdDocumento'] ?? null;

        $jsonData->IdDocumentoPadre = $datos['IdDocumentoPadre'] ?? null;


        $jsonData->IdPuesto = $datos['IdPuesto'] ?? null;

        $jsonData->IdSolicitudCobertura = $datos['IdSolicitudCobertura'] ?? null;



        /* **************************************************************************** *
      *                                                                              *
      *                          Bloque Escuela                                       *
      *                                                                              *
      * **************************************************************************** */
        if (!FuncionesPHPLocal::isEmpty($datos['IdEscuela'])) {
            $jsonData->Escuela = new stdClass();
            $jsonData->Escuela->Id = $datos['IdEscuela'];
            $jsonData->Escuela->Nombre = $datos['EscuelaNombre'];

            if (!FuncionesPHPLocal::isEmpty($datos['IdRegionEscuela'])) {
                $jsonData->Escuela->Region = new stdClass();
                $jsonData->Escuela->Region->Id = $datos['IdRegionEscuela'];
                $jsonData->Escuela->Region->Nombre = $datos['NombreRegionEscuela'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['IdDistritoEscuela'])) {
                $jsonData->Escuela->Distrito = new stdClass();
                $jsonData->Escuela->Distrito->Id = $datos['IdDistritoEscuela'];
                $jsonData->Escuela->Distrito->Nombre = $datos['NombreDistritoEscuela'];
            }
        }

        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque Escuela Destino                                     *
        *                                                                              *
        * **************************************************************************** */

        if (!FuncionesPHPLocal::isEmpty($datos['IdEscuelaDestino'])) {
            $jsonData->EscuelaDestino = new stdClass();
            $jsonData->EscuelaDestino->Id = $datos['IdEscuelaDestino'];
            $jsonData->EscuelaDestino->Nombre = $datos['EscuelaDestinoNombre'];

            if (!FuncionesPHPLocal::isEmpty($datos['IdRegionEscuelaDestino'])) {
                $jsonData->EscuelaDestino->Region = new stdClass();
                $jsonData->EscuelaDestino->Region->Id = $datos['IdRegionEscuelaDestino'];
                $jsonData->EscuelaDestino->Region->Nombre = $datos['NombreRegionEscuelaDestino'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['IdDistritoEscuela'])) {
                $jsonData->EscuelaDestino->Distrito = new stdClass();
                $jsonData->EscuelaDestino->Distrito->Id = $datos['IdDistritoEscuelaDestino'];
                $jsonData->EscuelaDestino->Distrito->Nombre = $datos['NombreDistritoEscuelaDestino'];
            }
        }

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Tipo Documento                                       *
         *                                                                              *
         * **************************************************************************** */


        if (!empty($datos['IdTipoDocumento'])) {
            $jsonData->TipoDocumento = new stdClass();
            $jsonData->TipoDocumento->Id = $datos['IdTipoDocumento'];
            $jsonData->TipoDocumento->IdRegistro = $datos['IdRegistroTipoDocumento'];
            $jsonData->TipoDocumento->Nombre = $datos['NombreTipoDocumento'];
            $jsonData->TipoDocumento->NombreCorto = $datos['NombreCortoTipoDocumento'];

            if (!empty($datos['IdCategoria'])) {
                $jsonData->TipoDocumento->Categoria = new stdClass();
                $jsonData->TipoDocumento->Categoria->Id = $datos['IdCategoria'];
                $jsonData->TipoDocumento->Categoria->Nombre = $datos['CategoriaNombre'];
            }
            if (!empty($datos['IdClasificacion'])) {
                $jsonData->TipoDocumento->Clasificacion = new stdClass();
                $jsonData->TipoDocumento->Clasificacion->Id = $datos['IdClasificacion'];
                $jsonData->TipoDocumento->Clasificacion->Nombre = $datos['ClasificacionNombre'];
            }

        }

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Agente                                      *
         *                                                                              *
         * **************************************************************************** */


        if (!empty($datos['IdPersona'])) {
            $jsonData->Agente = new stdClass();
            $jsonData->Agente->Id = $datos['IdPersona'] ?? null;
            if (!empty($datos['CuilPersona']))
                $jsonData->Agente->Cuil = $datos['CuilPersona'];
            if (!empty($datos['DniPersona']))
                $jsonData->Agente->Dni = $datos['DniPersona'];
            if (!empty($datos['SexoPersona']))
                $jsonData->Agente->Sexo = $datos['SexoPersona'];
            if (!empty($datos['NombrePersona']))
                $jsonData->Agente->Nombre = $datos['NombrePersona'];
            if (!empty($datos['ApellidoPersona']))
                $jsonData->Agente->Apellido = $datos['ApellidoPersona'];
            if (!empty($datos['NombreCompletoPersona']))
                $jsonData->Agente->NombreCompleto = $datos['NombreCompletoPersona'];
            if (!empty($datos['EmailPersona']))
                $jsonData->Agente->Email = $datos['EmailPersona'];
            if (!empty($datos['TelefonoPersona']))
                $jsonData->Agente->Telefono = $datos['TelefonoPersona'];
            if (!empty($datos['FallecidoFechaPersona']))
                $jsonData->Agente->FechaFallecido = $datos['FallecidoFechaPersona'];
            if (!empty($datos['BajaFechaPersona']))
                $jsonData->Agente->FechaBaja = $datos['BajaFechaPersona'];
            if (!empty($datos['NombreTipoDocumentoPersona']))
                $jsonData->Agente->TipoDocumentoNombre = $datos['NombreTipoDocumentoPersona'];


        }

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Periodo                               *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->Periodo = new stdClass();
       if (!empty($datos['PeriodoFechaDesde']) || !empty($datos['PeriodoFechaHasta'])) {
            if (!empty($datos['PeriodoFechaDesde']) && $datos['PeriodoFechaDesde'] != '0000-00-00')
                $jsonData->Periodo->FechaDesde = $datos['PeriodoFechaDesde'];
            if (!empty($datos['PeriodoFechaHasta'])  && $datos['PeriodoFechaDesde'] != '0000-00-00')
                $jsonData->Periodo->FechaHasta = $datos['PeriodoFechaHasta'];
        } else {
           $jsonData->Periodo->FechaHasta = $datos['PeriodoFechaHasta'];
       }



        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque Licencia                              *
        *                                                                              *
        * **************************************************************************** */


        $jsonData->Licencia = new stdClass();
        $jsonData->Licencia->Id = $datos['IdLicencia'] ?? null;

        if (!empty($datos['IdLicencia'])) {
            $jsonData->Licencia->Tipo = new stdClass();
            $jsonData->Licencia->Tipo->Id = $datos['IdTipoLicencia'];
            $jsonData->Licencia->Tipo->Nombre = $datos['NombreTipoLicencia'];
        }


        /* **************************************************************************** *
       *                                                                              *
       *                          Bloque Puestos                                      *
       *                                                                              *
       * **************************************************************************** */

        if (!FuncionesPHPLocal::isEmpty($datos['Puestos']) && is_array($datos['Puestos'])) {

            $jsonData->Puestos = [];
            foreach ($datos['Puestos'] as $ii => $DataPuesto) {

                $jsonData->Puestos[$ii] = new stdClass();
                $jsonData->Puestos[$ii]->Id = FuncionesPHPLocal::isEmpty($DataPuesto['IdPuesto']) ? null : (int)$DataPuesto['IdPuesto'];
                $jsonData->Puestos[$ii]->IdPuestoRaiz = FuncionesPHPLocal::isEmpty($DataPuesto['IdPuestoRaiz']) ? null : (int)$DataPuesto['IdPuestoRaiz'];
                $jsonData->Puestos[$ii]->IdPofa = FuncionesPHPLocal::isEmpty($DataPuesto['IdPofa']) ? null : (int)$DataPuesto['IdPofa'];
                $jsonData->Puestos[$ii]->Codigo = $DataPuesto['CodigoPuesto'] ?? null;
                $jsonData->Puestos[$ii]->Escuela = new stdClass();
                $jsonData->Puestos[$ii]->Escuela->Id = FuncionesPHPLocal::isEmpty($DataPuesto['IdEscuela']) ? null : (int)$DataPuesto['IdEscuela'];
                $jsonData->Puestos[$ii]->NivelModalidad = new stdClass();
                $jsonData->Puestos[$ii]->NivelModalidad->Id = FuncionesPHPLocal::isEmpty($DataPuesto['IdNivelModalidad']) ? null : (int)$DataPuesto['IdNivelModalidad'];

                $jsonData->Puestos[$ii]->Nivel = new stdClass();
                $jsonData->Puestos[$ii]->Nivel->Id = FuncionesPHPLocal::isEmpty($DataPuesto['IdNivel']) ? null : (int)$DataPuesto['IdNivel'];

                $jsonData->Puestos[$ii]->Turno = new stdClass();
                $jsonData->Puestos[$ii]->Turno->Id = FuncionesPHPLocal::isEmpty($DataPuesto['IdEscuelaTurno']) ? null : (int)$DataPuesto['IdEscuelaTurno'];
                $jsonData->Puestos[$ii]->Turno->Nombre = $DataPuesto['NombreTurno'] ?? null;
                $jsonData->Puestos[$ii]->Turno->NombreCorto = $DataPuesto['Turno'] ?? null;
                $jsonData->Puestos[$ii]->Turno->IdTurno = $DataPuesto['IdTurno'] ?? null;

                $jsonData->Puestos[$ii]->Cargo = new stdClass();
                $jsonData->Puestos[$ii]->Cargo->Id = FuncionesPHPLocal::isEmpty($DataPuesto['IdCargo']) ? null : (int)$DataPuesto['IdCargo'];
                $jsonData->Puestos[$ii]->Grupo = new stdClass();
                $jsonData->Puestos[$ii]->Grupo->Id = FuncionesPHPLocal::isEmpty($DataPuesto['IdGrupo']) ? null : (int)$DataPuesto['IdGrupo'];
                $jsonData->Puestos[$ii]->Materia = new stdClass();
                $jsonData->Puestos[$ii]->Materia->Id = FuncionesPHPLocal::isEmpty($DataPuesto['IdMateria']) ? null : (int)$DataPuesto['IdMateria'];
                $jsonData->Puestos[$ii]->Materia->CantidadHoras = FuncionesPHPLocal::isEmpty($DataPuesto['CantHoras']) ? null : (int)$DataPuesto['CantHoras'];
                $jsonData->Puestos[$ii]->Materia->CantidadModulos = FuncionesPHPLocal::isEmpty($DataPuesto['CantModulos']) ? null : (int)$DataPuesto['CantModulos'];
                $jsonData->Puestos[$ii]->Revista = new stdClass();
                $jsonData->Puestos[$ii]->Revista->Id = FuncionesPHPLocal::isEmpty($DataPuesto['IdRevista']) ? null : (int)$DataPuesto['IdRevista'];
                $jsonData->Puestos[$ii]->CodigoLiquidador = $DataPuesto['CodigoLiquidador'] ?? null;


                $jsonData->Puestos[$ii]->Materia->Codigo = $DataPuesto['CodigoMateria'] ?? null;
                $jsonData->Puestos[$ii]->Materia->Nombre = $DataPuesto['NombreMateria'] ?? null;
                $jsonData->Puestos[$ii]->Cargo->Codigo = $DataPuesto['CodigoCargo'] ?? null;
                $jsonData->Puestos[$ii]->Cargo->Nombre = $DataPuesto['NombreCargo'] ?? null;


                $jsonData->Puestos[$ii]->Estado = new stdClass();
                $jsonData->Puestos[$ii]->Estado->Id = FuncionesPHPLocal::isEmpty($DataPuesto['IdEstado']) ? null : (int)$DataPuesto['IdEstado'];
                $jsonData->Puestos[$ii]->Estado->Nombre = $DataPuesto['NombreEstado'] ?? null;

                $jsonData->Puestos[$ii]->Seccion = new stdClass();
                $jsonData->Puestos[$ii]->Seccion->Id = FuncionesPHPLocal::isEmpty($DataPuesto['IdSeccion']) ? null : (int)$DataPuesto['IdSeccion'];
                $jsonData->Puestos[$ii]->Seccion->Nombre = $DataPuesto['Seccion'] ?? null;
                $jsonData->Puestos[$ii]->Grado = new stdClass();
                $jsonData->Puestos[$ii]->Grado->Id = null; //TODO: Agregar IdGrado a la consulta
                $jsonData->Puestos[$ii]->Grado->Nombre = $DataPuesto['Grado'] ?? null;
                $jsonData->Puestos[$ii]->Grado->NombreCorto = $DataPuesto['GradoNombreCorto'] ?? null;

                $jsonData->Puestos[$ii]->Fechas = new stdClass();
                $jsonData->Puestos[$ii]->Fechas->Entre = new stdClass();
                $jsonData->Puestos[$ii]->Fechas->Entre->gte = $DataPuesto['FechaDesde'] ?? null;
                $jsonData->Puestos[$ii]->Fechas->Entre->lte = $DataPuesto['FechaHasta'] ?? null;
                $jsonData->Puestos[$ii]->Fechas->Designacion = empty($DataPuesto['FechaDesignacion']) || $DataPuesto['FechaDesignacion'] === '0000-00-00' ? null : $DataPuesto['FechaDesignacion'];
               // $jsonData->Puestos[$ii]->Fechas->TomaPosesion = empty($DataPuesto['FechaTomaPosesion']) ? null : substr($DataPuesto['FechaTomaPosesion'], 0, 10);
                $jsonData->Puestos[$ii]->Fechas->TomaPosesion =
                    empty($DataPuesto['FechaTomaPosesion']) || $DataPuesto['FechaTomaPosesion'] === '0000-00-00 00:00:00'
                        ? null
                        : substr($DataPuesto['FechaTomaPosesion'], 0, 10);

            }
        }


        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque Observaciones                                      *
        *                                                                              *
        * **************************************************************************** */


        if (isset($datos['Observaciones'])) {
            $jsonData->Observaciones = new stdClass();
            $jsonData->Observaciones = $datos['Observaciones'];
        }


        if (!empty($datos['IdArea'])) {
            $jsonData->Area = new stdClass();
            $jsonData->Area->Id = $datos['IdArea'];

            if (!empty($datos['NombreArea']))
                $jsonData->Area->Nombre = $datos['NombreArea'];
        }

        if (!empty($datos['IdEstado'])) {
            $jsonData->Estado = new stdClass();
            $jsonData->Estado->Id = $datos['IdEstado'];
            $jsonData->Estado->Nombre = $datos['NombreEstado'];
        }

      /*  if (!empty($datos['FechaTomaPosesion'])) {
            $jsonData->FechaTomaPosesion = new stdClass();
            $jsonData->FechaTomaPosesion = $datos['FechaTomaPosesion'];
        }*/
        if ( !empty($datos['FechaTomaPosesion'] )&& $datos['FechaTomaPosesion'] != '0000-00-00 00:00:00' ) {
            $jsonData->FechaTomaPosesion = $datos['FechaTomaPosesion'];
        }


        if (!empty($datos['FechaDesde'])) {
            $jsonData->FechaDesde = new stdClass();
            $jsonData->FechaDesde = $datos['FechaDesde'];
        }

        if (!empty($datos['FechaDesignacion'])&& $datos['FechaDesignacion'] != '0000-00-00' ) {
            $jsonData->FechaDesignacion = new stdClass();
            $jsonData->FechaDesignacion = $datos['FechaDesignacion'];
        }

        if (!empty($datos['MovimientoFecha'])) {
            $jsonData->MovimientoFecha = new stdClass();
            $jsonData->MovimientoFecha = $datos['MovimientoFecha'];
        }

        if (!empty($datos['FechaEnvio'])) {
            $jsonData->FechaEnvio = new stdClass();
            $jsonData->FechaEnvio = $datos['FechaEnvio'];
        }

        if (!empty($datos['FechaDesde'])) {
            $jsonData->FechaDesde = new stdClass();
            $jsonData->FechaDesde = $datos['FechaDesde'];
        }

        if (!empty($datos['NroResolucion'])) {
            $jsonData->NroResolucion = new stdClass();
            $jsonData->NroResolucion = $datos['NroResolucion'];
        }

        if (!empty($datos['Firma'])) {
            $jsonData->Firma = new stdClass();
            $jsonData->Firma = $datos['Firma'];
        }

        if (!empty($datos['AltaFecha'])) {
            $jsonData->Alta = new stdClass();
            $jsonData->Alta->Fecha = $datos['AltaFecha'];

            if (!empty($datos['AltaUsuario'])) {
                $jsonData->Alta->Usuario = new stdClass();
                $jsonData->Alta->Usuario->Id = $datos['AltaUsuario'];
                $jsonData->Alta->Usuario->Nombre = $datos['NombreAltaUsuario'];
            }

            if (!empty($datos['AltaEscuela'])) {
                $jsonData->Alta->Escuela = new stdClass();
                $jsonData->Alta->Escuela->Id = $datos['AltaEscuela'];
                $jsonData->Alta->Escuela->Nombre = $datos['NombreAltaEscuela'];
            }

            if (!empty($datos['AltaRol'])) {
                $jsonData->Alta->Rol = new stdClass();
                $jsonData->Alta->Rol->Id = $datos['AltaRol'];
                $jsonData->Alta->Rol->Nombre = $datos['NombreAltaRol'];
            }
        }

        if (!empty($datos['UltimaModificacionFecha'])) {
            $jsonData->UltimaModificacion = new stdClass();
            $jsonData->UltimaModificacion->Fecha = $datos['UltimaModificacionFecha'];

            if (!empty($datos['UltimaModificacionUsuario'])) {
                $jsonData->UltimaModificacion->Usuario = new stdClass();
                $jsonData->UltimaModificacion->Usuario->Id = $datos['UltimaModificacionUsuario'];
                $jsonData->UltimaModificacion->Usuario->Nombre = $datos['NombreUltimaModificacionUsuario'];
            }

            if (!empty($datos['UltimaModificacionEscuela'])) {
                $jsonData->UltimaModificacion->Escuela = new stdClass();
                $jsonData->UltimaModificacion->Escuela->Id = $datos['UltimaModificacionEscuela'];
                $jsonData->UltimaModificacion->Escuela->Nombre = $datos['NombreUltimaModificacionEscuela'];
            }

            if (!empty($datos['UltimaModificacionRol'])) {
                $jsonData->UltimaModificacion->Rol = new stdClass();
                $jsonData->UltimaModificacion->Rol->Id = $datos['UltimaModificacionRol'];
                $jsonData->UltimaModificacion->Rol->Nombre = $datos['NombreUltimaModificacionRol'];
            }
        }


        $jsonData = FuncionesPHPLocal::ConvertiraUtf8($jsonData);

        return $encode ? json_encode($jsonData) : $jsonData;

    }

    /**
     * @inheritDoc
     */
    public static function getIndex(): string {
        return self::INDEX;
    }

    /**
     * @param array $datos
     *
     * @return array
     */
    private static function detectarSizeFrom(array $datos): array {
        $size = !isset($datos['size']) || !is_numeric($datos['size']) || $datos['size'] < 0 ? 20 : (int)$datos['size'];
        $from = !isset($datos['from']) || !is_numeric($datos['from']) || $datos['from'] < 0 ? 0 : (int)$datos['from'];
        return array($size, $from);
    }

    /**
     * Novedades destructor
     */
    public function __destruct() {
        $this->error = [];
    }

    /**
     * @inheritDoc
     */
    public static function obtenerId($datos): ?int {
        if (is_array($datos) && isset($datos['IdDocumento']))
            return (int)$datos['IdDocumento'];
        if (is_object($datos) && isset($datos->Id))
            return (int)$datos->Id;
        return null;
    }


    /**
     * @param $datos
     *
     * @return bool
     */
    public function Eliminar($datos): bool {

        $id = $datos['id'];
        if (!$this->cnx->sendDelete('dev-rh-novedades', '_doc', $data, $codigoRetorno, $id))
            return false;

        if (!isset($data['acknowledged']) || $data['acknowledged'] === false) {
            $this->setError('400', Funciones::DevolverError($data));
            return false;
        }

        return true;
    }

    /**
     * @param array      $datos
     * @param array|null $dataResult
     *
     * @return bool
     */
    public function autoCompletarNombre(array $datos, ?array &$dataResult): bool {
        //$datos['Nombre'] = preg_replace(self::PATTERN, self::REPLACEMENT, utf8_decode($datos['Nombre']));
        $nombres = [];
        $jsonData = new stdClass();
        $jsonData->size = 0;
        //$jsonData->_source = array("Nombre", "Apellido", "Documento.*");
        $jsonData->query = new stdClass();
        $jsonData->query->bool = new stdClass();
        $jsonData->query->bool->must = [];
        $mm = -1;
        $must = [];
        $jsonData->query->bool->filter = [];
        $ff = -1;
        $filter = [];

        $must[++$mm] = new stdClass();
        $must[$mm]->multi_match = new stdClass();
        $must[$mm]->multi_match->query = $datos['Nombre'];
        $must[$mm]->multi_match->type = 'bool_prefix';
        $must[$mm]->multi_match->fields = [
            'Agente.Nombre.prefix',
            'Agente.Nombre.prefix._2gram',
            'Agente.Nombre.prefix._3gram',
            'Agente.Apellido.prefix',
            'Agente.Apellido.prefix._2gram',
            'Agente.Apellido.prefix._3gram',
            'Agente.Documento.Numero.prefix',
            'Agente.Documento.Numero.prefix._2gram',
            'Agente.Documento.Numero.prefix._3gram'
        ];
        $filter[++$ff] = new stdClass();
        $filter[$ff]->term = new stdClass();
        $filter[$ff]->term->Tipo = new stdClass();
        $filter[$ff]->term->Tipo->value = 'Persona';

        if (!FuncionesPHPLocal::isEmpty($_SESSION['IdEscuela'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->has_parent = new stdClass();
            $filter[$ff]->has_parent->parent_type = 'Puesto';
            $filter[$ff]->has_parent->query = new stdClass();
            $filter[$ff]->has_parent->query->term = new stdClass();
            $filter[$ff]->has_parent->query->term->{'Escuela.Id'} = new stdClass();
            $filter[$ff]->has_parent->query->term->{'Escuela.Id'}->value = (int)$_SESSION['IdEscuela'];
        }

        if (!FuncionesPHPLocal::isEmpty($filter))
            $jsonData->query->bool->filter = $filter;

        if (!FuncionesPHPLocal::isEmpty($must))
            $jsonData->query->bool->must = $must;


        $cuerpo = json_encode($jsonData);
        //$this->cnx->setDebug(true);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado, $codigoRetorno)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($resultado['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado));
            return false;
        }

        if ($resultado['hits']['total']['value'] < 1) {
            $this->setError(404, 'No se encuentra');
            return false;
        }

        $dataResult = array_map(function ($item) {
            return $item['Datos']['hits']['hits'][0]['_source'];
        }, $resultado['aggregations']['Personas']['buckets']);

        return true;
    }

    /**
     *  Busca las novedades
     *
     * @param array       $datos
     * @param array|null  $resultado
     * @param int|null    $numfilas
     * @param int|null    $total
     * @param string|null $scroll_id
     *
     * @return bool
     */
    public function BuscarDocumentosTodos(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total, ?string &$scroll_id): bool {

        $SortField = 'Id';
        $SortOrder = 'desc';

        list($size, $from) = self::detectarSizeFrom($datos);

        $bool = Query::bool();

        $i = 0;

        $scroll = "";
        if (!empty($datos['scroll']) && preg_match("/\d+[dhms]/", $datos['scroll'])) {
            $scroll = "&scroll={$datos['scroll']}";
            unset($datos['scroll']);
        }


        if (isset($datos['IdCategoria']) && $datos['IdCategoria'] != "")
            $bool->addFilter(Query::term('TipoDocumento.Categoria.Id', $datos['IdCategoria']));

        /*
        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != "")
            $bool->addFilter(Query::term('Licencia.Id', (int)$datos['IdLicencia']));
        */

        if (isset($datos['IdDocumento']) && $datos['IdDocumento'] != "")
            $bool->addFilter(Query::term('IdDocumento', (int)$datos['IdDocumento']));

        if (isset($datos['IdClasificacion']) && $datos['IdClasificacion'] != "")
            $bool->addFilter(Query::term('TipoDocumento.Clasificacion.Id', $datos['IdClasificacion']));

        if (isset($datos['TipoDocumento']) && is_array($datos['TipoDocumento']) && count($datos['TipoDocumento']) > 0)
            $bool->addFilter(Query::terms('TipoDocumento.Id', $datos['TipoDocumento']));

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $escuelas = is_array($datos['IdEscuela']) ? $datos['IdEscuela'] : explode(',', $datos['IdEscuela']);
            $bool->addFilter(Query::bool()
                ->addShould(Query::terms('Escuela.Id', $escuelas))
                ->addShould(Query::terms('EscuelaDestino.Id', $escuelas))
            );
        }

        //if(isset($datos['IdEstado']) && $datos['IdEstado']!="")
        if (!FuncionesPHPLocal::isEmpty($datos['IdEstado'])) {
            $consulta = is_array($datos['IdEstado']) ? 'terms' : 'term';
            $bool->addFilter(Query::{$consulta}('Estado.Id', $datos['IdEstado']));
        }

        if (isset($datos['IdArea']) && $datos['IdArea'] != "")
            $bool->addFilter(Query::terms('Area.Id', is_array($datos['IdArea']) ? $datos['IdArea'] : explode(',', $datos['IdArea'])));


        if (isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre'] != "")
            $bool->addFilter(Query::term('IdDocumentoPadre', (int)$datos['IdDocumentoPadre']));

        $Campo = "UltimaModificacion.Fecha";
        if (isset($datos['Enviados']) && $datos['Enviados'] != "") {

            switch ($datos['Enviados']) {
                case 1:
                    $bool->addMustNot(Query::exists($Campo));
                    break;
                case 2:
                    $mesAnterior = date("01/m/Y", strtotime("-1 month"));
                    $mesActual = date("01/m/Y");
                    try {
                        $rango = Query::range($Campo, ['gte' => $mesAnterior, 'lt' => $mesActual])->setFormat('dd/MM/yyyy');
                    } catch (ExcepcionLogica $e) {
                        $this->setError($e->getError());
                        return false;
                    }
                    break;
                case 3:
                    $anioActual = date("Y");
                    $anioSiguiente = date("Y", strtotime("+1 year"));
                    try {
                        $rango = Query::range($Campo, ['gte' => $anioActual, 'lt' => $anioSiguiente])->setFormat('yyyy');
                    } catch (ExcepcionLogica $e) {
                        $this->setError($e->getError());
                        return false;
                    }
                    break;
                case 4:
                    $anioAnterior = date("Y", strtotime("-1 year"));
                    $anioActual = date("Y");
                    try {
                        $rango = Query::range($Campo, ['gte' => $anioAnterior, 'lt' => $anioActual])->setFormat('yyyy');
                    } catch (ExcepcionLogica $e) {
                        $this->setError($e->getError());
                        return false;
                    }
                    break;
                case 5:
                    $mesSiguiente = date("01/m/Y", strtotime("+1 month"));
                    $mesActual = date("01/m/Y");
                    try {
                        $rango = Query::range($Campo, ['gte' => $mesActual, 'lt' => $mesSiguiente])->setFormat('dd/MM/yyyy');
                    } catch (ExcepcionLogica $e) {
                        $this->setError($e->getError());
                        return false;
                    }
                    break;
            }

            if (isset($rango))
                $bool->addFilter($rango);
        }


        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            try {
                $bool->addFilter(Query::range($Campo, ['gte' => $datos['FechaDesde'] . ' 00:00:00'])->setFormat('dd/MM/yyyy HH:mm:ss'));
            } catch (ExcepcionLogica $e) {
                $this->setError($e->getError());
                return false;
            }
        }

        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            try {
                $bool->addFilter(Query::range($Campo, ['lt' => $datos['FechaHasta'] . ' 23:59:59'])->setFormat('dd/MM/yyyy HH:mm:ss'));
            } catch (ExcepcionLogica $e) {
                $this->setError($e->getError());
                return false;
            }

        } else {
            try {
                $bool->addFilter(Query::range($Campo, ['lte' => date('d/m/Y H:i:s')])->setFormat('dd/MM/yyyy HH:mm:ss'));
            } catch (ExcepcionLogica $e) {
                $this->setError($e->getError());
                return false;
            }
        }


        if (isset($datos['AgenteNombre']) && $datos['AgenteNombre'] != "") {
            $NombreCampo = "Agente.Nombre";
            $pattern = array(utf8_decode("/[ÁáÀàÂâÄäÃã]/"), utf8_decode("/[ÉéÈèËëÊê]/"), utf8_decode("/[ÍíÌìÏïÎî]/"), utf8_decode("/[ÓóÒòÖöÔôÕõ]/"), utf8_decode("/[ÚúÙùÜüÛû]/"), utf8_decode("/[Ññ]/"), utf8_decode("/[Çç]/"), "/[\"']/");
            $replacement = array("a", "e", "i", "o", "u", "n", "c", "'");
            $datosPreproc = strtolower(preg_replace($pattern, $replacement, utf8_decode($datos['AgenteNombre'])));
            $query = preg_replace("/\b(['\-\w]+)\b/", $NombreCampo . ":(($1~2)^2 OR *$1*)", $datosPreproc);
            //$query = preg_replace("/\b(['\-\w]+)\b/",$NombreCampo.":(($1~2)^2 OR *$1*)",$datosPreproc)." OR \"{$datosPreproc}\"~5";
            $bool->addMust(Query::query_string(null, $query)->setDefaultOperator('AND'));



        }
        if (isset($datos['AgenteCuil']) && $datos['AgenteCuil'] != "")
            $bool->addMust(Query::term('Agente.Cuil', $datos['AgenteCuil']));



        if (isset($datos['AgenteDNI']) && $datos['AgenteDNI'] != "")
            $bool->addMust(Query::term('Agente.Dni', $datos['AgenteDNI']));


        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != ""){
            $bool->addFilter(Query::nested("Puestos", Query::multi_match($datos['IdPuesto'], ['IdPuesto','Puestos.Id'])));
        }

        self::agregarFiltrosTipoAcceso($datos, $bool);


        $datosEnviar = Consultas\Base::nueva($size, $from)
            ->setQuery($bool);

        if (isset($datos['sidx']) && is_array($datos['sidx']) && count($datos['sidx']) > 0) {
            foreach ($datos['sidx'] as $Order)
                $datosEnviar->addSort(new Sort($Order['Field'], $Order['Sort']));

        } else {
            if (isset($datos['sidx']) && $datos['sidx'] != '')
                $SortField = $datos['sidx'];
            if (isset($datos['sord']) && $datos['sord'] != '')
                $SortOrder = $datos['sord'];

            $datosEnviar->addSort(new Sort($SortField, $SortOrder));
        }

        $cuerpo = $datosEnviar->toJson();


        $this->cnx->setDebug(false);

        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $data, $codigoRetorno, 'track_total_hits=true' . $scroll)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($data['hits'])) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }
        $numfilas = intval($data['hits']['total']['value']);
        $total = (int)$data['hits']['total']['value'];
        $resultado = $data['hits']['hits'];
        if (isset($data['_scroll_id']))
            $scroll_id = $data['_scroll_id'];

        return true;
    }

    public function buscarxDNI (array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total){

        $SortField = 'Id';
        $SortOrder = 'desc';

        list($size, $from) = self::detectarSizeFrom($datos);

        $bool = Query::bool();

        $i = 0;

        $scroll = "";
        if (!empty($datos['scroll']) && preg_match("/\d+[dhms]/", $datos['scroll'])) {
            $scroll = "&scroll={$datos['scroll']}";
            unset($datos['scroll']);
        }

        if (FuncionesPHPLocal::isEmpty($datos["AgenteDNI"])) {
            $this->setError("400","No se recibe DNI al buscar novedades");
            return false;
        }

        $bool->addMust(Query::term('Agente.Dni', $datos['AgenteDNI']));

        self::agregarFiltrosTipoAcceso($datos, $bool);

        $datosEnviar = Consultas\Base::nueva($size, $from)
            ->setQuery($bool);

        if (isset($datos['sidx']) && is_array($datos['sidx']) && count($datos['sidx']) > 0) {
            foreach ($datos['sidx'] as $Order)
                $datosEnviar->addSort(new Sort($Order['Field'], $Order['Sort']));

        } else {
            if (isset($datos['sidx']) && $datos['sidx'] != '')
                $SortField = $datos['sidx'];
            if (isset($datos['sord']) && $datos['sord'] != '')
                $SortOrder = $datos['sord'];

            $datosEnviar->addSort(new Sort($SortField, $SortOrder));
        }

        $cuerpo = $datosEnviar->toJson();

        $this->cnx->setDebug(false);

        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $data, $codigoRetorno, 'track_total_hits=true' . $scroll)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($data['hits'])) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }
        $numfilas = intval($data['hits']['total']['value']);
        $total = (int)$data['hits']['total']['value'];
        $resultado = $data['hits']['hits'];

        return true;
    }

    public function buscarxCodigo(array $datos, ?array &$resultado, ?int &$numfilas): bool {
        $numfilas = 0;
        $id = self::obtenerId($datos);

        //$this->cnx->setDebug(true);
        if (!$this->cnx->sendGet(self::INDEX, '_doc', $resultadoConsulta, $codigoRetorno, $id)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        $numfilas = $resultadoConsulta['hits']['total']['value'];
        $resultado = current($resultado['hits']['hits'])['_source'];

        return true;
    }

    //busqueda por id puesto raiz para historico de puesto
    public function buscarxIdPuestoRaiz(array $datos, ?array &$resultado, ?int &$numfilas) {
        $SortField = 'Id';
        $SortOrder = 'desc';

        list($size, $from) = self::detectarSizeFrom($datos);

        $bool = Query::bool();

        $i = 0;

        $scroll = "";
        if (!empty($datos['scroll']) && preg_match("/\d+[dhms]/", $datos['scroll'])) {
            $scroll = "&scroll={$datos['scroll']}";
            unset($datos['scroll']);
        }

        if (FuncionesPHPLocal::isEmpty($datos["IdPuesto"])) {
            $this->setError("400","No se aenvia idpuesto al buscar novedades");
            return false;
        }

        //$bool->addFilter(Query::nested('Puestos', Query::term('Puestos.Id', (int)$datos['IdPuestoRaiz'])));

        $arrayIdPuestos = explode(",", $datos["IdPuesto"]);

        $bool->addFilter(Query::nested('Puestos', Query::terms('Puestos.Id', $arrayIdPuestos)));


        self::agregarFiltrosTipoAcceso($datos, $bool);

        $datosEnviar = Consultas\Base::nueva($size, $from)
            ->setQuery($bool);

        if (isset($datos['sidx']) && is_array($datos['sidx']) && count($datos['sidx']) > 0) {
            foreach ($datos['sidx'] as $Order)
                $datosEnviar->addSort(new Sort($Order['Field'], $Order['Sort']));

        } else {
            if (isset($datos['sidx']) && $datos['sidx'] != '')
                $SortField = $datos['sidx'];
            if (isset($datos['sord']) && $datos['sord'] != '')
                $SortOrder = $datos['sord'];

            $datosEnviar->addSort(new Sort($SortField, $SortOrder));
        }

        $cuerpo = $datosEnviar->toJson();

        $this->cnx->setDebug(false);

        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $data, $codigoRetorno, 'track_total_hits=true' . $scroll)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($data['hits'])) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }
        $numfilas = (int)$data['hits']['total']['value'];
        $resultado = $data['hits']['hits'];

        //var_dump($resultado);die;

        return true;
    }


    /**
     * Busca las novedades del dashboard de Equipo de conduccion
     *
     * @param array      $datos
     * @param array|null $resultado
     * @param array|null $aggs
     * @param int|null   $numfilas
     * @param int|null   $total
     *
     * @return bool
     */
    public function BuscarDashboardEquipoConduccion(array $datos, ?array &$resultado, ?array &$aggs, ?int &$numfilas, ?int &$total): bool {

        $datosEnviar = Consultas\Base::nueva(...self::detectarSizeFrom($datos));
        $bool = Query::bool();

        $SortField = "Id";
        $SortOrder = "desc";

        $i = 0;

        if (isset($datos['IdCategoria']) && $datos['IdCategoria'] != "")
            $bool->addFilter(Query::term('TipoDocumento.Categoria.Id', (int)$datos['IdCategoria']));

        if (isset($datos['TipoDocumento']) && is_array($datos['TipoDocumento']) && count($datos['TipoDocumento']) > 0)
            $bool->addFilter(Query::terms('TipoDocumento.Id', $datos['TipoDocumento']));

        if (isset($datos['IdNivel']) && is_array($datos['IdNivel']) && count($datos['IdNivel']) > 0)
            $bool->addFilter(Query::terms('Nivel.Id', $datos['IdNivel']));

        if (isset($datos['IdEscuela']) && is_array($datos['IdEscuela']) && count($datos['IdEscuela']) > 0)
            $bool->addFilter(Query::terms('IdEscuela', $datos['IdEscuela']));
        elseif (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "")
            $bool->addFilter(Query::term('IdEscuela', $datos['IdEscuela']));


        if (!FuncionesPHPLocal::isEmpty($datos['IdEstado'])) {
            if (is_array($datos['IdEstado']))
                $bool->addFilter(Query::terms('Estado.Id', $datos['IdEstado']));
            else
                $bool->addFilter(Query::term('Estado.Id', $datos['IdEstado']));
        }

        if (isset($datos['IdArea']) && $datos['IdArea'] != "")
            $bool->addFilter(Query::terms('Area.Id', is_array($datos['IdArea']) ? $datos['IdArea'] : explode(',', $datos['IdArea'])));

        if (isset($datos['IdPersona']) && $datos['IdPersona'] != "")
            $bool->addFilter(Query::terms('Agente.Id', is_array($datos['IdPersona']) ? $datos['IdPersona'] : explode(',', $datos['IdPersona'])));


        self::agregarFiltrosTipoAcceso($datos, $bool);
            $datosEnviar->setQuery($bool);

        if ($bool->countMust() == 0 && $bool->countShould() == 0)
            $datosEnviar->addSort(new Sort($SortField, $SortOrder));


        if (!FuncionesPHPLocal::isEmpty($datos['IdEstadoTomaPosesion'])) {
            $datosEnviar->setAgg(
                'TomasPosesionPendiente',
                Agg::filter(
                    Query::term('Estado.Id', $datos['IdEstadoTomaPosesion'])
                )
            );
        }

        $cuerpo = $datosEnviar->toJson();
        $this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $data, $codigoRetorno, 'track_total_hits=true')) {
            $this->setError($this->cnx->getError());
            return false;
        }


        if (!isset($data['hits'])) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }
        $numfilas = intval($data['hits']['total']['value']);
        $total = (int)$data['hits']['total']['value'];
        $resultado = $data['hits']['hits'];


        $aggs = [];
        if (!FuncionesPHPLocal::isEmpty($datos['IdEstadoTomaPosesion'])) {
            $aggs = $data['aggregations'];
        }

        return true;
    }


    public function verificarHashDatos(array $datos): bool {
        $oObjeto = new \cDocumentos(null, FMT_ARRAY);
        if (!$oObjeto->verificarHashDatos($datos)) {
            $this->setError($oObjeto->getError());
            return false;
        }
        return true;
    }

    /**
     * @param array                    $datos
     * @param \Elastic\Consultas\Query $bool
     */
    private static function agregarFiltrosTipoAcceso(array $datos, Query &$bool): void {
        $nestedQuery = Query::bool();
        switch ($_SESSION['TipoAcceso']) {
            case 1: // Seleccion de regiones
                //$datos['filtarxRegionxNivelxTurno']
                if (!isset($datos['filtarxRegionxNivelxTurno']))
                    break;
                foreach($datos['filtarxRegionxNivelxTurno'] as $filtro) {
                    $subQuery = Query::bool();
                    $subQuery->addFilter(Query::term('Escuela.Region.Id', $filtro['Region']));

                    if (0 != $filtro['Nivel'])
                        $nestedQuery->addFilter(Query::term('Puestos.Nivel.Id', $filtro['Nivel']));

                    if (0 != $filtro['Nivel'])
                        $nestedQuery->addFilter(Query::term('Puestos.Turno.Id', $filtro['Turno']));

                    if ($nestedQuery->countFilter() > 0)
                        $subQuery->addFilter(Query::nested('Puestos', $nestedQuery));
                    $bool->addShould($subQuery);
                }
                break;
            case 2: // seleccion de escuela
                //$datos['IdEscuela'] se resuleve por el circuito normal
                if (!isset($datos['filtarxEscuelaxNivelxTurno']))
                    break;
                foreach($datos['filtarxEscuelaxNivelxTurno'] as $filtro) {
                    $subQuery = Query::bool();
                    if (0 != $filtro['Nivel'])
                        $nestedQuery->addFilter(Query::term('Puestos.Nivel.Id', $filtro['Nivel']));

                    if (0 != $filtro['Nivel'])
                        $nestedQuery->addFilter(Query::term('Puestos.Turno.Id', $filtro['Turno']));

                    if ($nestedQuery->countFilter() > 0)
                        $bool->addShould(Query::nested('Puestos', $nestedQuery));
                }
                break;
            case 3: // seleccion de nivel y escuelas
                if (isset($datos['IdNivel']))
                {
                    if(is_array($datos['IdNivel']))
                        $bool->addFilter(Query::nested('Puestos', Query::terms('Puestos.Nivel.Id', $datos['IdNivel'])));
                    else
                        $bool->addFilter(Query::nested('Puestos', Query::term('Puestos.Nivel.Id', $datos['IdNivel'])));
                }
                if (!empty($datos['IdsEscuela']))
                    $bool->addFilter(Query::terms('Escuela.Id', $datos['IdsEscuela']));
                break;
            default:
                break;
        }
    }

}
