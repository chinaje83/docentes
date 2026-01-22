<?php 

include(DIR_CLASES_DB."cDocumentosCargosReemplazo.db.php");

class cDocumentosCargosReemplazo extends cDocumentosCargosReemplazodb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}


	
	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	public function BuscarxIdDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function InsertarCargosReemplazo($datos)
	{

		if (isset($datos['CargosSeleccionadosDniReemplazo']) && is_array($datos['CargosSeleccionadosDniReemplazo']))
		{
			foreach($datos['CargosSeleccionadosDniReemplazo'] as $ReemplazoSecuencias)
			{
				$datosInsertar = $ReemplazoSecuencias;
				$datosInsertar['IdDocumento'] = $datos['IdDocumento'];
				if (isset($datos['AgenteRevista']) && $datos['AgenteRevista']!="")
					$datosInsertar['Revista'] = $datos['AgenteRevista'];
				if(!$this->Insertar($datosInsertar))
					return false;
				
			}
		}
		
		return true;
	}

    public function ModificarSecuenciaSubsecuenciaCargosReemplazo($datos)
    {


        if (isset($datos['CargosSeleccionadosSecuenciaSubsecuenciaDniReemplazo']) && is_array($datos['CargosSeleccionadosSecuenciaSubsecuenciaDniReemplazo']))
        {
            foreach ($datos['CargosSeleccionadosSecuenciaSubsecuenciaDniReemplazo'] as $ReemplazoSecuencias)
            {

                $datosModif['Secuencia'] = $datos['CargoSecuencia_' . $ReemplazoSecuencias['SecuenciaReemplazo'] . '_' . $ReemplazoSecuencias['SubSecuenciaReemplazo'] . "_" . $ReemplazoSecuencias['RealIntOut']];
                $datosModif['SubSecuencia'] = $datos['CargoSubSecuencia_' . $ReemplazoSecuencias['SecuenciaReemplazo'] . '_' . $ReemplazoSecuencias['SubSecuenciaReemplazo'] . "_" . $ReemplazoSecuencias['RealIntOut']];
                $datosModif['SecuenciaReemplazo'] = $ReemplazoSecuencias['SecuenciaReemplazo'];
                $datosModif['SubSecuenciaReemplazo'] = $ReemplazoSecuencias['SubSecuenciaReemplazo'];
                $datosModif['RealIntOut'] = $ReemplazoSecuencias['RealIntOut'];
                $datosModif['IdDocumento'] = $datos['IdDocumento'];
                if (!$this->ModificiarSecuenciaSubsecuencia($datosModif))
                    return false;

            }
        }

        return true;

    }




	
	public function Insertar($datos)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		
		if (!parent::Insertar($datos))
			return false;
		
		$oAuditoriasDocumentosCargosReemplazo = new cAuditoriasDocumentosCargosReemplazo($this->conexion,$this->formato);
		$datos['Accion'] = INSERTAR;
		if(!$oAuditoriasDocumentosCargosReemplazo->InsertarLog($datos,$codigoInsertadolog))
			return false;
		
		return true;
	}



    public function ModificiarSecuenciaSubsecuencia($datos)
    {
        if (!$this->_ValidarModificar($datos))
            return false;

        $this->_SetearNull($datos);

        if (!parent::ModificiarSecuenciaSubSecuencia($datos))
            return false;

        $oAuditoriasDocumentosCargosReemplazo = new cAuditoriasDocumentosCargosReemplazo($this->conexion,$this->formato);
        $datos['Accion'] = MODIFICACION;
        if(!$oAuditoriasDocumentosCargosReemplazo->InsertarLog($datos,$codigoInsertadolog))
            return false;

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

    private function _ValidarModificar($datos)
    {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        return true;
    }
	

	
	private function _ValidarDatosVacios($datos)
	{

		return true;
	}
	
	
	private function _SetearNull(&$datos)
	{
		if (!isset($datos['Secuencia']) || $datos['Secuencia']=="")
			$datos['Secuencia']="NULL";
			
		if (!isset($datos['SubSecuencia']) || $datos['SubSecuencia']=="")
			$datos['SubSecuencia']="NULL";	
			
		if (!isset($datos['RealIntOut']) || $datos['RealIntOut']=="")
			$datos['RealIntOut']="NULL";	
			
		if (!isset($datos['ModalidadCarrera']) || $datos['ModalidadCarrera']=="")
			$datos['ModalidadCarrera']="NULL";	
			
		if (!isset($datos['Asignatura']) || $datos['Asignatura']=="")
			$datos['Asignatura']="NULL";			
			
		if (!isset($datos['Area']) || $datos['Area']=="")
			$datos['Area']="NULL";	
			
		if (!isset($datos['CargoCodigo']) || $datos['CargoCodigo']=="")
			$datos['CargoCodigo']="NULL";
			
		if (!isset($datos['CargoDescripcion']) || $datos['CargoDescripcion']=="")
			$datos['CargoDescripcion']="NULL";
		
		if (!isset($datos['GrupoCodigo']) || $datos['GrupoCodigo']=="")
			$datos['GrupoCodigo']="NULL";
			
		if (!isset($datos['GrupoDescripcion']) || $datos['GrupoDescripcion']=="")
			$datos['GrupoDescripcion']="NULL";
		
		
		if (!isset($datos['SubGrupoCodigo']) || $datos['SubGrupoCodigo']=="")
			$datos['SubGrupoCodigo']="NULL";
			
		if (!isset($datos['SubGrupoDescripcion']) || $datos['SubGrupoDescripcion']=="")
			$datos['SubGrupoDescripcion']="NULL";
		
		
		if (!isset($datos['RegimenEstatutarioCodigo']) || $datos['RegimenEstatutarioCodigo']=="")
			$datos['RegimenEstatutarioCodigo']="NULL";
			
		if (!isset($datos['RegimenEstatutarioDescripcion']) || $datos['RegimenEstatutarioDescripcion']=="")
			$datos['RegimenEstatutarioDescripcion']="NULL";
		
		if (!isset($datos['CargoHsMod']) || $datos['CargoHsMod']=="")
			$datos['CargoHsMod']="NULL";	

		if (!isset($datos['CargoEnsenanza']) || $datos['CargoEnsenanza']=="")
			$datos['CargoEnsenanza']="NULL";	
		
		if (!isset($datos['Anio']) || $datos['Anio']=="")
			$datos['Anio']="NULL";	

		if (!isset($datos['Seccion']) || $datos['Seccion']=="")
			$datos['Seccion']="NULL";	
		
		if (!isset($datos['IdTurno']) || $datos['IdTurno']=="")
			$datos['IdTurno']="NULL";	
		
		if (!isset($datos['HsDesignacion']) || $datos['HsDesignacion']=="")
			$datos['HsDesignacion']="NULL";	
		
		if (!isset($datos['HsDesignacionDescripcion']) || $datos['HsDesignacionDescripcion']=="")
			$datos['HsDesignacionDescripcion']="NULL";

        if (!isset($datos['Tipo']) || $datos['Tipo']=="")
            $datos['Tipo']="NULL";

        if (!isset($datos['CodigoMovimiento']) || $datos['CodigoMovimiento']=="")
            $datos['CodigoMovimiento']="NULL";
			
		return true;
	}


}
?>