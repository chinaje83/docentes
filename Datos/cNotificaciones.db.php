<?php

abstract class cNotificacionesDB {
    use ManejoErrores;

    /**
     * @var accesoBDLocal
     */
    protected $conexion;
    /**
     * @var mixed
     */
    protected $formato;

    /**
     * cParentescosDB constructor.
     *
     * @param accesoBDLocal $conexion
     * @param               $formato
     */
    public function __construct(accesoBDLocal $conexion, $formato) {
        $this->conexion =& $conexion;
        $this->formato = $formato;
    }

    /**
     * Destructor de la clase
     */
    public function __destruct() {
        $this->error = [];
    }



    /**
     * @param          $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */




    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */

    protected function insertar(array $datos, ?int &$codigoInsertado): bool {

        $sp_nombre = 'ins_Alertas';
        $sp_param = [
            'pBase' => BASEDATOS_NOTIFICACIONES,
            'pIdTipo' => $datos['IdTipo'],
            'pIdEstado' => $datos['IdEstado'],
            'pIdPersona' => $datos['IdPersona'],
            'pIdUsuario' => $datos['IdUsuario'],
            'pMensaje' => $datos['Mensaje'],
            'pPrioridad' => $datos['Prioridad'],
            'pHilo' => $datos['Hilo'],
            'pNombre' => $datos['Nombre'],
            'pApellido' => $datos['Apellido'],
            'pEmail' => $datos['Email'],
            'pTelefono' => $datos['Telefono'],
            'pIdAlertaTemplate' => $datos['IdAlertaTemplate'],
            'pAltaApp' => $datos['AltaApp'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pFechaEnvio' => $datos['FechaEnvio'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario']
        ];

        try {
            $this->conexion->ejecutarStoredProcedure($sp_nombre, $sp_param, $resultado, $numfilas, $errno);
        } catch (Bigtree\ExcepcionDB $e) {
            $this->setError(400, 'Error al insertar.');
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();

        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */


}