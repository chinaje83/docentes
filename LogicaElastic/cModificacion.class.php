<?php


namespace Elastic;

use FuncionesPHPLocal;
use ManejoErrores;
use stdClass;

/**
 * Class Modificacion
 *
 * @package Elastic
 *
 *
 * @author  José R. Méndez <jmendez@bigtree.com.ar>
 */
class Modificacion {
	use ManejoErrores;

	/** @var string */
	protected $indexSuffix;
	/** @var string */
	protected $index;
	/** @var Conexion */
	protected $conexionES;


	/**
	 * Constructor de la clase
	 *
	 * @param string   $indexSuffix
	 * @param Conexion $conexionES
	 */
	function __construct(string $indexSuffix, Conexion $conexionES) {
		$this->indexSuffix = $indexSuffix;
		$this->index = INDEXPREFIX . $indexSuffix;
		$this->conexionES =& $conexionES;
	}


	/**
	 * Destructor de la clase
	 */
	function __destruct() {
		$this->error = [];
	}

	/**
	 * @param array|stdClass $datos
	 * @param array|stdClass $DataActualizacion
	 * @return bool
	 */
	public function Actualizar($datos, $DataActualizacion): bool {
		$Id = $this->_ObtenerId($datos);
		//var_dump($Id);die;
		$ExisteId = $this->_BuscarxCodigo($Id, $datosRegistro);
		$datosEnvio = new stdClass();
		if (!$ExisteId) {
			if (!$this->Insertar($DataActualizacion))
				return false;

			return true;
		}
		$datosEnvio->doc = $DataActualizacion;
		//        $this->conexionES->setDebug(true);
		$datosEnviar = json_encode($datosEnvio);
		if (!$this->conexionES->sendPost($this->index, '_update', $datosEnviar, $data, $codigoRetorno, $Id ?? '')) {
			$this->setError($this->conexionES->getError());
			return false;
		}

		//file_put_contents(PUBLICA."actualizar_{$this->indexSuffix}-result.txt",print_r($data,true)."\n");
		//file_put_contents(PUBLICA."actualizar_{$this->indexSuffix}-result.json",$result."\n");
		if (!isset($data['result']) || ($data['result'] != "updated" && $data['result'] != "noop")) {
			$this->setError('400', Funciones::DevolverError($data));
			return false;
		}

       return true;

	}


	/**
	 * @param array|stdClass $datos
	 * @return bool
	 */
	public function Insertar($datos): bool {
		$Id = $this->_ObtenerId($datos);

		if (is_null($Id))
			$param = '';
		else
			$param = $Id;

		$datosEnviar = json_encode($datos);

		if (!$this->conexionES->sendPost($this->index, '_doc', $datosEnviar, $data, $codigoRetorno, $param))
			return false;

		if (!isset($data['result']) || ($data['result'] != "created" && $data['result'] != "updated" && $data['result'] != "noop")) {
			$this->setError('400', Funciones::DevolverError($data));
			return false;
		}

		return true;
	}

	/**
	 * @param array|stdClass $datos
	 * @param array|stdClass $script
	 * @return bool
	 */
	public function ActualizarConScript($datos, $script): bool {
		$Id = $this->_ObtenerId($datos);
		$ExisteId = $this->_BuscarxCodigo($Id, $datosRegistro);
		if (!$ExisteId) {
			FuncionesPHPLocal::MostrarMensaje($this->conexionES, MSG_ERRGRAVE, 'Error al actualizar, el documento no existe', array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => ''));
			return false;
		}
		$datosEnvio = new stdClass();
		$datosEnvio->script = (object) $script;
		$datosEnviar = json_encode($datosEnvio);
		if (!$this->conexionES->sendPost($this->index, '_update', $datosEnviar, $data, $codigoRetorno, $Id))
			return false;


		if (!isset($data['result']) || ($data['result'] != "updated" && $data['result'] != "noop")) {
			$this->setError('400', Funciones::DevolverError($data));
			return false;
		}

