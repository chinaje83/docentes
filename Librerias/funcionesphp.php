<?php

class FuncionesPHPLocal {

    public static function exceptionHandler(Throwable $e): void {
        http_response_code(500);
        $error = ['error'=>500, 'error_description'=>$e->getMessage()];
        die(json_encode($error));
    }
//-----------------------------------------------------------------------------------------
// Genera todas las constantes necesarias para el sistema

    static function removeMagicQuotes($postArray, $trim = false) {
        if (1 == ini_get('magic_quotes_gpc')) {
            $newArray = array();

            foreach ($postArray as $key => $val) {
                if (is_array($val)) {
                    $newArray[$key] = FuncionesPHPLocal::removeMagicQuotes($val, $trim);
                } else {
                    if ($trim == true) {
                        $val = trim($val);
                    }
                    $newArray[$key] = stripslashes($val);
                }
            }

            return $newArray;
        } else {
            return $postArray;
        }
    }


    /**
     * @param accesoBDLocal $conexion
     * @param               $tipocarga
     */
    static function CargarConstantes($conexion, $tipocarga) {

        $cargarTodo = true;
        if (isset($tipocarga['no_carga']) && $tipocarga['no_carga'] == true)
            $cargarTodo = false;


        if ($cargarTodo) {

            $array = [];
            $array['MODIFICACION']['titulo'] = "Modificación";
            $array['MODIFICACION']['id'] = 1;
            $array['INSERTAR']['titulo'] = "Alta";
            $array['INSERTAR']['id'] = 2;
            $array['ELIMINAR']['titulo'] = "Eliminar";
            $array['ELIMINAR']['id'] = 3;
            $array['MODIFICARVIGENCIA']['titulo'] = "Modificación de Vigencia";
            $array['MODIFICARVIGENCIA']['id'] = 8;
            $array['INSERTARNUEVAVIGENCIA']['titulo'] = "Alta - Nueva Vigencia";
            $array['INSERTARNUEVAVIGENCIA']['id'] = 9;

            /*
             * //MODIFICADO PARA AUMENTAR LA VELOCIDAD DE CARGA
            $spparam = ['pBaseAuditorias' => BASEDATOSAUDITORIAS];
            if (!$conexion->ejecutarStoredProcedure("sel_AuditoriaAcciones", $spparam, $resultado, $numfilas, $errno))
                die("Error al cargar las constantes de las auditorias");

            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                if (!defined($fila['ConstanteAccion']))
                    define($fila['ConstanteAccion'], $fila['NombreAccion']);
                if (!defined("ID" . $fila['ConstanteAccion']))
                    define("ID" . $fila['ConstanteAccion'], $fila['IdAccion']);
            }*/
            foreach ($array as $constante => $data) {
                if (!defined($constante))
                    define($constante, $data['titulo']);
                if (!defined("ID" . $constante))
                    define("ID" . $constante, $data['id']);
            }

        }

        if (isset($tipocarga['sistema'])) {

            // Constantes Generales
            $spparam = ['pNombreSistema' => $tipocarga['sistema']];

            if (!$conexion->ejecutarStoredProcedure("sel_constantes_grales_xsistemanom", $spparam, $resultado, $numfilas, $errno))
                die("Error al cargar las constantes");

            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado))
                if (!defined($fila['Nombre']))
                    define($fila['Nombre'], $fila['Codigo']);

            $spparam = [];

            if (!$conexion->ejecutarStoredProcedure("sel_DocumentosObservacionesEstados_Constantes", $spparam, $resultado, $numfilas, $errno))
                die("Error al cargar las constantes");

            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado))
                if (!defined($fila['Constante']))
                    define($fila['Constante'], $fila['IdEstado']);

        }

        if (isset($tipocarga['roles'])) {

            # Búsqueda a las constantes por roles
            if (!$conexion->ejecutarStoredProcedure('sel_Roles_Constantes', [], $resultado, $numfilas, $errno))
                die('Error al cargar las constantes de Roles');

            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                if (!empty($fila['Constante']) && !defined($fila['Constante']))
                    define($fila['Constante'], (int)$fila['IdRol']);
            }

        }
        if (isset($tipocarga['multimedia'])) {

            // Constantes Generales
            $spparam = [];

            if (!$conexion->ejecutarStoredProcedure("sel_mul_multimedia_constantes", $spparam, $resultado, $numfilas, $errno))
                die("Error al cargar las constantes de multimedia");

            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado))
                if (!defined($fila['cte']))
                    define($fila['cte'], $fila['valor']);
        }

        if (isset($tipocarga['novedades'])) {

            if (!$conexion->ejecutarStoredProcedure('sel_DocumentosTipos_combo', [], $resultado, $numfilas, $errno))
                die('Error al cargar las constantes de acciones');

            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                if (!empty($fila['IdRegistro']) && !defined($fila['IdRegistro'])) {
                    define("NOV_REGISTRO_" . $fila['Constante'], $fila['IdRegistro']);
                    define("NOV_" . $fila['Constante'], $fila['IdTipoDocumento']);
                }
            }

            if (defined('NOV_SC_DOC_PRIMARIO')) {

                define('NOV_SC_DEFAULT', NOV_SC_DOC_PRIMARIO);
                define("NOV_REGISTRO_SC_DEFAULT", NOV_REGISTRO_SC_DOC_PRIMARIO);

                define('NOV_SC_DEFAULT_PRIMARIO', NOV_SC_DOC_PRIMARIO);
                define("NOV_REGISTRO_SC_DEFAULT_PRIMARIO", NOV_REGISTRO_SC_DOC_PRIMARIO);

                define('NOV_SC_INICIAL', NOV_SC_DOC_PRIMARIO);
                define("NOV_REGISTRO_SC_INICIAL", NOV_REGISTRO_SC_DOC_PRIMARIO);
            }

            if (defined('NOV_SC_DOC_SECUNDARIO')) {
                define('NOV_SC_DEFAULT_SECUNDARIO', NOV_SC_DOC_SECUNDARIO);
                define("NOV_REGISTRO_SC_DEFAULT_SECUNDARIO", NOV_REGISTRO_SC_DOC_SECUNDARIO);
            }

            if (!defined('NOV_SC_AUX_PRIMARIO')) {
                define('NOV_SC_AUX_PRIMARIO', "");
            }
            if (!defined('NOV_SC_AUX_SECUNDARIO')) {
                define('NOV_SC_AUX_SECUNDARIO', "");
            }

            if (!defined('NOV_ALTA_SUPLENTE_DOC')) {
                define('NOV_ALTA_SUPLENTE_DOC', "");
            }

            if (!defined('NOV_REGISTRO_ALTA_SUPLENTE_DOC')) {
                define('NOV_REGISTRO_ALTA_SUPLENTE_DOC', "");
            }

        }

        if (!defined('NOV_SC_DIR_VACANTE_PRIMARIO')) {
            define('NOV_SC_DIR_VACANTE_PRIMARIO', "");
        }

        if (!defined('NOV_SC_VACANTE')) {
            define('NOV_SC_VACANTE', "");
        }
        if (!defined('NOV_SC_DIR_VACANTE_SECUNDARIO')) {
            define('NOV_SC_DIR_VACANTE_SECUNDARIO', "");
        }


        if ($cargarTodo) {

            /*
            //		if(isset($tipocarga['estados_personas'])) {
            if (!$conexion->ejecutarStoredProcedure('sel_EscuelasPuestosPersonasEstados_constantes', [], $resultado, $numfilas, $errno))
                die('Error al cargar las constantes de estados de POFA');

            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                if (!empty($fila['cte']) && !defined($fila['cte']))
                    define($fila['cte'], (int)$fila['valor']);
            }*/


            # Búsqueda a los articulos que se ignoran en incompatibilidades
            if (!$conexion->ejecutarStoredProcedure('sel_Articulos_xIgnoraCargos', ['pBase' => BASEDATOSLICENCIAS], $resultado, $numfilas, $errno))
                die('Error al cargar las constantes de articulos');
            $articulos = json_decode('[' . $conexion->ObtenerSiguienteRegistro($resultado)['Articulos'] . ']');
            define('ARTICULOS_IGNORAN_CARGOS', $articulos);


            # Búsqueda a las constantes de aacciones
            if (!$conexion->ejecutarStoredProcedure('sel_carga_constantes_completas', [], $resultado, $numfilas, $errno))
                die('Error al cargar las constantes completas');

            while ($filaConstante = $conexion->ObtenerSiguienteRegistro($resultado)) {
                switch ($filaConstante['tipo']) {
                    case "ModuloAcciones":
                        if (!empty($filaConstante['Id']) && !defined("AC_" . $filaConstante['Id']))
                            define("AC_" . $filaConstante['Id'], $filaConstante['Id']);
                        break;

                    case "CircuitosAcciones":
                        if (!empty($filaConstante['Id']) && !defined("AC_CIRC_" . $filaConstante['Id']))
                            define("AC_CIRC_" . $filaConstante['Id'], $filaConstante['Id']);
                        break;
                    case "Niveles":
                        if (!empty($filaConstante['Id']) && !defined("NIVEL_" . $filaConstante['Id']))
                            define("NIVEL_" . $filaConstante['Nombre'], $filaConstante['Id']);
                        break;
                    case "CicuitosEstados":
                        if (!empty($filaConstante['Nombre']))
                            define("NOV_" . $filaConstante['Nombre'], $filaConstante['Id']);
                        break;
                    case "MadEstados":
                        if (!empty($filaConstante['Nombre']))
                            define("MAD_" . $filaConstante['Nombre'], $filaConstante['Id']);
                        break;
                    case "EscuelaPuestosPersonasEstados":
                        if (!empty($filaConstante['Nombre']) && !defined($filaConstante['Nombre']))
                            define($filaConstante['Nombre'], (int)$filaConstante['Id']);
                        break;


                }

            }
            /*
            # Búsqueda a las constantes de aacciones
            if (!$conexion->ejecutarStoredProcedure('sel_ModulosAcciones_combo_Descripcion', [], $resultado, $numfilas, $errno))
                die('Error al cargar las constantes de acciones');

            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                if (!empty($fila['IdAccion']) && !defined($fila['IdAccion']))
                    define("AC_".$fila['IdAccion'], $fila['IdAccion']);
            }

            if (!$conexion->ejecutarStoredProcedure('sel_CircuitosAcciones_combo', [], $resultado, $numfilas, $errno))
                die('Error al cargar las constantes de acciones');


            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                if (!empty($fila['IdAccion']) && !defined($fila['IdAccion']))
                    define("AC_CIRC_".$fila['IdAccion'], $fila['IdAccion']);
            }*/

            /*
                        if (!$conexion->ejecutarStoredProcedure('sel_DocumentosTipos_combo', [], $resultado, $numfilas, $errno))
                            die('Error al cargar las constantes de acciones');


                        while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                            if (!empty($fila['IdRegistro']) && !defined($fila['IdRegistro'])) {
                                define("NOV_REGISTRO_" . $fila['Constante'], $fila['IdRegistro']);
                                define("NOV_".$fila['Constante'], $fila['IdTipoDocumento']);
                            }
                        }

                        if (defined('NOV_SC_DOC_PRIMARIO')) {

                            define('NOV_SC_DEFAULT', NOV_SC_DOC_PRIMARIO);
                            define("NOV_REGISTRO_SC_DEFAULT", NOV_REGISTRO_SC_DOC_PRIMARIO);

                            define('NOV_SC_DEFAULT_PRIMARIO', NOV_SC_DOC_PRIMARIO);
                            define("NOV_REGISTRO_SC_DEFAULT_PRIMARIO", NOV_REGISTRO_SC_DOC_PRIMARIO);

                            define('NOV_SC_INICIAL', NOV_SC_DOC_PRIMARIO);
                            define("NOV_REGISTRO_SC_INICIAL", NOV_REGISTRO_SC_DOC_PRIMARIO);
                        }

                        if (defined('NOV_SC_DOC_SECUNDARIO')) {
                            define('NOV_SC_DEFAULT_SECUNDARIO', NOV_SC_DOC_SECUNDARIO);
                            define("NOV_REGISTRO_SC_DEFAULT_SECUNDARIO", NOV_REGISTRO_SC_DOC_SECUNDARIO);
                        }*/

            /*
                if (!$conexion->ejecutarStoredProcedure('sel_Niveles_combo_constante', [], $resultado, $numfilas, $errno))
                    die('Error al cargar las constantes de niveles');

                while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                    if (!empty($fila['IdNivel']) && !defined($fila['IdNivel'])) {
                        define("NIVEL_".$fila['Constante'], $fila['IdNivel']);
                    }
                }

                if (!$conexion->ejecutarStoredProcedure('sel_NovEstados_combo_constante', [], $resultado, $numfilas, $errno))
                    die('Error al cargar las constantes de estados Novedades');

                while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                    if (!empty($fila['Constante'])) {
                        define("NOV_" . $fila['Constante'], $fila['IdEstado']);
                    }
                }


                if (!$conexion->ejecutarStoredProcedure('sel_MadEstados_combo_constante', [], $resultado, $numfilas, $errno))
                    die('Error al cargar las constantes de estados Mad');

                while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                    if (!empty($fila['Constante'])) {
                        define("MAD_" . $fila['Constante'], $fila['Id']);
                    }
                }*/


            include_once DIR_ROOT . 'config/constantes_mad.php';

        }
    }


    static function CargarConstantesModulosVigentes($conexion, $datos) {
        // Constantes Generales
        if (!isset($datos['Vigencia']) || $datos['Vigencia'] != "") {
            $spparam = ["pVigencia" => $datos['Vigencia']];
            if (!$conexion->ejecutarStoredProcedure("sel_ModulosConstantes_Vigentes", $spparam, $resultado, $numfilas, $errno))
                die("Error al cargar las constantes de los Modulos");

            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado))
                if (!defined($fila['Constante']))
                    define($fila['Constante'], $fila['ValorConstante']);

        }

    }

//-----------------------------------------------------------------------------------------
// Usada para el envio automatico de mails, seg�n rol/jurisdiccion

