<?php
require_once DIR_CLASES_DB . 'cNotificaciones.db.php';

class cNotificaciones extends cNotificacionesDB {
    /**
     * cArticulos constructor.
     *
     * @param accesoBDLocal $conexion
     * @param               $formato
     */
    public function __construct(accesoBDLocal $conexion, $formato = FMT_ARRAY) {

        parent::__construct($conexion, $formato);
    }

    /**
     * @inheritDoc
     */
    public function __destruct() {

        parent::__destruct();
    }

    /**
     * @param array    $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */




    public function insertar($datos, &$codigoInsertado): bool {

        if (!self::_validarInsertar($datos))
            return false;

        self::_setearNull($datos);
        self::_setearDatos($datos);

        if (!parent::insertar($datos, $codigoInsertado))
            return false;

        return true;
    }



    protected function _validarInsertar($datos): bool {

        if (!self::_validarDatosVacios($datos))
            return false;

        return true;
    }
    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function _validarDatosVacios($datos): bool {


        return true;
    }

    /**
     * @param array $datos
     *
     * @return void
     */
    protected static function _setearNull(&$datos): void {

        if (FuncionesPHPLocal::isEmpty($datos['IdPersona']))
            $datos['IdPersona'] = 'NULL';

        if (FuncionesPHPLocal::isEmpty($datos['IdUsuario']))
            $datos['IdUsuario'] = 'NULL';

        if (FuncionesPHPLocal::isEmpty($datos['Mensaje']))
            $datos['Mensaje'] = 'NULL';

        if (FuncionesPHPLocal::isEmpty($datos['Nombre']))
            $datos['Nombre'] = 'NULL';

        if (FuncionesPHPLocal::isEmpty($datos['Apellido']))
            $datos['Apellido'] = 'NULL';

        if (FuncionesPHPLocal::isEmpty($datos['Email']))
            $datos['Email'] = 'NULL';

        if (FuncionesPHPLocal::isEmpty($datos['Telefono']))
            $datos['Telefono'] = 'NULL';

        if (FuncionesPHPLocal::isEmpty($datos['IdAlertaTemplate']))
            $datos['IdAlertaTemplate'] = 'NULL';

        if (FuncionesPHPLocal::isEmpty($datos['FechaEnvio']))
            $datos['FechaEnvio'] = 'NULL';

        if (!isset($datos['AltaApp']) || $datos['AltaApp']=="")
            $datos['AltaApp']="NULL";


    }

    /**
     * @param array $datos
     *
     * @return void
     */
    protected static function _setearDatos(&$datos): void {
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

    }
}
