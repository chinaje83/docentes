<?php
abstract class cEscuelasPOFDB
{
	/** @var accesoBDLocal  */
	protected $conexion;
	/** @var mixed  */
	protected $formato;
	/** @var array  */
	protected $error;
	/**
	 * Constructor de la clase cEscuelasPOFDB.
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
	 * Destructor de la clase cEscuelasPOFDB.
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

	protected function BuscarxCodigo(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasPOF_xIdPof";
		$sparam=array(
			'pIdPof'=> $datos['IdPof']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			$this->setError(400,"Error al buscar al buscar por codigo. ");
			return false;
		}
		return true;
	}

    protected function BuscarPOFAEditable(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPOF_xIdEscuela";
        $sparam=array(
            'pIdEscuela'=> $datos['IdEscuela']
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
		$spnombre="sel_EscuelasPOF_busqueda_avanzada";
		$sparam=array(
			'pxIdEscuela'=> $datos['xIdEscuela'],
			'pIdEscuela'=> $datos['IdEscuela'],
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


	protected function BuscarAuditoriaRapida(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasPOF_AuditoriaRapida";
		$sparam=array(
			'pIdPof'=> $datos['IdPof']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			$this->setError(400,"Error al buscar al buscar por codigo. ");
			return false;
		}
		return true;
	}


    protected function BuscarExistente(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_EscuelasPOF_xIdEscuela_xIdCicloLectivo";
        $sparam=array(
            'pIdEscuela'=> $datos['IdEscuela']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


	protected function Insertar(array $datos, ?int &$codigoInsertado): bool
	{
		$spnombre="ins_EscuelasPOF";
		$sparam=array(
			'pIdEscuela'=> $datos['IdEscuela'],
			'pPofEditable'=> $datos['PofEditable'],
			'pPofaEditable'=> $datos['PofaEditable'],
			'pPofaAdminEditable' => $datos['PofaAdminEditable'],
			'pPermiteCargaDesempeno' => $datos['PermiteCargaDesempeno'] ?? 0,
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario']
		);
		if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
		    $this->setError(400, 'Error al insertar datos de escuela');
			return false;
		}
		$codigoInsertado=$this->conexion->UltimoCodigoInsertado();
		return true;
	}


	protected function Modificar(array $datos): bool
	{
		$spnombre="upd_EscuelasPOF_xIdPof";
		$sparam=array(
			'pIdEscuela'=> $datos['IdEscuela'],
			'pPofEditable'=> $datos['PofEditable'],
			'pPofEcEditable'=> $datos['PofEcEditable'],
			'pPofaEditable'=> $datos['PofaEditable'],
            'pPofaAdminEditable'=> $datos['PofaAdminEditable'],
            'pPermiteCargaDesempeno'=> $datos['PermiteCargaDesempeno'],
			'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdPof'=> $datos['IdPof']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al modificar. ");
			return false;
		}
		return true;
	}


    protected function modificarxIdEscuela(array $datos): bool
    {
        $spnombre = 'upd_EscuelasPOF_xIdEscuela';
        $sparam = [
            'pPofEditable' => $datos['PofEditable'],
            'pPofaEditable' => $datos['PofaEditable'],
            'pPofaAdminEditable' => $datos['PofaAdminEditable'],
            'pPofEcEditable' => $datos['PofEcEditable'],
            'pPermiteCargaDesempeno' => $datos['PermiteCargaDesempeno'],
            'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pIdEscuela' => $datos['IdEscuela'],
        ];

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400, 'Error al modificar por escuela');
            return false;
        }
        return true;
    }


    protected function ModificarPOF(array $datos): bool
    {
        $spnombre="upd_EscuelasPOF_PofEditable";
        $sparam=array(
            'pPofEditable'=> $datos['PofEditable'],
            'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pIdPof'=> $datos['IdPof']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400, 'Error al modificar');
            return false;
        }
        return true;
    }


    protected function ModificarPOFA(array $datos): bool
    {
        $spnombre="upd_EscuelasPOF_PofaEditable";
        $sparam=array(
            'pPofaEditable'=> $datos['PofaEditable'],
            'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pIdPof'=> $datos['IdPof']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400, 'Error al modificar');
            return false;
        }
        return true;
    }

	protected function Eliminar(array $datos): bool
	{
		$spnombre="del_EscuelasPOF_xIdPof";
		$sparam=array(
			'pIdPof'=> $datos['IdPof']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al eliminar por codigo. ");
			return false;
		}
		return true;
	}


	protected function BuscarUltimoOrden(array $datos,  &$resultado, ?int &$numfilas): bool
	{
		$spnombre="sel_EscuelasPOF_max_orden";
		$sparam=array();
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			$this->setError(400,"Error al buscar el maximo orden. ");
			return false;
		}
		return true;
	}


	protected function ModificarOrden(array $datos): bool
	{
		$spnombre="upd_EscuelasPOF_IdPof_xIdPof";
		$sparam=array(
			'pIdPof'=> $datos['IdPof'],
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			$this->setError(400,"Error al modificar el orden. ");
			return false;
		}
		return true;
	}


	protected function ModificarEstado(array $datos): bool
	{
		$spnombre="upd_EscuelasPOF_Estado_xIdPof";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pIdPof'=> $datos['IdPof']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
            $this->setError(400, 'Error al modificar el estado.');
			return false;
		}
		return true;
	}


    protected function CerrarPOFA(array $datos): bool
    {
        $spnombre="upd_EscuelasPOF_PofaEditable";
        $sparam=array(
            'pPofaEditable' => 0,
            'pIdPof'=> $datos['IdPof'],
            'pUltimaModificacionFecha'=>$datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario'=>$datos['UltimaModificacionUsuario']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            $this->setError(400,"Error al modificar el estado. ");
            return false;
        }
        return true;
    }
    protected function DesbloquearCargaDesempeno(array $datos):bool
    {
        $spnombre="upd_EscuelasPOF_xIdEscuela_PermiteCargaDesempeno";
        $sparam=array(
            'pPermiteCargaDesempeno'=>$datos['PermiteCargaDesempeno'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pUltimaModificacionFecha'=>$datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario'=>$datos['UltimaModificacionUsuario']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            $this->setError(400, 'Error al modificar el estado.');
            return false;
        }
            return true;
    }


}