// Retorna true si no hubo problema,
//		sino retorna false en caso de error al ejecutar el SP para seleccionar los usuarios,
//		por rol inv�lido o por error al enviar el mail.

    static function MandarMail($conexion, $rolcod, $jurisdcod, $subject, $texto) {
        switch ($rolcod) {
            case ADMISITE:
                $spparam = ['pIdRol' => implode(",", $rolcod), 'pIdEstado' => USUARIOACT];
                $spnombre = "sel_mail_usuarios_con_mail_xrol_xmenorestado";
                break;

            default:
                FuncionesPHPLocal::MostrarMensaje($conexion, MSG_ERRGRAVE, "Intento de enviar mail a rol inexistente.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => FMT_TEXTO]);
                return false;
        }


        if (!isset($spnombre)) {
            FuncionesPHPLocal::MostrarMensaje($conexion, MSG_ERRGRAVE, "Error al generar el mail.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => FMT_TEXTO]);
            return false;
        }

        if (!$conexion->ejecutarStoredProcedure($spnombre, $spparam, $resultado, $numfilas, $errno))
            return false;

        while ($fila = $conexion->ObtenerSiguienteRegistro($resultado))
            if (!mail($fila['Email'], $subject, $texto)) {
                echo "Imposible enviar mail.";
                return false;
            }

        return true;
    }

//-----------------------------------------------------------------------------------------
// Muestra un mensaje en pantalla y, segun el nivel del mensaje, env�a tambien mail a ADMISITE

// Parametros: $formatomensaje es un array asociativo
//					$formatomensaje['formato']=FMT_TEXTO|FMT_TABLA
//					$formatomensaje['cantcols'] usado para colspan cuando es FMT_TABLA
//				$enviarmail: en caso que sea necesario enviar mail y esta variable esta seteada en true, envia mail al administrador
//						definida en la conexion

    static function MostrarMensaje($conexion, $nivelmensaje, $texto, $ubicerror, $formatomensaje, $enviarmail = true, $msgPopup = false) {
        //if ($nivelmensaje==MSG_ERRGRAVE)
        //$texto.=" Avise a su Administrador.";

        if ($msgPopup) {
            switch ($nivelmensaje) {
                case MSG_OK:
                    $icon = '<i class="far fa-check-circle"></i>&nbsp;Ok';
                case MSG_INF:
                    $icon = '<i class="far fa-info"></i>&nbsp;Informaci&oacute;n';
                    break;
                case MSG_ERRGRAVE:
                case MSG_ERRSOSP:
                    $icon = '<i class="fas fa-exclamation-triangle"></i>&nbsp;Error';
                    break;
            }
            ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title"><?php echo $icon ?></h4>
            </div>

            <div class="modal-body">

            <?php
        }

        switch ($nivelmensaje) {
            case MSG_OK:
                if ($formatomensaje['formato'] == FMT_TEXTO)
                    echo '<div class="alert alert-success"><p><i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;' . $texto . '</p></div>';
                else
                    echo $texto;
                break;
            case MSG_INF:
                if ($formatomensaje['formato'] == FMT_TEXTO)
                    echo '<div class="alert alert-info"><p><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;' . $texto . '</p></div>';
                else
                    echo $texto;
                break;
            case MSG_ERRGRAVE:
            case MSG_ERRSOSP:
                if ($formatomensaje['formato'] == FMT_TEXTO)
                    echo '<div class="alert alert-danger"><p><i class="fa fa-exclamation-circle danger" aria-hidden="true"></i>&nbsp;' . $texto . '</p></div>';
                else
                    echo $texto;

                $roles = "-";
                $usuariocod = "-";
                if (isset($_SESSION['rolcod']) && is_array($_SESSION['rolcod']))
                    $roles = implode(", ", $_SESSION['rolcod']);
                if (isset($_SESSION['usuariocod']) && $_SESSION['usuariocod'] != "")
                    $usuariocod = $_SESSION['usuariocod'];

                $log = "IP: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("d/m/yyyy H:i:s") . PHP_EOL .
                    "Texto: " . ($texto) . PHP_EOL .
                    "Archivos: " . $ubicerror['archivo'] . PHP_EOL .
                    "Funcion: " . ($ubicerror['funcion'] == "" ? "-" : $ubicerror['funcion']) . PHP_EOL .
                    "Linea: " . $ubicerror['linea'] . PHP_EOL .
                    "Usuario: " . $usuariocod . PHP_EOL .
                    "Rol: " . $roles . PHP_EOL .
                    "File: " . $_SERVER['PHP_SELF'] . PHP_EOL .
                    "-------------------------" . PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents(DIR_ROOT . 'error_logs/log_' . date("Ymd") . '.txt', $log, FILE_APPEND);
                break;
        }

        if ($msgPopup) {
            ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
            <div class="clearboth"></div>
            <?php
        }
        if ($enviarmail) {
            // envio de mail
            /*
            switch($nivelmensaje)
            {
                case MSG_ERRGRAVE:
                    $subject=SISTEMA." - Error de Inconsistencia de Datos ";
                    $textomail="Se produjo un error:\n";
                    $textomail.="  Ejecutando Archivo: ".$_SERVER['PHP_SELF']."\n";
                    $textomail.="  Ubic Archivo: ".$ubicerror['archivo']."\n";
                    $textomail.="  Ubic Funci�n: ".($ubicerror['funcion']==""?"-":$ubicerror['funcion'])."\n";
                    $textomail.="  Ubic L�nea: ".$ubicerror['linea']."\n";
                    $textomail.="Texto error:".$texto."\n";
                    if(isset($_SESSION['usuariocod']))
                        $textomail.="Usuario sesion: ".$_SESSION['usuariocod']."\n";
                    echo $textomail;
                    break;
                case MSG_ERRSOSP:
                    $subject=SISTEMA." - Error Datos Sospechosos ";
                    $textomail="Se produjo un error:\n";
                    $textomail.="  Ejecutando Archivo: ".$_SERVER['PHP_SELF']."\n";
                    $textomail.="  Ubic Archivo: ".$ubicerror['archivo']."\n";
                    $textomail.="  Ubic Funci�n: ".($ubicerror['funcion']==""?"-":$ubicerror['funcion'])."\n";
                    $textomail.="  Ubic L�nea: ".$ubicerror['linea']."\n";
                    $textomail.="Texto error:".$texto."\n";
                    if(isset($_SESSION['usuariocod']))
                        $textomail.="Usuario sesion: ".$_SESSION['usuariocod']."\n";
                    echo $textomail;
                    break;
            }*/
        }
    }
//-----------------------------------------------------------------------------------------
// Registra el acceso de un usuario en el sistema

    static function RegistrarAcceso($conexion, $codigo_mensaje, $acciondesc, $usuariocod) {
        /*
        $archivonom=substr(strrchr($_SERVER['PHP_SELF'],'/'),1);

        if($usuariocod=='')
            $usuariocod=0;

        $spnombrelog='ins_logusuarios';
        $spparamlog=array('pcodigo_mensaje'=>$codigo_mensaje,'pacciondesc'=>$acciondesc,'pnumeroip'=>$_SERVER['REMOTE_ADDR'],'parchivonom'=>$archivonom,'pfecha'=>date('Y/m/d H:i:s'),'pusuariocod'=>$usuariocod);

        if(!$conexion->ejecutarStoredProcedure($spnombrelog,$spparamlog,$resultado,$numfilas,$errno) || $numfilas!=1)
        {
            $textoerror="Error registrar_acceso ".$errno." - ".$conexion->TextoError();
            // MANDAR MAIL con los par�metros de la llamada err�nea

            $subject=SISTEMA." - Error en registrar acceso";
            $texto="Se ha producido un error en ".$_SERVER['PHP_SELF']." \n\n";
            if($usuariocod!=0)
            {
                if(!$conexion->TraerCampo("usuarios","concat(usuarionombre,' ',usuarioapellido)",array("usuariocod='",$usuariocod,"'" ),$nombreusuario,$numfilas,$errno))
                    $texto.="Usuario ".$nombreusuario."\n\n";
            }
            $texto.="par�metros de acceso \n";
            $texto.="pcodigo_mensaje ".$codigo_mensaje."\n";
            $texto.="pacciondesc ".$acciondesc."\n";
            $texto.="pnumeroip ".$_SERVER['REMOTE_ADDR']."\n";
            $texto.="parchivonom ".$archivonom."\n";
            $texto.="pfecha ".date('Y/m/d H:i:s')."\n";
            $texto.="pusuariocod ".$usuariocod;
            FuncionesPHPLocal::MandarMail($conexion,$conexion->VerAdmiGeneral(),"",$subject,$texto);
            return false;
        }*/
        return true;
    }
//-----------------------------------------------------------------------------------------
// Arma un combo que recarga la p�gina seg�n la opci�n seleccionada

// Parametros:
// 		->spnombre y spparam se usan para armar el query
// 		->nomformulario es el nombre del formulario sobre el que est� el combo
// 		->nomcombo es el nombre del combo
// 		->clavecombo y desccombo son campos del query con los que arma el combo
// 		->querystring lo coloca despues del nombre del php
// 		->textolineauno es el texto del primer option del combo, cuando no hay nada seleccionado
// 		->regactual, en caso que exista una variables GET con el nombre del combo y coincide con algun registro del query, retorna la fila
// 		->seleccionado, en caso que se haya seleccionado un option del combo

    static function ArmarCombo_BD($conexion, $spnombre, $spparam, $nomformulario, $nomcombo, $clavecombo, $desccombo, $querystring, $textolineauno, &$regactual, &$seleccionado) {
        // Cargo los registros del query seleccionado
        if (!$conexion->ejecutarStoredProcedure($spnombre, $spparam, $resultado, $num_filas, $errno))
            return false;

        // Genera codigo JavaScript para autollamarse cuando selecciona un elemento del combo
        echo "<script type='text/javascript'>\n";
        echo "<!--\n";

        echo "function " . $nomcombo . "_newpage() {\n";

        $temp = "";
        if ($querystring != '')
            $temp = "?";

        echo "if(document." . $nomformulario . "." . $nomcombo . ".selectedIndex==0)\n";
        echo "    location='" . $_SERVER['PHP_SELF'] . $temp . $querystring . "';\n";
        echo "else\n";

        if ($querystring != '')
            $querystring .= "&";

        echo "    location='" . $_SERVER['PHP_SELF'] . "?" . $querystring . $nomcombo . "='+document." . $nomformulario . "." . $nomcombo . "[document." . $nomformulario . "." . $nomcombo . ".selectedIndex].value;\n";
        echo "}\n";

        echo "//-->\n";
        echo "</script>\n";

        // Generaci�n del combo, poniendo como primera linea $textolineauno
        echo "<select name='" . $nomcombo . "' id='" . $nomcombo . "' size='1' onchange='" . $nomcombo . "_newpage();' class='textoinput' style='width: 370px'>\n";
        echo "<option value=''>" . FuncionesPHPLocal::HtmlspecialcharsSistema($textolineauno, ENT_QUOTES) . "</option>\n";
        $seleccionado = false;
        if ($num_filas > 0) {
            // Recorre el cursor desde el principio, poniendo cada fila en una linea del combo
            $textopopup = '';
            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                if (isset($_GET[$nomcombo]) && $fila[$clavecombo] == $_GET[$nomcombo]) {
                    echo "<option selected value='" . FuncionesPHPLocal::HtmlspecialcharsSistema($fila[$clavecombo], ENT_QUOTES) . "'> " . FuncionesPHPLocal::HtmlspecialcharsSistema($fila[$desccombo], ENT_QUOTES) . " </option>\n";
                    $seleccionado = true;
                    $regactual = $fila;
                    $textopopup = $fila[$desccombo];
                } else
                    echo "<option value='" . FuncionesPHPLocal::HtmlspecialcharsSistema($fila[$clavecombo], ENT_QUOTES) . "'> " . FuncionesPHPLocal::HtmlspecialcharsSistema($fila[$desccombo], ENT_QUOTES) . " </option>\n";
            }
        }

        echo("</select>\n");
        if (isset ($textopopup))
            echo "<br /><a href='javascript:void(0)' onclick=\"javascript:popup('ventanatexto.php?textopopup='+" . $nomformulario . "." . $clavecombo . "[" . $nomformulario . "." . $clavecombo . ".selectedIndex].text,'ventanatexto',330,80,screen.availWidth-350,screen.availHeight-140,'yes')\" class='linkfondoblanco'><span class='textoaclaraciones'>Ver texto completo</span></a>\n";
        else
            echo "<div style='font-size: 7px;'>&nbsp;</div>\n";
    }

//-----------------------------------------------------------------------------------------
// Arma un combo con la informacion del SP enviado

// Parametros:
// 		->spnombre y spparam se usan para armar el query
// 		->nomformulario es el nombre del formulario sobre el que est� el combo
// 		->nomcombo es el nombre del combo
// 		->clavecombo y desccombo son campos del query con los que arma el combo
// 		->claveselec es el registro que hay que seleccionar
// 		->textolineauno es el texto del primer option del combo, cuando no hay nada seleccionado
// 		->regactual, en caso que se haya seleccionado un option se retorna la fila completa
// 		->seleccionado, en caso que se haya seleccionado un option del combo
//		->$filas, si es 1, es un combo, sino es una lista
//		->$onchange: la accion javascript al momento de seleccionar un elemento de la lista
//		->$style: estilo a aplicar sobre el combo
//		->$multiple: si se permite que en una lista se puedan elegir mas de un item
//		->$vertextocompleto: indica si se muestra el link abajo para ver texto completo

    static function HtmlspecialcharsSistema($string, $flags = "ENT_COMPAT | ENT_HTML401", $encoding = "ISO8859-1", $double_encode = true) {

        if ($string != null || $string != "null") {
            if (is_string($string) && trim($string) != "") {
                return htmlspecialchars($string, $flags, $encoding, $double_encode);
            }
        }
        return "";
    }


    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     *
     * @return string
     * @throws Exception
     */
    public static function randomString(int $length = 16): string {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytesSize = (int)ceil($size / 3) * 3;

            $bytes = random_bytes($bytesSize);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }


//-----------------------------------------------------------------------------------------
// Arma un combo en base a la informacion proveniente en un array. Con salto.

// Parametros:
// 		->valores_combo: array asociativo, del que salen los valores para el combo
//			valores_combo=array(1=>array($clavecombo=>"valor",$desccombo=>"valor"),2=>array($clavecombo=>"valor",$desccombo=>"valor"))
// 		->nomformulario es el nombre del formulario sobre el que est� el combo
// 		->nomcombo es el nombre del combo
// 		->clavecombo y desccombo son campos del array con los que arma el combo
//		->querystring es el string que se agrega a la linea
// 		->textolineauno es el texto del primer option del combo, cuando no hay nada seleccionado
// 		->regactual, en caso que se haya seleccionado un option se retorna la fila completa
// 		->seleccionado, en caso que se haya seleccionado un option del combo

    static function ArmarCombo_deArray($valores_combo, $nomformulario, $nomcombo, $clavecombo, $desccombo, $querystring, $textolineauno, &$regactual, &$seleccionado, $filas = 1, $style = "width: 370px", $multiple = false, $vertextocompleto = true) {
        $seleccionado = false;
        $regactual = [];

        // Genera codigo JavaScript para autollamarse cuando selecciona un elemento del combo
        echo "<script  type='text/javascript'>\n";
        echo "<!--\n";

        echo "static function " . $nomcombo . "_newpage() {\n";

        $temp = "";
        if ($querystring != '')
            $temp = "?";

        echo "if(document." . $nomformulario . "." . $nomcombo . ".selectedIndex==0)\n";
        echo "    location='" . $_SERVER['PHP_SELF'] . $temp . $querystring . "';\n";
        echo "else\n";

        if ($querystring != '')
            $querystring .= "&";

        echo "    location='" . $_SERVER['PHP_SELF'] . "?" . $querystring . $nomcombo . "='+document." . $nomformulario . "." . $nomcombo . "[document." . $nomformulario . "." . $nomcombo . ".selectedIndex].value;\n";
        echo "}\n";

        echo "//-->\n";
        echo "</script>\n";

        // Generaci�n del combo, poniendo como primera linea $textolineauno
        echo "<select name='" . $nomcombo . "' id='" . $nomcombo . "' size='1' style='" . $style . "' " . ($multiple ? "multiple ='multiple'" : "") . "  onchange='" . $nomcombo . "_newpage();' class='textotabla' >";
        echo "<option value=''>" . FuncionesPHPLocal::HtmlspecialcharsSistema($textolineauno, ENT_QUOTES) . "</option>";

        foreach ($valores_combo as $optioncombo) {
            // Recorre el array, poniendo cada fila en una linea del combo
            echo "<option ";

            if (isset($_GET[$nomcombo]) && $optioncombo[$clavecombo] == $_GET[$nomcombo]) {
                echo " selected ";
                $seleccionado = true;
                $regactual = $optioncombo;
            }

            echo " value='" . FuncionesPHPLocal::HtmlspecialcharsSistema($optioncombo[$clavecombo], ENT_QUOTES) . "'> " . FuncionesPHPLocal::HtmlspecialcharsSistema($optioncombo[$desccombo], ENT_QUOTES);
            echo "</option>";
        }

        echo("</select>");
        if ($vertextocompleto)
            echo "<br /><a href='javascript:void(0)' onclick=\"javascript:popup('ventanatexto.php?textopopup='+formulario." . $nomcombo . "[formulario." . $nomcombo . ".selectedIndex].text,'ventanatexto',330,80,screen.availWidth-350,screen.availHeight-140,'yes')\" class='linkfondoblanco'><span class='textoaclaraciones'>Ver texto completo</span></a>";
    }

//-----------------------------------------------------------------------------------------
// Arma un combo en base a la informacion proveniente en un array. Con salto.

// Parametros:
// 		->valores_combo: array asociativo, del que salen los valores para el combo
//			valores_combo=array(1=>array($clavecombo=>"valor",$desccombo=>"valor"),2=>array($clavecombo=>"valor",$desccombo=>"valor"))
// 		->nomformulario es el nombre del formulario sobre el que est� el combo
// 		->nomcombo es el nombre del combo
// 		->clavecombo y desccombo son campos del array con los que arma el combo
//		->querystring es el string que se agrega a la linea
// 		->textolineauno es el texto del primer option del combo, cuando no hay nada seleccionado
// 		->regactual, en caso que se haya seleccionado un option se retorna la fila completa
// 		->seleccionado, en caso que se haya seleccionado un option del combo

    static function ArmarCombo_SinSalto_deArray($valores_combo, $nomformulario, $nomcombo, $clavecombo, $desccombo, $querystring, $textolineauno, &$regactual, &$seleccionado, $mostrar_txt_completo = true, $claveselec = "", $style = "width: 370px") {
        $seleccionado = false;
        $regactual = [];

        // Generaci�n del combo, poniendo como primera linea $textolineauno
        echo "<select name='" . $nomcombo . "' id='" . $nomcombo . "' size=1 class='textotabla' style='" . $style . "'>";
        echo "<option value=''>" . FuncionesPHPLocal::HtmlspecialcharsSistema($textolineauno, ENT_QUOTES) . "</option>";

        foreach ($valores_combo as $optioncombo) {
            // Recorre el array, poniendo cada fila en una linea del combo
            echo "<option ";


            if ($optioncombo[$clavecombo] == $claveselec) {
                echo " selected ";
                $seleccionado = true;
                $regactual = $optioncombo;
            }
            /*
            if(isset($_GET[$nomcombo]) && $optioncombo[$clavecombo]==$_GET[$nomcombo])
            {
                echo " selected ";
                $seleccionado=true;
                $regactual=$optioncombo;
            }*/

            echo " value='" . FuncionesPHPLocal::HtmlspecialcharsSistema($optioncombo[$clavecombo], ENT_QUOTES) . "'> " . FuncionesPHPLocal::HtmlspecialcharsSistema($optioncombo[$desccombo], ENT_QUOTES);
            echo "</option>";
        }

        echo("</select>");
        if ($mostrar_txt_completo)
            echo "<br /><a href='javascript:void(0)' onclick=\"javascript:popup('ventanatexto.php?textopopup='+formulario." . $nomcombo . "[formulario." . $nomcombo . ".selectedIndex].text,'ventanatexto',330,80,screen.availWidth-350,screen.availHeight-140,'yes')\" class='linkfondoblanco'><span class='textoaclaraciones'>Ver texto completo</span></a>";
    }


//-----------------------------------------------------------------------------------------
// Si existe la variable en el QUERY_STRING, elimina todo desde ahi en adelante

    static function ArmarQueryString($variable) {
        // si existe en el get la variable $variable, la elimino
        if (isset($_GET[$variable])) {
            $posicion = strpos($_SERVER['QUERY_STRING'], $variable);
            if ($posicion === false)
                return $_SERVER['QUERY_STRING'];
            elseif ($posicion == 0)
                return "";
            else
                return substr($_SERVER['QUERY_STRING'], 0, $posicion - 1);
        } else
            return $_SERVER['QUERY_STRING'];
    }

//-----------------------------------------------------------------------------------------
// Valida el contenido del campo seg�n tipovalidacion

    static function ValidarContenido($conexion, $campo, $tipovalidacion) {
        switch ($tipovalidacion) {
            case "AlfanumericoPuro": // campo alfanumerico sin caracteres especiales
                if (strspn(strtoupper($campo), '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ') != strlen($campo))
                    return false;
                break;
            case "Email": // campo alfanumerico con algunos caracteres especiales (emails)
                if (!preg_match("/^[-._a-z0-9]+@([a-z0-9]+[-.]){1,2}[a-z]{2,4}([.][a-z]{2})?$/", $campo))
                    return false;
                break;
            case "FechaDDMMAAAA": // valida fecha en formato DD/MM/AAAA
                if (!preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $campo))
                    return false;
                [$dia, $mes, $anio] = explode('/', $campo);
                if (!checkdate($mes, $dia, $anio))
                    return false;
                break;
            case "FechaAAAAMMDD": // valida fecha en formato AAAA-MM-DD
                if (!preg_match("/^(?'anio'\d{4})-(?'mes'0[0-9]|1[0-2])-(?'dia'[0-2][0-9]|3[01])$/", $campo, $matches))
                    return false;

                if (!checkdate($matches['mes'], $matches['dia'], $matches['anio']))
                    return false;
                break;
            case "Periodo": // campo MM/AAAA
                if (!preg_match("/^[0-9]{2}\/[0-9]{4}$/", $campo))
                    return false;

                $anio = substr($campo, 3, 4);
                $mes = substr($campo, 0, 2);
                if (($anio < 2000) || ($anio > 2050) || ($mes < 1) || ($mes > 12))
                    return false;

                break;
            case "Hexa6Digitos": // color sem�foro
                if (!preg_match("/^[0-9A-F]{6}$/", $campo))
                    return false;
                break;
            case "Jurisdiccion": // campo numerico con puntos (s�lo para Jurisdicciones)
                if (!preg_match("/^[0-9]{2}\.([0-9]{3}\.([0-9a-zA-Z ]{2}\.){0,7}){0,1}$/", $campo))
                    return false;
                break;
            case "NumericoEntero": // campo numerico entero
                if (strspn($campo, '0123456789') != strlen($campo))
                    return false;
                break;
            case "NumericoEnteroPuro": // campo numerico entero que no comience por cero (puede ser solo 0)
                if (!preg_match("/^-?[1-9][0-9]*$|^0$/", $campo))
                    return false;
                break;
            case "Numerico2Decimales": // campo numerico con 2 decimales maximo
                if (!preg_match("/^[0-9]+([.][0-9]{1,2}){0,1}$/", $campo))
                    return false;
                break;
            case "Numerico3Decimales": // campo numerico con 2 decimales maximo
                if (!preg_match("/^[0-9]+([.][0-9]{1,3}){0,1}$/", $campo))
                    return false;
                break;
            case "Numerico6Decimales": // campo numerico con 2 decimales maximo
                if (!preg_match("/^[0-9]+([.][0-9]{1,6}){0,1}$/", $campo))
                    return false;
                break;
            case "URL": // campo numerico con 2 decimales maximo
                if (!preg_match('/^[a-z\d_-]{1,200}$/i', $campo))
                    return false;
                break;
            case "CUIT": // 11 posiciones numericas con digito mod.11
                if (strspn($campo, '0') == strlen($campo))
                    return false;

                if (!preg_match("/^[0-9]{11}$/", $campo))
                    return false;

                $array_factor_peso = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
                $suma = 0;
                $digito_verificador = 0;

                for ($i = 0; $i <= 9; $i++)
                    $suma = $suma + (substr($campo, $i, 1) * $array_factor_peso[$i]);

                $digito_verificador = 11 - ($suma % 11);

                if ($digito_verificador == 11)
                    $digito_verificador = 0;

                if ($digito_verificador != substr($campo, 10, 1))
                    return false;
                break;
            default:
                FuncionesPHPLocal::MostrarMensaje($conexion, MSG_ERRGRAVE, "Validaci�n no definida.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => FMT_TEXTO]);
                return false;
                break;
        }
        return true;
    }
//-----------------------------------------------------------------------------------------
// Valida la clave

    static function ValidarPassword($clave, $claveactual, $identificacion, $longmin) {

        $i = 0;
        $p = 0; // codigo de error que se produjo
        $c3 = 1; // cantidad de letras iguales secuenciales
        $a = 0; // mantiene el codigo ascii al recorrer la clave para las validaciones
        $sa = 1; // cantidad de letras consecutivas segun abecedario
        $sd = 1; // cantidad de letras consecutivas segun abecedario inverso
        $pa = 0; // mantiene la cantidad de caracteres iguales entre la nueva clave y la clave actual
        $ca = 0; // cuenta la cantidad de letras
        $cn = 0; // cuenta la cantidad de numeros
        $ant = 0; // mantiene un registro de la letra anterior
        $layout = []; // array que contiene secuencia de caracteres comunes

        $layout[] = "QWERTY";
        $layout[] = "YTREWQ";
        $layout[] = "ASDFG";
        $layout[] = "GFDSA";
        $layout[] = "ZXCVB";
        $layout[] = "BVCXZ";

        if (strlen($clave) < $longmin)
            $p = 12;

        $UsrId = strrev($identificacion); // contiene la identificacion al reves

        // Verifica si la clave contiene a la identificacion (al derecho y al reves)
        if (strpos($identificacion, $clave) !== false || strpos($clave, $identificacion) !== false)
            $p = 9;
        elseif (strpos($UsrId, $clave) !== false || strpos($clave, $UsrId) !== false)
            $p = 10;

        // Verifica si la clave contiene una secuencia de caracteres comunes
        if ($p == 0) {
            for ($i = 0; $i < count($layout); $i++) {
                if (strpos(strtoupper($clave), $layout[$i]) !== false) {
                    $p = 6;
                    break;
                }
            }
        }

        if ($p == 0) {
            for ($i = 0; $i < strlen($clave); $i++) {
                $a = ord(substr($clave, $i, 1));

                if ($a >= 48 && $a <= 57)
                    $cn++;
                elseif (($a >= 65 && $a <= 90) || ($a >= 97 && $a <= 122))
                    $ca++;

                if ($a == $ant) {
                    if (++$c3 > 3) // si tengo mas de 3 letras iguales consecutivas
                    {
                        $p = 2;
                        break;
                    }
                } else {
                    $c3 = 1;
                    if ($ant == $a - 1) {
                        if (++$sa > 3) // mas de 3 letras consecutivas segun abecedario
                        {
                            $p = 4;
                            break;
                        }
                        $sd = 1;
                    } else {
                        if ($ant == $a + 1) {
                            if (++$sd > 3) // mas de 3 letras consecutivas segun abecedario inverso
                            {
                                $p = 5;
                                break;
                            }
                            $sa = 1;
                        } else {
                            $sa = 1;
                            $sd = 1;
                        }
                    }
                }

                if ($a == ord(substr($claveactual, $i, 1))) {
                    if (++$pa > 5) // si tiene mas de 5 caracteres iguales que la clave actual
                    {
                        $p = 14;
                        break;
                    }
                }

                $ant = $a;

            }
        }


        //Al menos una mayuscula
        if ($p == 0) {
            $containsLetter = preg_match('/[A-Z]/', $clave);
            if ($containsLetter == 0)
                $p = 12;
        }


        //SI LA CANTIDAD DE NUMEROS ES IGUAL A 0 Y LA SUMA DE cantidad de caracteres con numeros es igual es porque no tiene ningun caracter especial
        if (($p == 0) && ($cn == 0) && (($ca + $cn) == strlen($clave)))
            $p = 13;


        if ($p != 0)
            return false;
        else
            return true;
    }

    /*
    //-----------------------------------------------------------------------------------------
    // Arma los 2 listbox para pasar de uno al otro (de izquierda a derecha)

    // Parametros:
    // 		->sp_nombre y sp_param es el SP con el que se genera la lista de la izquierda
    // 		->clavecombo y desccombo son campos del array con los que arma el combo
    // 		->nomformulario es el nombre del formulario sobre el que est� el combo
    //		->nombrelista es el nombre que van a recibir las listas, botones, etc
    //		->textoizq y textoder es el texto que aparecer� arriba de cada lista
    //		->style es el estilo que se aplicar� a las listas
    //		->altura1/altura2: son las alturas en px de cada lista
    //		->subebaja si se quiere que en la lista de la derecha aparezcan los botones para subir y bajar elementos

        function ArmarListasConCajaTexto($conexion,$sp_nombre,$sp_param,$clavecombo,$desccombo,$nomformulario,$nombrelista,$textoizq,$textoder,$style,$altura1,$altura2,$subebaja)
        {
    ?>
    <table width="100%" class="textotabla">
        <tr>
            <td width="50%" valign="bottom">
                <label>
                    <b><?php  echo  FuncionesPHPLocal::HtmlspecialcharsSistema($textoizq,ENT_QUOTES) ?></b><br />
                    <input type="text" name="model_<?php  echo $nombrelista ?>" id="intext_<?php  echo $nombrelista ?>" size="25" onkeyup="ListaBuscarTexto('<?php  echo $nombrelista ?>',this.value,cod_todos_<?php  echo $nombrelista ?>,txt_todos_<?php  echo $nombrelista ?>,cod_activos_<?php  echo $nombrelista ?>,txt_activos_<?php  echo $nombrelista ?>);" style="width:188px;">
                </label>
    <?php
        if(!$conexion->ejecutarStoredProcedure($sp_nombre,$sp_param,$result,$numfilas,$errno)) die();

        $codigos=array();
        $textos=array();
        while($fila=mysql_fetch_assoc($result))
        {
            $codigos[]=FuncionesPHPLocal::ReemplazarComillas($fila[$clavecombo]);
            $textos[]=FuncionesPHPLocal::ReemplazarComillas($fila[$desccombo]);
        }

        if(count($codigos)>0)
        {
            $codigos="'".implode("','",$codigos)."'";
            $textos="'".implode("','",$textos)."'";
        }
        else
        {
            $codigos="";
            $textos="";
        }
    ?>
    <script language="javascript" type="text/javascript">

    var cod_todos_<?php  echo $nombrelista ?>=new Array(<?php  echo $codigos ?>);
    var txt_todos_<?php  echo $nombrelista ?>=new Array(<?php  echo $textos ?>);
    var cod_activos_<?php  echo $nombrelista ?>=new Array();
    var txt_activos_<?php  echo $nombrelista ?>=new Array();

    var selec_cod_<?php  echo $nombrelista ?> = new Array();
    var selec_txt_<?php  echo $nombrelista ?> = new Array();

    </script>

            </td>
            <td width="50%" valign="bottom">
                <b><?php  echo  FuncionesPHPLocal::HtmlspecialcharsSistema($textoder,ENT_QUOTES) ?>:</b>
            </td>
        </tr>
        <tr>
            <td width="50%" valign="top">
                <i>click para agregar:</i>
                <div id="<?php  echo $nombrelista ?>" style="height:<?php  echo $altura1 ?>px;border:1px solid #c0c0c0;padding:5px;overflow:auto;<?php  echo $style ?>"></div>
            </td>
            <td width="50%" valign="top">
                <i>click para eliminar:</i>
                <div id="<?php  echo $nombrelista ?>_selec" style="height:<?php  echo $altura2 ?>px;border:1px solid #c0c0c0;padding:5px;overflow:auto;<?php  echo $style ?>"></div>
                <p>
                <input type="hidden" name="<?php  echo $nombrelista ?>_cod_selec" id="<?php  echo $nombrelista ?>_cod_selec">
                </p>
            </td>
        </tr>
    </table>
    <?php
        }
    */
//-----------------------------------------------------------------------------------------
// Arma los 2 listbox para pasar de uno al otro (de izquierda a derecha)

// Parametros:
// 		->sp_nombre y sp_param es el SP con el que se genera la lista de la izquierda
// 		->clavecombo y desccombo son campos del array con los que arma el combo
// 		->nomformulario es el nombre del formulario sobre el que est� el combo
//		->nombrelista es el nombre que van a recibir las listas, botones, etc
//		->textoizq y textoder es el texto que aparecer� arriba de cada lista
//		->style es el estilo que se aplicar� a las listas
//		->filas es la cantidad de filas en la lista
//		->subebaja si se quiere que en la lista de la derecha aparezcan los botones para subir y bajar elementos


    static function ArmarListas($conexion, $sp_nombre, $sp_param, $clavecombo, $desccombo, $nomformulario, $nombrelista, $textoizq, $textoder, $style, $filas, $subebaja) {
        ?>


        <table class="nostyle">
            <tr>
                <td style="width:300px;">
                    <label><?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($textoizq, ENT_QUOTES) ?></label>
                    <?php
                    $texto = "PasarItemListaMultiple(document." . $nomformulario . "[\"" . $nombrelista . "izq[]\"],document." . $nomformulario . "[\"" . $nombrelista . "der[]\"],true)";
                    FuncionesPHPLocal::ArmarCombo_SinSalto_BD($conexion, $sp_nombre, $sp_param, $nomformulario, $nombrelista . "izq[]", $clavecombo, $desccombo, "", "", $regnousar, $validonousar, $filas, "", $style, true, true, $texto)
                    ?>
                </td>
                <td style="width:50px; vertical-align:middle; text-align:center">
                    <div style="margin-top:0px;">
                        <a href="JavaScript:PasarItemListaMultiple(<?php echo "document." . $nomformulario . "['" . $nombrelista . "izq" ?>[]'],<?php echo "document." . $nomformulario . "['" . $nombrelista . "der" ?>[]'],true)"><img
                                src="assets/images/ver.png" alt="der" border="0"/></a>
                        <br/>
                        <a href="JavaScript:PasarItemListaMultiple(<?php echo "document." . $nomformulario . "['" . $nombrelista . "der" ?>[]'],<?php echo "document." . $nomformulario . "['" . $nombrelista . "izq" ?>[]'],true)"><img
                                src="assets/images/izq.png" alt="izq" border="0" align="middle"/></a>
                    </div>
                </td>
                <td style="width:300px;">
                    <label><?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($textoder, ENT_QUOTES) ?></label>
                    <?php
                    $texto = "PasarItemListaMultiple(document." . $nomformulario . "[\"" . $nombrelista . "der[]\"],document." . $nomformulario . "[\"" . $nombrelista . "izq[]\"],true)";
                    ?>
                    <select name="<?php echo $nombrelista . 'der' ?>[]" id="<?php echo $nombrelista . 'der' ?>[]"
                            size="<?php echo $filas ?>" style="<?php echo $style ?>" ondblclick='<?php echo $texto ?>'
                            multiple="multiple"></select>
                    <?php
                    if ($subebaja) {
                        ?>
                        <input type="button" name="<?php echo $nombrelista . "botonsubir" ?>" value="Subir"
                               class="botones"
                               onClick="MoverItemLista(formulario['<?php echo $nombrelista . "der[]" ?>'],'subir')"/>&nbsp;&nbsp;
                        <input type="button" name="<?php echo $nombrelista . "botonbajar" ?>" value="Bajar"
                               class="botones"
                               onClick="MoverItemLista(formulario['<?php echo $nombrelista . "der[]" ?>'],'bajar')"/>
                        <?php
                    }
                    ?>
                </td>
            </tr>
        </table>

        <?php
        return true;
    }
    /*
    //-----------------------------------------------------------------------------------------
    // Arma los 2 listbox para pasar de uno al otro (de arriba a abajo )

    // Parametros:
    // 		->sp_nombre y sp_param es el SP con el que se genera la lista de la izquierda
    // 		->clavecombo y desccombo son campos del array con los que arma el combo
    // 		->nomformulario es el nombre del formulario sobre el que est� el combo
    //		->nombrelista es el nombre que van a recibir las listas, botones, etc
    //		->textoarriba y textoabajo es el texto que aparecer� arriba de cada lista
    //		->style es el estilo que se aplicar� a las listas
    //		->filas es la cantidad de filas en la lista
    //		->subebaja si se quiere que en la lista de la derecha aparezcan los botones para subir y bajar elementos

        function ArmarListasAB($conexion,$sp_nombre,$sp_param,$clavecombo,$desccombo,$nomformulario,$nombrelista,$textoarriba,$textoabajo,$style,$filas,$subebaja)
        {
    ?>

    <table width="100%" class="textotabla">
        <tr align="center">
            <td><strong><?php  echo  FuncionesPHPLocal::HtmlspecialcharsSistema($textoarriba,ENT_QUOTES) ?></strong></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td align="center">
    <?php
            FuncionesPHPLocal::ArmarCombo_SinSalto_BD($conexion,$sp_nombre,$sp_param,$nomformulario,$nombrelista."arriba",$clavecombo,$desccombo,"","",$regnousar,$validonousar,$filas,"",$style)
    ?>
            </td>
        </tr>
        <tr>
            <td align="center">
                <a href="JavaScript:PasarItemLista(<?php  echo "document.".$nomformulario.".".$nombrelista."arriba" ?>,<?php  echo "document.".$nomformulario."['".$nombrelista."abajo" ?>[]'],true)"><img src="images/bajar.jpg" border="0" alt="bajar"></a>
                <a href="JavaScript:PasarItemLista(<?php  echo "document.".$nomformulario."['".$nombrelista."abajo" ?>[]'],<?php  echo "document.".$nomformulario.".".$nombrelista."arriba" ?>,true)"><img src="images/subir.jpg" border="0" alt="subir"></a>
            </td>

        </tr>
        <tr align="center">
            <td><strong><?php  echo  FuncionesPHPLocal::HtmlspecialcharsSistema($textoabajo,ENT_QUOTES) ?></strong></td>
        </tr>
        <tr>
            <td align="center">
                <select name="<?php  echo $nombrelista.'abajo' ?>[]" size="<?php  echo $filas ?>" style="<?php  echo $style ?>" multiple></select>
    <?php
            if($subebaja)
            {
    ?>
                <input type="button" name="<?php  echo $nombrelista."botonsubir" ?>" value="Subir" onclick="MoverItemLista(formulario['<?php  echo $nombrelista."abajo[]" ?>'],'subir')">&nbsp;&nbsp;
                <input type="button" name="<?php  echo $nombrelista."botonbajar" ?>" value="Bajar" onclick="MoverItemLista(formulario['<?php  echo $nombrelista."abajo[]" ?>'],'bajar')">
    <?php
            }
    ?>

            </td>
        </tr>
    </table>

    <?php
            return true;
        }

    */
//-----------------------------------------------------------------------------------------
// cambia algunos caracteres para que no de error el JS

    static function ArmarCombo_SinSalto_BD($conexion, $spnombre, $spparam, $nomformulario, $nombrecombo, $clavecombo, $desccombo, $claveselec, $textolineauno, &$regactual, &$seleccionado, $filas = 1, $onchange = "", $style = "width: 370px", $multiple = false, $vertextocompleto = true, $onDbClick = "", $disabled = false, $class = "", $tabindex = "") {
        // Cargo los registros del query seleccionado
        if (!$conexion->ejecutarStoredProcedure($spnombre, $spparam, $resultado, $num_filas, $errno))
            return false;
        $txtdisabled = "";
        if ($disabled)
            $txtdisabled = "disabled='disabled'";

        $tabindextxt = "";
        if ($tabindex != "")
            $tabindextxt = " tabindex='" . $tabindex . "'";

        // Generaci�n del combo, poniendo como primera linea $textolineauno
        echo "<select name='" . $nombrecombo . "' id='" . $nombrecombo . "' size='" . $filas . "' class='form-control input-md " . $class . "' style='" . $style . "' onchange='" . $onchange . "'  ondblclick='" . $onDbClick . "' " . ($multiple ? "multiple ='multiple'" : "") . " " . $txtdisabled . $tabindextxt . "  >\n";
        if ($filas == 1)
            echo "<option value=''>" . FuncionesPHPLocal::HtmlspecialcharsSistema($textolineauno, ENT_QUOTES) . "</option>\n";

        $seleccionado = false;
        if ($num_filas > 0) {
            // Recorre el cursor desde el principio, poniendo cada fila en una linea del combo
            while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
                if ($fila[$clavecombo] == $claveselec) {
                    echo "<option selected=\"selected\" value='" . FuncionesPHPLocal::HtmlspecialcharsSistema($fila[$clavecombo], ENT_QUOTES) . "'> " . FuncionesPHPLocal::HtmlspecialcharsSistema($fila[$desccombo], ENT_QUOTES) . " </option>\n";
                    $seleccionado = true;
                    $regactual = $fila;
                } else
                    echo "<option value='" . FuncionesPHPLocal::HtmlspecialcharsSistema($fila[$clavecombo], ENT_QUOTES) . "'> " . FuncionesPHPLocal::HtmlspecialcharsSistema($fila[$desccombo], ENT_QUOTES) . " </option>\n";
            }
        }

        echo("</select>\n");
        /*
        if($filas==1 && $vertextocompleto)
            echo "<br /><a href='javascript:void(0)' onclick=\"javascript:popup('ventanatexto.php?textopopup='+".$nomformulario.".".$nombrecombo."[".$nomformulario.".".$nombrecombo.".selectedIndex].text,'ventanatexto',330,80,screen.availWidth-350,screen.availHeight-140,'yes')\" class='linkfondoblanco'><span class='textoaclaraciones'>Ver texto completo</span></a>\n";
        */
    }

