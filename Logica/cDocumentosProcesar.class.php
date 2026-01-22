<?php 
class cDocumentosProcesar
{
	
	protected $conexion;
	protected $formato;
	protected $previsualizar;
	protected $generarForm;
	protected $dataLoad;
	
	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		$this->previsualizar = false;
		$this->generarForm = true;
		$this->dataLoad = array();
    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	

//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 

	public function GenerarFormulario($tipo)
	{
		$this->generarForm = $tipo;	
	}


	public function Procesar($datosregistro, $dataJson, &$html)
	{
		
		if (!file_exists(CARPETA_SERVIDOR_TIPOSDOCUMENTOS_FISICA."documento_".$datosregistro['IdRegistroTipoDocumento'].".json"))
			return false;
			
		$jsonData = file_get_contents(CARPETA_SERVIDOR_TIPOSDOCUMENTOS_FISICA."documento_".$datosregistro['IdRegistroTipoDocumento'].".json");	
		
		$data = json_decode($jsonData,1);
		$data = FuncionesPHPLocal::DecodificarUtf8($data);

		$tienePermisosModificacion = false;
		if (isset($datosregistro['PermisosModificacion']) && $datosregistro['PermisosModificacion']==true)
			$tienePermisosModificacion = true;
			
		$arreglozonas = array();
		$html = "";
		if ($this->generarForm==true)
		{
			if (isset($data['LoadTipoDocumento']) && $data['LoadTipoDocumento']!="")
				$html .= '<script type="text/javascript">'.utf8_decode($data['LoadTipoDocumento']).'</script>';
			if (isset($data['UnLoadTipoDocumento']) && $data['UnLoadTipoDocumento']!="")
				$html .= '<script type="text/javascript">'.utf8_decode($data['UnLoadTipoDocumento']).'</script>';
		}
		$html .= '<div class="macrossite row">';
		foreach($data['macros'] as $idMacro=>$dataMacro)
		{
			$html .= '<div class="macros '.$dataMacro['Clase'].'" id="macro_'.$idMacro.'">';
			$html .= '<div class="clearboth">&nbsp;</div>';
			foreach($dataMacro['Estructuras'] as $IdEstructura=>$Estructuras)
			{
			    $html .= '<div class="estructuras '.$Estructuras['Clase'].'" id="zona_'.$IdEstructura.'">';
				foreach($Estructuras['Modulos'] as $IdModulo=>$DataModulo)
				{
					$DataModulo['PermisosModificacion'] = $tienePermisosModificacion;
					if(!$this->CargarModulo($DataModulo,$dataJson,$html))
						return false;										
				}
				$html .= '</div>';
			}
			$html .= '<div class="clearboth">&nbsp;</div></div>';
			
		}
		$html .= '<div class="clearboth">&nbsp;</div></div>';
		return true;
		
	}

	public function ProcesarEncabezado($datosregistro, $dataJson, &$html)
	{
		
		if (!isset($this->dataLoad[$datosregistro['IdRegistroTipoDocumento']]))
		{
			$file = CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_FISICA."cliente_".$_SESSION['IdCliente']."/json/encabezado_".$datosregistro['IdRegistroTipoDocumento'].".json";
			if (!file_exists($file))
				return false;
				
				
			$jsonData = file_get_contents($file);	
	
			$data = json_decode($jsonData,true);
			$data = FuncionesPHPLocal::DecodificarUtf8($data);
			$this->dataLoad[$datosregistro['IdRegistroTipoDocumento']] = $data;
		}else
			$data = $this->dataLoad[$datosregistro['IdRegistroTipoDocumento']];


		//echo "<pre>";print_r($datosregistro);echo "</pre>";//die;
		//echo "<pre>";print_r($data);echo "</pre>";//die;
		$arreglozonas = array();
		
		$html = "";
		$html .= '<div class="">';
		$cantCol = 0;
		foreach($data as $DataModulo)
		{
			$datosCol = json_decode(utf8_encode($DataModulo['DataJson']),true);
			if(!isset($datosCol['CantidadColumnas']) || $datosCol['CantidadColumnas']=="")
				$datosCol['CantidadColumnas'] = 3;
			$oldCant = $cantCol;
			$cantCol += intval($datosCol['CantidadColumnas']);
			if($cantCol>12 && $cantCol%12!=0)
				$datosCol['CantidadColumnas'] = 12 - $oldCant;
			$html .= "<div class=\"col-md-{$datosCol['CantidadColumnas']}\">";
			if(!$this->CargarModuloEncabezado($DataModulo,$dataJson,$html))
				return false;
			$html .= '</div>';
			if($cantCol>=12)
			{
				$html .=  '<div class="clearboth">&nbsp;</div>';
				$cantCol = 0;
			}
		}
		$html .= '<div class="clearboth">&nbsp;</div></div>';
		return true;
		
	}




