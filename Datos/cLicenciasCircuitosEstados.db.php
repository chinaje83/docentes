<?php 
abstract class cLicenciasCircuitosEstadosdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_LicenciasCircuitosEstados_xIdNodoWorkflow";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    protected function BuscarxIdCircuito($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_LicenciasCircuitosEstados_xIdCircuito";
        $sparam=array(
            'pIdCircuito'=> $datos['IdCircuito']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    protected function BuscarEstadosNodoInicialxCircuitoBd($datos, &$resultado,&$numfilas)
    {
        $spnombre="sel_LicenciasCircuitosEstados_NodoInicial_xIdCircuito";
        $sparam=array(
            "pIdCircuito" => $datos['IdCircuito']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    protected function InsertarBD($datos,&$codigoinsertado)
    {

        $spnombre="ins_LicenciasCircuitosEstados";
        $sparam=array(
            'pIdCircuito'=> $datos['IdCircuito'],
            'pIdEstado'=> $datos['IdEstado'],
            'pPosicionArriba'=> $datos['PosicionArriba'],
            'pPosicionIzquierda'=> $datos['PosicionIzquierda'],
            'pNodoInicial'=> $datos['NodoInicial'],
            'pNodoGeneral' => $datos['NodoGeneral'],
            'pFechaAlta'=> $datos['FechaAlta'],
            'pAltaUsuario'=> $_SESSION['usuariocod'],
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar un circuito de workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        $codigoinsertado=$this->conexion->UltimoCodigoInsertado();

        return true;
    }

    protected function ModificarPosicion ($datos)
    {
        $spnombre="upd_LicenciasCircuitosEstados_Posicion_xIdNodoWorkflow";
        $sparam=array(
            'pPosicionArriba'=> $datos['PosicionArriba'],
            'pPosicionIzquierda'=> $datos['PosicionIzquierda'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar la posicion de las areas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function ModificarBD ($datos)
    {
        $spnombre="upd_LicenciasCircuitosEstados_xIdNodoWorkflow";
        $sparam=array(
            'pIdEstado'=> $datos['IdEstado'],
            'pNodoInicial'=> $datos['NodoInicial'],
            'pNodoGeneral' => $datos['NodoGeneral'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar la posicion de las areas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


    protected function EliminarDB ($datos)
    {
        $spnombre="del_LicenciasCircuitosEstados_xIdNodoWorkflow";
        $sparam=array(
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar la posicion de las areas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


}
?>