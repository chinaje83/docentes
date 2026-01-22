<?php /** @noinspection PhpUnused */

/** @noinspection DuplicatedCode */


namespace Elastic;


use ManejoErrores;
use GuzzleHttp\Ring\Future\FutureArray;
use GuzzleHttp\Ring\Client\CurlMultiHandler;

/**
 * Class Conexion
 *
 * @author  José R. Méndez <jmendez@bigtree.com.ar>
 * @package Elastic
 *
 *
 */
class Conexion {
    use ManejoErrores;

    /** @var array */
    const DEFAULT_HEADER = ['Content-Type' => ['application/json']];
    /** @var array */
    const DEFAULT_OPTIONS = [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_TIMEOUT => 300];
    /** @var string */
    const AUTH = ELASTIC_USER . ':' . ELASTIC_PASSWORD;
    private const SLOW_QUERY_LOG = true;
    private const SLOW_QUERY_SECONDS = 10;

    /** @var false|resource */
    protected $ch;
    /** @var CurlMultiHandler */
    protected $handler;
    /** @var array */
    protected $header;
    /** @var array */
    protected $options;
    /** @var array */
    protected $hosts;
    /** @var string */
    protected $activeHost;
    /** @var bool */
    protected $active;
    /** @var bool */
    private $debug = false;


    /**
     * Conexion constructor.
     *
     * @param array         $options
     * @param resource|null $mh
     */
    public function __construct(array $options = [], $mh = null) {
        //por ahí hay que cambiar esto por un curl_multi, o un array de conexiones
        //$this->ch = curl_init();
        $this->handler = new CurlMultiHandler(['mh' => $mh ?? curl_multi_init()]);
        $this->setHeader(self::DEFAULT_HEADER);
        $this->setHosts(ELASTIC_SERVER);
        $this->setOptions($options);
        $this->active = true;
    }

    /**
     * Conexion destructor.
     */
    public function __destruct() {
        $this->error = [];
    }

    /**
     * @param string $indice
     * @param string $endPoint
     * @param int    $codigoRetorno
     * @param string $param
     *
     * @return bool
     */
    public function sendHead(string $indice, string $endPoint, ?int &$codigoRetorno, string $param = ''): bool {

        if (!$this->isActive()) {
            $this->setError('400', 'Servidor elasticsearch inactivo.');
            return false;
        }

        $request['url'] = self::obtenerHostActivo() . $indice;

        switch ($endPoint) {
            case '':
                $request['url'] .= '' !== $param ? "/$param" : '';
                break;
            case '_template':
                $request['url'] .= "/$endPoint" . ('' !== $param ? "/$param" : '');
                break;
            default:
                $request['url'] .= "/$endPoint";
        }


        $request['http_method'] = 'HEAD';
        $request['headers'] = $this->getHeader();
        $request['client'] = ['curl' => $this->getOptions()];

        if ($this->isDebug()) {
            $pathName = substr($request['url'], strlen($this->getActiveHost()));
            echo "HEAD $pathName\n";
        }

        $return = $this->ejecutar($request);
        $error = $return->offsetExists('curl') ? $return->offsetGet('curl') : [];
        if (!empty($error)) {
            $this->hostMuerto($this->getActiveHost());
            return $this->sendHead($indice, $endPoint, $codigoRetorno);
        }
        $codigoRetorno = $return->offsetGet('status');

        //$result = stream_get_contents($return->offsetGet('body'));

        return true;
    }

