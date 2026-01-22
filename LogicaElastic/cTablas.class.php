<?php

namespace Elastic;

use Bigtree\ExcepcionBase;
use Bigtree\ExcepcionES;
use stdClass;
use FuncionesPHPLocal;
use ManejoErrores;


/**
 * Class Tablas
 *
 * @package Elastic
 */
class Tablas implements InterfaceBase {
	use ManejoErrores;

	/** @var string */
	private const INDEX = INDEXPREFIX . SUFFIX_TABLAS;
	/** @var Conexion */
	private $cnx;

	/**
	 * Tablas constructor.
	 *
	 * @param Conexion $cnx
	 */
	public function __construct(Conexion $cnx) {
		$this->cnx =& $cnx;
	}

	/**
	 * @inheritDoc
	 */
	public static function Configuracion(stdClass &$jsonData): void {

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
		/* Filtros posibles
		 * Para Escuelas:
		 * - Region
		 * - Nivel
		 * - Modalidad
		 * - Distrito
		 * - Localidad
		 * - Departamento
		 * - Dependencia
		 * - Ambito
		 * - Municipio
		 * - Planes educativos
		 * Para Autorizantes:
		 * - Especialidades
		 */


		$jsonData = new Tipos\Mapping('strict');

		$jsonData->Tabla = new Tipos\Keyword();

		$jsonData->Id = new Tipos\Entero();

		$jsonData->Nombre = (new Tipos\Texto('spanish'))
			->addField('prefix', new Tipos\Autocompletar('custom_es'))
			->addField('raw', new Tipos\Keyword());

		$jsonData->Identificadores = (new Tipos\Keyword())
			->addField('prefix', new Tipos\Autocompletar('custom_es'));


		/*             Campos para Escuelas               */
		$jsonData->Coordenadas = new Tipos\Coordenadas();

		$jsonData->Region = new Tipos\Objeto();
		$jsonData->Region->Id = new Tipos\Entero();
		$jsonData->Region->Nombre = (new Tipos\Keyword())
			->noIndexar();

		$jsonData->Distrito = new Tipos\Objeto();
		$jsonData->Distrito->Id = new Tipos\Entero();
		$jsonData->Distrito->Nombre = (new Tipos\Keyword())
			->noIndexar();

		$jsonData->Localidad = new Tipos\Objeto();
		$jsonData->Localidad->Id = new Tipos\Entero();
		$jsonData->Localidad->Nombre = (new Tipos\Keyword())
			->noIndexar();

		$jsonData->Departamento = new Tipos\Objeto();
		$jsonData->Departamento->Id = new Tipos\Entero();
		$jsonData->Departamento->Nombre = (new Tipos\Keyword())
			->noIndexar();

		$jsonData->Dependencia = new Tipos\Objeto();
		$jsonData->Dependencia->Id = new Tipos\Entero();
		$jsonData->Dependencia->Nombre = (new Tipos\Keyword())
			->noIndexar();

		$jsonData->Ambito = new Tipos\Objeto();
		$jsonData->Ambito->Id = new Tipos\Entero();
		$jsonData->Ambito->Nombre = (new Tipos\Keyword())
			->noIndexar();

		$jsonData->Municipio = new Tipos\Objeto();
		$jsonData->Municipio->Id = new Tipos\Entero();
		$jsonData->Municipio->Nombre = (new Tipos\Keyword())
			->noIndexar();

		$jsonData->Niveles = new Tipos\Entero();

		$jsonData->Modalidades = new Tipos\Entero();

		$jsonData->PlanesEducativos = new Tipos\Entero();
		/**************************************************/

		/*           Campos para Autorizantes             */
		$jsonData->Especialidades = new Tipos\Entero();
		/**************************************************/

		/*           Campos para Usuarios             */
		$jsonData->Region = new Tipos\Objeto();
		$jsonData->Region->Id = new Tipos\Entero();
		$jsonData->Region->Nombre = new Tipos\Keyword();

		$jsonData->Escuela = new Tipos\Objeto();
		$jsonData->Escuela->Id = new Tipos\Entero();
		$jsonData->Escuela->Nombre = new Tipos\Keyword();

		$jsonData->Roles = new Tipos\Objeto();
		$jsonData->Roles->Id = new Tipos\Entero();
		$jsonData->Roles->Nombre = new Tipos\Keyword();
		/**************************************************/

		$jsonData->Datos = new Tipos\Json();

		$jsonData->Estado = new Tipos\Objeto();
		$jsonData->Estado->Id = new Tipos\Byte();
		$jsonData->Estado->Nombre = (new Tipos\Keyword())
			->noIndexar();

		$jsonData->Alta = new Tipos\Objeto();
		$jsonData->Alta->Fecha = new Tipos\Fecha();
		$jsonData->Alta->Usuario = new Tipos\Objeto();
		$jsonData->Alta->Usuario->Id = new Tipos\Entero();
		$jsonData->Alta->Usuario->Nombre = (new Tipos\Keyword())
			->noIndexar();

		$jsonData->UltimaModificacion = new Tipos\Objeto();
		$jsonData->UltimaModificacion->Fecha = new Tipos\Fecha();
		$jsonData->UltimaModificacion->Usuario = new Tipos\Objeto();
		$jsonData->UltimaModificacion->Usuario->Id = new Tipos\Entero();
		$jsonData->UltimaModificacion->Usuario->Nombre = (new Tipos\Keyword())
			->noIndexar();


		return $devolverJson ? json_encode($jsonData) : $jsonData;
	}