//-----------------------------------------------------------------------------------------
// Eliminamos los espacios en blanco

    static function ReemplazarComillas($texto) {
        if ($texto != "")
            return str_replace(["'", "\r", "\n", "</"], ["\'", "\\r", "\\n", "<\/"], $texto);
        else
            return $texto;
    }

//-----------------------------------------------------------------------------------------
// Convierte la fecha de un formato a otro

    static function RemplazarEspaciosBlanco($texto) {
        if ($texto != "")
            return str_replace(" ", "", $texto);
        else
            return $texto;
    }

    static function ConvertirFecha($fecha, $formatoinput, $formatooutput) {
        if ($fecha == '') return "";

        if ($formatoinput == 'dd/mm/aaaa' && $formatooutput == 'aaaa/mm/dd')
            return substr($fecha, 6, 4) . "/" . substr($fecha, 3, 2) . "/" . substr($fecha, 0, 2);
        elseif ($formatoinput == 'dd/mm/aaaa' && $formatooutput == 'aaaa-mm-dd')
            return substr($fecha, 6, 4) . "-" . substr($fecha, 3, 2) . "-" . substr($fecha, 0, 2);
        elseif ($formatoinput == 'dd/mm/aaaa' && $formatooutput == 'aaaammdd')
            return substr($fecha, 6, 4) . substr($fecha, 3, 2) . substr($fecha, 0, 2);
        elseif ($formatoinput == 'aaaammdd' && $formatooutput == 'dd/mm/aaaa')
            return substr($fecha, 6, 2) . "/" . substr($fecha, 4, 2) . "/" . substr($fecha, 0, 4);
        elseif ($formatoinput == 'aaaammdd' && $formatooutput == 'aaaa-mm-dd')
            return substr($fecha, 0, 4) . "-" . substr($fecha, 4, 2) . "-" . substr($fecha, 6, 2);
        elseif ($formatoinput == 'aaaa-mm-dd' && $formatooutput == 'dd/mm/aaaa')
            return substr($fecha, 8, 2) . "/" . substr($fecha, 5, 2) . "/" . substr($fecha, 0, 4);
        elseif ($formatoinput == 'aaaa-mm-dd' && $formatooutput == 'dd-mm-aaaa')
            return substr($fecha, 8, 2) . "-" . substr($fecha, 5, 2) . "-" . substr($fecha, 0, 4);
        elseif ($formatoinput == 'timestamp' && $formatooutput == 'dd/mm/aaaa')
            return substr($fecha, 6, 2) . "/" . substr($fecha, 4, 2) . "/" . substr($fecha, 0, 4);
        elseif ($formatoinput == 'aaaammdd' && $formatooutput == 'mm/aaaa')
            return substr($fecha, 4, 2) . "/" . substr($fecha, 0, 4);
        elseif ($formatoinput == 'datetime' && $formatooutput == 'dd/mm/aaaa')
            return substr($fecha, 8, 2) . "/" . substr($fecha, 5, 2) . "/" . substr($fecha, 0, 4);
        elseif ($formatoinput == 'aaaa-mm-dd' && $formatooutput == 'mm/aaaa')
            return substr($fecha, 5, 2) . "/" . substr($fecha, 0, 4);
        elseif ($formatoinput == 'dd-mm-aaaa' && $formatooutput == 'aaaa/mm/dd')
            return substr($fecha, 6, 4) . "/" . substr($fecha, 3, 2) . "/" . substr($fecha, 0, 2);
    }

    /**
     * @param string $fecha
     * @param string $formatoOutput
     *
     * @return string
     * @throws Exception
     */
    static function ConvertirFechaAutodetect(string $fecha, string $formatoOutput = 'aaaa-mm-dd'): string {
        if (!preg_match("/^(?'anio'\d{4}).(?'mes'0[0-9]|1[0-2]).(?'dia'[0-2][0-9]|3[01]).*/", $fecha, $matches)) {
            if (preg_match("/^(?'anio'\d{3}).(?'mes'0[0-9]|1[0-2]).(?'dia'[0-2][0-9]|3[01]).*/", $fecha, $matches))
                $matches['anio'] = "2{$matches['anio']}";
            elseif (preg_match("/^(?'anio'\d{2}).(?'mes'0[0-9]|1[0-2]).(?'dia'[0-2][0-9]|3[01]).*/", $fecha, $matches))
                $matches['anio'] = "20{$matches['anio']}";
            elseif (!preg_match("/^(?'dia'[0-2][0-9]|3[01]).(?'mes'0[0-9]|1[0-2])-(?'anio'\d{4}).*/", $fecha, $matches)) {
                throw new Exception("Error, formato de fecha incorrecto ($fecha)");
            }
        }

        switch ($formatoOutput) {
            case 'aaaa/mm/dd':
                $fecha = "{$matches['anio']}/{$matches['mes']}/{$matches['dia']}";
                break;
            case 'aaaammdd':
                $fecha = "{$matches['anio']}{$matches['mes']}{$matches['dia']}";
                break;
            case 'dd/mm/aaaa':
                $fecha = "{$matches['dia']}/{$matches['mes']}/{$matches['anio']}";
                break;
            case 'mm/aaaa':
                $fecha = "{$matches['mes']}/{$matches['anio']}";
                break;
            case 'aaaa-mm':
                $fecha = "{$matches['anio']}-{$matches['mes']}";
                break;
            default: // 'aaaa-mm-dd'
                $fecha = "{$matches['anio']}-{$matches['mes']}-{$matches['dia']}";
        }
        return $fecha;
    }
    /*
    Resultado:
    31
    30
    */
    /*
    //-----------------------------------------------------------------------------------------
    // Genera una tabla, cuya celda inferior se puede cerrar y abrir

        function GenerarTablaDesplegable($nombrecelda,$textotitulo,$textoinferior)
        {
    ?>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #A0C6E5" class="textotabla">
            <tr onclick="EC('<?php  echo $nombrecelda ?>');" style="cursor:pointer" class='colorfondoseleccion'>
                <td style="padding-left:10px"><?php  echo $textotitulo ?></td>
                <td align="right" style="padding-right:7px"><img id="<?php  echo $nombrecelda ?>img" src="images/i.p.arr.down.jpg" border=0 align="middle" hspace="1" alt="flecha"></td>
            </tr>
            <tr>
                <td colspan="2" style="padding:0 0 0 0">
                    <div id="<?php  echo $nombrecelda ?>" style="display:block; visibility:hidden; position:absolute"><?php  echo $textoinferior ?></div>
                </td>
            </tr>
        </table>
    <?php
        }

    //-----------------------------------------------------------------------------------------
    // Arma el link para enlances abajo de los combos

    // Parametros:
    // 		->archivonom es a donde va a apuntar el link
    //		->querystring es el texto despues del archivonom
    //		->nomnivelactualiz y codnivelactualiz es el nuevo codigo que se agrega al get
    //		->separador es el texto antes del link
    //		->texto que va en el link

        function ArmarLinkEstrucVertical($conexion,$archivonom,$querystring,$nomnivelactualiz,$codnivelactualiz,$separador,$texto)
        {
            echo "<span class='textoaclaraciones'>". FuncionesPHPLocal::HtmlspecialcharsSistema($separador,ENT_QUOTES);
            if($nomnivelactualiz!="")
                $querystring.="&".$nomnivelactualiz."=".$codnivelactualiz;
            if($querystring!="")
                $querystring="?".$querystring;

            echo "<a href='". FuncionesPHPLocal::HtmlspecialcharsSistema($archivonom,ENT_QUOTES). FuncionesPHPLocal::HtmlspecialcharsSistema($querystring,ENT_QUOTES)."' class='linkfondoblanco'>";
            echo  FuncionesPHPLocal::HtmlspecialcharsSistema($texto,ENT_QUOTES);
            echo "</a></span>";
        } // fin ArmarLinkEstrucVertical

    //-----------------------------------------------------------------------------------------
    // Retorna un nombre de archivo que no se repita con uno existente

    // Parametros:
    // 		->spnombre y spparam para la tabla donde estan los nombres de archivos existentes
    //		->nombrecampo es el campo donde buscar el nombre de archivo
    //		->archivo es el nombre original de archivo
    //		->nuevonombre es el nombre que no se repite

        function NombreArchivoValido($conexion,$spnombre,$spparam,$nombrecampo,$archivo,&$nuevonombre)
        {
            $encontrado=false;
            $nuevonombre=$archivo;
            $n=1;
            while(!$encontrado)
            {
                $arraybusq=array($nombrecampo=>$nuevonombre);
                if(!$conexion->BuscarRegistroxClave($spnombre,$spparam,$arraybusq,$query,$filaret,$numfilasmatcheo,$errno))
                {
                    FuncionesPHPLocal::MostrarMensaje($conexion,MSG_ERRGRAVE,"Se produjo un error al obtener la imagen. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
                    return false;
                }

                if($numfilasmatcheo==0)
                    $encontrado=true;
                else
                {
                    $nuevonombre=substr($nuevonombre,0,strrpos($nuevonombre,"."))."_".$n.".".substr(strrchr($nuevonombre,"."),1);
                    $n++;
                }
            }

            return true;
        }
    */
