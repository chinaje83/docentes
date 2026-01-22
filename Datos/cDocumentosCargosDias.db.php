<?php 
abstract class cDocumentosCargosDiasdb
{


	function __construct(){}

	function __destruct(){}

	
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
        $spnombre="sel_DocumentosCargosDias_xIdDocumento_Secuencia_SubSecuencia_Dia_Turno";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento'],
            'pSecuencia'=> $datos['Secuencia'],
            'pSubSecuencia'=> $datos['SubSecuencia'],
            'pDia'=> $datos['Dia'],
            'pTurno'=> $datos['Turno']
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
        $spnombre="sel_DocumentosCargosDias_xIdDocumento";
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

        $spnombre="ins_DocumentosCargosDias";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento'],
            'pSecuencia'=> $datos['Secuencia'],
            'pSubSecuencia'=> $datos['SubSecuencia'],
            'pDia'=> $datos['Dia'],
            'pTurno'=> $datos['Turno'],
            'pHoraInicio'=> $datos['HoraInicio'],
            'pHoraFin'=> $datos['HoraFin'],
            'pCuilAlta'=> $datos['CuilAlta'],
            'pEscalafonAlta'=> $datos['EscalafonAlta'],
            'pClaveEscuelaAlta'=> $datos['ClaveEscuelaAlta'],
            'pAltaApp'=> $datos['AltaApp'],
            'pAltaFecha'=> $datos['AltaFecha']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            echo "aca";die;
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el cargo al documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }



        return true;
    }
	
	
	protected function Eliminar($datos)
	{
        $spnombre="del_DocumentosCargosDias_xIdDocumento_Secuencia_SubSecuencia_Dia_Turno";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento'],
            'pSecuencia'=> $datos['Secuencia'],
            'pSubSecuencia'=> $datos['SubSecuencia'],
            'pDia'=> $datos['Dia'],
            'pTurno'=> $datos['Turno']
        );
		
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo del cargo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    protected function EliminarxIdDocumento($datos)
    {
        $spnombre="del_DocumentosCargosDias_xIdDocumento";
        $sparam=array(
            'pIdDocumento'=> $datos['IdDocumento']
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