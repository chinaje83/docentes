<?php

abstract class cInstrumentosLegalesDB
{
    use ManejoErrores;
    /**
     * @var accesoBDLocal
     */
    protected $conexion;
    /**
     * @var mixed
     */
    protected $formato;

    /**
     * cParentescosDB constructor.
     *
     * @param accesoBDLocal $conexion
     * @param               $formato
     */
    public function __construct(accesoBDLocal $conexion, $formato)
    {
        $this->conexion =& $conexion;
        $this->formato = $formato;
    }

    /**
     * Destructor de la clase
     */
    public function __destruct()
    {
        $this->error = [];
    }

    /**
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */


    protected function buscarListado($datos, &$resultado, &$numfilas)
    {
        $spnombre = "sel_InstrumentosLegales_listado";

        $sparam = array(
            'ptable'        => BASEDATOS,
            'pxIdTipo'      => $datos['xIdTipo'],
            'pIdTipo'       => $datos['IdTipo'],
            'pxAnio'        => $datos['xAnio'],
            'pAnio'         => $datos['Anio'],
            'pxFecha'       => $datos['xFecha'],
            'pFecha'        => $datos['Fecha'],
            'pxNumero'      => $datos['xNumero'],
            'pNumero'       => $datos['Numero'],
            'pxLetra'       => $datos['xLetra'],
            'pLetra'        => $datos['Letra'],
            'pxIdEscuela'   => $datos['xIdEscuela'],
            'pIdEscuela'    => $datos['IdEscuela'],
            'pxFechaDesde'  => $datos['xFechaDesde'],
            'pFechaDesde'   => $datos['FechaDesde'],
            'pxFechaHasta'  => $datos['xFechaHasta'],
            'pFechaHasta'   => $datos['FechaHasta'],
            'pxExpediente'  => $datos['xExpediente'],
            'pExpediente'   => $datos['Expediente'],
            'pxEstado'      => $datos['xEstado'],
            'pEstado'       => $datos['Estado'],
            'pxUrl'         => $datos['xUrl'],
            'pUrl'          => $datos['Url'],
            'pxOrigen'      => $datos['xOrigen'],
            'pOrigen'       => $datos['Origen'],
            'pxDescripcion' => $datos['xDescripcion'],
            'pDescripcion'  => $datos['Descripcion'],
            'porderby'      => $datos['orderby'],
            'plimit'        => $datos['limit'],
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar el listado . ", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


    protected function buscarXCodigo($datos, &$resultado, &$numfilas)
    {
        $spnombre = "sel_InstrumentosLegales_xId";

        $sparam = array(
            'ptable'=> BASEDATOS,
			'pId' => $datos['Id']
        
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo . ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
    }


    protected function listarTipos($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_InstrumentosLegalesTipo_combo_Nombre";

        $sparam = array(
            'ptable'=> BASEDATOS
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


    protected function Modificar($datos)
    {
        $spnombre = "upd_InstrumentosLegales";

        $sparam = array(
            'ptable'        => BASEDATOS,
            'pIdTipo'       => $datos['IdTipo'],
            'pAnio'         => $datos['Anio'],
            'pFecha'        => $datos['Fecha'],
            'pNumero'       => $datos['Numero'],
            'pLetra'        => $datos['Letra'],
            'pIdEscuela'    => $datos['IdEscuela'],
            'pFechaDesde'   => $datos['FechaDesde'],
            'pFechaHasta'   => $datos['FechaHasta'],
            'pExpediente'   => $datos['Expediente'],
            'pEstado'       => $datos['Estado'],
            //'pUrl'          => $datos['Url'],
            //'pOrigen'       => $datos['Origen'],
            //'pAdjunto'      => $datos['Adjunto'],
            'pDescripcion'  => $datos['Descripcion'],
            'pId'           => $datos['IdInstrumento'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s")
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al actualizar la configuracion. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


    protected function Insertar($datos, &$codigoinsertado)
    {
        $spnombre = "ins_InstrumentosLegales";

        $sparam = array(
            'ptable'        => BASEDATOS,
            'pIdTipo'       => $datos['IdTipo'],
            'pAnio'         => $datos['Anio'],
            'pFecha'        => $datos['Fecha'],
            'pNumero'       => $datos['Numero'],
            'pLetra'        => $datos['Letra'],
            'pIdEscuela'    => $datos['IdEscuela'],
            'pFechaDesde'   => $datos['FechaDesde'],
            'pFechaHasta'   => $datos['FechaHasta'],
            'pExpediente'   => $datos['Expediente'],
            'pEstado'       => $datos['Estado'],
            //'pUrl'          => $datos['Url'],
            //'pOrigen'       => $datos['Origen'],
            //'pAdjunto'      => $datos['Adjunto'],
            'pDescripcion'  => $datos['Descripcion'],
            'pAltaUsuario' => $_SESSION['usuariocod'],
            'pAltaFecha' => date("Y/m/d H:i:s")
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje(
                $this->conexion,
                MSG_ERRGRAVE,
                "Error al registrar la configuracion de la constante.",
                array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__),
                array("formato" => $this->formato)
            );
            return false;
        }

        $codigoinsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }


    protected function GuardarDocumento($datos)
    {
        $spnombre = "upd_InstrumentosLegales_documento";

        $sparam = array(
            'ptable'             => BASEDATOS,
            'pArchivoUbicacion'  => $datos['ArchivoUbicacion'],
            'pArchivoNombre'     => $datos['ArchivoNombre'],
            'pArchivoSize'       => $datos['ArchivoSize'],
            'pArchivoHash'       => $datos['ArchivoHash'],
            'pId'                => $datos['IdInstrumento'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s")
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al guardar el documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


}