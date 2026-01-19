<?php

use Bigtree\ExcepcionDB;

abstract class cCargosDB {
    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    /**
     * Constructor de la clase cCargosDB.
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
     * Destructor de la clase cCargosDB.
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

    protected function CargosTiposSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_CargosTipos_combo_Nombre';
        $sparam = [];
    }

    public abstract function CargosTiposSPResult(&$resultado, ?int &$numfilas): bool;


    protected function RegimenTipos(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_RegimenSalarial_Combo';
        $sparam = [];
    }


    /**
     * @return array
     * @throws ExcepcionDB
     */
    protected function cargosComputables(): array {
        $spnombre = 'sel_Cargos_computables';
        $sparam = [];
        $this->conexion->getParent()->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno);
        $datos = $this->conexion->getParent()->ObtenerSiguienteRegistro($resultado);
        return explode(',', $datos['cargos_computables'] ?? '');

    }

    public abstract function RegimenTiposResult(&$resultado, ?int &$numfilas): bool;


    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Cargos_xIdCargo";
        $sparam = [
            'pIdCargo' => $datos['IdCargo'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BuscarxJerarquia(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Cargos_xJerarquico";
        $sparam = [
            'pJerarquico' => $datos['Jerarquico'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Cargos_busqueda_avanzada";
        $sparam = [
            'pxIdCargo' => $datos['xIdCargo'],
            'pIdCargo' => $datos['IdCargo'],
            'pxIdTipoCargo' => $datos['xIdTipoCargo'],
            'pIdTipoCargo' => $datos['IdTipoCargo'],
            'pxCodigo' => $datos['xCodigo'],
            'pCodigo' => $datos['Codigo'],
            'pxDescripcion' => $datos['xDescripcion'],
            'pDescripcion' => $datos['Descripcion'],
            'pxEsdeno' => $datos['xEsdeno'],
            'pEsdeno' => $datos['Esdeno'],
            'pxEquivalenciaHs' => $datos['xEquivalenciaHs'],
            'pEquivalenciaHs' => $datos['EquivalenciaHs'],
            'pxJerarquico' => $datos['xJerarquico'],
            'pJerarquico' => $datos['Jerarquico'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'pxIdRegimenSalarial' => $datos['xIdRegimenSalarial'],
            'pIdRegimenSalarial' => $datos['IdRegimenSalarial'],
            'pxIdEscalafon' =>  $datos['xIdEscalafon'],
            'pIdEscalafon' => $datos['IdEscalafon'],
            'pxDesempenoLugar' =>  $datos['xDesempenoLugar'],
            'pDesempenoLugar' =>  $datos['DesempenoLugar'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BuscarAuditoriaRapida(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Cargos_AuditoriaRapida";
        $sparam = [
            'pIdCargo' => $datos['IdCargo'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BuscarCombo(&$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Cargos_combo";
        $sparam = [];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function Insertar(array $datos, ?int &$codigoInsertado): bool {
        $spnombre = "ins_Cargos";
        $sparam = [
            'pIdTipoCargo' => $datos['IdTipoCargo'],
            'pCodigo' => $datos['Codigo'],
            'pIdExterno' => $datos['IdExterno'],
            'pAdmiteSuplente' => $datos['AdmiteSuplente'],
            'pDescripcion' => $datos['Descripcion'],
            'pEsdeno' => $datos['Esdeno'],
            'pEquivalenciaHs' => $datos['EquivalenciaHs'],
            'pJerarquico' => $datos['Jerarquico'],
            'pPermiteSimultaneo' => $datos["PermiteSimultaneo"],
            'pSCParcial' => $datos['SCParcial'],
            'pIdRegimenSalarial' => $datos['IdRegimenSalarial'],
            'pIdJornada' => $datos['IdJornada'],
            'pEstado' => $datos['Estado'],
            'pIdTipo' => $datos["IdTipo"],
            'pIdEscalafon' => $datos["IdEscalafon"],
            'pDesempenoLugar' => $datos["DesempenoLugar"],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }


    protected function Modificar(array $datos): bool {
        $spnombre = "upd_Cargos_xIdCargo";
        $sparam = [
            'pIdTipoCargo' => $datos['IdTipoCargo'],
            'pCodigo' => $datos['Codigo'],
            'pIdExterno' => $datos['IdExterno'],
            'pAdmiteSuplente' => $datos['AdmiteSuplente'],
            'pDescripcion' => $datos['Descripcion'],
            'pEsdeno' => $datos['Esdeno'],
            'pEquivalenciaHs' => $datos['EquivalenciaHs'],
            'pJerarquico' => $datos['Jerarquico'],
            'pPermiteSimultaneo' => $datos["PermiteSimultaneo"],
            'pSCParcial' => $datos['SCParcial'],
            'pIdRegimenSalarial' => $datos['IdRegimenSalarial'],
            'pIdJornada' => $datos['IdJornada'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pIdCargo' => $datos['IdCargo'],
            'pIdTipo' => $datos["IdTipo"],
            'pIdEscalafon' => $datos["IdEscalafon"],
            'pDesempenoLugar' => $datos["DesempenoLugar"],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function ModificarJerarquia(array $datos): bool {
        $spnombre = "upd_Cargos_Jerarquia_xIdCargo";
        $sparam = [
            'pJerarquico' => $datos['Jerarquico'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pIdCargo' => $datos['IdCargo'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function Eliminar(array $datos): bool {
        $spnombre = "del_Cargos_xIdCargo";
        $sparam = [
            'pIdCargo' => $datos['IdCargo'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function ModificarEstado(array $datos): bool {
        $spnombre = "upd_Cargos_Estado_xIdCargo";
        $sparam = [
            'pEstado' => $datos['Estado'],
            'pIdCargo' => $datos['IdCargo'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function ComboDesempenosLugar($datos, &$resultado, &$numfilas): bool
    {
        $spnombre="sel_CargosDesempenoLugar_combo";
        $sparam=array();

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la busqueda de datos para el cupof",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

}
