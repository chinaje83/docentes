<?php 

/**
 * Class de Importaciones SUNABOT
 * Instancia de importaciones.
 *
 * El constructor la clase de importaciones maneja la persistencia a la base de datos
 *
 * @category  MyLibrary
 * @example   importaciones.php
 * @example <br />
 * 	$oObjeto = new cImportacionesSunaBot($conexion);<br />
 *  $oObjeto->BuscarxCodigo($datos,$resultado,$numfilas);<br />
 *  $data = $conexion->ObtenerSiguienteRegistro($resultado);<br />
 *  print_r($data);<br />
 * @version   0.01
 * @since     2017-08-02
 * @author    Alejandro Precioso <aprecioso@gmail.com>
 */
 
include(DIR_CLASES_DB."cImportacionesSunaBot.db.php");

class cImportacionesSunaBot extends cImportacionesSunaBotdb
{
	/**
	 * Conexion a la base de datos.
	 * @var objeto conexion
	 */
	protected $conexion;
	/**
	 * Formato de errores. Formato en que se muestran los errores.
	 * @var string
	 */
    protected $formato;
    /**
     * Conexion al elastic.
     * @var objeto curl
     */
    protected $ch;

    /**
     * constante USUARIO BOT
     * @var CUIL BOT
     */
    const USUARIO = '31456';
    const CUIL = 'SUNABOT';

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	function BuscarTiposDocumento($datos,&$resultado,&$numfilas)
	{
		if(!isset($datos['Estado']) || $datos['Estado']=="")
			$datos['Estado'] = "10";
		if (!parent::BuscarTiposDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}
/**
 * Retorna datos de usuario por codigo.
 *
 * @param array $datos['IdUsuario'], array con clave de codigo de usuario
 *
 * @return  Query con los datos del usuario
 * @todo    Retorna falso en caso de que exista un problema con el store procedure.
 *
 * @since   2017-08-02
 * @author  Alejandro Precioso <aprecioso@gmail.com>
 *
 */

	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if(!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
			$datos['IdUsuario'] = $datos['usuariocod'];
			
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

/**
 * Retorna datos de usuario por codigo.
 *
 * @param array $datos['IdUsuario'], array con clave de codigo de usuario
 *
 * @return  Query con los datos del usuario
 * @todo    Retorna falso en caso de que exista un problema con el store procedure.
 *
 * @since   2017-08-02
 * @author  Alejandro Precioso <aprecioso@gmail.com>
 *
 */

	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
			$sparam=array(
			'xIdRegistro'=> 0,
			'IdRegistro'=> "",
			'xIdDocumento'=> 0,
			'IdDocumento'=> "",
			'xSecuencia'=> 0,
			'Secuencia'=> "",
			'xIdArea'=> 0,
			'IdArea'=> "",
            'xObservacionesHOST'=> 0,
            'ObservacionesHOST'=> "",
            'xIdEstado'=> 0,
            'IdEstado'=> "",
            'xIdEstadoImportacion'=> 0,
            'IdEstadoImportacion'=> "",
			'limit'=> '',
			'orderby'=> "IdRegistro DESC"
		);

		if(isset($datos['IdRegistro']) && $datos['IdRegistro']!="")
		{
			$sparam['IdRegistro']= $datos['IdRegistro'];
			$sparam['xIdRegistro']= 1;
		}
		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			$sparam['IdDocumento']= $datos['IdDocumento'];
			$sparam['xIdDocumento']= 1;
		}
		if(isset($datos['Secuencia']) && $datos['Secuencia']!="")
		{
			$sparam['Secuencia']= $datos['Secuencia'];
			$sparam['xSecuencia']= 1;
		}
        if(isset($datos['IdArea']) && $datos['IdArea']!="")
        {
            $sparam['IdArea']= $datos['IdArea'];
            $sparam['xIdArea']= 1;
        }
        if(isset($datos['ObservacionesHOST']) && $datos['ObservacionesHOST']!="")
        {
            $sparam['ObservacionesHOST']= $datos['ObservacionesHOST'];
            $sparam['xObservacionesHOST']= 1;
        }
        if(isset($datos['IdEstado']) && $datos['IdEstado']!="")
        {
            $sparam['IdEstado']= $datos['IdEstado'];
            $sparam['xIdEstado']= 1;
        }
        if(isset($datos['IdEstadoImportacion']) && $datos['IdEstadoImportacion']!="")
        {
            $sparam['IdEstadoImportacion']= $datos['IdEstadoImportacion'];
            $sparam['xIdEstadoImportacion']= 1;
        }


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}


	

