<?php 
/*
CLASE LOGICA PARA EL MANEJO DE LOS BOTONES DE CARGA DE MULTIMEDIA.
*/

class cMultimediaFormulario
{

	protected $conexion;
	protected $formato;
	protected $prefijo;
	protected $codigoTablaRelacionMultimedia;
	protected $TipoMultimedia = array();
	
	// Constructor de la clase
	function __construct($conexion, $prefijo, $codigoTablaRelacionMultimedia, $formato = FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		$this->prefijo = $prefijo;
		$this->codigoTablaRelacionMultimedia = $codigoTablaRelacionMultimedia;
		$this->Inilializar($prefijo);
    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	


//-----------------------------------------------------------------------------------------
//							 PUBLICAS	
//----------------------------------------------------------------------------------------- 
	
	
	private function Inilializar($prefijo)
	{
		
		$oMultimediaConfiguracion = new cMultimediaConfiguracion($this->conexion);
		$datos['configtipo'] = $prefijo;
		if (!$oMultimediaConfiguracion->BucarConfiguracionxTipo($datos,$resultado,$numfilas))
			return false;
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Configuracion inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}	
		$datosDevueltos = $this->conexion->ObtenerSiguienteRegistro($resultado);

		while (list($clave, $valor) = each($datosDevueltos)) {
			$this->TipoMultimedia[$clave] = $valor;
		} 
	}


	public function getTipoMultimedia()
	{
		return $this->TipoMultimedia;	
	}

//----------------------------------------------------------------------------------------- 
// Retorna la direccion donde buscar la imagen por Tamaño

// Parámetros de Entrada:
//		carpeta: Carpeta a donde buscar el archivo
//		archivo: Nombre del archivo a buscar
// Retorna:
//		un string con la direccion de la imagen

	public function CargarBotonera()
	{
		
		$html = '<script type="text/javascript" src="js/multimedia.js"></script>';
		$html .= '<script type="text/javascript" src="modulos/mul_multimedia/js/mul_multimedia_popup_general.js"></script>';
		$html .= '<link href="modulos/mul_multimedia/css/multimedia.css" rel="stylesheet" title="style" media="all" />';
		$html .= $this->CargarConfiguracionJs();
		$html .= '<form action="mul_multimedia_popup_general_upd.php" name="formmultimediapopup" id="formmultimediapopup" method="post">'; 
		$html .= '<input  type="hidden" id="prefijo" name="prefijo" value="'.$this->prefijo.'" />';
		$html .= '<input  type="hidden" id="codigoTablaRelacionMultimedia" name="codigoTablaRelacionMultimedia" value="'.$this->codigoTablaRelacionMultimedia.'"  />';
		$html .= '<div class="menubarra">';
        $html .= '<ul>';
		if($this->TipoMultimedia['tieneimg'])
		        $html .=  '<li><a style="font-size:.9em" class="left multimediaImagen boton azul" href="javascript:void(0)">Nueva Foto</a></li>';
        if($this->TipoMultimedia['tienevideo'])
			$html .=  '<li><a style="font-size:.9em" class="left multimediaVideo boton azul" href="javascript:void(0)">Nuevo Video</a></li>';
        if($this->TipoMultimedia['tieneaudio'])
			$html .=  '<li><a style="font-size:.9em" class="left multimediaAudio boton azul" href="javascript:void(0)">Nuevo Audio</a></li>';
        if($this->TipoMultimedia['tienearchivos'])
			$html .=  '<li><a style="font-size:.9em" class="left multimediaArchivos boton azul" href="javascript:void(0)">Nuevo Archivo</a></li>';
	    $html .= '</ul>';
        $html .= '</div>';
		$html .= '</form>';
		$html .= '<div class="msgaccionmultimedia"></div>';
		$html .= '<div id="PopupMultimedia"></div>';
	
		return $html;
		
	}
	
