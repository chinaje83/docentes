<?php

use Mpdf\Output\Destination;

class cDeclaracionJuradaPDF
{
	use ManejoErrores;
    var $conexionEs;
    var $conexion;
    var $formato;
    var $pdfBase64;
    var $idEscuela;
    var $cantPages;


    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO)
    {
        $this->conexion = &$conexion;
        $this->conexionES = new Elastic\Conexion();
        $this->formato = $formato;
        $this->pdfBase64 = "";
        $this->idEscuela = "";
        $this->cantPages = 0;

    }

    function __destruct()
    {
    }


    public function getCountPage()
    {
        return $this->cantPages;
    }

    public function getPdf()
    {
        $archivo = CARPETADDJJESCUELAS_FISICA."ddjj_". $this->idEscuela.".pdf";
        $file = file_get_contents($archivo);
        return base64_encode($file);
    }

    public function saveSignerPdf($data_base64)
    {
        $data = base64_decode($data_base64);
        $archivo = CARPETADDJJESCUELAS_FISICA."ddjj_". $this->idEscuela.".pdf";
        $file = file_put_contents($archivo,$data);
        return true;
    }

    public function getPdfBase64()
    {
        return base64_encode($this->pdfBase64);
    }

    public function getPdfBase64File()
    {
        $archivo = CARPETADDJJESCUELAS_FISICA."ddjj_". $this->idEscuela.".pdf";
        $file = file_get_contents($archivo);
        return base64_encode($file);
    }

    public function setIdEscuela($idEscuela)
    {
        $this->idEscuela = $idEscuela;
    }

    public function getPdfExists($idEscuela) {

        $this->setIdEscuela($idEscuela);
        $archivo = CARPETADDJJESCUELAS_FISICA."ddjj_". $this->idEscuela.".pdf";
        return file_exists($archivo);
    }

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public function generarPDF(array $datosBusqueda,$type="I"): bool
    {
        $datosBusquedaTipo['IdTipoDDJJ'] = 1;
        $oDeclaracionJuradaTipos = new cDeclaracionJuradaTipos($this->conexion);
        if (!$oDeclaracionJuradaTipos->BuscarxTipo($datosBusquedaTipo, $resultadoTipo, $numfilasTipo))
            return false;
        $filaTipo = $this->conexion->ObtenerSiguienteRegistro($resultadoTipo);

        $this->setIdEscuela($datosBusqueda['IdEscuela']);
        $oEscuela = new cEscuelas($this->conexion,$this->formato);
        if (!$oEscuela->BuscarxCodigo($datosBusqueda, $resultadoEscuela, $numfilasEscuela))
            return false;

        if ($numfilasEscuela == 1)
            $resultadoEscuela = $this->conexion->ObtenerSiguienteRegistro($resultadoEscuela);

        $oPuestos   = new Elastic\Puestos($this->conexionES);
        if (!$oPuestos->getPuestosxEscuela($datosBusqueda, $resultado))
            die();

        $aggsTipos = $resultado['aggregations']['TiposCargos']['buckets'];
        $archivoTipos = file_get_contents(PUBLICA."json/car_cargos_tipos.json");
        $arrayTipos = json_decode($archivoTipos, 1);
        $arrayTipoEncontrado = array();
        foreach ($aggsTipos as $dataTipo)
            $arrayTipoEncontrado[$dataTipo['key']] = $dataTipo['doc_count'];

        $arrayTiposMostrar = array();
        foreach ($arrayTipos as $Tipo)
        {
            if (array_key_exists($Tipo['IdTipoCargo'], $arrayTipoEncontrado))
            {
                $arrayTiposMostrar[$Tipo['IdTipoCargo']]['Nombre'] = $Tipo['Nombre'];
                $arrayTiposMostrar[$Tipo['IdTipoCargo']]['Cantidad'] = $arrayTipoEncontrado[$Tipo['IdTipoCargo']];
            }
        }
        $arrayTiposMostrar["hscatedra"]['Nombre'] = "Materias";
        $arrayTiposMostrar["hscatedra"]['Cantidad'] = 0;


        $arrayPuestos = array();
        foreach ($resultado['hits']['hits'] as $dataResult)
        {
            $dataEncontrada = $dataResult['_source'];
            if ($dataEncontrada['Estado'] == ACTIVO)
            {
                if (isset($dataEncontrada['Cargo']) && isset($dataEncontrada['Cargo']['Tipo']) && isset($dataEncontrada['Cargo']['Tipo']['Id'])
                    && array_key_exists($dataEncontrada['Cargo']['Tipo']['Id'], $arrayTiposMostrar))
                {
                    $arrayPuestos[$dataEncontrada['Cargo']['Tipo']['Id']][] = FuncionesPHPLocal::DecodificarUtf8($dataEncontrada);
                }
                else
                    $arrayPuestos["hscatedra"][] = FuncionesPHPLocal::DecodificarUtf8($dataEncontrada);
            }
        }

        $arrayPuestosPersonas = array();
        $aggsPuestosPersonas = $resultado['aggregations']['PuestosPersonas']['buckets'];

        foreach ($aggsPuestosPersonas as $PuestosPersonasDatos)
        {
            if (isset($PuestosPersonasDatos['Personas']['Filtro']['Nombres']['hits']['hits'])) {
                $arrayRecorrer = $PuestosPersonasDatos['Personas']['Filtro']['Nombres']['hits']['hits'];
            } else {
                $arrayRecorrer = $PuestosPersonasDatos['Personas']['Nombres']['hits']['hits'];
            }
            $arrayPuestosPersonas[$PuestosPersonasDatos['key']] = array();
            foreach ($arrayRecorrer as $Personas)
            {
                $arrayPuestosPersonas[$PuestosPersonasDatos['key']][] = $Personas['_source'];
            }
        }


        $arrayPuestosDesempenos = array();
        $aggsPuestosDesempenos = $resultado['aggregations']['Desempenos']['buckets'];
        foreach ($aggsPuestosDesempenos as $PuestosDesempenos)
        {
            $arrayRecorrer = $PuestosDesempenos['Desempeno']['Dias']['hits']['hits'];
            $arrayPuestosDesempenos[$PuestosDesempenos['key']] = array();
            foreach ($arrayRecorrer as $Personas)
            {
                $arrayPuestosDesempenos[$PuestosDesempenos['key']][] = $Personas['_source'];
            }
        }

        $mpdfConfig = array(
            'mode' => 'utf-8',
            'format' => 'A4',    // format - A4, for example, default ''
            'default_font_size' => 0,     // font size - default 0
            'default_font' => 'open-sans',    // default font family
            'margin_left' => 15,    	// 15 margin_left
            'margin_right' => 15,    	// 15 margin right
            'margin_top' => 35,     // 9 margin header
            'margin_bottom' => 25,     // 9 margin footer
            'margin_header' => 5,     // 9 margin header
            'margin_footer' => 5,     // 9 margin footer
            'orientation' => 'L'  	// L - landscape, P - portrait
        );
        $mpdf = new \Mpdf\Mpdf($mpdfConfig);
        $mpdf->SetWatermarkText( PROVINCIA_NOMBRE, 0.1);
        $mpdf->showWatermarkText = true;
        $html = '';
        $mpdf->DefHTMLHeaderByName(
            'html_firstpage','
            <div>
                
            </div>');
        $mpdf->DefHTMLHeaderByName(
            'Chapter2Header','
            <div style="text-align: right; font-weight: bold; border-bottom: 1px solid #CCC;">
                <table border="0" style="width: 100%;">
                    <tr>
                        <td><img style="text-align: right;" src="/assets/provincia/'.PROVINCIA.'/images/logo.png" /></td>
                        <td  style="text-align: right;" align="right">Declaraci&oacute;n jurada | '.PLANTA_ANALITICA.'</td>
                    </tr>
                </table>
            </div>');

        $html .= '<style>
                    @page {size: auto;odd-header-name: html_Chapter2Header;}                  
                    @page :first {header: html_firstpage;}                    
                    table, h3 { font-family: Lato, sans-serif; }
                    table { font-size: 14px; border-collapse: collapse; }
                    .dataTable td { border-top: thin solid; border-bottom: thin solid; }
                    .dataTable td:first-child { border-left: thin solid; }
                    .dataTable td:last-child { border-right: thin solid; }
                    .row-title { background: #7280B2; }
                    .row-title td { background: #7280B2; padding: 5px 0; font-size: 16px; }
                    .title { color: #FFFFFF; font-weight: 500; }
                    .fondoTitulo h1{color: #7280B2; }
                </style>';

        $html .= '<div style="text-align: center;padding-top: 30px; ">';
        $html .= '<img style="text-align: center;" src="/assets/provincia/'.PROVINCIA.'/images/logo.png" />';
        $html .= '</div>';
        $html .= '<div class="fondoTitulo" style="padding-top: 5px; padding-bottom: 30px;">';
        $html .= '<div style="text-align: center"><h1>'.utf8_encode($resultadoEscuela['Nombre']).' N&deg; '.$resultadoEscuela['CodigoEscuela'].' | #'.$resultadoEscuela['ClaveUnicaEscuela'].'</h1></div>';
        $html .= '<div style="margin-top: 10px; text-align: center"><h2>'.PLANTA_ANALITICA.'</h2></div>';
        $html .= '</div>';

        $html .= '<div style=" text-align: right;">';
        $html .= '<div style="margin-top: 30px;"><h3>'.utf8_encode($resultadoEscuela['NombreRegion']).' | '.utf8_encode($resultadoEscuela['Localidad']).'</h3></div>';
        $html .= '<div><strong>Direcci&oacute;n: </strong>'.utf8_encode($resultadoEscuela['Direccion']).'</div>';
        $html .= '<div><strong>Localidad: </strong>'.utf8_encode($resultadoEscuela['Localidad']).'</div>';
        $html .= '<div><strong>'.utf8_encode("&Aacute;mbito").': </strong>'.utf8_encode($resultadoEscuela['NombreAmbito']).'</div>';
        $html .= '<div><strong>Anexo: </strong>'.($resultadoEscuela['EsAnexo']?"SI":"NO").'</div>';
        $html .= '</div>';

        $html .= '<pagebreak></pagebreak>';


        $html .= '<div style="text-align: left;">';
        $html .= utf8_encode($filaTipo['TextoExplicativo']);
        $html .= '</div>';

        $html .= '<pagebreak></pagebreak>';


        //Firmas Chubut
        if (PROVINCIA == "AR-U") //Si la provincia es Chubut
        {
            $html .= '<div style="text-align: left;">';
            $html .= '<h3>DIRECTOR</h3>';
            $html .= 'Declaro bajo juramento que todos los datos consignados son veraces y exactos, ';
            $html .= 'de acuerdo a mi leal saber y entender. Asimismo, me notifico que cualquier falsedad, ';
            $html .= 'ocultamiento u omisi&oacute;n dar&aacute; motivo a las m&aacute;s severas sanciones disciplinarias, ';
            $html .= 'como as&iacute; tambi&eacute;n que estoy obligado a denunciar dentro de las cuarenta y ocho horas las modificaciones ';
            $html .= 'que se produzcan en el futuro.';
            $html .= '</div>';
            $html .= '<div style="clear: both; margin-top: 45px;">';
            $html .= '<div style="float:left; width: 50%;"><div style="text-align: center;">Lugar y Fecha</div></div>';
            $html .= '<div style="float:right; width: 50%;"><div style="text-align: center;">Firma del Declarante</div></div> ';
            $html .= '</div>';

            $html .= '<div style="text-align: left; margin-top: 30px;">';
            $html .= '<h3>SUPERVISOR</h3>';
            $html .= 'Certifico la exactitud de los datos consignados en el presente formulario y la ';
            $html .= 'autenticidad de la firma que antecede. Manifiesto que no tengo conocimiento que en la ';
            $html .= 'presente el declarante haya incurrido en ninguna falsedad, ocultamiento u omisi&oacute;n.';
            $html .= '</div>';

            $html .= '<div style="clear: both; margin-top: 45px;">';
            $html .= '<div style="float:left; width: 50%;"><div style="text-align: center;">Lugar y Fecha</div></div>';
            $html .= '<div style="float:right; width: 50%;"><div style="text-align: center;">Firma del Declarante</div></div> ';
            $html .= '</div>';

            $html .= '<div style="text-align: left; margin-top: 30px;">';
            $html .= '<h3>DIRECCION DE RECURSOS HUMANOS</h3>';
            $html .= 'Certifico la exactitud de los datos consignados en el presente formulario y la ';
            $html .= 'autenticidad de la firma que antecede. Dejo constancia que en el presente formulario ';
            $html .= 'no se observa ninguna transgresi&oacute;n a la Ley VIII N&deg; 69.';
            $html .= '</div>';

            $html .= '<div style="clear: both; margin-top: 45px;">';
            $html .= '<div style="float:left; width: 50%;"><div style="text-align: center;">Lugar y Fecha</div></div>';
            $html .= '<div style="float:right; width: 50%;"><div style="text-align: center;">Firma del Declarante</div></div> ';
            $html .= '</div>';
        }

        //Firmas Misiones
        if (PROVINCIA == "AR-N") //Si la provincia es Misiones
        {
            $html .= '<div style="text-align: left;">';
            $html .= '<h3>DIRECTOR</h3>';
            $html .= 'Declaro bajo juramento que todos los datos consignados son veraces y exactos, ';
            $html .= 'de acuerdo a mi leal saber y entender. Asimismo, me notifico que cualquier falsedad, ';
            $html .= 'ocultamiento u omisi&oacute;n dar&aacute; motivo a las m&aacute;s severas sanciones disciplinarias, ';
            $html .= 'como as&iacute; tambi&eacute;n que estoy obligado a denunciar dentro de las cuarenta y ocho horas las modificaciones ';
            $html .= 'que se produzcan en el futuro.';
            $html .= '</div>';
            $html .= '<div style="clear: both; margin-top: 45px;">';
            $html .= '<div style="float:left; width: 50%;"><div style="text-align: center;">Lugar y Fecha</div></div>';
            $html .= '<div style="float:right; width: 50%;"><div style="text-align: center;">Firma del Declarante</div></div> ';
            $html .= '</div>';

            $html .= '<div style="text-align: left; margin-top: 30px;">';
            $html .= '<h3>SUPERVISOR</h3>';
            $html .= 'Certifico la exactitud de los datos consignados en el presente formulario y la ';
            $html .= 'autenticidad de la firma que antecede. Manifiesto que no tengo conocimiento que en la ';
            $html .= 'presente el declarante haya incurrido en ninguna falsedad, ocultamiento u omisi&oacute;n.';
            $html .= '</div>';

            $html .= '<div style="clear: both; margin-top: 45px;">';
            $html .= '<div style="float:left; width: 50%;"><div style="text-align: center;">Lugar y Fecha</div></div>';
            $html .= '<div style="float:right; width: 50%;"><div style="text-align: center;">Firma del Declarante</div></div> ';
            $html .= '</div>';

            $html .= '<div style="text-align: left; margin-top: 30px;">';
            $html .= '<h3>DIRECCION DE NIVEL</h3>';
            $html .= 'Certifico la exactitud de los datos consignados en el presente formulario y la ';
            $html .= 'autenticidad de la firma que antecede. Manifiesto que no tengo conocimiento que en la ';
            $html .= 'presente el declarante haya incurrido en ninguna falsedad, ocultamiento u omisi&oacute;n.';
            $html .= '</div>';

            $html .= '<div style="clear: both; margin-top: 45px;">';
            $html .= '<div style="float:left; width: 50%;"><div style="text-align: center;">Lugar y Fecha</div></div>';
            $html .= '<div style="float:right; width: 50%;"><div style="text-align: center;">Firma del Declarante</div></div> ';
            $html .= '</div>';

            $html .= '<div style="text-align: left; margin-top: 30px;">';
            $html .= '<h3>DIRECCION DE PERSONAL</h3>';
            $html .= 'Certifico la exactitud de los datos consignados en el presente formulario y la ';
            $html .= 'autenticidad de la firma que antecede. Manifiesto que no tengo conocimiento que en la ';
            $html .= 'presente el declarante haya incurrido en ninguna falsedad, ocultamiento u omisi&oacute;n.';
            $html .= '</div>';

            $html .= '<div style="clear: both; margin-top: 45px;">';
            $html .= '<div style="float:left; width: 50%;"><div style="text-align: center;">Lugar y Fecha</div></div>';
            $html .= '<div style="float:right; width: 50%;"><div style="text-align: center;">Firma del Declarante</div></div> ';
            $html .= '</div>';
        }

        $html .= '<pagebreak></pagebreak>';
       foreach ($arrayTiposMostrar as $key => $Tipo) {

            $html .= '<h2>'.$Tipo['Nombre'].'</h2>';

            if (array_key_exists($key, $arrayPuestos)) {
                $html .= '<table class="dataTable" width="100%" cellspacing="0">';

                $html .= '<tr class="row-title">
                    <td class="title" style="width: 10%;" align="center"><small>CUPOF</small></td>
                    <td class="title" style="width: 30%;" align="center"><small>Cargo/Materia</small></td>
                    <td class="title" style="width: 5%;" align="center"><small>Turno</small></td>
                    <td class="title" style="width: 5%;" align="center"><small>'.utf8_encode("A&ntilde;o").'</small></td>
                    <td class="title" style="width: 5%;" align="center"><small>'.utf8_encode("Secci&oacute;n").'</small></td>
                    <td class="title" style="width: 30%;" align="center"><small>Persona</small></td>
                    <td class="title" style="width: 25%;" align="center"><small>Desempe&ntilde;o</small></td>
                  </tr>';

                foreach ($arrayPuestos[$key] as $dataPuesto) {

                    $cupof = (isset($dataPuesto['CodigoPuesto']) ? $dataPuesto['CodigoPuesto'] : $dataPuesto['Codigo']);

                    $html .= '<tr>';
                    $html .= "<td align='center'>#".$cupof."</td>";

                    if (isset($dataPuesto['Cargo'])) {
                        $CargoMateria = utf8_encode($dataPuesto['Cargo']['Descripcion']);
                    } else if (isset($dataPuesto['Materia'])) {
                        $CargoMateria = utf8_encode($dataPuesto['Materia']['Descripcion']);
                    }

                    $html .= "<td>&nbsp;".ucfirst(strtolower($CargoMateria))."</td>";

                    if (isset($dataPuesto['Turno']))
                        $html .= "<td align='center'>&nbsp;".utf8_encode($dataPuesto['Turno']['NombreCorto'])."</td>";
                    else
                        $html .= "<td align='center'>&nbsp;</td>";
                    if (isset($dataPuesto['GradoAnio']))
                        $html .= "<td align='center'>".utf8_encode($dataPuesto['GradoAnio']['NombreCorto'])."</td>";
                    else
                        $html .= "<td align='center'>&nbsp;</td>";

                    if (isset($dataPuesto['SeccionDivision']))
                        $html .= "<td align='center'>".utf8_encode($dataPuesto['SeccionDivision']['Descripcion'])."</td>";
                    else
                        $html .= "<td align='center'>&nbsp;</td>";


                    $html .= "<td>";
                    if (array_key_exists($dataPuesto['Id'], $arrayPuestosPersonas)) {

                        $encontroPersona = false;

                        foreach ($arrayPuestosPersonas[$dataPuesto['Id']] as $Persona) {

                            /** @var array $Persona */

                            if (isset($Persona['Nombre'])) {
                                $NombreCompleto = $Persona['Nombre'];
                                if (isset($Persona['Apellido'])) {
                                    $NombreCompleto = $Persona['Nombre']." ".$Persona['Apellido'];
                                }
                                $encontroPersona = true;
                            }

                            if (isset($Persona['EstadoPersona'])) {

                                switch ($Persona['EstadoPersona']['Id']) {
                                    case 1:
                                        $style = 'style="color: #1D9D1D;"';
                                        break;
                                    case 2:
                                        $style = 'style="color: #FF6E19E0;"';
                                        break;
                                }
                                $EstadoPersona = $Persona['EstadoPersona'];
                            }

                            //$html .= "&nbsp;".ucwords(strtolower($NombreCompleto))." <small ".$style.">(".$Persona['Descripcion']['Descripcion'].")</small><br>";
                            $html .= "&nbsp;".ucwords(strtolower($NombreCompleto))." <small><strong>(".$Persona['Revista']['Descripcion'].")</strong></small><br>";
                        }

                        if (!$encontroPersona) {
                            $html .= '&nbsp;N/A';
                        }
                    }
                    $html .= "</td>";

                    $html .= '<td>';
                    if (array_key_exists($dataPuesto['Id'], $arrayPuestosDesempenos) && count($arrayPuestosDesempenos[$dataPuesto['Id']]) > 0) {

                        $NumeroDia = 0;
                        foreach ($arrayPuestosDesempenos[$dataPuesto['Id']] as $key => $desempeno) {

                            if ($NumeroDia != $desempeno['Dia']['Numero']) {

                                $NumeroDia = $desempeno['Dia']['Numero'];
                                if ($key > 0) {
                                    $html .= "<br>";
                                }
                                $html .= '&nbsp;<b>'.$desempeno['Dia']['Descripcion'].': </b>';
                            } else {
                                $html .= ' - ';
                            }
                            $html .= $desempeno['Horario']['gte'].' a '.$desempeno['Horario']['lte'];
                        }
                    } else {
                        $html .= '&nbsp;N/A';
                    }
                    $html .= '</td>';

                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
        }

        try {
	        $mpdf->WriteHTML($html);
        } catch (Mpdf\MpdfException $e) {
        	$this->setError(500, $e->getMessage());
        	return false;
        }
        $this->cantPages = $mpdf->page;

        $archivo = CARPETADDJJESCUELAS_FISICA."ddjj_".$this->idEscuela.".pdf";
        switch($type)
        {
            case "F":
                $mpdf->Output($archivo, Destination::FILE);
                break;
            case "S":
                $this->pdfBase64 = $mpdf->Output("", Destination::STRING_RETURN);
                break;
            default:
                $mpdf->Output(PLANTA_ANALITICA_ALIAS.".pdf","I");
                break;

        }

        return true;
    }

}