	public function Insertar($datos,&$codigoinsertado)
	{
		if(!isset($datos['IdUsuario']) && isset($datos['usuariocod']))
			$datos['IdUsuario'] = $datos['usuariocod'];
		$oUsuarios_Roles = new cUsuariosRoles($this->conexion);
		
		if (!$this->_ValidarInsertar($datos))
			return false;
		
		/*	
		if (!$this->_ValidarRoles($datos))
			return false;	
		*/
		$datos['Password'] = $datos['DNI'];
		$datos['IdEstado'] = USUARIONUEVO;
		$this->_SetearNull($datos);
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		/*
		$datos["IdUsuario"]=$codigoinsertado;
		if (!$oUsuarios_Roles->ActualizarRolesUsuario($datos))
			return false;
		*/
		return true;
	}


	public function Eliminar($datos)
	{
		

		if (!$this->_ValidarEliminar($datos))
			return false;
	
		if (!parent::Eliminar($datos))
			return false;
		return true;
	}



	public function ModificarEstado($datos)
	{
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}

	public function ImportarCsv($datos)
    {


        $file = CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$datos['nombrearchivotmp'];

        if(!file_exists($file))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no existe el archivo csv.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $file_handle = fopen($file, 'r');
        while (!feof($file_handle) ) {
            $line_of_text[] =fgetcsv($file_handle, 2048,",");
        }
        fclose($file_handle);
        //var_dump($line_of_text);die;


        $ArchivoAreas = file_get_contents(PUBLICA."json/areas.json");
        $arrayAreas = json_decode($ArchivoAreas,1);

        $ArchivoEstados = file_get_contents(PUBLICA."json/estados.json");
        $arrayEstados = json_decode($ArchivoEstados,1);

        //$date = date("H:i:s");
        $date = "00:00:00";

        $datosIns=array();
        if(count($line_of_text)>0)
        {
            $i=0;
            for ($row = 1; $row <= count($line_of_text)-2; $row++)
            {
                $arraytmp = array();
                $arraytmp = $line_of_text[$row];

                if(!$this->_ValidarColumna($arraytmp))
                    return false;

                 $datosIns[$i]['IdDocumento']=$arraytmp[0];
                 $datosIns[$i]['IdArea']=$arraytmp[4];
                 $datosIns[$i]['IdEstado']=$arraytmp[3];
                 $datosIns[$i]['ObservacionesHOST']=$arraytmp[1];
                 $datosIns[$i]['AltaFecha']= date("Y-m-d H:i:s");
                 $datosIns[$i]['Secuencia']=$arraytmp[2];
                 $datosIns[$i]['IdEstadoImportacion']="1";//nuevo

                 $this->_ValidarCampoExistente($datosIns[$i],$arrayAreas,$arrayEstados);

                 $i++;
            }

            if(!$this->InsertBulk($datosIns))
                return false;
            $datosEstado['IdEstadoImportacion'] = 3;//Reg Invalido
            if(!$this->ActualizarEstadoNotExistDocumento($datosEstado))
                return false;


        }

        if(file_exists($file))
         unlink($file);
        return true;
    }


