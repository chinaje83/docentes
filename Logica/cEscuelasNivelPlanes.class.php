<?php
include(DIR_CLASES_DB."cEscuelasNivelPlanes.db.php");
class cEscuelasNivelPlanes extends cEscuelasNivelPlanesdb
{
    /**
     * Constructor de la clase cEscuelasNivelPlanes.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion,$formato=FMT_TEXTO){
        parent::__construct($conexion,$formato);
    }
    /**
     * Destructor de la clase cEscuelasNivelPlanes.
     */
    function __destruct(){
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

    public function getPlanes( &$resultado,&$numfilas): bool
    {
        if (!parent::getPlanes($resultado,$numfilas))
            return false;
        return true;
    }
    public function BuscarxCodigo($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
    {
        $sparam=array(
            'xIdPlanEducativo'=> 0,
            'IdPlanEducativo'=> "",
            'xIdNivelModalidad'=> 0,
            'IdNivelModalidad'=> "",
            'xEstado'=> 0,
            'Estado'=> "",
            'limit'=> '',
            'orderby'=> "Id ASC"
        );
        if(isset($datos['IdPlanEducativo']) && $datos['IdPlanEducativo']!="")
        {
            $sparam['IdPlanEducativo']= $datos['IdPlanEducativo'];
            $sparam['xIdPlanEducativo']= 1;
        }
        if(isset($datos['IdNivelModalidad']) && $datos['IdNivelModalidad']!="")
        {
            $sparam['IdNivelModalidad']= $datos['IdNivelModalidad'];
            $sparam['xIdNivelModalidad']= 1;
        }
        if(isset($datos['Estado']) && $datos['Estado']!="")
        {
            $sparam['Estado']= $datos['Estado'];
            $sparam['xEstado']= 1;
        }

        if(isset($datos['orderby']) && $datos['orderby']!="")
            $sparam['orderby']= $datos['orderby'];
        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];
        if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarExistente($datos, &$resultado, &$numfilas): bool
    {
        if (!parent::BuscarExistente($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarxIdNivelModalidad($datos, &$resultado, &$numfilas): bool
    {
        if (!parent::BuscarxIdNivelModalidad($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdEscuelaTurno($datos, &$resultado, &$numfilas): bool
    {
        if (!parent::BuscarxIdEscuelaTurno($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function Insertar($datos,&$codigoInsertado): bool
    {
        if (!$this->BuscarExistente($datos,$resultado,$numfilas))
            return false;

        if ($numfilas > 0) {
            $this->setError(400, 'Actualmente el plan ya se encuentra asociado');
            return false;
        }

        if (!$this->_ValidarInsertar($datos))
            return false;
        $this->_SetearNull($datos);
        $datos['AltaFecha']=date("Y-m-d H:i:s");
        $datos['AltaUsuario']=$_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;
        if (!parent::Insertar($datos,$codigoInsertado))
            return false;

        $oAuditoriasEscuelasNivelPlanes = new cAuditoriasEscuelasNivelPlanes($this->conexion,$this->formato);
        $datos['Id'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        if(!$oAuditoriasEscuelasNivelPlanes->InsertarLog($datos,$codigoInsertadolog))
            return false;
        return true;
    }


    public function Modificar($datos): bool
    {
        if (!$this->_ValidarModificar($datos,$datosRegistro))
            return false;
        $datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;
        $oAuditoriasEscuelasNivelPlanes = new cAuditoriasEscuelasNivelPlanes($this->conexion,$this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if(!$oAuditoriasEscuelasNivelPlanes->InsertarLog($datosRegistro,$codigoInsertadolog))
            return false;
        return true;
    }


    public function Eliminar($datos): bool
    {
        if (!$this->_ValidarEliminar($datos,$datosRegistro))
            return false;
        $oAuditoriasEscuelasNivelPlanes = new cAuditoriasEscuelasNivelPlanes($this->conexion,$this->formato);
        $datosLog =$datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if(!$oAuditoriasEscuelasNivelPlanes->InsertarLog($datosLog,$codigoInsertadolog))
            return false;
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = ELIMINADO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        return true;
    }

    public function EliminarFisico($datos): bool
    {
        if (!$this->_ValidarEliminar($datos,$datosRegistro))
            return false;
        $oAuditoriasEscuelasNivelPlanes = new cAuditoriasEscuelasNivelPlanes($this->conexion,$this->formato);
        $datosLog =$datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if(!$oAuditoriasEscuelasNivelPlanes->InsertarLog($datosLog,$codigoInsertadolog))
            return false;
        $datosmodif['Id'] = $datos['Id'];
        if (!parent::Eliminar($datosmodif))
            return false;
        return true;
    }


    public function ModificarEstado($datos): bool
    {
        if (!parent::ModificarEstado($datos))
            return false;
        return true;
    }


    public function Activar(array $datos): bool
    {
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = ACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        if (!$this->_ValidarEliminar($datos,$datosRegistro))
            return false;
        $oAuditoriasEscuelasNivelPlanes = new cAuditoriasEscuelasNivelPlanes($this->conexion,$this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if(!$oAuditoriasEscuelasNivelPlanes->InsertarLog($datosRegistro,$codigoInsertadolog))
            return false;
        return true;
    }


    public function DesActivar(array $datos): bool
    {
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        if (!$this->_ValidarEliminar($datos,$datosRegistro))
            return false;
        $oAuditoriasEscuelasNivelPlanes = new cAuditoriasEscuelasNivelPlanes($this->conexion,$this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if(!$oAuditoriasEscuelasNivelPlanes->InsertarLog($datosRegistro,$codigoInsertadolog))
            return false;
        return true;
    }


    public function ModificarOrdenCompleto($datos): bool
    {
        $datosmodif['Id'] = 1;
        $arregloOrden = explode(",",$datos['orden']);
        foreach ($arregloOrden as $Id){
            $datosmodif['Id'] = $Id;
            if (!parent::ModificarOrden($datosmodif))
                return false;
            $datosmodif['Id']++;
        }
        return true;
    }


    private function ObtenerProximoOrden(array $datos, ?int &$proxorden): bool
    {
        $proxorden = 0;
        if (!parent::BuscarUltimoOrden($datos,$resultado,$numfilas))
            return false;
        if ($numfilas!=0){
            $datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $proxorden = $datos['maximo'] + 1;
        }
        return true;
    }




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

    private function _ValidarInsertar($datos)
    {
        if (!$this->_ValidarDatosVacios($datos))
            return false;
        return true;
    }


    private function _ValidarModificar($datos,&$datosRegistro)
    {
        if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1) {
            $this->setError(400, 'Error debe ingresar un código valido.');
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        if (!$this->_ValidarDatosVacios($datos))
            return false;
        return true;
    }


    private function _ValidarEliminar($datos,&$datosRegistro)
    {
        if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1) {
            $this->setError(400, utf8_encode('Error, debe ingresar un código valido.'));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        //buscoque el plan no tenga años/secciones asociadas

        $datosBusqueda['IdNivelModalidad'] = $datosRegistro['IdNivelModalidad'];
        $datosBusqueda['IdEscuela'] = $datos['IdEscuela'];

        $oEscuelasTurnos = new cEscuelasTurnos($this->conexion, "");
        if (!$oEscuelasTurnos->BuscarxIdEscuelaxIdNivelModalidadxIdCicloLectivo($datosBusqueda, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            $this->setError(400, "Error, no se puede eliminar un plan con turnos asociados.");
            return false;
        }

        return true;
    }


    private function _SetearNull(&$datos): void
    {
        if (!isset($datos['IdPlanEducativo']) || $datos['IdPlanEducativo']=="")
            $datos['IdPlanEducativo']="NULL";

        if (!isset($datos['IdNivelModalidad']) || $datos['IdNivelModalidad']=="")
            $datos['IdNivelModalidad']="NULL";

        if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
            $datos['UltimaModificacionFecha']="NULL";
    }


    private function _ValidarDatosVacios($datos)
    {
        if (!isset($datos['IdPlanEducativo']) || $datos['IdPlanEducativo']=="") {
            $this->setError(400, 'Debe ingresar un plan educativo');
            return false;
        }

        if (isset($datos['IdPlanEducativo']) && $datos['IdPlanEducativo']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdPlanEducativo'],"NumericoEntero"))
            {
                $this->setError(400, 'Error debe ingresar un campo numérico para el campo Plan Educativo.');
                return false;
            }
            if (strlen($datos['IdPlanEducativo'])>11)
            {
                $this->setError(400, 'Error, el campo Plan Educativo no puede ser mayor a 11');
                return false;
            }
        }

        if (!isset($datos['IdNivelModalidad']) || $datos['IdNivelModalidad']=="")
        {
            $this->setError(400, 'Debe ingresar un nivel/modalidad');
            return false;
        }

        if (isset($datos['IdNivelModalidad']) && $datos['IdNivelModalidad']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdNivelModalidad'],"NumericoEntero"))
            {
                $this->setError(400, 'Error debe ingresar un campo numérico para el campo Nivel/Modalidad.');
                return false;
            }
            if (strlen($datos['IdNivelModalidad'])>11)
            {
                $this->setError(400, 'Error, el campo Nivel/Modalidad no puede ser mayor a 11');
                return false;
            }
        }
        return true;
    }




}