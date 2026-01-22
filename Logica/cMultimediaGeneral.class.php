<?php 
/*
CLASE LOGICA PARA EL MANEJO DE LOS BOTONES DE CARGA DE MULTIMEDIA.
*/

class cMultimediaGeneral
{
	protected $conexion;
	protected $formato;
	private $vartipo;

	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	

	//GETTERS Y SETTERS DE LAS MULTIMEDIA GENERAL//
	
	public function setTipo($vartipo){
		$this->vartipo = $vartipo;	
	}
	
	public function getTipo(){
		switch($this->vartipo)
		{
			case "NOT":
				return new cNoticiasMultimedia($this->conexion,$this->formato);
			break;
			case "PAG":
				return new cPaginasMultimedia($this->conexion,$this->formato);	
			break;
			case "AGE":
				return new cAgendaMultimedia($this->conexion,$this->formato);	
			break;
			case "LIC":
				return new cLicitacionesMultimedia($this->conexion,$this->formato);
			break;	
		}
	}

	
//-----------------------------------------------------------------------------------------
//							 PUBLICAS	
//----------------------------------------------------------------------------------------- 



	public function SubirRelacionarMultimedia($datos)
	{
		switch($datos['tipo'])
		{
			case FOTOS:
				if (!$this->InsertarImagen($datos,$multimediacod))
					return false;
				break;	
			case VIDEOS:
				if (!$this->InsertarVideo($datos,$multimediacod))
					return false;
				break;	
			case AUDIOS:
				if (!$this->InsertarAudio($datos,$multimediacod))
					return false;
				break;	
			case FILES:
				if (!$this->InsertarArchivo($datos,$multimediacod))
					return false;
				break;	
			default:
				return false;	
		}
		
		$datos['multimediacod'] = $multimediacod;
	
		if (isset($datos['multimedia_titulo']))
			$datos['multimediatitulo'] = $datos['multimedia_titulo'];
		elseif(isset($datos['multimedia_titulo_prop']))
			$datos['multimediatitulo'] = $datos['multimedia_titulo_prop'];

		if (isset($datos['multimedia_desc']))
			$datos['multimedia_desc'] = $datos['multimedia_desc'];
		elseif(isset($datos['multimedia_desc_prop']))
			$datos['multimedia_desc'] = $datos['multimedia_desc_prop'];
			
		$datos['multimediadesc'] = $datos['multimedia_desc'];  //DESCRIPCION DEL MULTIMEDIA
		if(!$this->Asociar($datos))	
			return false;

		return true;	
	}



//----------------------------------------------------------------------------------------- 
// Genera un nuevo archivo multimedia y lo inserta como imagen y lo asocia al tipo que corresponda

// Parámetros de Entrada:
//		datos: arreglo de datos
//			codigo = codigo de la relacion (Ej. noticiacod, pagcod, etc)
//				FOTOS


// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function InsertarImagen($datos,&$multimediacod)
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
	
//----------------------------------------------------------------------------------------- 
// Genera un nuevo archivo multimedia y lo inserta como video en el tipo que corresponda

// Parámetros de Entrada:
//		datos: arreglo de datos
//			codigo = codigo de la relacion (Ej. noticiacod, pagcod, etc)
//				VIDEOS


// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function InsertarVideo($datos,&$multimediacod)
	{

		// CREO EL OBJETO DE MULTIMEDIA
		$oMultimedia = new cMultimedia($this->conexion,CARPNOTICIAS."videos/",$this->formato);

		//SETEO LOS CAMPOS A INSERTAR EN LA TABLA MULTIMEDIA 
		
		if (!isset($datos['tipovideosubir']))
			$datos['tipovideosubir']=1; // TOMA LOS VIDEOS SOLO DE VIMEO O YOUTUBE POR DEFAULT
		
		
		$datos['multimediaconjuntocod'] = VIDEOS; // TIPO DE MULTIMEDIA CONJUNTO
		switch($datos['tipovideosubir'])
		{
			case 1:
			
				$datos['multimediatitulo'] = $datos['multimedia_titulo'];
				$datos['multimediadesc'] = $datos['multimedia_desc']; // DESCRIPCION DEL MULTIMEDIA
				$datos['multimediaidexterno'] = $datos['mulcodepage'];
				$datos['multimediacatcod'] = 1; // MUL_MULTIMEDIA_CATEGORIAS (VALOR 1 SIEMPRE)
				$datos['multimedianombre']="";
				switch ($datos["cajavideosexterno"])
				{
					case "VIM":
						$datos['mulvideotipopage']="Vimeo";
						$datos['multimedianombre']="Vimeo";
						$datos['extensionvideo']="VIM";
						$datos['multimediaidexterno'] = $datos['mulcodepage'];
					break;	
					case "YOU":
						$datos['mulvideotipopage']="YouTube";
						$datos['multimedianombre']="YouTube";
						$datos['extensionvideo']="YOU";
						$datos['multimediaidexterno'] = $datos['mulcodepagey'];
					break;	
				}
		
		
				//INSERTO EL ARCHIVO MULTIMEDIA EN LA TABLA MULTIMEDIA
				if(!$oMultimedia->InsertarVideo($datos,$multimediacod))
					return false;
					
				break;
			case 2:
			
				$datos['multimediatitulo'] = $datos['multimedia_titulo_prop'];
				$datos['multimediadesc'] = $datos['multimedia_desc_prop']; // DESCRIPCION DEL MULTIMEDIA
				$datos['multimediaidexterno'] = "NULL";

				$datos['multimedianombre'] = $datos['mul_multimedia_name'];
				$datos['multimediaubic'] = $datos['mul_multimedia_file'];

				$datos['multimediacatcod'] = 1; // MUL_MULTIMEDIA_CATEGORIAS (VALOR 1 SIEMPRE)
				$datos['catcod'] = $datos['catcodvideoprop']; 
					
				//INSERTO EL ARCHIVO MULTIMEDIA EN LA TABLA MULTIMEDIA
				if(!$oMultimedia->InsertarVideoDesdeTemporal($datos,$multimediacod))
					return false;
				break;					
		}
			
		return true;	
	}


//----------------------------------------------------------------------------------------- 
// Genera un nuevo archivo multimedia y lo inserta como audio de la noticia

// Parámetros de Entrada:
//		datos: arreglo de datos
//			codigo = codigo de la relacion (Ej. noticiacod, pagcod, etc)
//				FOTOS


// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function InsertarAudio($datos,&$multimediacod)
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
				if(!$oMultimedia->InsertarAudioDesdeTemporal($datos,$multimediacod))
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
				if(!$oMultimedia->InsertarAudioExterno($datos,$multimediacod))
					return false;

