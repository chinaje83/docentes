<?php


namespace Elastic;


use Bigtree\ExcepcionLogica;
use Elastic\Consultas\Agg;
use Elastic\Consultas\Query;
use Elastic\Consultas\Sort;
use Elastic\Consultas\Source;
use Elastic\Consultas\Base as Consulta;
use FuncionesPHPLocal;
use ManejoErrores;
use stdClass;

class Licencias implements InterfaceBase {
	use ManejoErrores;

	/** @var string */
	private const INDEX = INDEXPREFIX . INDICELICENCIAS;
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
	 * Parametros espec�ficos del �ndice
	 *
	 * Esta funci�n agrega configuraciones propias del �ndice al objeto su objetivo es
	 * poder modificar la creaci�n de los �ndices desde las clases espec�ficas en lugar de
	 * requerir modificar cMapping el cual deber�a ser mayormente invariante
	 *
	 * @param stdClass $jsonData
	 */
	public static function Configuracion(&$jsonData): void {
	}

	/**
	 * Estructura de dados del �ndice
	 *
	 * @param bool $devolverJson
	 *
	 * @return false|stdClass|string
	 */
	public static function Estructura(bool $devolverJson = true) {
		$jsonData = new stdClass();
		$jsonData->dynamic = 'strict';

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
	public static function armarDatosElastic(array $datos, bool $encode = false) {
		$jsonData = new stdClass();

		return $encode ? json_encode($jsonData) : $jsonData;
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
	 * @param array|stdClass $datos
	 *
	 * @return integer|null
	 */
	public static function obtenerId($datos): ?int {
		if (is_array($datos) && isset($datos['Id']))
			return (int)$datos['Id'];
		if (is_object($datos) && isset($datos->Id))
			return (int)$datos->Id;
		return NULL;
	}

	public function __destruct() {
	}

	public function autoCompletarNombre(array $datos, ?array &$nombres): bool {
		//$datos['Nombre'] = preg_replace(self::PATTERN, self::REPLACEMENT, utf8_decode($datos['Nombre']));
		$nombres = [];
		$jsonData = new stdClass();
		$jsonData->size = 10;
		$jsonData->_source = 'Persona.NombreCompleto';
		$jsonData->query = new stdClass();
		$jsonData->query->multi_match = new stdClass();
		$jsonData->query->multi_match->query = $datos['Nombre'];
		$jsonData->query->multi_match->type = 'bool_prefix';
		$jsonData->query->multi_match->fields = [
			'Persona.NombreCompleto.prefix',
			'Persona.NombreCompleto.prefix._2gram',
			'Persona.NombreCompleto.prefix._3gram'
		];

		$jsonData->aggs = new stdClass();
		$jsonData->aggs->Respuestas = new stdClass();
		$jsonData->aggs->Respuestas->terms = new stdClass();
		$jsonData->aggs->Respuestas->terms->field = 'Persona.NombreCompleto.raw';
		$jsonData->aggs->Respuestas->terms->size = 15;

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
		//print_r($resultado['hits']);die;
		if ($resultado['hits']['total']['value'] < 1) {
			$this->setError(404, 'No se encuentra');
			return false;
		}

		foreach ($resultado['aggregations']['Respuestas']['buckets'] as $bucket)
			$nombres[] = $bucket['key'];


		return true;
	}

	/**
	 * Trae los datos del registro pedido
	 *
	 *
	 * @param array      $datos
	 * @param array|null $datosLicencia
	 *
	 * @return bool
	 */
	public function buscarxCodigo(array $datos, ?array &$datosLicencia): bool {

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

		$datosLicencia = $data['_source'];
		return true;
	}

	public function busquedaAvanzada(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total, ?string &$scroll_id = NULL): bool {
        $SortField = 'Id';
		$SortOrder = 'desc';
		$esControl = (bool)($datos['esControl'] ?? false);
		$jsonData = Consulta::nueva($datos['size'] ?? PAGINAR, $datos['from'] ?? 0);
		// $jsonData->_source = new stdClass();
		if (!FuncionesPHPLocal::isEmpty($datos['excluirCampos']))
			$jsonData->setSource(Source::excludes(...$datos['excluirCampos']));
		if (!FuncionesPHPLocal::isEmpty($datos['camposMostrar']))
			$jsonData->setSource(Source::includes(...$datos['camposMostrar']));

		//$jsonData->query = new stdClass();
		$bool = Query::bool();
		$sort = true;

		$scroll = "";
		if (!empty($datos['scroll']) && preg_match("/\d+[dhms]/", $datos['scroll'])) {
			$scroll = "&scroll={$datos['scroll']}";
			unset($datos['scroll']);
		}

        if (!FuncionesPHPLocal::isEmpty($datos['DiasHabilesAnteriores']) && !FuncionesPHPLocal::isEmpty($datos['Semaforo'])) {
            switch ($datos['Semaforo']) {
                case 'ultimas_24_horas':
                    $bool->addFilter(Query::range('FechaEnvio', ['gte'=>$datos['DiasHabilesAnteriores'][0] ]));
                        break;
                case 'entre_24_y_48_horas':
                    $bool->addFilter(Query::range('FechaEnvio', ['lte'=>$datos['DiasHabilesAnteriores'][0],  'gte'=>$datos['DiasHabilesAnteriores'][1]]));
                        break;
                case 'mas_de_48_horas':
                    $bool->addFilter(Query::range('FechaEnvio', ['lte'=>$datos['DiasHabilesAnteriores'][1] ]));
                        break;

            }

        }

        if (!FuncionesPHPLocal::isEmpty($_SESSION['IdEscuela']))
        {
            if (!FuncionesPHPLocal::isEmpty($datos["IdEscuela"]) && is_string($datos["IdEscuela"])) {
                $bool->addFilter(Query::nested('Cargos', Query::term('Cargos.Escuela.Id', (int)$datos['IdEscuela'])));
            }
            else {
                if(is_array($_SESSION['IdEscuela']))
                    $bool->addFilter(Query::nested('Cargos', Query::terms('Cargos.Escuela.Id', $_SESSION['IdEscuela'])));
                else
                    $bool->addFilter(Query::nested('Cargos', Query::term('Cargos.Escuela.Id', (int)$_SESSION['IdEscuela'])));
            }

        }
        elseif (!FuncionesPHPLocal::isEmpty($datos['IdEscuela']))
        {
            if(is_array($datos['IdEscuela']))
                $bool->addFilter(Query::nested('Cargos', Query::terms('Cargos.Escuela.Id', $datos['IdEscuela'])));
            else
                $bool->addFilter(Query::nested('Cargos', Query::term('Cargos.Escuela.Id', (int)$datos['IdEscuela'])));
        }

		if(!FuncionesPHPLocal::isEmpty($datos['IdRegion']))
			$bool->addFilter(Query::nested('Cargos', Query::term('Cargos.IdRegion', (int)$datos['IdRegion'])));

		if(!FuncionesPHPLocal::isEmpty($datos['IdLocalidad']))
			$bool->addFilter(Query::nested('Cargos', Query::term('Cargos.Localidad.Id', (int)$datos['IdLocalidad'])));

		if (!FuncionesPHPLocal::isEmpty($datos['Id']))
			$bool->addFilter(Query::term('Id', (int)$datos['Id']));

		if (!FuncionesPHPLocal::isEmpty($datos['IdPersona']))
			$bool->addFilter(Query::term('Persona.Id', (int)$datos['IdPersona']));

		if (!FuncionesPHPLocal::isEmpty($datos['Inicio']))
			$bool->addFilter(Query::term('Inicio', $datos['Inicio']));

		if (!FuncionesPHPLocal::isEmpty($datos['Fin']))
			$bool->addFilter(Query::term('Fin', $datos['Fin']));

		if (!FuncionesPHPLocal::isEmpty($datos['IdMotivo']))
			$bool->addFilter(Query::term('Motivo.Id', (int)$datos['IdMotivo']));

        if (!FuncionesPHPLocal::isEmpty($datos['IdArticulo']))
            $bool->addFilter(Query::nested('Cargos', Query::term('Cargos.Articulo.Id', (int)$datos['IdArticulo'])));

		if (!FuncionesPHPLocal::isEmpty($datos['IdDiagnostico']))
			$bool->addFilter(Query::term('Diagnostico.Id', (int)$datos['IdDiagnostico']));



        if (!FuncionesPHPLocal::isEmpty($datos['IdNivel'])) {
            if(is_array($datos['IdNivel']))
                $bool->addFilter(Query::nested('Cargos', Query::terms('Cargos.Escuela.Niveles.Id', $datos['IdNivel'])));

            else
                $bool->addFilter(Query::nested('Cargos', Query::term('Cargos.Escuela.Niveles.Id', $datos['IdNivel'])));
        }


        if (!FuncionesPHPLocal::isEmpty($datos['IdTipo'])) {
            $idTipos = is_array($datos['IdTipo']) ? $datos['IdTipo'] : [$datos['IdTipo']];

            $bool->addFilter(Query::terms('Tipo.Id', $idTipos));
        }

		if (!FuncionesPHPLocal::isEmpty($datos['IdTipoAutorizante']))
			$bool->addFilter(Query::term('Autorizante.Tipo.Id', (int)$datos['IdTipoAutorizante']));


		if (!FuncionesPHPLocal::isEmpty($datos['FechaDesde'])) {
			try {
				$bool->addFilter(Query::range('Inicio', ['gte' => FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd')]));
			} catch (ExcepcionLogica $e) {
				$this->setError($e->getError());
				return false;
			}
		}
		if (!FuncionesPHPLocal::isEmpty($datos['FechaHasta'])) {
			try {
				$bool->addFilter(Query::range('Fin', ['lte' => FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], 'dd/mm/aaaa', 'aaaa-mm-dd')]));
			} catch (ExcepcionLogica $e) {
				$this->setError($e->getError());
				return false;
			}
		}


		if (!FuncionesPHPLocal::isEmpty($datos['claseEstados']) || !FuncionesPHPLocal::isEmpty($datos['claseEstadosPublicos'])) {
			$subQuery = Query::bool();
			$arrayEstados = NULL;

			if (!FuncionesPHPLocal::isEmpty($datos['claseEstados'])) {
				$arrayEstados = is_array($datos['claseEstados']) ? $datos['claseEstados'] :
					explode(',', $datos['claseEstados']);

				$subQuery->addShould(Query::terms('Estado.Class', $arrayEstados));
			}


            if (!FuncionesPHPLocal::isEmpty($datos['claseEstadosPublicos'])) {

				$arrayEstados = is_array($datos['claseEstadosPublicos']) ? $datos['claseEstadosPublicos'] :
					explode(',', $datos['claseEstadosPublicos']);

				$subQuery->addShould(Query::terms('Estado.ClassPublica', $arrayEstados));
			}
			if (!FuncionesPHPLocal::isEmpty($arrayEstados)) {
				try {
					$subQuery->addShould(Query::bool()
						->addMust(Query::terms('Estado.ClassTmp', $arrayEstados))
						->addMust(Query::range('Estado.MostrarTmpHasta', ['gte' => time()]))
					);
				} catch (ExcepcionLogica $e) {
					$this->setError($e->getError());
					return false;
				}
			}

			if ($subQuery->countFilter() + $subQuery->countShould() + $subQuery->countMust() + $subQuery->countMustNot() > 0)
				$bool->addFilter($subQuery);
		}


		if (!FuncionesPHPLocal::isEmpty($datos['IdEstado']))
			$bool->addFilter(Query::terms('Estado.Id', is_array($datos['IdEstado']) ? $datos['IdEstado'] : explode(',', $datos['IdEstado'])));


		if (!FuncionesPHPLocal::isEmpty($datos['Duracion']))
			$bool->addFilter(Query::term('Duracion', (int)$datos['Duracion']));

		if (!FuncionesPHPLocal::isEmpty($datos['Nombre'])) {
			$datos['Nombre'] = preg_replace(self::PATTERN, self::REPLACEMENT, utf8_decode($datos['Nombre']));
			$query = preg_replace(self::WORD_SEPARATOR_P, self::WORD_SEPARATOR_R, $datos['Nombre']) .
				" \"{$datos['Nombre']}\"~5";
			$bool->addMust(Query::query_string('Persona.NombreCompleto', $query)->setDefaultOperator('OR'));
		}

		if (!FuncionesPHPLocal::isEmpty($datos['Dni']))
			$bool->addMust(Query::query_string('Persona.Dni', $datos['Dni']));

		if (!FuncionesPHPLocal::isEmpty($datos['estadosExcluir']))
			$bool->addMustNot(Query::terms('Estado.Id', is_array($datos['estadosExcluir']) ? $datos['estadosExcluir'] : explode(',', $datos['estadosExcluir'])));

        if(!FuncionesPHPLocal::isEmpty($datos["Reincorporado"])) {

            if ($datos["Reincorporado"] == "1") {
                $bool->addMust(Query::exists('FechaReintegro'));
            }

            if ($datos["Reincorporado"] == "0") {
                $bool->addMustNot(Query::exists('FechaReintegro'));
            }
        }


        $this->agregarFiltrosTipoAcceso($datos, $bool);

		if ($bool->countShould() + $bool->countMust() > 0)
			$sort = false;


		if ($sort) {

			if (isset($datos['sort']) && is_array($datos['sort']) && count($datos['sort']) > 0) {
				foreach ($datos['sort'] as $srt)
					$jsonData->addSort(new Sort($srt['field'], $srt['order']));
			} else
				$jsonData->addSort(new Sort($SortField, $SortOrder));
		}

		$cuerpo = $jsonData->setQuery($bool)->toJson();

		$param = 'track_total_hits=true' . $scroll;
		$this->cnx->setDebug(false);
		if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado_consulta, $codigoRetorno, $param)) {
			$this->setError($this->cnx->getError());
			return false;
		}


		if (!isset($resultado_consulta['hits'])) {
			$this->setError(500, Funciones::DevolverError($resultado_consulta));
			return false;
		}


		$resultado = $resultado_consulta['hits']['hits'];
		$total = (int)$resultado_consulta['hits']['total']['value'];
		$numfilas = (int)count($resultado);
		if (isset($resultado_consulta['_scroll_id']))
			$scroll_id = $resultado_consulta['_scroll_id'];

		return true;
	}

	public function busquedaLicenciasSinReincorporar(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total, ?string &$scroll_id = NULL): bool {


        $SortField = 'Fin';
		$SortOrder = 'desc';
		$esControl = (bool)($datos['esControl'] ?? false);
		$jsonData = Consulta::nueva($datos['size'] ?? PAGINAR, $datos['from'] ?? 0);
		// $jsonData->_source = new stdClass();
		if (!FuncionesPHPLocal::isEmpty($datos['excluirCampos']))
			$jsonData->setSource(Source::excludes(...$datos['excluirCampos']));
		if (!FuncionesPHPLocal::isEmpty($datos['camposMostrar']))
			$jsonData->setSource(Source::includes(...$datos['camposMostrar']));

		//$jsonData->query = new stdClass();
		$bool = Query::bool();
		$sort = true;

		$scroll = "";
		if (!empty($datos['scroll']) && preg_match("/\d+[dhms]/", $datos['scroll'])) {
			$scroll = "&scroll={$datos['scroll']}";
			unset($datos['scroll']);
		}

        if (!FuncionesPHPLocal::isEmpty($datos['IdEstadoEvitar'])){
             $bool->addFilter(Query::bool()->addMustNot(Query::terms('Estado.Id', is_array($datos['IdEstadoEvitar']) ? $datos['IdEstadoEvitar'] : explode(',', $datos['IdEstadoEvitar']))));
        }



        if (!FuncionesPHPLocal::isEmpty($datos['IdEstadoBuscar'])){
              $bool->addFilter(Query::terms('Estado.Id', is_array($datos['IdEstadoBuscar']) ? $datos['IdEstadoBuscar'] : explode(',', $datos['IdEstadoBuscar'])));
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Dni']))
            $bool->addMust(Query::query_string('Persona.Dni', $datos['Dni']));


        if (!FuncionesPHPLocal::isEmpty($_SESSION['IdEscuela']))
        {
            if(is_array($_SESSION['IdEscuela']))
                $bool->addFilter(Query::nested('Cargos', Query::terms('Cargos.Escuela.Id', $_SESSION['IdEscuela'])));
            else
                $bool->addFilter(Query::nested('Cargos', Query::term('Cargos.Escuela.Id', (int)$_SESSION['IdEscuela'])));
        }
        elseif (!FuncionesPHPLocal::isEmpty($datos['IdEscuela']))
        {
            if(is_array($datos['IdEscuela']))
                $bool->addFilter(Query::nested('Cargos', Query::terms('Cargos.Escuela.Id', $datos['IdEscuela'])));
            else
                $bool->addFilter(Query::nested('Cargos', Query::term('Cargos.Escuela.Id', (int)$datos['IdEscuela'])));
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdTipo'])) {
            $idTipos = is_array($datos['IdTipo']) ? $datos['IdTipo'] : [$datos['IdTipo']];

            $bool->addFilter(Query::terms('Tipo.Id', $idTipos));
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Nombre'])) {
            $datos['Nombre'] = preg_replace(self::PATTERN, self::REPLACEMENT, utf8_decode($datos['Nombre']));
            $query = preg_replace(self::WORD_SEPARATOR_P, self::WORD_SEPARATOR_R, $datos['Nombre']) .
                " \"{$datos['Nombre']}\"~5";
            $bool->addMust(Query::query_string('Persona.NombreCompleto', $query)->setDefaultOperator('OR'));
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Id']))
            $bool->addFilter(Query::term('Id', (int)$datos['Id']));


        if (!FuncionesPHPLocal::isEmpty($datos['Inicio'])) {
            try {
                $bool->addFilter(Query::range('Inicio', ['gte' => FuncionesPHPLocal::ConvertirFecha($datos['Inicio'], 'dd/mm/aaaa', 'aaaa-mm-dd')]));
            } catch (ExcepcionLogica $e) {
                $this->setError($e->getError());
                return false;
            }
        }

        if (!FuncionesPHPLocal::isEmpty($datos['Fin'])) {
            try {
                $bool->addFilter(Query::range('Fin', ['lte' => FuncionesPHPLocal::ConvertirFecha($datos['Fin'], 'dd/mm/aaaa', 'aaaa-mm-dd')]));
            } catch (ExcepcionLogica $e) {
                $this->setError($e->getError());
                return false;
            }
        }

        if (!FuncionesPHPLocal::isEmpty($datos['FechaFin'])) {
			try {

                $fechaHasta = FuncionesPHPLocal::ConvertirFecha($datos['FechaFin'], 'dd/mm/aaaa', 'aaaa-mm-dd');

                $fechaHastaMenosUno = date("Y-m-d H:i:s", strtotime($fechaHasta) - 1);

                $bool->addFilter(Query::range('Fin', ['lte' => $fechaHastaMenosUno]));

            } catch (ExcepcionLogica $e) {
				$this->setError($e->getError());
				return false;
			}
		}

        $bool->addMustNot(Query::exists('FechaReintegro'));



		$this->agregarFiltrosTipoAcceso($datos, $bool);

		if ($bool->countShould() + $bool->countMust() > 0)
			$sort = false;


		if ($sort) {

			if (isset($datos['sort']) && is_array($datos['sort']) && count($datos['sort']) > 0) {
				foreach ($datos['sort'] as $srt)
					$jsonData->addSort(new Sort($srt['field'], $srt['order']));
			} else
				$jsonData->addSort(new Sort($SortField, $SortOrder));
		}

		$cuerpo = $jsonData->setQuery($bool)->toJson();

		$param = 'track_total_hits=true' . $scroll;
		$this->cnx->setDebug(false);
		if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado_consulta, $codigoRetorno, $param)) {
			$this->setError($this->cnx->getError());
			return false;
		}


		if (!isset($resultado_consulta['hits'])) {
			$this->setError(500, Funciones::DevolverError($resultado_consulta));
			return false;
		}


		$resultado = $resultado_consulta['hits']['hits'];
		$total = (int)$resultado_consulta['hits']['total']['value'];
		$numfilas = (int)count($resultado);
		if (isset($resultado_consulta['_scroll_id']))
			$scroll_id = $resultado_consulta['_scroll_id'];

		return true;
	}

	public function buscarxPersona(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {


		$jsonData = Consultas\Base::nueva($datos['size'] ?? 1000, $datos['from'] ?? 0)
			->setQuery(Query::term('Persona.Id', (int)$datos['IdPersona']));

		if (isset($datos['Sort']) && is_array($datos['Sort'])) {
			foreach ($datos['Sort'] as $campo => $orden)
				$jsonData->addSort(new Sort($campo, $orden));
		}
		/*$jsonData = new stdClass();


		$jsonData->query = new stdClass();

		$jsonData->query->term = new stdClass();
		$jsonData->query->term->{'Persona.Id'} = new stdClass();
		$jsonData->query->term->{'Persona.Id'}->value = $datos['IdPersona'];*/


//		$cuerpo = json_encode($jsonData);
		$param = 'track_total_hits=true';

		$this->cnx->setDebug(false);

		$cuerpo = $jsonData->toJson();
		if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado_consulta, $codigoRetorno, $param)) {
			$this->setError($this->cnx->getError());
			return false;
		}

		if (!isset($resultado_consulta['hits'])) {
			$this->setError(500, Funciones::DevolverError($resultado_consulta));
			return false;
		}

		$resultado = $resultado_consulta['hits']['hits'];
		$total = (int)$resultado_consulta['hits']['total']['value'];
		$numfilas = (int)count($resultado);

		return true;
	}

