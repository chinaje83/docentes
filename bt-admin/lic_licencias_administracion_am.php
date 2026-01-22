<?php
require("./config/include.php");
//require_once DIR_ROOT . 'config/include_elastic.php';
require(DIR_LIBRERIAS . 'CurlBigtree.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cServiciosLicencias.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cAutorizantes.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cArticulos.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cEspecialidadesAutorizantes.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cJuntasTipos.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cJuntasMotivos.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cServiciosPersonas.class.php');
require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cServiciosFamiliares.class.php');

$conexion = new accesoBDLocal(SERVIDORBD, USUARIOBD, CLAVEBD);
$conexion->SeleccionBD(BASEDATOS);
//$conexionES = new Elastic\Conexion();

FuncionesPHPLocal::CargarConstantes($conexion, ["roles" => "si", "sistema" => SISTEMA, "multimedia" => "si"]);
$conexion->SetearAdmiGeneral(ADMISITE);

$sesion = new Sesion($conexion, false);
$sesion->TienePermisos($conexion, $_SESSION['usuariocod'], $_SESSION['rolcod'], $_SERVER['PHP_SELF']);

$oSistemaBloqueo = new SistemaBloqueo();
$oSistemaBloqueo->VerificarBloqueo($conexion);

$oEncabezados = new cEncabezados($conexion);
$oEncabezados->CargarPreload();
$oEncabezados->setTitle("Revisi&oacute;n de Licencias M&eacute;dicas <small> - Id: " . $_GET['Id'] . "</small>");
$oEncabezados->addScript("/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.es.js?v=1.1");
$oEncabezados->addScript("/assets/lib/typehead/handlebars.js");
$oEncabezados->addScript("/assets/lib/typehead/typehead.js");
$oEncabezados->addScript("/modulos/lic_licencias_administracion/js/lic_licencias_administracion_am.js?v=2.2");
$oEncabezados->addScript("/assets/js/tiny_mce/tiny_mce.min.js");
$oEncabezados->addScript("/assets/lib/fine-uploader/fine-uploader.js");
$oEncabezados->addScript("/assets/lib/panzoom/js/jquery.mousewheel.js?v=1.1");
$oEncabezados->addScript("/assets/lib/panzoom/js/panzoom.js?v=1.1");
$oEncabezados->addScript("/modulos/lic_licencias/js/lic_licencias_certificado_adjunto.js?v=2.5");
$oEncabezados->addScript("/assets/lib/choosen/chosen.jquery.min.js");
$oEncabezados->addScript('/modulos/esc_escuelas_confeccionar/js/esc_escuelas_confeccion_puesto_destino_popup.js?v=1.3');
$oEncabezados->addCss("/assets/lib/choosen/chosen.css?v=1");
$oEncabezados->addCss("/assets/css/choosen-materialize.css");
$oEncabezados->addCss('/modulos/lic_licencias_administracion/css/lic_licencias_administracion_am.css');

$oEncabezados->EncabezadoMenuEmergente($_SESSION['rolcod'], $_SESSION['usuariocod']);
$oObjeto = new cServiciosLicencias($conexion);
$oAutorizantes = new cAutorizantes($conexion);

const EN_ESPERA_DE_CERTIFICADO = 12;

$esModif = false;
$botonejecuta = "BtAlta";
$boton = "Alta";
$btn = "BtnInsertar";
$btnEnviar = "BtnInsertarEnviar";
$Id = "";
$IdTipo = 1;
$IdPersona = "";
$Dni = "";
$NombrePersona = "";
$ApellidoPersona = "";
$NombreCompleto = "";
$IdEstado = "";
$Inicio = "";
$Fin = "";
$FechaFinAbierta = "";
$Duracion = "";
$Unidad = "";
$Horas = "";
$IdMotivo = "";
$IdAutorizante = "";
$Matricula = "";
$NombreAutorizante = "";
$ApellidoAutorizante = "";
$IdEspecialidad = "";
$EncontroAutorizante = "";
$Familiar = "";
$IdFamiliar = "";
$FamiliarDni = "";
$FamiliarNombre = "";
$FamiliarApellido = "";
$IdParentesco = "";
$Adecuacion = "";
$CertificadosContenido = "";
$CertificadosNombre = "";
$MotivoDetalleDescripcion = "";
$MotivoDetalleId = "";
$Descripcion = "";
$Direccion = "";
$Piso = "";
$IdArticulo = "";
$ArticuloDescripcion = "";
$AptoFisico = "";
$DiagnosticoDetalleId = "";
$IdDiagnostico = "";
$DiagnosticoDetalleDescripcion = "";
$IdTipoAutorizante = $NombreTipoAutorizante = '';
$Motivos = [];
$MatriculaJunta = $IdAutorizanteJunta = $ObservacionesJunta = '';
$editable = $tienePermisosModificar = $AsociarJuntaMedica = $tienePermisoVerCertificado = $tienePermisoModifCertificado = $tienePermisosObservaciones = $tienePermisosVerInstrumento = $tienePermisoVerDescripcion =
$tienePermisoVerAutorizante = $tienePermisoModifArticulo = $tienePermisoModifAutorizante = $tienePermisoEliminar = $tienePermisoFamiliar = $tienePermisoVerJuntaMedica = $tienePermisoVerCIE10 = $tienePermisoModifCIE10 = $tienePermisoVerAptoFisico = $tienePermisoModifAptoFisico = $tienePermisoVerTareaPasiva = $tienePermisoModifTareaPasiva = $tienePermisosModifInstrumento = $tienePermisoModifDescripcion = $tienePermisoVerMovimientosPendientes = $tienePermisoLiquidarMovimientos = false;
$TareaPasiva = $FechaReintegro = $NroResolucion = "";
$resultadoDetalles = ['filas' => []];
$Parentesco = "";
$IdEscuelaSeleccionada = '';
$acciones = [];

