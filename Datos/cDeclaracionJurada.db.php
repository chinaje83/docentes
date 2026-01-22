<?php
abstract class cDeclaracionJuradaDB
{
    protected $conexion;
    protected $formato;
    protected $error;

    function __construct(accesoBDLocal $conexion,$formato){

        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    function __destruct(){}

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public abstract function getError(): array;

    /**
     * Guarda un mensaje de error
     *
     * @param string|array  $error
     * @param string        $error_description
     */
    protected function setError($error,$error_description=''): void {
        $this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
    }

    protected function Insertar($datos,&$codigoInsertado): bool {

        $spnombre = "ins_DDJJ";
        $sparam = array(
            'pIdUsuario'=> $datos['IdUsuario'],
            'pIdTipoDDJJ'=> $datos['IdTipoDDJJ'],
            'pAnio'=> date('Y'),
            'pIdEscuela'=> $datos['IdEscuela'],
            'pRegistroSeguridad'=> $datos['RegistroSeguridad'],
            'pTexto'=> $datos['PuestosJson'],
            'pAltaFecha'=> $datos['AltaFecha'],
            'pAltaUsuario'=> $datos['AltaUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();

        return true;
    }
}