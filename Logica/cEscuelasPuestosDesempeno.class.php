<?php

use Bigtree\ExcepcionLogica;
use Elastic\Consultas\Base;
use Elastic\Consultas\Query;

include(DIR_CLASES_DB."cEscuelasPuestosDesempeno.db.php");
class cEscuelasPuestosDesempeno extends cEscuelasPuestosDesempenodb
{
    /**
     * @var Elastic\Conexion
     */
    private $conexionES;
	/**
	 * Constructor de la clase cEscuelasPuestosDesempeno.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
    public function __construct(accesoBDLocal $conexion, ?Elastic\Conexion $conexionES=null, $formato = FMT_ARRAY) {
        $this->conexionES =& $conexionES;
		parent::__construct($conexion,$formato);
	}
	/**
	 * Destructor de la clase cEscuelasPuestosDesempeno.
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

    public function BuscarxCodigoxEstado($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxCodigoxEstado($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarxIdDesempeno($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdDesempeno($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdPuestoxIdDesempeno($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdPuestoxIdDesempeno($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdPuestoxIdSeccionxIdCicloLectivo($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdPuestoxIdSeccionxIdCicloLectivo($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function buscarTotalDesempenosxEscuelas(&$resultado,&$numfilas): bool
    {
        if (!parent::buscarTotalDesempenosxEscuelas($resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdPuestoxIdSeccion($datos, &$resultado,&$numfilas): bool
    {
        $sparam = [
            'IdSeccion' => $datos['IdSeccion'],
            'xPuestoPadre' => 0
        ];

        if (isset($datos['SoloPadre']) && !empty($datos['SoloPadre'])) {
            $sparam['xPuestoPadre'] = 1;
        }

        if (!parent::BuscarxIdPuestoxIdSeccion($sparam,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarHorasxIdPuestoxIdSeccion($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarHorasxIdPuestoxIdSeccion($datos,$resultado,$numfilas))
            return false;
        return true;
    }


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xIdPuesto'=> 0,
			'IdPuesto'=> "",
			'xDia'=> 0,
			'Dia'=> "",
			'xHoraInicio'=> 0,
			'HoraInicio'=> "",
			'xHoraFin'=> 0,
			'HoraFin'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdPuesto DESC"
		);
		if(isset($datos['IdPuesto']) && $datos['IdPuesto']!="")
		{
			$sparam['IdPuesto']= $datos['IdPuesto'];
			$sparam['xIdPuesto']= 1;
		}
		if(isset($datos['Dia']) && $datos['Dia']!="")
		{
			$sparam['Dia']= $datos['Dia'];
			$sparam['xDia']= 1;
		}
		if(isset($datos['HoraInicio']) && $datos['HoraInicio']!="")
		{
			$sparam['HoraInicio']= $datos['HoraInicio'];
			$sparam['xHoraInicio']= 1;
		}
		if(isset($datos['HoraFin']) && $datos['HoraFin']!="")
		{
			$sparam['HoraFin']= $datos['HoraFin'];
			$sparam['xHoraFin']= 1;
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


    public function BusquedaAvanzadaPofa($datos,&$resultado,&$numfilas): bool
    {

        $sparam=array(
            'xIdEscuela'=> 0,
            'IdEscuela'=> "",
            'xIdCicloLectivo'=> 0,
            'IdCicloLectivo'=> "",
            'xIdNivelModalidad'=> 0,
            'IdNivelModalidad'=> "",
            'xIdTurno'=> 0,
            'IdTurno'=> "",
            'xIdGradoAnio'=> 0,
            'IdGradoAnio'=> "",
            'xIdSeccion'=> 0,
            'IdSeccion'=> "",
            'limit'=> '',
            'orderby'=> "IdDesempeno DESC"
        );
        if(isset($datos['IdEscuela']) && $datos['IdEscuela']!="")
        {
            $sparam['IdEscuela']= $datos['IdEscuela'];
            $sparam['xIdEscuela']= 1;
        }
        if(isset($datos['IdCicloLectivo']) && $datos['IdCicloLectivo']!="")
        {
            $sparam['IdCicloLectivo']= $datos['IdCicloLectivo'];
            $sparam['xIdCicloLectivo']= 1;
        }
        if(isset($datos['IdNivelModalidad']) && $datos['IdNivelModalidad']!="")
        {
            $sparam['IdNivelModalidad']= $datos['IdNivelModalidad'];
            $sparam['xIdNivelModalidad']= 1;
        }
        if(isset($datos['IdTurno']) && $datos['IdTurno']!="")
        {
            $sparam['IdTurno']= $datos['IdTurno'];
            $sparam['xIdTurno']= 1;
        }
        if(isset($datos['IdGradoAnio']) && $datos['IdGradoAnio']!="")
        {
            $sparam['IdGradoAnio']= $datos['IdGradoAnio'];
            $sparam['xIdGradoAnio']= 1;
        }
        if(isset($datos['IdSeccion']) && $datos['IdSeccion']!="")
        {
            $sparam['IdSeccion']= $datos['IdSeccion'];
            $sparam['xIdSeccion']= 1;
        }

        if(isset($datos['orderby']) && $datos['orderby']!="")
            $sparam['orderby']= $datos['orderby'];
        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];
        if (!parent::BusquedaAvanzadaPofa($sparam,$resultado,$numfilas))
            return false;
        return true;
    }


	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas): bool
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}

    public function InsertarDB($datos, &$codigoInsertado): bool {

        return parent::Insertar($datos,$codigoInsertado);
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
		$datos['Estado'] = ACTIVO;

        if ($datos['Dia'] == 0) {

            if (!parent::InsertarBulk($datos))
                return false;

            if (!parent::BuscarUltimosInsertados($datos, $resultado, $numfilas))
                return false;

            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

                $datosBusqueda = array();
                $datosRegistro['IdDesempeno'] = $codigoInsertado = $fila['IdDesempeno'];
                $datosRegistro['Dia'] = $fila['Dia'];


                if(!$this->_armarObjetoElastic($datosRegistro, $datosBusqueda, $datosElastic))
                    return false;

                $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
                if(!$oElastic->Insertar((array) $datosElastic, $datosElastic)) {
                    $this->setError($oElastic->getError());
                    print_r($oElastic->getError() );
                    return false;
                }

                $oAuditoriasEscuelasPuestosDesempeno = new cAuditoriasEscuelasPuestosDesempeno($this->conexion,$this->formato);
                $datos['IdDesempeno'] = $codigoInsertado;
                $datos['Accion'] = INSERTAR;
                if(!$oAuditoriasEscuelasPuestosDesempeno->InsertarLog($datos,$codigoInsertadolog))
                    return false;
            }

        } else {

            if (!parent::Insertar($datos,$codigoInsertado))
                return false;

            $datosBusqueda = array();
            $datosRegistro['IdDesempeno'] = $codigoInsertado;
            if(!$this->_armarObjetoElastic($datosRegistro, $datosBusqueda, $datosElastic))
                return false;

            $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
            if(!$oElastic->Insertar((array) $datosElastic, $datosElastic)) {
                $this->setError($oElastic->getError());
                print_r($oElastic->getError() );
                return false;
            }

            $oAuditoriasEscuelasPuestosDesempeno = new cAuditoriasEscuelasPuestosDesempeno($this->conexion,$this->formato);
            $datos['IdDesempeno'] = $codigoInsertado;
            $datos['Accion'] = INSERTAR;
            if(!$oAuditoriasEscuelasPuestosDesempeno->InsertarLog($datos,$codigoInsertadolog))
                return false;
        }

		return true;
	}



    public function InsertarEvento($datos, &$codigoInsertado): bool
    {
        $oEscuelasPuestos = new cEscuelasPuestos($this->conexion,null,$this->formato);
        if(!$oEscuelasPuestos->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;
        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            // $this->setError(400,"Error debe ingresar un código valido.");
            return false;
        }

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!$this->_ValidarDatosVacios($datos))
            return false;

        if (FuncionesPHPLocal::isEmpty($datos['IgnorarConflictos'])) {

            $oEscuelasPuestosPersonas = new cEscuelasPuestosPersonas($this->conexion);
            if (!$oEscuelasPuestosPersonas->BuscarPersonaxIdPuesto($datos, $resultadoPersona, $numfilasPersona)) {
                $this->setError($oEscuelasPuestosPersonas->getError());
                return false;
            }

            if ($numfilasPersona > 0) {

                $oEscuelasPuestos = new cEscuelasPuestos($this->conexion);
                $oIncompatibilidades = new Elastic\Incompatibilidades($this->conexionES);

                while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoPersona)) {

                    try {
                        $saltear = $oIncompatibilidades->verificarAgenteIgnorable($fila);
                    } catch (ExcepcionLogica $e) {
                        $this->setError($e->getError());
                        return false;
                    }
                    if ($saltear)
                        continue;

                    if (!$oEscuelasPuestos->BuscarNombrePuesto($datos, $resultadoPuesto, $numfilasPuesto)) {
                        $this->setError($oEscuelasPuestos->getError());
                        return false;
                    }

                    $NombrePuesto = '-';
                    if ($numfilasPuesto > 0) {
                        $NombrePuesto = $this->conexion->ObtenerSiguienteRegistro($resultadoPuesto)['PuestoNombre'];
                    }

                    if (!$oEscuelasPuestos->BuscarNombreEscuelaPuesto($datos, $resultadoPuesto, $numfilasPuesto)) {
                        $this->setError($oEscuelasPuestos->getError());
                        return false;
                    }

                    $NombreEscuela = '-';
                    if ($numfilasPuesto > 0) {
                        $NombreEscuela = $this->conexion->ObtenerSiguienteRegistro($resultadoPuesto)['Nombre'];
                    }

                    $datos['NombreCompleto'] = $fila['NombreCompleto'];
                    $datos['IdPersona'] = $fila['IdPersona'];
                    $datos['POFA'] = true;
                    $datosNuevo = ['desempeno' => [], 'horas' => []];
                    $idPuesto = $datos['IdPuesto'] . 'p';
                    $dia = (int)$datos['Dia'];
                    $horario = new stdClass();
                    $horario->gte = new DateTime($datos['HoraInicio']);
                    $datos['HoraInicio'] = substr($datos['HoraInicio'], 0, 5);
                    $horario->lte = new DateTime($datos['HoraFin']);
                    $datos['HoraFin'] = substr($datos['HoraFin'], 0, 5);
                    $datosNuevo['desempeno'][$dia][$idPuesto] = [
                        'id' => (int)$datos['IdPuesto'],
                        'dia' => $datos['Dia'],
                        'horario' => (object)['gte' => $datos['HoraInicio'], 'lte' => $datos['HoraFin']],
                        'desde' => substr($datos['HoraInicio'], 0, 5),
                        'hasta' => substr($datos['HoraFin'], 0, 5),
                        'puesto' => [
                            'Cargo'   => ['Descripcion' => utf8_encode($NombrePuesto)],
                            'Escuela' => ['Nombre' => utf8_encode($NombreEscuela)],
                            'Nivel'   => ['Descripcion' => '-']
                        ]
                    ];
                    $datosNuevo['horas'][$dia][$idPuesto] = $horario;

                    try {
                        if (!$oIncompatibilidades->validarSuperposicionHoraria($datos, $resumen, $datosNuevo)) {
                            $this->setError($oIncompatibilidades->getError());
                            return false;
                        }
                    } catch (Exception $e) {
                        $this->setError(500, $e->getMessage());
                        return false;
                    }

                    if ($resumen['hay_conflictos']) {
                        $this->setError(409, json_encode($resumen['colisiones']));
                        $this->error['nombre'] = $datos['NombreCompleto'];
                        return false;
                    }
                }
            }
        }

        if (!$this->validarSimultaneidad($datos, $datosRegistro))
            return false;

        $this->_SetearNull($datos);
        $datos['AltaFecha']=date("Y-m-d H:i:s");
        $datos['AltaUsuario']=$_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;
        if (!parent::Insertar($datos,$codigoInsertado))
            return false;

        if (!$this->_ValidarHorariosModulos($datos))
            return false;

        $oAuditoriasEscuelasPuestosDesempeno = new cAuditoriasEscuelasPuestosDesempeno($this->conexion,$this->formato);
        $datos['IdDesempeno'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        if(!$oAuditoriasEscuelasPuestosDesempeno->InsertarLog($datos,$codigoInsertadolog))
            return false;

        $datosBusqueda = array();
        $datosRegistro['IdDesempeno'] = $datos['IdDesempeno'];
        if(!$this->_armarObjetoElastic($datosRegistro, $datosBusqueda, $datosElastic))
            return false;
        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if(!$oElastic->Insertar((array) $datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            print_r($oElastic->getError() );
            return false;
        }

        return true;
    }


    public function validarSimultaneidad(array $datos, array $datosRegistro): bool {
        if (!parent::BuscarRangoHorarioxSeccion($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            if ($numfilas == 1) {
                $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);

                if ($fila['PermiteSimultaneo'] == 0 || $datosRegistro['PermiteSimultaneo'] == 0) {
                    $this->setError(400, 'La Materia/ Cargo  no permite simultaneidad de horario.');
                    return false;
                }
            } else {

                $PermiteSimultaneo = (bool)$datosRegistro['PermiteSimultaneo'];

                if ($PermiteSimultaneo) {
                    while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                        if ($fila['PermiteSimultaneo'] == 0 && $PermiteSimultaneo)
                            $PermiteSimultaneo = false;
                    }
                }

                if (!$PermiteSimultaneo) {
                    $this->setError(400, 'La Materia/ Cargo  no permite simultaneidad de horario.');
                    return false;
                }
            }
        }
        return true;
    }

	public function Modificar($datos): bool
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

        if (!$this->_ValidarHorariosModulos($datos))
            return false;

		$oAuditoriasEscuelasPuestosDesempeno = new cAuditoriasEscuelasPuestosDesempeno($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasPuestosDesempeno->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;


		return true;
	}


    public function ModificarHoraEvento($datos): bool
    {
        if (!$this->BuscarxIdPuestoxIdDesempeno($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
           // $this->setError(400,"Error debe ingresar un código valido.");
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        if (!$this->_ValidarDatosVacios($datos))
            return false;


        if (FuncionesPHPLocal::isEmpty($datos['IgnorarConflictos'])) {

            $oEscuelasPuestosPersonas = new cEscuelasPuestosPersonas($this->conexion);
            if (!$oEscuelasPuestosPersonas->BuscarPersonaxIdPuesto($datos, $resultadoPersona, $numfilasPersona)) {
                $this->setError($oEscuelasPuestosPersonas->getError());
                return false;
            }

            if ($numfilasPersona > 0) {

                $oEscuelasPuestos = new cEscuelasPuestos($this->conexion);
                $oIncompatibilidades = new Elastic\Incompatibilidades($this->conexionES);

                while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoPersona)) {
                    try {
                        $saltear = $oIncompatibilidades->verificarAgenteIgnorable($fila);
                    } catch (ExcepcionLogica $e) {
                        $this->setError($e->getError());
                        return false;
                    }
                    if ($saltear)
                        continue;

                    if (!$oEscuelasPuestos->BuscarNombrePuesto($datos, $resultadoPuesto, $numfilasPuesto)) {
                        $this->setError($oEscuelasPuestos->getError());
                        return false;
                    }

                    $NombrePuesto = '-';
                    if ($numfilasPuesto > 0) {
                        $NombrePuesto = $this->conexion->ObtenerSiguienteRegistro($resultadoPuesto)['PuestoNombre'];
                    }

                    if (!$oEscuelasPuestos->BuscarNombreEscuelaPuesto($datos, $resultadoPuesto, $numfilasPuesto)) {
                        $this->setError($oEscuelasPuestos->getError());
                        return false;
                    }

                    $NombreEscuela = '-';
                    if ($numfilasPuesto > 0) {
                        $NombreEscuela = $this->conexion->ObtenerSiguienteRegistro($resultadoPuesto)['Nombre'];
                    }

                    $datos['NombreCompleto'] = $fila['NombreCompleto'];
                    $datos['IdPersona'] = $fila['IdPersona'];
                    $datos['POFA'] = true;
                    $datos['PuestosIgnorar'] = [$datos['IdPuesto']];
                    $datosNuevo = ['desempeno' => [], 'horas' => []];
                    $idPuesto = $datos['IdPuesto'] . 'p';
                    $dia = (int)$datos['Dia'];
                    $horario = new stdClass();
                    $horario->gte = new DateTime($datos['HoraInicio']);
                    $datos['HoraInicio'] = substr($datos['HoraInicio'], 0, 5);
                    $horario->lte = new DateTime($datos['HoraFin']);
                    $datos['HoraFin'] = substr($datos['HoraFin'], 0, 5);
                    $datosNuevo['desempeno'][$dia][$idPuesto] = [
                        'id' => (int)$datos['IdPuesto'],
                        'dia' => $datos['Dia'],
                        'horario' => (object)['gte' => $datos['HoraInicio'], 'lte' => $datos['HoraFin']],
                        'desde' => substr($datos['HoraInicio'], 0 , 5),
                        'hasta' => substr($datos['HoraFin'], 0 , 5),
                        'puesto' => [
                            'Cargo'   => ['Descripcion' => utf8_encode($NombrePuesto)],
                            'Escuela' => ['Nombre' => utf8_encode($NombreEscuela)],
                            'Nivel'   => ['Descripcion' => '-']
                        ]
                    ];
                    $datosNuevo['horas'][$dia][$idPuesto] = $horario;
                    try {
                        if (!$oIncompatibilidades->validarSuperposicionHoraria($datos, $resumen, $datosNuevo)) {
                            $this->setError($oIncompatibilidades->getError());
                            return false;
                        }
                    } catch (Exception $e) {
                        $this->setError(500, $e->getMessage());
                        return false;
                    }

                    if ($resumen['hay_conflictos']) {
                        $this->setError(409, json_encode($resumen['colisiones']));
                        $this->error['nombre'] = $datos['NombreCompleto'];
                        return false;
                    }
                }
            }
        }

        if (!parent::BuscarRangoHorarioxSeccion($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0)
        {
            if($numfilas==1)
            {
                $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);



                if($datos['IdDesempeno']!=$fila['IdDesempeno'])
                {
                    if($fila['PermiteSimultaneo']==0 ||  $datosRegistro['PermiteSimultaneo']==0)
                    {
                        $this->setError(400, "La Materia/ Cargo  no permite simultaneidad de horario." );
                        return false;
                    }

                }
            }
            else
            {

                $PermiteSimultaneo = true;
                if($datosRegistro['PermiteSimultaneo']==0)
                    $PermiteSimultaneo= false;

                if($PermiteSimultaneo)
                {
                    while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado) )
                    {
                        if($fila['PermiteSimultaneo']==0 && $PermiteSimultaneo)
                            $PermiteSimultaneo= false;
                    }
                }

                if(!$PermiteSimultaneo)
                {
                    $this->setError(400, "La Materia/ Cargo  no permite simultaneidad de horario." );
                    return false;
                }

            }
        }

        $datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;

        if (!$this->_ValidarHorariosModulos($datos))
            return false;

        $oAuditoriasEscuelasPuestosDesempeno = new cAuditoriasEscuelasPuestosDesempeno($this->conexion,$this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if(!$oAuditoriasEscuelasPuestosDesempeno->InsertarLog($datosRegistro,$codigoInsertadolog))
            return false;

        $datosBusqueda = array();
        if(!$this->_armarObjetoElastic($datosRegistro, $datosBusqueda, $datosElastic))
            return false;
        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if(!$oElastic->Actualizar((array) $datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            print_r($oElastic->getError() );
            return false;
        }


        return true;
    }





	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasPuestosDesempeno = new cAuditoriasEscuelasPuestosDesempeno($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasEscuelasPuestosDesempeno->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		$datosEliminar['IdPuesto'] = $datos['IdPuesto'];
        $datosEliminar['IdDesempeno'] = $datos['IdDesempeno'];
		if (!parent::EliminarxIdPuestoxIdDesempeno($datosEliminar)) {
            $this->setError(400,"Error al eliminar el desempeno del puesto.");
		    return false;
        }
		$datosEliminar['Id'] = $datos['IdDesempeno'];
        $datosEliminar['Tipo']['parent'] = $datosRegistro['IdPuesto'];
        $datosEliminar['Tipo']['name'] = "Desempeno";
        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if(!$oElastic->Eliminar($datosEliminar)) {
            print_r($oElastic->getError());
            $this->setError(400,"Error al eliminar el desempeno del puesto.");
            return false;
        }

		return true;
	}




    public function EliminarxIdPuestoxIdDesempeno($datos): bool
    {
        if (!$this->_ValidarEliminar($datos,$datosRegistro))
            return false;

        $oAuditoriasEscuelasPuestosDesempeno = new cAuditoriasEscuelasPuestosDesempeno($this->conexion,$this->formato);
        $datosLog =$datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if(!$oAuditoriasEscuelasPuestosDesempeno->InsertarLog($datosLog,$codigoInsertadolog))
            return false;

        $datosmodif['IdPuesto'] = $datos['IdPuesto'];
        $datosmodif['IdDesempeno'] = $datos['IdDesempeno'];
        if (!parent::EliminarxIdPuestoxIdDesempeno($datosmodif))
            return false;

        $datosEliminar['Id'] = $datos['IdDesempeno'];
        $datosEliminar['Tipo']['parent'] = $datosRegistro['IdPuesto'];
        $datosEliminar['Tipo']['name'] = "Desempeno";
        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if(!$oElastic->Eliminar($datosEliminar)) {
            print_r($oElastic->getError());
            $this->setError(400,"Error al eliminar el desempeno del puesto.");
            return false;
        }

        return true;
    }
    public function EliminarxIdPuesto($datos): bool
    {
        $datosEliminar['IdPuesto'] = $datos['IdPuesto'];
        if (!parent::Eliminar($datosEliminar)) {
            $this->setError(400,"Error al eliminar el desempeno del puesto.");
            return false;
        }

        $cuerpo = Base::nueva(null, null)
            ->setQuery(
                Query::bool()
                ->addFilter(Query::term('Tipo', 'Desempeno'))
                ->addFilter(Query::has_parent('Puesto', Elastic\Consultas\Query::term('Id',$datos['IdPuesto'])))
            );

        $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
        if(!$oElastic->EliminarxQuery($cuerpo)) {
            print_r($oElastic->getError());
            $this->setError(400,"Error al eliminar el horario del puesto.");
            return false;
        }

        return true;
    }

	public function ModificarEstado($datos): bool
	{
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}


	public function Activar(array $datos): bool
	{
		$datosmodif['IdPuesto'] = $datos['IdPuesto'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasPuestosDesempeno = new cAuditoriasEscuelasPuestosDesempeno($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasPuestosDesempeno->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdPuesto'] = $datos['IdPuesto'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasEscuelasPuestosDesempeno = new cAuditoriasEscuelasPuestosDesempeno($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEscuelasPuestosDesempeno->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

    public function _armarObjetoElastic(array $datos, ?array &$datosRegistro, &$datosElastic): bool {

        if(empty($datosRegistro)) {

            if(!$this->BuscarxIdDesempeno($datos, $resultado, $numfilas))
                return false;
            if($numfilas != 1) {
                $this->setError(400,"Error al buscar horarios.");
                return false;
            }

            $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

            $dias = FuncionesPHPLocal::ObtenerDiasSemanaNumerico();
            $datosRegistro['Id'] = $datosRegistro['IdDesempeno'];
            $datosRegistro['Tipo'] = "Desempeno";
            $datosRegistro['DiaNumero'] =$datosRegistro['Dia'];
            $datosRegistro['DiaDescripcion'] = $dias[$datosRegistro['Dia']];
            $datosRegistro['HorarioDesde'] = substr($datosRegistro['HoraInicio'],0,5);
            $datosRegistro['HorarioHasta'] = substr($datosRegistro['HoraFin'],0,5);
        }

        try {
            $datosElastic = Elastic\Puestos::armarDatosElastic($datosRegistro);
        } catch (Exception $e) {
            $this->setError($e->getCode(), $e->getMessage());
            return false;
        }

        return true;
    }

    public function buscarParaElasticxEscuela($datos, &$resultado, &$numfilas):bool {
        return parent::buscarParaElasticxEscuela($datos, $resultado, $numfilas);
    }


    private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;

        if ($datos['Dia'] == 0) {

            $Dias = [1, 2, 3, 4, 5];

            foreach ($Dias as $r) {

                $datosBusqueda = $datos;
                $datosBusqueda['Dia'] = $r;
                if (!parent::BuscarRangoHorario($datosBusqueda, $resultado, $numfilas))
                    return false;

                if ($numfilas > 0) {
                    $this->setError(400,"Actualmente ya existe el horario.");
                    return false;
                }
            }

        } else {

            if (!parent::BuscarRangoHorario($datos, $resultado, $numfilas))
                return false;

            if ($numfilas > 0)
            {
                $this->setError(400,"Actualmente ya existe el horario.");
                return false;
            }
        }

		return true;
	}

	private function _ValidarHorariosModulos($datos)
    {
        $oEscuelasPuestos = new cEscuelasPuestos($this->conexion,$this->conexionES,$this->formato);
        if(!$oEscuelasPuestos->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        $filaEscuelasPuestos = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if(!FuncionesPHPLocal::isEmpty($filaEscuelasPuestos["IdTipo"]))
            $IdTipo = $filaEscuelasPuestos["IdTipo"];
        else
            $IdTipo = $filaEscuelasPuestos["CargoIdTipo"];

        $CantHoras = $filaEscuelasPuestos['CantHoras'];
        $CantModulos = $filaEscuelasPuestos['CantModulos'];
        $cero = new DateTime('today midnight');
        $TotalHoras = clone $cero;
        if($CantHoras!="" &&  $CantHoras!=0) {
            //$IdTipo = 1; // horas
            $Cantidad = $CantHoras;
            $minutesToAdd = CANT_HORAS_PUESTO;
        } else {
            //$IdTipo= 2; //modulos
            $Cantidad = $CantModulos;
            $minutesToAdd = CANT_MODULOS_PUESTO;
        }

        for($i=1;$i<=$Cantidad;$i++)
            $TotalHoras->modify("+{$minutesToAdd} minutes");


        if(!$this->BuscarHorasxIdPuestoxIdSeccion($datos,$resultadoHoras,$numfilasHoras))
            return false;


        $Total = clone $cero;
        //echo $Total->format('H:i:s').'<br/>';
        while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoHoras))
        {
            $hora_ini =  new DateTime($fila['HoraInicio']);
            $hora_fin =  new DateTime($fila['HoraFin']);
            //$dif=date("H:i:s", strtotime("00:00:00") + strtotime($hora_fin) - strtotime($hora_ini));
            $dif = $hora_fin->diff($hora_ini, true);
            //echo $dif->format('%H:%I:%S')."<BR>";
            $Total->add($dif);

        }
        //echo "TotalHoras: ".$TotalHoras->format('H:i:s')."<BR>";//date("H:i:s",$Total)
        //echo "Total: ".$Total->format('H:i:s')."<BR>";//date("H:i:s",$Total)

        //$TotalHoras = $TotalHoras->format('H:i:s');//date("H:i:s",$Total)
        //$Total = $Total->format('H:i:s');//date("H:i:s",$Total)

        $fecha = date("Y-m-d");

        //die();
        if($Total > $TotalHoras) {
            $this->setError(400, "Error, tiene más cantidad de horas cargadas que las permitidas.");
            return false;
        }
        return true;
    }


	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxIdPuestoxIdDesempeno($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			$this->setError(400,"Error debe ingresar un código valido.");
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;

        if (!parent::BuscarRangoHorario($datos, $resultado, $numfilas))
            return false;


        if ($numfilas > 0)
        {
            $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);

            if($datos['IdDesempeno']!=$fila['IdDesempeno'])
            {
                $this->setError(400,"Actualmente el rango de horas se encuentra dentro de otro existente.");
                return false;
            }

        }
		return true;
	}


	private function _ValidarEliminar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxIdPuestoxIdDesempeno($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			$this->setError(400,"Error debe ingresar un código valido.");
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}


	private function _SetearNull(&$datos): void
	{

        if (!isset($datos['IdPuesto']) || $datos['IdPuesto']=="")
            $datos['IdPuesto']="NULL";

		if (!isset($datos['IdDesempeno']) || $datos['IdDesempeno']=="")
			$datos['IdDesempeno']="NULL";

		if (!isset($datos['Dia']) || $datos['Dia']=="")
			$datos['Dia']="NULL";

		if (!isset($datos['HoraInicio']) || $datos['HoraInicio']=="")
			$datos['HoraInicio']="NULL";

		if (!isset($datos['HoraFin']) || $datos['HoraFin']=="")
			$datos['HoraFin']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

	}


	private function _ValidarDatosVacios($datos)
	{
		if (!isset($datos['Dia']) || $datos['Dia']=="")
		{
			$this->setError(400,"Debe ingresar un día");
			return false;
		}

		if (!isset($datos['HoraInicio']) || $datos['HoraInicio']=="")
		{
			$this->setError(400,"Debe ingresar una Hora de Inicio");
			return false;
		}

		if (!isset($datos['HoraFin']) || $datos['HoraFin']=="")
		{
			$this->setError(400,"Debe ingresar una Hora de Fin");
			return false;
		}

        if ((isset($datos['HoraInicio']) && $datos['HoraInicio']!="") && (isset($datos['HoraFin']) || $datos['HoraFin']!=""))
        {
            if (strtotime($datos['HoraInicio']) >= strtotime($datos['HoraFin'])) {
                $this->setError(400,"La hora de fin debe ser mayor a la de inicio");
                return false;
            }
        }

        return true;
	}

    public function duplicarDesempenos(array $datos): bool {
        $desde = date_create_immutable($datos['FechaDesde']);
        $hasta = date_create_immutable($datos['FechaHasta'])->modify('+1 day');
        if (false === $desde || false === $hasta) {
            $this->setError(400, 'Fecha incorrecta');
            return false;
        }
        $datos['Opcion'] = 0;
        $datos['DiaSemanaDesde'] = $desde->format('N');
        $datos['DiaSemanaHasta'] = $hasta->format('N');

        if ($hasta->diff($desde)->days < 7) {
            switch( $desde <=> $hasta) {
                case -1:
                    $datos['Opcion'] = 1;
                    break;
                case 1:
                    $datos['Opcion'] = 2;
                    break;
                case 0:
                    $datos['Opcion'] = 3;
                    break;
            }
        }

        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        if (!parent::duplicarDesempenos($datos))
            return false;

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            unset($datosRegistro);
            if (!$this->_armarObjetoElastic($fila, $datosRegistro, $datosElastic))
                return false;
            $oElastic = new Elastic\Modificacion(SUFFIX_PUESTOS, $this->conexionES);
            if (!$oElastic->Insertar($datosElastic)) {
                $this->setError($oElastic->getError());
                return false;
            }
        }

        return true;
    }


}
