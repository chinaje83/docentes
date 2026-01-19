<?php
require_once 'THUMB/ThumbLib.inc.php';

class Multimedia {


    public function __construct() {
    }





    /**
     *  Metodo estatico sin necesidad de instanciar el objeto
     */
   static public function GetImagenStatic($width, $height, $advLink = "", $resize = true, $crop=0, $credito = 'N', $watermark = 'N') {

        $width_s = $width;
        $height_s = $height;

        //$widthX = $width;

        /* if ($advLink==""){
          $filename = $this->GetValor("link");
          }else { */
        $filename = $advLink;
        //}

        if (!is_file(PATH_STORAGE . $filename)) {

			return "";
        } else {
            $bufFilename[0] = substr($filename, 0, strrpos($filename, "."));
            $bufFilename[1] = substr($filename, strrpos($filename, ".") + 1);

           	$pathinfo = pathinfo($filename);
			$extension = strtolower($pathinfo['extension']);
			if($extension=='gif')
			{
				return $filename;
			}

		    if ($resize == false) {
                return $filename;
            }

            $filename = PATH_STORAGE . $filename;

            // Antes que nada nos fijamos si el thumb ya existe

            list($widthor, $heightor, $type, $attr) = getimagesize($filename);

            //$height = (int)($heightor * ($width/$widthor));


            /**/
			if ($crop > 0) {
				if ($widthor<$width)
					$width = $widthor;

				if ($heightor<$height)
					$height = $heightor;

				$thumbname = PATH_STORAGE . $bufFilename[0] . "_" . $width . "x" . $height . "." . $bufFilename[1];
				$thumb = PhpThumbFactory::create($filename);
				$thumb->adaptiveResize($width,$height);
				$thumb->save($thumbname);
           		return str_replace(PATH_STORAGE, "", $thumbname);
			}
			else{
				$x=0;
				if ($width > 0 && $height > 0) {

					/* Proporcion de Imagen */
					if ($widthor == $heightor) {
						if ($width > $height) {
							$width = (int) ($widthor * ($height / $heightor));
						} else {
							$height = (int) ($heightor * ($width / $widthor));
						}
					}

					if ($widthor > $heightor) {
						if ($width > $height) {
							$height = (int) ($heightor * ($width / $widthor));
						} else {
							$width = (int) ($widthor * ($height / $heightor));
						}
					}

					if ($widthor < $heightor) {
						if ($width > $height) {
							$width = (int) ($widthor * ($height / $heightor));
						} else {
							$height = (int) ($heightor * ($width / $widthor));
						}
					}

					//para que no superen los topes
					if ($height > $height_s) {
						$height = $height_s;
						$width = (int) ($widthor * ($height / $heightor));
					}
					//
					if ($width > $width_s) {
						$width = $width_s;
						$height = (int) ($heightor * ($width / $widthor));
					}
				} else {

					if ($width > 0 && $height == 0) {
						if ($widthor<$width)
							$width = $widthor;

						$height = (int) ($heightor * ($width / $widthor));
					}

					if ($width == 0 && $height > 0) {
						$width = (int) ($widthor * ($height / $heightor));
					}
				}
			}
            /**/


            $thumbname = PATH_STORAGE . $bufFilename[0] . "_" . $width . "x" . $height . "." . $bufFilename[1];

            if (!is_file($thumbname)) {
                if (preg_match("/png/", $filename)) {
                    $or = imagecreatefrompng($filename);
                    $im = imagecreatetruecolor($width, $height);
                    imagealphablending($im, false);
                    $colorTransparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
                    imagefill($im, 0, 0, $colorTransparent);
                    imagesavealpha($im, true);
                    imagecopyresampled($im, $or, 0, 0, $x, 0, $width, $height, $widthor, $heightor);
                    imagepng($im, $thumbname);

                    imagedestroy($im);
                    imagedestroy($or);
                }
                if (preg_match("/jpg/", $filename)) {
                    $im = imagecreatetruecolor($width, $height);
                    $or = imagecreatefromjpeg($filename);
                    imagecopyresampled($im, $or, 0, 0, $x, 0, $width, $height, $widthor, $heightor);
                }
                if (preg_match("/gif/", $filename)) {
                    $im = imagecreate($width, $height);
                    $or = imagecreatefromgif($filename);
                    imagecopyresized($im, $or, 0, 0, $x, 0, $width, $height, $widthor, $heightor);
                }

                if (preg_match("/(jpg|gif)/", $filename)) {
                    $im2 = imagecreatetruecolor($width, $height);
                    imagecopy($im2, $im, 0, 0, $x, 0, $width, $height);

                    imagejpeg($im2, $thumbname, 92);
                    imagedestroy($im);
                    imagedestroy($or);
                    imagedestroy($im2);
                }

				//Agregar Marca de Agua
				//if($watermark == 'S') Multimedia::AddWaterMark($thumbname);


            }


            return str_replace(PATH_STORAGE, "", $thumbname);
        }
    }


