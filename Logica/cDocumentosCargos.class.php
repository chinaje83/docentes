<?php 
include(DIR_CLASES_DB."cDocumentosCargos.db.php");

class cDocumentosCargos extends cDocumentosCargosdb
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
	
	
	public function ActualizarDatos($datos,&$arrayInsertados)
	{
		if(!$this->BuscarxIdDocumento($datos,$resultado,$numfilas))
			return false;


		$arrayActualizar = array();
		foreach($datos['CargosSeleccionados'] as $datosInsertar)
			$arrayActualizar[$datosInsertar['Secuencia']][$datosInsertar['SubSecuencia']][$datosInsertar['RealIntOut']]= $datosInsertar;



		$arrayEliminar = array();
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			if (array_key_exists($fila['Secuencia'],$arrayActualizar) && array_key_exists($fila['SubSecuencia'],$arrayActualizar[$fila['Secuencia']]) && array_key_exists($fila['RealIntOut'],$arrayActualizar[$fila['Secuencia']][$fila['SubSecuencia']]))
				unset($arrayActualizar[$fila['Secuencia']][$fila['SubSecuencia']][$fila['RealIntOut']]);
			else
				$arrayEliminar[$fila['Secuencia']][$fila['SubSecuencia']][$fila['RealIntOut']] = $fila;
		}

		foreach($arrayActualizar as $Secuencia => $dataActualizar)
		{	
			foreach($dataActualizar as $SubSecuencia => $vecRealintOut)
			{
				foreach($vecRealintOut as $RealIntOut => $datosInsertar)
				{
					$datosInsertar['CuilAlta']=$_SESSION['Cuil'];
					$datosInsertar['EscalafonAlta']=$_SESSION['IdEscalafon'];
					$datosInsertar['ClaveEscuelaAlta']=$_SESSION['ClaveEscuela'];
					$datosInsertar['AltaFecha'] = date("Y-m-d H:i:s");		
					$datosInsertar['AltaApp'] = APP;		
					$datosInsertar['IdDocumento'] = $datos['IdDocumento'];
					$datosInsertar['HashDato'] = "";
					if(!$this->Insertar($datosInsertar,$codigoinsertado))
						return false;
	
					$arrayInsertados[$codigoinsertado] = $codigoinsertado;
				}
			}
		}
		
		foreach($arrayEliminar as $Secuencia => $dataEliminar)
		{	
			foreach($dataEliminar as $SubSecuencia => $vecRealIntOut)
			{
				foreach($vecRealIntOut as $realIntOut => $datosEliminar)
				{
					if(!$this->Eliminar($datosEliminar))
						return false;
				}
			}
		}
		
		return true;
	}
	
	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);



		
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		
		
		$oAuditoriasDocumentosCargos = new cAuditoriasDocumentosCargos($this->conexion,$this->formato);
		$datos['IdDocumento'] = $datos['IdDocumento'];
		$datos['Secuencia'] = $datos['Secuencia'];
		$datos['SubSecuencia'] = $datos['SubSecuencia'];
		$datos['Accion'] = INSERTAR;
		if(!$oAuditoriasDocumentosCargos->InsertarLog($datos,$codigoInsertadolog))
			return false;
		
		return true;
	}
	
	
	public function Eliminar($datos)
	{
		$oAuditoriasDocumentosCargos = new cAuditoriasDocumentosCargos($this->conexion,$this->formato);
		$datos['IdDocumento'] = $datos['IdDocumento'];
		$datos['Secuencia'] = $datos['Secuencia'];
		$datos['SubSecuencia'] = $datos['SubSecuencia'];
		$datos['RealIntOut'] = $datos['RealIntOut'];
		$datos['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosCargos->InsertarLog($datos,$codigoInsertadolog))
			return false;
		
		if (!parent::Eliminar($datos))
			return false;
		
		return true;
	}
//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------


	private function _ArmarArrayAdjuntos($datos)
	{
		
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		
		
		$datosEnviar['IdArchivo'] = $datosRegistro['IdDocumentoArchivo'];
		$datosEnviar['IdDocumento'] = $datosRegistro['IdDocumento'];
		$datosEnviar['TipoDocumento']['Id'] = $datosRegistro['IdTipoDocumento'];
		$datosEnviar['TipoDocumento']['IdRegistro'] = $datosRegistro['IdRegistroTipoDocumento'];
		$datosEnviar['TipoDocumento']['Nombre'] = utf8_encode($datosRegistro['NombreTipoDocumento']);
		$datosEnviar['TipoArchivo']['Id'] = $datosRegistro['IdDocumentoAdjunto'];
		$datosEnviar['TipoArchivo']['Nombre'] = utf8_encode($datosRegistro['NombreDocumentoAdjunto']);
		$datosEnviar['IdDocumento'] = $datosRegistro['IdDocumento'];
		$datosEnviar['NombreArchivo'] = utf8_encode($datosRegistro['ArchivoNombre']);
		$datosEnviar['TamanioArchivo'] = $datosRegistro['ArchivoSize'];
		$datosEnviar['UbicacionArchivo'] =  utf8_encode($datosRegistro['ArchivoUbicacion']);
		$datosEnviar['HashArchivo'] =  utf8_encode($datosRegistro['ArchivoHash']);
		$datosEnviar['Tipo'] = TIPOARCHIVO;
		$datosEnviar['AltaAPP'] = APP;
		$datosEnviar['Estado']['Id'] = ACTIVO;
		$datosEnviar['Estado']['Nombre'] = "Activo";
		$datosEnviar['AltaUsuario'] = $datosRegistro['AltaUsuario'];
		$datosEnviar['AltaFecha'] = $datosRegistro['AltaFecha'];
		$datosEnviar['UltimaModificacionUsuario'] = $datos['UltimaModificacionUsuario'];
		$datosEnviar['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
		$datosEnviar['UltimaModificacionAPP'] = APP;
		return $datosEnviar;
	}
	
	
	
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

		return true;
	}
	
	private function _SetearNull(&$datos)
	{
		if (!isset($datos['RealIntOut']) || $datos['RealIntOut']=="")
			$datos['RealIntOut']="NULL";
			
		if (!isset($datos['RealIntOut']) || $datos['RealIntOut']=="")
			$datos['RealIntOut']="NULL";	
			
		if (!isset($datos['ModalidadCarrera']) || $datos['ModalidadCarrera']=="")
			$datos['ModalidadCarrera']="NULL";	
			
		if (!isset($datos['Asignatura']) || $datos['Asignatura']=="")
			$datos['Asignatura']="NULL";			
			
		if (!isset($datos['Area']) || $datos['Area']=="")
			$datos['Area']="NULL";	
			
		if (!isset($datos['CargoCodigo']) || $datos['CargoCodigo']=="")
			$datos['CargoCodigo']="NULL";
			
		if (!isset($datos['CargoDescripcion']) || $datos['CargoDescripcion']=="")
			$datos['CargoDescripcion']="NULL";
		
		if (!isset($datos['GrupoCodigo']) || $datos['GrupoCodigo']=="")
			$datos['GrupoCodigo']="NULL";
			
		if (!isset($datos['GrupoDescripcion']) || $datos['GrupoDescripcion']=="")
			$datos['GrupoDescripcion']="NULL";
		
		
		if (!isset($datos['SubGrupoCodigo']) || $datos['SubGrupoCodigo']=="")
			$datos['SubGrupoCodigo']="NULL";
			
		if (!isset($datos['SubGrupoDescripcion']) || $datos['SubGrupoDescripcion']=="")
			$datos['SubGrupoDescripcion']="NULL";
		
		
		if (!isset($datos['RegimenEstatutarioCodigo']) || $datos['RegimenEstatutarioCodigo']=="")
			$datos['RegimenEstatutarioCodigo']="NULL";
			
		if (!isset($datos['RegimenEstatutarioDescripcion']) || $datos['RegimenEstatutarioDescripcion']=="")
			$datos['RegimenEstatutarioDescripcion']="NULL";
		
		if (!isset($datos['CargoHsMod']) || $datos['CargoHsMod']=="")
			$datos['CargoHsMod']="NULL";	

		if (!isset($datos['CargoEnsenanza']) || $datos['CargoEnsenanza']=="")
			$datos['CargoEnsenanza']="NULL";	
		
		if (!isset($datos['Anio']) || $datos['Anio']=="")
			$datos['Anio']="NULL";	

		if (!isset($datos['Seccion']) || $datos['Seccion']=="")
			$datos['Seccion']="NULL";	
		
		if (!isset($datos['IdTurno']) || $datos['IdTurno']=="")
			$datos['IdTurno']="NULL";	
		
		if (!isset($datos['HsDesignacion']) || $datos['HsDesignacion']=="")
			$datos['HsDesignacion']="NULL";	
		
		if (!isset($datos['HsDesignacionDescripcion']) || $datos['HsDesignacionDescripcion']=="")
			$datos['HsDesignacionDescripcion']="NULL";

        if (!isset($datos['Tipo']) || $datos['Tipo']=="")
            $datos['Tipo']="NULL";

        if (!isset($datos['CodigoMovimiento']) || $datos['CodigoMovimiento']=="")
            $datos['CodigoMovimiento']="NULL";
			
		return true;
	}


}
?>