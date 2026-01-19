<?php 
require_once 'THUMB/ThumbLib.inc.php';

//Clase para el manejo de imagenes
class cFuncionesMultimedia
{
	
	
	// Constructor de la clase
	function __construct(){


    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	



//----------------------------------------------------------------------------------------- 
// Guarda una foto en dos tipos de formato Thumb o redimensiona la foto.

// Parametros:
// 		->file = archivo a utilizar
// 		->thumbD = Tamaño máximo de ancho o alto al que se redimensiona la foto
// 		->porcentajeCalidad = porcentaje de calidad de la foto
//		->formatoSalida = Formato de la salida
//			->T = Thumb (foto cuadrada, toma el medio de la foto)

//La funcion retorna la imagen en formato jpg.

	public function Guardafoto($file, $thumbD, $porcentajeCalidad, $formatoSalida){
		//Obtenemos la informacion de la imagen, el array info tendra los siguientes indices:
		//0: ancho de la imagen
		//1: alto de la imagen
		//mime: el mime_type de la imagen
		$info = getimagesize($file);
		//Dependiendo del mime type, creamos una imagen a partir del archivo original:
		switch($info['mime']){
			case 'image/jpeg':
			$image = imagecreatefromjpeg($file);
			break;
			case 'image/gif';
			$image = imagecreatefromgif($file);
			break;
			case 'image/png':
			$image = imagecreatefrompng($file);
			break;
		}
		if($formatoSalida == "T"){
				//Si el ancho es igual al alto, la imagen ya es cuadrada, por lo que podemos ahorrarnos unos pasos:		
				if($info[0] == $info[1]){
					$xpos = 0;
					$ypos = 0;
					$width = $info[1];
					$height = $info[1];
				//Si la imagen no es cuadrada, hay que hacer un par de averiguaciones:
				}else{
					if($info[0] > $info[1]){ 
						//imagen horizontal
						$xpos = ceil(($info[0] - $info[1]) /2);
						$ypos = 0;
						$width  = $info[1];
						$height = $info[1];
					}else{ 
						//imagen vertical
						$ypos = ceil(($info[1] - $info[0]) /2);
						$xpos = 0;
						$width  = $info[0];
						$height = $info[0];
					}
				}
				//Creamos una nueva imagen cuadrada con las dimensiones que queremos:
				$image_new = imagecreatetruecolor($thumbD, $thumbD);
				$bgcolor = imagecolorallocate($image_new, 255, 255, 255);  
				imagefilledrectangle($image_new, 0, 0, $thumbD, $thumbD, $bgcolor);
				imagealphablending($image_new, true);
				//Copiamos la imagen original con las nuevas dimensiones
				imagecopyresampled($image_new, $image, 0, 0, $xpos, $ypos, $thumbD, $thumbD, $width, $height);
			}else{ //if($formatoSalida == "T"){
				$xpos = 0;
				$ypos = 0;
				$width  = $info[0];
				$height = $info[1];
				if($info[0] > $info[1]){ 
					//imagen horizontal
					//preguntamos si el ancho es mayor que el parametro de tamaño, para no agrandar una foto pequeña y pixelarla
					if($info[0] > $thumbD){
						$nueva_altura = ceil($thumbD*($info[1]/$info[0]));
						$image_new = imagecreatetruecolor($thumbD, $nueva_altura);
						$bgcolor = imagecolorallocate($image_new, 255, 255, 255);  
						imagefilledrectangle($image_new, 0, 0, $thumbD, $nueva_altura, $bgcolor);
						imagealphablending($image_new, true);
						imagecopyresampled($image_new, $image, 0, 0, $xpos, $ypos, $thumbD, $nueva_altura, $width, $height);
					}else{ //if($info[0] > $ancho){
						$image_new = imagecreatetruecolor($info[0], $info[1]);
						$bgcolor = imagecolorallocate($image_new, 255, 255, 255);  
						imagefilledrectangle($image_new, 0, 0, $info[0], $info[1], $bgcolor);
						imagealphablending($image_new, true);
						imagecopyresampled($image_new, $image, 0, 0, $xpos, $ypos, $info[0], $info[1], $width, $height);
					} //if($info[0] > $ancho){
				}else{ //if($info[0] > $info[1]){ 
					//imagen vertical
					//preguntamos si el alto es mayor que el parametro de tamaño, para no agrandar una foto pequeña y pixelarla
					if($info[1] > $thumbD){
						$nueva_altura = ceil($thumbD*($info[0]/$info[1]));
						$image_new = imagecreatetruecolor($nueva_altura, $thumbD);
						$bgcolor = imagecolorallocate($image_new, 255, 255, 255);  
						imagefilledrectangle($image_new, 0, 0, $nueva_altura, $thumbD, $bgcolor);
						imagealphablending($image_new, true);
						imagecopyresampled($image_new, $image, 0, 0, $xpos, $ypos, $nueva_altura, $thumbD, $width, $height);
					}else{ //if($info[1] > $ancho){
						$image_new = imagecreatetruecolor($info[0], $info[1]);
						$bgcolor = imagecolorallocate($image_new, 255, 255, 255);  
						imagefilledrectangle($image_new, 0, 0, $info[0], $info[1], $bgcolor);
						imagealphablending($image_new, true);
						imagecopyresampled($image_new, $image, 0, 0, $xpos, $ypos, $info[0], $info[1], $width, $height);
					} //if($info[1] > $ancho){
				} //if($info[0] > $info[1]){ 
		} //if($formatoSalida == "T"){
		//Guardamos la nueva imagen como jpg
		return $image_new;
	} //function Guardafoto($file, $savePath, $thumbD, $porcentajeCalidad, $formatoSalida){


//----------------------------------------------------------------------------------------- 
// Guarda una foto en dos tipos de formato Thumb o redimensiona la foto.

// Parametros:
// 		->imagen = base64 a convertir a imagen

//La funcion retorna la imagen en formato jpg.
	public function MoverArchivoTemporal($archivo,$carpetadestino)
	{
		
		$thumb = PhpThumbFactory::create(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$archivo);
		$thumb->resize(TAMANIONORMAL, TAMANIONORMAL);
		$thumb->save($carpetadestino.$archivo);
		
		
		//if(!copy(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$archivo,$carpetadestino.$archivo))
			//return false;

		if(!unlink(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$archivo))
			return false;

		return true;
	}



//----------------------------------------------------------------------------------------- 
// Guarda una foto en dos tipos de formato Thumb o redimensiona la foto.

// Parametros:
// 		->imagen = base64 a convertir a imagen

//La funcion retorna la imagen en formato jpg.
	public function MoverArchivoTemporalCompleto($archivo,$carpetadestino)
	{
		
		if(!copy(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$archivo,$carpetadestino.$archivo))
			return false;

		if(!unlink(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$archivo))
			return false;

		return true;
	}
	
	
	public function RedimensionarImagen($nombrearchivo,$carpetaoriginal,$carpetadestino,$ancho,$alto)
	{
		$thumb = PhpThumbFactory::create($carpetaoriginal.$nombrearchivo);
		$thumb->resize($ancho, $alto);
		$thumb->save($carpetadestino.$nombrearchivo);

		return true;
	}
	
	

	

	public function CropearImagen($nombrearchivo,$carpetaoriginal,$carpetadestino,$ancho,$alto)
	{
		$thumb = PhpThumbFactory::create($carpetaoriginal.$nombrearchivo);
		$thumb->adaptiveResize($ancho,$alto);
		$thumb->save($carpetadestino.$nombrearchivo);

		return true;
	}
	



} // Fin clase cImagenes
?>