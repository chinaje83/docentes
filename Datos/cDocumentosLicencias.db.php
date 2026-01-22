<?php

abstract class cDocumentosLicenciasDB
{
    /** @var accesoBDLocal  */
    protected $conexion;
    /** @var mixed  */
    protected $formato;
    /** @var array  */
    protected $error;
    /**
     * Constructor de la clase cDocumentosPuestosDB.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion,$formato){

        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    /**
     * Destructor de la clase cDocumentosPuestosDB.
     */
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
    protected function setError($error, $error_description=''): void {
        $this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
    }

    protected function BuscarxCodigo(array $datos,  &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_DocumentosLicencias';
        $sparam = [
            'pIdDocumento'=> $datos['IdDocumento'],
            'pIdLicencia'=> $datos['IdLicencia']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam,$resultado, $numfilas, $errno)) {
            $this->setError(400,'Error al buscar por código.');
            return false;
        }

        return true;
    }

    protected function BuscarxDocumento(array $datos,  &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_DocumentosLicencias_xIdDocumento';
        $sparam = [
            'pIdDocumento'=> $datos['IdDocumento']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam,$resultado, $numfilas, $errno)) {
            $this->setError(400,'Error al buscar por código.');
            return false;
        }

        return true;
    }

    protected function Insertar(array $datos): bool {

        $spnombre = 'ins_DocumentosLicencias';
        $sparam = [
            'pIdDocumento'=> $datos['IdDocumento'],
            'pIdLicencia'=> $datos['IdLicencia'],
            'pAltaUsuario'=> $datos['AltaUsuario'],
            'pAltaFecha'=> $datos['AltaFecha'],
            'pAltaEscuela'=> $datos['AltaEscuela'],
            'pAltaRol'=> $datos['AltaRol'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionEscuela'=> $datos['UltimaModificacionEscuela'],
            'pUltimaModificacionRol'=> $datos['UltimaModificacionRol'],
            'pHashDato'=> $datos['HashDato']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam,$resultado, $numfilas, $errno)) {
            $this->setError(400,'Error al insertar.');
            return false;
        }

        return true;
    }

    protected function ModificarHashDato($datos) {

        $spnombre = 'upd_DocumentosLicencias_Hash';
        $sparam = [
            'pHashDato'=> $datos['HashDato'],
            'pIdDocumento'=> $datos['IdDocumento'],
            'pIdLicencia'=> $datos['IdLicencia']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam,$resultado, $numfilas, $errno)) {
            $this->setError(400,'Error al modificar hash.');
            return false;
        }

        return true;
    }

}