//-----------------------------------------------------------------------------------------
// Genera una password aleatoria del largo ingresado

    static function ObtenerUltimoDiaMes($elAnio, $elMes) {
        return date("d", (mktime(0, 0, 0, $elMes + 1, 1, $elAnio) - 1));
    }

//-----------------------------------------------------------------------------------------
// Retorna el querystring y el md5 correspondiente

    static function GenerarPassword($largo) {
        $nuevapwd = "";

        for ($i = 1; $i <= $largo; $i++)
            $nuevapwd .= chr(rand(65, 90));

        return $nuevapwd;

    }

    static function ArmarLinkMD5($pagina, $parametros, &$get, &$md5) {
        $codigospagina["usuarios_am.php"] = "COD1000";
//		$codigospagina["usuarios_modificar_datos.php"]="COD1000";
        $codigospagina["usuarios_areas.php"] = "COD1001";
        $codigospagina["usuarios_modificar_datos_upd.php"] = "COD1002";
        $codigospagina["usuario_eliminar_rol_upd.php"] = "COD1004";
        $codigospagina["usuarios_act_dct.php"] = "COD1005";
        $codigospagina["usuarios_cargar_acciones.php"] = "COD1006";
        $codigospagina["usuarios_clientes.php"] = "COD1007";

        //CONFIGURACION DE ARCHIVOS FILE
        $codigospagina["fil_config_am.php"] = "COD12001";
        $codigospagina["fil_config_upd.php"] = "COD12002";

        $codigospagina["roles_am.php"] = "COD12003";
        $codigospagina["grupos_categorias_am.php"] = "COD12004";
        $codigospagina["grupos_categorias_modulos_am.php"] = "COD12004";

        //CONFIGURACION DE ARCHIVOS FILE
        $codigospagina["municipios.php"] = "COD13004";


        $codigospagina["cir_circuito_workflow_confeccionar.php"] = "COD8001";
        $codigospagina["dig_digitalizar_verificacion_archivos.php"] = "COD15001";
        $codigospagina["dig_digitalizar_ver_archivo.php"] = "COD16001";
        $codigospagina["doc_documentos_tipos_confeccionar_campo_avanzado.php"] = "COD17001";


        $codigospagina["doc_documentos_tipos.php"] = "COD17001";
        $codigospagina["doc_documentos_tipos_am.php"] = "COD17002";


        $codigospagina["doc_documentos_alta.php"] = "COD19002";
        $codigospagina["doc_documentos_am.php"] = "COD19003";
        $codigospagina["doc_documentos_dependientes.php"] = "COD19004";
        $codigospagina["doc_documentos_reintegro.php"] = "COD19005";

        $codigospagina["cli_clientes_empresas_am.php"] = "COD30001";
        $codigospagina["cli_clientes_empresas_auditoria.php"] = "COD30002";

        $codigospagina["novedades_am.php"] = "COD40002";
        $codigospagina["novedades_log.php"] = "COD40003";
        $codigospagina["novedades_tree.php"] = "COD40004";

        $codigospagina["novedades_observaciones_lst.php"] = "COD40005";
        $codigospagina["novedades_documentacion_adjunta_descargar.php"] = "COD40006";

        $codigospagina["licencias_procesar.php"] = "COD50001";

        $codigospagina["esc_escuelas_confeccion_pdf.php"] = "COD60001";

        $codigospagina["detalle_puestos_pdf.php"] = "COD60002";

        $codigospagina["calendario_pdf.php"] = "COD60003";


        $codigospagina['lic_licencias_am.php'] = 'COD70001';
        $codigospagina['lic_licencias_administracion_am.php'] = 'COD70002';
        $codigospagina['lic_licencias_administracion_administrativas_am.php'] = 'COD70003';
        $codigospagina['lic_licencias_administracion_art_am.php'] = 'COD70004';
        $codigospagina['lic_licencias_administracion_mat_am.php'] = 'COD70005';
        $codigospagina['lic_licencias_administracion_inasistencias_am.php'] = 'COD70006';

        $codigospagina['solicitudes_log.php'] = 'COD70007';

        $codigospagina['liq_liquidador_am.php'] = 'COD70008';
        $codigospagina['liq_liquidador_pdf.php'] = 'COD70009';
        $codigospagina['log_movimientos_descarga.php'] = 'COD70010';

        $codigospagina['ddjj_otros_cargos_am'] = 'COD80001';

        $codigospagina['instrumentos_legales_documentacion_adjunta_descargar.php'] = 'COD80029';

        $codigospagina['modulo_documentacion_adjunta_descargar.php'] = 'COD80030';


        $get_array = [];
        $get = "";
        $codigo = "";
        $md5 = "";

        if (!isset($codigospagina[$pagina]) || count($parametros) < 1)
            return false;

        foreach ($parametros as $nombparam => $valparam) {
            $get_array[] = $nombparam . "=" . $valparam;
            $codigo .= $valparam;
        }

        $codigo .= $codigospagina[$pagina];
        $codigo .= $_SESSION["usuariocod"];

        $md5 = md5($codigo);

        $get = implode("&amp;", $get_array);
        $get .= "&amp;md5=" . $md5;

        return true;
    }

    static function ArmarLinkMD5Front($pagina, $parametros, &$get, &$md5) {
        $codigospagina["usuario_valida_cliente.php"] = "USER06314dFE$%&-{";
        $codigospagina["login_upd.php"] = "MSGENVIO0394dFE$%&-{";
        $codigospagina["licencias_procesar.php"] = "ENCHOST0482EgH$%&-{";

        $get_array = [];
        $get = "";
        $codigo = "";
        $md5 = "";

        if (!isset($codigospagina[$pagina]) || count($parametros) < 1)
            return false;

        foreach ($parametros as $nombparam => $valparam) {
            $get_array[] = $nombparam . "=" . $valparam;
            $codigo .= $valparam;
        }

        $codigo .= $codigospagina[$pagina];

        $md5 = md5($codigo);

        $get = implode("&amp;", $get_array);
        $get .= "&amp;md5=" . $md5;

        return true;
    }

    static function ConvertiraUtf8(array|object $arreglo): array|object {
        $arregloEnviar = [];
        $convertirObjeto = false;
        if (is_object($arreglo)) {
            $convertirObjeto = true;
            $arreglo = (array)$arreglo;
        }

        foreach ($arreglo as $clave => $valor) {
            if (is_array($valor) || is_object($valor)) {
                $arregloEnviar[$clave] = self::ConvertirAUtf8($valor);
            } elseif (is_string($valor)) {
                $arregloEnviar[$clave] = utf8_encode($valor);
            } else {
                $arregloEnviar[$clave] = $valor;
            }
        }
        if ($convertirObjeto === true) {
            $arregloEnviar = (object)$arregloEnviar;
        }

        return $arregloEnviar;
    }

    static function DecodificarUtf8(array|object $arreglo): array|object {
        $arregloEnviar = [];
        $convertirObjeto = false;
        if (is_object($arreglo)) {
            $convertirObjeto = true;
            $arreglo = (array)$arreglo;
        }
        foreach ($arreglo as $clave => $valor) {
            if (is_array($valor) || is_object($valor)) {
                $arregloEnviar[$clave] = self::DecodificarUtf8($valor);
            } elseif (is_string($valor)) {
                $arregloEnviar[$clave] = utf8_decode($valor);
            } else {
                $arregloEnviar[$clave] = $valor;
            }
        }
        if ($convertirObjeto === true) {
            $arregloEnviar = (object)$arregloEnviar;
        }


        return $arregloEnviar;
    }



    /*
        function EstilosTextAreaHTML($textarea)
        {
    ?>
        <div style='font-size: 3px;'>&nbsp;</div>
        <input type="button" name="URL" value="URL" class="botones" onclick="AgregarTagLink(<?php  echo $textarea ?>)">
        <input type="button" name="Bold" value="B" class="botones" onclick="AgregarEstilo(<?php  echo $textarea ?>,'B',true)">
        <input type="button" name="BoldFin" value="/B" class="botones" onclick="AgregarEstilo(<?php  echo $textarea ?>,'B',false)">
        <input type="button" name="Italic" value="I" class="botones" onclick="AgregarEstilo(<?php  echo $textarea ?>,'I',true)">
        <input type="button" name="ItalicFin" value="/I" class="botones" onclick="AgregarEstilo(<?php  echo $textarea ?>,'I',false)">
        <input type="button" name="Underline" value="U" class="botones" onclick="AgregarEstilo(<?php  echo $textarea ?>,'U',true)">
        <input type="button" name="UnderlineFin" value="/U" class="botones" onclick="AgregarEstilo(<?php  echo $textarea ?>,'U',false)">
        <input type="button" name="Bullet" value="&#8226;" class="botones" onclick="AgregarEstilo(<?php  echo $textarea ?>,'V',true)">
        <div style='font-size: 3px;'>&nbsp;</div>
    <?php

        }
    */
