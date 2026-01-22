<?php 
include(DIR_CLASES_DB."cLicenciasAnuladas.db.php");

class cLicenciasAnuladas extends cLicenciasAnuladasdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	
	public function BuscarxTaskIdxFechaModificacion($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxTaskIdxFechaModificacion($datos,$resultado,$numfilas))
			return false;
		return true;	
	}
	
	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xTaskId'=> 0,
			'TaskId'=> "",
			'limit'=> '',
			'orderby'=> "IdRegistro DESC"
		);

		if(isset($datos['TaskId']) && $datos['TaskId']!="")
		{
			$sparam['TaskId']= $datos['TaskId'];
			$sparam['xTaskId']= 1;
		}

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function InsertBulk($datos)
	{
		if(isset($datos) && count($datos)>0)
		{
			//$datosBuscar = array();
			//if(!$this->BusquedaAvanzada($datosBuscar,$resultado,$numfilas))
			//	return false;
			
			$ArrayLicencias = array();
			//while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
			//	$ArrayLicencias[$fila['TaskId']] = $fila['TaskId']; 
			
			
			$ArrayEstablecimientos = array();
			$queryLicencias = "INSERT INTO ".BASEDATOSLICENCIAS.".`LicenciasAnuladas` (
				  `TaskId`,
				  `Tipo`,
				  `Cuil`,
				  `Tenant`,
				  `FechaCreacion`,
				  `FechaInicio`,
				  `FechaFin`,
				  `FechaModificacion`,
				  `Doc`,
				  `Aux`,
				  `DuracionLicencia`,
				  `StatusLicencia`,
				  `StatusTarea`,
				  `Familia`,
				  `Adecuacion`,
				  `CuilUsuarioModificacion`,
				  `CuilUsuarioOriginador`,
				  `CuilUsuarioReview`,
				  `DatosJsonEstablecimientos`,
				  `IdEstadoProceso`,
				  `Procesada` 
				)  VALUES ";

				$valuesLicencias="";
				
				foreach($datos as $key=>$datosInsertar)
				{
					if(!array_key_exists($datosInsertar['TaskId'],$ArrayLicencias))
					{
						$this->_SetearNull($datosInsertar);

						$valuesLicencias.='(
							"'.$datosInsertar['TaskId'].'",
							"'.$datosInsertar['Tipo'].'",
							"'.$datosInsertar['Cuil'].'",
							"'.$datosInsertar['Tenant'].'",
							"'.$datosInsertar['FechaCreacion'].'",
							"'.$datosInsertar['FechaInicio'].'",
							"'.$datosInsertar['FechaFin'].'",
							"'.$datosInsertar['FechaModificacion'].'",
							"'.$datosInsertar['Doc'].'",
							"'.$datosInsertar['Aux'].'",
							"'.$datosInsertar['DuracionLicencia'].'",
							"'.$datosInsertar['StatusLicencia'].'",
							"'.$datosInsertar['StatusTarea'].'",
							"'.$datosInsertar['Familia'].'",
							"'.$datosInsertar['Adecuacion'].'",
							"'.$datosInsertar['CuilUsuarioModificacion'].'",
							"'.$datosInsertar['CuilUsuarioOriginador'].'",
							"'.$datosInsertar['CuilUsuarioReview'].'",
						';

						if(isset($datosInsertar['Establecimientos']) && count($datosInsertar['Establecimientos'])>0)
						{	
							$j=0;	
							foreach($datosInsertar['Establecimientos'] as $key2=>$datosEstablecimientos)
							{
								$ArrayEstablecimientos[$j]['ClaveEscuela']= $datosEstablecimientos['ClaveEscuela'];
								$j++;
							}


						}
						
						$Establecimientos =NULL;
						if(isset($ArrayEstablecimientos) && count($ArrayEstablecimientos)>0)
							$Establecimientos = json_encode($ArrayEstablecimientos);
						
						$valuesLicencias.='
						"'.$Establecimientos.'",
						"'.$datosInsertar['IdEstadoProceso'].'",
						"'.$datosInsertar['Procesada'].'"
							
						),';	
					}
							
				}
			
				if($valuesLicencias!="")
				{	

					$valuesLicencias = substr($valuesLicencias, 0, strlen($valuesLicencias) - 1);
					$queryLicenciasCompleto = $queryLicencias.$valuesLicencias.";";

					$queryLicenciasCompleto = str_replace('"NULL"',"NULL",$queryLicenciasCompleto);
					
					$queryLicenciasCompleto = str_replace('"[','\'[',$queryLicenciasCompleto);
					$queryLicenciasCompleto = str_replace(']"',']\'',$queryLicenciasCompleto);
					$queryLicenciasCompleto = str_replace('true','1',$queryLicenciasCompleto);
					$queryLicenciasCompleto = str_replace('false','0',$queryLicenciasCompleto);

					
					$erroren="";
					if(!$this->conexion->_EjecutarQuery($queryLicenciasCompleto,$erroren,$resultado,$errno))
					{	
						FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el bulk de licencias.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
						return false;
					}
				}
			
		}
	
		return true;
	}
	
	
	public function ModificarIdEstadoProcesoxTaskIdxFechaModificacion($datos)
	{
		if (!parent::ModificarIdEstadoProcesoxTaskIdxFechaModificacion($datos))
			return false;
		return true;
	}
	
	public function ModificarProcesadaxTaskIdxFechaModificacion($datos)
	{
		if (!parent::ModificarProcesadaxTaskIdxFechaModificacion($datos))
			return false;
		return true;
	}
	
	private function _SetearNull(&$datos)
	{
		
		if (!isset($datos['TaskId']) || $datos['TaskId']=="")
			$datos['TaskId']="NULL";
			
		if (!isset($datos['Tipo']) || $datos['Tipo']=="")
			$datos['Tipo']="NULL";			

		if (!isset($datos['Cuil']) || $datos['Cuil']=="")
			$datos['Cuil']="NULL";

		if (!isset($datos['Tenant']) || $datos['Tenant']=="")
			$datos['Tenant']="NULL";

		if (!isset($datos['FechaCreacion']) || $datos['FechaCreacion']=="")
			$datos['FechaCreacion']="NULL";

		if (!isset($datos['FechaInicio']) || $datos['FechaInicio']=="")
			$datos['FechaInicio']="NULL";

		if (!isset($datos['FechaFin']) || $datos['FechaFin']=="")
			$datos['FechaFin']="NULL";

		if (!isset($datos['FechaModificacion']) || $datos['FechaModificacion']=="")
			$datos['FechaModificacion']="NULL";

		if (!isset($datos['Doc']) || $datos['Doc']=="")
			$datos['Doc']="NULL";

		if (!isset($datos['Aux']) || $datos['Aux']=="")
			$datos['Aux']="NULL";

		if (!isset($datos['DuracionLicencia']) || $datos['DuracionLicencia']=="")
			$datos['DuracionLicencia']="NULL";

		if (!isset($datos['StatusLicencia']) || $datos['StatusLicencia']=="")
			$datos['StatusLicencia']="NULL";

		if (!isset($datos['StatusTarea']) || $datos['StatusTarea']=="")
			$datos['StatusTarea']="NULL";

		if (!isset($datos['CuilUsuarioModificacion']) || $datos['CuilUsuarioModificacion']=="")
			$datos['CuilUsuarioModificacion']="NULL";
		
		if (!isset($datos['CuilUsuarioOriginador']) || $datos['CuilUsuarioOriginador']=="")
			$datos['CuilUsuarioOriginador']="NULL";
		
		if (!isset($datos['CuilUsuarioReview']) || $datos['CuilUsuarioReview']=="")
			$datos['CuilUsuarioReview']="NULL";
		
		if (!isset($datos['Procesada']) || $datos['Procesada']=="")
			$datos['Procesada']="0";
		
		
		return true;
	}
	
	

}
?>