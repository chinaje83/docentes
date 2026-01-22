<?php
include(DIR_CLASES_DB."cMaterias.db.php");
class cMaterias extends cMateriasdb
{
	/**
	 * Constructor de la clase cMaterias.
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
	}
	/**
	 * Destructor de la clase cMaterias.
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


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xSCParcial'=> 0,
			'SCParcial'=> "",
			'xPermiteSimultaneo'=> 0,
			'PermiteSimultaneo'=> "",
			'xIdMateria'=> 0,
			'IdMateria'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
            'xCodigo'=> 0,
            'Codigo'=> "",
            'xIdModalidad' => 0,
            'IdModalidad' => "",
            'xIdMateriaPadre' => 0,
            'IdMateriaPadre' => "",
            'xTienePadre' => 0,
            'TienePadre' => "",
            'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdMateria ASC"
		);
		if (isset($datos['SCParcial']) && $datos['SCParcial'] != "")
		{
			$sparam['SCParcial'] = $datos['SCParcial'];
			$sparam['xSCParcial'] = 1;
		}
		if (isset($datos['PermiteSimultaneo']) && $datos['PermiteSimultaneo'] != "")
		{
			$sparam['PermiteSimultaneo'] = $datos['PermiteSimultaneo'];
			$sparam['xPermiteSimultaneo'] = 1;
		}

		if (isset($datos['IdMateria']) && $datos['IdMateria'] != "")
		{
			$sparam['IdMateria'] = utf8_decode($datos['IdMateria']);
			$sparam['xIdMateria'] = 1;
		}

        if (isset($datos['Nombre']) && $datos['Nombre'] != "")
        {
            $sparam['Nombre'] = utf8_decode($datos['Nombre']);
            $sparam['xNombre'] = 1;
        }

        if (isset($datos['IdModalidad']) && $datos['IdModalidad'] != "")
        {
            $sparam['IdModalidad'] = $datos['IdModalidad'];
            $sparam['xIdModalidad'] = 1;
        }
        if (isset($datos['IdMateriaPadre']) && $datos['IdMateriaPadre'] != "")
        {
            $sparam['IdMateriaPadre'] = $datos['IdMateriaPadre'];
            $sparam['xIdMateriaPadre'] = 1;
        }

        if (isset($datos['TienePadre']) && $datos['TienePadre'] != "")
        {
            $sparam['TienePadre'] = $datos['TienePadre'];
            $sparam['xTienePadre'] = 1;
        }

        if(isset($datos['Codigo']) && $datos['Codigo']!="")
        {
            $sparam['Codigo']= utf8_decode($datos['Codigo']);
            $sparam['xCodigo']= 1;
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


	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas): bool
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}


    public function BuscarCombo(&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarCombo($resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarComboPadres(&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarComboPadres($resultado,$numfilas))
            return false;
        return true;
    }



    public function Insertar($datos,&$codigoInsertado): bool
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);
		$this->ObtenerProximoOrden($datos,$proxorden);
		$datos['IdMateria'] = $proxorden;
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoInsertado))
			return false;
		$oAuditoriasMaterias = new cAuditoriasMaterias($this->conexion,$this->formato);
		$datos['IdMateria'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasMaterias->InsertarLog($datos,$codigoInsertadolog))
			return false;
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
		$oAuditoriasMaterias = new cAuditoriasMaterias($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasMaterias->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasMaterias = new cAuditoriasMaterias($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasMaterias->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdMateria'] = $datos['IdMateria'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
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
		$datosmodif['IdMateria'] = $datos['IdMateria'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasMaterias = new cAuditoriasMaterias($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasMaterias->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdMateria'] = $datos['IdMateria'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasMaterias = new cAuditoriasMaterias($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasMaterias->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function ModificarOrdenCompleto($datos): bool
	{
		$datosmodif['IdMateria'] = 1;
		$arregloOrden = explode(",",$datos['orden']);
		foreach ($arregloOrden as $IdMateria){
			$datosmodif['IdMateria'] = $IdMateria;
			if (!parent::ModificarOrden($datosmodif))
				return false;
			$datosmodif['IdMateria']++;
		}
		return true;
	}


	private function ObtenerProximoOrden(array $datos, ?int &$proxorden): bool
	{
		$proxorden = 0;
		if (!parent::BuscarUltimoOrden($datos,$resultado,$numfilas))
			return false;
		if ($numfilas!=0){
			$datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$proxorden = $datos['maximo'] + 1;
		}
		return true;
	}




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar(&$datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;

        $oModalidades = new cModalidades($this->conexion);

        if(!FuncionesPHPLocal::isEmpty($datos["IdMateriaPadre"])) {
            $datosBuscarPadre = ["IdMateria" => $datos["IdMateriaPadre"]];
            if (!$this->BuscarxCodigo($datosBuscarPadre,$resultadoPadre,$numfilasPadre))
                return false;
            if($numfilasPadre != 1) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no se encontro materia relacionada.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
            $datos["IdModalidad"] = ""; // la modalidad la toma del padre
        } else {
            if(!FuncionesPHPLocal::isEmpty($datos["IdModalidad"])) {  // modalidad por el momento es opcional
                if(!$oModalidades->BuscarxCodigo(["IdModalidad" => $datos["IdModalidad"]], $resultadoModalidad, $numfilasModalidad))
                    return false;
                if($numfilasModalidad!=1) {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no se encontro modalidad.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;
                }
            }
        }

		return true;
	}


	private function _ValidarModificar(&$datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

		if (!$this->_ValidarDatosVacios($datos))
			return false;

        $oModalidades = new cModalidades($this->conexion);

        if(!FuncionesPHPLocal::isEmpty($datos["IdMateriaPadre"])) {
            $datosBuscarPadre = ["IdMateria" => $datos["IdMateriaPadre"]];
            if (!$this->BuscarxCodigo($datosBuscarPadre,$resultadoPadre,$numfilasPadre))
                return false;
            if($numfilasPadre != 1) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no se encontro materia relacionada.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
            $datos["IdModalidad"] = ""; // la modalidad la toma del padre
        } else {
            // modalidad por el momento es opcional
            if(!FuncionesPHPLocal::isEmpty($datos["IdModalidad"])) {
                if(!$oModalidades->BuscarxCodigo(["IdModalidad" => $datos["IdModalidad"]], $resultadoModalidad, $numfilasModalidad))
                    return false;
                if($numfilasModalidad!=1) {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no se encontro modalidad.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;
                }
            }
        }
		return true;
	}


	private function _ValidarEliminar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}


	private function _SetearNull(&$datos): void
	{
		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if(!isset($datos['PermiteSimultaneo']) || $datos['PermiteSimultaneo']!='1')
            $datos['PermiteSimultaneo'] = '0';

        if (!isset($datos['SCParcial']) || $datos['SCParcial'] == '')
            $datos['SCParcial'] = '0';

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

        if (!isset($datos['IdMateriaPadre']) || $datos['IdMateriaPadre']=="")
            $datos['IdMateriaPadre']="NULL";

        if (!isset($datos['IdModalidad']) || $datos['IdModalidad']=="")
            $datos['IdModalidad']="NULL";
	}


	private function _ValidarDatosVacios($datos)
	{
		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			if (strlen($datos['Nombre'])>255)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Nombre no puede ser mayor a 255 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
        if (!isset($datos['IdExterno']) || $datos['IdExterno']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar IdExterno",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (!isset($datos['Codigo']) || $datos['Codigo']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE, utf8_decode("Debe ingresar un código de materia"),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (isset($datos['Codigo']) && $datos['Codigo']!="")
        {
            if (strlen($datos['Codigo'])>255)
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Nombre no puede ser mayor a 255 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }
		return true;
	}




}
