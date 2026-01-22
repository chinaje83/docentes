<?php

namespace Elastic;

use Elastic\Tipos\Objeto;
use FuncionesPHPLocal;
use http\Exception\BadQueryStringException;
use Jose\Component\Signature\Serializer\JSONFlattenedSerializer;
use ManejoErrores;
use stdClass;

class SolicitudCobertura implements InterfaceBase {
    use ManejoErrores;

    /** @var string */
    private const INDEX = INDEXPREFIX . SUFFIX_SOLICITUDCOBERTURA;
    /** @var Conexion */
    private $cnx;

    /**
     * Personas constructor.
     *
     * @param Conexion $cnx
     */
    public function __construct(Conexion $cnx) {
        $this->cnx =& $cnx;
    }

    /**
     * Parametros espec�ficos del �ndice
     *
     * Esta funci�n agrega configuraciones propias del �ndice al objeto su objetivo es
     * poder modificar la creaci�n de los �ndices desde las clases espec�ficas en lugar de
     * requerir modificar cMapping el cual deber�a ser mayormente invariante
     *
     * @param stdClass $jsonData
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


        if (empty($jsonData->settings->analysis->char_filter))
            $jsonData->settings->analysis->char_filter = new stdClass();

        $jsonData->settings->analysis->char_filter->tres_letras = new stdClass();
        $jsonData->settings->analysis->char_filter->tres_letras->type = 'pattern_replace';
        $jsonData->settings->analysis->char_filter->tres_letras->pattern = '^(.{3}).$';
        $jsonData->settings->analysis->char_filter->tres_letras->replacement = '$1';

        if (empty($jsonData->settings->analysis->analyzer))
            $jsonData->settings->analysis->analyzer = new stdClass();

        $jsonData->settings->analysis->analyzer->custom_es = new stdClass();
        $jsonData->settings->analysis->analyzer->custom_es->type = 'custom';
        $jsonData->settings->analysis->analyzer->custom_es->tokenizer = 'standard';
        $jsonData->settings->analysis->analyzer->custom_es->filter = ['lowercase', 'asciifolding', 'spanish_stop', 'spanish_stemmer'];

        if (empty($jsonData->settings->analysis->normalizer))
            $jsonData->settings->analysis->normalizer = new stdClass();
        $jsonData->settings->analysis->normalizer->minusculas_tres_letras = new stdClass();
        $jsonData->settings->analysis->normalizer->minusculas_tres_letras->type = 'custom';
        $jsonData->settings->analysis->normalizer->minusculas_tres_letras->char_filter = ['tres_letras'];
        $jsonData->settings->analysis->normalizer->minusculas_tres_letras->filter = ['lowercase', 'asciifolding'];
    }

    /**
     * Estructura de dados del �ndice
     *
     * @param bool $devolverJson
     * @return false|stdClass|string
     */
    public static function Estructura($devolverJson = true) {


        $jsonData = new Tipos\Mapping('strict');

        $jsonData->Id = new Tipos\EnteroLargo();

        $jsonData->Tipo = (new Tipos\Join('Solicitud', ['Sub-Solicitud']))
            ->addRelacion('Sub-Solicitud', ['Puesto', 'Inscripto']);


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque solicitud                                    *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->Escuela = new Tipos\Objeto();
        $jsonData->Escuela->Id = new Tipos\Entero();
        $jsonData->Escuela->Nombre = new Tipos\Texto('spanish');
        $jsonData->Escuela->Codigo = new Tipos\Keyword();
        $jsonData->Escuela->CUE = new Tipos\Entero();
        $jsonData->Escuela->Region = new Tipos\Objeto();
        $jsonData->Escuela->Region->Id = new Tipos\Keyword();
        $jsonData->Escuela->Region->Nombre = new Tipos\Texto('spanish');
        $jsonData->Escuela->Departamento = new Tipos\Objeto();
        $jsonData->Escuela->Departamento->Id = new Tipos\Keyword();
        $jsonData->Escuela->Departamento->Nombre = new Tipos\Texto('spanish');
        $jsonData->Escuela->Localidad = new Tipos\Objeto();
        $jsonData->Escuela->Localidad->Id = new Tipos\Keyword();
        $jsonData->Escuela->Localidad->Nombre = new Tipos\Texto('spanish');
        $jsonData->Escuela->Nivel = new Tipos\Objeto();
        $jsonData->Escuela->Nivel->Id = new Tipos\Keyword();
        $jsonData->Escuela->Nivel->Nombre = new Tipos\Texto('spanish');
        $jsonData->Escuela->Turno = new Tipos\Objeto();
        $jsonData->Escuela->Turno->Id = new Tipos\Keyword();
        $jsonData->Escuela->Turno->Nombre = new Tipos\Texto('spanish');
        $jsonData->Escuela->Turno->NombreCorto = new Tipos\Texto('spanish');

        $jsonData->Licencia = new Tipos\Objeto();
        $jsonData->Licencia->Id = new Tipos\EnteroLargo();
        $jsonData->Licencia->Tipo = new Tipos\Objeto();
        $jsonData->Licencia->Tipo->Id = new Tipos\Entero();
        $jsonData->Licencia->Tipo->Nombre = new Tipos\Texto('spanish');


        $jsonData->Documento = new Tipos\Objeto();
        $jsonData->Documento->Id = new Tipos\Entero();
        $jsonData->Documento->Tipo = new Tipos\Objeto();
        $jsonData->Documento->Tipo->Id = new Tipos\Entero();
        $jsonData->Documento->Tipo->Nombre = new Tipos\Texto('spanish');

        $jsonData->PersonaSaliente = new Tipos\Objeto();
        $jsonData->PersonaSaliente->Id = new Tipos\Entero();
        $jsonData->PersonaSaliente->Documento = new Tipos\Objeto();
        $jsonData->PersonaSaliente->Documento->Numero = new Tipos\Keyword();
        $jsonData->PersonaSaliente->Documento->Tipo = new Tipos\Objeto();
        $jsonData->PersonaSaliente->Documento->Tipo->Id = new Tipos\Entero();
        $jsonData->PersonaSaliente->Documento->Tipo->Nombre = new Tipos\Texto('spanish');
        $jsonData->PersonaSaliente->Cuil = new Tipos\Objeto();
        $jsonData->PersonaSaliente->Cuil->Numero = new Tipos\Keyword();
        $jsonData->PersonaSaliente->Sexo = new Tipos\Objeto();
        $jsonData->PersonaSaliente->Sexo->Id = new Tipos\Keyword();
        $jsonData->PersonaSaliente->Sexo->Nombre = new Tipos\Texto('spanish');
        $jsonData->PersonaSaliente->Nombre = (new Tipos\Keyword())
            ->addField(
                'sort',
                (new Tipos\Keyword())->setNormalizer('minusculas_tres_letras')
            );


        $jsonData->FechaDesde = new Tipos\Fecha('yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis');

        $jsonData->FechaHasta = new Tipos\Fecha('yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis');

        $jsonData->Observaciones = new Tipos\Texto('spanish');


        $jsonData->Area = new Tipos\Objeto();
        $jsonData->Area->Id = new Tipos\Entero();
        $jsonData->Area->Nombre = new Tipos\Texto('spanish');
        $jsonData->Area->Tipo = new Tipos\Objeto();
        $jsonData->Area->Tipo->Id = new Tipos\Entero();
        $jsonData->Area->Tipo->Nombre = new Tipos\Texto('spanish');

        $jsonData->Estado = new Tipos\Objeto();
        $jsonData->Estado->Id = new Tipos\Entero();
        $jsonData->Estado->Nombre = new Tipos\Texto('spanish');

        $jsonData->AreaInicial = new Tipos\Objeto();
        $jsonData->AreaInicial->Id = new Tipos\Entero();
        $jsonData->AreaInicial->Nombre = new Tipos\Texto('spanish');
        $jsonData->AreaInicial->Tipo = new Tipos\Objeto();
        $jsonData->AreaInicial->Tipo->Id = new Tipos\Entero();
        $jsonData->AreaInicial->Tipo->Nombre = new Tipos\Texto('spanish');

        $jsonData->EstadoInicial = new Tipos\Objeto();
        $jsonData->EstadoInicial->Id = new Tipos\Entero();
        $jsonData->EstadoInicial->Nombre = new Tipos\Texto('spanish');

        $jsonData->MovimientoFecha = new Tipos\Fecha('yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis');

        $jsonData->FechaEnvio = new Tipos\Fecha('yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis');


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Sub-solicitud                                *
         *                                                                              *
         * **************************************************************************** */


        $jsonData->IdSolicitud = new Tipos\EnteroLargo();

        $jsonData->Novedad = new Tipos\Objeto();
        $jsonData->Novedad->Id = new Tipos\EnteroLargo();


        $jsonData->PersonaPropuesta = new Tipos\Objeto();
        $jsonData->PersonaPropuesta->Id = new Tipos\Entero();
        $jsonData->PersonaPropuesta->Documento = new Tipos\Objeto();
        $jsonData->PersonaPropuesta->Documento->Numero = new Tipos\Keyword();
        $jsonData->PersonaPropuesta->Documento->Tipo = new Tipos\Objeto();
        $jsonData->PersonaPropuesta->Documento->Tipo->Id = new Tipos\Entero();
        $jsonData->PersonaPropuesta->Documento->Tipo->Nombre = new Tipos\Texto('spanish');
        $jsonData->PersonaPropuesta->Cuil = new Tipos\Objeto();
        $jsonData->PersonaPropuesta->Cuil->Numero = new Tipos\Keyword();
        $jsonData->PersonaPropuesta->Sexo = new Tipos\Objeto();
        $jsonData->PersonaPropuesta->Sexo->Id = new Tipos\Keyword();
        $jsonData->PersonaPropuesta->Sexo->Nombre = new Tipos\Texto('spanish');
        $jsonData->PersonaPropuesta->Nombre = (new Tipos\Keyword())
            ->addField(
                'sort',
                (new Tipos\Keyword())->setNormalizer('minusculas_tres_letras')
            );

        $jsonData->PersonaDesignada = new Tipos\Objeto();
        $jsonData->PersonaDesignada->Id = new Tipos\Entero();
        $jsonData->PersonaDesignada->Documento = new Tipos\Objeto();
        $jsonData->PersonaDesignada->Documento->Numero = new Tipos\Keyword();
        $jsonData->PersonaDesignada->Documento->Tipo = new Tipos\Objeto();
        $jsonData->PersonaDesignada->Documento->Tipo->Id = new Tipos\Entero();
        $jsonData->PersonaDesignada->Documento->Tipo->Nombre = new Tipos\Texto('spanish');
        $jsonData->PersonaDesignada->Cuil = new Tipos\Objeto();
        $jsonData->PersonaDesignada->Cuil->Numero = new Tipos\Keyword();
        $jsonData->PersonaDesignada->Sexo = new Tipos\Objeto();
        $jsonData->PersonaDesignada->Sexo->Id = new Tipos\Keyword();
        $jsonData->PersonaDesignada->Sexo->Nombre = new Tipos\Texto('spanish');
        $jsonData->PersonaDesignada->Nombre = (new Tipos\Keyword())
            ->addField(
                'sort',
                (new Tipos\Keyword())->setNormalizer('minusculas_tres_letras')
            );


        $jsonData->InstrumentoLegal = new Tipos\Objeto();
        $jsonData->InstrumentoLegal->Numero = (new Tipos\Keyword())
            ->addField('auto', new Tipos\Autocompletar('keyword'));


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque puesto                                       *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->IdSolicitudCoberturaPersona = new Tipos\EnteroLargo();

        $jsonData->IdPuesto = new Tipos\Keyword();

        $jsonData->CodigoPuesto = new Tipos\Keyword();

        $jsonData->Cargo = new Tipos\Objeto();
        $jsonData->Cargo->Id = new Tipos\Keyword();
        $jsonData->Cargo->Nombre = new Tipos\Texto('spanish');
        $jsonData->Cargo->Codigo = new Tipos\Keyword();
        $jsonData->Materia = new Tipos\Objeto();
        $jsonData->Materia->Id = new Tipos\Keyword();
        $jsonData->Materia->Nombre = new Tipos\Texto('spanish');
        $jsonData->Materia->Codigo = new Tipos\Keyword();
        $jsonData->Modulos = new Tipos\Objeto();
        $jsonData->Modulos->Cantidad = new Tipos\Keyword();
        $jsonData->Horas = new Tipos\Objeto();
        $jsonData->Horas->Cantidad = new Tipos\Keyword();
        $jsonData->Grado = new Tipos\Objeto();
        $jsonData->Grado->Id = new Tipos\Keyword();
        $jsonData->Grado->Nombre = new Tipos\Texto('spanish');
        $jsonData->Division = new Tipos\Objeto();
        $jsonData->Division->Id = new Tipos\Keyword();
        $jsonData->Division->Nombre = new Tipos\Texto('spanish');
        $jsonData->Auxiliar = new Tipos\Booleano();


        $jsonData->Activo = new Tipos\Booleano();


        $jsonData->Desempenos = new Tipos\Nested();
        $jsonData->Desempenos->Id = new Tipos\EnteroLargo();
        $jsonData->Desempenos->HorasModulos = new Tipos\Objeto();
        $jsonData->Desempenos->HorasModulos = new Tipos\Objeto();
        $jsonData->Desempenos->HorasModulos->Tipo = new Tipos\Keyword();
        $jsonData->Desempenos->HorasModulos->Cantidad = new Tipos\EnteroCorto();
        $jsonData->Desempenos->Dia = new Tipos\Byte();
        $jsonData->Desempenos->Hora = new Tipos\RangoFecha('strict_hour_minute_second');
        $jsonData->Desempenos->Activo = new Tipos\Booleano();


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque inscripto                                    *
         *                                                                              *
         * **************************************************************************** */


        // TODO: agregar estado (aceptado/rechazado)

        /*        $jsonData->ActoPublico = new stdClass();
                $jsonData->ActoPublico->type = 'object';
                $jsonData->ActoPublico->properties = new stdClass();
                $jsonData->ActoPublico->properties->Id = new stdClass();
                $jsonData->ActoPublico->properties->Id->type = 'long';
                $jsonData->ActoPublico->properties->Estado = new stdClass();
                $jsonData->ActoPublico->properties->Estado->type = 'object';
                $jsonData->ActoPublico->properties->Estado->properties = new stdClass();
                $jsonData->ActoPublico->properties->Estado->properties->Id = new stdClass();
                $jsonData->ActoPublico->properties->Estado->properties->Id->type = 'integer';
                $jsonData->ActoPublico->properties->Estado->properties->Nombre = new stdClass();
                $jsonData->ActoPublico->properties->Estado->properties->Nombre->type = 'keyword';
                $jsonData->ActoPublico->properties->Estado->properties->Nombre->index = false;*/

        $jsonData->ActoPublico = new Tipos\Objeto();
        $jsonData->ActoPublico->Id = new Tipos\EnteroLargo();
        $jsonData->ActoPublico->Estado = new Tipos\Objeto();
        $jsonData->ActoPublico->Estado->Id = new Tipos\Entero();
        $jsonData->ActoPublico->Estado->Nombre = (new Tipos\Keyword())->noIndexar();


        /*        $jsonData->IdPersona = new stdClass();
                $jsonData->IdPersona->type = 'integer';*/

        $jsonData->IdPersona = new Tipos\Entero();


        $jsonData->Nombre = (new Tipos\Keyword())
            ->addField('prefix', new Tipos\Autocompletar('custom_es'));

        $jsonData->Apellido = (new Tipos\Keyword())
            ->addField('prefix', new Tipos\Autocompletar('custom_es'));

        $jsonData->DocumentoIdentidad = new Tipos\Objeto();
        $jsonData->DocumentoIdentidad->Tipo = new Tipos\Objeto();
        $jsonData->DocumentoIdentidad->Tipo->Id = new Tipos\Entero();
        $jsonData->DocumentoIdentidad->Tipo->Descripcion = new Tipos\Keyword();
        $jsonData->DocumentoIdentidad->Numero = (new Tipos\Keyword())
            ->addField('prefix', new Tipos\Autocompletar('pattern'));

        $jsonData->Sexo = new Tipos\Objeto();
        $jsonData->Sexo->Id = new Tipos\Entero();
        $jsonData->Sexo->Descripcion = new Tipos\Keyword();

        $jsonData->CUIL = new Tipos\Entero();


        $jsonData->Total = new Tipos\Flotante(100);


        /* **************************************************************************** */

        $jsonData->Alta = new Tipos\Objeto();
        $jsonData->Alta->Fecha = new Tipos\Fecha('yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis');
        $jsonData->Alta->Usuario = new Tipos\Objeto();
        $jsonData->Alta->Usuario->Id = new Tipos\Entero();
        $jsonData->Alta->Usuario->Nombre = new Tipos\Texto('spanish');

        $jsonData->UltimaModificacion = new Tipos\Objeto();
        $jsonData->UltimaModificacion->Fecha = new Tipos\Fecha('yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis');
        $jsonData->UltimaModificacion->Usuario = new Tipos\Objeto();
        $jsonData->UltimaModificacion->Usuario->Id = new Tipos\Entero();
        $jsonData->UltimaModificacion->Usuario->Nombre = new Tipos\Texto('spanish');

        return $devolverJson ? json_encode($jsonData) : $jsonData;
    }

