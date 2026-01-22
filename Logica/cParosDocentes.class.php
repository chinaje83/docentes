<?php 
include(DIR_CLASES_DB."cParosDocentes.db.php");

class cParosDocentes extends cParosDocentesdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	
	public function BuscarxClaveEscuelaxFecha($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxClaveEscuelaxFecha($datos,$resultado,$numfilas))
			return false;
		return true;	
	}
	
	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xTaskId'=> 0,
			'TaskId'=> "",
			'limit'=> '',
			'orderby'=> "TaskId DESC"
		);

		if(isset($datos['IdRegistro']) && $datos['IdRegistro']!="")
		{
			$sparam['IdRegistro']= $datos['IdRegistro'];
			$sparam['xIdRegistro']= 1;
		}
		if(isset($datos['IdArea']) && $datos['IdArea']!="")
		{
			$sparam['IdArea']= $datos['IdArea'];
			$sparam['xIdArea']= 1;
		}
		if(isset($datos['IdTipo']) && $datos['IdTipo']!="")
		{
			$sparam['IdTipo']= $datos['IdTipo'];
			$sparam['xIdTipo']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['IdAreaSuperior']) && $datos['IdAreaSuperior']!="")
		{
			$sparam['IdAreaSuperior']= $datos['IdAreaSuperior'];
			$sparam['xIdAreaSuperior']= 1;
		}
		if(isset($datos['RecepcionAutomatica']) && $datos['RecepcionAutomatica']!="")
		{
			$sparam['RecepcionAutomatica']= $datos['RecepcionAutomatica'];
			$sparam['xRecepcionAutomatica']= 1;
		}
		if(isset($datos['TieneBandejaEntrada']) && $datos['TieneBandejaEntrada']!="")
		{
			$sparam['TieneBandejaEntrada']= $datos['TieneBandejaEntrada'];
			$sparam['xTieneBandejaEntrada']= 1;
		}
		if(isset($datos['TieneBandejaSalida']) && $datos['TieneBandejaSalida']!="")
		{
			$sparam['TieneBandejaSalida']= $datos['TieneBandejaSalida'];
			$sparam['xTieneBandejaSalida']= 1;
		}
		if(isset($datos['ModificaCircuito']) && $datos['ModificaCircuito']!="")
		{
			$sparam['ModificaCircuito']= $datos['ModificaCircuito'];
			$sparam['xModificaCircuito']= 1;
		}

		if (!isset($datos['Anio']) || ($datos['Anio']!="" && is_numeric($datos['Anio'])))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) || ($datos['Mes']!="" && is_numeric($datos['Mes'])))
			$datos['Mes'] = date("m");

		$sparam['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";

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
			$queryLicencias = "INSERT INTO ".BASEDATOSLICENCIAS.".`Licencias` (
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
				  `IdEstadoProceso`
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
						"'.$datosInsertar['IdEstadoProceso'].'"
							
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
		
		
		return true;
	}
	
	function ObtenerArrayDb2($IdDocumento,$datosNovedad,&$arrayDb2)
	{
			$DOCUMENTOUSUARIOCARGA = substr($_SESSION['Cuil'],3,8);
			$caractRevOut = "";
			$secuencia = "";
			$subsecuencias = "";
			$i = 0;
			foreach($datosNovedad['cargos'] as $fila)
			{
				$secuencia = $fila['secuenciaOut'];
				$caractRevOut = $fila['caractRevOut'];
				
				if ($fila['area']=="" && $fila['anio']=="" && $fila['letraSeccion']=="")
					$subsecuencias .= "";
				else{
					$fila['area'] = substr($fila['area'],0,3);
					$fila['anio'] = str_pad($fila['anio'],2,"0",STR_PAD_LEFT);
					$fila['letraSeccion'] = str_pad($fila['letraSeccion'],2," ",STR_PAD_LEFT);
					$subsecuencias .= $fila['area'].$fila['anio'].$fila['letraSeccion'];
					$i++;
				}
				//if ($i==5)
				//	break;
			}
			$datosNovedadDb2['IDNOVEDADSUNA'] = $IdDocumento;
			$datosNovedadDb2['DOCUMENTO'] = $datosNovedad["AgenteDNI"];
			$datosNovedadDb2['SEXO'] = $datosNovedad["AgenteSexo"];
			$datosNovedadDb2['SECUENCIA'] = $secuencia;
			$datosNovedadDb2['CARACREV'] = $caractRevOut;
			$datosNovedadDb2['REGESTAT'] = $datosNovedad['RegimenEstatutarioCodigo'];
			$datosNovedadDb2['CLAVEESTAB'] = $_SESSION['ClaveEscuela'];
			$datosNovedadDb2['ENCUADRE'] = $datosNovedad['LicenciaEncuadreArticulo'];
			$datosNovedadDb2['FECHADESDE'] = date("Ymd",strtotime($datosNovedad['PeriodoFechaDesde']));
			$datosNovedadDb2['FECHAHASTA'] = date("Ymd",strtotime($datosNovedad['PeriodoFechaHasta']));
			$datosNovedadDb2['HSDESIG'] = ($datosNovedad['InasistenciaHsDesignadas']==""?0:$datosNovedad['InasistenciaHsDesignadas']);
			$datosNovedadDb2['HSTRAB'] = ($datosNovedad['InasistenciaHsTrabajadas']==""?0:$datosNovedad['InasistenciaHsTrabajadas']);
			$datosNovedadDb2['HSINASISTIDAS'] = ($datosNovedad['InasistenciaHsDescontadas']==""?0:$datosNovedad['InasistenciaHsDescontadas']);
			$datosNovedadDb2['DOCUMENTOUSUARIOCARGA'] = $DOCUMENTOUSUARIOCARGA;
			$datosNovedadDb2['fechaultmodif'] = date("Y-m-d H:i:s");
			$datosNovedadDb2['ESTADO'] = "1";
			$datosNovedadDb2['OBSERVACION'] = "";
			$datosNovedadDb2['SUBSECUENCIAS'] = $subsecuencias;
			$datosNovedadDb2['RECTIFICLIC'] = "";
			$datosNovedadDb2['TASKID'] = "";
		
			$arrayDb2[] = $datosNovedadDb2;
			return true;
		
	}
	
	public function InsertarNovedadParo(&$datosNovedad,$persona,$secuencia,$datosEscuela,&$IdDocumento){
		
		$this->_ArmarArrayNovedadParo($datosNovedad,$persona,$secuencia,$datosEscuela);
		
		//$NovedadesInsertar[]=$datosNovedad;

	   $sqlNovedad='
                              INSERT INTO Documentos (
                                ClaveEscuela,
                                IdCDependencia,
                                IdDistrito,
                                IdTipoOrganismo,
                                IdEscuela,
                                IdEnsenianza,

                                IdTipoDocumento,
                                NombreTipoDocumento,
                                TieneCUPOF,
                                ObservacionesCUPOF,
                                CUPOF,
                                AgenteCuil,
                                AgenteDNI,
                                AgenteNombre,
                                AgenteApellido,
                                AgenteFNacimiento,
                                AgenteSexo, 
                               
                                PeriodoFechaDesde,
                                PeriodoFechaHasta,
                                
                                InasistenciaHsDesignadas, 
                                InasistenciaHsTrabajadas, 
                                InasistenciaHsDescontadas, 
                               
                                LicenciaEncuadreArticulo,
                                LicenciaEncuadreInciso,
                                
                              
                                Observaciones,
                               
                                
                                IdArea,
                                IdRegistroTipoDocumento,
                                IdEstado,
                                IdAreaInicial,
                                IdEstadoInicial,
								
								MovimientoFecha,
                                UltimaModificacionCuil,
                                UltimaModificacionEscalafon,
                                UltimaModificacionClaveEscuela,
                                CuilAlta,
                                EscalafonAlta,
                                ClaveEscuelaAlta,
                                AltaApp,
                                UltimaModificacionApp,
                                AltaFecha,
                                FechaEnvio,
                                UltimaModificacionFecha
                              ) 
                              VALUES
                                (
                                  "'.$_SESSION['ClaveEscuela'].'",
                                  "'.$datosNovedad['IdCDependencia'].'",
                                  "'.$datosNovedad['IdDistrito'].'",
                                  "'.$datosNovedad['IdTipoOrganismo'].'",
                                  "'.$datosNovedad['IdEscuela'].'",
                                  "'.$datosNovedad['IdEnsenianza'].'",

                                  "'.$datosNovedad["IdTipoDocumento"].'",
                                  "'.$datosNovedad['NombreTipoDocumento'].'",
                                  "0",
                                  "",
                                  "",
                                  "'.$datosNovedad['AgenteCuil'].'",
                                  "'.$datosNovedad["AgenteDNI"].'",
                                  "'.$datosNovedad["AgenteNombre"].'",
                                  "",
                                  "'.$datosNovedad["AgenteFNacimiento"].'",
                                  "'.$datosNovedad["AgenteSexo"].'", 
                                  
                                  "'.$datosNovedad["PeriodoFechaDesde"].'",
                                  "'.$datosNovedad["PeriodoFechaHasta"].'",
                                  
                                  "'.$datosNovedad["InasistenciaHsDesignadas"] .'",
                                  "'.$datosNovedad["InasistenciaHsTrabajadas"].'",
                                  "'.$datosNovedad["InasistenciaHsDescontadas"].'",
                                  
                              
                                  "'.$datosNovedad["LicenciaEncuadreArticulo"].'",
                                  "'.$datosNovedad["LicenciaEncuadreInciso"].'",
                                  
                                  "Paro docente automatico",
                              
                              
                                  "'.$datosNovedad['IdArea'].'",
                                  "'.$datosNovedad['IdRegistroTipoDocumento'].'",
                                  "'.$datosNovedad['IdEstado'].'",
                                  "'.$datosNovedad['IdArea'].'",
                                  "'.$datosNovedad['IdEstadoInicial'].'",
								  "'.$datosNovedad['AltaFecha'].'",
                                  "'.$datosNovedad['UltimaModificacionCuil'].'",
                                  "'.$datosNovedad['UltimaModificacionEscalafon'].'",
                                  "'.$datosNovedad['UltimaModificacionClaveEscuela'].'",
                                  "'.$datosNovedad['CuilAlta'].'",
                                  "'.$datosNovedad['EscalafonAlta'].'",
                                  "'.$datosNovedad['ClaveEscuelaAlta'].'",
                                  "Cliente",
                                  "Cliente",
                                  "'.$datosNovedad['AltaFecha'].'",
                                  "'.$datosNovedad['AltaFecha'].'",
                                  "'.$datosNovedad['AltaFecha'].'"
                                );';
			$erroren=$errno="";
			if(!$this->conexion->_EjecutarQuery($sqlNovedad,$erroren,$resultado,$errno))
			{
				return false;
			}
			//obtengo el ID de novedad
			$IdDocumento=$datosNovedad['IdDocumento']=$this->conexion->UltimoCodigoInsertado();
			return true;
	}
	
	private function _ArmarArrayNovedadParo(&$datosNovedad, $persona,$secuencia,$datosEscuela){

        $datosNovedad["ClaveEscuelaDatos"] =$_SESSION['ClaveEscuela'];
		$datosNovedad["IdEnsenanza"]=$secuencia['Ensenanza'];
		$datosNovedad["AgenteCuil"] =  $datosNovedad['AgenteCuil'];
		$datosNovedad["AgenteDNI"] = $persona["documentoOut"];
		$datosNovedad["AgenteNombre"] = utf8_decode($persona["nombreOut"]);
		$fechaActual=date("Y-m-d");
		$datosNovedad["AgenteFNacimiento"] =  ($persona["fechaNacOut"]==""?$fechaActual:$persona["fechaNacOut"]);
		$datosNovedad["PeriodoFechaDesde"] =   FuncionesPHPLocal::ConvertirFecha($datosNovedad['FechaDesde'],'dd/mm/aaaa','aaaa-mm-dd');
		$datosNovedad["PeriodoFechaHasta"] =   FuncionesPHPLocal::ConvertirFecha($datosNovedad['FechaHasta'],'dd/mm/aaaa','aaaa-mm-dd');
		$datosNovedad["InasistenciaModSem"] =  "";
		$datosNovedad["InasistenciaModMen"] =  "";
		$datosNovedad["AgenteSexo"]=$persona["sexoOut"];
		
									
		//regimen estatutario
		$datosNovedad["RegimenEstatutarioCodigo"] =  $secuencia['RegEstatutario'];
						
		//datos de registro
		$datosNovedad['AltaUsuario']=$_SESSION['usuariocod'];
		$datosNovedad['UltimaModificacionCuil']=$_SESSION['Cuil'];
		$datosNovedad['UltimaModificacionEscalafon']=$_SESSION['IdEscalafon'];
		$datosNovedad['UltimaModificacionClaveEscuela']=$_SESSION['ClaveEscuela'];
		$datosNovedad['CuilAlta']=$_SESSION['Cuil'];
		$datosNovedad['EscalafonAlta']=$_SESSION['IdEscalafon'];
		$datosNovedad['ClaveEscuelaAlta']=$_SESSION['ClaveEscuela'];
		$datosNovedad['AltaFecha'] = $datosNovedad['UltimaModificacionFecha'] =date("Y-m-d H:i:s");
		$datosNovedad['AltaApp']="Cliente";
		$datosNovedad['UltimaModificacionApp']=date("Y-m-d H:i:s");
		if($datosNovedad["RegimenEstatutarioCodigo"]!="D") // es auxiliar - Doc: TL
		{
			$datosNovedad["IdTipoDocumento"] =  DOCPARODOCENTEAUX;
			$datosNovedad['IdArea']=DOCPARODOCENTEAUXAREAINICIAL;
			$datosNovedad['IdEstado']=DOCPARODOCENTEAUXESTADOINICIAL;
			$datosNovedad['IdEstadoInicial']=DOCPARODOCENTEAUXESTADOINICIAL;
			$datosNovedad['IdAreaInicial']=DOCPARODOCENTEAUXAREAINICIAL;
			$datosNovedad['IdEstadoFinal']=DOCPARODOCENTEAUXESTADOFINAL;
			$datosNovedad['IdAreaFinal']=DOCPARODOCENTEAUXAREAFINAL;
			$datosNovedad["LicenciaEncuadreArticulo"] = DOCPARODOCENTEAUXARTICULO; 
			
			$datosNovedad['NombreEstado']=DOCPARODOCENTEDOCESTADOINICIAL;
			$datosNovedad['NombreArea']=DOCPARODOCENTEDOCESTADOINICIAL;
			
			//tipo de doc
			$datosNovedad['IdRegistroTipoDocumento'] = DOCPAROAUXTIPODOCIDREGISTRO;
			$datosNovedad['NombreTipoDocumento'] = DOCPAROAUXTIPODOCNOMBRE;
			$datosNovedad['IdTipoDocumentoPadre'] = DOCPAROAUXTIPODOCPADRE;
			
			$datosNovedad['IdTipoDocumentoClasificacion'] = DOCPAROAUXTIPODOCCLASIF;
			$datosNovedad['IdTipoDocumentoClasificacionNombre'] = DOCPAROAUXTIPODOCCLASIFNOMBRE;
			
			$datosNovedad['NombreEstado']=DOCPARODOCENTEAUXAREAINICIALNOMBRE;
			$datosNovedad['NombreArea']=DOCPARODOCENTEAUXESTADOINICIALNOMBRE;
		}
		else
		{
			$datosNovedad["IdTipoDocumento"] =  DOCPARODOCENTEDOC;
			$datosNovedad['IdArea']=DOCPARODOCENTEDOCAREAINICIAL;
			$datosNovedad['IdEstado']=DOCPARODOCENTEDOCESTADOINICIAL;
			$datosNovedad['IdEstadoInicial']=DOCPARODOCENTEDOCESTADOINICIAL;
			$datosNovedad['IdAreaInicial']=DOCPARODOCENTEDOCAREAINICIAL;
			$datosNovedad['IdEstadoFinal']=DOCPARODOCENTEDOCESTADOFINAL;
			$datosNovedad['IdAreaFinal']=DOCPARODOCENTEDOCAREAFINAL;
			$datosNovedad["LicenciaEncuadreArticulo"] = DOCPARODOCENTEARTICULO; 
			
			$datosNovedad['IdEstado']=DOCPARODOCENTEDOCESTADOINICIAL;
			
			//tipo de doc
			$datosNovedad['IdRegistroTipoDocumento'] = DOCPARODOCENTETIPODOCIDREGISTRO;
			$datosNovedad['NombreTipoDocumento'] = DOCPARODOCENTETIPODOCNOMBRE;
			$datosNovedad['IdTipoDocumentoPadre'] = DOCPARODOCENTETIPODOCPADRE;
			
			$datosNovedad['IdTipoDocumentoClasificacion'] = DOCPARODOCENTETIPODOCCLASIF;
			$datosNovedad['IdTipoDocumentoClasificacionNombre'] = DOCPARODOCENTETIPODOCCLASIFNOMBRE;
			
			$datosNovedad['NombreEstado']=DOCPARODOCENTEDOCAREAINICIALNOMBRE;
			$datosNovedad['NombreArea']=DOCPARODOCENTEDOCESTADOINICIALNOMBRE;
		}
		
		//seteo los datos para la novedad
		
		$datosNovedad["LicenciaEncuadreInciso"] =  "";

		//hs inasistidas			
		$hsDesignadas=$hsTrabajadas=$secuencia["HsDesignadas"];
		$datosNovedad["InasistenciaHsDesignadas"] =  $hsDesignadas;
		$datosNovedad["InasistenciaHsTrabajadas"] =  $hsTrabajadas;
		$datosNovedad["InasistenciaHsDescontadas"] =  $secuencia["HsInasistidas"];
		
		//$datosNovedad['cuil'] =$cuil;
		$datosNovedad["cargos"]=$secuencia["subsecuencias"];
		
		$datosNovedad['IdDistrito'] = $datosEscuela['IdDistrito'];
		$datosNovedad['IdDistritoNombre'] = $datosEscuela['distrito'];
		$datosNovedad['IdTipoOrganismo'] = $datosEscuela['IdTipoOrganismo'];	
		$datosNovedad['IdTipoOrganismoNombre'] = $datosEscuela['tipoOrganizacion'];
		$datosNovedad['IdEscuela'] =$datosEscuela['IdEscuela'];	;	
		$datosNovedad['IdEscuelaNombre'] = $datosEscuela['nombre'];	
		$datosNovedad['IdCDependencia'] = $datosEscuela['IdCDependencia'];	
		$datosNovedad['IdEnsenianza'] = $datosEscuela['ensenanza'];	
		return true;
		
	}
	
	private function _ArmarArrayCargoParo(&$cargo){
		if ($cargo["anio"]=="")
			$cargo["anio"]="NULL";
		if ($cargo["secuenciaOut"]=="")
			$cargo["secuenciaOut"]="NULL";
		if ($cargo["subsecuenciaOut"]=="")
			$cargo["subsecuenciaOut"]="0";
		
		return true;			
		
	}

	public function ArmarSQLCargoParo($cargo, $datosNovedad){
		
		$this->_ArmarArrayCargoParo($cargo);
		//$NovedadesInsertar[]=$datosNovedad;
	    $sql='INSERT INTO '.BASEDATOS.'.DocumentosCargos (
							IdDocumento,
							Secuencia,
							SubSecuencia,
							Revista,
							RealIntOut,
							ModalidadCarrera,
							Asignatura,
							Area,
							CargoCodigo,
							CargoDescripcion,
							GrupoCodigo, 
							GrupoDescripcion, 
							SubGrupoCodigo, 
							SubGrupoDescripcion, 
							RegimenEstatutarioCodigo,
							RegimenEstatutarioDescripcion,  
							CargoHsMod,
							CargoEnsenanza,
							Anio,
							Seccion,
							IdTurno,
							HsDesignacion,
							HsDesignacionDescripcion,
							CuilAlta,
							EscalafonAlta,
							ClaveEscuelaAlta,
							AltaApp,
							AltaFecha,
							HashDato
						) 
						VALUES
							(
											'.$datosNovedad["IdDocumento"].',
											'.$cargo["secuenciaOut"].',
											'.$cargo["subsecuenciaOut"].',
											"'.$cargo["caractRevOut"].'",
											"'.$cargo["realIntOut"].'",
											"",
											"'.$cargo["asignatura"].'",
											"'.$cargo["area"].'",
											"'.$cargo["cargoCodigo"].'",
											"'.$cargo["cargoDesc"].'",
											"'.$cargo["grupoCodigo"].'",
											"'.$cargo["grupoDesc"].'",
											"'.$cargo["subGrupoCodigo"].'",
											"'.$cargo["subGrupoDesc"].'",
											"'.$cargo["regEstatCodigo"].'",
											"'.$cargo["regEstatDesc"].'",	
											"'.$cargo["cargoHsMod"].'",
											"'.$cargo["cargoEnse"].'",
											'.$cargo["anio"].',
											"'.$cargo["letraSeccion"].'",
											"'.$cargo["turno"].'",
											"'.$cargo["regHorario"].'",
											"'.$cargo["hsDesigDetalle"].'",
											"'.$datosNovedad['CuilAlta'].'",
											"'.$datosNovedad['EscalafonAlta'].'",
											"'.$datosNovedad['ClaveEscuelaAlta'].'",
											"Cliente",
											"'.$datosNovedad['AltaFecha'].'",
											""
							);';	

			return $sql;
	}
	
	public function ArmarSQLNovedadAuditoria($datosNovedad){

	   $sql='
			INSERT INTO '.BASEDATOSAUDITORIAS.'.AuditoriasDocumentos (
				Accion, 
				IdDocumento,
				ClaveEscuela,
				IdCDependencia,
				IdDistrito,
				IdTipoOrganismo,
				IdEscuela,
				IdEnsenianza,
				IdTipoDocumento,
				NombreTipoDocumento,
				TieneCUPOF,
				ObservacionesCUPOF,
				CUPOF,
				AgenteCuil,
				AgenteDNI,
				AgenteNombre,
				AgenteApellido,
				AgenteFNacimiento,
				AgenteSexo, 
				PeriodoFechaDesde,
				PeriodoFechaHasta,
				InasistenciaHsDesignadas, 
				InasistenciaHsTrabajadas, 
				InasistenciaHsDescontadas, 
				LicenciaEncuadreArticulo,
				LicenciaEncuadreInciso,
				Observaciones,
				IdArea,
				IdRegistroTipoDocumento,
				IdEstado,
				IdAreaInicial,
				IdEstadoInicial,
				UltimaModificacionCuil,
				UltimaModificacionEscalafon,
				UltimaModificacionClaveEscuela,
				CuilAlta,
				EscalafonAlta,
				ClaveEscuelaAlta,
				AltaApp,
				UltimaModificacionApp,
				AltaFecha,
				FechaEnvio,
				UltimaModificacionFecha
			) 
			VALUES
			(
					"'.INSERTAR.'",
				'.$datosNovedad["IdDocumento"].',
				  "'.$_SESSION['ClaveEscuela'].'",
				  "'.$datosNovedad['IdCDependencia'].'",
				"'.$datosNovedad['IdDistrito'].'",
											"'.$datosNovedad['IdTipoOrganismo'].'",
											"'.$datosNovedad['IdEscuela'].'",
											"'.$datosNovedad['IdEnsenianza'].'",
											"'.$datosNovedad["IdTipoDocumento"].'",
											"'.$datosNovedad['NombreTipoDocumento'].'",
											"0",
											"",
											"",
											"'.$datosNovedad['cuil'].'",
											"'.$datosNovedad["AgenteDNI"].'",
											"'.$datosNovedad["AgenteNombre"].'",
											"'.$datosNovedad["AgenteNombre"].'",
											"'.$datosNovedad["AgenteFNacimiento"].'",
											"'.$datosNovedad["AgenteSexo"].'", 
											
											"'.$datosNovedad["PeriodoFechaDesde"].'",
											"'.$datosNovedad["PeriodoFechaHasta"].'",
											
											"'.$datosNovedad["InasistenciaHsDesignadas"] .'",
											"'.$datosNovedad["InasistenciaHsTrabajadas"].'",
											"'.$datosNovedad["InasistenciaHsDescontadas"].'",
											
										
											"'.$datosNovedad["LicenciaEncuadreArticulo"].'",
											"'.$datosNovedad["LicenciaEncuadreInciso"].'",
											
											"Paro docente automatico",
										
										
											"'.$datosNovedad['IdArea'].'",
											"'.$datosNovedad['IdRegistroTipoDocumento'].'",
											"'.$datosNovedad['IdEstado'].'",
											"'.$datosNovedad['IdEstadoInicial'].'",
											"'.$datosNovedad['IdEstadoInicial'].'",
											"'.$datosNovedad['UltimaModificacionCuil'].'",
											"'.$datosNovedad['UltimaModificacionEscalafon'].'",
											"'.$datosNovedad['UltimaModificacionClaveEscuela'].'",
											"'.$datosNovedad['CuilAlta'].'",
											"'.$datosNovedad['EscalafonAlta'].'",
											"'.$datosNovedad['ClaveEscuelaAlta'].'",
											"Cliente",
											"Cliente",
											"'.$datosNovedad['AltaFecha'].'",
											"'.$datosNovedad['AltaFecha'].'",
			"'.$datosNovedad['AltaFecha'].'"
		);';
							 
			
		return $sql;
	}
	
	public function ArmarSQLCargoAuditoriaParo($cargo, $datosNovedad){
		
		$this->_ArmarArrayCargoParo($cargo);
		//$NovedadesInsertar[]=$datosNovedad;

	  //inserto la auditoria de los cargos
		$sql='INSERT INTO '.BASEDATOSAUDITORIAS.'.AuditoriasDocumentosCargos (
										  Accion,
										  IdDocumento,
										  Secuencia,
										  SubSecuencia,
										  Revista,
										  RealIntOut,
										  ModalidadCarrera,
										  Asignatura,
										  Area,
										  CargoCodigo,
										  CargoDescripcion,
										  GrupoCodigo, 
										  GrupoDescripcion, 
										  SubGrupoCodigo, 
										  SubGrupoDescripcion, 
										  RegimenEstatutarioCodigo,
										  RegimenEstatutarioDescripcion,  
										  CargoHsMod,
										  CargoEnsenanza,
										  Anio,
										  Seccion,
										  IdTurno,
										  HsDesignacion,
										  HsDesignacionDescripcion,
										  CuilAlta,
										  EscalafonAlta,
										  ClaveEscuelaAlta,
										  AltaApp,
										  AltaFecha,
										  HashDato
		) 
		VALUES
		(
										    "'.INSERTAR.'",
											'.$datosNovedad["IdDocumento"].',
											'.$cargo["secuenciaOut"].',
											'.$cargo["subsecuenciaOut"].',
											"'.$cargo["caractRevOut"].'",
											"'.$cargo["realIntOut"].'",
											"",
											"'.$cargo["asignatura"].'",
											"'.$cargo["area"].'",
											"'.$cargo["cargoCodigo"].'",
											"'.$cargo["cargoDesc"].'",
											"'.$cargo["grupoCodigo"].'",
											"'.$cargo["grupoDesc"].'",
											"'.$cargo["subGrupoCodigo"].'",
											"'.$cargo["subGrupoDesc"].'",
											"'.$cargo["regEstatCodigo"].'",
											"'.$cargo["regEstatDesc"].'",	
											"'.$cargo["cargoHsMod"].'",
											"'.$cargo["cargoEnse"].'",
											'.$cargo["anio"].',
											"'.$cargo["letraSeccion"].'",
											"'.$cargo["turno"].'",
											"'.$cargo["regHorario"].'",
											"'.$cargo["hsDesigDetalle"].'",
											"'.$datosNovedad['CuilAlta'].'",
											"'.$datosNovedad['EscalafonAlta'].'",
											"'.$datosNovedad['ClaveEscuelaAlta'].'",
											"Cliente",
											"'.$datosNovedad['AltaFecha'].'",
											""
			);';	
										   	
							 
			return $sql;
	}
	
	function ArmarArrayElasticParo($datosRegistro)
		{
			
			$datosRegistroformatoelastic=array();
			$datosRegistroformatoelastic['Agente']['Cuil'] = $datosRegistro['AgenteCuil'];
			$datosRegistroformatoelastic['Agente']['Dni'] = $datosRegistro['AgenteDNI'];
			$datosRegistroformatoelastic['Agente']['Nombre'] = $datosRegistro['AgenteNombre'];
			$datosRegistroformatoelastic['Agente']['FechaNacimiento'] = $datosRegistro['AgenteFNacimiento'];
			$datosRegistroformatoelastic['Agente']['Sexo'] = $datosRegistro['AgenteSexo'];
			
			if (isset($datosRegistro['AgenteTipoSeleccion']) && $datosRegistro['AgenteTipoSeleccion']!="" && $datosRegistro['AgenteTipoSeleccion']!="NULL")
				$datosRegistroformatoelastic['Agente']['TipoSeleccion'] = $datosRegistro['AgenteTipoSeleccion'];
		
			// Datos Cargo
			if (isset($datosRegistro['AgenteRevista']) && $datosRegistro['AgenteRevista']!="" && $datosRegistro['AgenteRevista']!="NULL")
				$datosRegistroformatoelastic['Cargo']['Revista'] = $datosRegistro['AgenteRevista'];
				
			if (isset($datosRegistro['AgenteModalidadCarrera']) && $datosRegistro['AgenteModalidadCarrera']!="" && $datosRegistro['AgenteModalidadCarrera']!="NULL")
				$datosRegistroformatoelastic['Cargo']['ModalidadCarrera'] = $datosRegistro['AgenteModalidadCarrera'];
				
			if (isset($datosRegistro['AgenteHsModCar']) && $datosRegistro['AgenteHsModCar']!="" && $datosRegistro['AgenteHsModCar']!="NULL")
				$datosRegistroformatoelastic['Cargo']['HsModCar'] = $datosRegistro['AgenteHsModCar'];
				
			if (isset($datosRegistro['AgenteHsCargo']) && $datosRegistro['AgenteHsCargo']!="" && $datosRegistro['AgenteHsCargo']!="NULL")
				$datosRegistroformatoelastic['Cargo']['HsCargo'] = $datosRegistro['AgenteHsCargo'];	
			
			if (isset($datosRegistro['AgenteHsTrabajadas']) && $datosRegistro['AgenteHsTrabajadas']!="" && $datosRegistro['AgenteHsTrabajadas']!="NULL")
				$datosRegistroformatoelastic['Cargo']['HsTrabajadas'] = $datosRegistro['AgenteHsTrabajadas'];	
			
				
			if (isset($datosRegistro['AgenteFuncion']) && $datosRegistro['AgenteFuncion']!="" && $datosRegistro['AgenteFuncion']!="NULL")
				$datosRegistroformatoelastic['Cargo']['Funcion'] = $datosRegistro['AgenteFuncion'];
				
			if (isset($datosRegistro['AgenteAnio']) && $datosRegistro['AgenteAnio']!="" && $datosRegistro['AgenteAnio']!="NULL")
				$datosRegistroformatoelastic['Cargo']['Anio'] = $datosRegistro['AgenteAnio'];	
		
			if (isset($datosRegistro['AgenteSeccion']) && $datosRegistro['AgenteSeccion']!="" && $datosRegistro['AgenteSeccion']!="NULL")
				$datosRegistroformatoelastic['Cargo']['Seccion'] = $datosRegistro['AgenteSeccion'];
				
			if (isset($datosRegistro['AgenteIdTurno']) && $datosRegistro['AgenteIdTurno']!="" && $datosRegistro['AgenteIdTurno']!="NULL")
				$datosRegistroformatoelastic['Cargo']['IdTurno'] = $datosRegistro['AgenteIdTurno'];	
				
			if (isset($datosRegistro['AgenteSecuencia']) && $datosRegistro['AgenteSecuencia']!="" && $datosRegistro['AgenteSecuencia']!="NULL")
				$datosRegistroformatoelastic['Cargo']['Secuencia'] = $datosRegistro['AgenteSecuencia'];		
		
			if (isset($datosRegistro['AgenteCodigoArea']) && $datosRegistro['AgenteCodigoArea']!="" && $datosRegistro['AgenteCodigoArea']!="NULL")
				$datosRegistroformatoelastic['Cargo']['CodigoArea'] = $datosRegistro['AgenteCodigoArea'];	
		
			if (isset($datosRegistro['AgenteCodigoAsignatura']) && $datosRegistro['AgenteCodigoAsignatura']!="" && $datosRegistro['AgenteCodigoAsignatura']!="NULL")
				$datosRegistroformatoelastic['Cargo']['CodigoAsignatura'] = $datosRegistro['AgenteCodigoAsignatura'];	
			
			if (isset($datosRegistro['AgenteRegimenEstatutario']) && $datosRegistro['AgenteRegimenEstatutario']!="" && $datosRegistro['AgenteRegimenEstatutario']!="NULL")
				$datosRegistroformatoelastic['Cargo']['RegimenEstatutario'] = $datosRegistro['AgenteRegimenEstatutario'];	
			
			if (isset($datosRegistro['AgenteGrupo']) && $datosRegistro['AgenteGrupo']!="" && $datosRegistro['AgenteGrupo']!="NULL")
				$datosRegistroformatoelastic['Cargo']['Grupo'] = $datosRegistro['AgenteGrupo'];
			
			if (isset($datosRegistro['AgenteSubGrupo']) && $datosRegistro['AgenteSubGrupo']!="" && $datosRegistro['AgenteSubGrupo']!="NULL")
				$datosRegistroformatoelastic['Cargo']['SubGrupo'] = $datosRegistro['AgenteSubGrupo'];
			
			
			if (isset($datosRegistro['DniReemplazo']) && $datosRegistro['DniReemplazo']!="" && $datosRegistro['DniReemplazo']!="NULL")
				$datosRegistroformatoelastic['DniReemplazo'] = $datosRegistro['DniReemplazo'];
			
			if (isset($datosRegistro['DniReemplazoSexo']) && $datosRegistro['DniReemplazoSexo']!="" && $datosRegistro['DniReemplazo']!="NULL")
				$datosRegistroformatoelastic['DniReemplazoSexo'] = $datosRegistro['DniReemplazoSexo'];
			
			if (isset($datosRegistro['DniReemplazoSecuencia']) && $datosRegistro['DniReemplazoSecuencia']!="" && $datosRegistro['DniReemplazoSecuencia']!="NULL")
				$datosRegistroformatoelastic['DniReemplazoSecuencia'] = $datosRegistro['DniReemplazoSecuencia'];	
				
			if (isset($datosRegistro['DniReemplazoSubSecuencia']) && $datosRegistro['DniReemplazoSubSecuencia']!="" && $datosRegistro['DniReemplazoSubSecuencia']!="NULL")
				$datosRegistroformatoelastic['DniReemplazoSubSecuencia'] = $datosRegistro['DniReemplazoSubSecuencia'];		
			
			if(isset($datosRegistro['DatosJsonDiasCargo']) && $datosRegistro['DatosJsonDiasCargo']!="")
			{
	
				$datosjson = FuncionesPHPLocal::DecodificarUtf8(json_decode($datosRegistro['DatosJsonDiasCargo'], true));
				$datosRegistro['DatosJsonDiasCargo'] = $datosjson;
				$datosRegistroformatoelastic['DatosJsonDiasCargo'] = $datosRegistro['DatosJsonDiasCargo'];	
			
			}
			foreach($datosRegistro["cargos"] as $DataCargo)
			{
				$datosRegistroCargo['Secuencia'] = $DataCargo['secuenciaOut'];
				$datosRegistroCargo['SubSecuencia'] = $DataCargo['subsecuenciaOut'];
				$datosRegistroCargo['Revista'] = $DataCargo['caractRevOut'];
				$datosRegistroCargo['RealIntOut'] = $DataCargo['realIntOut'];
				$datosRegistroCargo['ModalidadCarrera'] = "";
				$datosRegistroCargo['Asignatura'] = $DataCargo['asignatura'];
				$datosRegistroCargo['Area'] = $DataCargo['area'];
				$datosRegistroCargo['CargoCodigo'] = $DataCargo['cargoCodigo'];
				$datosRegistroCargo['CargoDescripcion'] = utf8_encode(trim($DataCargo['cargoDesc']));
				
				if (isset($DataCargo['grupoCodigo']))
					$datosRegistroCargo['GrupoCodigo'] = $DataCargo['grupoCodigo'];
				if (isset($DataCargo['grupoDesc']))
					$datosRegistroCargo['GrupoDescripcion'] = utf8_encode(trim($DataCargo['grupoDesc']));
				if (isset($DataCargo['subGrupoCodigo']))
					$datosRegistroCargo['SubGrupoCodigo'] = $DataCargo['subGrupoCodigo'];	
				if (isset($DataCargo['subGrupoDesc']))
					$datosRegistroCargo['SubGrupoDescripcion'] = utf8_encode(trim($DataCargo['subGrupoDesc']));
				
				$datosRegistroCargo['RegimenEstatutarioCodigo'] = $DataCargo['regEstatCodigo'];
				if (isset($DataCargo['regEstatDesc']))
					$datosRegistroCargo['RegimenEstatutarioDescripcion'] = utf8_encode(trim($DataCargo['regEstatDesc']));
				
				
				$datosRegistroCargo['CargoHsMod'] = $DataCargo['cargoHsMod'];
				$datosRegistroCargo['CargoEnsenanza'] = $DataCargo['cargoEnse'];
				$datosRegistroCargo['Anio'] = $DataCargo['anio'];
				$datosRegistroCargo['Seccion'] = $DataCargo['letraSeccion'];
				$datosRegistroCargo['IdTurno'] = $DataCargo['turno'];
				$datosRegistroCargo['Seccion'] = $DataCargo['letraSeccion'];
				$datosRegistroCargo['HsDesignacion'] = $DataCargo['regHorario'];
				$datosRegistroCargo['HsDesignacionDescripcion'] = $DataCargo['hsDesigDetalle'];
				$datosRegistroformatoelastic['Cargos'][] = $datosRegistroCargo;
			}
			
			$datosRegistro['CargosSeleccionadosReemplazo'] =array();
			
			if(isset($datosRegistro['DatosJsonCargosDniReemplazo']) && $datosRegistro['DatosJsonCargosDniReemplazo']!="")
			{
				$datosjson = FuncionesPHPLocal::DecodificarUtf8(json_decode($datosRegistro['DatosJsonCargosDniReemplazo'], true));
				$datosRegistro['CargosSeleccionadosReemplazo'] = $datosjson;
			}
			foreach($datosRegistro['CargosSeleccionadosReemplazo'] as $DataCargo)
			{
				$datosRegistroCargo['Secuencia'] = $DataCargo['Secuencia'];
				$datosRegistroCargo['SubSecuencia'] = $DataCargo['SubSecuencia'];
				$datosRegistroCargo['Revista'] = $DataCargo['Revista'];
				$datosRegistroCargo['RealIntOut'] = $DataCargo['RealIntOut'];
				$datosRegistroCargo['ModalidadCarrera'] = "";
				$datosRegistroCargo['Asignatura'] = $DataCargo['Asignatura'];
				$datosRegistroCargo['Area'] = $DataCargo['Area'];
				$datosRegistroCargo['CargoCodigo'] = $DataCargo['CargoCodigo'];
				$datosRegistroCargo['CargoDescripcion'] = utf8_encode(trim($DataCargo['CargoDescripcion']));
				$datosRegistroCargo['CargoHsMod'] = $DataCargo['CargoHsMod'];
				$datosRegistroCargo['CargoEnsenanza'] = $DataCargo['CargoEnsenanza'];
				$datosRegistroCargo['Anio'] = $DataCargo['Anio'];
				$datosRegistroCargo['Seccion'] = $DataCargo['Seccion'];
				$datosRegistroCargo['IdTurno'] = $DataCargo['IdTurno'];
				$datosRegistroCargo['Seccion'] = $DataCargo['Seccion'];
				$datosRegistroCargo['HsDesignacion'] = $DataCargo['HsDesignacion'];
				$datosRegistroCargo['HsDesignacionDescripcion'] = $DataCargo['HsDesignacionDescripcion'];
				$datosRegistroformatoelastic['CargosReemplazo'][] = $datosRegistroCargo;
			}
			
			
			
			
			if (isset($datosRegistro['TieneCUPOF']) && $datosRegistro['TieneCUPOF']!="")
			{
				$datosRegistro['CUPOF'] = ($datosRegistro['CUPOF']=="NULL")?"":$datosRegistro['CUPOF'];
				$datosRegistro['ObservacionesCUPOF'] = ($datosRegistro['ObservacionesCUPOF']=="NULL")?"":$datosRegistro['ObservacionesCUPOF'];
				if(isset($datosRegistro['CUPOF']) && $datosRegistro['CUPOF']!="" && $datosRegistro['TieneCUPOF']==1)
				{
					$datosRegistroformatoelastic['CUPOF'] = ($datosRegistro['CUPOF']);
					$datosRegistroformatoelastic['ObservacionesCUPOF'] = "";
				}elseif(isset($datosRegistro['ObservacionesCUPOF']) && $datosRegistro['ObservacionesCUPOF']!="")
				{
					$datosRegistroformatoelastic['CUPOF'] = "";
					$datosRegistroformatoelastic['ObservacionesCUPOF'] = ($datosRegistro['ObservacionesCUPOF']);
				}
			}
			
	
			//PERIODO
			if (isset($datosRegistro['PeriodoFechaDesde']) && $datosRegistro['PeriodoFechaDesde']!="" && $datosRegistro['PeriodoFechaDesde']!="NULL")
				$datosRegistroformatoelastic['Periodo']['FechaDesde'] = $datosRegistro['PeriodoFechaDesde'];
				
			if (isset($datosRegistro['PeriodoFechaHasta']) && $datosRegistro['PeriodoFechaHasta']!="" && $datosRegistro['PeriodoFechaHasta']!="NULL")
				$datosRegistroformatoelastic['Periodo']['FechaHasta'] = $datosRegistro['PeriodoFechaHasta'];	
				
			//FECHA TOMA POSESION
			if (isset($datosRegistro['FechaTomaPosesion']) && $datosRegistro['FechaTomaPosesion']!="" && $datosRegistro['FechaTomaPosesion']!="NULL")
				$datosRegistroformatoelastic['FechaTomaPosesion'] = $datosRegistro['FechaTomaPosesion'];	
			
			//NRO RESOLUCION
			if (isset($datosRegistro['NroResolucion']) && $datosRegistro['NroResolucion']!="" && $datosRegistro['NroResolucion']!="NULL")
				$datosRegistroformatoelastic['NroResolucion'] = $datosRegistro['NroResolucion'];
			
			//TASKID
			if (isset($datosRegistro['TaskId']) && $datosRegistro['TaskId']!="" && $datosRegistro['TaskId']!="NULL")
			{
	
				$datosRegistroformatoelastic['TaskId'] = $datosRegistro['TaskId'];
				$datosRegistroformatoelastic['DatosLicencia'] = json_decode($datosRegistro['DatosJsonLicencia'],true); 
				
			}
			//ORGANIZACION APOYO
			if (isset($datosRegistro['OrganizacionApoyo']) && $datosRegistro['OrganizacionApoyo']!="" && $datosRegistro['OrganizacionApoyo']!="NULL")
				$datosRegistroformatoelastic['OrganizacionApoyo'] = $datosRegistro['OrganizacionApoyo'];
			
			//OBSERVACIONES
			if (isset($datosRegistro['Observaciones']) && $datosRegistro['Observaciones']!="" && $datosRegistro['Observaciones']!="NULL")
				$datosRegistroformatoelastic['Observaciones'] = $datosRegistro['Observaciones'];	
			
			//INASISTENCIA
			if (isset($datosRegistro['InasistenciaModSem']) && $datosRegistro['InasistenciaModSem']!="" && $datosRegistro['InasistenciaModSem']!="NULL")
				$datosRegistroformatoelastic['Inasistencia']['ModSem'] = $datosRegistro['InasistenciaModSem'];
		
			if (isset($datosRegistro['InasistenciaModMen']) && $datosRegistro['InasistenciaModMen']!="" && $datosRegistro['InasistenciaModMen']!="NULL")
				$datosRegistroformatoelastic['Inasistencia']['ModMen'] = $datosRegistro['InasistenciaModMen'];
				
			if (isset($datosRegistro['LicenciaEncuadreArticulo']) && $datosRegistro['LicenciaEncuadreArticulo']!="" && $datosRegistro['LicenciaEncuadreArticulo']!="NULL")
				$datosRegistroformatoelastic['Inasistencia']['LicenciaEncuadreArticulo'] = $datosRegistro['LicenciaEncuadreArticulo'];	
		
			if (isset($datosRegistro['LicenciaEncuadreInciso']) && $datosRegistro['LicenciaEncuadreInciso']!="" && $datosRegistro['LicenciaEncuadreInciso']!="NULL")
				$datosRegistroformatoelastic['Inasistencia']['LicenciaEncuadreInsiso'] = $datosRegistro['LicenciaEncuadreInciso'];
				
			if (isset($datosRegistro['InasistenciaHsDesignadas']) && $datosRegistro['InasistenciaHsDesignadas']!="" && $datosRegistro['InasistenciaHsDesignadas']!="NULL")
				$datosRegistroformatoelastic['Inasistencia']['HsDesignadas'] = $datosRegistro['InasistenciaHsDesignadas'];
			
			if (isset($datosRegistro['InasistenciaHsTrabajadas']) && $datosRegistro['InasistenciaHsTrabajadas']!="" && $datosRegistro['InasistenciaHsTrabajadas']!="NULL")
				$datosRegistroformatoelastic['Inasistencia']['HsTrabajadas'] = $datosRegistro['InasistenciaHsTrabajadas'];
			
			if (isset($datosRegistro['InasistenciaHsDescontadas']) && $datosRegistro['InasistenciaHsDescontadas']!="" && $datosRegistro['InasistenciaHsDescontadas']!="NULL")
			{
				
				$datosRegistroformatoelastic['Inasistencia']['HsDescontadas'] = $datosRegistro['InasistenciaHsDescontadas'];
			}
			
			$datosRegistroformatoelastic['ClaveEscuela'] = $datosRegistro['ClaveEscuelaDatos'];
			$datosRegistroformatoelastic['CDependencia'] = $datosRegistro['IdCDependencia'];
			$datosRegistroformatoelastic['TipoOrg']['Id'] = $datosRegistro['IdTipoOrganismo'];
			$datosRegistroformatoelastic['Distrito']['Id'] = $datosRegistro['IdDistrito'];
			$datosRegistroformatoelastic['Escuela']['Id'] = $datosRegistro['IdEscuela'];
			$datosRegistroformatoelastic['Ensenanza'] = $datosRegistro['IdEnsenianza'];
			
		
			$datosRegistroformatoelastic['TipoOrg']['Nombre'] = $datosRegistro['IdTipoOrganismoNombre'];
			$datosRegistroformatoelastic['Distrito']['Nombre'] = $datosRegistro['IdDistritoNombre'];
			
			$datosRegistroformatoelastic['Escuela']['Nombre'] = utf8_decode($datosjson['IdEscuelaNombre']);
		
			$datosRegistroformatoelastic['TipoDocumento']['Id'] = $datosRegistro['IdTipoDocumento'];
			$datosRegistroformatoelastic['TipoDocumento']['IdRegistro'] = $datosRegistro['IdRegistroTipoDocumento'];
			$datosRegistroformatoelastic['TipoDocumento']['Nombre'] = ($datosRegistro['NombreTipoDocumento']);
			$datosRegistroformatoelastic['TipoDocumento']['Clasificacion']['Id'] = $datosRegistro['IdTipoDocumentoClasificacion'];
			$datosRegistroformatoelastic['TipoDocumento']['Clasificacion']['Nombre'] = utf8_encode($datosRegistro['IdTipoDocumentoClasificacionNombre']);
			if(isset($datosRegistro['NombreCorto']) && $datosRegistro['NombreCorto']!="")
				$datosRegistroformatoelastic['TipoDocumento']['NombreCorto'] = ($datosRegistro['NombreCorto']);
			if(isset($datosRegistro['IdCategoria']) && $datosRegistro['IdCategoria']!="")
				$datosRegistroformatoelastic['TipoDocumento']['Categoria']['Id'] = $datosRegistro['IdCategoria'];
			if(isset($datosRegistro['CategoriaNombre']) && $datosRegistro['CategoriaNombre']!="")
				$datosRegistroformatoelastic['TipoDocumento']['Categoria']['Nombre'] = utf8_encode($datosRegistro['CategoriaNombre']);
			$datosRegistroformatoelastic['IdDocumento'] = $datosRegistro['IdDocumento'];
			$datosRegistroformatoelastic['Tipo'] = TIPODOC;
			$datosRegistroformatoelastic['IdDocumentoPadre'] = $datosRegistro['IdDocumentoPadre'];
			
				
			$datosRegistroformatoelastic['Estado']['Id'] = ($datosRegistro['IdEstado']=="")?[]:$datosRegistro['IdEstado'];
			$datosRegistroformatoelastic['Estado']['Nombre'] = ($datosRegistro['IdEstado']=="")?[]:($datosRegistro['NombreEstado']);
			$datosRegistroformatoelastic['Area']['Id'] = ($datosRegistro['IdArea']=="")?[]:$datosRegistro['IdArea'];
			$datosRegistroformatoelastic['Area']['Nombre'] = ($datosRegistro['IdArea']=="")?[]:($datosRegistro['NombreArea']);
		
		
			if (isset($datosRegistro['AltaFecha']) && $datosRegistro['AltaFecha']!="")
				$datosRegistroformatoelastic['MovimientoFecha'] = $datosRegistro['AltaFecha'];
			
			$datosRegistroformatoelastic['Alta']['APP'] = $datosRegistro['AltaApp'];
			$datosRegistroformatoelastic['Alta']['ClaveEscuela'] = $datosRegistro['ClaveEscuelaAlta'];
			$datosRegistroformatoelastic['Alta']['Escalafon'] = $datosRegistro['EscalafonAlta'];
			$datosRegistroformatoelastic['Alta']['Cuil'] = $datosRegistro['CuilAlta'];
			$datosRegistroformatoelastic['Alta']['Fecha'] =  $datosRegistro['AltaFecha'];
			
			
			$datosRegistroformatoelastic['UltimaModificacion']['APP'] = $datosRegistro['UltimaModificacionApp'];
			$datosRegistroformatoelastic['UltimaModificacion']['ClaveEscuela'] = $datosRegistro['UltimaModificacionClaveEscuela'];
			$datosRegistroformatoelastic['UltimaModificacion']['Escalafon'] = $datosRegistro['UltimaModificacionEscalafon'];
			$datosRegistroformatoelastic['UltimaModificacion']['Cuil'] = $datosRegistro['UltimaModificacionCuil'];
			$datosRegistroformatoelastic['UltimaModificacion']['Fecha'] =  $datosRegistro['UltimaModificacionFecha'];
		
			
			if (isset($datosRegistro['FechaEnvio']) && $datosRegistro['FechaEnvio']!="")
				$datosRegistroformatoelastic['FechaEnvio'] = $datosRegistro['FechaEnvio'];
			
			return FuncionesPHPLocal::ConvertiraUtf8($datosRegistroformatoelastic);
		}
	

}
?>