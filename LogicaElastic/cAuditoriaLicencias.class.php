<?php


namespace Elastic;

use ManejoErrores;
use stdClass;
use FuncionesPHPLocal;

class AuditoriaLicencias implements InterfaceBase
{
	use ManejoErrores;
	/** @var Conexion */
	private $cnx;
	/** @var string  */
	private const INDEX = INDEXPREFIX.AUDITORIAS_LICENCIAS;
	/**
	 * AuditoriaLicencias constructor.
	 *
	 * @param Conexion $cnx
	 */
	public function __construct(Conexion $cnx) {
		$this->cnx =& $cnx;
	}
	public function __destruct() {
		$this->error = [];
	}
	/**
	 * @inheritDoc
	 */
	public static function Configuracion(stdClass &$jsonData): void {}
	/**
	 * @inheritDoc
	 */
	public static function Estructura(bool $devolverJson = true) {
		$jsonData = new stdClass();
		$jsonData->dynamic = 'strict';
		return $devolverJson? json_encode($jsonData) : $jsonData;
	}
	/**
	 * @inheritDoc
	 */
	public static function armarDatosElastic(array $datos, bool $encode = false) {
		$jsonData = new stdClass();
		return $encode? json_encode($jsonData) : $jsonData;
	}
	/**
	 * @inheritDoc
	 */
	public static function obtenerId($datos)
	{
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
	 * @param array      $datos
	 * @param array|null $datosAuditoria
	 * @param int|null   $numfilas
	 * @param int|null   $total
	 * @return bool
	 */
	public function buscarAuditoriaLicencia(array $datos, ?array &$datosAuditoria, ?int &$numfilas, ?int &$total): bool {
		$cuerpo = new stdClass();
		$cuerpo->size = $datos['size'] ?? PAGINAR;
		$cuerpo->from = $datos['from'] ?? 0;
		$cuerpo->query = new stdClass();
		$cuerpo->query->term = new stdClass();
		$cuerpo->query->term->Id = new stdClass();
		$cuerpo->query->term->Id->value = $datos['Id'];

        $cuerpo->sort = array();
        $cuerpo->sort[0] = new stdClass();
        $cuerpo->sort[0]->{"UltimaModificacion.Fecha"} = new stdClass();
        $cuerpo->sort[0]->{"UltimaModificacion.Fecha"}->order = 'desc';





        $this->cnx->setDebug(false);
		if(!$this->cnx->sendPost(self::INDEX, '_search', json_encode($cuerpo), $resultado, $codigoRetorno)) {
			$this->setError($this->cnx->getError());

			return false;
		}
		$datosAuditoria = $resultado['hits']['hits'] ?? [];
		$numfilas = count($datosAuditoria);
		$total = $resultado['hits']['total']['value'] ?? 0;
		return true;
	}


    public function buscarxCodigo(array $datos, ?array &$datosLicencia): bool {

        $id = $datos['Id'];

        $this->cnx->setDebug(false);
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

}
