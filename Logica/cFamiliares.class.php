<?php
require_once(DIR_CLASES_DB . "cFamiliares.db.php");


class cFamiliares extends cFamiliaresdb {

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


    public function BuscarxCodigo($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarxCUIL($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarxCUIL($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarAgenteRelacionado($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarAgenteRelacionado($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BusquedaAvanzada($datos, &$resultado, &$numfilas) {
        $sparam = [
            'xId' => 0,
            'Id' => "",
            'xIdFamiliarAgente' => 0,
            'IdFamiliarAgente' => "",
            'xNombre' => 0,
            'Nombre' => "",
            'xApellido' => 0,
            'Apellido' => "",
            'xDni' => 0,
            'Dni' => "",
            'xCUIL' => 0,
            'CUIL' => "",
            'xIdEstado' => 0,
            'IdEstado' => "",
            'xIdAccionesDocAuxSesion' => 0,
            'IdAccionesDocAuxSesion' => "-1",
            'xIdPersona' => 0,
            'IdPersona' => "",
            'limit' => '',
            'orderby' => "F.Id DESC",
        ];

        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }
        if (isset($datos['IdFamiliarAgente']) && $datos['IdFamiliarAgente'] != "") {
            $sparam['IdFamiliarAgente'] = $datos['IdFamiliarAgente'];
            $sparam['xIdFamiliarAgente'] = 1;
        }
        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            $sparam['Nombre'] = $datos['Nombre'];
            $sparam['xNombre'] = 1;
        }
        if (isset($datos['Apellido']) && $datos['Apellido'] != "") {
            $sparam['Apellido'] = $datos['Apellido'];
            $sparam['xApellido'] = 1;
        }
        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            $sparam['Dni'] = $datos['Dni'];
            $sparam['xDni'] = 1;
        }
        if (isset($datos['CUIL']) && $datos['CUIL'] != "") {
            $sparam['CUIL'] = $datos['CUIL'];
            $sparam['xCUIL'] = 1;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['IdAccionesDocAuxSesion']) && $datos['IdAccionesDocAuxSesion'] != "") {
            $sparam['IdAccionesDocAuxSesion'] = $datos['IdAccionesDocAuxSesion'];
            $sparam['xIdAccionesDocAuxSesion'] = 1;
        }

        if (isset($datos['IdPersona']) && $datos['IdPersona'] != "") {
            $sparam['IdPersona'] = $datos['IdPersona'];
            $sparam['xIdPersona'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    public function Insertar($datos, &$codigoinsertado) {

        if (!$this->_ValidarDatosVacios($datos))
            return false;

        $this->_SetearNull($datos, $datosBuscar);
        $datos['NombreCompleto'] = $datos['Nombre'] . ' ' . $datos['Apellido'];
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['Estado'] = ACTIVO;
        if (!isset($datos['EsFamiliar']) || $datos['EsFamiliar'] == '')
            $datos['EsFamiliar'] = 0;


        if (!parent::Insertar($datos, $codigoinsertado))
            return false;


        return true;
    }


    public function Modificar($datos) {

        if (!$this->_ValidarDatosVaciosModificar($datos))
            return false;

        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");

        if (!parent::Modificar($datos))
            return false;

        return true;
    }


    public function DenegarFamiliar($datos) {

        if (!$this->_ValidarDatosVaciosDenegar($datos))
            return false;

        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");

        if (!parent::DenegarFamiliar($datos))
            return false;

        return true;
    }

//-----------------------------------------------------------------------------------------

    private function _SetearNull($datos, &$datosBuscar) {


        if (!isset($datos['Email']) || $datos['Email'] == "") {
            $datosBuscar['Email'] = 'NULL';
        }

        if (!isset($datos['Telefono']) || $datos['Telefono'] == "") {
            $datosBuscar['Telefono'] = 'NULL';
        }

        if (!isset($datos['Calle']) || $datos['Calle'] == "") {
            $datosBuscar['Calle'] = 'NULL';
        }

        if (!isset($datos['NumeroPuerta']) || $datos['NumeroPuerta'] == "") {
            $datosBuscar['NumeroPuerta'] = 'NULL';
        }

        if (!isset($datos['CodigoPostal']) || $datos['CodigoPostal'] == "") {
            $datosBuscar['CodigoPostal'] = 'NULL';
        }

        if (!isset($datos['Depto']) || $datos['Depto'] == "") {
            $datosBuscar['Depto'] = 'NULL';
        }

        if (!isset($datos['IdDepartamento']) || $datos['IdDepartamento'] == "") {
            $datosBuscar['IdDepartamento'] = 'NULL';
        }

        if (!isset($datos['IdProvincia']) || $datos['IdProvincia'] == "") {
            $datosBuscar['IdProvincia'] = 'NULL';
        }

        if (!isset($datos['Piso']) || $datos['Piso'] == "") {
            $datosBuscar['Piso'] = 'NULL';
        }

        if (!isset($datos['IdLocalidad']) || $datos['IdLocalidad'] == "") {
            $datosBuscar['IdLocalidad'] = 'NULL';
        }

        if (!isset($datos['IdRegion']) || $datos['IdRegion'] == "") {
            $datosBuscar['IdRegion'] = 'NULL';
        }

        if (!isset($datos['Size']) || $datos['Size'] == "") {
            $datosBuscar['Size'] = 'NULL';
        }

        if (!isset($datos['File']) || $datos['File'] == "") {
            $datosBuscar['File'] = 'NULL';
        }

        if (!isset($datos['Name']) || $datos['Name'] == "") {
            $datosBuscar['Name'] = 'NULL';
        }


        return true;
    }


    private function _ValidarDatosVacios($datos) {

        if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un nombre", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['Apellido']) || $datos['Apellido'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un apellido", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        if (!isset($datos['CUIL']) || $datos['CUIL'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un CUIL", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['CUIL'], "CUIT")) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo numérico para CUIL.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        if (!isset($datos['Dni']) || $datos['Dni'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un DNI", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un tipo de documento", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['FechaNacimiento']) || $datos['FechaNacimiento'] == "") {

            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar una fecha de nacimiento.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['FechaNacimiento'], "FechaAAAAMMDD")) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar una fecha de nacimiento valida.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['Sexo']) || $datos['Sexo'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar el sexo.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        if (!isset($datos['Discapacidad']) || $datos['Discapacidad'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar si tiene discapacidad.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    private function _ValidarDatosVaciosModificar($datos) {

        if (!isset($datos['Id']) || $datos['Id'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar el id del familiar", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        if (!isset($datos['IdFamiliarPersona']) || $datos['IdFamiliarPersona'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar el id del familiar en personas", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        if (!isset($datos['IdEstado']) || $datos['IdEstado'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un estado", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    private function _ValidarDatosVaciosDenegar($datos) {

        if (!isset($datos['Id']) || $datos['Id'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar el id del familiar", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        if (!isset($datos['IdEstado']) || $datos['IdEstado'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un estado", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


}

?>
