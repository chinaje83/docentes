<?php
/*
CLASE LOGICA PARA EL MANEJO DE LOS ARCHIVOS MULTIMEDIA.
*/
include(DIR_CLASES_DB."cMultimedia.db.php");

class cMultimedia extends cMultimediadb
{
	protected $conexion;
	protected $formato;
	protected $carpeta;


	// Constructor de la clase
	function __construct($conexion,$carpeta,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		$this->carpeta = $carpeta;
		parent::__construct();
    }

	// Destructor de la clase
	function __destruct() {
		parent::__destruct();
    }


//-----------------------------------------------------------------------------------------
//							 PUBLICAS
//-----------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------
// Retorna la direccion donde buscar la imagen

// Par�metros de Entrada:
//		carpeta: Carpeta a donde buscar el archivo
//		archivo: Nombre del archivo a buscar
// Retorna:
//		un string con la direccion de la imagen

	public function DevolverDireccionImgThumb($carpeta,$archivo)
	{
		return DOMINIO_SERVIDOR_MULTIMEDIA.$carpeta.CARPETA_SERVIDOR_MULTIMEDIA_THUMBS.$archivo;

	}

//-----------------------------------------------------------------------------------------
// Retorna la direccion donde buscar la imagen

// Par�metros de Entrada:
//		carpeta: Carpeta a donde buscar el archivo
//		archivo: Nombre del archivo a buscar
// Retorna:
//		un string con la direccion de la imagen

	public function DevolverDireccionImgThumbXL($carpeta,$archivo)
	{
		return DOMINIO_SERVIDOR_MULTIMEDIA.$carpeta.CARPETA_SERVIDOR_MULTIMEDIA_THUMBSXL.$archivo;

	}





//-----------------------------------------------------------------------------------------
// Retorna la direccion donde buscar la imagen

// Par�metros de Entrada:
//		carpeta: Carpeta a donde buscar el archivo
//		archivo: Nombre del archivo a buscar
// Retorna:
//		un string con la direccion de la imagen

	public function DevolverDireccionImgNormal($carpeta,$archivo)
	{
		return DOMINIO_SERVIDOR_MULTIMEDIA.$carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES.$archivo;

	}//-----------------------------------------------------------------------------------------
// Retorna la direccion donde buscar la imagen por Tama�o

// Par�metros de Entrada:
//		carpeta: Carpeta a donde buscar el archivo
//		archivo: Nombre del archivo a buscar
// Retorna:
//		un string con la direccion de la imagen

	public function DevolverDireccionImgTamanio($carpeta,$tamanio,$archivo)
	{
		return DOMINIO_SERVIDOR_MULTIMEDIA.$carpeta.$tamanio.$archivo;

	}


//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------
// Retorna la direccion donde buscar la imagen (thumb) de youtube

// Par�metros de Entrada:
//		id: Id del video de youtube
// Retorna:
//		un string con la direccion de la imagen del video de youtube


	public function DevolverDireccionThumbImgYoutube($id,$imagen=1)
	{
		return cMultimedia::ArmarImagenVideo(YOU,$id);

	}

//-----------------------------------------------------------------------------------------
// Retorna la direccion donde buscar la imagen (thumb) de youtube

// Par�metros de Entrada:
//		id: Id del video de youtube
// Retorna:
//		un string con la direccion de la imagen del video de youtube


	public function DevolverDireccionThumbImgVimeo($id,$imagen=1)
	{
		return cMultimedia::ArmarImagenVideo(VIM,$id);

	}


//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------
// Retorna la direccion donde buscar la imagen (thumb) de youtube

// Par�metros de Entrada:
//		id: Id del video de youtube
// Retorna:
//		un string con la direccion de la imagen del video de youtube

	public function DevolverDireccionThumbImgAudio()
	{
		return "images/icono_sonido.png";
	}

//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------
// Retorna la direccion donde buscar la imagen (thumb) de youtube

// Par�metros de Entrada:
//		id: Id del video de youtube
// Retorna:
//		un string con la direccion de la imagen del video de youtube

	public function DevolverDireccionThumbImgDefault()
	{
		return "images/default_2.png";
	}


//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------
// Retorna el c�digo html para visualizar un archivo multimedia

// Par�metros de Entrada:
//		datosmultimedia: Registro de la base de datos del archivo multimedia
// Retorna:
//		un string con el c�digo html para visualizar

	public function VisualizarArchivoMultimedia($datosmultimedia)
	{
		$html = "";
		switch($datosmultimedia['multimediatipoarchivo'])
		{
			case "VIM":
				$html = '<iframe width="500" height="299" src="http://player.vimeo.com/video/'.$datosmultimedia['multimediaidexterno'].'" frameborder="0" allowfullscreen></iframe>';
				break;

			case "YOU":
				$html = '<iframe width="500" height="299" src="http://www.youtube.com/embed/'.$datosmultimedia['multimediaidexterno'].'" frameborder="0" allowfullscreen></iframe>';
				break;

			case "FLV":
			case "MP4":
				$html .= '<object type="application/x-shockwave-flash" data="player/player.swf" width="400" height="200">';
				$html .= '<param name="movie" value="player/player.swf" />';
				$html .= '<param name="FlashVars" value="file='.DOMINIO_SERVIDOR_MULTIMEDIA.$datosmultimedia['multimediacatcarpeta']."videos/".$datosmultimedia['multimediaubic'].'" />';
				$html .= '<param name="quality" value="high" />';
				$html .= '<param name="autoSize" value="false" />';
				$html .= '<param name="mantainAspectRadio" value="false" />';
				$html .= '<param name="wmode" value="transparent" />';
				$html .= '<param name="menu" value="false" />';
				$html .= '</object>';
				break;

			case "MP3":
				$html .= '<object type="application/x-shockwave-flash" data="player/player.swf" width="400" height="24">';
				$html .= '<param name="movie" value="player/player.swf" />';
				$html .= '<param name="FlashVars" value="file='.DOMINIO_SERVIDOR_MULTIMEDIA.$datosmultimedia['multimediacatcarpeta']."audios/".$datosmultimedia['multimediaubic'].'" />';
				$html .= '<param name="quality" value="high" />';
				$html .= '<param name="autoSize" value="false" />';
				$html .= '<param name="mantainAspectRadio" value="false" />';
				$html .= '<param name="wmode" value="transparent" />';
				$html .= '<param name="menu" value="false" />';
				$html .= '</object>';
			break;

			case "GOEA":
				$html = '<iframe width="580" height="118" src="http://www.goear.com/embed/sound/'.$datosmultimedia['multimediaidexterno'].'"  marginheight="0" align="top" scrolling="no" frameborder="0" hspace="0" vspace="0" allowfullscreen></iframe>';
			break;

			case "JPG":
			case "GIF":
			case "PNG":
				$html = '<img style="max-height:300px; max-width:500px; text-align:center" src="'.DOMINIO_SERVIDOR_MULTIMEDIA.$datosmultimedia['multimediacatcarpeta'].CARPETA_SERVIDOR_MULTIMEDIA_THUMBSXL.$datosmultimedia['multimediaubic'].'" alt="Imagen">';
				break;

			default:
				$html = '<a href="'.DOMINIO_SERVIDOR_MULTIMEDIA.$datosmultimedia['multimediacatcarpeta'].CARPETA_SERVIDOR_MULTIMEDIA_ARCHIVOS.$datosmultimedia['multimediaubic'].'" target="_blank"><strong>Bajar archivo &raquo;&raquo;</strong></a>';
				break;

		}

		return $html;

	}

