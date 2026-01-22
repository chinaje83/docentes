<?php

use Bigtree\LogicaExportacion\Movimientos;

include(DIR_CLASES_DB."cPeriodosLiquidacion.db.php");
class cPeriodosLiquidacion extends cPeriodosLiquidaciondb
{

	/**
	 * Constructor de la clase cAmbitos.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion,$formato=FMT_TEXTO){
		parent::__construct($conexion,$formato);
        $this->error = [];
	}
	/**
	 * Destructor de la clase cAmbitos.
	 */
	function __destruct(){
		parent::__destruct();
	}
	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public function getError(): array {
		return $this->error;
	}

	public function BuscarxCodigo($datos, &$resultado,&$numfilas): bool
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

    public function BuscarExistexEstados($datos, &$resultado,&$numfilas): bool
    {
        if(!isset($datos['IdEstado']) || $datos['IdEstado']=="")
            $datos['IdEstado']="-1";

        if (!parent::BuscarExistexEstados($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdLogMovimiento($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdLogMovimiento($datos,$resultado,$numfilas))
            return false;
        return true;
    }



    public function BuscarLicenciasLiquidacion($datos,&$resultado,&$numfilas): bool
    {

            $sparam=array(
                'FechaDesde' => $datos['FechaDesde'],
                'FechaHasta' => $datos['FechaHasta'],
                'xEstadosLicencias'=> 0,
                'EstadosLicencias'=> "-1",
                'Excluir_Escuela'=> "-1",
                'xEscuelas'=> 0,
                'Escuelas'=> "-1",
                'limit'=> '',
            );


        if (defined('ESTADOS_LICENCIAS_LIQUIDACION') && count(ESTADOS_LICENCIAS_LIQUIDACION)>0){
            $sparam['EstadosLicencias']=implode(",",ESTADOS_LICENCIAS_LIQUIDACION);
            $sparam['xEstadosLicencias'] = 1;
        }

        if (isset($datos['Excluir_Escuela']) && $datos['Excluir_Escuela'] != "") {
            $sparam['Excluir_Escuela'] = $datos['Excluir_Escuela'];
        }

        if(isset($datos['Escuelas']) && $datos['Escuelas']!="")
        {
            $sparam['Escuelas']= $datos['Escuelas'];
            $sparam['xEscuelas']= 1;
        }
        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];

        if (!parent::BuscarLicenciasLiquidacion($sparam,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarDocumentosLiquidacion($datos,&$resultado,&$numfilas): bool
    {

            $sparam=array(
                'FechaDesde' => $datos['FechaDesde'],
                'FechaHasta' => $datos['FechaHasta'],
                'xEstadosFinales'=> 0,
                'EstadosFinales'=> "-1",
                'Excluir_Escuela'=> "-1",
                'MovimientosAltaBaja'=> "-1",
                'NotDocumentos'=> "-1",
                'xEscuelas'=> 0,
                'Escuelas'=> "-1",
                'limit'=> '',
            );


        if (defined('ESTADOS_NOVEDADES_LIQUIDACION') && count(ESTADOS_NOVEDADES_LIQUIDACION)>0){
            $sparam['EstadosFinales']=implode(",",ESTADOS_NOVEDADES_LIQUIDACION);
            $sparam['xEstadosFinales'] = 1;
        }
        if (isset($datos['Excluir_Escuela']) && $datos['Excluir_Escuela'] != "") {
            $sparam['Excluir_Escuela'] = $datos['Excluir_Escuela'];
        }

        if (isset($datos['MovimientosAltaBaja']) && $datos['MovimientosAltaBaja'] != "") {
            $sparam['MovimientosAltaBaja'] = $datos['MovimientosAltaBaja'];
        }
        if (isset($datos['NotDocumentos']) && $datos['NotDocumentos'] != "") {
            $sparam['NotDocumentos'] = $datos['NotDocumentos'];
        }

        if(isset($datos['Escuelas']) && $datos['Escuelas']!="")
        {
            $sparam['Escuelas']= $datos['Escuelas'];
            $sparam['xEscuelas']= 1;
        }
        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];

        if (!parent::BuscarDocumentosLiquidacion($sparam,$resultado,$numfilas))
            return false;
        return true;
    }

    public function buscarUltimoPeriodoLiquidado(array $datos, &$resultado, &$numfilas):bool {

        return parent::buscarUltimoPeriodoLiquidado($datos, $resultado,$numfilas);
    }


    public function buscarLicenciasTiempoReal($datos,&$resultado,&$numfilas): bool
    {
        $sparam = array(
            'FechaDesde' => $datos['FechaDesde'],
            'FechaHasta' => $datos['FechaHasta'],
            'Excluir_Escuela'=> "-1",
            'xEscuelas'=> 0,
            'Escuelas'=> "-1",
            'limit'=> '',
        );

        if (isset($datos['Excluir_Escuela']) && $datos['Excluir_Escuela'] != "") {
            $sparam['Excluir_Escuela'] = $datos['Excluir_Escuela'];
        }

        if(isset($datos['Escuelas']) && $datos['Escuelas']!="")
        {
            $sparam['Escuelas']= $datos['Escuelas'];
            $sparam['xEscuelas']= 1;
        }
        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];

        if (!parent::buscarLicenciasTiempoReal($sparam,$resultado,$numfilas))
            return false;
        return true;
    }

    public function buscarDocumentosTiempoReal($datos,&$resultado,&$numfilas): bool
    {
        $sparam=array(
            'FechaDesde' => $datos['FechaDesde'],
            'FechaHasta' => $datos['FechaHasta'],
            'EstadosFinales'=> "-1",
            'Excluir_Escuela'=> "-1",
            'MovimientosAltaBaja'=> "-1",
            'NotDocumentos'=> "-1",
            'xEscuelas'=> 0,
            'Escuelas'=> "-1",
            'limit'=> '',
        );

        if (isset($datos['EstadosFinales']) && $datos['EstadosFinales'] != "") {
            $sparam['EstadosFinales'] = $datos['EstadosFinales'];
        }

        if (isset($datos['Excluir_Escuela']) && $datos['Excluir_Escuela'] != "") {
            $sparam['Excluir_Escuela'] = $datos['Excluir_Escuela'];
        }

        if (isset($datos['MovimientosAltaBaja']) && $datos['MovimientosAltaBaja'] != "") {
            $sparam['MovimientosAltaBaja'] = $datos['MovimientosAltaBaja'];
        }
        if (isset($datos['NotDocumentos']) && $datos['NotDocumentos'] != "") {
            $sparam['NotDocumentos'] = $datos['NotDocumentos'];
        }

        if(isset($datos['Escuelas']) && $datos['Escuelas']!="")
        {
            $sparam['Escuelas']= $datos['Escuelas'];
            $sparam['xEscuelas']= 1;
        }
        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];

        if (!parent::buscarDocumentosTiempoReal($sparam,$resultado,$numfilas))
            return false;
        return true;
    }




	public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
            'xId'=> 0,
            'Id'=> "",
            'xFechaDesde' => 0,
            'FechaDesde' => '',
            'xFechaHasta' => 0,
            'FechaHasta' => '',
			'xPeriodo'=> 0,
			'Periodo'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "Id DESC"
		);

        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }

        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            $sparam['FechaDesde'] = $datos['FechaDesde'];
            $sparam['xFechaDesde'] = 1;
        }

        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            $sparam['FechaHasta'] = $datos['FechaHasta'];
            $sparam['xFechaHasta'] = 1;
        }

		if(isset($datos['Periodo']) && $datos['Periodo']!="")
		{
			$sparam['Periodo']= $datos['Periodo'];
			$sparam['xPeriodo']= 1;
		}

		if(isset($datos['Estado']) && $datos['Estado']!="")
		{
			$sparam['Estado']= $datos['Estado'];
			$sparam['xEstado']= 1;
		}

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];
		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];
		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}

    public function AgregarPeriodo($datos,&$codigoInsertado): bool
    {
        if (!$this->_ValidarAgregarMovimiento($datos,$datosRegistro))
            return false;
        $IdEstado = ESTADO_LOG_NOVEDAD_NUEVO; //NUEVO
        if($datos['IdTipoLiquidacion']==1)
            $IdEstado = ESTADO_LOG_NOVEDAD_SIMULACION; //SIMULACION

        $oMovimientos = new Movimientos($this->conexion,$this->formato);
        $datosInsertar = [
            'IdUsuario'=> $_SESSION['usuariocod'],
            'IdEstado'=> $IdEstado,
            'FechaEjecucion' => date('Y-m-d H:i:s'),
            'FechaDesde' => $datosRegistro['FechaDesde'],
            'FechaHasta' => $datosRegistro['FechaHasta'],
            'IdPeriodo' => $datos['IdPeriodo'],
            'IdTipoLiquidacion' => $datos['IdTipoLiquidacion']
        ];

        if(!$oMovimientos->InsertarLog($datosInsertar,$codigoInsertado))
            return false;

        $datosRegistro['IdPeriodo'] = $datos['IdPeriodo'];
        $datosRegistro['IdEstado'] = $IdEstado;
        $datosRegistro['IdLogMovimientos'] = $codigoInsertado;


        //if(!$this->ProcesarLiquidacion($datosRegistro))
          //  return false;

        return true;
    }


    public function ProcesarLiquidacion($datos)
    {

        $datos['Excluir_Escuela']  = implode(',', ESCUELAS_DE_PRUEBA);
        $datos['EstadosFinales'] = implode(',',ESTADOS_NOVEDADES_LIQUIDACION);
        $datos['EstadosLicencias'] = implode(',',ESTADOS_LICENCIAS_LIQUIDACION);
        $datos['MovimientosAltaBaja'] = '';
        $datos['NotDocumentos'] = '';

        if(!$this->BuscarLicenciasLiquidacion($datos,$resultadoLicencias,$numfilasLicencias))
            return false;
        $datosInsertar = $filaLic = [];
        while ($filaLic = $this->conexion->ObtenerSiguienteRegistro($resultadoLicencias)) {
            $filaLic['Horas'] = Movimientos::detectarHorasTipoCargo($filaLic, Movimientos::TIPO_LICENCIA);
            #siempre meto el inicio y fin de la licencia, cambia la fecha de liquidacion nomas
            if ($filaLic['ExisteLiquidado']==0) {
                $filaLic['Movimiento'] = 10;
                $filaLic['FechaLiquidacion'] = $filaLic['Inicio'];
                if ( strtotime($filaLic['FechaLiquidacion'])<=strtotime($filaLic['FechaMovimiento']))
                    $filaLic['FechaLiquidacion'] = $filaLic['FechaMovimiento'];
                $fechakey=date("Ymd",strtotime($filaLic['Inicio']));
                $datosInsertar["{$filaLic['Movimiento']}--{$fechakey}-{$filaLic['IdPofa']}-l{$filaLic['IdLicencia']}"] = $filaLic;

                $filaLic['Movimiento'] = 12;
                $filaLic['FechaLiquidacion'] = $filaLic['Fin'];
                if ( strtotime($filaLic['FechaLiquidacion'])<=strtotime($filaLic['FechaMovimiento']))
                    $filaLic['FechaLiquidacion'] = $filaLic['FechaMovimiento'];
                $fechakey=date("Ymd",strtotime($filaLic['Fin']));
                $datosInsertar["{$filaLic['Movimiento']}--{$fechakey}-{$filaLic['IdPofa']}-l{$filaLic['IdLicencia']}"] = $filaLic;
            }
            else{
                    $filaLic['Movimiento'] = 13;
                    $fechakey=date("Ymd",strtotime($filaLic['Fin']));
                    $datosInsertar["{$filaLic['Movimiento']}--{$fechakey}-{$filaLic['IdPofa']}-l{$filaLic['IdLicencia']}"] = $filaLic;
                    $filaLic['FechaLiquidacion'] = $filaLic['FechaMovimiento'];
            }
        }
       //FuncionesPHPLocal::print_pre($datosInsertar);


        if(!$this->BuscarDocumentosLiquidacion($datos,$resultadoDocumentos,$numfilasDocumentos))
            return false;



        while ($filaNov = $this->conexion->ObtenerSiguienteRegistro($resultadoDocumentos)) {

            $filaNov['Horas'] = Movimientos::detectarHorasTipoCargo($filaNov, Movimientos::TIPO_NOVEDAD);
            $filaNov['IdRevistaNueva'] = 'NULL';
            $filaNov['IdRevistaAntigua'] = 'NULL';
            $filaNov['FechaLiquidacion']=$filaNov['FechaMovimiento'];
            if ( strtotime($filaNov['FechaLiquidacion'])<strtotime($filaNov['fechaDesde']))
                $filaNov['FechaLiquidacion']= $filaNov['fechaDesde'];

            if ($filaNov['Movimiento'] == 99) { //cambio de revista

                $filaNov['Movimiento'] = 3;//baja revista
                $filaNov['IdRevistaNueva'] = 'NULL';
                $filaNov['IdRevistaAntigua'] = $filaNov['oldRevista'];
                $fechakey=date("Ymd",strtotime($filaNov['UltimaModificacionFecha']));
                $datosInsertar["d{$filaNov['Movimiento']}--{$fechakey}-{$filaNov['IdPofa']}-n{$filaNov['IdDocumento']}"] = $filaNov;

                $filaNov['Movimiento'] = 4;//alta revista
                $filaNov['IdRevistaAntigua'] = 'NULL';
                $filaNov['IdRevistaNueva'] = $filaNov['newRevista'];
                $fechakey=date("Ymd",strtotime($filaNov['UltimaModificacionFecha']));
                $datosInsertar["d{$filaNov['Movimiento']}--{$fechakey}-{$filaNov['IdPofa']}-n{$filaNov['IdDocumento']}"] = $filaNov;

            } else {
                if (isset($filaNov['FechaToma']) && $filaNov['FechaToma']!="")
                {
                    $filaNov['fechaDesde']=$filaNov['FechaToma'];
                }
                $fechakey=date("Ymd",strtotime($filaNov['fechaDesde']));
                $datosInsertar["d{$fechakey}-{$filaNov['IdPofa']}-n{$filaNov['IdDocumento']}"] = $filaNov;
                #si viene la fecha de toma de posesion, tomamos la fecha de toma de posesion como la de inicio de la novedad

                /*#si el alta tiene fecha de baja presunta, agrego un movimiento de baja*/
                /*la baja tiene que ser diferented del alta*/
                if (isset($filaNov['fechaHasta']) && $filaNov['fechaHasta']!="" && $filaNov['fechaHasta']!=$filaNov['fechaDesde']) {
                    $filaNov['Movimiento'] = 2;//baja
                    $filaNov['FechaLiquidacion'] = $filaNov['fechaHasta'];
                    $fechakey=date("Ymd",strtotime($filaNov['fechaHasta']));
                    $datosInsertar["h{$fechakey}-{$filaNov['IdPofa']}-n{$filaNov['IdDocumento']}"] = $filaNov;
                }
            }
        }
        FuncionesPHPLocal::print_pre($datosInsertar);

        ksort($datosInsertar);
        $oMovimientosTmp = new cMovimientosTmp($this->conexion,$this->formato);
        $i = 0;
        foreach ($datosInsertar as $fila) {

//            # TODO: BORRAR
//            if (FuncionesPHPLocal::isEmpty($fila['IdPuestoMigracion']))
//                continue;

            $fila['Periodo'] = new DateTime($datos['FechaHasta'] ?? '@0');

            if (FuncionesPHPLocal::isEmpty($fila['CuilAgente'])) {

                $this->setError(400, 'Falta nro. de Cuil del agente.
                                                         IdPuestoMigracion: '.($fila['IdPuestoMigracion'] ?? '').'
                                                         Novedad: '.($fila['IdDocumento'] ?? '').'
                                                         Licencia: '.($fila['IdLicencia'] ?? '').'
                                                         Para falta de cuils: http://www0.unsl.edu.ar/~jolguin/cuit.php'
                );
                return false;
            }

            $movimiento = Movimientos::armarFila($fila, $resultadoBD);

            $datosInsertarNov = $resultadoBD;
            $datosInsertarNov['IdLogMovimientos'] = $datos['IdLogMovimientos'];
            $datosInsertarNov['IdEstado'] = $datos['IdEstado'];
//            $datosInsertarNov['Orden'] = ++$i;

            if (!$oMovimientosTmp->InsertarLogNovedadTmp($datosInsertarNov, $idLogNovTmp)) {
                $this->setError($oMovimientosTmp->getError());
                return false;
            }
        }

       $oMovimientos = new Movimientos($this->conexion,$this->formato);
        if (!$oMovimientos->InsertarLogNovedadBulkTmp($datos))
            return false;

        if (!$oMovimientosTmp->EliminarxIdLogMovimientos($datos))
            return false;

        return true;
    }

	public function Insertar($datos,&$codigoInsertado): bool
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['Estado'] = NOACTIVO;//Por defecto entran todos como no activos;
        $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], "dd/mm/aaaa","aaaa-mm-dd");
        $datos['FechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], "dd/mm/aaaa","aaaa-mm-dd");
        $datos['FechaFinReal'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaFinReal'], "dd/mm/aaaa","aaaa-mm-dd");
		if (!parent::Insertar($datos,$codigoInsertado))
			return false;

		return true;
	}


	public function Modificar($datos): bool
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
        $datos['FechaDesde'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'], "dd/mm/aaaa","aaaa-mm-dd");
        $datos['FechaHasta'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'], "dd/mm/aaaa","aaaa-mm-dd");
        $datos['FechaFinReal'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaFinReal'], "dd/mm/aaaa","aaaa-mm-dd");
        $this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$datosmodif['Id'] = $datos['Id'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}


	public function ModificarEstado($datos): bool {


		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}


	public function Activar(array $datos): bool
	{
        if (!$this->_ValidarEstado($datos))
            return false;

		$datosmodif['Id'] = $datos['Id'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['Id'] = $datos['Id'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		return true;
	}

    private function _ValidarEstado(array $datos): bool
    {
        $datosBusqueda["Estado"]=ACTIVO;
        if(!$this->BusquedaAvanzada($datosBusqueda,$resultado,$numfilas))
        {
            $this->setError(400, utf8_encode("Error al buscar período de liquidacion."));
            return false;
        }
        if ($numfilas>0)
        {
            $this->setError(400, utf8_encode("Ya existe otro período activo de liquidación. Desactivelo para poder activar el seleccionado."));
            return false;
        }

        return true;
    }






//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}


	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
            $this->setError(400, utf8_encode("Error debe ingresar un código valido."));
			return false;
		}


		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}


	private function _ValidarEliminar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
            $this->setError(400, utf8_encode("Error debe ingresar un código valido."));
            return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}

    private function _ValidarAgregarMovimiento($datos,&$datosRegistro)
    {
        $datosBuscar['Id'] = $datos['IdPeriodo'];
        if (!$this->BuscarxCodigo($datosBuscar,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            $this->setError(400, utf8_encode("Error debe ingresar un código valido."));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);


        $datosBuscarExiste['IdPeriodo']= $datos['IdPeriodo'];
        $datosBuscarExiste['IdTipoLiquidacion']= $datos['IdTipoLiquidacion'];
        $datosBuscarExiste['IdEstado']= ESTADO_LOG_NOVEDAD_NUEVO.",".ESTADO_LOG_NOVEDAD_PENDIENTE_LIQUIDACION.",".ESTADO_LOG_NOVEDAD_SIMULACION;

        if(!$this->BuscarExistexEstados($datosBuscarExiste,$resultadoExiste,$numfilasExiste))
            return false;

        if($numfilasExiste>0)
        {
            $filaExiste = $this->conexion->ObtenerSiguienteRegistro($resultadoExiste);

            $this->setError(400, 'Error, ya existe otro periodo del tipo');
            return false;
        }

        if($datos['IdTipoLiquidacion']==3) //RELIQUIDACION
        {
            $datosBuscarExiste['Id']= $datos['IdPeriodo'];
            $datosBuscarExiste['IdTipoLiquidacion']= 2; // TIPO LIQUIDACION
            $datosBuscarExiste['IdEstado']= ESTADO_LOG_NOVEDAD_LIQUIDADO.",".ESTADO_LOG_NOVEDAD_LIQUIDADO_CON_ERRORES;

            if(!$this->BuscarExistexEstados($datosBuscarExiste,$resultadoExiste,$numfilasExiste))
                return false;

            if($numfilasExiste==0)
            {
                $this->setError(400, utf8_encode("Error, no se puede reliquidar sin no hay una liquidación sin finalizar"));
                return false;
            }
        }



        return true;
    }


	private function _SetearNull(&$datos): void
	{

        if (!isset($datos['FechaDesde']) || $datos['FechaDesde']=="")
            $datos['FechaDesde']="NULL";

        if (!isset($datos['FechaHasta']) || $datos['FechaHasta']=="")
            $datos['FechaHasta']="NULL";

		if (!isset($datos['Periodo']) || $datos['Periodo']=="")
			$datos['Periodo']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

	}


	private function _ValidarDatosVacios($datos)
	{

        if (!isset($datos['FechaDesde']) || $datos['FechaDesde']=="")
        {
            $this->setError(400, utf8_encode("Debe ingresar una fecha desde"));
            return false;
        }

        if (!isset($datos['FechaHasta']) || $datos['FechaHasta']=="")
        {
            $this->setError(400, utf8_encode("Debe ingresar una fecha hasta"));
            return false;
        }

        if (!isset($datos['FechaFinReal']) || $datos['FechaFinReal']=="")
        {
            $this->setError(400, utf8_encode("Debe ingresar una fecha fin real"));
            return false;
        }


        $FechaDesde = strtotime(FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd"));
        $FechaHasta = strtotime(FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],"dd/mm/aaaa","aaaa-mm-dd"));
        $FechaFinReal = strtotime(FuncionesPHPLocal::ConvertirFecha($datos['FechaFinReal'],"dd/mm/aaaa","aaaa-mm-dd"));


        if($FechaDesde>$FechaHasta)
        {
            $this->setError(400, utf8_encode("Error, la fecha desde debe ser menor a la fecha hasta"));
            return false;
        }

        if($FechaDesde>$FechaFinReal)
        {
            $this->setError(400, utf8_encode("Error, la fecha desde debe ser menor a la fecha fin real"));
            return false;
        }

		if (!isset($datos['Periodo']) || $datos['Periodo']=="")
		{
            $this->setError(400, utf8_encode("Debe ingresar un periodo"));
			return false;
		}


		return true;
	}




}
