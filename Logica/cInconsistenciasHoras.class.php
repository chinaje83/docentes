<?php
require_once DIR_CLASES_DB . 'cInconsistenciasHoras.db.php';

use Bigtree\ExcepcionLogica;

/**
 * Class cInconsistenciasHoras
 */
class cInconsistenciasHoras extends cInconsistenciasHorasdb {
    use Validaciones;

    /** @var Elastic\Conexion */
    private $conexionES;
    /** @var array */
    private $arbolDecisiones;
    /** @var array */
    private $reglas = [];

    private const CERO = [
        'Materias' => [
            'Horas' => [
                'value' => 0]
            ,
            'Modulos' => [
                'value' => 0,
            ],
        ],
        'CargosJerarquicos' => [
            'Cardinality' => [
                'value' => 0,
            ],
        ],
        'CargosNoJerarquicos' => [
            'Cardinality' => [
                'value' => 0,
            ],
        ],
        'CargosAdministrativos' => [
            'Cardinality' => [
                'value' => 0,
            ],
        ],
        'CargosBase' => [
            'Cardinality' => [
                'value' => 0,
            ],
        ],
        'CargoSupervisor' => [
            'Cardinality' => [
                'value' => 0,
            ],
        ],

    ];
    private const PERSONA_NULA = ['IdPersona' => null, 'Nombre' => null, 'Apellido' => null];

