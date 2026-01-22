<?php 
abstract class cDocumentosRelacionadosdb
{


	function __construct(){}

	function __destruct(){}


	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
        $spnombre="sel_DocumentosRelacionados_xIdDocumento_IdDocumentoRelacionado";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento'],
            'pIdDocumentoRelacionado'=> $datos['IdDocumentoRelacionado']
        );
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarxIdDocumento($datos,&$resultado,&$numfilas)
	{
        $spnombre="sel_DocumentosRelacionados_xIdDocumento";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento']
        );
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function Insertar($datos)
	{
        $spnombre="ins_DocumentosRelacionados";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento'],
            'pIdDocumentoRelacionado'=> $datos['IdDocumentoRelacionado'],
            'pAltaFecha'=> $datos['AltaFecha'],
            'pAltaUsuario'=> $datos['AltaUsuario'],
            'pAltaApp'=> $datos['AltaApp'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionApp'=> $datos['UltimaModificacionApp']
        );
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}



		return true;
	}

    protected function Eliminar($datos)
    {
        $spnombre="del_DocumentosRelacionados_xIdDocumento_IdDocumentoRelacionado";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento'],
            'pIdDocumentoRelacionado'=> $datos['IdDocumentoRelacionado']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {

            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }
	
	
	protected function EliminarxIdDocumento($datos)
	{
        $spnombre="del_DocumentosRelacionados_xIdDocumento";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento']
        );
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}








}
?>