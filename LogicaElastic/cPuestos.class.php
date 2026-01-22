<?php


namespace Elastic;


use Elastic\Consultas\Query;
use Exception;
use FuncionesPHPLocal;
use ManejoErrores;
use stdClass;

class Puestos implements InterfaceBase {
    use ManejoErrores;

    /** @var string */
    private const INDEX = INDEXPREFIX . SUFFIX_PUESTOS;
    /** @var Conexion */
    private $cnx;

    /**
     * Docentes constructor.
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
        $jsonData = new stdClass();
        $jsonData->dynamic = 'strict';
        $jsonData->properties = new stdClass();

        $jsonData->properties->Id = new stdClass();
        $jsonData->properties->Id->type = 'integer';

        $jsonData->properties->IdPuestoPadre = new stdClass();
        $jsonData->properties->IdPuestoPadre->type = 'long';

        $jsonData->properties->IdPuestoRaiz = new stdClass();
        $jsonData->properties->IdPuestoRaiz->type = 'long';


        $jsonData->properties->IdPuestoOrigen = new stdClass();
        $jsonData->properties->IdPuestoOrigen->type = 'integer';

        $jsonData->properties->Estado = new stdClass();
        $jsonData->properties->Estado->type = 'short';

        $jsonData->properties->AdmiteSuplente = new Tipos\Booleano();

        $jsonData->properties->Conflictos = new Tipos\Objeto();
        $jsonData->properties->Conflictos->Desempenos = new Tipos\Booleano();
        $jsonData->properties->Conflictos->CantidadHorasModulos = new Tipos\Booleano();


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque puesto                                       *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->properties->Codigo = new stdClass();
        $jsonData->properties->Codigo->type = 'keyword';

        $jsonData->properties->IdPlaza = new stdClass();
        $jsonData->properties->IdPlaza->type = 'keyword';

        $jsonData->properties->CodigoPuesto = new stdClass();
        $jsonData->properties->CodigoPuesto->type = 'keyword';


        $jsonData->properties->CargaManual = new stdClass();
        $jsonData->properties->CargaManual->type = 'boolean';

        $jsonData->properties->IdEscuelaTurno = new stdClass();
        $jsonData->properties->IdEscuelaTurno->type = 'integer';

        $jsonData->properties->IdTipo = new stdClass();
        $jsonData->properties->IdTipo->type = 'integer';

        $jsonData->properties->IdEscalafon = new stdClass();
        $jsonData->properties->IdEscalafon->type = 'integer';

        $jsonData->properties->DesempenoLugar = new stdClass();
        $jsonData->properties->DesempenoLugar->type = 'integer';

        $jsonData->properties->Fechas = new Tipos\Objeto();
        $jsonData->properties->Fechas->Desde = new Tipos\Fecha('strict_date||epoch_millis');
        $jsonData->properties->Fechas->Hasta = new Tipos\Fecha('strict_date||epoch_millis');

        $jsonData->properties->Regimen = new stdClass();
        $jsonData->properties->Regimen->type = 'object';
        $jsonData->properties->Regimen->properties = new stdClass();
        $jsonData->properties->Regimen->properties->IdRegimenSalarial = new stdClass();
        $jsonData->properties->Regimen->properties->IdRegimenSalarial->type = 'integer';
        $jsonData->properties->Regimen->properties->NombreRegimenSalarial = new stdClass();
        $jsonData->properties->Regimen->properties->NombreRegimenSalarial->type = 'text';
        $jsonData->properties->Regimen->properties->NombreRegimenSalarial->analyzer = 'spanish';
        $jsonData->properties->Regimen->properties->ComputaCargos = new stdClass();
        $jsonData->properties->Regimen->properties->ComputaCargos->type = 'integer';

        $jsonData->properties->Materia = new stdClass();
        $jsonData->properties->Materia->type = 'object';
        $jsonData->properties->Materia->properties = new stdClass();
        $jsonData->properties->Materia->properties->Id = new stdClass();
        $jsonData->properties->Materia->properties->Id->type = 'integer';
        $jsonData->properties->Materia->properties->Codigo = new stdClass();
        $jsonData->properties->Materia->properties->Codigo->type = 'keyword';
        $jsonData->properties->Materia->properties->Descripcion = new stdClass();
        $jsonData->properties->Materia->properties->Descripcion->type = 'text';
        $jsonData->properties->Materia->properties->Descripcion->analyzer = 'spanish';
        $jsonData->properties->Materia->properties->AdmiteSuplente = new Tipos\Booleano();

        $jsonData->properties->Cargo = new stdClass();
        $jsonData->properties->Cargo->type = 'object';
        $jsonData->properties->Cargo->properties = new stdClass();
        $jsonData->properties->Cargo->properties->Tipo = new stdClass();
        $jsonData->properties->Cargo->properties->Tipo->properties = new stdClass();
        $jsonData->properties->Cargo->properties->Tipo->properties->Id = new stdClass();
        $jsonData->properties->Cargo->properties->Tipo->properties->Id->type = 'integer';
        $jsonData->properties->Cargo->properties->Tipo->properties->Descripcion = new stdClass();
        $jsonData->properties->Cargo->properties->Tipo->properties->Descripcion->type = 'text';
        $jsonData->properties->Cargo->properties->Tipo->properties->Descripcion->analyzer = 'spanish';
        $jsonData->properties->Cargo->properties->Id = new stdClass();
        $jsonData->properties->Cargo->properties->Id->type = 'integer';
        $jsonData->properties->Cargo->properties->Codigo = new stdClass();
        $jsonData->properties->Cargo->properties->Codigo->type = 'keyword';
        $jsonData->properties->Cargo->properties->Descripcion = new stdClass();
        $jsonData->properties->Cargo->properties->Descripcion->type = 'text';
        $jsonData->properties->Cargo->properties->Descripcion->analyzer = 'spanish';
        $jsonData->properties->Cargo->properties->AdmiteSuplente = new Tipos\Booleano();
        $jsonData->properties->Cargo->properties->Jerarquico = new Tipos\Booleano();
        $jsonData->properties->Cargo->properties->IdTipo = new stdClass();
        $jsonData->properties->Cargo->properties->IdTipo->type = 'integer';
        $jsonData->properties->Cargo->properties->IdEscalafon = new stdClass();
        $jsonData->properties->Cargo->properties->IdEscalafon->type = 'integer';
        $jsonData->properties->Cargo->properties->DesempenoLugar = new stdClass();
        $jsonData->properties->Cargo->properties->DesempenoLugar->type = 'integer';

        $jsonData->properties->Cargo->properties->Regimen = new stdClass();
        $jsonData->properties->Cargo->properties->Regimen->properties = new stdClass();
        $jsonData->properties->Cargo->properties->Regimen->properties->IdRegimenSalarial = new stdClass();
        $jsonData->properties->Cargo->properties->Regimen->properties->IdRegimenSalarial->type = 'integer';
        $jsonData->properties->Cargo->properties->Regimen->properties->NombreRegimenSalarial = new stdClass();
        $jsonData->properties->Cargo->properties->Regimen->properties->NombreRegimenSalarial->type = 'text';
        $jsonData->properties->Cargo->properties->Regimen->properties->NombreRegimenSalarial->analyzer = 'spanish';
        $jsonData->properties->Cargo->properties->Regimen->properties->ComputaCargos = new stdClass();
        $jsonData->properties->Cargo->properties->Regimen->properties->ComputaCargos->type = 'integer';

        $jsonData->properties->Cargo->properties->GrupoOcupacional = new stdClass();
        $jsonData->properties->Cargo->properties->GrupoOcupacional->properties = new stdClass();
        $jsonData->properties->Cargo->properties->GrupoOcupacional->properties->IdGrupo = new stdClass();
        $jsonData->properties->Cargo->properties->GrupoOcupacional->properties->IdGrupo->type = 'integer';
        $jsonData->properties->Cargo->properties->GrupoOcupacional->properties->IdSubGrupo = new stdClass();
        $jsonData->properties->Cargo->properties->GrupoOcupacional->properties->IdSubGrupo->type = 'integer';

        $jsonData->properties->Turno = new stdClass();
        $jsonData->properties->Turno->type = 'object';
        $jsonData->properties->Turno->properties = new stdClass();
        $jsonData->properties->Turno->properties->Id = new stdClass();
        $jsonData->properties->Turno->properties->Id->type = 'integer';
        $jsonData->properties->Turno->properties->Descripcion = new stdClass();
        $jsonData->properties->Turno->properties->Descripcion->type = 'text';
        $jsonData->properties->Turno->properties->Descripcion->analyzer = 'spanish';
        $jsonData->properties->Turno->properties->NombreCorto = new stdClass();
        $jsonData->properties->Turno->properties->NombreCorto->type = 'text';
        $jsonData->properties->Turno->properties->NombreCorto->analyzer = 'spanish';

        $jsonData->properties->GradoAnio = new stdClass();
        $jsonData->properties->GradoAnio->type = 'object';
        $jsonData->properties->GradoAnio->properties = new stdClass();
        $jsonData->properties->GradoAnio->properties->Id = new stdClass();
        $jsonData->properties->GradoAnio->properties->Id->type = 'integer';
        $jsonData->properties->GradoAnio->properties->Descripcion = new stdClass();
        $jsonData->properties->GradoAnio->properties->Descripcion->type = 'text';
        $jsonData->properties->GradoAnio->properties->Descripcion->analyzer = 'spanish';
        $jsonData->properties->GradoAnio->properties->NombreCorto = new stdClass();
        $jsonData->properties->GradoAnio->properties->NombreCorto->type = 'text';
        $jsonData->properties->GradoAnio->properties->NombreCorto->analyzer = 'spanish';

        $jsonData->properties->SeccionDivision = new stdClass();
        $jsonData->properties->SeccionDivision->type = 'object';
        $jsonData->properties->SeccionDivision->properties = new stdClass();
        $jsonData->properties->SeccionDivision->properties->Id = new stdClass();
        $jsonData->properties->SeccionDivision->properties->Id->type = 'integer';
        $jsonData->properties->SeccionDivision->properties->Descripcion = new stdClass();
        $jsonData->properties->SeccionDivision->properties->Descripcion->type = 'text';
        $jsonData->properties->SeccionDivision->properties->Descripcion->analyzer = 'spanish';

        $jsonData->properties->Horas = new stdClass();
        $jsonData->properties->Horas->type = 'short';

        $jsonData->properties->Modulos = new stdClass();
        $jsonData->properties->Modulos->type = 'short';

        $jsonData->properties->Ciclo = new stdClass();
        $jsonData->properties->Ciclo->type = 'object';
        $jsonData->properties->Ciclo->properties = new stdClass();
        $jsonData->properties->Ciclo->properties->Id = new stdClass();
        $jsonData->properties->Ciclo->properties->Id->type = 'integer';
        $jsonData->properties->Ciclo->properties->Descripcion = new stdClass();
        $jsonData->properties->Ciclo->properties->Descripcion->type = 'text';
        $jsonData->properties->Ciclo->properties->Descripcion->analyzer = 'spanish';

        $jsonData->properties->Nivel = new stdClass();
        $jsonData->properties->Nivel->type = 'object';
        $jsonData->properties->Nivel->properties = new stdClass();
        $jsonData->properties->Nivel->properties->Id = new stdClass();
        $jsonData->properties->Nivel->properties->Id->type = 'integer';
        $jsonData->properties->Nivel->properties->Descripcion = new stdClass();
        $jsonData->properties->Nivel->properties->Descripcion->type = 'text';
        $jsonData->properties->Nivel->properties->Descripcion->analyzer = 'spanish';

        $jsonData->properties->Modalidad = new stdClass();
        $jsonData->properties->Modalidad->type = 'object';
        $jsonData->properties->Modalidad->properties = new stdClass();
        $jsonData->properties->Modalidad->properties->Id = new stdClass();
        $jsonData->properties->Modalidad->properties->Id->type = 'integer';
        $jsonData->properties->Modalidad->properties->Descripcion = new stdClass();
        $jsonData->properties->Modalidad->properties->Descripcion->type = 'text';
        $jsonData->properties->Modalidad->properties->Descripcion->analyzer = 'spanish';

        $jsonData->properties->NivelModalidad = new stdClass();
        $jsonData->properties->NivelModalidad->type = 'object';
        $jsonData->properties->NivelModalidad->properties = new stdClass();
        $jsonData->properties->NivelModalidad->properties->Id = new stdClass();
        $jsonData->properties->NivelModalidad->properties->Id->type = 'keyword';

        $jsonData->properties->CicloLectivo = new stdClass();
        $jsonData->properties->CicloLectivo->type = 'short';

        $jsonData->properties->Escuela = new stdClass();
        $jsonData->properties->Escuela->type = 'object';
        $jsonData->properties->Escuela->properties = new stdClass();
        $jsonData->properties->Escuela->properties->Id = new stdClass();
        $jsonData->properties->Escuela->properties->Id->type = 'integer';
        $jsonData->properties->Escuela->properties->Nombre = new stdClass();
        $jsonData->properties->Escuela->properties->Nombre->type = 'text';
        $jsonData->properties->Escuela->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->Escuela->properties->Codigo = new stdClass();
        $jsonData->properties->Escuela->properties->Codigo->type = 'keyword';
        $jsonData->properties->Escuela->properties->CUE = new stdClass();
        $jsonData->properties->Escuela->properties->CUE->type = 'keyword';
        $jsonData->properties->Escuela->properties->Anexo = new stdClass();
        $jsonData->properties->Escuela->properties->Anexo->type = 'boolean';

        $jsonData->properties->Escuela->properties->Region = new stdClass();
        $jsonData->properties->Escuela->properties->Region->type = 'object';
        $jsonData->properties->Escuela->properties->Region->properties = new stdClass();

        $jsonData->properties->Escuela->properties->Region->properties->Id = new stdClass();
        $jsonData->properties->Escuela->properties->Region->properties->Id->type = 'integer';

        $jsonData->properties->Escuela->properties->Region->properties->Nombre = new stdClass();
        $jsonData->properties->Escuela->properties->Region->properties->Nombre->type = 'keyword';
        $jsonData->properties->Escuela->properties->Region->properties->Nombre->index = false;

        $jsonData->properties->Escuela->properties->Localidad = new stdClass();
        $jsonData->properties->Escuela->properties->Localidad->properties = new stdClass();
        $jsonData->properties->Escuela->properties->Localidad->properties->Id = new Tipos\Entero();
        $jsonData->properties->Escuela->properties->Localidad->properties->Nombre = new Tipos\Keyword();



        $jsonData->properties->SolicitudAbierta = new Tipos\Objeto();
        $jsonData->properties->SolicitudAbierta->Id = new Tipos\Entero();
        $jsonData->properties->SolicitudAbierta->Area = new Tipos\Objeto();
        $jsonData->properties->SolicitudAbierta->Area->Id = new Tipos\Entero();
        $jsonData->properties->SolicitudAbierta->Area->Nombre = new Tipos\Keyword();
        $jsonData->properties->SolicitudAbierta->Area->Nombre->noIndexar();
        $jsonData->properties->SolicitudAbierta->Estado = new Tipos\Objeto();
        $jsonData->properties->SolicitudAbierta->Estado->Id = new Tipos\Entero();
        $jsonData->properties->SolicitudAbierta->Estado->Nombre = new Tipos\Keyword();
        $jsonData->properties->SolicitudAbierta->Estado->Nombre->noIndexar();


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque desempe�o                                    *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->properties->Dia = new stdClass();
        $jsonData->properties->Dia->type = 'object';
        $jsonData->properties->Dia->properties = new stdClass();
        $jsonData->properties->Dia->properties->Numero = new stdClass();
        $jsonData->properties->Dia->properties->Numero->type = 'integer';
        $jsonData->properties->Dia->properties->Descripcion = new stdClass();
        $jsonData->properties->Dia->properties->Descripcion->type = 'keyword';

        $jsonData->properties->Horario = new stdClass();
        $jsonData->properties->Horario->type = 'date_range';
        $jsonData->properties->Horario->format = 'strict_hour_minute||epoch_millis';


        $jsonData->properties->HoraDesde = new stdClass();
        $jsonData->properties->HoraDesde->type = 'date';
        $jsonData->properties->HoraDesde->format = 'strict_hour_minute||epoch_millis';

        $jsonData->properties->HoraHasta = new stdClass();
        $jsonData->properties->HoraHasta->type = 'date';
        $jsonData->properties->HoraHasta->format = 'strict_hour_minute||epoch_millis';


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque persona                                      *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->properties->NroResolucion = new Tipos\Keyword();

        $jsonData->properties->IdPersona = new stdClass();
        $jsonData->properties->IdPersona->type = 'integer';

        $jsonData->properties->IdPofaMigracion = new Tipos\Keyword();
        $jsonData->properties->IdPofaOrigen = new Tipos\Keyword();

        $jsonData->properties->TieneConflictos = new Tipos\Booleano();


        $jsonData->properties->IdExcepcionTipo = new Tipos\Keyword();
        $jsonData->properties->ExcepcionNombre = new stdClass();
        $jsonData->properties->ExcepcionNombre->type = 'keyword';


        $jsonData->properties->Nombre = new stdClass();
        $jsonData->properties->Nombre->type = 'keyword';
        $jsonData->properties->Nombre->fields = new stdClass();
        $jsonData->properties->Nombre->fields->prefix = new stdClass();
        $jsonData->properties->Nombre->fields->prefix->type = 'search_as_you_type';
        $jsonData->properties->Nombre->fields->prefix->analyzer = 'custom_es';


        $jsonData->properties->Apellido = new stdClass();
        $jsonData->properties->Apellido->type = 'keyword';
        $jsonData->properties->Apellido->fields = new stdClass();
        $jsonData->properties->Apellido->fields->prefix = new stdClass();
        $jsonData->properties->Apellido->fields->prefix->type = 'search_as_you_type';
        $jsonData->properties->Apellido->fields->prefix->analyzer = 'custom_es';

        $jsonData->properties->Documento = new stdClass();
        $jsonData->properties->Documento->type = 'object';
        $jsonData->properties->Documento->properties = new stdClass();
        $jsonData->properties->Documento->properties->Tipo = new stdClass();
        $jsonData->properties->Documento->properties->Tipo->properties = new stdClass();
        $jsonData->properties->Documento->properties->Tipo->properties->Id = new stdClass();
        $jsonData->properties->Documento->properties->Tipo->properties->Id->type = 'integer';
        $jsonData->properties->Documento->properties->Tipo->properties->Descripcion = new stdClass();
        $jsonData->properties->Documento->properties->Tipo->properties->Descripcion->type = 'keyword';
        $jsonData->properties->Documento->properties->Numero = new stdClass();
        $jsonData->properties->Documento->properties->Numero->type = 'keyword';
        $jsonData->properties->Documento->properties->Numero->fields = new stdClass();
        $jsonData->properties->Documento->properties->Numero->fields->prefix = new stdClass();
        $jsonData->properties->Documento->properties->Numero->fields->prefix->type = 'search_as_you_type';
        $jsonData->properties->Documento->properties->Numero->fields->prefix->analyzer = 'pattern';

        $jsonData->properties->Sexo = new stdClass();
        $jsonData->properties->Sexo->type = 'object';
        $jsonData->properties->Sexo->properties = new stdClass();
        $jsonData->properties->Sexo->properties->Id = new stdClass();
        $jsonData->properties->Sexo->properties->Id->type = 'integer';
        $jsonData->properties->Sexo->properties->Descripcion = new stdClass();
        $jsonData->properties->Sexo->properties->Descripcion->type = 'keyword';

        $jsonData->properties->CUIL = new stdClass();
        $jsonData->properties->CUIL->type = 'keyword';

        $jsonData->properties->Revista = new stdClass();
        $jsonData->properties->Revista->type = 'object';
        $jsonData->properties->Revista->properties = new stdClass();
        $jsonData->properties->Revista->properties->Id = new stdClass();
        $jsonData->properties->Revista->properties->Id->type = 'integer';
        $jsonData->properties->Revista->properties->Codigo = new stdClass();
        $jsonData->properties->Revista->properties->Codigo->type = 'keyword';
        $jsonData->properties->Revista->properties->Descripcion = new stdClass();
        $jsonData->properties->Revista->properties->Descripcion->type = 'keyword';

        $jsonData->properties->CodigoLiquidador = new stdClass();
        $jsonData->properties->CodigoLiquidador->type = 'keyword';

        $jsonData->properties->FechaDesignacion = new stdClass();
        $jsonData->properties->FechaDesignacion->type = 'date';
        $jsonData->properties->FechaDesignacion->format = 'strict_date||epoch_millis';

        $jsonData->properties->FechaTomaPosesion = new stdClass();
        $jsonData->properties->FechaTomaPosesion->type = 'date';
        $jsonData->properties->FechaTomaPosesion->format = 'strict_date||epoch_millis';

        $jsonData->properties->FechaHastaPosesion = new stdClass();
        $jsonData->properties->FechaHastaPosesion->type = 'date';
        $jsonData->properties->FechaHastaPosesion->format = 'strict_date||epoch_millis';

        $jsonData->properties->ExtiendeSuplencia = new stdClass();
        $jsonData->properties->ExtiendeSuplencia->type = 'object';
        $jsonData->properties->ExtiendeSuplencia->properties = new stdClass();
        $jsonData->properties->ExtiendeSuplencia->properties->Id = new stdClass();
        $jsonData->properties->ExtiendeSuplencia->properties->Id->type = 'integer';


        $jsonData->properties->Orden = new stdClass();
        $jsonData->properties->Orden->type = 'byte';

        $jsonData->properties->EstadoPersona = new stdClass();
        $jsonData->properties->EstadoPersona->properties = new stdClass();
        $jsonData->properties->EstadoPersona->properties->Id = new stdClass();
        $jsonData->properties->EstadoPersona->properties->Id->type = 'integer';
        $jsonData->properties->EstadoPersona->properties->Descripcion = new stdClass();
        $jsonData->properties->EstadoPersona->properties->Descripcion->type = 'keyword';
        $jsonData->properties->EstadoPersona->properties->Codigo = new stdClass();
        $jsonData->properties->EstadoPersona->properties->Codigo->type = 'keyword';



        $jsonData->properties->EstadoPersona->properties->Licencia = new Tipos\Nested();
        $jsonData->properties->EstadoPersona->properties->Licencia->Id = new Tipos\EnteroLargo();
        $jsonData->properties->EstadoPersona->properties->Licencia->Estado = new Tipos\EnteroLargo();
        $jsonData->properties->EstadoPersona->properties->Licencia->Fechas = new Tipos\RangoFecha('strict_date||epoch_millis');
        $jsonData->properties->EstadoPersona->properties->Licencia->FechaHastaEstimada = new Tipos\Fecha('strict_date||epoch_millis');
        $jsonData->properties->EstadoPersona->properties->Licencia->Motivo = new Tipos\Keyword();
        $jsonData->properties->EstadoPersona->properties->Licencia->IdMotivo = new Tipos\EnteroLargo();
        $jsonData->properties->EstadoPersona->properties->Licencia->Prioridad = new Tipos\EnteroCorto();
        $jsonData->properties->EstadoPersona->properties->Licencia->Articulo = new Tipos\Objeto();
        $jsonData->properties->EstadoPersona->properties->Licencia->Articulo->Id = new Tipos\Keyword();
        $jsonData->properties->EstadoPersona->properties->Licencia->Articulo->Codigo = new Tipos\Keyword();
        $jsonData->properties->EstadoPersona->properties->Licencia->Articulo->Descripcion = new Tipos\Texto('spanish');
        $jsonData->properties->EstadoPersona->properties->Licencia->Pendiente = new Tipos\Booleano();
        $jsonData->properties->EstadoPersona->properties->Licencia->FechaFinAbierta = new Tipos\Booleano();
        $jsonData->properties->EstadoPersona->properties->Licencia->FechaReintegro = new Tipos\Fecha('strict_date||epoch_millis');




        /* **************************************************************************** */

