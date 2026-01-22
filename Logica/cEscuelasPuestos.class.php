<?php

use Bigtree\ExcepcionLogica;

include(DIR_CLASES_DB . "cEscuelasPuestos.db.php");

class cEscuelasPuestos extends cEscuelasPuestosdb {
    use ManejoErrores;
    use Validaciones;

    /**
     * @var Elastic\Conexion
     */
    private $conexionES;
    /**
     * Constructor de la clase cEscuelasPuestos.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    /**
     * @inheritDoc
     */
    public function __construct(accesoBDLocal $conexion, ?Elastic\Conexion $conexionES = null, $formato = FMT_ARRAY) {
        $this->conexionES =& $conexionES;
        parent::__construct($conexion, $formato);
    }

    /**
     * Destructor de la clase cEscuelasPuestos.
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public function getError(): array {
        return $this->error;
    }

    static function CargarCUPOF($datos) {
        return str_pad($datos['IdPuesto'], "8", "0", STR_PAD_LEFT);
    }

    static function CargarNombreLiquidador() {
        if (defined("NOMBRELIQUIDADOR"))
            return NOMBRELIQUIDADOR;

        return "C&oacute;digo Liquidador";
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    public function buscarParaElastic(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarParaElastic($datos, $resultado, $numfilas);
    }

    public function buscarParaElasticxEscuela($datos, &$resultado, &$numfilas): bool {
        return parent::buscarParaElasticxEscuela($datos, $resultado, $numfilas);
    }

    public function buscarArbolDePuestos($datos, &$resultado, &$numfilas): bool {
        return parent::buscarArbolDePuestos($datos, $resultado, $numfilas);
    }

    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxCodigoVista($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigoVista($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarCargoxIdPuesto($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarCargoxIdPuesto($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaxIdPuestoRaiz($datos, &$resultado, &$numfilas): bool {
        if (!parent::BusquedaxIdPuestoRaiz($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function buscarExistentexEscuela($datos, &$resultado, &$numfilas): bool {

        return parent::buscarExistentexEscuela($datos, $resultado, $numfilas);
    }

    public function buscarxCodigoPuesto($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxCodigoPuesto($datos, $resultado, $numfilas);
    }

    public function buscarPuestosMad($datos, &$resultado, &$numfilas): bool {
        return parent::buscarPuestosMad($datos, $resultado, $numfilas);
    }

    public function puestosComputables($datos): array {
        return parent::puestosComputables($datos);
    }

    public function BuscarxIdSeccion($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'IdSeccion' => $datos['IdSeccion'],
            'xPuestoPadre' => 0,
        ];

        if (isset($datos['SoloPadre']) && !empty($datos['SoloPadre'])) {
            $sparam['xPuestoPadre'] = 1;
        }

        if (!parent::BuscarxIdSeccion($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxIdSeccionxIdPuesto($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxIdSeccionxIdPuesto($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarPuestosVacios($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarPuestosVacios($datos, $resultado, $numfilas);
    }

    public function BuscarGradoDivision($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarGradoDivision($datos, $resultado, $numfilas);
    }

    public function BuscarxPuestoOrigen($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarxPuestoOrigen($datos, $resultado, $numfilas);
    }


    //busqeuda recursiva PUESTOS

    public function BuscarHijos($datos, ?array &$hijos): bool {
        $hijos = $hijos ?? [];
        if (!parent::BuscarxIdPuestoPadre(['IdPuestoPadre' => $datos['IdPuesto']], $resultado, $numfilas)) {
            $this->setError(1, 1);
            return false;
        }
        if ($numfilas > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

                $hijos[] = $fila['IdPuesto'];
                if (!$this->BuscarHijos($fila, $hijos)) {
                    return false;
                }
            }
        }


        return true;
    }

    public function BusquedaAvanzadaxHijo($datos, &$resultado, &$numfilas): bool {
        return parent::BusquedaAvanzadaxHijo($datos, $resultado, $numfilas);
    }


    public function BusquedaAvanzadaxSeccion($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdEscuelaTurno' => 0,
            'IdEscuelaTurno' => "",
            'xIdCiclo' => 0,
            'IdCiclo' => "",
            'xIdOrientacion' => 0,
            'IdOrientacion' => "",
            'xIdGradoAnio' => 0,
            'IdGradoAnio' => "",
            'xIdSeccionBusqueda' => 0,
            'IdSeccionBusqueda' => "",
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "IdPuesto DESC",
        ];

        if (isset($datos['IdEscuelaTurno']) && $datos['IdEscuelaTurno'] != "") {
            $sparam['IdEscuelaTurno'] = $datos['IdEscuelaTurno'];
            $sparam['xIdEscuelaTurno'] = 1;
        }

        if (isset($datos['IdCiclo']) && $datos['IdCiclo'] != "") {
            $sparam['IdCiclo'] = $datos['IdCiclo'];
            $sparam['xIdCiclo'] = 1;
        }

        if (isset($datos['IdOrientacion']) && $datos['IdOrientacion'] != "") {
            $sparam['IdOrientacion'] = $datos['IdOrientacion'];
            $sparam['xIdOrientacion'] = 1;
        }

        if (isset($datos['IdGradoAnio']) && $datos['IdGradoAnio'] != "") {
            $sparam['IdGradoAnio'] = $datos['IdGradoAnio'];
            $sparam['xIdGradoAnio'] = 1;
        }

        if (isset($datos['IdSeccionBusqueda']) && $datos['IdSeccionBusqueda'] != "") {
            $sparam['IdSeccionBusqueda'] = $datos['IdSeccionBusqueda'];
            $sparam['xIdSeccionBusqueda'] = 1;
        }

        $datos['Estado'] = ACTIVO;
        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }


        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzadaxSeccion($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaAvanzadaCantidad($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdPuesto' => 0,
            'IdPuesto' => "",
            'xIdPuestoRaiz' => 0,
            'IdPuestoRaiz' => "",
            'xIdEscuelaTurno' => 0,
            'IdEscuelaTurno' => "",
            'xIdSeccion' => 0,
            'IdSeccion' => "",
            'xIdCargo' => 0,
            'IdCargo' => "",
            'xIdGrupo' => 0,
            'IdGrupo' => "",
            'xIdMateria' => 0,
            'IdMateria' => "",
            'xJerarquico' => 0,
            'Jerarquico' => "",
            'xIdTipoCargo' => 0,
            'IdTipoCargo' => "",
            'xCodigoPuesto' => 0,
            'CodigoPuesto' => "",
            'xTurno' => 0,
            'Turno' => "",
            'xGrado' => 0,
            'Grado' => "",
            'xIdPuestoMigracion' => 0,
            'IdPuestoMigracion' => "",
            'xCuil' => 0,
            'Cuil' => "",
            'xEstado' => 0,
            'Estado' => "-1",
            'xIdPofaMigracion' => 0,
            'IdPofaMigracion' => "",
            'xIdExcepcionTipo' => 0,
            'IdExcepcionTipo' => "",
            'xEstadoPofa' => 1,
            'EstadoPofa' => ACTIVO . "," . NOACTIVO,
            'xPuestoVacante' => 0,
            'xEsPuestoRaiz' => 0,
            'limit' => '',
            'orderby' => "CargoCodigo DESC",
        ];

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != "") {
            $sparam['IdPuesto'] = $datos['IdPuesto'];
            $sparam['xIdPuesto'] = 1;
        }
        if (isset($datos['IdPuestoRaiz']) && $datos['IdPuestoRaiz'] != "") {
            $sparam['IdPuestoRaiz'] = $datos['IdPuestoRaiz'];
            $sparam['xIdPuestoRaiz'] = 1;
        }
        if (isset($datos['IdEscuelaTurno']) && $datos['IdEscuelaTurno'] != "") {
            $sparam['IdEscuelaTurno'] = $datos['IdEscuelaTurno'];
            $sparam['xIdEscuelaTurno'] = 1;
        }
        if (isset($datos['IdSeccion']) && $datos['IdSeccion'] != "") {
            $sparam['IdSeccion'] = $datos['IdSeccion'];
            $sparam['xIdSeccion'] = 1;
        }
        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }
        if (isset($datos['IdGrupo']) && $datos['IdGrupo'] != "") {
            $sparam['IdGrupo'] = $datos['IdGrupo'];
            $sparam['xIdGrupo'] = 1;
        }
        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }
        if (isset($datos['Jerarquico']) && $datos['Jerarquico'] != "") {
            $sparam['Jerarquico'] = $datos['Jerarquico'];
            $sparam['xJerarquico'] = 1;
        }
        if (isset($datos['IdTipoCargo']) && $datos['IdTipoCargo'] != "") {
            $sparam['IdTipoCargo'] = $datos['IdTipoCargo'];
            $sparam['xIdTipoCargo'] = 1;
        }
        if (isset($datos['CodigoPuesto']) && $datos['CodigoPuesto'] != "") {
            $sparam['CodigoPuesto'] = $datos['CodigoPuesto'];
            $sparam['xCodigoPuesto'] = 1;
        }
        if (isset($datos['Turno']) && $datos['Turno'] != "") {
            $sparam['Turno'] = $datos['Turno'];
            $sparam['xTurno'] = 1;
        }
        if (isset($datos['Grado']) && $datos['Grado'] != "") {
            $sparam['Grado'] = $datos['Grado'];
            $sparam['xGrado'] = 1;
        }
        if (isset($datos['IdPuestoMigracion']) && $datos['IdPuestoMigracion'] != "") {
            $sparam['IdPuestoMigracion'] = $datos['IdPuestoMigracion'];
            $sparam['xIdPuestoMigracion'] = 1;
        }
        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }
        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }
        if (isset($datos['IdPofaMigracion']) && $datos['IdPofaMigracion'] != "") {
            $sparam['IdPofaMigracion'] = $datos['IdPofaMigracion'];
            $sparam['xIdPofaMigracion'] = 1;
        }
        if (isset($datos['IdExcepcionTipo']) && $datos['IdExcepcionTipo'] != "") {
            $sparam['IdExcepcionTipo'] = $datos['IdExcepcionTipo'];
            $sparam['xIdExcepcionTipo'] = 1;
        }
        if (isset($datos['EstadoPofa']) && $datos['EstadoPofa'] != "") {
            if ($datos['EstadoPofa'] == "0") {
                $sparam['EstadoPofa'] = "-1";
                $sparam['xEstadoPofa'] = 0;
                $sparam['xPuestoVacante'] = "1";
            } else {
                $sparam['EstadoPofa'] = $datos['EstadoPofa'];
                $sparam['xEstadoPofa'] = 1;
                $sparam['xPuestoVacante'] = 0;
            }
        }
        if (isset($datos['EsPuestoRaiz']) && $datos['EsPuestoRaiz'] == "1") {
            $sparam['xEsPuestoRaiz'] = $datos['EsPuestoRaiz'];
        }

        if (!parent::BusquedaAvanzadaCantidad($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdPuesto' => 0,
            'IdPuesto' => "",
            'xIdPuestoRaiz' => 0,
            'IdPuestoRaiz' => "",
            'xIdEscuelaTurno' => 0,
            'IdEscuelaTurno' => "",
            'xIdSeccion' => 0,
            'IdSeccion' => "",
            'xIdCargo' => 0,
            'IdCargo' => "",
            'xIdGrupo' => 0,
            'IdGrupo' => "",
            'xIdMateria' => 0,
            'IdMateria' => "",
            'xJerarquico' => 0,
            'Jerarquico' => "",
            'xIdTipoCargo' => 0,
            'IdTipoCargo' => "",
            'xCodigoPuesto' => 0,
            'CodigoPuesto' => "",
            'xTurno' => 0,
            'Turno' => "",
            'xGrado' => 0,
            'Grado' => "",
            'xIdPuestoMigracion' => 0,
            'IdPuestoMigracion' => "",
            'xCuil' => 0,
            'Cuil' => "",
            'xEstado' => 0,
            'Estado' => "-1",
            'xIdPofaMigracion' => 0,
            'IdPofaMigracion' => "",
            'xIdExcepcionTipo' => 0,
            'IdExcepcionTipo' => "",
            'xIdPofa' => 0,
            'IdPofa' => "",
            'xEstadoPofa' => 0,
            'EstadoPofa' => ACTIVO . "," . NOACTIVO,
            'xPuestoVacante' => 0,
            'xEsPuestoRaiz' => 0,
            'limit' => '',
            'orderby' => "IdEscuela DESC",
        ];


        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != "") {
            $sparam['IdPuesto'] = $datos['IdPuesto'];
            $sparam['xIdPuesto'] = 1;
        }
        if (isset($datos['IdPuestoRaiz']) && $datos['IdPuestoRaiz'] != "") {
            $sparam['IdPuestoRaiz'] = $datos['IdPuestoRaiz'];
            $sparam['xIdPuestoRaiz'] = 1;
        }
        if (isset($datos['IdEscuelaTurno']) && $datos['IdEscuelaTurno'] != "") {
            $sparam['IdEscuelaTurno'] = $datos['IdEscuelaTurno'];
            $sparam['xIdEscuelaTurno'] = 1;
        }
        if (isset($datos['IdSeccion']) && $datos['IdSeccion'] != "") {
            $sparam['IdSeccion'] = $datos['IdSeccion'];
            $sparam['xIdSeccion'] = 1;
        }
        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }
        if (isset($datos['IdGrupo']) && $datos['IdGrupo'] != "") {
            $sparam['IdGrupo'] = $datos['IdGrupo'];
            $sparam['xIdGrupo'] = 1;
        }
        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }
        if (isset($datos['Jerarquico']) && $datos['Jerarquico'] != "") {
            $sparam['Jerarquico'] = $datos['Jerarquico'];
            $sparam['xJerarquico'] = 1;
        }
        if (isset($datos['IdTipoCargo']) && $datos['IdTipoCargo'] != "") {
            $sparam['IdTipoCargo'] = $datos['IdTipoCargo'];
            $sparam['xIdTipoCargo'] = 1;
        }
        if (isset($datos['CodigoPuesto']) && $datos['CodigoPuesto'] != "") {
            $sparam['CodigoPuesto'] = $datos['CodigoPuesto'];
            $sparam['xCodigoPuesto'] = 1;
        }
        if (isset($datos['Turno']) && $datos['Turno'] != "") {
            $sparam['Turno'] = $datos['Turno'];
            $sparam['xTurno'] = 1;
        }
        if (isset($datos['Grado']) && $datos['Grado'] != "") {
            $sparam['Grado'] = $datos['Grado'];
            $sparam['xGrado'] = 1;
        }
        if (isset($datos['IdPuestoMigracion']) && $datos['IdPuestoMigracion'] != "") {
            $sparam['IdPuestoMigracion'] = $datos['IdPuestoMigracion'];
            $sparam['xIdPuestoMigracion'] = 1;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }
        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }
        if (isset($datos['EsPlazaTransitoria']) && $datos['EsPlazaTransitoria'] != "") {
            if($datos['EsPlazaTransitoria'] == 1) {
                $sparam['Estado'] = PLAZA_TRANSITORIA;
            } else {
                $sparam['Estado'] = ACTIVO;
            }

            $sparam['xEstado'] = 1;
        }

        if (isset($datos['IdPofaMigracion']) && $datos['IdPofaMigracion'] != "") {
            $sparam['IdPofaMigracion'] = $datos['IdPofaMigracion'];
            $sparam['xIdPofaMigracion'] = 1;
        }
        if (isset($datos['IdExcepcionTipo']) && $datos['IdExcepcionTipo'] != "") {
            $sparam['IdExcepcionTipo'] = $datos['IdExcepcionTipo'];
            $sparam['xIdExcepcionTipo'] = 1;
        }

        if (isset($datos['IdPofa']) && $datos['IdPofa'] != "") {
            $sparam['IdPofa'] = $datos['IdPofa'];
            $sparam['xIdPofa'] = 1;
        }

        if (isset($datos['EstadoPofa']) && $datos['EstadoPofa'] != "") {
            if ($datos['EstadoPofa'] == "0") {
                $sparam['EstadoPofa'] = "-1";
                $sparam['xEstadoPofa'] = 0;
                $sparam['xPuestoVacante'] = "1";
            } else {
                $sparam['EstadoPofa'] = $datos['EstadoPofa'];
                $sparam['xEstadoPofa'] = 1;
                $sparam['xPuestoVacante'] = 0;
            }
        }
        if (isset($datos['EsPuestoRaiz']) && $datos['EsPuestoRaiz'] == "1") {
            $sparam['xEsPuestoRaiz'] = $datos['EsPuestoRaiz'];
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BusquedaAvanzadaSinSeccion($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdPuesto' => 0,
            'IdPuesto' => "",
            'xIdEscuela' => 0,
            'IdEscuela' => "",
            'xIdNivelModalidad' => 0,
            'IdNivelModalidad' => "",
            'xIdEscuelaTurno' => 0,
            'IdEscuelaTurno' => "",
            'xIdCargo' => 0,
            'IdCargo' => "",
            'xIdGrupo' => 0,
            'IdGrupo' => "",
            'xIdMateria' => 0,
            'IdMateria' => "",
            'xCodigoPuesto' => 0,
            'CodigoPuesto' => "",
            'xJerarquico' => 0,
            'Jerarquico' => "",
            'xIdTipoCargo' => 0,
            'IdTipoCargo' => "",
            'xPuestoPadre' => 0,
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "IdPuesto DESC",
        ];

        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != "") {
            $sparam['IdPuesto'] = $datos['IdPuesto'];
            $sparam['xIdPuesto'] = 1;
        }
        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }
        if (isset($datos['IdNivelModalidad']) && $datos['IdNivelModalidad'] != "") {
            $sparam['IdNivelModalidad'] = $datos['IdNivelModalidad'];
            $sparam['xIdNivelModalidad'] = 1;
        }
        if (isset($datos['IdEscuelaTurno']) && $datos['IdEscuelaTurno'] != "") {
            $sparam['IdEscuelaTurno'] = $datos['IdEscuelaTurno'];
            $sparam['xIdEscuelaTurno'] = 1;
        }
        if (isset($datos['IdSeccion']) && $datos['IdSeccion'] != "") {
            $sparam['IdSeccion'] = $datos['IdSeccion'];
            $sparam['xIdSeccion'] = 1;
        }
        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }
        if (isset($datos['IdGrupo']) && $datos['IdGrupo'] != "") {
            $sparam['IdGrupo'] = $datos['IdGrupo'];
            $sparam['xIdGrupo'] = 1;
        }
        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }

        if (isset($datos['CodigoPuesto']) && $datos['CodigoPuesto'] != "") {
            $sparam['CodigoPuesto'] = trim($datos['CodigoPuesto']);
            $sparam['xCodigoPuesto'] = 1;
        }
        if (isset($datos['Jerarquico']) && $datos['Jerarquico'] != "") {
            $sparam['Jerarquico'] = $datos['Jerarquico'];
            $sparam['xJerarquico'] = 1;
        }
        if (isset($datos['IdTipoCargo']) && $datos['IdTipoCargo'] != "") {
            $sparam['IdTipoCargo'] = $datos['IdTipoCargo'];
            $sparam['xIdTipoCargo'] = 1;
        }

        if (isset($datos['SoloPadre']) && !empty($datos['SoloPadre'])) {
            $sparam['xPuestoPadre'] = 1;
        }

        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];
        if (!parent::BusquedaAvanzadaSinSeccion($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaAvanzadaPofa($datos, &$resultado, &$numfilas): bool {

        $sparam = [
            'xIdEscuela' => 0,
            'IdEscuela' => "",
            'xIdCicloLectivo' => 0,
            'IdCicloLectivo' => "",
            'xIdNivelModalidad' => 0,
            'IdNivelModalidad' => "",
            'xIdTurno' => 0,
            'IdTurno' => "",
            'xIdGradoAnio' => 0,
            'IdGradoAnio' => "",
            'xIdSeccion' => 0,
            'IdSeccion' => "",
            'limit' => '',
            'orderby' => "IdPuesto DESC",
        ];
        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }
        if (isset($datos['IdCicloLectivo']) && $datos['IdCicloLectivo'] != "") {
            $sparam['IdCicloLectivo'] = $datos['IdCicloLectivo'];
            $sparam['xIdCicloLectivo'] = 1;
        }
        if (isset($datos['IdNivelModalidad']) && $datos['IdNivelModalidad'] != "") {
            $sparam['IdNivelModalidad'] = $datos['IdNivelModalidad'];
            $sparam['xIdNivelModalidad'] = 1;
        }
        if (isset($datos['IdTurno']) && $datos['IdTurno'] != "") {
            $sparam['IdTurno'] = $datos['IdTurno'];
            $sparam['xIdTurno'] = 1;
        }
        if (isset($datos['IdGradoAnio']) && $datos['IdGradoAnio'] != "") {
            $sparam['IdGradoAnio'] = $datos['IdGradoAnio'];
            $sparam['xIdGradoAnio'] = 1;
        }
        if (isset($datos['IdSeccion']) && $datos['IdSeccion'] != "") {
            $sparam['IdSeccion'] = $datos['IdSeccion'];
            $sparam['xIdSeccion'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];
        if (!parent::BusquedaAvanzadaPofa($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarCargosPuestos($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarCargosPuestos($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarSecciones($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarSecciones($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    function BuscarEscuelasPuestos($datos, &$resultado, &$numfilas): bool {
        if (!$this->BusquedaAvanzada($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarEscuelaPOF($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarEscuelaPOF($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarDetallePuesto($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarDetallePuesto($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarPuestosxEscuela($datos, &$resultado, &$numfilas): bool {

        return parent::BuscarPuestosxEscuela($datos, $resultado, $numfilas);
    }

    public function BuscarDetallePuestosxIdPersona($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarDetallePuestosxIdPersona($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarDetallePuestosxIdEscuela($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarDetallePuestosxIdEscuela($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarDetallePuestoVacantexIdEscuela($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarDetallePuestoVacantexIdEscuela($datos, $resultado, $numfilas);
    }

    public function buscarNoVacantesxEscuela($datos, &$resultado, &$numfilas): bool {

        $query = "";
        $exists = false;
        /*
        if (!FuncionesPHPLocal::isEmpty($datos['filtroNivelTurno'])) {

            foreach ($datos['filtroNivelTurno'] as $key => $r) {

                $query .= $key == 0 ? 'AND' : 'OR';

                # si existe nivel y turno
                if ((isset($r['Nivel']) && $r['Nivel'] <> 0) && (isset($r['Turno']) && $r['Turno'] <> 0)) {
                    $exists = true;
                    $query .= ' (n.IdNivel = '.$r['Nivel'] .' AND t.IdTurno = '.$r['Turno'].') ';
                }

                # si existe nivel y no turno, o turno es "Todos"
                if ((isset($r['Nivel']) && $r['Nivel'] <> 0) && (!isset($r['Turno']) || ($r['Turno'] == 0))) {
                    $exists = true;
                    $query .= ' (n.IdNivel = '.$r['Nivel'] .') ';
                }
            }
        }*/

