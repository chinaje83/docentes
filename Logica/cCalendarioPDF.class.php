<?php
use Mpdf\Output\Destination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\Style\Fill;


class cCalendarioPDF
{
    use ManejoErrores;
    var $conexion;
    var $formato;

    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO) {
        $this->conexion = &$conexion;
        $this->formato = $formato;
    }

    function __destruct() {}

    /**
     * @param array $datosBusqueda
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws Exception
     */
    # Excel
    public function generarPDF(array $datos): bool {

        $oEscuelasPuestosDesempeno = new cEscuelasPuestosDesempeno($this->conexion);
        if (!$oEscuelasPuestosDesempeno->BuscarxIdPuestoxIdSeccion($datos, $resultado,$numfilas))
            return false;

        $count = 0;
        if ($numfilas > 0) {

            $countFirst = false;
            $Comienzo = $Fin = 0;
            $Materias = [ 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => [] ];
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

                $Nombre = '';
                if ($fila['IdCargo'] != '')
                    $Nombre = utf8_encode($fila['CargoDescripcion'].'('.$fila['CargoCodigo'].')')."\n";

                if ($fila['IdMateria'] != '')
                    $Nombre .= utf8_encode($fila['MateriaNombre'].' ('.$fila['MateriaCodigo'].')');

                $HoraIni = date('H:i', strtotime($fila['HoraInicio']));
                $HoraFin = date('H:i', strtotime($fila['HoraFin']));

                $Materias[$fila['Dia']][$HoraIni][$count]['IdPuesto'] = $fila['IdPuesto'];
                $Materias[$fila['Dia']][$HoraIni][$count]['title'] = $Nombre;
                $Materias[$fila['Dia']][$HoraIni][$count]['start_hour'] = $HoraIni;
                $Materias[$fila['Dia']][$HoraIni][$count++]['end_hour'] = $HoraFin;

                if (!$countFirst) {
                    $Comienzo = date('H', strtotime($fila['HoraInicio'])) - 1;
                    $countFirst = true;
                }

                $strHoraFin = date('H', strtotime($fila['HoraFin']));

                if ($Fin < $strHoraFin) {
                    $Fin = $strHoraFin + 1;
                }
            }

//            print '<pre>'; print_r($Materias); print '<pre>'; die;

            require(DIR_LIBRERIAS.'PhpSpreadsheet/autoload.php');

            global $ExcelObject;
            $spreadsheet = new Spreadsheet();
            $ExcelObject = $spreadsheet->getActiveSheet();

            $hours = $Fin - $Comienzo; # total de horas
            $cellsPerHour = ($hours * HORA_OCUPA_ROWS) + 1; # 1 hora contiene x celdas
            $A = $ADiff = CALENDARIO_INTERVALO_EXCEL; # comienzo de horas
            $comienzo = $Comienzo; # primer hora del calendario
            $minutes = 0;
            $columnHour = 'A';
            $AllHours = [];
            $ZeroHour = [];

            # Centrar
            $ExcelObject->getStyle($columnHour)
                        ->getAlignment()
                        ->setVertical('center')
                        ->setHorizontal('center');

            $styleCellRow = [
                'borders' => [
                    'top' => [
                        'borderStyle' => Style\Border::BORDER_THIN
                    ]
                ]
            ];

            $styleCellCol = [
                'borders' => [
                    'right' => [
                        'borderStyle' => Style\Border::BORDER_THIN
                    ]
                ]
            ];

            # Recorro todas las celdas A, para agregar las horas
            for ($i = 0; $i < $cellsPerHour; $i++) {

                $hour = '';
                $hourCompare = str_pad($comienzo, 2, '0', STR_PAD_LEFT).':'.str_pad($minutes, 2, '0', STR_PAD_LEFT);
                $AllHours[$hourCompare] = $A;

                # Rango de minutos a mostrar
                if (in_array($minutes, array(0, 15, 30, 45))) {
                    $hour = $hourCompare;
                }

                # Asigno valor de horas
                $ExcelObject->setCellValue($columnHour.$A, $hour);
                # Alto de filas A
                $ExcelObject->getRowDimension($A)
                            ->setRowHeight(10);
                $ExcelObject->getDefaultRowDimension()
                            ->setRowHeight(15);

                # Border top
                if ($minutes == 0) {
                    $ZeroHour[] = $A;
                    $ExcelObject->getStyle($columnHour.$A)
                                ->applyFromArray($styleCellRow);
                }

                $lastRow = $A;

                # Incremento minutos/hora
                $A++;
                $minutes += CALENDARIO_INTERVALO_EXCEL;
                # A los 60 minutos, seteo la siguiente hora
                if ($minutes == 60) {
                    $minutes = 0;
                    $comienzo++;
                }
            }

            $styleArray = [
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'],
                    'bold' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Style\Border::BORDER_THIN
                    ]
                ]
            ];

            $Days = [ 1 => 'LUN.', 2 => 'MAR.', 3 => utf8_encode('MIÉ.'), 4 => 'JUE.', 5 => 'VIE.', 6 => utf8_encode('SÁB.') ];

            $BDiff = $ADiff - 1; # comienzo de días
            $day = $dayTemp = $startWeek = 'B';
            $maxColumnDay = $minColumnDay = 'B';
            foreach ($Materias as $keyDay => $materia) {

                foreach ($AllHours as $key => $r) {

                    if (isset($materia[$key])) {

                        foreach ($materia[$key] as $M) {

                            $start = new DateTime('0000-00-00'.$M['start_hour']);
                            $end = new DateTime('0000-00-00'.$M['end_hour']);
                            $diff = date_diff($start, $end);

                            $rows = 0;
                            if ($diff->h > 0) {
                                $rows = ($diff->h * HORA_OCUPA_ROWS) - 1;

                            }

                            if ($diff->i > 0) {
                                $rows += ($diff->i/CALENDARIO_INTERVALO_EXCEL);
                            }

                            $ExcelObject->setCellValue(($dayTemp).$r, utf8_encode($M['title']));
                            $cell = $dayTemp.$r.':'.$dayTemp.($r+$rows);
                            $ExcelObject->mergeCells($cell);
                            $ExcelObject->getStyle($cell)
                                        ->getAlignment()
                                        ->setVertical('center')
                                        ->setHorizontal('center')
                                        ->setWrapText(true);

                            $ExcelObject->getStyle($cell)
                                        ->getFill()
                                        ->setFillType(Style\Fill::FILL_SOLID);

                            $ExcelObject->getStyle($cell)
                                        ->getFill()
                                        ->getStartColor()
                                        ->setARGB('1D90AF');

                            $ExcelObject->getStyle($cell)
                                        ->applyFromArray($styleArray);

                            if ($maxColumnDay < $dayTemp) {
                                $maxColumnDay = $dayTemp;
                            }
                            $dayTemp++;
                        }
                    }
                    $dayTemp = $day;
                }

                # Asigno título del día
                $ExcelObject->setCellValue($minColumnDay.$BDiff, $Days[$keyDay]);
                $cellDay = $minColumnDay.$BDiff.':'.$maxColumnDay.$BDiff;
                $ExcelObject->mergeCells($cellDay);
                $ExcelObject->getStyle($cellDay)
                            ->getAlignment()
                            ->setVertical('center')
                            ->setHorizontal('center');
                $ExcelObject->getStyle($cellDay)
                            ->applyFromArray($styleCellRow);


                $ExcelObject->getStyle($maxColumnDay.$BDiff.':'.$maxColumnDay.$lastRow)
                            ->applyFromArray($styleCellCol);

                $lastColumn = $maxColumnDay;
                $dayTemp = $day = $minColumnDay = ++$maxColumnDay;
            }

            # Border Top
            foreach ($ZeroHour as $key => $r) {
                $ExcelObject->getStyle($columnHour.$r.':'.$lastColumn.$r)
                            ->applyFromArray($styleCellRow);
            }

            # Ancho columnas de días
            for ($i = $startWeek; $startWeek < $lastColumn; $startWeek++) {
                $ExcelObject->getColumnDimension($i)->setWidth(19);
                $ExcelObject->getDefaultColumnDimension()->setWidth (19); // Set default column width to 12
            }

            # Logo
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Provincia');
            $drawing->setDescription('Provincia');
            $drawing->setPath('assets/provincia/'.PROVINCIA.'/images/logo.png');
            $drawing->setCoordinates('A1');
            $drawing->getShadow()->setVisible(true);
            $drawing->getShadow()->setDirection(45);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            # Títulos
            $ExcelObject->setCellValue('E1', utf8_encode('Calendario Escolar | '.utf8_decode(PLANTA)));
            $ExcelObject->mergeCells('E1:'.$lastColumn.'1');
            $ExcelObject->getStyle('E1:'.$lastColumn.'1')
                        ->getAlignment()
                        ->setVertical('center')
                        ->setHorizontal('right');

            # Título Escuela
            $oEscuela = new cEscuelas($this->conexion,$this->formato);
            if (!$oEscuela->BuscarxCodigo($datos, $resultado, $numfilas))
                return false;

            $DatosEscuela = $this->conexion->ObtenerSiguienteRegistro($resultado);

            $NroEscuela = $DatosEscuela['CodigoEscuela'];

            $ExcelObject->setCellValue('E2', utf8_encode($DatosEscuela['Nombre']).' '.utf8_encode('N°').' '.$DatosEscuela['CodigoEscuela'].' | # '.$DatosEscuela['ClaveUnicaEscuela']);
            $ExcelObject->mergeCells('E2:'.$lastColumn.'2');
            $ExcelObject->getStyle('E2:'.$lastColumn.'2')
                        ->getAlignment()
                        ->setVertical('center')
                        ->setHorizontal('right');

            # Título Plan Educativo / Año
            $oEscuelasAGSecciones = new cEscuelasAGSecciones($this->conexion);
            if (!$oEscuelasAGSecciones->BuscarDatosCompletosxIdSeccion($datos,$resultado,$numfilas))
                return false;

            $DatosEscuela = $this->conexion->ObtenerSiguienteRegistro($resultado);

            $styleTitle = [
                'font' => [
                    'name' => 'Arial',
                    'bold' => true,
                ]
            ];

            $ExcelObject->setCellValue('D3', utf8_encode($DatosEscuela['NombrePlanEducativo']).' | '.ucfirst(mb_strtolower(utf8_encode($DatosEscuela['Descripcion']),'UTF-8')).' / '.utf8_encode($DatosEscuela['GradoAnio']).' / '.$DatosEscuela['NombreSeccion']);
            $ExcelObject->mergeCells('D3:'.$lastColumn.'3');
            $ExcelObject->getStyle('D3:'.$lastColumn.'3')
                        ->getAlignment()
                        ->setVertical('center')
                        ->setHorizontal('right')
                        ->applyFromArray($styleTitle);


            $Plan = $DatosEscuela['NombrePlanEducativo'];
            $Turno = $DatosEscuela['Descripcion'];
            $Grado = $DatosEscuela['GradoAnio'];
            $Division = $DatosEscuela['NombreSeccion'];

            $filename = 'Calendario Escolar - Esc. N '.$NroEscuela.' Plan '.$Plan.' - '.$Turno.' '.$Grado.' '.$Division;
            $type = $datos['type'] == 1 ? '.xlsx' : '.pdf';

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="'.$filename.$type.'"');
            header('Cache-Control: max-age=0');

            if ($datos['type'] == 1) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            } else {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
                $writer->setTempDir(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA);
                $writer->setOrientation('L');
                $writer->save('php://output');
                /*$mpdfConfig = array(
                    'mode' => 'utf-8',
                    'format' => 'A4',    // format - A4, for example, default ''
                    'default_font_size' => 0,     // font size - default 0
                    'default_font' => 'open-sans',    // default font family
                    'margin_left' => 15,    	// 15 margin_left
                    'margin_right' => 15,    	// 15 margin right
                    'margin_top' => 30,     // 9 margin header
                    'margin_bottom' => 25,     // 9 margin footer
                    'margin_header' => 5,     // 9 margin header
                    'margin_footer' => 5,     // 9 margin footer
                    'orientation' => 'L'  	// L - landscape, P - portrait
                );

                $mpdf = new \Mpdf\Mpdf($mpdfConfig);
                $mpdf->SetWatermarkText( PROVINCIA_NOMBRE, 0.1);*/
            }
        }

        return true;
    }
}