    /**
     *
     * Si la imagen es vertical, se lleva al ancho y debe aplicarse un OVERFLOW HIDDEN para ocultar el sobrante.
     * Si es Wide, se lleva al alto deseado y la imagen debería mostrarse como BACKGROUND-POSITION CENTER en un DIV
     * @param $width_box
     * @param $height_box
     * @param $advLink
     */
    static public function GetImagenStaticFitBoxCentered($width_box, $height_box, $advLink = "") {


        $filename = $advLink;

        if (!is_file(PATH_STORAGE . $filename)) {

        } else {
            $bufFilename[0] = substr($filename, 0, strrpos($filename, "."));
            $bufFilename[1] = substr($filename, strrpos($filename, ".") + 1);

            $filename = PATH_STORAGE . $filename;

            list($widthor, $heightor, $type, $attr) = getimagesize($filename);

            $finalW = $finalH = 0;

            if ($widthor > $heightor) {
                $finalH = (int) $height_box;
                $finalW = ceil($widthor * ($height_box / $heightor));

                if ($finalW < $width_box) {
                    $finalW = (int) $width_box;
                    $finalH = ceil($heightor * ($width_box / $widthor));
                }
            } else {
                $finalW = (int) $width_box;
                $finalH = ceil($heightor * ($width_box / $widthor));
            }

            //Arma la imagen final
            $thumbname = PATH_STORAGE . $bufFilename[0] . "_" . $finalW . "x" . $finalH . "." . $bufFilename[1];

            if (!is_file($thumbname)) {
                if (preg_match("/png/", $filename)) {
                    $or = imagecreatefrompng($filename);
                    $im = imagecreatetruecolor($finalW, $finalH);
                    imagealphablending($im, false);
                    $colorTransparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
                    imagefill($im, 0, 0, $colorTransparent);
                    imagesavealpha($im, true);
                    imagecopyresampled($im, $or, 0, 0, 0, 0, $finalW, $finalH, $widthor, $heightor);
                    imagepng($im, $thumbname);

                    imagedestroy($im);
                    imagedestroy($or);
                }
                if (preg_match("/jpg/", $filename)) {
                    $im = imagecreatetruecolor($finalW, $finalH);
                    $or = imagecreatefromjpeg($filename);
                    imagecopyresampled($im, $or, 0, 0, 0, 0, $finalW, $finalH, $widthor, $heightor);
                }
                if (preg_match("/gif/", $filename)) {
                    $im = imagecreate($finalW, $finalH);
                    $or = imagecreatefromgif($filename);
                    imagecopyresized($im, $or, 0, 0, 0, 0, $finalW, $finalH, $widthor, $heightor);
                }

                if (preg_match("/(jpg|gif)/", $filename)) {
                    $im2 = imagecreatetruecolor($finalW, $finalH);
                    imagecopy($im2, $im, 0, 0, 0, 0, $finalW, $finalH);

                    imagejpeg($im2, $thumbname, 92);
                    imagedestroy($im);
                    imagedestroy($or);
                    imagedestroy($im2);
                }
            }

            return str_replace(PATH_STORAGE, "", $thumbname);
        }
    }



