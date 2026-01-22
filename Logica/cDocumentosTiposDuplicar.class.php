<?php 

class cDocumentosTiposDuplicar
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
	}

	function __destruct(){}

	public function DuplicarMacros($datos)
	{
		
		$oDocumentosTipos = new cDocumentosTipos($this->conexion,$this->formato);
		$oDocumentosTiposMacros = new cDocumentosTiposMacros($this->conexion,$this->formato);	
		$oDocumentosTiposMacrosZonas = new cDocumentosTiposMacrosZonas($this->conexion,$this->formato);	
		$oDocumentosTiposZonas = new cDocumentosTiposZonas($this->conexion,$this->formato);	
		if(!$oDocumentosTipos->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el tipo por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}	
		$TipoOriginal = $this->conexion->ObtenerSiguienteRegistro($resultado);
		
		$datosBuscar['IdRegistroTipoDocumento'] = $TipoOriginal['IdRegistro'];
		if(!$oDocumentosTiposMacros->BuscarPasosMacros($datosBuscar,$resultadoMacros,$numfilas))
			return false;
		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el tipo de documento tiene columnas asociadas, por favor elimine todas las columnas. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

			
		$datosDuplicar['IdRegistro'] = $datos['IdRegistroDuplicar'];	
		if(!$oDocumentosTipos->BuscarxCodigo($datosDuplicar,$resultado,$numfilas))
			return false;
		
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el tipo a duplicar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}	
		$TipoADuplicar = $this->conexion->ObtenerSiguienteRegistro($resultado);
		
			
		$datosBuscar['IdRegistroTipoDocumento'] = $TipoADuplicar['IdRegistro'];
		if(!$oDocumentosTiposMacros->BuscarPasosMacros($datosBuscar,$resultadoMacros,$numfilas))
			return false;
			
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoMacros))
		{
			$datosInsertar = $fila;
			$datosInsertar['IdRegistroTipoDocumento'] = $TipoOriginal['IdRegistro'];
			if(!$oDocumentosTiposMacros->InsertarMacro($datosInsertar,$IdMacroPosicion))
				return false;
			
			if(!$oDocumentosTiposMacrosZonas->BuscarZonasxIdRegistroTipoDocumento($fila,$resultadoMacrosZonas,$numfilas))
				return false;
			
				
			while($filaMacrosZonas = $this->conexion->ObtenerSiguienteRegistro($resultadoMacrosZonas))
			{
				$datosInsertar = $filaMacrosZonas;
				$datosInsertar['IdMacroPosicion'] = $IdMacroPosicion;
				$datosInsertar['IdRegistroTipoDocumento'] = $TipoOriginal['IdRegistro'];
					
				if(!$oDocumentosTiposMacrosZonas->Insertar($datosInsertar,$IdZona))
					return false;
				
				if(!$oDocumentosTiposZonas->BuscarxZonaTipoDocumento($filaMacrosZonas,$resultadoZonas,$numfilas))
					return false;

				while($filaZona = $this->conexion->ObtenerSiguienteRegistro($resultadoZonas))
				{
					$datosInsertar = $filaZona;
					$datosInsertar['IdZona'] = $IdZona;
					$datosInsertar['IdRegistroTipoDocumento'] = $TipoOriginal['IdRegistro'];
					unset($datosInsertar['IdZonaModulo']);
					if(!$oDocumentosTiposZonas->Insertar($datosInsertar,$IdZonaModulo))
						return false;
					
				}


			}
			
		}				
		return true;
	}


	public function DuplicarCircuito($datos)
	{
		
		$oDocumentosTipos = new cDocumentosTipos($this->conexion,$this->formato);
		$oCircuitosWorkflow = new cCircuitosWorkflow($this->conexion,$this->formato);
		$oCircuitosAreasEstadosAreas = new cCircuitosAreasEstadosAreas($this->conexion,$this->formato);
		$oCircuitosAreasEstadosRoles = new cCircuitosAreasEstadosRoles($this->conexion,$this->formato);
		$oCircuitosAreasEstadosRolesAcciones = new cCircuitosAreasEstadosRolesAcciones($this->conexion,$this->formato);


		$oCircuitosWorkflowRoles = new cCircuitosWorkflowRoles($this->conexion,$this->formato);
		$oCircuitosWorkflowRolesAcciones = new cCircuitosWorkflowRolesAcciones($this->conexion,$this->formato);
		$oCircuitosWorkflowTiposDocumentos = new cCircuitosWorkflowTiposDocumentos($this->conexion,$this->formato);

		
		if(!$oDocumentosTipos->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el tipo a duplicar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}	
		$TipoOriginal = $this->conexion->ObtenerSiguienteRegistro($resultado);

		$oCircuitosAreasEstadosOriginal = new cCircuitosAreasEstados($this->conexion,$TipoOriginal['IdCircuito'],$this->formato);

		if(!$oCircuitosAreasEstadosOriginal->BuscarAreasEstadosxCircuito($resultadoCircuitoAreas,$numfilasCircuitoAreas))
			return false;
		
		if ($numfilasCircuitoAreas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el tipo de documento ya tiene un circuito asignado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}	

		$datosDuplicar['IdRegistro'] = $datos['IdRegistroDuplicar'];	
		if(!$oDocumentosTipos->BuscarxCodigo($datosDuplicar,$resultado,$numfilas))
			return false;
		
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el tipo a duplicar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}	
		$TipoADuplicar = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$oCircuitosAreasEstadosDuplicar = new cCircuitosAreasEstados($this->conexion,$TipoADuplicar['IdCircuito'],$this->formato);
		
		$arrayNodosWorkflow = array();
		if(!$oCircuitosAreasEstadosDuplicar->BuscarAreasEstadosxCircuito($resultEstadosDuplicar,$numfilasCircuito))
			return false;

			
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultEstadosDuplicar))
		{
			$datosInsertar = $fila;
			$datosInsertar['NodoInicialDatos'] = ($fila['NodoInicial']==1)?"true":"false";
			if ($fila['NodoGeneral']==0)
			{
				$datosInsertar['AreasCompletas'] = "false";
				if(!$oCircuitosAreasEstadosAreas->BuscarxCodigoNodo($fila,$resultAreas,$numfilas))
					return false;
				$arrayAreas = array();
				while($filaAreas = $this->conexion->ObtenerSiguienteRegistro($resultAreas))	
					$arrayAreas[]=$filaAreas['IdArea'];
					
				$datosInsertar['IdArea'] = implode(",",$arrayAreas);	
				
			}else
			{
				$datosInsertar['AreasCompletas'] = "true";
				$datosInsertar['IdArea']="";
			}	

			if(!$oCircuitosAreasEstadosOriginal->Insertar($datosInsertar,$datosNodo,$IdNodoWorkflow))
				return false;
				
			$arrayNodosWorkflow[$fila['IdNodoWorkflow']] = $IdNodoWorkflow; 	

			if(!$oCircuitosAreasEstadosRoles->BuscarxIdNodoWorkflow($fila,$resultRoles,$numfilas))
				return false;
			
			
			if(!$oCircuitosAreasEstadosRolesAcciones->BuscarxIdNodoWorkflow($fila,$resultAcciones,$numfilas))
				return false;

			
			while($filaRoles = $this->conexion->ObtenerSiguienteRegistro($resultRoles))
			{
				$datosInsertar = $filaRoles;
				$datosInsertar['IdNodoWorkflow'] = $IdNodoWorkflow;
				if(!$oCircuitosAreasEstadosRoles->Insertar($datosInsertar))
					return false;
				
			}

			while($filaAcciones = $this->conexion->ObtenerSiguienteRegistro($resultAcciones))
			{
				$datosInsertar = $filaAcciones;
				$datosInsertar['IdNodoWorkflow'] = $IdNodoWorkflow;
				if(!$oCircuitosAreasEstadosRolesAcciones->Insertar($datosInsertar))
					return false;
				
			}
			
		}		
		
		if(!$oCircuitosWorkflow->BuscarConexionesxCircuito($TipoADuplicar, $resultadoWorkflow,$numfilas))
			return false;
		
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoWorkflow))
		{
			if (array_key_exists($fila['IdNodoWorkflowActual'],$arrayNodosWorkflow) && array_key_exists($fila['IdNodoWorkflowFinal'],$arrayNodosWorkflow))
			{
				$datosInsertar = $fila;
				$datosInsertar['IdCircuito'] = $TipoOriginal['IdCircuito'];
				$datosInsertar['IdNodoWorkflowActual'] = $arrayNodosWorkflow[$fila['IdNodoWorkflowActual']];
				$datosInsertar['IdNodoWorkflowFinal'] = $arrayNodosWorkflow[$fila['IdNodoWorkflowFinal']];
				if(!$oCircuitosWorkflow->Insertar($datosInsertar,$IdWorkflow))
					return false;
		


				if(!$oCircuitosWorkflowRoles->BuscarxIdWorkflow($fila,$resultRoles,$numfilas))
					return false;
				
				
				$datosAccionesRoles['IdWorkflow'] = $fila['IdWorkflow'];
				if(!$oCircuitosWorkflowRolesAcciones->BusquedaAvanzada($datosAccionesRoles,$resultadoAccionesRoles,$numfilasAccionesRoles))	
					return false;
					

				
				while($filaRoles = $this->conexion->ObtenerSiguienteRegistro($resultRoles))
				{
					$datosInsertar = $filaRoles;
					$datosInsertar['IdNodoWorkflow'] = $arrayNodosWorkflow[$filaRoles['IdNodoWorkflow']];
					$datosInsertar['IdWorkflow'] = $IdWorkflow;
					if(!$oCircuitosWorkflowRoles->Insertar($datosInsertar))
						return false;
					
				}
	
				while($filaAcciones = $this->conexion->ObtenerSiguienteRegistro($resultAcciones))
				{
					$datosInsertar = $filaAcciones;
					$datosInsertar['IdNodoWorkflow'] = $arrayNodosWorkflow[$filaRoles['IdNodoWorkflow']];
					$datosInsertar['IdWorkflow'] = $IdWorkflow;
					if(!$oCircuitosAreasEstadosRolesAcciones->Insertar($datosInsertar))
						return false;
					
				}
				
				if(!$oCircuitosWorkflowTiposDocumentos->BuscarxIdWorkflow($fila,$resultRoles,$numfilas))
					return false;
			
				while($filaDoc = $this->conexion->ObtenerSiguienteRegistro($resultRoles))
				{
					$datosInsertar = $filaDoc;
					$datosInsertar['IdWorkflow'] = $IdWorkflow;
					if(!$oCircuitosWorkflowTiposDocumentos->Insertar($datosInsertar))
						return false;
					
				}
									
		
			}
		}
		
		//return false;		
			
		return true;
	}

    public function DuplicarDocumento($datos,int $NuevoEstado = null, &$IdRegistroTipoDocumento = null)
    {

        if (!isset($datos['IdRegistroTipoDocumentoDuplicar']) || $datos['IdRegistroTipoDocumentoDuplicar'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe selecionar un documento a duplicar. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }

        /*if(!isset($datos['IdRegistroTipoDocumentoPadre']) || $datos['IdRegistroTipoDocumentoPadre']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe selecionar un documento padre. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

        // BUSCO EL DOCUMENTO A DUPLICAR
        $oDocumentosTipos = new cDocumentosTipos($this->conexion, $this->formato);
        $datosBuscarOriginal['IdRegistro'] = $datos['IdRegistroTipoDocumentoDuplicar'];
        if (!$oDocumentosTipos->BuscarxCodigo($datosBuscarOriginal, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar el tipo por codigo. ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
            return false;
        }
        $TipoOriginal = $this->conexion->ObtenerSiguienteRegistro($resultado);



        $TipoOriginal['VigenciaDesde'] = date("m/Y", strtotime(FuncionesPHPLocal::ConvertirFecha($TipoOriginal["VigenciaDesde"], 'aaaammdd', 'aaaa-mm-dd')));
        if ($TipoOriginal["VigenciaHasta"] != "")
            $TipoOriginal['VigenciaHasta'] = date("m/Y", strtotime(FuncionesPHPLocal::ConvertirFecha($TipoOriginal["VigenciaHasta"], 'aaaammdd', 'aaaa-mm-dd')));

        //Separo los datos del TipoOriginal en un nuevo array modificable para el doc a duplicar
        $datosDuplicarDesdeOriginal = $TipoOriginal;



        // Si se manda un nuevo estado para el documento duplicado, lo seteo directamente
        if($NuevoEstado)
            $datosDuplicarDesdeOriginal['Estado'] = $NuevoEstado;

        $datosDuplicarDesdeOriginal['Nombre'] = $datosDuplicarDesdeOriginal['Nombre']." - DUPLICADO";
        $datosDuplicarDesdeOriginal['NombreCorto'] = "DUPLICADO";

        if (!$oDocumentosTipos->Insertar($datosDuplicarDesdeOriginal, $codigoinsertado, $IdTipoDocumento))
            return false;

        $IdRegistroTipoDocumento = $codigoinsertado;
        //Modifico el evento
        $datosEvento['IdRegistro'] = $codigoinsertado;
        $datosEvento['LoadTipoDocumento'] = $TipoOriginal['LoadTipoDocumento'];
        $datosEvento['UnLoadTipoDocumento'] = $TipoOriginal['UnLoadTipoDocumento'];
        if (!$oDocumentosTipos->ModificarEvento($datosEvento))
            return false;

        //DUPLICO DOCUMENTOS TIPOS MODULOS

        $oDocumentosTiposModulos = new cDocumentosTiposModulos($this->conexion, $this->formato);

        //BUSCO LOS DOCUMENTOS TIPOS MODULOS A DUPLICAR
        $datosArea['IdRegistroTipoDocumento'] = $TipoOriginal['IdRegistro'];
        if (!$oDocumentosTiposModulos->BuscarxIdRegistroTipoDocumento($datosArea, $resultado, $numfilas))
            return false;

        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $fila['IdRegistroTipoDocumento'] = $codigoinsertado;
            $fila['IdTipoDocumento'] = $IdTipoDocumento;

            $datosJson = json_decode($fila['DatosJson'],true);
            if(!isset($datosJson['MiEscuela']) || $datosJson['MiEscuela']!=true)
                $fila['MiEscuela'] = false;
            else
                $fila['MiEscuela'] = true;

            if(!isset($datosJson['TodosLosEstados']) || $datosJson['TodosLosEstados']!=true)
                $fila['TodosLosEstados'] = false;
            else
                $fila['TodosLosEstados'] = true;

            if(!isset($datosJson['TodosLosCargosSeleccionados']) || $datosJson['TodosLosCargosSeleccionados']!=true)
                $fila['TodosLosCargosSeleccionados'] = false;
            else
                $fila['TodosLosCargosSeleccionados'] = true;

            if (!$oDocumentosTiposModulos->Insertar($fila,$IdTipoDocumentoModulo))
                return false;
        }




        // DUPLICO AREAS Y ROLES
        $oAreasTiposDocumentos = new cAreasTiposDocumentos($this->conexion, $this->formato);

        //BUSCO LAS AREAS DEL TIPO DOCUMENTO A DUPLICAR
        $datosArea['IdRegistroTipoDocumento'] = $TipoOriginal['IdRegistro'];
        if (!$oAreasTiposDocumentos->BuscarxIdRegistroTipoDocumento($datosArea, $resultado, $numfilas))
            return false;

        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $fila['IdRegistroTipoDocumento'] = $codigoinsertado;
            $fila['IdTipoDocumento'] = $IdTipoDocumento;

            if (!$oAreasTiposDocumentos->Insertar($fila))
                return false;
        }

        //BUSCO LOS AREAS ROLES DEL TIPO DOCUMENTO A DUPLICAR
        $oAreasTiposDocumentosRoles = new cAreasTiposDocumentosRoles($this->conexion, $this->formato);
        $datosArea['IdRegistroTipoDocumento'] = $TipoOriginal['IdRegistro'];
        if (!$oAreasTiposDocumentosRoles->BuscarRolesxIdRegistroTipoDocumento($datosArea, $resultado, $numfilas))
            return false;

        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $fila['IdRegistroTipoDocumento'] = $codigoinsertado;
            if (!$oAreasTiposDocumentosRoles->Insertar($fila))
                return false;
        }

        //BUSCO LOS DOCUMENTOS DEPENDIENTES A DUPLICAR
        $oDocumentosTiposDependientes = new cDocumentosTiposDependientes($this->conexion, $this->formato);
        $datosArea['IdRegistroTipoDocumento'] = $TipoOriginal['IdRegistro'];
        if (!$oDocumentosTiposDependientes->BuscarxIdRegistroTipoDocumento($datosArea, $resultado, $numfilas))
            return false;

        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
            $fila['IdRegistroTipoDocumento'] = $codigoinsertado;
            if (!$oDocumentosTiposDependientes->Insertar($fila, $codigoinsertadoTiposDependientes))
                return false;
        }



        // SETEO DATOS PARA INSERTAR CIRCUITOS
        $datosDuplicar['IdRegistro'] = $codigoinsertado;
        $datosDuplicar['IdRegistroDuplicar'] = $TipoOriginal['IdRegistro'];

        // DUPLICO CIRCUITO
        if (!$this->DuplicarCircuito($datosDuplicar))
            return false;


        return true;
    }



}
?>