				//INSERTO EL ARCHIVO MULTIMEDIA EN LA TABLA MULTIMEDIA
				break;
		}


		
		
		
		return true;	
	}

	

//----------------------------------------------------------------------------------------- 
// Genera un nuevo archivo multimedia y lo inserta como audio de la noticia

// Parámetros de Entrada:
//		datos: arreglo de datos
//			codigo = codigo de la relacion (Ej. noticiacod, pagcod, etc)
//				FOTOS


// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function InsertarArchivo($datos,&$multimediacod)
	{
		// CREO EL OBJETO DE MULTIMEDIA
		$oMultimedia = new cMultimedia($this->conexion,CARPNOTICIAS,$this->formato);
		
		//SETEO LOS CAMPOS A INSERTAR EN LA TABLA MULTIMEDIA 
		$datos['multimediaconjuntocod'] = FILES; // TIPO DE MULTIMEDIA CONJUNTO
		$datos['multimediatitulo'] = $datos['multimedia_titulo'];
		$datos['multimediadesc'] = $datos['multimedia_desc'];  //DESCRIPCION DEL MULTIMEDIA
		$datos['multimedianombre'] = $datos['mul_multimedia_name'];
		$datos['multimediaubic'] = $datos['mul_multimedia_file'];
		$datos['multimediacatcod'] = 1; // MUL_MULTIMEDIA_CATEGORIAS (VALOR 1 SIEMPRE)
		
		//INSERTO EL ARCHIVO MULTIMEDIA EN LA TABLA MULTIMEDIA
		if(!$oMultimedia->InsertarArchivoDesdeTemporal($datos,$multimediacod))
			return false;
		
		return true;	
	}

	
	

//----------------------------------------------------------------------------------------- 
// Asocia un multimedia ya creado

// Parámetros de Entrada:
//		datos: arreglo de datos
//			codigo = codigo de la relacion (Ej. noticiacod, pagcod, etc)
//			multimediacod = codigo del multimedia


// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no
	
	public function AsociarMultimedia($datos)
	{
		$oMultimedia = new cMultimedia($this->conexion,"noticias", $this->formato);
		if(!$oMultimedia->BuscarMultimediaxCodigo($datos,$resultado,$numfilas))
			return false;
		$datosMultimedia = $this->conexion->ObtenerSiguienteRegistro($resultado);
		
		$datos['multimediatitulo'] = $datosMultimedia['multimediatitulo'];
		$datos['multimediadesc'] = $datosMultimedia['multimediadesc'];
		if (isset($datosMultimedia['multimediapreview']) && $datosMultimedia['multimediapreview']!="")
			$datos['multimediacodpreview'] = $datosMultimedia['multimediapreview'];
		else	
			$datos['multimediacodpreview'] = "NULL";
		$datos['multimediadesc'] = $datosMultimedia['multimediadesc'];
		if(!$this->Asociar($datos))
			return false;
			
		return true;	
		
	}
	


//----------------------------------------------------------------------------------------- 
// DesAsocia un multimedia ya creado

// Parámetros de Entrada:
//		datos: arreglo de datos
//			codigo = codigo de la relacion (Ej. noticiacod, pagcod, etc)
//			multimediacod = codigo del multimedia


// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no
	
	public function DesAsociarMultimedia($datos)
	{
		if(!$this->DesAsociar($datos))
			return false;
			
		return true;	
		
	}
	
	
