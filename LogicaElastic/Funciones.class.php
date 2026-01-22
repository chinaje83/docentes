<?php


namespace Elastic;


use ManejoErrores;
use stdClass;

/**
 * Class Funciones
 *
 * @package Elastic
 *
 *
 * @author José R. Méndez <jmendez@bigtree.com.ar>
 */
class Funciones
{
	use ManejoErrores;
	public static function AbrirIndice($indice, Conexion &$conexionES, ?string &$error)
	{
		$error = null;
		if (!$conexionES->sendPost($indice, '_open', '', $data, $codigoRetorno))
			return false;

		if (!isset($data['acknowledged']) || $data['acknowledged'] === false)
		{
			$error = self::DevolverError($data);
			return false;
		}

		return true;

	}

	public static function MostrarError($data)
	{
		if (isset($data['error']) && is_array($data['error']))
		{
			echo self::ProcesarErrores($data['error']);
		}
		else
			print_r($data);
	}

	public static function DevolverError($data)
	{
		if (isset($data['error']) && is_array($data['error']))
		{
			return self::ProcesarErrores($data['error']);
		}
		else
			return print_r($data, true);
	}

	private static function ProcesarErrores($datos)
	{
		$status = "";
		$tipo = "";
		$razon = "Error";
		if (isset($datos['status']) && $datos['status'] != "")
			$status = $datos['status'];
		if (isset($datos['type']) && $datos['type'] != "")
			$tipo = $datos['type'];

		switch ($status)
		{
			/** @noinspection PhpMissingBreakStatementInspection */
			case 500:
				if (isset($datos['caused_by']['caused_by']['caused_by']['reason']) && $datos['caused_by']['caused_by']['caused_by']['reason'] != "")
					return $datos['caused_by']['caused_by']['caused_by']['reason'];
			default:
				if (isset($datos['reason']) && $datos['reason'] != "")
					$razon = ucfirst($datos['reason']);
				if (isset($datos['line']) && $datos['line'] != "")
					$razon .= " en linea {$datos['line']}";
				if (isset($datos['col']) && $datos['col'] != "")
					$razon .= " columna {$datos['col']}";
				if ($tipo != "")
					$razon .= " (".ucfirst($tipo).")";
				$razon .= ".".PHP_EOL;
				if (isset($datos['caused_by']) && !empty($datos['caused_by']))
					$razon .= " Causa del error: ".self::ProcesarErrores($datos['caused_by']);
				if (isset($datos['root_cause']) && !empty($datos['root_cause']))
				{
					foreach ($datos['root_cause'] as $key => $root_cause)
						$razon .= " Error - documento ($key): ".self::ProcesarErrores($root_cause);
				}

				return nl2br($razon);
				break;
		}

	}

	public static function CerrarIndice($indice, Conexion &$conexionES, ?string &$error)
	{
		$error = null;
		if (!$conexionES->sendPost($indice, '_close', '', $data, $codigoRetorno))
			return false;

		if (!isset($data['acknowledged']) || $data['acknowledged'] === false)
		{
			$error = self::DevolverError($data);
			return false;
		}

		return true;

	}


	public static function Scroll($datos, &$dataResult, Conexion &$conexionES, ?string &$error)
	{
		$error = null;
		$datosEnviar = new StdClass;
		$datosEnviar->scroll = $datos['scroll'];
		$datosEnviar->scroll_id = $datos['scroll_id'];

		$dataEnvio = json_encode($datosEnviar);
		$conexionES->setDebug(false);
		if (!$conexionES->sendPost('', '_search/scroll', $dataEnvio, $dataResult, $codigoRetorno)){
            $error = $conexionES->getError('error_description');
            return false;
        }


		if (isset($dataResult['error']))
		{
			$error = self::DevolverError($dataResult);
			return false;
		}

		return true;
	}

	public static function clearScroll($scroll_id, Conexion &$conexionES, ?string &$error)
	{
		$error = null;
		$dataEnviar = new stdClass();
		$dataEnviar->scroll_id = $scroll_id;
		$cuerpo = json_encode($dataEnviar);
		if (!$conexionES->sendDelete('', '_search/scroll', $dataResult, $codigoRetorno, '', $cuerpo))
			return false;

		if (isset($dataResult['error']))
		{
			$error = self::DevolverError($dataResult);
			return false;
		}

		return true;
	}


	public static function armarQuerySimple($datos)
	{
		$ff = 0;
		$query = new StdClass;
		$query->query = new StdClass;
		$query->query->bool = new StdClass;
		$query->query->bool->filter = array();
		foreach ($datos as $campo => $valor)
		{
			$query->query->bool->filter[$ff] = new StdClass;
			$query->query->bool->filter[$ff]->term = new StdClass;
			$query->query->bool->filter[$ff]->term->{$campo} = utf8_encode($valor);
			$ff++;
		}
		return $query;
	}


	public static function VerificarTask($task, Conexion &$conexionES)
	{
		if (!$conexionES->sendGet('', '_tasks', $dataResult, $codigoRetorno, $task))
			return false;
		return $dataResult['completed'];

	}
}
