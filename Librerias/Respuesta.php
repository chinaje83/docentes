<?php

namespace Bigtree;

use SimpleXMLElement;

class Respuesta {
    /**
     * @var string
     */
    public $version;

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var string
     */
    protected $statusText;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var array
     */
    protected $httpHeaders = [];

    /**
     * @var array
     */
    public static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    ];

    /**
     * @param array $parameters
     * @param int   $statusCode
     * @param array $headers
     *
     * @throws \Bigtree\ExcepcionLogica
     */
    public function __construct(array $parameters = [], int $statusCode = 200, array $headers = []) {
        $this->setParameters($parameters);
        $this->setStatusCode($statusCode);
        $this->setHttpHeaders($headers);
        $this->version = '1.1';
    }

    /**
     * Converts the response object to string containing all headers and the response content.
     *
     * @return string The response with headers and content
     * @throws \Bigtree\ExcepcionLogica
     */
    public function __toString() {
        $headers = array();
        foreach ($this->httpHeaders as $name => $value) {
            $headers[$name] = (array)$value;
        }

        return
            sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText) . "\r\n" .
            $this->getHttpHeadersAsString($headers) . "\r\n" .
            $this->getResponseBody();
    }

    /**
     * Returns the build header line.
     *
     * @param string $name  The header name
     * @param string $value The header value
     *
     * @return string The built header line
     */
    protected function buildHeader(string $name, string $value): string {
        return sprintf("%s: %s\n", $name, $value);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int {
        return $this->statusCode;
    }

    /**
     * @param int         $statusCode
     * @param string|null $text
     *
     * @throws \Bigtree\ExcepcionLogica
     */
    public function setStatusCode(int $statusCode, ?string $text = null) {
        $this->statusCode = $statusCode;
        if ($this->isInvalid()) {
            throw new ExcepcionLogica(sprintf('The HTTP status code "%s" is not valid.', $statusCode));
        }

        $this->statusText = is_null($text) ? (self::$statusTexts[$this->statusCode]??'') : $text;
    }

    /**
     * @return string
     */
    public function getStatusText(): string {
        return $this->statusText;
    }

    /**
     * @return array
     */
    public function getParameters(): array {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     *
     * @return Respuesta
     */
    public function setParameters(array $parameters): Respuesta {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @param array $parameters
     *
     * @return Respuesta
     */
    public function addParameters(array $parameters): Respuesta {
        $this->parameters = array_merge($this->parameters, $parameters);
        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParameter(string $name, $default = null) {
        return $this->parameters[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return Respuesta
     */
    public function setParameter(string $name, $value): Respuesta {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * @param array $httpHeaders
     *
     * @return \Bigtree\Respuesta
     */
    public function setHttpHeaders(array $httpHeaders): Respuesta {
        $this->httpHeaders = $httpHeaders;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return \Bigtree\Respuesta
     */
    public function setHttpHeader(string $name, $value): Respuesta {
        $this->httpHeaders[$name] = $value;
        return $this;
    }

    /**
     * @param array $httpHeaders
     *
     * @return \Bigtree\Respuesta
     */
    public function addHttpHeaders(array $httpHeaders): Respuesta {
        $this->httpHeaders = array_merge($this->httpHeaders, $httpHeaders);
        return $this;
    }

    /**
     * @return array
     */
    public function getHttpHeaders(): array {
        return $this->httpHeaders;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getHttpHeader(string $name, $default = null) {
        return $this->httpHeaders[$name] ?? $default;
    }

    /**
     * @param string $format
     *
     * @return bool|string
     * @throws ExcepcionLogica
     */
    public function getResponseBody(string $format = 'json') {
        switch ($format) {
            case 'json':
                return $this->parameters ? json_encode($this->parameters) : '';
            case 'xml':
                // this only works for single-level arrays
                $xml = new SimpleXMLElement('<response/>');
                foreach ($this->parameters as $key => $param) {
                    $xml->addChild($key, $param);
                }

                return $xml->asXML();
        }

        throw new ExcepcionLogica(sprintf('The format %s is not supported', $format));

    }

    /**
     * @param string $format
     *
     * @throws \Bigtree\ExcepcionLogica
     */
    public function send(string $format = 'json'): void {
        // headers have already been sent by the developer
        if (headers_sent()) {
            return;
        }

        switch ($format) {
            case 'json':
                $this->setHttpHeader('Content-Type', 'application/json');
                break;
            case 'xml':
                $this->setHttpHeader('Content-Type', 'text/xml');
                break;
        }
        // status
        header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText));

        foreach ($this->getHttpHeaders() as $name => $header)
            header(sprintf('%s: %s', $name, $header));

        echo $this->getResponseBody($format);
    }

    /**
     * @param int|array   $statusCode
     * @param string|null $error
     * @param string|null $errorDescription
     * @param string|null $errorUri
     *
     * @return self
     * @throws \Bigtree\ExcepcionLogica
     */
    public function setError($statusCode, ?string $errorDescription = null, ?string $errorUri = null): self {
        if (is_array($statusCode)) {
            $errorDescription = $statusCode['error_description']??'';
            $statusCode = (int)$statusCode['error']??400;
        }

        if (!is_null($errorUri)) {
            if (strlen($errorUri) > 0 && $errorUri[0] == '#') {
                // we are referencing an oauth bookmark (for brevity)
                $errorUri = 'https://tools.ietf.org/html/rfc6749' . $errorUri;
            }
            $parameters['error_uri'] = $errorUri;
        }

        $httpHeaders = array(
            'Cache-Control' => 'no-store'
        );

        $this->setStatusCode($statusCode);
        if ('UTF-8' != mb_detect_encoding($errorDescription, 'UTF-8', true))
            $errorDescription = utf8_encode($errorDescription);
        $parameters = array(
            'error' => $this->getStatusText(),
            'error_description' => $errorDescription,
        );
        $this->addParameters($parameters);
        $this->addHttpHeaders($httpHeaders);

        if (!$this->isClientError() && !$this->isServerError()) {
            throw new ExcepcionLogica(sprintf('The HTTP status code is not an error ("%s" given).', $statusCode));
        }
        return $this;
    }

    /**
     * @param int         $statusCode
     * @param string      $url
     * @param string|null $state
     * @param string|null $error
     * @param string|null $errorDescription
     * @param string|null $errorUri
     *
     * @return \Bigtree\Respuesta
     * @throws \Bigtree\ExcepcionLogica
     */
    public function setRedirect(int $statusCode, string $url, ?string $state = null, ?string $error = null, ?string $errorDescription = null, ?string $errorUri = null): Respuesta {
        if (empty($url)) {
            throw new ExcepcionLogica('Cannot redirect to an empty URL.');
        }

        $parameters = array();

        if (!is_null($state)) {
            $parameters['state'] = $state;
        }

        if (!is_null($error)) {
            $this->setError(400, $error, $errorDescription, $errorUri);
        }
        $this->setStatusCode($statusCode);
        $this->addParameters($parameters);

        if (count($this->parameters) > 0) {
            // add parameters to URL redirection
            $parts = parse_url($url);
            $sep = isset($parts['query']) && !empty($parts['query']) ? '&' : '?';
            $url .= $sep . http_build_query($this->parameters);
        }

        $this->addHttpHeaders(array('Location' => $url));

        if (!$this->isRedirection()) {
            throw new ExcepcionLogica(sprintf('The HTTP status code is not a redirect ("%s" given).', $statusCode));
        }
        return $this;
    }

    /**
     * @return Boolean
     *
     * @api
     *
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     */
    public function isInvalid(): bool {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

    /**
     * @return Boolean
     *
     * @api
     */
    public function isInformational(): bool {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    /**
     * @return Boolean
     *
     * @api
     */
    public function isSuccessful(): bool {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * @return Boolean
     *
     * @api
     */
    public function isRedirection(): bool {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * @return Boolean
     *
     * @api
     */
    public function isClientError(): bool {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * @return Boolean
     *
     * @api
     */
    public function isServerError(): bool {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * Function from Symfony2 HttpFoundation - output pretty header
     *
     * @param array $headers
     *
     * @return string
     */
    private function getHttpHeadersAsString(array $headers): string {
        if (count($headers) == 0) {
            return '';
        }

        $max = max(array_map('strlen', array_keys($headers))) + 1;
        $content = '';
        ksort($headers);
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                $content .= sprintf("%-{$max}s %s\r\n", $this->beautifyHeaderName($name) . ':', $value);
            }
        }

        return $content;
    }

    /**
     * Function from Symfony2 HttpFoundation - output pretty header
     *
     * @param string $name
     *
     * @return array|string|string[]|null
     */
    private function beautifyHeaderName(string $name) {
        return preg_replace_callback('/-(.)/', array($this, 'beautifyCallback'), ucfirst($name));
    }

    /**
     * Function from Symfony2 HttpFoundation - output pretty header
     *
     * @param array $match
     *
     * @return string
     */
    private function beautifyCallback(array $match): string {
        return '-' . strtoupper($match[1]);
    }
}