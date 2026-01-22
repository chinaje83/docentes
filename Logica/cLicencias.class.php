<?php
include(DIR_CLASES_DB."cLicencias.db.php");

class cLicencias extends cLicenciasdb
{

	protected $conexion;
	protected $formato;
    protected $conexionES;
    const FACTOR_ESCALA = 100;

    function __construct(accesoBDLocal $conexion,$formato=FMT_TEXTO){
        parent::__construct($conexion,$formato);
        $this->error = [];
    }
    function setConexionES($conexionES)
    {
        $this->conexionES = $conexionES;
    }

	function __destruct(){parent::__destruct();}


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xTaskId'=> 0,
			'TaskId'=> "",
			'limit'=> '',
			'orderby'=> "IdRegistro DESC"
		);

		if(isset($datos['TaskId']) && $datos['TaskId']!="")
		{
			$sparam['TaskId']= $datos['TaskId'];
			$sparam['xTaskId']= 1;
		}

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}

    public function BuscarLicenciasAfectadasxIdPofa($datos,&$resultado,&$numfilas)
    {
        $sparam=array(
            'IdPofa'=> $datos["IdPofa"]
        );

        if (!parent::BuscarLicenciasAfectadasxIdPofa($sparam,$resultado,$numfilas))
            return false;
        return true;
    }

    public function InactivarLicenciasCargosxIdPofa($datos)
    {
        $sparam=array(
            'IdPofa'=> $datos["IdPofa"],
            'IdLicencias'=> $datos["IdLicencias"],
            'UltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'UltimaModificacionFecha'=>date("Y-m-d H:i:s"),
            'BajaFecha'=>date("Y-m-d H:i:s")
        );

        if (!parent::InactivarLicenciasCargosxIdPofa($sparam))
            return false;
        return true;
    }

    public function InsertarLicenciasCargos($datos, &$codigoinsertado)
    {
        $sparam=array(
            'pIdPofaNueva'=> $datos["pIdPofaNueva"],
            'IdPofa'=> $datos["IdPofa"],
            'IdLicencias'=> $datos["IdLicencias"]
        );

        if (!parent::InsertarLicenciasCargos($sparam,$codigoinsertado))
            return false;
        return true;
    }
    public function RepublicarLicenciaElastic($datos, $conexionES)
    {
        $this->setConexionES($conexionES);
        $oElastic = new Elastic\Modificacion("licencias", $this->conexionES);
        $Certificados=$this->BuscarCertificados($datos);
        $Cargos=$this->BuscarCargos($datos);
        $Juntas=$this->BuscarJuntas($datos);
        if (!$this->BuscarLicenciasESPorId($datos,$resultado,$numfilas))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar licencia para Elastic", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        $bulkData = '';
        $ii = 0;
        $jj = 1;
        $cantBulk = 1;
        $Action_and_MetaData = new StdClass;
        $Action_and_MetaData->index = new StdClass;
        $Action_and_MetaData->index->_index = INDEXPREFIX . "licencias";
        $exitCode = 255;
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $fila['Certificados'] = $Certificados[$fila['Id']] ?? [];
            $fila['Cargos'] = $Cargos[$fila['Id']] ?? [];
            $fila['Juntas'] = $Juntas[$fila['Id']] ?? [];

            $Action_and_MetaData->index->_id = (int) $fila['Id'];
            $bulkData .= json_encode($Action_and_MetaData) . "\n";
            $bulkData .= $this->armarDatosElastic($fila, true) . "\n";

            if (++$ii > $bulkSize) {
                //echo $bulkData . PHP_EOL . PHP_EOL;
                if (!$oElastic->ActualizarBulk($bulkData))
                    die(print_r($this->conexionES->getError(), true));

                ++$jj;
                $bulkData = '';
                $ii = 0;
            }
        }
        if (!empty($bulkData)) {
            if (!$oElastic->ActualizarBulk($bulkData)) {
                print_r($this->conexionES->getError());
                die('exitCode');
            }
        }
        return true;
    }

    public function BuscarCertificados($datos)
    {
        $bdLicencias=BASEDATOSLICENCIAS;
        $sql = /** @lang MariaDB */
            <<<MySQL
                SELECT Id,
                       IdLicencia,
                       Nombre,
                       Ubicacion,
                       Tamanio,
                       TipoMIME
                FROM {$bdLicencias}.Certificados
                WHERE Estado = 10
                AND IdLicencia={$datos['IdLicencia']}
                MySQL;

        $this->conexion->ejecutarSQL($sql, "SEL", $resultado, $numfilas, $error);

        $Certificados = [];
        if ($numfilas>0)
        {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                $Certificados[$fila['IdLicencia']][] = $fila;
            }
        }

        return $Certificados;
    }

    public function BuscarCargos($datos)
    {
        $bdLicencias=BASEDATOSLICENCIAS;
        $sql = /** @lang MariaDB */
            <<<MySQL
                SELECT 	LC.*,
                        EP.CodigoPuesto AS Cupof,
                        R.Codigo      	AS CodigoRevista,
                        R.Descripcion 	AS DescripcionRevista,
                        E.CodigoEscuela,
                        N.Nombre 	AS NivelNombre,
                        T.Descripcion 	AS NombreTurno,
                        T.Turno       	AS NombreTurnoCorto,
                        EAS.NombreSeccion,
                        GA.Nombre 	AS AnioNombre,
                        GA.NombreCorto 	AS AnioNombreCorto,
                        EP.CantHoras,
                        EP.CantModulos,
                        T.IdTurno 	AS IdTurnos,
                        T.Turno		AS NombreTurnos,
                        N.IdNivel	AS IdNiveles,
                        N.Nombre	AS NombreNiveles,
                        A.Codigo	AS CodigoArticulo,
                        A.Descripcion	AS DescripcionArticulo,
                        A.CantidadMaximaDias,
                        A.ConGoceSueldo,
                        A.PermiteOtroOrganismo,
                        A.EsAnual,
                        A.EsAuxiliar,
                        PUA.NombreCompleto   AS AltaUsuarioNombre,
                        PUM.NombreCompleto   AS UltimaModificacionUsuarioNombre,
                        E.IdLocalidad,
                        LOC.Nombre              AS LocalidadNombre,
                        LC.Estado          AS EstadoLicenciaCargo
                FROM {$bdLicencias}.LicenciasCargos LC
                         LEFT JOIN Escuelas E ON E.IdEscuela = LC.IdEscuela
                         LEFT JOIN EscuelasPuestos EP ON EP.IdPuesto = LC.IdPuesto
                         LEFT JOIN  EscuelasAGSecciones EAS ON EAS.IdSeccion = EP.IdSeccion
                         LEFT JOIN EscuelasTurnos ET ON ET.IdEscuelaTurno = LC.IdEscuelaTurno
                         LEFT JOIN Turnos T ON T.IdTurno = ET.IdTurno
                         LEFT JOIN EscuelasNivelModalidad ENM ON ENM.Id = ET.IdNivelModalidad
                         LEFT JOIN Niveles N ON N.IdNivel = ENM.IdNivel
                         LEFT JOIN  EscuelasTurnosAG ETA ON ETA.IdEscuelaTurnoAnioGrado = EAS.IdEscuelaTurnoAnioGrado
                         LEFT JOIN  GradosAnios GA ON GA.IdGradoAnio = ETA.IdGradoAnio
                         LEFT JOIN Revistas R ON R.IdRevista = LC.IdRevista
                         LEFT JOIN  Articulos A ON LC.IdArticulo = A.IdArticulo
                         INNER JOIN Usuarios UA ON UA.IdUsuario = LC.AltaUsuario
                         INNER JOIN Personas PUA ON PUA.IdPersona = UA.IdPersona
                         INNER JOIN Usuarios UM ON UM.IdUsuario = LC.UltimaModificacionUsuario
                         INNER JOIN Personas PUM ON PUM.IdPersona = UA.IdPersona
                         LEFT JOIN  Localidades LOC ON LOC.IdLocalidad = LC.IdLocalidad
                        WHERE LC.IdLicencia={$datos['IdLicencia']}
                        and LC.Estado=10
                MySQL;

        $this->conexion->ejecutarSQL($sql, "SEL", $resultado, $numfilas, $error);

        $Cargos = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $Cargos[$fila['IdLicencia']][] = $fila;
        }

        return $Cargos;
    }


    public function BuscarJuntas($datos)
    {
        $bdLicencias=BASEDATOSLICENCIAS;
        $sql = /** @lang MariaDB */
            <<<MySQL
                SELECT  J.IdLicencia,
                            J.Id AS IdJunta,
                            J.Fecha AS FechaJunta,
                            J.Comentarios,
                            J.IdTipoJunta,
                            JT.Descripcion AS NombreTipoJunta,
                            J.IdMotivoJunta,
                            JM.Descripcion AS NombreMotivoJunta,
                            J.IdRegion AS IdRegionJunta,
                            R.Nombre AS NombreRegionJunta,
                            J.IdEstado AS IdEstadoJunta,
                            JE.Nombre AS NombreEstadoJunta,
                            J.AltaFecha AS AltaFechaJunta,
                            J.AltaUsuario AS AltaUsuarioJunta,
                            PA.NombreCompleto AS AltaUsuarioNombreJunta,
                            J.UltimaModificacionFecha AS UltimaModificacionFechaJunta,
                            J.UltimaModificacionUsuario AS UltimaModificacionUsuarioJunta,
                            PM.NombreCompleto AS UltimaModificacionUsuarioNombreJunta,
                            J.IdAutorizante AS IdAutorizanteJunta,
                            A.Nombre AS NombreAutorizanteJunta,
                            T.Nombre AS TipoAutorizanteJunta,
                            J.FechaJunta AS FechaJuntaResultado,
                            J.Integracion
                    FROM {$bdLicencias}.Juntas J
                    JOIN {$bdLicencias}.JuntasTipos JT ON J.IdTipoJunta = JT.Id
                    JOIN {$bdLicencias}.JuntasMotivos JM ON J.IdMotivoJunta = JM.Id
                    JOIN Regiones R ON R.IdRegion = J.IdRegion
                    JOIN {$bdLicencias}.JuntasEstados JE on J.IdEstado = JE.Id
                    JOIN Usuarios UA ON J.AltaUsuario = UA.IdUsuario
                    JOIN Personas PA ON UA.IdPersona = PA.IdPersona
                    JOIN Usuarios UM ON J.AltaUsuario = UM.IdUsuario
                    JOIN Personas PM ON UM.IdPersona = PM.IdPersona
                    LEFT JOIN {$bdLicencias}.Autorizantes A ON A.Id = J.IdAutorizante
                    LEFT JOIN {$bdLicencias}.AutorizantesTipos T on A.IdTipoAutorizante = T.Id
                    WHERE J.IdLicencia={$datos['IdLicencia']}
                MySQL;

        $this->conexion->ejecutarSQL($sql, "SEL", $resultado, $numfilas, $error);

        $Juntas = [];
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $Juntas[$fila['IdLicencia']][] = $fila;
        }

        return $Juntas;
    }

    public function BuscarLicenciasESPorId($datos,&$resultado,&$numfilas)
    {
        $sparam=array(
            'Id' =>$datos['IdLicencia']
        );

        if (!parent::BuscarLicenciasESPorId($sparam,$resultado,$numfilas))
            return false;

        return true;
    }

    public static function armarDatosElastic(array $datos, $encode=false) {
        $jsonData = new stdClass();

        if(!FuncionesPHPLocal::isEmpty($datos['Id']))
            $jsonData->Id = (int) $datos['Id'];

        $jsonData->Tipo = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['IdTipo']))
            $jsonData->Tipo->Id = (int) $datos['IdTipo'];
        if(!FuncionesPHPLocal::isEmpty($datos['TipoNombre']))
            $jsonData->Tipo->Nombre = $datos['TipoNombre'];
        if(!FuncionesPHPLocal::isEmpty($datos['Prioridad']))
            $jsonData->Tipo->Prioridad = (int) $datos['Prioridad'];


        $jsonData->Persona = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['IdPersona']))
            $jsonData->Persona->Id = (int) $datos['IdPersona'];
        if(!FuncionesPHPLocal::isEmpty($datos['Nombre']))
            $jsonData->Persona->Nombre = $datos['Nombre'];
        if(!FuncionesPHPLocal::isEmpty($datos['Apellido']))
            $jsonData->Persona->Apellido = $datos['Apellido'];
        if(!FuncionesPHPLocal::isEmpty($datos['NombreCompleto']))
            $jsonData->Persona->NombreCompleto =$datos['NombreCompleto'];
        if(!FuncionesPHPLocal::isEmpty($datos['Dni']))
            $jsonData->Persona->Dni = $datos['Dni'];
        if(!FuncionesPHPLocal::isEmpty($datos['Cuil']))
            $jsonData->Persona->Cuil = $datos['Cuil'];
        $jsonData->Persona->Sexo = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['Sexo'])) {
            $jsonData->Persona->Sexo->Id = (int) $datos['Sexo'];
            $jsonData->Persona->Sexo->Nombre = self::obtenerSexo($datos['Sexo']);
        }


        $jsonData->Estado = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['IdEstado']))
            $jsonData->Estado->Id = (int) $datos['IdEstado'];
        if(!FuncionesPHPLocal::isEmpty($datos['EstadoNombre']))
            $jsonData->Estado->Nombre = $datos['EstadoNombre'];
        if(!FuncionesPHPLocal::isEmpty($datos['EstadoNombrePublico']))
            $jsonData->Estado->NombrePublico = $datos['EstadoNombrePublico'];
        if(!FuncionesPHPLocal::isEmpty($datos['EstadoNombrePublicoTmp']))
            $jsonData->Estado->NombrePublicoTmp = $datos['EstadoNombrePublicoTmp'];
        if(!FuncionesPHPLocal::isEmpty($datos['CantHorasEstadoTmp'])) {
            $jsonData->Estado->MostrarTmpHasta = FuncionesPHPLocal::isEmpty($datos['FechaEnvio']) ? time():
                strtotime("{$datos['FechaEnvio']} +{$datos['CantHorasEstadoTmp']} hours");
        }
        $jsonData->Estado->Editable = false;
        if(!FuncionesPHPLocal::isEmpty($datos['EstadoEditable']))
            $jsonData->Estado->Editable = (bool) $datos['EstadoEditable'];

        if(isset($datos['FechaFinAbierta'])){
            $jsonData->FechaFinAbierta = FuncionesPHPLocal::isEmpty($datos['FechaFinAbierta']) ? false:((bool) $datos['FechaFinAbierta']);
        }

        self::definirClasesMostrar($jsonData->Estado);
        $jsonData->Fechas = new stdClass();
        $inicio = 0;
        if(!FuncionesPHPLocal::isEmpty($datos['Inicio'])) {
            $inicio = strtotime($datos['Inicio']);
            $jsonData->Inicio = $datos['Inicio'];
            $jsonData->Fechas->gte = date("Y-m-d",$inicio);
            $jsonData->Fechas->lte = NULL;
        }
        if(!FuncionesPHPLocal::isEmpty($datos['Fin'])) {
            $fin = strtotime($datos['Fin']);
            $jsonData->Fin = $datos['Fin'];
            if($fin > $inicio)
                $jsonData->Fechas->lte = date("Y-m-d",$fin);
        }


        if(!FuncionesPHPLocal::isEmpty($datos['DuracionHabiles']))
            $jsonData->DuracionHabiles = (int) $datos['DuracionHabiles'];

        if(!FuncionesPHPLocal::isEmpty($datos['Duracion']))
            $jsonData->Duracion = (int) $datos['Duracion'];
        $jsonData->Unidad = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['Unidad']))
            $jsonData->Unidad->Id = (int) $datos['Unidad'];
        if(!FuncionesPHPLocal::isEmpty($datos['UnidadNombre']))
            $jsonData->Unidad->Nombre = $datos['UnidadNombre'];

        $jsonData->Motivo = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['IdMotivo']))
            $jsonData->Motivo->Id = (int) $datos['IdMotivo'];
        $jsonData->Motivo->Descripcion = null;
        if(!FuncionesPHPLocal::isEmpty($datos['Descripcion']))
            $jsonData->Motivo->Descripcion = $datos['Descripcion'];
        if(!FuncionesPHPLocal::isEmpty($datos['MotivoNombre']))
            $jsonData->Motivo->Nombre = $datos['MotivoNombre'];

        $jsonData->Articulo = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['IdArticuloSeleccionado']))
            $jsonData->Articulo->Id = (int) $datos['IdArticuloSeleccionado'];
        $jsonData->Articulo->Descripcion = null;
        if(!FuncionesPHPLocal::isEmpty($datos['DescripcionArticuloSeleccionado']))
            $jsonData->Articulo->Descripcion = $datos['DescripcionArticuloSeleccionado'];


        $jsonData->Diagnostico = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['IdDiagnostico']))
            $jsonData->Diagnostico->Id = (int) $datos['IdDiagnostico'];
        if(!FuncionesPHPLocal::isEmpty($datos['DiagnosticoDescripcion']))
            $jsonData->Diagnostico->Descripcion = $datos['DiagnosticoDescripcion'];
        if(!FuncionesPHPLocal::isEmpty($datos['DescripcionLicencias']))
            $jsonData->Diagnostico->Texto = $datos['DescripcionLicencias'];
        if(!FuncionesPHPLocal::isEmpty($datos['DiagnosticoNombre']))
            $jsonData->Diagnostico->Nombre = $datos['DiagnosticoNombre'];
        $jsonData->Diagnostico->Detalle = new stdClass();
        $jsonData->Diagnostico->Detalle->Id = null;
        if(!FuncionesPHPLocal::isEmpty($datos['IdDiagnosticoDetalle']))
            $jsonData->Diagnostico->Detalle->Id = (int) $datos['IdDiagnosticoDetalle'];
        $jsonData->Diagnostico->Detalle->Descripcion = null;
        if(!FuncionesPHPLocal::isEmpty($datos['DiagnosticoDetalleDescripcion']))
            $jsonData->Diagnostico->Detalle->Descripcion = $datos['DiagnosticoDetalleDescripcion'];
        $jsonData->Diagnostico->Detalle->Nombre = null;
        if(!FuncionesPHPLocal::isEmpty($datos['DiagnosticoDetalleNombre']))
            $jsonData->Diagnostico->Detalle->Nombre = $datos['DiagnosticoDetalleNombre'];

        $jsonData->Autorizante = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['IdAutorizante']))
            $jsonData->Autorizante->Id = (int) $datos['IdAutorizante'];
        if(!FuncionesPHPLocal::isEmpty($datos['AutorizanteMatricula']))
            $jsonData->Autorizante->Matricula = $datos['AutorizanteMatricula'];
        if(!FuncionesPHPLocal::isEmpty($datos['AutorizanteNombre']))
            $jsonData->Autorizante->Nombre = $datos['AutorizanteNombre'];
        if(!FuncionesPHPLocal::isEmpty($datos['AutorizanteApellido']))
            $jsonData->Autorizante->Apellido = $datos['AutorizanteApellido'];
        $jsonData->Autorizante->Especialidad = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['IdEspecialidad']))
            $jsonData->Autorizante->Especialidad->Id = (int) $datos['IdEspecialidad'];
        if(!FuncionesPHPLocal::isEmpty($datos['EspecialidadNombre']))
            $jsonData->Autorizante->Especialidad->Nombre = $datos['EspecialidadNombre'];

        $jsonData->Autorizante->Tipo = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['IdTipoAutorizante']))
            $jsonData->Autorizante->Tipo->Id = (int) $datos['IdTipoAutorizante'];
        if(!FuncionesPHPLocal::isEmpty($datos['TipoAutorizanteNombre']))
            $jsonData->Autorizante->Tipo->Nombre = $datos['TipoAutorizanteNombre'];

        if(isset($datos['Familiar'])){
            $jsonData->esFamiliar = FuncionesPHPLocal::isEmpty($datos['Familiar']) ? false:((bool) $datos['Familiar']);

            $jsonData->Familiar = new stdClass();
            if($jsonData->esFamiliar) {
                if(!FuncionesPHPLocal::isEmpty($datos['IdFamiliar']))
                    $jsonData->Familiar->Id = (int) $datos['IdFamiliar'];
                if(!FuncionesPHPLocal::isEmpty($datos['FamiliarNombre']))
                    $jsonData->Familiar->Nombre = $datos['FamiliarNombre'];
                if(!FuncionesPHPLocal::isEmpty($datos['FamiliarApellido']))
                    $jsonData->Familiar->Apellido = $datos['FamiliarApellido'];
                if(!FuncionesPHPLocal::isEmpty($datos['FamiliarDni']))
                    $jsonData->Familiar->Dni = $datos['FamiliarDni'];
                $jsonData->Familiar->Parentesco = new stdClass();
                if(!FuncionesPHPLocal::isEmpty($datos['IdParentesco']))
                    $jsonData->Familiar->Parentesco->Id = (int) $datos['IdParentesco'];
                if(!FuncionesPHPLocal::isEmpty($datos['ParentescoNombre']))
                    $jsonData->Familiar->Parentesco->Nombre = $datos['ParentescoNombre'];
            }
        }



        if(!FuncionesPHPLocal::isEmpty($datos['Adecuacion']))
            $jsonData->Adecuacion = (bool) $datos['Adecuacion'];

        if(!FuncionesPHPLocal::isEmpty($datos['FechaEnvio']))
            $jsonData->FechaEnvio = $datos['FechaEnvio'];

        if(!FuncionesPHPLocal::isEmpty($datos['FechaEnvioInicial']))
            $jsonData->FechaEnvioInicial = $datos['FechaEnvioInicial'];

        if(!FuncionesPHPLocal::isEmpty($datos['FechaReintegro']))
            $jsonData->FechaReintegro = $datos['FechaReintegro'];

        if(!FuncionesPHPLocal::isEmpty($datos['TareaPasiva']))
            $jsonData->TareaPasiva = (bool) $datos['TareaPasiva'];

        if(!FuncionesPHPLocal::isEmpty($datos['AptoFisico']))
            $jsonData->AptoFisico = (bool) $datos['AptoFisico'];

        $jsonData->Rectificada = false;
        if(!FuncionesPHPLocal::isEmpty($datos['Rectificada']))
            $jsonData->Rectificada = (bool) $datos['Rectificada'];

        if (!FuncionesPHPLocal::isEmpty($datos['NroResolucion'])) {
            $jsonData->Resolucion = new stdClass();
            $jsonData->Resolucion->Numero = $datos['NroResolucion'];
            if (!empty($datos['FechaResolucion']))
                $jsonData->Resolucion->Fecha = $datos['FechaResolucion'];
        }

        $jsonData->Certificados = [];
        if(!FuncionesPHPLocal::isEmpty($datos['Certificados'])) {
            foreach($datos['Certificados'] as $cc=>$certificado) {
                $jsonData->Certificados[$cc] = new stdClass();
                $jsonData->Certificados[$cc]->Id = (int) $certificado['Id'];
                $jsonData->Certificados[$cc]->Nombre = $certificado['Nombre'];
                $jsonData->Certificados[$cc]->Ubicacion = $certificado['Ubicacion'];
                $jsonData->Certificados[$cc]->Tamanio = $certificado['Tamanio'];
                $jsonData->Certificados[$cc]->Tipo = $certificado['TipoMIME'];
            }
        }

        $min = NULL;
        $jsonData->Cargos = [];
        if(!FuncionesPHPLocal::isEmpty($datos['Cargos'])) {
            foreach($datos['Cargos'] as $cc=>$cargo)
            {
                $jsonData->Cargos[$cc] = new stdClass();
                $jsonData->Cargos[$cc]->Puesto = new stdClass();
                $jsonData->Cargos[$cc]->Puesto->Id = (int)$cargo['IdPuesto'];
                $jsonData->Cargos[$cc]->Puesto->IdLicenciaCargo = (int)$cargo['Id'];
                $jsonData->Cargos[$cc]->Puesto->IdPofa = (int)$cargo['IdPofa'];
                $jsonData->Cargos[$cc]->Puesto->Nombre = $cargo['Cargo'];
                $jsonData->Cargos[$cc]->Puesto->Cupof = $cargo['Cupof'];
                $jsonData->Cargos[$cc]->Puesto->IdTipoCargo = $cargo['IdTipoCargo'];

                $jsonData->Cargos[$cc]->Puesto->ImpactoPofa = $cargo['ImpactoPofa'];
                $jsonData->Cargos[$cc]->Puesto->FechaImpactoPofa = $cargo['FechaImpactoPofa'];

                $jsonData->Cargos[$cc]->Puesto->Catedra = new stdClass();
                $jsonData->Cargos[$cc]->Puesto->Catedra->Cantidad = $cargo['CantHoras'] ?? $cargo['CantModulos'];
                $jsonData->Cargos[$cc]->Puesto->Catedra->Unidad = new stdClass();
                $jsonData->Cargos[$cc]->Puesto->Catedra->Unidad->Nombre = (!empty($cargo['CantHoras']) ? 'horas' : 'modulos' ) ;
                $jsonData->Cargos[$cc]->Puesto->Catedra->Unidad->NombreCorto = (!empty($cargo['CantHoras']) ? 'hs' : 'mod' );

                $jsonData->Cargos[$cc]->Puesto->Anio = $cargo['AnioNombre']??'';
                $jsonData->Cargos[$cc]->Puesto->AnioNombreCorto = $cargo['AnioNombreCorto']??'';
                $jsonData->Cargos[$cc]->Puesto->Seccion = $cargo['NombreSeccion']??'';

                $jsonData->Cargos[$cc]->Puesto->Revista = new stdClass();
                $jsonData->Cargos[$cc]->Puesto->Revista->Id = $cargo['IdRevista']??'';
                $jsonData->Cargos[$cc]->Puesto->Revista->Codigo = $cargo['CodigoRevista']??'';
                $jsonData->Cargos[$cc]->Puesto->Revista->Descripcion = $cargo['DescripcionRevista']??'';

                $jsonData->Cargos[$cc]->Escuela = new stdClass();
                $jsonData->Cargos[$cc]->Escuela->Id = (int)$cargo['IdEscuela']??null;
                $jsonData->Cargos[$cc]->Escuela->Nombre = $cargo['Escuela']??'';
                $jsonData->Cargos[$cc]->Escuela->IdTurno = (int)$cargo['IdEscuelaTurno']??null;
                $jsonData->Cargos[$cc]->IdRegion = (int)$cargo['IdRegion']??null;
                $jsonData->Cargos[$cc]->Escuela->Codigo = $cargo['CodigoEscuela']??'';
                $jsonData->Cargos[$cc]->Escuela->NombreTurno = $cargo['NombreTurno']??'';
                $jsonData->Cargos[$cc]->Escuela->NombreTurnoCorto = $cargo['NombreTurnoCorto']??'';

                /* if (!FuncionesPHPLocal::isEmpty($cargo['IdDepartamento'])) {
                    $jsonData->Cargos[$cc]->Departamento = new stdClass();
                    $jsonData->Cargos[$cc]->Departamento->Id = $cargo['IdDepartamento'];
                    $jsonData->Cargos[$cc]->Departamento->Nombre = $cargo['DepartamentoNombre'];
                }*/

                if (!FuncionesPHPLocal::isEmpty($cargo['IdLocalidad'])) {
                    $jsonData->Cargos[$cc]->Localidad = new stdClass();
                    $jsonData->Cargos[$cc]->Localidad->Id = $cargo['IdLocalidad'];
                    $jsonData->Cargos[$cc]->Localidad->Nombre = $cargo['LocalidadNombre'];
                }

                if (!FuncionesPHPLocal::isEmpty($cargo['EstadoLicenciaCargo']))
                    $jsonData->Cargos[$cc]->Puesto->Estado = $cargo['EstadoLicenciaCargo'];

                if (!FuncionesPHPLocal::isEmpty($cargo['IdNiveles'])) {
                    $jsonData->Cargos[$cc]->Escuela->Niveles = new stdClass();
                    $jsonData->Cargos[$cc]->Escuela->Niveles->Id = $cargo['IdNiveles'];
                    $jsonData->Cargos[$cc]->Escuela->Niveles->Nombre = $cargo['NombreNiveles'];
                }

                if (!FuncionesPHPLocal::isEmpty($cargo['IdTurnos'])) {
                    $jsonData->Cargos[$cc]->Escuela->Turnos = new stdClass();
                    $jsonData->Cargos[$cc]->Escuela->Turnos->Id = $cargo['IdTurnos'];
                    $jsonData->Cargos[$cc]->Escuela->Turnos->Nombre = $cargo['NombreTurnos'];
                }

                $jsonData->Cargos[$cc]->Escuela->Nivel = $cargo['NivelNombre']??'';

                if (!FuncionesPHPLocal::isEmpty($cargo['IdArticulo'])) {

                    if (!empty($cargo['CantidadMaximaDias'])) {
                        if (!$min || $min > $cargo['CantidadMaximaDias']) {
                            $min = (int) $cargo['CantidadMaximaDias'];
                        }
                    }

                    $jsonData->Cargos[$cc]->Articulo = new stdClass();
                    $jsonData->Cargos[$cc]->Articulo->Id = $cargo['IdArticulo'];
                    $jsonData->Cargos[$cc]->Articulo->Codigo = $cargo['CodigoArticulo'];
                    $jsonData->Cargos[$cc]->Articulo->Descripcion = $cargo['DescripcionArticulo'];
                    $jsonData->Cargos[$cc]->Articulo->CantidadMaximaDias = new stdClass();
                    $jsonData->Cargos[$cc]->Articulo->CantidadMaximaDias->Anio = FuncionesPHPLocal::isEmpty($cargo['CantidadMaximaDias'])
                        ? null : (int)$cargo['CantidadMaximaDias'];
                    $jsonData->Cargos[$cc]->Articulo->CantidadMaximaDias->Mes = FuncionesPHPLocal::isEmpty($cargo['CantidadMaximaDiasMes'])
                        ? null : (int)$cargo['CantidadMaximaDiasMes'];
                    $jsonData->Cargos[$cc]->Articulo->GoceDeSueldo = (bool) $cargo['ConGoceSueldo'];
                    $jsonData->Cargos[$cc]->Articulo->PermiteOtroOrganismo = (bool) $cargo['PermiteOtroOrganismo'];
                    $jsonData->Cargos[$cc]->Articulo->EsAnual = (bool) $cargo['EsAnual'];
                    $jsonData->Cargos[$cc]->Articulo->EsAuxiliar = (bool) $cargo['EsAuxiliar'];
                }

                $jsonData->Cargos[$cc]->HorasAfectadas =  $cargo['HorasAfectadas']/self::FACTOR_ESCALA;

                $jsonData->Cargos[$cc]->Alta = new stdClass();
                $jsonData->Cargos[$cc]->Alta->Fecha = $cargo['AltaFecha'];
                $jsonData->Cargos[$cc]->Alta->App = $cargo['AltaApp'];
                $jsonData->Cargos[$cc]->Alta->Usuario = new stdClass();
                $jsonData->Cargos[$cc]->Alta->Usuario->Id = (int)$cargo['AltaUsuario'];
                $jsonData->Cargos[$cc]->Alta->Usuario->Nombre = $cargo['AltaUsuarioNombre'];

                $jsonData->Cargos[$cc]->UltimaModificacion = new stdClass();
                $jsonData->Cargos[$cc]->UltimaModificacion->Fecha = $cargo['UltimaModificacionFecha'];
                $jsonData->Cargos[$cc]->UltimaModificacion->App = $cargo['UltimaModificacionApp'];
                $jsonData->Cargos[$cc]->UltimaModificacion->Usuario = new stdClass();
                $jsonData->Cargos[$cc]->UltimaModificacion->Usuario->Id = (int)$cargo['UltimaModificacionUsuario'];
                $jsonData->Cargos[$cc]->UltimaModificacion->Usuario->Nombre = $cargo['UltimaModificacionUsuarioNombre'];
            }
        }


        $jsonData->Articulo->CantidadMaximaDias = $min;

        $jsonData->Juntas = [];
        if (!FuncionesPHPLocal::isEmpty($datos['Juntas'])) {

            foreach($datos['Juntas'] as $r => $junta) {

                $jsonData->Juntas[$r] = new stdClass();
                $jsonData->Juntas[$r]->Junta = new stdClass();
                $jsonData->Juntas[$r]->Junta->Id = (int)$junta['IdJunta'];
                $jsonData->Juntas[$r]->Junta->Fecha = $junta['FechaJunta'];
                $jsonData->Juntas[$r]->Junta->Comentarios = $junta['Comentarios'];

                $jsonData->Juntas[$r]->Junta->Tipo = new stdClass();
                $jsonData->Juntas[$r]->Junta->Tipo->Id = $junta['IdTipoJunta'];
                $jsonData->Juntas[$r]->Junta->Tipo->Nombre = $junta['NombreTipoJunta'];

                $jsonData->Juntas[$r]->Junta->Motivo = new stdClass();
                $jsonData->Juntas[$r]->Junta->Motivo->Id = $junta['IdMotivoJunta'];
                $jsonData->Juntas[$r]->Junta->Motivo->Nombre = $junta['NombreMotivoJunta'];

                $jsonData->Juntas[$r]->Junta->Region = new stdClass();
                $jsonData->Juntas[$r]->Junta->Region->Id = $junta['IdRegionJunta'];
                $jsonData->Juntas[$r]->Junta->Region->Nombre = $junta['NombreRegionJunta'];

                $jsonData->Juntas[$r]->Junta->Estado = new stdClass();
                $jsonData->Juntas[$r]->Junta->Estado->Id = $junta['IdEstadoJunta'];
                $jsonData->Juntas[$r]->Junta->Estado->Nombre = $junta['NombreEstadoJunta'];

                $jsonData->Juntas[$r]->Junta->Autorizante = new stdClass();
                $jsonData->Juntas[$r]->Junta->Autorizante->Id = $junta['IdAutorizanteJunta'];
                $jsonData->Juntas[$r]->Junta->Autorizante->Nombre = $junta['NombreAutorizanteJunta'];
                $jsonData->Juntas[$r]->Junta->Autorizante->Tipo = $junta['TipoAutorizanteJunta'];

                $jsonData->Juntas[$r]->Junta->FechaJunta = $junta['FechaJuntaResultado'];
                $jsonData->Juntas[$r]->Junta->Integracion = $junta['Integracion'];

                $jsonData->Juntas[$r]->Junta->Alta = new stdClass();
                $jsonData->Juntas[$r]->Junta->Alta->Fecha = $junta['AltaFechaJunta'];
                $jsonData->Juntas[$r]->Junta->Alta->Usuario = new stdClass();
                $jsonData->Juntas[$r]->Junta->Alta->Usuario->Id = $junta['AltaUsuarioJunta'];
                $jsonData->Juntas[$r]->Junta->Alta->Usuario->Nombre = $junta['AltaUsuarioNombreJunta'];

                $jsonData->Juntas[$r]->Junta->UltimaModificacion = new stdClass();
                $jsonData->Juntas[$r]->Junta->UltimaModificacion->Fecha = $junta['UltimaModificacionFechaJunta'];
                $jsonData->Juntas[$r]->Junta->UltimaModificacion->Usuario = new stdClass();
                $jsonData->Juntas[$r]->Junta->UltimaModificacion->Usuario->Id = $junta['UltimaModificacionUsuarioJunta'];
                $jsonData->Juntas[$r]->Junta->UltimaModificacion->Usuario->Nombre = $junta['UltimaModificacionUsuarioNombreJunta'];
            }
        }



        $jsonData->Observaciones = [];
        if(!FuncionesPHPLocal::isEmpty($datos['Observaciones'])) {
            foreach($datos['Observaciones'] as $oo=>$observacion) {
                $jsonData->Observaciones[$oo] = new stdClass();
                $jsonData->Observaciones[$oo]->Id = (int) $observacion['Id'];
                $jsonData->Observaciones[$oo]->Usuario = new stdClass();
                $jsonData->Observaciones[$oo]->Usuario->Id = (int) $observacion['IdUsuario'];
                $jsonData->Observaciones[$oo]->Usuario->Nombre = $observacion['UsuarioNombre'];
                $jsonData->Observaciones[$oo]->Observacion = $observacion['Observacion'];
                $jsonData->Observaciones[$oo]->Tamanio = $observacion['Fecha'];
            }
        }

        if (!FuncionesPHPLocal::isEmpty($datos['AprobanteUsuario'])) {

            $jsonData->Aprobante = new stdClass();
            $jsonData->Aprobante->Fecha = $datos['AprobanteFecha'];
            $jsonData->Aprobante->Usuario = new stdClass();
            $jsonData->Aprobante->Usuario->Id = (int) $datos['AprobanteUsuario'];
            $jsonData->Aprobante->Usuario->Nombre = $datos['AprobanteNombre'];
        }

        $jsonData->Alta = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['AltaFecha']))
            $jsonData->Alta->Fecha = $datos['AltaFecha'];
        if(!FuncionesPHPLocal::isEmpty($datos['AltaUsuario']))
            $jsonData->Alta->Usuario = (int) $datos['AltaUsuario'];
        if(!FuncionesPHPLocal::isEmpty($datos['AltaApp']))
            $jsonData->Alta->App = $datos['AltaApp'];

        $jsonData->UltimaModificacion = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionFecha']))
            $jsonData->UltimaModificacion->Fecha = $datos['UltimaModificacionFecha'];
        if(!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionUsuario']))
            $jsonData->UltimaModificacion->Usuario = (int) $datos['UltimaModificacionUsuario'];
        if(!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionApp']))
            $jsonData->UltimaModificacion->App = $datos['UltimaModificacionApp'];

        $jsonData = FuncionesPHPLocal::ConvertiraUtf8($jsonData);

        return $encode? json_encode($jsonData) : $jsonData;
    }

    private static function obtenerSexo(?string $letra): ?string {
        switch(strtoupper($letra)) {
            case 'M':
                return 'Masculino';
            case 'F':
                return 'Femenino';
            default:
                return null;
        }
    }

    private static function definirClasesMostrar(stdClass &$estado): void {

        switch($estado->Id??null) {
            case 5:
                $class_publica = 'success';
                $class = 'default';
                break;
            case 1:
                $class = 'default';
                break;
            case 12:
                $class = 'light';
                break;
            case 7:
            case 8:
            case 14:
            case 23:
            case 24:
                $class_publica = 'danger';
                $class = 'default';
                break;
            case 4:
                $class_publica = 'success';
                $class = 'default';
                $class_tmp = 'warning';
                break;
            case 9:
            case 16:
                $class = 'info';
                break;
            case ESTADO_AP_CONDICIONAL:
                $class = 'secondary';
                break;
            default:
                $class_publica = 'warning';
                $class = 'danger';
        }

        $estado->Class = $class;
        $estado->ClassTmp =  $class_tmp ?? $class;
        $estado->ClassPublica = $class_publica??$class;
    }
}
?>
