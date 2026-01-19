<?php
/**
 * @noinspection PhpStatementHasEmptyBodyInspection
 * @noinspection PhpUnusedParameterInspection
 * @noinspection PhpUnused
 * @noinspection HtmlFormInputWithoutLabel
 * @noinspection HtmlUnknownTarget
 */

/**
 * Class cEncabezados
 */
class cEncabezados {
    /** @var accesoBDLocal */
    var $conexionencab;
    /** @var array */
    var $arreglomenumostrar = [];
    /** @var bool */
    var $preload;
    /** @var string */
    var $title;
    /** @var  bool */
    var $logueado;
    /** @var array */
    var $arrayScript = [];
    /** @var array */
    var $arrayCss = [];
    /** @var array */
    var $menuSuperior = [];

    private bool $usarAPM = false;

    function __construct(accesoBDLocal $conexion = null) {
        $this->conexionencab = &$conexion;
        $this->preload = false;
        $this->title = "";
        $this->usarAPM = defined('USAR_APM') && USAR_APM;
    }

//-----------------------------------------------------------------------------------------
//							 PUBLICAS
//-----------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------
//

    public function addScript($script_src) {
        $this->arrayScript[] = $script_src;
    }

    public function addCss($css_src) {
        $this->arrayCss[] = $css_src;
    }


    public function CargarPreload() {
        $this->preload = true;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    function loadAnalytics() {

        if (!defined('GOOGLE_ANALYTICS'))
            return;
        ?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= GOOGLE_ANALYTICS; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());

            gtag('config', '<?= GOOGLE_ANALYTICS; ?>');
        </script>
        <?php
    }

    /**
     * @param $rolcod
     * @param $usuariocod
     * @param $tienePeriodo
     *
     * @return void
     * @noinspection t
     */
function EncabezadoMenuEmergente($rolcod, $usuariocod, $tienePeriodo = false) {
    $HtmlMenu = '';
    $this->logueado = false;
    $VariosRoles = false;
    $variasEscuelas = false;
    if ($rolcod != "" && $usuariocod != "") {
        $this->logueado = true;
        if (isset($_SESSION['roles_seleccion']) && count($_SESSION['roles_seleccion']) > 1)
            $VariosRoles = true;
        if (isset($_SESSION['selecciona_escuelas']) && count($_SESSION['selecciona_escuelas']) > 1)
            $variasEscuelas = true;
    }
    if ($this->usarAPM) {
        $apmContext = new stdClass();
        $apmContext->serviceName = APPLICATION . '-' . PROVINCIA;
        $apmContext->serverUrl = APM_SERVER;

        $userContext = new stdClass();
        $userContext->id = $_SESSION['usuariocod'];
        $userContext->username = utf8_encode($_SESSION['usuarionombre']);
        $userContext->email = utf8_encode($_SESSION['email'] ?? '');

        $customContext = new stdClass();
        if (!empty($_SESSION['rolcod'])) {
            $customContext->rol_id = current($_SESSION['rolcod']);
            $customContext->rol_nombre = utf8_encode($_SESSION['NombreRol'] ?? '');
        }
        if (!empty($_SESSION['IdEscuela'])) {
            $customContext->escuela_id = $_SESSION['IdEscuela'];
            $customContext->escuela_nombre = utf8_encode($_SESSION['NombreEscuela'] ?? '');
        }
        if (!empty($_SESSION['IdsRegion'])) {
            $customContext->regiones = $_SESSION['IdsRegion'];
        }
    }

    ?>
    <!DOCTYPE html>
    <html lang="es" translate='no'>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="/assets/provincia/<?php echo PROVINCIA ?>/favicon/favicon.ico?v=1" type="image/vnd.microsoft.icon"/>
        <link rel="icon" type="image/png" sizes="32x32" href="/assets/provincia/<?php echo PROVINCIA ?>/favicon/favicon-32x32.png?v=1">
        <link rel="icon" type="image/png" sizes="16x16" href="/assets/provincia/<?php echo PROVINCIA ?>/favicon/favicon-16x16.png?v=1">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/assets/provincia/<?php echo PROVINCIA ?>/favicon/ms-icon-144x144.png">
        <meta name="theme-color" content="#1AA3D1">
        <title>Sistema de Novedades de Agentes - <?= PROVINCIA_NOMBRE; ?></title>
        <link href="/assets/plugins/bootstrap/css/bootstrap.min.css?v=1.0" rel="stylesheet">
        <link href="/assets/plugins/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">
        <link href="/assets/css/style.css?v=1.17" rel="stylesheet">
        <link href="/assets/css/colors/blue.css" id="theme" rel="stylesheet">
        <link href="/assets/lib/fontawesome/5/css/all.min.css" id="theme" rel="stylesheet">
        <link rel="stylesheet" href="/assets/lib/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.min.css">
        <link media="screen" rel="stylesheet" type="text/css" href="/assets/plugins/jqgrid/css/ui.jqgrid-bootstrap4.css"/>
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <?php
        $this->loadAnalytics();

        foreach ($this->arrayCss as $css_src) {
            ?>
            <link rel="stylesheet" href="<?php echo $css_src ?>"/>
        <?php } ?>
        <?php if ($this->usarAPM) { ?>
            <script type="text/javascript">
                const x<?= md5('apmContext')?> = '<?php echo base64_encode(json_encode($apmContext));?>';
                const x<?= md5('userContext')?> = '<?php echo base64_encode(json_encode($userContext));?>';
                const x<?= md5('customContext')?> = '<?php echo base64_encode(json_encode($customContext));?>';
            </script>
        <?php } ?>

        <script type="text/javascript">

            function sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }

