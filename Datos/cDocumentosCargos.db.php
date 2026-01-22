<?php 
abstract class cDocumentosCargosdb
{


	function __construct(){}

	function __destruct(){}

	
	
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosCargos_IdDocumento_Secuencia_SubSecuencia";
		$sparam=array(
			'pIdDocumento'=> $datos['IdDocumento'],
			'pSecuencia'=> $datos['Secuencia'],
			'pSubSecuencia'=> $datos['SubSecuencia'],
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
		$spnombre="sel_DocumentosCargos_IdDocumento";
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

    protected function Insertar($datos,&$codigoinsertado)
    {

        $spnombre="ins_DocumentosCargos";
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
            'pRegimenEstatutarioCodigo'=> $datos['RegimenEstatutarioCodigo'],
            'pRegimenEstatutarioDescripcion'=> $datos['RegimenEstatutarioDescripcion'],
            'pCargoHsMod'=> $datos['CargoHsMod'],
            'pCargoEnsenanza'=> $datos['CargoEnsenanza'],
            'pAnio'=> $datos['Anio'],
            'pSeccion'=> $datos['Seccion'],
            'pIdTurno'=> $datos['IdTurno'],
            'pHsDesignacion'=> $datos['HsDesignacion'],
            'pHsDesignacionDescripcion'=> $datos['HsDesignacionDescripcion'],
            'pTipo'=> $datos['Tipo'],
            'pCodigoMovimiento'=> $datos['CodigoMovimiento'],
            'pCuilAlta'=> $datos['CuilAlta'],
            'pEscalafonAlta'=> $datos['EscalafonAlta'],
            'pClaveEscuelaAlta'=> $datos['ClaveEscuelaAlta'],
            'pAltaApp'=> $datos['AltaApp'],
            'pAltaFecha'=> $datos['AltaFecha'],
            'pHashDato'=> $datos['HashDato']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            echo "aca";die;
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el cargo al documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        $codigoinsertado=$this->conexion->UltimoCodigoInsertado();

        return true;
    }
	
	
	protected function Eliminar($datos)
	{
		$spnombre="del_DocumentosCargos_xIdDocumento_xSecuencia_xSubSecuencia";
		$sparam=array(
			'pIdDocumento'=> $datos['IdDocumento'],
			'pSecuencia'=> $datos['Secuencia'],
			'pSubSecuencia'=> $datos['SubSecuencia'],
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