	private function CargarModulo($datosModulo,$dataJson,&$html_generado)
	{
		$carpeta = "estructuras/modulos";
		$Eliminar = 1;
		$carpetaFinal = "html";
		if ($datosModulo['IdCampo']!="")
		{
			$carpeta = "estructuras/campos";
			$Eliminar = 2;
		}
		if ($datosModulo['IdDocumentoAdjunto']!="")
		{
			$carpeta = "estructuras/archivos";
			$Eliminar = 3;
			$carpetaFinal = "html_carga";
		}
		
		if ($this->generarForm==false)
			$carpetaFinal = "pdf";
		
		$datosModulo['conexion'] = $this->conexion;
		$datosModulo['mouseaction'] = "";
		$datosModulo['htmledit'] = "";
		$datosModulo['htmleditfooter'] = "";
		
		$datosModulo['Datos'] = false;
		if ($datosModulo['TieneValores']==1)
		{
			if (isset($datosModulo['Valores']))
				$datosModulo['Datos'] = $datosModulo['Valores'];	
		}
		
		$datosModulo['DatosCargados'] = $dataJson;
		$datosModulo['enBuscador'] = 0;
		
		$htmlModuleRender = FuncionesPHPLocal::RenderFile($carpeta."/".$carpetaFinal."/".$datosModulo['NombreArchivo'],$datosModulo);
		$html = $this->ProcesarHtmlInterno($htmlModuleRender);
		$html_generado .= $html;
		
		return true;
	}




	private function CargarModuloEncabezado($datosModulo,$dataJson,&$html_generado)
	{
		$carpeta = "estructuras/modulos";
		$Eliminar = 1;
		$carpetaFinal = "header";
		if($datosModulo['TipoCampoElastic'] == "date" && !empty($dataJson[$datosModulo['NombreCampo']]))
		{
			$NombreCampo = $datosModulo['NombreCampo'];
			$dataJson[$NombreCampo] = FuncionesPHPLocal::ConvertirFecha($dataJson[$NombreCampo],"aaaa-mm-dd","dd/mm/aaaa");
		}	
		if ($datosModulo['IdCampo']!="")
		{
			$carpeta = "estructuras/campos";
			$Eliminar = 2;
		}
		if (isset($datosModulo['IdDocumentoAdjunto']) && $datosModulo['IdDocumentoAdjunto']!="")
		{
			$carpeta = "estructuras/archivos";
			$Eliminar = 3;
			$carpetaFinal = "header";
		}
		$datosModulo['conexion'] = $this->conexion;
		$datosModulo['mouseaction'] = "";
		$datosModulo['htmledit'] = "";
		
		$datosModulo['Datos'] = false;
		if ($datosModulo['TieneValores']==1)
		{
			if (isset($datosModulo['Valores']))
				$datosModulo['Datos'] = $datosModulo['Valores'];	
		}
		
		$datosModulo['DatosCargados'] = $dataJson;
		$htmlModuleRender = FuncionesPHPLocal::RenderFile($carpeta."/".$carpetaFinal."/".$datosModulo['NombreArchivo'],$datosModulo);
		$html = $this->ProcesarHtmlInterno($htmlModuleRender);
		$html_generado .= $html;
		
		return true;
	}


	
	private function ProcesarHtmlInterno($htmlModuleRender)
	{
		$html = "";
		cSepararHTML::ProcesarHTML($htmlModuleRender,$partes);
		foreach($partes as $partehtml)
		{
			if(is_array($partehtml))
			{
			}else
				$html .= $partehtml;
		}

		return $html;
	}
	
	
	
	
}//FIN CLASE

?>