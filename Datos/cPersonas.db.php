<?php

abstract class cPersonasDB {
    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    /**
     * Constructor de la clase cPersonasDB.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion, $formato) {

        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    /**
     * Destructor de la clase cPersonasDB.
     */
    function __destruct() {}

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public abstract function getError(): array;


    /**
     * Guarda un mensaje de error
     *
     * @param string|array $error
     * @param string       $error_description
     */
    protected function setError($error, $error_description = ''): void {
        $this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
    }

    protected function BuscarxCodigo($datos, &$resultado, &$numfilas) {
        $spnombre = 'sel_Personas_xIdPersona';
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pBase' => BASEDATOS,
            'pIdPersona' => $datos['IdPersona'],
            'pxEstado' => $datos['Estado'],
            'pEstado' => $datos['xEstado'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar persona por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function buscarxCuil(array $datos, &$resultado, ?int &$numfilas): bool {
        $sp_nombre = 'sel_Personas_xCuil';
        $sp_param = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pCuil' => $datos['Cuil'],
        ];

        try {
            $this->conexion->ejecutarStoredProcedure($sp_nombre, $sp_param, $resultado, $numfilas, $errno);
        } catch (Bigtree\ExcepcionDB $e) {
            $this->setError(400, 'Error al buscar por c�digo. ');
            return false;
        }

        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function buscarxDni(array $datos, &$resultado, ?int &$numfilas): bool {
        $sp_nombre = 'sel_Personas_xDni';
        $sp_param = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pDni' => $datos['Dni'],
        ];
        try {
            $this->conexion->ejecutarStoredProcedure($sp_nombre, $sp_param, $resultado, $numfilas, $errno);
        } catch (Bigtree\ExcepcionDB $e) {
            $this->setError(400, 'Error al buscar por c�digo. ');
            return false;
        }

        return true;
    }


    protected function buscarParaElastic(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_Personas_xIdPersona_es';
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pBase' => BASEDATOS,
            'pIdPersona' => $datos['IdPersona'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar para elastic. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function InsertarDB($datos, &$codigoInsertado) {
        $spnombre = 'ins_Personas';
        $sparam = [
            'pIdExterno' => $datos['IdExterno'],
            'pCUIL' => $datos['CUIL'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pDNI' => $datos['DNI'],
            'pSexo' => $datos['Sexo'],
            'pNombre' => $datos['Nombre'],
            'pApellido' => $datos['Apellido'],
            'pNombreCompleto' => $datos['NombreCompleto'],
            'pEmail' => $datos['Email'],
            'pTelefono' => $datos['Telefono'],
            'pFechaNacimiento' => $datos['FechaNacimiento'],
            'pFechaIngreso' => $datos['FechaIngreso'],
            'pFechaAntiguedadDocente' => $datos['FechaAntiguedadDocente'],
            'pFechaAntiguedadAdministrativo' => $datos['FechaAntiguedadAdministrativo'],
            'pTipoEstudio' => $datos['TipoEstudio'],
            'pFallecidoFecha' => $datos['FallecidoFecha'],
            'pIdEstadoPersona' => $datos['IdEstadoPersona'],
            'pCalle' => $datos['Calle'],
            'pNumeroPuerta' => $datos['NumeroPuerta'],
            'pPiso' => $datos['Piso'],
            'pDepto' => $datos['Depto'],
            'pCodigoPostal' => $datos['CodigoPostal'],
            'pIdProvincia' => $datos['IdProvincia'],
            'pIdDepartamento' => $datos['IdDepartamento'],
            'pIdLocalidad' => $datos['IdLocalidad'],
            'pIdRegion' => $datos['IdRegion'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar persona. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $codigoInsertado = (int)$this->conexion->UltimoCodigoInsertado();

        return true;
    }

    protected function ModificarDB($datos) {
        $spnombre = 'upd_Personas_xIdPersona';
        $sparam = [
            'pIdExterno' => $datos['IdExterno'],
            'pCUIL' => $datos['CUIL'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pDNI' => $datos['DNI'],
            'pSexo' => $datos['Sexo'],
            'pNombre' => $datos['Nombre'],
            'pApellido' => $datos['Apellido'],
            'pEmail' => $datos['Email'],
            'pNombreCompleto' => $datos['NombreCompleto'],
            'pTelefono' => $datos['Telefono'],
            'pFechaNacimiento' => $datos['FechaNacimiento'],
            'pFechaIngreso' => $datos['FechaIngreso'],
            'pFechaAntiguedadDocente' => $datos['FechaAntiguedadDocente'],
            'pFechaAntiguedadAdministrativo' => $datos['FechaAntiguedadAdministrativo'],
            'pTipoEstudio' => $datos['TipoEstudio'],
            'pFallecidoFecha' => $datos['FallecidoFecha'],
            'pIdEstadoPersona' => $datos['IdEstadoPersona'],
            'pCalle' => $datos['Calle'],
            'pNumeroPuerta' => $datos['NumeroPuerta'],
            'pPiso' => $datos['Piso'],
            'pDepto' => $datos['Depto'],
            'pCodigoPostal' => $datos['CodigoPostal'],
            'pIdProvincia' => $datos['IdProvincia'],
            'pIdDepartamento' => $datos['IdDepartamento'],
            'pIdLocalidad' => $datos['IdLocalidad'],
            'pIdRegion' => $datos['IdRegion'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pIdPersona' => $datos['IdPersona'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar persona. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function ModificarFotoPersona(array $datos): bool {
        $spnombre = "upd_Personas_Foto_xIdPersona";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pUbicacionAvatar' => $datos['UbicacionAvatar'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pIdPersona' => $datos['IdPersona'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar Foto Persona. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }


        return true;
    }

    protected function actualizarPersonaFamiliar(array $datos): bool {
        $spnombre = "upd_Personas_EsFamiliar";
        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
            'pEsFamiliar' => $datos['EsFamiliar'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar Foto Persona. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }


        return true;
    }

    protected function buscarAntiguedadParaElastic(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_PersonasAntiguedades_xIdPersona';
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdPersona' => $datos['IdPersona'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar para elastic. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

}