        $sparam = [
            'IdEscuela' => $datos['IdEscuela'],
            'xIdPersona' => 0,
            'IdPersona' => "",
            'xCodigoPuesto' => 0,
            'CodigoPuesto' => "",
            'xIdCargo' => 0,
            'IdCargo' => "",
            'xIdMateria' => 0,
            'IdMateria' => "",
            'xIdNivel' => 0,
            'IdNivel' => "",
            'xIdTipoCargo' => 0,
            'IdTipoCargo' => "",
            'xDNI' => 0,
            'DNI' => "",
        ];
        if (isset($datos['IdEscuelaAnexo']) && $datos['IdEscuelaAnexo'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuelaAnexo'];
        }
        if (isset($datos['IdPersona']) && $datos['IdPersona'] != "") {
            $sparam['IdPersona'] = $datos['IdPersona'];
            $sparam['xIdPersona'] = 1;
        }
        if (isset($datos['DNI']) && $datos['DNI'] != "") {
            $sparam['DNI'] = trim($datos['DNI']);
            $sparam['xDNI'] = 1;
        }

        if (isset($datos['CodigoPuesto']) && $datos['CodigoPuesto'] != "") {
            $sparam['CodigoPuesto'] = $datos['CodigoPuesto'];
            $sparam['xCodigoPuesto'] = 1;
        }

        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }

        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }

        if (isset($datos['IdTipoCargo']) && $datos['IdTipoCargo'] != "") {
            $sparam['IdTipoCargo'] = $datos['IdTipoCargo'];
            $sparam['xIdTipoCargo'] = 1;
        }
        //print_r($sparam);
        return parent::buscarNoVacantesxEscuela($sparam, $resultado, $numfilas);
    }

    public function buscarNoVacantesxIdPuesto($datos, &$resultado, &$numfilas): bool {
        return parent::buscarNoVacantesxIdPuesto($datos, $resultado, $numfilas);
    }

    public function obtenerSeccionesSinAtender($datos, &$resultado, &$numfilas): bool {
        return parent::obtenerSeccionesSinAtender($datos, $resultado, $numfilas);
    }


    public function buscarActivosxEscuela($datos, &$resultado, &$numfilas): bool {

        $query = "";

        $sparam = [
            'IdEscuela' => (is_array($datos['IdEscuela'])) ? implode(",", $datos['IdEscuela']) : $datos['IdEscuela'],
            #,'Query' => $exists ? $query : "",
        ];
        return parent::buscarActivosxEscuela($sparam, $resultado, $numfilas);
    }

    public function BuscarDesempenosFaltantesxEscuela($datos, &$resultado, &$numfilas): bool {

        $sparam = [
            'IdEscuela' => $datos['IdEscuela'],
            'PlazaInconsistente' => PI,
            'AdmiteSuplente' => '', # idem
            'xAdmiteSuplente' => 0,
            'EnDisponibilidad' => '', # idem
            'xEnDisponibilidad' => 0,
        ];

        if (isset($datos['AdmiteSuplente']) && $datos['AdmiteSuplente'] != "") {
            $sparam['AdmiteSuplente'] = 1;
            $sparam['xAdmiteSuplente'] = 1;
        }

        if (isset($datos['EnDisponibilidad']) && $datos['EnDisponibilidad'] != "") {
            $sparam['EnDisponibilidad'] = REU;
            $sparam['xEnDisponibilidad'] = 1;
        }

        if (!parent::BuscarDesempenosFaltantesxEscuela($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarPersonasFaltantesxEscuela($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarPersonasFaltantesxEscuela($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarConflictosEnPuestos($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarConflictosEnPuestos($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarNombrePuesto($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarNombrePuesto($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarNombreEscuelaPuesto($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarNombreEscuelaPuesto($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function buscarTotalxEscuelas(&$resultado, &$numfilas): bool {

        return parent::buscarTotalxEscuelas($resultado, $numfilas);
    }

    public function BusquedaRecursivaRaizNullxIdPuesto($datos, &$resultado, &$numfilas): bool {
        if (!parent::BusquedaRecursivaRaizNullxIdPuesto($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaSuplentesActivos($datos, &$resultado, &$numfilas): bool {
        if (!parent::BusquedaSuplentesActivos($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function ModificarAdmisionSuplente($datos): bool {

        if (!parent::ModificarAdmisionSuplente($datos))
            return false;

        if (!$this->_armarObjetoElastic($datos, $datosModif, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        $datosEnvio = (array)$datosElastic;
        if (!$oElastic->Actualizar($datosEnvio, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }

    public function buscarCargosxPersonaxEscuela($datos, &$resultado, &$numfilas): bool {

        $sparam = [
            'IdPersona' => $datos['IdPersona'],
            'xIdEscuela' => 0,
            'IdEscuela' => '',
            'xIdPuesto' => 0,
            'IdPuesto' => '',
            'xEstadosPuestoPersona' => 0,
            'EstadosPuestoPersona' => '',
            'xEstadosPuesto' => 0,
            'EstadosPuesto' => '',
        ];

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != '') {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != '') {
            $sparam['IdPuesto'] = $datos['IdPuesto'];
            $sparam['xIdPuesto'] = 1;
        }

        if (isset($datos['EstadosPuestoPersona']) && $datos['EstadosPuestoPersona'] != '') {
            $sparam['EstadosPuestoPersona'] = $datos['EstadosPuestoPersona'];
            $sparam['xEstadosPuestoPersona'] = 1;
        }
        if (isset($datos['EstadosPuesto']) && $datos['EstadosPuesto'] != '') {
            $sparam['EstadosPuesto'] = $datos['EstadosPuesto'];
            $sparam['xEstadosPuesto'] = 1;
        }

        return parent::buscarCargosxPersonaxEscuela($sparam, $resultado, $numfilas);
    }

    public function getCargosxIdPersona($IdPersona) {
        $arrayDevolver = [];
        $datos['IdPersona'] = $IdPersona;
        if (!$this->BuscarDetallePuestosxIdPersona($datos, $resultado, $numfilas))
            return [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            $arrayDevolver[] = $fila;


        $arrayDevolver = $this->array_orderby($arrayDevolver, 'IdPuesto', SORT_ASC);
        return $arrayDevolver;

    }

    public function getCargosxIdEscuela($IdEscuela) {
        $arrayDevolver = [];
        $datos['IdEscuela'] = $IdEscuela;
        if (!$this->BuscarDetallePuestosxIdEscuela($datos, $resultado, $numfilas))
            return [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            $arrayDevolver[] = $fila;


        $arrayDevolver = $this->array_orderby($arrayDevolver, 'IdPuesto', SORT_ASC);
        return $arrayDevolver;

    }

    public function getCargoVacantexIdEscuela($datos) {
        $arrayDevolver = [];
        if (!$this->BuscarDetallePuestoVacantexIdEscuela($datos, $resultado, $numfilas))
            return [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            $arrayDevolver[] = $fila;


        $arrayDevolver = $this->array_orderby($arrayDevolver, 'IdPuesto', SORT_ASC);
        return $arrayDevolver;

    }

    private function array_orderby() {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = [];
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }


    function InsertarDB(array $datos, ?int &$codigoInsertado): bool {

        if (!self::_ValidarInsertarPuesto($datos))
            return false;

        self::_SetearNull($datos);
        self::_SetearFechas($datos);

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $datos['IdPuesto'] = $codigoInsertado;
        if (!$this->ModificarPuestoRaiz($datos))
            return false;

        return true;
    }

    public function ArregloPadres($IdPuesto, &$arrcat, &$nivelarbol) {
        if ($IdPuesto != "") {
            $datoscat['IdPuesto'] = $IdPuesto;
            if (!$this->BuscarxCodigo($datoscat, $resultado, $numfilas))
                return false;
            $result = true;

            if ($numfilas == 0)
                $result = false;


            if ($result) {
                while ($filasub = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                    $padre = $filasub['IdPuestoPadre'];
                    $arrcat[] = $filasub;
                }
                $nivelarbol++;

                if ($padre != "")
                    if (!$this->ArregloPadres($padre, $arrcat, $nivelarbol))
                        return false;
                if (is_array($arrcat) && count($arrcat) > 0)
                    $darvueltaarreglo = asort($arrcat);
            }
        }
        return true;
    }

    public function ModificarPuestoRaiz(array $datos): bool {
        if (!$this->ArregloPadres($datos['IdPuesto'], $arrcat, $nivelarbol))
            return false;

        $PadreSuperior = current($arrcat);
        $datos['IdPuestoRaiz'] = $PadreSuperior['IdPuesto'];/*if($datos['IdPuestoRaiz']==$datos['IdPuesto'])
            $datos['IdPuestoRaiz']="NULL"*/;

        if (!isset($datos['UltimaModificacionUsuario']) || $datos['UltimaModificacionUsuario'] == "")
            $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

        if (!parent::ModificarPuestoRaiz($datos))
            return false;

        return true;
    }

    function Insertar(array $datos, ?int &$codigoInsertado): bool {

        if (!self::_ValidarInsertarPuesto($datos))
            return false;

        if (!empty($datos['IdPuestoPadre'])) {
            // si tiene IdPuestoPadre busco en EscuelasTurnos el id del turno para insertar

            if (!empty($datos['IdEscuelaTurno'])) {
                $oEscuelasTurnos = new cEscuelasTurnos($this->conexion, $this->formato);
                $datosTurno['IdEscuelaTurno'] = $datos['IdEscuelaTurno'];
                if (!$oEscuelasTurnos->BuscarxCodigo($datosTurno, $resultadoTurno, $nunfilasTurno))
                    return false;
                if ($nunfilasTurno != 1) {
                    $this->setError(400, "Error al buscar el turno.");
                    return false;
                }
                $filaTurno = $this->conexion->ObtenerSiguienteRegistro($resultadoTurno);

                $oEscuelasPuestosExtendida = new cEscuelasPuestosExtendida($this->conexion, $this->formato);
                $datosExtendida['IdCargoOrigen'] = $datos['IdCargo'];
                $datosExtendida['IdTurno'] = $filaTurno['IdTurno'];

                if (!$oEscuelasPuestosExtendida->BuscarxCodigo($datosExtendida, $resultadoExtendida, $nunfilasExtendida))
                    return false;

                if ($nunfilasExtendida == 1) {
                    // si existe regla extendida cambio el IdCargo por el IdCargoDestino
                    $filaExtendida = $this->conexion->ObtenerSiguienteRegistro($resultadoExtendida);
                    $datos['IdCargo'] = $filaExtendida['IdCargoDestino'];
                    $datos['CantHoras'] = $filaExtendida['HorasDestino'];
                }
            }

        }

        self::_SetearNull($datos);
        self::_SetearFechas($datos);

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $datos['IdPuesto'] = $codigoInsertado;
        if (!$this->ModificarPuestoRaiz($datos))
            return false;

        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datos['Accion'] = INSERTAR;
        $datos['IdPuesto'] = $codigoInsertado;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datos, $codigoInsertadolog))
            return false;

        if ($this->conexionES === null) {
            $this->conexionES = new Elastic\Conexion();
        }

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        $datosElastic = [];
        $datosBusqueda['IdPuesto'] = $codigoInsertado;
        if (!$this->_armarObjetoElastic($datosBusqueda, $datosElastic, $datosElastic))
            return false;

        if (!$oElastic->Insertar($datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }

    function InsertarMateria(array $datos, ?int &$codigoInsertado): bool {
        if (!$this->_ValidarInsertarMaterias($datos))
            return false;

        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;
        $datos['CodigoPuesto'] = 'NULL';

        if (!parent::InsertarMateria($datos, $codigoInsertado))
            return false;
        $datos['IdPuesto'] = $codigoInsertado;

        # Codigo Puesto siempre automático para el caso (inserta materias al crear una nueva división)
        if (!parent::BuscarPuestosxSeccionxEscuelaTurnoxNivelModalidad($datos, $resultadoPuestosInsertados, $numfilasPuestosInsertados))
            return false;

        if ($numfilasPuestosInsertados > 0) {

            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoPuestosInsertados)) {
                $datosModificar['IdPuesto'] = $fila['IdPuesto'];
                $datosModificar['CodigoPuesto'] = self::CargarCUPOF($fila);
                if (!self::ModificarCodigoPuesto($datosModificar))
                    return false;
            }
        }


        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        if (!isset($datos['CargaManual']))
            $datos['CargaManual'] = 0;
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datos, $codigoInsertadolog))
            return false;

        if (!$this->BuscarxIdSeccion($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                $datosElastic = [];
                $datosBusqueda['IdPuesto'] = $fila['IdPuesto'];
                if (!$this->_armarObjetoElastic($datosBusqueda, $datosElastic, $datosElastic))
                    return false;

                if (!$oElastic->Insertar($datosElastic)) {
                    $this->setError($oElastic->getError());
                    return false;
                }
            }
        }
        return true;
    }

    public function InsertarCargoSinAula($datos, &$codigoInsertado): bool {

        if (!$this->_ValidarDatosVaciosCargoSinSeccion($datos))
            return false;

        if (!$this->_ValidarDatosVaciosHorasModulos($datos))
            return false;

        if (!$this->_ValidarDatosVaciosFuncionCargo($datos))
            return false;

        if (!isset($datos['IdCargo']) || $datos['IdCargo'] == '') {
            $this->setError(400, 'Error, debe ingresar un cargo.');
            return false;
        }

        if (CARGACUPOF_MANUAL) {
            if (!isset($datos['CodigoPuesto']) || $datos['CodigoPuesto'] == "") {
                $this->setError(400, "Error, debe ingresar un código de puesto.");
                return false;
            }
        } else {

            // despues de las validaciones hechas recien puedo generar el cupof con los datos enviados en formulario
            try {
                $datos["CodigoPuesto"] = $this->GenerarCupof($datos);
            } catch (\Bigtree\ErrorDeGeneracionCupof $e) {
                $this->setError(400, "Error en la generacion del cupof automatica.");
                return false;
            }
        }


        /*if ($datos['IdCargo'] == "" && $datos['IdMateria'] == "") {
            $this->setError(400, "Error, debe ingresar un cargo o materia.");
            return false;
        }*/

        if (!isset($datos['IdCargo']) || $datos['IdCargo'] == '') {
            $this->setError(400, 'Error, debe ingresar un cargo.');
            return false;
        }

        if (!isset($datos['IdFuenteFinanciamiento']) || $datos['IdFuenteFinanciamiento'] == "") {
            $this->setError(400, "Error, debe ingresar una fuente de financiamiento.");
            return false;
        }

        $datos['Estado'] = (!FuncionesPHPLocal::isEmpty($datos['PlazaTransitoria']) ? $datos['PlazaTransitoria'] : ACTIVO);
        if (!$this->BuscarEscuelasPuestos($datos, $resultado, $numfilas))
            return false;

        $datos['CantModulos']= $datos['CantHoras']=0;
        if ($datos['IdTipo'] == 2)
            $datos['CantModulos'] = $datos['CantHorasModulos'];
        else
            $datos['CantHoras'] = $datos['CantHorasModulos'];

       /* if ($datos["IdTipo"] != 3) { // solo se completa escalafon cuando tipo de cargo es "cargo"
            $datos['IdEscalafon'] = 'NULL';
        }*/

        //$datos['IdSeccion'] = "";
        $this->_SetearNull($datos);

        if (!isset($datos['CargaManual']))
            $datos['CargaManual'] = 1;

        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $datos['IdPuesto'] = $codigoInsertado;
/*
        if (!CARGACUPOF_MANUAL) {
            $datos['CodigoPuesto'] = self::CargarCUPOF($datos);
            if (!self::ModificarCodigoPuesto($datos))
                return false;
        }*/

        if (!$this->ModificarPuestoRaiz($datos))
            return false;


        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datos, $codigoInsertadolog))
            return false;

        if (!$this->_armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Insertar($datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }


    public function InsertarCargoSeccionCompleto($datos, &$codigoInsertado): bool {


        if (!$this->_ValidarInsertar($datos))
            return false;

        /*if ($datos['IdCargo'] == "" && $datos['IdMateria'] == "") {
           $this->setError(400, "Error, debe ingresar un cargo o materia.");
           return false;
       }*/

        if (!isset($datos['IdCargo']) || $datos['IdCargo'] == '') {
            $this->setError(400, 'Error, debe ingresar un cargo.');
            return false;
        }

        if (CARGACUPOF_MANUAL) {
            if (!isset($datos['CodigoPuesto']) || $datos['CodigoPuesto'] == "") {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, ("Error, debe ingresar un código de puesto."), ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }
        } else {
            $datos['CodigoPuesto'] = 'NULL';
        }
        /*$datosBuscar['Estado'] = 10;
        $datosBuscar['IdSeccion'] = $datos['IdSeccion'];
        $datosBuscar['IdCargo'] = $datos['IdCargo'];
        $datosBuscar['IdMateria'] = $datos['IdMateria'];
        if (!$this->BuscarEscuelasPuestos($datosBuscar, $resultado, $numfilas))
            return false;*/

        if (!isset($datos['IdFuenteFinanciamiento']) || $datos['IdFuenteFinanciamiento'] == "") {
            $this->setError(400, "Error, debe ingresar una fuente de financiamiento.");
            return false;
        }

        if (!$this->_ValidarDatosVaciosHorasModulos($datos))
            return false;

        /*if ($datos["IdTipo"] != 3) { // solo se completa escalafon cuando tipo de cargo es "cargo"
            $datos['IdEscalafon'] = 'NULL';
        }*/

        $this->_SetearNull($datos);

        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;


        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $datos['IdPuesto'] = $codigoInsertado;

        if (!$this->ModificarPuestoRaiz($datos))
            return false;

        if (!CARGACUPOF_MANUAL) {

            if(!empty($datos['FechaDesde']))
                $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'aaaa-mm-dd', 'dd/mm/aaaa');

            $datos['CodigoPuesto'] = $this->GenerarCupof($datos);

            if (!self::ModificarCodigoPuesto($datos))
                return false;
        }

        $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');

        if ($datos['IdTipo'] ==2) {
            if (!parent::ModificarModulos($datos))
                return false;
        } else {
            if (!parent::ModificarHoras($datos))
                return false;
        }


        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datos['IdPuesto'] = $codigoInsertado;
        if (!isset($datos['CargaManual']))
            $datos['CargaManual'] = 0;
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datos, $codigoInsertadolog))
            return false;

        if (!$this->_armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Insertar($datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }

    public function ModificarCargoSeccionCompleto($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        /*if ($datos['IdCargo'] == "" && $datos['IdMateria'] == "") {
                   $this->setError(400, "Error, debe ingresar un cargo o materia.");
                   return false;
               }*/

        if (!isset($datos['IdCargo']) || $datos['IdCargo'] == '') {
            $this->setError(400, 'Error, debe ingresar un cargo.');
            return false;
        }

        if (CARGACUPOF_MANUAL) {
            if (!isset($datos['CodigoPuesto']) || $datos['CodigoPuesto'] == "") {
                $this->setError(400, "Error, debe ingresar un código de puesto.");
                return false;

            }
        } else {
            $datos['CodigoPuesto'] = 'NULL';
        }

        $datosBuscar['Estado'] = 10;
        $datosBuscar['IdSeccion'] = $datos['IdSeccion'];
        $datosBuscar['IdCargo'] = $datos['IdCargo'];
        $datosBuscar['IdMateria'] = $datos['IdMateria'];
        if (!$this->BuscarEscuelasPuestos($datosBuscar, $resultado, $numfilas))
            return false;

        if (!$this->_ValidarDatosVaciosHorasModulos($datos))
            return false;

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $this->_SetearNull($datos);

        if (!parent::ModificarSeccionCargoMateriaxIdPuesto($datos))
            return false;

        if (!FuncionesPHPLocal::isEmpty($datos['NroResolucion'])) {

            if (!self::ModificarNumeroResolucion($datos))
                return false;
        }

        if (!CARGACUPOF_MANUAL) {
            try {
                $datos['CodigoPuesto'] = $this->GenerarCupof($datos);
            } catch (\Bigtree\ErrorDeGeneracionCupof $e) {
                $this->setError(400, "Error en la generacion del cupof automatica.");
                return false;
            }
            if (!self::ModificarCodigoPuesto($datos))
                return false;
        }

        /*if (isset($datos['IdTipo']) && $datos['IdTipo'] == "1") {
            if (!parent::ModificarHoras($datos))
                return false;
        }*/

        if (isset($datos['IdTipo']) && $datos['IdTipo'] == "2") {
            if (!parent::ModificarModulos($datos))
                return false;
        }
        else
        {
            if (!parent::ModificarHoras($datos))
                return false;
        }


        $datosModif = [];
        if (!$this->_armarObjetoElastic($datos, $datosModif, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        $datosEnvio = (array)$datosElastic;
        if (!$oElastic->Actualizar($datosEnvio, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }


        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function ModificarCargoSeccion($datos): bool {

        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        /*     if (isset($datos['IdCargo']) && $datos['IdCargo'] != "" && isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
                 $this->setError(400, "Error, debe ingresar un cargo o materia.");
                 return false;

             } */

        if (!isset($datos['IdCargo']) || $datos['IdCargo'] == '') {
            $this->setError(400, 'Error, debe ingresar un cargo.');
            return false;
        }

        $datos['Estado'] = 10;
        if (!$this->BuscarEscuelasPuestos($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {

            if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
                $this->setError(400, "Actualmente ya existe la materia.");
            }

            return false;
        }

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $this->_SetearNull($datos);
        if (!parent::ModificarSeccionCargoMateriaxIdPuesto($datos))
            return false;
        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function ModificarCargoSinAula($datos): bool {

        if (!$this->_validarExistencia($datos, $datosRegistro))
            return false;

        if (!$this->_ValidarDatosVaciosCargoSinSeccion($datos))
            return false;

        if (!$this->_ValidarDatosVaciosHorasModulos($datos))
            return false;

        if (!$this->_ValidarModificarIdPuestoMigracion($datos, $datosRegistro))
            return false;

        if (!$this->_ValidarDatosVaciosFuncionCargo($datos))
            return false;

        /*
        if (FuncionesPHPLocal::isEmpty($datos['IdSeccion']) && !FuncionesPHPLocal::isEmpty($datosRegistro['IdSeccion'])) {
            $datos['IdSeccion'] = $datosRegistro['IdSeccion'];
        }
        */
/*
        if ((!isset($datos['CodigoPuesto']) || $datos['CodigoPuesto'] == "") && CARGACUPOF_MANUAL) {
            $this->setError(400, "Debe ingresar un código de puesto");
            return false;
        }
*/
        /*if ($datos['IdCargo'] == "" && $datos['IdMateria'] == "") {
               $this->setError(400, "Error, debe ingresar un cargo o materia.");
               return false;
           }*/

        if (!isset($datos['IdCargo']) || $datos['IdCargo'] == '') {
            $this->setError(400, 'Error, debe ingresar un cargo.');
            return false;
        }

        if (CARGACUPOF_MANUAL) {
            if (!isset($datos['CodigoPuesto']) || $datos['CodigoPuesto'] == "") {
                $this->setError(400, "Error, debe ingresar un código de puesto.");
                return false;
            }
        } else {

            // despues de las validaciones hechas recien puedo generar el cupof con los datos enviados en formulario
            try {
                $datos["CodigoPuesto"] = $this->GenerarCupof($datos);
            } catch (\Bigtree\ErrorDeGeneracionCupof) {
                $this->setError(400, "Error en generacion de cupof automatica.");
                return false;
            }
        }

        if (!isset($datos['IdCargo']) || $datos['IdCargo'] == '') {
            $this->setError(400, 'Error, debe ingresar un cargo.');
            return false;
        }

        if (!isset($datos['IdFuenteFinanciamiento']) || $datos['IdFuenteFinanciamiento'] == "") {
            $this->setError(400, "Error, debe ingresar una fuente de financiamiento.");
            return false;
        }

        if(defined('AM_CARGO_GRUPO_SUBGRUPO') && AM_CARGO_GRUPO_SUBGRUPO) {
            if(!FuncionesPHPLocal::isEmpty($datos['IdSubGrupo']) && FuncionesPHPLocal::isEmpty($datos['IdGrupo'])){
                $this->setError(400, "Error debe ingresar un grupo ocupacional si selecciona un subgrupo.");
                return false;
            }
        } else {
            $datos['IdGrupo'] = "";
            $datos['IdSubGrupo'] = "";
        }

        $datos['Estado'] = (!FuncionesPHPLocal::isEmpty($datos['PlazaTransitoria']) ? $datos['PlazaTransitoria'] : ACTIVO);
        if (!$this->BuscarEscuelasPuestos($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {

            $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);
            if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
                if ($fila['IdPuesto'] != $datos['IdPuesto']) {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Actualmente ya existe el cargo.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                    return false;
                }
            }

            if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
                if ($fila['IdPuesto'] != $datos['IdPuesto']) {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Actualmente ya existe la materia.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                    return false;
                }
            }
        }

        $datos['CantModulos'] = $datos['CantHoras']=0;
        if ($datos['IdTipo'] == 2)
            $datos['CantModulos'] = $datos['CantHorasModulos'];
        else
            $datos['CantHoras'] = $datos['CantHorasModulos'];

       /* if ($datos["IdTipo"] != 3) { // solo se completa escalafon cuando tipo de cargo es "cargo"
            $datos['IdEscalafon'] = 'NULL';
        }*/

        //$datos["IdTipoCargo"] = $datos["IdTipo"];

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

        if (FuncionesPHPLocal::isEmpty($datos['CodigoPuesto']))
            $datos['CodigoPuesto'] = $datosRegistro['CodigoPuesto'];

        $this->_SetearNull($datos);

        if (!$this->buscarArbolDePuestos($datos, $resultadoArbol, $numfilasArbol))
            return false;

        if ($numfilasArbol <= 0) {
            $this->setError(400, 'No se encontraron puestos existentes.');
            return false;
        }

        $idPuestos = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoArbol)) {
            $idPuestos[] = $fila['IdPuesto'];
        }


        $datosModificar = $datos;
        $datosModificar['IdPuesto'] = $idPuestos;

        if (!parent::Modificar($datosModificar))
            return false;


        foreach ($idPuestos as $puesto) {

            $datos['IdPuesto'] = $puesto;
            $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
            $datosRegistro['Accion'] = MODIFICACION;
            if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
                return false;
        }

        $oMad = new \Bigtree\Logica\cMad($this->conexion, $this->conexionES);
        if (!$oMad->modificarElastic($datos)) {
            $this->setError($oMad->getError());
            return false;
        }

        return true;
    }

    public function ObtenerDatosPlazasXRangoFecha($datos, &$resultado, &$numfilas) {

        $sparam = [
            'xIdPuesto' => 0,
            'IdPuesto' => "",
            'xIdPuestoMigracion' => 0,
            'IdPuestoMigracion' => "",
            'xCodigoPuesto' => 0,
            'CodigoPuesto' => "",
            'xUltimaModificacionFecha' => 0,
            'UltimaModificacionFecha' => "",
            'xAltaFecha' => 0,
            'AltaFecha' => "",
            'xFechaFinPuesto' => 0,
            'FechaFinPuesto' => "",
            'limit' => '',
            'orderby' => "FechaAlta ASC",
        ];


        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != "") {
            $sparam['IdPuesto'] = $datos['IdPuesto'];
            $sparam['xIdPuesto'] = 1;
        }

        if (isset($datos['IdPuestoMigracion']) && $datos['IdPuestoMigracion'] != "") {
            $sparam['IdPuestoMigracion'] = $datos['IdPuestoMigracion'];
            $sparam['xIdPuestoMigracion'] = 1;
        }

        if (isset($datos['CodigoPuesto']) && $datos['CodigoPuesto'] != "") {
            $sparam['CodigoPuesto'] = $datos['CodigoPuesto'];
            $sparam['xCodigoPuesto'] = 1;
        }

        if (isset($datos['UltimaModificacionFecha']) && $datos['UltimaModificacionFecha'] != "") {
            $sparam['UltimaModificacionFecha'] = FuncionesPHPLocal::ConvertirFecha($datos['UltimaModificacionFecha'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xUltimaModificacionFecha'] = 1;
        }

        if (isset($datos['AltaFecha']) && $datos['AltaFecha'] != "") {
            $sparam['AltaFecha'] = FuncionesPHPLocal::ConvertirFecha($datos['AltaFecha'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xAltaFecha'] = 1;
        }

        if (isset($datos['FechaFinPuesto']) && $datos['FechaFinPuesto'] != "") {
            $sparam['FechaFinPuesto'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaFinPuesto'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xFechaFinPuesto'] = 1;
        }


        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::ObtenerDatosPlazasXRangoFecha($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    public function ObtenerDatosPlazasXRangoFecha_Cantidad($datos, &$resultado, &$numfilas) {

        $sparam = [
            'xIdPuesto' => 0,
            'IdPuesto' => "",
            'xIdPuestoMigracion' => 0,
            'IdPuestoMigracion' => "",
            'xCodigoPuesto' => 0,
            'CodigoPuesto' => "",
            'xUltimaModificacionFecha' => 0,
            'UltimaModificacionFecha' => "",
            'xAltaFecha' => 0,
            'AltaFecha' => "",
            'xFechaFinPuesto' => 0,
            'FechaFinPuesto' => "",
            'limit' => '',
            'orderby' => "FechaAlta ASC",
        ];


        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != "") {
            $sparam['IdPuesto'] = $datos['IdPuesto'];
            $sparam['xIdPuesto'] = 1;
        }

        if (isset($datos['IdPuestoMigracion']) && $datos['IdPuestoMigracion'] != "") {
            $sparam['IdPuestoMigracion'] = $datos['IdPuestoMigracion'];
            $sparam['xIdPuestoMigracion'] = 1;
        }

        if (isset($datos['CodigoPuesto']) && $datos['CodigoPuesto'] != "") {
            $sparam['CodigoPuesto'] = $datos['CodigoPuesto'];
            $sparam['xCodigoPuesto'] = 1;
        }

        if (isset($datos['UltimaModificacionFecha']) && $datos['UltimaModificacionFecha'] != "") {
            $sparam['UltimaModificacionFecha'] = FuncionesPHPLocal::ConvertirFecha($datos['UltimaModificacionFecha'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xUltimaModificacionFecha'] = 1;
        }

        if (isset($datos['AltaFecha']) && $datos['AltaFecha'] != "") {
            $sparam['AltaFecha'] = FuncionesPHPLocal::ConvertirFecha($datos['AltaFecha'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xAltaFecha'] = 1;
        }

        if (isset($datos['FechaFinPuesto']) && $datos['FechaFinPuesto'] != "") {
            $sparam['FechaFinPuesto'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaFinPuesto'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $sparam['xFechaFinPuesto'] = 1;
        }


        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::ObtenerDatosPlazasXRangoFecha_Cantidad($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    public function Modificar($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;
        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;
        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        if (!$this->_armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        $datosEnvio = (array)$datosElastic;

        if (!$oElastic->Actualizar($datosEnvio, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }

    public function ModificarEvento($datos): bool {
        $oEscuelasPuestosDesempeno = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES, $this->formato);

        if (!$oEscuelasPuestosDesempeno->Modificar($datos))
            return false;
        return true;
    }

    public function ModificarHorasModulos($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        if (!$this->_ValidarDatosVaciosHorasModulos($datos))
            return false;

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $this->_SetearNull($datos);

        if ($datos['IdTipo'] == 2) {
            if (!parent::ModificarModulos($datos))
                return false;
        } else{
            if (!parent::ModificarHoras($datos))
                return false;
        }

        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function ModificarCargo($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;
        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $this->_SetearNull($datos);
        if (!parent::ModificarCargo($datos))
            return false;
        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function ModificarMateria($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;
        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $this->_SetearNull($datos);
        if (!parent::ModificarMateria($datos))
            return false;
        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function ModificarCodigoPuesto($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        if (!parent::ModificarCodigoPuesto($datos))
            return false;

        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }

    public function ModificarNumeroResolucion($datos): bool {

        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        if (strlen($datos['NroResolucion']) > 50) {
            $this->setError(400, "Error, cantidad de caracteres excedido.");
            return false;
        }

        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

        if (!parent::ModificarNumeroResolucion($datos))
            return false;

        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        if (!$this->_armarObjetoElastic($datos, $datosModif, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        $datosEnvio = (array)$datosElastic;
        if (!$oElastic->Actualizar($datosEnvio, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }

    public function InsertarDesempeno($datos): bool {
        $oEscuelaDesempeno = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES, $this->formato);

        $datos['Estado'] = ACTIVO;
        if (!$oEscuelaDesempeno->BusquedaAvanzada($datos, $resultado, $numfilas)) {
            $this->setError($oEscuelaDesempeno->getError());
            return false;
        }

        if ($numfilas > 0) {
            $this->setError(400, "Actualmente ya existe el horario.");
            return false;
        }

        # Valida que no sobrepase el total de horas que puede tener el puesto
        $oEscuelasPuestos = new cEscuelasPuestos($this->conexion, $this->conexionES, $this->formato);
        if (!$oEscuelasPuestos->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        $filaEscuelasPuestos = $this->conexion->ObtenerSiguienteRegistro($resultado);

        $CantHoras = $filaEscuelasPuestos['CantHoras'];
        $CantModulos = $filaEscuelasPuestos['CantModulos'];
        $Cero = new DateTime('00:00:00');
        $TotalHoras = clone $Cero;
        if ($CantHoras != "" && $CantHoras != 0)
            $minutesToAdd = $CantHoras * CANT_HORAS_PUESTO;
        else
            $minutesToAdd = $CantModulos * CANT_MODULOS_PUESTO;

        $TotalHoras->modify("+{$minutesToAdd} minutes");


        if ($TotalHoras->format('H:i') > $Cero->format('H:i')) {
            if (!$oEscuelaDesempeno->BuscarxCodigo($datos, $resultadoHoras, $numfilasHoras)) {
                $this->setError($oEscuelaDesempeno->getError());
                return false;
            }

            $diff = $TotalHoras->diff($Cero);
            $totalNuevo = $diff->days * 2400 + $diff->h * 100 + $diff->i;
            $Total = $TotalMinutes = 0;
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoHoras)) {
                $hora_ini = new DateTime($fila['HoraInicio']);
                $hora_fin = new DateTime($fila['HoraFin']);
                $dif = $hora_fin->diff($hora_ini, true);
                $Total += $dif->h + $dif->days * 24;
                $TotalMinutes = $dif->i;
            }

            $new_inicio = new DateTime($datos['HoraInicio']);
            $new_fin = new DateTime($datos['HoraFin']);
            $new_diff = $new_fin->diff($new_inicio, true);

            if ($datos['Dia'] == 0) {
                $Total = $Total + ($new_diff->h + $new_diff->days * 24) * 5;
                $minutes = ($new_diff->i * 5) + $TotalMinutes;
            } else {
                $Total = $Total + $new_diff->h + $new_diff->days * 24;
                $minutes = $new_diff->i + $TotalMinutes;
            }

            $Total += floor($minutes / 60);
            $minutes = $minutes % 60;
//            $Total = new DateTime($Total.':'.floor($minutes));

            $Total *= 100;
            $Total += $minutes;

            if ($Total > $totalNuevo) {
                $this->setError(400, 'Error, se esta intentando cargar más horas que las permitidas en el puesto.');
                return false;
            }
        }
        #

        if (FuncionesPHPLocal::isEmpty($datos['IgnorarConflictos'])) {

            $oEscuelasPuestos = new cEscuelasPuestos($this->conexion);
            $oEscuelasPuestosPersonas = new cEscuelasPuestosPersonas($this->conexion);
            if (!$oEscuelasPuestosPersonas->BuscarPersonaxIdPuesto($datos, $resultadoPersona, $numfilasPersona)) {
                $this->setError($oEscuelasPuestosPersonas->getError());
                return false;
            }

            if ($numfilasPersona > 0) {
                $oIncompatibilidades = new Elastic\Incompatibilidades($this->conexionES);

                while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoPersona)) {
                    try {
                        $saltear = $oIncompatibilidades->verificarAgenteIgnorable($fila);
                    } catch (ExcepcionLogica $e) {
                        $this->setError($e->getError());
                        return false;
                    }
                    if ($saltear)
                        continue;
                    $datos['IdPersona'] = $fila['IdPersona'];
                    $datos['NombreCompleto'] = $fila['NombreCompleto'];

                    if (!FuncionesPHPLocal::isEmpty($datos['IdPersona'])) {

                        if (!$oEscuelasPuestos->BuscarNombrePuesto($datos, $resultadoPuesto, $numfilasPuesto)) {
                            $this->setError($oEscuelasPuestos->getError());
                            return false;
                        }

                        $NombrePuesto = '-';
                        if ($numfilasPuesto > 0) {
                            $NombrePuesto = $this->conexion->ObtenerSiguienteRegistro($resultadoPuesto)['PuestoNombre'];
                        }

                        if (!$oEscuelasPuestos->BuscarNombreEscuelaPuesto($datos, $resultadoPuesto, $numfilasPuesto)) {
                            $this->setError($oEscuelasPuestos->getError());
                            return false;
                        }

                        $NombreEscuela = '-';
                        if ($numfilasPuesto > 0) {
                            $NombreEscuela = $this->conexion->ObtenerSiguienteRegistro($resultadoPuesto)['Nombre'];
                        }

                        $dia = (int)$datos['Dia'];
                        $tope = $dia + 1;
                        if ($datos['Dia'] == 0) {
                            $dia = 1;
                            $tope = 6;
                        }

                        while ($dia < $tope) {

                            $datos['POFA'] = true;
                            $datosNuevo = ['desempeno' => [], 'horas' => []];
                            $idPuesto = $datos['IdPuesto'] . 'p';
                            $horario = new stdClass();
                            $horario->gte = new DateTime($datos['HoraInicio']);
                            $datos['HoraInicio'] = substr($datos['HoraInicio'], 0, 5);
                            $horario->lte = new DateTime($datos['HoraFin']);
                            $datos['HoraFin'] = substr($datos['HoraFin'], 0, 5);
                            $datosNuevo['desempeno'][$dia][$idPuesto] = [
                                'id' => (int)$datos['IdPuesto'],
                                'dia' => $dia,
                                'horario' => (object)['gte' => $datos['HoraInicio'], 'lte' => $datos['HoraFin']],
                                'desde' => substr($datos['HoraInicio'], 0, 5),
                                'hasta' => substr($datos['HoraFin'], 0, 5),
                                'puesto' => [
                                    'Cargo' => ['Descripcion' => utf8_encode($NombrePuesto)],
                                    'Escuela' => ['Nombre' => utf8_encode($NombreEscuela)],
                                    'Nivel' => ['Descripcion' => '-'],
                                ],
                            ];
                            $datosNuevo['horas'][$dia][$idPuesto] = $horario;

                            try {
                                if (!$oIncompatibilidades->validarSuperposicionHoraria($datos, $resumen, $datosNuevo)) {
                                    $this->setError($oIncompatibilidades->getError());
                                    return false;
                                }
                            } catch (Exception $e) {
                                $this->setError(500, $e->getMessage());
                                return false;
                            }

                            if ($resumen['hay_conflictos']) {
                                $this->setError(409, json_encode($resumen['colisiones']));
                                $this->error['nombre'] = $datos['NombreCompleto'];
                                return false;
                            }

                            $dia++;
                        }
                    }
                }
            }
        }


        //

        if (!empty($datos['validarSimultaneidad'])) {
            if (!$oEscuelaDesempeno->validarSimultaneidad($datos, $filaEscuelasPuestos)) {
                $this->setError($oEscuelaDesempeno->getError());
                return false;
            }
        }
        if (!$oEscuelaDesempeno->Insertar($datos, $codigoInsertado)) {
            $error = $oEscuelaDesempeno->getError();
            $this->setError($error['error'], $error['error_description']);
            return false;
        }

        return true;
    }

    public function EliminarCargo($datos): bool {

        $oEscuelaDesempeno = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES);
        if (!$oEscuelaDesempeno->BuscarxCodigo($datos, $resultado, $numfilas)) {
            $error = $oEscuelaDesempeno->getError();
            $this->setError($error['error'], $error['error_description']);
            return false;
        }
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        if ($numfilas > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                $datosEliminar['IdDesempeno'] = $fila['IdDesempeno'];
                $datosEliminar['IdPuesto'] = $fila['IdPuesto'];
                if (!$oEscuelaDesempeno->EliminarxIdPuestoxIdDesempeno($datosEliminar)) {

                    $error = $oEscuelaDesempeno->getError();
                    $this->setError($error['error'], $error['error_description']);
                    return false;
                }
            }
        }

        $datosModif['IdPuesto'] = $datos['IdPuesto'];
        $datosModif['Estado'] = ELIMINADO;

        if (!$this->ModificarEstado($datosModif))
            return false;

        if (!$this->enviarAgentesADisponibilidad($datos))
            return false;

        return true;
    }


    public function ReacomodarPuesto($datos, $datosRegistro): bool {

        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];


        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        if (!isset($datosRegistro['CargaManual']))
            $datosRegistro['CargaManual'] = 0;
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        if (!parent::ReacomodarPuesto($datos, $datosRegistro))
            return false;


        return true;
    }

    public function ReacomodarRaiz($datos, $datosRegistro): bool {

        if (!$this->BusquedaRecursivaRaizNullxIdPuesto($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            $IdPuestoRaizActualizar = "-1";
            $datosRegistro['UltimaModificacionFecha'] = $datosActualizar['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
            $datosRegistro['UltimaModificacionUsuario'] = $datosActualizar['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];

            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

                if ($fila['nivel'] == 1) {
                    $IdPuestoRaizActualizar = $fila['IdPuesto'];
                    $datosActualizar['IdPuesto'] = $fila['IdPuesto'];
                    $datosActualizar['IdPuestoRaiz'] = "NULL";
                    if (!parent::ModificarPuestoRaiz($datosActualizar))
                        return false;

                } else {
                    if ($IdPuestoRaizActualizar != "-1") {
                        $datosActualizar['IdPuesto'] = $fila['IdPuesto'];
                        $datosActualizar['IdPuestoRaiz'] = $IdPuestoRaizActualizar;
                        if (!parent::ModificarPuestoRaiz($datosActualizar))
                            return false;
                    }

                }

                $datosRegistro['IdPuesto'] = $fila['IdPuesto'];
                $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
                $datosRegistro['Accion'] = MODIFICACION;
                if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
                    return false;

            }
        }

        return true;
    }


    public function EliminarCargoPof($datos): bool {
        $oEscuelaDesempeno = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES);
        $datos['Estado'] = ACTIVO;
        if (!$oEscuelaDesempeno->BuscarxCodigoxEstado($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            $this->setError(400, "El cargo aún tiene desempeños asociados.");
            return false;
        }

        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!isset($datos['CargaManual']))
            $datosLog['CargaManual'] = 0;

        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosLog, $codigoInsertadolog))
            return false;

        $datosModif['IdPuesto'] = $datos['IdPuesto'];
        $datosModif['Estado'] = ELIMINADO;

        if (!$this->ModificarEstado($datosModif))
            return false;

        if (!$this->enviarAgentesADisponibilidad($datos))
            return false;

        return true;
    }

    public function EliminarCargoPofTitular($datos): bool {

        if (!$this->_ValidarEliminarPofTitular($datos))
            return false;

        $this->EliminarCargo($datos);

        return true;
    }

    private function enviarAgentesADisponibilidad(array $datos): bool {
        $oAgentes = new cEscuelasPuestosPersonas($this->conexion, $this->conexionES, $this->formato);

        $datosBuscar['IdPuesto'] = $datos['IdPuesto'];
        $datosBuscar['IdEstado'] = ALT;
        if (!$oAgentes->BuscarxPuestoEstado($datosBuscar, $resultado_personas, $numfilas_personas)) {
            if (FMT_ARRAY == $this->formato)
                $this->setError($oAgentes->getError());
            return false;
        }

        if ($numfilas_personas > 0) {
            while ($filaAgente = $this->conexion->ObtenerSiguienteRegistro($resultado_personas)) {
                if (1 == $filaAgente['IdRevista']) {
                    $filaAgente['Razon'] = 'Cese por cierre del puesto';
                    if (!$oAgentes->Eliminar($filaAgente)) {
                        if (FMT_ARRAY == $this->formato)
                            $this->setError($oAgentes->getError());
                        return false;
                    }
                } else {
                    $filaAgente['IdEstado'] = REU;
                    if (!$oAgentes->ModificarEstado($filaAgente)) {
                        if (FMT_ARRAY == $this->formato)
                            $this->setError($oAgentes->getError());
                        return false;
                    }
                }
            }
        }

        return true;
    }


    public function EliminarDesempeno($datos): bool {
        $oEscuelaDesempeno = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES, $this->formato);

        if (!$oEscuelaDesempeno->Eliminar($datos))
            return false;
        return true;
    }


    public function ModificarEstadoBD($datos): bool {

        if (!parent::ModificarEstado($datos))
            return false;

        if ($datos['Estado'] == ELIMINADO) {
            $datos['FechaHasta'] = $datos['FechaHasta'] ?? date('Y-m-d');
            if (!parent::ModificarFechaHasta($datos))
                return false;
        }

        return true;
    }

    public function ModificarEstado($datos): bool {
        if (!parent::ModificarEstado($datos))
            return false;

        if ($datos['Estado'] == ELIMINADO) {
            $datos['FechaHasta'] = $datos['FechaHasta'] ?? date('Y-m-d');
            if (!parent::ModificarFechaHasta($datos))
                return false;
        }

        if (!$this->_armarObjetoElastic($datos, $datosModif, $datosElastic))
            return false;

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        $datosEnvio = (array)$datosElastic;
        if (!$oElastic->Actualizar($datosEnvio, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }

        return true;
    }


    public function Activar(array $datos): bool {
        $datosmodif['IdPuesto'] = $datos['IdPuesto'];
        $datosmodif['Estado'] = ACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        if (!$this->_ValidarExistencia($datos, $datosRegistro))
            return false;
        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function DesActivar(array $datos): bool {
        $datosmodif['IdPuesto'] = $datos['IdPuesto'];
        $datosmodif['Estado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        if (!$this->_ValidarExistencia($datos, $datosRegistro))
            return false;
        $oAuditoriasEscuelasPuestos = new cAuditoriasEscuelasPuestos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasPuestos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function ComboDesempenosLugar($datos, &$resultado, &$numfilas): bool
    {
        return parent::ComboDesempenosLugar($datos, $resultado, $numfilas);
    }



//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------
    /**
     * @param array      $datos
     * @param array|null $datosRegistro
     * @param            $datosElastic
     *
     * @return bool
     */
    public function _armarObjetoElastic(array $datos, ?array &$datosRegistro, &$datosElastic): bool {

        if (empty($datosRegistro)) {

            if (!$this->buscarParaElastic($datos, $resultado, $numfilas))
                return false;

            if ($numfilas != 1) {
                $this->setError(404, 'Error, no existe el puesto');
                return false;
            }

            $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $datosRegistro['Codigo'] = $datosRegistro['CodigoPuesto'];
            $datosRegistro['Id'] = $datosRegistro['IdPuesto'];
        }

        try {
            $datosElastic = Elastic\Puestos::armarDatosElastic($datosRegistro);
        } catch (Exception $e) {
            $this->setError(400, $e->getMessage());
            return false;
        }


        return true;
    }

    public function duplicarPuesto(array $datos, ?int &$idPuestoHijo): bool {
        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        if (!parent::duplicarPuesto($datos, $idPuestoHijo))
            return false;

        $oEscuelasPuestosDesempeno = new cEscuelasPuestosDesempeno($this->conexion, $this->conexionES, FMT_ARRAY);
        $datos['IdPuestoPadre'] = $datos['IdPuesto'];
        $datos['IdPuesto'] = $idPuestoHijo;
        if (!$oEscuelasPuestosDesempeno->duplicarDesempenos($datos)) {
            $this->setError($oEscuelasPuestosDesempeno->getError());
            return false;
        }

        if (!$this->_armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;


        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Insertar($datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }
        return true;
    }


    public function GenerarCupof(array $datos):string {

        if (!$this->BuscarDatosCupof($datos, $datosCupof))
            return false;

        $filaDatosCupof = $this->conexion->ObtenerSiguienteRegistro($datosCupof);

        $CodigoEscuela = $filaDatosCupof['CodigoEscuela'];

        $NroAnexo = $datos['NroAnexo']??"0";

        $CodigoNivel = $filaDatosCupof["CodigoNivel"]??"N";

        $GradoAnio = $filaDatosCupof["GradoAnio"]??"N"; // misiones

        $Division = $filaDatosCupof["Division"]??"N";

        $Funcion = $filaDatosCupof["Funcion"];

        $Materia = "Ninguna";
        if (!FuncionesPHPLocal::isEmpty($filaDatosCupof["Materia"])){
            $Materia = $filaDatosCupof["Materia"];
        }

        $Turno = $filaDatosCupof["Turno"];

        if(empty($datos['FechaDesde'])) {
            $FechaCreacionPlaza="N/N/NNNN";
        } else {
            function esFormatoYmd($fecha) {
                $d = DateTime::createFromFormat('Y-m-d', $fecha);
                return $d && $d->format('Y-m-d') === $fecha;
            }
            if (esFormatoYmd($datos['FechaDesde'])) {
                $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'aaaa-mm-dd', 'dd/mm/aaaa');
            }
            $fechaCreacion = explode("/",$datos['FechaDesde'] );
            $FechaCreacionPlaza = $fechaCreacion[0]."/".$fechaCreacion[1]."/".$fechaCreacion[2];
        }

        $Planta= $datos["PlazaTransitoria"] == PLAZA_TRANSITORIA ? "TR" : "PP";

        //$CargoHoras = $datos['CantHorasModulos'] . ($datos["IdTipo"] == "1" ? "Hs" : "Mod");

        $CantHoras = $datos['CantHorasModulos'];

        $CargoHoras = "C";

        if (!FuncionesPHPLocal::isEmpty($datos['IdTipo'])) {
            if($datos['IdTipo'] == 2){
                $CargoHoras = "H";
                if(!FuncionesPHPLocal::isEmpty($CantHoras))
                    $CargoHoras .= "-".$CantHoras;
            } else {
                $CargoHoras = "C";
            }
        } else {
            if (!FuncionesPHPLocal::isEmpty($filaDatosCupof['IdRegimenSalarial'])){
                if($filaDatosCupof['IdRegimenSalarial'] == REGIMEN_SALARIAL_CARGO){
                    $CargoHoras = "C";
                } elseif ($filaDatosCupof['IdRegimenSalarial'] == REGIMEN_SALARIAL_HORAS) {
                    $CargoHoras = "H";
                    if(!FuncionesPHPLocal::isEmpty($CantHoras))
                        $CargoHoras .= "-".$CantHoras;
                }
            }
        }


        // Misiones     CodigoEscuela - NroAnexo - CodigoNivel - GradoAnio - Division - Funcion - Materia - Turno - FechaCreacionPlaza - Planta - CargoHoras
        return $CodigoEscuela."-".$NroAnexo."-".$CodigoNivel."-".$GradoAnio."-".$Division."-".$Turno."-".$Funcion."-".$Materia."-".$FechaCreacionPlaza."-".$Planta."-".$CargoHoras;


        // TDF          CodigoEscuela - CodigoNivel - NroAnexo - GradoAnio - Division - Turno - Turno (2 veces?) - Materia - CargoHoras - CantHoras - CargoCodigo - Funcion - FechaDesde - "POFA"
        // $CargoCodigo = $filaDatosCupof['CodigoCargo'];
            // tdf: return $CodigoEscuela."-".$CodigoNivel."-".$GradoAnio."-".$Division."-".$Turno."-".$Materia."-".$CargoHoras."-".$CantHoras."-".$CargoCodigo."-".$Funcion."-".$FechaCreacionPlaza."-"."POFA";

    }


    protected function BuscarDatosCupof($datos, &$resultado) {

        $sparam = [
            "IdEscuela" => $datos["IdEscuela"],
            "xIdPlanEducativo" => 0,
            'IdPlanEducativo'=> "",
            'xIdEscuelaTurnoAnioGrado'=> 0,
            'IdEscuelaTurnoAnioGrado'=> "",
            'xIdSeccion'=> 0,
            'IdSeccion'=> "",
            'IdFuncionCargo'=> $datos['IdFuncionCargo'],
            'xIdMateria'=> 0,
            'IdMateria'=> "",
            'IdEscuelaTurno'=> $datos['IdEscuelaTurno'],
            "xIdPuesto" => 0,
            "IdPuesto" => "",
            'IdCargo'=> $datos['IdCargo']
        ];

        if (isset($datos['IdPlanEducativo']) && $datos['IdPlanEducativo'] != "") {
            $sparam['IdPlanEducativo'] = $datos['IdPlanEducativo'];
            $sparam['xIdPlanEducativo'] = 1;
        }

        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != "") {
            $sparam['IdPuesto'] = $datos['IdPuesto'];
            $sparam['xIdPuesto'] = 1;
        }

        if (isset($datos['IdEscuelaTurnoAnioGrado']) && $datos['IdEscuelaTurnoAnioGrado'] != "") {
            $sparam['IdEscuelaTurnoAnioGrado'] = $datos['IdEscuelaTurnoAnioGrado'];
            $sparam['xIdEscuelaTurnoAnioGrado'] = 1;
        }

        if (isset($datos['IdSeccion']) && $datos['IdSeccion'] != "") {
            $sparam['IdSeccion'] = $datos['IdSeccion'];
            $sparam['xIdSeccion'] = 1;
        }

        if (isset($datos['IdMateria']) && $datos['IdMateria'] != "") {
            $sparam['IdMateria'] = $datos['IdMateria'];
            $sparam['xIdMateria'] = 1;
        }

        return parent::BuscarDatosCupof($sparam, $resultado);
    }



    protected function _actualizarElastic(array $datos, array $datosIniciales): bool {


        if (!$this->_armarObjetoElastic($datos, $datosRegistro, $datosElastic))
            return false;


        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if (!$oElastic->Actualizar($datos, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }
        return true;
    }

    private function _ValidarInsertarPuesto($datos) {

        if (FuncionesPHPLocal::isEmpty($datos['IdEscuela'])) {
            $this->setError(400, 'Error, falta asignar escuela del puesto');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdNivelModalidad'])) {
            $this->setError(400, 'Error, falta asignar escuela del puesto');
            return false;
        }


        return true;
    }


    private function _ValidarInsertar($datos) {

        if ($datos['accion'] == 1) {
            if (!$this->_ValidarDatosVaciosCargo($datos))
                return false;
        } else {
            if (!$this->_ValidarDatosVaciosSeccion($datos))
                return false;
        }

        if (!$this->_ValidarInsertarIdPuestoMigracion($datos))
            return false;

        if (!$this->_ValidarDatosVaciosFuncionCargo($datos))
            return false;


        return true;
    }

    private function _ValidarInsertarMaterias($datos) {
        if (!$this->_ValidarDatosVaciosMaterias($datos))
            return false;

        return true;
    }

    private function _ValidarModificar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;


        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        return true;
    }

    private function _ValidarEliminarPofTitular($datos) {

        $oEscuelasPuestosPersonas = new cEscuelasPuestosPersonas($this->conexion);

        if (!$oEscuelasPuestosPersonas->BuscarxIdPuestoRaiz($datos, $resultadoPersonas, $numfilasPersonas))
            return false;

        if ($numfilasPersonas > 0) {
            $this->setError(400, "El puesto tiene agentes asignados, se debe cesarlos para poder eliminar.");
            return false;
        }

        return true;
    }

    private function _ValidarEliminar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        /*
        if ($oEscuelasPuestosPersonas->BuscarPersonaxIdPuesto($datos, $resultadoPersona, $numfilasPersona))

            if ($numfilasPersona > 0) {
                $this->setError(400, "El puesto tiene un agente asignado, se debe cesar el agente para eliminar.");
                return false;
            }
          */

        return true;
    }


    private function _SetearNull(&$datos): void {

        if (FuncionesPHPLocal::isEmpty($datos['Estado']))
            $datos['Estado'] = ACTIVO;

        if (FuncionesPHPLocal::isEmpty($datos['AdmiteSuplente']))
            $datos['AdmiteSuplente'] = 1;

        if (!CARGACUPOF_MANUAL) {
            if (!isset($datos['CodigoPuesto']) || $datos['CodigoPuesto'] == "")
                $datos['CodigoPuesto'] = "NULL";
        }

        if (!isset($datos['IdPuestoPadre']) || $datos['IdPuestoPadre'] == "")
            $datos['IdPuestoPadre'] = "NULL";

        if (!isset($datos['IdEscuela']) || $datos['IdEscuela'] == "")
            $datos['IdEscuela'] = "NULL";

        if (!isset($datos['IdNivelModalidad']) || $datos['IdNivelModalidad'] == "")
            $datos['IdNivelModalidad'] = "NULL";

        if (!isset($datos['IdEscuelaTurno']) || $datos['IdEscuelaTurno'] == "")
            $datos['IdEscuelaTurno'] = "NULL";
        /*
           if (!isset($datos['IdPlanEducativo']) || empty($datos['IdPlanEducativo']))
               $datos['IdPlanEducativo'] = "NULL";

           if (!isset($datos['IdEscuelaTurnoAnioGrado']) || empty($datos['IdEscuelaTurnoAnioGrado']))
               $datos['IdEscuelaTurnoAnioGrado'] = "NULL";
      */

        if (!isset($datos['IdSeccion']) || $datos['IdSeccion'] == "")
            $datos['IdSeccion'] = "NULL";

        if (!isset($datos['IdCargo']) || $datos['IdCargo'] == "")
            $datos['IdCargo'] = "NULL";

        if (!isset($datos['IdGrupo']) || $datos['IdGrupo'] == "")
            $datos['IdGrupo'] = "NULL";

        if (!isset($datos['IdMateria']) || $datos['IdMateria'] == "")
            $datos['IdMateria'] = "NULL";

        if (!isset($datos['IdFuncionCargo']) || $datos['IdFuncionCargo'] == "")
            $datos['IdFuncionCargo'] = "NULL";

        if (!isset($datos['CantHoras']) || $datos['CantHoras'] == "")
            $datos['CantHoras'] = "NULL";

        if (!isset($datos['CantModulos']) || $datos['CantModulos'] == "")
            $datos['CantModulos'] = "NULL";

        if (!isset($datos['CargaManual']) || $datos['CargaManual'] == "")
            $datos['CargaManual'] = 0;

        if (!isset($datos['IdPuestoMigracion']) || $datos['IdPuestoMigracion'] == "")
            $datos['IdPuestoMigracion'] = "NULL";

        if (!isset($datos['MotivoPuesto']) || $datos['MotivoPuesto'] == "")
            $datos['MotivoPuesto'] = "NULL";

        if (!isset($datos['IdTemporalidadPuesto']) || $datos['IdTemporalidadPuesto'] == "")
            $datos['IdTemporalidadPuesto'] = "NULL";

        if (!isset($datos['FechaFinPuesto']) || $datos['FechaFinPuesto'] == "")
            $datos['FechaFinPuesto'] = "NULL";

        if (!isset($datos['IdFuenteFinanciamiento']) || $datos['IdFuenteFinanciamiento'] == "")
            $datos['IdFuenteFinanciamiento'] = "NULL";


        if (FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaFinPuesto'], 'FechaDDMMAAAA')) {
            $datos['FechaFinPuesto'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaFinPuesto'], 'dd/mm/aaaa', 'aaaa-mm-dd');
        }


        if (!isset($datos['FechaDesde']) || $datos['FechaDesde'] == "") {
            $datos['FechaDesde'] = date('Y-m-d');
        } elseif (FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaDesde'], 'FechaDDMMAAAA')) {
            $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], 'dd/mm/aaaa', 'aaaa-mm-dd');
        }

        if (FuncionesPHPLocal::isEmpty($datos['NroResolucion'])) {
            $datos['NroResolucion'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdRegimenSalarial'])) {
            $datos['IdRegimenSalarial'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['DesempenoLugar'])) {
            $datos['DesempenoLugar'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdEscalafon'])) {
            $datos['IdEscalafon'] = 'NULL';
        }

        if (!isset($datos['IdTipo']) || $datos['IdTipo']=="")
            $datos['IdTipo']="NULL";

        if (!isset($datos['IdPuestoOrigen']) || $datos['IdPuestoOrigen']=="")
            $datos['IdPuestoOrigen']="NULL";

        if (!isset($datos['IdGrupo']) || $datos['IdGrupo']=="")
            $datos['IdGrupo']="NULL";

        if (!isset($datos['IdSubGrupo']) || $datos['IdSubGrupo']=="")
            $datos['IdSubGrupo']="NULL";
    }

    private function _SetearFechas(&$datos): void {

        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
    }

    private function _ValidarDatosVaciosHorasModulos($datos) {
        if (!isset($datos['IdTipo']) || $datos['IdTipo'] == "") {
            $this->setError(400, "Debe seleccionar tipo");
            return false;
        }


        if (!isset($datos['CantHorasModulos']) || $datos['CantHorasModulos'] == "") {
           /* if ($datos['IdTipo'] == 1){
                $this->setError(400, "Debe ingresar una cantidad de horas");
                return false;
            }
            if ($datos['IdTipo'] == 2) {
                $this->setError(400, "Debe ingresar una cantidad de módulos");
                return false;
            }*/
            $this->setError(400, "Debe ingresar una cantidad de horas correcta");


        }

        if (isset($datos['CantHorasModulos']) && $datos['CantHorasModulos'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['CantHorasModulos'], "NumericoEntero")) {
               /* if ($datos['IdTipo'] == 1) {
                    $this->setError(400, "Debe ingresar un valor numérico para el campo horas.");
                    return false;
                }
                if ($datos['IdTipo'] == 2) {
                    $this->setError(400, "Debe ingresar un valor numérico para el campo módulos.");
                    return false;
                }*/
                $this->setError(400, "Debe ingresar un valor numérico para el campo cantidad.");
                return false;

            }
            if (strlen($datos['CantHorasModulos']) > 4) {
                /*if ($datos['IdTipo'] == 1){
                    $this->setError(400, "Error, el campo CantHoras no puede ser mayor a 4 .");
                    return false;
                }
                if ($datos['IdTipo'] == 2){
                    $this->setError(400, "Error, el campo CantModulos no puede ser mayor a 4 .");
                    return false;
                }*/
                $this->setError(400, "Debe ingresar un valor numérico para el campo cantidad.");
            }
        }
        return true;
    }

    private function _ValidarDatosVaciosFuncionCargo($datos) {
        /*
        if (!isset($datos['IdFuncionCargo']) || $datos['IdFuncionCargo'] == "") {
            $this->setError(400, "Error, debe seleccionar una Función Cargo");
            return false;
        }
        */
        if (isset($datos['IdFuncionCargo']) && $datos['IdFuncionCargo'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdFuncionCargo'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para la Función Cargo.");
                return false;
            }
            if (strlen($datos['IdFuncionCargo']) > 10) {
                $this->setError(400, "Error, el campo Función Cargo no puede ser mayor a 10 .");
                return false;
            }
        }


        return true;
    }


    private function _ValidarModificarIdPuestoMigracion($datos, $datosregistro) {

        if (cUsuariosPermisos::TienePermiso("009944")) {
            if (isset($datos['IdPuestoMigracion']) && $datos['IdPuestoMigracion'] != "") {
                /*
                if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdPuestoMigracion'], "NumericoEntero")) {
                    $this->setError(400, "Error debe ingresar un campo numérico para el campo IdPlazaExterno.");
                    return false;
                }*/

                if (strlen($datos['IdPuestoMigracion']) > 10) {
                    $this->setError(400, "Error debe ingresar menos de 10 caracteres para el campo IdPlazaExterno.");
                    return false;
                }
            }
        } else {
            if (isset($datosregistro['IdPuestoMigracion']) && $datosregistro['IdPuestoMigracion'] != "") {
                if ($datos['IdPuestoMigracion'] != $datosregistro['IdPuestoMigracion']) {
                    $this->setError(400, "No tiene permisos para modificar el IdPlazaExterno.");
                    return false;
                }
            } else {
                /*
                if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdPuestoMigracion'], "NumericoEntero")) {
                    $this->setError(400, "Error debe ingresar un campo numérico para el campo IdPlazaExterno.");
                    return false;
                }*/

                if (strlen($datos['IdPuestoMigracion']) > 10) {
                    $this->setError(400, "Error debe ingresar menos de 10 caracteres para el campo IdPlazaExterno.");
                    return false;
                }
            }

        }

        return true;
    }

    private function _ValidarInsertarIdPuestoMigracion($datos) {

        if (cUsuariosPermisos::TienePermiso("009944")) {
            if (isset($datos['IdPuestoMigracion']) && $datos['IdPuestoMigracion'] != "") {

                if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdPuestoMigracion'], "NumericoEntero")) {
                    $this->setError(400, "Error debe ingresar un campo numérico para el campo IdPlazaExterno.");
                    return false;
                }

                if (strlen($datos['IdPuestoMigracion']) > 10) {
                    $this->setError(400, "Error debe ingresar menos de 10 caracteres para el campo IdPlazaExterno.");
                    return false;
                }
            }
        }

        return true;
    }


    private function _ValidarDatosVaciosCargo($datos) {
        if (!isset($datos['IdEscuelaTurno']) || $datos['IdEscuelaTurno'] == "") {
            $this->setError(400, "Debe ingresar un turno");
            return false;
        }

        if (isset($datos['IdEscuelaTurno']) && $datos['IdEscuelaTurno'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdEscuelaTurno'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo IdEscuelaTurno.");
                return false;
            }
            if (strlen($datos['IdEscuelaTurno']) > 10) {
                $this->setError(400, "Error, el campo IdEscuelaTurno no puede ser mayor a 10 .");
                return false;
            }
        }

        if (!isset($datos['IdCargo']) || $datos['IdCargo'] == "") {
            $this->setError(400, "Debe seleccionar un cargo");
            return false;
        }

        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdCargo'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo IdCargo.");
                return false;
            }
            if (strlen($datos['IdCargo']) > 11) {
                $this->setError(400, "Error, el campo IdCargo no puede ser mayor a 11 .");
                return false;
            }
        }


        return true;
    }


    private function _ValidarDatosVaciosCargoSinSeccion($datos) {

        if (isset($datos['IdNivelModalidad']) && $datos['IdNivelModalidad'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdNivelModalidad'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo NivelModalidad.");
                return false;
            }
            if (strlen($datos['IdNivelModalidad']) > 10) {
                $this->setError(400, "Error, el campo IdNivelModalidad no puede ser mayor a 10 .");
                return false;
            }
        } else {
            $this->setError(400, "Error,debe seleccionar un nivel y modalidad para el cargo .");
            return false;
        }
        /*
        if (isset($datos['IdEscuelaTurno']) && $datos['IdEscuelaTurno'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdEscuelaTurno'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo IdEscuelaTurno.");
                return false;
            }
            if (strlen($datos['IdEscuelaTurno']) > 10) {
                $this->setError(400, "Error, el campo IdEscuelaTurno no puede ser mayor a 10 .");
                return false;
            }
        }*/

        if (!empty($datos['IdPlanEducativo']) && $datos['IdEscuelaTurnoAnioGrado'] == "") {

            $this->setError(400, "Error, debe seleccionar un Grado/Año.");
            return false;

        }

        if (!empty($datos['IdEscuelaTurnoAnioGrado']) && $datos['IdSeccion'] == "") {

            $this->setError(400, "Error, debe seleccionar una División");
            return false;

        }
        if (defined('VALIDACION_FUENTE_FINANCIAMIENTO') && VALIDACION_FUENTE_FINANCIAMIENTO) {
            if (!isset($datos['IdFuenteFinanciamiento']) || $datos['IdFuenteFinanciamiento'] == "") {

                $this->setError(400, "Error, debe seleccionar una fuente de financiamiento");
                return false;

            }
        }


        return true;
    }


    private function _ValidarDatosVaciosSeccion($datos) {
        if (!isset($datos['IdEscuelaTurno']) || $datos['IdEscuelaTurno'] == "") {
            $this->setError(400, "Debe ingresar un idescuelaturno");
            return false;
        }

        if (isset($datos['IdEscuelaTurno']) && $datos['IdEscuelaTurno'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdEscuelaTurno'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo IdEscuelaTurno.");
                return false;
            }
            if (strlen($datos['IdEscuelaTurno']) > 10) {
                $this->setError(400, "Error, el campo IdEscuelaTurno no puede ser mayor a 10 .");
                return false;
            }
        }

        if (!isset($datos['IdSeccion']) || $datos['IdSeccion'] == "") {
            $this->setError(400, "Debe seleccionar una sección");
            return false;
        }

        if (isset($datos['IdSeccion']) && $datos['IdSeccion'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdSeccion'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo IdSeccion.");
                return false;
            }
            if (strlen($datos['IdSeccion']) > 10) {
                $this->setError(400, "Error, el campo IdSeccion no puede ser mayor a 10 .");
                return false;
            }
        }

        if ((!isset($datos['IdCargo']) || $datos['IdCargo'] == "") && (!isset($datos['IdMateria']) || $datos['IdMateria'] == "")) {
            $this->setError(400, "Error, debe seleccionar un cargo o materia");
            return false;
        }

        if (defined('VALIDACION_FUENTE_FINANCIAMIENTO') && VALIDACION_FUENTE_FINANCIAMIENTO) {
            if (!isset($datos['IdFuenteFinanciamiento']) || $datos['IdFuenteFinanciamiento'] == "") {

                $this->setError(400, "Error, debe seleccionar una fuente de financiamiento");
                return false;

            }
        }

        return true;
    }


    private function _ValidarDatosVaciosMaterias($datos) {
        if (!isset($datos['IdEscuelaTurno']) || $datos['IdEscuelaTurno'] == "") {
            $this->setError(400, "Debe ingresar un idescuelaturno");
            return false;
        }

        if (isset($datos['IdEscuelaTurno']) && $datos['IdEscuelaTurno'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdEscuelaTurno'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo IdEscuelaTurno.");
                return false;
            }
            if (strlen($datos['IdEscuelaTurno']) > 10) {
                $this->setError(400, "Error, el campo IdEscuelaTurno no puede ser mayor a 10 .");
                return false;
            }
        }

        if (!isset($datos['IdSeccion']) || $datos['IdSeccion'] == "") {
            $this->setError(400, "Debe seleccionar una sección");
            return false;
        }

        if (isset($datos['IdSeccion']) && $datos['IdSeccion'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdSeccion'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo IdSeccion.");
                return false;
            }
            if (strlen($datos['IdSeccion']) > 10) {
                $this->setError(400, "Error, el campo IdSeccion no puede ser mayor a 10 .");
                return false;
            }
        }

        return true;
    }


    private function _ValidarDatosApiPlazas($datos) {
        if (!isset($datos['IdEscuelaTurno']) || $datos['IdEscuelaTurno'] == "") {
            $this->setError(400, "Debe ingresar un idescuelaturno");
            return false;
        }

        if (isset($datos['IdEscuelaTurno']) && $datos['IdEscuelaTurno'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdEscuelaTurno'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo IdEscuelaTurno.");
                return false;
            }
            if (strlen($datos['IdEscuelaTurno']) > 10) {
                $this->setError(400, "Error, el campo IdEscuelaTurno no puede ser mayor a 10 .");
                return false;
            }
        }

        if (!isset($datos['IdSeccion']) || $datos['IdSeccion'] == "") {
            $this->setError(400, "Debe seleccionar una sección");
            return false;
        }

        if (isset($datos['IdSeccion']) && $datos['IdSeccion'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdSeccion'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo IdSeccion.");
                return false;
            }
            if (strlen($datos['IdSeccion']) > 10) {
                $this->setError(400, "Error, el campo IdSeccion no puede ser mayor a 10 .");
                return false;
            }
        }

        return true;
    }


}
