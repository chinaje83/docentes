<?php
include(DIR_CLASES_DB."cModPlantillas.db.php");

class cModPlantillas extends cModPlantillasdb
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



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdModulo'=> 0,
			'IdModulo'=> "",
			'xConstante'=> 0,
			'Constante'=> "",
			'limit'=> '',
			'orderby'=> "IdPlantilla DESC"
		);

		if(isset($datos['IdModulo']) && $datos['IdModulo']!="")
		{
			$sparam['IdModulo']= $datos['IdModulo'];
			$sparam['xIdModulo']= 1;
		}
		if(isset($datos['Constante']) && $datos['Constante']!="")
		{
			$sparam['Constante']= $datos['Constante'];
			$sparam['xConstante']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function ModulosSP(&$spnombre,&$sparam)
	{
		if (!parent::ModulosSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function ModulosSPResult(&$resultado,&$numfilas)
	{
		if (!$this->ModulosSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos))
			return false;

		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos))
			return false;

		if (!parent::Eliminar($datos))
			return false;

		return true;
	}



	public function ProcesarFechas($string,$mes="",$anio="")
	{
		setlocale(LC_TIME, array("es", "spa", "es_AR", "esp", "es_ES"));
		if($mes == "")
			$mes = ucwords(strftime("%B",strtotime("{$_SESSION['Anio']}-{$_SESSION['Mes']}-01")));
		if($anio == "")
			$anio = date("Y",strtotime("{$_SESSION['Anio']}-{$_SESSION['Mes']}-01"));


		$pattern = array('@\@mes\@@si','@\@anio\@@si','/\&nbsp;/','/\$\ ((\d)+)/','/\$\ (\@)/','/(\@)\ (litros)/');
		$replacement = array($mes,$anio," ","\$&#x202f;$1","\$&#x202f;$1","$1&#x202f;$2");
		$cuerpo = preg_replace($pattern, $replacement, $string);

		return $cuerpo;
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
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c�digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		return true;
	}



	private function _ValidarEliminar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c�digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdModulo']) || $datos['IdModulo']=="")
			$datos['IdModulo']="NULL";

		if (!isset($datos['Constante']) || $datos['Constante']=="")
			$datos['Constante']="NULL";

		if (!isset($datos['Clase']) || $datos['Clase']=="")
			$datos['Clase']="NULL";

		if (!isset($datos['DescripcionCorta']) || $datos['DescripcionCorta']=="")
			$datos['DescripcionCorta']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdModulo']) || $datos['IdModulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar un m�dulo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdModulo'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Constante']) || $datos['Constante']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una constante",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		/*if (!isset($datos['Clase']) || $datos['Clase']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una clase",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		/*if (!isset($datos['DescripcionCorta']) || $datos['DescripcionCorta']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripci�n corta",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripci�n",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->conexion->TraerCampo('Modulos','IdModulo',array('IdModulo='.$datos['IdModulo']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	public function GenerarPdf($datos,$formatosalida="F")
	{

		$mpdf=new mPDF('utf-8','A4','','','15','15','50','20');
		$stylesheet = file_get_contents('assets/css/pdf.css');
		$stylesheet = str_replace("Arial, Helvetica, sans-serif","Roman",$stylesheet);
		//print_r($stylesheet);
		$carpetaFecha = date("Ym")."/";
		$carpeta = PATH_STORAGE."archivos/";
		if(!is_dir($carpeta))
			@mkdir($carpeta);
		$carpeta .= $carpetaFecha;
		if(!is_dir($carpeta))
			@mkdir($carpeta);

		$filename = "archivo_".date("Ymd_").str_pad(mt_rand(0,1000),4,"0000",STR_PAD_LEFT).".pdf";
		if($formatosalida=="F")
		{
			if(file_exists($carpeta.$filename.".pdf"))
					unlink($carpeta.$filename.".pdf");
		}

		$mpdf->SetTitle(strtoupper(utf8_encode($datos['Nombre'])));
		$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

		$this->GenerarHeader($datos,$header);
		$this->GenerarFooter($datos,$footer,$mpdf);

		$mpdf->SetHTMLHeader($header);
		$mpdf->SetHTMLFooter($footer);

		if(!$this->GenerarHtml($datos,$html,$mpdf))
		{

			return false;
		}

		$this->FirmaAclaracion($datos,$html,$mpdf);

		$mpdf->WriteHTML($html);



		$mpdf->Output(PATH_STORAGE.$this->carpeta.$carpetaFecha.$filename, $formatosalida);
		return true;
	}

	public function GenerarHeader($datos,&$html)
	{
		$html = "";

		return true;
	}

	public function GenerarFooter($datos,&$html,$mpdf)
	{
		$html = '<div class="footer">{PAGENO} / {nb}</div>';
		return true;
	}


	public function GenerarHtml($datos,&$html,$mpdf)
	{
		/*$datosDocumento = $this->datosExpDocumento;
		$datosDocumento['conexion'] = $this->conexion;
		$datosDocumento['data'] = array();
		if ($this->datosExpDocumento['expdocumentodata']!="")
			$datosDocumento['data'] = json_decode($this->datosExpDocumento['expdocumentodata']);
		$htmlModuleRender = FuncionesPHPLocal::RenderFile("templatesdoc/pdf/".$this->datosDocumento['documentofile'],$datosDocumento);
		$html = $htmlModuleRender;*/
		$datosBuscar['Constante'] = $datos['Constante'];
		if(!$this->BusquedaAvanzada($datosBuscar,$resultado,$numfilas))
			return false;

		if($numfilas != 1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"La plantilla no existe",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$datosPlantilla = $this->conexion->ObtenerSiguienteRegistro($resultado);

		if(isset($datosPlantilla['DescripcionCorta']) && $datosPlantilla['DescripcionCorta']!="")
		{
			$cuerpo = $this->ProcesarFechas($datosPlantilla['DescripcionCorta']);
			if(preg_match("/\@/",$cuerpo))
				$html .= $this->ReemplazarEspecificos($cuerpo,$datos);
			else
				$html .= $cuerpo;
		}

		if(!$this->ProcesarDatos($datos,$html))
			return false;

		$cuerpo = $this->ProcesarFechas($datosPlantilla['Descripcion']);
		if(preg_match("/\@/",$cuerpo))
			$html .= $this->ReemplazarEspecificos($cuerpo,$datos);
		else
			$html .= $cuerpo;

		return true;
	}


	private function FirmaElectronica()
	{
		/*$carpetaFecha = date("Ym",strtotime($this->datosExpDocumento['expdocumentofalta']))."/";
		$filename = "archivo_".$this->datosExpDocumento['expdocumentocod'].".pdf";
		$filenameOutput = "archivo_".$this->datosExpDocumento['expdocumentocod']."_firmado.pdf";

		$worker_name = FIRMAWORKER;
		$file_to_sign = new \CURLFile(PATH_STORAGE.$this->carpeta.$carpetaFecha.$filename);
		$signserver_url = SERVERSIGNER;
		$output_filename = PATH_STORAGE.$this->carpeta.$carpetaFecha.$filenameOutput;
		$ch = curl_init();
		$data = array(
			'workerName' => $worker_name,
			'filerecievefile' => $file_to_sign
		);
		curl_setopt($ch,CURLOPT_URL,$signserver_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POST,count($data));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$fp = fopen($output_filename, 'w');
		fwrite($fp, $result);	*/

		return true;
	}


	private function FirmaAclaracion($datos,&$html,$mpdf)
	{
		$datosDocumento = $this->datosExpDocumento;

		$html .= '<div class="separacionfirma">&nbsp;</div>';
		$html .= '<div class="col-md-4">';
        $html .= '<div class="firmaaclaracion" style="text-align:center;">';
		//print_r($_SESSION);die;
        $html .= utf8_encode($_SESSION['usuarionombre']." ".$_SESSION['usuarioapellido']).'<br />';
       /* if($datos['roldesc']!="")
		{
        	$html .='('.ucwords(strtolower($datos['roldesc'])).')<br>';
        } */
      //  $html .= utf8_encode($datosDocumento['areanombre']);
        $html .='</div>';
        $html .='</div>';


		return true;
	}

	private function ReemplazarEspecificos($string,$datos)
	{
		$pattern = array();
		$replacement = array();
		if(isset($datos['Monto']) && $datos['Monto']!="" && is_numeric($datos['Monto']))
		{
			require_once(DIR_LIBRERIAS."convertirNumero.php");
			$datos['MontoPalabras'] = convertirNumero::numtoletras($datos['Monto']);
			$datos['Monto'] = number_format($datos['Monto'],2,",",".");
		}
		if(is_array($datos) && count($datos)>0)
		{
			foreach($datos as $key=>$value)
			{
				$pattern[] = "@\@".$key."\@@si";
				$replacement[] = $value;
			}
			$cuerpo = preg_replace($pattern, $replacement, $string);
		}
		else
			$cuerpo = $string;

		return $cuerpo;
	}


	private function ProcesarDatos($datos,&$html)
	{
		/*
		 * esta function va a tener uso en cada clase especifica, pero no ac� ya que cada
		 * una va a tener su propia estructura, e.g. <table> o <ul>
		 */
		return true;
	}





}
?>