	public function buscarxIdFechas(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {
		$rango = ['gte' => $datos['FechaDesde']];
		if (!FuncionesPHPLocal::isEmpty($datos['FechaHasta']))
			$rango['lte'] = $datos['FechaHasta'];


		$cuerpo = (new Consultas\Base($datos['size'] ?? 20, $datos['from'] ?? 0))
			->setQuery(Consultas\Query::bool());
		try {
			$qRango = Consultas\Query::range('Fechas', $rango);
			if (!FuncionesPHPLocal::isEmpty($datos['dateFormat']))
				$qRango->getActivo()->setFormat($datos['dateFormat']);
			$qRango->getActivo()->setRelation(Consultas\Range::C);
		} catch (ExcepcionLogica $e) {
			$this->setError($e > $this->getError());
			return false;
		}
		$cuerpo->getQuery()
			->addFilter(Consultas\Query::term('Id', (int)$datos['IdLicencia']))
			->addFilter($qRango);

		$this->cnx->setDebug(false);
		$param = 'track_total_hits=true';
		if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo->toJson(), $res, $codigoRetorno, $param)) {
			$this->setError($this->cnx->getError());
			return false;
		}

		if (!isset($res['hits'])) {
			$this->setError(400, Funciones::DevolverError($resultado));
			return false;
		}

