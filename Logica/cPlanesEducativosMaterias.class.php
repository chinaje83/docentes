<?php
include(DIR_CLASES_DB."cPlanesEducativosMaterias.db.php");
class cPlanesEducativosMaterias extends cPlanesEducativosMateriasdb
{
	/**
	 * Constructor de la clase cPlanesEducativosMaterias.
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
	 * Destructor de la clase cPlanesEducativosMaterias.
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
			'xIdPlanGradoAnio'=> 0,
			'IdPlanGradoAnio'=> "",
			'xIdCargo'=> 0,
			'IdCargo'=> "",
			'xIdMateria'=> 0,
			'IdMateria'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "Id ASC"
		);
		if(isset($datos['IdPlanGradoAnio']) && $datos['IdPlanGradoAnio']!="")
		{
			$sparam['IdPlanGradoAnio']= $datos['IdPlanGradoAnio'];
			$sparam['xIdPlanGradoAnio']= 1;
		}
		if(isset($datos['IdCargo']) && $datos['IdCargo']!="")
		{
			$sparam['IdCargo']= $datos['IdCargo'];
			$sparam['xIdCargo']= 1;
		}
		if(isset($datos['IdMateria']) && $datos['IdMateria']!="")
		{
			$sparam['IdMateria']= $datos['IdMateria'];
			$sparam['xIdMateria']= 1;
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

	public function BuscarExistente($datos, &$resultado, &$numfilas): bool
    {
        if (!parent::BuscarExistente($datos, $resultado, $numfilas))
            return false;

        return true;
    }

    public function BuscarInsertar($datos, &$resultado, &$numfilas): bool
    {
        if (!parent::BuscarInsertar($datos, $resultado, $numfilas))
            return false;

        return true;
    }

    public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas): bool
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function Insertar($datos,&$codigoInsertado): bool
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

        if (!$this->_ValidarCargo($datos["IdCargo"], $datosCargo))
            return false;

        $datos['IdTipo']="NULL";
        $this->_SetearNull($datos);

		if ($datosCargo['IdTipo'] == 2)
            $datos['Modulos'] = $datos['Cantidad'];
		else
            $datos['Horas'] = $datos['Cantidad'];

		$this->ObtenerProximoOrden($datos,$proxorden);
		$datos['Id'] = $proxorden;
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoInsertado))
			return false;
		$oAuditoriasPlanesEducativosMaterias = new cAuditoriasPlanesEducativosMaterias($this->conexion,$this->formato);
		$datos['Id'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasPlanesEducativosMaterias->InsertarLog($datos,$codigoInsertadolog))
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
		$oAuditoriasPlanesEducativosMaterias = new cAuditoriasPlanesEducativosMaterias($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasPlanesEducativosMaterias->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


    public function EliminarFisico($datos): bool
    {
        if (!$this->_ValidarEliminar($datos,$datosRegistro))
            return false;
        $oAuditoriasPlanesEducativosMaterias = new cAuditoriasPlanesEducativosMaterias($this->conexion,$this->formato);
        $datosLog =$datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if(!$oAuditoriasPlanesEducativosMaterias->InsertarLog($datosLog,$codigoInsertadolog))
            return false;
        $datosmodif['Id'] = $datos['Id'];
        if (!parent::Eliminar($datosmodif))
            return false;
        return true;
    }


    public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasPlanesEducativosMaterias = new cAuditoriasPlanesEducativosMaterias($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasPlanesEducativosMaterias->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['Id'] = $datos['Id'];
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
		$datosmodif['Id'] = $datos['Id'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasPlanesEducativosMaterias = new cAuditoriasPlanesEducativosMaterias($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasPlanesEducativosMaterias->InsertarLog($datosRegistro,$codigoInsertadolog))
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
		$oAuditoriasPlanesEducativosMaterias = new cAuditoriasPlanesEducativosMaterias($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasPlanesEducativosMaterias->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function ModificarOrdenCompleto($datos): bool
	{
		$datosmodif['Id'] = 1;
		$arregloOrden = explode(",",$datos['orden']);
		foreach ($arregloOrden as $Id){
			$datosmodif['Id'] = $Id;
			if (!parent::ModificarOrden($datosmodif))
				return false;
			$datosmodif['Id']++;
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



    private function _ValidarCargo($Id, &$datosregistro): bool {

	    if(FuncionesPHPLocal::isEmpty($Id))
            return false;

        $oObjeto = new cCargos($this->conexion);

        if(!$oObjeto->BuscarxCodigo(['IdCargo' => $Id], $resultado, $numfilas))
            return false;

        if  ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un cargo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        $datosregistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        return true;

    }

	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
        /*
        $datosBuscar['IdPlanGradoAnio'] = $datos['IdPlanGradoAnio'];
        $datosBuscar['CargoMaterias'] = ($datos['IdCargo'] != 0 ? $datos['IdCargo'] : $datos['IdMateria']);
        if (!$this->BuscarInsertar($datosBuscar, $resultado, $numfilas))
            return false;

        if ($numfilas > 0)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Actualmente la Materia/Cargo ya se encuentra asociado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

		return true;
	}


	private function _ValidarModificar($datos,&$datosRegistro)
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
		if (!isset($datos['IdCargo']) || $datos['IdCargo']=="")
			$datos['IdCargo']="NULL";

		if (!isset($datos['IdMateria']) || $datos['IdMateria']=="")
			$datos['IdMateria']="NULL";

		if (!isset($datos['Horas']) || $datos['Horas']=="")
			$datos['Horas']=0;

		if (!isset($datos['Modulos']) || $datos['Modulos']=="")
			$datos['Modulos']=0;

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
	}


	private function _ValidarDatosVacios($datos)
	{
		if (!isset($datos['IdPlanGradoAnio']) || $datos['IdPlanGradoAnio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un IdPlanGradoAnio",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

        if (!isset($datos['IdCargo']) || $datos['IdCargo']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar un cargo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
/*
        if ((isset($datos['IdMateria']) && $datos['IdMateria'] != "" ) && (isset($datos['IdCargo']) && $datos['IdCargo'] != ""))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("No puede seleccionar cargo y materia simultáneamente"),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

		if (isset($datos['IdCargo']) && $datos['IdCargo']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdCargo'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un numero para el campo Cargo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdCargo'])>11)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Cargo no puede ser mayor a 11 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (isset($datos['IdMateria']) && $datos['IdMateria']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdMateria'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el campo Materia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (strlen($datos['IdMateria'])>11)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Materia no puede ser mayor a 11 .",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
/*
        if (!isset($datos['IdTipo']) || $datos['IdTipo']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar un tipo de cargo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

        if (!isset($datos['Cantidad']) || $datos['Cantidad']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Debe ingresar una cantidad de horas"),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }elseif(!is_numeric($datos['Cantidad']) || $datos['Cantidad']<1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Debe ingresar una cantidad de horas mayor a cero"),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

		return true;
	}




}
