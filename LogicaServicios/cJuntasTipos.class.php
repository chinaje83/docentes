<?php
use Bigtree\ExcepcionLogica;

class cJuntasTipos
{
    use ManejoErrores;

    protected $oCurl;
    protected $conexion;
    protected $error;
    protected $Utf8;
    protected $MemCache;

    const MemCacheExpire = 86400; # 1 dia

    function __construct($conexion) {
        $this->conexion = &$conexion;
        $this->error = array();
        $this->oCurl = new CurlBigtree();
        $this->Utf8 = false;
    }

    public function __destruct() {
        $this->oCurl->CloseCurl();
        unset($this->oCurl);
    }

    public function getCurl() {
        return $this->oCurl;
    }

    public function CodificarUtf8() {
        $this->Utf8 = true;
    }

    /**
     * @param array|null $resultado
     * @param int|null $numfilas
     * @return bool
     */
    public function obtenerListado(?array &$resultado, ?int &$numfilas): bool {

        $this->oCurl->setHeader(["Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setUrl(API_LICENCIAS . 'combos/tipos-juntas');
        $this->oCurl->setFunction(get_class($this) . '-' . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet('', $dataResult)) {
            $this->setError($dataResult['error'], utf8_decode($dataResult['error_description']));
            return false;
        }

        $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult['filas']);
        $numfilas = $dataResult['numfilas'];

        return true;
    }
}


