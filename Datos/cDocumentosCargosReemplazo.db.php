<?php 
abstract class cDocumentosCargosReemplazodb
{


	function __construct(){}

	function __destruct(){}

	
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosCargosReemplazo_IdDocumento_Secuencia_SubSecuencia";
		$sparam=array(
			'pIdDocumento'=> $datos['IdDocumento'],
			'pSecuenciaReemplazo'=> $datos['SecuenciaReemplazo'],
			'pSubSecuenciaReemplazo'=> $datos['SubSecuenciaReemplazo'],
			'pRealIntOut'=> $datos['RealIntOut']
			);	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los cargos del reemplazante por documentos. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	protected function BuscarxIdDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosCargosReemplazo_xIdDocumento";
		$sparam=array(
			'pIdDocumento'=> $datos['IdDocumento']
			);	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los cargos del reemplazante por documentos. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

    protected function Insertar($datos)
    {

		$spnombre="ins_DocumentosCargosReemplazo";
		$sparam=array(
			'pIdDocumento'=> $datos['IdDocumento'],
			'pSecuencia'=> $datos['Secuencia'],
			'pSubSecuencia'=> $datos['SubSecuencia'],
			'pRevista'=> $datos['Revista'],
			'pRealIntOut'=> $datos['RealIntOut'],
			'pModalidadCarrera'=> $datos['ModalidadCarrera'],
			'pAsignatura'=> $datos['Asignatura'],
			'pArea'=> $datos['Area'],
			'pCargoCodigo'=> $datos['CargoCodigo'],
			'pCargoDescripcion'=> $datos['CargoDescripcion'],
			'pGrupoCodigo'=> $datos['GrupoCodigo'],
			'pGrupoDescripcion'=> $datos['GrupoDescripcion'],
			'pSubGrupoCodigo'=> $datos['SubGrupoCodigo'],
			'pSubGrupoDescripcion'=> $datos['SubGrupoDescripcion'],
			'pRegimenEStatutarioCodigo'=> $datos['RegimenEStatutarioCodigo'],
			'pRegimenEStatutarioDescripcion'=> $datos['RegimenEStatutarioDescripcion'],
			'pCargoHsMod'=> $datos['CargoHsMod'],
			'pCargoEnsenanza'=> $datos['CargoEnsenanza'],
			'pAnio'=> $datos['Anio'],
			'pSeccion'=> $datos['Seccion'],
			'pIdTurno'=> $datos['IdTurno'],
			'pHsDesignacion'=> $datos['HsDesignacion'],
			'pHsDesignacionDescripcion'=> $datos['HsDesignacionDescripcion'],
			'pTipo'=> $datos['Tipo'],
			'pCodigoMovimiento'=> $datos['CodigoMovimiento'],
			'pSecuenciaReemplazo'=> $datos['SecuenciaReemplazo'],
			'pSubSecuenciaReemplazo'=> $datos['SubSecuenciaReemplazo'],
			'pCuilAlta'=> $datos['CuilAlta'],
			'pEscalafonAlta'=> $datos['EscalafonAlta'],
			'pClaveEscuelaAlta'=> $datos['ClaveEscuelaAlta'],
			'pAltaApp'=> $datos['AltaApp'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pHashDato'=> $datos['HashDato']
			);
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el cargo del reemplazante al documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }


        return true;
    }

    protected function ModificiarSecuenciaSubSecuencia($datos)
    {

        $spnombre="upd_DocumentosCargosReemplazo_Secuencia_SubSecuencia_xIdDocumento_RealIntOut_SecuenciaReemplazo_SubSecuenciaReemplazo";
        $sparam=array(
            'pSecuencia'=> $datos['Secuencia'],
            'pSubSecuencia'=> $datos['SubSecuencia'],
            'pIdDocumento'=> $datos['IdDocumento'],
            'pRealIntOut'=> $datos['RealIntOut'],
            'pSecuenciaReemplazo'=> $datos['SecuenciaReemplazo'],
            'pSubSecuenciaReemplazo'=> $datos['SubSecuenciaReemplazo']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el cargo del reemplazante al documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }


        return true;
    }


	

}
?>