            var startTime = new Date().getTime();
            var img = new Image();
            img.onload = function () {
                var loadtime = new Date().getTime() - startTime;
                checkConnectionSpeed(loadtime);
            };
            img.src = "/assets/provincia/<?php echo PROVINCIA; ?>/images/logo.png?" + startTime;

            function checkConnectionSpeed(millisecond) {
                var x = document.getElementById("connection-message1");
                var y = document.getElementById("connection-message2");
                if (millisecond > 4000) {
                    x.style.color = 'red';
                    y.style.color = 'red';
                    x.innerHTML = 'Su conexi&oacute;n de internet es muy lenta';
                    y.innerHTML = 'Su conexi&oacute;n de internet es muy lenta';
                } else if (millisecond > 1500) {
                    x.style.color = y.style.color = 'orange';
                    x.innerHTML = y.innerHTML = 'Su conexi&oacute;n de internet se encuentra congestionada';
                }
                /*else{
                    x.style.display = 'none';
                    x.style.backgroundColor = 'green';
                    x.innerHTML = 'Su conexi�n esta OK';
                }*/
            }


            async function keepCheckingConnectionSpeed() {

                while (true) {
                    await sleep(20000);
                    checkConnectionSpeed(1000);
                    console.log("connection speed checked");
                }

            }
        </script>
        <?php if (defined("RIBBON_HEADER") && RIBBON_HEADER != "") { ?>
            <style>
                body:after {
                    content: "<?php echo RIBBON_HEADER?>";
                    position: fixed;
                    z-index: 1070;
                    width: 80px;
                    height: 25px;
                    background: red;
                    top: 7px;
                    left: -20px;
                    text-align: center;
                    font-size: 12px;
                    letter-spacing: 1px;
                    font-family: sans-serif;
                    text-transform: uppercase;
                    font-weight: bold;
                    color: #fff;
                    line-height: 27px;
                    -ms-transform: rotate(-45deg);
                    -webkit-transform: rotate(-45deg);
                    transform: rotate(-45deg);
                }
            </style>
            <?php
        } ?>

        <?php
            // Aviso de sistema en mantenimiento
            if (!empty($_SESSION['sistemaEnMantenimiento'])) {

                // Si ya hay RIBBON_HEADER, el de mantenimiento va un poco más abajo
                $topMantenimiento = (defined("RIBBON_HEADER") && RIBBON_HEADER != "")
                    ? '22px'   // debajo del ribbon rojo
                    : '7px';   // mismo lugar que el original si no existe RIBBON_HEADER

                $widthMantenimiento = (defined("RIBBON_HEADER") && RIBBON_HEADER != "")
                    ? '150px'
                    : '80px';

                $leftMantenimiento = (defined("RIBBON_HEADER") && RIBBON_HEADER != "")
                    ? '-35px'
                    : '-20px';
                ?>

                <style>
                    body:before {
                        content: "LOCK";
                        position: fixed;
                        z-index: 1070;
                        width: <?= $widthMantenimiento ?>;
                        height: 25px;
                        background: #6C63FF;
                        top: <?= $topMantenimiento ?>;
                        left: <?= $leftMantenimiento ?>;
                        text-align: center;
                        font-size: 12px;
                        letter-spacing: 1px;
                        font-family: sans-serif;
                        text-transform: uppercase;
                        font-weight: bold;
                        color: #fff;
                        line-height: 27px;
                        transform: rotate(-45deg);
                    }
                </style>
        <?php } ?>
    </head>

    <body class="fix-header card-no-border"> <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
            <span id="connection-message1" style="font-size:18px;display: block;position:absolute;top: 50px;z-index:9999;width:100%;text-align: center;"></span>
        </svg>
    </div>
    <div id="main-wrapper">
        <div id="connection-message2" style="display: block;font-size:14px;position:absolute;top: 50px;z-index:9999;width:100%;text-align: center;"></div>
        <header class="topbar mx-0">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <!-- <div class="navbar-header">
                     <div class="logo-header">
                         <a class="navbar-brand" href="<?/*= ($this->logueado) ? "/ingreso" : "/"; */?>">
                             <img src="/assets/provincia/<?/*= PROVINCIA; */?>/images/logo2.png" alt="<?/*= PROVINCIA_NOMBRE; */?>" class="float-right imglogo">
                         </a>
                     </div>
                 </div>-->
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0 ">
                        <li class="nav-item"><a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a></li>
                    </ul>
                    <?php if ($rolcod != "" && $usuariocod != "") { ?>

                        <ul class="navbar-nav my-lg-0 justify-content-center w-100">

                            <?php $oObjetoAcciones = new cRolesModulosAcciones($this->conexionencab);
                            $acciones = $oObjetoAcciones->BuscarAccionesxRol(['IdRol' => array_values($_SESSION['rolcod'])[0]]);

                            if (isset($acciones[9933]) && in_array(AC_009941, $acciones[9933])) { ?>
                                <li class="nav-item hidden-sm-down w-100">
                                    <form class="app-search">
                                        <input type="text" class="form-control" placeholder="Buscar agente" id="DniAgente"> <a class="srh-btn"
                                                                                                                               id="DataSearch"><i
                                                class="ti-search"></i></a>
                                    </form>
                                    <script id="result-template-finder" type="text/x-handlebars-template">
                                        <div class="ProfileCard u-cf">
                                            <div class="ProfileCard-details">
                                                <div class="ProfileCard-realName">{{TipoDocumento}} <strong>{{id}}</strong></div>
                                                <div class="ProfileCard-screenName">{{nombre_completo}}</div>
                                            </div>
                                        </div>
                                    </script>
                                </li>
                            <?php } ?>
                        </ul>

                    <?php } ?>
                </div>
                <div class="minimenu-header d-flex col-auto justify-content-end">