    /**
     * Estructura de datos del indice correspondiente de ES
     *
     * Devuelve el json o un objeto PHP con el contenido
     *
     * @param array $datos
     * @param bool $encode
     * @return false|stdClass|string
     */
    public static function armarDatosElastic(array $datos, bool $encode = false) {
        $jsonData = new stdClass();

        $jsonData->Id = (int)$datos['Id'];

        switch ($datos['Tipo'] ?? NULL) {
            case 'Inscripto':
                self::_armarDatosInscriptos($datos, $jsonData);
                break;
            case 'Sub-Solicitud':
                self::_armarDatosSubSolicitud($datos, $jsonData);
                break;
            case 'Puesto':
                self::_armarDatosSolicitudPuesto($datos, $jsonData);
                break;
            case 'Solicitud':
            default:
                self::_armarDatosSolicitud($datos, $jsonData);
        }


        if (!FuncionesPHPLocal::isEmpty($datos['AltaFecha'])) {
            $jsonData->Alta = new stdClass();
            $jsonData->Alta->Fecha = $datos['AltaFecha'];

            if (!FuncionesPHPLocal::isEmpty($datos['AltaUsuario'])) {
                $jsonData->Alta->Usuario = new stdClass();
                $jsonData->Alta->Usuario->Id = $datos['AltaUsuario'];
                $jsonData->Alta->Usuario->Nombre = $datos['NombreAltaUsuario'];
            }
        }

        if (!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionFecha'])) {
            $jsonData->UltimaModificacion = new stdClass();
            $jsonData->UltimaModificacion->Fecha = $datos['UltimaModificacionFecha'];

            if (!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionUsuario'])) {
                $jsonData->UltimaModificacion->Usuario = new stdClass();
                $jsonData->UltimaModificacion->Usuario->Id = $datos['UltimaModificacionUsuario'];
                $jsonData->UltimaModificacion->Usuario->Nombre = $datos['NombreUltimaModificacionUsuario'];
            }
        }

        $jsonData = FuncionesPHPLocal::ConvertiraUtf8($jsonData);

        return $encode ? json_encode($jsonData) : $jsonData;

    }

