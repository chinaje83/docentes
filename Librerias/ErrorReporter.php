<?php
/**
 * ErrorReporter.php (v3) — accesoBDLocal (Bigtree) + PHPMailer opcional
 *
 * - Captura warnings/notices (como excepciones), excepciones no capturadas y fatals.
 * - Canaliza a: correo (PHPMailer/SMTP) opcional y/o MySQL usando **accesoBDLocal** (wrapper Bigtree).
 *
 * Requisitos:
 *  - PHP 7.4+
 *  - Si usás email: Composer con phpmailer/phpmailer
 *  - Para DB: tener disponible la clase accesoBDLocal (wrapper) y sus dependencias cargadas
 *
 * Uso mínimo (solo DB con accesoBDLocal):
 *   require __DIR__.'/accesoBDLocal.wrapper.php'; // o tu include habitual
 *   require __DIR__.'/ErrorReporter.php';
 *   ErrorReporter::init([
 *     'channels' => ['email' => false, 'db' => true],
 *     'db' => [
 *        'adapter' => 'accesoBD',
 *        'wrapper_class' => 'accesoBDLocal',           // opcional (default)
 *        'include' => __DIR__.'/accesoBDLocal.wrapper.php', // opcional si no está autoloaded
 *        'server' => '127.0.0.1',
 *        'user'   => 'log_user',
 *        'pass'   => 'log_pass',
 *        'port'   => 3306,
 *        'database' => 'logs',
 *        'table' => 'error_reports',
 *        // O bien, si ya tenés una instancia creada en tu app:
 *        // 'instance' => $conexionAccesoBDLocal,
 *     ],
 *   ]);
 *
 * Con email + DB:
 *   require __DIR__.'/vendor/autoload.php'; // PHPMailer
 *   require __DIR__.'/accesoBDLocal.wrapper.php';
 *   require __DIR__.'/ErrorReporter.php';
 *   ErrorReporter::init([
 *     'channels' => ['email' => true, 'db' => true],
 *     'to'   => 'alertas@tu-dominio.com',
 *     'from' => 'noreply@tu-dominio.com',
 *     'from_name' => 'Error Reporter',
 *     'smtp' => [
 *       'host' => 'smtp.tu-proveedor.com',
 *       'port' => 587,
 *       'username' => 'usuario',
 *       'password' => 'clave',
 *       'secure' => 'tls'
 *     ],
 *     'db' => [
 *       'adapter' => 'accesoBD',
 *       'include' => __DIR__.'/accesoBDLocal.wrapper.php',
 *       'server' => '127.0.0.1',
 *       'user'   => 'log_user',
 *       'pass'   => 'log_pass',
 *       'database' => 'logs',
 *       'table' => 'error_reports',
 *     ]
 *   ]);
 */

class ErrorReporter {
    /** @var array<string,mixed> */
    private static $config = [
        'enabled' => true,
        'channels' => ['email' => true, 'db' => true],
        // Email
        'to' => '',
        'from' => '',
        'from_name' => 'Error Reporter',
        'smtp' => [
            'host' => '',
            'port' => 587,
            'username' => '',
            'password' => '',
            'secure' => 'tls', // tls|ssl|''
            'auth' => true,
            'timeout' => 10,
        ],
        'min_seconds_between_emails' => 60,
        'rate_limit_file' => null,
        // DB accesoBD
        'db' => [
            'adapter' => 'accesoBD',         // 'accesoBD' | (futuro: 'pdo')
            'include' => null,               // ruta al wrapper si hace falta require_once
            'wrapper_class' => 'accesoBDLocal',
            'instance' => null,              // instancia ya creada (opcional)
            'server' => '',
            'user' => '',
            'pass' => '',
            'port' => 3306,
            'database' => '',
            'table' => 'error_reports',
        ],
        // Otros
        'mask_fields' => ['password', 'pass', 'clave', 'token', 'secret', 'authorization', 'api_key'],
        'show_friendly_page' => true,
        'max_body_len' => 20000,
        'max_json_len' => 16000,
        'user' => null, // string|callable: username fijo o función que lo devuelva
        'rol' => null, // string|callable: rol fijo o función que lo devuelva
    ];

    /** @var object|null accesoBDLocal */
    private static $adb = null; // instancia accesoBDLocal

    public static function init(array $cfg = []): void {
        self::$config = array_replace_recursive(self::$config, $cfg);

        if (empty(self::$config['rate_limit_file'])) {
            self::$config['rate_limit_file'] = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR . 'php_error_email_rl.lock';
        }

        ini_set('display_errors', '0');
        error_reporting(E_ALL);
        ini_set('log_errors', '1');

        //   set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);

        if (!ob_get_level()) @ob_start();
    }

