<?php


namespace Elastic;


use Exception;
use FuncionesPHPLocal;
use ManejoErrores;
use stdClass;

class NovedadesHistoricos implements InterfaceBase
{
    use ManejoErrores;
    /** @var string */
    private const INDEX = INDEXPREFIX.SUFFIX_NOVEDADES_HISTORICOS;
    /** @var Conexion */
    private $cnx;

    /**
     * Docentes constructor.
     *
     * @param Conexion $cnx
     */
    public function __construct(Conexion $cnx)
    {
        $this->cnx =& $cnx;
    }
	
	/**
	 * @inheritDoc
	 */
	public static function Configuracion(&$jsonData): void
	{
		
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
	 * @param bool $devolverJson
	 * @return false|stdClass|string
	 * @deprecated
	 */
	public static function Estructura_($devolverJson = true)
	{
		$jsonData = new stdClass();
		$jsonData->dynamic = 'strict';
		$jsonData->properties = new stdClass();
		
		$jsonData->properties->Id = new stdClass();
        $jsonData->properties->Id->type = 'keyword';

        $jsonData->properties->AccionCambio = new stdClass();
        $jsonData->properties->AccionCambio->type = 'keyword';

        $jsonData->properties->IdFilaLog = new stdClass();
        $jsonData->properties->IdFilaLog->type = 'integer';


        $jsonData->properties->IdEscuela = new stdClass();
        $jsonData->properties->IdEscuela->type = 'integer';
        $jsonData->properties->IdEscuelaDestino = new stdClass();
        $jsonData->properties->IdEscuelaDestino->type = 'integer';
        $jsonData->properties->IdDocumento = new stdClass();
        $jsonData->properties->IdDocumento->type = 'integer';
        $jsonData->properties->IdDocumentoPadre = new stdClass();
        $jsonData->properties->IdDocumentoPadre->type = 'integer';
        $jsonData->properties->Tipo = new stdClass();
        $jsonData->properties->Tipo->type = 'keyword';

        /* **************************************************************************** *
      *                                                                              *
      *                          Bloque Escuela                                       *
      *                                                                              *
      * **************************************************************************** */
        $jsonData->properties->Escuela = new stdClass();
        $jsonData->properties->Escuela->type = 'object';
        $jsonData->properties->Escuela->properties = new stdClass();
        $jsonData->properties->Escuela->properties->Id = new stdClass();
        $jsonData->properties->Escuela->properties->Id->type = 'integer';
        $jsonData->properties->Escuela->properties->Nombre = new stdClass();
        $jsonData->properties->Escuela->properties->Nombre->type = 'text';
        $jsonData->properties->Escuela->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->Escuela->properties->Nombre->fields = new stdClass();
        $jsonData->properties->Escuela->properties->Nombre->fields->raw = new stdClass();
        $jsonData->properties->Escuela->properties->Nombre->fields->raw->type = 'keyword';


        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque Escuela Destino                                     *
        *                                                                              *
        * **************************************************************************** */
        $jsonData->properties->EscuelaDestino = new stdClass();
        $jsonData->properties->EscuelaDestino->type = 'object';
        $jsonData->properties->EscuelaDestino->properties = new stdClass();
        $jsonData->properties->EscuelaDestino->properties->Id = new stdClass();
        $jsonData->properties->EscuelaDestino->properties->Id->type = 'integer';
        $jsonData->properties->EscuelaDestino->properties->Nombre = new stdClass();
        $jsonData->properties->EscuelaDestino->properties->Nombre->type = 'text';
        $jsonData->properties->EscuelaDestino->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->EscuelaDestino->properties->Nombre->fields = new stdClass();
        $jsonData->properties->EscuelaDestino->properties->Nombre->fields->raw = new stdClass();
        $jsonData->properties->EscuelaDestino->properties->Nombre->fields->raw->type = 'keyword';



        /* **************************************************************************** *
       *                                                                              *
       *                          Bloque Tipo Documento                                       *
       *                                                                              *
       * **************************************************************************** */

        $jsonData->properties->TipoDocumento = new stdClass();
        $jsonData->properties->TipoDocumento->type = 'object';
        $jsonData->properties->TipoDocumento->properties = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Id = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Id->type = 'integer';
        $jsonData->properties->TipoDocumento->properties->IdRegistro = new stdClass();
        $jsonData->properties->TipoDocumento->properties->IdRegistro->type = 'integer';
        $jsonData->properties->TipoDocumento->properties->Nombre = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Nombre->type = 'text';
        $jsonData->properties->TipoDocumento->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->TipoDocumento->properties->Nombre->fields = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Nombre->fields->raw = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Nombre->fields->raw->type = 'keyword';
        $jsonData->properties->TipoDocumento->properties->NombreCorto = new stdClass();
        $jsonData->properties->TipoDocumento->properties->NombreCorto->type = 'text';
        $jsonData->properties->TipoDocumento->properties->NombreCorto->analyzer = 'spanish';
        $jsonData->properties->TipoDocumento->properties->NombreCorto->fields = new stdClass();
        $jsonData->properties->TipoDocumento->properties->NombreCorto->fields->raw = new stdClass();
        $jsonData->properties->TipoDocumento->properties->NombreCorto->fields->raw->type = 'keyword';
        $jsonData->properties->TipoDocumento->properties->Categoria = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Categoria->type = 'object';
        $jsonData->properties->TipoDocumento->properties->Categoria->properties = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Categoria->properties->Id = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Categoria->properties->Id->type = 'integer';
        $jsonData->properties->TipoDocumento->properties->Categoria->properties->Nombre = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Categoria->properties->Nombre->type = 'text';
        $jsonData->properties->TipoDocumento->properties->Categoria->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->TipoDocumento->properties->Categoria->properties->Nombre->fields = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Categoria->properties->Nombre->fields->raw = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Categoria->properties->Nombre->fields->raw->type = 'keyword';
        $jsonData->properties->TipoDocumento->properties->Clasificacion = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Clasificacion->type = 'object';
        $jsonData->properties->TipoDocumento->properties->Clasificacion->properties = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Clasificacion->properties->Id = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Clasificacion->properties->Id->type = 'integer';
        $jsonData->properties->TipoDocumento->properties->Clasificacion->properties->Nombre = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Clasificacion->properties->Nombre->type = 'text';
        $jsonData->properties->TipoDocumento->properties->Clasificacion->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->TipoDocumento->properties->Clasificacion->properties->Nombre->fields = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Clasificacion->properties->Nombre->fields->raw = new stdClass();
        $jsonData->properties->TipoDocumento->properties->Clasificacion->properties->Nombre->fields->raw->type = 'keyword';


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Agente                                       *
         *                                                                              *
         * **************************************************************************** */


        $jsonData->properties->Agente = new stdClass();
        $jsonData->properties->Agente->type = 'object';
        $jsonData->properties->Agente->properties = new stdClass();
        $jsonData->properties->Agente->properties->Id = new stdClass();
        $jsonData->properties->Agente->properties->Id->type = 'integer';
        $jsonData->properties->Agente->properties->Cuil = new stdClass();
        $jsonData->properties->Agente->properties->Cuil->type = 'keyword';
        $jsonData->properties->Agente->properties->Dni = new stdClass();
        $jsonData->properties->Agente->properties->Dni->type = 'keyword';
        $jsonData->properties->Agente->properties->TipoDocumento = new stdClass();
        $jsonData->properties->Agente->properties->TipoDocumento->type = 'integer';
        $jsonData->properties->Agente->properties->TipoDocumentoNombre = new stdClass();
        $jsonData->properties->Agente->properties->TipoDocumentoNombre->type = 'keyword';

        $jsonData->properties->Agente->properties->Nombre = new stdClass();
        $jsonData->properties->Agente->properties->Nombre->type = 'text';
        $jsonData->properties->Agente->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->Agente->properties->Nombre->fields = new stdClass();
        $jsonData->properties->Agente->properties->Nombre->fields->raw = new stdClass();
        $jsonData->properties->Agente->properties->Nombre->fields->raw->type = 'keyword';
        $jsonData->properties->Agente->properties->Apellido = new stdClass();
        $jsonData->properties->Agente->properties->Apellido->type = 'text';
        $jsonData->properties->Agente->properties->Apellido->analyzer = 'spanish';
        $jsonData->properties->Agente->properties->Apellido->fields = new stdClass();
        $jsonData->properties->Agente->properties->Apellido->fields->raw = new stdClass();
        $jsonData->properties->Agente->properties->Apellido->fields->raw->type = 'keyword';
        $jsonData->properties->Agente->properties->NombreCompleto = new stdClass();
        $jsonData->properties->Agente->properties->NombreCompleto->type = 'text';
        $jsonData->properties->Agente->properties->NombreCompleto->analyzer = 'spanish';
        $jsonData->properties->Agente->properties->NombreCompleto->fields = new stdClass();
        $jsonData->properties->Agente->properties->NombreCompleto->fields->raw = new stdClass();
        $jsonData->properties->Agente->properties->NombreCompleto->fields->raw->type = 'keyword';
        $jsonData->properties->Agente->properties->Sexo = new stdClass();
        $jsonData->properties->Agente->properties->Sexo->type = 'keyword';

        $jsonData->properties->Agente->properties->Email = new stdClass();
        $jsonData->properties->Agente->properties->Email->type = 'text';
        $jsonData->properties->Agente->properties->Email->analyzer = 'spanish';
        $jsonData->properties->Agente->properties->Email->fields = new stdClass();
        $jsonData->properties->Agente->properties->Email->fields->raw = new stdClass();
        $jsonData->properties->Agente->properties->Email->fields->raw->type = 'keyword';

        $jsonData->properties->Agente->properties->Telefono = new stdClass();
        $jsonData->properties->Agente->properties->Telefono->type = 'text';
        $jsonData->properties->Agente->properties->Telefono->analyzer = 'spanish';
        $jsonData->properties->Agente->properties->Telefono->fields = new stdClass();
        $jsonData->properties->Agente->properties->Telefono->fields->raw = new stdClass();
        $jsonData->properties->Agente->properties->Telefono->fields->raw->type = 'keyword';

        $jsonData->properties->Agente->properties->FechaFallecido = new stdClass();
        $jsonData->properties->Agente->properties->FechaFallecido->type = 'date';
        $jsonData->properties->Agente->properties->FechaFallecido->format = 'yyyy-MM-dd||epoch_millis';

        $jsonData->properties->Agente->properties->FechaBaja = new stdClass();
        $jsonData->properties->Agente->properties->FechaBaja->type = 'date';
        $jsonData->properties->Agente->properties->FechaBaja->format = 'yyyy-MM-dd||epoch_millis';


        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Periodo                                 *
         *                                                                              *
         * **************************************************************************** */

        $jsonData->properties->Periodo = new stdClass();
        $jsonData->properties->Periodo->type = 'object';
        $jsonData->properties->Periodo->properties = new stdClass();
        $jsonData->properties->Periodo->properties->FechaDesde = new stdClass();
        $jsonData->properties->Periodo->properties->FechaDesde->type = 'date';
        $jsonData->properties->Periodo->properties->FechaDesde->format = 'yyyy-MM-dd||epoch_millis';
        $jsonData->properties->Periodo->properties->FechaHasta = new stdClass();
        $jsonData->properties->Periodo->properties->FechaHasta->type = 'date';
        $jsonData->properties->Periodo->properties->FechaHasta->format = 'yyyy-MM-dd||epoch_millis';

        /* **************************************************************************** *
       *                                                                              *
       *                          Bloque Licencias                                     *
       *                                                                              *
       * **************************************************************************** */

        $jsonData->properties->Licencia = new stdClass();
        $jsonData->properties->Licencia->type = 'object';
        $jsonData->properties->Licencia->properties = new stdClass();
        $jsonData->properties->Licencia->properties->Id = new stdClass();
        $jsonData->properties->Licencia->properties->Id->type = 'integer';
		$jsonData->properties->Licencia->properties->Tipo = new Tipos\Objeto();
		$jsonData->properties->Licencia->properties->Tipo->Id = new Tipos\Entero();
		$jsonData->properties->Licencia->properties->Tipo->Nombre = new Tipos\Keyword();
        $jsonData->properties->Licencia->properties->Nombre = new stdClass();
        $jsonData->properties->Licencia->properties->Nombre->type = 'text';
        $jsonData->properties->Licencia->properties->Nombre->analyzer = 'spanish';



        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque FechaDesignacion                             *
        *                                                                              *
        * **************************************************************************** */

        /* $jsonData->properties->FechaDesignacion = new stdClass();
         $jsonData->properties->FechaDesignacion->type = 'date';
         $jsonData->properties->FechaDesignacion->format = 'strict_date||epoch_millis';

        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque FechaTomaPosesion                             *
        *                                                                              *
        * **************************************************************************** */

        /* $jsonData->properties->FechaTomaPosesion = new stdClass();
         $jsonData->properties->FechaTomaPosesion->type = 'date';
         $jsonData->properties->FechaTomaPosesion->format = 'strict_date||epoch_millis';*/


        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque MovimientoFecha                                       *
        *                                                                              *
        * **************************************************************************** */

        $jsonData->properties->MovimientoFecha = new stdClass();
        $jsonData->properties->MovimientoFecha->type = 'date';
        $jsonData->properties->MovimientoFecha->format ='yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';


        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque FechaEnvio                                       *
        *                                                                              *
        * **************************************************************************** */

        $jsonData->properties->FechaEnvio = new stdClass();
        $jsonData->properties->FechaEnvio->type = 'date';
        $jsonData->properties->FechaEnvio->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque Observaciones                                     *
        *                                                                              *
        * **************************************************************************** */

        $jsonData->properties->Observaciones = new stdClass();
        $jsonData->properties->Observaciones->type = 'text';
        $jsonData->properties->Observaciones->analyzer = 'spanish';


        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque Estado                                     *
        *                                                                              *
        * **************************************************************************** */

        $jsonData->properties->Estado = new stdClass();
        $jsonData->properties->Estado->type = 'object';
        $jsonData->properties->Estado->properties = new stdClass();
        $jsonData->properties->Estado->properties->Inicial = new stdClass();
        $jsonData->properties->Estado->properties->Inicial->type = 'object';
        $jsonData->properties->Estado->properties->Inicial->properties = new stdClass();
        $jsonData->properties->Estado->properties->Inicial->properties->Id = new stdClass();
        $jsonData->properties->Estado->properties->Inicial->properties->Id->type = 'integer';
        $jsonData->properties->Estado->properties->Inicial->properties->Nombre = new stdClass();
        $jsonData->properties->Estado->properties->Inicial->properties->Nombre->type = 'text';
        $jsonData->properties->Estado->properties->Inicial->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->Estado->properties->Inicial->properties->Nombre->fields = new stdClass();
        $jsonData->properties->Estado->properties->Inicial->properties->Nombre->fields->raw = new stdClass();
        $jsonData->properties->Estado->properties->Inicial->properties->Nombre->fields->raw->type = 'keyword';
        $jsonData->properties->Estado->properties->Final = new stdClass();
        $jsonData->properties->Estado->properties->Final->type = 'object';
        $jsonData->properties->Estado->properties->Final->properties = new stdClass();
        $jsonData->properties->Estado->properties->Final->properties->Id = new stdClass();
        $jsonData->properties->Estado->properties->Final->properties->Id->type = 'integer';
        $jsonData->properties->Estado->properties->Final->properties->Nombre = new stdClass();
        $jsonData->properties->Estado->properties->Final->properties->Nombre->type = 'text';
        $jsonData->properties->Estado->properties->Final->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->Estado->properties->Final->properties->Nombre->fields = new stdClass();
        $jsonData->properties->Estado->properties->Final->properties->Nombre->fields->raw = new stdClass();
        $jsonData->properties->Estado->properties->Final->properties->Nombre->fields->raw->type = 'keyword';

        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque Area                                     *
        *                                                                              *
        * **************************************************************************** */

        $jsonData->properties->Area = new stdClass();
        $jsonData->properties->Area->type = 'object';
        $jsonData->properties->Area->properties = new stdClass();
        $jsonData->properties->Area->properties->Inicial = new stdClass();
        $jsonData->properties->Area->properties->Inicial->type = 'object';
        $jsonData->properties->Area->properties->Inicial->properties = new stdClass();
        $jsonData->properties->Area->properties->Inicial->properties->Id = new stdClass();
        $jsonData->properties->Area->properties->Inicial->properties->Id->type = 'integer';
        $jsonData->properties->Area->properties->Inicial->properties->Nombre = new stdClass();
        $jsonData->properties->Area->properties->Inicial->properties->Nombre->type = 'text';
        $jsonData->properties->Area->properties->Inicial->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->Area->properties->Inicial->properties->Nombre->fields = new stdClass();
        $jsonData->properties->Area->properties->Inicial->properties->Nombre->fields->raw = new stdClass();
        $jsonData->properties->Area->properties->Inicial->properties->Nombre->fields->raw->type = 'keyword';
        $jsonData->properties->Area->properties->Final = new stdClass();
        $jsonData->properties->Area->properties->Final->type = 'object';
        $jsonData->properties->Area->properties->Final->properties = new stdClass();
        $jsonData->properties->Area->properties->Final->properties->Id = new stdClass();
        $jsonData->properties->Area->properties->Final->properties->Id->type = 'integer';
        $jsonData->properties->Area->properties->Final->properties->Nombre = new stdClass();
        $jsonData->properties->Area->properties->Final->properties->Nombre->type = 'text';
        $jsonData->properties->Area->properties->Final->properties->Nombre->analyzer = 'spanish';
        $jsonData->properties->Area->properties->Final->properties->Nombre->fields = new stdClass();
        $jsonData->properties->Area->properties->Final->properties->Nombre->fields->raw = new stdClass();
        $jsonData->properties->Area->properties->Final->properties->Nombre->fields->raw->type = 'keyword';

        /* **************************************************************************** *
         *                                                                              *
         *                          Bloque Alta  Modificacion                                       *
         *                                                                              *
         * **************************************************************************** */

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

        $jsonData->properties->Alta->properties->Escuela = new stdClass();
        $jsonData->properties->Alta->properties->Escuela->properties = new stdClass();
        $jsonData->properties->Alta->properties->Escuela->properties->Id = new stdClass();
        $jsonData->properties->Alta->properties->Escuela->properties->Id->type = 'integer';
        $jsonData->properties->Alta->properties->Escuela->properties->Nombre = new stdClass();
        $jsonData->properties->Alta->properties->Escuela->properties->Nombre->type = 'keyword';
        $jsonData->properties->Alta->properties->Rol = new stdClass();
        $jsonData->properties->Alta->properties->Rol->properties = new stdClass();
        $jsonData->properties->Alta->properties->Rol->properties->Id = new stdClass();
        $jsonData->properties->Alta->properties->Rol->properties->Id->type = 'integer';
        $jsonData->properties->Alta->properties->Rol->properties->Nombre = new stdClass();
        $jsonData->properties->Alta->properties->Rol->properties->Nombre->type = 'keyword';


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
        $jsonData->properties->UltimaModificacion->properties->Escuela = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Escuela->properties = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Escuela->properties->Id = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Escuela->properties->Id->type = 'integer';
        $jsonData->properties->UltimaModificacion->properties->Escuela->properties->Nombre = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Escuela->properties->Nombre->type = 'keyword';
        $jsonData->properties->UltimaModificacion->properties->Rol = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Rol->properties = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Rol->properties->Id = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Rol->properties->Id->type = 'integer';
        $jsonData->properties->UltimaModificacion->properties->Rol->properties->Nombre = new stdClass();
        $jsonData->properties->UltimaModificacion->properties->Rol->properties->Nombre->type = 'keyword';


        return $devolverJson? json_encode($jsonData) : $jsonData;
	}
	
	/**
	 * @inheritDoc
	 */
	public static function Estructura($devolverJson = true)
	{
		$jsonData = Novedades::Estructura(false);
		
		$jsonData->AccionCambio = new Tipos\Keyword();
		$jsonData->IdFilaLog = new Tipos\Entero();
		
		
		/* **************************************************************************** *
		 *                                                                              *
		 *                            Bloque Estado                                     *
		 *                                                                              *
		 * **************************************************************************** */
		
		
		$estadoInicial = clone $jsonData->Estado;
		$estadoFinal = clone $jsonData->Estado;
		$jsonData->Estado = new Tipos\Objeto();
		$jsonData->Estado->Inicial = $estadoInicial;
		$jsonData->Estado->Final = $estadoFinal;
		
		/* **************************************************************************** *
		 *                                                                              *
		 *                              Bloque Area                                     *
		 *                                                                              *
		 * **************************************************************************** */
		
		
		$areaInicial = clone $jsonData->Area;
		$areaFinal = clone $jsonData->Area;
		$jsonData->Area = new Tipos\Objeto();
		$jsonData->Area->Inicial = $areaInicial;
		$jsonData->Area->Final = $areaFinal;
		
		return $devolverJson ? json_encode($jsonData) : $jsonData;
	}
	
	/**
	 * @param array $datos
	 * @param bool  $encode
	 * @return array|false|object|string
	 * @deprecated
	 */
	public static function armarDatosElastic_(array $datos, bool $encode = false)
	{
		$jsonData = new stdClass();
		
		$jsonData->Id = $datos['Id'];
        $jsonData->IdFilaLog = $datos['Id'];

        $jsonData->Tipo = new stdClass();
        $jsonData->Tipo = 'Documento';

        if(!FuncionesPHPLocal::isEmpty($datos['AccionCambio'])) {
            $jsonData->AccionCambio = new stdClass();
            $jsonData->AccionCambio = $datos['AccionCambio'];
        }

        if(!FuncionesPHPLocal::isEmpty($datos['IdEscuela'])) {
            $jsonData->IdEscuela = new stdClass();
            $jsonData->IdEscuela = $datos['IdEscuela'];
        }

        if(!FuncionesPHPLocal::isEmpty($datos['IdEscuelaDestino'])) {
            $jsonData->IdEscuelaDestino = new stdClass();
            $jsonData->IdEscuelaDestino = $datos['IdEscuelaDestino'];
        }

        if(!FuncionesPHPLocal::isEmpty($datos['IdDocumento'])) {
            $jsonData->IdDocumento = new stdClass();
            $jsonData->IdDocumento = $datos['IdDocumento'];
        }

        if(!FuncionesPHPLocal::isEmpty($datos['IdDocumentoPadre'])) {
            $jsonData->IdDocumentoPadre = new stdClass();
            $jsonData->IdDocumentoPadre = $datos['IdDocumentoPadre'] ;
        }

        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque Escuela                                       *
        *                                                                              *
        * **************************************************************************** */
        if(!FuncionesPHPLocal::isEmpty($datos['IdEscuela'])) {
            $jsonData->Escuela = new stdClass();
            $jsonData->Escuela->Id = $datos['IdEscuela'];
            $jsonData->Escuela->Nombre = $datos['EscuelaNombre'];

        }

        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque Escuela Destino                                     *
        *                                                                              *
        * **************************************************************************** */

        if(!FuncionesPHPLocal::isEmpty($datos['IdEscuelaDestino'])) {
            $jsonData->EscuelaDestino = new stdClass();
            $jsonData->EscuelaDestino->Id = $datos['IdEscuelaDestino'];
            $jsonData->EscuelaDestino->Nombre = $datos['EscuelaDestinoNombre'];

        }

        /* **************************************************************************** *
        *                                                                              *
        *                          Bloque Tipo Documento                                       *
        *                                                                              *
        * **************************************************************************** */


        if(!FuncionesPHPLocal::isEmpty($datos['IdTipoDocumento']))
        {
            $jsonData->TipoDocumento = new stdClass();
            $jsonData->TipoDocumento->Id = $datos['IdTipoDocumento'];
            $jsonData->TipoDocumento->IdRegistro = $datos['IdRegistroTipoDocumento'];
            $jsonData->TipoDocumento->Nombre = $datos['NombreTipoDocumento'];
            $jsonData->TipoDocumento->NombreCorto = $datos['NombreCortoTipoDocumento'];

            if (!FuncionesPHPLocal::isEmpty($datos['IdCategoria'])) {
                $jsonData->TipoDocumento->Categoria = new stdClass();
                $jsonData->TipoDocumento->Categoria->Id = $datos['IdCategoria'];
                $jsonData->TipoDocumento->Categoria->Nombre = $datos['CategoriaNombre'];
            }
            if (!FuncionesPHPLocal::isEmpty($datos['IdClasificacion'])) {
                $jsonData->TipoDocumento->Clasificacion = new stdClass();
                $jsonData->TipoDocumento->Clasificacion->Id = $datos['IdClasificacion'];
                $jsonData->TipoDocumento->Clasificacion->Nombre = $datos['ClasificacionNombre'];
            }

        }

        if(!FuncionesPHPLocal::isEmpty($datos['IdPersona']))
        {
            $jsonData->Agente = new stdClass();
            $jsonData->Agente->Id = $datos['IdPersona'] ?? null;
            if(!FuncionesPHPLocal::isEmpty($datos['CuilPersona']))
                $jsonData->Agente->Cuil =$datos['CuilPersona'];
            if(!FuncionesPHPLocal::isEmpty($datos['DniPersona']))
                $jsonData->Agente->Dni =$datos['DniPersona'];
            if(!FuncionesPHPLocal::isEmpty($datos['SexoPersona']))
                $jsonData->Agente->Sexo =$datos['SexoPersona'];
            if(!FuncionesPHPLocal::isEmpty($datos['NombrePersona']))
                $jsonData->Agente->Nombre =$datos['NombrePersona'];
            if(!FuncionesPHPLocal::isEmpty($datos['ApellidoPersona']))
                $jsonData->Agente->Apellido =$datos['ApellidoPersona'];
            if(!FuncionesPHPLocal::isEmpty($datos['NombreCompletoPersona']))
                $jsonData->Agente->NombreCompleto =$datos['NombreCompletoPersona'];
            if(!FuncionesPHPLocal::isEmpty($datos['EmailPersona']))
                $jsonData->Agente->Email =$datos['EmailPersona'];
            if(!FuncionesPHPLocal::isEmpty($datos['TelefonoPersona']))
                $jsonData->Agente->Telefono =$datos['TelefonoPersona'];
            if(!FuncionesPHPLocal::isEmpty($datos['FallecidoFechaPersona']))
                $jsonData->Agente->FechaFallecido =$datos['FallecidoFechaPersona'];
            if(!FuncionesPHPLocal::isEmpty($datos['BajaFechaPersona']))
                $jsonData->Agente->FechaBaja =$datos['BajaFechaPersona'];
            if(!FuncionesPHPLocal::isEmpty($datos['NombreTipoDocumentoPersona']))
                $jsonData->Agente->TipoDocumentoNombre =$datos['NombreTipoDocumentoPersona'];


        }

        if(!FuncionesPHPLocal::isEmpty($datos['PeriodoFechaDesde']) || !FuncionesPHPLocal::isEmpty($datos['PeriodoFechaHasta']) )
        {
            $jsonData->Periodo = new stdClass();
            if(!FuncionesPHPLocal::isEmpty($datos['PeriodoFechaDesde']))
                $jsonData->Periodo->FechaDesde = $datos['PeriodoFechaDesde'];
            if(!FuncionesPHPLocal::isEmpty($datos['PeriodoFechaHasta']))
                $jsonData->Periodo->FechaHasta = $datos['PeriodoFechaHasta'];

        }

        if(!FuncionesPHPLocal::isEmpty($datos['IdLicencia'])) {
            $jsonData->Licencia = new stdClass();
            $jsonData->Licencia->Id = $datos['IdLicencia'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdLicencia'])) {
            $jsonData->Licencia->Tipo = new stdClass();
            $jsonData->Licencia->Tipo->Id = $datos['IdTipoLicencia'];
            $jsonData->Licencia->Tipo->Nombre = $datos['NombreTipoLicencia'];
        }


        if (!FuncionesPHPLocal::isEmpty($datos['Observaciones'])) {
            $jsonData->Observaciones = new stdClass();
            $jsonData->Observaciones = $datos['Observaciones'];
        }

        $jsonData->Area = new stdClass();
        if (!FuncionesPHPLocal::isEmpty( $datos['Area']['Inicial']['Id'])) {
            $jsonData->Area->Inicial = new stdClass();
            $jsonData->Area->Inicial->Id = $datos['Area']['Inicial']['Id'];
            $jsonData->Area->Inicial->Nombre = $datos['Area']['Inicial']['Nombre'];
        }


        if (!FuncionesPHPLocal::isEmpty( $datos['Area']['Final']['Id'])) {
        $jsonData->Area->Final = new stdClass();
        $jsonData->Area->Final->Id = $datos['Area']['Final']['Id'];
        $jsonData->Area->Final->Nombre = $datos['Area']['Final']['Nombre'];
        }

        $jsonData->Estado = new stdClass();
        if (!FuncionesPHPLocal::isEmpty( $datos['Estado']['Inicial']['Id'])) {
            $jsonData->Estado->Inicial = new stdClass();
            $jsonData->Estado->Inicial->Id = $datos['Estado']['Inicial']['Id'];
            $jsonData->Estado->Inicial->Nombre = $datos['Estado']['Inicial']['Nombre'];
        }

        if (!FuncionesPHPLocal::isEmpty( $datos['Estado']['Final']['Id'])) {
            $jsonData->Estado->Final = new stdClass();
            $jsonData->Estado->Final->Id = $datos['Estado']['Final']['Id'];
            $jsonData->Estado->Final->Nombre = $datos['Estado']['Final']['Nombre'];
        }
        if (!FuncionesPHPLocal::isEmpty($datos['MovimientoFecha'])) {
            $jsonData->MovimientoFecha = new stdClass();
            $jsonData->MovimientoFecha = $datos['MovimientoFecha'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['FechaEnvio'])) {
            $jsonData->FechaEnvio = new stdClass();
            $jsonData->FechaEnvio = $datos['FechaEnvio'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['AltaFecha'])) {
            $jsonData->Alta = new stdClass();
            $jsonData->Alta->Fecha = $datos['AltaFecha'];

            if (!FuncionesPHPLocal::isEmpty($datos['AltaUsuario'])) {
                $jsonData->Alta->Usuario = new stdClass();
                $jsonData->Alta->Usuario->Id = $datos['AltaUsuario'];
                $jsonData->Alta->Usuario->Nombre = $datos['NombreAltaUsuario'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['AltaEscuela'])) {
                $jsonData->Alta->Escuela = new stdClass();
                $jsonData->Alta->Escuela->Id = $datos['AltaEscuela'];
                $jsonData->Alta->Escuela->Nombre = $datos['NombreAltaEscuela'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['AltaRol'])) {
                $jsonData->Alta->Rol = new stdClass();
                $jsonData->Alta->Rol->Id = $datos['AltaRol'];
                $jsonData->Alta->Rol->Nombre = $datos['NombreAltaRol'];
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

            if (!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionEscuela'])) {
                $jsonData->UltimaModificacion->Escuela = new stdClass();
                $jsonData->UltimaModificacion->Escuela->Id = $datos['UltimaModificacionEscuela'];
                $jsonData->UltimaModificacion->Escuela->Nombre = $datos['NombreUltimaModificacionEscuela'];
            }

            if (!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionRol'])) {
                $jsonData->UltimaModificacion->Rol = new stdClass();
                $jsonData->UltimaModificacion->Rol->Id = $datos['UltimaModificacionRol'];
                $jsonData->UltimaModificacion->Rol->Nombre = $datos['NombreUltimaModificacionRol'];
            }
        }
		
		$jsonData = FuncionesPHPLocal::ConvertiraUtf8($jsonData);

		return $encode? json_encode($jsonData) : $jsonData;
	}
	
	/**
	 * @inheritDoc
	 */
	public static function armarDatosElastic(array $datos, bool $encode = false)
	{
		$jsonData = Novedades::armarDatosElastic($datos);
		
		$jsonData->IdFilaLog = $datos['Id'];
		
		$jsonData->Tipo = new stdClass();
		$jsonData->Tipo = 'Documento';
		
		if (!FuncionesPHPLocal::isEmpty($datos['AccionCambio']))
		{
			$jsonData->AccionCambio = new stdClass();
			$jsonData->AccionCambio = $datos['AccionCambio'];
		}
		
		
		$jsonData->Area = new stdClass();
		if (!FuncionesPHPLocal::isEmpty($datos['Area']['Inicial']['Id']))
		{
			$jsonData->Area->Inicial = new stdClass();
			$jsonData->Area->Inicial->Id = $datos['Area']['Inicial']['Id'];
			$jsonData->Area->Inicial->Nombre = $datos['Area']['Inicial']['Nombre'];
		}
		
		
		if (!FuncionesPHPLocal::isEmpty($datos['Area']['Final']['Id']))
		{
			$jsonData->Area->Final = new stdClass();
			$jsonData->Area->Final->Id = $datos['Area']['Final']['Id'];
			$jsonData->Area->Final->Nombre = $datos['Area']['Final']['Nombre'];
		}
		
		$jsonData->Estado = new stdClass();
		if (!FuncionesPHPLocal::isEmpty($datos['Estado']['Inicial']['Id']))
		{
			$jsonData->Estado->Inicial = new stdClass();
			$jsonData->Estado->Inicial->Id = $datos['Estado']['Inicial']['Id'];
			$jsonData->Estado->Inicial->Nombre = $datos['Estado']['Inicial']['Nombre'];
		}
		
		if (!FuncionesPHPLocal::isEmpty($datos['Estado']['Final']['Id']))
		{
			$jsonData->Estado->Final = new stdClass();
			$jsonData->Estado->Final->Id = $datos['Estado']['Final']['Id'];
			$jsonData->Estado->Final->Nombre = $datos['Estado']['Final']['Nombre'];
		}
		
		
		$jsonData = FuncionesPHPLocal::ConvertiraUtf8($jsonData);
		
		return $encode ? json_encode($jsonData) : $jsonData;
	}
	
	/**
	 * @inheritDoc
	 */
	public static function obtenerId($datos)
	{
		/*if(is_array($datos) && isset($datos['Id']))
			return (int) $datos['Id'];
		if(is_object($datos) && isset($datos->Id))
			return (int) $datos->Id;*/
		return null;
	}
	
	/**
	 * @inheritDoc
	 */
	public static function getIndex(): string
	{
		return self::INDEX;
	}
	
	/**
	 *
	 * @param array       $datos
	 * @param array|null  $resultado
	 * @param int|null    $numfilas
	 * @param int|null    $total
	 * @param string|null $scroll_id
	 * @return bool
	 */
	public function BuscarLogDocumentos(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total, ?string &$scroll_id): bool
    {
        $datosEnviar = new stdClass();
        $datosEnviar->query = new stdClass();
        $datosEnviar->query->bool = new stdClass();
        $datosEnviar->query->bool->must = [];
        $datosEnviar->query->bool->filter = [];

        $datosEnviar->from = $datos['from'] ?? 0;
        $datosEnviar->size = $datos['size'] ?? 20;

        $SortField = "UltimaModificacion.Fecha";
        $SortOrder = "desc";

        $i = 0;

        $scroll = "";
        if(!empty($datos['scroll']) && preg_match("/\d+[dhms]/",$datos['scroll']))
        {
            $scroll = "&scroll={$datos['scroll']}";
            unset($datos['scroll']);
        }
        $datosEnviar->sort = array();
        if (isset($datos['sidx']) && is_array($datos['sidx']) && count($datos['sidx'])>0)
        {
            foreach($datos['sidx'] as $Order)
            {
                $datosEnviar->sort[0] = new stdClass();
                $datosEnviar->sort[0]->{$Order['Field']} = new stdClass();
                $datosEnviar->sort[0]->{$Order['Field']}->order = $Order['Sort'];
            }
        }else{
            if (isset($datos['sidx']) && $datos['sidx']!="")
                $SortField = $datos['sidx'];
            if (isset($datos['sord']) && $datos['sord']!="")
                $SortOrder = $datos['sord'];

            $datosEnviar->sort[0] = new stdClass();
            $datosEnviar->sort[0]->{$SortField} = new stdClass();
            $datosEnviar->sort[0]->{$SortField}->order = $SortOrder;
        }


        $datosEnviar->query->bool->filter[$i] = new stdClass();
        $datosEnviar->query->bool->filter[$i]->term = new stdClass();
        $datosEnviar->query->bool->filter[$i]->term->{'IdDocumento'} = new stdClass();
        $datosEnviar->query->bool->filter[$i]->term->{'IdDocumento'}->value = $datos['IdDocumento'];
        $i++;

        $Campo="UltimaModificacion.Fecha";
        if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
        {
            $datosEnviar->query->bool->filter[$i] = new stdClass();
            $datosEnviar->query->bool->filter[$i]->range = [new stdClass];
            $datosEnviar->query->bool->filter[$i]->range[$Campo]->gte = new stdClass();
            $datosEnviar->query->bool->filter[$i]->range[$Campo]->gte->field = $datos['FechaDesde']." 00:00:00";
            if (isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
            {
                $datosEnviar->query->bool->filter[$i]->range[$Campo]->lt = new stdClass();
                $datosEnviar->query->bool->filter[$i]->range[$Campo]->lt->field = $datos['FechaHasta']." 23:59:59";
            }
            else
            {
                $datosEnviar->query->bool->filter[$i]->range[$Campo]->lte = new stdClass();
                $datosEnviar->query->bool->filter[$i]->range[$Campo]->lte->field = date("d/m/Y H:i:s");
            }

            $datosEnviar->query->bool->filter[$i]->range[$Campo]->format->field = "dd/MM/yyyy HH:mm:ss";
            $i++;
        }



        $cuerpo = json_encode($datosEnviar);

        //echo $cuerpo;die;
        //$this->cnx->setDebug(true);


        if(!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $data, $codigoRetorno, 'track_total_hits=true'.$scroll))
        {
            $this->setError($this->cnx->getError());
            return false;
        }

        if (!isset($data['hits']))
        {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }
        $numfilas = intval($data['hits']['total']['value']);
        $total = (int) $data['hits']['total']['value'];
        $resultado = FuncionesPHPLocal::ConvertiraUtf8($data['hits']['hits']);
        if(isset($data['_scroll_id']))
            $scroll_id = $data['_scroll_id'];

        return true;
    }
}