<?php

/**
 * Clase universal para manejo de archivos de documentación.
 * Compatible con PHP 5.4
 */
class cArchivosDocumentacion
{
    /** @var accesoBDLocal */
    protected $conexion;

    /** @var string Carpeta relativa de configuración (ej: CARPETA_CONFIGURACION_SOLICITUD_COBERTURA) */
    protected $carpetaConfiguracion;

    /** @var mixed Formato para MostrarMensaje (si tu proyecto lo usa) */
    protected $formato;

    /**
     * @param accesoBDLocal $conexion
     * @param string        $carpetaConfiguracion  Ej: CARPETA_CONFIGURACION_SOLICITUD_COBERTURA
     * @param mixed         $formato               (opcional) mismo $this->formato que usás en tus clases
     */
    public function __construct($conexion, $carpetaConfiguracion, $formato = null)
    {
        $this->conexion            = $conexion;
        $this->carpetaConfiguracion = $carpetaConfiguracion;
        $this->formato             = $formato;
    }

    /**
     * Inserta un archivo moviéndolo desde la carpeta temporal
     *
     * @param array $datos
     * @return bool
     */
    public function InsertarArchivo($datos, $mover = true)
    {
        $nombrearchivotmp = $datos['nombrearchivotmp'];
        $nombreDestino    = $datos["ArchivoUbicacion"];

        // Carpeta física de destino usando la carpeta de configuración inyectada
        $carpetaFisica = PATH_STORAGE . $this->carpetaConfiguracion;

        if (!is_dir($carpetaFisica))
            @mkdir($carpetaFisica);

        if (!is_writable($carpetaFisica))
        {
            FuncionesPHPLocal::MostrarMensaje(
                $this->conexion,
                MSG_ERRGRAVE,
                "Error, no se ha podido subir el archivo.",
                array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__),
                array("formato" => $this->formato)
            );
            return false;
        }

        if (!is_dir($carpetaFisica))
            @mkdir($carpetaFisica);

        $bytes = disk_total_space(DOCUMENT_ROOT);
        if ($datos["ArchivoSize"] > $bytes)
        {
            FuncionesPHPLocal::MostrarMensaje(
                $this->conexion,
                MSG_ERRGRAVE,
                "Error, no se ha podido subir el archivo.",
                array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__),
                array("formato" => $this->formato)
            );
            return false;
        }

        if (file_exists(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA . $nombrearchivotmp) && $mover)
        {
            if (!$this->MoverArchivoTemporal($nombrearchivotmp, $carpetaFisica, $nombreDestino))
            {
                FuncionesPHPLocal::MostrarMensaje(
                    $this->conexion,
                    MSG_ERRGRAVE,
                    "Error al mover el archivo temporal.",
                    array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__),
                    array("formato" => $this->formato)
                );
                return false;
            }
        }

        return true;
    }

    /**
     * Mueve un archivo desde la carpeta temporal a la carpeta destino
     *
     * @param string $archivoOrigen
     * @param string $carpetadestino  Carpeta física de destino (ej: PATH_STORAGE . CARPETA...)
     * @param string $archivoDestino
     * @return bool
     */
    public function MoverArchivoTemporal($archivoOrigen, $carpetadestino, $archivoDestino = "")
    {
        if ($archivoDestino == "")
            $archivoDestino = $archivoOrigen;

        if (!is_writable($carpetadestino))
        {
            FuncionesPHPLocal::MostrarMensaje(
                $this->conexion,
                MSG_ERRGRAVE,
                "Error, no se a podido subir el archivo.",
                array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__),
                array("formato" => $this->formato)
            );
            return false;
        }

        if (!copy(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA . $archivoOrigen, $carpetadestino . $archivoDestino))
            return false;

        if (!unlink(CARPETA_SERVIDOR_MULTIMEDIA_TMP_FISICA . $archivoOrigen))
            return false;

        return true;
    }

    /**
     * Devuelve la carpeta de configuración usada
     *
     * @return string
     */
    public function getCarpetaConfiguracion()
    {
        return $this->carpetaConfiguracion;
    }
}
