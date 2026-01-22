<?php

use Bigtree\ExcepcionLogica;

class cTiposAutorizantes
{
	use ManejoErrores;

	protected $oCurl;
	protected $conexion;
	protected $error;
	protected $Utf8;

	protected $MemCache;

	const MemCacheExpire = 86400;// 1 dia

	function __construct($conexion)
	{
		$this->conexion = &$conexion;
		$this->error = array();
		$this->oCurl = new CurlBigtree();
		$this->Utf8 = false;
	}

	public function __destruct()
	{
		$this->oCurl->CloseCurl();
		unset($this->oCurl);
	}

	public function getCurl()
	{
		return $this->oCurl;
	}

	public function CodificarUtf8()
	{
		$this->Utf8 = true;
	}
	public function BuscarxCodigo($datos, &$resultado, &$numfilas)
	{

		$url = "tipos-autorizantes/".$datos['IdTipoAutorizante'];

		$header = array("Authorization: Bearer {$_SESSION['token']}");
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl(API_LICENCIAS );
		$this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
		$this->oCurl->setDebug(false);

		if (!$this->oCurl->sendGet($url, $dataResult)) {


			$this->setError("Error", utf8_decode("Ocurrió un error al buscar el autorizante."));
			return false;
		}

		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		$numfilas = count($array);
		$resultado = $array;

		return $array;
	}