		$resultado = $res['hits']['hits'];
		$total = (int)$res['hits']['total']['value'];
		$numfilas = (int)count($resultado);

		return true;
	}

	public function buscarxRangoFechas(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {
		$rango = ['gte' => $datos['FechaDesde']];
		$rango['lte'] = !FuncionesPHPLocal::isEmpty($datos['FechaHasta']) ?
			$datos['FechaHasta'] : $datos['FechaDesde'];

		try {
			$qRango = Consultas\Query::range('Fechas', $rango);
			if (!FuncionesPHPLocal::isEmpty($datos['dateFormat']))
				$qRango->getActivo()->setFormat($datos['dateFormat']);
			$qRango->getActivo()->setRelation(Consultas\Range::C);
		} catch (ExcepcionLogica $e) {
			$this->setError($e > $this->getError());
			return false;
		}

		$query = $qRango;
		if (!empty($datos['IdEscuela'])) {
			$bool = (new Consultas\Booleano())
				->addFilter($qRango)
				->addFilter(Consultas\Query::nested('Cargos',
					Consultas\Query::term('Cargos.Escuela.Id', $datos['IdEscuela']))
				);
			$query = Consultas\Query::bool($bool);
		}

		$cuerpo = (new Consultas\Base($datos['size'] ?? 20, $datos['from'] ?? 0))
			->setQuery($query);

		$param = 'track_total_hits=true';
		$this->cnx->setDebug(false);
		if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo->toJson(), $resultado_consulta, $codigoRetorno, $param)) {
			$this->setError($this->cnx->getError());
			return false;
		}

		if (FuncionesPHPLocal::isEmpty($resultado_consulta['hits'])) {
			$this->setError(400, Funciones::DevolverError($resultado));
			return false;
		}

		$resultado = $resultado_consulta['hits']['hits'];
		$numfilas = (int)count($resultado);
		$total = (int)$resultado_consulta['hits']['total']['value'];

		return true;
	}

	public function buscarxPersonaxRangoFecha(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {
/*var_dump($datos);die;*/
		$i = 0;
		$jsonData = Consultas\Base::nueva($datos['size'] ?? PAGINAR, $datos['from'] ?? 0);


		$jsonData->_source = new stdClass();
		if (!FuncionesPHPLocal::isEmpty($datos['excluirCampos']))
			$jsonData->setSource(Source::excludes(...$datos['excluirCampos']));
		if (!FuncionesPHPLocal::isEmpty($datos['camposMostrar']))
			$jsonData->setSource(Source::includes(...$datos['camposMostrar']));

		$bool = Query::bool();

		$bool->addFilter(Query::term('Persona.Id', (int)$datos['IdPersona']));
		$bool->addFilter(Query::terms('Estado.Id', [4, 5, 6, 7]));


		if (!FuncionesPHPLocal::isEmpty($datos['IdLicencias'])) {
			$bool->addFilter(Query::terms(
				'Id',
				is_array($datos['IdLicencias']) ? $datos['IdLicencias'] : explode(',', $datos['IdLicencias'])
			));
		} else {
			try {
				$bool->addFilter(Query::range('Fechas', $datos['Fechas'] ?? ['gte' => date('Y-m-d')]));
			} catch (ExcepcionLogica $e) {
				$this->setError($e->getError());
				return false;
			}
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdPuesto'])) {
			$nested = Query::nested(
				'Cargos',
				Query::terms(
					'Cargos.Puesto.Id',
					is_array($datos['IdPuesto']) ? $datos['IdPuesto'] : explode(',', $datos['IdPuesto'])
				)
			);
			$bool->addFilter($nested);
		}


		if (isset($datos['Sort']) && is_array($datos['Sort'])) {
			foreach ($datos['Sort'] as $campo => $orden)
				$jsonData->addSort(new Sort($campo, $orden));
		}


		$cuerpo = $jsonData->setQuery($bool)->toJson();
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

		$resultado = $resultado_consulta['hits']['hits'];
		$total = (int)$resultado_consulta['hits']['total']['value'];
		$numfilas = (int)count($resultado);

		return true;
	}

	public function buscarLicencias(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {

		$jsonData = new stdClass();
		$jsonData->_source = new stdClass();
		if (!FuncionesPHPLocal::isEmpty($datos['excluirCampos']))
			$jsonData->_source->excludes = $datos['excluirCampos'];
		if (!FuncionesPHPLocal::isEmpty($datos['camposMostrar']))
			$jsonData->_source->includes = $datos['camposMostrar'];

		$jsonData->query = new stdClass();
		$jsonData->query->bool = new stdClass();
		$jsonData->query->bool->filter = [];
		$jsonData->query->bool->filter[0] = new stdClass();
		$jsonData->query->bool->filter[0]->terms = new stdClass();
		$jsonData->query->bool->filter[0]->terms->{'Id'} = is_array($datos['IdLicencias']) ? $datos['IdLicencias'] : explode(',', $datos['IdLicencias']);

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

		$resultado = $resultado_consulta['hits']['hits'];
		$total = (int)$resultado_consulta['hits']['total']['value'];
		$numfilas = (int)count($resultado);

		return true;
	}

	public function buscarxIdPofa(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {

		$SortField = 'Id';
		$SortOrder = 'desc';
		$jsonData = Consulta::nueva($datos['size'] ?? PAGINAR, $datos['from'] ?? 0);
		// $jsonData->_source = new stdClass();
		if (!FuncionesPHPLocal::isEmpty($datos['excluirCampos']))
			$jsonData->setSource(Source::excludes(...$datos['excluirCampos']));
		if (!FuncionesPHPLocal::isEmpty($datos['camposMostrar']))
			$jsonData->setSource(Source::includes(...$datos['camposMostrar']));

		$bool = Query::bool();
		$sort = true;

		$bool->addFilter(Query::nested('Cargos', Query::term('Cargos.Puesto.IdPofa', (int)$datos['IdPofa'])));

		if ($bool->countShould() + $bool->countMust() > 0)
			$sort = false;

		if ($sort) {
			if (isset($datos['sort']) && is_array($datos['sort']) && count($datos['sort']) > 0) {
				foreach ($datos['sort'] as $srt)
					$jsonData->addSort(new Sort($srt['field'], $srt['order']));
			} else
				$jsonData->addSort(new Sort($SortField, $SortOrder));
		}

		$cuerpo = $jsonData->setQuery($bool)->toJson();

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

		$resultado = $resultado_consulta['hits']['hits'];
		$total = (int)$resultado_consulta['hits']['total']['value'];
		$numfilas = (int)count($resultado);

		return true;

	}

	public function buscarxIdPuestoRaiz(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {

		$SortField = 'Id';
		$SortOrder = 'desc';
		$jsonData = Consulta::nueva($datos['size'] ?? PAGINAR, $datos['from'] ?? 0);
		// $jsonData->_source = new stdClass();
		if (!FuncionesPHPLocal::isEmpty($datos['excluirCampos']))
			$jsonData->setSource(Source::excludes(...$datos['excluirCampos']));
		if (!FuncionesPHPLocal::isEmpty($datos['camposMostrar']))
			$jsonData->setSource(Source::includes(...$datos['camposMostrar']));

		$bool = Query::bool();
		$sort = true;

		//$bool->addFilter(Query::nested('Cargos', Query::term('Cargos.Puesto.Id', (int)$datos['IdPuestoRaiz'])));

        if (FuncionesPHPLocal::isEmpty($datos["IdPuesto"])) {
            $this->setError("400","No se array de puestos al buscar novedades");
            return false;
        }

        $arrayIdPuestos = explode(",", $datos["IdPuesto"]);

        $bool->addFilter(Query::nested('Cargos', Query::terms('Cargos.Puesto.Id', $arrayIdPuestos)));


        if ($bool->countShould() + $bool->countMust() > 0)
			$sort = false;

		if ($sort) {
			if (isset($datos['sort']) && is_array($datos['sort']) && count($datos['sort']) > 0) {
				foreach ($datos['sort'] as $srt)
					$jsonData->addSort(new Sort($srt['field'], $srt['order']));
			} else
				$jsonData->addSort(new Sort($SortField, $SortOrder));
		}

		$cuerpo = $jsonData->setQuery($bool)->toJson();

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

		$resultado = $resultado_consulta['hits']['hits'];
		$total = (int)$resultado_consulta['hits']['total']['value'];
		$numfilas = (int)count($resultado);
		return true;

	}

	public function estadisticasDashboard(array $datos, ?array &$resultado): bool {
		$SortField = 'Id';
		$SortOrder = 'desc';
		$esControl = $datos['esControl'] ?? false;
		$jsonData = new stdClass();
		$jsonData->size = 0;
		$jsonData->from = 0;
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

        if (!FuncionesPHPLocal::isEmpty($_SESSION['IdEscuela'])) {
            $filter[++$ff] = new stdClass();
            $filter[$ff]->nested = new stdClass();
            $filter[$ff]->nested->path = 'Cargos';
            $filter[$ff]->nested->query = new stdClass();

            if(is_array($_SESSION['IdEscuela']))
            {
                $filter[$ff]->nested->query->terms = new stdClass();
                $filter[$ff]->nested->query->terms->{'Cargos.Escuela.Id'} = new stdClass();
                $filter[$ff]->nested->query->terms->{'Cargos.Escuela.Id'}->value = $_SESSION['IdEscuela'];
            }
            else
            {
                $filter[$ff]->nested->query->term = new stdClass();
                $filter[$ff]->nested->query->term->{'Cargos.Escuela.Id'} = new stdClass();
                $filter[$ff]->nested->query->term->{'Cargos.Escuela.Id'}->value = (int)$_SESSION['IdEscuela'];
            }

        }


		if (!FuncionesPHPLocal::isEmpty($datos['IdPersona'])) {
			$filter[++$ff] = new stdClass();
			$filter[$ff]->term = new stdClass();
			$filter[$ff]->term->{'Persona.Id'} = new stdClass();
			$filter[$ff]->term->{'Persona.Id'}->value = (int)$datos['IdPersona'];
		}

		if (!FuncionesPHPLocal::isEmpty($datos['Inicio'])) {
			$filter[++$ff] = new stdClass();
			$filter[$ff]->term = new stdClass();
			$filter[$ff]->term->{'Inicio'} = new stdClass();
			$filter[$ff]->term->{'Inicio'}->value = $datos['Inicio'];
		}

		if (!FuncionesPHPLocal::isEmpty($datos['Fin'])) {
			$filter[++$ff] = new stdClass();
			$filter[$ff]->term = new stdClass();
			$filter[$ff]->term->{'Fin'} = new stdClass();
			$filter[$ff]->term->{'Fin'}->value = $datos['Fin'];
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdMotivo'])) {
			$filter[++$ff] = new stdClass();
			$filter[$ff]->term = new stdClass();
			$filter[$ff]->term->{'Motivo.Id'} = new stdClass();
			$filter[$ff]->term->{'Motivo.Id'}->value = (int)$datos['IdMotivo'];
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdDiagnostico'])) {
			$filter[++$ff] = new stdClass();
			$filter[$ff]->term = new stdClass();
			$filter[$ff]->term->{'Diagnostico.Id'} = new stdClass();
			$filter[$ff]->term->{'Diagnostico.Id'}->value = (int)$datos['IdDiagnostico'];
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdTipo'])) {
			$filter[++$ff] = new stdClass();
			$filter[$ff]->term = new stdClass();
			$filter[$ff]->term->{'Tipo.Id'} = new stdClass();
			$filter[$ff]->term->{'Tipo.Id'}->value = (int)$datos['IdTipo'];
		}


		if (!FuncionesPHPLocal::isEmpty($datos['IdEstado'])) {
			$filter[++$ff] = new stdClass();
			$filter[$ff]->terms = new stdClass();
			$filter[$ff]->terms->{'Estado.Id'} = is_array($datos['IdEstado']) ? $datos['IdEstado'] : explode(',', $datos['IdEstado']);
		} elseif ($esControl) {
			$filter[++$ff] = new stdClass();
			$filter[$ff]->bool = new stdClass();
			$filter[$ff]->bool->should = [];
			$filter[$ff]->bool->should[0] = new stdClass();
			$filter[$ff]->bool->should[0]->bool = new stdClass();
			$filter[$ff]->bool->should[0]->bool->must = [];
			$filter[$ff]->bool->should[0]->bool->must[0] = new stdClass();
			$filter[$ff]->bool->should[0]->bool->must[0]->term = new stdClass();
			$filter[$ff]->bool->should[0]->bool->must[0]->term->{'Estado.Id'} = 4;
			$filter[$ff]->bool->should[0]->bool->must[1] = new stdClass();
			$filter[$ff]->bool->should[0]->bool->must[1]->range = new stdClass();
			$filter[$ff]->bool->should[0]->bool->must[1]->range->{'Estado.MostrarTmpHasta'} = new stdClass();
			$filter[$ff]->bool->should[0]->bool->must[1]->range->{'Estado.MostrarTmpHasta'}->gte = time();
			$filter[$ff]->bool->should[1] = new stdClass();
			$filter[$ff]->bool->should[1]->bool = new stdClass();
			$filter[$ff]->bool->should[1]->bool->must_not = [];
			$filter[$ff]->bool->should[1]->bool->must_not[0] = new stdClass();
			$filter[$ff]->bool->should[1]->bool->must_not[0]->terms = new stdClass();
			$filter[$ff]->bool->should[1]->bool->must_not[0]->terms->{'Estado.Id'} = [4, 5, 7, 8];
		}


		if (!FuncionesPHPLocal::isEmpty($datos['Duracion'])) {
			$filter[++$ff] = new stdClass();
			$filter[$ff]->term = new stdClass();
			$filter[$ff]->term->{'Duracion'} = new stdClass();
			$filter[$ff]->term->{'Duracion'}->value = (int)$datos['Duracion'];
		}

		if (!FuncionesPHPLocal::isEmpty($datos['Nombre'])) {
			$datos['Nombre'] = preg_replace(self::PATTERN, self::REPLACEMENT, utf8_decode($datos['Nombre']));
			$query = preg_replace(self::WORD_SEPARATOR_P, self::WORD_SEPARATOR_R, $datos['Nombre']) .
				" \"{$datos['Nombre']}\"~5";
			$must[++$mm] = new stdClass();
			$must[$mm]->query_string = new stdClass();
			$must[$mm]->query_string->default_operator = 'OR';
			$must[$mm]->query_string->default_field = 'Persona.NombreCompleto';
			$must[$mm]->query_string->query = $query;
		}

		if (!FuncionesPHPLocal::isEmpty($datos['Dni'])) {
			$query = preg_replace(self::WORD_SEPARATOR_P, self::WORD_SEPARATOR_R, $datos['Dni']);
			$must[++$mm] = new stdClass();
			$must[$mm]->query_string = new stdClass();
			$must[$mm]->query_string->default_operator = 'OR';
			$must[$mm]->query_string->default_field = 'Persona.Dni';
			$must[$mm]->query_string->query = $query;
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

		$jsonData->aggs = new stdClass();
		if (!FuncionesPHPLocal::isEmpty($_SESSION['IdEscuela'])) {
			$jsonData->aggs->TotalesLicenciasConAccionxEscuela = new stdClass();
			$jsonData->aggs->TotalesLicenciasConAccionxEscuela->filter = new stdClass();
			$jsonData->aggs->TotalesLicenciasConAccionxEscuela->filter->bool = new stdClass();
			$jsonData->aggs->TotalesLicenciasConAccionxEscuela->filter->bool->must = new stdClass();
			$jsonData->aggs->TotalesLicenciasConAccionxEscuela->filter->bool->must->terms = new stdClass();
			$jsonData->aggs->TotalesLicenciasConAccionxEscuela->filter->bool->must->terms->{"Estado.Id"} = explode(",", LIC_ESTADOS_EQUIPOCONDUCCION);
		}

		if (!empty($datos['armarAggInasistenciasPendiente'])) {
			$jsonData->aggs->PendienteRevisionDP = new stdClass();
			$jsonData->aggs->PendienteRevisionDP->filter = new stdClass();
			$jsonData->aggs->PendienteRevisionDP->filter->term = new stdClass();
			$jsonData->aggs->PendienteRevisionDP->filter->term->{'Estado.Id'} = 15;

		}

		if (!empty($datos['armarAggInasistenciasPendienteCertificado'])) {
			$jsonData->aggs->PendienteCertificado = new stdClass();
			$jsonData->aggs->PendienteCertificado->filter = new stdClass();
			$jsonData->aggs->PendienteCertificado->filter->term = new stdClass();
			$jsonData->aggs->PendienteCertificado->filter->term->{'Estado.Id'} = 12;

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

		$resultado = $resultado_consulta;

		return true;
	}

	public function cantidadDashboardReconocimientoMedico(array $datos, ?array &$resultado): bool {
		$SortField = 'Id';
		$SortOrder = 'desc';
		$sort = true;
		$size = $datos['size'] ?? 5;
		$jsonData = Consulta::nueva($size);

		$bool = Query::bool();
		if (!FuncionesPHPLocal::isEmpty($datos['excluirCampos']))
			$jsonData->setSource(Source::excludes(...$datos['excluirCampos']));
		if (!FuncionesPHPLocal::isEmpty($datos['camposMostrar']))
			$jsonData->setSource(Source::includes(...$datos['camposMostrar']));


		if (!FuncionesPHPLocal::isEmpty($datos['IdTipo']))
			$bool->addFilter(Query::terms('Tipo.Id', is_array($datos['IdTipo']) ? $datos['IdTipo'] : explode(',', $datos['IdTipo'])));


		if (!FuncionesPHPLocal::isEmpty($datos['anio'])) {
			$str = $datos['anio'] . '||/y';
			try {
				$bool->addFilter(Query::range('Inicio', ['gte' => $str, 'lte' => $str])->setFormat('yyyy'));
			} catch (ExcepcionLogica $e) {
				$this->setError($e->getError());
				return false;
			}
		}

		if (!FuncionesPHPLocal::isEmpty($datos['IdEstadoPendientes']))
			$bool->addFilter(Query::terms('Estado.Id', is_array($datos['IdEstadoPendientes']) ? $datos['IdEstadoPendientes'] : explode(',', $datos['IdEstadoPendientes'])));
		else
			$bool->addMustNot(Query::terms('Estado.Id', ESTADOS_LICENCIAS_IGNORAR_DASH));


		if (!FuncionesPHPLocal::isEmpty($datos['Duracion']))
			$bool->addFilter(Query::term('Duracion', (int)$datos['Duracion']));


		/*if (!FuncionesPHPLocal::isEmpty($datos['IdEscuela'])) {
			$nestedQuery = Query::term('Cargos.Escuela.Id', (int)$datos['IdEscuela']);
		}else {
			$nestedQuery = Query::bool();
			$nestedQuery->addMustNot(Query::terms('Cargos.Escuela.Id', ESCUELAS_DE_PRUEBA));
		}*/

        if (!FuncionesPHPLocal::isEmpty($datos['IdEscuela']) && is_array($datos['IdEscuela'])) {
            $nestedQuery = Query::terms('Cargos.Escuela.Id', $datos['IdEscuela']);
        }else if( !FuncionesPHPLocal::isEmpty($datos['IdEscuela']) && !is_array($datos['IdEscuela'])){
            $nestedQuery = Query::term('Cargos.Escuela.Id', (int)$datos['IdEscuela']);
        }else{
            $nestedQuery = Query::bool();
            $nestedQuery->addMustNot(Query::terms('Cargos.Escuela.Id', ESCUELAS_DE_PRUEBA));
        }

		$bool->addFilter(Query::nested('Cargos', $nestedQuery));


		$this->agregarFiltrosTipoAcceso($datos, $bool);

		if ($bool->countMust() + $bool->countShould() > 0)
			$sort = false;

		if ($sort) {
			if (isset($datos['sort']) && is_array($datos['sort']) && count($datos['sort']) > 0) {
				foreach ($datos['sort'] as $srt)
					$jsonData->addSort(new Sort($srt['field'], $srt['order']));
			} else
				$jsonData->addSort(new Sort($SortField, $SortOrder));
		}

		$jsonData->setQuery($bool);

		// TODO: mover el array de estados a constante
		$queryFinalizadas = Query::terms('Estado.Id', [4, 5, 7, 8]);
		$jsonData->setAgg('TotalFinalizadas', Agg::filter($queryFinalizadas));

		$jsonData->setAgg('TotalPendientes', Agg::filter(Query::bool()->addMustNot($queryFinalizadas)));

		try {
			// TODO: mover duración minima de lic larga a constante
			$jsonData->setAgg('TotalLicenciaLarga', Agg::filter(Query::range('Duracion', ['gte' => 6])));
		} catch (ExcepcionLogica $e) {
			$this->setError($e->getError());
			return false;
		}

		try {
			// TODO: mover duración minima de lic larga a constante
			$jsonData->setAgg('TotalLicenciaCorta', Agg::filter(Query::range('Duracion', ['lte' => 6])));
		} catch (ExcepcionLogica $e) {
			$this->setError($e->getError());
			return false;
		}

		$jsonData->setAgg('Familiares', Agg::filter(Query::term('esFamiliar', true)));

		$jsonData->setAgg('Sexo', Agg::terms('Persona.Sexo.Id'));

		$jsonData->setAgg('Especialidades',
			Agg::terms('Autorizante.Especialidad.Id', 5)
				->addAgg('Nombre', Agg::top_hits(1)
					->setSource(Source::includes('Autorizante.Especialidad.Nombre')))
		);

		$jsonData->setAgg('Autorizantes',
			Agg::terms('Autorizante.Id', 5)
				->addAgg('Nombre', Agg::top_hits(1)
					->setSource(Source::includes('Autorizante.Nombre','Autorizante.Apellido')))
		);

		$jsonData->setAgg('LicenciasMensualesFInicio',
			Agg::date_histogram('Inicio', 'month')->setFormat('yyyy-MM-dd')
		);

		$jsonData->setAgg('LicenciasMensualesAlta',
			Agg::date_histogram('Alta.Fecha', 'month')->setFormat('yyyy-MM-dd')
		);


		$cuerpo = $jsonData->toJson();
		//var_dump($cuerpo);
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

		$resultado = $resultado_consulta;

		return true;
	}

	public function totalLicenciasxAnios(array $datos, ?array &$resultado): bool {
		$SortField = 'Id';
		$SortOrder = 'desc';
		$esControl = $datos['esControl'] ?? false;
		$jsonData = new stdClass();
		$jsonData->size = 0;
		$jsonData->from = 0;
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


		if (isset($datos['anios'])) {
			foreach ($datos['anios'] as $anio) {
				if (!FuncionesPHPLocal::isEmpty($anio)) {
					$filter[++$ff] = new stdClass();
					$filter[$ff]->range = new stdClass();
					$filter[$ff]->range->Inicio = new stdClass();
					$filter[$ff]->range->Inicio->gte = $anio . "||/y";
					$filter[$ff]->range->Inicio->lte = $anio . "||/y";
					$filter[$ff]->range->Inicio->format = "yyyy";
				}
			}
		}


		$jsonData->query->bool->filter = new stdClass();
		$jsonData->query->bool->filter->bool = new stdClass();
		$jsonData->query->bool->filter->bool->should = $filter;

		$i = 0;
		$jsonData->query->bool->must = array();
		$jsonData->query->bool->must[$i] = new stdClass();
		$jsonData->query->bool->must[$i]->terms = new stdClass();
		$jsonData->query->bool->must[$i]->terms->{"Estado.Id"} = [4, 5];

		if (isset($datos['IdTipo']) && !FuncionesPHPLocal::isEmpty($datos['IdTipo'])) {
			$i = 1;
			$jsonData->query->bool->must[$i] = new stdClass();
			$jsonData->query->bool->must[$i]->terms = new stdClass();
			$jsonData->query->bool->must[$i]->terms->{"Tipo.Id"} = [$datos['IdTipo']];
		}


		$jsonData->aggs = new stdClass();
		$jsonData->aggs->TotalesAnio = new stdClass();
		$jsonData->aggs->TotalesAnio->date_histogram = new stdClass();
		$jsonData->aggs->TotalesAnio->date_histogram->field = "Inicio";
		$jsonData->aggs->TotalesAnio->date_histogram->calendar_interval = "month";


		unset($ff, $mm, $sh, $mn, $ss);

		$cuerpo = json_encode($jsonData);
		//echo $cuerpo;

		$param = 'track_total_hits=true';
		//		$this->cnx->setDebug(true);
		if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado_consulta, $codigoRetorno, $param)) {
			$this->setError($this->cnx->getError());
			return false;
		}

		if (!isset($resultado_consulta['hits'])) {
			$this->setError(500, Funciones::DevolverError($resultado_consulta));
			return false;
		}
		//print_r($resultado_consulta);die;

		$resultado = $resultado_consulta;

		return true;
	}

	/**
	 * @param array      $datos
	 * @param array|null $resultado
	 *
	 * @return bool
	 */
	public function buscarHorasXArticulo(array $datos, ?array &$resultado): bool {

		try {
			$rangos = [
				'Anual' => Query::range('Fechas', [
					'gte' => date('Y-01-01'),
					'lte' => date('Y-12-31'),
				]),
				'Mensual' => Query::range('Fechas', [
					'gte' => date_create('first day of this month')->format('Y-m-d'),
					'lte' => date_create('last day of this month')->format('Y-m-d'),
				]),
			];
		} catch (ExcepcionLogica $e) {
			$this->setError($e->getError());
			return false;
		}

		$cuerpo = Consultas\Base::nueva(0)
			->setQuery(
				Query::bool()
					->addMust(Query::term('Persona.Id', (int)$datos['IdPersona']))
					->addMust($rangos['Anual'])
					->addMust(Query::nested('Cargos', Query::exists('Cargos.Articulo.Id')))
			)
			->setAgg('Nested',
				Agg::nested('Cargos')
					->addAgg('Articulos',
						Agg::terms('Cargos.Articulo.Id', 1000)
							->addAgg('Datos',
								Agg::top_hits(1)
									->setSource(Source::includes('Cargos.Articulo.*'))
							)
							->addAgg('Padre',
								Agg::reverse_nested()
									->addAgg('Anual', Agg::sum('Duracion'))
                                    ->addAgg('AnualHabiles', Agg::sum('DuracionHabiles'))
									->addAgg('Mensual', Agg::filter($rangos['Mensual'])
										->addAgg('Valor', Agg::sum('Duracion'))
									)
                                    ->addAgg('MensualHabiles', Agg::filter($rangos['Mensual'])
                                        ->addAgg('Valor', Agg::sum('DuracionHabiles'))
                                    )
							)
					)
			)
			->toJson();

		if (!$this->buscar($cuerpo, $resultado_consulta))
			return false;

		$resultado = $resultado_consulta['aggregations']['Nested']['Articulos']['buckets'] ?? [];

		return true;
	}

	/**
	 * @param string     $cuerpo
	 * @param array|null $resultado
	 * @param string     $param
	 *
	 * @return bool
	 */
	private function buscar(string $cuerpo, ?array &$resultado, string $param = ''): bool {

		$this->cnx->setDebug(false);
		if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado, $codigoRetorno, $param)) {
			$this->setError($this->cnx->getError());
			return false;
		}

		if (!isset($resultado['hits'])) {
			$this->setError(500, Funciones::DevolverError($resultado));
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

				if (!isset($datos['filtarxRegionxNivelxTurno']))
					break;
				foreach ($datos['filtarxRegionxNivelxTurno'] as $filtro) {
					$subQuery = Query::bool();
					$subQuery->addFilter(Query::term('Cargos.IdRegion', $filtro['Region']));

					if (0 != $filtro['Nivel'])
						$subQuery->addFilter(Query::term('Cargos.Escuela.Niveles.Id', $filtro['Nivel']));

					if (0 != $filtro['Turno'])
						$subQuery->addFilter(Query::term('Cargos.Escuela.Turnos.Id', $filtro['Turno']));

					$nestedQuery->addShould($subQuery);
				}
				break;
			case 2: // seleccion de escuela
				//$datos['IdEscuela'] se resuleve por el circuito normal

				if (!isset($datos['filtarxEscuelaxNivelxTurno']))
					break;
				foreach ($datos['filtarxEscuelaxNivelxTurno'] as $filtro) {
					$subQuery = Query::bool();
					if (0 != $filtro['Nivel'])
						$subQuery->addFilter(Query::term('Cargos.Escuela.Niveles.Id', $filtro['Nivel']));

					if (0 != $filtro['Turno'])
						$subQuery->addFilter(Query::term('Cargos.Escuela.Turnos.Id', $filtro['Turno']));

					if ($subQuery->countFilter() > 0)
						$nestedQuery->addShould($subQuery);
				}
				break;
			case 3: // seleccion de nivel y escuelas
				if (isset($datos['IdNivel']))
                {
                    if(is_array($datos['IdNivel']))
                        $nestedQuery->addFilter(Query::terms('Cargos.Escuela.Niveles.Id', $datos['IdNivel']));
                    else
                        $nestedQuery->addFilter(Query::term('Cargos.Escuela.Niveles.Id', $datos['IdNivel']));
                }
				if (!empty($datos['IdsEscuela']))
					$nestedQuery->addFilter(Query::terms('Cargos.Escuela.Id', $datos['IdsEscuela']));
				break;
			case 4:
				if (!FuncionesPHPLocal::isEmpty($_SESSION['personcod']))
					$bool->addFilter(Query::term('Persona.Id', (int)$_SESSION['personcod']));
				break;
			default:
				break;
		}

		if ($nestedQuery->countFilter() + $nestedQuery->countShould() > 0)
			$bool->addFilter(Query::nested('Cargos', $nestedQuery));
	}
}