    /**
     * @param array|stdClass $datos
     * @return null|integer|string
     */
    public static function obtenerId($datos) {

        if (is_object($datos))
            $datos = (array)$datos;
        if (isset($datos['ActoPublico'])) {
            $aActoPublico = (array)$datos['ActoPublico'];
            $datos['IdActoPublico'] = $aActoPublico['Id'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Id'])) {
            if (!FuncionesPHPLocal::isEmpty($datos['Tipo']))
                return self::procesarId($datos['Tipo'], $datos['Id'], $datos['IdSolicitud'] ?? null);
            else
                return (int)$datos['Id'];
        }

        return NULL;
    }

    /**
     * Devuelve el �ndice correspondiente a la clase
     *
     * @return string
     */
    public static function getIndex(): string {
        return self::INDEX;
    }

    /**
     * @param array $datos
     * @param object $jsonData
     */
    private static function _armarDatosSolicitud(array $datos, object &$jsonData): void {

        $jsonData->Tipo = new stdClass();
        $jsonData->Tipo->name = 'Solicitud';

        $jsonData->Escuela = new stdClass();
        $jsonData->Escuela->Id = $datos['IdEscuela'] ?? NULL;

        if (!FuncionesPHPLocal::isEmpty($datos['IdEscuela'])) {

            if (!FuncionesPHPLocal::isEmpty($datos['Codigo']))
                $jsonData->Escuela->Codigo = $datos['Codigo'];

            if (!FuncionesPHPLocal::isEmpty($datos['ClaveUnicaEscuela']))
                $jsonData->Escuela->CUE = $datos['ClaveUnicaEscuela'];

            if (!FuncionesPHPLocal::isEmpty($datos['NombreEscuela']))
                $jsonData->Escuela->Nombre = $datos['NombreEscuela'];

            if (!FuncionesPHPLocal::isEmpty($datos['IdRegion'])) {
                $jsonData->Escuela->Region = new stdClass();
                $jsonData->Escuela->Region->Id = (int)$datos['IdRegion'];
                $jsonData->Escuela->Region->Nombre = $datos['NombreRegion'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['IdDepartamento'])) {
                $jsonData->Escuela->Departamento = new stdClass();
                $jsonData->Escuela->Departamento->Id = (int)$datos['IdDepartamento'];
                $jsonData->Escuela->Departamento->Nombre = $datos['NombreDepartamento'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['IdLocalidad'])) {
                $jsonData->Escuela->Localidad = new stdClass();
                $jsonData->Escuela->Localidad->Id = (int)$datos['IdLocalidad'];
                $jsonData->Escuela->Localidad->Nombre = $datos['NombreLocalidad'];
            }
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdRegistroTipoDocumento'])) {
            $jsonData->Documento = new stdClass();
            $jsonData->Documento->Id = $datos['IdRegistroTipoDocumento'];

            if (!FuncionesPHPLocal::isEmpty($datos['IdTipoDocumento'])) {
                $jsonData->Documento->Tipo = new stdClass();
                $jsonData->Documento->Tipo->Id = (int)$datos['IdTipoDocumento'];
                if (!FuncionesPHPLocal::isEmpty($datos['NombreTipoDocumento']))
                    $jsonData->Documento->Tipo->Nombre = $datos['NombreTipoDocumento'];
            }
        }

        $jsonData->Licencia = new stdClass();
        $jsonData->Licencia->Id = ((int)($datos['IdLicencia'] ?? 0)) ?: NULL;

        if (!FuncionesPHPLocal::isEmpty($datos['IdLicencia'])) {
            $jsonData->Licencia->Tipo = new stdClass();
            $jsonData->Licencia->Tipo->Id = $datos['IdTipoLicencia'];
            $jsonData->Licencia->Tipo->Nombre = $datos['NombreTipoLicencia'];
        }

        $jsonData->PersonaSaliente = new stdClass();
        $jsonData->PersonaSaliente->Id = ((int)($datos['IdPersonaSaliente'] ?? 0)) ?: NULL;

        if (!FuncionesPHPLocal::isEmpty($datos['IdPersonaSaliente'])) {

            if (!FuncionesPHPLocal::isEmpty($datos['DocumentoPersonaSaliente'])) {
                $jsonData->PersonaSaliente->Documento = new stdClass();
                $jsonData->PersonaSaliente->Documento->Numero = $datos['DocumentoPersonaSaliente'];

                if (!FuncionesPHPLocal::isEmpty($datos['IdTipoDocumentoPersonaSaliente'])) {
                    $jsonData->PersonaSaliente->Documento->Tipo = new stdClass();
                    $jsonData->PersonaSaliente->Documento->Tipo->Id = $datos['IdTipoDocumentoPersonaSaliente'];
                    $jsonData->PersonaSaliente->Documento->Tipo->Nombre = $datos['NombreTipoDocumentoPersonaSaliente'];
                }
            }

            if (!FuncionesPHPLocal::isEmpty($datos['CuilPersonaSaliente'])) {
                $jsonData->PersonaSaliente->Cuil = new stdClass();
                $jsonData->PersonaSaliente->Cuil->Numero = $datos['CuilPersonaSaliente'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['NombrePersonaSaliente'])) {
                $jsonData->PersonaSaliente->Nombre = $datos['NombrePersonaSaliente'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['IdSexoPersonaSaliente'])) {
                $jsonData->PersonaSaliente->Sexo = new stdClass();
                $jsonData->PersonaSaliente->Sexo->Id = $datos['IdSexoPersonaSaliente'];
                $jsonData->PersonaSaliente->Sexo->Nombre = self::obtenerSexo($datos['SexoPersonaSaliente']);
            }
        }

        if (!FuncionesPHPLocal::isEmpty($datos['FechaDesde'])) {
            $jsonData->FechaDesde = new stdClass();
            $jsonData->FechaDesde = $datos['FechaDesde'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['FechaHasta'])) {
            $jsonData->FechaHasta = new stdClass();
            $jsonData->FechaHasta = $datos['FechaHasta'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Observaciones'])) {
            $jsonData->Observaciones = new stdClass();
            $jsonData->Observaciones = $datos['Observaciones'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['EsAuxiliar'])) {
            $jsonData->Auxiliar = new stdClass();
            $jsonData->Auxiliar = $datos['EsAuxiliar'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdArea'])) {
            $jsonData->Area = new stdClass();
            $jsonData->Area->Id = $datos['IdArea'];

            if (!FuncionesPHPLocal::isEmpty($datos['NombreArea']))
                $jsonData->Area->Nombre = $datos['NombreArea'];

            if (!FuncionesPHPLocal::isEmpty($datos['IdTipoArea'])) {
                $jsonData->Area->Tipo = new stdClass();
                $jsonData->Area->Tipo->Id = $datos['IdTipoArea'];
                $jsonData->Area->Tipo->Nombre = $datos['NombreTipoArea'];
            }
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdEstado'])) {
            $jsonData->Estado = new stdClass();
            $jsonData->Estado->Id = $datos['IdEstado'];
            $jsonData->Estado->Nombre = $datos['NombreEstado'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdAreaInicial'])) {
            $jsonData->AreaInicial = new stdClass();
            $jsonData->AreaInicial->Id = $datos['IdAreaInicial'];

            if (!FuncionesPHPLocal::isEmpty($datos['NombreAreaInicial']))
                $jsonData->AreaInicial->Nombre = $datos['NombreAreaInicial'];

            if (!FuncionesPHPLocal::isEmpty($datos['IdTipoAreaInicial'])) {
                $jsonData->AreaInicial->Tipo = new stdClass();
                $jsonData->AreaInicial->Tipo->Id = $datos['IdTipoAreaInicial'];
                $jsonData->AreaInicial->Tipo->Nombre = $datos['NombreTipoAreaInicial'];
            }
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdEstadoInicial'])) {
            $jsonData->EstadoInicial = new stdClass();
            $jsonData->EstadoInicial->Id = $datos['IdEstadoInicial'];
            $jsonData->EstadoInicial->Nombre = $datos['NombreEstadoInicial'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['MovimientoFecha'])) {
            $jsonData->MovimientoFecha = new stdClass();
            $jsonData->MovimientoFecha = $datos['MovimientoFecha'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['FechaEnvio'])) {
            $jsonData->FechaEnvio = new stdClass();
            $jsonData->FechaEnvio = $datos['FechaEnvio'];
        }
    }

    /**
     * @param array $datos
     * @param object $jsonData
     */
    private static function _armarDatosSubSolicitud(array $datos, object &$jsonData): void {
        $jsonData->Tipo = new stdClass();
        $jsonData->Tipo->name = 'Sub-Solicitud';
        $jsonData->Tipo->parent = (int)$datos['IdSolicitudCobertura'];
        $jsonData->IdSolicitud = (int)$datos['IdSolicitudCobertura'];

        $jsonData->Novedad = new stdClass();
        $jsonData->Novedad->Id = ((int)($datos['IdNovedad'] ?? 0)) ?: NULL;

        $jsonData->PersonaPropuesta = NULL;
        if (!FuncionesPHPLocal::isEmpty($datos['IdPersonaPropuesta'])) {
            $jsonData->PersonaPropuesta = new stdClass();
            $jsonData->PersonaPropuesta->Id = $datos['IdPersonaPropuesta'];

            if (!FuncionesPHPLocal::isEmpty($datos['DocumentoPersonaPropuesta'])) {
                $jsonData->PersonaPropuesta->Documento = new stdClass();
                $jsonData->PersonaPropuesta->Documento->Numero = $datos['DocumentoPersonaPropuesta'];

                if (!FuncionesPHPLocal::isEmpty($datos['IdTipoDocumentoPersonaPropuesta'])) {
                    $jsonData->PersonaPropuesta->Documento->Tipo = new stdClass();
                    $jsonData->PersonaPropuesta->Documento->Tipo->Id = $datos['IdTipoDocumentoPersonaPropuesta'];
                    $jsonData->PersonaPropuesta->Documento->Tipo->Nombre = $datos['NombreTipoDocumentoPersonaPropuesta'];
                }
            }

            if (!FuncionesPHPLocal::isEmpty($datos['CuilPersonaPropuesta'])) {
                $jsonData->PersonaPropuesta->Cuil = new stdClass();
                $jsonData->PersonaPropuesta->Cuil->Numero = $datos['CuilPersonaPropuesta'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['NombrePersonaPropuesta'])) {
                $jsonData->PersonaPropuesta->Nombre = $datos['NombrePersonaPropuesta'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['IdSexoPersonaPropuesta'])) {
                $jsonData->PersonaPropuesta->Sexo = new stdClass();
                $jsonData->PersonaPropuesta->Sexo->Id = $datos['IdSexoPersonaPropuesta'];
                $jsonData->PersonaPropuesta->Sexo->Nombre = self::obtenerSexo($datos['SexoPersonaPropuesta']);
            }
        }


        $jsonData->PersonaDesignada = NULL;
        if (!FuncionesPHPLocal::isEmpty($datos['IdPersonaDesignada'])) {
            $jsonData->PersonaDesignada = new stdClass();
            $jsonData->PersonaDesignada->Id = $datos['IdPersonaDesignada'];
            if (!FuncionesPHPLocal::isEmpty($datos['DocumentoPersonaDesignada'])) {
                $jsonData->PersonaDesignada->Documento = new stdClass();
                $jsonData->PersonaDesignada->Documento->Numero = $datos['DocumentoPersonaDesignada'];

                if (!FuncionesPHPLocal::isEmpty($datos['IdTipoDocumentoPersonaDesignada'])) {
                    $jsonData->PersonaDesignada->Documento->Tipo = new stdClass();
                    $jsonData->PersonaDesignada->Documento->Tipo->Id = $datos['IdTipoDocumentoPersonaDesignada'];
                    $jsonData->PersonaDesignada->Documento->Tipo->Nombre = $datos['NombreTipoDocumentoPersonaDesignada'];
                }
            }

            if (!FuncionesPHPLocal::isEmpty($datos['CuilPersonaDesignada'])) {
                $jsonData->PersonaDesignada->Cuil = new stdClass();
                $jsonData->PersonaDesignada->Cuil->Numero = $datos['CuilPersonaDesignada'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['NombrePersonaDesignada'])) {
                $jsonData->PersonaDesignada->Nombre = $datos['NombrePersonaDesignada'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['InstrumentoLegal'])) {
                $jsonData->InstrumentoLegal = new stdClass();
                $jsonData->InstrumentoLegal->Numero = $datos['InstrumentoLegal'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['IdSexoPersonaDesignada'])) {
                $jsonData->PersonaDesignada->Sexo = new stdClass();
                $jsonData->PersonaDesignada->Sexo->Id = $datos['IdSexoPersonaDesignada'];
                $jsonData->PersonaDesignada->Sexo->Nombre = self::obtenerSexo($datos['SexoPersonaDesignada']);
            }

            # ARMAR
//            if (!FuncionesPHPLocal::isEmpty($datos['IdEstadoPersona'])) {
//                $jsonData->PersonaDesignada->Estado = new stdClass();
//                $jsonData->PersonaDesignada->Estado->Id = $datos['IdEstadoPersona'];
//            }
        }

    }

    /**
     * @param array $datos
     * @param object $jsonData
     */
    private static function _armarDatosSolicitudPuesto(array $datos, object &$jsonData): void {
        $jsonData->Tipo = new stdClass();
        $jsonData->Tipo->name = 'Puesto';
        $jsonData->Tipo->parent = self::procesarId(
            ['name' => 'Sub-Solicitud', 'parent' => (int)$datos['IdSolicitudCobertura']],
            $datos['IdSolicitudCoberturaPersona']
        );

        $jsonData->IdSolicitud = (int)$datos['IdSolicitudCobertura'];

        $jsonData->IdSolicitudCoberturaPersona = (int)$datos['IdSolicitudCoberturaPersona'];
        $jsonData->IdPuesto = $datos['IdPuesto'] ?? NULL;

        $jsonData->CodigoPuesto = $datos['CodigoPuesto'] ?: '';


        if (!FuncionesPHPLocal::isEmpty($datos['IdPuesto'])) {

            $jsonData->Cargo = new stdClass();
            $jsonData->Cargo->Id = ((int)($datos['IdCargo'] ?? 0)) ?: NULL;
            if (!FuncionesPHPLocal::isEmpty($datos['IdCargo'])) {
                $jsonData->Cargo->Nombre = $datos['NombreCargo'] ?? '';
                $jsonData->Cargo->Codigo = $datos['CodigoCargo'] ?? '';
            }

            $jsonData->Materia = new stdClass();
            $jsonData->Materia->Id = ((int)($datos['IdMateria'] ?? 0)) ?: NULL;
            if (!FuncionesPHPLocal::isEmpty($datos['IdMateria'])) {
                $jsonData->Materia->Nombre = $datos['NombreMateria'] ?? '';
                $jsonData->Materia->Codigo = $datos['CodigoMateria'] ?? '';
            }

            if (!FuncionesPHPLocal::isEmpty($datos['CantidadHorasModulos']) && $datos['CantidadHorasModulos'] > 0) {
                switch($datos['TipoCantidad']??0) {
                    case 2:
                        $jsonData->Modulos = new stdClass();
                        $jsonData->Modulos->Cantidad = $datos['CantidadHorasModulos'];
                        break;

                    case 1:
                    default:
                        $jsonData->Horas = new stdClass();
                        $jsonData->Horas->Cantidad = $datos['CantidadHorasModulos'];
                }
            }


            if (!FuncionesPHPLocal::isEmpty($datos['IdGradoAnio'])) {
                $jsonData->Grado = new stdClass();
                $jsonData->Grado->Id = (int)$datos['IdGradoAnio'];
                $jsonData->Grado->Nombre = $datos['NombreGrado'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['IdSeccion'])) {
                $jsonData->Division = new stdClass();
                $jsonData->Division->Id = (int)$datos['IdSeccion'];
                $jsonData->Division->Nombre = $datos['NombreSeccion'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['IdNivel'])) {
                if (!isset($jsonData->Escuela))
                    $jsonData->Escuela = new stdClass();
                $jsonData->Escuela->Nivel = new stdClass();
                $jsonData->Escuela->Nivel->Id = (int)$datos['IdNivel'];
                $jsonData->Escuela->Nivel->Nombre = $datos['NombreNivel'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['IdTurno'])) {
                if (!isset($jsonData->Escuela))
                    $jsonData->Escuela = new stdClass();
                $jsonData->Escuela->Turno = new stdClass();
                $jsonData->Escuela->Turno->Id = (int)$datos['IdTurno'];
                $jsonData->Escuela->Turno->Nombre = $datos['NombreTurno'];
                $jsonData->Escuela->Turno->NombreCorto = $datos['NombreCortoTurno'];
            }

            $jsonData->Auxiliar = (bool)($datos['Auxiliar'] ?? false);
        }
        $jsonData->Activo = (bool)($datos['Tildado']??false);


        $jsonData->Desempenos = [];
        if (!FuncionesPHPLocal::isEmpty($datos['Desempenos']) && is_array($datos['Desempenos'])) {
            $jj = -1;
            foreach ($datos['Desempenos'] as $desempeno) {
                $tipoCantidad = strtolower(trim(explode('(',((2==$desempeno['TipoCantidad']) ? NOMBRE_MODULOS : NOMBRE_HORAS))[0]));
                $jsonData->Desempenos[++$jj] = new stdClass();
                $jsonData->Desempenos[$jj]->Id = (int)$desempeno['Id'];
                $jsonData->Desempenos[$jj]->HorasModulos = new stdClass();
                $jsonData->Desempenos[$jj]->HorasModulos->Tipo = $tipoCantidad;
                $jsonData->Desempenos[$jj]->HorasModulos->Cantidad = (int)$desempeno['CantidadHorasModulos'];
                $jsonData->Desempenos[$jj]->Dia = (int)$desempeno['Dia'];
                $jsonData->Desempenos[$jj]->Hora = $desempeno['Hora'];
                $jsonData->Desempenos[$jj]->Activo = (bool)($desempeno['Tildado']??false);
            }
        }


    }

    /**
     * @param array $datos
     * @param object $jsonData
     * @return void
     */
    private static function _armarDatosInscriptos(array $datos, object &$jsonData): void {
        $jsonData->Tipo = new stdClass();
        $jsonData->Tipo->name = 'Inscripto';
        $jsonData->Tipo->parent = self::procesarId(
            ['name' => 'Sub-Solicitud', 'parent' => (int)$datos['IdSolicitudCobertura']],
            $datos['IdSolicitudCoberturaPersona']
        );
        //(int)$datos['IdSolicitudCoberturaPersona'];

        $jsonData->IdSolicitud = (int)$datos['IdSolicitud'];

        $jsonData->IdSolicitudCoberturaPersona = (int)$datos['IdSolicitudCoberturaPersona'];

        $jsonData->ActoPublico = new stdClass();
        $jsonData->ActoPublico->Id = $datos['IdActoPublico'] ?? NULL;
        $jsonData->ActoPublico->Estado = new stdClass();
        if (!FuncionesPHPLocal::isEmpty($datos['IdEstadoActoPublico'])) {
            $jsonData->ActoPublico->Estado->Id = (int)$datos['IdEstadoActoPublico'];
            $jsonData->ActoPublico->Estado->Nombre = $datos['EstadoActoPublicoNombre'] ?? NULL;
        }

        $jsonData->IdPersona = (int)$datos['IdPersona'];

        $jsonData->Nombre = $datos['Nombre'];

        if (isset($datos['Apellido']) && $datos['Apellido'] != "" && !FuncionesPHPLocal::isEmpty($datos['Apellido']))
            $jsonData->Apellido = $datos['Apellido'];

        $jsonData->DocumentoIdentidad = new stdClass();
        $jsonData->DocumentoIdentidad->Tipo = new stdClass();
        $jsonData->DocumentoIdentidad->Tipo->Id = (int)$datos['IdTipoDocumento'];
        $jsonData->DocumentoIdentidad->Tipo->Descripcion = $datos['DescripcionTipoDocumento'];
        $jsonData->DocumentoIdentidad->Numero = $datos['DNI'];

        if (isset($datos['IdSexo']) && $datos['IdSexo'] != "" && !FuncionesPHPLocal::isEmpty($datos['IdSexo'])) {
            $jsonData->Sexo = new stdClass();
            $jsonData->Sexo->Id = (int)$datos['IdSexo'];
            $jsonData->Sexo->Descripcion = $datos['DescripcionSexo'];
        }
        if (isset($datos['CUIL']) && $datos['CUIL'] != "" && !FuncionesPHPLocal::isEmpty($datos['CUIL']))
            $jsonData->CUIL = $datos['CUIL'];

        if (isset($datos['Total']) && '' !== $datos['Total'])
            $jsonData->Total = $datos['Total'] / 100;

    }

    /**
     * @param mixed $tipo
     * @param string $id
     * @param int|null $routing
     * @return string|integer
     */
    private static function procesarId($tipo, string $id, int $routing = null) {
        if (is_object($tipo))
            $tipo = (array)$tipo;
        $name = $tipo['name'] ?? (string)$tipo;
        switch ($name) {
            case 'Inscripto':
                $_id = "{$tipo['parent']}-i{$id}";
                if (!empty($routing))
                    $_id .= "?routing={$routing}";
                return $_id;
            case 'Puesto':
                $_id = "{$tipo['parent']}-p{$id}";
                if (!empty($routing))
                    $_id .= "?routing={$routing}";
                return $_id;
            case 'Sub-Solicitud':
                $_id = "{$tipo['parent']}-$id";
                if (!empty($routing))
                    $_id .= "?routing={$routing}";
                return $_id;
            default:
                return (int)$id;
        }
    }

    /**
     * @param string|null $letra
     * @return string|null
     */
    private static function obtenerSexo(?string $letra): ?string {
        switch (strtoupper($letra)) {
            case 'M':
                return 'Masculino';
            case 'F':
                return 'Femenino';
            default:
                return NULL;
        }
    }

    public function __destruct() {
        $this->error = [];
    }

    /**
     * @param array $datos
     * @param array|null $resultado
     * @return bool
     */
    public function buscarxCodigo(array $datos, ?array &$resultado): bool {
        $id = self::obtenerId($datos);
        if (isset($datos['excluirCampos']))
            $id .= '?_source_excludes=' . implode(',', $datos['excluirCampos']);

        if (!$this->cnx->sendGet(self::INDEX, '_doc', $data, $codigoRetorno, $id)) {
            $this->setError($this->cnx->getError());
            return false;
        }
        if (FuncionesPHPLocal::isEmpty($data['_source'])) {
            $this->setError(404, 'Error, no se encuentra la licencia');
            return false;
        }

        $resultado = $data['_source'];
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
     *
     * @return bool
     */
    public function buscarDatosAnexos(array $datos, ?array &$resultado): bool {

        /*
         * $jsonData->Tipo = (new Tipos\Join('Solicitud', ['Sub-Solicitud']))
            ->addRelacion('Sub-Solicitud', ['Puesto', 'Inscripto']);
         */


        $cuerpo = (new Consultas\Base(100, 0))
            ->setQuery(Consultas\Query::parent_id('Sub-Solicitud', $datos['IdSolicitudCobertura']))
            ->setAgg('Puestos',
                Consultas\Agg::children('Puesto')
                    ->setAgg('Puestos',
                        Consultas\Agg::terms('Id', 10000)
                            ->setAgg('Datos', Consultas\Agg::top_hits(1))
                    )
            )
            ->toJson();
        $this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $res, $codigoRetorno)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($res['hits'])) {
            $this->setError(500, Funciones::DevolverError($res));
            return false;
        }

        $resultado = ['Personas' => [], 'Puestos' => []];

        foreach ($res['hits']['hits'] as $hit)
            $resultado['Personas'][$hit['_source']['Id']] = $hit['_source'];

        foreach ($res['aggregations']['Puestos']['Puestos']['buckets'] as $bucket) {
            //print_r($bucket['Datos']['hits']['hits'][0]['_source']);die;
            $resultado['Puestos'][] = $bucket['Datos']['hits']['hits'][0]['_source'];
        }


        //die;

        return true;
    }

    /**
     * @param array $datos
     * @param array|null $resultado
     * @param int|null $numfilas
     * @param int|null $total
     * @return bool
     */
    public function BusquedaAvanzada(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {

        $datosEnviar = new stdClass();
        $datosEnviar->query = new stdClass();
        $datosEnviar->query = new stdClass();
        $datosEnviar->query->bool = new stdClass();
        $datosEnviar->query->bool->must = [];
        $datosEnviar->query->bool->filter = [];
        $datosEnviar->query->bool->must_not = [];

        $SortField = 'Id';
        $SortOrder = 'desc';
        $datosEnviar->from = $datos['from'] ?? 0;
        $datosEnviar->size = $datos['size'] ?? PAGINAR;

        $sort = true;
        $ff = $mm = $ss = $mn = 0;

        $datosEnviar->query->bool->filter[$ff] = new stdClass();
        $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
        $datosEnviar->query->bool->filter[$ff]->term->{'Tipo'} = new stdClass();
        $datosEnviar->query->bool->filter[$ff]->term->{'Tipo'}->value = 'Solicitud';
        $ff++;

        if (isset($datos['Id']) && $datos['Id'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Id'}->value = $datos['Id'];
            $ff++;
        }

        if (isset($datos['Codigo']) && $datos['Codigo'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.Codigo'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.Codigo'}->value = $datos['Codigo'];
            $ff++;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.Id'}->value = (int)$datos['IdEscuela'];
            $ff++;
        }

        if (isset($datos['ClaveUnicaEscuela']) && $datos['ClaveUnicaEscuela'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.CUE'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.CUE'}->value = $datos['ClaveUnicaEscuela'];
            $ff++;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.Region.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.Region.Id'}->value = $datos['IdRegion'];
            $ff++;
        }

        if (isset($datos['IdsRegion']) && $datos['IdsRegion'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Escuela.Region.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Escuela.Region.Id'} = $datos['IdsRegion'];
            $ff++;
        }

        if (isset($datos['IdsNiveles']) && $datos['IdsNiveles'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Escuela.Nivel.Id'} = is_array($datos['IdsNiveles']) ? $datos['IdsNiveles'] : explode(',', $datos['IdsNiveles']);
            $ff++;
        }

        if (isset($datos['IdsTurnos']) && $datos['IdsTurnos'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Escuela.Turno.Id'} = is_array($datos['IdsTurnos']) ? $datos['IdsTurnos'] : explode(',', $datos['IdsTurnos']);
            $ff++;
        }

        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Puesto.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Puesto.Id'}->value = $datos['IdPuesto'];
            $ff++;
        }

        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Puesto.Cargo.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Puesto.Cargo.Id'}->value = $datos['IdCargo'];
            $ff++;
        }

        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Puesto.Materia.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Puesto.Materia.Id'}->value = $datos['IdMateria'];
            $ff++;
        }

        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Licencia.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Licencia.Id'}->value = $datos['IdLicencia'];
            $ff++;
        }

        if (isset($datos['IdNovedad']) && $datos['IdNovedad'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Novedad.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Novedad.Id'}->value = $datos['IdNovedad'];
            $ff++;
        }

        if (isset($datos['IdPersonaSaliente']) && $datos['IdPersonaSaliente'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'PersonaSaliente.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'PersonaSaliente.Id'}->value = $datos['IdPersonaSaliente'];
            $ff++;
        }

        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->range = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->range->{'FechaDesde'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->range->{'FechaDesde'}->gte = $datos['FechaDesde'];
            $ff++;
        }

        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            $datos['FechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->range = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->range->{'FechaHasta'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->range->{'FechaHasta'}->lte = $datos['FechaHasta'];
            $ff++;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Estado.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Estado.Id'}->value = $datos['IdEstado'];
            $ff++;
        }


        if (isset($datos['IdArea']) && $datos['IdArea'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Area.Id'} = is_array($datos['IdArea']) ? $datos['IdArea'] : explode(',', $datos['IdArea']);
            $ff++;
        }


        if (isset($datos['NombreEscuela']) && $datos['NombreEscuela'] != "") {
            $datos['NombreEscuela'] = preg_replace(self::PATTERN, self::REPLACEMENT, $datos['NombreEscuela']);
            $query = preg_replace(self::WORD_SEPARATOR_P, self::WORD_SEPARATOR_R, $datos['NombreEscuela']);
            $datosEnviar->query->bool->must[$mm] = new stdClass();
            $datosEnviar->query->bool->must[$mm]->query_string = new stdClass();
            $datosEnviar->query->bool->must[$mm]->query_string->default_field = 'Escuela.Nombre';
            $datosEnviar->query->bool->must[$mm]->query_string->query = $query;
            $mm++;
        }

        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            $datosEnviar->query->bool->must[$mm] = new stdClass();
            $datosEnviar->query->bool->must[$mm]->multi_match = new stdClass();
            $datosEnviar->query->bool->must[$mm]->multi_match->query = $datos['Dni'];
            $datosEnviar->query->bool->must[$mm]->multi_match->operator = 'OR';
            $datosEnviar->query->bool->must[$mm]->multi_match->fields = [
                'PersonaSaliente.Documento.Numero',
                'PersonaPropuesta.Documento.Numero',
                'PersonaDesignada.Documento.Numero'
            ];
            $mm++;
        }


        if (isset($datos['IdEstadoIgnorar']) && $datos['IdEstadoIgnorar'] != "") {
            $datosEnviar->query->bool->must_not[$mn] = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term->{'Estado.Id'} = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term->{'Estado.Id'}->value = $datos['IdEstadoIgnorar'];
            $mn++;
        }

        if (isset($datos['IdAreaIgnorar']) && $datos['IdAreaIgnorar'] != "") {
            $datosEnviar->query->bool->must_not[$mn] = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term->{'Area.Id'} = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term->{'Area.Id'}->value = $datos['IdAreaIgnorar'];
            $mn++;
        }
        if ($mm > 0)
            $sort = false;

        if ($sort) {
            $datosEnviar->sort = [];

            if (isset($datos['sort']) && is_array($datos['sort']) && count($datos['sort']) > 0) {
                foreach ($datos['sort'] as $sort) {
                    $datosEnviar->sort[$ss] = new StdClass;
                    $datosEnviar->sort[$ss]->{$sort['field']} = new StdClass;
                    $datosEnviar->sort[$ss]->{$sort['field']}->order = $sort['order'];
                    $ss++;
                }
            } else {
                $datosEnviar->sort[++$ss] = new StdClass;
                $datosEnviar->sort[$ss]->{$SortField} = new StdClass;
                $datosEnviar->sort[$ss]->{$SortField}->order = $SortOrder;
            }
        }

        $cuerpo = json_encode($datosEnviar);
        $this->cnx->setDebug(false);

        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $data, $codigoRetorno, 'track_total_hits=true')) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($data['hits'])) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }
        $total = (int)$data['hits']['total']['value'];
        $resultado = $data['hits']['hits'];
        $numfilas = count($resultado);

        return true;
    }

    /**
     * @param array $datos
     * @param array|null $resultado
     * @param int|null $numfilas
     * @param int|null $total
     * @return bool
     */
    public function BusquedaDashboard(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {

        $datosEnviar = new stdClass();
        $datosEnviar->query = new stdClass();
        $datosEnviar->query = new stdClass();
        $datosEnviar->query->bool = new stdClass();
        $datosEnviar->query->bool->must = [];
        $datosEnviar->query->bool->filter = [];
        $datosEnviar->query->bool->must_not = [];

        $SortField = 'Id';
        $SortOrder = 'desc';
        $datosEnviar->from = $datos['from'] ?? 0;
        $datosEnviar->size = $datos['size'] ?? PAGINAR;

        $sort = true;
        $ff = $mm = $ss = $mn = 0;

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.Nivel.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.Nivel.Id'}->value = (int)$datos['IdNivel'];
            $ff++;
        }


        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->term->{'Escuela.Id'}->value = (int)$datos['IdEscuela'];
            $ff++;
        }

        if (isset($datos['IdsRegion']) && $datos['IdsRegion'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Escuela.Region.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Escuela.Region.Id'} = $datos['IdsRegion'];
            $ff++;
        }

        if (isset($datos['IdsTurnos']) && $datos['IdsTurnos'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Escuela.Turno.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Escuela.Turno.Id'} = is_array($datos['IdsTurnos']) ? array_values(array_unique($datos['IdsTurnos'])) : explode(',', $datos['IdsTurnos']);
            $ff++;
        }

        if (isset($datos['IdsNiveles']) && $datos['IdsNiveles'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Escuela.Nivel.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Escuela.Nivel.Id'} = is_array($datos['IdsNiveles']) ? array_values(array_unique($datos['IdsNiveles'])) : explode(',', $datos['IdsNiveles']);
            $ff++;
        }


        if (isset($datos['IdArea']) && $datos['IdArea'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Area.Id'} = is_array($datos['IdArea']) ? $datos['IdArea'] : explode(',', $datos['IdArea']);
            $ff++;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $datosEnviar->query->bool->filter[$ff] = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms = new stdClass();
            $datosEnviar->query->bool->filter[$ff]->terms->{'Estado.Id'} = is_array($datos['IdEstado']) ? $datos['IdEstado'] : explode(',', $datos['IdEstado']);
            $ff++;
        }

        if (isset($datos['IdEstadoIgnorar']) && $datos['IdEstadoIgnorar'] != "") {
            $datosEnviar->query->bool->must_not[$mn] = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term->{'Estado.Id'} = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term->{'Estado.Id'}->value = $datos['IdEstadoIgnorar'];
            $mn++;
        }

        if (isset($datos['IdAreaIgnorar']) && $datos['IdAreaIgnorar'] != "") {
            $datosEnviar->query->bool->must_not[$mn] = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term->{'Area.Id'} = new stdClass();
            $datosEnviar->query->bool->must_not[$mn]->term->{'Area.Id'}->value = $datos['IdAreaIgnorar'];
            $mn++;
        }


        $datosEnviar->aggs = new stdClass();
        $datosEnviar->aggs->Pendientes = new stdClass();
        $datosEnviar->aggs->Pendientes->filter = new stdClass();
        $datosEnviar->aggs->Pendientes->filter->terms = new stdClass();
        $datosEnviar->aggs->Pendientes->filter->terms->{'Estado.Id'} = (!is_null(SC_PENDIENTES_DE_APROBACION) ? [SC_PENDIENTES, SC_PENDIENTES_DE_APROBACION] : [SC_PENDIENTES]);


        $datosEnviar->aggs->Pendientes->aggs = new stdClass();
        $datosEnviar->aggs->Pendientes->aggs->Datos = new stdClass();
        $datosEnviar->aggs->Pendientes->aggs->Datos->top_hits = new stdClass();
        $datosEnviar->aggs->Pendientes->aggs->Datos->top_hits->size = $datos['sizePendientes'];
        $datosEnviar->aggs->Pendientes->aggs->Datos->top_hits->sort = new stdClass();
        $datosEnviar->aggs->Pendientes->aggs->Datos->top_hits->sort->{'Id'} = new stdClass();
        $datosEnviar->aggs->Pendientes->aggs->Datos->top_hits->sort->{'Id'}->order = 'desc';

        $datosEnviar->aggs->Finalizados = new stdClass();
        $datosEnviar->aggs->Finalizados->filter = new stdClass();
        $datosEnviar->aggs->Finalizados->filter->term = new stdClass();
        $datosEnviar->aggs->Finalizados->filter->term->{'Estado.Id'} = new stdClass();
        $datosEnviar->aggs->Finalizados->filter->term->{'Estado.Id'}->value = SC_FINALIZADAS;
        $datosEnviar->aggs->Finalizados->aggs = new stdClass();
        $datosEnviar->aggs->Finalizados->aggs->Datos = new stdClass();
        $datosEnviar->aggs->Finalizados->aggs->Datos->top_hits = new stdClass();
        $datosEnviar->aggs->Finalizados->aggs->Datos->top_hits->size = $datos['sizeFinalizadas'];
        $datosEnviar->aggs->Finalizados->aggs->Datos->top_hits->sort = new stdClass();
        $datosEnviar->aggs->Finalizados->aggs->Datos->top_hits->sort->{'Id'} = new stdClass();
        $datosEnviar->aggs->Finalizados->aggs->Datos->top_hits->sort->{'Id'}->order = 'desc';

        $datosEnviar->aggs->PendientesTP = new stdClass();
        $datosEnviar->aggs->PendientesTP->filter = new stdClass();
        $datosEnviar->aggs->PendientesTP->filter->term = new stdClass();
        $datosEnviar->aggs->PendientesTP->filter->term->{'Estado.Id'} = new stdClass();
        $datosEnviar->aggs->PendientesTP->filter->term->{'Estado.Id'}->value = SC_PENDIENTES_TOMA_POSICION;

        $cuerpo = json_encode($datosEnviar);
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
        $resultado = $data;

        return true;
    }

    /**
     * @param array $datos
     * @param array|null $resultado
     * @param int|null $total
     * @return bool
     */
    public function buscarxLicencia(array $datos, ?array &$resultado, ?int &$total): bool {
        $cuerpo = (new Consultas\Base(1, 0))
			->setSource((new Consultas\Source())->addIncludes('Id', 'Estado.Id'))
            ->setQuery(Consultas\Query::bool());

        $cuerpo->getQuery()
            ->addFilter(Consultas\Query::term('Licencia.Id', $datos['IdLicencia']))
            ->addFilter(Consultas\Query::term('Puesto.Id', $datos['IdPuesto']));

        $cuerpo->getQuery()
            ->addMustNot(Consultas\Query::terms('Estado.Id', [RECHAZADO]));

        $this->cnx->setDebug(false);

        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo->toJson(), $data, $codigoRetorno)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($data['hits'])) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }

        $resultado = $data['hits']['hits'][0]['_source'] ?? [];
        $total = (int)$data['hits']['total']['value'];

        return true;
    }

    /**
     * @param array      $datos
     * @param array|null $resultado
     * @param int|null   $total
     * @return bool
     */
    public function buscarSuplenteExistente(array $datos, ?array &$resultado, ?int &$total): bool {

        # si total > 1 -> tiene suplente
        # si tiene suplente no puede volver a tramitar sc
        # y puede ver la actual (ME FALTA ID DE SC)

        # si total == 0 -> sin suplente
        # puede solicitar sc

        # para nueva versi�n de SC
        /*$datos['IdPuestoPadre'] = $datos['IdPuesto'];

        $cuerpo = (new Consultas\Base())
            ->setQuery(Consultas\Query::bool());

        $cuerpo->getQuery()
            ->addMust(Consultas\Query::term('IdPuestoPadre', $datos['IdPuestoPadre']))
            ->addMust(Consultas\Query::term('Estado',  10));

        $this->cnx->setDebug(false);

        if (!$this->cnx->sendPost(INDEXPREFIX.SUFFIX_PUESTOS, '_search', $cuerpo->toJson(), $data, $codigoRetorno)) {
            $this->setError($this->cnx->getError());
            return false;
}

        if (!isset($data['hits'])) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }*/

        $cuerpo = (new Consultas\Base())
            ->setSource((new Consultas\Source())->addIncludes('Orden'))
            ->setQuery(Consultas\Query::bool());

        $cuerpo->getQuery()
            ->addMust(Consultas\Query::term('Tipo', 'Persona'))
            ->addMust(Consultas\Query::term('_routing', $datos['IdPuesto']))
            ->addMust(Consultas\Query::term('Orden', ($datos['Orden'] + 1)));

        $this->cnx->setDebug(false);

        if (!$this->cnx->sendPost(INDEXPREFIX.SUFFIX_PUESTOS, '_search', $cuerpo->toJson(), $data, $codigoRetorno)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($data['hits'])) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }

        $resultado = $data['hits']['hits'][0]['_source'] ?? [];
        $total = (int) $data['hits']['total']['value'];

        return true;
    }
}