	/**
	 * @inheritDoc
	 * @throws ExcepcionES
	 * @throws ExcepcionBase
	 */
	public static function armarDatosElastic(array $datos, bool $encode = false) {
		$jsonData = new stdClass();

		if (FuncionesPHPLocal::isEmpty($datos['Tabla']))
			throw new ExcepcionES('Debe ingresar una tabla');

		$jsonData->Tabla = $datos['Tabla'];

		$id = self::procesarIdTabla($datos);
		if (FuncionesPHPLocal::isEmpty($id))
			throw new ExcepcionES('Debe ingresar un id');

		$jsonData->Id = (int) $id;

		$jsonData->Nombre = $datos['Nombre'] ?: NULL;

		$jsonData->Identificadores = $datos['Identificadores'] ?: NULL;

		if (!FuncionesPHPLocal::isEmpty($datos['Coordenadas']))
			$jsonData->Coordenadas = $datos['Coordenadas'];

		if (!FuncionesPHPLocal::isEmpty($datos['IdRegion'])) {
			$jsonData->Region = new stdClass();
			$jsonData->Region->Id = (int) $datos['IdRegion'];
			$jsonData->Region->Nombre = $datos['RegionNombre'] ?? NULL;
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdDistrito'])) {
			$jsonData->Distrito = new stdClass();
			$jsonData->Distrito->Id = (int) $datos['IdDistrito'];
			$jsonData->Distrito->Nombre = $datos['DistritoNombre'] ?? NULL;
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdLocalidad'])) {
			$jsonData->Localidad = new stdClass();
			$jsonData->Localidad->Id = (int) $datos['IdLocalidad'];
			$jsonData->Localidad->Nombre = $datos['LocalidadNombre'] ?? NULL;
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdDepartamento'])) {
			$jsonData->Departamento = new stdClass();
			$jsonData->Departamento->Id = (int) $datos['IdDepartamento'];
			$jsonData->Departamento->Nombre = $datos['DepartamentoNombre'] ?? NULL;
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdDependencia'])) {
			$jsonData->Dependencia = new stdClass();
			$jsonData->Dependencia->Id = (int) $datos['IdDependencia'];
			$jsonData->Dependencia->Nombre = $datos['DependenciaNombre'] ?? NULL;
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdAmbito'])) {
			$jsonData->Ambito = new stdClass();
			$jsonData->Ambito->Id = (int) $datos['IdAmbito'];
			$jsonData->Ambito->Nombre = $datos['AmbitoNombre'] ?? NULL;
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdMunicipio'])) {
			$jsonData->Municipio = new stdClass();
			$jsonData->Municipio->Id = (int) $datos['IdMunicipio'];
			$jsonData->Municipio->Nombre = $datos['MunicipioNombre'] ?? NULL;
		}

		if (!FuncionesPHPLocal::isEmpty($datos['Niveles'])) {
			$jsonData->Niveles = is_array($datos['Niveles']) ?
				$datos['Niveles'] : explode(',', $datos['Niveles']);
		}

		if (!FuncionesPHPLocal::isEmpty($datos['Modalidades'])) {
			$jsonData->Modalidades = is_array($datos['Modalidades']) ?
				$datos['Modalidades'] : explode(',', $datos['Modalidades']);
		}

		if (!FuncionesPHPLocal::isEmpty($datos['PlanesEducativos'])) {
			$jsonData->PlanesEducativos = is_array($datos['PlanesEducativos']) ?
				$datos['PlanesEducativos'] : explode(',', $datos['PlanesEducativos']);
		}

		if (!FuncionesPHPLocal::isEmpty($datos['Especialidades'])) {
			$jsonData->Especialidades = is_array($datos['Especialidades']) ?
				$datos['Especialidades'] : explode(',', $datos['Especialidades']);
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdRoles'])) {
			$jsonData->Roles = new stdClass();
			$jsonData->Roles->Id = is_array($datos['IdRoles']) ? $datos['IdRoles'] : explode(',', $datos['IdRoles']);
			$jsonData->Roles->Nombre = is_array($datos['Roles']) ? $datos['Roles'] : explode(',', $datos['Roles']);
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdEscuelas'])) {
			$jsonData->Roles = new stdClass();
			$jsonData->Roles->Id = is_array($datos['IdEscuelas']) ? $datos['IdEscuelas'] : explode(',', $datos['IdEscuelas']);
			$jsonData->Roles->Nombre = is_array($datos['Escuelas']) ? $datos['Escuelas'] : explode(',', $datos['Escuelas']);
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdRegiones'])) {
			$jsonData->Roles = new stdClass();
			$jsonData->Roles->Id = is_array($datos['IdRegiones']) ? $datos['IdRegiones'] : explode(',', $datos['IdRegiones']);
			$jsonData->Roles->Nombre = is_array($datos['Regiones']) ? $datos['Regiones'] : explode(',', $datos['Regiones']);
		}

		$Tabla = $datos['Tabla'];
		unset($datos['Tabla']);
		$jsonData->Datos = (object) $datos;


		$jsonData->Estado = new stdClass();
		$jsonData->Estado->Id = (int) $datos['Estado'];
		$jsonData->Estado->Nombre = FuncionesPHPLocal::getEstado($datos['Estado'], $Tabla);

		$jsonData->Alta = new stdClass();
		$jsonData->Alta->Fecha = $datos['AltaFecha'];
		$jsonData->Alta->Usuario = new stdClass();
		$jsonData->Alta->Usuario->Id = (int) $datos['AltaUsuario'];
		$jsonData->Alta->Usuario->Nombre = $datos['AltaUsuarioNombre'];

		$jsonData->UltimaModificacion = new stdClass();
		$jsonData->UltimaModificacion->Fecha = $datos['UltimaModificacionFecha'];
		$jsonData->UltimaModificacion->Usuario = new stdClass();
		$jsonData->UltimaModificacion->Usuario->Id = (int) $datos['UltimaModificacionUsuario'];
		$jsonData->UltimaModificacion->Usuario->Nombre = $datos['UltimaModificacionUsuarioNombre'];


		$jsonData = FuncionesPHPLocal::ConvertiraUtf8($jsonData);

		return $encode ? json_encode($jsonData) : $jsonData;
	}