                    <button class="minimenu-boton" id="btnMinimenu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <g clip-path="url(#clip0_4418_9640)">
                                <path d="M22 8.27V4.23C22 2.64 21.36 2 19.77 2H15.73C14.14 2 13.5 2.64 13.5 4.23V8.27C13.5 9.86 14.14 10.5 15.73 10.5H19.77C21.36 10.5 22 9.86 22 8.27Z" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10.5 8.52V3.98C10.5 2.57 9.86 2 8.27 2H4.23C2.64 2 2 2.57 2 3.98V8.51C2 9.93 2.64 10.49 4.23 10.49H8.27C9.86 10.5 10.5 9.93 10.5 8.52Z" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10.5 19.77V15.73C10.5 14.14 9.86 13.5 8.27 13.5H4.23C2.64 13.5 2 14.14 2 15.73V19.77C2 21.36 2.64 22 4.23 22H8.27C9.86 22 10.5 21.36 10.5 19.77Z" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M14.5 17.5H20.5" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M17.5 20.5V14.5" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" />
                            </g>
                            <defs>
                                <clipPath id="clip0_4418_9640">
                                    <rect width="24" height="24" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </button>

                </div>
                <?php
                if ($rolcod != "" && $usuariocod != "") {
                    $grupomodulo = "";
                    $HtmlMenu = $this->_MenuEmergente($_SESSION['rolcod'], $grupomodulo);
                    $this->_MenuEmergenteTop();
                }
                ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark waves-effect waves-dark" href="#" data-toggle="dropdown" aria-haspopup="true"
                       aria-expanded="false">
                        <div
                            class="d-none d-md-block"><?php echo FuncionesPHPLocal::HtmlspecialcharsSistema(utf8_decode($_SESSION['usuarionombre']), ENT_QUOTES) ?></div>
                        <div class="datarol"><?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION['NombreRol'], ENT_QUOTES) ?></div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-usuario">

                        <div class="dropdown-usuario-header">
                            <div class="dropdown-usuario-nombre">
                                <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema(utf8_decode($_SESSION['usuarionombre']), ENT_QUOTES) ?>
                            </div>
                            <div class="dropdown-usuario-rol">
                                <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($_SESSION['NombreRol'], ENT_QUOTES) ?>
                            </div>
                        </div>

                        <ul class="dropdown-user">
                            <?php if ($VariosRoles) { ?>
                                <li>
                                    <a href="/login/response"><i class="fas fa-sync-alt"></i>Cambiar rol</a>
                                </li>
                            <?php } ?>

                            <li>
                                <a href="<?php echo URL_MIS_DATOS ?>"><i class="ti-user"></i>Mis datos</a>
                            </li>

                            <li class="dropdown-usuario-salir">
                                <a href="/salir"><i class="fa fa-power-off"></i>Cerrar sesi&oacute;n</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <?php if ($variasEscuelas) { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="#" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false" id="dropdownMenuButton">
                            <i class="fas fa-school"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right animated flipInY" aria-labelledby="dropdownMenuButton">
                            <ul class="dropdown-user"
                                style=" background-color: transparent; width:100%; height:auto; max-height: 500px;overflow-y: scroll">
                                <?php foreach ($_SESSION['selecciona_escuelas'] as $escuela) {
                                    $back = '';
                                    $weight = 'font-weight-normal';
                                    if ($escuela['IdEscuela'] == $_SESSION['IdEscuela']) {
                                        $weight = 'font-weight-bold';
                                        $back = 'background: #dfeeff';
                                    }

                                    echo "<li><a style='{$back}' href=\"javascript:void(0);\" class=\"btnCambiarEscuelaEncabezado\" data-toggle='tooltip' data-escuela='{$escuela['IdEscuela']}' data-cue='{$escuela['ClaveUnicaEscuela']}' data-placement='bottom' title='{$escuela['Nombre']}'><small class='$weight'>{$_SESSION['NombreRol']}&nbsp;-&nbsp;N&deg;&nbsp;{$escuela['CodigoEscuela']}</small></a></li>\n";

                                } ?>
                            </ul>
                        </div>
                    </li>
                <?php } ?>
            </nav>
        </header>

