<?php
require_once DIR_CLASES_DB . 'cInstrumentosLegales.db.php';

class cInstrumentosLegales extends cInstrumentosLegalesDB
{
    /**
     * cParentescos constructor.
     *
     * @param accesoBDLocal $conexion
     * @param               $formato
     */
    public function __construct(accesoBDLocal $conexion, $formato=FMT_ARRAY) {
        parent::__construct($conexion, $formato);
    }

    /**
     * @inheritDoc
     */
    public function __destruct()
    {
        parent::__destruct();
    }


    public function buscarListado($datos,&$resultado,&$numfilas)
    {
        $sparam = array(
            'xIdTipo'         => 0,
            'IdTipo'          => "",
            'xAnio'         => 0,
            'Anio'          => "",
            'xFecha'        => 0,
            'Fecha'         => "",
            'xNumero'       => 0,
            'Numero'        => "",
            'xLetra'        => 0,
            'Letra'         => "",
            'xIdEscuela'    => 0,
            'IdEscuela'     => "",
            'xFechaDesde'   => 0,
            'FechaDesde'    => "",
            'xFechaHasta'   => 0,
            'FechaHasta'    => "",
            'xExpediente'   => 0,
            'Expediente'    => "",
            'xEstado'       => 0,
            'Estado'        => "",
            'xUrl'          => 0,
            'Url'           => "",
            'xOrigen'       => 0,
            'Origen'        => "",
            'xDescripcion'  => 0,
            'Descripcion'   => "",
            'limit'         => "",
            'orderby'       => "",
        );

        if (isset($datos['IdTipo']) && $datos['IdTipo']!="") {
            $sparam['IdTipo']  = $datos['IdTipo'];
            $sparam['xIdTipo'] = 1;
        }
        
        if (isset($datos['Anio']) && $datos['Anio']!="") {
            $sparam['Anio']  = $datos['Anio'];
            $sparam['xAnio'] = 1;
        }

        if (isset($datos['Fecha']) && $datos['Fecha']!="") {
            $sparam['Fecha']  = $datos['Fecha'];
            $sparam['xFecha'] = 1;
        }

        if (isset($datos['Numero']) && $datos['Numero']!="") {
            $sparam['Numero']  = $datos['Numero'];
            $sparam['xNumero'] = 1;
        }

        if (isset($datos['Letra']) && $datos['Letra']!="") {
            $sparam['Letra']  = $datos['Letra'];
            $sparam['xLetra'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela']!="") {
            $sparam['IdEscuela']  = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['FechaDesde']) && $datos['FechaDesde']!="") {
            $sparam['FechaDesde']  = $datos['FechaDesde'];
            $sparam['xFechaDesde'] = 1;
        }

        if (isset($datos['FechaHasta']) && $datos['FechaHasta']!="") {
            $sparam['FechaHasta']  = $datos['FechaHasta'];
            $sparam['xFechaHasta'] = 1;
        }

        if (isset($datos['Expediente']) && $datos['Expediente']!="") {
            $sparam['Expediente']  = $datos['Expediente'];
            $sparam['xExpediente'] = 1;
        }

        if (isset($datos['Estado']) && $datos['Estado']!="") {
            $sparam['Estado']  = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }

        if (isset($datos['Url']) && $datos['Url']!="") {
            $sparam['Url']  = $datos['Url'];
            $sparam['xUrl'] = 1;
        }

        if (isset($datos['Origen']) && $datos['Origen']!="") {
            $sparam['Origen']  = $datos['Origen'];
            $sparam['xOrigen'] = 1;
        }

        if (isset($datos['Descripcion']) && $datos['Descripcion']!="") {
            $sparam['Descripcion']  = $datos['Descripcion'];
            $sparam['xDescripcion'] = 1;
        }

        if (isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit'] = $datos['limit'];

        if (isset($datos['orderby']) && $datos['orderby']!="")
            $sparam['orderby'] = $datos['orderby'];

        if (!parent::buscarListado($sparam,$resultado,$numfilas))
            return false;
        return true;
    }


    public function buscarXCodigo($datos, &$resultado, &$numfilas)
    {
	    return parent::buscarXCodigo($datos, $resultado, $numfilas);
    }


    public function listarTipos($datos, &$resultado, &$numfilas)
    {
	    return parent::listarTipos($datos, $resultado, $numfilas);
    }


    public function Insertar($datos, &$codigoinsertado)
    {
        if (!parent::Insertar($datos, $codigoinsertado))
            return false;

        $datos['IdInstrumento'] = $codigoinsertado;

        if (!$this->ValidarGuardarDocumento($datos))
			return false;

        return true;
    }


    public function Modificar($datos)
    {
        if (!parent::Modificar($datos))
            return false;

        if (!$this->ValidarGuardarDocumento2($datos))
			return false;

        return true;
    }


    public function GuardarDocumento($datos)
    {
        if (!parent::GuardarDocumento($datos))
            return false;

        return true;
    }


    public function ValidarGuardarDocumento($datos)
    {
        if ($datos['IdInstrumento'] <= 0 || empty($datos['ArchivoNombre'])) {
            return false;
        }

        $archivosDatos = [];

        $archivosDatos['ArchivoUbicacion']  = $datos['IdInstrumento']."_".$datos['ArchivoUbicacion'];
        $archivosDatos['ArchivoNombre']  = $datos['ArchivoNombre'];
        $archivosDatos['ArchivoSize']  = $datos['ArchivoSize'];
        $archivosDatos['ArchivoHash']  = $datos['ArchivoHash'];
        $archivosDatos['IdInstrumento'] = $datos['IdInstrumento'];

        if (!parent::GuardarDocumento($archivosDatos))
            return false;

        $archivosDatos['nombrearchivotmp'] = $datos['nombrearchivotmp'];

        if (!$this->InsertarArchivo($archivosDatos))
			return false;

        return true;
    }


    public function ValidarGuardarDocumento2($datos)
    {
        if ($datos['IdInstrumento'] <= 0) {
            return false;
        }

        $archivosDatos = [];

        if (!empty($datos['ArchivoNombre'])) {
            $partes = explode("_", $datos['ArchivoUbicacion']);
            $primerValor = $partes[0];

            if ($primerValor == $datos['IdInstrumento']) {
                $archivosDatos['ArchivoUbicacion'] = $datos['ArchivoUbicacion'];
            } else {
                $archivosDatos['ArchivoUbicacion'] = $datos['IdInstrumento'] . "_" . $datos['ArchivoUbicacion'];
            }

            $archivosDatos['ArchivoNombre']  = $datos['ArchivoNombre'];
            $archivosDatos['ArchivoSize']  = $datos['ArchivoSize'];
            $archivosDatos['ArchivoHash']  = $datos['ArchivoHash'];
            $archivosDatos['IdInstrumento'] = $datos['IdInstrumento'];
        } else {
            $archivosDatos['ArchivoUbicacion']  = NULL;
            $archivosDatos['ArchivoNombre']  = NULL;
            $archivosDatos['ArchivoSize']  = NULL;
            $archivosDatos['ArchivoHash']  = NULL;
            $archivosDatos['IdInstrumento'] = $datos['IdInstrumento'];

            if (!parent::GuardarDocumento($archivosDatos))
                return false;

            return true;
        }

        if (!parent::GuardarDocumento($archivosDatos))
            return false;

        if (isset($datos['nombrearchivotmp'])) {
            $archivosDatos['nombrearchivotmp'] = $datos['nombrearchivotmp'];

            if (!$this->InsertarArchivo($archivosDatos))
                return false;
        }

        return true;
    }


    public function InsertarArchivo($datos)
	{
		$nombrearchivotmp = $datos['nombrearchivotmp'];
        $nombreDestino = $datos["ArchivoUbicacion"];

		if(!is_dir(PATH_STORAGE.CARPETA_CONFIGURACION_INSTRUMENTOS_LEGALES))
					@mkdir(PATH_STORAGE.CARPETA_CONFIGURACION_INSTRUMENTOS_LEGALES);

		if(!is_writable(PATH_STORAGE.CARPETA_CONFIGURACION_INSTRUMENTOS_LEGALES))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no se ha podido subir el archivo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(!is_dir(PATH_STORAGE.CARPETA_CONFIGURACION_INSTRUMENTOS_LEGALES))
					@mkdir(PATH_STORAGE.CARPETA_CONFIGURACION_INSTRUMENTOS_LEGALES);

		$bytes = disk_total_space(DOCUMENT_ROOT);
		if($datos["ArchivoSize"] > $bytes)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no se ha podido subir el archivo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;

		}
		if (file_exists(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$nombrearchivotmp))
		{
				if(!$this->MoverArchivoTemporal($nombrearchivotmp,PATH_STORAGE.CARPETA_CONFIGURACION_INSTRUMENTOS_LEGALES,$nombreDestino))
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al mover el archivo temporal.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
		}

		return true;
	}


    public function MoverArchivoTemporal($archivoOrigen, $carpetadestino, $archivoDestino = "")
    {
        if ($archivoDestino == "")
            $archivoDestino = $archivoOrigen;

        if(!is_writable($carpetadestino))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no se a podido subir el archivo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if(!copy(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$archivoOrigen, $carpetadestino.$archivoDestino))
            return false;

        if(!unlink(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA.$archivoOrigen))
            return false;

        return true;
    }

}