	public function CargarListado()
	{
		
		$html = '<div class="multimedia">';
        $html .= '<div id="tabs">';
        $html .= '<h3>Archivos Multimedia</h3>';
        $html .= '<ul>';
		if($this->TipoMultimedia['tieneimg'])
        	$html .= '<li><a href="#fotos">Fotos</a></li>';
        if($this->TipoMultimedia['tienevideo'])
			$html .= '<li><a href="#videos">Videos</a></li>';
        if($this->TipoMultimedia['tieneaudio'])
			$html .= '<li><a href="#audios">Audios</a></li>';
        if($this->TipoMultimedia['tienearchivos'])
			$html .= '<li><a href="#files">Archivos</a></li>';
        $html .= '</ul>';                          
		if($this->TipoMultimedia['tieneimg'])
			$html .= '<div id="fotos">&nbsp;</div>';
        if($this->TipoMultimedia['tienevideo'])
			$html .= '<div id="videos">&nbsp;</div>';
        if($this->TipoMultimedia['tieneaudio'])
			$html .= '<div id="audios">&nbsp;</div>';                      
        if($this->TipoMultimedia['tienearchivos'])
			$html .= '<div id="files">&nbsp;</div>';                      
        $html .= '</div>';
        $html .= '</div>';
		
		return $html;
		
	}
	
	
	public function CargarListadoPublicado()
	{
		$html = '<script type="text/javascript" src="js/multimedia.js"></script>';
		$html .= '<script type="text/javascript" src="modulos/mul_multimedia/js/mul_multimedia_popup_general.js"></script>';
		$html .= '<link href="modulos/mul_multimedia/css/multimedia.css" rel="stylesheet" title="style" media="all" />';
		$html .= $this->CargarConfiguracionJs();
		$html .= '<form action="mul_multimedia_popup_general_upd.php" name="formmultimediapopup" id="formmultimediapopup" method="post">'; 
		$html .= '<input  type="hidden" id="prefijo" name="prefijo" value="'.$this->prefijo.'" />';
		$html .= '<input  type="hidden" id="codigoTablaRelacionMultimedia" name="codigoTablaRelacionMultimedia" value="'.$this->codigoTablaRelacionMultimedia.'"  />';
		$html .= '</form>';
		$html .= '<div class="multimedia">';
        $html .= '<div id="tabs">';
        $html .= '<h3>Archivos Multimedia</h3>';
        $html .= '<ul>';
		if($this->TipoMultimedia['tieneimg'])
        	$html .= '<li><a href="#fotos">Fotos</a></li>';
        if($this->TipoMultimedia['tienevideo'])
			$html .= '<li><a href="#videos">Videos</a></li>';
        if($this->TipoMultimedia['tieneaudio'])
			$html .= '<li><a href="#audios">Audios</a></li>';
        if($this->TipoMultimedia['tienearchivos'])
			$html .= '<li><a href="#files">Archivos</a></li>';
        $html .= '</ul>';                          
		if($this->TipoMultimedia['tieneimg'])
			$html .= '<div id="fotos">&nbsp;</div>';
        if($this->TipoMultimedia['tienevideo'])
			$html .= '<div id="videos">&nbsp;</div>';
        if($this->TipoMultimedia['tieneaudio'])
			$html .= '<div id="audios">&nbsp;</div>';                      
        if($this->TipoMultimedia['tienearchivos'])
			$html .= '<div id="files">&nbsp;</div>';                      
        $html .= '</div>';
        $html .= '</div>';
		
		return $html;
		
	}





	public function CargarListadoMultimedia($datos,&$arreglo)
	{
		$oMultimediaGeneral = new cMultimediaGeneral($this->conexion,$this->formato);
		$oMultimediaGeneral->setTipo($datos['prefijo']);
		$objeto = $oMultimediaGeneral->getTipo();

		if(!$objeto->BuscarMultimedias($datos,$arreglo))
			return false;

		return true;		
	}
	
	
	
	private function CargarConfiguracionJs()
	{
		$arreglo = array();
		if(!$this->TipoMultimedia['tieneimg'])
			$arreglo[] = "Imagen:false";
		
		if(!$this->TipoMultimedia['tieneaudio'])	
			$arreglo[] = "Audio:false";
		
		if(!$this->TipoMultimedia['tienevideo'])
			$arreglo[] = "Video:false";

		if(!$this->TipoMultimedia['tienearchivos'])
			$arreglo[] = "Archivos:false";

		$html = "<script type=\"text/javascript\">";
		
		$html .= "var sizeLimitFile = ".TAMANIOARCHIVOS."; \n";
		$html .= "var sizeLimitFileAudio = ".TAMANIOARCHIVOSAUDIO."; \n";
		
		$html .= "var ObjMultimedia; ";
		$html .= "jQuery(document).ready(function(){ \n";
		$html .= "$('#tabs').tabs(); \n";
		$html .= "ObjMultimedia = $(document).multimediaBigTree({";
			$html .= implode(",",$arreglo);	
		$html .= "});";
		$html .= "ObjMultimedia.Inicializate();});";
		$html .= "</script>";
		return $html;
	}
	
}//fin clase	

?>