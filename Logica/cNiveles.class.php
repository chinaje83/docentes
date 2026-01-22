<?php 
include(DIR_CLASES_DB."cNiveles.db.php");

class cNiveles extends cNivelesdb
{
	/**
	 * Constructor de la clase cNiveles.
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
	 * Destructor de la clase cNiveles.
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


    public function buscarCombo(&$resultado,&$numfilas): bool {

        return parent::buscarCombo($resultado,$numfilas);
    }
	
	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdNivel'=> 0,
			'IdNivel'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xIdNivelExterno'=> 0,
			'IdNivelExterno'=> "",
			'xCodigo'=> 0,
			'Codigo'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdNivel DESC"
		);
		if(isset($datos['IdNivel']) && $datos['IdNivel']!="")
		{
			$sparam['IdNivel']= $datos['IdNivel'];
			$sparam['xIdNivel']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['IdNivelExterno']) && $datos['IdNivelExterno']!="")
		{
			$sparam['IdNivelExterno']= $datos['IdNivelExterno'];
			$sparam['xIdNivelExterno']= 1;
		}
		if(isset($datos['Codigo']) && $datos['Codigo']!="")
		{
			$sparam['Codigo']= $datos['Codigo'];
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


	public function CiclosSPResult(&$resultado,&$numfilas)
    {
        if (!$this->CiclosCamposSP($spnombre,$sparam))
            return false;

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


    public function EstructurasSPResult(&$resultado,&$numfilas)
    {
        if (!$this->EstructurasCamposSP($spnombre,$sparam))
            return false;

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


    public function GradosAniosSPResult(&$resultado,&$numfilas)
    {
        if (!$this->GradosAniosCamposSP($spnombre,$sparam))
            return false;

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		$oAuditoriasNiveles = new cAuditoriasNiveles($this->conexion,$this->formato);
		$datos['IdNivel'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasNiveles->InsertarLog($datos,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;
		$oAuditoriasNiveles = new cAuditoriasNiveles($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasNiveles->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function Eliminar($datos)
	{
	    if (!parent::BuscarEstructurasxIdNivel($datos, $resultado,$numfilas))
	        return false;

        $oNivelesGradosAnios = new cNivelesGradosAnios($this->conexion,$this->formato);
        $oCiclosNivelesAnioGrados = new cCiclosNivelesAnioGrados($this->conexion,$this->formato);
	    if ($numfilas > 0) {

            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

                if(!$oCiclosNivelesAnioGrados->Eliminar($fila))
                    return false;

                if(!$oNivelesGradosAnios->Eliminar($fila))
                    return false;

            }
        }


        if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasNiveles = new cAuditoriasNiveles($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasNiveles->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		$datosmodif['IdNivel'] = $datos['IdNivel'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;

		return true;
	}


	public function ModificarEstado($datos)
	{
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}


	public function Activar($datos)
	{
		$datosmodif['IdNivel'] = $datos['IdNivel'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasNiveles = new cAuditoriasNiveles($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasNiveles->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function DesActivar($datos)
	{
		$datosmodif['IdNivel'] = $datos['IdNivel'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$oAuditoriasNiveles = new cAuditoriasNiveles($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasNiveles->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}


	public function InsertarEstructura($datos)
    {
        $datos['Estado'] = ACTIVO;
        if (!parent::BuscarEstructurasxId($datos,$resultado,$numfilas))
            return false;

        if ($numfilas > 0) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Actualmente ya existe la estructura."),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        $oNivelGrado = new cNivelesGradosAnios($this->conexion, $this->formato);
        if (!$oNivelGrado->Insertar($datos, $codigoinsertado))
            return false;

        $oCiclosNivelesAnioGrados = new cCiclosNivelesAnioGrados($this->conexion, $this->formato);
        $datos['IdNivelGradoAnio'] = $codigoinsertado;
        if (!$oCiclosNivelesAnioGrados->Insertar($datos, $codigoinsertado2))
            return false;

        return true;
    }


    // Elimina datos de las tablas CiclosNivelesAnioGrados y NivelesGradosAnios
    public function EliminarEstructuraxIdNivel($datos)
    {
        if (!parent::EliminarEstructuraxIdNivel($datos))
            return false;

        return true;
    }


    // Cambia estado de los registros de las tablas CiclosNivelesAnioGrados y NivelesGradosAnios
    public function EliminarEstructura($datos)
    {
        if (!$this->_ValidarEliminarCiclosNivelesAnioGrados($datos, $datosRegistro))
            return false;

        if (!$this->_InsertarAuditoriaCiclosNivelesAnioGrados($datosRegistro))
            return false;

        if (!$this->_ValidarEliminarNivelesGradosAnios($datos, $datosRegistro))
            return false;

        $datosRegistro['IdNivelGradoAnio'] = $datos['IdNivelGradoAnio'];
        if (!$this->_InsertarAuditoriaNivelesGradosAnios($datosRegistro))
            return false;

        $datos['Estado'] = ELIMINADO;
        if (!$this->ModificarEstadoEstructura($datos))
            return false;

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


	private function _SetearNull(&$datos)
	{


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['IdNivelExterno']) || $datos['IdNivelExterno']=="")
			$datos['IdNivelExterno']="NULL";

		if (!isset($datos['Codigo']) || $datos['Codigo']=="")
			$datos['Codigo']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}


	private function _ValidarDatosVacios($datos)
	{

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdNivelExterno']) && $datos['IdNivelExterno']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdNivelExterno'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Error debe ingresar un campo numérico."),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

        return true;
	}


    private function _ValidarEliminarCiclosNivelesAnioGrados ($datos, &$datosRegistro)
    {
        $oCiclosNivelesAnioGrados = new cCiclosNivelesAnioGrados($this->conexion, $this->formato);

        if (!$oCiclosNivelesAnioGrados->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Error debe ingresar un código valido."),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }


    private function _InsertarAuditoriaCiclosNivelesAnioGrados($datosRegistro) {

        $oAuditoriasCiclosNivelesAnioGrados = new cAuditoriasCiclosNivelesAnioGrados($this->conexion,$this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if(!$oAuditoriasCiclosNivelesAnioGrados->InsertarLog($datosLog,$codigoInsertadolog))
            return false;
        return true;
    }


    private function _ValidarEliminarNivelesGradosAnios($datos, &$datosRegistro) {

        if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,utf8_decode("Error debe ingresar un código válido."),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }


    private function _InsertarAuditoriaNivelesGradosAnios($datosRegistro) {

        $oAuditoriasNivelesGradosAnios = new cAuditoriasNivelesGradosAnios($this->conexion,$this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;

        if(!$oAuditoriasNivelesGradosAnios->InsertarLog($datosLog,$codigoInsertadolog))
            return false;
        return true;
    }

}