    public static function setEnabled(bool $enabled): void {
        self::$config['enabled'] = $enabled;
    }

    /** Permite inyectar una instancia existente de accesoBDLocal */
    public static function setDbInstance($adb): void {
        self::$adb = $adb;
    }

    public static function handleError(int $severity, string $message, string $file, int $line): bool {
        if (!(error_reporting() & $severity)) {
            return false; // respeta el operador @
        }
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    public static function handleException(Throwable $e): void {
        try {
            $ctx = self::context();
            $body = self::formatException($e, $ctx);
            self::notify('UNCAUGHT EXCEPTION', $body);
            self::logToDb('exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ], $ctx);
        } catch (Throwable $internal) {
            error_log('ErrorReporter::handleException failed: ' . $internal->getMessage());
        } finally {
            if (self::$config['show_friendly_page']) self::renderFriendlyPage();
            self::terminateFast();
        }
    }

    public static function handleShutdown(): void {

        $err = error_get_last();
        if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            try {
                $ctx = self::context();
                print_r($ctx);
                die();
                $body = self::formatFatal($err, $ctx);
                self::notify('FATAL ERROR', $body);
                self::logToDb('fatal', [
                    'message' => $err['message'] ?? '',
                    'file' => $err['file'] ?? '',
                    'line' => (int)($err['line'] ?? 0),
                    'trace' => '', // No hay stack real en shutdown
                ], $ctx);
            } catch (Throwable $internal) {
                error_log('ErrorReporter::handleShutdown failed: ' . $internal->getMessage());
            } finally {
                if (self::$config['show_friendly_page']) self::renderFriendlyPage();
            }
        }
        self::terminateFast();
    }

    /** ------------------------ accesoBD helpers ------------------------ */

    /** @return object|null accesoBDLocal */
    private static function adb() {
        if (self::$adb) return self::$adb;

        $db = self::$config['db'] ?? [];
        if (($db['adapter'] ?? '') !== 'accesoBD') return null;

        if (!empty($db['instance'])) {
            self::$adb = $db['instance'];
            return self::$adb;
        }

        if (!empty($db['include']) && is_file($db['include'])) {
            require_once $db['include'];
        }

        $class = $db['wrapper_class'] ?? 'accesoBDLocal';
        if (!class_exists($class)) {
            error_log('ErrorReporter accesoBD: no se encuentra la clase wrapper ' . $class);
            return null;
        }

        try {
            $port = (int)($db['port'] ?? 3306);
            self::$adb = new $class((string)$db['server'], (string)$db['user'], (string)$db['pass'], $port);
            if (!empty($db['database'])) {
                // Selecciona la base
                self::$adb->SeleccionBD((string)$db['database']);
            }
        } catch (Throwable $e) {
            error_log('ErrorReporter accesoBD init failed: ' . $e->getMessage());
            self::$adb = null;
        }
        return self::$adb;
    }

    /** Ejecuta INSERT usando ejecutarSQL del wrapper */
    private static function adbInsert(string $sql): void {
        $adb = self::adb();
        if (!$adb) return;
        try {
            $resultado = null;
            $numfilas = 0;
            $errno = 0;
            // Operación textual usada por su lib (suele ser 'INSERT')
            $adb->ejecutarSQL($sql, 'INS', $resultado, $numfilas, $errno);
            if ($errno) {
                error_log('ErrorReporter accesoBD insert errno: ' . $errno);
            }
        } catch (Throwable $e) {
            error_log('ErrorReporter accesoBD insert failed: ' . $e->getMessage());
        }
    }

    /** Sanitiza y escapa usando la propia lib si está disponible */
    private static function adbEscape($val) {
        $adb = self::adb();
        if (is_null($adb)) return addslashes((string)$val);
        if (method_exists($adb, 'escapearCaracteres')) {
            return $adb->escapearCaracteres((string)$val);
        }
        if (method_exists($adb, 'EscapeaElString')) {
            return $adb->EscapeaElString((string)$val);
        }
        return addslashes((string)$val);
    }

    /** ------------------------ DB logging ------------------------ */

    /**
     * @param array<string,mixed> $errData message,file,line,trace
     * @param array<string,mixed> $ctx
     */
    private static function logToDb(string $type, array $errData, array $ctx): void {
        if (!self::$config['enabled']) return;
        if (!(self::$config['channels']['db'] ?? false)) return;
        $db = self::$config['db'] ?? [];
        if (($db['adapter'] ?? '') !== 'accesoBD') return;

        $table = (string)($db['table'] ?? 'error_reports');

        $getJson = self::jsonTruncated($ctx['get'] ?? []);
        $postJson = self::jsonTruncated($ctx['post'] ?? []);

        $cols = '`created_at`,`type`,`host`,`uri`,`method`,`ip`,`script`,`referer`,`user_agent`,`session_id`,'
            . '`app_user`,'  // <--- NUEVO
            . '`app_rol`,'  // <--- NUEVO
            . '`get_json`,`post_json`,`message`,`file`,`line`,`trace`,`memory_usage`,`memory_peak`';

        // Escapar todos los valores
        $vals = [
            date('Y-m-d H:i:s'),
            $type,
            (string)($ctx['host'] ?? ''),
            (string)($ctx['uri'] ?? ''),
            (string)($ctx['method'] ?? ''),
            (string)($ctx['ip'] ?? ''),
            (string)($ctx['script'] ?? ''),
            (string)($ctx['referer'] ?? ''),
            (string)($ctx['user_agent'] ?? ''),
            (string)($ctx['session_id'] ?? ''),
            (string)($ctx['app_user'] ?? ''),          // <--- NUEVO
            (string)($ctx['app_rol'] ?? ''),          // <--- NUEVO
            $getJson,
            $postJson,
            (string)($errData['message'] ?? ''),
            (string)($errData['file'] ?? ''),
            (string)((string)($errData['line'] ?? '0')),
            (string)($errData['trace'] ?? ''),
            (string)((string)($ctx['memory_usage'] ?? '0')),
            (string)((string)($ctx['memory_peak'] ?? '0')),
        ];


        $esc = array_map(function ($v) {
            return "'" . self::adbEscape($v) . "'";
        }, $vals);

        $sql = "INSERT INTO $table ($cols) VALUES (" . implode(',', $esc) . ")";
        self::adbInsert($sql);
    }

    private static function jsonTruncated($data): string {
        try {
            $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false) $json = 'null';
        } catch (Throwable $e) {
            $json = 'null';
        }
        $max = (int)self::$config['max_json_len'];
        if (strlen($json) > $max) {
            return substr($json, 0, $max) . '...[truncated]';
        }
        return $json;
    }

