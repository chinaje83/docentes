<?php 
include(DIR_CLASES_DB."cCircuitosEstadosPublicos.db.php");

class cCircuitosEstadosPublicos extends cCircuitosEstadosPublicosdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdEstadoPublico'=> 0,
			'IdEstadoPublico'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
            'xEstado'=> 0,
            'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdEstadoPublico DESC"
		);

		if(isset($datos['IdEstadoPublico']) && $datos['IdEstadoPublico']!="")
		{
			$sparam['IdEstadoPublico']= $datos['IdEstadoPublico'];
			$sparam['xIdEstadoPublico']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
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
        $datos['Estado']=ACTIVO;
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['AltaFecha']=date("Y/m/d H:i:s");
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y/m/d H:i:s");
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		/*$oAuditoriasCircuitosEstados = new cAuditoriasCircuitosEstados($this->conexion,$this->formato);
		$datos['IdEstadoPublico'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasCircuitosEstados->InsertarLog($datos,$codigoInsertadolog))
			return false;*/

		if (!$this->PublicarListadoJson())
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		/*$oAuditoriasCircuitosEstados = new cAuditoriasCircuitosEstados($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasCircuitosEstados->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;*/

		if (!$this->PublicarListadoJson())
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		/*$oAuditoriasCircuitosEstados = new cAuditoriasCircuitosEstados($this->conexion,$this->formato);
		$datosLog=$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasCircuitosEstados->InsertarLog($datosLog,$codigoInsertadolog))
			return false;*/

        $datosmodif['IdEstadoPublico'] = $datos['IdEstadoPublico'];
        $datosmodif['Estado'] = ELIMINADO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
		return true;
	}


    public function ModificarEstado($datos)
    {
        if (!parent::ModificarEstado($datos))
            return false;
        if (!$this->PublicarListadoJson($datos))
            return false;

        return true;
    }


	
	public function GuardarDatosJson($nombrearchivo,$carpeta,$array)
	{
		$datosJson = FuncionesPHPLocal::ConvertiraUtf8($array);
		$jsonData = json_encode($datosJson);
		if(!is_dir($carpeta)){
			@mkdir($carpeta);
		}
		if(!FuncionesPHPLocal::GuardarArchivo($carpeta,$jsonData,$nombrearchivo.".json"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_INF,"Error, al generar el archivo json. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


	
	public function PublicarListadoJson()
	{
		$nombrearchivo = "estados_publicos";
		$carpeta = PUBLICA."json/";
		if(!$this->GerenarArrayDatosJsonListado($array))
			return false;

		if(!$this->GuardarDatosJson($nombrearchivo,$carpeta,$array))
			return false;


		return true;
	}



	public function GerenarArrayDatosJsonListado(&$array)
	{
		$array = array();
		$datos['orderby'] = "IdEstadoPublico ASC";
		$oCircuitosEstados = new cCircuitosEstados($this->conexion,$this->formato);
		if(!$oCircuitosEstados->BusquedaAvanzada($datos,$resultados,$numfilas))
			return false;
		if($numfilas>0)
		{
			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultados))
			{
			    if($fila['IdEstadoPublico']!="" && $fila['EstadoPublicado']==ACTIVO)
                {
                   // $array[$fila['IdEstadoPublico']]['NombrePublico'] = $fila['NombrePublico'];
                   // $array[$fila['IdEstadoPublico']]['Estados'][$fila['IdEstado']];
                   // $array[$fila['IdEstadoPublico']]['Estados'][$fila['IdEstado']]['Nombre']=$fila['Nombre'];
                    $array['Estados'][$fila['IdEstado']] = $fila['NombrePublico'];
                    $array['EstadosPublicos'][$fila['IdEstadoPublico']]['NombrePublico'] = $fila['NombrePublico'];
                    $array['EstadosPublicos'][$fila['IdEstadoPublico']]['Estados'][$fila['IdEstado']]['Nombre']=$fila['Nombre'];

                    $array['EstadosSad'][$fila['IdEstado']] = $fila['NombrePublicoSad'];
                    $array['EstadosPublicosSad'][$fila['IdEstadoPublicoSad']]['NombrePublico'] = $fila['NombrePublicoSad'];
                    $array['EstadosPublicosSad'][$fila['IdEstadoPublicoSad']]['Estados'][$fila['IdEstado']]['Nombre']=$fila['Nombre'];

                    $array['EstadosConsejo'][$fila['IdEstado']] = $fila['NombrePublicoConsejo'];
                    $array['EstadosPublicosConsejo'][$fila['IdEstadoPublicoConsejo']]['NombrePublico'] = $fila['NombrePublicoConsejo'];
                    $array['EstadosPublicosConsejo'][$fila['IdEstadoPublicoConsejo']]['Estados'][$fila['IdEstado']]['Nombre']=$fila['Nombre'];
                }

			}
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

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Nombre del Circuito",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>