	public function BusquedaAvanzadaTipo($datos, &$resultado, &$numfilas)
	{

		$url = "tipos-autorizantes?";

		$dataEnviar = new stdClass();

		if (!FuncionesPHPLocal::isEmpty($datos['Id']))
			$dataEnviar->Id = $datos['Id'];

		if (!FuncionesPHPLocal::isEmpty($datos['Nombre']))
			$dataEnviar->Nombre = $datos['Nombre'];

		if (!FuncionesPHPLocal::isEmpty($datos['Estado']))
			$dataEnviar->Estado = $datos['Estado'];

		if (!FuncionesPHPLocal::isEmpty($datos['rows']))
			$dataEnviar->rows = $datos['rows'];

		if (!FuncionesPHPLocal::isEmpty($datos['page']))
			$dataEnviar->page = $datos['page'];

		if (!FuncionesPHPLocal::isEmpty($datos['sidx']))
			$dataEnviar->sidx = $datos['sidx'];

		if (!FuncionesPHPLocal::isEmpty($datos['sord']))
			$dataEnviar->sord = $datos['sord'];


		$cuerpo = http_build_query($dataEnviar);

		$header = array("Authorization: Bearer {$_SESSION['token']}");
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl(API_LICENCIAS . $url);
		$this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
		$this->oCurl->setDebug(false);

		if (!$this->oCurl->sendGet($cuerpo, $dataResult)) {

			$this->setError("Error", utf8_decode("Ocurrió un error al buscar el listado de autorizantes."));
			return false;
		}

		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		$numfilas = count($array['filas']);
		$resultado = $array;

		return $array;
	}
	public function Insertar($datos, &$codigoInsertado)
	{
		if (!self::_ValidarInsertar($datos))
			return false;

		$url = "tipos-autorizantes";
		$header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl(API_LICENCIAS.$url);
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		$this->oCurl->setHttpBuildPost(true);
		$this->oCurl->setDebug(false);

		$dataEnviar = new stdClass();

		$dataEnviar->IdTipoAutorizante = (int)$datos['IdTipoAutorizante'];

		if (!FuncionesPHPLocal::isEmpty($datos['Nombre']))
			$dataEnviar->Nombre = ($datos['Nombre']);


		$cuerpo = json_encode($dataEnviar);

		if (!$this->oCurl->sendPost($cuerpo,$dataResult)) {
			$this->setError($dataResult['error'],$dataResult['error_description']);

			return false;
		}

		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['error'])) {
			$this->setError($array['error'],$array['error_description']);
			return false;
		}

		$codigoInsertado = $array['Id'];
		return $array;
	}

	public function Modificar($datos)
	{

		if (!self::_ValidarModificar($datos))
			return false;

		$url = "tipos-autorizantes/".$datos['IdTipoAutorizante'];
		$header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl(API_LICENCIAS.$url);
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		$this->oCurl->setHttpBuildPost(false);
		$this->oCurl->setDebug(false);

		$dataEnviar = new stdClass();

		$dataEnviar->IdTipoAutorizante = (int)$datos['IdTipoAutorizante'];

		if (!FuncionesPHPLocal::isEmpty($datos['Nombre']))
			$dataEnviar->Nombre = utf8_encode($datos['Nombre']);


//		print_r($dataEnviar);die;
			$cuerpo = json_encode($dataEnviar);


		if (!$this->oCurl->sendPut($cuerpo,$dataResult)) {
			$this->setError($dataResult['error'],$dataResult['error_description']);
			return false;
		}

		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['error'])) {
			$this->setError($array['error'],$array['error_description']);
			return false;
		}


		return $array;
	}

	public function Eliminar($datos)
	{

		if (!self::_ValidarEliminar($datos))
			return false;

		$url = "tipos-autorizantes/".$datos['IdTipoAutorizante'];
		$header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl(API_LICENCIAS.$url);
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		$this->oCurl->setHttpBuildPost(false);
		$this->oCurl->setDebug(true);

		$dataEnviar = new stdClass();

		$dataEnviar->Id = (int)$datos['IdTipoAutorizante'];

			$dataEnviar->Estado = ELIMINADO;


		//print_r($dataEnviar);die;
		$cuerpo = json_encode($dataEnviar);

		if (!$this->oCurl->sendPut($cuerpo,$dataResult)) {
			$this->setError($dataResult['error'],$dataResult['error_description']);
			return false;
		}

		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['error'])) {
			$this->setError($array['error'],$array['error_description']);
			return false;
		}


		return $array;
	}





	public function Activar(array $datos): bool
	{
		$url = "tipos-autorizantes/".$datos['IdTipoAutorizante'];
		$header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl(API_LICENCIAS.$url);
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		$this->oCurl->setHttpBuildPost(false);
		$this->oCurl->setDebug(false);

		$dataEnviar = new stdClass();

		if (!FuncionesPHPLocal::isEmpty($datos['IdTipoAutorizante']))
			$dataEnviar->Id = $datos['IdTipoAutorizante'];

		$dataEnviar->Estado = ACTIVO;

		$cuerpo = json_encode($dataEnviar);

		if (!$this->oCurl->sendPut($cuerpo,$dataResult)) {
			$this->setError($dataResult['error'],$dataResult['error_description']);
			return false;
		}

		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['error'])) {
			$this->setError($array['error'],$array['error_description']);
			return false;
		}

		return true;
	}


	public function Desactivar(array $datos): bool
	{
		$url = "tipos-autorizantes/".$datos['IdTipoAutorizante'];
		$header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl(API_LICENCIAS.$url);
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		$this->oCurl->setHttpBuildPost(false);
		$this->oCurl->setDebug(false);

		$dataEnviar = new stdClass();

		if (!FuncionesPHPLocal::isEmpty($datos['IdTipoAutorizante']))
			$dataEnviar->Id = $datos['IdTipoAutorizante'];

		$dataEnviar->Estado = NOACTIVO;

		$cuerpo = json_encode($dataEnviar);

		if (!$this->oCurl->sendPut($cuerpo,$dataResult)) {
			$this->setError($dataResult['error'],$dataResult['error_description']);
			return false;
		}

		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['error'])) {
			$this->setError($array['error'],$array['error_description']);
			return false;
		}

		return true;
	}


	public static function preprocesarDatosElastic(array $datos): array {
		$datos['Tabla'] = 'AutorizantesTipos';
		$datos['Identificadores'] = [$datos['Documento'], $datos['Matricula']];
		return $datos;
	}


	private function _ValidarInsertar($datos): bool {

		if (!self::_ValidarDatosVacios($datos))
			return false;

		return true;
	}

	private function _ValidarModificar($datos): bool {


		if (!self::_ValidarDatosVacios($datos))
			return false;

		return true;
	}

	private function _ValidarEliminar($datos): bool {

		if (FuncionesPHPLocal::isEmpty($datos['IdTipoAutorizante'])) {
			$this->setError(400, 'Error, registro inexistente.');
			return false;
		}

		return true;
	}

	private function _ValidarDatosVacios($datos): bool {

		if (FuncionesPHPLocal::isEmpty($datos['Nombre'])) {
			$this->setError(400, 'Error, debe ingresar un nombre');
			return false;
		}

		return true;
	}
	public function obtenerTiposAutorizantes(): array {

		$url = 'combos/autorizantes-tipos';
		$header = ["Authorization: Bearer {$_SESSION['token']}"];
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl(API_LICENCIAS . $url);
		$this->oCurl->setFunction(get_class($this) . '-' . __FUNCTION__);
		$this->oCurl->setHttpBuildPost(false);
		$this->oCurl->setDebug(false);

		if (!$this->oCurl->sendGet('',$dataResult)) {
			throw new ExcepcionLogica('Error al buscar tipos de autorizante');
		}

		return FuncionesPHPLocal::DecodificarUtf8($dataResult['hits']);

	}
}