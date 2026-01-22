<?php

namespace Bigtree\Logica;

require_once DIR_CLASES_DB . 'cReportes.db.php';

use accesoBDLocal;
use Bigtree\Datos\Reportes as ReportesDB;
use cSolicitudesCoberturaDesempenoDB;
use Validaciones;

class Reportes extends ReportesDB {
    use Validaciones;

    /**
     * @inheritDoc
     */
    public function __construct(accesoBDLocal $conexion, int $formato) {
        parent::__construct($conexion, $formato);
    }

    /**
     * @inheritDoc
     */
    public function __destruct() {
        parent::__destruct();
    }

    /**
     * @param object|null $datosConflicto
     * @param string|null $tipo
     *
     * @return string
     */


    public function BusquedaAvanzadaCantidad($datos,&$resultado,&$numfilas): bool
    {
        $sparam=array(
            'xDni'=> 0,
            'Dni'=> "",
            'xIdUsuario'=> 0,
            'IdUsuario'=> "",
            'xIdRol'=> 0,
            'IdRol'=> "-1",
            'xEscuela'=> 0,
            'Escuela'=> "-1",
            'xEstado'=> 0,
            'Estado'=> "",
            'xFechaRango'=> 0,
            'FechaDesde'=> "2024-01-01",
            'FechaHasta'=> "2024-01-01",
        );

        if(isset($datos['Dni']) && $datos['Dni']!="")
        {
            $sparam['Dni']= $datos['Dni'];
            $sparam['xDni']= 1;
        }
        if(isset($datos['IdUsuario']) && $datos['IdUsuario']!="")
        {
            $sparam['IdUsuario']= $datos['IdUsuario'];
            $sparam['xIdUsuario']= 1;
        }

        if(isset($datos['IdRol']) && $datos['IdRol']!="")
        {
            $sparam['IdRol']= $datos['IdRol'];
            $sparam['xIdRol']= 1;
        }

        if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
        {
            $sparam['FechaDesde']= $datos['FechaDesde'];
            $sparam['xFechaRango']= 1;
        }

        if(isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
        {
            $sparam['FechaHasta']= $datos['FechaHasta'];
            $sparam['xFechaRango']= 1;
        }

        if(isset($datos['Escuela']) && $datos['Escuela']!="")
        {
            $sparam['Escuela']= $datos['Escuela'];
            $sparam['xEscuela']= 1;
        }

        if(isset($datos['Estado']) && $datos['Estado']!="")
        {
            $sparam['Estado']= $datos['Estado'];
            $sparam['xEstado']= 1;
        }


        if (!parent::BusquedaAvanzadaCantidad($sparam,$resultado,$numfilas))
            return false;
        return true;
    }





    public function BuscarRegistroxUsuario($datos,&$resultado,&$numfilas)
    {
        $sparam=array(
            'xDni'=> 0,
            'Dni'=> "",
            'xIdUsuario'=> 0,
            'IdUsuario'=> "",
            'xIdRol'=> 0,
            'IdRol'=> "-1",
            'xEscuela'=> 0,
            'Escuela'=> "",
            'xEstado'=> 0,
            'Estado'=> "",
            'xFechaRango'=> 0,
            'FechaDesde'=> "2024-01-01",
            'FechaHasta'=> "2024-01-01",
            'limit'=> '',
            'orderby'=> "u.Id DESC"

        );

        if(isset($datos['Dni']) && $datos['Dni']!="")
        {
            $sparam['Dni']= $datos['Dni'];
            $sparam['xDni']= 1;
        }
        if(isset($datos['IdUsuario']) && $datos['IdUsuario']!="")
        {
            $sparam['IdUsuario']= $datos['IdUsuario'];
            $sparam['xIdUsuario']= 1;
        }

        if(isset($datos['IdRol']) && $datos['IdRol']!="")
        {
            $sparam['IdRol']= $datos['IdRol'];
            $sparam['xIdRol']= 1;
        }

        if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
        {
            $sparam['FechaDesde']= $datos['FechaDesde'];
            $sparam['xFechaRango']= 1;
        }

        if(isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
        {
            $sparam['FechaHasta']= $datos['FechaHasta'];
            $sparam['xFechaRango']= 1;
        }

        if(isset($datos['Escuela']) && $datos['Escuela']!="")
        {
            $sparam['Escuela']= $datos['Escuela'];
            $sparam['xEscuela']= 1;
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


        $this->_SetearFecha($sparam);


        if (!parent::BuscarRegistroxUsuario($sparam,$resultado,$numfilas))
            return false;
        return true;
    }



    public static function armarBoton(?object $datosConflicto, ?string &$tipo): string {
        $tipo = '';
        switch ($datosConflicto->TipoConflicto ?? -1) {
            case cSolicitudesCoberturaDesempenoDB::CONFLICTO_REGLAS:

                return self::armarBotonReglas($datosConflicto->Conflictos, $tipo);
            case cSolicitudesCoberturaDesempenoDB::CONFLICTO_HORARIO:

                return self::armarBotonHoras($datosConflicto->Conflictos, $tipo);
            case cSolicitudesCoberturaDesempenoDB::CONFLICTO_AMBOS:
                $tipo = 'Ambos tipos de conflictos';
                return sprintf('%s&nbsp;%s',
                    self::armarBotonReglas($datosConflicto->Conflictos->Reglas),
                    self::armarBotonHoras($datosConflicto->Conflictos->Horario)
                );
            default:
                return json_encode($datosConflicto);
        }
    }

    /**
     * @param string      $conflicto
     * @param string|null $tipo
     *
     * @return string
     */
    private static function armarBotonReglas(string $conflicto, ?string &$tipo = null): string {
        $tipo = 'Conflicto de cargos';
        return sprintf('<button data-conflictos=\'%s\' data-toggle="tooltip" data-placement="top" class="btn btn-link btnConflictoReglas" title="%s"><i class="fas fa-info-circle" aria-hidden="true"></i></button>', preg_replace(['/<button.*/', "/'/"], ['', '"'], utf8_decode($conflicto)), $tipo);
    }

    /**
     * @param object      $conflictos
     * @param string|null $tipo
     *
     * @return string
     */
    private static function armarBotonHoras(object $conflictos, ?string &$tipo = null): string {
        $tipo = 'Superposici�n horaria';
        return sprintf('<button data-conflictos=\'%s\' data-toggle="tooltip" data-placement="bottom" class="btn btn-link btnConflictoHorario" title="%s"><i class="fas fa-info-circle" aria-hidden="true"></i></button>', json_encode($conflictos), $tipo);
    }

    /**
     * @inheritDoc
     */
    public function buscarConflictosSolicitudesCobertura(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarConflictosSolicitudesCobertura($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function buscarConflictosEscuelasPuestos(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarConflictosEscuelasPuestos($datos, $resultado, $numfilas);
    }

    public function totalNovedadesxAnios(array $datos, array &$vecResultados){
       if (!parent::BuscarTotalNovedadesxAnios($datos, $resultado, $numfilas))
       {
           return false;
       }
       $vecResultados = array();
       if ($numfilas>0) {
           while($fila=$this->conexion->ObtenerSiguienteRegistro($resultado))
           {
               $vecResultados[$fila["anio"]][$fila["mes"]]=$fila["cantidad"];
           }
       }
    }

    public function totalEventosPorDia(array $datos, array &$vecResultados){
        $sparam["xAnio"]=0;
        $sparam["Anio"]="";
        $sparam["xMes"]=0;
        $sparam["Mes"]="";
        if (isset($datos["Anio"]) && $datos["Anio"]!="")
        {
            $sparam["xAnio"]=1;
            $sparam["Anio"]=$datos["Anio"];
        }

        if (isset($datos["Mes"]) && $datos["Mes"]!="")
        {
            $sparam["xMes"]=1;
            $sparam["Mes"]=$datos["Mes"];
        }

        if (!parent::BuscarTotalEventosPorDiaProcesados($sparam, $resultado, $numfilas))
        {
            return false;
        }
        $vecResultados = array();
        if ($numfilas>0) {
            while($fila=$this->conexion->ObtenerSiguienteRegistro($resultado))
            {
                $vecResultados[utf8_encode($fila["categoria"]."-".$fila["tipo"])][$fila["dia"]]=$fila["cantidad"];
                //$vecResultados[utf8_encode($fila["tipo"]])[$fila["fecha"]]["categoria"]=$fila["categoria"];
            }
        }
    }


    private function _SetearFecha(&$datos)
    {
        if(!empty($datos['FechaDesde']) && empty($datos['FechaHasta']))
        {
            $datos['FechaHasta'] = date('Y-m-d');

        }

    }


    public function asistenciaPerfecta(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::asistenciaPerfecta($datos, $resultado, $numfilas);
    }


}
