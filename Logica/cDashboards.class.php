<?php
include(DIR_CLASES_DB . "cDashboards.db.php");

//include_once ('modulos/dashboards/dashboards.css');

class cDashboards extends cDashboardsDB {



    function __construct($conexion, $formato = FMT_TEXTO, ?Elastic\Conexion $conexionES = NULL) {
        parent::__construct($conexion, $formato);
        $this->conexionES = &$conexionES;
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private static function armarLinkNovedad(string $id): string {
        FuncionesPHPLocal::ArmarLinkMD5('novedades_am.php', array('IdDocumento' => $id), $get, $md5);
        return sprintf("/novedades/%s/%s", $id, $md5);
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private static function armarLinkSolicitudCobertura(array $r): string {

        if ($r['IdTipoDocumento'] == NOV_SC_VACANTE) {
            return '/solicitudes/cobertura/vacante/' . $r['Id'];
        } else {
            return '/solicitudes-cobertura/' . $r['Id'];
        }
    }

    function __destruct() {
        parent::__destruct();
    }

    public function armarDatosPersonales() {

        require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cServiciosUsuario.class.php');
        $oObjeto = new cServiciosUsuario($this->conexion);
        $usuario = $oObjeto->getUsuario();
        $Avatar = $this->buscarAvatar($usuario); ?>

        <style>
            .avatar, .card-perfil {
                display: block;
            }

            @media (max-width: 1450px) {
                .avatar {
                    display: none;
                }
            }

            @media (max-width: 1099px) {
                .card-perfil {
                    display: none;
                }
            }
        </style>

        <div class="card w-100 card-perfil">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 avatar" align="center">
                        <div class="roundHome">
                            <?= $Avatar?>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <h3 class="mt-3"><?= $usuario['NombreCompleto']; ?></h3>
                        <div class="row">
                            <div class="col-md-6">
                                <small>CUIL: <?= $usuario['Cuil'] ?? ''; ?></small>
                            </div>
                            <div class="col-md-6">
                                <small>DNI: <?= $usuario['Dni'] ?? ''; ?></small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <small>MAIL: <?= $usuario['Email'] ?? ''; ?></small>
                            </div>
                            <div class="col-md-6">
                                <small>TEL.: <?= $usuario['Telefono'] ?? ''; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }




    public function armarNovedadesReubicacion( array $datosBuscar): bool
    {

        $oNovedades = new Elastic\Novedades($this->conexionES);

        $datosBuscar['TipoDocumento'] = NOVEDAD_REUBICACION;


        if (!$oNovedades->BuscarDashboardEquipoConduccion($datosBuscar, $resultado, $aggs, $numfilas, $total))
        {
            $err = $oNovedades->getError('error_description');
            echo $err;
            return false;
        }

    ?>
        <div class="card w-100">
            <div class="card-body ">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Novedades de Reubicaci&oacute;n</h3>
                        <?php if ($numfilas > 0) { ?>
                        <div class="table-responsive">
                            <table class="table stylish-table font-14">
                                <thead>
                                <tr>
                                    <th style="color: #354476!important;">Agente</th>
                                    <th style="color: #354476!important; text-align: center">Tipo</th>
                                    <th style="color: #354476!important; text-align: center">Desde</th>
                                    <th style="color: #354476!important; text-align: center">Estado</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <?php foreach ($resultado as $r) {
                                    $s = $r['_source'];
                                    $linkEdit = self::armarLinkNovedad($s['Id']);
                                    $Tipo = $this->titularTipo($s['TipoDocumento']['Id']);
                                    $Estado = $this->armarBadgeEstado($s['Estado']['Id'], $s['Estado']['Nombre']); ?>
                                    <tbody>
                                    <tr>
                                        <td><?= $s['Agente']['NombreCompleto']; ?><br><?= $s['Agente']['Dni'] ?></td>
                                        <td class="text-center"><?= $Tipo; ?></td>
                                        <td class="text-center"><?= FuncionesPHPLocal::ConvertirFecha($s['Periodo']['FechaDesde'], 'aaaa-mm-dd', 'dd/mm/aaaa'); ?></td>
                                        <td class="text-center"><?= $Estado; ?></td>
                                        <td class="text-center"><a href="<?= $linkEdit; ?>"><i class="far fa-file-alt fa-lg"
                                                                                               style="color: #354476!important;"></i></a></td>
                                    </tr>
                                    </tbody>
                                <?php } ?>
                            </table>
                        <?php } else { ?>
                            <div align="center">
                                <img src="/assets/images/dashboards/no_hay_novedades.png" width="300px">
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php return true;
}






    public function armarNovedadesCese(string $nivel, array $datosBuscar): bool {

        $oNovedades = new Elastic\Novedades($this->conexionES);
        switch ($nivel) {
            case NIVEL_PRIMARIO:
                $datosBuscar['TipoDocumento'] = [
                    NOVEDAD_CESE_FALLECIMIENTO_PRIMARIA,
                    NOVEDAD_CESE_JUBILACION,
                    NOVEDAD_CESE_RENUNCIA_TITULAR_PRIMARIA,
                    NOVEDAD_CESE_RENUNCIA_SUPLENTE_PRIMARIA
                ];
                break;
            case NIVEL_SECUNDARIO:
                $datosBuscar['TipoDocumento'] = [
                    NOVEDAD_CESE_FALLECIMIENTO_SECUNDARIA,
                    NOVEDAD_CESE_JUBILACION,
                    NOVEDAD_CESE_RENUNCIA_TITULAR_SECUNDARIO,
                    NOVEDAD_CESE_RENUNCIA_SUPLENTE_SECUNDARIO
                ];
                break;
            default:
                break;
        }

        if (!$oNovedades->BuscarDashboardEquipoConduccion($datosBuscar, $resultado, $aggs, $numfilas, $total))
            return false;
        ?>

        <div class="card w-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Novedades de Cese</h3>
                        <?php if ($numfilas > 0) { ?>
                            <table class="table stylish-table font-14">
                                <thead>
                                <tr>
                                    <th style="color: #354476!important;">Agente</th>
                                    <th style="color: #354476!important; text-align: center">Tipo</th>
                                    <th style="color: #354476!important; text-align: center">Desde</th>
                                    <th style="color: #354476!important; text-align: center">Estado</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <?php foreach ($resultado as $r) {
                                    $s = $r['_source'];
                                    $linkEdit = self::armarLinkNovedad($s['Id']);
                                    $Tipo = $this->titularTipo($s['TipoDocumento']['Id']);
                                    $Estado = $this->armarBadgeEstado($s['Estado']['Id'], $s['Estado']['Nombre']); ?>
                                    <tbody>
                                    <tr>
                                        <td><?= $s['Agente']['NombreCompleto']; ?><br><?= $s['Agente']['Dni'] ?></td>
                                        <td class="text-center"><?= $Tipo; ?></td>
                                        <td class="text-center"><?= FuncionesPHPLocal::ConvertirFecha($s['Periodo']['FechaDesde'], 'aaaa-mm-dd', 'dd/mm/aaaa'); ?></td>
                                        <td class="text-center"><?= $Estado; ?></td>
                                        <td class="text-center"><a href="<?= $linkEdit; ?>"><i class="far fa-file-alt fa-lg"
                                                                                               style="color: #354476!important;"></i></a></td>
                                    </tr>
                                    </tbody>
                                <?php } ?>
                            </table>
                        <?php } else { ?>
                            <div align="center">
                                <img src="/assets/images/dashboards/no_hay_novedades.png" width="300px">
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <?php return true;
    }

    public function armarNovedadesCambioRevista(string $nivel, array $datosBuscar): bool {

        $oNovedades = new Elastic\Novedades($this->conexionES);
        switch ($nivel) {
            case NIVEL_PRIMARIO:
                $datosBuscar['TipoDocumento'] = [NOV_EST_LAB_A_TITULAR,NOV_REV_INT_A_TITULAR];
                break;
            case NIVEL_SECUNDARIO:
                $datosBuscar['TipoDocumento'] = [NOV_REV_INT_A_TITULAR,NOV_EST_LAB_A_TITULAR];
                break;
            default:
                break;
        }

        if (!$oNovedades->BuscarDashboardEquipoConduccion($datosBuscar, $resultado, $aggs, $numfilas, $total))
            return false;
        ?>

        <div class="card w-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Novedades de Cambios de Revista Interino - Titular</h3>
                        <?php if ($numfilas > 0) { ?>
                            <table class="table stylish-table font-14">
                                <thead>
                                <tr>
                                    <th style="color: #354476!important;">Agente</th>
                                    <th style="color: #354476!important; text-align: center">Desde</th>
                                    <th style="color: #354476!important; text-align: center">Estado</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <?php foreach ($resultado as $r) {
                                    $s = $r['_source'];
                                    $linkEdit = self::armarLinkNovedad($s['Id']);
                                    $Estado = $this->armarBadgeEstado($s['Estado']['Id'], $s['Estado']['Nombre']); ?>
                                    <tbody>
                                    <tr>
                                        <td><?= $s['Agente']['NombreCompleto']; ?><br><?= $s['Agente']['Dni'] ?></td>
                                        <td class="text-center"><?= FuncionesPHPLocal::ConvertirFecha($s['Periodo']['FechaDesde'], 'aaaa-mm-dd', 'dd/mm/aaaa'); ?></td>
                                        <td class="text-center"><?= $Estado; ?></td>
                                        <td class="text-center"><a href="<?= $linkEdit ?>"><i class="far fa-file-alt fa-lg"
                                                                                              style="color: #354476!important;"></i></a></td>
                                    </tr>
                                    </tbody>

                                <?php } ?>
                            </table>
                        <?php } else { ?>
                            <div align="center">
                                <img src="/assets/images/dashboards/no_hay_novedades.png" width="300px">
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <?php return true;
    }

    public function armarSolicitudesPendientes(array $datosBuscar, $titulo = 'Solicitudes pendientes', $filtrarArea = true): bool {

        $oSolicitudCobertura = new cSolicitudesCobertura($this->conexion, FMT_ARRAY);

        if ($filtrarArea)
            $datosBuscar['IdArea'] = implode(',', empty($_SESSION['IdArea']) ? ['-1'] : $_SESSION['IdArea']);

        $datosBuscar['limit'] = 'LIMIT 0, 10';

        if (!$oSolicitudCobertura->busquedaAvanzada($datosBuscar, $resultado, $numfilas))
            print_r($oSolicitudCobertura->getError());

        ?>

        <div class="card w-100">
            <div class="card-body pb-0">
                <div class="row">
                    <div class="col-md-12">
                        <h3><?= $titulo; ?></h3>
                        <?php if ($numfilas > 0) { ?>
                           <div class="table-responsive">
                               <table class="table stylish-table font-14">
                                   <thead>
                                   <tr>
                                       <!--                                    <th style="color: #354476!important;">Id</th>-->
                                       <th style="color: #354476!important; text-align: center">Cod. Escuela</th>
                                       <th style="color: #354476!important; text-align: center">Suplido</th>
                                       <th style="color: #354476!important; text-align: center">Desde</th>
                                       <th style="color: #354476!important; text-align: center">Hasta</th>
                                       <th style=" color: #354476!important; text-align: center">Estado</th>
                                       <th></th>
                                   </tr>
                                   </thead>
                                   <tbody>
                                   <?php while ($r = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                                       $linkEdit = self::armarLinkSolicitudCobertura($r);
                                       $Estado = $this->armarBadgeEstado($r['IdEstado'], utf8_encode($r['NombreEstado'])); ?>
                                       <tr>
                                           <!--                                        <td class="text-center">--><?php //= $r['Id']; ?><!--</td>-->
                                           <td class="text-center"><?= $r['CodigoEscuela']; ?></td>
                                           <td class="text-center"><?= $r['NombreCompleto']; ?><br><?= $r['DNI']; ?></td>
                                           <td class="text-center"><?= FuncionesPHPLocal::ConvertirFecha($r['FechaDesde'], 'aaaa-mm-dd', 'dd/mm/aaaa'); ?></td>
                                           <td class="text-center"><?= FuncionesPHPLocal::ConvertirFecha($r['FechaHasta'], 'aaaa-mm-dd', 'dd/mm/aaaa'); ?></td>
                                           <td class="text-center"><?= $Estado; ?></td>
                                           <td class="text-center"><a href="<?= $linkEdit ?>"><i class="far fa-file-alt fa-lg"
                                                                                                 style="color: #354476!important;"></i></a></td>
                                       </tr>
                                       <?php
                                   } ?>
                                   </tbody>
                               </table>
                           </div>
                        <?php } else { ?>
                            <div align="center">
                                <img src="/assets/images/dashboards/no_hay_solicitudes.png" width="300px">
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <?php return true;
    }

    public function armarEscuelasAsignadas($datosBuscar): bool {

         $oEscuelas = new cEscuelas($this->conexion);
         if(isset($datosBuscar['IdEscuela']) && is_array($datosBuscar['IdEscuela']))
                 $datos['IdEscuela'] = implode(",",$datosBuscar['IdEscuela']);
         else
               $datos['IdEscuela'] = $datosBuscar['IdsEscuela'] ?? null;


         $datos['limit'] = 'LIMIT 0, 5';
         if (!$oEscuelas->BusquedaAvanzada($datos, $resultado, $numfilas)) {
             $this->setError($oEscuelas->getError());
             return false;
         } ?>
        <div class="card  w-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Escuelas asignadas</h3>
                        <?php if ($numfilas > 0) { ?>
                            <table class="table stylish-table font-14">
                                <thead>
                                <tr>
                                    <th style="color: #354476!important;">Cod.</th>
                                    <th style="color: #354476!important;">Nombre</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while ($r = $this->conexion->ObtenerSiguienteRegistro($resultado)) { ?>
                                    <tr>
                                        <td><?= $r['CodigoEscuela']; ?></td>
                                        <td><?= $r['Nombre']; ?></td>
                                    </tr>
                                    <?php
                                } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div align="center">
                                <img src="/assets/images/dashboards/no_existen_escuelas.png" width="300px">
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <?php return true;
    }

    public function buscarAvatar($usuario = null): string {

        if (empty($usuario)) {
            require_once(DIR_CLASES_LOGICA_SERVICIOS . 'cServiciosUsuario.class.php');
            $oObjeto = new cServiciosUsuario($this->conexion);
            $usuario = $oObjeto->getUsuario();
        }

        $Avatar = $usuario['Avatar'];
        $TieneAvatar = 0;
        if (!FuncionesPHPLocal::isEmpty($Avatar))
        {
            $avatarData = $Avatar;
            $Avatar = $_SESSION['usuario_avatar'];
            $TieneAvatar = 1;
        }else
        {
            $avatarData = file_get_contents(PATH_STORAGE.'avatars/avatar-l/default.png');
            $Avatar = "data:image/jpeg;base64,".base64_encode($avatarData);
            $Avatar = '<img src="'.$Avatar.'" class="img-circle" width="100" />';
        }

        return $Avatar;
    }

    public function titularTipo($tipoDocumento): string {

        switch ($tipoDocumento) {

            case NOVEDAD_CESE_JUBILACION:
                $titulo = 'Jubilaci&oacute;n';
                break;
            case NOVEDAD_CESE_FALLECIMIENTO_PRIMARIA:
            case NOVEDAD_CESE_FALLECIMIENTO_SECUNDARIA:
                $titulo = 'Fallecimiento';
                break;
            case NOVEDAD_CESE_RENUNCIA_TITULAR_PRIMARIA:
            case NOVEDAD_CESE_RENUNCIA_SUPLENTE_PRIMARIA:
            case NOVEDAD_CESE_RENUNCIA_TITULAR_SECUNDARIO:
            case NOVEDAD_CESE_RENUNCIA_SUPLENTE_SECUNDARIO:
                $titulo = 'Renuncia';
                break;
            default:
                $titulo = '-';
                break;
        }

        return $titulo;
    }

    public function armarBadgeEstado($id, $nombre): string {

        switch ($id) {
            case NOVEDAD_ESTADO_AUTORIZADO:
                $color = '#55CE636E';
                break;
            case NOVEDAD_ESTADO_NO_AUTORIZADO:
                $color = '#FFB792';
                break;
            CASE NOVEDAD_ESTADO_PENDIENTE_DE_APROBACION:
			case SC_PENDIENTES_DE_RECTIFICACION:
			case SC_PENDIENTES:
				$color = '#F8B711';
				break;
            case NOVEDAD_ESTADO_PENDIENTE_VERIFICACION_DP:
            case NOVEDAD_ESTADO_NUEVO:
            default:
                $color = '#57BFF49E';
                break;
        }

        return '<span class="badge" style="color: #354476; background-color: ' . $color . '; font-size: 14px; width: 100px; white-space: normal!important;">' . utf8_decode($nombre) . '</span>';
    }

    /**
     * ### Agrega los campos de busqueada correspondientes por tipo de acceso
     *
     * @param array|null $datos
     *
     * @return array
     */
    public function buscarxTipoAcceso(?array $datos = null): array {

        $datos = $datos ?? [];
        switch ($_SESSION['TipoAcceso']) {
            case 1:
                # POR REGION
                $tmp = [];
                foreach ($_SESSION['Regiones'] as $key => $r) {

                    foreach ($r['Niveles'] as $n) {
                        $tmp[$key]['Region'] = (int)$key;
                        $tmp[$key]['Nivel'] = (int)$n['IdNivel'];
                        $tmp[$key]['Turno'] = (int)$n['IdTurno'];
                    }
                }
                $datos['filtarxRegionxNivelxTurno'] = array_values($tmp);
                break;
            case 2:
                # POR ESCUELA
                $tmp = [];
                $datos['IdEscuela'] = FuncionesPHPLocal::DevolverIdEscuela($_SESSION['IdEscuela'],true);

                foreach ($_SESSION['Niveles'] as $key => $n) {

                    $tmp[$key]['Nivel'] = (int)$n['IdNivel'];
                    $tmp[$key]['Turno'] = (int)$n['IdTurno'];
                }
                $datos['filtarxEscuelaxNivelxTurno'] = array_values($tmp);
                break;
            case 3:
                # POR NIVEL
               /* $datos['IdNivel'] = (int)$_SESSION['Nivel']['Id'];
                if ($_SESSION['Nivel']['Escuelas'][0] != 0) {
                    //$datos['IdsEscuela'] = $_SESSION['Nivel']['Escuelas'];
                    $datos['IdsEscuela'] = array_map('intval', $_SESSION['Nivel']['Escuelas']);
                }*/
                $datos['IdNivel'] = $_SESSION['Nivel']['Id'];
                break;
            case 5:
                $datos['IdEscuela'] = FuncionesPHPLocal::DevolverIdEscuela($_SESSION['IdEscuela'],true);
                break;
            default:
                break;
        }

        return $datos;
    }

    public function buscarxTipoAccesoSupNivel($datosBuscar,?array $datos = null): array {

        $datos = $datos ?? [];

                # POR REGION X NIVEL
                $tmp = [];
                foreach ($_SESSION['Regiones'] as $key => $r) {

                    foreach ($r['Niveles'] as $n) {
                        $tmp[$key]['Region'] = (int)$key;
                        $tmp[$key]['Nivel'] = $datosBuscar['IdNivel'];
                        $tmp[$key]['Turno'] = (int)$n['IdTurno'];
                    }
                }
                $datos['filtarxRegionxNivelxTurno'] = array_values($tmp);

        return $datos;
    }

    public function armarCajaLicencias(array $datosBuscar, string $nombreTipoLicencia): void {
        $oLicencias = new Elastic\Licencias($this->conexionES);
        $datosBuscar['size'] = 10;
        $datosBuscar['camposMostrar'] = ['Id', 'Persona.NombreCompleto', 'Persona.Dni', 'Estado', 'Cargos', 'Inicio', 'Fin', 'Duracion', 'Tipo.Nombre'];
        if (!array_key_exists('IdEstadoPendientes', $datosBuscar))
            $datosBuscar['IdEstadoPendientes'] = [2];
        if (!$oLicencias->cantidadDashboardReconocimientoMedico($datosBuscar, $licencias)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$oLicencias->getError('error_description'), ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => FMT_TEXTO]);
            return;
        }

        switch ($datosBuscar['IdTipo']) {
            case TIPO_ART:
                $href = '/licencias/art/revision/';
                $archivo = 'lic_licencias_administracion_art_am.php';
                break;
            case TIPO_ADMIN:
                $href = '/licencias/administrativas/revision/';
				$archivo = 'lic_licencias_administracion_administrativas_am.php';
                break;
			case TIPO_MATERNIDAD:
				$href = '/licencias/maternidad/revision/';
				$archivo = 'lic_licencias_administracion_mat_am.php';
				break;
			case TIPO_MEDICA:
				$href = '/licencias/medicas/revision/';
				$archivo = 'lic_licencias_administracion_am.php';
				break;
			case TIPO_INASISTENCIAS:
				$href = '/licencias/inasistencias/revision/';
				$archivo = 'lic_licencias_administracion_inasistencias_am.php';
				break;
            default:
				$archivo='';
                $href = '';
                break;
        }



		$total = $licencias['hits']['total']['value']; ?>
            <div class='card w-100'>
                <div class='card-body'>
                    <h3><?= $nombreTipoLicencia;?></h3>
                    <?php if ($total > 0) { ?>
                        <table class='table stylish-table font-14'>
                            <thead>
                            <tr>
                                <th style='text-align: center'>Id</th>
                                <th style="text-align: center">Nombre</th>
                                <th style='text-align: center'>Desde</th>
                                <th style='text-align: center'>Hasta</th>
                                <th style='text-align: center'></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($licencias['hits']['hits'] as ['_source' => $dataLicencia ]) {
								FuncionesPHPLocal::ArmarLinkMD5($archivo, ['Id' => $dataLicencia['Id']], $get, $md5);
                                ?>
                                <tr>
                                    <td style="text-align: center"><?= $dataLicencia['Id']; ?></td>
                                    <td style="text-align: center"><b><?= FuncionesPHPLocal::HtmlspecialcharsSistema(utf8_decode(strtoupper($dataLicencia['Persona']['NombreCompleto'])), ENT_QUOTES); ?></b>
                                        <br><small><?= $dataLicencia['Persona']['Dni'] ?? ''; ?></small>
                                    </td>
                                    <td style="text-align: center"><?= FuncionesPHPLocal::ConvertirFecha($dataLicencia['Inicio'], 'aaaa-mm-dd', 'dd/mm/aaaa'); ?></td>
                                    <td style="text-align: center"><?= FuncionesPHPLocal::ConvertirFecha($dataLicencia['Fin'], 'aaaa-mm-dd', 'dd/mm/aaaa') ?? '-'; ?></td>
                                    <td style="text-align: center"><a href="<?= $href.$dataLicencia['Id']. '/'. $md5 ; ?>"><i class="far fa-file-alt"></i></a></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <div align="center">
                            <img src="/assets/images/dashboards/no_hay_licencias.png" width="300px">
                        </div>
                    <?php } ?>
                </div>
            </div>
<?php
    }

    public function armarCajaLicenciasRectificacion(array $datosBuscar, string $nombreTipoLicencia): void {
        $oLicencias = new Elastic\Licencias($this->conexionES);
        $datosBuscar['size'] = 10;
        $datosBuscar['camposMostrar'] = ['Id', 'Persona.NombreCompleto', 'Persona.Dni', 'Estado', 'Cargos', 'Inicio', 'Fin', 'Duracion', 'Tipo.Nombre', 'Tipo.Id'];
        if (!array_key_exists('IdEstadoPendientes', $datosBuscar))
            $datosBuscar['IdEstadoPendientes'] = [2];
        if (!$oLicencias->cantidadDashboardReconocimientoMedico($datosBuscar, $licencias)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$oLicencias->getError('error_description'), ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => FMT_TEXTO]);
            return;
        }
		$href = '/licencias/';
		$archivo = 'lic_licencias_am.php';
		$total = $licencias['hits']['total']['value']; ?>
            <div class='card w-100'>
                <div class='card-body'>
                    <h3><?= $nombreTipoLicencia;?></h3>
                    <?php if ($total > 0) { ?>
                        <table class='table stylish-table font-14'>
                            <thead>
                            <tr>
                                <th style='text-align: center'>Id</th>
                                <th style="text-align: center">Nombre</th>
                                <th style='text-align: center'>Desde</th>
                                <th style='text-align: center'>Hasta</th>
                                <th style='text-align: center'></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($licencias['hits']['hits'] as ['_source' => $dataLicencia ]) {

								FuncionesPHPLocal::ArmarLinkMD5($archivo, ['Id' => $dataLicencia['Id']], $get, $md5);
                                ?>
                                <tr>
                                    <td style="text-align: center"><?= $dataLicencia['Id']; ?></td>
                                    <td style="text-align: center"><b><?= FuncionesPHPLocal::HtmlspecialcharsSistema(utf8_decode(strtoupper($dataLicencia['Persona']['NombreCompleto'])), ENT_QUOTES); ?></b>
                                        <br><small><?= $dataLicencia['Persona']['Dni'] ?? ''; ?></small>
                                    </td>
                                    <td style="text-align: center"><?= FuncionesPHPLocal::ConvertirFecha($dataLicencia['Inicio'], 'aaaa-mm-dd', 'dd/mm/aaaa'); ?></td>
                                    <td style="text-align: center"><?= FuncionesPHPLocal::ConvertirFecha($dataLicencia['Fin'], 'aaaa-mm-dd', 'dd/mm/aaaa') ?? '-'; ?></td>
                                    <td style="text-align: center"><a href="<?= $href.$dataLicencia['Id']. '/'. $md5 ; ?>"><i class="far fa-file-alt"></i></a></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <div align="center">
                            <img src="/assets/images/dashboards/no_hay_licencias.png" width="300px">
                        </div>
                    <?php } ?>
                </div>
            </div>
<?php
    }
}