    /**
     * @param string       $indice
     * @param string       $endPoint
     * @param string|array $resultado
     * @param int          $codigoRetorno
     * @param string       $param
     * @param string       $cuerpo
     * @param bool         $returnJson
     *
     * @return bool
     */
    public function sendGet(string $indice, string $endPoint, &$resultado, ?int &$codigoRetorno, string $param = '', string $cuerpo = '', bool $returnJson = false): bool {
        if (!$this->isActive()) {
            $this->setError('400', 'Servidor elasticsearch inactivo.');
            return false;
        }
        $request['url'] = self::obtenerHostActivo() . $indice;
        switch ($endPoint) {
            case '_cluster':
            case '_cat':
            case '_doc':
            case '_template':
            case '_mapping':
                $request['url'] .= "/$endPoint" . ('' !== $param ? "/$param" : '');
                break;
            case '_search':
            case '_termvectors':
                $request['url'] .= "/$endPoint" . ('' !== $param ? "?$param" : '');
                break;
            default:
                $request['url'] .= "/$endPoint";

        }

        $request['http_method'] = 'GET';
        $request['headers'] = $this->getHeader();

        if ($this->isDebug()) {
            $pathName = substr($request['url'], strlen($this->getActiveHost()));
            echo "GET $pathName\n" . ($cuerpo ?? '');
        }

        if (!empty($cuerpo))
            $request['body'] = $cuerpo;
        $request['client'] = ['curl' => $this->getOptions()];
        $start = microtime(true);
        $return = $this->ejecutar($request);
        $error = $return->offsetExists('curl') ? $return->offsetGet('curl') : [];
        $this->slowLog($start, $request['url'], $cuerpo, 'get');
        if (!empty($error)) {
            $this->hostMuerto($this->getActiveHost());
            return $this->sendGet($indice, $endPoint, $resultado, $codigoRetorno, $param, $returnJson);
        }
        $codigoRetorno = $return->offsetGet('status');
        $result = stream_get_contents($return->offsetGet('body'));

        $data = json_decode($result, true);
        $resultado = $returnJson ? $result : $data;
        if (isset($data['errors']) && $data['errors']) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }
        return true;
    }

    /**
     * @param string       $indice
     * @param string       $endPoint
     * @param string       $cuerpo
     * @param string|array $resultado
     * @param int          $codigoRetorno
     * @param string       $param
     * @param bool         $returnJson
     *
     * @return bool
     *
     * @noinspection t
     */
    public function sendPost(string $indice, string $endPoint, string $cuerpo, &$resultado, ?int &$codigoRetorno, string $param = '', bool $returnJson = false): bool {
        if (!$this->isActive()) {
            $this->setError('400', 'Servidor elasticsearch inactivo.');
            return false;
        }
        $request['url'] = self::obtenerHostActivo() . $indice;

        $request['http_method'] = 'POST';
        $request['headers'] = $this->getHeader();
        switch ($endPoint) {
            case '_doc':
                $request['url'] .= "/$endPoint" . ('' !== $param ? "/$param" : '');
                break;
            case '_update':
                if (defined('INCLUDE_TYPE') && true === INCLUDE_TYPE)
                    $request['url'] .= TYPE . ('' !== $param ? "/$param" : '') . "/$endPoint";
                else
                    $request['url'] .= "/$endPoint" . ('' !== $param ? "/$param" : '');
                break;
            case '_search':
            case '_delete_by_query':
            case '_update_by_query':
                $request['url'] .= "/$endPoint" . ('' !== $param ? "?$param" : '');
                break;
            case '_search/scroll':
                $request['url'] .= "$endPoint" . ('' !== $param ? "?$param" : '');
                break;
            case '_count':
                $request['url'] .= "/$endPoint";
                break;
            case '_bulk':
                $request['headers']['Content-Type'] = ['application/x-ndjson'];
                $request['url'] .= "/$endPoint";
                break;
            case '_close':
            case '_open':
            default:
                $request['url'] .= "/$endPoint";
        }

        if ($this->isDebug()) {
            $pathName = substr($request['url'], strlen($this->getActiveHost()));
            echo "POST $pathName\n$cuerpo";
        }

        $request['body'] = $cuerpo;
        $request['client'] = ['curl' => $this->getOptions()];

        $start = microtime(true);
        $return = $this->ejecutar($request);
        $error = $return->offsetExists('curl') ? $return->offsetGet('curl') : [];
        $this->slowLog($start, $request['url'], $cuerpo, 'post');
        if (!empty($error)) {

            $this->hostMuerto($this->getActiveHost());
            return $this->sendPost($indice, $endPoint, $cuerpo, $resultado, $codigoRetorno, $param, $returnJson);
        }
        $codigoRetorno = $return->offsetGet('status');
        //var_dump($codigoRetorno);echo "\n";
        $result = stream_get_contents($return->offsetGet('body'));

        $data = json_decode($result, true);
        $resultado = $returnJson ? $result : $data;
        if (isset($data['errors']) && $data['errors']) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }
        return true;
    }

    /**
     * @param string       $indice
     * @param string       $endPoint
     * @param string       $cuerpo
     * @param string|array $resultado
     * @param int|null     $codigoRetorno
     * @param string|null  $param
     * @param bool         $returnJson
     *
     * @return bool
     */
    public function sendPut(string $indice, string $endPoint, string $cuerpo, &$resultado, ?int &$codigoRetorno, string $param = '', bool $returnJson = false): bool {
        if (!$this->isActive()) {
            $this->setError('400', 'Servidor elasticsearch inactivo.');
            return false;
        }
        $request['url'] = rtrim(self::obtenerHostActivo() . $indice, '/');

        switch ($endPoint) {
            case '_doc':
            case '_create':
            case '_ingest':
            case '_mapping':
            case '_template':
                $request['url'] .= "/$endPoint" . ('' !== $param ? "/$param" : '');
                break;
            case '_settings':
            case '_aliases':
                $request['url'] .= "/$endPoint";
                break;
            default:
                $request['url'] .= "/$endPoint";
        }

        //echo $request['url'];die;
        $request['http_method'] = 'PUT';
        $request['headers'] = $this->getHeader();
        $request['body'] = $cuerpo;
        $request['client'] = ['curl' => $this->getOptions()];


        if ($this->isDebug()) {
            $pathName = substr($request['url'], strlen($this->getActiveHost()));
            echo "PUT $pathName\n$cuerpo\n";
        }

        $start = microtime(true);
        $return = $this->ejecutar($request);
        $error = $return->offsetExists('curl') ? $return->offsetGet('curl') : [];
        $this->slowLog($start, $request['url'], $cuerpo, 'put');
        if (!empty($error)) {
            $this->hostMuerto($this->getActiveHost());
            return $this->sendPut($indice, $endPoint, $cuerpo, $resultado, $codigoRetorno, $param, $returnJson);
        }
        $codigoRetorno = $return->offsetGet('status');
        //print_r($codigoRetorno);echo "\n";
        $result = stream_get_contents($return->offsetGet('body'));
        //print_r($result);echo "\n";

        $data = json_decode($result, true);
        $resultado = $returnJson ? $result : $data;
        if (isset($data['errors']) && $data['errors']) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }
        return true;
    }

    /**
     * @param string       $indice
     * @param string       $endPoint
     * @param string|array $resultado
     * @param int          $codigoRetorno
     * @param string       $param
     * @param string       $cuerpo
     * @param bool         $returnJson
     *
     * @return bool
     */
    public function sendDelete(string $indice, string $endPoint, &$resultado, ?int &$codigoRetorno, string $param = '', string $cuerpo = '', bool $returnJson = false): bool {
        if (!$this->isActive()) {
            $this->setError('400', 'Servidor elasticsearch inactivo.');
            return false;
        }
        $request['url'] = self::obtenerHostActivo() . $indice;
        switch ($endPoint) {
            case '_doc':
                $request['url'] .= "/$endPoint" . ('' !== $param ? "/$param" : '');
                break;
            case '_search/scroll':
                $request['url'] .= $endPoint;
                break;
            default:
                $request['url'] .= "/$endPoint";

        }

        $request['http_method'] = 'DELETE';
        $request['headers'] = $this->getHeader();
        $request['body'] = $cuerpo;
        $request['client'] = ['curl' => $this->getOptions()];

        if ($this->isDebug()) {
            $pathName = substr($request['url'], strlen($this->getActiveHost()));
            echo "DELETE $pathName\n" . ($cuerpo ?? '');
        }

        $start = microtime(true);
        $return = $this->ejecutar($request);
        $error = $return->offsetExists('curl') ? $return->offsetGet('curl') : [];
        $this->slowLog($start, $request['url'], $cuerpo, 'delete');
        if (!empty($error)) {
            $this->hostMuerto($this->getActiveHost());
            return $this->sendDelete($indice, $endPoint, $resultado, $codigoRetorno, $param, $returnJson);
        }
        $codigoRetorno = $return->offsetGet('status');
        $result = stream_get_contents($return->offsetGet('body'));
        /*if(floor($codigoRetorno/200) > 1)
        {
            $this->hostMuerto($this->getActiveHost());
            return $this->{__FUNCTION__}($indice, $endPoint, $resultado, $codigoRetorno, $param, $returnJson);
        }*/
        $data = json_decode($result, true);
        $resultado = $returnJson ? $result : $data;
        if (isset($data['errors']) && $data['errors']) {
            $this->setError(500, Funciones::DevolverError($data));
            return false;
        }

        return true;
    }

    /**
     * @return string|false
     */
    private function obtenerHostActivo() {
        if (!$this->isActive()) {
            $this->setError('400', 'Servidor elasticsearch inactivo.');
            return false;
        }
        $activeHost = $this->getActiveHost();
        $hosts = $this->getHosts();

        if (empty($activeHost) || !in_array($activeHost, $hosts)) {
            shuffle($hosts);
            $activeHost = array_pop($hosts);
        }
        $this->setActiveHost($activeHost);

        return $activeHost;
    }

    /**
     * @param string $host
     *
     * @return false|string
     */
    protected function hostMuerto(string $host) {
        $hosts = $this->getHosts();

        $key = array_search($host, $hosts);

        unset($hosts[$key]);

        if (empty($hosts))
            $this->setActive(false);

        $this->setHosts($hosts);

        return $this->obtenerHostActivo();
    }

    /**
     * @param array $request
     *
     * @return FutureArray
     */
    private function ejecutar(array $request): FutureArray {
        return $this->handler->__invoke($request);
    }

    //----------------------------------------------------------------------------------
    //
    //          Setters and Getters
    //
    //----------------------------------------------------------------------------------


    /**
     * @return string|null
     */
    public function getActiveHost(): ?string {
        return $this->activeHost;
    }

    /**
     * @param string $activeHost
     */
    public function setActiveHost(string $activeHost): void {
        $this->activeHost = $activeHost;
    }

    /**
     * @return array
     */
    public function getHosts(): array {
        return $this->hosts;
    }

    /**
     * @param array|string $hosts
     */
    public function setHosts($hosts): void {
        if (is_object($hosts))
            $hosts = (array)$hosts;
        $this->hosts = is_array($hosts) ? $hosts : [$hosts];
    }

    /**
     * @return bool
     */
    public function isActive(): bool {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void {
        $this->active = $active;
    }

    /**
     * @return array
     */
    public function getHeader(): array {
        return $this->header;
    }

    /**
     * @param array $header
     */
    public function setHeader(array $header): void {
        $this->header = $header;
    }

    /**
     * @return array
     */
    public function getOptions(): array {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void {
        $this->options = empty($options) ? self::DEFAULT_OPTIONS : array_merge(self::DEFAULT_OPTIONS, $options);
        if (defined('XPACK_SECURITY') && true === XPACK_SECURITY)
            $this->options[CURLOPT_USERPWD] = self::AUTH;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug): void {
        $this->debug = $debug;
    }

    /**
     * @param $header
     */
    protected function pushHeader($header): void {
        $this->header[] = $header;
    }

    /**
     * @param             $start
     * @param             $url
     * @param string|null $cuerpo
     * @param string      $lcMethod
     *
     * @return void
     */
    protected function slowLog($start, $url, ?string $cuerpo, string $lcMethod): void {
        if (!self::SLOW_QUERY_LOG) {
            return;
        }
        $segundos = microtime(true) - $start;
        // var_dump($segundos, self::SLOW_QUERY_SECONDS, $segundos >= self::SLOW_QUERY_SECONDS);die;
        if ($segundos >= self::SLOW_QUERY_SECONDS) {
            if (!in_array($lcMethod, ['get', 'post', 'put', 'delete', 'head'])) {
                $lcMethod = 'get';
            }
            $ucMethod = strtoupper($lcMethod);
            $dir = ltrim(PATH_STORAGE, '/') . '/log/docentes';
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
            $file = $dir . "/elastic_{$lcMethod}_" . date('Ymd') . '.log';
            $pathName = substr($url, strlen($this->getActiveHost()));
            file_put_contents(
                $file,
                "La siguiente consulta tardó $segundos segundos.\n$ucMethod $pathName\n" .
                ($cuerpo ?? '') .
                PHP_EOL .
                '------------------------------------------------------------------------------------------' .
                PHP_EOL,
                FILE_APPEND
            );
        }
    }
}