	/**
	 * @inheritDoc
	 */
	public static function obtenerId($datos): ?string {
		if (is_array($datos) && isset($datos['Tabla'])) {
			$id = self::procesarIdTabla($datos);
			return isset($id) ? md5($datos['Tabla'] . $id) : NULL;
		} elseif (is_object($datos) && isset($datos->Tabla) && isset($datos->Id))
			return md5($datos->Tabla . $datos->Id);

		return NULL;
	}

	/**
	 * @inheritDoc
	 */
	public static function getIndex(): string {
		return self::INDEX;
	}

	/**
	 * @param array $datos
	 * @return string|null
	 */
	private static function procesarIdTabla(array $datos): ?string {
		switch ($datos['Tabla']) {
			case 'Escuelas':
				return isset($datos['IdEscuela']) ? $datos['IdEscuela'] : NULL;
			case 'Regiones':
				return isset($datos['IdRegion']) ? $datos['IdRegion'] : NULL;
			case 'Usuarios':
				//				return  isset($datos['Id']) ?  $datos['Id'] : null;
			case 'Autorizantes':
				return isset($datos['Id']) ? $datos['Id'] : NULL;
		}
		return NULL;
	}

	/**
	 * Destructor de la clase
	 */
	public function __destruct() {
	}

	public function autoCompletar(array $datos, ?array &$dataResult, ?string $tabla='Escuelas'): bool {

		$jsonData = new stdClass();
		$jsonData->size = 10;
		$jsonData->_source = new stdClass();
		$jsonData->_source->excludes = ['Alta.*', 'UltimaModificacion.*'];
		$jsonData->query = new stdClass();
		$jsonData->query->bool = new stdClass();
		$jsonData->query->bool->must = [new stdClass(), new stdClass()];
		$jsonData->query->bool->must[0]->term = new stdClass();
		$jsonData->query->bool->must[0]->term->Tabla = new stdClass();
		$jsonData->query->bool->must[0]->term->Tabla->value = $tabla;
		$jsonData->query->bool->must[1]->multi_match = new stdClass();
		$jsonData->query->bool->must[1]->multi_match->query = $datos['Cadena'];
		$jsonData->query->bool->must[1]->multi_match->type = 'bool_prefix';
		$jsonData->query->bool->must[1]->multi_match->fields = [
			'Nombre.prefix',
			'Nombre.prefix._2gram',
			'Nombre.prefix._3gram',
			'Identificadores.prefix',
			'Identificadores.prefix._2gram',
			'Identificadores.prefix._3gram',
		];

        if(!FuncionesPHPLocal::isEmpty($datos['Estado'])) {
            $jsonData->query->bool->must[2] = new stdClass();
            $jsonData->query->bool->must[2]->term = new stdClass();
            $jsonData->query->bool->must[2]->term->{'Estado.Id'} = new stdClass();;
            $jsonData->query->bool->must[2]->term->{'Estado.Id'}->value = $datos['Estado'];
        }

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
		$dataResult = $resultado['hits'];

		return true;
	}
}
