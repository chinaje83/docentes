<?php

namespace Elastic;

use Elastic\Consultas\MultiMatch;
use Elastic\Consultas\Source;
use Elastic\Consultas\Booleano;
use Elastic\Consultas\Query;

//use Elastic\Consultas\;
//use Elastic\Consultas\;
use FuncionesPHPLocal;
use ManejoErrores;
use stdClass;

class Personas implements InterfaceBase {
    use ManejoErrores;

    /** @var string */
    private const INDEX = INDEXPREFIX . PERSONAS;
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

        if (empty($jsonData->settings->analysis->analyzer))
            $jsonData->settings->analysis->analyzer = new stdClass();

        $jsonData->settings->analysis->analyzer->custom_es = new stdClass();
        $jsonData->settings->analysis->analyzer->custom_es->type = 'custom';
        $jsonData->settings->analysis->analyzer->custom_es->tokenizer = 'standard';
        $jsonData->settings->analysis->analyzer->custom_es->filter = ['lowercase', 'asciifolding', 'spanish_stop', 'spanish_stemmer'];

    }

    /**
     * Estructura de dados del �ndice
     *
     * @param bool $devolverJson
     *
     * @return false|stdClass|string
     */
    public static function Estructura($devolverJson = true) {
        $jsonData = new stdClass();
        $jsonData->dynamic = 'strict';
        $jsonData->properties = new stdClass();


        $jsonData->properties->Id = new stdClass();
        $jsonData->properties->Id->type = 'integer';


        $jsonData->properties->CUIL = new stdClass();
        $jsonData->properties->CUIL->type = 'text';
        $jsonData->properties->CUIL->analyzer = 'pattern';
        $jsonData->properties->CUIL->fields = new stdClass();
        $jsonData->properties->CUIL->fields->prefix = new stdClass();
        $jsonData->properties->CUIL->fields->prefix->type = 'search_as_you_type';


        $jsonData->properties->Documento = new stdClass();
        $jsonData->properties->Documento->type = 'object';
        $jsonData->properties->Documento->properties = new stdClass();
        $jsonData->properties->Documento->properties->Tipo = new stdClass();
        $jsonData->properties->Documento->properties->Tipo->type = 'object';
        $jsonData->properties->Documento->properties->Tipo->properties = new stdClass();
        $jsonData->properties->Documento->properties->Tipo->properties->Id = new stdClass();
        $jsonData->properties->Documento->properties->Tipo->properties->Id->type = 'integer';
        $jsonData->properties->Documento->properties->Tipo->properties->Nombre = new stdClass();
        $jsonData->properties->Documento->properties->Tipo->properties->Nombre->type = 'text';
        $jsonData->properties->Documento->properties->Tipo->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->Documento->properties->Numero = new stdClass();
        $jsonData->properties->Documento->properties->Numero->type = 'text';
        $jsonData->properties->Documento->properties->Numero->analyzer = 'pattern';
        $jsonData->properties->Documento->properties->Numero->fields = new stdClass();
        $jsonData->properties->Documento->properties->Numero->fields->prefix = new stdClass();
        $jsonData->properties->Documento->properties->Numero->fields->prefix->type = 'search_as_you_type';


        $jsonData->properties->Sexo = new stdClass();
        $jsonData->properties->Sexo->type = 'object';
        $jsonData->properties->Sexo->properties = new stdClass();
        $jsonData->properties->Sexo->properties->Id = new stdClass();
        $jsonData->properties->Sexo->properties->Id->type = 'keyword';
        $jsonData->properties->Sexo->properties->Nombre = new stdClass();
        $jsonData->properties->Sexo->properties->Nombre->type = 'keyword';


        $jsonData->properties->Nombre = new stdClass();
        $jsonData->properties->Nombre->type = 'text';
        $jsonData->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->Nombre->fields = new stdClass();
        $jsonData->properties->Nombre->fields->prefix = new stdClass();
        $jsonData->properties->Nombre->fields->prefix->type = 'search_as_you_type';
        $jsonData->properties->Nombre->fields->prefix->analyzer = 'custom_es';


        $jsonData->properties->Apellido = new stdClass();
        $jsonData->properties->Apellido->type = 'text';
        $jsonData->properties->Apellido->analyzer = 'spanish';
        $jsonData->properties->Apellido->fields = new stdClass();
        $jsonData->properties->Apellido->fields->prefix = new stdClass();
        $jsonData->properties->Apellido->fields->prefix->type = 'search_as_you_type';
        $jsonData->properties->Apellido->fields->prefix->analyzer = 'custom_es';


        $jsonData->properties->NombreCompleto = new stdClass();
        $jsonData->properties->NombreCompleto->type = 'text';
        $jsonData->properties->NombreCompleto->analyzer = 'spanish';
        $jsonData->properties->NombreCompleto->fields = new stdClass();
        $jsonData->properties->NombreCompleto->fields->prefix = new stdClass();
        $jsonData->properties->NombreCompleto->fields->prefix->type = 'search_as_you_type';
        $jsonData->properties->NombreCompleto->fields->prefix->analyzer = 'custom_es';


        $jsonData->properties->Email = new stdClass();
        $jsonData->properties->Email->type = 'keyword';


        $jsonData->properties->Telefono = new stdClass();
        $jsonData->properties->Telefono->type = 'keyword';


        $jsonData->properties->UbicacionAvatar = new stdClass();
        $jsonData->properties->UbicacionAvatar->type = 'keyword';

        $jsonData->properties->FechaNacimiento = new Tipos\Fecha('strict_date||epoch_millis');


        $jsonData->properties->IdExterno = new stdClass();
        $jsonData->properties->IdExterno->type = 'integer';

        $jsonData->properties->TipoEstudio = new stdClass();
        $jsonData->properties->TipoEstudio->type = 'integer';

        $jsonData->properties->FechaAntiguedadDocente = new stdClass();
        $jsonData->properties->FechaAntiguedadDocente->type = 'date';
        $jsonData->properties->FechaAntiguedadDocente->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

        $jsonData->properties->DiasAntiguedadDocente = new stdClass();
        $jsonData->properties->DiasAntiguedadDocente->type = 'integer';

        $jsonData->properties->FechaAntiguedadAdministrativo = new stdClass();
        $jsonData->properties->FechaAntiguedadAdministrativo->type = 'date';
        $jsonData->properties->FechaAntiguedadAdministrativo->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

        $jsonData->properties->DiasAntiguedadAdministrativo = new stdClass();
        $jsonData->properties->DiasAntiguedadAdministrativo->type = 'integer';

        //objeto de antiguedades
        $jsonData->properties->Antiguedades = new stdClass();
        $jsonData->properties->Antiguedades->type = 'nested'; // o 'object' si no necesitás nested queries
        $jsonData->properties->Antiguedades->properties = new stdClass();

        // Id interno de la antigüedad
        $jsonData->properties->Antiguedades->properties->Id = new stdClass();
        $jsonData->properties->Antiguedades->properties->Id->type = 'integer';

        // Tipo de antigüedad (docente, admin, etc.)
        $jsonData->properties->Antiguedades->properties->Tipo = new stdClass();
        $jsonData->properties->Antiguedades->properties->Tipo->type = 'object';
        $jsonData->properties->Antiguedades->properties->Tipo->properties = new stdClass();

        $jsonData->properties->Antiguedades->properties->Tipo->properties->Id = new stdClass();
        $jsonData->properties->Antiguedades->properties->Tipo->properties->Id->type = 'integer';

        $jsonData->properties->Antiguedades->properties->Tipo->properties->Nombre = new stdClass();
        $jsonData->properties->Antiguedades->properties->Tipo->properties->Nombre->type = 'keyword';

        // Fechas y días de la antigüedad
        $jsonData->properties->Antiguedades->properties->FechaDesde = new stdClass();
        $jsonData->properties->Antiguedades->properties->FechaDesde->type = 'date';
        $jsonData->properties->Antiguedades->properties->FechaDesde->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

        $jsonData->properties->Antiguedades->properties->Dias = new stdClass();
        $jsonData->properties->Antiguedades->properties->Dias->type = 'integer';

        // Estado (por ej. 10 = activo, 90 = anulado)
        $jsonData->properties->Antiguedades->properties->Estado = new stdClass();
        $jsonData->properties->Antiguedades->properties->Estado->type = 'integer';

        // Importada: 0/1
        $jsonData->properties->Antiguedades->properties->Importada = new stdClass();
        $jsonData->properties->Antiguedades->properties->Importada->type = 'integer';



        $jsonData->properties->FechaIngreso = new Tipos\Fecha('strict_date||epoch_millis');
        $jsonData->properties->FallecidoFecha = new Tipos\Fecha('yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis');


        $jsonData->properties->Baja = new stdClass();
        $jsonData->properties->Baja->type = 'object';
        $jsonData->properties->Baja->properties = new stdClass();
        $jsonData->properties->Baja->properties->Fecha = new stdClass();
        $jsonData->properties->Baja->properties->Fecha->type = 'date';
        $jsonData->properties->Baja->properties->Fecha->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
        $jsonData->properties->Baja->properties->Usuario = new stdClass();
        $jsonData->properties->Baja->properties->Usuario->type = 'object';
        $jsonData->properties->Baja->properties->Usuario->properties = new stdClass();
        $jsonData->properties->Baja->properties->Usuario->properties->Id = new stdClass();
        $jsonData->properties->Baja->properties->Usuario->properties->Id->type = 'integer';
        $jsonData->properties->Baja->properties->Usuario->properties->Nombre = new stdClass();
        $jsonData->properties->Baja->properties->Usuario->properties->Nombre->type = 'keyword';
        $jsonData->properties->Baja->properties->CausaBaja = new stdClass();
        $jsonData->properties->Baja->properties->CausaBaja->type = 'integer';

        $jsonData->properties->EstadoPersona = new stdClass();
        $jsonData->properties->EstadoPersona->type = 'object';
        $jsonData->properties->EstadoPersona->properties = new stdClass();
        $jsonData->properties->EstadoPersona->properties->Id = new stdClass();
        $jsonData->properties->EstadoPersona->properties->Id->type = 'integer';
        $jsonData->properties->EstadoPersona->properties->Nombre = new stdClass();
        $jsonData->properties->EstadoPersona->properties->Nombre->type = 'keyword';
        $jsonData->properties->EstadoPersona->properties->Activo = new Tipos\Booleano();


        $jsonData->properties->Estado = new stdClass();
        $jsonData->properties->Estado->type = 'object';
        $jsonData->properties->Estado->properties = new stdClass();
        $jsonData->properties->Estado->properties->Id = new stdClass();
        $jsonData->properties->Estado->properties->Id->type = 'integer';
        $jsonData->properties->Estado->properties->Nombre = new stdClass();
        $jsonData->properties->Estado->properties->Nombre->type = 'keyword';


        $jsonData->properties->Alta = new stdClass();
        $jsonData->properties->Alta->type = 'object';
        $jsonData->properties->Alta->properties = new stdClass();
        $jsonData->properties->Alta->properties->Fecha = new stdClass();
        $jsonData->properties->Alta->properties->Fecha->type = 'date';
        $jsonData->properties->Alta->properties->Fecha->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
        $jsonData->properties->Alta->properties->Usuario = new stdClass();
        $jsonData->properties->Alta->properties->Usuario->type = 'object';
        $jsonData->properties->Alta->properties->Usuario->properties = new stdClass();
        $jsonData->properties->Alta->properties->Usuario->properties->Id = new stdClass();
        $jsonData->properties->Alta->properties->Usuario->properties->Id->type = 'integer';
        $jsonData->properties->Alta->properties->Usuario->properties->Nombre = new stdClass();
        $jsonData->properties->Alta->properties->Usuario->properties->Nombre->type = 'keyword';


        $jsonData->properties->UltimaModificacion = new stdClass();
        $jsonData->properties->UltimaModificacion->type = 'object';
        $jsonData->properties->UltimaModificacion->properties = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Fecha = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Fecha->type = 'date';
        $jsonData->properties->UltimaModificacion->properties->Fecha->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
        $jsonData->properties->UltimaModificacion->properties->Usuario = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Usuario->type = 'object';
        $jsonData->properties->UltimaModificacion->properties->Usuario->properties = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Usuario->properties->Id = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Usuario->properties->Id->type = 'integer';
        $jsonData->properties->UltimaModificacion->properties->Usuario->properties->Nombre = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Usuario->properties->Nombre->type = 'keyword';

        $jsonData->properties->EsFamiliar = new stdClass();
        $jsonData->properties->EsFamiliar->type = 'boolean';

        return $devolverJson ? json_encode($jsonData) : $jsonData;
    }

    /**
     * Estructura de datos del indice correspondiente de ES
     *
     * Devuelve el json o un objeto PHP con el contenido
     *
     * @param array $datos
     * @param bool  $encode
     *
     * @return false|stdClass|string
     */
    public static function armarDatosElastic(array $datos, $encode = false) {
        //print_r($datos);
        $jsonData = new stdClass();

        $jsonData->Id = (int)$datos['IdPersona'];

        if (!empty($datos['CUIL'])) {
            $jsonData->CUIL = $datos['CUIL'];
        }

        if (!empty($datos['DNI'])) {
            $jsonData->Documento = new stdClass();
            $jsonData->Documento->Tipo = new stdClass();
            if (!empty($datos['IdTipoDocumento']))
                $jsonData->Documento->Tipo->Id = (int)$datos['IdTipoDocumento'];
            if (!empty($datos['TipoDocumentoNombre']))
                $jsonData->Documento->Tipo->Nombre = $datos['TipoDocumentoNombre'];
            $jsonData->Documento->Numero = $datos['DNI'];
        }

        if (!empty($datos['Sexo'])) {
            $jsonData->Sexo = new stdClass();
            $jsonData->Sexo->Id = $datos['Sexo'];
            $jsonData->Sexo->Nombre = self::obtenerSexo($datos['Sexo']);
        }

        if (!empty($datos['Nombre'])) {
            $jsonData->Nombre = $datos['Nombre'];
        }

        if (!empty($datos['Apellido'])) {
            $jsonData->Apellido = $datos['Apellido'];
        }

        if (!empty($datos['NombreCompleto'])) {
            $jsonData->NombreCompleto = $datos['NombreCompleto'];
        }

        if (!empty($datos['Email'])) {
            $jsonData->Email = $datos['Email'];
        }

        if (!empty($datos['Telefono'])) {
            $jsonData->Telefono = $datos['Telefono'];
        }

        if (!empty($datos['UbicacionAvatar'])) {
            $jsonData->UbicacionAvatar = $datos['UbicacionAvatar'];
        }

        if (!empty($datos['FechaNacimiento'])) {
            $jsonData->FechaNacimiento = $datos['FechaNacimiento'];
        }

        if (!empty($datos['FechaIngreso'])) {
            $jsonData->FechaIngreso = $datos['FechaIngreso'];
        }

        if (!empty($datos['FallecidoFecha'])) {
            $jsonData->FallecidoFecha = $datos['FallecidoFecha'];
        }

        if (!empty($datos['IdExterno'])) {
            $jsonData->IdExterno = $datos['IdExterno'];
        }

        if (!empty($datos['TipoEstudio'])) {
            $jsonData->TipoEstudio = $datos['TipoEstudio'];
        }

        if (!empty($datos['FechaAntiguedadDocente'])) {
            $jsonData->FechaAntiguedadDocente = $datos['FechaAntiguedadDocente'];
        }

        if (!empty($datos['DiasAntiguedadDocente'])) {
            $jsonData->DiasAntiguedadDocente = $datos['DiasAntiguedadDocente'];
        }

        if (!empty($datos['FechaAntiguedadAdministrativo'])) {
            $jsonData->FechaAntiguedadAdministrativo = $datos['FechaAntiguedadAdministrativo'];
        }
        if (!empty($datos['DiasAntiguedadAdministrativo'])) {
            $jsonData->DiasAntiguedadAdministrativo = $datos['DiasAntiguedadAdministrativo'];
        }

        // ----------------------------------------------------
        // Antigüedades (mapping: nested Antiguedades)
        // ----------------------------------------------------
        if (!empty($datos['Antiguedades']) && is_array($datos['Antiguedades'])) {
            $jsonData->Antiguedades = [];

            foreach ($datos['Antiguedades'] as $ant) {
                $antObj = new stdClass();

                // Id interno de la antigüedad
                if (isset($ant['Id'])) {
                    $antObj->Id = (int)$ant['Id'];
                }

                // Tipo (Docente / Administrativo / etc.)
                if (!FuncionesPHPLocal::isEmpty($ant['IdAntiguedadTipo'])) {
                    $antObj->Tipo = new stdClass();
                    $antObj->Tipo->Id = (int)$ant['IdAntiguedadTipo'];
                }

                // FechaDesde
                if (!empty($ant['FechaDesde'])) {
                    $antObj->FechaDesde = $ant['FechaDesde'];
                }

                // Días de antigüedad
                if (isset($ant['Dias'])) {
                    $antObj->Dias = (int)$ant['Dias'];
                }

                // Estado (10=activo, 90=anulado, etc.)
                if (isset($ant['Estado'])) {
                    $antObj->Estado = (int)$ant['Estado'];
                }

                // Importada (0/1)
                if (isset($ant['Importada'])) {
                    $antObj->Importada = (int)$ant['Importada'];
                }

                $jsonData->Antiguedades[] = $antObj;
            }
        }
        // ----------------------------------------------------

        $jsonData->Baja = new stdClass();
        $jsonData->Baja->Fecha = null;
        if (!empty($datos['BajaFecha'])) {
            $jsonData->Baja->Fecha = $datos['BajaFecha'];
            if ($datos['BajaFecha'] == substr($datos['UltimaModificacionFecha'], 0, 10)) {
                $jsonData->Baja->Usuario = new stdClass();
                $jsonData->Baja->Usuario->Id = (int)$datos['UltimaModificacionUsuario'];
                $jsonData->Baja->Usuario->Nombre = $datos['UltimaModificacionUsuarioNombre'];
            }

            if (!empty($datos['CausaBaja'])) {
                $jsonData->Baja->CausaBaja = (int)$datos['CausaBaja'];
            }
        }

        if (!empty($datos['IdEstadoPersona'])) {
            $jsonData->EstadoPersona = new stdClass();
            $jsonData->EstadoPersona->Id = (int)$datos['IdEstadoPersona'];
            $jsonData->EstadoPersona->Nombre = $datos['NombreEstadoPersona'];
            if (isset($datos['EsActivo']))
                $jsonData->EstadoPersona->Activo = (bool)$datos['EsActivo'];
        }

        if (!empty($datos['Estado'])) {
            $jsonData->Estado = new stdClass();
            $jsonData->Estado->Id = (int)$datos['Estado'];
            $jsonData->Estado->Nombre = self::obtenerEstado($datos['Estado']);
        }

        $jsonData->Alta = new stdClass();
        $jsonData->Alta->Fecha = $datos['AltaFecha'];
        $jsonData->Alta->Usuario = new stdClass();
        $jsonData->Alta->Usuario->Id = (int)$datos['AltaUsuario'];
        $jsonData->Alta->Usuario->Nombre = $datos['AltaUsuarioNombre'];

        $jsonData->UltimaModificacion = new stdClass();
        $jsonData->UltimaModificacion->Fecha = $datos['UltimaModificacionFecha'];
        $jsonData->UltimaModificacion->Usuario = new stdClass();
        $jsonData->UltimaModificacion->Usuario->Id = (int)$datos['UltimaModificacionUsuario'];
        $jsonData->UltimaModificacion->Usuario->Nombre = $datos['UltimaModificacionUsuarioNombre'];

        $jsonData->EsFamiliar = new stdClass();
        $jsonData->EsFamiliar = (bool)$datos['EsFamiliar'];

        $jsonData = FuncionesPHPLocal::ConvertiraUtf8($jsonData);

        return $encode ? json_encode($jsonData) : $jsonData;
    }

    /**
     * @param array|stdClass $datos
     *
     * @return integer|null
     */
    public static function obtenerId($datos): ?int {

        if (is_array($datos) && isset($datos['IdPersona']))
            return (int)$datos['IdPersona'];
        if (is_object($datos) && isset($datos->Id))
            return (int)$datos->Id;
        return null;
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
     * @param string|null $letra
     *
     * @return string|null
     */
    private static function obtenerSexo(?string $letra): ?string {
        switch (strtoupper($letra)) {
            case 'M':
                return 'Masculino';
            case 'F':
                return 'Femenino';
            case 'X':
                return 'No Binario';
            default:
                return null;
        }
    }

    private static function obtenerEstado(?int $id): ?string {
        switch ($id) {
            case 10:
                return 'Activo';
            case 30:
                return 'Eliminado';
            default:
                return null;
        }
    }

    public function __destruct() {
        $this->error = [];
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
        $source = (new Source())->addIncludes('NombreCompleto', 'Nombre', 'Apellido', 'Documento.*', 'CUIL', 'Email', 'Telefono', 'Sexo.*', 'FechaNacimiento');
        $bool = (new Booleano())->addMust(
            Query::autocompletar($datos['Nombre'], ['NombreCompleto.prefix', 'Documento.Numero.prefix', 'CUIL.prefix'])
        );
        if (!empty($datos['soloActivos']))
            $bool->addMust(Query::term('EstadoPersona.Activo', true));
        $bool->addMust(Query::term('Estado.Id', ACTIVO));

        /*   //Busca personas mayores de 18
           $edadMayor = Consultas\Query::range('FechaNacimiento', ['lte' => 'now-17y/d']);

           //Busca personas sin fecha de nacimiento
           $sinFechaNacimiento = Query::bool()
               ->addMustNot(Query::exists('FechaNacimiento')
               );

           $bool->addShould($edadMayor)
               ->addShould($sinFechaNacimiento);
   */

        // Busca personas mayores de 18
        $edadMayor = Query::range('FechaNacimiento', ['lte' => 'now-17y/d']);

        // Busca personas sin fecha de nacimiento
        $sinFechaNacimiento = Query::bool()
            ->addMustNot(Query::exists('FechaNacimiento'));

        // Agrupa esas dos condiciones en un bool con minimum_should_match
        $boolEdad = Query::bool()
            ->addShould($edadMayor)
            ->addShould($sinFechaNacimiento)
            ->setMinimumShouldMatch(1);

        // Ese grupo se agrega como must
        $bool->addMust($boolEdad);

        $cuerpo = (new Consultas\Base(10))
            ->setSource($source)
            ->setQuery(Query::bool($bool))
            ->toJson();
        /*$jsonData = new stdClass();
        $jsonData->size = 10;
        $jsonData->_source = array("NombreCompleto", "Nombre", "Apellido", "Documento.*", "CUIL","Email","Telefono","Sexo.*");
        $jsonData->query = new stdClass();
        $jsonData->query->multi_match = new stdClass();
        $jsonData->query->multi_match->query = $datos['Nombre'];
        $jsonData->query->multi_match->type = 'bool_prefix';
        $jsonData->query->multi_match->fields = [
            'NombreCompleto.prefix',
            'NombreCompleto.prefix._2gram',
            'NombreCompleto.prefix._3gram',
            'Documento.Numero.prefix',
            'Documento.Numero.prefix._2gram',
            'Documento.Numero.prefix._3gram',
            'CUIL.prefix',
            'CUIL.prefix._2gram',
            'CUIL.prefix._3gram',
        ];

        $cuerpo = json_encode($jsonData);*/
        $this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado, $codigoRetorno)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($resultado['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado));
            return false;
        }
        //print_r($resultado['hits']);die;
        if ($resultado['hits']['total']['value'] < 1) {
            $this->setError(404, 'No se encuentra');
            return false;
        }
        $dataResult = $resultado['hits'];


        return true;
    }

    /**
     * Trae los datos del registro pedido
     *
     *
     * @param array      $datos
     * @param array|null $datosPersona
     *
     * @return bool
     */
    public function buscarxCodigo(array $datos, ?array &$datosPersona): bool {

        $id = self::obtenerId($datos);
        if (isset($datos['excluirCampos']))
            $id .= '?_source_excludes=' . implode(',', $datos['excluirCampos']);
        elseif (isset($datos['incluirCampos']))
            $id .= '?_source_includes=' . implode(',', $datos['incluirCampos']);

        if (!$this->cnx->sendGet(self::INDEX, '_doc', $data, $codigoRetorno, $id)) {
            $this->setError($this->cnx->getError());
            return false;
        }
        if (FuncionesPHPLocal::isEmpty($data['_source'])) {
            $this->setError(404, 'Error, no se encuentra la persona');
            return false;
        }
       // var_dump($datosPersona);die();
        $datosPersona = $data['_source'];
        return true;
    }

    /**
     * @param array      $datos
     * @param array|null $resultado
     * @param int|null   $numfilas
     *
     * @return bool
     */
    public function buscarxDni(array $datos, ?array &$resultado, ?int &$numfilas): bool {

        if (!$this->_ValidarDatosVacios_xDni($datos))
            return false;

        if (empty($datos['Dni'])) {
            $this->setError('400', 'Error, debe ingresar un DNI');
            return false;
        }

        //        $query = preg_replace(self::WORD_SEPARATOR_P, self::WORD_SEPARATOR_R, $datos['Dni']);
        $query = $datos['Dni'];


        $bool = (new Booleano())
            ->addMust(Query::query_string('Documento.Numero', $query))
            ->addMust(Query::term('Estado.Id', ACTIVO));

        //Busca personas mayores de 18
        $edadMayor = Consultas\Query::range('FechaNacimiento', ['lte' => 'now-17y/d']);

        //Busca personas sin fecha de nacimiento
        $sinFechaNacimiento = Query::bool()
            ->addMustNot(Query::exists('FechaNacimiento')
            );

        $bool->addShould($edadMayor)
            ->addShould($sinFechaNacimiento);


        $cuerpo = (new Consultas\Base(10))
            ->setQuery(Query::bool($bool))
            ->toJson();


        //	echo "$cuerpo<br/>";
        //	        $this->cnx->setDebug(true);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $data, $codigoRetorno, 'track_total_hits=true')) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($data['hits'])) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }
        $numfilas = intval($data['hits']['total']['value']);
        $resultado = $data['hits']['hits'];
        //        $resultado = FuncionesPHPLocal::ConvertiraUtf8($data['hits']['hits']);

        return true;
    }

    /**
     * @param array      $datos
     * @param array|null $resultado
     *
     * @return bool
     */
    public function buscarxCuil(array $datos, ?array &$resultado, ?int &$numfilas): bool {

        $datosEnviar = new stdClass();
        $datosEnviar->query = new stdClass();
        if (empty($datos['Cuil'])) {
            $this->setError('400', 'Error, debe ingresar un CUIL');
            return false;
        }

        $query = preg_replace(self::WORD_SEPARATOR_P, self::WORD_SEPARATOR_R, $datos['Cuil']);
        $datosEnviar->query->query_string = new StdClass;
        $datosEnviar->query->query_string->default_field = "CUIL";
        $datosEnviar->query->query_string->query = $query;

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
        $resultado = FuncionesPHPLocal::ConvertiraUtf8($data['hits']['hits']);

        return true;
    }

    public function BusquedaAvanzada(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total, ?string &$scroll_id): bool {
        $datosEnviar = new stdClass();
        $datosEnviar->query = new stdClass();
        $datosEnviar->query->bool = new stdClass();
        $datosEnviar->query->bool->must = [];
        $datosEnviar->query->bool->filter = [];

        $datosEnviar->from = $datos['from'] ?? 0;
        $datosEnviar->size = $datos['size'] ?? 20;

        $i = 0;

        $scroll = "";
        if (!empty($datos['scroll']) && preg_match("/\d+[dhms]/", $datos['scroll'])) {
            $scroll = "&scroll={$datos['scroll']}";
            unset($datos['scroll']);
        }


        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            $query = preg_replace(self::WORD_SEPARATOR_P, self::WORD_SEPARATOR_R, $datos['Dni']);
            $datosEnviar->query->bool->must[$i] = new stdClass();
            $datosEnviar->query->bool->must[$i]->query_string = new stdClass();
            $datosEnviar->query->bool->must[$i]->query_string->default_field = 'Documento.Numero';
            $datosEnviar->query->bool->must[$i]->query_string->query = $datos['Dni'];
            $i++;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $query = preg_replace(self::WORD_SEPARATOR_P, self::WORD_SEPARATOR_R, $datos['Cuil']);
            $datosEnviar->query->bool->must[$i] = new stdClass();
            $datosEnviar->query->bool->must[$i]->query_string = new stdClass();
            $datosEnviar->query->bool->must[$i]->query_string->default_field = 'CUIL';
            $datosEnviar->query->bool->must[$i]->query_string->query = $datos['Cuil'];
            $i++;
        }

        if (isset($datos['NombreCompleto']) && $datos['NombreCompleto'] != "") {
            $query = preg_replace(self::WORD_SEPARATOR_P, self::WORD_SEPARATOR_R, $datos['NombreCompleto']) . " \"{$datos['NombreCompleto']}\"~5";
            $datosEnviar->query->bool->must[$i] = new stdClass();
            $datosEnviar->query->bool->must[$i]->query_string = new stdClass();
            $datosEnviar->query->bool->must[$i]->query_string->default_field = 'NombreCompleto';
            $datosEnviar->query->bool->must[$i]->query_string->query = $query;
            $i++;
        }

        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            $datosEnviar->query->bool->must[$i] = new stdClass();
            $datosEnviar->query->bool->must[$i]->match = new stdClass();
            $datosEnviar->query->bool->must[$i]->match->{'Nombre'} = new stdClass();
            $datosEnviar->query->bool->must[$i]->match->{'Nombre'} = $datos['Nombre'];
            $i++;
        }

        if (isset($datos['Apellido']) && $datos['Apellido'] != "") {
            $datosEnviar->query->bool->must[$i] = new stdClass();
            $datosEnviar->query->bool->must[$i]->match = new stdClass();
            $datosEnviar->query->bool->must[$i]->match->{'Apellido'} = new stdClass();
            $datosEnviar->query->bool->must[$i]->match->{'Apellido'} = $datos['Apellido'];
            $i++;
        }

        if (isset($datos['Email']) && $datos['Email'] != "") {
            $datosEnviar->query->bool->must[$i] = new stdClass();
            $datosEnviar->query->bool->must[$i]->match = new stdClass();
            $datosEnviar->query->bool->must[$i]->match->{'Email'} = new stdClass();
            $datosEnviar->query->bool->must[$i]->match->{'Email'} = $datos['Email'];
            $i++;
        }

        if (isset($datos['Telefono']) && $datos['Telefono'] != "") {
            $datosEnviar->query->bool->must[$i] = new stdClass();
            $datosEnviar->query->bool->must[$i]->match = new stdClass();
            $datosEnviar->query->bool->must[$i]->match->{'Telefono'} = new stdClass();
            $datosEnviar->query->bool->must[$i]->match->{'Telefono'} = $datos['Telefono'];
            $i++;
        }

        $i = 0;

        if (isset($datos['Id']) && $datos['Id'] != "") {
            $datosEnviar->query->bool->filter[$i] = new stdClass();
            $datosEnviar->query->bool->filter[$i]->term = new stdClass();
            $datosEnviar->query->bool->filter[$i]->term->{'Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$i]->term->{'Id'}->value = $datos['Id'];
            $i++;
        }

        if (isset($datos['Sexo']) && $datos['Sexo'] != "") {
            $datosEnviar->query->bool->filter[$i] = new stdClass();
            $datosEnviar->query->bool->filter[$i]->term = new stdClass();
            $datosEnviar->query->bool->filter[$i]->term->{'Sexo.Id'} = new stdClass();
            $datosEnviar->query->bool->filter[$i]->term->{'Sexo.Id'}->value = $datos['Sexo'];
        }
        $must_not = [];
        $mn = -1;
        if (!FuncionesPHPLocal::isEmpty($datos['estadosExcluir'])) {
            $must_not[++$mn] = new stdClass();
            $must_not[$mn]->terms = new stdClass();
            $must_not[$mn]->terms->{'Estado.Id'} = is_array($datos['estadosExcluir']) ? $datos['estadosExcluir'] : explode(',', $datos['estadosExcluir']);
        }
        if (!FuncionesPHPLocal::isEmpty($must_not)) {
            $datosEnviar->query->bool->must_not = $must_not;
        }


        $cuerpo = json_encode($datosEnviar);

        //echo $cuerpo;die;
        //$this->cnx->setDebug(true);


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
        $resultado = FuncionesPHPLocal::ConvertiraUtf8($data['hits']['hits']);
        if (isset($data['_scroll_id']))
            $scroll_id = $data['_scroll_id'];

        return true;
    }

    private function _ValidarDatosVacios_xDni($datos) {
        if (!isset($datos['Dni']) || $datos['Dni'] == "") {
            $this->setError(400, 'Debe ingresar DNI');
            return false;
        }

        return true;
    }
}