        <aside class="left-sidebar py-0">
            <div class="sidebar-header" style="height: 70px; display: flex; align-items: center; justify-content: space-between; padding: 0 15px; border-bottom: 1px solid #eef5f9;">
                <div class="logo-box" style="display: flex; align-items: center;">
                    <a class=" logo-iso" href="<?= ($this->logueado) ? "/ingreso" : "/";?>">
                        <img src="/assets/provincia/<?= PROVINCIA; ?>/images/logo2.png" alt="<?= PROVINCIA_NOMBRE; ?>" class="float-right imglogo">
                    </a>
                    <a class=" logo-full" href="<?= ($this->logueado) ? "/ingreso" : "/";?>">
                        <img src="/assets/provincia/<?= PROVINCIA; ?>/images/logoTF.png" alt="<?= PROVINCIA_NOMBRE; ?>" class="float-right imglogo">
                    </a>
                </div>
                <div class="sidebar-toggler-btn" title="Fijar menú" style="cursor:pointer;">
                    <i class="far fa-circle"></i>
                </div>
            </div>

            <div class="scroll-sidebar">
                <?php
                if ($rolcod != "" && $usuariocod != "") {
                    echo $HtmlMenu;
                }
                ?>
            </div>
        </aside>


        <div class="page-wrapper">
            <div class="container-fluid">
                <?php if (defined("MSG_ALERT_TOP") && MSG_ALERT_TOP != "") { ?>
                    <div class="myAlert-top alert alert-<?php echo MSG_ALERT_TOP_TYPE ?> alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <?php echo MSG_ALERT_TOP ?>
                    </div>
                <?php } ?>
                <?php if ($this->title != "") { ?>
                    <div class="row page-titles">
                        <div class="col-12 align-self-center">
                            <h3 class="text-themecolor theme-font mb-0 mt-0"><?php echo $this->title; ?></h3>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($tienePeriodo && TIENEPERIODO) {
                    include("header_periodos.php");
                } ?>
                <?php
                }

                function _MenuEmergente($roles, $grupomodulo = "") {
                $menu = [];
                foreach ($roles as $rol) {
                    $file = PATH_STORAGE . "menu/rol_$rol.json";
                    if (file_exists($file)) {
                        $str = file_get_contents($file);
                        $array = FuncionesPHPLocal::DecodificarUtf8(json_decode($str, true));
                        $menu = array_replace_recursive($menu, $array);
                    }
                }

                $htmlMenu = '<nav class="sidebar-nav"><ul id="sidebarnav">';

                $htmlMenu .= '<li class="nav-item">
                    <a href="/ingreso" class="nav-link" title="Mi escritorio"><i class="mdi mdi-gauge menu-icon"></i>
                        <span class="hide-menu">Mi escritorio</span>
                    </a>
                  </li>';

                foreach ($menu as $MenuItem) {
                $SubMenu = isset($MenuItem['Subitems']) ? $MenuItem['Subitems'] : [];
                $cantSubItems = count($SubMenu);

                    $esLinkDirecto = ($cantSubItems === 1);
                    if ($esLinkDirecto) {
                        $SubMenuItem = current($SubMenu);

                        if (isset($SubMenuItem['MuestraMenuSuperior']) && $SubMenuItem['MuestraMenuSuperior'] == 1) {
                            $this->menuSuperior[] = $SubMenuItem;
                        }

                        $Url = $SubMenuItem['Url'];
                        if (!isset($SubMenuItem['Url']) || empty(trim($SubMenuItem['Url']))) {
                            $Url = "/" . $SubMenuItem['archivonom'];
                        }

                        $htmlMenu .= '<li class="nav-item">
                                        <a href="' . FuncionesPHPLocal::HtmlspecialcharsSistema($Url, ENT_QUOTES) . '" class="nav-link" title="' . FuncionesPHPLocal::HtmlspecialcharsSistema($MenuItem['NombreMenu'], ENT_QUOTES) . '">
                                            <i class="' . $MenuItem['ClassImagen'] . ' menu-icon"></i>
                                            <span class="hide-menu">' . FuncionesPHPLocal::HtmlspecialcharsSistema($MenuItem['NombreMenu'], ENT_QUOTES) . '</span>
                                        </a>
                                      </li>';
                    }else {
                $htmlMenu .= '<li class="nav-item">
                                <a href="javascript:void(0)" class="nav-link has-arrow" aria-expanded="false">
                                    <i class="' . $MenuItem['ClassImagen'] . ' menu-icon"></i>
                                    <span class="hide-menu">' . FuncionesPHPLocal::HtmlspecialcharsSistema($MenuItem['NombreMenu'], ENT_QUOTES) . '</span>
                                </a>
                                <ul aria-expanded="false" class="collapse">';

                foreach ($SubMenu as $SubMenuItem) {
                if (isset($SubMenuItem['MuestraMenuSuperior']) && $SubMenuItem['MuestraMenuSuperior'] == 1) {
                    $this->menuSuperior[] = $SubMenuItem;
                }

                $Url = $SubMenuItem['Url'];
                if (!isset($SubMenuItem['Url']) || empty(trim($SubMenuItem['Url']))) {
                    $Url = "/" . $SubMenuItem['archivonom'];
                }

                $iconHtml = '';
                if (!empty($SubMenuItem['UbicacionImagen'])) {
                    $iconHtml = '<i class="' . $SubMenuItem['UbicacionImagen'] . '"></i> ';
                }


                    $htmlMenu .= '<li>
                                    <a href="' . FuncionesPHPLocal::HtmlspecialcharsSistema($Url, ENT_QUOTES) . '">
                                        ' . $iconHtml . FuncionesPHPLocal::HtmlspecialcharsSistema($SubMenuItem['TextoMenu'], ENT_QUOTES) . '
                                    </a>
                                  </li>';
                }
                    $htmlMenu .= '</ul></li>';
                }
                }

                    if ($this->logueado) {

                        $htmlMenu .= '<li class="nav-item">
                                        <a href="/mi-perfil" class="nav-link">
                                            <i class="ti-user menu-icon"></i>
                                            <span class="hide-menu">Mi perfil</span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="/mi-password" class="nav-link">
                                            <i class="ti-settings menu-icon"></i>
                                            <span class="hide-menu">Mi contrase&ntilde;a</span>
                                        </a>
                                    </li>

                                    <li class="nav-item sidebar-logout">
                                        <a href="/salir" class="nav-link text-danger">
                                            <i class="mdi mdi-power menu-icon"></i>
                                            <span class="hide-menu">Cerrar sesi&oacute;n</span>
                                        </a>
                                    </li>';
                    }

                    $htmlMenu .= '</ul></nav>';
                    return $htmlMenu;
                }


