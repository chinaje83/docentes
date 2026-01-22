<?php
abstract class cLicenciasdb
{

    /** @var accesoBDLocal  */
    protected $conexion;
    /** @var mixed  */
    protected $formato;
    /** @var array  */
    protected $error;

    function __construct(accesoBDLocal $conexion,$formato){

        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }


    function __destruct(){
    }




	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Licencias_busqueda_avanzada";
		$sparam=array(
			'pBaseLicencias'=> BASEDATOSLICENCIAS,
			'pTaskId'=> $datos['TaskId'],
			'plimit'=> $datos['limit'],
			'porderby'=> $datos['orderby']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
    protected function BuscarLicenciasAfectadasxIdPofa($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_LicenciasCargosxIdPofa";
        $sparam=array(
            'pBaseLicencias'=> BASEDATOSLICENCIAS,
            'pIdPofa'=>$datos['IdPofa']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar Licencias afectadas por el IdPOFAAA. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    protected function InactivarLicenciasCargosxIdPofa($datos)
    {
        $spnombre="upd_BajaLicenciasCargosxIdPofaxIdLicencias";
        $sparam=array(
            'pBaseLicencias'=> BASEDATOSLICENCIAS,
            'pIdPofa'=> $datos['IdPofa'],
            'pIdLicencias'=> $datos['IdLicencias'],
            'pUltimaModificacionUsuario'=>  $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pBajaFecha'=> $datos['BajaFecha']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al dar de baja licenciasCargos ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }
    protected function InsertarLicenciasCargos($datos, &$codigoinsertado)
    {
        $spnombre="ins_LicenciasCargosCopiarIdPofa";
        $sparam=array(
            'pBaseLicencias'=> BASEDATOSLICENCIAS,
            'pIdPofa'=> $datos['IdPofa'],
             'pIdPofaNueva'=> $datos['pIdPofaNueva'],
            'pIdLicencias'=> $datos['IdLicencias']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar nueva POFA ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        $codigoinsertado = $this->conexion->UltimoCodigoInsertado();

        return true;
    }


    protected function BuscarLicenciasESPorId($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_Licencias_es_xId";
        $sparam=array(
            'pBaseLicencias'=> BASEDATOSLICENCIAS,
            'pId'=> $datos['Id']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }
}
?>