		return true;

	}


	/**
	 * @param array|object $consulta
	 * @param string       $script
	 * @param array|null   $resultado
	 * @param string       $lang
	 * @return bool
	 */
	public function actualizarPorConsulta($consulta, string $script, ?array &$resultado, string $lang = 'painless', array $params = []): bool {
		$jsonData = new stdClass();
		$jsonData->query = (object) $consulta;
		$jsonData->script = new stdClass();
		$jsonData->script->lang = $lang;
		$jsonData->script->source = $script;

		if (!empty($params)) {
			$jsonData->script->params = $params;
		}

		$cuerpo = json_encode($jsonData);
		$this->conexionES->setDebug(false);
		if (!$this->conexionES->sendPost($this->index, '_update_by_query', $cuerpo, $resultado, $codigoRetorno))
			return false;

		if (!FuncionesPHPLocal::isEmpty($resultado['failures'])) {
			$this->setError('400', Funciones::DevolverError($resultado));
			return false;
		}
		return true;
	}


	/**
	 * @param string $datosEnviarJson
	 * @return bool
	 */
	public function ActualizarBulkIndex(string $datosEnviarJson): bool {

        if (defined('DEBUGELASTIC') && DEBUGELASTIC)
            file_put_contents(DIR_ROOT . "/error_logs/bulk.json", $datosEnviarJson . "\n");

		if (!$this->conexionES->sendPost($this->index, '_bulk', $datosEnviarJson, $data, $codigoRetorno)) {
			$this->setError($this->conexionES->getError());
			return false;
		}

        if (defined('DEBUGELASTIC') && DEBUGELASTIC)
            file_put_contents(DIR_ROOT . "/error_logs/bulk-result.txt", print_r($data, true) . "\n");

		if (isset($data['errors']) && $data['errors']) {
			$this->setError('400', Funciones::DevolverError($data));
			return false;
		}

		return true;
	}


	/**
	 * @param string $datosEnviarJson
	 * @return bool
	 */
	public function ActualizarBulk(string $datosEnviarJson): bool {
		if (defined('DEBUGELASTIC') && DEBUGELASTIC)
			file_put_contents(DIR_ROOT . "/error_logs/bulk.json", $datosEnviarJson . "\n");
		$this->conexionES->setDebug(false);
		if (!$this->conexionES->sendPost('', '_bulk', $datosEnviarJson, $data, $codigoRetorno)) {
			$this->setError($this->conexionES->getError());
			return false;
		}

		if (defined('DEBUGELASTIC') && DEBUGELASTIC)
			file_put_contents(DIR_ROOT . "/error_logs/bulk-result.txt", print_r($data, true) . "\n");

		if (isset($data['errors']) && $data['errors']) {
			echo "Fallo en elastic search en funcion ActualizarBulk";
			$this->setError('400', Funciones::DevolverError($data));
			return false;
		}

		return true;
	}


	/**
	 * @param array|stdClass $datos
	 * @return bool
	 */
	public function Eliminar($datos): bool {

		$Id = $this->_ObtenerId($datos);
		$ExisteId = $this->_BuscarxCodigo($Id, $datosRegistro);
		if (!$ExisteId)
			return true;
		$endPoint = defined('INCLUDE_TYPE') && true === INCLUDE_TYPE ? TYPE : '_doc';
		if (!$this->conexionES->sendDelete($this->index, $endPoint, $data, $codigoRetorno, $Id))
			return false;


		if (isset($data['errors']) && $data['errors']) {
			$this->setError('400', Funciones::DevolverError($data));
			return false;
		}
		return true;
	}


	/**
	 * @param array|stdClass|Consultas\Base $datos
	 * @param bool                          $usarTask
	 * @return bool
	 */
	public function EliminarxQuery($datos, bool $usarTask = false): bool {
		$datosEnviar = json_encode($datos);
		$param = $usarTask ? 'wait_for_completion=false' : '';
		$this->conexionES->setDebug(false);
		if (!$this->conexionES->sendPost($this->index, '_delete_by_query', $datosEnviar, $data, $codigoRetorno, $param)) {
			$this->setError($this->conexionES->getError());
			return false;
		}

		if ($usarTask) {
			echo $data['task'];
			while (!Funciones::VerificarTask($data['task'], $this->conexionES))
				sleep(10);
		}
		if (isset($data['errors']) && $data['errors']) {
			$this->setError('400', Funciones::DevolverError($data));
			return false;
		}

		return true;
	}

	/**
	 *
	 * @param array|stdClass $datos
	 * @return false|int|string
	 */
	private function _ObtenerId($datos) {
		$id = false;
		if (defined("CLASES") && isset(CLASES[$this->indexSuffix])) {
			/** @var InterfaceBase $clase */
			$clase = 'Elastic\\' . CLASES[$this->indexSuffix];
			if (class_exists($clase) && method_exists($clase, 'obtenerId'))
				$id = $clase::obtenerId($datos);

		}
		return $id;

	}

	/**
	 * @param string|int $Id
	 * @param array|null $datosRegistro
	 * @return bool
	 */
	private function _BuscarxCodigo($Id, ?array &$datosRegistro): bool {

		$datosRegistro = $datosEnviar = [];
		if (!$this->conexionES->sendGet($this->index, '_doc', $data, $codigoRetorno, $Id))
			return false;

		if (!isset($data['found'])) {
			$this->setError('400', Funciones::DevolverError($data));
			return false;
		} elseif ($data['found'] === false)
			return false;
		else {
			$datosRegistro = $data['_source'];
			return true;
		}

	}



}
