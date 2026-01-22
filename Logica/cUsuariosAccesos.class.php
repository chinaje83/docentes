<?php
require_once DIR_CLASES_DB . "cUsuariosAccesos.db.php";

class cUsuariosAccesos extends cUsuariosAccesosdb {
    protected $conexion;
    protected $formato;


    // Constructor de la clase
    function __construct($conexion, $formato = FMT_TEXTO) {
        $this->conexion = &$conexion;
        $this->formato = $formato;
        parent::__construct();
    }

    // Destructor de la clase
    function __destruct() {
        parent::__destruct();
    }



//-----------------------------------------------------------------------------------------
//                             PUBLICAS
//-----------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------
    public function BuscarMiUltimoAcceso(&$resultado, &$numfilas): bool {
        $datos['IdUsuario'] = $_SESSION['usuariocod'];
        if (!parent::BuscarUltimoAcceso($datos, $resultado, $numfilas)) {
            return false;
        }
        return true;
    }


    public function Insertar(): bool {
        $sistemaoperativo = $_SERVER["HTTP_USER_AGENT"];
        if (str_contains($sistemaoperativo, 'Win')) {
            $sistemaoperativo = 'Windows';
        } elseif (str_contains($sistemaoperativo, 'Mac')) {
            $sistemaoperativo = 'Mac OS';
        } elseif (str_contains($sistemaoperativo, 'Linux')) {
            $sistemaoperativo = 'Linux';
        } elseif (str_contains($sistemaoperativo, 'Unix')) {
            $sistemaoperativo = 'Unix';
        } else {
            $sistemaoperativo = 'Otro';
        }


        $ClaveEscuela = "NULL";
        if (isset($_SESSION['ClaveEscuela']) && $_SESSION['ClaveEscuela'] != "") {
            $ClaveEscuela = $_SESSION['ClaveEscuela'];
        }

        $IdEscalafon = "NULL";
        if (isset($_SESSION['IdEscalafon']) && $_SESSION['IdEscalafon'] != "") {
            $IdEscalafon = $_SESSION['IdEscalafon'];
        }

        $IdArea = "NULL";
        if (isset($_SESSION['IdArea']) && $_SESSION['IdArea'] != "") {
            $IdArea = is_array($_SESSION['IdArea']) ? current($_SESSION['IdArea']) : $_SESSION['IdArea'];
        }

        $NumeroDistrito = "NULL";
        if (isset($_SESSION['IdDistrito']) && $_SESSION['IdDistrito'] != "") {
            $NumeroDistrito = $_SESSION['IdDistrito'];
        }

        $TipoLogin = "NULL";
        if (isset($_SESSION['TipoLogin']) && $_SESSION['TipoLogin'] != "") {
            $TipoLogin = $_SESSION['TipoLogin'];
        }


        $Metapuestos = "NULL";
        if (isset($_SESSION['puestos']) && count($_SESSION['puestos']) > 0) {
            $Metapuestos = json_encode(FuncionesPHPLocal::ConvertiraUtf8($_SESSION['puestos']));
        }

        $datos = [
            'IdUsuario' => $_SESSION['usuariocod'],
            'Ip' => $_SERVER['REMOTE_ADDR'],
            'SistemaOperativo' => $sistemaoperativo,
            'Navegador' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_USER_AGENT'],
            'ClaveEscuela' => $ClaveEscuela,
            'IdEscalafon' => $IdEscalafon,
            'rolcod' => is_array($_SESSION['rolcod']) ? implode(",", $_SESSION['rolcod']) : ((string)$_SESSION['rolcod']),
            'IdArea' => $IdArea,
            'NumeroDistrito' => $NumeroDistrito,
            'Metapuestos' => $Metapuestos,
            'TipoLogin' => $TipoLogin,
            'FechaMovimiento' => date("Y-m-d H:i:s"),
        ];
        if (!parent::InsertarDB($datos)) {
            return false;
        }

        /*$datos['usuario'] = [
            'Id' => $datos['IdUsuario'],
            'Nombre' => utf8_decode($_SESSION['usuarioapellido'].', '.$_SESSION['usuarionombre']),
            'Documento' => $_SESSION['Dni'],
            'CUIL' => $_SESSION['Cuil'],
        ];

        $datos['aplicacion'] = [
            'Id' => 3,
            'Codigo' => 'DEAS',
            'Nombre' => 'Agentes',
        ];
        $oRoles = new cRoles($this->conexion, FMT_ARRAY);
        if (!$oRoles->buscarCombo($resultado, $numfilas)) {
            return true;
        }
        $roles = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            if (!in_array($fila['IdRol'], $_SESSION['roles_seleccion'])) {
                continue;
            }

            $roles[$fila['IdRol']] = ['Codigo' => $fila['IdRol'], 'Nombre' => $fila['Descripcion']];
        }
        $datos['roles'] = array_values($roles);
        $datos['rolActivo'] = [
            'Codigo' => $datos['rolcod'],
            'Nombre' => $_SESSION['NombreRol'],
        ];
        if ('NULL' !== $ClaveEscuela) {
            $datos['escuelaSeleccionada'] = [
                'Id' => $ClaveEscuela,
                'Codigo' => $_SESSION['NombreEscuela'],
                'Nombre' => $_SESSION['CUE'],
            ];
        }
        if ('NULL' !== $NumeroDistrito) {
            $datos['regionSeleccionada'] = [
                'Id' => $NumeroDistrito,
                'Codigo' => null,
                'Nombre' => null,
            ];
        }
        if ('NULL' !== $TipoLogin) {
            $datos['tipo'] = [
                'Id' => $TipoLogin,
                'Nombre' => cServiciosAccesos::TIPO[$TipoLogin],
            ];
        } else {
            $datos['tipo'] = [
                'Id' => 1,
                'Nombre' => cServiciosAccesos::TIPO[1],
            ];
        }
        $datosElastic = \Elastic\Accesos::armarDatosElastic($datos);
        try {
            $elastic = new \Bigtree\Elastic\Modificacion(\Elastic\Accesos::class, new \Bigtree\Elastic\Conexion(null, [CURLOPT_TIMEOUT => 300]));
        } catch (\Bigtree\Elastic\Excepciones\ExcepcionES $e) {
            return true;
        }
        if (!$elastic->insertar($datosElastic)) {
            FuncionesPHPLocal::MostrarMensaje(
                $this->conexion,
                MSG_ERRGRAVE,
                $elastic->getError('error_description'),
                ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__],
                ['formato' => $this->formato]
            );
            return false;
        }*/

        return true;
    }


}
