<?php
class cDocumentosTiposProcesar
{

	protected $conexion;
	protected $formato;
	protected $previsualizar;
	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		$this->previsualizar = false;

    }

	// Destructor de la clase
	function __destruct() {
    }

//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------


	static function IncluirBuscadorGeneral($IdRegistro)
	{
		if (!file_exists(CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_FISICA."cliente_".$_SESSION['IdCliente']."/html/buscador_general_".$IdRegistro.".html"))
			return false;

		include (CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_FISICA."cliente_".$_SESSION['IdCliente']."/html/buscador_general_".$IdRegistro.".html");
	}


	public function RecargarModulo($datos,&$html_generado)
	{
		$html_generado='';
		$oDocumentosTiposZonas = new cDocumentosTiposZonas($this->conexion,$this->formato);
		$datosbusqueda['IdZonaModulo'] = $datos['IdZonaModulo'];
		if(!$oDocumentosTiposZonas->BuscarxCodigo($datosbusqueda,$resultado,$numfilas))
			return false;

		if($numfilas>0){
			$datosmodulo = $this->conexion->ObtenerSiguienteRegistro($resultado);
			if(!$this->CargarModulo($datosmodulo,$html_generado))
				return false;
		}

		return true;
	}



	public function Procesar($datosregistro,&$html)
	{
		$arreglozonas = array();

		$DocumentosTiposMacros = new cDocumentosTiposMacros($this->conexion,$this->formato);
		$oDocumentosTiposZonas = new cDocumentosTiposZonas($this->conexion,$this->formato);

		$oDocumentosTiposMacrosZonas = new cDocumentosTiposMacrosZonas($this->conexion,$this->formato);
		if(!$DocumentosTiposMacros->BuscarPasosMacros($datosregistro,$resultado,$numfilas))
			return false;

		$html = "";
		$html .= '<div class="macrossite row">';
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			if(!$oDocumentosTiposMacrosZonas->BuscarZonasxIdRegistroTipoDocumento($fila,$resultadoZona,$numfilasZona))
				return false;
			$html .= '<div class="macros '.$fila['ClaseMacro'].'" id="macro_'.$fila['IdMacroPosicion'].'">';
			$html .= '<div class="moverMacro btn-xs btn btn-primary"><i class="fas fa-expand-arrows-alt"></i>&nbsp;Mover</div>';
			$html .= '<div class="eliminarMacro"><a href="javascript:void(0)" class="btn-xs btn btn-danger" data-id="'.$fila['IdMacroPosicion'].'" onclick="EliminarMacro(this)" title="Eliminar"><i class="fas fa-times"></i>&nbsp;Eliminar</a></div>';
			$html .= '<div class="clearboth">&nbsp;</div>';
			while($filaZona = $this->conexion->ObtenerSiguienteRegistro($resultadoZona))
			{
			    $html .= '<div class="estructuras '.$filaZona['ClaseEstructura'].'" id="zona_'.$filaZona['IdZona'].'">';
				$datosbusquedaZona['IdZona'] = $filaZona['IdZona'];
				if(!$oDocumentosTiposZonas->BuscarxIdZona($datosbusquedaZona,$resultadoModulos,$numfilasModulos))
					return false;
				while ($datosModulo = $this->conexion->ObtenerSiguienteRegistro($resultadoModulos))
				{
					if(!$this->CargarModulo($datosModulo,$html))
						return false;
				}
				$html .= '</div>';
			}
			$html .= '<div class="clearboth">&nbsp;</div></div>';

		}
			$html .= '<div class="clearboth">&nbsp;</div></div>';
		return true;

	}



	private function CargarModulo($datosModulo,&$html_generado,$enBuscador=false)
	{
		$carpeta = "estructuras/modulos";
		$Eliminar = 1;
		if ($datosModulo['IdCampo']!="")
		{
			$carpeta = "estructuras/campos";
			$Eliminar = 2;
		}
		if (isset($datosModulo['IdDocumentoAdjunto']) && $datosModulo['IdDocumentoAdjunto']!="")
		{
			$carpeta = "estructuras/archivos";
			$Eliminar = 3;
		}
		$datosModulo['htmleditfooter']="";
		$datosModulo['mouseaction'] = "";
		$datosModulo['htmledit'] = "";
		$datosModulo['conexion'] = $this->conexion;
		if (!$this->previsualizar)
		{
			$datosModulo['htmledit'] = "";
			FuncionesPHPLocal::ArmarLinkMD5("doc_documentos_tipos_confeccionar_campo_avanzado.php",array("IdZonaModulo"=>$datosModulo['IdZonaModulo']),$get,$md5);
			if ($datosModulo['IdCampo']!="")
			{
				//$datosModulo['htmledit'] .= '<div class="modules_header_advance">';
				//$datosModulo['htmledit'] .='<div class="btn-advance btn-xs btn btn-primary" data-id="'.$datosModulo['IdZonaModulo'].'" data-md5="'.$md5.'"><i class="fas fa-cog"></i>&nbsp;Avanzado</div>';
				//$datosModulo['htmledit'] .= '</div>';
			}
			$datosModulo['htmledit'] .= '<div class="modules_header" id="tools_'.$datosModulo['IdZonaModulo'].'">';
			$datosModulo['htmledit'] .='<div class="moverModulo btn-xs btn btn-primary"><i class="fas fa-expand-arrows-alt"></i></div>';
			if ($datosModulo['IdCampo']!="")
				$datosModulo['htmledit'] .= '	<div class="modules_edit"><a href="javascript:void(0)" data-href="'.$datosModulo['IdZonaModulo'].'" data-id="'.$datosModulo['IdCampo'].'" class="btn btn-info btn-xs editarCampo" title="Editar"><i class="fa fa-th"></i>&nbsp;Editar campo</a></div>';
			if ($datosModulo['CampoEditable'])
				$datosModulo['htmledit'] .= '	<div class="modules_edit"><a href="javascript:void(0)" class="btn btn-info btn-xs" onclick="AbrirEditarModulos('.$datosModulo['IdZonaModulo'].')" title="Editar"><i class="fa fa-edit"></i>&nbsp;Editar</a></div>';
			$datosModulo['htmledit'] .= '	<div class="modules_delete"><a href="javascript:void(0)" class="btn btn-danger btn-xs"  onclick="EliminarModulo('.$datosModulo['IdZonaModulo'].','.$Eliminar.')" title="Eliminar"><i class="fa fa-times"></i>&nbsp;Eliminar</a></div>';
			$datosModulo['htmledit'] .= '</div><div style="clear:both"></div>';
			$datosModulo['mouseaction'] = "";

			if ($datosModulo['IdCampo']!="")
			{
				$campoObligatorio=$datosModulo['CampoObligatorio'];
				$classUno = ($campoObligatorio==1)?"active":"";
				$classDos = ($campoObligatorio==0)?"active":"";
				$checkUno = ($campoObligatorio==1)?'checked="checked"':'';
				$checkDos = ($campoObligatorio==0)?'checked="checked"':'';
				$datosModulo['htmleditfooter'] .= '<div class="footerAdvance"><div class="data_onoff"><label class="title_onoff">Obligatorio</label><div class="btn-group" id="status" data-toggle="buttons">
													  <label data-id="'.$datosModulo['IdZonaModulo'].'" data-check="1" class="btn btn-default btn-on btn-xs '.$classUno.'">
													  <input type="radio" class="radio" value="1" name="CampoObligatorio_'.$datosModulo['IdZonaModulo'].'" '.$checkUno.'>SI</label>
													  <label data-id="'.$datosModulo['IdZonaModulo'].'" data-check="0" class="btn btn-default btn-off btn-xs '.$classDos.'">
													  <input type="radio" class="radio" value="0" name="CampoObligatorio_'.$datosModulo['IdZonaModulo'].'" '.$checkDos.'>NO</label>
													</div><div style="clear:both"></div></div></div>';
			}
		}

		$datosModulo['Valores'] = false;
		if ($datosModulo['TieneValores']==1)
		{
			$oEstructuraCamposValores = new cEstructuraCamposValores($this->conexion,$this->formato);
			if(!$oEstructuraCamposValores->BuscarxIdCampo($datosModulo,$resultadoCampos,$numfilas))
				return false;

			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoCampos))
			{
				$datosModulo['Valores'][] = $fila;
			}

		}
		$datosModulo['enBuscador'] = $enBuscador;
		$htmlModuleRender = FuncionesPHPLocal::RenderFile($carpeta."/html/".$datosModulo['NombreArchivo'],$datosModulo);
		$html = $this->ProcesarHtmlInterno($htmlModuleRender);
		$html_generado .= $html;


		return true;
	}



	private function ProcesarHtmlInterno($htmlModuleRender)
	{
		$html = "";
		cSepararHTML::ProcesarHTML($htmlModuleRender,$partes);
		foreach($partes as $partehtml)
		{
			if(is_array($partehtml))
			{
			}else
				$html .= $partehtml;
		}

		return $html;
	}



	public function MapearElasticSearch(&$result)
	{
		$datosBusqueda = array();
		$oEstructuraCampos = new cEstructuraCampos($this->conexion,$this->formato);
		if(!$oEstructuraCampos->BuscarCamposMapeoElasticSearch($datosBusqueda,$resultado,$numfilas))
			return false;

		$arrayCampos = array();
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
			$arrayCampos[] = $fila;


		/*$oElastic = new cMappingElastic(CLIENTINDEX);
		if(!$oElastic->Mapping())
			return false;
		$oElastic = new cMappingElastic(CLIENTAUDIT);
		if(!$oElastic->Mapping())
			return false;*/


		return true;
	}

	public function GenerarJson($datosregistro)
	{

		return true;
		//Subir imagenes
	}


    public function GenerarJsonBuscador($datosregistro)
    {
        $oDocumentosTipos = new cDocumentosTipos($this->conexion,$this->formato);
        $datos['IdRegistro'] = $datosregistro['IdRegistroTipoDocumento'];
        if (!$oDocumentosTipos->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;
        $DatosCamposJson = array();
        $datosDocumento = $this->conexion->ObtenerSiguienteRegistro($resultado);
        //$DatosCamposJson = json_decode($datosDocumento['DatosCamposJson'],true);

        $datosBusqueda['IdRegistroTipoDocumento'] = $datosregistro['IdRegistroTipoDocumento'];
        $oDocumentosTiposModulos = new cDocumentosTiposModulos($this->conexion,$this->formato);
        if(!$oDocumentosTiposModulos->BuscarxIdRegistroTipoDocumento($datosBusqueda,$resultado,$numfilas))
            return false;
        $arrayModulos = array();
        while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            $arrayModulos[$fila['IdDocumentoTipoModulo']] = $fila;




        if(!$this->_ValidarDatosCajas($arrayModulos))
            return false;

        //NUEVO
        $oModulosDocumentosTipos = new cModulosDocumentosTipos($this->conexion,$this->formato);
        $datosModulosDocumentosTipos['orderby'] = "Nombre ASC";
        if (!$oModulosDocumentosTipos->BusquedaAvanzada($datosModulosDocumentosTipos,$resultadoModulosDocumentosTipos,$numfilasModulosDocumentosTipos))
            return false;

        $PHP = "<?php \n";
        $PHP .= "//DATOS GENERALES \n";
        $i = 1;
        while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoModulosDocumentosTipos))
        {
            $valorobligatorio =$valorvisualiza  = 'false';
            $titulo = $descripcion = "";
                $orden = $i;

            $campo = $fila['NombreCte'];
            if(array_key_exists($fila['IdDocumentoTipoModulo'],$arrayModulos))
                $valorvisualiza = 'true';

            if(isset($arrayModulos[$fila['IdDocumentoTipoModulo']]['Obligatorio']) && $arrayModulos[$fila['IdDocumentoTipoModulo']]['Obligatorio']=="1")
                $valorobligatorio = 'true';

            if(isset($arrayModulos[$fila['IdDocumentoTipoModulo']]['Titulo']) && $arrayModulos[$fila['IdDocumentoTipoModulo']]['Titulo']!="")
                $titulo = utf8_encode($arrayModulos[$fila['IdDocumentoTipoModulo']]['Titulo']);

            if(isset($arrayModulos[$fila['IdDocumentoTipoModulo']]['Descripcion']) && $arrayModulos[$fila['IdDocumentoTipoModulo']]['Descripcion']!="")
                $descripcion = utf8_encode($arrayModulos[$fila['IdDocumentoTipoModulo']]['Descripcion']);

            if(isset($arrayModulos[$fila['IdDocumentoTipoModulo']]['Orden']) && $arrayModulos[$fila['IdDocumentoTipoModulo']]['Orden']!="")
                $orden = $arrayModulos[$fila['IdDocumentoTipoModulo']]['Orden'];


            $PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['visualiza'] = ".$valorvisualiza.";\n";
            $PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['obligatorio'] = ".$valorobligatorio.";\n";
            $PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['titulo'] = '".$titulo."';\n";
            $PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['descripcion'] = '".$descripcion."';\n";
            $PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['orden'] = '".$orden."';\n";
            if($campo!="DatosDelAgente")
                $PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = '".$fila['Archivo']."';\n";



            if(!empty($arrayModulos[$fila['IdDocumentoTipoModulo']]['DatosJson']))
            {
                $arrayjson = json_decode($arrayModulos[$fila['IdDocumentoTipoModulo']]['DatosJson'],true);

                foreach($arrayjson as $CampoCaja=>$valor)
                {

                    if($valor==1){
                        if($valor==="1"){
                            $valorcaja = $valor;
                        }else{
                            $valorcaja = 'true';
                        }
                    }elseif($valor==0){
                        $valorcaja ='false';
                    }else{
                        $valorcaja = $valor;
                    }

                    if (isset($valorcaja) && $valorcaja!="")
                        $PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['CajaCargaDatos']['".$CampoCaja."'] = ".$valorcaja.";\n";
                    else
                        $PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['CajaCargaDatos']['".$CampoCaja."'] = '';\n";

                }
            }

            $i++;
        }


        $PHP .= "ksort(\$ArrayValidacion);\n";

        $datosBusqueda['IdRegistroTipoDocumento'] = $datosregistro['IdRegistroTipoDocumento'];
        $datosBusqueda['Anio'] = $_SESSION['Anio'];
        $datosBusqueda['Mes'] = $_SESSION['Mes'];
        $oDocumentosTiposDependientes = new cDocumentosTiposDependientes($this->conexion);
        if(!$oDocumentosTiposDependientes->BuscarxIdRegistroTipoDocumento($datosBusqueda,$resultado,$numfilas))
            return false;

        $PHP .= "//DATOS DOCUMENTOS DEPENDIENTES \n";
        if($numfilas==0)
        {
            $PHP .= "\$ArrayDocumentosDependientes = array();\n";
        }else{
            while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            {
                $PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['IdTipoDocumentoDependiente'] = ".$fila['IdTipoDocumentoDependiente'].";\n";
                $PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['IdRegistroTipoDocumento'] = ".$fila['IdRegistroTipoDocumento'].";\n";
                $PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['IdTipoDocumento'] = ".$fila['IdTipoDocumento'].";\n";
                $PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['IdEstado'] = ".$fila['IdEstado'].";\n";
                $PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['Orden'] = ".$fila['Orden'].";\n";
                $PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['NombreTipoDocumento'] = '".$fila['Nombre']."';\n";
                $PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['NombreEstado'] = '".$fila['NombreEstado']."';\n";
            }

        }

        if(!is_dir(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA)){
            @mkdir(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA);
        }

        if(!is_dir(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA."documentos_tipos")){
            @mkdir(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA."documentos_tipos");
        }

        $file = CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA."documentos_tipos/documento_tipo_".$datosDocumento['IdTipoDocumento'].".php";
        if (!file_put_contents($file, $PHP, LOCK_EX) !== false) {
        	FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error al guardar el archivo', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato'=>$this->formato]);
	        return false;
        }

        return true;

    }



    private function _ValidarDatosCajas($arrayModulos)
    {
        $array1 = $array2 = $arrayModulos;

        $oModulosDocumentosTiposRestricciones = new cModulosDocumentosTiposRestricciones($this->conexion,$this->formato);
        $datosBuscar['orderby'] = "IdDocumentoTipoModulo Asc";
        if(!$oModulosDocumentosTiposRestricciones->BusquedaAvanzada($datosBuscar,$resultado,$numfilas))
            return false;
        $arrayError = array();
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            $arrayError[$fila['IdDocumentoTipoModulo']][$fila['IdDocumentoTipoModuloRestriccion']] = $fila['Descripcion'];

        if(count($arrayModulos)>0 && count($arrayError)>0)
        {
            foreach ($array1 as $IdDocumentoTipoModulo => $filaModulos)
            {
                foreach ($array2 as $IdDocumentoTipoModulo2 => $filaModulos2)
                {
                    $Error = false;
                    $ErrorDesc ="";
                    if($IdDocumentoTipoModulo!=$IdDocumentoTipoModulo2)
                    {
                        if(isset($arrayError[$IdDocumentoTipoModulo][$IdDocumentoTipoModulo2]))
                        {
                            $Error = true;
                            $ErrorDesc = $arrayError[$IdDocumentoTipoModulo][$IdDocumentoTipoModulo2];
                        }

                        if(isset($arrayError[$IdDocumentoTipoModulo2][$IdDocumentoTipoModulo]))
                        {
                            $Error = true;
                            $ErrorDesc = $arrayError[$IdDocumentoTipoModulo2][$IdDocumentoTipoModulo];
                        }

                    }

                    if($Error)
                    {
                        FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"".$ErrorDesc."",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                        return false;
                    }
                }

            }
        }

        return true;

    }




	/*public function GenerarJsonBuscador($datosregistro)
	{

		$oDocumentosTipos = new cDocumentosTipos($this->conexion,$this->formato);
		$oDocumentosTipos->SetIdCliente($_SESSION['IdCliente']);
		$datos['IdRegistro'] = $datosregistro['IdRegistroTipoDocumento'];
		if (!$oDocumentosTipos->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		$DatosCamposJson = array();
		$datosDocumento = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$DatosCamposJson = json_decode($datosDocumento['DatosCamposJson'],true);


		if(!$this->_ValidarDatosCajas($DatosCamposJson))
			return false;

		if(file_exists(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA."documentos_tipos/documento_tipo_campos.php"))
			include(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA."documentos_tipos/documento_tipo_campos.php");


		$MuestraBloqueAccion =  $MuestraBloqueInasistencia = 'false';

		$PHP = "<?php \n";
		$PHP .= "//DATOS GENERALES \n";
		foreach($ArrayCampos['DatosGenerales'] as $campo=>$label)
		{

			$valorobligatorio =$valorvisualiza  = 'false';
			if(isset($DatosCamposJson[$campo]['visualiza']) && $DatosCamposJson[$campo]['visualiza']===true)
				$valorvisualiza = 'true';


			if(isset($DatosCamposJson[$campo]['obligatorio']) && $DatosCamposJson[$campo]['obligatorio']===true)
				$valorobligatorio = 'true';

			$PHP .= "\$ArrayValidacion['".$campo."']['visualiza'] = ".$valorvisualiza.";\n";
			$PHP .= "\$ArrayValidacion['".$campo."']['obligatorio'] = ".$valorobligatorio.";\n";
		}

		$PHP .= "//DATOS AGENTE \n";
		foreach($ArrayCampos['DatosAgente'] as $campo=>$label)
		{

			$valorobligatorio =$valorvisualiza  = 'false';
			if(isset($DatosCamposJson[$campo]['visualiza']) && $DatosCamposJson[$campo]['visualiza']===true)
				$valorvisualiza = 'true';

			if(isset($DatosCamposJson[$campo]['obligatorio']) && $DatosCamposJson[$campo]['obligatorio']===true)
				$valorobligatorio = 'true';

			$PHP .= "\$ArrayValidacion['".$campo."']['visualiza'] = ".$valorvisualiza.";\n";
			$PHP .= "\$ArrayValidacion['".$campo."']['obligatorio'] = ".$valorobligatorio.";\n";
		}


		$PHP .= "//DATOS ACCION \n";
		foreach($ArrayCampos['DatosAccion'] as $campo=>$label)
		{

			$valorobligatorio =$valorvisualiza  = 'false';
			if(isset($DatosCamposJson[$campo]['visualiza']) && $DatosCamposJson[$campo]['visualiza']===true)
			{
				$valorvisualiza = 'true';
				$MuestraBloqueAccion = 'true';
			}
			if(isset($DatosCamposJson[$campo]['obligatorio']) && $DatosCamposJson[$campo]['obligatorio']===true)
				$valorobligatorio = 'true';

			$PHP .= "\$ArrayValidacion['".$campo."']['visualiza'] = ".$valorvisualiza.";\n";
			$PHP .= "\$ArrayValidacion['".$campo."']['obligatorio'] = ".$valorobligatorio.";\n";
		}
		$PHP .= "//DATOS INACISTENCIA \n";
		foreach($ArrayCampos['DatosInasistencia'] as $campo=>$label)
		{

			$valorobligatorio =$valorvisualiza  = 'false';
			if(isset($DatosCamposJson[$campo]['visualiza']) && $DatosCamposJson[$campo]['visualiza']===true)
			{
				$valorvisualiza = 'true';
				$MuestraBloqueInasistencia = 'true';
			}
			if(isset($DatosCamposJson[$campo]['obligatorio']) && $DatosCamposJson[$campo]['obligatorio']===true)
				$valorobligatorio = 'true';

			$PHP .= "\$ArrayValidacion['".$campo."']['visualiza'] = ".$valorvisualiza.";\n";
			$PHP .= "\$ArrayValidacion['".$campo."']['obligatorio'] = ".$valorobligatorio.";\n";
		}

		$PHP .= "\$MuestraBloqueAccion = ".$MuestraBloqueAccion.";\n";
		$PHP .= "\$MuestraBloqueInasistencia = ".$MuestraBloqueInasistencia.";\n";


		$PHP .= "?>";


		$file = CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA."documentos_tipos/documento_tipo_".$datosDocumento['IdTipoDocumento'].".php";

		if (!file_put_contents($file, $PHP, LOCK_EX) !== false)
			return false;



		//NUEVO

		if(file_exists(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA."documentos_tipos/documento_tipo_campos_suna.php"))
			include(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA."documentos_tipos/documento_tipo_campos_suna.php");


		$PHP = "<?php \n";
		$PHP .= "//DATOS GENERALES \n";
		$i = 1;

		foreach($ArrayCampos['DatosGenerales'] as $campo=>$label)
		{

			$valorobligatorio =$valorvisualiza  = 'false';
			$titulo = $descripcion = "";
			$orden = $i;
			if(isset($DatosCamposJson[$campo]['visualiza']) && $DatosCamposJson[$campo]['visualiza']===true)
				$valorvisualiza = 'true';

			if(isset($DatosCamposJson[$campo]['obligatorio']) && $DatosCamposJson[$campo]['obligatorio']===true)
				$valorobligatorio = 'true';


			if(isset($DatosCamposJson[$campo]['titulo']) && $DatosCamposJson[$campo]['titulo']!="")
				$titulo = $DatosCamposJson[$campo]['titulo'];

			if(isset($DatosCamposJson[$campo]['descripcion']) && $DatosCamposJson[$campo]['descripcion']!="")
				$descripcion = $DatosCamposJson[$campo]['descripcion'];

			if(isset($DatosCamposJson[$campo]['orden']) && $DatosCamposJson[$campo]['orden']!="")
				$orden = $DatosCamposJson[$campo]['orden'];

			$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['visualiza'] = ".$valorvisualiza.";\n";
			$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['obligatorio'] = ".$valorobligatorio.";\n";
			$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['titulo'] = '".$titulo."';\n";
			$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['descripcion'] = '".$descripcion."';\n";
			$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['orden'] = '".$orden."';\n";

			switch($campo)
			{
				case 'SeleccionaCargo':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'cargos_propios_escuela.php';\n";
				break;

				case 'SeleccionaCargoCualquiera':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'cargos_no_escuela.php';\n";
				break;

				case 'NuevoCargo':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'datos_nuevo_cargo.php';\n";
				break;

				case 'NuevoCargoAD':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'datos_nuevo_cargo_AD.php';\n";
				break;

				case 'NuevoCargoAM':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'datos_nuevo_cargo_AM.php';\n";
				break;

				case 'NuevoCargoAuxiliar':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'datos_nuevo_cargo_auxiliar.php';\n";
				break;

				case 'VisualizaCargo':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'cargos_visualiza.php';\n";
				break;

				case 'OrganizacionApoyo':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'organizacion_apoyo.php';\n";
				break;

				case 'InasistenciaConEncuadre':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'inasistencia_con_encuadre.php';\n";
				break;

				case 'InasistenciaSinEncuadre':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'inasistencia_sin_encuadre.php';\n";
				break;

				case 'InasistenciaCalculoAutomatico':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'inasistencia_calculo_automatica.php';\n";
				break;

				case 'LicenciaAutomatica':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'licencia_automatica.php';\n";
				break;

				case 'DniReemplazo':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'documento_reemplazante.php';\n";
				break;

				case 'Observaciones':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'observaciones.php';\n";
				break;

				/*case 'DatosAltasMovimientosCeses':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'transacciones.php';\n";
				break;*/
				/*
				case 'FechaDesdeFechaHasta':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'periodo.php';\n";
				break;

				case 'TieneCUPOF':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'datos_cupof.php';\n";
				break;

				case 'FechaTomaPosesion':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'fecha_toma_posesion.php';\n";
				break;


				case 'NovedadRelacionada':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'novedad_relacionada.php';\n";
				break;
				case 'NovedadRelacionadaCuil':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'novedad_relacionada_cuil.php';\n";
				break;

                case 'InasistenciaRelacionada':
                    $PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'novedad_inasistencia_relacionada.php';\n";
                    break;

                case 'FechaDesde':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'fecha_desde.php';\n";
				break;

				case 'NroResolucion':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'nro_resolucion.php';\n";
				break;

				case 'Turno':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'turno.php';\n";
				break;

				case 'SeleccionaCargoAux':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'cargos_propios_nodoc_escuela.php';\n";
				break;
				case 'InasistenciaDocenteSinJustificar':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'inasistencia_docente_sinjustificar.php';\n";
				break;

				case 'FechaDesignacion':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'fecha_designacion.php';\n";
				break;

				case 'NuevoCargoMAD':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'datos_nuevo_cargo_MAD.php';\n";
				break;

				case 'DiasHorarios':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'diasHorarios.php';\n";
				break;
				case 'PID':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'PID.php';\n";
				break;
				case 'LicenciaParo':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'licencia_paro.php';\n";
				break;
				case 'LicenciaAdministrativa':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'licencia_administrativa.php';\n";
				break;
				case 'LicenciaAdministrativaNoDocente':
					$PHP .= "\$ArrayValidacion[".$orden."]['".$campo."']['archivo'] = 'licencia_administrativa_noDocente.php';\n";
				break;



			}



			$i++;
		}

		$PHP .= "ksort(\$ArrayValidacion);\n";

		$datosBusqueda['IdRegistroTipoDocumento'] = $datosregistro['IdRegistroTipoDocumento'];
		$datosBusqueda['Anio'] = $_SESSION['Anio'];
		$datosBusqueda['Mes'] = $_SESSION['Mes'];
		$oDocumentosTiposDependientes = new cDocumentosTiposDependientes($this->conexion);
		if(!$oDocumentosTiposDependientes->BuscarxIdRegistroTipoDocumento($datosBusqueda,$resultado,$numfilas))
			return false;

		$PHP .= "//DATOS DOCUMENTOS DEPENDIENTES \n";

		if($numfilas==0)
		{
		$PHP .= "\$ArrayDocumentosDependientes = array();\n";
		}else{
			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
			{
				$PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['IdTipoDocumentoDependiente'] = ".$fila['IdTipoDocumentoDependiente'].";\n";
				$PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['IdRegistroTipoDocumento'] = ".$fila['IdRegistroTipoDocumento'].";\n";
				$PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['IdTipoDocumento'] = ".$fila['IdTipoDocumento'].";\n";
				$PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['IdEstado'] = ".$fila['IdEstado'].";\n";
				$PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['Orden'] = ".$fila['Orden'].";\n";
				$PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['NombreTipoDocumento'] = '".$fila['Nombre']."';\n";
				$PHP .= "\$ArrayDocumentosDependientes[".$fila['IdEstado']."][".$fila['IdTipoDocumento']."]['NombreEstado'] = '".$fila['NombreEstado']."';\n";
			}

		}

		$file = CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA."documentos_tipos/documento_tipo_suna_".$datosDocumento['IdTipoDocumento'].".php";

		if (!file_put_contents($file, $PHP, LOCK_EX) !== false)
			return false;


		return true;
		//Subir imagenes
	}

	private function _ValidarDatosCajas($datos)
	{
		if(isset($datos['FechaDesdeFechaHasta']['visualiza']) && $datos['FechaDesdeFechaHasta']['visualiza']===true &&  isset($datos['FechaDesde']['visualiza']) && $datos['FechaDesde']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Periodo (Fecha Desde / Hasta)' y 'Fecha Desde'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['InasistenciaDocenteSinJustificar']['visualiza']) && $datos['InasistenciaDocenteSinJustificar']['visualiza']===true &&  isset($datos['InasistenciaSinEncuadre']['visualiza']) && $datos['InasistenciaSinEncuadre']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Inasistencia sin Encuadre' y 'Inasistencia sin justificar'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['InasistenciaDocenteSinJustificar']['visualiza']) && $datos['InasistenciaDocenteSinJustificar']['visualiza']===true &&  isset($datos['FechaDesdeFechaHasta']['visualiza']) && $datos['FechaDesdeFechaHasta']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Inasistencia sin justificar' y 'Periodo (Fecha Desde / Hasta)'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['InasistenciaDocenteSinJustificar']['visualiza']) && $datos['InasistenciaDocenteSinJustificar']['visualiza']===true &&  isset($datos['InasistenciaConEncuadre']['visualiza']) && $datos['InasistenciaConEncuadre']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Inasistencia con Encuadre' y 'Inasistencia sin justificar'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['InasistenciaConEncuadre']['visualiza']) && $datos['InasistenciaConEncuadre']['visualiza']===true &&  isset($datos['InasistenciaSinEncuadre']['visualiza']) && $datos['InasistenciaSinEncuadre']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Inasistencia con Encuadre' y 'Inasistencia sin Encuadre'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		if(isset($datos['InasistenciaConEncuadre']['visualiza']) && $datos['InasistenciaConEncuadre']['visualiza']===true &&  isset($datos['InasistenciaCalculoAutomatico']['visualiza']) && $datos['InasistenciaCalculoAutomatico']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Inasistencia con Encuadre' y 'Inasistencia Automatica'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['InasistenciaSinEncuadre']['visualiza']) && $datos['InasistenciaSinEncuadre']['visualiza']===true &&  isset($datos['InasistenciaCalculoAutomatico']['visualiza']) && $datos['InasistenciaCalculoAutomatico']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Inasistencia sin Encuadre' y 'Inasistencia Automatica'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		if(isset($datos['NuevoCargo']['visualiza']) && $datos['NuevoCargo']['visualiza']===true &&  isset($datos['Turno']['visualiza']) && $datos['Turno']['visualiza']===true )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Nuevo Cargo AM' y 'Nuevo Turno / Cambio de Turno'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['NuevoCargo']['visualiza']) && $datos['NuevoCargo']['visualiza']===true &&  isset($datos['NuevoCargoAuxiliar']['visualiza']) && $datos['NuevoCargoAuxiliar']['visualiza']===true )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Nuevo Cargo AM' y 'Nuevo Cargo Auxiliar'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['NuevoCargo']['visualiza']) && $datos['NuevoCargo']['visualiza']===true &&  isset($datos['NuevoCargoAD']['visualiza']) && $datos['NuevoCargoAD']['visualiza']===true )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Nuevo Cargo AM' y 'Nuevo Cargo AD'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['NuevoCargoAuxiliar']['visualiza']) && $datos['NuevoCargoAuxiliar']['visualiza']===true &&  isset($datos['Turno']['visualiza']) && $datos['Turno']['visualiza']===true )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Nuevo Cargo Auxiliar' y 'Nuevo Turno / Cambio de Turno'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['NuevoCargoAD']['visualiza']) && $datos['NuevoCargoAD']['visualiza']===true && isset($datos['NuevoCargoAuxiliar']['visualiza']) && $datos['NuevoCargoAuxiliar']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Nuevo Cargo Auxiliar' y 'Nuevo Cargo AD'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['NuevoCargoAD']['visualiza']) && $datos['NuevoCargoAD']['visualiza']===true &&  isset($datos['Turno']['visualiza']) && $datos['Turno']['visualiza']===true )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Nuevo Cargo AD' y 'Nuevo Turno / Cambio de Turno'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}



		if(isset($datos['LicenciaAutomatica']['visualiza']) && $datos['LicenciaAutomatica']['visualiza']===true &&  isset($datos['FechaDesdeFechaHasta']['visualiza']) && $datos['FechaDesdeFechaHasta']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Licencia Automatica' y 'Periodo (Fecha Desde / Hasta)'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if(isset($datos['LicenciaAutomatica']['visualiza']) && $datos['LicenciaAutomatica']['visualiza']===true &&  isset($datos['FechaDesde']['visualiza']) && $datos['FechaDesde']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Licencia Automatica' y 'Fecha Desde'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['LicenciaAutomatica']['visualiza']) && $datos['LicenciaAutomatica']['visualiza']===true &&  isset($datos['InasistenciaConEncuadre']['visualiza']) && $datos['InasistenciaConEncuadre']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Licencia Automatica' y 'Inasistencia con Encuadre'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['LicenciaAutomatica']['visualiza']) && $datos['LicenciaAutomatica']['visualiza']===true &&  isset($datos['InasistenciaCalculoAutomatico']['visualiza']) && $datos['InasistenciaCalculoAutomatico']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Licencia Automatica' y 'Inasistencia Automatica'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		if(isset($datos['SeleccionaCargo']['visualiza']) && $datos['SeleccionaCargo']['visualiza']===true &&  isset($datos['SeleccionaCargoAux']['visualiza']) && $datos['SeleccionaCargoAux']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Selecciona Cargo' y 'Selecciona Cargo Auxiliar'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['SeleccionaCargo']['visualiza']) && $datos['SeleccionaCargo']['visualiza']===true &&  isset($datos['SeleccionaCargoCualquiera']['visualiza']) && $datos['SeleccionaCargoCualquiera']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Selecciona Cargo' y 'Selecciona Cargo Cualquiera'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['SeleccionaCargoAux']['visualiza']) && $datos['SeleccionaCargoAux']['visualiza']===true &&  isset($datos['SeleccionaCargoCualquiera']['visualiza']) && $datos['SeleccionaCargoCualquiera']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Selecciona Cargo Auxiliar' y 'Selecciona Cargo Cualquiera'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['NovedadRelacionada']['visualiza']) && $datos['NovedadRelacionada']['visualiza']===true &&  isset($datos['NovedadRelacionadaCuil']['visualiza']) && $datos['NovedadRelacionadaCuil']['visualiza']===true)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no puede seleccionar al mismo tiempo 'Novedad Relaciona Todas' y 'Novedad Relaciona Cuil'",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		return true;
	}*/


}//FIN CLASE

?>