                function _MenuEmergenteTop() {
                    if (count($this->menuSuperior) > 0) {
                        ?>
                        <div class="minimenu-header d-flex col-auto justify-content-end">

                            <button class="minimenu-boton" id="btnMinimenu">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <g clip-path="url(#clip0_4418_10092)">
                                        <path d="M12.37 2.14984L21.37 5.74982C21.72 5.88982 22 6.30981 22 6.67981V9.99982C22 10.5498 21.55 10.9998 21 10.9998H3C2.45 10.9998 2 10.5498 2 9.99982V6.67981C2 6.30981 2.28 5.88982 2.63 5.74982L11.63 2.14984C11.83 2.06984 12.17 2.06984 12.37 2.14984Z" stroke="#5755fe" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M22 22H2V19C2 18.45 2.45 18 3 18H21C21.55 18 22 18.45 22 19V22Z" stroke="#5755fe" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M4 18V11" stroke="#5755fe" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M8 18V11" stroke="#5755fe" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M12 18V11" stroke="#5755fe" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M16 18V11" stroke="#5755fe" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M20 18V11" stroke="#5755fe" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M1 22H23" stroke="#5755fe" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M12 8.5C12.8284 8.5 13.5 7.82843 13.5 7C13.5 6.17157 12.8284 5.5 12 5.5C11.1716 5.5 10.5 6.17157 10.5 7C10.5 7.82843 11.1716 8.5 12 8.5Z" stroke="#5755fe" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_4418_10092">
                                            <rect width="24" height="24" fill="white"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </button>

                            <div class="minimenu-opciones" id="minimenuOpciones">

                                <div class="minimenu-titulo">
                                    Acceso r&aacute;pido
                                </div>

                                <div class="minimenu-grid">

                                    <?php foreach ($this->menuSuperior as $data) {

                                        $Url = $data['Url'];
                                        if (trim($Url) == "") {
                                            $Url = "/" . $data['archivonom'];
                                        }

                                        $texto = $data['TextoMenuSuperior'];

                                        switch (strtolower($texto)) {
                                            case 'novedades':
                                                $icono = 'icono-novedades';
                                                break;
                                            case 'licencias':
                                                $icono = 'icono-licencias';
                                                break;
                                            case 'establecimientos':
                                                $icono = 'icono-establecimiento';
                                                break;
                                            default:
                                                $icono = 'icono-default';
                                        }
                                        ?>

                                        <a href="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($Url, ENT_QUOTES) ?>"
                                           class="minimenu-item <?php echo $icono; ?>">

                                            <?php
                                            if ($icono === 'icono-novedades') {
                                                ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <g clip-path="url(#clip0_4418_9484)">
                                                        <path d="M22 16.7397V4.6697C22 3.4697 21.02 2.5797 19.83 2.6797H19.77C17.67 2.8597 14.48 3.9297 12.7 5.0497L12.53 5.1597C12.24 5.3397 11.76 5.3397 11.47 5.1597L11.22 5.0097C9.44 3.8997 6.26 2.8397 4.16 2.6697C2.97 2.5697 2 3.4697 2 4.6597V16.7397C2 17.6997 2.78 18.5997 3.74 18.7197L4.03 18.7597C6.2 19.0497 9.55 20.1497 11.47 21.1997L11.51 21.2197C11.78 21.3697 12.21 21.3697 12.47 21.2197C14.39 20.1597 17.75 19.0497 19.93 18.7597L20.26 18.7197C21.22 18.5997 22 17.6997 22 16.7397Z" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M12 5.49023V20.4902" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M7.75 8.49023H5.5" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M8.5 11.4902H5.5" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_4418_9484">
                                                            <rect width="24" height="24" fill="white"/>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                <?php
                                            } elseif ($icono === 'icono-licencias') {
                                                ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#5755fe">
                                                    <path opacity="0.4" d="M7.95996 3.27062C8.37337 3.24851 8.72666 3.56615 8.74902 3.9796C8.77113 4.39302 8.4535 4.74632 8.04004 4.76867C6.43417 4.85552 5.43288 5.19293 4.80176 5.87609C4.16232 6.56841 3.75 7.78113 3.75 10.0001V16.0001C3.75004 17.9866 4.0114 19.231 4.68945 20.006C5.34591 20.7561 6.56481 21.2501 9 21.2501H15C17.4352 21.2501 18.6541 20.7561 19.3105 20.006C19.9886 19.231 20.25 17.9866 20.25 16.0001V10.0001C20.25 7.77569 19.8371 6.56393 19.1982 5.87316C18.6463 5.27654 17.8112 4.94433 16.5342 4.81261L15.96 4.76867L15.8828 4.76085C15.5075 4.70198 15.2303 4.36707 15.251 3.9796C15.2719 3.5918 15.5841 3.28848 15.9639 3.27062H16.04L16.3604 3.29113C17.9482 3.4125 19.3268 3.80326 20.2988 4.85363C21.3273 5.96539 21.75 7.65484 21.75 10.0001V16.0001C21.75 18.0131 21.5111 19.7684 20.4395 20.9933C19.3459 22.2431 17.5647 22.7501 15 22.7501H9C6.43533 22.7501 4.65411 22.2431 3.56055 20.9933C2.48886 19.7684 2.25004 18.0131 2.25 16.0001V10.0001C2.25 7.6595 2.67241 5.97122 3.7002 4.85851C4.73651 3.73675 6.23611 3.3638 7.95996 3.27062Z" fill="white" style="fill: var(--fillg);"/>
                                                    <path d="M14 1.25078C14.4352 1.25078 15.1904 1.22635 15.791 1.62675C16.4752 2.08298 16.75 2.90183 16.75 4.00078C16.75 4.43608 16.7743 5.19137 16.374 5.79179C15.9178 6.4759 15.0989 6.75078 14 6.75078H10C9.56481 6.75078 8.80956 6.77508 8.20898 6.3748C7.52476 5.91865 7.25007 5.09962 7.25 4.00078C7.25 2.90183 7.52481 2.08298 8.20898 1.62675C8.80959 1.22635 9.56476 1.25078 10 1.25078H14ZM10 2.75078C9.43524 2.75078 9.19041 2.77521 9.04102 2.8748C8.97509 2.91881 8.75 3.10067 8.75 4.00078C8.75007 4.90138 8.97533 5.08297 9.04102 5.12675C9.19042 5.22625 9.43547 5.25078 10 5.25078H14C14.8997 5.25078 15.0819 5.02581 15.126 4.95976C15.2255 4.81041 15.25 4.56526 15.25 4.00078C15.25 3.10067 15.0249 2.91881 14.959 2.8748C14.847 2.80011 14.6812 2.7672 14.3672 2.75566L14 2.75078H10Z" fill="white" style="fill: var(--fillg);"/>
                                                </svg>
                                                <?php
                                            } elseif ($icono === 'icono-establecimiento') {
                                                ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <g clip-path="url(#clip0_4418_9744)">
                                                        <path d="M20 14C21.1046 14 22 13.1046 22 12C22 10.8954 21.1046 10 20 10C18.8954 10 18 10.8954 18 12C18 13.1046 18.8954 14 20 14Z" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M20 6C21.1046 6 22 5.10457 22 4C22 2.89543 21.1046 2 20 2C18.8954 2 18 2.89543 18 4C18 5.10457 18.8954 6 20 6Z" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M20 22C21.1046 22 22 21.1046 22 20C22 18.8954 21.1046 18 20 18C18.8954 18 18 18.8954 18 20C18 21.1046 18.8954 22 20 22Z" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M4 14C5.10457 14 6 13.1046 6 12C6 10.8954 5.10457 10 4 10C2.89543 10 2 10.8954 2 12C2 13.1046 2.89543 14 4 14Z" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M6 12H18" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M18 4H14C12 4 11 5 11 7V17C11 19 12 20 14 20H18" stroke="#5755fe" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_4418_9744">
                                                            <rect width="24" height="24" fill="white"/>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                <?php
                                            }
                                            ?>
                                            <span><?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($texto, ENT_QUOTES); ?></span>
                                        </a>

                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }



                function PieMenuEmergente($cargarSugerencias = true)
                {
                if (isset($_SESSION['rolcod']) && $_SESSION['rolcod'] != "" && $cargarSugerencias) {
                    //$conexion = $this->conexionencab;
                    //include("sugerencia_modal.php");
                }
                ?>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const btnMinimenu = document.getElementById('btnMinimenu');
                const minimenu = document.getElementById('minimenuOpciones');

                if (!btnMinimenu || !minimenu) return;

                btnMinimenu.addEventListener('click', function (e) {
                    e.stopPropagation();
                    minimenu.classList.toggle('activo');
                });

                document.addEventListener('click', function (e) {

                    if (e.target.closest('#btnMinimenu') || e.target.closest('#minimenuOpciones')) {
                        return;
                    }

                    if (e.target.closest('.dropdown-menu') || e.target.closest('.dropdown-toggle')) {
                        return;
                    }

                    minimenu.classList.remove('activo');
                });

            });


        </script>

        <footer class="footer" style="position: fixed; width: 100%; bottom: 0; z-index: 100">
            <div class="footerInterno">
                <a class="float-left font-weight-bold text-decoration-none"
                   href="/cambios">Versi&oacute;n <?= trim(file_get_contents(DOCUMENT_ROOT . '/.version')) ?></a>
                &copy; 2014 - <?php echo date('Y'); ?> Tablero de gesti&oacute;n educativa - Todos los derechos reservados
            </div>
        </footer>
    </div>
    <script src="/assets/plugins/jquery/jquery.min.js"></script>
    <script src="/assets/plugins/jquery-md5/jquery.md5.min.js"></script>
    <script src="/assets/plugins/bootstrap/js/popper.min.js"></script>
    <script src="/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/jquery.slimscroll.js"></script>
    <script src="/assets/js/waves.js"></script>
    <script src="/assets/js/sidebarmenu.js"></script>
    <script src="/assets/plugins/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="/assets/js/custom.min.js"></script>
    <script type="text/javascript" src="/assets/js/jquery.blockUI.js"></script>
    <script src="/assets/plugins/sweetalert/sweetalert.min.js"></script>
    <script type="text/javascript" charset="UTF-8" src="/assets/plugins/jqgrid/js/i18n/grid.locale-es.js"></script>
    <script type="text/javascript" src="/assets/plugins/jqgrid/js/jquery.jqGrid.min.js"></script>
    <script type="text/javascript" src="/assets/js/funcionesjs.js?v=1.0"></script>
    <script type="text/javascript" src="/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="/assets/lib/typehead/handlebars.js" charset="UTF-8"></script>
    <script type="text/javascript" src="/assets/lib/typehead/typehead.js" charset="UTF-8"></script>
    <script type="text/javascript" src="/modulos/js/archivos/encabezado.js?v=1.4.5"></script>
    <?php if ($this->usarAPM) { ?>
        <script type="text/javascript" src="/assets/lib/apm/elastic-apm-rum.umd.min.js"></script>
        <script type="text/javascript"
                src="/assets/lib/apm/elastic-apm-start.min.js?hash=<?php echo hash_file('md5', DIR_ROOT . '/assets/lib/apm/elastic-apm-start.min.js'); ?>"></script>
    <?php } ?>
    <script src="/assets/lib/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js"></script>
    <?php foreach ($this->arrayScript as $scriptSrc) { ?>
        <script type="text/javascript" src="<?php echo $scriptSrc ?>" charset="UTF-8"></script>
    <?php } ?>
    <script src="/assets/js/modal_selector_escuelas.js"></script>

    </body>


    </html>
    <?php
    //$this->conexionencab->CerrarConexion();;
}

function EncabezadoMenuEmergenteLogin() {
    ?>
    <!DOCTYPE html>
    <html lang="en">


    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
        <title><?php echo PROJECTNAME ?></title>
        <link href="/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="/assets/css/style.css?v=1.12" rel="stylesheet">
        <link href="/assets/css/colors/blue.css" id="theme" rel="stylesheet">
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <?php $this->loadAnalytics(); ?>
    </head>

    <body>
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
        </svg>
    </div>
    <section id="wrapper">


        <?php
        }



