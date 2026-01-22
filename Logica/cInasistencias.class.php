<?php

class cInasistencias
{
    protected $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function tieneInasistencia($array, $value)
    {
        $n = $array['hits']['hits'];
        $inasistencias = [];
        $novedades = array_map(function($element) {
            return $element['_source'];
        }, $n);


        if(is_numeric($value['FechaInicio']) && is_numeric($value['FechaFin'])){
            $FechaInicio = date("d/m/Y",substr($value['FechaInicio'],0,10));
            $FechaFin = date("d/m/Y",substr($value['FechaFin'],0,10));
            $FechaInicioFormat = FuncionesPHPLocal::ConvertirFecha($FechaInicio, 'dd/mm/aaaa', 'aaaa-mm-dd');
            $FechaFinFormat = FuncionesPHPLocal::ConvertirFecha($FechaFin, 'dd/mm/aaaa', 'aaaa-mm-dd');
        } else {
            $FechaInicioFormat = FuncionesPHPLocal::ConvertirFecha($value['FechaInicio'], 'dd/mm/aaaa', 'aaaa-mm-dd');
            $FechaFinFormat = FuncionesPHPLocal::ConvertirFecha($value['FechaFin'], 'dd/mm/aaaa', 'aaaa-mm-dd');
        }

        if(is_array($novedades) && count($novedades) <= 0)
            return NULL;
        else {

            foreach ($novedades as $key => $novedad) {
                $FechaDesde = $novedad['Periodo']['FechaDesde'];
                $FechaHasta = $novedad['Periodo']['FechaHasta'];
                /*if(($FechaDesde < $FechaInicioFormat) && ($FechaDesde < $FechaFinFormat) && 
                    ($FechaHasta < $FechaInicioFormat) && ($FechaHasta < $FechaFinFormat))
                    return NULL;
                if(($FechaDesde > $FechaInicioFormat) && ($FechaDesde > $FechaFinFormat) && 
                    ($FechaHasta > $FechaInicioFormat) && ($FechaHasta > $FechaFinFormat))
                    return NULL;*/
                if((strtotime($FechaDesde) >= strtotime($FechaInicioFormat)) &&
                    (strtotime($FechaHasta) <= strtotime($FechaFinFormat)))
                    $inasistencias[] = $novedad;
                else if((strtotime($FechaDesde) <= strtotime($FechaInicioFormat)) &&
                    (strtotime($FechaHasta) >= strtotime($FechaInicioFormat)))
                    $inasistencias[] = $novedad;
                else if((strtotime($FechaDesde) <= strtotime($FechaFinFormat)) &&
                    (strtotime($FechaHasta) >= strtotime($FechaFinFormat)))
                    $inasistencias[] = $novedad;

            }
        }

        return empty($inasistencias)? NULL : $inasistencias;
    }


    public function getInasistenciasManules($oObjeto1, $licencia, $cargo)
    {
        $tipos = $this->getTipoDocumentosSinFiltrar();
        $_SESSION['BusquedaNovedadesInasistenciasMan']['AgenteCuil'] = $licencia["Cuil"];
        $_SESSION['BusquedaNovedadesInasistenciasMan']['TipoDocumento'] = array_values($tipos);
        $_SESSION['BusquedaNovedadesInasistenciasMan']['secuencia'] = $cargo["secuenciaOut"];
        $_SESSION['BusquedaNovedadesInasistenciasMan']['ClaveEscuela'] = $_SESSION['ClaveEscuela'];

        if(!$oObjeto1->BuscarInasistencias ($_SESSION['BusquedaNovedadesInasistenciasMan'], $result)){
            die();
        }
        return $result;
    }

    public function getInasistencias($oObjeto1, $licencia, $cargo)
    {
        $tipos = $this->getTipoDocumentos();
        $_SESSION['BusquedaNovedadesInasistencias']['AgenteCuil'] = $licencia["Cuil"];
        $_SESSION['BusquedaNovedadesInasistencias']['TipoDocumento'] = array_values($tipos);
        $_SESSION['BusquedaNovedadesInasistencias']['secuencia'] = $cargo["secuenciaOut"];
        $_SESSION['BusquedaNovedadesInasistencias']['ClaveEscuela'] = $_SESSION['ClaveEscuela'];
        if(isset($cargo["subsecuenciaOut"]))
            $_SESSION['BusquedaNovedadesInasistencias']['subsecuencia'] = $cargo["subsecuenciaOut"];
        if(!$oObjeto1->BuscarInasistencias ($_SESSION['BusquedaNovedadesInasistencias'], $result)){
            die();
        }
        return $result;
    }