	public function VisualizarArchivoSimpleMultimedia($datosmultimedia)
  	{
  		  $html = "";
  		  $html = '<img style="vertical-align: bottom; width: 120px;" src="'.$this->DevolverDireccionImg($datosmultimedia).'" alt="Imagen">';

  		  return $html;

  	}



//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------
// Retorna la direccion donde buscar la imagen

// Par�metros de Entrada:
//		multimediatipocod: Tipo de multimedia del registro multimedia a mostrar
//		multimediacatcarpeta: Carpeta multimedia en el caso de tipo de imagen
//		multimediaubic: Nombre del archivo multimedia en caso de tipo de imagen
//		multimediaidexterno: Id del video de youtube en caso de video externo
// Retorna:
//		un string con la direccion de la imagen del video de youtube

	public function DevolverDireccionImg($datos)
	{

		switch($datos['multimediatipocod'])
		{
			case YOU:
				if (isset($datos['previewubic']) && $datos['previewubic']!="")
					return cMultimedia::DevolverDireccionImgThumb($datos['multimediacatcarpeta'],$datos['previewubic']);
				else
					return cMultimedia::DevolverDireccionThumbImgYoutube($datos['multimediaidexterno']);
				break;

			case VIM:
				if (isset($datos['previewubic']) && $datos['previewubic']!="")
					return cMultimedia::DevolverDireccionImgThumb($datos['multimediacatcarpeta'],$datos['previewubic']);
				else
					return cMultimedia::DevolverDireccionThumbImgVimeo($datos['multimediaidexterno']);
				break;

			case JPG:
			case PNG:
			case GIF:
				return cMultimedia::DevolverDireccionImgThumb($datos['multimediacatcarpeta'],$datos['multimediaubic']);
				break;

			case GOEA:
			case MP3:
				if (isset($datos['previewubic']) && $datos['previewubic']!="")
					return cMultimedia::DevolverDireccionImgThumb($datos['multimediacatcarpeta'],$datos['previewubic']);
				else
					return cMultimedia::DevolverDireccionThumbImgDefault();
				break;

			case FLV:
			case MP4:
				if (isset($datos['previewubic']) && $datos['previewubic']!="")
					return cMultimedia::DevolverDireccionImgThumb($datos['multimediacatcarpeta'],$datos['previewubic']);
				else
					return cMultimedia::DevolverDireccionThumbImgDefault();
				break;

			default:
				$archivo = "";
				if (isset($datos['multimediatipoicono']))
					$archivo  = $datos['multimediatipoicono'];

				return $archivo;
				break;
		}

	}


	public function DireccionImgThumbXL($datos)
	{

		switch($datos['multimediatipocod'])
		{
			case YOU:
				if (isset($datos['previewubic']) && $datos['previewubic']!="")
					return cMultimedia::DevolverDireccionImgThumbXL($datos['multimediacatcarpeta'],$datos['previewubic']);
				else
					return cMultimedia::DevolverDireccionThumbImgYoutube($datos['multimediaidexterno']);
				break;

			case VIM:
				if (isset($datos['previewubic']) && $datos['previewubic']!="")
					return cMultimedia::DevolverDireccionImgThumbXLs($datos['multimediacatcarpeta'],$datos['previewubic']);
				else
					return cMultimedia::DevolverDireccionThumbImgVimeo($datos['multimediaidexterno']);
				break;

			case JPG:
			case PNG:
			case GIF:
				return cMultimedia::DevolverDireccionImgThumbXL($datos['multimediacatcarpeta'],$datos['multimediaubic']);
				break;

			case GOEA:
			case MP3:
				if (isset($datos['previewubic']) && $datos['previewubic']!="")
					return cMultimedia::DevolverDireccionImgThumb($datos['multimediacatcarpeta'],$datos['previewubic']);
				else
					return cMultimedia::DevolverDireccionThumbImgDefault();
				break;

			case FLV:
			case MP4:
				if (isset($datos['previewubic']) && $datos['previewubic']!="")
					return cMultimedia::DevolverDireccionImgThumbXL($datos['multimediacatcarpeta'],$datos['previewubic']);
				else
					return cMultimedia::DevolverDireccionThumbImgDefault();
				break;

			default:
				$archivo = "";
				if (isset($datos['multimediatipoicono']))
					$archivo  = $datos['multimediatipoicono'];

				return $archivo;
				break;
		}

	}
//-----------------------------------------------------------------------------------------
// Retorna la direccion donde buscar la imagen

// Par�metros de Entrada:
//		carpeta: Carpeta a donde buscar el archivo
//		archivo: Nombre del archivo a buscar
// Retorna:
//		un string con la direccion de la imagen

	public function DireccionImgNormal($datos)
	{
		switch($datos['multimediatipocod'])
		{

			case JPG:
			case PNG:
			case GIF:
				return cMultimedia::DevolverDireccionImgNormal($datos['multimediacatcarpeta'],$datos['multimediaubic']);
				break;

			default:
				$archivo = "";
				if (isset($datos['multimediatipoicono']))
					$archivo  = $datos['multimediatipoicono'];

				return $archivo;
				break;
		}
	}
//-----------------------------------------------------------------------------------------
// Retorna una consulta los datos de multimedia

// Par�metros de Entrada:
//		datos: arreglo de datos
//			multimediacod = codigo del archivo multimedia

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function BuscarMultimediaxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarMultimediaxCodigo($datos,$resultado,$numfilas))
			return false;

		return true;
	}




// Retorna una consulta con los archivos multimedia.