    public function InsertBulk($datos)
    {
        if(isset($datos) && count($datos)>0)
        {


            $ArrayEstablecimientos = array();
            $queryLicencias = "INSERT INTO ".BASEDATOS.".`ImportacionesSUNABOT` (
				  `IdDocumento`,
                  `IdArea`,
                  `IdEstado`,
                  `ObservacionesHOST`,
                  `AltaFecha`,
                  `Secuencia`,
				  `IdEstadoImportacion` 
				  
				)  VALUES ";

            $valuesImportacion="";

            foreach($datos as $key=>$datosInsertar)
            {

                    $this->_SetearNull($datosInsertar);

                    $valuesImportacion.='(
							"'.$datosInsertar['IdDocumento'].'",
							"'.$datosInsertar['IdArea'].'",
							"'.$datosInsertar['IdEstado'].'",
							"'.$datosInsertar['ObservacionesHOST'].'",
							"'.$datosInsertar['AltaFecha'].'",
							"'.$datosInsertar['Secuencia'].'",
							"'.$datosInsertar['IdEstadoImportacion'].'"
						),';


            }

            if($valuesImportacion!="")
            {

                $valuesImportacion = substr($valuesImportacion, 0, strlen($valuesImportacion) - 1);
                $queryImportacionCompleto = $queryLicencias.$valuesImportacion.";";

                $queryImportacionCompleto = str_replace('"NULL"',"NULL",$queryImportacionCompleto);

               // $queryLicenciasCompleto = str_replace('"[','\'[',$queryLicenciasCompleto);
              //  $queryLicenciasCompleto = str_replace(']"',']\'',$queryLicenciasCompleto);
               // $queryLicenciasCompleto = str_replace('true','1',$queryLicenciasCompleto);
               // $queryLicenciasCompleto = str_replace('false','0',$queryLicenciasCompleto);

                $sql = "DELETE FROM ".BASEDATOS.".ImportacionesSUNABOT";
                $this->conexion->ejecutarSQL($sql, "del", $resultadosalida, $numfilassalida, $errnosalida);

                $erroren="";

                if(!$this->conexion->_EjecutarQuery($queryImportacionCompleto,$erroren,$resultado,$errno))
                {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el bulk de licencias.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;
                }
            }

        }

        return true;
    }

    public function ActualizarEstadoNotExistDocumento($datos)
    {
        $query = "UPDATE `ImportacionesSUNABOT` a
                            LEFT JOIN `Documentos` b ON b.IdDocumento = a.`IdDocumento`
                            SET a.`IdEstadoImportacion` = ".$datos['IdEstadoImportacion']."
                            WHERE b.IdDocumento IS NULL";

        $erroren="";

        if(!$this->conexion->_EjecutarQuery($query,$erroren,$resultado,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar el bulk de licencias.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        
        
        return true;
    }



    public function ProcesarNuevos($datos,&$arrayCodigosDevolver)
    {
        $arrayCodigosDevolver=array();
        $this->ch = curl_init();
        $arrayAreas = $arrayEstados = array();
        $sql = "SELECT * FROM Areas WHERE IdArea";
        $this->conexion->ejecutarSQL($sql, "SEL", $resultadosalida, $numfilassalida, $errnosalida);
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadosalida)) {
            $arrayAreas[$fila['IdArea']] = $fila['Nombre'];
        }

        $sql = "SELECT * FROM CircuitosEstados WHERE IdEstado";
        $this->conexion->ejecutarSQL($sql, "SEL", $resultadosalida, $numfilassalida, $errnosalida);
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadosalida)) {
            $arrayEstados[$fila['IdEstado']] = $fila['Nombre'];
        }

        $sql = 'SELECT a.*, b.IdEstado AS EstadoDocumento, b.IdArea AS AreaDocumento 
                FROM ImportacionesSUNABOT AS a INNER JOIN Documentos AS b ON a.IdDocumento=b.IdDocumento 
                WHERE IdEstadoImportacion=1 limit 0,50';
        $this->conexion->ejecutarSQL($sql, "SEL", $resultadosalida, $numfilassalida, $errnosalida);
        $oAuditoriasDocumentos = new cAuditoriasDocumentos($this->conexion, "");
        $oAuditoriasElastic = new cModifElastic(INDICEAUDITORIA);
        $log = "";
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultadosalida)) {
            $error = false;
            if ($fila['IdArea']=="")
                $fila['IdArea'] = $fila['AreaDocumento'];
            if ($fila['IdEstado']=="")
                $fila['IdEstado'] = $fila['EstadoDocumento'];

            $this->conexion->ManejoTransacciones("B");
            $datosModif['Area']['Nombre'] = utf8_encode($arrayAreas[$fila["IdArea"]]);
            $datosModif['Area']['Id'] = $fila["IdArea"];
            $datosModif['UltimaModificacion']['Fecha'] = date("Y-m-d H:i:s");
            $datosModif['IdDocumento'] = $fila['IdDocumento'];
            $datosModif['Estado']['Nombre'] = utf8_encode($arrayEstados[$fila['IdEstado']]);
            $datosModif['Estado']['Id'] = $fila['IdEstado'];
            $datosModif['UltimaModificacion']['Fecha'] = date("Y-m-d H:i:s");
            $fila["ObservacionesHOST"] = substr($fila["ObservacionesHOST"], 0, 200);
            $datosModif['ObservacionesHOST'] = addslashes($fila["ObservacionesHOST"]);

			$datosModif['CargosReemplazo']['Secuencia']="";
            if (isset($fila['Secuencia']) && is_numeric($fila['Secuencia']))
                $datosModif['CargosReemplazo']['Secuencia'] = $fila['Secuencia'];


            if (!$this->ModificarNovedad($oAuditoriasDocumentos, $datosModif, $codigoInsertadolog, $log)) {
                $error = true;
            }

            /*ANULO NOVEDAD EN ELASTICSEARCH E INSERTO EL LOG EN ELASTIC*/
            if (!$error) {
                if (!$this->ModificarNovedadElastic($oAuditoriasElastic, $datosModif, $fila, $codigoInsertadolog, $log)) {
                    $error = true;
                }
                if (!$error) {
                    $sqldel = 'DELETE FROM ImportacionesSUNABOT where IdRegistro=' . $fila["IdRegistro"];
                    $this->conexion->ejecutarSQL($sqldel, "DEL", $resultadodel, $numfilasdel, $errnosalida);
                }
            }
            if(!$error) {
                $this->conexion->ManejoTransacciones("C");
                $arrayCodigosDevolver[$fila["IdDocumento"]] = $fila["IdDocumento"];
            }else
                $this->conexion->ManejoTransacciones("R");

            //sleep(1);
        }
        file_put_contents(DIR_ROOT.'error_logs/mover_documentos_'.date("Ymd_His").'.txt', $log, FILE_APPEND);
        curl_close($this->ch);
        return true;
    }



    private function ModificarNovedad(&$oAuditoriasDocumentos,$datosModif,&$codigoInsertadolog,&$log)
    {


        $sql = "UPDATE Documentos SET IdArea=".$datosModif['Area']['Id'].",
			IdEstado=".$datosModif['Estado']['Id'].",
			ObservacionesHOST='".$datosModif['ObservacionesHOST']."',
			UltimaModificacionFecha='".$datosModif['UltimaModificacion']['Fecha']."'
			WHERE IdDocumento=".$datosModif['IdDocumento'];
        if(!$this->conexion->ejecutarSQL($sql,"UPD",$resultadoUpdate,$numfilassalida,$errnosalida))
        {
            $log  .= PHP_EOL."
					Date: ".date("d/m/Y H:i:s").PHP_EOL.
                "IdDocumento: ".$datosModif['IdDocumento'].PHP_EOL.
                "SQL = ".$sql.PHP_EOL.
                "Texto: Error al modificar el documento (proceso de aprobacion).".PHP_EOL.
                "-------------------------".PHP_EOL;
            $error = 1;
            return false;
        }
        if (isset($datosModif['CargosReemplazo']['Secuencia']) && is_numeric($datosModif['CargosReemplazo']['Secuencia']))
        {
            //actualizo cargos reemplazo
            $sqlupd="UPDATE DocumentosCargosReemplazo SET Secuencia='".$datosModif['CargosReemplazo']['Secuencia']."' WHERE IdDocumento='".$datosModif['IdDocumento']."'";
            //echo $sqlupd;
            if(!$this->conexion->ejecutarSQL($sqlupd,"UPD",$resultadoUpdate,$numfilassalida,$errnosalida))
            {
                $log  .= PHP_EOL."
						Date: ".date("d/m/Y H:i:s").PHP_EOL.
                    "IdDocumento: ".$datosModif['IdDocumento'].PHP_EOL.
                    "SQL = ".$sql.PHP_EOL.
                    "Texto: Error al modificar secuencia de cargos reemplazo del documento (proceso de aprobacion).".PHP_EOL.
                    "-------------------------".PHP_EOL;
                $error = 1;
                return false;
            }
        }

        $dataNovedadModif['IdDocumento'] = $datosModif['IdDocumento'];
        $dataNovedadModif['Accion'] = MODIFICACIONAREA;
        $dataNovedadModif['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        if(!$oAuditoriasDocumentos->InsertarLog($dataNovedadModif,$codigoInsertadolog))
        {
            $log  .= PHP_EOL."
					Date: ".date("d/m/Y H:i:s").PHP_EOL.
                "IdDocumento: ".$datosModif['IdDocumento'].PHP_EOL.
                "SQL = ".$sql.PHP_EOL.
                "Texto: Error al insertar auditoria (DB) del documento (proceso envio de pendientes).".PHP_EOL.
                "-------------------------".PHP_EOL;
            return false;
        }

        return true;
    }



    private function ModificarNovedadElastic(&$oAuditoriasElastic,$datosModif,$novedad,$codigoInsertadolog,&$log)
    {

        if (!$this->ActualizarElastic( $datosModif))
        {
            $log  .= PHP_EOL."
							Date: ".date("d/m/Y H:i:s").PHP_EOL.
                "IdDocumento: ".$novedad['IdDocumento'].PHP_EOL.
                "Texto: Error al actualizar base de datos documental (proceso envio de aprobadas).".PHP_EOL.
                "-------------------------".PHP_EOL;
            return false;
        }else
        {


            $datosEnviarLog = array();
            $datosEnviarLog['AccionCambio'] = "Movimiento";
            $datosEnviarLog['Tipo'] = TIPODOC;
            $datosEnviarLog['IdFilaLog']=$codigoInsertadolog;
            $datosEnviarLog['IdDocumento']=$datosModif['IdDocumento'];
            $datosEnviarLog['Estado']['Inicial']['Id'] = $datosModif['Estado']['Id'];
            $datosEnviarLog['Estado']['Inicial']['Nombre'] = $datosModif['Estado']['Nombre'];
            $datosEnviarLog['Estado']['Final']['Id'] = $datosModif['Estado']['Id'];
            $datosEnviarLog['Estado']['Final']['Nombre'] = $datosModif['Estado']['Nombre'];
            $datosEnviarLog['Area']['Inicial']['Id'] = $datosModif['Area']['Id'];
            $datosEnviarLog['Area']['Inicial']['Nombre'] = $datosModif['Area']['Nombre'];
            $datosEnviarLog['Area']['Final']['Id'] = $datosModif['Area']['Id'];
            $datosEnviarLog['Area']['Final']['Nombre'] = $datosModif['Area']['Nombre'];

            $datosEnviarLog['UltimaModificacion']['APP'] = APP;
            $datosEnviarLog['UltimaModificacion']['ClaveEscuela'] = "";
            $datosEnviarLog['UltimaModificacion']['Escalafon'] = "";
            $datosEnviarLog['UltimaModificacion']['Cuil'] = self::CUIL;
            $datosEnviarLog['UltimaModificacion']['Fecha'] =  $datosModif['UltimaModificacion']['Fecha'];
            if(!$oAuditoriasElastic->Insertar($datosEnviarLog))
            {
                $log  .= PHP_EOL."
							Date: ".date("d/m/Y H:i:s").PHP_EOL.
                    "IdDocumento: ".$novedad['IdDocumento'].PHP_EOL.
                    "Texto: Error al insertar el log de base de datos documental (proceso envio de pendientes).".PHP_EOL.
                    "-------------------------".PHP_EOL;
                return false;

            }
        }

        return true;
    }




    private function ActualizarElastic($datos)
    {
        $datosModif['Area']['Nombre'] =$datos['Area']['Nombre'];
        $datosModif['Area']['Id'] = $datos['Area']['Id'];
        $datosModif['Estado']['Nombre'] = $datos['Estado']['Nombre'];
        $datosModif['Estado']['Id'] = $datos['Estado']['Id'];
        $datosModif['UltimaModificacion']['Fecha'] = $datos['UltimaModificacion']['Fecha'];
        $datosModif['Estado']['Id'] = $datos['Estado']['Id'];
        $datosModif['ObservacionesHOST'] = utf8_encode($datos['ObservacionesHOST']);

        if (isset($datos['CargosReemplazo']['Secuencia']) && is_numeric($datos['CargosReemplazo']['Secuencia']))
        {
            if($this->ObtenerCargosElastic($datos,$CargosElastic))
                $datosModif['CargosReemplazo'] =$CargosElastic;
        }
        $datosEnvio['doc'] = $datosModif;
        $datosEnviar = json_encode($datosEnvio);
        $urlBase = ELASTICSERVER."/".INDICE.INDICESUNA."/".TYPE."/".PREFIJODOC.$datos['IdDocumento']."/_update";
        $header = array("Content-Type: application/json");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_URL, $urlBase);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        if (!UTILIZAPROXY)
        {
            curl_setopt($this->ch, CURLOPT_PROXY, "");
            curl_setopt($this->ch, CURLOPT_PROXYPORT, "");
        }
        $result = curl_exec($this->ch);
        $data = json_decode($result,true);
        if(!isset($data['result']) || ($data['result'] != "updated" && $data['result'] != "noop") )
        {
            $log  .= PHP_EOL."
							Date: ".date("d/m/Y H:i:s").PHP_EOL.
                "IdDocumento: ".$novedad['IdDocumento'].PHP_EOL.
                $data['error'].
                "Texto: Error al actualizar los datos en ELASTIC.".PHP_EOL.
                "-------------------------".PHP_EOL;
            return false;
        }
        return true;
    }


    private function ObtenerCargosElastic($datos,&$datosCargosDevolver)
    {
        $datosCargosDevolver = array();
        $oDocumentosCargosReemplazo = new cDocumentosCargosReemplazo($this->conexion);
        if(!$oDocumentosCargosReemplazo->BuscarxIdDocumento($datos,$resultado,$numfilas))
            return false;
        $arrayReemplazo=array();
        while($DataCargo = $this->conexion->ObtenerSiguienteRegistro($resultado))
        {
            $TieneCargoReemplazo = "SI";

            $datosCargo=array();
            $datosCargo['Secuencia'] = $DataCargo['Secuencia'];
            $datosCargo['SecuenciaReemplazo'] = $DataCargo['SecuenciaReemplazo'];
            $datosCargo['SubSecuenciaReemplazo'] = $DataCargo['SubSecuenciaReemplazo'];
            $datosCargo['Revista'] = $DataCargo['Revista'];
            $datosCargo['RealIntOut'] = $DataCargo['RealIntOut'];
            $datosCargo['ModalidadCarrera'] =  $DataCargo['ModalidadCarrera'];;
            $datosCargo['Asignatura'] = $DataCargo['Asignatura'];
            $datosCargo['Area'] = $DataCargo['Area'];
            $datosCargo['CargoCodigo'] = $DataCargo['CargoCodigo'];
            $datosCargo['CargoDescripcion'] = utf8_encode(trim($DataCargo['CargoDescripcion']));
            $datosCargo['CargoHsMod'] = $DataCargo['CargoHsMod'];
            $datosCargo['CargoEnsenanza'] = $DataCargo['CargoEnsenanza'];
            $datosCargo['Anio'] = $DataCargo['Anio'];
            $datosCargo['Seccion'] = $DataCargo['Seccion'];
            $datosCargo['IdTurno'] = $DataCargo['IdTurno'];
            $datosCargo['Seccion'] = $DataCargo['Seccion'];
            $datosCargo['HsDesignacion'] = $DataCargo['HsDesignacion'];
            $datosCargo['HsDesignacionDescripcion'] = $DataCargo['HsDesignacionDescripcion'];
            $datosCargo['Tipo'] = $DataCargo['Tipo'];
            $datosCargo['CodigoMovimiento'] = $DataCargo['CodigoMovimiento'];
            $arrayReemplazo[] = $datosCargo;

        }
        $datosCargosDevolver = FuncionesPHPLocal::ConvertiraUtf8($arrayReemplazo);

        return true;
    }