if (isset($_GET['Id']) && $_GET['Id'] != "") {
    FuncionesPHPLocal::ArmarLinkMD5(basename($_SERVER['PHP_SELF']), ['Id' => $_GET['Id']], $get, $md5);
    if ($_GET['md5'] != $md5) {
        $oEncabezados->setTitle('Error de permisos');
        ?>
        <div class="panel-style space">
            <div class="form">
                <div class="alert alert-danger">
                    <strong>Error!</strong> Usted no tiene permisos para visualizar el documento (ERR-209387).
                </div>
            </div>
        </div>
        <?php
        $oEncabezados->PieMenuEmergente();
        die();
    }
    $esModif = true;
    $datos = $_GET;

    $datosRegistro = $oObjeto->ObtenerLicenciaxId($datos, true);

    if ($datosRegistro === false) {
        $error = $oObjeto->getError();
        FuncionesPHPLocal::MostrarMensaje($conexion, MSG_ERRGRAVE, utf8_decode($error['error_description']), ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => FMT_TEXTO]);
        $oEncabezados->PieMenuEmergente();
        die();
    }

    if ($datosRegistro['Tipo']['Id'] != TIPO_MEDICA) {
        FuncionesPHPLocal::MostrarMensaje($conexion, MSG_ERRGRAVE, 'Error en el acceso de la licencia', ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => FMT_TEXTO]);
        $oEncabezados->PieMenuEmergente();
        die();
    }

    if (!$oObjeto->obtenerAcciones($datos, $acciones)) {
        $error = $oObjeto->getError();
        FuncionesPHPLocal::MostrarMensaje($conexion, MSG_ERRGRAVE, utf8_decode($error['error_description']), ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => FMT_TEXTO]);
        $oEncabezados->PieMenuEmergente();
        die();
    }

    $btn = "BtnModificar";
    $btnEnviar = "BtnModificarEnviar";
    $Id = $datosRegistro["Id"];
    $IdTipo = $datosRegistro["Tipo"]["Id"];
    $IdMotivo = $datosRegistro['Motivo']['Id'];
    $Motivos = $oObjeto->ObtenerMotivoxId($IdMotivo);
    $IdPersona = $datosRegistro["Persona"]["Id"];
    $NroResolucion = $datosRegistro['Resolucion']['Numero'] ?? '';

    $oPersonas = new cServiciosPersonas($conexion);

    $datosBuscarPersona['IdPersona'] = $IdPersona;

    $datosPersona = $oPersonas->ObtenerPersonaxId($datosBuscarPersona);
    if (false === $datosPersona) {
        FuncionesPHPLocal::MostrarMensaje($conexion, MSG_ERRGRAVE, utf8_decode($oPersonas->getError('error_description')), ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => FMT_TEXTO]);
        $oEncabezados->PieMenuEmergente();
    }

    $Direccion = isset($datosPersona['Calle']) ? $datosPersona['Calle'] : '';

    if (isset($datosPersona['NumeroPuerta']))
        $Direccion .= ' ' . $datosPersona['NumeroPuerta'] . ', ';

    if (isset($datosPersona['Piso'])) {
        $Direccion .= 'P' . $datosPersona['Piso'];
    }

    if (isset($datosPersona['Depto'])) {
        $Direccion .= ', Dpto ' . $datosPersona['Depto'];
    }

    if (isset($datosPersona['NombreLocalidad'])) {
        $Direccion .= ', ' . trim($datosPersona['NombreLocalidad']);
    }

    if (isset($datosPersona['NombreDepartamento'])) {
        $Direccion .= ', ' . trim($datosPersona['NombreDepartamento']);
    }

    $Direccion .= ', ' . $datosPersona['NombreProvincia'];


    $Direccion = FuncionesPHPLocal::HtmlspecialcharsSistema(trim($Direccion), ENT_QUOTES);

    $datosPersona['NombreProvincia'] = FuncionesPHPLocal::HtmlspecialcharsSistema(trim($datosPersona['NombreProvincia'] ?? ''), ENT_QUOTES);

    if (isset($datosRegistro["Persona"]["Dni"]))
        $Dni = $datosRegistro["Persona"]["Dni"];

    if (isset($datosRegistro["Persona"]["Nombre"]))
        $NombrePersona = $datosRegistro["Persona"]["Nombre"];

    if (isset($datosRegistro["Persona"]["Apellido"]))
        $ApellidoPersona = $datosRegistro["Persona"]["Apellido"];

    if (isset($datosRegistro["Persona"]["NombreCompleto"]))
        $NombreCompleto = $datosRegistro["Persona"]["NombreCompleto"];

    if (isset($datosRegistro["IdEscuelaSeleccionada"]))
        $IdEscuelaSeleccionada = $datosRegistro["IdEscuelaSeleccionada"];

    $IdEstado = $datosRegistro["Estado"]["Id"];

    if (!empty($acciones['accionesABM'])) {
        if (isset($acciones['accionesABM']['000003']) || isset($acciones['accionesABM']['000006']))
            $editable = true;
        if (isset($acciones['accionesABM']['000002']))
            $tienePermisosModificar = true;
        if (isset($acciones['accionesABM']['000004']))
            $AsociarJuntaMedica = true;
        if (isset($acciones['accionesABM']['000001']))
            $tienePermisoVerCertificado = true;
        if (isset($acciones['accionesABM']['000005']))
            $tienePermisosObservaciones = true;
        if (isset($acciones['accionesABM']['000019']))
            $tienePermisosVerInstrumento = true;
        if (isset($acciones['accionesABM']['000191']))
            $tienePermisosModifInstrumento = true;
        if (isset($acciones['accionesABM']['000020']))
            $tienePermisoVerDescripcion = true;
        if (isset($acciones['accionesABM']['000201']))
            $tienePermisoModifDescripcion = true;
        if (isset($acciones['accionesABM']['000021']))
            $tienePermisoVerAutorizante = true;
        if (isset($acciones['accionesABM']['000022']))
            $tienePermisoFamiliar = true;
        if (isset($acciones['accionesABM']['000023']))
            $tienePermisoModifCertificado = true;
        if (isset($acciones['accionesABM']['000028']))
            $tienePermisoVerTareaPasiva = true;
        if (isset($acciones['accionesABM']['000281']))
            $tienePermisoModifTareaPasiva = true;
        if (isset($acciones['accionesABM']['000029']))
            $tienePermisoVerCIE10 = true;
        if (isset($acciones['accionesABM']['000291']))
            $tienePermisoModifCIE10 = true;
        if (isset($acciones['accionesABM']['000026']))
            $tienePermisoVerAptoFisico = true;
        if (isset($acciones['accionesABM']['000261']))
            $tienePermisoModifAptoFisico = true;
        if (isset($acciones['accionesABM']['000030']))
            $tienePermisoVerJuntaMedica = true;
        if (isset($acciones['accionesABM']['000294']))
            $tienePermisoVerMovimientosPendientes = true;
        if (isset($acciones['accionesABM']['000295']))
            $tienePermisoLiquidarMovimientos = true;
        if (isset($acciones['accionesABM']['000031']))
            $tienePermisoEliminar = true;
        if (isset($acciones['accionesABM']['000211']))
            $tienePermisoModifAutorizante = true;
        if (isset($acciones['accionesABM']['000032']))
            $tienePermisoModifArticulo = true;


    }

    $Inicio = (isset($datosRegistro["Inicio"]) ? FuncionesPHPLocal::ConvertirFecha($datosRegistro["Inicio"], "aaaa-mm-dd", "dd/mm/aaaa") : "");
    $Fin = (isset($datosRegistro["Fin"]) ? FuncionesPHPLocal::ConvertirFecha($datosRegistro["Fin"], "aaaa-mm-dd", "dd/mm/aaaa") : "");

    $Duracion = $datosRegistro["Duracion"];
    $Unidad = $datosRegistro["Unidad"]["Id"];

    if (!FuncionesPHPLocal::isEmpty($datosRegistro["FechaFinAbierta"]))
        $FechaFinAbierta = $datosRegistro["FechaFinAbierta"];

    $IdDiagnostico = (isset($datosRegistro["Diagnostico"]["Id"]) ? $datosRegistro["Diagnostico"]["Id"] : "");
    if (isset($datosRegistro["Diagnostico"]["Descripcion"]) && !empty($datosRegistro["Diagnostico"]["Descripcion"]))
        $DiagnosticoDetalleDescripcion = $DiagnosticoDescripcion = $datosRegistro["Diagnostico"]["Descripcion"];

    if (!FuncionesPHPLocal::isEmpty($datosRegistro['Diagnostico']['Texto']))
        $Descripcion = $datosRegistro['Diagnostico']['Texto'];
    elseif (isset($datosRegistro["Motivo"]["Descripcion"]) && !empty($datosRegistro["Motivo"]["Descripcion"]))
        $Descripcion = $datosRegistro["Motivo"]["Descripcion"];

    if (isset($datosRegistro["Articulo"]["Id"]) && !empty($datosRegistro["Articulo"]["Id"]))
        $IdArticulo = $datosRegistro["Articulo"]["Id"];

    if (isset($datosRegistro["Articulo"]["Descripcion"]) && !empty($datosRegistro["Articulo"]["Descripcion"]))
        $ArticuloDescripcion = $datosRegistro["Articulo"]["Descripcion"];

    $ExisteDetalle = false;
    if (!empty($datosRegistro['Diagnostico']['Detalle'])) {
        $ExisteDetalle = true;
        $DiagnosticoDetalleId = $datosRegistro['Diagnostico']['Detalle']['Id'];
        $datos['Id'] = $IdDiagnostico;
        if (!$oObjeto->ObtenerDiagnosticoDetalle($datos, $resultadoDetalles, $numfilas)) {
            $error = $oObjeto->getError();
            FuncionesPHPLocal::MostrarMensaje($conexion, MSG_ERRGRAVE, utf8_decode($error['error_description']), ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => FMT_TEXTO]);
            $oEncabezados->PieMenuEmergente();
            die();
        }
    }

    $IdAutorizante = $datosRegistro["Autorizante"]["Id"] ?? "";

    if ($IdAutorizante != "") {
        //busco datos de autorizante por BD
        $datosAut["IdAutorizante"] = $IdAutorizante;
        $oAutorizantes->BuscarxCodigo($datosAut, $resultadoAut, $numfilasAut);

        if ($numfilasAut > 0) {
            $IdTipoAutorizante = $resultadoAut["IdTipoAutorizante"] ?? '';
            $NombreTipoAutorizante = $resultadoAut['NombreTipoAutorizante'] ?? '';
            $Matricula = $resultadoAut["Matricula"] ?? '';
            $NombreAutorizante = $resultadoAut["Nombre"] ?? '';
            $ApellidoAutorizante = $resultadoAut["Apellido"] ?? '';
            $EncontroAutorizante = 1;
        } else {
            $IdTipoAutorizante = $datosRegistro['Autorizante']["Id"] ?? 1; //x default medico
            $Matricula = $datosRegistro['Autorizante']["Matricula"] ?? '';
            $NombreAutorizante = $datosRegistro['Autorizante']["Nombre"] ?? '';
            $ApellidoAutorizante = $datosRegistro['Autorizante']["Apellido"] ?? '';
            $EncontroAutorizante = (isset($IdAutorizante) && $IdAutorizante != "" ? 1 : 0);
        }
    } else {
        $IdTipoAutorizante = $datosRegistro['Autorizante']["Id"] ?? 1; //x default medico
        $Matricula = $datosRegistro['Autorizante']["Matricula"] ?? '';
        $NombreAutorizante = $datosRegistro['Autorizante']["Nombre"] ?? '';
        $ApellidoAutorizante = $datosRegistro['Autorizante']["Apellido"] ?? '';
        $EncontroAutorizante = (isset($IdAutorizante) && $IdAutorizante != "" ? 1 : 0);
    }

    $IdEspecialidad = $datosRegistro['Autorizante']['Especialidad']['Id'] ?? '';

    $Familiar = $datosRegistro["esFamiliar"];

    if ($Familiar) {
        $oFamiliares = new cServiciosFamiliares($conexion);
        $datosFamiliarServicio["Id"] = $datosRegistro["Familiar"]["Id"];

        #busco familiar en el sso

        $FamiliarVec = $oFamiliares->ObtenerFamiliarxId($datosFamiliarServicio);


        $IdFamiliar = $FamiliarVec["Id"];
        $FamiliarDni = $FamiliarVec["Dni"];
        $FamiliarNombre = $FamiliarVec["Nombre"] ?? '';
        $FamiliarApellido = $FamiliarVec["Apellido"];
        $IdParentesco = $FamiliarVec["IdParentesco"];
        $Parentesco = $FamiliarVec["NombreParentesco"];

    }

    $FechaReintegro = (!FuncionesPHPLocal::isEmpty($datosRegistro["FechaReintegro"]) ? FuncionesPHPLocal::ConvertirFecha($datosRegistro["FechaReintegro"], "aaaa-mm-dd", "dd/mm/aaaa") : "");
    $TareaPasiva = $datosRegistro['TareaPasiva'] ?? "";
    $Adecuacion = $datosRegistro["Adecuacion"] ?? "";


    $AptoFisico = $datosRegistro['DatosAptoFisico']["Id"] ?? "";


    $Certificado = [];
    foreach ($datosRegistro["Certificados"] as $key => $c) {
        $Certificado[$key]["Id"] = $c["Id"];
        $Certificado[$key]["Contenido"] = $c["Contenido"];
        $Certificado[$key]["Nombre"] = $c["Nombre"];
        $Certificado[$key]["Tipo"] = $c["Tipo"];
    }

    if ($datosRegistro['Estado']['Id'] == EN_ESPERA_DE_CERTIFICADO)
        $claseTitulo = 'default';
    else
        $claseTitulo = 'text-' . (time() >= $datosRegistro['Estado']['MostrarTmpHasta'] ? $datosRegistro['Estado']['Class'] : $datosRegistro['Estado']['ClassTmp']);
}