        $jsonData->properties->Tipo = new stdClass();
        $jsonData->properties->Tipo->type = 'join';
        $jsonData->properties->Tipo->relations = new stdClass();
        $jsonData->properties->Tipo->relations->Puesto = ['Desempeno', 'Persona'];


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

        return $devolverJson ? json_encode($jsonData) : $jsonData;

    }

    /**
     *
     * @param array $datos
     * @param bool  $encode
     *
     * @return array|false|object|string
     * @throws Exception
     */
    public static function armarDatosElastic($datos, $encode = false) {
        $jsonData = new stdClass();

        $jsonData->Id = $datos['Id'];
        $jsonData->Estado = $datos['Estado'];

        switch ($datos['Tipo']) {
            case 'Puesto':
                self::_armarDatosPuestos($datos, $jsonData);
                break;
            case 'Desempeno':
                self::_armarDatosDesempeno($datos, $jsonData);
                break;
            case 'Persona':
                self::_armarDatosPersona($datos, $jsonData);
                break;
            default:
                throw new Exception('Error, no existe el tipo.');
        }


        $jsonData->Alta = new stdClass();
        if (!FuncionesPHPLocal::isEmpty($datos['AltaFecha']))
            $jsonData->Alta->Fecha = $datos['AltaFecha'];
        $jsonData->Alta->Usuario = new stdClass();
        if (!FuncionesPHPLocal::isEmpty($datos['AltaUsuario']))
            $jsonData->Alta->Usuario->Id = (int)$datos['AltaUsuario'];
        if (!FuncionesPHPLocal::isEmpty($datos['AltaUsuarioNombre']))
            $jsonData->Alta->Usuario->Nombre = $datos['AltaUsuarioNombre'];

        $jsonData->UltimaModificacion = new stdClass();
        if (!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionFecha']))
            $jsonData->UltimaModificacion->Fecha = $datos['UltimaModificacionFecha'];
        $jsonData->UltimaModificacion->Usuario = new stdClass();
        if (!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionUsuario']))
            $jsonData->UltimaModificacion->Usuario->Id = (int)$datos['UltimaModificacionUsuario'];
        if (!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionUsuarioNombre']))
            $jsonData->UltimaModificacion->Usuario->Nombre = $datos['UltimaModificacionUsuarioNombre'];

        $jsonData = FuncionesPHPLocal::ConvertiraUtf8($jsonData);

        return $encode ? json_encode($jsonData) : $jsonData;
    }

    /**
     * @param array  $datos
     * @param object $jsonData
     */
    private static function _armarDatosPuestos(array $datos, object &$jsonData): void {
        $jsonData->Tipo = new stdClass();
        $jsonData->Tipo->name = 'Puesto';

        if (!FuncionesPHPLocal::isEmpty($datos['IdPuestoPadre']))
            $jsonData->IdPuestoPadre = (int)$datos['IdPuestoPadre'];

        if (!FuncionesPHPLocal::isEmpty($datos['IdPuestoRaiz']))
            $jsonData->IdPuestoRaiz = (int)$datos['IdPuestoRaiz'];

        if (!FuncionesPHPLocal::isEmpty($datos['IdPuestoOrigen']))
            $jsonData->IdPuestoOrigen = (int)$datos['IdPuestoOrigen'];

        $jsonData->AdmiteSuplente = true;
        if (!FuncionesPHPLocal::isEmpty($datos['AdmiteSuplente']))
            $jsonData->AdmiteSuplente = (bool)$datos['AdmiteSuplente'];


        $jsonData->Codigo = $datos['Codigo'];
        if (isset($datos['CodigoPuesto']) && $datos['CodigoPuesto'] != "")
            $jsonData->CodigoPuesto = $datos['CodigoPuesto'];
        $jsonData->IdEscuelaTurno = (int)$datos['IdEscuelaTurno'];

        if (isset($datos['IdPuestoMigracion']) && $datos['IdPuestoMigracion'] != "")
            $jsonData->IdPlaza = $datos['IdPuestoMigracion'];

        if (!FuncionesPHPLocal::isEmpty($datos['NroResolucion']))
            $jsonData->NroResolucion = $datos['NroResolucion'];

        if (!FuncionesPHPLocal::isEmpty($datos['IdTipo']))
            $jsonData->IdTipo = (int)$datos['IdTipo'];
        if (!FuncionesPHPLocal::isEmpty($datos['DesempenoLugar']))
            $jsonData->DesempenoLugar = (int)$datos['DesempenoLugar'];
        if (!FuncionesPHPLocal::isEmpty($datos['IdEscalafon']))
            $jsonData->IdEscalafon = (int)$datos['IdEscalafon'];

        if (!FuncionesPHPLocal::isEmpty($datos["IdRegimenSalarialPuesto"])){
            $jsonData->Regimen = new stdClass();
            $jsonData->Regimen->IdRegimenSalarial = $datos['IdRegimenSalarialPuesto'];
            $jsonData->Regimen->NombreRegimenSalarial = $datos['NombreRegimenSalarialPuesto'];
            $jsonData->Regimen->ComputaCargos = $datos['ComputaCargosRegimenSalarialPuesto'];
        }

        $jsonData->CargaManual = false;
        if (isset($datos['CargaManual']) && $datos['CargaManual'] != "")
            $jsonData->CargaManual = (bool)$datos['CargaManual'];

        $jsonData->Fechas = new stdClass();
        $jsonData->Fechas->Desde = FuncionesPHPLocal::isEmpty($datos['FechaDesde']) ?
            NULL : $datos['FechaDesde'];
        $jsonData->Fechas->Hasta = FuncionesPHPLocal::isEmpty($datos['FechaHasta']) ?
            NULL : $datos['FechaHasta'];


        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $jsonData->Materia = new stdClass();
            $jsonData->Materia->Id = (int)$datos['IdMateria'];
            $jsonData->Materia->Codigo = $datos['MateriaCodigo'];
            $jsonData->Materia->Descripcion = $datos['MateriaNombre'];
            $jsonData->Materia->AdmiteSuplente = (bool)$datos['MateriaAdmiteSuplente'];
        }
        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $jsonData->Cargo = new stdClass();
            $jsonData->Cargo->Tipo = new stdClass();
            $jsonData->Cargo->Tipo->Id = (int)$datos['CargoTipo'];
            $jsonData->Cargo->Tipo->Descripcion = $datos['CargoTipoNombre'];
            $jsonData->Cargo->Id = (int)$datos['IdCargo'];
            $jsonData->Cargo->Codigo = $datos['CargoCodigo'];
            $jsonData->Cargo->Descripcion = $datos['CargoDescripcion'];
            $jsonData->Cargo->AdmiteSuplente = (bool)$datos['CargoAdmiteSuplente'];
            $jsonData->Cargo->Jerarquico = (bool)$datos['CargoJerarquico'];

            $jsonData->Cargo->IdTipo = (int)$datos['CargoIdTipo'];
            $jsonData->Cargo->IdEscalafon = (int)$datos['CargoIdEscalafon'];
            $jsonData->Cargo->DesempenoLugar = (int)$datos['CargoDesempenoLugar'];

            $jsonData->Cargo->Regimen = new stdClass();
            $jsonData->Cargo->Regimen->IdRegimenSalarial = $datos['IdRegimenSalarial'];
            if(!FuncionesPHPLocal::isEmpty($datos['NombreRegimenSalarial']))
                $jsonData->Cargo->Regimen->NombreRegimenSalarial = $datos['NombreRegimenSalarial'];
            if(!FuncionesPHPLocal::isEmpty($datos['ComputaCargosRegimenSalarial']))
                $jsonData->Cargo->Regimen->ComputaCargos = $datos['ComputaCargosRegimenSalarial'];


            if(!FuncionesPHPLocal::isEmpty($datos['IdGrupo'])) {
                $jsonData->Cargo->GrupoOcupacional = new stdClass();
                $jsonData->Cargo->GrupoOcupacional->IdGrupo = new stdClass();
                $jsonData->Cargo->GrupoOcupacional->IdGrupo = (int)$datos['IdGrupo'];
            }
            if(!FuncionesPHPLocal::isEmpty($datos['IdSubGrupo'])) {
                $jsonData->Cargo->GrupoOcupacional->IdSubGrupo = new stdClass();
                $jsonData->Cargo->GrupoOcupacional->IdSubGrupo = (int)$datos['IdSubGrupo'];
            }

        }
        if (isset($datos['IdTurno']) && $datos['IdTurno'] != "") {
            $jsonData->Turno = new stdClass();
            $jsonData->Turno->Id = (int)$datos['IdTurno'];
            $jsonData->Turno->Descripcion = $datos['TurnoDescripcion'];
            $jsonData->Turno->NombreCorto = $datos['TurnoNombreCorto'];
        }

        if (isset($datos['IdGradoAnio']) && $datos['IdGradoAnio'] != "") {
            $jsonData->GradoAnio = new stdClass();
            $jsonData->GradoAnio->Id = (int)$datos['IdGradoAnio'];
            $jsonData->GradoAnio->Descripcion = $datos['GradoNombre'];
            $jsonData->GradoAnio->NombreCorto = $datos['GradoNombreCorto'];
        }

        if (isset($datos['IdSeccion']) && $datos['IdSeccion'] != "") {
            $jsonData->SeccionDivision = new stdClass();
            $jsonData->SeccionDivision->Id = (int)$datos['IdSeccion'];
            $jsonData->SeccionDivision->Descripcion = $datos['SeccionNombre'];
        }

        if (isset($datos['CantHoras']) && $datos['CantHoras'] != "") {
            $jsonData->Horas = (int)$datos['CantHoras'];
        }
        if (isset($datos['CantModulos']) && $datos['CantModulos'] != "") {
            $jsonData->Modulos = (int)$datos['CantModulos'];
        }



        if (isset($datos['IdCiclo']) && $datos['IdCiclo'] != "") {
            $jsonData->Ciclo = new stdClass();
            $jsonData->Ciclo->Id = (int)$datos['IdCiclo'];
            $jsonData->Ciclo->Descripcion = $datos['CicloNombre'];
        }

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $jsonData->Nivel = new stdClass();
            $jsonData->Nivel->Id = (int)$datos['IdNivel'];
            $jsonData->Nivel->Descripcion = $datos['NivelNombre'];
        }
        if (isset($datos['IdModalidad']) && $datos['IdModalidad'] != "") {
            $jsonData->Modalidad = new stdClass();
            $jsonData->Modalidad->Id = (int)$datos['IdModalidad'];
            $jsonData->Modalidad->Descripcion = $datos['ModalidadNombre'];
        }

        if (isset($datos['IdNivelModalidad']) && $datos['IdNivelModalidad'] != "") {
            $jsonData->NivelModalidad = new stdClass();
            $jsonData->NivelModalidad->Id = (int)$datos['IdNivelModalidad'];
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $jsonData->Escuela = new stdClass();
            $jsonData->Escuela->Id = (int)$datos['IdEscuela'];
            $jsonData->Escuela->Nombre = $datos['EscuelaNombre'];
            $jsonData->Escuela->Codigo = $datos['EscuelaCodigo'];
            $jsonData->Escuela->CUE = $datos['CUE'];
            $jsonData->Escuela->Anexo = (bool)$datos['EscuelaAnexo'];

            if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
                $jsonData->Escuela->Region = new stdClass();
                $jsonData->Escuela->Region->Id = (int)$datos['IdRegion'];
                $jsonData->Escuela->Region->Nombre = $datos['RegionNombre'];
            }

            if (isset($datos['IdLocalidad']) && $datos['IdLocalidad'] != "") {
                $jsonData->Escuela->Localidad = new stdClass();
                $jsonData->Escuela->Localidad->Id = (int)$datos['IdLocalidad'];
                $jsonData->Escuela->Localidad->Nombre = $datos['LocalidadNombre'];
            }
        }

        if (isset($datos['IdSolicitudCobertura']) && $datos['IdSolicitudCobertura'] != "" && !FuncionesPHPLocal::isEmpty($datos['IdSolicitudCobertura'])) {
            $jsonData->SolicitudAbierta = new stdClass();
            $jsonData->SolicitudAbierta->Id = (int)$datos['IdSolicitudCobertura'];
            $jsonData->SolicitudAbierta->Area = new stdClass();
            $jsonData->SolicitudAbierta->Area->Id = (int)$datos['IdAreaSolicitudCobertura'];
            $jsonData->SolicitudAbierta->Area->Nombre = $datos['SolicitudCoberturaAreaNombre'];
            $jsonData->SolicitudAbierta->Estado = new stdClass();
            $jsonData->SolicitudAbierta->Estado->Id = (int)$datos['IdEstadoSolicitudCobertura'];
            $jsonData->SolicitudAbierta->Estado->Nombre = $datos['SolicitudCoberturaEstadoNombre'];

        }




    }

    /**
     * @param array  $datos
     * @param object $jsonData
     */
    private static function _armarDatosDesempeno(array $datos, object &$jsonData): void {
        $jsonData->Tipo = new stdClass();
        $jsonData->Tipo->name = 'Desempeno';
        $jsonData->Tipo->parent = (int)$datos['IdPuesto'];

        $jsonData->Dia = new stdClass();
        $jsonData->Dia->Numero = (int)$datos['DiaNumero'];
        $jsonData->Dia->Descripcion = $datos['DiaDescripcion'];


        $jsonData->Horario = new stdClass();
        $jsonData->Horario->gte = $datos['HorarioDesde'];
        $jsonData->Horario->lte = $datos['HorarioHasta'];

        $jsonData->HoraDesde = $datos['HorarioDesde'];
        $jsonData->HoraHasta = $datos['HorarioHasta'];

    }

    /**
     * @param array  $datos
     * @param object $jsonData
     */
    private static function _armarDatosPersona(array $datos, object &$jsonData): void {
        $jsonData->Tipo = new stdClass();
        $jsonData->Tipo->name = 'Persona';
        $jsonData->Tipo->parent = (int)$datos['IdPuesto'];

        $jsonData->TieneConflictos = false;

        $jsonData->IdPofaMigracion = (int)$datos['IdPofaMigracion'] ?? NULL;
        $jsonData->IdPofaOrigen = (int)$datos['IdPofaOrigen'] ?? NULL;

        $jsonData->ExcepcionNombre = $datos['ExcepcionNombre'] ?? NULL;
        $jsonData->IdExcepcionTipo = (int)$datos['IdExcepcionTipo'] ?? NULL;

        $jsonData->IdPersona = (int)$datos['IdPersona'];

        $jsonData->Nombre = $datos['Nombre'];

        if (!FuncionesPHPLocal::isEmpty($datos['InstrumentoLegal']))
            $jsonData->NroResolucion = $datos['InstrumentoLegal'];

        if (isset($datos['Apellido']) && $datos['Apellido'] != "" && !FuncionesPHPLocal::isEmpty($datos['Apellido']))
            $jsonData->Apellido = $datos['Apellido'];

        $jsonData->Documento = new stdClass();
        $jsonData->Documento->Tipo = new stdClass();
        $jsonData->Documento->Tipo->Id = (int)$datos['IdTipoDocumento'];
        $jsonData->Documento->Tipo->Descripcion = $datos['DescripcionTipoDocumento'];
        $jsonData->Documento->Numero = $datos['DNI'];

        if (isset($datos['IdSexo']) && $datos['IdSexo'] != "" && !FuncionesPHPLocal::isEmpty($datos['IdSexo'])) {
            $jsonData->Sexo = new stdClass();
            $jsonData->Sexo->Id = (int)$datos['IdSexo'];
            $jsonData->Sexo->Descripcion = $datos['DescripcionSexo'];
        }
        if (isset($datos['CUIL']) && $datos['CUIL'] != "" && !FuncionesPHPLocal::isEmpty($datos['CUIL']))
            $jsonData->CUIL = $datos['CUIL'];

        $jsonData->Revista = new stdClass();
        $jsonData->Revista->Id = (int)$datos['IdRevista'];
        $jsonData->Revista->Codigo = $datos['CodigoRevista'];
        $jsonData->Revista->Descripcion = $datos['DescripcionRevista'];

        $jsonData->CodigoLiquidador = $datos['CodigoLiquidador'];

        $jsonData->FechaDesignacion = $datos['FechaDesignacion'];

        $jsonData->FechaTomaPosesion = $datos['FechaTomaPosesion'];
        $jsonData->FechaHastaPosesion = $datos['FechaHastaPosesion'];

        $jsonData->Orden = (int)$datos['Orden'];

        $jsonData->Fechas = new stdClass();
        $jsonData->Fechas->Desde = FuncionesPHPLocal::isEmpty($datos['FechaDesde']) ? NULL : $datos['FechaDesde'];
        $jsonData->Fechas->Hasta = FuncionesPHPLocal::isEmpty($datos['FechaHasta']) ? NULL : $datos['FechaHasta'];

        if (!FuncionesPHPLocal::isEmpty($datos['ExtiendeSuplencia'])) {
            $jsonData->ExtiendeSuplencia = new stdClass();
            $jsonData->ExtiendeSuplencia->Id = $datos['ExtiendeSuplencia'];
        }

        if (isset($datos['IdEstadoPersona']) && $datos['IdEstadoPersona'] != "" && !FuncionesPHPLocal::isEmpty($datos['IdEstadoPersona'])) {
            $jsonData->EstadoPersona = new stdClass();
            $jsonData->EstadoPersona->Id = (int)$datos['IdEstadoPersona'];
            $jsonData->EstadoPersona->Descripcion = $datos['DescripcionEstadoPersona'];
            $jsonData->EstadoPersona->Codigo = $datos['CodigoEstadoPersona'];

        }

    }

    /**
     * @inheritDoc
     */
    public static function getIndex(): string {
        return self::INDEX;
    }

    public function __destruct() {
        $this->error = [];
    }

    /**
     * Trae los datos del registro pedido
     *
     *
     * @param array      $datos
     * @param array|null $datosPuesto
     *
     * @return bool
     */
    public function buscarxCodigo(array $datos, ?array &$datosPuesto): bool {

        $id = self::obtenerId($datos);
        if (isset($datos['excluirCampos']))
            $id .= '?_source_excludes=' . implode(',', $datos['excluirCampos']);
        $this->cnx->setDebug(false);
        if (!$this->cnx->sendGet(self::INDEX, '_doc', $data, $codigoRetorno, $id)) {
            $this->setError($this->cnx->getError());
            return false;
        }
        if (FuncionesPHPLocal::isEmpty($data['_source'])) {
            $this->setError(404, 'Error, no se encuentra el puesto de la persona');
            return false;
        }

        $datosPuesto = $data['_source'];
        return true;
    }

    /**
     * @param array|stdClass $datos
     *
     * @return string|null
     */
    public static function obtenerId($datos): ?string {
        if (is_object($datos))
            $datos = (array)$datos;

        if (!FuncionesPHPLocal::isEmpty($datos['Id'])) {
            if (!FuncionesPHPLocal::isEmpty($datos['Tipo']))
                return self::procesarId($datos['Tipo'], $datos['Id']);
            else
                return $datos['Id'];
        }
        return NULL;
    }

    /**
     * @param mixed  $tipo
     * @param string $id
     *
     * @return string
     */
    private static function procesarId($tipo, string $id): string {
        if (is_object($tipo))
            $tipo = (array)$tipo;
        $name = $tipo['name'] ?? (string)$tipo;
        switch ($name) {
            case 'Persona':
                return "per-{$tipo['parent']}-$id?routing={$tipo['parent']}";
            case 'Desempeno':
                return "des-{$tipo['parent']}-$id?routing={$tipo['parent']}";
            default:
                return $id;
        }
    }

    /**
     * @param array      $datos
     * @param array|null $resultado
     *
     * @return bool
     */
    public function getPuestosxEscuela(array $datos, ?array &$resultado): bool {

        if(isset($datos['EsPlazaTransitoria']) && $datos['EsPlazaTransitoria'] != ''){
            if ($datos['EsPlazaTransitoria'] == 1){
                $datos['Estado'] = 40;
            } else {
                $datos['Estado'] = [10, 20];
            }
        }

        $jsonData = new stdClass();
        $jsonData->query = new stdClass();
        $jsonData->query->bool = new stdClass();
        $jsonData->query->bool->filter = [];
        $jsonData->from = $datos['from'] ?? 0;
        $jsonData->size = $datos['size'] ?? 1000;
        $jsonData->sort = array(
            0 => array("CodigoPuesto" => array("order" => "asc")),
            1 => array("Id" => array("order" => "asc"))
        );

        $i = 0;
        $jsonData->query->bool->filter[$i] = new stdClass();
        $jsonData->query->bool->filter[$i]->terms = new stdClass();
        $jsonData->query->bool->filter[$i]->terms->{'Estado'} = (isset($datos['Estado']) ? (is_array($datos['Estado']) ? $datos['Estado'] : explode(',', $datos['Estado'])) : [ACTIVO]);

        /*
        ++$i;
        $jsonData->query->bool->filter[$i] = new stdClass();
        $jsonData->query->bool->filter[$i]->term = new stdClass();
        $jsonData->query->bool->filter[$i]->term->{'Escuela.Id'} = new stdClass();
        $jsonData->query->bool->filter[$i]->term->{'Escuela.Id'}->value = $datos['IdEscuela'];
        */

        ++$i;
        $jsonData->query->bool->filter[$i] = new stdClass();
        $jsonData->query->bool->filter[$i]->terms = new stdClass();
        $jsonData->query->bool->filter[$i]->terms->{'Escuela.Id'} = is_array($datos['IdEscuela'])?$datos['IdEscuela']:explode(",",$datos['IdEscuela']);


        $i++;
        $jsonData->query->bool->filter[$i] = new stdClass();
        $jsonData->query->bool->filter[$i]->term = new stdClass();
        $jsonData->query->bool->filter[$i]->term->{'Tipo'} = new stdClass();
        $jsonData->query->bool->filter[$i]->term->{'Tipo'}->value = "Puesto";

        if (!FuncionesPHPLocal::isEmpty($datos['Dia'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->type = "Desempeno";
            $jsonData->query->bool->filter[$i]->has_child->query = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->term = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->term->{'Dia.Numero'} = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->term->{'Dia.Numero'}->value = $datos['Dia'];
        }


        if (!FuncionesPHPLocal::isEmpty($datos['IdNivelModalidad'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'NivelModalidad.Id'} = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'NivelModalidad.Id'}->value = $datos['IdNivelModalidad'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Id'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'Id'} = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'Id'}->value = $datos['Id'];
        }
        if (!FuncionesPHPLocal::isEmpty($datos['IdPuestoRaiz'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'IdPuestoRaiz'} = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'IdPuestoRaiz'}->value = $datos['IdPuestoRaiz'];
        }


        if (!FuncionesPHPLocal::isEmpty($datos['IdTurno'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'Turno.Id'} = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'Turno.Id'}->value = $datos['IdTurno'];
        }


        if (!FuncionesPHPLocal::isEmpty($datos['IdGradoAnio'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'GradoAnio.Id'} = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'GradoAnio.Id'}->value = $datos['IdGradoAnio'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdNivel'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'Nivel.Id'} = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'Nivel.Id'}->value = $datos['IdNivel'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdSeccion'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'SeccionDivision.Id'} = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'SeccionDivision.Id'}->value = $datos['IdSeccion'];
        }


        if (!FuncionesPHPLocal::isEmpty($datos['IdCargo'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'Cargo.Id'} = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'Cargo.Id'}->value = $datos['IdCargo'];
        }


        if (!FuncionesPHPLocal::isEmpty($datos['IdRegimenSalarial'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->bool = new stdClass();
            $jsonData->query->bool->filter[$i]->bool->should = [];

            $cond1 = new stdClass();
            $cond1->bool = new stdClass();
            $cond1->bool->must = [];

            $exists = new stdClass();
            $exists->exists = new stdClass();
            $exists->exists->field = "Regimen.IdRegimenSalarial";
            $cond1->bool->must[] = $exists;

            $term1 = new stdClass();
            $term1->term = new stdClass();
            $term1->term->{'Regimen.IdRegimenSalarial'} = new stdClass();
            $term1->term->{'Regimen.IdRegimenSalarial'}->value = $datos['IdRegimenSalarial'];
            $cond1->bool->must[] = $term1;

            $cond2 = new stdClass();
            $cond2->bool = new stdClass();
            $cond2->bool->must = [];

            $mustNotExists = new stdClass();
            $mustNotExists->exists = new stdClass();
            $mustNotExists->exists->field = "Regimen.IdRegimenSalarial";

            $notExists = new stdClass();
            $notExists->bool = new stdClass();
            $notExists->bool->must_not = [$mustNotExists];
            $cond2->bool->must[] = $notExists;

            $term2 = new stdClass();
            $term2->term = new stdClass();
            $term2->term->{'Cargo.Regimen.IdRegimenSalarial'} = new stdClass();
            $term2->term->{'Cargo.Regimen.IdRegimenSalarial'}->value = $datos['IdRegimenSalarial'];
            $cond2->bool->must[] = $term2;

            $jsonData->query->bool->filter[$i]->bool->should[] = $cond1;
            $jsonData->query->bool->filter[$i]->bool->should[] = $cond2;

            $jsonData->query->bool->filter[$i]->bool->minimum_should_match = 1;
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdPlaza'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->term->IdPlaza = new stdClass();
            $jsonData->query->bool->filter[$i]->term->IdPlaza->value = $datos['IdPlaza'];
        }


        if (!FuncionesPHPLocal::isEmpty($datos['IdMateria'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'Materia.Id'} = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'Materia.Id'}->value = $datos['IdMateria'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdsTurnos'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->terms = new stdClass();
            $jsonData->query->bool->filter[$i]->terms->{'Turno.Id'} = is_array($datos['IdsTurnos']) ? $datos['IdsTurnos'] : explode(',', $datos['IdsTurnos']);
        }


        if (!FuncionesPHPLocal::isEmpty($datos['cupofxPersona'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->terms = new stdClass();
            $jsonData->query->bool->filter[$i]->terms->{'CodigoPuesto'} = is_array($datos['cupofxPersona']) ? $datos['cupofxPersona'] : explode(',', $datos['cupofxPersona']);
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdPersona'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->type = 'Persona';
            $jsonData->query->bool->filter[$i]->has_child->query = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0] = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term->IdPersona = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term->IdPersona->value = $datos['IdPersona'];
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[1] = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[1]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[1]->term->{'Estado'} = 10;
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdExcepcionTipo'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->type = 'Persona';
            $jsonData->query->bool->filter[$i]->has_child->query = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0] = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term->IdExcepcionTipo = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term->IdExcepcionTipo->value = $datos['IdExcepcionTipo'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos["IdPofa"])) {
                $i++;
                $jsonData->query->bool->filter[$i] = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child->type = 'Persona';
                $jsonData->query->bool->filter[$i]->has_child->query = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child->query->bool = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0] = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term->Id = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term->Id->value = $datos['IdPofa'];

                $i++;
                $jsonData->query->bool->filter[$i] = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child->type = 'Persona';
                $jsonData->query->bool->filter[$i]->has_child->query = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child->query->term = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child->query->term->{'Estado'} = new stdClass();
                $jsonData->query->bool->filter[$i]->has_child->query->term->{'Estado'}->value = 10;
        }

        if (!FuncionesPHPLocal::isEmpty($datos['NroResolucion'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'NroResolucion'} = new stdClass();
            $jsonData->query->bool->filter[$i]->term->{'NroResolucion'}->value = $datos['NroResolucion'];
        }


        if (!FuncionesPHPLocal::isEmpty($datos['EstadoPersona'])) {
            $ambos = false;
            ++$i;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->bool = new stdClass();
            $jsonData->query->bool->filter[$i]->bool->must = [];
            $epmm = 0;

            $est = NULL;
            switch ($datos['EstadoPersona']) {
                case 'lic':
                    $ambos = true;
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm] = new stdClass();
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child = new stdClass();
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->type = 'Persona';
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query = new stdClass();
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->nested = new stdClass();
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->nested->path = 'EstadoPersona.Licencia';
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->nested->query = new stdClass();
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->nested->query->term = new stdClass();
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->nested->query->term->{'EstadoPersona.Licencia.Fechas'} = new stdClass();
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->nested->query->term->{'EstadoPersona.Licencia.Fechas'}->value = date('Y-m-d');
                    ++$epmm;
                    break;
                case 'alt':
//			        $ambos = true;
                    $est = ALT;
                    break;
                case 'exp':
                    $est = LIC;
                    break;
              /*  case 'reu':
                    $est = REU;
                    break;*/

                case "supvencida":

                    $jsonData->query->bool->filter[$i]->bool->must[$epmm] = new stdClass();
                    $child = $jsonData->query->bool->filter[$i]->bool->must[$epmm];
                    $child->has_child = new stdClass();
                    $child->has_child->type = 'Persona';

                    $child->has_child->query = new stdClass();
                    $child->has_child->query->bool = new stdClass();
                    $child->has_child->query->bool->must = [];

                    $range = new stdClass();
                    $range->range = new stdClass();
                    $range->range->{'Fechas.Hasta'} = new stdClass();
                    $range->range->{'Fechas.Hasta'}->lt = 'now';
                    $child->has_child->query->bool->must[] = $range;

                    $term1 = new stdClass();
                    $term1->term = new stdClass();
                    $term1->term->{'Revista.Id'} = new stdClass();
                    $term1->term->{'Revista.Id'}->value = REVISTA_SUPLENTE;
                    $child->has_child->query->bool->must[] = $term1;

                    $term2 = new stdClass();
                    $term2->term = new stdClass();
                    $term2->term->{'Estado'} = new stdClass();
                    $term2->term->{'Estado'}->value = ACTIVO;
                    $child->has_child->query->bool->must[] = $term2;

                    break;
            }

            if (!empty($est)) {

                $jsonData->query->bool->filter[$i]->bool->must[$epmm] = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->type = 'Persona';
                $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->term = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->term->{'EstadoPersona.Id'} = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->term->{'EstadoPersona.Id'}->value = $est;
                $jsonData->query->bool->filter[$i]->bool->must_not = [new stdClass()];
                $jsonData->query->bool->filter[$i]->bool->must_not[0]->has_child = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must_not[0]->has_child->type = 'Persona';
                $jsonData->query->bool->filter[$i]->bool->must_not[0]->has_child->query = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must_not[0]->has_child->query->nested = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must_not[0]->has_child->query->nested->path = 'EstadoPersona.Licencia';
                $jsonData->query->bool->filter[$i]->bool->must_not[0]->has_child->query->nested->query = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must_not[0]->has_child->query->nested->query->term = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must_not[0]->has_child->query->nested->query->term->{'EstadoPersona.Licencia.Fechas'} = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must_not[0]->has_child->query->nested->query->term->{'EstadoPersona.Licencia.Fechas'}->value = date('Y-m-d');
                ++$epmm;
            }

            if (defined(strtoupper($datos['EstadoPersona']))) {
                $jsonData->query->bool->filter[$i]->bool->must[$epmm] = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child = new stdClass();
                $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->type = 'Persona';
                $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query = new stdClass();
                if ($ambos && defined('LIC') && defined('ALT')) {
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->terms = new stdClass();
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->terms->{'EstadoPersona.Id'} = [LIC, ALT];
                } else {
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->term = new stdClass();
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->term->{'EstadoPersona.Id'} = new stdClass();
                    $jsonData->query->bool->filter[$i]->bool->must[$epmm]->has_child->query->term->{'EstadoPersona.Id'}->value = constant(strtoupper($datos['EstadoPersona']));
                }
            }
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Cupof'])) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->multi_match = new StdClass;
            $jsonData->query->bool->filter[$i]->multi_match->query = trim($datos['Cupof']);
            $jsonData->query->bool->filter[$i]->multi_match->fields = ['CodigoPuesto', 'Codigo'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['puestoVacante']) && !$datos['puestoVacante']) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->type = 'Persona';
            $jsonData->query->bool->filter[$i]->has_child->query = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->term = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->term->{'Estado'} = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->term->{'Estado'}->value = 10;

        } else if ($datos['puestoVacante']) {

            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->bool = new stdClass();
            $jsonData->query->bool->filter[$i]->bool->must_not = new stdClass();
            $jsonData->query->bool->filter[$i]->bool->must_not->has_child = new stdClass();
            $jsonData->query->bool->filter[$i]->bool->must_not->has_child->type = 'Persona';
            $jsonData->query->bool->filter[$i]->bool->must_not->has_child->query = new stdClass();
            $jsonData->query->bool->filter[$i]->bool->must_not->has_child->query->term = new stdClass();
            $jsonData->query->bool->filter[$i]->bool->must_not->has_child->query->term->{'Estado'} = new stdClass();
            $jsonData->query->bool->filter[$i]->bool->must_not->has_child->query->term->{'Estado'}->value = 10;
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdRevista'])/* && (isset($datos['puestoVacante']) && !$datos['puestoVacante'])*/) {
            $i++;
            $jsonData->query->bool->filter[$i] = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->type = 'Persona';
            $jsonData->query->bool->filter[$i]->has_child->query = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must = [];

            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0] = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term->{'Revista.Id'} = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[0]->term->{'Revista.Id'}->value = $datos['IdRevista'];

            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[1] = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[1]->term = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[1]->term->{'Estado'} = new stdClass();
            $jsonData->query->bool->filter[$i]->has_child->query->bool->must[1]->term->{'Estado'}->value = 10;
        }

        /*
        $jsonData->query->bool->filter[++$i] = new stdClass();
        $jsonData->query->bool->filter[$i]->bool = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should = [new stdClass(), new stdClass()];
        $jsonData->query->bool->filter[$i]->bool->should[0]->range = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should[0]->range->{'Fechas.Hasta'} = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should[0]->range->{'Fechas.Hasta'}->gte = 'now';
        $jsonData->query->bool->filter[$i]->bool->should[1]->bool = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should[1]->bool->must_not = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should[1]->bool->must_not->exists = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should[1]->bool->must_not->exists->field = 'Fechas.Hasta';
        */

        $jsonData->query->bool->filter[++$i] = new stdClass();
        $jsonData->query->bool->filter[$i]->bool = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should = [new stdClass(), new stdClass()];
        $jsonData->query->bool->filter[$i]->bool->should[0]->bool = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should[0]->bool->must_not = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should[0]->bool->must_not->exists = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should[0]->bool->must_not->exists->field = 'Fechas.Desde';
        $jsonData->query->bool->filter[$i]->bool->should[1]->range = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should[1]->range->{'Fechas.Desde'} = new stdClass();
        $jsonData->query->bool->filter[$i]->bool->should[1]->range->{'Fechas.Desde'}->lte = 'now';


        $jsonData->aggs = new stdClass();

        $jsonData->aggs->TiposCargos = new stdClass();
        $jsonData->aggs->TiposCargos->terms = new stdClass();
        $jsonData->aggs->TiposCargos->terms->field = 'Cargo.Tipo.Id';
        $jsonData->aggs->TiposCargos->terms->size = 1000;

        $jsonData->aggs->GradosAniosAgg = new stdClass();
        $jsonData->aggs->GradosAniosAgg->terms = new stdClass();
        $jsonData->aggs->GradosAniosAgg->terms->field = 'GradoAnio.Id';
        $jsonData->aggs->GradosAniosAgg->terms->size = 1000;

        $jsonData->aggs->PuestosPersonas = new stdClass();
        $jsonData->aggs->PuestosPersonas->terms = new stdClass();
        $jsonData->aggs->PuestosPersonas->terms->field = 'Id';
        $jsonData->aggs->PuestosPersonas->terms->size = 5000;

        $jsonData->aggs->PuestosPersonas->aggs = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->children = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->children->type = "Persona";


        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool = new stdClass();

        /*$jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must_not = [];
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must_not[0] = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must_not[0]->term = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must_not[0]->term->{'Estado'} = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must_not[0]->term->{'Estado'}->value = ELIMINADO;*/

        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must = [];
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must[0] = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must[0]->term = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must[0]->term->{'Estado'} = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must[0]->term->{'Estado'}->value = ACTIVO;

        if (!FuncionesPHPLocal::isEmpty($datos['IdRevista'])) {
            //$jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must = [];
            $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must[1] = new stdClass();
            $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must[1]->term = new stdClass();
            $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must[1]->term->{'Revista.Id'} = new stdClass();
            $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->filter->bool->must[1]->term->{'Revista.Id'}->value = $datos['IdRevista'];
        }


        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->aggs = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->aggs->Nombres = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->aggs->Nombres->top_hits = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->aggs->Nombres->top_hits->size = 100;
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Filtro->aggs->Nombres->top_hits->sort = array("Orden" => array("order" => "asc"));


        $jsonData->aggs->Desempenos = new stdClass();
        $jsonData->aggs->Desempenos->terms = new stdClass();
        $jsonData->aggs->Desempenos->terms->field = 'Id';
        $jsonData->aggs->Desempenos->terms->size = 1000;

        $jsonData->aggs->Desempenos->aggs = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno->children = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno->children->type = "Desempeno";

        $jsonData->aggs->Desempenos->aggs->Desempeno->aggs = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno->aggs->Dias = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno->aggs->Dias->top_hits = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno->aggs->Dias->top_hits->size = 100;
        $jsonData->aggs->Desempenos->aggs->Desempeno->aggs->Dias->top_hits->sort = array(0 => array("Dia.Numero" => array("order" => "asc")), 1 => array("HoraDesde" => array("order" => "asc")));


        $cuerpo = json_encode($jsonData);
        $this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado, $codigoRetorno)) {
            //var_dump($resultado);die;
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($resultado['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado));
            return false;
        }

        if ($resultado['hits']['total']['value'] < 1) {
            $this->setError(404, 'No se encuentra');
        }

        return true;

    }

    /**
     * @param array      $datos
     * @param array|null $resultado
     * @param int|null   $numfilas
     * @param int|null   $total
     *
     * @return bool
     */
    public function busquedaAvanzada(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {

        $SortField = 'Id';
        $SortOrder = 'desc';
        $jsonData = new stdClass();
        $jsonData->size = $datos['size'] ?? PAGINAR;
        $jsonData->from = $datos['from'] ?? 0;
        $jsonData->_source = new stdClass();
        if (!FuncionesPHPLocal::isEmpty($datos['excluirCampos']))
            $jsonData->_source->excludes = $datos['excluirCampos'];
        if (!FuncionesPHPLocal::isEmpty($datos['camposMostrar']))
            $jsonData->_source->includes = $datos['camposMostrar'];

        $jsonData->query = new stdClass();
        $jsonData->query->bool = new stdClass();
        $filter = [];
        $ff = -1;
        $must = [];
        $mm = -1;
        $should = [];
        $sh = -1;
        $must_not = [];
        $mn = -1;
        $sort = true;
        $ss = -1;

        if (!FuncionesPHPLocal::isEmpty($datos['IdEscuela'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->term = new stdClass();
            $filter[$ff]->term->{'Escuela.Id'} = new stdClass();
            $filter[$ff]->term->{'Escuela.Id'}->value = (int)$datos['IdEscuela'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdTurno'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->term = new stdClass();
            $filter[$ff]->term->{'Turno.Id'} = new stdClass();
            $filter[$ff]->term->{'Turno.Id'}->value = (int)$datos['IdTurno'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdGradoAnio'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->term = new stdClass();
            $filter[$ff]->term->{'GradoAnio.Id'} = new stdClass();
            $filter[$ff]->term->{'GradoAnio.Id'}->value = (int)$datos['IdGradoAnio'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdPuesto'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->term = new stdClass();
            $filter[$ff]->term->{'Id'} = new stdClass();
            $filter[$ff]->term->{'Id'}->value = (int)$datos['IdPuesto'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Estado'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->term = new stdClass();
            $filter[$ff]->term->{'Estado'} = new stdClass();
            $filter[$ff]->term->{'Estado'}->value = $datos['Estado'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['estadosExcluir'])) {
            $must_not[++$mn] = new stdClass();
            $must_not[$mn]->terms = new stdClass();
            $must_not[$mn]->terms->{'Estado.Id'} = is_array($datos['estadosExcluir']) ? $datos['estadosExcluir'] : explode(',', $datos['estadosExcluir']);

        }
        if (!FuncionesPHPLocal::isEmpty($filter))
            $jsonData->query->bool->filter = $filter;

        if (!FuncionesPHPLocal::isEmpty($must)) {
            $jsonData->query->bool->must = $must;
            $sort = false;
        }
        if (!FuncionesPHPLocal::isEmpty($should)) {
            $jsonData->query->bool->should = $should;
            $sort = false;
        }
        if (!FuncionesPHPLocal::isEmpty($must_not)) {
            $jsonData->query->bool->must_not = $must_not;
        }

        if ($sort) {
            $jsonData->sort = [];

            if (isset($datos['sort']) && is_array($datos['sort']) && count($datos['sort']) > 0) {
                foreach ($datos['sort'] as $sort) {
                    $jsonData->sort[++$ss] = new StdClass;
                    $jsonData->sort[$ss]->{$sort['field']} = new StdClass;
                    $jsonData->sort[$ss]->{$sort['field']}->order = $sort['order'];
                }
            } else {
                $jsonData->sort[++$ss] = new StdClass;
                $jsonData->sort[$ss]->{$SortField} = new StdClass;
                $jsonData->sort[$ss]->{$SortField}->order = $SortOrder;
            }
        }

        unset($ff, $mm, $sh, $mn, $ss);

        $cuerpo = json_encode($jsonData);
        $param = 'track_total_hits=true';
        $this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado_consulta, $codigoRetorno, $param)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($resultado_consulta['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado_consulta));
            return false;
        }
        //print_r($resultado_consulta);die;

        $resultado = $resultado_consulta['hits']['hits'];
        $total = (int)$resultado_consulta['hits']['total']['value'];
        $numfilas = (int)count($resultado);

        return true;
    }

    /**
     * @param array      $datos
     * @param array|null $resultado
     * @param int|null   $numfilas
     * @param int|null   $total
     *
     * @return bool
     */
    public function buscarxPersona(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {

        //print_r($datos);die;
        $jsonData = new stdClass();
        $jsonData->query = new stdClass();
        $jsonData->query->bool = new stdClass();
        $jsonData->query->bool->filter = [];
        $jsonData->from = $datos['from'] ?? 0;
        $jsonData->size = $datos['size'] ?? 1000;

        $ii = 0; $jj = 0;
        if (!FuncionesPHPLocal::isEmpty($datos['IdPersona'])) {
            $jsonData->query->bool->filter[$ii] = new stdClass();
            $jsonData->query->bool->filter[$ii]->has_child = new stdClass();
            $jsonData->query->bool->filter[$ii]->has_child->type = 'Persona';
            $jsonData->query->bool->filter[$ii]->has_child->query = new stdClass();
            $jsonData->query->bool->filter[$ii]->has_child->query->bool = new stdClass();
            $jsonData->query->bool->filter[$ii]->has_child->query->bool->filter = [];
            $jsonData->query->bool->filter[$ii]->has_child->query->bool->filter[$jj] = new stdClass();
            $jsonData->query->bool->filter[$ii]->has_child->query->bool->filter[$jj]->term = new stdClass();
            $jsonData->query->bool->filter[$ii]->has_child->query->bool->filter[$jj]->term->IdPersona = new stdClass();
            $jsonData->query->bool->filter[$ii]->has_child->query->bool->filter[$jj]->term->IdPersona->value = $datos['IdPersona'];
            $ii++;
            ++$jj;
        }
        if (!FuncionesPHPLocal::isEmpty($datos['EstadoPuestoPersona'])) {
            if (!FuncionesPHPLocal::isEmpty($datos['IdPersona']))
                $ii--;
            else {
                $jsonData->query->bool->filter[$ii] = new stdClass();
                $jsonData->query->bool->filter[$ii]->has_child = new stdClass();
                $jsonData->query->bool->filter[$ii]->has_child->type = 'Persona';
                $jsonData->query->bool->filter[$ii]->has_child->query = new stdClass();
                $jsonData->query->bool->filter[$ii]->has_child->query->bool = new stdClass();
                $jsonData->query->bool->filter[$ii]->has_child->query->bool->filter = [];
            }
            $jsonData->query->bool->filter[$ii]->has_child->query->bool->filter[$jj] = new stdClass();
            $jsonData->query->bool->filter[$ii]->has_child->query->bool->filter[$jj]->term = new stdClass();
            $jsonData->query->bool->filter[$ii]->has_child->query->bool->filter[$jj]->term->Estado = new stdClass();
            $jsonData->query->bool->filter[$ii]->has_child->query->bool->filter[$jj]->term->Estado->value = $datos['EstadoPuestoPersona'];
            $ii++;
            ++$jj;
        }

        if (!FuncionesPHPLocal::isEmpty($datos['EstadoPuesto'])) {
            if (!is_array($datos['EstadoPuesto'])) {
                $jsonData->query->bool->filter[$ii] = new stdClass();
                $jsonData->query->bool->filter[$ii]->term = new stdClass();
                $jsonData->query->bool->filter[$ii]->term->Estado = new stdClass();
                $jsonData->query->bool->filter[$ii]->term->Estado->value = $datos['EstadoPuesto'];
                $ii++;
            }else{
                $jsonData->query->bool->filter[$ii] = new stdClass();
                $jsonData->query->bool->filter[$ii]->terms = new stdClass();
                $jsonData->query->bool->filter[$ii]->terms->{'Estado'} = (isset($datos['EstadoPuesto']) ? (is_array($datos['EstadoPuesto']) ? $datos['EstadoPuesto'] : explode(',', $datos['EstadoPuesto'])) : [ACTIVO]);
                $ii++;
            }
        }

        $jsonData->aggs = new stdClass();
        $jsonData->aggs->TiposCargos = new stdClass();
        $jsonData->aggs->TiposCargos->terms = new stdClass();
        $jsonData->aggs->TiposCargos->terms->field = 'Cargo.Tipo.Id';
        $jsonData->aggs->TiposCargos->terms->size = 1000;

        $jsonData->aggs->PuestosPersonas = new stdClass();
        $jsonData->aggs->PuestosPersonas->terms = new stdClass();
        $jsonData->aggs->PuestosPersonas->terms->field = 'Id';
        $jsonData->aggs->PuestosPersonas->terms->size = 1000;

        $jsonData->aggs->PuestosPersonas->aggs = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->children = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->children->type = "Persona";
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->bool = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->bool->must = [];
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->bool->must[0] = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->bool->must[0]->term = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->bool->must[0]->term->{'IdPersona'} = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->bool->must[0]->term->{'IdPersona'}->value = $datos['IdPersona'];
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->bool->must_not = [];
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->bool->must_not[0] = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->bool->must_not[0]->terms = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->bool->must_not[0]->terms->{'Estado'} = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->bool->must_not[0]->terms->{'Estado'} = [ELIMINADO,NOACTIVO];


        //        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs = new stdClass();
      //   $jsonData->aggs->PuestosPersonas->aggs->Personas->children->type = "Persona";
        //  $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona = new stdClass();
      //  $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter = new stdClass();
      //  $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->term = new stdClass();
      //  $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->term->IdPersona = new stdClass();
      //  $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->filter->term->IdPersona->value = $datos['IdPersona'];

        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->aggs = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->aggs->Nombres = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->aggs->Nombres->top_hits = new stdClass();
        $jsonData->aggs->PuestosPersonas->aggs->Personas->aggs->Persona->aggs->Nombres->top_hits->size = 1;


        $jsonData->aggs->Desempenos = new stdClass();
        $jsonData->aggs->Desempenos->terms = new stdClass();
        $jsonData->aggs->Desempenos->terms->field = 'Id';
        $jsonData->aggs->Desempenos->terms->size = 1000;

        $jsonData->aggs->Desempenos->aggs = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno->children = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno->children->type = "Desempeno";

        $jsonData->aggs->Desempenos->aggs->Desempeno->aggs = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno->aggs->Dias = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno->aggs->Dias->top_hits = new stdClass();
        $jsonData->aggs->Desempenos->aggs->Desempeno->aggs->Dias->top_hits->size = 100;
        $jsonData->aggs->Desempenos->aggs->Desempeno->aggs->Dias->top_hits->sort = array(0 => array("Dia.Numero" => array("order" => "asc")), 1 => array("HoraDesde" => array("order" => "asc")));


        $cuerpo = json_encode($jsonData);
        //echo $cuerpo;
        $this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado_consulta, $codigoRetorno)) {
            $this->setError($this->cnx->getError());
            return false;
        }
        //die;
        if (!isset($resultado_consulta['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado));
            return false;
        }

        $resultado = $resultado_consulta;
        $total = (int)$resultado_consulta['hits']['total']['value'];
        $numfilas = (int)count($resultado['hits']['hits']);


        return true;
    }


    public function buscarPuestosPersonasxIdPersona(array $datos, &$resultado, ?int &$numfilas, ?int &$total): bool {

        $source = new Consultas\Source();
        if (!empty($datos['excluirCampos']))
            $source->addExcludes(...$datos['excluirCampos']);
        if (!empty($datos['camposMostrar']))
            $source->addIncludes(...$datos['camposMostrar']);

        $cuerpo = Consultas\Base::nueva(10000)
            ->setSource($source)
            ->setQuery(Query::term('IdPersona', (int)$datos['IdPersona']))
            ->toJson();

        $this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado_consulta, $codigoRetorno)) {
            $this->setError($this->cnx->getError());
            return false;
        }
        if (!isset($resultado_consulta['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado));
            return false;
        }
        $resultado = $resultado_consulta['hits']['hits'];
        $total = (int)$resultado_consulta['hits']['total']['value'];
        $numfilas = count($resultado);

        return true;
    }


    /**
     * @param $datos
     *
     * @return bool
     */
    public function Eliminar($datos): bool {

        $id = $datos['id'];
        if (!$this->cnx->sendDelete('dev-rh-puestos', '_doc', $data, $codigoRetorno, $id))
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
            'Nombre.prefix',
            'Nombre.prefix._2gram',
            'Nombre.prefix._3gram',
            'Apellido.prefix',
            'Apellido.prefix._2gram',
            'Apellido.prefix._3gram',
            'Documento.Numero.prefix',
            'Documento.Numero.prefix._2gram',
            'Documento.Numero.prefix._3gram'
        ];
        $filter[++$ff] = new stdClass();
        $filter[$ff]->term = new stdClass();
        $filter[$ff]->term->Tipo = new stdClass();
        $filter[$ff]->term->Tipo->value = 'Persona';

        if (!FuncionesPHPLocal::isEmpty($datos['IdEscuela'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->has_parent = new stdClass();
            $filter[$ff]->has_parent->parent_type = 'Puesto';
            $filter[$ff]->has_parent->query = new stdClass();
            $filter[$ff]->has_parent->query->term = new stdClass();
            $filter[$ff]->has_parent->query->term->{'Escuela.Id'} = new stdClass();
            $filter[$ff]->has_parent->query->term->{'Escuela.Id'}->value = (int)$datos['IdEscuela'];
        }

        if (!FuncionesPHPLocal::isEmpty($filter))
            $jsonData->query->bool->filter = $filter;

        if (!FuncionesPHPLocal::isEmpty($must))
            $jsonData->query->bool->must = $must;

        $jsonData->aggs = new stdClass();
        $jsonData->aggs->Personas = new stdClass();
        $jsonData->aggs->Personas->terms = new stdClass();
        $jsonData->aggs->Personas->terms->field = 'IdPersona';
        $jsonData->aggs->Personas->terms->size = 10;
        $jsonData->aggs->Personas->aggs = new stdClass();
        $jsonData->aggs->Personas->aggs->Datos = new stdClass();
        $jsonData->aggs->Personas->aggs->Datos->top_hits = new stdClass();
        $jsonData->aggs->Personas->aggs->Datos->top_hits->size = 1;
        $jsonData->aggs->Personas->aggs->Datos->top_hits->_source = ['Nombre', 'Apellido', 'Documento.*', 'IdPersona'];


        $cuerpo = json_encode($jsonData);
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
//		$dataResult = $resultado['hits'];
//		print_R($resultado);die;
        $dataResult = array_map(function ($item) {
            return $item['Datos']['hits']['hits'][0]['_source'];
        }, $resultado['aggregations']['Personas']['buckets']);

        return true;
    }

    /**
     * @param array      $datos
     * @param array|null $dataResult
     *
     * @return bool
     */
    public function estadisticasDashboard(array $datos, ?array &$dataResult): bool {
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


        $jsonData->aggs = new stdClass();
        $jsonData->aggs->Cargos = new stdClass();
        $jsonData->aggs->Cargos->filters = new stdClass();
        $jsonData->aggs->Cargos->filters->filters = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->Materias = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->Materias->exists = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->Materias->exists->field = "Materia";

        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool->must = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool->must->exists = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool->must->exists->field = "Cargo";

        $i = 0;
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool->must = array();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool->must[$i] = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool->must[$i]->exists = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool->must[$i]->exists->field = "Cargo";
        $i++;
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool->must[$i] = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool->must[$i]->term = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool->must[$i]->term->{'Cargo.Tipo.Id'} = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->CargosAuxiliares->bool->must[$i]->term->{'Cargo.Tipo.Id'}->value = 2;

        $jsonData->aggs->Cargos->filters->filters->Cargos = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->Cargos->bool = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->Cargos->bool->must = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->Cargos->bool->must->exists = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->Cargos->bool->must->exists->field = "Cargo";
        $jsonData->aggs->Cargos->filters->filters->Cargos->bool->must_not = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->Cargos->bool->must_not->term = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->Cargos->bool->must_not->term->{'Cargo.Tipo.Id'} = new stdClass();
        $jsonData->aggs->Cargos->filters->filters->Cargos->bool->must_not->term->{'Cargo.Tipo.Id'}->value = 2;

        $cuerpo = json_encode($jsonData);
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
//		$dataResult = $resultado['hits'];
//		print_R($resultado);die;
        $dataResult = $resultado['aggregations'];

        return true;
    }


    /**
     * @param array      $datos
     * @param array|null $dataResult
     *
     * @return bool
     */
    public function DashboardPuestosSinDesempeno(array $datos, ?array &$dataResult): bool {
        //$datos['Nombre'] = preg_replace(self::PATTERN, self::REPLACEMENT, utf8_decode($datos['Nombre']));
        $nombres = [];
        $jsonData = new stdClass();
        $jsonData->from = $datos['from'] ?? 0;
        $jsonData->size = $datos['size'] ?? 1000;
        $jsonData->query = new stdClass();
        $jsonData->query->bool = new stdClass();
        $jsonData->query->bool->must = [];
        $mm = -1;
        $must = [];
        $jsonData->query->bool->filter = [];
        $ff = -1;
        $filter = [];

        $mm++;
        $jsonData->query->bool->must[$mm] = new stdClass();
        $jsonData->query->bool->must[$mm]->bool = new stdClass();
        $jsonData->query->bool->must[$mm]->bool->must_not = [];
        $jsonData->query->bool->must[$mm]->bool->must_not[0] = new stdClass();
        $jsonData->query->bool->must[$mm]->bool->must_not[0]->has_child = new stdClass();
        $jsonData->query->bool->must[$mm]->bool->must_not[0]->has_child->type = "Persona";
        $jsonData->query->bool->must[$mm]->bool->must_not[0]->has_child->query = new stdClass();
        $jsonData->query->bool->must[$mm]->bool->must_not[0]->has_child->query->match_all = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($_SESSION['IdEscuela'])) {
            $mm++;
            $jsonData->query->bool->must[$mm] = new stdClass();
            $jsonData->query->bool->must[$mm]->term = new stdClass();
            $jsonData->query->bool->must[$mm]->term->{"Escuela.Id"} = $_SESSION['IdEscuela'];
        }

        $cuerpo = json_encode($jsonData);
        echo $cuerpo;
		$this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado, $codigoRetorno)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($resultado['hits'])) {
            $this->setError(500, Funciones::DevolverError($resultado));
            return false;
        }

        $dataResult = $resultado;

        return true;
    }

    /**
     * @param $datos
     *
     * @return bool|array
     */
    public function obtenerTotalesDashboardEquipoConduccion($datos) {
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


        $jsonData->query->bool->must[0] = new stdClass();
        $jsonData->query->bool->must[0]->term = new stdClass();
        $jsonData->query->bool->must[0]->term->{"Escuela.Id"} = $datos['IdEscuela'];


        $jsonData->aggs = new stdClass();
        $jsonData->aggs->PuestosSinPersonas = new stdClass();
        $jsonData->aggs->PuestosSinPersonas->filter = new stdClass();
        $jsonData->aggs->PuestosSinPersonas->filter->bool = new stdClass();
        $jsonData->aggs->PuestosSinPersonas->filter->bool->must_not = array();
        $jsonData->aggs->PuestosSinPersonas->filter->bool->must_not[0] = new stdClass();
        $jsonData->aggs->PuestosSinPersonas->filter->bool->must_not[0]->has_child = new stdClass();
        $jsonData->aggs->PuestosSinPersonas->filter->bool->must_not[0]->has_child->type = "Persona";
        $jsonData->aggs->PuestosSinPersonas->filter->bool->must_not[0]->has_child->query = new stdClass();
        $jsonData->aggs->PuestosSinPersonas->filter->bool->must_not[0]->has_child->query->match_all = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['EstadoPuesto'])) {
            $jsonData->query->bool->filter = new stdClass();
            $jsonData->query->bool->filter->term = new stdClass();
            $jsonData->query->bool->filter->term->Estado = new stdClass();
            $jsonData->query->bool->filter->term->Estado->value = $datos['EstadoPuesto'];
        }

        $jsonData->aggs->PuestosVacantesConPersonas = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must = array();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0] = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->type = "Persona";
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->min_children = 1;
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not = array();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[0] = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[0]->term = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[0]->term->{"EstadoPersona.Id"} = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[0]->term->{"EstadoPersona.Id"}->value = 1;
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[1] = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[1]->has_parent = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[1]->has_parent->parent_type = "Puesto";
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[1]->has_parent->query = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[1]->has_parent->query->has_child = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[1]->has_parent->query->has_child->type = "Persona";
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[1]->has_parent->query->has_child->query = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[1]->has_parent->query->has_child->query->term = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[1]->has_parent->query->has_child->query->term->{"EstadoPersona.Id"} = new stdClass();
        $jsonData->aggs->PuestosVacantesConPersonas->filter->bool->must[0]->has_child->query->bool->must_not[1]->has_parent->query->has_child->query->term->{"EstadoPersona.Id"}->value = 1;


        $jsonData->aggs->ReintegrosDelDia = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must = [];
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0] = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->type = "Persona";
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must = [];
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0] = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->parent_type = "Puesto";
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->type = "Persona";
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->path = "EstadoPersona.Licencia";
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must = [];
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must[0] = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must[0]->range = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must[0]->range->{"EstadoPersona.Licencia.FechaHastaEstimada"} = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must[0]->range->{"EstadoPersona.Licencia.FechaHastaEstimada"}->gte = $datos['FechaActual'];
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must[0]->range->{"EstadoPersona.Licencia.FechaHastaEstimada"}->lte = $datos['FechaActual'];

        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must_not = [];
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must_not[0] = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must_not[0]->range = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must_not[0]->range->{"EstadoPersona.Licencia.Fechas"} = new stdClass();
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must_not[0]->range->{"EstadoPersona.Licencia.Fechas"}->gte = $datos['FechaActual'];
        $jsonData->aggs->ReintegrosDelDia->filter->bool->must[0]->has_child->query->bool->must[0]->has_parent->query->has_child->query->bool->must->nested->query->bool->must_not[0]->range->{"EstadoPersona.Licencia.Fechas"}->lte = $datos['FechaActual'];

        $cuerpo = json_encode($jsonData);
        $this->cnx->setDebug(false);
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

        return $resultado;
    }


    public function buscarPersonas(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {
        $bool = (new Consultas\Booleano())->addFilter(Query::term('Tipo', 'Persona'));
        if (!empty($datos['IdEscuela']))
            $bool->addFilter(
                Query::has_parent('Puesto', Query::term('Escuela.Id', $datos['IdEscuela']))
            );
        $cuerpo = (new Consultas\Base($datos['size'] ?? 20, $datos['from'] ?? 0))
            ->setQuery(Query::bool($bool));

        if (!FuncionesPHPLocal::isEmpty($datos['sourceIncludes']))
            $cuerpo->setSource((new Consultas\Source())->addIncludes(...$datos['sourceIncludes']));
        elseif (!FuncionesPHPLocal::isEmpty($datos['sourceExcludes']))
            $cuerpo->setSource((new Consultas\Source())->addExcludes(...$datos['sourceExcludes']));

        $param = 'track_total_hits=true';
        $this->cnx->setDebug(false);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo->toJson(), $resultado_consulta, $codigoRetorno, $param)) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($resultado_consulta['hits'])) {
            $this->setError(400, Funciones::DevolverError($resultado_consulta));
            return false;
        }

        $resultado = $resultado_consulta['hits']['hits'];
        $numfilas = (int)count($resultado);
        $total = (int)$resultado_consulta['hits']['total']['value'];

        return true;
    }

    public function verificarPersonaLicenciada(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {
        $bool = Query::bool()->addFilter(Query::parent_id('Persona', (int)$datos['IdPuesto']));
        if (!FuncionesPHPLocal::isEmpty($datos['Fecha'])) {
            $bool->addFilter(
                Query::nested('EstadoPersona.Licencia',
                    Query::term('EstadoPersona.Licencia.Fechas', substr($datos['Fecha'], 0, 10))
                )
            );
        }
        $cuerpo = Consultas\Base::nueva($datos['size']??PAGINAR, $datos['from']??0)
            ->setQuery($bool)
            ->toJson();

        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $data, $codigoRetorno, 'track_total_hits=true')) {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($data['hits'])) {
            $this->setError(400, Funciones::DevolverError($data));
            return false;
        }

        $resultado = $data['hits']['hits'];
        $numfilas = count($resultado);
        $total = $data['hits']['total']['value'];

        return true;
    }
}