//-----------------------------------------------------------------------------------------
    /**
     * array_column_sort
     *
     * function to sort an "arrow of rows" by its columns
     * exracts the columns to be sorted and then
     * uses eval to flexibly apply the standard
     * array_multisort function
     *
     * uses a temporary copy of the array whith "_" prefixed to  the keys
     * this makes sure that array_multisort is working with an associative
     * array with string type keys, which in turn ensures that the keys
     * will be preserved.
     *
     * TODO: find a way of modifying the keys of $array directly, without using
     * a copy of the array.
     *
     * flexible syntax:
     * $new_array = array_column_sort($array [, 'col1' [, SORT_FLAG [, SORT_FLAG]]]...);
     *
     * original code credited to Ichier (www.ichier.de) here:
     * http://uk.php.net/manual/en/function.array-multisort.php
     *
     * prefixing array indeces with "_" idea credit to steve at mg-rover dot org, also here:
     * http://uk.php.net/manual/en/function.array-multisort.php
     *
     */
    /*
        function array_column_sort()
        {
            $args = func_get_args();
            $array = array_shift($args);
            $sort_array=array();

            if(count($array)==0)
                return $array;

            // make a temporary copy of array for which will fix the
            // keys to be strings, so that array_multisort() doesn't
            // destroy them
            $array_mod = array();
            foreach ($array as $key => $value)
                $array_mod['_' . $key] = $value;

            $i = 0;
            $multi_sort_line = "return array_multisort( ";
            foreach ($args as $arg)
            {
                $i++;
                if ( is_string($arg) )
                {
                    foreach ($array_mod as $row_key => $row)
                    {
                        $sort_array[$i][$row_key] = $row[$arg];
                    }
                }
                else
                {
                    $sort_array[$i] = $arg;
                }
                $multi_sort_line .= "\$sort_array[" . $i . "], ";
            }
            $multi_sort_line .= "SORT_ASC,\$array_mod );";

            eval($multi_sort_line);

            // now copy $array_mod back into $array, stripping off the "_"
            // that we added earlier.
            $array = array();
            foreach ($array_mod as $key => $value)
                $array[ substr($key, 1) ] = $value;

            return $array;
        }

    //-----------------------------------------------------------------------------------------
    // Separa el codigo post de una imagen para obtener el codigo

        function DesglosarBt ($cadenapost,$cadenainicio,&$codigo)
        {
            $codigo=strstr ($cadenapost, $cadenainicio );  // string empezando por Borrar
             $pos1=0;
            $pos2=0;
            $pos3=0;
            $pos1 = strpos($codigo, '_');
            if (!$pos1===false)
                $possig1 = strpos(substr($codigo,$pos1+1), '_');
            if (!$possig1===false)
            {
                $pos2= $pos1+$possig1+1;
            }

            if ($pos1>0 && $pos2>0)
            {
                $codigolength=$pos2-$pos1-1;
                $codigo=substr($codigo,$pos1+1,$codigolength);
            }
        }

    //-----------------------------------------------------------------------------------------
    // Hace la inversa a  FuncionesPHPLocal::HtmlspecialcharsSistema

        function htmldecode($encoded)
        {
            return strtr($encoded,array_flip(get_html_translation_table(HTML_ENTITIES)));
        }
    */
    static function array_column_sort() {
        $args = func_get_args();
        $array = array_shift($args);
        $sort_array = [];

        if (count($array) == 0)
            return $array;

        // make a temporary copy of array for which will fix the
        // keys to be strings, so that array_multisort() doesn't
        // destroy them
        $array_mod = [];
        foreach ($array as $key => $value)
            $array_mod['_' . $key] = $value;

        $i = 0;
        $multi_sort_line = "return array_multisort( ";
        foreach ($args as $arg) {
            $i++;
            if (is_string($arg)) {
                foreach ($array_mod as $row_key => $row) {
                    $sort_array[$i][$row_key] = $row[$arg];
                }
            } else {
                $sort_array[$i] = $arg;
            }
            $multi_sort_line .= "\$sort_array[" . $i . "], ";
        }
        $multi_sort_line .= "SORT_DESC,\$array_mod );";

        eval($multi_sort_line);

        // now copy $array_mod back into $array, stripping off the "_"
        // that we added earlier.
        $array = [];
        foreach ($array_mod as $key => $value)
            $array[substr($key, 1)] = $value;

        return $array;
    } //function Guardafoto($file, $savePath, $thumbD, $porcentajeCalidad, $formatoSalida){


    /*
    function Guardafoto($file, $thumbD, $porcentajeCalidad, $formatoSalida){
        //Obtenemos la informacion de la imagen, el array info tendra los siguientes indices:
        // 0: ancho de la imagen
        // 1: alto de la imagen
        // mime: el mime_type de la imagen
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
                //Creamos una nueva imagen cuadrada con las dimensiones que queremos:
                $nueva_altura = ceil($thumbD*($info[1]/$info[0]));
                $image_new = imagecreatetruecolor($thumbD, $nueva_altura);
                $bgcolor = imagecolorallocate($image_new, 255, 255, 255);
                imagefilledrectangle($image_new, 0, 0, $thumbD, $nueva_altura, $bgcolor);
                imagealphablending($image_new, true);
                //Copiamos la imagen original con las nuevas dimensiones
                imagecopyresampled($image_new, $image, 0, 0, $xpos, $ypos, $thumbD, $nueva_altura, $width, $height);
        } //if($formatoSalida == "T"){
        //Guardamos la nueva imagen como jpg
        return $image_new;
    } //function Guardafoto($file, $savePath, $thumbD, $porcentajeCalidad, $formatoSalida){
    */


    static function Guardafoto($file, $thumbD, $porcentajeCalidad, $formatoSalida) {
        //Obtenemos la informacion de la imagen, el array info tendra los siguientes indices:
        //0: ancho de la imagen
        //1: alto de la imagen
        //mime: el mime_type de la imagen
        $info = getimagesize($file);
        //Dependiendo del mime type, creamos una imagen a partir del archivo original:
        switch ($info['mime']) {
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
        if ($formatoSalida == "T") {
            //Si el ancho es igual al alto, la imagen ya es cuadrada, por lo que podemos ahorrarnos unos pasos:
            if ($info[0] == $info[1]) {
                $xpos = 0;
                $ypos = 0;
                $width = $info[1];
                $height = $info[1];
                //Si la imagen no es cuadrada, hay que hacer un par de averiguaciones:
            } else {
                if ($info[0] > $info[1]) {
                    //imagen horizontal
                    $xpos = ceil(($info[0] - $info[1]) / 2);
                    $ypos = 0;
                    $width = $info[1];
                    $height = $info[1];
                } else {
                    //imagen vertical
                    $ypos = ceil(($info[1] - $info[0]) / 2);
                    $xpos = 0;
                    $width = $info[0];
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
        } else { //if($formatoSalida == "T"){
            $xpos = 0;
            $ypos = 0;
            $width = $info[0];
            $height = $info[1];
            if ($info[0] > $info[1]) {
                //imagen horizontal
                //preguntamos si el ancho es mayor que el parametro de tama�o, para no agrandar una foto peque�a y pixelarla
                if ($info[0] > $thumbD) {
                    $nueva_altura = ceil($thumbD * ($info[1] / $info[0]));
                    $image_new = imagecreatetruecolor($thumbD, $nueva_altura);
                    $bgcolor = imagecolorallocate($image_new, 255, 255, 255);
                    imagefilledrectangle($image_new, 0, 0, $thumbD, $nueva_altura, $bgcolor);
                    imagealphablending($image_new, true);
                    imagecopyresampled($image_new, $image, 0, 0, $xpos, $ypos, $thumbD, $nueva_altura, $width, $height);
                } else { //if($info[0] > $ancho){
                    $image_new = imagecreatetruecolor($info[0], $info[1]);
                    $bgcolor = imagecolorallocate($image_new, 255, 255, 255);
                    imagefilledrectangle($image_new, 0, 0, $info[0], $info[1], $bgcolor);
                    imagealphablending($image_new, true);
                    imagecopyresampled($image_new, $image, 0, 0, $xpos, $ypos, $info[0], $info[1], $width, $height);
                } //if($info[0] > $ancho){
            } else { //if($info[0] > $info[1]){
                //imagen vertical
                //preguntamos si el alto es mayor que el parametro de tama�o, para no agrandar una foto peque�a y pixelarla
                if ($info[1] > $thumbD) {
                    $nueva_altura = ceil($thumbD * ($info[0] / $info[1]));
                    $image_new = imagecreatetruecolor($nueva_altura, $thumbD);
                    $bgcolor = imagecolorallocate($image_new, 255, 255, 255);
                    imagefilledrectangle($image_new, 0, 0, $nueva_altura, $thumbD, $bgcolor);
                    imagealphablending($image_new, true);
                    imagecopyresampled($image_new, $image, 0, 0, $xpos, $ypos, $nueva_altura, $thumbD, $width, $height);
                } else { //if($info[1] > $ancho){
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
    }

    static function ArmarLinkMD5PaginasComunes($parametros, &$get, &$md5) {
        $codigospagina = "COD1000";
        $get_array = [];
        $get = "";
        $codigo = "";
        $md5 = "";
        if (count($parametros) < 1)
            return false;
        foreach ($parametros as $nombparam => $valparam) {
            $get_array[] = $nombparam . "=" . $valparam;
            $codigo .= $valparam;
        }
        $codigo .= $codigospagina;
        $md5 = md5($codigo);
        $get = implode("&amp;", $get_array);
        $get .= "&amp;md5=" . $md5;

        return true;
    }

    static function aleatorio($cantidad) {
        srand(FuncionesPHPLocal::crear_semilla());

        // Generamos la clave
        $clave = "";
        $max_chars = round(rand($cantidad, $cantidad));  // tendr� entre 8 y 8 caracteres
        $chars = [];
        for ($i = "a"; $i < "z"; $i++) $chars[] = $i;  // creamos vector de letras
        $chars[] = "z";
        for ($i = 0; $i < $max_chars; $i++) {
            $letra = round(rand(0, 1));  // primero escogemos entre letra y n�mero
            if ($letra) // es letra
                $clave .= $chars[round(rand(0, count($chars) - 1))];
            else // es numero
                $clave .= round(rand(0, 9));
        }

        return $clave;
    }

    static function crear_semilla() {
        [$usec, $sec] = explode(' ', microtime());
        return (float)$sec + ((float)$usec * 100000);
    }

    static function eliminarImagen($archivo) {
        //el archivo viene con path
        if (unlink($archivo))
            return true;
        else
            return false;
    }

    static function get_file_extension($filename) {
        preg_match("/(.*)\.([a-zA-Z0-9]{0,5})$/", $filename, $regs);
        return ($regs[2]);
    }

    static function MostrarJerarquia($oCategorias, $catcod, &$jerarquia, &$nivel) {
        $i = 1;
        $jerarquia = "";
        $nivel = 0;
        $arrjerarquia = [];
        if (!$oCategorias->ArregloPadres($catcod, $arrjerarquia, $nivel))
            return false;


        foreach ($arrjerarquia as $clave => $valor) {
            $categoriadominio = FuncionesPHPLocal::EscapearCaracteres($valor['catnom']);
            $categoriadominio = preg_replace('/[^a-zA-Z0-9-_+ ]/', '-', $categoriadominio);
            $categoriadominio = str_replace(' ', '-', $categoriadominio);

            if ($i != $nivel) {
                $jerarquia .= "<a href='" . $categoriadominio . "_";
                $jerarquia .= $valor['catcod'];
                $jerarquia .= "/' class='bold'>";
                $jerarquia .= $valor['catnom'] . "</a> &raquo; ";
            } else
                $jerarquia .= "<span class=\"bold\">" . $valor['catnom'] . "</span>";

            $i++;
        }
        $nivel = 0;

        return true;
    }

    static function EscapearCaracteres($oracion) {
        $oracion = strtolower($oracion);
        $caracteresescapeados = trim(str_replace(['/'], '', $oracion));
        $caracteresescapeados = trim(str_replace(["�", "�", "�", "�", "�", "�"], ["a", "e", "i", "o", "u", "n"], $caracteresescapeados));
        $caracteresescapeados = trim(str_replace(["�", "�", "�", "�", "�", "�"], ["a", "e", "i", "o", "u", "n"], $caracteresescapeados));
        return $caracteresescapeados;
    }

    static function ReemplazarTextoFechas($texto) {
        // reemplazo meses
        $texto = str_replace("January", "Enero", $texto);
        $texto = str_replace("February", "Febrero", $texto);
        $texto = str_replace("March", "Marzo", $texto);
        $texto = str_replace("April", "Abril", $texto);
        $texto = str_replace("May", "Mayo", $texto);
        $texto = str_replace("June", "Junio", $texto);
        $texto = str_replace("July", "Julio", $texto);
        $texto = str_replace("August", "Agosto", $texto);
        $texto = str_replace("September", "Septiembre", $texto);
        $texto = str_replace("October", "Octubre", $texto);
        $texto = str_replace("November", "Noviembre", $texto);
        $texto = str_replace("December", "Diciembre", $texto);

        // reemplazo meses cortos
        $texto = str_replace("Jan", "Ene", $texto);
        $texto = str_replace("Apr", "Abr", $texto);
        $texto = str_replace("Aug", "Ago", $texto);
        $texto = str_replace("Sep", "Sep", $texto);
        $texto = str_replace("Oct", "Oct", $texto);
        $texto = str_replace("Nov", "Nov", $texto);
        $texto = str_replace("Dec", "Dic", $texto);

        // reemplazo d�as
        $texto = str_replace("Monday", "Lunes", $texto);
        $texto = str_replace("Tuesday", "Martes", $texto);
        $texto = str_replace("Wednesday", "Mi�rcoles", $texto);
        $texto = str_replace("Thursday", "Jueves", $texto);
        $texto = str_replace("Friday", "Viernes", $texto);
        $texto = str_replace("Saturday", "S�bado", $texto);
        $texto = str_replace("Sunday", "Domingo", $texto);

        // reemplazo dias cortos
        $texto = str_replace("Mon", "Lun", $texto);
        $texto = str_replace("Tue", "Mar", $texto);
        $texto = str_replace("Wed", "Mie", $texto);
        $texto = str_replace("Thu", "Jue", $texto);
        $texto = str_replace("Fri", "Vie", $texto);
        $texto = str_replace("Sat", "S�b", $texto);
        $texto = str_replace("Sun", "Dom", $texto);

        return $texto;
    }

    static function ReemplazarMeses($texto) {
        // reemplazo meses usando dos cifras
        $texto = str_replace("01", "Enero", $texto);
        $texto = str_replace("02", "Febrero", $texto);
        $texto = str_replace("03", "Marzo", $texto);
        $texto = str_replace("04", "Abril", $texto);
        $texto = str_replace("05", "Mayo", $texto);
        $texto = str_replace("06", "Junio", $texto);
        $texto = str_replace("07", "Julio", $texto);
        $texto = str_replace("08", "Agosto", $texto);
        $texto = str_replace("09", "Septiembre", $texto);
        $texto = str_replace("10", "Octubre", $texto);
        $texto = str_replace("11", "Noviembre", $texto);
        $texto = str_replace("12", "Diciembre", $texto);

        // reemplazo meses usando una cifra
        $texto = str_replace("1", "Enero", $texto);
        $texto = str_replace("2", "Febrero", $texto);
        $texto = str_replace("3", "Marzo", $texto);
        $texto = str_replace("4", "Abril", $texto);
        $texto = str_replace("5", "Mayo", $texto);
        $texto = str_replace("6", "Junio", $texto);
        $texto = str_replace("7", "Julio", $texto);
        $texto = str_replace("8", "Agosto", $texto);
        $texto = str_replace("9", "Septiembre", $texto);


        return $texto;
    }


    /*FUNCTIONES DEL CALENDAR*/

    static function CargarComboMes($mesActual = 1) {
        ?>
        <option value="01" <?php if ($mesActual == 1) echo 'selected="selected"' ?>>Enero</option>
        <option value="02" <?php if ($mesActual == 2) echo 'selected="selected"' ?>>Febrero</option>
        <option value="03" <?php if ($mesActual == 3) echo 'selected="selected"' ?>>Marzo</option>
        <option value="04" <?php if ($mesActual == 4) echo 'selected="selected"' ?>>Abril</option>
        <option value="05" <?php if ($mesActual == 5) echo 'selected="selected"' ?>>Mayo</option>
        <option value="06" <?php if ($mesActual == 6) echo 'selected="selected"' ?>>Junio</option>
        <option value="07" <?php if ($mesActual == 7) echo 'selected="selected"' ?>>Julio</option>
        <option value="08" <?php if ($mesActual == 8) echo 'selected="selected"' ?>>Agosto</option>
        <option value="09" <?php if ($mesActual == 9) echo 'selected="selected"' ?>>Septiembre</option>
        <option value="10" <?php if ($mesActual == 10) echo 'selected="selected"' ?>>Octubre</option>
        <option value="11" <?php if ($mesActual == 11) echo 'selected="selected"' ?>>Noviembre</option>
        <option value="12" <?php if ($mesActual == 12) echo 'selected="selected"' ?>>Diciembre</option>
        <?php
    }

    static function ObtenerDiasSemana() {
        $arreglodias = [];
        $arreglodias['Lu'] = 'Lunes';
        $arreglodias['Ma'] = 'Martes';
        $arreglodias['Mi'] = 'Miercoles';
        $arreglodias['Ju'] = 'Jueves';
        $arreglodias['Vi'] = 'Viernes';
        $arreglodias['Sa'] = 'Sabado';
        $arreglodias['Do'] = 'Domingo';

        return $arreglodias;
    }

    static function ObtenerDiasSemanaNumerico() {
        $arreglodias = [];
        $arreglodias[1] = 'Lunes';
        $arreglodias[2] = 'Martes';
        $arreglodias[3] = 'Miercoles';
        $arreglodias[4] = 'Jueves';
        $arreglodias[5] = 'Viernes';
        $arreglodias[6] = 'Sabado';
        $arreglodias[7] = 'Domingo';

        return $arreglodias;
    }

    static public function obtenerNombreDiaSemana(int $dia): ?string {
        switch ($dia % 7) {
            case 0:
                return 'Domingo';
            case 1:
                return 'Lunes';
            case 2:
                return 'Martes';
            case 3:
                return 'Miércoles';
            case 4:
                return 'Jueves';
            case 5:
                return 'Viernes';
            case 6:
                return 'Sábado';
        }
        return null;
    }

    static function ReemplazarDiasSemanaBase($texto) {
        // reemplazo dias cortos
        $texto = str_replace("Monday", "Lu", $texto);
        $texto = str_replace("Tuesday", "Ma", $texto);
        $texto = str_replace("Wednesday", "Mi", $texto);
        $texto = str_replace("Thursday", "Ju", $texto);
        $texto = str_replace("Friday", "Vi", $texto);
        $texto = str_replace("Saturday", "Sa", $texto);
        $texto = str_replace("Sunday", "Do", $texto);

        // reemplazo dias cortos
        $texto = str_replace("Mon", "Lu", $texto);
        $texto = str_replace("Tue", "Ma", $texto);
        $texto = str_replace("Wed", "Mi", $texto);
        $texto = str_replace("Thu", "Ju", $texto);
        $texto = str_replace("Fri", "Vi", $texto);
        $texto = str_replace("Sat", "Sa", $texto);
        $texto = str_replace("Sun", "Do", $texto);

        return $texto;
    }

    static function ObtenerEdad($fecha_nacimiento) {
        [$y, $m, $d] = explode("-", $fecha_nacimiento);
        $y_dif = date("Y") - $y;
        $m_dif = date("m") - $m;
        $d_dif = date("d") - $d;
        if ((($d_dif < 0) && ($m_dif == 0)) || ($m_dif < 0))
            $y_dif--;
        return $y_dif;
    }

    static function RenderFile($template_file, $vars = []) {
        if (file_exists($template_file)) {
            ob_start();
            extract($vars);
            include($template_file);
            return ob_get_clean();
        }
    }

    static function EncriptarFrase($string, $key) {
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }
        return base64_encode($result);
    }

    static function DesencriptarFrase($string, $key) {
        $result = '';
        $string = base64_decode($string);
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }
        return $result;
    }

    static function js_array($arreglo) {
        $temp = [];
        foreach ($arreglo as $fila) {
            $temp[] = FuncionesPHPLocal::js_str($fila);
        }
        return '[' . implode(',', $temp) . ']';
    }

    static function js_str($s) {
        return '"' . addcslashes($s, "\0..\37\"\\") . '"';
    }

    static function js_query($conexion, $resultado, $clave) {
        $temp = [];
        while ($fila = $conexion->ObtenerSiguienteRegistro($resultado)) {
            $temp[] = FuncionesPHPLocal::js_str($fila[$clave]);
        }
        return '[' . implode(',', $temp) . ']';
    }

    static function GuardarArchivo($path, $data, $archivo) {
        if (file_exists($path . $archivo)) {
            unlink($path . $archivo);
        }

        if (!$file = fopen($path . $archivo, "w"))
            return false;

        $data = trim($data);
        $fwrite = 0;
        for ($written = 0; $written < strlen($data); $written += $fwrite) {
            $fwrite = fwrite($file, substr($data, $written));
            if ($fwrite === false)
                echo "error";
            if ($fwrite == 0 || $fwrite === false) {
                break;
            }
        }

        fclose($file);

        return true;
    }

    static function cortar_string($string, $largo) {
        $marca = "<!--corte-->";

        if (strlen($string) > $largo) {

            $string = wordwrap($string, $largo, $marca);
            $string = explode($marca, $string);
            $string = $string[0] . "...";
        }
        return $string;

    }

    static function ArmarPaginado($cantidadpaginacion, $element_count, $page, &$primera, &$ultima, &$numpages, &$current, &$TotalSiguiente, &$TotalVer) {

        $cant = $cantidadpaginacion;
        if ($element_count > $cantidadpaginacion)
            $TotalSiguiente = ($page * $cantidadpaginacion);
        else
            $TotalSiguiente = $element_count;

        if ($TotalSiguiente > $cantidadpaginacion)
            $TotalVer = $TotalSiguiente - $cantidadpaginacion;
        elseif ($element_count == 0)
            $TotalVer = 0;
        else
            $TotalVer = 1;

        $current = ($page - 1) * $cant;

        // Paginacion
        if ($current + $cant <= $element_count)
            $next_page = $page + 1;
        else
            $next_page = $page;

        if ($current - $cant >= 0)
            $prev_page = $page - 1;
        else
            $prev_page = $page;

        $mostrar = 8;
        $primera = $page - abs($mostrar / 2);
        $ultima = $page + abs($mostrar / 2);
        $numpages = ceil($element_count / $cant);


        if ($numpages < 1)
            $numpages = 1;

        if ($primera < 1) {
            $primera = 1;
            if ($numpages > $mostrar)
                $ultima = $mostrar;
            else
                $ultima = $numpages;
        }

        if ($ultima > $numpages) {
            $ultima = $numpages;
            $primera = $ultima - $mostrar;
            if ($primera < 1)
                $primera = 1;
        }

        return true;
    }

    static function ObtenerSistemaOperativo() {
        $sistemaoperativo = $_SERVER["HTTP_USER_AGENT"];
        if (strstr($sistemaoperativo, 'Win')) {
            $sistemaoperativo = 'Windows';
        } elseif (strstr($sistemaoperativo, 'Mac')) {
            $sistemaoperativo = 'Mac OS';
        } elseif (strstr($sistemaoperativo, 'Linux')) {
            $sistemaoperativo = 'Linux';
        } elseif (strstr($sistemaoperativo, 'Unix')) {
            $sistemaoperativo = 'Unix';
        } else {
            $sistemaoperativo = 'Otro';
        }
        return $sistemaoperativo;

    }


    static function ConvertirFormatoCuit($cuit) {
        $total = strlen($cuit);
        $cuit = substr($cuit, 0, 2) . "-" . substr($cuit, 2, ($total - 3)) . "-" . substr($cuit, ($total - 1));
        return $cuit;

    }


    static function ArmarPaginadoDatos($cantidadpaginacion, $element_count, $page, &$primera, &$ultima, &$numpages, &$current, &$TotalSiguiente, &$TotalVer) {

        $cant = $cantidadpaginacion;
        if ($element_count > $cantidadpaginacion)
            $TotalSiguiente = ($page * $cantidadpaginacion);
        else
            $TotalSiguiente = $element_count;

        if ($TotalSiguiente > $cantidadpaginacion)
            $TotalVer = $TotalSiguiente - $cantidadpaginacion;
        elseif ($element_count == 0)
            $TotalVer = 0;
        else
            $TotalVer = 1;

        $current = ($page - 1) * $cant;

        // Paginacion
        if ($current + $cant <= $element_count)
            $next_page = $page + 1;
        else
            $next_page = $page;

        if ($current - $cant >= 0)
            $prev_page = $page - 1;
        else
            $prev_page = $page;

        $mostrar = 8;
        $primera = $page - abs($mostrar / 2);
        $ultima = $page + abs($mostrar / 2);
        $numpages = ceil($element_count / $cant);


        if ($numpages < 1)
            $numpages = 1;

        if ($primera < 1) {
            $primera = 1;
            if ($numpages > $mostrar)
                $ultima = $mostrar;
            else
                $ultima = $numpages;
        }

        if ($ultima > $numpages) {
            $ultima = $numpages;
            $primera = $ultima - $mostrar;
            if ($primera < 1)
                $primera = 1;
        }

        return true;
    }

    static function CargarAreas($areanombre, $Arbol, $nivel, $arregloAreasSeleccionadas) {
        $areanombreorig = $areanombresincodigo = $areanombre;
        foreach ($Arbol as $fila) {
            $areanombre = $areanombre;

            $AreaNombre2 = ucwords(mb_strtolower(trim($fila['Nombre'])));

            ?>
            <option data-id="<?php echo $fila['IdArea'] ?>" <?php echo in_array($fila['IdArea'], $arregloAreasSeleccionadas) ? "selected" : ""; ?>
                    value="<?php echo $fila['IdArea'] ?>"><?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($areanombre, ENT_QUOTES) . $nivel . FuncionesPHPLocal::HtmlspecialcharsSistema($AreaNombre2, ENT_QUOTES) ?></option>
            <?php
            if (isset($fila['SubArbol']) && count($fila['SubArbol']) > 0) {
                $areanombre = $areanombresincodigo . html_entity_decode(" &raquo;&raquo; ") . $AreaNombre2;
                CargarAreas($areanombre, $fila['SubArbol'], $nivel, $arregloAreasSeleccionadas);
                $areanombre = $areanombreorig;
            }
            $areanombre = $areanombreorig;
        }
    }


    static function getBrowserSo($u_agent) {
        //$u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/Trident/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "Trident";
        }
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Edge/i', $u_agent)) {
            $bname = 'Edge';
            $ub = "Edge";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = ['Version', $ub, 'other'];
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return [
            //'userAgent' => $u_agent,
            'name' => ucwords($bname),
            'version' => $version,
            'platform' => $platform,
            //'pattern'    => $pattern
        ];
    }

    static function CantidadArchivosCarpeta($dir) {
        return (count(scandir($dir)) - 2);
    }

    static public function IncluirJSConHash($file_js) {
        $hash = md5_file(DIR_ROOT . $file_js);
        print <<<JS
		<script type="text/javascript" src="{$file_js}?hash=$hash"></script>

JS;
//<?php
    }

    static public function IncluirCSSConHash($file_css) {
        $hash = md5_file(DIR_ROOT . $file_css);
        print <<<CSS
		<link href="{$file_css}?hash=$hash" rel="stylesheet" media="all" />

CSS;
    }


    static function validarNumerico($campo, $maxval) {
        //print_r($datos);
        if ((trim($campo) != "")) {
            if (!preg_match("/^-?[1-9][0-9]*$|^0$/", $campo))
                return false;

            if (intval($campo) > $maxval)
                return false;


        } else
            $campo = null;

        return true;
    }


    static function ParsearMetaPuesto($metaPuesto) {
        //print_r($datos);
        $arrayMetaPuesto = [];
        $arrayMetaPuesto['Escalafon'] = substr($metaPuesto, 0, 1);
        $arrayMetaPuesto['Cargo'] = substr($metaPuesto, 1, 4);
        $arrayMetaPuesto['Inciso'] = substr($metaPuesto, 5, 1);
        $arrayMetaPuesto['Item'] = substr($metaPuesto, 6, 2);
        $arrayMetaPuesto['Dependencia'] = substr($metaPuesto, 8, 1);
        $arrayMetaPuesto['CDependencia'] = substr($metaPuesto, 9, 1);
        $arrayMetaPuesto['Region'] = substr($metaPuesto, 10, 2);
        $arrayMetaPuesto['Distrito'] = substr($metaPuesto, 12, 3);
        $arrayMetaPuesto['Rama'] = substr($metaPuesto, 15, 1);
        $arrayMetaPuesto['Tipo'] = substr($metaPuesto, 16, 2);
        $arrayMetaPuesto['Escuela'] = substr($metaPuesto, 18, 4);
        $arrayMetaPuesto['Equipo'] = substr($metaPuesto, 22, 1);
        $arrayMetaPuesto['Personal'] = substr($metaPuesto, 23, 1);
        $arrayMetaPuesto['Turno'] = substr($metaPuesto, 24, 1);
        $arrayMetaPuesto['Orientacion'] = substr($metaPuesto, 25, 10);
        $arrayMetaPuesto['Curso'] = substr($metaPuesto, 35, 8);
        $arrayMetaPuesto['Division'] = substr($metaPuesto, 43, 3);
        $arrayMetaPuesto['IdPuesto'] = substr($metaPuesto, 46, 9);

        $arrayMetaPuesto['ClaveEscuela'] = $arrayMetaPuesto['CDependencia'] . $arrayMetaPuesto['Distrito'] . $arrayMetaPuesto['Tipo'] . $arrayMetaPuesto['Escuela'];

        return $arrayMetaPuesto;
    }


    static function ParsearClaveEscuela($claveEscuela) {
        //print_r($datos);
        $arrayClaveEscuela = [];
        $arrayClaveEscuela['CDependencia'] = substr($claveEscuela, 0, 1);
        $arrayClaveEscuela['Distrito'] = substr($claveEscuela, 1, 3);
        $arrayClaveEscuela['Tipo'] = substr($claveEscuela, 4, 2);
        $arrayClaveEscuela['Escuela'] = substr($claveEscuela, 6, 4);


        return $arrayClaveEscuela;
    }


    static function array_diff_assoc_recursive($array1, $array2) {
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key])) {
                    $difference[$key] = $value;
                } elseif (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = FuncionesPHPLocal::array_diff_assoc_recursive($value, $array2[$key]);
                    if ($new_diff != false) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!isset($array2[$key]) || $array2[$key] != $value) {
                $difference[$key] = $value;
            }
        }
        return !isset($difference) ? [] : $difference;
    }


    static function DiffInDays($start_date, $end_date) {
        if (is_string($start_date))
            $start_date = strtotime($start_date);
        if (is_string($end_date))
            $end_date = strtotime($end_date);

        $days = ($end_date - $start_date) / 60 / 60 / 24;

        return floor($days);
    }

    /**
     * Recibe el tiempo en Timestamp y lo devuelve como d d�as h horas m minuts s segundos
     * la segunda variable seteada en true, y en combinaci�n con la funcion Reloj de js
     * permite incrmentar automaticamente cada un segundo el tiempo.
     */
    static function FormatearTimestamp($tiempo, $incrementador = false) {
        $increment = "";
        $bloq = "";
        if ($incrementador) {
            $increment = "increment";
            $bloq = "<br/>(Actualmente bloqueado)";
        }
        $tiempoFormateado = "";
        $sec = $tiempo % 60;
        $tiempo -= $sec;
        $tiempo /= 60;
        $min = $tiempo % 60;
        $tiempo -= $min;
        $tiempo /= 60;
        $hora = $tiempo % 24;
        $tiempo -= $hora;
        $tiempo /= 24;
        if ($tiempo > 0)
            $tiempoFormateado = "<span id='mostrardias'><span class='day$increment'>$tiempo</span> d&iacute;as</span>";
        else
            $tiempoFormateado = "<span id='mostrardias' class='hidden'><span class='day$increment'>$tiempo</span> d&iacute;as</span>";
        if ($hora > 0)
            $tiempoFormateado .= "<span id='mostrarhoras'> <span class='hour$increment'>$hora</span> horas</span>";
        else
            $tiempoFormateado .= "<span id='mostrarhoras' class='hidden'> <span class='hour$increment'>$hora</span> horas</span>";
        /*if($min > 0)
            $tiempoFormateado .= "<span id='mostrarminutos'> <span class='min$increment'>$min</span> minutos</span>";
        else
            $tiempoFormateado .= "<span id='mostrarminutos' class='hidden'> <span class='min$increment'>$min</span> minutos</span>";
        if($tiempoFormateado === "")
            $tiempoFormateado = "<span class='sec$increment'>$sec</span> segundos$bloq";
        elseif($sec > 0)
            $tiempoFormateado .= " <span class='sec$increment'>$sec</span> segundos$bloq";
            */
        return $tiempoFormateado;
    }


    static function calcularDiferenciaHoras($horaInicio, $horaFin) {
        try {
            $desde = new DateTime($horaInicio);
            $hasta = new DateTime($horaFin);
        } catch (Exception $e) {
            throw new ExcepcionLogica('Error al procesar las fechas', $e->getCode(), $e);
        }

        $diff = $hasta->diff($desde);

        $horas = $diff->h + ($diff->i / 60);

        return $horas;
    }

    static function calcularDiferenciaModulos($horaInicio, $horaFin) {
        try {
            $desde = new DateTime($horaInicio);
            $hasta = new DateTime($horaFin);
        } catch (Exception $e) {
            throw new ExcepcionLogica('Error al procesar las fechas', $e->getCode(), $e);
        }

        $diff = $hasta->diff($desde);

        $minutosTotales = $diff->h * 60 + $diff->i;

        $intervalos = [];

        while ($minutosTotales >= 40) {
            $intervalos[] = 40;
            $minutosTotales -= 40;
        }

        if ($minutosTotales > 0) {
            $intervalos[] = $minutosTotales;
        }

        $modulos = count($intervalos);


        return $modulos;
    }

    static function ObtenerSexoxCuil($Cuil) {

        $rest = substr($Cuil, 0, 2);
        switch ($rest) {
            case 20:
                $AgenteSexo = "M";
                break;
            case 27:
                $AgenteSexo = "F";
                break;
            case 23: // 23, 24, 25 o 26 para ambos (en caso de que ya exista un CUIT id�ntico)
            case 24:
            case 25:
            case 26;
                $AgenteSexo = "";
                break;
            case 30; //30 para empresas
                $AgenteSexo = "";
                break;
            default:
                $AgenteSexo = "";
                break;
        }


        return $AgenteSexo;
    }

    static function _obtenerNesimoDiaHabil($fechaInicio, $N, $diasFeriados = [], $getTimestamp = false) {
        // Convirtiendo en timestamp las fechas
        if (is_numeric($fechaInicio))
            $miDia = $fechaInicio;
        else
            $miDia = strtotime($fechaInicio);
        // Incremento en 1 dia
        $diaInc = 24 * 60 * 60;
        $ii = 1;
        while ($ii <= $N) {
            $miDia += $diaInc;
            // Si el dia indicado, no es sabado o domingo es habil
            if (in_array(date('N', $miDia), [6, 7]) || in_array(date('Y-m-d', $miDia), $diasFeriados))
                continue;
            $ii++;

        }
        return $getTimestamp ? $miDia : date("Y-m-d H:i:s", $miDia);
    }


    public static function obtenerDiasAnteriores($diasAnteriores, $diasFeriados = [], $getTimestamp = false) {

        $hoy = strtotime("today");

        $resultados = [];
        foreach ($diasAnteriores as $diasAtras) {
            $miDia = $hoy - ($diasAtras * 24 * 60 * 60); // Restar días


            while (in_array(date('N', $miDia), [6, 7]) || in_array(date('Y-m-d', $miDia), $diasFeriados)) {
                $miDia -= 24 * 60 * 60; // Restar 1 día
            }

            $resultados[] = $getTimestamp ? $miDia : date("Y-m-d H:i:s", $miDia);
        }

        return $resultados;
    }


    static function _verificarFeriado($hoy, $diasFeriados = []) {
        //$hora = intval(date("H"));
        //$verificaHora = ($hora < $horaInicio || $hora >= $horaFin);
        $verificarDia = (in_array(date('N', $hoy), [6, 7]) || in_array(date('Y-m-d', $hoy), $diasFeriados));
        return $verificarDia;
    }

    /**
     * Traspone el array
     *
     * Si el array no tiene estructura de matriz agrega campos vacíos para compensar
     *
     * @param array $matrix
     *
     * @return array
     */
    static function transpose(array $matrix) {
        return self::array_map_join_array(function ($key, $items) {
            return $items;
        }, $matrix);
    }

    static function array_map_join_array(callable $callback, array $arrays) {
        $keys = [];
        // try to list all intern keys
        array_walk($arrays, function ($array) use (&$keys) {
            $keys = array_merge($keys, array_keys($array));
        });
        $keys = array_unique($keys);
        $res = [];
        // for each intern key
        foreach ($keys as $key) {
            $items = [];
            // walk through each array
            array_walk($arrays, function ($array, $arrKey) use ($key, &$items) {
                if (isset($array[$key])) {
                    // stack/transpose existing value for intern key with the array (extern) key
                    $items[$arrKey] = $array[$key];
                } else {
                    // or stack a null value with the array (extern) key
                    $items[$arrKey] = null;
                }
            });
            // call the callback with intern key and all the associated values keyed with array (extern) keys
            $res[$key] = call_user_func($callback, $key, $items);
        }
        return $res;
    }

    static function print_pre($value, $die = true): void {
        print '<pre>';
        print_r($value);
        print '</pre>';
        if ($die)
            die;
    }


    /**
     * Evalua si la variable está vacía
     *
     * @param mixed $var
     *
     * @return bool
     */
    static function isEmpty(&$var): bool {
        // isset devuelve FALSE si $var === NULL
        // el método ignora FALSE, 0, 0.0 y "0" los cuales devuelven TRUE en el caso de empty($var)
        if (!isset($var) || '' === $var || 'NULL' === $var || (is_countable($var) && 0 === count($var)))
            return true;
        return false;
    }


    static function dateDifference($startDate, $endDate) {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate) {
            return false;
        }
        $years = date('Y', $endDate) - date('Y', $startDate);

        $endMonth = date('m', $endDate);
        $startMonth = date('m', $startDate);

        // Calculate months
        $months = $endMonth - $startMonth;
        if ($months <= 0) {
            $months += 12;
            $years--;
        }
        if ($years < 0)
            return false;

        // Calculate the days
        $offsets = [];
        if ($years > 0)
            $offsets[] = $years . (($years == 1) ? ' year' : ' years');
        if ($months > 0)
            $offsets[] = $months . (($months == 1) ? ' month' : ' months');
        $offsets = count($offsets) > 0 ? '+' . implode(' ', $offsets) : 'now';

        $days = $endDate - strtotime($offsets, $startDate);
        $days = date('z', $days);
        return [$years, $months, $days];
    }

    function ConvertirFechaAntiguedad($stringFecha) {
        if ($stringFecha == "000000")
            return "";
        else {
            $dia = substr($stringFecha, 0, 2);
            $mes = substr($stringFecha, 2, 2);
            $anio = substr($stringFecha, 4, 2);
            if ($anio > 30)
                $anio = "19" . $anio;
            else
                $anio = "20" . $anio;
            return $anio . "-" . $mes . "-" . $dia;
        }

    }

    /**
     * Compara si una fecha está entre otras dos fechas
     *
     *
     * Las fechas deben estar en formato canónico o ISO (pueden incluir información de tiempo).
     * Genera un excepción si las fechas están en un formato incorrecto o si el parámetro opcional $strict tiene un valor incorrecto.
     *
     * @param string   $date_str      Fecha que va a ser comparada si cae ente $startDate y $endDate
     * @param string   $startDate_str Si la fecha es posterior a esta devuelve verdadero
     * @param string   $endDate_str   Si la fecha es anterior a esta devuelve verdadero
     * @param int|null $strict        (Opcional) Decide si el rango es abierto, cerrado, semiabierto a la derecha o semiabierto a la izquierda (null, 0, 1, -1)
     *
     * @return bool
     * @throws Exception
     */
    static function isDateStringBetweenDates(string $date_str, string $startDate_str, string $endDate_str, ?int $strict = PHP_INT_MAX): bool {
        $date = new DateTime ($date_str);
        $startDate = new DateTime ($startDate_str);
        $endDate = new DateTime ($endDate_str ?: 'last day of this year');
        return self::isDateBetweenDates($date, $startDate, $endDate, $strict);

    }

    /**
     * Compara si una fecha está entre otras dos fechas
     *
     * Las recibe objetos DateTime
     *
     * @param DateTimeInterface      $date
     * @param DateTimeInterface      $startDate
     * @param DateTimeInterface|null $endDate
     * @param int|null               $strict
     *
     * @return bool
     */
    static function isDateBetweenDates(DateTimeInterface $date, DateTimeInterface $startDate, ?DateTimeInterface $endDate, ?int $strict = PHP_INT_MAX): bool {
        if (empty($endDate))
            $endDate = new DateTime('+100 years');
        switch ($strict) {
            case 1:
                return $date > $startDate && $date <= $endDate;
            case -1:
                return $date >= $startDate && $date < $endDate;
            case 0:
                return $date >= $startDate && $date <= $endDate;
            default:
                return $date > $startDate && $date < $endDate;
        }

    }

    public static function areDateRangesOverlapping($range, ...$ranges): bool {
        $return = false;
        foreach ($ranges as $tmp) {
            $return = $return || ($range->gte < $tmp->lte && $range->lte > $tmp->gte);
            /*$return = $return || self::isDateBetweenDates($range->gte, $tmp->gte, $tmp->lte, -1);
            $return = $return || self::isDateBetweenDates($range->lte, $tmp->gte, $tmp->lte, 1);
            $return = $return || self::isDateBetweenDates($tmp->gte, $range->gte, $range->lte, -1);
            $return = $return || self::isDateBetweenDates($tmp->lte, $range->gte, $range->lte, 1);*/
        }

        return $return;
    }

    public static function VerificarBloqueo($conexion, $MostrarMensaje = true): bool {
        switch (array_key_first($_SESSION['rolcod'])) {
            # Director
            case ROL_EQUIPO_CONDUCCION:

                if (is_array($_SESSION['IdEscuela']))
                    $datos['IdEscuela'] = implode(",", $_SESSION['IdEscuela']);
                else
                    $datos['IdEscuela'] = (int)$_SESSION['IdEscuela'];

                $oPofa = new cEscuelasPOF($conexion);
                if (!$oPofa->BuscarPOFAEditable($datos, $resultado, $numfilas))
                    return false;

                if ($numfilas > 0) {

                    $r = $conexion->ObtenerSiguienteRegistro($resultado);

                    if ($r['PofaEditable'] == 1)
                        $_SESSION['Bloqueo'] = true;
                    # Bloquea módulos
                    else
                        $_SESSION['Bloqueo'] = false;
                }

                if (isset($_SESSION['Bloqueo']) && $_SESSION['Bloqueo']) {

                    if ($MostrarMensaje) {
                        echo
                            '<div class="alert alert-info" role="alert">
                            Para continuar, debe aprobar la ' . PLANTA_ANALITICA_ALIAS . '.
                        </div>
                        <div class="text-center">
                            <a class="btn btn-secondary" href="/mi-escuela#declaracionJurada">Ir a Declaraci&oacute;n Jurada</a>
                        </div>';
                    }
                    return false;
                }
                break;
        }
        return true;
    }

    /**
     * @param int $estado
     *
     * @return string
     * @throws Bigtree\ExcepcionBase
     */
    public static function getEstado(int $estado, string $tabla): string {

        if ($tabla == 'Usuarios') {
            switch ($estado) {
                case 0:
                    return 'Nuevo';
                case 30:
                    return 'Activo';
                default:
                    throw new Bigtree\ExcepcionBase('Estado inválido');
            }
        } else {
            switch ($estado) {
                case ACTIVO:
                    return 'Activo';
                case NOACTIVO:
                    return 'No activo';
                case ELIMINADO:
                    return 'Eliminado';
                default:
                    throw new Bigtree\ExcepcionBase('Estado inválido');
            }
        }
    }


    static function redireccionarSegunIdTipo($idTipo, $IdLicencia) {

        $url = '';
        switch ($idTipo) {
            case 1:
                FuncionesPHPLocal::ArmarLinkMD5('lic_licencias_administracion_am.php', ['IdLicencia' => $IdLicencia], $get, $md5);
                $url = "/licencias/medicas/revision/" . $IdLicencia . "/" . $md5;
                break;
            case 2:
                FuncionesPHPLocal::ArmarLinkMD5('lic_licencias_administracion_administrativas_am.php', ['IdLicencia' => $IdLicencia], $get, $md5);
                $url = "/licencias/administrativas/revision/" . $IdLicencia . "/" . $md5;
                break;
            case 3:
                FuncionesPHPLocal::ArmarLinkMD5('lic_licencias_administracion_art_am.php', ['IdLicencia' => $IdLicencia], $get, $md5);
                $url = "/licencias/art/revision/" . $IdLicencia . "/" . $md5;
                break;
            case 4:
                FuncionesPHPLocal::ArmarLinkMD5('lic_licencias_administracion_mat_am.php', ['IdLicencia' => $IdLicencia], $get, $md5);
                $url = "/licencias/maternidad/revision/" . $IdLicencia . "/" . $md5;
                break;
            case 5:
                FuncionesPHPLocal::ArmarLinkMD5('lic_licencias_administracion_inasistencias_am.php', ['IdLicencia' => $IdLicencia], $get, $md5);
                $url = "/licencias/inasistencias/revision/" . $IdLicencia . "/" . $md5;
                break;
        }
        return $url;
    }

    /**
     * @param array $datos
     *
     * @return string
     */
    static function armarCuerpoDesignado(array $datos): string {

        switch ($datos['modo'] ?? -1) {
            case 1: // persona a designar
                $puestoDesempeno = explode('_', $datos['id']);
                $idPersona = $datos['persona']['id'] ?? '';
                $nombrePersona = $datos['persona']['nombre'] ?? '';
                $documento = $datos['persona']['documento'] ?? '';

                $nombre = $style = $disabledExists = '';
                if ($nombrePersona != '') {
                    $nombre = $nombrePersona . ' - ' . $documento;
//                    $style = 'font-weight: 600;';
                    $disabledExists = 'disabled';
                }

                $disabled = $datos['tienePermisosDesignar'] ? '' : 'disabled="disabled" ';

                $classChild = $classChildInsertar = $classChildEliminar = '';
                if (isset($datos['puesto']) && isset($datos['desempeno'])) {
                    $classChild = 'child_' . $datos['puesto'];
                    $classChildInsertar = 'child_insertar_' . $datos['puesto'];
                    $classChildEliminar = 'child_eliminar_' . $datos['puesto'];
                }

                $cuerpo = "<style>
                            input::placeholder {
                              color: #909090!important;
                              font-size: 14px;
                              font-style: italic;
                            }
                            </style>";
                $cuerpo .= "<div class='row'>";
                $cuerpo .= "<div class='col-md-10'>";


                $claseGenerica = $datos['id'] != 'todos' ? 'DniPersonaInscripta' : '';

                $cuerpo .= "<input type='text' class='form-control input-md {$claseGenerica} {$classChild}' name='DniPersonaInscripta[{$datos['name']}][valor]' "
                    . "data-id='{$datos['id']}' id='DniPersonaInscripta_{$datos['id']}' value='{$nombre}' autocomplete='off'{$disabled} {$disabledExists} style='{$style}' />"
                    . PHP_EOL;


                $cuerpo .= "</div>";
                $cuerpo .= "<input type='hidden' name='IdPersonaDesignada[{$datos['name']}][valor]' "
                    . "id='IdPersonaDesignada_{$datos['id']}' value='{$idPersona}'/>"
                    . PHP_EOL;

                $btn = 'btn-secondary';
                $title = 'Edicion deshabilitada';
                $icon = 'far fa-edit';
                $id = $puesto = $desempeno = '';
                $total = $datos['totalPuestos'] ?? 0;
                $cuerpoDesignar = '';
                $cuerpoEliminar = '';
                if ($datos['tienePermisosDesignar']) {

                    if (isset($datos['puesto']) && isset($datos['desempeno'])) {
                        $puesto = $datos['puesto'];
                        $desempeno = $datos['desempeno'];
                    } else {
                        $puesto = $datos['id'];
                    }

                    $classInsertar = 'hide';
                    $classEliminar = '';
                    if ($nombrePersona == "") {
                        $classInsertar = '';
                        $classEliminar = 'hide';
                    }

                    $cuerpoDesignar = "<div class='col-md-1 btnInsertar insertar_{$datos['id']} {$classInsertar} {$classChildInsertar}'>
                            <a  href=\"javascript:void(0);\"
                            class=\"btn btn-sm btn-success btn-insertar\"
                                id=\"btnInsertar_{$datos['id']}\"
                                data-puesto=\"{$puesto}\"
                                data-desempeno=\"{$desempeno}\"
                                data-total=\"{$total}\"
                                title=\"Designar\">
                            <i class=\"fas fa-plus\" aria-hidden=\"true\"></i></a>
                        </div>";


                    $cuerpoEliminar = "<div class='col-md-1 btnEliminar eliminar_{$datos['id']} {$classEliminar} {$classChildEliminar}'>
                                <a  href=\"javascript:void(0);\"
                                class=\"btn btn-sm btn-danger btn-eliminar\"
                                    id=\"btnEliminar_{$datos['id']}\"
                                    data-puesto=\"{$puesto}\"
                                    data-desempeno=\"{$desempeno}\"
                                    data-total=\"{$total}\"
                                    title=\"Eliminar\">
                                <i class=\"fas fa-trash\" aria-hidden=\"true\"></i></a>
                            </div>";
                }

                $cuerpo .= $cuerpoDesignar . $cuerpoEliminar;

                $classResolucion = empty($datos['persona']['resolucion']) ? 'hide' : '';
                $cuerpo .= "<div style='text-align: left !important;' class='col-md-12 $classResolucion bloqueResolucionPersona_{$datos['persona']['id']}'>";
                $cuerpo .= '<div class="clearboth">&nbsp;</div>' . PHP_EOL;
                $cuerpo .= "<output>N&uacute;mero de resoluci&oacute;n: <span class='text-muted ResolucionPersona_{$datos['persona']['id']}' id='ResolucionPersona_{$datos['id']}'>{$datos['persona']['resolucion']}</span></output>" . PHP_EOL;
                $cuerpo .= '</div>';

                $cuerpo .= "</div>";

                $cuerpo .= '<div class=\'col-md-12\'>' . PHP_EOL;
                $cuerpo .= "<div id='datosInscripto_{$datos['id']}'></div>" . PHP_EOL;
                $cuerpo .= '</div>' . PHP_EOL;
                break;
            case 2: // mostrar persona designada
                if (empty($datos['tienePermisosEliminarInscripto']))
                    $datos['tienePermisosEliminarInscripto'] = false;
                //$cuerpo = $datos['personaDesignada'];
                $cuerpo = '<div class=\'row\'>' . PHP_EOL;
                $cuerpo .= '<div class=\'col-md-12\'>' . PHP_EOL;
                $cuerpo .= '<table class=\'table table-stripped table-hover\'>' . PHP_EOL;
                $cuerpo .= '<thead>' . PHP_EOL;
                $cuerpo .= '<tr>' . PHP_EOL;
                $cuerpo .= '<th>Persona</th>' . PHP_EOL;
                $cuerpo .= '<th>Estado</th>' . PHP_EOL;
                $cuerpo .= '<th class="'
                    . ($datos['tienePermisosEliminarInscripto'] ? '' : 'hide')
                    . '" >&nbsp;</th>' . PHP_EOL;
                $cuerpo .= '</tr>' . PHP_EOL;
                $cuerpo .= '</thead>' . PHP_EOL;
                $cuerpo .= '<tbody>' . PHP_EOL;
                $cuerpo .= '<tr>' . PHP_EOL;
                $cuerpo .= "<td class=\"nombreInscripto\">{$datos['persona']['nombre']}</td>" . PHP_EOL;
                $cuerpo .= "<td>{$datos['persona']['estado']}</td>" . PHP_EOL;
                $cuerpo .= '<td class="text-center'
                    . ($datos['tienePermisosEliminarInscripto'] ? '' : ' hide') . '" >';
                $cuerpo .= '<a href="javascript:void(0);"'
                    . 'class="btn btn-danger btn-sm btnEliminarInscripto"'
                    . "data-id='{$datos['persona']['id']}' data-el='{$datos['id']}' >" . PHP_EOL;
                $cuerpo .= '<i class="far fa-trash-alt" aria-hidden="true"></i>' . PHP_EOL;
                $cuerpo .= '</a>' . PHP_EOL;
                $cuerpo .= '</td>' . PHP_EOL;
                $cuerpo .= '</tr>' . PHP_EOL;
                $cuerpo .= '</tbody>' . PHP_EOL;
                $cuerpo .= '</table>' . PHP_EOL;
                $cuerpo .= '</div>' . PHP_EOL;
                $cuerpo .= '</div>' . PHP_EOL;
                $cuerpo .= '</thead>' . PHP_EOL;
                //$cuerpo .= '';
                break;
            case 3: // persona propuesta
            case 4: // mostrar persona propuesta
                $cuerpo = 'próximamente';
                break;
            default:
                $cuerpo = '';
        }
        return $cuerpo;
    }

    static function buscarxTipoAcceso(&$datos): bool {

        $datos = $datos ?? [];
        switch ($_SESSION['TipoAcceso']) {
            case 1:
                # POR REGION
                /*$tmp = [];
                foreach ($_SESSION['Regiones'] as $key => $r) {

                    foreach ($r['Niveles'] as $n) {
                        $tmp[$key]['Region'] = (int)$key;
                        $tmp[$key]['Nivel'] = (int)$n['IdNivel'];
                        $tmp[$key]['Turno'] = (int)$n['IdTurno'];
                    }
                }
                $datos['filtarxRegionxNivelxTurno'] = array_values($tmp);*/
                break;
            case 2:
                # POR ESCUELA
                $tmp = [];
                $datos['IdEscuela'] = (int)$_SESSION['IdEscuelaSeleccionada'];
                foreach ($_SESSION['Niveles'] as $key => $n) {

                    $tmp[$key]['Nivel'] = (int)$n['IdNivel'];
                    $tmp[$key]['Turno'] = (int)$n['IdTurno'];
                }
                $datos['filtroNivelTurno'] = array_values($tmp);
                break;
            case 3:
                # POR NIVEL
                $datos['IdNivel'] = (int)$_SESSION['Nivel']['Id'];
                if ($_SESSION['Nivel']['Escuelas'][0] != 0) {
                    $datos['IdsEscuela'] = array_map('intval', $_SESSION['Nivel']['Escuelas']);
                }
                break;
            default:
                break;
        }

        return true;
    }


    static function DevolverIdEscuela($IdEscuela, $Elastic = false) {

        if (is_array($IdEscuela)) {
            if ($Elastic)
                $DevolverIdEscuela = $IdEscuela;
            else
                $DevolverIdEscuela = implode(",", $IdEscuela);
        } else
            $DevolverIdEscuela = (int)$IdEscuela;

        return $DevolverIdEscuela;

    }

    static function EsLoginMultipleEscuela() {
        $MultipleEscuelas = false;
        if (isset($_SESSION["IdEscuela"]) && is_array($_SESSION["IdEscuela"]) && count($_SESSION["IdEscuela"]) > 1)
            $MultipleEscuelas = true;

        return $MultipleEscuelas;
    }

    /**
     * @param $IdEscuela
     *                  se agrega a la url
     * @param $IdPuesto
     *                 se agrega a la url
     * @param $Texto
     *              aparece como enlace en pantalla
     *
     * @return string   retorna un elemento Anchor que redirecciona al puesto en la PON
     */
    static function EnlacePuestoPofa($IdEscuela, $IdPuesto, $Texto): string {
        return <<<LINK
                <a href="/establecimientos/$IdEscuela/pofa/puesto/$IdPuesto" target="_blank">
                    $Texto
                </a>
            LINK;
    }

    /**
     * @param $IdEscuela
     *                  se agrega a la url
     * @param $Texto
     *              aparece como enlace en pantalla
     *
     * @return string   retorna un elemento Anchor que redirecciona a la PON de la escuela
     */
    static function EnlaceEscuelaPofa($IdEscuela, $Texto): string {
        return <<<LINK
                <a href="/establecimientos/$IdEscuela/pofa" target="_blank">
                    $Texto
                </a>
            LINK;
    }

    /**
     * @param $dni
     *              se agrega a la url
     * @param $Texto
     *              aparece como enlace en pantalla
     *
     * @return string   retorna un elemento Anchor que redirecciona al legajo
     */
    static function EnlaceLegajo($dni, $Texto): string {
        return <<<LINK
            <a href="/agentes/$dni" target="_blank">
                $Texto
            </a>
        LINK;
    }

    /**
     * @param $id
     *           el id de la novedad (IdDocumento)
     * @param $Texto
     *              opcional, si no se envia se muestra el id de novedad
     *
     * @return string retorna un elemento Anchor que direcciona a la novedad
     */
    static function EnlaceNovedad($id, $Texto = ""): string {
        self::ArmarLinkMD5('novedades_am.php', array('IdDocumento' => $id), $get, $md5);
        if ($Texto == "") $Texto = (string) $id;
        return <<<LINK
            <a href="/novedades/$id/$md5" target="_blank">
                $Texto
            </a>
        LINK;
    }

    /**
     * @param $id
     *           Id de Licencia
     * @param $Texto
     *           opcional, si no se envia se muestra el id
     *
     * @return string retorna un elemento Anchor que direcciona a la licencia
     */
    static function EnlaceLicencia($id, $Texto = ""){
        self::ArmarLinkMD5('lic_licencias_am.php', ["Id" => $id], $get, $md5);
        if ($Texto == "") $Texto = (string) $id;
        return <<<LINK
            <a href="/licencias/$id/$md5" target="_blank">
                $Texto
            </a>
        LINK;
    }

    /**
     * @param $id
     *           Id de Licencia
     * @param $Texto
     *           opcional, si no se envia se muestra el id
     *
     * @return string retorna un elemento Anchor que direcciona a la revision de licencia segun el tipo de licencia
     */
    static function EnlaceRevisionLicencia($tipo, $id, $Texto = ""){

        if ($Texto == "") $Texto = (string) $id;

        switch ($tipo) {

            case TIPO_MEDICA:
                self::ArmarLinkMD5('lic_licencias_administracion_am.php', ["Id" => $id], $get, $md5);
                return <<<LINK
                    <a href="/licencias/medicas/revision/$id/$md5" target="_blank">
                        $Texto
                    </a>
                LINK;

            case TIPO_ADMIN:
                self::ArmarLinkMD5('lic_licencias_administracion_administrativas_am.php', ["Id" => $id], $get, $md5);
                return <<<LINK
                    <a href="/licencias/administrativas/revision/$id/$md5" target="_blank">
                        $Texto
                    </a>
                LINK;

            case TIPO_MATERNIDAD:
                self::ArmarLinkMD5('lic_licencias_administracion_mat_am.php', ["Id" => $id], $get, $md5);
                return <<<LINK
                    <a href="/licencias/maternidad/revision/$id/$md5" target="_blank">
                        $Texto
                    </a>
                LINK;

            case TIPO_ART:
                self::ArmarLinkMD5('lic_licencias_administracion_art_am.php',  ["Id" => $id], $get, $md5);
                return <<<LINK
                    <a href="/licencias/art/revision/$id/$md5" target="_blank">
                        $Texto
                    </a>
                LINK;

            case TIPO_INASISTENCIAS:
                self::ArmarLinkMD5('lic_licencias_administracion_inasistencias_am.php', ["Id" => $id], $get, $md5);
                if ($Texto == "") $Texto = (string) $id;
                return <<<LINK
                    <a href="/licencias/inasistencias/revision/$id/$md5" target="_blank">
                        $Texto
                    </a>
                LINK;

            default: break;
        }
    }


    /**
     * Genera (y cachea) miniaturas de imágenes.
     * Para videos u otros formatos devuelve la ruta a una imagen default.
     *
     * @param string $file Nombre o ruta del archivo original
     * @param int $w Ancho deseado (default 200)
     * @param int $h Alto deseado (default 200)
     * @return array ['thumb' => rutaThumb, 'type' => 'image'|'video']
     */
    public static function generarThumb(string $file, int $w = 200, int $h = 200): array {
        $baseDir   = PATH_STORAGE . 'noticias/';           // carpeta con originales
        $cacheDir  = $baseDir . "thumbs/";          // carpeta para cache
        $default   = $baseDir . 'default.png'; // fallback

        // Seguridad básica
        $origName = basename($file);
        $origPath = $baseDir . $origName;

        // Detectar MIME
        $mime = self::mimeOf($origPath);
        $isImage = $mime && str_starts_with($mime, 'image/');
        $isVideo = $mime && str_starts_with($mime, 'video/');

        // Si no es imagen válida → uso default
        if (!$isImage && !$isVideo) {
            $origPath = $default;
            $mime = self::mimeOf($origPath) ?: 'image/png';
        }

        if ($isImage) {
            // Extensión de salida según MIME
            $extOut = match ($mime) {
                'image/jpeg' => 'jpeg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
                default      => 'png'
            };

            // Ruta cache
            if (!is_dir($cacheDir)) { @mkdir($cacheDir, 0775, true); }
            $baseNameNoExt = pathinfo($origName, PATHINFO_FILENAME);
            $cachePath = sprintf('%s/%s__%dx%d.%s', $cacheDir, $baseNameNoExt, $w, $h, $extOut);

            // Si ya existe y está actualizado, devuelvo directo
            if (is_file($cachePath) && filemtime($cachePath) >= filemtime($origPath)) {
                return [
                    'thumb' => $cachePath,
                    'type'  => 'image'
                ];
            }

            // Abrir con GD
            $src = self::openImage($origPath, $mime);
            if (!$src) {
                return [
                    'thumb' => $default,
                    'type'  => 'image'
                ];
            }

            // Crear destino
            $dst = imagecreatetruecolor($w, $h);
            if (in_array($mime, ['image/png','image/gif','image/webp'])) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $trans = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefilledrectangle($dst, 0, 0, $w, $h, $trans);
            }

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, imagesx($src), imagesy($src));

            // Guardar en cache
            self::saveImage($dst, $mime, $cachePath);

            imagedestroy($src);
            imagedestroy($dst);

            return [
                'thumb' => $cachePath,
                'type'  => 'image'
            ];
        }

        if($isVideo) {
            return [
                'thumb' => $origName,
                'type'  => 'video'
            ];
        }

        return [
            'thumb' => $default,
            'type'  => 'image'
        ];

    }

    /* Helpers */
     static function mimeOf(string $path): ?string {
        if (!is_file($path)) return null;
        if (function_exists('finfo_open')) {
            $f = finfo_open(FILEINFO_MIME_TYPE);
            $m = $f ? finfo_file($f, $path) : null;
            if ($f) finfo_close($f);
            return $m ?: null;
        }
        $i = @getimagesize($path);
        return $i['mime'] ?? null;
    }
     static function openImage(string $path, string $mime) {
        return match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png'  => imagecreatefrompng($path),
            'image/gif'  => imagecreatefromgif($path),
            'image/webp' => (function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : null),
            default      => null
        };
    }
    static function saveImage($im, string $mime, string $path) {
        return match ($mime) {
            'image/jpeg' => imagejpeg($im, $path, 90),
            'image/png'  => imagepng($im, $path),
            'image/gif'  => imagegif($im, $path),
            'image/webp' => (function_exists('imagewebp') ? imagewebp($im, $path, 90) : imagepng($im, $path)),
            default      => imagepng($im, $path)
        };
    }

    /**
     * @param string $valor
     *
     * @return string
     */
    static function sanitizarValorCSV(string $valor): string {
        // Reemplazar saltos de línea por espacio (evita cortar filas)
        $valor = str_replace(["\r", "\n"], " ", $valor);

        // Escapar comillas dobles internas -> "" (doble comilla)
        $valor = str_replace('"', '""', $valor);

        // Encerrar en comillas dobles
        return '"' . $valor . '"';
    }

    static function diasToYMD($totalDias, $formato="") {
        // Fecha base
        $fechaInicio = new DateTime();

        // Fecha final sumando la cantidad de días
        $fechaFin = (clone $fechaInicio)->add(new DateInterval("P{$totalDias}D"));

        // Diferencia
        $diferencia = $fechaInicio->diff($fechaFin);
        switch ($formato) {
            case "A-M-D":
                return $diferencia->y." a&ntilde;os - ".$diferencia->m." meses - ".$diferencia->d." d&iacute;as";
            case "a-m-d":
                return $diferencia->y."A - ".$diferencia->m."M - ".$diferencia->d."D";
            case "A":
                return $diferencia->y." a&ntilde;os";
            case "a":
                return $diferencia->y;
            case "M":
                return $diferencia->m." meses";
            case "m":
                return $diferencia->m;
            case "D":
                return $diferencia->d." d&iacute;as";
            case "d":
                return $diferencia->d;
            default:
                return $totalDias." d&iacute;as";
        }
    }





} // Fin clase FuncionesPHPLocal



//Para versiones de PHP anteriores a 7.3
if (!function_exists('is_countable')) {
    function is_countable($var) {
        return (is_array($var) || $var instanceof Countable);
    }
}

