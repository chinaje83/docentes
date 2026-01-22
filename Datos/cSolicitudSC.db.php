<?php

abstract class cSolicitudSCDB
{
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
    public function __construct(accesoBDLocal $conexion, $formato)
    {
        $this->conexion =& $conexion;
        $this->formato = $formato;
    }

    /**
     * Destructor de la clase
     */
    public function __destruct()
    {
        $this->error = [];
    }

    /**
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */


    protected function buscarListado($datos, &$resultado, &$numfilas)
    {
        $spnombre = "sel_solicitudes_coberturas_tipo_listado";
        $sparam = array(
            'ptable'=> BASEDATOS,
			'pxTipo_cargo'=> $datos['xTipo_cargo'],
            'pTipo_cargo' => $datos['Tipo_cargo'],
			'pxNiveles'=> $datos['xNiveles'],
			'pNiveles'=> $datos['Niveles'],
			'pxVacante'=> $datos['xVacante'],
			'pVacante'=> $datos['Vacante'],
			'pxUrgente'=> $datos['xUrgente'],
			'pUrgente'=> $datos['Urgente'],
            'pxCampo_constante'=> $datos['xCampo_constante'],
            'pCampo_constante'=> $datos['Campo_constante'],
            'porderby'=> $datos['orderby'],
            'plimit'=> $datos['limit']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar el listado . ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
    }


    protected function buscarXCodigo($datos, &$resultado, &$numfilas)
    {
        $spnombre = "sel_solicitudes_coberturas_tipo_xId";
        $sparam = array(
            'ptable'=> BASEDATOS,
			'pId' => $datos['Id']
        
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo . ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
    }


    protected function listarConstantes($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_DocumentosTipos_combo_Constante";

        $sparam=array(

        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


    protected function Modificar($datos)
    {
        $spnombre="upd_solicitudes_coberturas_tipo_xId";

        $sparam=array(
            'ptable'=> BASEDATOS,
            'pIdTipoCargo' => $datos['Tipo_cargo'],
			'pIdNivel'=> $datos['Niveles'],
			'pVacante'=> $datos['Vacante'],
			'pUrgente'=> $datos['Urgente'],
            'pConstanteTipoDocumento'=> $datos['Campo_constante'],
            'pId'=> $datos['IdSolicitud']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al actualizar la configuracion. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


    protected function Insertar($datos, &$codigoinsertado)
    {
        $spnombre = "ins_solicitudes_coberturas_tipo";

        $sparam = array(
            'ptable'=> BASEDATOS,
            'pIdTipoCargo' => $datos['Tipo_cargo'],
			'pIdNivel'=> $datos['Niveles'],
			'pVacante'=> $datos['Vacante'],
			'pUrgente'=> $datos['Urgente'],
            'pConstanteTipoDocumento'=> $datos['Campo_constante'],
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje(
                $this->conexion,
                MSG_ERRGRAVE,
                "Error al registrar la configuracion de la constante.",
                array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__),
                array("formato" => $this->formato)
            );
            return false;
        }

        $codigoinsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }





}