        //-----------------------------------------------------------------------------------------
        //							 PRIVADAS
        //-----------------------------------------------------------------------------------------

        //-----------------------------------------------------------------------------------------
        //

        function PieMenuEmergenteLogin()
        {
        ?>
    </section>
    <script src="/assets/plugins/jquery/jquery.min.js"></script>
    <script src="/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/js/custom.login.min.js"></script>
    </body>
    </html>

    <?php
}

    function _MenuEmergenteOld($rolcod, $grupomodulo) {
        if ($rolcod == '' || $rolcod == 0)
            return false;

        // se piden todos los gruposmod que tengan 'S' o 'L'
        // en modulomostrar
        $roles = implode(",", $rolcod);
        $param = ['pIdRol' => $roles];
        if (!$this->conexionencab->ejecutarStoredProcedure("sel_menusuperior_xrol", $param, $menuprinc, $numfilas, $errno))
            return false;

        ?>

        <ul class="drop-area">
            <li class="no-back">
                <a href="/ingreso">Mi escritorio</a>
            </li>
            <?php
            $i = 0;
            while ($filaprinc = $this->conexionencab->ObtenerSiguienteRegistro($menuprinc)) {
                $param = ['pIdRol' => $roles, 'pIdGrupoMod' => $filaprinc['IdGrupoMod']];
                if (!$this->conexionencab->ejecutarStoredProcedure("sel_menuizq_xrolcod_xgrupocod", $param, $menusecund, $numfilas, $errno))
                    return false;

                // si tiene una sola fila con 'L' en modulomostrar
                // es porque no tiene submenu
                // pone link en el men principal izquierdo
                $filasecund = $this->conexionencab->ObtenerSiguienteRegistro($menusecund);

                if ($numfilas == 1 && $filasecund['modulomostrar'] == "L") {
                    ?>
                    <li>
                        <a href="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filasecund['archivonom'], ENT_QUOTES) ?>"
                           title="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaprinc['NombreMenu'], ENT_QUOTES) ?>">
                            <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaprinc['NombreMenu'], ENT_QUOTES) ?>
                        </a>
                    </li>
                    <?php
                } else {
                    ?>
                    <li>
                        <a href="javascript:void(0)" class="groupMenu"
                           title="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaprinc['NombreMenu'], ENT_QUOTES) ?>">
                            <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filaprinc['NombreMenu'], ENT_QUOTES) ?>
                        </a>
                        <?php //echo  FuncionesPHPLocal::HtmlspecialcharsSistema($filaprinc['grupomodtextomenu'],ENT_QUOTES);
                        $param = ['pIdRol' => $roles, 'pIdGrupoMod' => $filaprinc['IdGrupoMod']];
                        if (!$this->conexionencab->ejecutarStoredProcedure("sel_menuizq_xrolcod_xgrupocod", $param, $menusecund, $numfilas, $errno))
                            return false;
                        ?>
                        <ul>
                            <?php
                            while ($filasecund = $this->conexionencab->ObtenerSiguienteRegistro($menusecund)) {
                                ?>
                                <li>
                                    <a href="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filasecund['archivonom'], ENT_QUOTES) ?>"
                                       title="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filasecund['TextoMenu'], ENT_QUOTES) ?>">
                                        <?php if ($filasecund['UbicacionImagen'] != "") { ?>
                                            <i class="fa <?php echo $filasecund['UbicacionImagen'] ?>"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-caret-right"></i>
                                        <?php } ?>
                                        <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($filasecund['TextoMenu'], ENT_QUOTES) ?>
                                    </a>
                                </li>
                                <?php

                            }
                            ?>
                        </ul>
                    </li>
                    <?php
                }
                $i++;
            }
            ?>
        </ul>
        <?php $this->TraerAlertas(); ?>
        <?php
        return null;
    }

    function TraerAlertas() {

        $datos = [];
        $datos['IdRol'] = implode($_SESSION['rolcod'], ",");
        $oAlertas = new cAlertas($this->conexionencab, "");
        if (!$oAlertas->BusquedaAvanzada($datos, $resultado, $numfilas))
            return false;

        $total = $oAlertas->ObtenerCantidadMsgNoLeidos();


        ?>
        <ul class="header-notifications">
            <li class="dropdown">
                <a href="#" class="message msgAlertas"><i class="far fa-bell"></i></a>
                <?php if ($total > 0) { ?>
                    <span class="lbl" id="totalMsgNoLeidos"><?php echo $total ?></span>
                <?php } ?>
                <ul class="dropdown-menu message-dropdown">
                    <?php
                    $totalRestante = $total;
                    $cantMsgLeido = $cantALeer = 0;
                    while ($fila = $this->conexionencab->ObtenerSiguienteRegistro($resultado)) {
                        if (boolval($fila['Leida']))
                            $cantMsgLeido++;
                        else {
                            $cantALeer++;
                            $totalRestante--;
                        }

                        if ($cantALeer < 0) {
                            $cantALeer = 0;
                        }
                        ?>
                        <li class="message-preview <?php if (!boolval($fila['Leida'])) echo "msgnoleido" ?>">
                            <a href="alerta_leer.php?IdAlerta=<?php echo $fila['IdAlerta'];//javascript:void(0)
                            ?>" data-id="<?php echo $fila['IdAlerta'] ?>" data-msgleido="<?php echo $fila['Leida'] ?>" class="alertascod">
                                <div class="msg">
                                    <i style="color:<?php echo $fila['Color'] ?>" class="fa fa-paper-plane-o" aria-hidden="true"></i>
                                    <?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($fila['IdAlertaTipodesc'], ENT_QUOTES) ?>
                                    <div class="hours"><?php echo date("d/m/Y H:i", strtotime($fila['FechaAlta'])) ?></div>
                                </div>
                            </a>
                        </li>
                    <?php }
                    ?>
                    <li class="message-footer">
                        <a id="totalesmsg" href="/alertas" data-totalmsgnoleidos="<?php echo $total ?>" data-msgleidos="<?php echo $cantALeer ?>"
                           title="Ver todas las alertas" data-msgnoleidos="<?= $totalRestante ?>"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>&nbsp;Ver
                            todas las alertas<?php if ($totalRestante > 0) { ?><span class="lbl"><?php echo $totalRestante ?></span><?php } ?></a>
                    </li>
                </ul>

            </li>
        </ul>
        <?php
        return null;
    }

function EncabezadoConsulta() {
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title></title>
        <link rel="stylesheet" type="text/css" href="css/estilos.css">
        <script src="js/funcionesjs.js" type="text/javascript"></script>
    </head>
    <body>
    <?php

    }

    function PieConsulta()
    {
    ?>

    <hr class="noscreen"/>

    <!-- Footer -->
    <div id="footer" class="box">


    </div> <!-- /footer -->

    </div> <!-- /main -->

    </body>
    </html>
    <?php
}

    function ModalUniversal() {
//        $this->addScript('https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js');
        ?>
        <script src="/modulos/modal_universal/js/modal_universal.js?v=1.1"></script>

        <div id="ModalUniversal" class="modal fade">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header universal-header"></div>
                    <div class="modal-body universal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;Cerrar</button>
                        <!--                        <button type="button" class="btn btn-secondary close" data-dismiss="modal" aria-hidden="true">Cerrar</button>-->
                    </div>
                </div>
            </div>
        </div>
    <?php }


}//fin clase
?>
