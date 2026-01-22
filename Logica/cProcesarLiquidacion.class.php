<?php
require_once DIR_CLASES_DB . 'cProcesarLiquidacion.db.php';

/**
 * Class cProcesarLiquidacion
 */
class cProcesarLiquidacion extends cProcesarLiquidacionDB {

	public const meses = [
		'01' => 'Enero',
		'02' => 'Febrero',
		'03' => 'Marzo',
		'04' => 'Abril',
		'05' => 'Mayo',
		'06' => 'Junio',
		'07' => 'Julio',
		'08' => 'Agosto',
		'09' => 'Septiembre',
		'10' => 'Octubre',
		'11' => 'Noviembre',
		'12' => 'Diciembre',
	];
	private $anio;
	private $mes;

	public function __construct(accesoBDLocal $conexion, $anio = NULL, $mes = NULL) {
		parent::__construct($conexion);
		$this->anio = $anio ?? date('Y');
		$this->mes = $mes ?? date('m');
	}

	/**
	 * @param string $anio
	 * @return cProcesarLiquidacion
	 */
	public function setAnio(string $anio) {
		$this->anio = $anio;
		return $this;
	}

	/**
	 * @param string $mes
	 * @return cProcesarLiquidacion
	 */
	public function setMes(string $mes) {
		$this->mes = $mes;
		return $this;
	}

	/**
	 * @param array    $datos
	 * @param int|null $codigoInsertado
	 * @return bool
	 * @todo crear tabla y guardar que archivos se subieron ah�, en estado nuevo
	 */
	public function subirArchivo(array $datos, ?int &$codigoInsertado): bool {
		$mes_nombre = self::meses[$this->mes];
		$archivo = PATH_STORAGE . "liquidaciones/{$mes_nombre}_{$this->anio}.csv";
		if(file_exists($archivo)) {
			$this->setError(400, 'Error, ya existe el archivo');
			return false;
		}
		$archivo_tmp = CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA . $datos['NombreTmp'];
		if(!file_exists($archivo_tmp)) {
			$this->setError(400, 'Error, ya existe no el archivo temporal');
			return false;
		}
		if(!rename($archivo_tmp, $archivo)) {
			$this->setError(500, 'Error al guardar el archivo');
			return false;
		}

		$codigoInsertado = NULL;

		return true;
	}

	public function preProcesarArchivo(array $datos, ?array &$salida): bool {
		switch(PHP_OS) {
			case 'WINNT':
				$python = 'python';
				break;
			default:
				$python = 'python3.8';
		}
		$dir = DIR_ROOT . '/importador-liquidaciones';
		if(!chdir ( $dir )) {
			$this->setError(500, 'Error al cambiar directorio');
			return false;
		}
		exec("$python $dir/main.py {$this->anio} {$this->mes} 2>&1", $salida, $return_var);

		if(0 != $return_var) {
			$this->setError(500, $return_var.' - '.implode(" ", $salida));
			return false;
		}
		return true;
	}

	public function procesarArchivo(array $datos, ?array &$salida): bool {
		switch(PHP_OS) {
			case 'WINNT':
				$python = 'python';
				break;
			default:
				$python = 'python3.8';
		}
		$dir = DIR_ROOT . '/importador-liquidaciones';
		if(!chdir ( $dir )) {
			$this->setError(500, 'Error al cambiar directorio');
			return false;
		}
		exec("$python $dir/enviar.py {$this->anio} {$this->mes} 2>&1", $salida, $return_var);

		if(0 != $return_var) {
			$this->setError(500, $return_var.' - '.implode(" ", $salida));
			return false;
		}
		return true;
	}


	public function subirYProcesar(array $datos, ?array &$salida, ?int &$codigoInsertado): bool {
		$salida = [];
		if(!$this->subirArchivo($datos, $codigoInsertado))
			return false;
		if(!$this->preProcesarArchivo($datos, $salida))
			return false;
		return $this->procesarArchivo($datos, $salida);
		//return true;
	}


}
