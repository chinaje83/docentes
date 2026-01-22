<?php


class cServiciosAccesos
{
	use ManejoErrores;
	/** @var CurlBigtree  */
	protected $oCurl;
	/** @var accesoBDLocal */
	protected $conexion;
	/** @var bool  */
	protected $Utf8;
	/** @var string[]  */
	public const OS = [
		1 => 'Windows',
		2 => 'Linux',
		3 => 'Mac OS',
		4 => 'Unix',
		5 => 'Otros'
	];
	/** @var string[]  */
	public const TIPO = [
		1 => 'Login SSO',
		2 => 'Login API',
		3 => 'Renovación de token',
		4 => 'Cambio de rol',
		5 => 'Cambio de escuela',
	];

	function __construct($conexion)
	{
		$this->conexion = &$conexion;
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

	public function buscarListado(array $datos, ?array &$resultado)
	{
		$url = 'v1/log-accesos?';

		$urlAnexa = [];
		$cuerpo = new stdClass();

		if (isset($datos['page']) && $datos['page'] != "")
			$urlAnexa['page'] =  $datos['page'];

		if (isset($datos['rows']) && $datos['rows'] != "")
			$urlAnexa['rows'] = $datos['rows'];


		if (isset($datos['sidx']) && $datos['sidx'] != "")
			$urlAnexa['sidx'] = $datos['sidx'];


		if (isset($datos['sord']) && $datos['sord'] != "")
			$urlAnexa['sord'] = $datos['sord'];

		if(isset($datos['IdUsuario']) && $datos['IdUsuario'] != '')
			$cuerpo->IdUsuario = (int) $datos['IdUsuario'];

		if(isset($datos['Roles']) && $datos['Roles'] != '')
			$cuerpo->Roles = $datos['Roles'];

		if(isset($datos['Aplicacion']) && $datos['Aplicacion'] != '')
			$cuerpo->Aplicacion = $datos['Aplicacion'];

		if(isset($datos['FechaDesde']) && $datos['FechaDesde'] != '')
			$cuerpo->FechaDesde = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],
			                                                        'dd/mm/aaaa', 'aaaa-mm-dd') . ' 00:00:00';

		if(isset($datos['FechaHasta']) && $datos['FechaHasta'] != '')
			$cuerpo->FechaHasta = FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],
			                                                        'dd/mm/aaaa', 'aaaa-mm-dd') . ' 23:59:59';


		$url .= http_build_query($urlAnexa);
		$header = ["Authorization: Bearer {$_SESSION['token']}"];
		$header[] = 'Content-Type: application/json';
		$this->oCurl->setUrl(APISSO);
		$this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
		$this->oCurl->setHeader($header);
		$this->oCurl->setDebug(false);
		if(!$this->oCurl->sendGet($url,$dataResult, json_encode($cuerpo)))
		{
			$this->setError("Error","Ocurrió un error al buscar accesos");
			return false;
		}
		if (!$this->Utf8)
			$resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$resultado = $dataResult;

		return true;
	}


	public function buscarAplicaciones(?array &$resultado): bool {
		$url = "v1/aplicaciones";
		$header = ["Authorization: Bearer {$_SESSION['token']}"];
		$this->oCurl->setUrl(APISSO);
		$this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
		$this->oCurl->setHeader($header);
		$this->oCurl->setDebug(false);
		if(!$this->oCurl->sendGet($url,$dataResult))
		{
			$this->setError("Error","Ocurrió un error al buscar aplicaciones");
			return false;
		}
		if (!$this->Utf8)
			$resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$resultado = $dataResult;

		return true;
	}


	public function actualizarAcceso(): bool {
		$url = 'v1/log-accesos';
		$header = ["Authorization: Bearer {$_SESSION['token']}"];
		$this->oCurl->setUrl(APISSO . $url);
		$this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
		$this->oCurl->setHeader($header);
		$this->oCurl->setDebug(false);

		$datosEnviar = new stdClass();
		$datosEnviar->RolActivo = current($_SESSION['rolcod']);
		$datosEnviar->EscuelaSeleccionada = $_SESSION['IdEscuela'] ?? null;
		$datosEnviar->RegionSeleccionada = $_SESSION['IdRegion'] ?? null;
		$cuerpo = json_encode($datosEnviar);
		if(!$this->oCurl->sendPatch($cuerpo,$dataResult))
		{
			$this->setError("Error","Ocurrió un error al actualizar el log");
			return false;
		}

		return true;
	}

}
