<?php

namespace Bigtree\Logica;

use cReincorporacionesCesesPendientesdb;
use FuncionesPHPLocal;

include(DIR_CLASES_DB . "cReincorporacionesCesesPendientes.db.php");

class cReincorporacionesCesesPendientes extends cReincorporacionesCesesPendientesdb {

    protected $conexion;
    protected $formato;

    function __construct($conexion, $formato = FMT_TEXTO) {
        $this->conexion = &$conexion;
        $this->formato = &$formato;
        parent::__construct();
    }

    function __destruct() {
        parent::__destruct();
    }


    public function busquedaAvanzada($datos, &$numfilas, &$resultado) {
        $sparam = [
            'xId' => 0,
            'Id' => '',
            'xIdPuesto' => 0,
            'IdPuesto' => '',
            'xIdPofa' => 0,
            'IdPofa' => '',
            'xNombreSuplente' => 0,
            'NombreSuplente' => '',
            'xIdEscuela' => 0,
            'IdEscuela' => '',
            'xNombreSuplido' => 0,
            'NombreSuplido' => '',
            'limit' => '',
            'orderby' => 'rcp.AltaFecha ASC',
        ];

        if (isset($datos['Id']) && $datos['Id'] != '') {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }

        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != '') {
            $sparam['IdPuesto'] = $datos['IdPuesto'];
            $sparam['xIdPuesto'] = 1;
        }

        if (isset($datos['IdPofa']) && $datos['IdPofa'] != '') {
            $sparam['IdPofa'] = $datos['IdPofa'];
            $sparam['xIdPofa'] = 1;
        }

        if (isset($datos['NombreSuplente']) && $datos['NombreSuplente'] != '') {
            $sparam['NombreSuplente'] = $datos['NombreSuplente'];
            $sparam['xNombreSuplente'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != '') {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['NombreSuplido']) && $datos['NombreSuplido'] != '') {
            $sparam['NombreSuplido'] = $datos['NombreSuplido'];
            $sparam['xNombreSuplido'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != '')
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != '')
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;

        return true;
    }


    public function buscarXIdEscuela($datos, &$numfilas, &$resultado) {

        if (!parent::buscarXIdEscuela($datos, $numfilas, $resultado))
            return false;

        return true;
    }


    public function Insertar($datos, &$codigoinsertado) {

        if (!$this->_ValidarInsertar($datos))
            return false;

        if (isset($datos['FechaAlta']) && $datos['FechaAlta'] != "" && substr($datos['FechaAlta'], 2, 1) == "/")
            $datos['FechaAlta'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaAlta'], 'dd/mm/aaaa', 'aaaa-mm-dd');

        $this->_SetearNull($datos);

        if (!parent::Insertar($datos, $codigoinsertado))
            return false;

        return true;
    }


    public function CambiarEstadoxIdPofa($datos) {

        $datos['Estado'] = 2; //Resuelto

        if (!parent::CambiarEstadoxIdPofa($datos))
            return false;
        return true;
    }

//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

    private function _ValidarInsertar($datos) {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        return true;
    }

    private function _SetearNull(&$datos) {

        return true;
    }


    private function _ValidarDatosVacios($datos) {

        if (!isset($datos['IdPersonaReincorporada']) || $datos['IdPersonaReincorporada'] == '') {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Debe ingresar una persona para reincorporar', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }

        if (!isset($datos['IdEscuela']) || $datos['IdEscuela'] == '') {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Debe ingresar un escuela', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }


        if (!isset($datos['IdPuesto']) || $datos['IdPuesto'] == '') {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Debe ingresar un puesto', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }

        return true;
    }

}

?>