//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

    private function _ValidarColumna($arraytmp)
    {
        #Cantidad de columnas del csv 6
        $cantCol =5;

        if(count($arraytmp)!=$cantCol)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error,cantidad de columnas incorrecta csv.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        /* Campo IdSuna*/
        if(!FuncionesPHPLocal::ValidarContenido($this->conexion,$arraytmp[0],"NumericoEntero"))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo IdSuna no es un campo numerico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        /* Campo Secuencia*/
        if(!FuncionesPHPLocal::ValidarContenido($this->conexion,$arraytmp[2],"NumericoEntero"))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Secuencia no es un campo numerico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        /* Campo Area*/
        if(!FuncionesPHPLocal::ValidarContenido($this->conexion,$arraytmp[3],"NumericoEntero"))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Area no es un campo numerico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        /* Campo Estado*/
        if(!FuncionesPHPLocal::ValidarContenido($this->conexion,$arraytmp[4],"NumericoEntero"))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo Estado no es un campo numerico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        /*
        if(!FuncionesPHPLocal::ValidarContenido($this->conexion,$arraytmp[5],"FechaDDMMAAAA"))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el campo FechaModif no tiene el formato fecha dd/mm/aaaa.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

        return true;
    }

    private function _ValidarCampoExistente(&$datos,$arrayAreas,$arrayEstados)
    {
        /* Valido Area exixtente*/
        if(!array_key_exists($datos['IdArea'],$arrayAreas) && $datos['IdArea']!="")
            $datos['IdEstadoImportacion']=3;//Registro Invalidad

        /* Valido Estado exixtente*/
        if(!array_key_exists($datos['IdEstado'],$arrayEstados) && $datos['IdEstado']!="")
            $datos['IdEstadoImportacion']=3;//Registro Invalidad


        return true;
    }


	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		if (!$this->BuscarxActiveDirectory($datos,$resultado,$numfilas))
			return false;

		if ($numfilas==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error ya existe el usuario Active Directory ingresado1.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	private function _ValidarModificar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		

		if (!$this->BuscarxActiveDirectory($datos,$resultado,$numfilas))
			return false;

		if ($numfilas==1)
		{
			$datosUsuarioEncontrado = $this->conexion->ObtenerSiguienteRegistro($resultado);
			if ($datosUsuarioEncontrado['IdUsuario']!=$datos['IdUsuario'])
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error ya existe el usuario Active Directory ingresado2.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}


		return true;
	}



	private function _ValidarEliminar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c&oacute;digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



	private function _SetearNull(&$datos)
	{
		if (!isset($datos['IdDocumento']) || $datos['IdDocumento']=="")
			$datos['IdDocumento']="NULL";
		
		if (!isset($datos['IdArea']) || $datos['IdArea']=="")
			$datos['IdArea']="NULL";

		if (!isset($datos['IdEstado']) || $datos['IdEstado']=="")
			$datos['IdEstado']="NULL";

		if (!isset($datos['ObservacionesHOST']) || $datos['ObservacionesHOST']=="")
			$datos['ObservacionesHOST']="NULL";

		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

		if (!isset($datos['Secuencia']) || $datos['Secuencia']=="")
			$datos['Secuencia']="NULL";

		if (!isset($datos['IdEstadoImportacion']) || $datos['IdEstadoImportacion']=="")
			$datos['IdEstadoImportacion']="NULL";
			

		return true;
	}



	private function _ValidarDatosVacios($datos)
	{
		if (!isset($datos['Email']) || $datos['Email']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un email",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Apellido']) || $datos['Apellido']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un apellido",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!isset($datos['DNI']) || $datos['DNI']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un dni",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!isset($datos['UsuarioAd']) || $datos['UsuarioAd']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Usuario Active Directory",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		


		return true;
	}
	

}
?>