    /** ------------------------ formatting & context ------------------------ */

    private static function sanitizeArray($data, array $maskKeys): array {
        $out = [];
        if (!is_array($data)) return $out;
        foreach ($data as $k => $v) {
            $keyLower = is_string($k) ? strtolower($k) : $k;
            if (is_array($v)) {
                $out[$k] = self::sanitizeArray($v, $maskKeys);
            } else {
                if (is_string($keyLower) && self::keyIn($keyLower, $maskKeys)) {
                    $out[$k] = '[MASKED]';
                } else {
                    $out[$k] = $v;
                }
            }
        }
        return $out;
    }

    private static function keyIn(string $keyLower, array $maskKeys): bool {
        foreach ($maskKeys as $mk) {
            $mkLower = strtolower($mk);
            if ($mkLower === $keyLower) return true;
            if (strpos($keyLower, $mkLower) !== false) return true; // match parcial
        }
        return false;
    }

    private static function context(): array {
        return [
            'time' => date('c'),
            'host' => php_uname('n'),
            'php_version' => PHP_VERSION,
            'sapi' => PHP_SAPI,
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'script' => $_SERVER['SCRIPT_FILENAME'] ?? '',
            'referer' => $_SERVER['HTTP_REFERER'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'session_id' => session_id() ?: '',
            'get' => $_GET ?? [],
            'post' => self::sanitizeArray($_POST ?? [], self::$config['mask_fields']),
            'app_user' => self::resolveUser(),       // <--- NUEVO
            'app_rol' => self::resolveRole(),       // <--- NUEVO
        ];
    }

    private static function truncateText(string $txt, int $maxLen = 20000): string {
        if (strlen($txt) > $maxLen) {
            return substr($txt, 0, $maxLen) . "\n...[truncated]...";
        }
        return $txt;
    }

    private static function formatException(Throwable $e, array $ctx): string {
        $lines = [
            "Message : " . $e->getMessage(),
            "Type    : " . get_class($e),
            "File    : " . $e->getFile() . ':' . $e->getLine(),
            "Trace   :\n" . $e->getTraceAsString(),
            "\n-- CONTEXT --\n" . print_r($ctx, true),
        ];
        return self::truncateText(implode("\n", $lines), (int)self::$config['max_body_len']);
    }

    private static function formatFatal(array $err, array $ctx): string {
        $map = [
            E_ERROR => 'E_ERROR',
            E_PARSE => 'E_PARSE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        ];
        $type = $map[$err['type']] ?? ('TYPE ' . $err['type']);
        $lines = [
            "Type    : $type",
            "Message : {$err['message']}",
            "File    : {$err['file']}:{$err['line']}",
            "\n-- CONTEXT --\n" . print_r($ctx, true),
        ];
        return self::truncateText(implode("\n", $lines), (int)self::$config['max_body_len']);
    }

    private static function renderFriendlyPage(): void {
        try {
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: text/html; charset=UTF-8');
            }
            if (ob_get_level()) {
                @ob_clean();
            }
            echo "<h1>Ups… algo salió mal</h1><p>Ya nos llegó el reporte y lo veremos a la brevedad.</p>";
            if (ob_get_level()) {
                @ob_end_flush();
            }
        } catch (Throwable $e) {
            // ignore
        }
    }

    private static function terminateFast(): void {
        if (function_exists('fastcgi_finish_request')) {
            @fastcgi_finish_request();
        }
    }

    /** ------------------------ notification (email) ------------------------ */

    private static function shouldEmail(): bool {
        if (!self::$config['enabled']) return false;
        $ch = self::$config['channels']['email'] ?? false;
        return (bool)$ch && !empty(self::$config['to']);
    }

    private static function notify(string $prefix, string $body): void {
        if (!self::shouldEmail()) return;

        // Rate limit solo para emails (DB registra todo)
        $now = time();
        $rlFile = (string)self::$config['rate_limit_file'];
        $last = is_file($rlFile) ? (int)@file_get_contents($rlFile) : 0;
        if ($now - $last < (int)self::$config['min_seconds_between_emails']) {
            return;
        }
        @file_put_contents($rlFile, (string)$now);

        $subject = sprintf("[%s] %s - %s %s",
            $_SERVER['HTTP_HOST'] ?? 'CLI',
            $prefix,
            $_SERVER['REQUEST_METHOD'] ?? '',
            $_SERVER['REQUEST_URI'] ?? (basename($_SERVER['SCRIPT_NAME'] ?? ''))
        );

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = (string)self::$config['smtp']['host'];
            $mail->Port = (int)self::$config['smtp']['port'];
            $mail->SMTPAuth = (bool)(self::$config['smtp']['auth'] ?? true);
            if (!empty(self::$config['smtp']['username'])) $mail->Username = (string)self::$config['smtp']['username'];
            if (!empty(self::$config['smtp']['password'])) $mail->Password = (string)self::$config['smtp']['password'];
            $secure = (string)(self::$config['smtp']['secure'] ?? '');
            if ($secure === 'ssl') $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            if ($secure === 'tls') $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Timeout = (int)(self::$config['smtp']['timeout'] ?? 10);
            $mail->CharSet = 'UTF-8';

            $from = (string)self::$config['from'] ?: 'noreply@' . ((string)($_SERVER['HTTP_HOST'] ?? 'localhost'));
            $fromName = (string)self::$config['from_name'];
            $mail->setFrom($from, $fromName);
            $mail->addAddress((string)self::$config['to']);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $body;

            try {
                $mail->send();
            } catch (\Throwable $sendEx) {
                error_log('ErrorReporter email send failed: ' . $sendEx->getMessage());
            }
        } catch (\Throwable $ex) {
            error_log('ErrorReporter init mailer failed: ' . $ex->getMessage());
        }
    }

    private static function resolveUser(): string {
        $u = self::$config['user'] ?? null;
        if (is_callable($u)) {
            try {
                $u = call_user_func($u);
            } catch (\Throwable $e) {
                $u = null;
            }
        }
        if (!is_string($u) || $u === '') {
            // fallback común en tu framework
            $u = $_SESSION['usuariocod'] ?? $_SESSION['usuario'] ?? '';
        }
        return (string)$u;
    }

    private static function resolveRole(): string {
        $u = self::$config['rol'] ?? null;
        if (is_callable($u)) {
            try {
                $u = call_user_func($u);
            } catch (\Throwable $e) {
                $u = null;
            }
        }
        if (!is_string($u) || $u === '') {
            // fallback común en tu framework
            $u = $_SESSION['rolcod'] ?? $_SESSION['rol'] ?? '';
        }
        return (string)$u;
    }
}
