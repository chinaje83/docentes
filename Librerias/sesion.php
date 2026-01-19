<?php

include_once DIR_LIBS . 'autoload.php';

/* La clase requiere que esten definidos:

    Stored Básicos: sel_sessions_xsessionid
                    ins_sessions
                    upd_sessions_xsessionid
                    del_sessions_xsessionid
                    del_sessions_xmayortiempo
                    sel_roles_modulos_xrolcod_xarchivonom

    Registros de Acceso:
*/

class Sesion {

    private accesoBDLocal $conexionSesion;

    public function __construct($conexion, $inicializa = false) {
        $this->conexionSesion = &$conexion;
        switch (SESSION_TYPE) {
            case 1:
                session_set_save_handler(
                    [& $this, 'abrir_sesion'],
                    [& $this, 'cerrar_sesion'],
                    [& $this, 'leer_sesion'],
                    [& $this, 'escribir_sesion'],
                    [& $this, 'destruir_sesion'],
                    [& $this, 'gc_sesion']
                );
                break;
            case 2:
                break;
            default:
                die("Debe definir un tipo de acceso a sesiones - Variable SESSION_TYPE");
        }
        //session_set_cookie_params(['httponly' => true, 'secure' => true,]);
        session_set_cookie_params([
            'lifetime' => TIEMPOSESION, // 2 horas
            'path' => '/',
            'domain' => '', // Aplica al dominio actual
            'secure' => isset($_SERVER['HTTPS']), // Solo en HTTPS si es seguro
            'httponly' => true, // Evita acceso JavaScript a la cookie
            'samesite' => 'Strict' // Evita ataques CSRF
        ]);


        session_start();

        if ($inicializa || !isset($_SESSION['rolcod']) || !isset($_SESSION['usuariocod'])) {
            $_SESSION = [];
            $_SESSION['rolcod'] = 0;
            $_SESSION['usuariocod'] = 0;
            $_SESSION['usuarionombre'] = 0;
            $_SESSION['usuarioapellido'] = 0;
            $_SESSION['usuarioid'] = 0;
        }
    }

    public function abrir_sesion($aSavaPath, $aSessionName): bool {
        //$this->gc_sesion(TIEMPOSESION);
        return true;
    }

    public function cerrar_sesion(): bool {
        return true;
    }

    public function leer_sesion($aKey) {
        $spparam = ['pkey' => $aKey];
        if (!$this->conexionSesion->ejecutarStoredProcedure("sel_sessions_xsessionid", $spparam, $resultado, $numfilas, $errno)) {
            die("Error al acceder a sessions - " . $errno);
        }

        if ($numfilas == 1) {
            $r = $this->conexionSesion->ObtenerSiguienteRegistro($resultado);
            // OJO cambiar si se cambia el ejecutar stored_procedures
            return $r['DataValue'];
        } else {
            $spparam = ['pkey' => $aKey];
            if (!$this->conexionSesion->ejecutarStoredProcedure("ins_sessions", $spparam, $resultado, $numfilas, $errno)) {
                die("Error al acceder a sessions - " . $errno);
            }

            return "";
        }
    }

    public function escribir_sesion($aKey, $aVal) {
        $spparam = [
            'pkey' => $aKey,
            'pIdUsuario' => ($_SESSION['usuariocod'] ?? 'NULL') ?: 'NULL',
            'pdata' => $aVal
        ];
        if (!$this->conexionSesion->ejecutarStoredProcedure("upd_sessions_xsessionid", $spparam, $resultado, $numfilas, $errno)) {
            die("Error al acceder a sessions - " . $errno);
        }

        return true;
    }

    public function destruir_sesion($aKey) {
        $spparam = ['pkey' => $aKey];
        if (!$this->conexionSesion->ejecutarStoredProcedure("del_sessions_xsessionid", $spparam, $resultado, $numfilas, $errno)) {
            die("Error al acceder a sessions - " . $errno);
        }

        return true;
    }

    public function gc_sesion($aMaxLifeTime) {
        $spparam = ['ptiempolimite' => $aMaxLifeTime];
        if (!$this->conexionSesion->ejecutarStoredProcedure("del_sessions_xmayortiempo", $spparam, $resultado, $numfilas, $errno)) {
            die("Error al acceder a sessions - " . $errno);
        }

        return true;
    }

//-----------------------------------------------------------------------------------------


