<?php
include(DIR_CLASES_DB."cDocumentosPuestos.db.php");
class cDocumentosPuestos extends cDocumentosPuestosdb
{
	/**
	 * Constructor de la clase cDocumentosPuestos.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el mÃ©todo getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion,$formato=FMT_TEXTO){
		parent::__construct($conexion,$formato);
	}
	/**
	 * Destructor de la clase cDocumentosPuestos.
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

    public function getCargosxIdDocumento($IdDocumento)
    {
        $arrayDevolver = array();
        $datos['IdDocumento'] = $IdDocumento;
        if(!$this->BuscarxIdDocumento($datos,$resultado,$numfilas))
            return false;
        while($fila =$this->conexion->ObtenerSiguienteRegistro($resultado))
            $arrayDevolver[] = $fila;


        $arrayDevolver = $this->array_orderby($arrayDevolver, 'IdPuesto', SORT_ASC);
        return $arrayDevolver;

    }

	public function BuscarxCodigo($datos, &$resultado,&$numfilas): bool
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
        return true;
	}

    public function BuscarxIdDocumento($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdDocumento($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarDocumentos(&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarDocumentos($resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarDatosJson($datos,&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarDatosJson($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xIdDocumento'=> 0,
			'IdDocumento'=> "",
			'xIdPuesto'=> 0,
			'IdPuesto'=> "",
			'limit'=> '',
			'orderby'=> "IdDocumento DESC"
		);
		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			$sparam['IdDocumento']= $datos['IdDocumento'];
			$sparam['xIdDocumento']= 1;
		}
		if(isset($datos['IdPuesto']) && $datos['IdPuesto']!="")
		{
			$sparam['IdPuesto']= $datos['IdPuesto'];
			$sparam['xIdPuesto']= 1;
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


    public function ActualizarDatos($datos,&$arrayInsertados)
    {

        if(!$this->BuscarxIdDocumento($datos,$resultado,$numfilas))
            return false;

        if ((isset($datos['AltaPlaza']) && $datos['AltaPlaza'] == 1) && (($datos['IdPuesto']=='NULL') || $datos['IdPuesto'] == 0)) {

            if ($numfilas==1){
                $del = [
                    'IdDocumento' => $datos['IdDocumento'],
                    'IdPuesto'    => 0,
                ];
                if (!$this->Eliminar($del))
                    return false;
            }

            $datosInsertar = [];
            $datosInsertar['IdDocumento'] = $datos['IdDocumento'];
            $datosInsertar['IdPuesto'] = 0;       // puesto dummy
            $datosInsertar['IdRevista'] = $datos['IdRevista'];
            $datosInsertar['CodigoLiquidador'] = null;
            $datosInsertar['IdEstado'] = 1;
            $datosInsertar['DatosJson'] = json_encode(FuncionesPHPLocal::ConvertiraUtf8($datos));
            $datosInsertar['IdPofa'] = null;
            $datosInsertar['HashDato'] = '';

            if (!$this->Insertar($datosInsertar))
                return false;

            $arrayInsertados[0] = 0; // guardamos que insertamos el dummy
        }

        $arrayActualizar = array();
        foreach($datos['PuestosSeleccionados'] as $datosInsertados)
            $arrayActualizar[$datosInsertados['IdPuesto']]= $datosInsertados;



        $arrayEliminar = array();
        while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
        {
            if (array_key_exists($fila['IdPuesto'],$arrayActualizar))
                unset($arrayActualizar[$fila['IdPuesto']]);
            elseif (0 != $fila['IdPuesto'])
                $arrayEliminar[$fila['IdPuesto']] = $fila;
        }


        foreach($arrayActualizar as $IdPuesto => $dataActualizar)
        {
                    $datosInsertar['IdDocumento'] = $datos['IdDocumento'];
                    $datosInsertar['IdPuesto'] = $IdPuesto;
                    $datosInsertar['IdRevista'] = $dataActualizar['IdRevista'];

                    // para las altas manuales con liquidacion desde pofa viene el alta con seleccion de revista
                    if (isset($datos["GuardaRevista"]) && $datos["GuardaRevista"] == true) {
                        $datosInsertar["IdRevista"] = $datos["IdRevista"];
                        $dataActualizar["IdRevista"] = $datos["IdRevista"];
                    }

                    // las altas manuales con liquidacion envian tipo de excepcion
                    if (!FuncionesPHPLocal::isEmpty($datos["IdExcepcionTipo"])) {
                        $dataActualizar["IdExcepcionTipo"] = $datos["IdExcepcionTipo"];
                    }

                    $datosInsertar['CodigoLiquidador'] = $dataActualizar['CodigoLiquidador'];
                    $datosInsertar['IdEstado'] = $dataActualizar['IdEstado'];
                    $datosInsertar['DatosJson'] = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataActualizar));
                    $datosInsertar['IdPofa'] = $dataActualizar['IdPofa'];
                    $datosInsertar['HashDato'] = "";

                    if(!$this->Insertar($datosInsertar))
                        return false;

                    $arrayInsertados[$IdPuesto] = $IdPuesto;

        }
        foreach($arrayEliminar as $IdPuesto => $dataEliminar)
        {
            $datosEliminar['IdDocumento'] = $datos['IdDocumento'];
            $datosEliminar['IdPuesto'] = $IdPuesto;
            if(!$this->Eliminar($datosEliminar))
               return false;

        }

        return true;
    }



    public function ActualizarDatosPlazaDestino($datos,&$arrayInsertados)
    {

        $datosBuscar = [
            'IdDocumento' => $datos['IdDocumento'],
            'IdPuesto'    => 0,
        ];
        if(!$this->BuscarxCodigo($datosBuscar,$resultado,$numfilas))
            return false;

        if ($numfilas==1){
            $del = [
                'IdDocumento' => $datos['IdDocumento'],
                'IdPuesto'    => 0,
            ];
            if (!$this->Eliminar($del))
                return false;
        }


        $arrayActualizar = array();
        foreach($datos['PuestosSeleccionados'] as $datosInsertados)
            $arrayActualizar[$datosInsertados['IdPuesto']]= $datosInsertados;



        foreach($arrayActualizar as $IdPuesto => $dataActualizar)
        {
                    $datosInsertar['IdDocumento'] = $datos['IdDocumento'];
                    $datosInsertar['IdPuesto'] = $IdPuesto;
                    $datosInsertar['IdRevista'] = $dataActualizar['IdRevista'];

                    // para las altas manuales con liquidacion desde pofa viene el alta con seleccion de revista
                    if (isset($datos["GuardaRevista"]) && $datos["GuardaRevista"] == true) {
                        $datosInsertar["IdRevista"] = $datos["IdRevista"];
                        $dataActualizar["IdRevista"] = $datos["IdRevista"];
                    }

                    // las altas manuales con liquidacion envian tipo de excepcion
                    if (!FuncionesPHPLocal::isEmpty($datos["IdExcepcionTipo"])) {
                        $dataActualizar["IdExcepcionTipo"] = $datos["IdExcepcionTipo"];
                    }

                    $datosInsertar['CodigoLiquidador'] = $dataActualizar['CodigoLiquidador'];
                    $datosInsertar['IdEstado'] = $dataActualizar['IdEstado'];
                    $datosInsertar['DatosJson'] = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataActualizar));
                    $datosInsertar['IdPofa'] = $dataActualizar['IdPofa'];
                    $datosInsertar['HashDato'] = "";

                    if(!$this->Insertar($datosInsertar))
                        return false;

                    $arrayInsertados[$IdPuesto] = $IdPuesto;

        }


        return true;
    }


    public function BuscarxIdPofa($datos, &$resultado,&$numfilas): bool
    {
        if(FuncionesPHPLocal::isEmpty($datos['IdPofa']))
            return false;

        $sparam=array(
            'IdPofa'=> $datos['IdPofa'],
            'limit'=> '',
            'orderby'=> "IdDocumento DESC"
        );

        if(isset($datos['orderby']) && $datos['orderby']!="")
            $sparam['orderby']= $datos['orderby'];

        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];

        if (!parent::BuscarxIdPofa($sparam,$resultado,$numfilas))
            return false;
        return true;
    }

    public function ActualizarIdPofaxPuesto($datos): bool
    {
        if (!parent::ActualizarIdPofaxPuesto($datos))
            return false;
        return true;
    }


	public function Insertar($datos): bool
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);

        $AltaEscuela =  $datos['IdEscuela']?? 'NULL';;
        $datos['AltaUsuario']= $datos['UltimaModificacionUsuario']= $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] =date("Y-m-d H:i:s");
        $datos['AltaEscuela']= $datos['UltimaModificacionEscuela']= $AltaEscuela;
        $datos['AltaRol'] = $datos['UltimaModificacionRol'] = implode(',', $_SESSION['rolcod']);
		if (!parent::Insertar($datos))
			return false;

        if(!$this->ModificarHashDato($datos))
        {
            return false;
        }

		$oAuditoriasDocumentosPuestos = new cAuditoriasDocumentosPuestos($this->conexion,$this->formato);
		$datos['Accion'] = INSERTAR;
		if(!$oAuditoriasDocumentosPuestos->InsertarLog($datos,$codigoInsertadolog))
			return false;
		return true;
	}



        public function Eliminar($datos): bool
        {
            if (!$this->_ValidarEliminar($datos,$datosRegistro))
                return false;

            $oAuditoriasDocumentosPuestos = new cAuditoriasDocumentosPuestos($this->conexion,$this->formato);
            $datosLog =$datosRegistro;
            $datosLog['Accion'] = ELIMINAR;
            $datosLog['UltimaModificacionUsuario']= $_SESSION['usuariocod'];
            $datosLog['UltimaModificacionFecha'] =date("Y-m-d H:i:s");
            $datosLog['UltimaModificacionEscuela']= $_SESSION['IdEscuela']??'NULL';
            $datosLog['UltimaModificacionRol'] = implode(',', $_SESSION['rolcod']);
            if(!$oAuditoriasDocumentosPuestos->InsertarLog($datosLog,$codigoInsertadolog))
                return false;
            if (!parent::Eliminar($datos))
                return false;
            return true;
        }


    public function ModificarHashDato($datos)
    {
        if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);


        $arrayhash =array(
            "IdDocumento" =>"",
            "IdPuesto" =>""
        );

        $result = array_intersect_key($datos,$arrayhash);

        $datos['HashDato'] = md5 (implode("",$result));
        if (!parent::ModificarHashDato($datos))
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un cÃ³digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código válido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        return true;
	}


	private function _SetearNull(&$datos): void
	{


		if (!isset($datos['IdPuesto']) || $datos['IdPuesto']=="")
			$datos['IdPuesto']="NULL";

        if (!isset($datos['IdRevista']) || $datos['IdRevista']=="")
            $datos['IdRevista']="NULL";

        if (!isset($datos['CodigoLiquidador']) || $datos['CodigoLiquidador']=="")
            $datos['CodigoLiquidador']="NULL";

        if (!isset($datos['IdEstado']) || $datos['IdEstado']=="")
            $datos['IdEstado']="NULL";

        if (!isset($datos['IdPofa']) || $datos['IdPofa']=="")
            $datos['IdPofa']="NULL";

	}


	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdPuesto']) || $datos['IdPuesto']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id puesto 2",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		return true;
	}

    private function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }


}