// Par�metros de Entrada:
//		datosbuscar:

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	function BusquedaAvanzada ($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'catcod'=> "",
			'estadomulcatcod'=> 0,
			'estadomultimediadesc'=> 0,
			'multimediadesc'=> "",
			'xmultimediatitulo'=> 0,
			'multimediatitulo'=> "",
			'estadomultimediatipoarchivo'=> 0,
			'multimediatipoarchivo'=> "",
			'estadomultimedianombre'=> 0,
			'multimedianombre'=> "",
			'estadomultimediaestadocod'=> 0,
			'multimediaestadocod'=> "-1",
			'estadomultimediacatcod'=> 0,
			'multimediacatcod'=> "",
			'estadomultimediaconjuntocod'=> 0,
			'multimediaconjuntocod'=> "",
			'xmultimediaidexterno'=> 0,
			'multimediaidexterno'=> "",
			'orderby'=> "multimediacod DESC",
			'limit'=> ""
			);

		if (isset ($datos['multimediatitulo']) && $datos['multimediatitulo']!="")
		{
			$sparam['multimediatitulo'] = $datos['multimediatitulo'];
			$sparam['xmultimediatitulo'] = 1;
		}


		if (isset ($datos['multimediaidexterno']) && $datos['multimediaidexterno']!="")
		{
			$sparam['multimediaidexterno']= $datos['multimediaidexterno'];
			$sparam['xmultimediaidexterno']= 1;
		}
		if (isset ($datos['catcod']) && $datos['catcod']!="")
		{
			$sparam['catcod']= $datos['catcod'];
			$sparam['estadomulcatcod']= 1;
		}
		if (isset ($datos['multimediatipoarchivo']) && $datos['multimediatipoarchivo']!="")
		{
			$sparam['multimediatipoarchivo']= $datos['multimediatipoarchivo'];
			$sparam['estadomultimediatipoarchivo']= 1;
		}
		if (isset ($datos['multimediadesc']) && $datos['multimediadesc']!="")
		{
			$sparam['multimediadesc']= $datos['multimediadesc'];
			$sparam['estadomultimediadesc']= 1;
		}
		if (isset ($datos['multimedianombre']) && $datos['multimedianombre']!="")
		{
			$sparam['multimedianombre']= $datos['multimedianombre'];
			$sparam['estadomultimedianombre']= 1;
		}
		if (isset ($datos['multimediaestadocod']) && $datos['multimediaestadocod']!="")
		{
			$sparam['multimediaestadocod']= $datos['multimediaestadocod'];
			$sparam['estadomultimediaestadocod']= 1;
		}
		if (isset ($datos['multimediacatcod']) && $datos['multimediacatcod']!="")
		{
			$sparam['multimediacatcod']= $datos['multimediacatcod'];
			$sparam['estadomultimediacatcod']= 1;
		}
		if (isset ($datos['multimediaconjuntocod']) && $datos['multimediaconjuntocod']!="")
		{
			$sparam['multimediaconjuntocod']= $datos['multimediaconjuntocod'];
			$sparam['estadomultimediaconjuntocod']= 1;
		}

		if (isset ($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if (isset ($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];


		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;

		return true;
	}



// Retorna una consulta con los archivos multimedia.

// Par�metros de Entrada:
//		datosbuscar:

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

function BusquedaPopup($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'criteriobusqueda'=> "",
			'estadomultimediaconjuntocod'=> 0,
			'multimediaconjuntocod'=> "",
			'multimediaestadocod'=> ACTIVO,
			'xmultimediaidexterno'=> 0,
			'multimediaidexterno'=> "",
			'limit'=> "",
			'orderby'=> ""
			);

		if (isset ($datos['criteriobusqueda']) && $datos['criteriobusqueda']!="")
		{
			$sparam['criteriobusqueda'] = $datos['criteriobusqueda'];
			$sparam['xcriteriobusqueda'] = 1;
		}

		if (isset ($datos['multimediaconjuntocod']) && $datos['multimediaconjuntocod']!="")
		{
			$sparam['multimediaconjuntocod']= $datos['multimediaconjuntocod'];
			$sparam['estadomultimediaconjuntocod']= 1;
		}
		if (isset ($datos['multimediaestadocod']) && $datos['multimediaestadocod']!="")
		{
			$sparam['multimediaestadocod']= $datos['multimediaestadocod'];
		}

		if (isset ($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (isset ($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];


		if (!parent::BusquedaPopup($sparam,$resultado,$numfilas))
			return false;

		return true;
	}
//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// Insertar un nuevo archivo multimedia imagen
// Par�metros de Entrada:
//		datos: array asociativo con los datos a agregar
//			multimediatitulo: titulo del archivo multimedia
//			multimediadesc: descripcion del archivo multimedia
//			multimedianombre: nombre del archivo
//			multimediaubic: nombre de la ubicacion del archivo
//			multimediaidexterno: id externo
//			multimediatipocod: tipo del multimedia
//		imagen: archivo imagen
// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function InsertarImagenDesdeTemporal($datos,&$multimediacod)
	{

		$pathinfo = pathinfo($datos['multimedianombre']);
		$extension = strtolower($pathinfo['extension']);

		$oMultimediaTipos = new cMultimediaTipos($this->conexion,$this->formato);
		$datosbuscar['multimediatipoarchivo'] = strtoupper($extension);
		if(!$oMultimediaTipos->BuscarMultimediaTiposxTipoArchivo($datosbuscar,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Formato de imagen no permitido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datostipo = $this->conexion->ObtenerSiguienteRegistro($resultado);

		$datos['multimediatipocod'] = $datostipo['multimediatipocod'];
		if ($datos['multimediatitulo']=="")
			$datos['multimediatitulo'] = "NULL";
		$datos['multimediaidexterno'] = "NULL";
		$datos['multimediaestadocod'] = MULTACTIVO;
		$carpetaFecha = date("Ym")."/";
		$datos['multimediaubic'] = $carpetaFecha.$datos['multimediaubic'];

		if (!$this->Insertar($datos,$multimediacod))
			return false;


		if(!is_dir(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES.$carpetaFecha)){
			@mkdir(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES.$carpetaFecha);
			@mkdir(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_THUMBS.$carpetaFecha);
			@mkdir(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_THUMBSXL.$carpetaFecha);
		}
		//Subir imagenes
		$oImagen = new cFuncionesMultimedia();
		$nombrearchivo = $datos['mul_multimedia_file'];
		if($datostipo['multimediatipocod']==GIF)
		{
			if(!$oImagen->MoverArchivoTemporalCompleto($nombrearchivo,PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES.$carpetaFecha))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al mover el archivo temporal.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		else
		{
			if(!$oImagen->MoverArchivoTemporal($nombrearchivo,PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES.$carpetaFecha))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al mover el archivo temporal.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		$nombrearchivo = $datos['multimediaubic'];

		//multimediatipocod JPG,GIF,etc
		$oMultimediaFormatos = new cMultimediaFormatos($this->conexion,$this->formato);
		if(!$oMultimediaFormatos->BuscarMultimediaFormatosActivos($resultado,$numfilas))
			return false;

		$carpetalocal = PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES;

		$carpetadestino = PATH_STORAGE."/".$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_THUMBS;
		if(!$oImagen->RedimensionarImagen($nombrearchivo,$carpetalocal,$carpetadestino,TAMANIOTHUMB,TAMANIOTHUMB))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al generar las imagenes.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$carpetadestino = PATH_STORAGE."/".$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_THUMBSXL;
		if(!$oImagen->RedimensionarImagen($nombrearchivo,$carpetalocal,$carpetadestino,TAMANIOTHUMBXL,TAMANIOTHUMBXL))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al generar las imagenes.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		while($fila=$this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			$carpetadestino = PATH_STORAGE."/".$this->carpeta.$fila['formatocarpeta']."/";

			if (!is_dir ($carpetadestino.$carpetaFecha))
			{
				if (!mkdir($carpetadestino.$carpetaFecha))
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al crear la carpeta en el servidor de multimedia (".$datoscategoria['multimediacatcarpeta']."".$datos['formatocarpeta'].") ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
			}

			if ($fila['formatocrop']==1)
			{
				if(!$oImagen->CropearImagen($nombrearchivo,$carpetalocal,$carpetadestino,$fila['formatoancho'],$fila['formatoalto']))
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al generar las imagenes.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
			}else
			{
				if(!$oImagen->RedimensionarImagen($nombrearchivo,$carpetalocal,$carpetadestino,$fila['formatoancho'],$fila['formatoalto']))
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al generar las imagenes.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
			}
		}


		return true;

	}



//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// Insertar un nuevo archivo multimedia imagen
// Par�metros de Entrada:
//		datos: array asociativo con los datos a agregar
//			multimediatitulo: titulo del archivo multimedia
//			multimediadesc: descripcion del archivo multimedia
//			multimedianombre: nombre del archivo
//			multimediaubic: nombre de la ubicacion del archivo
//			multimediaidexterno: id externo
//			multimediatipocod: tipo del multimedia
//		imagen: archivo imagen
// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function InsertarArchivoDesdeTemporal($datos,&$multimediacod)
	{
		$pathinfo = pathinfo($datos['multimedianombre']);
		$extension = strtolower($pathinfo['extension']);
		$oMultimediaTipos = new cMultimediaTipos($this->conexion,$this->formato);
		$datosbuscar['multimediatipoarchivo'] = strtoupper($extension);
		if(!$oMultimediaTipos->BuscarMultimediaTiposxTipoArchivo($datosbuscar,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Formato de archivo no permitido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datostipo = $this->conexion->ObtenerSiguienteRegistro($resultado);

		$datos['multimediatipocod'] = $datostipo['multimediatipocod'];
		if ($datos['multimediatitulo']=="")
			$datos['multimediatitulo'] = "NULL";
		$datos['multimediaidexterno'] = "NULL";
		$datos['multimediaestadocod'] = MULTACTIVO;
		$carpetaFecha = date("Ym")."/";
		$datos['multimediaubic'] = $carpetaFecha.$datos['multimediaubic'];

		if (!$this->Insertar($datos,$multimediacod))
			return false;


		if(!is_dir(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ARCHIVOS.$carpetaFecha)){
			@mkdir(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ARCHIVOS.$carpetaFecha);
		}

		//Subir imagenes
		$oImagen = new cFuncionesMultimedia();
		$nombrearchivo = $datos['mul_multimedia_file'];
		if(!$oImagen->MoverArchivoTemporalCompleto($nombrearchivo,PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ARCHIVOS.$carpetaFecha))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al mover el archivo temporal.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;

	}




//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// Insertar un nuevo archivo multimedia audio desde una carpeta temporal
// Par�metros de Entrada:
//		datos: array asociativo con los datos a agregar
//			multimediatitulo: titulo del archivo multimedia
//			multimediadesc: descripcion del archivo multimedia
//			multimedianombre: nombre del archivo
//			multimediaubic: nombre de la ubicacion del archivo
//			multimediaidexterno: id externo
//			multimediatipocod: tipo del multimedia
//		imagen: archivo imagen
// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function InsertarAudioDesdeTemporal($datos,&$multimediacod)
	{

		$pathinfo = pathinfo($datos['multimedianombre']);
		$extension = strtolower($pathinfo['extension']);

		$oMultimediaTipos = new cMultimediaTipos($this->conexion,$this->formato);
		$datosbuscar['multimediatipoarchivo'] = strtoupper($extension);
		if(!$oMultimediaTipos->BuscarMultimediaTiposxTipoArchivo($datosbuscar,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Formato de audio no permitido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datostipo = $this->conexion->ObtenerSiguienteRegistro($resultado);


		$datos['multimediatipocod'] = $datostipo['multimediatipocod'];
		if ($datos['multimediatitulo']=="")
			$datos['multimediatitulo'] = "NULL";
		$datos['multimediaidexterno'] = "NULL";
		$datos['multimediaestadocod'] = MULTACTIVO;

		if (!$this->Insertar($datos,$multimediacod))
			return false;

		//Subir imagenes
		$oAudio = new cFuncionesMultimedia();
		$nombrearchivo = $datos['mul_multimedia_file'];
		if(!$oAudio->MoverArchivoTemporalCompleto($nombrearchivo,PATH_STORAGE.$this->carpeta))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al mover el archivo temporal.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;

	}



//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// Insertar un nuevo archivo multimedia video desde una carpeta temporal
// Par�metros de Entrada:
//		datos: array asociativo con los datos a agregar
//			multimediatitulo: titulo del archivo multimedia
//			multimediadesc: descripcion del archivo multimedia
//			multimedianombre: nombre del archivo
//			multimediaubic: nombre de la ubicacion del archivo
//			multimediatipocod: tipo del multimedia
//		imagen: archivo imagen
// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function InsertarVideoDesdeTemporal($datos,&$multimediacod)
	{

		$pathinfo = pathinfo($datos['multimedianombre']);
		$extension = strtolower($pathinfo['extension']);

		$oMultimediaTipos = new cMultimediaTipos($this->conexion,$this->formato);
		$datosbuscar['multimediatipoarchivo'] = strtoupper($extension);
		if(!$oMultimediaTipos->BuscarMultimediaTiposxTipoArchivo($datosbuscar,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Formato de audio no permitido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datostipo = $this->conexion->ObtenerSiguienteRegistro($resultado);

		$carpetaFecha = date("Ym")."/";
		$datos['multimediaubic'] = $carpetaFecha.$datos['multimediaubic'];

		if(!is_dir(PATH_STORAGE.$this->carpeta.$carpetaFecha)){
			@mkdir(PATH_STORAGE.$this->carpeta.$carpetaFecha);
		}


		$datos['multimediatipocod'] = $datostipo['multimediatipocod'];
		if ($datos['multimediatitulo']=="")
			$datos['multimediatitulo'] = "NULL";
		$datos['multimediaidexterno'] = "NULL";
		$datos['multimediaestadocod'] = MULTACTIVO;

		if (!$this->Insertar($datos,$multimediacod))
			return false;

		//Subir imagenes
		$oVideo = new cFuncionesMultimedia();
		$nombrearchivo = $datos['mul_multimedia_file'];
		if(!$oVideo->MoverArchivoTemporalCompleto($nombrearchivo,PATH_STORAGE.$this->carpeta.$carpetaFecha))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al mover el archivo temporal.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;

	}



//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// Insertar un nuevo archivo multimedia audio desde una carpeta temporal
// Par�metros de Entrada:
//		datos: array asociativo con los datos a agregar
//			multimediatitulo: titulo del archivo multimedia
//			multimediadesc: descripcion del archivo multimedia
//			multimedianombre: nombre del archivo
//			multimediaubic: nombre de la ubicacion del archivo
//			multimediaidexterno: id externo
//			multimediatipocod: tipo del multimedia
//		imagen: archivo imagen
// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function InsertarAudioExterno($datos,&$multimediacod)
	{

		$extension = $datos['extensionaudio'];
		$oMultimediaTipos = new cMultimediaTipos($this->conexion,$this->formato);
		$datosbuscar['multimediatipoarchivo'] = strtoupper($extension);

		if(!$oMultimediaTipos->BuscarMultimediaTiposxTipoArchivo($datosbuscar,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Formato de audio no permitido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datostipo = $this->conexion->ObtenerSiguienteRegistro($resultado);

		$datos['multimediatipocod'] = $datostipo['multimediatipocod'];
		if ($datos['multimediatitulo']=="")
			$datos['multimediatitulo'] = "NULL";
		$datos['multimediaidexterno'] = $datos['multimediaidexterno'];
		$datos['multimediaestadocod'] = MULTACTIVO;
		$datos['multimediaubic'] = "NULL";
		if (!$this->Insertar($datos,$multimediacod))
			return false;

		return true;

	}



// Par�metros de Entrada:
//		multimediacod= codigo multimedia.
//      multimediaestadocod = nuevo estado del multimedia

// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no
	public function DesActivarMultimedia($datos)
	{
		if(!$this->TieneMultimediaAsociado($datos))
			return false;

		$datosmodificar['multimediacod'] = $datos['multimediacod'];
		$datosmodificar['multimediaestadocod'] = MULTNOACTIVO;
		if (!$this->ModificarEstadoMultimedia($datosmodificar))
			return false;

		return true;
	}

	// Par�metros de Entrada:
//		multimediacod= codigo multimedia.
//      multimediaestadocod = nuevo estado del multimedia

// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no
	public function ActivarMultimedia($datos)
	{

		$datosmodificar['multimediacod'] = $datos['multimediacod'];
		$datosmodificar['multimediaestadocod'] = MULTACTIVO;
		if (!$this->ModificarEstadoMultimedia($datosmodificar))
			return false;

		return true;
	}
// Retorna true o false si pudo cambiarle el estado del multimedia
// Par�metros de Entrada:
//		multimediacod = codigo multimedia.
// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function ModificarEstadoMultimedia($datos)
	{
		if (!parent::ModificarEstadoMultimedia($datos))
			return false;

		return true;
	}

//-----------------------------------------------------------------------------------------
// Genera un nuevo archivo multimedia

// Par�metros de Entrada:
//		datos: arreglo de datos
//				FOTOS


// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function InsertarImagenMultimedia($datos)
	{


		$datos['multimediaconjuntocod'] = FOTOS;
		$datos['multimediadesc'] = $datos['multimedia_desc'];
		$datos['multimediatitulo'] = $datos['multimedia_titulo'];
		$datos['multimedianombre'] = $datos['mul_multimedia_name'];
		$datos['multimediaubic'] = $datos['mul_multimedia_file'];
		$datos['multimediacatcod'] = 1;
		$this->carpeta = CARPNOTICIAS;
		if(!$this->InsertarImagenDesdeTemporal($datos,$multimediacod))
			return false;

		return true;
	}

	public function InsertarAudioMultimedia($datos)
	{
		// CREO EL OBJETO DE MULTIMEDIA
		$accion = 1;
		if (isset($datos['tipovideosubir']) && $datos['tipovideosubir']!="")
			$accion = $datos['tipovideosubir'];


		$oMultimedia = new cMultimedia($this->conexion,CARPNOTICIAS."audios/",$this->formato);
		switch($accion)
		{
			case 1:
				//SETEO LOS CAMPOS A INSERTAR EN LA TABLA MULTIMEDIA
				$datos['multimediaconjuntocod'] = AUDIOS; // TIPO DE MULTIMEDIA CONJUNTO
				$datos['multimediatitulo'] = $datos['multimedia_titulo'];
				$datos['multimediadesc'] = $datos['multimedia_desc'];  //DESCRIPCION DEL MULTIMEDIA
				$datos['multimedianombre'] = $datos['mul_multimedia_name'];
				$datos['multimediaubic'] = $datos['mul_multimedia_file'];
				$datos['multimediacatcod'] = 1; // MUL_MULTIMEDIA_CATEGORIAS (VALOR 1 SIEMPRE)

				//INSERTO EL ARCHIVO MULTIMEDIA EN LA TABLA MULTIMEDIA
				if(!$this->InsertarAudioDesdeTemporal($datos,$multimediacod))
					return false;
				break;

			case 2:


				//SETEO LOS CAMPOS A INSERTAR EN LA TABLA MULTIMEDIA
				$datos['multimediaconjuntocod'] = AUDIOS; // TIPO DE MULTIMEDIA CONJUNTO
				$datos['multimediatitulo'] = $datos['multimedia_titulo'];
				$datos['multimediadesc'] = $datos['multimedia_desc'];  //DESCRIPCION DEL MULTIMEDIA
				$datos['multimediacatcod'] = 1; // MUL_MULTIMEDIA_CATEGORIAS (VALOR 1 SIEMPRE)
				$datos['multimedianombre']="";
				switch ($datos["cajasonidoexterno"])
				{
					case "GOEA":
						$datos['mulvideotipopage']="Goear";
						$datos['multimedianombre']="Goear";
						$datos['extensionaudio']="GOEA";
						$datos['multimediaidexterno'] = $datos['multcodgoearaudio'];
					break;
					default:
						return false;
						break;
				}
				if(!$this->InsertarAudioExterno($datos,$multimediacod))
					return false;

				//INSERTO EL ARCHIVO MULTIMEDIA EN LA TABLA MULTIMEDIA
				break;
		}

		return true;
	}

	public function InsertarVideoMultimedia($datos)
	{
		if($datos["cajavideosexterno"]=="VIM")
			$datos['mulvideotipopage']="Vimeo";

		if($datos["cajavideosexterno"]=="YOU")
			$datos['mulvideotipopage']="YouTube";

		if(isset($datos['mulcodepagey']) && $datos['mulcodepagey']!="")
			$datos['mulcodepage']=$datos['mulcodepagey'];


		$datos['multimediaconjuntocod'] = VIDEOS;
		$datos['multimediadesc'] = $datos['multimedia_desc'];
		$datos['multimediatitulo'] = $datos['multimedia_titulo'];
		$datos['multimedianombre'] = $datos['mulvideotipopage'];
		$datos['multimediaidexterno'] = $datos['mulcodepage'];
		$datos['multimediacatcod'] = 1;
		$datos['extensionvideo'] = $datos['cajavideosexterno']; // Youtube
		$this->carpeta = CARPNOTICIAS;

		if(!$this->InsertarVideo($datos,$multimediacod))
			return false;

		return true;
	}

	public function InsertarArchivosMultimedia($datos)
	{
		$datos['multimediaconjuntocod'] = FILES;
		$datos['multimediadesc'] = $datos['multimedia_desc'];
		$datos['multimediatitulo'] = $datos['multimedia_titulo'];
		$datos['multimedianombre'] = $datos['mul_multimedia_name'];
		$datos['multimediaubic'] = $datos['mul_multimedia_file'];
		$datos['multimediacatcod'] = 1;
		$this->carpeta = CARPNOTICIAS;
		if(!$this->InsertarArchivoDesdeTemporal($datos,$multimediacod))
			return false;

		return true;
	}


//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// Insertar un nuevo archivo multimedia imagen
// Par�metros de Entrada:
//		datos: array asociativo con los datos a agregar
//			multimediatitulo: titulo del archivo multimedia
//			multimediadesc: descripcion del archivo multimedia
//			multimedianombre: nombre del archivo
//			multimediaubic: nombre de la ubicacion del archivo
//			multimediaidexterno: id externo
//			multimediatipocod: tipo del multimedia
//		imagen: archivo imagen
// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function InsertarImagen($datos,&$multimediacod)
	{

		$pathinfo = pathinfo($datos['multimedianombre']);
		$extension = strtolower($pathinfo['extension']);

		$oMultimediaTipos = new cMultimediaTipos($this->conexion,$this->formato);
		$datosbuscar['multimediatipoarchivo'] = strtoupper($extension);
		if(!$oMultimediaTipos->BuscarMultimediaTiposxTipoArchivo($datosbuscar,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Formato de imagen no permitido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datostipo = $this->conexion->ObtenerSiguienteRegistro($resultado);

		$datos['multimediatipocod'] = $datostipo['multimediatipocod'];
		$datos['multimediatitulo'] = "NULL";
		$datos['multimediaidexterno'] = "NULL";
		$datos['multimediaestadocod'] = MULTACTIVO;
		$carpetaFecha = date("Ym")."/";
		$datos['multimediaubic'] = $carpetaFecha.$datos['multimediaubic'];

		if (!$this->Insertar($datos,$multimediacod))
			return false;

		$input = fopen($datos['ubicacionfisica'], "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);


		$carpetaFecha = date("Ym")."/";
		$nombrearchivo=$datos['multimediaubic'];

		if(!is_dir(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES.$carpetaFecha)){
			@mkdir(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES.$carpetaFecha);
			@mkdir(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_THUMBS.$carpetaFecha);
			@mkdir(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_THUMBSXL.$carpetaFecha);
		}
		if($datostipo['multimediatipocod']==GIF)
		{
			$target = fopen(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES.$nombrearchivo, "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			$target = fopen(PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES.$nombrearchivo, "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
			//Redimensiona la imagen al tama�o de la img normal
			$oImagen = new cFuncionesMultimedia();
			$carpetalocal = PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES;
			$carpetadestino = PATH_STORAGE."/".$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES;

			if(!$oImagen->RedimensionarImagen($nombrearchivo,$carpetalocal,$carpetadestino,TAMANIONORMAL,TAMANIONORMAL))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al generar las imagenes.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		//Subir imagenes
		$oImagen = new cFuncionesMultimedia();
		$carpetalocal = PATH_STORAGE.$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES;
		$carpetadestino = PATH_STORAGE."/".$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_THUMBS;
		if(!$oImagen->RedimensionarImagen($nombrearchivo,$carpetalocal,$carpetadestino,TAMANIOTHUMB,TAMANIOTHUMB))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al generar las imagenes.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		$carpetadestino = PATH_STORAGE."/".$this->carpeta.CARPETA_SERVIDOR_MULTIMEDIA_THUMBSXL;
		if(!$oImagen->CropearImagen($nombrearchivo,$carpetalocal,$carpetadestino,TAMANIOTHUMBXL,TAMANIOTHUMBXL))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al generar las imagenes.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		//multimediatipocod JPG,GIF,etc
		$oMultimediaFormatos = new cMultimediaFormatos($this->conexion,$this->formato);
		if(!$oMultimediaFormatos->BuscarMultimediaFormatosActivos($resultado,$numfilas))
			return false;


		while($fila=$this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			$carpetadestino = PATH_STORAGE."/".$this->carpeta.$fila['formatocarpeta']."/";
			if (!is_dir ($carpetadestino.$carpetaFecha))
			{
				if (!mkdir($carpetadestino.$carpetaFecha))
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al crear la carpeta en el servidor de multimedia (".$datoscategoria['multimediacatcarpeta']."".$datos['formatocarpeta'].") ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
			}

			if ($fila['formatocrop']==1)
			{
				if(!$oImagen->CropearImagen($nombrearchivo,$carpetalocal,$carpetadestino,$fila['formatoancho'],$fila['formatoalto']))
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al generar las imagenes.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
			}else
			{
				if(!$oImagen->RedimensionarImagen($nombrearchivo,$carpetalocal,$carpetadestino,$fila['formatoancho'],$fila['formatoalto']))
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al generar las imagenes.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
			}
		}
		return true;

	}



//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// Insertar un nuevo archivo multimedia audio desde un archivo subido
// Par�metros de Entrada:
//		datos: array asociativo con los datos a agregar
//			multimediatitulo: titulo del archivo multimedia
//			multimediadesc: descripcion del archivo multimedia
//			multimedianombre: nombre del archivo
//			multimediaubic: nombre de la ubicacion del archivo
//			multimediaidexterno: id externo
//			multimediatipocod: tipo del multimedia
//		imagen: archivo imagen
// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function InsertarAudio($datos,&$multimediacod)
	{

		$pathinfo = pathinfo($datos['multimedianombre']);
		$extension = strtolower($pathinfo['extension']);

		$oMultimediaTipos = new cMultimediaTipos($this->conexion,$this->formato);
		$datosbuscar['multimediatipoarchivo'] = strtoupper($extension);
		if(!$oMultimediaTipos->BuscarMultimediaTiposxTipoArchivo($datosbuscar,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Formato de audio no permitido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datostipo = $this->conexion->ObtenerSiguienteRegistro($resultado);

		$datos['multimediatipocod'] = $datostipo['multimediatipocod'];
		$datos['multimediatitulo'] = "NULL";
		$datos['multimediaidexterno'] = "NULL";
		$datos['multimediaestadocod'] = MULTACTIVO;

		if (!$this->Insertar($datos,$multimediacod))
			return false;

		//Subir audio
		$input = fopen($datos['ubicacionfisica'], "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);

		$nombrearchivo=$datos['multimediaubic'];

		$target = fopen(PATH_STORAGE.$this->carpeta.$nombrearchivo, "w");
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		return true;

	}



//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// Insertar un nuevo archivo multimedia imagen
// Par�metros de Entrada:
//		datos: array asociativo con los datos a agregar
//			multimediatitulo: titulo del archivo multimedia
//			multimediadesc: descripcion del archivo multimedia
//			multimediaidexterno: id externo
//			multimediatipocod: tipo del multimedia
//		imagen: archivo imagen
// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function InsertarVideo($datos,&$multimediacod)
	{
		$extension = $datos['extensionvideo'];
		$oMultimediaTipos = new cMultimediaTipos($this->conexion,$this->formato);
		$datosbuscar['multimediatipoarchivo'] = strtoupper($extension);

		if(!$oMultimediaTipos->BuscarMultimediaTiposxTipoArchivo($datosbuscar,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Formato de video no permitido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datostipo = $this->conexion->ObtenerSiguienteRegistro($resultado);

		$datos['multimediatipocod'] = $datostipo['multimediatipocod'];
		if ($datos['multimediatitulo']=="")
			$datos['multimediatitulo'] = "NULL";
		$datos['multimediaidexterno'] = $datos['multimediaidexterno'];
		$datos['multimediaestadocod'] = MULTACTIVO;
		$datos['multimediaubic'] = "NULL";
		if (!$this->Insertar($datos,$multimediacod))
			return false;

		return true;

	}



//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// Insertar un nuevo archivo multimedia

// Par�metros de Entrada:
//		datos: array asociativo con los datos a agregar
//			multimediatitulo: titulo del archivo multimedia
//			multimediadesc: descripcion del archivo multimedia
//			multimedianombre: nombre del archivo
//			multimediaubic: nombre de la ubicacion del archivo
//			multimediaidexterno: id externo
//			multimediatipocod: tipo del multimedia

// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function Insertar($datos,&$codigoinsertado)
	{

		if (!$this->_ValidarInsertar($datos))
			return false;


		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		return true;

	}



	public function EliminarLogica($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosmultimedia))
			return false;

		$datoseliminar['multimediacod'] = $datos['multimediacod'];
		$datoseliminar['multimediaestadocod'] = MULELIMINADO;
		if (!$this->ModificarEstadoMultimedia($datoseliminar))
			return false;

		return true;
	}


//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// Insertar un nuevo tag a la noticia

// Par�metros de Entrada:
//		datos: array asociativo con los datos a agregar
//			noticiacod: codigo de la noticia a eliminar

// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosmultimedia))
			return false;



		switch($datosmultimedia['multimediaconjuntocod'])
		{
			case FOTOS:
				$archivoNormal = PATH_STORAGE.$datosmultimedia['multimediacatcarpeta'].CARPETA_SERVIDOR_MULTIMEDIA_ORIGINALES.$datosmultimedia['multimediaubic'];
				@unlink($archivoNormal);
				$archivoThumb = PATH_STORAGE.$datosmultimedia['multimediacatcarpeta'].CARPETA_SERVIDOR_MULTIMEDIA_THUMBS.$datosmultimedia['multimediaubic'];
				@unlink($archivoThumb);
				$archivoXL = PATH_STORAGE.$datosmultimedia['multimediacatcarpeta'].CARPETA_SERVIDOR_MULTIMEDIA_THUMBSXL.$datosmultimedia['multimediaubic'];
				@unlink($archivoXL);


				//multimediatipocod JPG,GIF,etc
				$oMultimediaFormatos = new cMultimediaFormatos($this->conexion,$this->formato);
				if(!$oMultimediaFormatos->BuscarMultimediaFormatosActivos($resultado,$numfilas))
					return false;

				while($fila=$this->conexion->ObtenerSiguienteRegistro($resultado))
				{
					$archivoDestino = PATH_STORAGE.$datosmultimedia['multimediacatcarpeta'].$fila['formatocarpeta']."/".$datosmultimedia['multimediaubic'];
					@unlink($archivoDestino);
				}
			break;

			case AUDIOS:
				$archivoNormal = PATH_STORAGE.$datosmultimedia['multimediacatcarpeta']."audios/".$datosmultimedia['multimediaubic'];
				@unlink($archivoNormal);
			break;

		}

		if (!parent::Eliminar($datos))
			return false;

		return true;

	}


//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// Modifica la descripcion del archivo multimedia

// Par�metros de Entrada:
//		datos: array asociativo con los datos a agregar
//			multimediadesc: descripcion a modificar
//			multimediacod: c�digo del archivo multimedia

// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	public function ModificarDescripcion($datos)
	{

		if (!$this->_ValidarModificarDescripcion($datos))
			return false;

		if (!$this->BuscarMultimediaxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Multimedia inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!isset($datos['catcod']))
		{
			$datosmult = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$datos['catcod'] = $datosmult['catcod'];
			if ($datos['catcod']=="")
				$datos['catcod']="NULL";
		}elseif ($datos['catcod']=="")
				$datos['catcod']="NULL";




		if (!parent::ModificarDescripcion($datos))
			return false;

		return true;

	}



	public function ModificarTitulo($datos)
	{

		if (!$this->BuscarMultimediaxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Multimedia inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!isset($datos['catcod']))
		{
			$datosmult = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$datos['catcod'] = $datosmult['catcod'];
			if ($datos['catcod']=="")
				$datos['catcod']="NULL";
		}elseif ($datos['catcod']=="")
				$datos['catcod']="NULL";


		if (!parent::ModificarTitulo($datos))
			return false;

		return true;

	}

	public function ModificarTituloDescripcion($datos)
	{
		if (!$this->BuscarMultimediaxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Multimedia inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!isset($datos['catcod']))
		{
			$datosmult = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$datos['catcod'] = $datosmult['catcod'];
			if ($datos['catcod']=="")
				$datos['catcod']="NULL";
		}elseif ($datos['catcod']=="")
				$datos['catcod']="NULL";

		if (!parent::ModificarTitulo($datos))
			return false;

		if (!parent::ModificarDescripcion($datos))
			return false;

		return true;
	}



	public function ModificarPreview($datos)
	{

		if (!parent::ModificarPreview($datos))
			return false;

		return true;
	}




//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// function que valida los datos al momento de insertar una nuevo tag a la noticia

// Par�metros de Entrada:
//		datos: array asociativo con los datos a validar

// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	private function _ValidarInsertar(&$datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		return true;
	}


//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// function que valida la modificaci�n de la descripci�n del archivo multimedia

// Par�metros de Entrada:
//		datos: array asociativo con los datos a validar
//		multimediadesc: Descripci�n del Archivo multimedia
//		multimediacod: C�digo del archivo multimedia

// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	private function _ValidarModificarDescripcion($datos)
	{



		return true;
	}

//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// function que valida los datos al momento de eliminar el archivo multimedia

// Par�metros de Entrada:
//		datos: array asociativo con los datos a validar
//			noticiacod: Valida que este seteada la noticia y que exista en la bd

// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	private function _ValidarEliminar($datos,&$datosmultimedia)
	{

		if(!$this->BuscarMultimediaxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Formato de imagen no permitido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosmultimedia = $this->conexion->ObtenerSiguienteRegistro($resultado);

		if(!$this->TieneMultimediaAsociado($datos))
			return false;

		return true;
	}



//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// function que valida los datos al momento de eliminar el archivo multimedia

// Par�metros de Entrada:
//		datos: array asociativo con los datos a validar
//			noticiacod: Valida que este seteada la noticia y que exista en la bd

// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	private function TieneMultimediaAsociado($datos)
	{

		if(!parent::BuscarMultimediasRelacionados($datos,$resultado,$numfilas))
			return false;

		$datoscantidad = $this->conexion->ObtenerSiguienteRegistro($resultado);

		if ($datoscantidad['total']>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el multimedia tiene asociados ".$datoscantidad['descripcion']." (".$datoscantidad['DescRelacionados'].")",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		return true;
	}

//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------

// function que valida los datos obigatorios

// Par�metros de Entrada:
//		datos: array asociativo con los datos a validar

// Retorna:
//		la funci�n retorna true o false si se pudo ejecutar con �xito o no

	private function _ValidarDatosVacios(&$datos)
	{


		if(!isset ($datos["catcod"]) || $datos["catcod"]=="")
		  	$datos["catcod"]="NULL";


		return true;
	}



	public function ArmarImagenVideo($tipo,$idExterno)
	{
		$url = "";
		switch($tipo)
		{
			case 5:

				if (file_exists(PATH_STORAGE."externalimg/you/".$idExterno.".jpg"))
					$url = DOMINIO_SERVIDOR_MULTIMEDIA."externalimg/you/".$idExterno.".jpg";
				else
				{
					$url = "http://img.youtube.com/vi/".$idExterno."/0.jpg";
					cMultimedia::grab_image($url,PATH_STORAGE."externalimg/you/".$idExterno.".jpg");
				}
				break;
			case 7:
				if (file_exists(PATH_STORAGE."externalimg/vim/".$idExterno.".jpg"))
					$url = DOMINIO_SERVIDOR_MULTIMEDIA."externalimg/vim/".$idExterno.".jpg";
				else
				{
					$ch = curl_init();
					$timeout = 0;
					curl_setopt ($ch, CURLOPT_URL, "http://vimeo.com/api/v2/video/".$idExterno.".json");
					curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

					// Getting binary data
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
					$image = json_decode(curl_exec($ch));
					curl_close($ch);
					$url = $image[0]->thumbnail_medium;
					cMultimedia::grab_image($url,PATH_STORAGE."externalimg/vim/".$idExterno.".jpg");
				}
				break;
		}
		return $url;
	}


	public function grab_image($url,$saveto){
		$ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$raw=curl_exec($ch);
		curl_close ($ch);
		if(file_exists($saveto)){
			unlink($saveto);
		}
		$fp = fopen($saveto,'x');
		fwrite($fp, $raw);
		fclose($fp);
	}

	public function EliminarMultimediasSeleccionados($datos)
	{
		$multimedias = explode(",",$datos['multimedia']);
		if (count($multimedias)>0)
		{
			foreach($multimedias as $multimediacod)
			{
				if ($multimediacod!="")
				{
					$datoseliminar['multimediacod'] = $multimediacod;
					if (!$this->EliminarLogica($datoseliminar))
						return false;
				}
			}
		}
		return true;
	}


	public function DesAsociarMultimedia($datos)
	{
		$datos["multimediapreview"]="NULL";
		if(!$this->ModificarPreview($datos))
			return false;

		return true;
	}

	public function AsociarMultimedia($datos)
	{

		if(!$this->ModificarPreview($datos))
			return false;

		return true;
	}

	public function SubirRelacionarPreview($datos)
	{

		if (!$this->InsertarImagenPreview($datos,$multimediacod))
			return false;


		$datos['multimediapreview'] = $multimediacod;  //DESCRIPCION DEL MULTIMEDIA
		if(!$this->ModificarPreview($datos))
			return false;

		return true;
	}
	public function InsertarImagenPreview($datos,&$multimediacod)
	{

		// CREO EL OBJETO DE MULTIMEDIA
		$oMultimedia = new cMultimedia($this->conexion,CARPNOTICIAS,$this->formato);
		//SETEO LOS CAMPOS A INSERTAR EN LA TABLA MULTIMEDIA
		$datos['multimediaconjuntocod'] = FOTOS;
		$datos['multimediatitulo'] = $datos['multimedia_titulo'];
		$datos['multimediadesc'] = $datos['multimedia_desc'];
		$datos['multimedianombre'] = $datos['mul_multimedia_name'];
		$datos['multimediaubic'] = $datos['mul_multimedia_file'];
		$datos['multimediacatcod'] = 1; // MUL_MULTIMEDIA_CATEGORIAS (VALOR 1 SIEMPRE)

		//INSERTO EL ARCHIVO MULTIMEDIA EN LA TABLA MULTIMEDIA
		if(!$oMultimedia->InsertarImagenDesdeTemporal($datos,$multimediacod))
			return false;

		return true;
	}


}//fin clase

?>