      /**
     * Función copiada de http://php.net/manual/en/function.imagecopyresampled.php (nombre original "image_resize")
     * Toma una url de una imagen externa y la redimensiona al igual que lo hace GetImagenStaticFitoBoxCenter
     * @see cMultimedia::GetImagenStaticFitoBoxCenter
     * @global type $app_path
     * @global type $var_url
     * @param type $src link de la imagen externa
     * @param type $width ancho requerido
     * @param type $height altura requerida
     * @param type $crop opcional para 'cropear' la imagen, por defecto no lo hace.
     * @return string link absoluto de la imagen
     */
    static public function GetExternalImageResized($src, $picName, $width, $height, $crop = 0) {

        if (!list($w, $h) = @getimagesize($src))
            return ''; //"Unsupported picture type!"

        $type = strtolower(substr(strrchr($src, "."), 1));
        //@todo verificar si la uri de destino es correcta. La idea es vaciar esta carpeta cada x tiempo
        $relativPath = '/img/tmpRedesSociales/' . $picName . '.' . $type;
        $dst = PATH_STORAGE . $relativPath;
        if(file_exists($dst)) {
            return $relativPath;
        }

        if ($type == 'jpeg')
            $type = 'jpg';

        switch ($type) {
            case 'bmp': $img = imagecreatefromwbmp($src);
                break;
            case 'gif': $img = imagecreatefromgif($src);
                break;
            case 'jpg': $img = imagecreatefromjpeg($src);
                break;
            case 'png': $img = imagecreatefrompng($src);
                break;
            default :
                return ''; //Unsupported picture type!
        }

        // resize
        if ($crop > 0) {
            if ($w < $width or $h < $height)
                return ''; //Picture is too small!
            $ratio = max($width / $w, $height / $h);
            $h = $height / $ratio;
            $x = ($w - $width / $ratio) / 2;
            $w = $width / $ratio;
        }
        else {
            /* Lógica original
             * if ($w < $width and $h < $height)
              return "Picture is too small!";
              $ratio = min($width / $w, $height / $h);
              echo "en crop w: " . $w . " h: " . $h;
              echo "<br>";
              echo "ratio: " . $ratio;
              echo "<br>";
              $width = $w * $ratio;
              $height = $h * $ratio;
              $x = 0; */

            if ($w > $h) {
                $finalH = (int) $height;
                $finalW = ceil($w * ($height / $h));

                if ($finalW < $width) {
                    $finalW = (int) $width;
                    $finalH = ceil($h * ($width / $w));
                }
            } else {
                $finalW = (int) $width;
                $finalH = ceil($h * ($width / $w));
            }
            $x = 0;
        }
        if($finalW <= 0 || $finalH <= 0) {
            return ''; // Invalid image dimensions
        }
        $new = imagecreatetruecolor($finalW, $finalH);

        // preserve transparency
        if ($type == "gif" or $type == "png") {
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled($new, $img, 0, 0, $x, 0, $finalW, $finalH, $w, $h);

        switch ($type) {
            case 'bmp': imagewbmp($new, $dst);
                break;
            case 'gif': imagegif($new, $dst);
                break;
            case 'jpg': imagejpeg($new, $dst);
                break;
            case 'png': imagepng($new, $dst);
                break;
            default:
                break;
        }
        return $relativPath;
    }




 	static function VerVideo(&$conexion,$multimediacod,$width=500,$height=300,$conPreview=false)
	{

		if (!intval($multimediacod))
			return "";


		$sql = "SELECT a.*, mc.multimediacatcarpeta, b.multimediatipoarchivo, c.multimediaubic AS previewubic FROM mul_multimedia AS a
				INNER JOIN mul_multimedia_tipos AS b ON a.multimediatipocod=b.multimediatipocod
				INNER JOIN mul_multimedia_categorias AS mc ON a.multimediacatcod=mc.multimediacatcod
				LEFT JOIN mul_multimedia AS c ON a.multimediapreview = c.multimediacod
				WHERE b.multimediaconjuntocod=2 AND a.multimediacod=".$multimediacod;

		$erroren = "";
		if (!$conexion->_EjecutarQuery($sql,$erroren,$resultado,$errno))
			return "";

		if ($conexion->ObtenerCantidadDeRegistros($resultado)==0)
			return "";

		$datosMultimedia = $conexion->ObtenerSiguienteRegistro($resultado);
		$str = "";

		switch ($datosMultimedia['multimediatipocod'])
		{
			case YOU:
                $str = '<iframe id="video-'.$datosMultimedia['multimediacod'].'" width="' . $width . '" height="' . $height . '" src="//www.youtube.com/embed/' . $datosMultimedia['multimediaidexterno'] . '?showinfo=0&autohide=1&rel=0" frameborder="0" allowfullscreen></iframe>';
				break;
			case VIM:
                $str = '<iframe id="video-'.$datosMultimedia['multimediacod'].'" src="//player.vimeo.com/video/' . $datosMultimedia['multimediaidexterno'] . '?portrait=0&title=0&byline=0" width="' . $width . '" height="' . $height . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe> ';
				break;
			default:
				$url = DOMINIO_SERVIDOR_MULTIMEDIA.$datosMultimedia['multimediacatcarpeta']."videos/".$datosMultimedia['multimediaubic'];
				$img= "";
				if ($datosMultimedia['previewubic']!="")
				{
					$img = DOMINIO_SERVIDOR_MULTIMEDIA.cMultimedia::GetImagenStatic($width, $height, $datosMultimedia['multimediacatcarpeta']."N/".$datosMultimedia['previewubic'],true,1);
				}
                $str = "<div id='videoplayer_" . $datosMultimedia['multimediacod'] . "'>&nbsp;</div>
					<script type='text/javascript'>
							  jwplayer('videoplayer_" . $datosMultimedia['multimediacod'] . "').setup({
								'id': 'player_" . $datosMultimedia['multimediacod'] . "',
								'width': '" . $width . "',
								'height': '" . $height . "',
								'file': '" . $url . "',
								'image': '" . $img . "',
								'controlbar': 'bottom',
								'modes': [
									{type: 'html5'},
									{type: 'flash', src: '/player/player.swf'},
									{type: 'download'}
								]
							  });
					</script>";
				break;
		}

		return $str;

	}


}

?>
