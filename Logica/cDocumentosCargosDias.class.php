<?php 
include(DIR_CLASES_DB."cDocumentosCargosDias.db.php");

class cDocumentosCargosDias extends cDocumentosCargosDiasdb
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
	
	
	public function BuscarxIdDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function ActualizarDatos($datos)
	{
		if(!$this->EliminarxIdDocumento($datos))
			return false;


        $datosInsertar['IdDocumento']=$datos['IdDocumento'];
        if(isset($datos['DocumentosCargosDias']) && count($datos['DocumentosCargosDias'])>0)
        {
            foreach($datos['DocumentosCargosDias'] as $clave => $valor)
            {

                $datosDias = explode("_",$valor);


                $datosInsertar['Secuencia']=$datosDias[0];
                $datosInsertar['SubSecuencia']=$datosDias[1];
                $datosInsertar['Dia']=$datosDias[2];
                $datosInsertar['Turno']=$datosDias[3];
                $datosInsertar['HoraInicio']=$datosDias[4];
                $datosInsertar['HoraFin']=$datosDias[5];

                $datosInsertar['CuilAlta']=$_SESSION['Cuil'];
                $datosInsertar['EscalafonAlta']=$_SESSION['IdEscalafon'];
                $datosInsertar['ClaveEscuelaAlta']=$_SESSION['ClaveEscuela'];
                $datosInsertar['AltaFecha'] = date("Y-m-d H:i:s");
                $datosInsertar['AltaApp'] = APP;

                if(!$this->Insertar($datosInsertar))
                    return false;



            }


        }

		

		
		return true;
	}
	
	public function Insertar($datos)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);

		if (!parent::Insertar($datos))
			return false;
		
		return true;
	}
	
	
	public function Eliminar($datos)
	{
		if (!parent::Eliminar($datos))
			return false;
		
		return true;
	}

    public function EliminarxIdDocumento($datos)
    {
        if (!parent::EliminarxIdDocumento($datos))
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
	
	private function _ValidarDatosVacios($datos)
	{

        if(!isset($datos['Secuencia']) || $datos['Secuencia']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una secuencia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Secuencia'],"NumericoEntero"))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para la secuencia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if(!isset($datos['SubSecuencia']) || $datos['SubSecuencia']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una secuencia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['SubSecuencia'],"NumericoEntero"))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para la subsecuencia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if(!isset($datos['Dia']) || $datos['Dia']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un dia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Dia'],"NumericoEntero"))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico para el dia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        if($datos['Dia']<1 || $datos['Dia']>6)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un dia valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if(!isset($datos['Turno']) || $datos['Turno']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un turno.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        /*if($datos['Turno']!="M" && $datos['Turno']!="T")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un turno valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

        $archivoTurnos = file_get_contents(PUBLICA."json/tur_turnos.json");
        $arrayTurnos = json_decode($archivoTurnos,1);
        $arryaTurnosValidar = array();
        foreach($arrayTurnos as $key=>$DatosTurno)
            if($DatosTurno['Turno']!="")
                $arryaTurnosValidar[$DatosTurno['Turno']] = $DatosTurno['Turno'];

        //print_r($arrayTurnos);
        //print_r($datos);die;


        if(!array_key_exists($datos['Turno'],$arryaTurnosValidar))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un turno valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }




        if(!isset($datos['HoraInicio']) || $datos['HoraInicio']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una hora de inicio.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $HoraInicio = (int)$datos['HoraInicio'];
        if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$HoraInicio,"NumericoEntero"))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una hora de inicio valida.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if(!isset($datos['HoraFin']) || $datos['HoraFin']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una hora de fin.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $HoraFin = (int)$datos['HoraFin'];
        if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$HoraFin,"NumericoEntero"))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una hora de fin valida.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        $FechaHoraInicio = date("Y-m-d")." ".substr_replace( $datos['HoraInicio'], ":", 2, 0 ).":00";
        $FechaHoraFin =  date("Y-m-d")." ".substr_replace( $datos['HoraFin'], ":", 2, 0 ).":00";


        if(strtotime($FechaHoraInicio)>strtotime($FechaHoraFin))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, la hora de inicio debe ser menor o igual a la hora de fin",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;

        }


        $oDocumentosCargos = new cDocumentosCargos($this->conexion,$this->formato);

        if(!$oDocumentosCargos->BuscarxIdDocumento($datos,$resultado,$numfilas))
            return false;

        if($numfilas==0)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no hay cargos seleccionados.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;

        }
        $arrayCargos = array();
        while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            $arrayCargos[$fila['Secuencia']][$fila['SubSecuencia']]= $fila['Secuencia']."_".$fila['SubSecuencia'];



        if(!isset($arrayCargos[$datos['Secuencia']][$datos['SubSecuencia']]))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,	"Error. La secuencia ".$datos['Secuencia']." - subsecuencia ".$datos['SubSecuencia']." tiene días y horarios asociados pero no se encuentra seleccionada. Por favor elimine los horarios de las subsecuencias que no van a ser solicitadas para su cobertura",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;

        }

       // var_dump($datos);die;

        return true;
	}
	
	private function _SetearNull(&$datos)
	{
		if (!isset($datos['Secuencia']) || $datos['Secuencia']=="")
			$datos['Secuencia']="NULL";

        if (!isset($datos['SubSecuencia']) || $datos['SubSecuencia']=="")
            $datos['SubSecuencia']="NULL";

        if (!isset($datos['Dia']) || $datos['Dia']=="")
            $datos['Dia']="NULL";

        if (!isset($datos['Turno']) || $datos['Turno']=="")
            $datos['Turno']="NULL";

        if (!isset($datos['HoraInicio']) || $datos['HoraInicio']=="")
            $datos['HoraInicio']="NULL";
        else
            $datos['HoraInicio'] = date("Y-m-d")." ".substr_replace( $datos['HoraInicio'], ":", 2, 0 ).":00";

        if (!isset($datos['HoraFin']) || $datos['HoraFin']=="")
            $datos['HoraFin']="NULL";
        else
            $datos['HoraFin'] =  date("Y-m-d")." ".substr_replace( $datos['HoraFin'], ":", 2, 0 ).":00";
		return true;
	}


}
?>