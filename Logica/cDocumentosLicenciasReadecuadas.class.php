<?php 
include(DIR_CLASES_DB."cDocumentosLicenciasReadecuadas.db.php");

class cDocumentosLicenciasReadecuadas extends cDocumentosLicenciasReadecuadasdb
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
	
	

	
	public function Insertar($datos)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);


        $datos['AltaApp'] = $datos['UltimaModificacionApp'] = APP;
        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");

		if (!parent::Insertar($datos))
			return false;
		
		
		$oAuditoriasDocumentosLicenciasReadecuadas = new cAuditoriasDocumentosLicenciasReadecuadas($this->conexion,$this->formato);
		$datos['IdDocumento'] = $datos['IdDocumento'];
		$datos['Secuencia'] = $datos['Secuencia'];
		$datos['RealIntOut'] = $datos['RealIntOut'];
		$datos['Accion'] = INSERTAR;
		if(!$oAuditoriasDocumentosLicenciasReadecuadas->InsertarLog($datos,$codigoInsertadolog))
			return false;
		
		return true;
	}

	public function ModificarClaveEscuelaDestinoxIdDocumento($datos)
    {
        if (!$this->_ValidarModificar($datos,$datosRegistro))
            return false;

        $datos['UltimaModificacionApp']= $datosRegistro['UltimaModificacionApp'] =APP;
        $datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $this->_SetearNull($datos);

        if (!parent::ModificarClaveEscuelaDestinoxIdDocumento($datos))
            return false;

        $oAuditoriasDocumentosLicenciasReadecuadas = new cAuditoriasDocumentosLicenciasReadecuadas($this->conexion,$this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if(!$oAuditoriasDocumentosLicenciasReadecuadas->InsertarLog($datosRegistro,$codigoInsertadolog))
            return false;
        return true;
    }
	
	
	public function Eliminar($datos)
	{
		$oAuditoriasDocumentosLicenciasReadecuadas = new cAuditoriasDocumentosLicenciasReadecuadas($this->conexion,$this->formato);
		$datos['IdDocumento'] = $datos['IdDocumento'];
		$datos['Secuencia'] = $datos['Secuencia'];
		$datos['SubSecuencia'] = $datos['SubSecuencia'];
		$datos['RealIntOut'] = $datos['RealIntOut'];
		$datos['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosLicenciasReadecuadas->InsertarLog($datos,$codigoInsertadolog))
			return false;
		
		if (!parent::Eliminar($datos))
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
        if (!$this->BuscarxIdDocumento($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        /*if (!isset($datos['ClaveEscuelaDestino']) || ($datos['ClaveEscuelaDestino']==""))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar una Clave Escuela Destino.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/
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

		return true;
	}
	
	private function _SetearNull(&$datos)
	{

        if (!isset($datos['ClaveEscuela']) || $datos['ClaveEscuela']=="")
            $datos['ClaveEscuela']="NULL";

        if (!isset($datos['ClaveEscuelaDestino']) || $datos['ClaveEscuelaDestino']=="")
            $datos['ClaveEscuelaDestino']="NULL";

        if (!isset($datos['PeriodoFechaDesde']) || $datos['PeriodoFechaDesde']=="")
            $datos['PeriodoFechaDesde']="NULL";

	    if (!isset($datos['PeriodoFechaHasta']) || $datos['PeriodoFechaHasta']=="")
            $datos['PeriodoFechaHasta']="NULL";

		if (!isset($datos['Secuencia']) || $datos['Secuencia']=="")
			$datos['Secuencia']="NULL";
			
		if (!isset($datos['RealIntOut']) || $datos['RealIntOut']=="")
			$datos['RealIntOut']="NULL";

		if (!isset($datos['Cuil']) || $datos['Cuil']=="")
            $datos['Cuil']="NULL";

		if (!isset($datos['DNI']) || $datos['DNI']=="")
            $datos['DNI']="NULL";
			

			
		return true;
	}


}
?>