if (isset($_POST['Dni']) && $_POST['Dni'] != "" && $Dni == "")
    $Dni = $_POST['Dni'];

if (isset($_POST['Nombre']) && $_POST['Nombre'] != "")
    $NombreCompleto = $_POST['Nombre'];

if (isset($_POST['IdPersona']) && $_POST['IdPersona'] != "")
    $IdPersona = $_POST['IdPersona'];

$Diagnosticos = $oObjeto->ObtenerDiagnosticos(1);
$Especialidades = $oObjeto->ObtenerEspecialidades();
//$Parentescos = $oObjeto->ObtenerParentescos();


$files = strtolower("JPG,JPEG,PNG,PDF");
$cantidad = 10;

$oSolicitudesCobertura = new cSolicitudesCobertura($conexion);
$datosBuscarSolicitud['IdLicencia'] = $Id;

?>
    <script type="text/template" id="qq-template">
        <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Mover archivos aqui">
            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                     class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar" style="display: none;"></div>
            </div>
            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                <span class="qq-upload-drop-area-text-selector"></span>
            </div>
            <?php if ($tienePermisoModifCertificado) { ?>
                <div class="qq-upload-button-selector btn btn-info qq-upload-button">
                    <div style=" padding-top: 3px;">Subir <?= ARCHIVO_LICENCIAS_TITULO ?></div>
                </div>
            <?php } ?>
            <span class="qq-drop-processing-selector qq-drop-processing">

        <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
    </span>
            <div class="clearboth">&nbsp;</div>
            <ul class="qq-upload-list-selector qq-upload-list col-md-6" aria-live="polite" aria-relevant="additions removals">
                <li>
                    <div class="qq-progress-bar-container-selector">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                    </div>
                    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                    <img class="qq-thumbnail-selector" style="display: none !important; " qq-max-size="100" qq-server-scale>
                    <span class="qq-upload-file-selector qq-upload-file"></span>
                    <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
                    <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                    <span class="qq-upload-size-selector qq-upload-size"></span>
                    <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancelar</button>
                    <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Reintentar</button>
                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                </li>
            </ul>

            <dialog class="qq-alert-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Cerrar</button>
                </div>
            </dialog>

            <dialog class="qq-confirm-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">No</button>
                    <button type="button" class="qq-ok-button-selector">Si</button>
                </div>
            </dialog>

            <dialog class="qq-prompt-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <input type="text">
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Cancelar</button>
                    <button type="button" class="qq-ok-button-selector">Ok</button>
                </div>
            </dialog>
        </div>
    </script>
    <script type="text/javascript">
        let sizeLimitFile = <?php echo TAMANIOARCHIVOS?>;
        let puestos = [];
        let buscarCargos = false;
        var certificadosPdf;
    </script>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="float-left">
                                <h4 class="<?php echo $claseTitulo; ?>"><?php
                                    echo $datosRegistro['Estado']['Nombre'];
                                    if ($datosRegistro['Rectificada']) echo '&ensp;<span class="label label-primary label-rounded">Licencia rectificada</span>'; ?>

                                </h4>
                            </div>
                            <div class="float-right text-right">
                                <?php if (file_exists(CARPETA_BAREMO . 'Baremo.pdf')) { ?>
                                    <a href="/lic_descargar_baremo.php" target="_blank" class="btn btn-outline-info" title="Baremo"><i
                                            class="far fa-file-pdf"></i>&nbsp;&nbsp;Descargar Baremo</a>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $class = "col-md-12 col-lg-4 col-xlg-5";
        if (!$tienePermisoVerCertificado) {
            $class = 'col-12';
        } ?>

        <div class="<?= $class ?>">
            <div class="card">
                <div class="card-body">
                    <div class="form">
                        <form class="form-material" action="javascript:void(0);" method="post" name="formalta" id="formalta">
                            <input type="hidden" name="IdPersona" id="IdPersona" value="<?php echo $IdPersona; ?>"/>
                            <input type="hidden" name="Id" id="Id" value="<?php echo $Id; ?>"/>
                            <input type="hidden" name="IdTipo" id="IdTipo" value="<?php echo $IdTipo; ?>"/>
                            <input type="hidden" name="AltaLicencia" id="AltaLicencia" value="0"/>
                            <input type="hidden" name="Estado" id="Estado" value="<?= $IdEstado; ?>">
                            <input type="hidden" name="IdEscuelaSeleccionada" id="IdEscuelaSeleccionada" value="<?= $IdEscuelaSeleccionada; ?>">

                            <h4 class="card-title mt-0 mb-4">Datos Personales</h4>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 col-sm-12">
                                    <div class="form-group clearfix">
                                        <label for="Dni">N&uacute;mero de Documento</label>
                                        <input type="text" class="form-control input-md " maxlength="9" name="Dni" id="Dni" value="<?php echo $Dni; ?>"
                                               readonly/>
                                    </div>
                                </div>
                                <div class="col-md-12 col-xs-12 col-sm-12">
                                    <div class="form-group clearfix">
                                        <label for="Nombre">Nombre Completo</label>
                                        <input type="text" class="form-control input-md " maxlength="255" name="Nombre" id="Nombre"
                                               value="<?php echo $NombreCompleto; ?>" readonly/>
                                    </div>
                                </div>
                            </div>

                            <h4 class="card-title mt-0 mb-4">Datos de la licencia</h4>
                            <div class="row">

                                <div class="col-md-12 col-xs-12 col-sm-12">
                                    <div class="form-group clearfix">
                                        <label for="Inicio">Inicio</label>
                                        <input name="Inicio" id="Inicio" placeholder="dd/mm/aaaa" class="form-control input-md" type="text" maxlength="10"
                                               value="<?= $Inicio; ?>" <?= $editable ? '' : 'disabled="disabled"'; ?> autocomplete="off" disabled/>
                                    </div>
                                </div>

                                <div
                                    class="<?php echo($Unidad != 0 ? "col-md-4" : "col-md-12"); ?> col-xs-12 col-sm-12 hs <?php echo $FechaFinAbierta ? "hide" : ""; ?>">
                                    <div class="form-group clearfix">
                                        <label for="Horas">Duraci&oacute;n</label>
                                        <select name="Horas" id="Horas" class="form-control input-md" <?= $editable ? '' : 'disabled="disabled"'; ?> disabled>
                                            <option value="">Seleccione</option>
                                            <option value="24" <?= ($Unidad == 0 && $Duracion != "" && $Duracion == "1" ? "selected" : ""); ?>>24 hs</option>
                                            <option value="48" <?= ($Unidad == 0 && $Duracion != "" && $Duracion == "2" ? "selected" : ""); ?>>48 hs</option>
                                            <option value="72" <?= ($Unidad == 0 && $Duracion != "" && $Duracion == "3" ? "selected" : ""); ?>>72 hs</option>
                                            <option value="0" <?= ($Unidad != 0 ? "selected" : ""); ?>>Otros</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-2 col-xs-12 col-sm-12 duracion <?= ($Unidad != 0 && !$FechaFinAbierta ? "" : "hide"); ?>">
                                    <div class="form-group clearfix">
                                        <label for="Duracion">&nbsp;</label>
                                        <input type="text" class="form-control input-md " maxlength="255" name="Duracion" id="Duracion"
                                               value="<?= $Duracion ?>" <?= $editable ? '' : 'disabled="disabled"'; ?> disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12 col-sm-12 unidad <?= ($Unidad != 0 && !$FechaFinAbierta ? "" : "hide"); ?>">
                                    <div class="form-group clearfix">
                                        <label for="Unidad">&nbsp;</label>
                                        <select name="Unidad" id="Unidad" class="form-control input-md" <?= $editable ? '' : 'disabled="disabled"'; ?> disabled>
                                            <option value="1" selected>d&iacute;as</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 col-xs-12 col-sm-12 fin <?php echo $FechaFinAbierta ? "hide" : ""; ?>">
                                    <div class="form-group clearfix">
                                        <label for="Fin">Fin</label>
                                        <input name="Fin" id="Fin" placeholder="dd/mm/aaaa" class="form-control input-md" type="text" maxlength="10"
                                               value="<?= $Fin; ?>" autocomplete="off" disabled/>
                                    </div>
                                </div>


                                <?php
                                if (
                                    ($Motivos['filas'][0]['Id'] == $IdMotivo && (int)$Motivos['filas'][0]['FechaFinOptativa'])
                                    || (!FuncionesPHPLocal::isEmpty($FechaFinAbierta) && $FechaFinAbierta)
                                ): ?>

                                    <div class="col-md-6 col-xs-12 col-sm-6" style="margin-left:1.25rem">
                                        <div class="form-group clearfix">

                                            <input name="FechaFinAbierta" id="FechaFinAbierta"
                                                   class="form-check-input" type="checkbox"
                                                   value="1"
                                                <?php echo $FechaFinAbierta ? 'checked' : ''; ?>
                                                <?= $editable ? '' : 'disabled="disabled"'; ?> disabled
                                            />
                                            <label for="FechaFinAbierta" class="form-check-label">Sin fecha de finalizaci&oacute;n</label>
                                        </div>
                                    </div>


                                <?php else: ?>

                                    <input type="hidden" name="FechaFinAbierta" value="0"/>

                                <?php endif; ?>

                            </div>


                            <?php if (!FuncionesPHPLocal::isEmpty($FechaReintegro)) { ?>
                                <div class="row">
                                    <div class="col-md-12 col-xs-12 col-sm-12">
                                        <div class="form-group clearfix">
                                            <label for="FechaReintegro">Fecha de reincorporaci&oacute;n</label>
                                            <input name="FechaReintegro" id="FechaReintegro" placeholder="dd/mm/aaaa" class="form-control input-md" type="text"
                                                   maxlength="10"
                                                   value="<?= $FechaReintegro; ?>" autocomplete="off" disabled/>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ($tienePermisoVerDescripcion) { ?>
                                <div class="row">
                                    <div class="col-md-12 col-xs-12 col-sm-6">
                                        <div class="form-group mb-1">
                                            <?php if (PROVINCIA == 'AR-U') { ?>
                                                <label for="Descripcion">S&iacute;ntomas manifestados / Aclaraciones</label>
                                            <?php } else { ?>
                                                <label for="Descripcion">S&iacute;ntomas manifestados</label>
                                            <?php } ?>
                                            <textarea class="form-control" maxlength="255" rows="4" id="Descripcion"
                                                      name="Descripcion"<?php echo ($tienePermisoModifDescripcion) ? '' : 'disabled="disabled"'; ?>><?php echo $Descripcion; ?></textarea>
                                            <span class="bar"></span>
                                        </div>
                                    </div>
                                    <div class="clearboth">&nbsp;</div>
                                </div>
                            <?php } else { ?>
                                <input type="hidden" id="Descripcion" name="Descripcion" value="<?= $Descripcion; ?>">
                            <?php } ?>

                            <?php /*if($IdArticulo != "") {?>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 col-sm-6">
                                    <div class="form-group mb-1">
                                            <label for="ArticuloDescripcion">Art&iacute;culo seleccionado</label>
                                        <textarea class="form-control" maxlength="255" rows="4" id="ArticuloDescripcion" name="ArticuloDescripcion" disabled="disabled"><?php echo $ArticuloDescripcion; ?></textarea>
                                        <span class="bar"></span>
                                    </div>
                                </div>
                                <div class="clearboth">&nbsp;</div>
                            </div>
                            <input type="hidden" id="IdArticuloSeleccionado" name="IdArticuloSeleccionado" value="<?= $IdArticulo; ?>">
                        <?php } */ ?>

                            <input type="hidden" name="IdMotivo" id="IdMotivo" value="<?= $IdMotivo; ?>"
                                   data-cargo="<?= ((int)$Motivos['filas'][0]['SeleccionaCargos']); ?>"
                                   data-familiar="<?= ((int)$Motivos['filas'][0]['PermiteLicenciaFamiliar']); ?>"/>

                            <?php if ($tienePermisoVerCIE10) { ?>
                                <h4 class="card-title mt-0 mb-4">Datos de auditor&iacute;a</h4>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group clearfix">
                                            <label for="IdDiagnostico">CIE10 (3)</label>
                                            <select name="IdDiagnostico" id="IdDiagnostico"
                                                    class="form-control input-md chzn-select" <?php echo ($tienePermisoModifCIE10) ? '' : 'disabled="disabled"'; ?> >
                                                <option value="">Seleccione</option>
                                                <?php foreach ($Diagnosticos['filas'] as $r) { ?>
                                                    <option
                                                        value="<?= $r['Id']; ?>" <?= ($r['Id'] == $IdDiagnostico ? 'selected="selected"' : ''); ?>> <?= $r['Nombre'];
                                                        echo($r['Descripcion'] != "" ? ' - ' . $r['Descripcion'] : ''); ?> </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12" id="Detalles">
                                        <div class="form-group clearfix" id="DetalleDiagnostico">
                                            <?php if (isset($ExisteDetalle) && $ExisteDetalle && $IdDiagnostico <> 1) { ?>
                                                <label for="IdDiagnosticoDetalle">CIE10 (4)</label>
                                                <select name="IdDiagnosticoDetalle" id="IdDiagnosticoDetalle"
                                                        class="form-control input-md chzn-select" <?php echo ($tienePermisoModifCIE10) ? '' : 'disabled="disabled"'; ?> >
                                                    <option value="">Seleccione</option>
                                                    <?php foreach ($resultadoDetalles['filas'] as $r) { ?>
                                                        <option
                                                            value="<?php echo $r['Id']; ?>" <?php echo($DiagnosticoDetalleId == $r['Id'] ? "selected" : ""); ?> > <?php echo $r['Nombre'] . '-' . $r['Descripcion']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($tienePermisoVerTareaPasiva) { ?>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group mb-1">
                                            <label for="TareaPasiva">Implica Tareas Pasivas o Cambio de Funci&oacute;n? </label>
                                            <select id="TareaPasiva" name="TareaPasiva"
                                                    class="form-control input-md" <?php echo ($tienePermisoModifTareaPasiva) ? '' : 'disabled="disabled"'; ?>>
                                                <option value="" <?php if ($TareaPasiva == '') echo 'selected'; ?>>Seleccione una opci&oacute;n</option>
                                                <option value="1" <?php if ($TareaPasiva == '1') echo 'selected'; ?>>S&iacute;</option>
                                                <option value="0" <?php if ($TareaPasiva == '0') echo 'selected'; ?>>No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (MOSTRAR_APTO_FISICO) { ?>
                                <?php if ($tienePermisoVerAptoFisico) {
                                    $combo_aptofisico = $oObjeto->ObtenerAptoFisico();
                                    ?>
                                    <br>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mb-1">
                                                <label for="AptoFisico">Ingres&oacute; Apto F&iacute;sico?</label>
                                                <select id="AptoFisico" name="AptoFisico"
                                                        class="form-control input-md" <?php echo ($tienePermisoModifAptoFisico) ? '' : 'disabled="disabled"' ?>>
                                                    <option value="" <?php if ($AptoFisico == "") echo 'selected'; ?>>Seleccione una opci&oacute;n</option>
                                                    <?php
                                                    foreach ($combo_aptofisico["filas"] as $fila_aptofisico):
                                                        ?>
                                                        <option
                                                            value="<?php echo $fila_aptofisico["Id"]; ?>" <?php if ($AptoFisico == $fila_aptofisico["Id"]) echo 'selected'; ?>>
                                                            <?php echo $fila_aptofisico["Nombre"]; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>

                            <br>
                            <?php if ($tienePermisosVerInstrumento) { ?>
                                <div class="row">
                                    <div class="col-12 mt-2">
                                        <div class="form-group">
                                            <label for="NroResolucion">Instrumento legal</label>
                                            <input name="NroResolucion" id="NroResolucion" class="form-control input-md"
                                                   type="text"
                                                   autocomplete="off"
                                                   value="<?php echo $NroResolucion; ?>"
                                                <?php echo ($tienePermisosModifInstrumento) ? '' : 'disabled="disabled"'; ?>
                                            />
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($tienePermisoVerAutorizante) { ?>
                                <h4 class="card-title mt-0 mb-4">Datos del autorizante</h4>
                                <div id="DatosAutorizante">
                                    <div id="DatosMatricula">
                                        <div class="row">
                                            <div class="col-12" id="NroMatricula">
                                                <div class="form-group clearfix">
                                                    <label for="Matricula">Matricula</label>
                                                    <br><small>Ingrese la matricula o el nombre del autorizante de la licencia</small>
                                                    <input type="text" class="form-control input-md " name="Matricula" id="Matricula"
                                                           value="<?= $Matricula; ?>" <?php echo ($tienePermisoModifAutorizante) ? '' : 'disabled="disabled"'; ?>
                                                           autocomplete="off"/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group clearfix">
                                                    <label for="TipoAutorizante">Tipo de tratante</label>
                                                    <select class="form-control input-md " name="TipoAutorizante" id="TipoAutorizante">
                                                        <option value="<?= $IdTipoAutorizante; ?>"><?= $NombreTipoAutorizante; ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group clearfix">
                                                    <label for="IdEspecialidad">Especialidad</label>
                                                    <select name="IdEspecialidad" id="IdEspecialidad"
                                                            class="form-control input-md" <?php echo ($tienePermisoModifAutorizante) ? '' : 'disabled="disabled"'; ?>>
                                                        <option value="">Seleccione</option>
                                                        <?php
                                                        if (!empty($IdAutorizante)) {
                                                            $datosBusqueda['IdAutorizante'] = $IdAutorizante;
                                                            $oEspecialidadAutorizante = new cEspecialidadesAutorizantes($conexion);
                                                            if (!$oEspecialidadAutorizante->BuscarxCodigoAutorizante($datosBusqueda, $resultadoEspecialidad, $numfilasEspecialidad))
                                                                return false;
                                                        }

                                                        if (isset($numfilasEspecialidad) && $numfilasEspecialidad > 0) {
                                                            if ($resultadoEspecialidad['total'] == 0 && $resultadoEspecialidad['Id'] == "") { ?>
                                                                <option value="0" <?= ($IdEspecialidad == 0 ? 'selected' : ''); ?>>Sin asignar</option>
                                                            <?php } else {
                                                                foreach ($resultadoEspecialidad['filas'] as $r) { ?>
                                                                    <option
                                                                        value="<?= $r['IdEspecialidad']; ?>" <?= ($r['IdEspecialidad'] == $IdEspecialidad ? 'selected' : ''); ?> ><?= utf8_encode($r['Nombre']); ?></option>
                                                                <?php }
                                                            }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12 col-sm-6">
                                                <div class="form-group clearfix">
                                                    <label for="NombreAutorizante">Nombre completo</label>
                                                    <input type="text" class="form-control input-md " maxlength="20"
                                                           value="<?php echo $NombreAutorizante . ' ' . $ApellidoAutorizante; ?>" disabled/>
                                                </div>
                                            </div>
                                            <?php if ($esModif) { ?>
                                                <input type="hidden" id="NombreAutorizante" name="NombreAutorizante"
                                                       value="<?php echo $NombreAutorizante . ' ' . $ApellidoAutorizante; ?>">
                                            <?php } ?>
                                            <input type="hidden" id="IdAutorizante" name="IdAutorizante" value="<?php echo $IdAutorizante; ?>">
                                            <input type="hidden" id="EncontroAutorizante" name="EncontroAutorizante"
                                                   value="<?php echo $EncontroAutorizante; ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="clearboth">&nbsp;</div>
                            <?php if ($Familiar) { ?>
                                <div class="custom-control custom-checkbox d-none">
                                    <input type="checkbox" class="custom-control-input" id="Familiar"
                                           name="Familiar" <?php echo(isset($Familiar) && $Familiar == 1 ? 'value="1" checked' : 'value="0"'); ?> <?php echo $editable ? '' : 'disabled="disabled"'; ?>
                                           disabled>
                                    <label class="custom-control-label" for="Familiar"><!--suppress HtmlUnknownTag -->
                                        <h4>Licencia Familiar</h4>
                                    </label>
                                </div>
                            <?php } ?>

                            <div class="clearboth">&nbsp;</div>

                            <div id="dataLicenciaFamiliar" class="hide">
                                <h4 class="card-title mt-0 mb-4">Datos del Familiar</h4>
                                <div class="row">
                                    <div class="col-md-12 col-xs-12 col-sm-12">
                                        <div class="form-group clearfix">
                                            <label for="IdParentesco">Relaci&oacute;n</label>:
                                            <?php /*<select name="IdParentesco" id="IdParentesco" class="form-control input-md" <?php echo ($tienePermisoFamiliar ? '' : 'disabled') ?> >
                                            <option value="">Seleccione</option>
                                            <?php foreach ($Parentescos['filas'] as $r) { ?>
                                                <option value="<?php echo $r['Id']; ?>" <?php echo($r['Id'] == $IdParentesco ? 'selected="selected"' : ''); ?>> <?php echo $r['Nombre']; ?> </option>
                                            <?php } ?>
                                        </select>*/ ?>
                                            <input type="text" class="form-control input-md " maxlength="9" name="ParentescoNombre" id="ParentescoNombre"
                                                   value="<?php echo $Parentesco; ?>" disabled autocomplete="off"/>
                                            <input type="hidden" name="IdParentesco" id="IdParentesco" value="<?php echo $IdParentesco ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xs-12 col-sm-12">
                                        <div class="form-group clearfix">
                                            <div class="form-group clearfix">
                                                <label for="FamiliarDni">N&uacute;mero de Documento</label>
                                                <input type="text" class="form-control input-md " maxlength="9" name="FamiliarDni" id="FamiliarDni"
                                                       value="<?php echo $FamiliarDni; ?>" <?php echo($tienePermisoFamiliar ? '' : 'disabled') ?>
                                                       autocomplete="off"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="DatosFamiliares">
                                    <div class="row" id="DatosFamilia">
                                        <div class='col-md-12 col-xs-12 col-sm-12'>
                                            <div class='form-group clearfix'>
                                                <label for='FamiliarNombre'>Apellido y nombre</label>
                                                <input type='text' class='form-control input-md ' maxlength='90' name='FamiliarNombreCompleto'
                                                       id='FamiliarNombreCompleto'
                                                       value="<?= "$FamiliarApellido $FamiliarNombre"; ?>"
                                                       autocomplete="off" disabled/>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12 col-sm-12 hide">
                                            <div class="form-group clearfix">
                                                <label for="FamiliarNombre">Nombre</label>
                                                <input type="text" class="form-control input-md " name="FamiliarNombre" id="FamiliarNombre"
                                                       value="<?php echo $FamiliarNombre; ?>" <?php echo($tienePermisoFamiliar ? '' : 'disabled') ?>
                                                       autocomplete="off"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12 col-sm-12 hide">
                                            <div class="form-group clearfix">
                                                <div class="form-group clearfix">
                                                    <label for="FamiliarApellido">Apellido</label>
                                                    <input type="text" class="form-control input-md " name="FamiliarApellido" id="FamiliarApellido"
                                                           value="<?php echo $FamiliarApellido; ?>" <?php echo($tienePermisoFamiliar ? '' : 'disabled') ?>
                                                           autocomplete="off" disabled/>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="IdFamiliar" name="IdFamiliar" value="<?php echo $IdFamiliar; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="menuAcciones">
                                <?php if ($editable) { ?>
                                    <h5><b>Recuerde guardar antes de aprobar</b></h5>
                                    <hr>
                                <?php } ?>
                                <div class="menubarra">
                                    <?php

                                    if ($editable) {
                                        //$tooltip = empty($acciones['guardado']['Tooltip']) ? '' : "data-toggle=\"tooltip\" data-placement=\"top\" title=\"{$acciones['guardado']['Tooltip']}\" ";
                                        $tooltip = "Guardar";
                                        $btnModificar = 'btnNull';
                                        if (!empty($acciones['accionesABM']['000003'])) {
                                            $AccionesGuardado = $acciones['accionesABM']['000003'];
                                            $btnModificar = 'btnModificar';
                                        } elseif (!empty($acciones['accionesABM']['000006'])) {
                                            $AccionesGuardado = $acciones['accionesABM']['000006'];
                                            $btnModificar = 'btnModificarReabierto';
                                        }
                                        echo "\t\t\t\t\t\t\t\t\t";
                                        echo "<a class=\"btn btn-outline-success\" href=\"javascript:void(0)\" id=\"$btnModificar\" $tooltip><i class=\"fas fa-save\"></i>&nbsp;Guardar</a>\n";
                                        if ($tienePermisosModificar) {
                                            echo '<a class="btn btn-outline-info" href="javascript:void(0)" id="btnEditar" data-toggle="tooltip" data-placement="top" title="Habilitar la modificaci�n de datos de la licencia." ><i class="fa fa-edit"></i>&nbsp;Editar</a>';
                                            echo '<a class="btn btn-outline-info hide" href="javascript:void(0)" id="btnCancelar" data-toggle="tooltip" data-placement="top" title="Deshabilitar la modificaci�n de datos de la licencia." ><i class="fa fa-undo"></i>&nbsp;Cancelar</a>';
                                        }

                                    }

                                    if (!empty($acciones['acciones'])) {
                                        if ($editable)
                                            echo '<span style="border:none; border-left: 1px solid hsla(200, 10%, 50%,100); height: 100%; width: 1px; " ></span>&nbsp;';
                                        foreach ($acciones['acciones'] as $accion) {
                                            if (ESTADO_DERIVADOR_AUTOMATICO == $accion['IdEstadoFinal']) continue;

                                            $tooltip = empty($accion['Tooltip']) ? '' : "data-toggle=\"tooltip\" data-placement=\"top\" title=\"{$accion['Tooltip']}\" ";
                                            echo "\t\t\t\t\t\t\t\t\t";
                                            echo "<a class=\"btn btn-outline-{$accion['Clase']} btnMover\" data-target='{$accion['Id']}' data-id='{$accion['IdEstadoFinal']}' data-nombre='{$accion['Nombre']}' href=\"javascript:void(0)\" id=\"btnMover_{$accion['Id']}{$accion['Accion']}\" $tooltip><i class=\"{$accion['Icono']}\" $tooltip></i>&nbsp;{$accion['Nombre']}</a>\n";
                                        }

                                        //echo "\t\t\t\t\t\t\t\t\t";
                                        //echo  "<a class=\"btn btn-danger\" href=\"javascript:void(0)\" id=\"btnAnular\"><i class=\"fas fa-times-circle\"></i>&nbsp;Anular</a>\n";
                                        //echo "\t\t\t\t\t\t\t\t\t";
                                        //echo "<a class=\"btn btn-danger\" href=\"javascript:void(0)\" id=\"btnDenegar\"><i class=\"far fa-times-circle\"></i>&nbsp;Denegar</a>\n";

                                    }
                                    ?>
                                    <input type="hidden" id="IdEstado" name="IdEstado" value="<?php echo $IdEstado; ?>">

                                    <div class="msgaccionupd">&nbsp;</div>
                                    <div class="menubarra float-right">
                                        <a class="btn btn-outline-secondary" href="/licencias/medicas/revision">Volver</a>
                                    </div>
                                </div>
                            </div>

                            <div class="clearboth">&nbsp;</div>
                        </form>
                        <div class="col-md-4 col-xs-12 col-sm-6">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($tienePermisoVerCertificado) { ?>
            <div class="col-md-12 col-lg-8 col-xlg-7">
                <div class="card">
                    <div class="card-body">
                        <div class="form">
                            <h4 class="card-title mt-0 mb-4"><?= ARCHIVO_LICENCIAS_TITULO ?></h4>
                            <form class="form-material" action="javascript:void (0);" method="post" name="formaltaCertificados" id="formaltaCertificados">
                                <div class="row">
                                    <div class="col-md-12 col-xs-12 col-sm-6 text-right" style="height: 30px">
                                        <div class="form-group clearfix">
                                            <div id="fileadjunto" data-id="<?php echo $files ?>" data-type="<?php echo $cantidad ?>" title="Seleccione archivo"
                                                 class="fileUpload"></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="fileadjuntoLst"></div>
                                <div id="CertificadosEliminar"></div>
                            </form>
                            <div class="clearboth aire">&nbsp;</div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-12">
                                        <?php
                                        $certificadosPdf = [];
                                        if (count($Certificado) <> 0) { ?>
                                            <?php foreach ($Certificado as $c) { ?>
                                                <div id="Certificado_<?php echo $c['Id']; ?>" class="text-center">
                                                    <div class="clearboth aire">&nbsp;</div>
                                                    <div class="input-group input-md" style="z-index: 100;">
                                                        <span class="input-group-addon bg-red font-white" id="btnMostrarImagen"
                                                              data-app="<?php echo $c['Tipo']; ?>" data-contenido="<?php echo $c['Contenido']; ?>"
                                                              href="javascript:void(0)" data-img='<?php echo json_encode($c); ?>'><i
                                                                class="fa fa-search-plus"></i></span>
                                                        &nbsp;<?php if ($tienePermisoModifCertificado) { ?>
                                                            <span class="input-group-addon bg-red font-white" href="javascript:void(0)"
                                                                  onclick="return EliminarDocumento(<?php echo $c['Id']; ?>)"><i class="fa fa-trash"></i></span>
                                                        <?php } ?>
                                                    </div>
                                                    <?php

                                                    if ('application/pdf' == $c['Tipo'])
                                                        $certificadosPdf[] = ["id" => $c['Id'], "base64" => $c['Contenido']];
                                                    else
                                                        echo "<img src=\"data:{$c['Tipo']};base64, {$c['Contenido']}\" alt=\"{$c['Nombre']}\" style=\"width: auto; max-width: 80%;\" >"; ?>
                                                </div>
                                            <?php }
                                        } else { ?>
                                            <div id="carouselExampleIndicators2" class="carousel" data-ride="carousel" data-interval="false">
                                                <?php if (count($Certificado) > 1) { ?>
                                                    <ol class="carousel-indicators">
                                                        <?php for ($ii = 0; $ii < count($Certificado); $ii++) {
                                                            $active = 0 === $ii ? 'active' : '';
                                                            echo "<li data-target=\"#carouselExampleIndicators2\" data-slide-to=\"$ii\" class=\"$active\"></li>\n";
                                                        } ?>
                                                    </ol>
                                                    <?php
                                                }
                                                ?>
                                                <div class="carousel-inner" role="listbox">
                                                    <?php foreach ($Certificado as $key => $c) { ?>
                                                        <div class="carousel-item <?php echo($key == 1 ? "active" : ""); ?>">
                                                            <div id="Certificado_<?php echo $c['Id']; ?>" class="text-center">
                                                                <div class="input-group input-md" style="z-index: 10;">
                                                                <span class="input-group-addon bg-red font-white" id="btnMostrarImagen"
                                                                      data-app="<?php echo $c['Tipo']; ?>" data-contenido="<?php echo $c['Contenido']; ?>"
                                                                      href="javascript:void(0)" data-img='<?php echo json_encode($c); ?>'><i
                                                                        class="fa fa-search-plus"></i></span>
                                                                </div>
                                                                <div class="clearboth aire">&nbsp;</div>
                                                                <?php

                                                                if ('application/pdf' == $c['Tipo'])
                                                                    $certificadosPdf[] = ["id" => $c['Id'], "base64" => $c['Contenido']];
                                                                else
                                                                    echo "<img src=\"data:{$c['Tipo']};base64, {$c['Contenido']}\" alt=\"{$c['Nombre']}\" style=\"width: auto; max-width: 80%;\" >"; ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <a class="carousel-control-prev" href="#carouselExampleIndicators2" role="button" data-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#carouselExampleIndicators2" role="button" data-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </div>
                                        <?php } ?>
                                        <script>
                                            certificadosPdf = '<?php echo $certificadosPdf !== [] ? json_encode($certificadosPdf) : ""; ?>';
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 col-xs-12 col-sm-6 hide" id="puestos">
                    <div class="form-group clearfix" id="comboPuestos"></div>
                </div>
                <div class="col-md-12 tablePuestos">
                    <h4 class="card-title mt-0 mb-1 ">Cargos afectados</h4>
                    <small class="mb-4">Corresponden al momento en que se emiti&oacute; la licencia</small>
                    <div class="clearboth"> &nbsp;</div>
                    <form action="javascript:void(0)" name="formCargosAfectados" id="formCargosAfectados" method="post">
                        <div class="col-md-12 p-0">
                            <div class="form-group clearfix">
                                <table class="table table-custom table-sm" style="font-size: 14px;">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width: 5%">Id Pofa</th>
                                        <th style="width: 5%">Puesto</th>
                                        <th style="width: 40%"></th>
                                        <th style="width: 10%">Horas/M&oacute;dulos</th>
                                        <th style="width: 10%">Horas afectadas</th>
                                        <th style="width: 25%">Art&iacute;culo</th>
                                        <th style="width: 5%">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody id="lstCargosAfectados">
                                    <?php include_once DIR_ROOT . '/lic_licencias_administracion_am_cargos_lst_ajax.php'; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php if ($tienePermisoVerJuntaMedica) { ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 tableJuntas">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-left">
                                <?php if (PROVINCIA == "AR-U") { ?>
                                    <h4 class="card-title mt-0 mb-1 ">Juntas/Monitoreos Domiciliarios asociados</h4>
                                <?php } else { ?>
                                    <h4 class="card-title mt-0 mb-1 ">Juntas/visitas m&eacute;dicas asociadas</h4>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-right">
                                <?php
                                if ($AsociarJuntaMedica) {
                                    echo "<a class=\"btn btn-info\" href=\"javascript:void(0)\" id=\"btnFechaJunta\"><i class=\"\"></i>&nbsp;Asociar nueva</a>";
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="clearboth"> &nbsp;</div>
                    <div id="LstDatos" style="width:100%;">
                        <table id="listarDatos"></table>
                        <div id="pager2"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php } ?>
<?php if ($tienePermisoVerMovimientosPendientes) { ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 tableMovimientos">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-left">
                                <h4 class="card-title mt-0 mb-1">Movimientos liquidatorios de esta licencia</h4>
                            </div>
                        </div>
                    </div>
                    <div class="clearboth"> &nbsp;</div>
                    <div id="LstMovimientos" style="width:100%;">
                        <table id="listarMovimientos"></table>
                        <div id="pager3"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php } ?>
    <div class="card">
        <div class="card-body">
            <div class="form">
                <form class="form-material" action="javascript:void (0);" method="post" name="formaltaobservacion" id="formaltaobservacion">
                    <h4 class="card-title mt-0 mb-2">Comentarios</h4>

                    <div id="ContenedorComentarios">
                        <div class="chat-rbox" style="height: 280px; overflow: auto;">
                            <ul class="chat-list">
                            </ul>
                        </div>
                    </div>
                    <?php if ($tienePermisosObservaciones) { ?>
                        <div class="card-body border-top p-r-0 p-l-0 p-t-30 mt-2">
                            <div class="row">
                                <div class="col-11">
                                    <textarea class="form-control b-0" style="height: 80%;" cols="30" rows="6" id="Observacion" name="Observacion"
                                              placeholder="Ingrese su comentario aqu&iacute;"></textarea>
                                </div>

                                <div class="col-1 text-right">
                                    <label for="Observacion"><a class="btn btn-info" href="javascript:void(0)" id="BtnEnviarComentario"><i
                                                class="fa fa-paper-plane"></i></a></label>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <input type="hidden" name="IdTipo" id="IdTipo" value="<?php echo $IdTipo; ?>"/>
                    <input type="hidden" name="IdPersona" id="IdPersona" value="<?php echo $IdPersona; ?>"/>
                    <input type="hidden" name="Id" id="Id" value="<?php echo $Id; ?>"/>

                </form>
            </div>
        </div>
    </div>

    <div id="ModalData" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                </div>
                <div class="modal-body">
                    <div id="DataCertificado">
                        <div class="zoom_certificado"></div>
                    </div>
                    <div class="clearboth"></div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div id="ModalJunta" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header m-0">
                    <?php if (PROVINCIA == "AR-U") { ?>
                        <h3 class="new-modal-title">Asociar nueva junta/Monitoreo Domiciliario</h3>
                    <?php } else { ?>
                        <h3 class="new-modal-title">Asociar nueva junta/visita</h3>
                    <?php } ?>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form">
                        <form action="javascript:void(0);" method="post" name="formNuevaJunta" id="formNuevaJunta" class="form-material">
                            <div class="row">
                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div class="form-group clearfix">
                                        <label for="Fecha">Fecha</label>
                                        <input name="Fecha" id="Fecha" placeholder="dd/mm/aaaa" class="form-control input-md" type="text" maxlength="10"
                                               value="" autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <div class="form-group clearfix">
                                        <label for="Hora">Hora</label>
                                        <input type="time" class="form-control input-md " name="Hora" id="Hora" value="" min="00:00" max="23:59"/>
                                    </div>
                                </div>
                                <?php
                                $oRegiones = new cRegiones($conexion);
                                if (!$oRegiones->BuscarCombo($combo, $total))
                                    return false; ?>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-12 col-sm-6">
                                    <label for="IdRegion">Regi&oacute;n/Nodo</label>
                                    <select name="IdRegion" id="IdRegion" class="form-control input-md">
                                        <option value="">Seleccione</option>
                                        <?php if ($total > 0) {
                                            while ($fila = $conexion->ObtenerSiguienteRegistro($combo)) { ?>
                                                <option value="<?= $fila['IdRegion']; ?>"> <?= $fila['Nombre']; ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                </div>

                                <?php $oTipos = new cJuntasTipos($conexion);

                                if (!$oTipos->obtenerListado($resultadoTipoJunta, $numfilasTipoJunta)) { ?>
                                    <div class="col-12 mt-3 mb-3">
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo $oTipos->getError()['error_description']; ?>
                                        </div>
                                    </div>
                                    <?php $oEncabezados->PieMenuEmergente();
                                    die();
                                }

                                if ($numfilasTipoJunta > 0) { ?>
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                        <label for="IdTipoJunta">Tipo</label>
                                        <select name="IdTipoJunta" id="IdTipoJunta" class="form-control input-md">
                                            <option value="">Seleccione</option>
                                            <?php foreach ($resultadoTipoJunta as $r) { ?>
                                                <option value="<?= $r['Id']; ?>"> <?= $r['Descripcion']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mt-4">
                                <?php $oMotivos = new cJuntasMotivos($conexion);
                                if (!$oMotivos->obtenerListado($resultadoMotivoJunta, $numfilasMotivoJunta)) { ?>
                                    <div class="col-12 mt-3 mb-3">
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo $oMotivos->getError()['error_description']; ?>
                                        </div>
                                    </div>
                                    <?php $oEncabezados->PieMenuEmergente();
                                    die();
                                }

                                if ($numfilasMotivoJunta > 0) { ?>
                                    <div class="col-md-6 col-xs-12 col-sm-6">
                                        <label for="IdMotivoJunta">Motivo</label>
                                        <select name="IdMotivoJunta" id="IdMotivoJunta" class="form-control input-md">
                                            <option value="">Seleccione</option>
                                            <?php foreach ($resultadoMotivoJunta as $r) { ?>
                                                <option value="<?= $r['Id']; ?>"> <?= $r['Descripcion']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>


                                <div class="clearboth">&nbsp;</div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-10">
                                    <label for="Direccion">Direcci&oacute;n</label>
                                    <textarea value="<?php echo $Direccion; ?>" class="form-control" maxlength="255" rows="4" id="Direccion"
                                              name="Direccion"><?php echo isset($Direccion) ? $Direccion : ''; ?></textarea>
                                    <span class="bar"></span>
                                </div>

                                <div class="clearboth mt-2">
                                    <div class="col-md-12 text-right"><br>
                                        <a class="btn btn-success" href="javascript:void (0);" id="btnAgregarJunta">&nbsp;Insertar</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="mt-2">&nbsp;</div>
                    <div id="LstDatos" style="width: 764px;">
                        <table id="listarDatos"></table>
                        <div id="pager2"></div>
                    </div>
                    <div id="Popup"></div>

                    <div class="clearboth"></div>
                </div>
            </div>
        </div>
    </div>


    <div id="ModalEditarJunta" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header m-0">
                    <h3 class="new-modal-title">Informaci&oacute;n de Junta/Visita</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="content-junta">

                </div>

            </div>
        </div>
    </div>


    <form action="/solicitudes-cobertura/" id="formSolicitud" name="formSolicitud" class="hide" target="_blank" method="post">
        <input type="hidden" name="IdEscuela" id="IdEscuela" value="">
        <input type="hidden" name="IdPuesto" id="IdPuesto" value="">
        <input type="hidden" name="IdLicencia" id="IdLicencia" value="">
    </form>

    <script id="result-template" type="text/x-handlebars-template">
        <div class="ProfileCard u-cf">
            <div class="ProfileCard-details">
                <div class="ProfileCard-realName"><span style="font-size: 12px;"><strong>Matricula {{Matricula}}</strong></span></div>
                <div class="ProfileCard-screenName"><span style="font-size: 13px;">{{nombre_completo}}</span></div>
            </div>
        </div>
    </script>


    <script id="result-template-junta" type="text/x-handlebars-template">
        <div class="ProfileCard u-cf">
            <div class="ProfileCard-details">
                <div class="ProfileCard-realName"><span style="font-size: 12px;"><strong>Matricula {{Matricula}}</strong></span></div>
                <div class="ProfileCard-screenName"><span style="font-size: 13px;">{{nombre_completo}}</span></div>
            </div>
        </div>
    </script>


<?php
$oEncabezados->PieMenuEmergente();