    public function getLicenciasConSecuencia($oObjeto1, $licencia,$cargo)
    {
        $tipos = $this->getTipoDocumentos();
        $_SESSION['BusquedaNovedadesInasistencias']['AgenteCuil'] = $licencia["Cuil"];
        $_SESSION['BusquedaNovedadesInasistencias']['TipoDocumento'] = [178];
        $_SESSION['BusquedaNovedadesInasistencias']['ClaveEscuela'] = $_SESSION['ClaveEscuela'];
        $_SESSION['BusquedaNovedadesInasistencias']['secuencia'] = $cargo["secuenciaOut"];
        if(isset($cargo["subsecuenciaOut"]))
            $_SESSION['BusquedaNovedadesInasistencias']['subsecuencia'] = $cargo["subsecuenciaOut"];
        if(!$oObjeto1->BuscarInasistencias ($_SESSION['BusquedaNovedadesInasistencias'], $result)){
            die();
        }
        return $result;
    }


    public function getLicencias($oObjeto1, $licencia)
    {
        $tipos = $this->getTipoDocumentos();
        $_SESSION['BusquedaNovedadesInasistencias']['AgenteCuil'] = $licencia["Cuil"];
        $_SESSION['BusquedaNovedadesInasistencias']['TipoDocumento'] = [178];
        $_SESSION['BusquedaNovedadesInasistencias']['ClaveEscuela'] = $_SESSION['ClaveEscuela'];
        if(!$oObjeto1->BuscarInasistencias ($_SESSION['BusquedaNovedadesInasistencias'], $result)){
            die();
        }
        return $result;
    }

    public function getInasistenciasSinSecuencia($oObjeto1, $licencia)
    {
        $tipos = $this->getTipoDocumentos();
        $_SESSION['BusquedaNovedadesInasistenciasSinSec']['AgenteCuil'] = $licencia["Cuil"];
        $_SESSION['BusquedaNovedadesInasistenciasSinSec']['TipoDocumento'] = array_values($tipos);
        $_SESSION['BusquedaNovedadesInasistenciasSinSec']['ClaveEscuela'] = $_SESSION['ClaveEscuela'];

        if(!$oObjeto1->BuscarInasistencias ($_SESSION['BusquedaNovedadesInasistenciasSinSec'], $result)){
            die();
        }
        return $result;
    }

    public function getTipoDocumentos()
    {
    
        $arrayCategorias = $this->getTodosTipoDocumentos();

        $arrayFiltrado = array_filter($arrayCategorias[6]->tipos, function($var){ return $var !== 178;});

        return $arrayFiltrado;

    } 


    public function getTipoDocumentosSinFiltrar()
    {
        $arrayCategorias = $this->getTodosTipoDocumentos();

        return $arrayCategorias[6]->tipos;
    } 

    public function getTodosTipoDocumentos()
    {
        $oObjeto = new cDocumentosTipos($this->conexion);

        if(!$oObjeto->relacionTipsCategoriasSPResult($resultado_cat,$numfilas_cat))
            return false;

        $arrayCategorias  = [];
        
        if($numfilas_cat >0) 
        {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado_cat))
            {
                $categoria = (int) $fila['IdCategoria'];
                $tipoDocumento = (int) $fila['IdTipoDocumento'];
                
                if(empty($arrayCategorias[$categoria])){
                    $arrayCategorias[$categoria] = new stdClass();
                    $arrayCategorias[$categoria]->id = $categoria;
                    $arrayCategorias[$categoria]->nombre = $fila['Nombre'];
                    $arrayCategorias[$categoria]->tipos = [];
                }
                $arrayCategorias[$categoria]->tipos[] = $tipoDocumento;
            }
        }
        return $arrayCategorias;
    }
}