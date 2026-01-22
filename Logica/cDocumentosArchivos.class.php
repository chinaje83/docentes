<?php
include(DIR_CLASES_DB."cDocumentosArchivos.db.php");

class cDocumentosArchivos extends cDocumentosArchivosdb
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

	public function BuscarxIdDocumentoxIdDocumentoAdjunto($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdDocumentoxIdDocumentoAdjunto($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarxIdDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarAdjuntoxTipoElastic($datos,&$resultJson)
	{
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["must"] = array();
		$datosEnviar["query"]["bool"]["must"][0]["term"]["Tipo"] = TIPOARCHIVO;
		$datosEnviar["query"]["bool"]["must"][1]["term"]["IdDocumento"] = $datos['IdDocumento'];
		$datosEnviar["query"]["bool"]["must"][2]["term"]["Estado.Id"] = ACTIVO;
		$datosEnviar["query"]["bool"]["must"][3]["match"]["TipoArchivo.Id"] = $datos['IdDocumentoAdjunto'];
		$datosEnviar["from"] = 0;
		$datosEnviar["size"] = 1;

		$oElastic = new cClientesElastic($this->conexion,$this->formato);
		if(!$oElastic->Buscar($datosEnviar,$resultJson))
			return false;

		return true;
	}



	/*public function ActualizarArchivos($datos,$datosRegistro,&$arrayInsertados)
	{

		if (!file_exists(CARPETA_SERVIDOR_TIPOSDOCUMENTOS_FISICA."documento_".$datosRegistro['IdRegistroTipoDocumento'].".json"))
			return false;

		$jsonData = file_get_contents(CARPETA_SERVIDOR_TIPOSDOCUMENTOS_FISICA."documento_".$datosRegistro['IdRegistroTipoDocumento'].".json");

		$dataJsonArray = json_decode($jsonData,1);

		$ArrayTiposDocumentacionAdjunta	=array();
		if(isset($dataJsonArray['Adjuntos']) && count($dataJsonArray['Adjuntos'])>0)
		{
			foreach($dataJsonArray['Adjuntos'] as $fila)
				$ArrayTiposDocumentacionAdjunta[] = $fila;
		}

		$datosArchivo['IdDocumento'] = $datos['IdDocumento'];
		if(!$this->BuscarxIdDocumento($datosArchivo,$resultadoArchivo,$numfilasArchivo))
			return false;
		$arrayEliminar = array();
		while($filaArchivo = $this->conexion->ObtenerSiguienteRegistro($resultadoArchivo))
			$arrayEliminar[$filaArchivo['IdDocumentoArchivo']] = $filaArchivo['IdDocumentoArchivo'];

		$arrayInsertados = array();
		if(count($ArrayTiposDocumentacionAdjunta)>0)
		{

			foreach($ArrayTiposDocumentacionAdjunta as $fila)
			{

				//if(!$this->_ValidarArchivos($datos,$fila))
				//	return false;
				if(isset($datos['nombrearchivotmp_'.$fila['IdDocumentoAdjunto']]) && count($datos['nombrearchivotmp_'.$fila['IdDocumentoAdjunto']])>0)
				{
					$datosDoc['IdDocumento'] = $datos['IdDocumento'];
					$datosDoc['IdRegistroTipoDocumento'] = $datosRegistro['IdRegistroTipoDocumento'];
					$datosDoc['IdDocumentoAdjunto'] = $fila['IdDocumentoAdjunto'];

					foreach($datos['nombrearchivotmp_'.$fila['IdDocumentoAdjunto']] as $k=>$documento)
					{
						$datosDoc['nombrearchivo'] = $datos['nombrearchivo_'.$fila['IdDocumentoAdjunto']][$k];;
						$datosDoc['size'] = $datos['size_'.$fila['IdDocumentoAdjunto']][$k];;
						$datosDoc['nombrearchivotmp'] = $datos['nombrearchivotmp_'.$fila['IdDocumentoAdjunto']][$k];
						if (!$this->InsertarArchivo($datosDoc,$codigoinsertadoDoc,$arrayInsertados))
							return false;

					}
				}
			}

		}

		if(count($ArrayTiposDocumentacionAdjunta)>0)
		{

			foreach($ArrayTiposDocumentacionAdjunta as $fila)
			{
				if(isset($datos['IdDocumentoArchivo_'.$fila['IdDocumentoAdjunto']]))
				{
					foreach($datos['IdDocumentoArchivo_'.$fila['IdDocumentoAdjunto']] as $IdDocumentoArchivo)
					{
						if(array_key_exists($IdDocumentoArchivo,$arrayEliminar))
							unset($arrayEliminar[$IdDocumentoArchivo]);
					}

				}
			}
		}


		if(count($arrayEliminar)>0)
		{
			foreach($arrayEliminar as $IdDocumentoArchivo)
			{
				$datoseliminar['IdDocumentoArchivo'] = $IdDocumentoArchivo;
				if (!$this->EliminarxIdDocumentoArchivo($datoseliminar))
					return false;
			}

		}



		return true;
	}*/



	public function ActualizarArchivos($datos,$datosRegistro,&$arrayInsertados)
	{
		$oDocumentosPermisos = new cDocumentosPermisos($this->conexion,$this->formato);
        $datosBusqueda = [];
		$datosBusqueda['IdArea'] = $_SESSION['IdArea'];
		$datosBusqueda['IdTipoDocumento'] = $datos['IdTipoDocumento'];
		$datosBusqueda['IdDocumento'] = $datos['IdDocumento'];

		if (!$oDocumentosPermisos->PuedeAgregarAdjuntoDocumento($datosBusqueda,$resultado,$numfilas))
			return false;

		$TienePermisosAgregarAdjuntos = false;
		if ($numfilas > 0)
			$TienePermisosAgregarAdjuntos = true;

		$datosArchivo['IdDocumento'] = $datos['IdDocumento'];
		if (!$this->BuscarxIdDocumento($datosArchivo,$resultadoArchivo,$numfilasArchivo))
			return false;

		$arrayEliminar = array();
		while($filaArchivo = $this->conexion->ObtenerSiguienteRegistro($resultadoArchivo)) {
            $arrayEliminar[$filaArchivo['IdDocumentoArchivo']] = $filaArchivo['IdDocumentoArchivo'];
        }

		$arrayInsertados = array();

		if (isset($datos['nombrearchivotmp']) && count($datos['nombrearchivotmp'])>0)
		{

			if ($TienePermisosAgregarAdjuntos===false)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no tiene permisos para agregar documentacion adjunta al documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}

			$datosDoc['IdDocumento'] = $datos['IdDocumento'];
			$datosDoc['IdRegistroTipoDocumento'] = $datosRegistro['IdRegistroTipoDocumento'];
			$datosDoc['IdDocumentoAdjunto'] = 1;

			foreach($datos['nombrearchivotmp'] as $k=>$documento)
			{
				$datosDoc['nombrearchivo'] = $datos['nombrearchivo'][$k];
				$datosDoc['size'] = $datos['size'][$k];;
				$datosDoc['nombrearchivotmp'] = $datos['nombrearchivotmp'][$k];
				if (!$this->InsertarArchivo($datosDoc,$codigoinsertadoDoc,$arrayInsertados))
					return false;
			}
		}

		if(isset($datos['IdDocumentoArchivo']) && count($datos['IdDocumentoArchivo'])>0)
		{
			foreach($datos['IdDocumentoArchivo'] as $IdDocumentoArchivo)
			{
				if(array_key_exists($IdDocumentoArchivo,$arrayEliminar))
					unset($arrayEliminar[$IdDocumentoArchivo]);
			}

		}


		if(count($arrayEliminar)>0)
		{
			if ($TienePermisosAgregarAdjuntos===false)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no tiene permisos para eliminar documentacion adjunta al documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			foreach($arrayEliminar as $IdDocumentoArchivo)
			{
				$datoseliminar['IdDocumentoArchivo'] = $IdDocumentoArchivo;
				if (!$this->EliminarxIdDocumentoArchivo($datoseliminar))
					return false;
			}

		}



		return true;
	}








	public function InsertarArchivo($datos,&$codigoinsertado,&$archivosInsertados)
	{

	    $carpetaFecha = date("Ym")."/";
		$datos["ArchivoNombre"] =$datos["nombrearchivo"];
		$datos["ArchivoSize"] =$datos["size"];
		$datos["ArchivoUbicacion"] =$carpetaFecha.$datos["nombrearchivotmp"];

//		var_dump($datos['nombrearchivotmp']);
		//Subir imagenes reclamos documentacion
		$nombrearchivo = $datos['nombrearchivotmp'];

		if(!is_dir(PATH_STORAGE.CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_ARCHIVOS))
					@mkdir(PATH_STORAGE.CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_ARCHIVOS);

		if(!is_writable(PATH_STORAGE.CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_ARCHIVOS))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no se ha podido subir el archivo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(!is_dir(PATH_STORAGE.CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_ARCHIVOS.$carpetaFecha))
					@mkdir(PATH_STORAGE.CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_ARCHIVOS.$carpetaFecha);

		$bytes = disk_total_space(DOCUMENT_ROOT);
		if($datos["size"] > $bytes)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no se ha podido subir el archivo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;

		}
		if (file_exists(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$nombrearchivo))
		{
				if(!$this->MoverArchivoTemporal($nombrearchivo,PATH_STORAGE.CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_ARCHIVOS.$carpetaFecha))
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al mover el archivo temporal.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
		}

//        var_dump(PATH_STORAGE.CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_ARCHIVOS.$datos["ArchivoUbicacion"]);
		$datos["ArchivoHash"] = hash_file("md5", PATH_STORAGE.CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_ARCHIVOS.$datos["ArchivoUbicacion"]);
//		var_dump($datos['ArchivoHash']);
//		die;

		$datos['AltaUsuario']= $datos['UltimaModificacionUsuario']= $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = $datos['AltaFecha'] =date("Y-m-d H:i:s");


		if(!$this->Insertar($datos,$codigoinsertado))
			return false;




		$datos['IdDocumentoArchivo'] = $codigoinsertado;
		/*$oElastic = new cModifElastic(CLIENTINDEX);
		$datosEnviar = $this->_ArmarArrayAdjuntos($datos);
		if ($datosEnviar===false)
			return false;

		if(!$oElastic->Actualizar($datosEnviar,$datosEnviar))
			return false;

		$datosAuditoria = cAuditoriaClientesElastic::ArmarArrayModificacion($datosEnviar,$this->conexion,$this->formato);
		$oAuditoriasElastic = new cModifElastic(CLIENTAUDIT);
		if(!$oAuditoriasElastic->Insertar($datosAuditoria))
			return false;*/

		$archivosInsertados[] = $datos;
		return true;
	}


	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);

		if (!parent::Insertar($datos,$codigoinsertado))
			return false;


		$oAuditoriasDocumentosArchivos = new cAuditoriasDocumentosArchivos($this->conexion,$this->formato);
		$datos['IdDocumentoArchivo'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasDocumentosArchivos->InsertarLog($datos,$codigoInsertadolog))
			return false;

		return true;
	}

	public function MoverArchivoTemporal($archivo,$carpetadestino)
	{
		if(!is_writable($carpetadestino))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no se a podido subir el archivo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if(!copy(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$archivo,$carpetadestino.$archivo))
			return false;

		if(!unlink(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$archivo))
			return false;

		return true;

	}

	public function EliminarxIdDocumentoArchivo($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;





		$oAuditoriasDocumentosArchivos = new cAuditoriasDocumentosArchivos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosArchivos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		$datosEnviar = $this->_ArmarArrayAdjuntos($datosRegistro);


		if (!parent::EliminarxIdDocumentoArchivo($datos))
			return false;


		/*$oElastic = new cModifElastic(CLIENTINDEX);
		$Inicial = $datosEnviar;
		if ($datosEnviar===false)
			return false;

		$datosEnviar['Estado']['Id'] = ELIMINADO;
		$datosEnviar['Estado']['Nombre'] = "Eliminado";
		$datos['Tipo'] = TIPOARCHIVO;
		if(!$oElastic->Eliminar($datosEnviar))
			return false;

		$datosAuditoria = cAuditoriaClientesElastic::ArmarArrayMovimientos($datosEnviar,"Eliminar",$Inicial);
		$oAuditoriasElastic = new cModifElastic(CLIENTAUDIT);
		if(!$oAuditoriasElastic->Insertar($datosAuditoria))
			return false;*/
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



	private function _ValidarArchivos($datos,$datosArchivo)
	{
		if($datosArchivo['EsObligatorio']=="1")
		{
			if($datosArchivo['Cantidad']<$datos['cant_archivos_'.$datosArchivo['IdDocumentoAdjunto']])
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, la cantidad maxima de archivos a subir es de ".$datosArchivo['Cantidad'].".",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}

			if($datosArchivo['CantidadMaxObligatoria']>$datos['cant_archivos_'.$datosArchivo['IdDocumentoAdjunto']])
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe subir ".$datosArchivo['CantidadMaxObligatoria']." archivos obligatorios.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}


		}
		else
		{
			if($datosArchivo['Cantidad']<$datos['cant_archivos_'.$datosArchivo['IdDocumentoAdjunto']])
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, la cantidad maxima de archivos a subir es de ".$datosArchivo['Cantidad'].".",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}

		}

		return true;
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
		if (!isset($datos['IdDocumento']) || $datos['IdDocumento']=="")
			$datos['IdDocumento']="NULL";

		if (!isset($datos['IdDocumentoAdjunto']) || $datos['IdDocumentoAdjunto']=="")
			$datos['IdDocumentoAdjunto']="NULL";

		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
			$datos['IdRegistroTipoDocumento']="NULL";

		if (!isset($datos['ArchivoUbicacion']) || $datos['ArchivoUbicacion']=="")
			$datos['ArchivoUbicacion']="NULL";

		if (!isset($datos['ArchivoNombre']) || $datos['ArchivoNombre']=="")
			$datos['ArchivoNombre']="NULL";

		if (!isset($datos['ArchivoSize']) || $datos['ArchivoSize']=="")
			$datos['ArchivoSize']="NULL";

		if (!isset($datos['ArchivoHash']) || $datos['ArchivoHash']=="")
			$datos['ArchivoHash']="NULL";


		return true;
	}


}
?>