    /**
     * Constructor de la clase cInconsistenciasHoras.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal         $conexion
     * @param mixed                 $formato
     * @param Elastic\Conexion|null $conexionES
     */
    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO, ?Elastic\Conexion $conexionES = null) {
        $conexionES = $conexionES ?? new Elastic\Conexion();
        parent::__construct($conexion, $formato);
        $this->conexionES =& $conexionES;
    }

    /**
     * Destructor de la clase cInconsistenciasHoras.
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xId' => 0,
            'Id' => '',
            'xEstado' => 0,
            'Estado' => '-1',
            'limit' => '',
            'orderby' => 'Id DESC',
        ];
        if (isset($datos['Id']) && $datos['Id'] != '') {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }
        if (isset($datos['Estado']) && $datos['Estado'] != '') {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != '')
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != '')
            $sparam['limit'] = $datos['limit'];
        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    /**
     * @param mysqli_result|null $resultado
     * @param int|null           $numfilas
     *
     * @return bool
     */
    public function buscarActivos(&$resultado, &$numfilas): bool {
        return parent::buscarActivos($resultado, $numfilas);
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    /**
     * @param array    $datos
     * @param int|null $codigoInsertado
     *
     * @return bool
     */
    public function Insertar($datos, &$codigoInsertado): bool {
        if (!$this->_ValidarInsertar($datos))
            return false;
        $this->_SetearNull($datos);
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = date('Y-m-d H:i:s');
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        $datos['Estado'] = ACTIVO;
        if (!parent::Insertar($datos, $codigoInsertado))
            return false;
        $datos['Accion'] = INSERTAR;
//		return $this->_insertarLog($datos);
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function Modificar($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;
        $datosRegistro['Accion'] = MODIFICACION;
//		return $this->_insertarLog($datosRegistro);
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function Eliminar($datos): bool {
        $datosModif['Accion'] = ELIMINAR;
        $datosModif['Id'] = $datos['Id'];
        $datosModif['Estado'] = ELIMINADO;
        return $this->ModificarEstado($datosModif);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function ModificarEstado($datos): bool {

        if (!$this->_validarExistencia($datos, $datosRegistro))
            return false;

        if (!parent::ModificarEstado($datos))
            return false;

        $datosRegistro['Accion'] = $datos['Accion'] ?? MODIFICACION;

//		return $this->_insertarLog($datosRegistro);
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function Activar(array $datos): bool {
        $datosModif['Id'] = $datos['Id'];
        $datosModif['Estado'] = ACTIVO;
        return $this->ModificarEstado($datosModif);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function DesActivar(array $datos): bool {
        $datosModif['Id'] = $datos['Id'];
        $datosModif['Estado'] = NOACTIVO;
        return $this->ModificarEstado($datosModif);
    }

    /**
     * @param array      $datos
     * @param array|null $registro
     * @param array|null $resumen
     * @param array|null $datosNuevo
     *
     * @return bool
     */
    public function validarPersona(array $datos, ?array &$registro, ?array &$resumen, ?array $datosNuevo = null): bool {

        # AGREGO / CAMBIO
        $resumen = [
            'tiene_conflictos' => false,
            'error_msg' => '',
        ];

        $oEscuelasPuestos = new cEscuelasPuestos($this->conexion);
        try {
            $puestosComputables = $oEscuelasPuestos->puestosComputables($datos);
        } catch (\Bigtree\ExcepcionDB $e) {
            registrar_error($e->getError());
            die();
        }

        // elimina valores vacíos dentro del array
        $puestosComputables = array_filter($puestosComputables);

// si después de filtrar quedó vacío, setear [-1]
        if (empty($puestosComputables)) {
            $puestosComputables = [-1];
        }

        $datosPersona = [];
        if (empty($registro)) {
            $oObjeto = new Elastic\Incompatibilidades($this->conexionES);
            if (!$oObjeto->validarModulosHorasAtendidos($datos, $resumenes, $_, $puestosComputables)) {
                $this->setError($oObjeto->getError());
                return false;
            }

            $registro = $resumenes[0]['Resumen'] ?? self::CERO;
            $datosPersona = $resumenes[0]['Persona']['hits']['hits'][0]['_source'] ?? self::PERSONA_NULA;
        }

        #
        if (!FuncionesPHPLocal::isEmpty($registro)) {

            $total = (int)$registro['Materias']['Horas']['value'];
            $total = MULTIPLICAR_HORAS ?
                $total + (int)($registro['Materias']['Modulos']['value'] * CANT_MODULOS_PUESTO / CANT_HORAS_PUESTO) :
                $total + (int)$registro['Materias']['Modulos']['value'];
            $datosRegistro = [
                'cargosJerarquicos' => (int)$registro['CargosJerarquicos']['Cardinality']['value'],
                'cargosNoJerarquicos' => (int)$registro['CargosNoJerarquicos']['Cardinality']['value'],
                'cargosAdministrativos' => null, // CHANGEME: estos tiene que ser reemplazados por los valores desagregados
                'cargosBase' => null, // CHANGEME: estos tiene que ser reemplazados por los valores desagregados
                'cargoSupervisor' => null, // CHANGEME: estos tiene que ser reemplazados por los valores desagregados
                'horasCatedra' => $total,
                'horasCatedraItinerantes' => null, // CHANGEME: estos tiene que ser reemplazados por los valores desagregados
            ];


//          var_dump($datosNuevo, $datosRegistro);
            if (!is_null($datosNuevo)) {

                array_walk($datosRegistro, function (&$v, $k) use ($datosNuevo) {

                    /*var_dump($datosNuevo[$k]);
                    var_dump($v);
                    var_dump(!empty($datosNuevo[$k]));
                    var_dump(!empty($v));*/
                    if (isset($datosNuevo[$k]) && !is_null($v)) {
                        $v += $datosNuevo[$k];
                    }
                });
            }
//	        var_dump($datosRegistro);
//          die;

            try {
                $tiene_conflictos = !$this->evaluarRegistro($datosRegistro, $error);
            } catch (Bigtree\ExcepcionLogica $e) {
                $this->setError($e->getError());
                return false;
            }


            $lnk = '';
            if (!empty($this->reglas)) {
                $dataReglas = json_encode($this->reglas);
                $lnk = "<br/><hr/><a href='javascript:void(0);' id='lnkReglasIncompatibilidadHoraria' data-reglas='$dataReglas'>Ver reglas de incompatibilidad</a>";
                if (SC_PERMITIR_SALTEAR_VALIDACION)
                    $lnk .= '<br/><hr/><button type="button" class="btn btn-primary" data-dismiss="modal" id="IgnorarInsertar" data-param="" data-asocdoc="true" data-modifdes="">&nbsp;Ignorar conflictos e insertar</button>';
            }
            //var_dump($reglas);die;
            $resumen = [
                'tiene_conflictos' => $tiene_conflictos,
                'error_msg' => cInconsistenciasHoras::analizarError($error, $datosPersona) . $lnk,
            ];
        }

        return true;
    }

    /**
     * @inheritDoc
     * @throws ExcepcionLogica
     */
    public function evaluarRegistro($resumen, &$error): bool {

        if (empty($this->arbolDecisiones)) {
            if (!$this->cargarArbol())
                throw new ExcepcionLogica($this->getError()['error_description']);
        }

        return self::recorrerArbol($resumen, 'cargosJerarquicos', $this->arbolDecisiones['cargosJerarquicos'], $path, $error);
    }

    /**
     * @param array|null $error
     * @param array      $datosPersona
     *
     * @return string
     */
    public static function analizarError(?array $error, array $datosPersona): string {
        $msg = '';
        if (!empty($error[2])) {
            if (empty($datosPersona['IdPersona']))
                $msg .= 'Considerando los cambios propuestos el agente tiene: <br/>';
            else
                $msg .= sprintf('Considerando los cambios propuestos el agente <em>%s, %s</em> tiene: <br/>', $datosPersona['Apellido'], $datosPersona['Nombre']);
            foreach ($error[2] as $nivel => $cantidad)
                $msg .= self::exportarPath($nivel, $cantidad);

            //$msg .= 'por lo tanto: ';
        }
        switch ($error[0] ?? '') {
            case 'cargosJerarquicos':
                $msg .= " - Más de {$error[1]} cargo jerárquico.<br/>";
                break;
            case 'cargosNoJerarquicos':
                $msg .= " - Más de {$error[1]} cargo/s base.<br/>";
                break;
            case 'cargosAdministrativos':
                $msg .= " - Más de {$error[1]} cargo/s en la administración pública.<br/>";
                break;
            case 'cargoSupervisor':
                $msg .= " - Más de {$error[1]} cargo/s como supervisor.<br/>";
                break;
            case 'horasCatedra':
                if ($error[1] == 0) {
                    $msg .= " - Al menos una hora.<br/>";
                } else {
                    $msg .= " - Más de {$error[1]} horas.<br/>";
                }
                break;
            case 'horasCatedraItinerantes':
                if ($error[1] == 0) {
                    $msg .= " - Al menos una hora como profesor itinerante.<br/>";
                } else {
                    $msg .= " - Más de {$error[1]} horas como profesor itinerante.<br/>";
                }
                break;
            case 'cargosBase':
                $msg .= " - Más de {$error[1]} cargos básicos.<br/>";
                break;
            default:
                return 'No se encontraron inconsistencias para el agente.';
        }
        return $msg . 'Se encontraron inconsistencias para el agente.';
    }

    //-----------------------------------------------------------------------------------------
    //FUNCIONES PRIVADAS
    //-----------------------------------------------------------------------------------------

    private static function exportarPath(string $nivel, int $cantidad): string {
        if ($cantidad < 1)
            return '';

        switch ($nivel) {
            case 'cargosJerarquicos':
                $msg = " - $cantidad Cargos jerárquicos<br/>";
                break;

            case 'cargosNoJerarquicos':
                $msg = " - $cantidad Cargos no jerárquicos<br/>";
                break;

            case 'cargosAdministrativos':
                $msg = " - $cantidad Cargos administrativos<br/>";
                break;

            case 'horasCatedra':
                $msg = " - $cantidad horas cátedra<br/>";
                break;

            case 'horasCatedraItinerantes':
                $msg = " - $cantidad horas cátedra itinerantes<br/>";
                break;

            case 'cargosBase':
                $msg = " - $cantidad Cargos docentes<br/>";
                break;

            default:
                $msg = '';
        }
        return $msg;
    }

    /**
     * @param $datos
     *
     * @return bool
     */
    protected function _ValidarInsertar($datos): bool {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        return true;
    }

    /**
     * @param $datos
     * @param $datosRegistro
     *
     * @return bool
     */
    protected function _ValidarModificar($datos, &$datosRegistro): bool {
        if (!$this->_validarExistencia($datos, $datosRegistro))
            return false;

        if (!$this->_ValidarDatosVacios($datos))
            return false;
        return true;
    }

    /**
     * @param $datos
     * @param $datosRegistro
     *
     * @return bool
     */
    protected function _ValidarEliminar($datos, &$datosRegistro): bool {
        if (!$this->_validarExistencia($datos, $datosRegistro))
            return false;

        return true;
    }

    /**
     * @param $datos
     */
    protected function _SetearNull(&$datos): void {


        if (!isset($datos['CargosJerarquicos']) || $datos['CargosJerarquicos'] == '')
            $datos['CargosJerarquicos'] = 'NULL';

        if (!isset($datos['CargosAdministrativos']) || $datos['CargosAdministrativos'] == '')
            $datos['CargosAdministrativos'] = 'NULL';

        if (!isset($datos['CargosBase']) || $datos['CargosBase'] == '')
            $datos['CargosBase'] = 'NULL';

        if (!isset($datos['CargoSupervisor']) || $datos['CargoSupervisor'] == '')
            $datos['CargoSupervisor'] = 'NULL';

        if (!isset($datos['HorasCatedra']) || $datos['HorasCatedra'] == '')
            $datos['HorasCatedra'] = 'NULL';

        if (!isset($datos['HorasCatedraItinerantes']) || $datos['HorasCatedraItinerantes'] == '')
            $datos['HorasCatedraItinerantes'] = 'NULL';

        if (!isset($datos['Observaciones']) || $datos['Observaciones'] == '')
            $datos['Observaciones'] = 'NULL';

        if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == '')
            $datos['UltimaModificacionFecha'] = 'NULL';

        if (!isset($datos['IdJornada']) || $datos['IdJornada'] == '')
            $datos['IdJornada'] = 'NULL';
    }

    /**
     * @param $datos
     *
     * @return bool
     */
    protected function _ValidarDatosVacios($datos): bool {

        if (!isset($datos['CargosJerarquicos']) || $datos['CargosJerarquicos'] == '') {
            $this->setError(400, 'Debe ingresar una cantidad de cargos jerárquicos');
            return false;
        }

        if (!isset($datos['CargosAdministrativos']) || $datos['CargosAdministrativos'] == '') {
            $this->setError(400, 'Debe ingresar una cantidad de  cargos administrativos');
            return false;
        }

        if (!isset($datos['CargosBase']) || $datos['CargosBase'] == '') {
            $this->setError(400, 'Debe ingresar una cantidad de  cargos base');
            return false;
        }

        if (!isset($datos['CargoSupervisor']) || $datos['CargoSupervisor'] == '') {
            $this->setError(400, 'Debe ingresar una cantidad de cargos supervisor');
            return false;
        }

        if (!isset($datos['HorasCatedra']) || $datos['HorasCatedra'] == '') {
            $this->setError(400, 'Debe ingresar una cantidad de horas cátedra');
            return false;
        }

        if (!isset($datos['HorasCatedraItinerantes']) || $datos['HorasCatedraItinerantes'] == '') {
            $this->setError(400, 'Debe ingresar una cantidad de horas cátedra (profesores itinerantes)');
            return false;
        }

        if (!isset($datos['ReglaVerbal']) || $datos['ReglaVerbal'] == '') {
            $this->setError(400, 'Debe ingresar una expresion verbal de la regla');
            return false;
        }

        /*if (!isset($datos['Observaciones']) || $datos['Observaciones']=='')
        {
            $this->setError(400,'Debe ingresar observaciones');
            return false;
        }*/

        return true;
    }

    /**
     * @param array $datosRegistro
     *
     * @return bool
     */
    protected function _insertarLog($datosRegistro): bool {
        $oAuditoriasInconsistenciasHoras = new cAuditoriasInconsistenciasHoras($this->conexion, FMT_ARRAY);
        if (!$oAuditoriasInconsistenciasHoras->InsertarLog($datosRegistro, $codigoInsertadolog)) {
            $this->setError($oAuditoriasInconsistenciasHoras->getError());
            return false;
        }

        return true;
    }

    /**
     * @param int|null $valor
     * @param array    $valores
     *
     * @return int|null
     */
    protected static function primerMayor($valor, $valores): ?int {
        $posibles = [];
        if (isset($valor)) {
            $posibles = array_filter($valores, function ($item) use ($valor) {
                return is_int($item) && $item >= $valor;
            });
        }
        return empty($posibles) ? null : min($posibles);
    }

    /**
     * @param array      $resumen
     * @param string     $nivel
     * @param array      $arbol
     * @param array|null $path
     * @param array|null $error
     *
     * @return bool
     */
    protected static function recorrerArbol($resumen, $nivel, $arbol, &$path, &$error): bool {
        if (empty($path))
            $path = [];

        if ($resumen[$nivel] > $arbol['max']) {
            $error = [$nivel, $arbol['max'], $path];
            return false;
        }

        $path[$nivel] = self::primerMayor($resumen[$nivel], array_keys($arbol));
        if (isset($path[$nivel])) {
            foreach ($arbol[$path[$nivel]] as $subNivel => $subArbol) {
                if (!isset($resumen[$nivel]))
                    continue;

                if (!self::recorrerArbol($resumen, $subNivel, $subArbol, $path, $error))
                    return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function cargarArbol(): bool {
        if (!$this->buscarActivos($resultado, $numfilas))
            return false;

        $arbol = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            if (!empty($fila['ReglaVerbal'])) {
                foreach (explode("\n", $fila['ReglaVerbal']) as $regla)
                    $this->reglas[] = utf8_encode(trim($regla));
            }
            $cargosNoJerarquicos = (int)($fila['CargosAdministrativos'] + $fila['CargosBase'] + $fila['CargoSupervisor']);

            $arbol['cargosJerarquicos']['max'] =
                max($arbol['cargosJerarquicos']['max'] ?? -1,
                    (int)$fila['CargosJerarquicos']);
            $arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosNoJerarquicos']['max'] =
                max($arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosNoJerarquicos']['max'] ?? -1,
                    $cargosNoJerarquicos);
            $arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosAdministrativos']['max'] =
                max($arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosAdministrativos']['max'] ?? -1,
                    (int)$fila['CargosAdministrativos']);
            $arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargoSupervisor']['max'] =
                max($arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargoSupervisor']['max'] ?? -1,
                    (int)$fila['CargoSupervisor']);
            $arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosNoJerarquicos'][$cargosNoJerarquicos]['horasCatedra']['max'] =
                max($arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosNoJerarquicos'][$cargosNoJerarquicos]['horasCatedra']['max'] ?? -1,
                    (int)$fila['HorasCatedra']);
            $arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosNoJerarquicos'][$cargosNoJerarquicos]['horasCatedraItinerantes']['max'] =
                max($arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosNoJerarquicos'][$cargosNoJerarquicos]['horasCatedraItinerantes']['max'] ?? -1,
                    (int)$fila['HorasCatedraItinerantes']);
            $arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosAdministrativos'][(int)$fila['CargosAdministrativos']]['cargosBase']['max'] =
                max($arbol['jerarquico'][(int)$fila['CargosJerarquicos']]['cargosAdministrativos'][(int)$fila['CargosAdministrativos']]['cargosBase']['max'] ?? -1,
                    (int)$fila['CargosBase']);
            $arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosAdministrativos'][(int)$fila['CargosAdministrativos']]['cargosBase'][(int)$fila['CargosBase']]['horasCatedra']['max'] =
                max($arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosAdministrativos'][(int)$fila['CargosAdministrativos']]['cargosBase'][(int)$fila['CargosBase']]['horasCatedra']['max'] ?? -1,
                    (int)$fila['HorasCatedra']);
            $arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosAdministrativos'][(int)$fila['CargosAdministrativos']]['cargosBase'][(int)$fila['CargosBase']]['horasCatedraItinerantes']['max'] =
                max($arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargosAdministrativos'][(int)$fila['CargosAdministrativos']]['cargosBase'][(int)$fila['CargosBase']]['horasCatedraItinerantes']['max'] ?? -1,
                    (int)$fila['HorasCatedraItinerantes']);
            $arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargoSupervisor'][(int)$fila['CargoSupervisor']]['cargosBase']['max'] =
                max($arbol['jerarquico'][(int)$fila['CargosJerarquicos']]['cargoSupervisor'][(int)$fila['CargoSupervisor']]['cargosBase']['max'] ?? -1,
                    (int)$fila['CargosBase']);
            $arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargoSupervisor'][(int)$fila['CargoSupervisor']]['cargosBase'][(int)$fila['CargosBase']]['horasCatedra']['max'] =
                max($arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargoSupervisor'][(int)$fila['CargoSupervisor']]['cargosBase'][(int)$fila['CargosBase']]['horasCatedra']['max'] ?? -1,
                    (int)$fila['HorasCatedra']);
            $arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargoSupervisor'][(int)$fila['CargoSupervisor']]['cargosBase'][(int)$fila['CargosBase']]['horasCatedraItinerantes']['max'] =
                max($arbol['cargosJerarquicos'][(int)$fila['CargosJerarquicos']]['cargoSupervisor'][(int)$fila['CargoSupervisor']]['cargosBase'][(int)$fila['CargosBase']]['horasCatedraItinerantes']['max'] ?? -1,
                    (int)$fila['HorasCatedraItinerantes']);
        }
        $this->arbolDecisiones = $arbol;
        return true;
    }

}