    /**
     * Verifica si tiene los permisos suficientes para acceder a un determinado archivo
     *
     * @param accesoBDLocal  $conexion
     * @param                $usuariocod
     * @param                $rolcod
     * @param                $archivonom
     * @param bool           $reload
     * @noinspection t
     */
    public function TienePermisos($conexion, $usuariocod, $rolcod, $archivonom, bool $reload = true): void {
        $_POST = FuncionesPHPLocal::RemoveMagicQuotes($_POST, true);
        $archivonom = substr(strrchr($archivonom, '/'), 1);
        if ($rolcod == 0 || $usuariocod == 0) {
            FuncionesPHPLocal::RegistrarAcceso($conexion, '010002', '', $usuariocod);
            session_destroy();
            $conexion->CerrarConexion();
            header('Location:/login', true, 302);
            exit;
        } elseif (!isset($_SESSION['sistema']) || $_SESSION['sistema'] != SISTEMA) {
            FuncionesPHPLocal::RegistrarAcceso($conexion, '010005', (isset($_SESSION['sistema']) ? $_SESSION['sistema'] : "") . " - " . $usuariocod, 0);
            $conexion->CerrarConexion();
            die ('Ud. no tiene los permisos suficientes para ingresar a la p&acute;gina solicitada.');
        } else {

            $spnombrerol = 'sel_roles_modulos_xrolcod_xarchivonom';
            $roles = implode(",", $rolcod);
            $spparamrol = ['pIdRol' => $roles, 'parchivonom' => $archivonom];
            if (!$conexion->ejecutarStoredProcedure($spnombrerol, $spparamrol, $resultado, $numfilas, $errno) || $numfilas < 1) {
                FuncionesPHPLocal::RegistrarAcceso($conexion, '010003', '', $usuariocod);
                die ('Ud. no tiene los permisos suficientes para ingresar a la p&acute;gina solicitada.');
            }
            //$datosarch = $this->conexionsesion->ObtenerSiguienteRegistro($resultado);
            //FuncionesPHPLocal::RegistrarAcceso($conexion,'010001','',$usuariocod);

            if (isset($_SESSION['expires_at']) && date_create() >= $_SESSION['expires_at']) {
                require_once DIR_LIBS . "autoload.php";
                require_once DIR_OAUTH . 'Login.php';
                $login = new \Bigtree\OAuth\Login;
                try {
                    $token_data = $login->renewAccessToken();
                } catch (\Bigtree\Excepciones\ExcepcionLogica $e) {
                    die ($e->getMessage());
                }
                $verificador = \Bigtree\JWT\Validator::createFromPublicKey(
                    SSO_PUBLIC_KEY,
                    ['iss', 'iat', 'nbf', 'exp', 'aud'],
                    ['jws_compact']
                );

                if (!$verificador->verificar($token_data['access_token'], $jws)) {
                    die($verificador->getError('error_description'));
                }

                $data = $jws->getPayload();
                if (!$verificador->verificarCampos($data, OPENID_CLIENT_ID, OPENID_ISS)) {
                    die($verificador->getError('error_description'));
                }

                $payload = json_decode($data, true);

                $_SESSION['subject'] = $payload['sub'];
                $_SESSION['token'] = $token_data['access_token'];
                $_SESSION['refresh_token'] = $token_data['refresh_token'];
                $_SESSION['jti'] = $payload['jti'];
                $_SESSION['expires_at'] = \Carbon\CarbonImmutable::createFromTimestamp($payload['exp'])->subSeconds(300)->toDateTimeImmutable();

            }

        }
    }

    /**
     * @param \accesoBDLocal $conexion
     *
     * @return bool
     */
    public static function bloquearAplicacion(accesoBDLocal $conexion): bool {
        $spnombre = 'del_sessions_xmayortiempo';
        $sparam = ['ptiempolimite' => -1];
        return $conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado_salida, $numfilas_salida, $errno_salida);
    }


    public function obtenerSessionPorSubject(string $subject, string $jti, &$datosSession): bool {
        $spparam = ['pSubject' => $subject];
        if (!$this->conexionSesion->ejecutarStoredProcedure('sel_sessions_xSubject', $spparam, $resultado,
            $numfilas, $errno)) {
            die('Error al acceder a sessions - ' . $errno);
        }
        while ($datosSession = $this->conexionSesion->ObtenerSiguienteRegistro($resultado)) {
            if (!session_decode($datosSession['DataValue'])) {
                return false;
            }
            if ($_SESSION['jti'] === $jti) {
                return true;
            }
            $_SESSION = [];
        }

        return false;
    }

}