//----------------------------------------------------------------------------------------- 
// Eliminar Multimedia a un tipo

// Parámetros de Entrada:
//		datos: arreglo de datos
//			codigo = codigo del tipo
//			multimediacod = codigol multimedia

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	
	
	public function ModificarHomeMultimedia($datos)
	{
		$objeto = $this->getTipo(); 
		if (!$objeto->ModificarHomeMultimedia($datos))
			return false;
			
		return true;	
	}



//----------------------------------------------------------------------------------------- 
// Modificar Titulo Multimedia

// Parámetros de Entrada:
//		datos: arreglo de datos
//			codigo = codigo del tipo
//			multimediacod = codigol multimedia
//			multimediatitulo = titulo del multimedia

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	
	
	public function ModificarTituloMultimedia($datos)
	{
		$objeto = $this->getTipo(); 
		if (!$objeto->ModificarTituloMultimedia($datos))
			return false;
			
		return true;	
	}

//----------------------------------------------------------------------------------------- 
// Modificar Descripcion Multimedia

// Parámetros de Entrada:
//		datos: arreglo de datos
//			codigo = codigo del tipo
//			multimediacod = codigol multimedia
//			multimediadesc = descripcion del multimedia

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	
	
	public function ModificarDescripcionMultimedia($datos)
	{
		$objeto = $this->getTipo(); 
		if (!$objeto->ModificarDescripcionMultimedia($datos))
			return false;
			
		return true;	
	}



//----------------------------------------------------------------------------------------- 
// Eliminar relacion de un multimedia ya creado al modulo necesario

// Parámetros de Entrada:
//		datos: arreglo de datos
//			tipo = tipo de multimedia
//			multimediacod = codigo multimedia
//			codigo = codigo de la relacion (Ej. noticiacod, pagcod, etc)

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no
	private function DesAsociar($datos)
	{
		if (!$this->ValidarTipo($datos["tipo"]))
			return false;

		$objeto = $this->getTipo(); 
		if (!$objeto->DesAsociar($datos))
			return false;
		return true;	
	}


//----------------------------------------------------------------------------------------- 
// Relacionar un multimedia ya creado al modulo necesario

// Parámetros de Entrada:
//		datos: arreglo de datos
//			tipo = tipo de multimedia
//			multimediacod = codigol multimedia

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	private function Asociar($datos)
	{
		if (!$this->ValidarTipo($datos["tipo"]))
			return false;
		
		$datos['multimediaconjuntocod'] = $datos["tipo"];
		$objeto = $this->getTipo(); 
		if (!$objeto->Asociar($datos))
			return false;
		
		return true;	
	}

//----------------------------------------------------------------------------------------- 
// Ordenar multimedias de algun módulo

// Parámetros de Entrada:
//		datos: arreglo de datos
//			multimedia = array con el orden de los multimedia
//			codigo = codigol de la relacion (Ej. noticiacod, pagcod, etc)

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	public function ModificarOrden($datos)
	{
		$objeto = $this->getTipo(); 
		if (!$objeto->ModificarOrden($datos))
			return false;
			
		return true;	
	}
	
	
//----------------------------------------------------------------------------------------- 
// Modificar Preview

// Parámetros de Entrada:
//		datos: arreglo de datos
//			codigo = codigo del tipo
//			multimediacod = codigo multimedia
//			multimediacodRelacion = codigo multimedia

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no

	
	
	public function ModificarPreview($datos)
	{
		$oMultimedia = new cMultimedia($this->conexion,CARPNOTICIAS,$this->formato);
		if (!$oMultimedia->BuscarMultimediaxCodigo($datos,$resultado,$numfilas))
			return false;
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Multimedia inexistente.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}	
		$datosMultimedia = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if ($datosMultimedia['multimediapreview']==""  && $datosMultimedia['multimediaidexterno']=="")
		{
			$datosMultimedia['multimediapreview'] = $datos['multimediacodRelacion'];
			if (!$oMultimedia->ModificarPreview($datosMultimedia))
				return false;
		}

		$objeto = $this->getTipo(); 
		if (!$objeto->ModificarPreview($datos))
			return false;
			
		return true;	
	}
	
	
	

	public function SubirRelacionarPreview($datos)
	{
		
		if (!$this->InsertarImagen($datos,$multimediacod))
			return false;
		
		
		$datos['multimediacodRelacion'] = $multimediacod;  //DESCRIPCION DEL MULTIMEDIA
		if(!$this->ModificarPreview($datos))	
			return false;

		return true;	
	}
	

//----------------------------------------------------------------------------------------- 
// Funcion que valida el tipo de multimedia

// Parámetros de Entrada:
//		tipo: Tipo de multimedia (Foto, video o audio)

// Retorna:
//		la función retorna true o false si se pudo ejecutar con éxito o no
	private function ValidarTipo($tipo)
	{
		switch($tipo)
		{
			case FOTOS:
			case VIDEOS:
			case AUDIOS:
			case FILES:
				return true;	
		}
		
		return false;
	}

	
	
}//fin clase	

?>