<?php
abstract class cEscuelasPuestosDesempenoDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cEscuelasPuestosDesempenoDB.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion,$formato){

		$this->conexion = &$conexion;
		$this->formato = &$formato;
	}

	/**
	 * Destructor de la clase cEscuelasPuestosDesempenoDB.
	 */
	function __destruct(){}

	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public abstract function getError(): array;


	/**
	 * Guarda un mensaje de error
	 *
	 * @param string|array  $error
	 * @param string        $error_description
	 */
	protected function setError($error,$error_description=''): void {
		$this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
	}

    public function duplicarDesempenos(array $datos) {

        $spnombre = 'ins_EscuelasPuestosDesempeno_duplicados_xIdPuesto';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pOpcion' => $datos['Opcion'],
            'pDiaSemanaDesde' => $datos['DiaSemanaDesde'],
            'pDiaSemanaHasta' => $datos['DiaSemanaHasta'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pIdPuestoPadre' => $datos['IdPuestoPadre'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar al buscar por codigo. ');
            return false;
        }

        return true;
    }

    protected function BuscarxCodigo(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasPuestosDesempeno_xIdPuesto";
		$sparam=array(
			'pIdPuesto'=> $datos['IdPuesto']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			$this->setError(400,"Error al duplicar los desempenos. ");
			return false;
		}
		return true;
	}


    protected function BuscarxCodigoxEstado(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPuestosDesempeno_xIdPuesto_Estado";
        $sparam=array(
            'pIdPuesto'=> $datos['IdPuesto'],
            'pEstado'=> $datos['Estado']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarxIdDesempeno(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPuestosDesempeno_xIdDesempeno";
        $sparam=array(
            'pIdDesempeno'=> $datos['IdDesempeno']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarxIdPuestoxIdDesempeno(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPuestosDesempeno_xIdPuesto_xIdDesempeno";
        $sparam=array(
            'pIdPuesto'=> $datos['IdPuesto'],
            'pIdDesempeno' => $datos['IdDesempeno']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarxIdPuestoxIdSeccionxIdCicloLectivo(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPuestosDesempeno_xIdSeccion_xIdCicloLectivo";
        $sparam=array(
            'pIdSeccion'=> $datos['IdSeccion'],
            'pIdCicloLectivo' => $datos['IdCicloLectivo']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function buscarTotalDesempenosxEscuelas(&$resultado, ?int &$numfilas): bool
    {
        $spnombre = 'sel_EscuelasPuestosDesempenos_total_habilitadas';
        $sparam = [
            'pIdEscuelaExcluir' => ESCUELAS_DE_PRUEBA

        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarxIdPuestoxIdSeccion(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPuestosDesempeno_xIdSeccion";
        $sparam=array(
            'pIdSeccion'=> $datos['IdSeccion'],
            'pxPuestoPadre' => $datos['xPuestoPadre'],
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarHorasxIdPuestoxIdSeccion(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPuestosDesempeno_xIdSeccion_IdPuesto";
        $sparam=array(
            'pIdSeccion'=> $datos['IdSeccion'],
            'pIdPuesto'=> $datos['IdPuesto']

        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }




	protected function BusquedaAvanzada(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasPuestosDesempeno_busqueda_avanzada";
		$sparam=array(
			'pxIdPuesto'=> $datos['xIdPuesto'],
			'pIdPuesto'=> $datos['IdPuesto'],
			'pxDia'=> $datos['xDia'],
			'pDia'=> $datos['Dia'],
			'pxHoraInicio'=> $datos['xHoraInicio'],
			'pHoraInicio'=> $datos['HoraInicio'],
			'pxHoraFin'=> $datos['xHoraFin'],
			'pHoraFin'=> $datos['HoraFin'],
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado'],
			'plimit'=> $datos['limit'],
			'porderby'=> $datos['orderby']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			$this->setError(400,"Error al realizar la búsqueda avanzada. ");
			return false;
		}
		return true;
	}

    protected function BusquedaAvanzadaPofa(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPuestosDesempeno_busqueda_avanzada_pofa";
        $sparam=array(
            'pxIdEscuela'=> $datos['xIdEscuela'],
            'pIdEscuela'=> $datos['IdEscuela'],
            'pxIdCicloLectivo'=> $datos['xIdCicloLectivo'],
            'pIdCicloLectivo'=> $datos['IdCicloLectivo'],
            'pxIdNivelModalidad'=> $datos['xIdNivelModalidad'],
            'pIdNivelModalidad'=> $datos['IdNivelModalidad'],
            'pxIdTurno'=> $datos['xIdTurno'],
            'pIdTurno'=> $datos['IdTurno'],
            'pxIdGradoAnio'=> $datos['xIdGradoAnio'],
            'pIdGradoAnio'=> $datos['IdGradoAnio'],
            'pxIdSeccion'=> $datos['xIdSeccion'],
            'pIdSeccion'=> $datos['IdSeccion'],
            'porderby'=> $datos['orderby'],
            'plimit'=> $datos['limit']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al realizar la búsqueda avanzada. ");
            return false;
        }
        return true;
    }

    protected function BuscarRangoHorario(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPuestosDesempeno_xHoraInicio_xHoraFin";
        $sparam=array(
            'pIdPuesto'=> $datos['IdPuesto'],
            'pDia' => $datos['Dia'],
            'pHoraInicio' => $datos['HoraInicio'],
            'pHoraFin' => $datos['HoraFin']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarRangoHorarioxSeccion(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPuestosDesempeno_xHoraInicio_xHoraFin_IdSeccion";
        $sparam=array(
            'pIdSeccion'=> $datos['IdSeccion'],
            'pDia' => $datos['Dia'],
            'pHoraInicio' => $datos['HoraInicio'],
            'pHoraFin' => $datos['HoraFin']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }




    protected function BuscarAuditoriaRapida(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasPuestosDesempeno_AuditoriaRapida";
		$sparam=array(
			'pIdPuesto'=> $datos['IdPuesto']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			$this->setError(400,"Error al buscar al buscar por codigo. ");
			return false;
		}
		return true;
	}


    protected function BuscarUltimosInsertados(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPuestosDesempeno_xIdPuesto_xAltaFecha";
        $sparam=array(
            'pIdPuesto'=> $datos['IdPuesto'],
            'pAltaFecha'=>$datos['AltaFecha']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function buscarParaElasticxEscuela(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_EscuelasPuestosDesempeno_xIdEscuela";
        $sparam = [
            'pIdEscuela'=> $datos['IdEscuela'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al buscar al buscar por escuela. ");
            return false;
        }
        return true;
    }


	protected function Insertar(array $datos, ?int &$codigoInsertado): bool
	{
        $spnombre="ins_EscuelasPuestosDesempeno";
		$sparam=array(
            'pIdPuesto'=> $datos['IdPuesto'],
            'pIdDesempeno'=> $datos['IdDesempeno'],
			'pDia'=> $datos['Dia'],
			'pHoraInicio'=> $datos['HoraInicio'],
			'pHoraFin'=> $datos['HoraFin'],
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al insertar. ");
			return false;
		}
		$codigoInsertado=$this->conexion->UltimoCodigoInsertado();
		return true;
	}


    protected function InsertarBulk(array $datos): bool
    {
        $spnombre="ins_EscuelasPuestosDesempeno_bulk";
        $sparam=array(
            'pIdPuesto'=> $datos['IdPuesto'],
            'pIdDesempeno'=> $datos['IdDesempeno'],
            'pDia_1'=> 1,
            'pDia_2'=> 2,
            'pDia_3'=> 3,
            'pDia_4'=> 4,
            'pDia_5'=> 5,
            'pHoraInicio'=> $datos['HoraInicio'],
            'pHoraFin'=> $datos['HoraFin'],
            'pEstado'=> $datos['Estado'],
            'pAltaFecha'=> $datos['AltaFecha'],
            'pAltaUsuario'=> $datos['AltaUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            $this->setError(400,"Error al insertar. ");
            return false;
        }
        return true;
    }


	protected function Modificar(array $datos): bool
	{
        $spnombre="upd_EscuelasPuestosDesempeno_xIdDesempeno";
		$sparam=array(
			'pDia'=> $datos['Dia'],
			'pHoraInicio'=> $datos['HoraInicio'],
			'pHoraFin'=> $datos['HoraFin'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pIdDesempeno'=> $datos['IdDesempeno']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al modificar. ");
			return false;
		}
		return true;
	}


	protected function Eliminar(array $datos): bool
	{
		$spnombre="del_EscuelasPuestosDesempeno_xIdPuesto";
		$sparam=array(
			'pIdPuesto'=> $datos['IdPuesto']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al eliminar por codigo. ");
			return false;
		}
		return true;
	}


    protected function EliminarxIdPuestoxIdDesempeno(array $datos): bool
    {
        $spnombre="del_EscuelasPuestosDesempeno_xIdPuesto_xIdDesempeno";
        $sparam=array(
            'pIdPuesto'=> $datos['IdPuesto'],
            'pIdDesempeno' => $datos['IdDesempeno']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            $this->setError(400,"Error al eliminar por codigo. ");
            return false;
        }
        return true;
    }

	protected function ModificarEstado(array $datos): bool
	{
		$spnombre="upd_EscuelasPuestosDesempeno_Estado_xIdPuesto_xIdDesempeno";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pIdPuesto'=> $datos['IdPuesto'],
            'pIdDesempeno' => $datos['IdDesempeno']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al modificar el estado. ");
			return false;
		}
		return true;
	}




}
