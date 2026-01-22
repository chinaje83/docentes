<?php 
abstract class cDocumentosLicenciasReadecuadasdb
{


	function __construct(){}

	function __destruct(){}

	
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
        $spnombre="sel_DocumentosLicenciasReadecuadas_xIdDocumento_Secuencia_RealIntOut";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento'],
            'pSecuencia'=> $datos['Secuencia'],
            'pRealIntOut'=> $datos['RealIntOut']
        );
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los cargos por documentos. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	

	protected function BuscarxIdDocumento($datos,&$resultado,&$numfilas)
	{
        $spnombre="sel_DocumentosLicenciasReadecuadas_xIdDocumento";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento']
        );
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los cargos por documentos. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    protected function Insertar($datos)
    {

        $spnombre="ins_DocumentosLicenciasReadecuadas";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento'],
            'pClaveEscuela'=> $datos['ClaveEscuela'],
            'pClaveEscuelaDestino'=> $datos['ClaveEscuelaDestino'],
            'pPeriodoFechaDesde'=> $datos['PeriodoFechaDesde'],
            'pPeriodoFechaHasta'=> $datos['PeriodoFechaHasta'],
            'pSecuencia'=> $datos['Secuencia'],
            'pRealIntOut'=> $datos['RealIntOut'],
            'pCuil'=> $datos['Cuil'],
            'pDNI'=> $datos['DNI'],
            'pAltaApp'=> $datos['AltaApp'],
            'pUltimaModificacionApp'=> $datos['UltimaModificacionApp'],
            'pAltaUsuario'=> $datos['AltaUsuario'],
            'pAltaFecha'=> $datos['AltaFecha'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
        );
        //print_r($sparam);die;

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el cargo al documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }


        return true;
    }

    protected function ModificarClaveEscuelaDestinoxIdDocumento($datos)
    {
        $spnombre="upd_DocumentosLicenciasReadecuadas_ClaveEscuelaDestino_xIdDocumento";
        $sparam=array(
            'pClaveEscuelaDestino'=> $datos['ClaveEscuelaDestino'],
            'pUltimaModificacionApp'=> $datos['UltimaModificacionApp'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pIdDocumento'=> $datos['IdDocumento']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }
	
	
	protected function Eliminar($datos)
	{
        $spnombre="del_DocumentosLicenciasReadecuadas_xIdDocumento";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento'],
            'pSecuencia'=> $datos['Secuencia'],
            'pRealIntOut'=> $datos['RealIntOut']
        );
		
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo del cargo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}






}
?>