<?php
include(DIR_CLASES_DB."cNoticias.db.php");

class cNoticias extends cNoticiasdb
{
	/**
	 * Constructor de la clase cNoticias.
	 *
	 * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
	 * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
	 *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
	 *            otros escribe en pantalla el mensaje en texto plano
	 *
	 * @param accesoBDLocal $conexion
	 * @param mixed         $formato
	 */
	function __construct(accesoBDLocal $conexion,$formato=FMT_TEXTO){
		parent::__construct($conexion,$formato);
	}

	/**
	 * Destructor de la clase cNoticias.
	 */
	function __destruct(){
		parent::__destruct();
	}

	/**
	 * Devuelve el mensaje de error almacenado
	 *
	 * @return array
	 */
	public function getError(): array {
		return $this->error;
	}

	public function BuscarxCodigo($datos, &$resultado,&$numfilas): bool
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
	{
		$sparam=array(
			'xIdNoticia'=> 0,
			'IdNoticia'=> "",
			'xTitulo'=> 0,
			'Titulo'=> "",
			'xCuerpo'=> 0,
			'Cuerpo'=> "",
			'xFechaDesde'=> 0,
			'FechaDesde'=> "",
			'xFechaHasta'=> 0,
			'FechaHasta'=> "",
			'xLink'=> 0,
			'Link'=> "",
			'xOrden'=> 0,
			'Orden'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "FechaDesde DESC"
		);

		if(isset($datos['IdNoticia']) && $datos['IdNoticia']!="")
		{
			$sparam['IdNoticia']= $datos['IdNoticia'];
			$sparam['xIdNoticia']= 1;
		}
		if(isset($datos['Titulo']) && $datos['Titulo']!="")
		{
			$sparam['Titulo']= utf8_decode($datos['Titulo']);
			$sparam['xTitulo']= 1;
		}
		if(isset($datos['Cuerpo']) && $datos['Cuerpo']!="")
		{
			$sparam['Cuerpo']= utf8_decode($datos['Cuerpo']);
			$sparam['xCuerpo']= 1;
		}
		if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
            $fecha_desde = DateTime::createFromFormat('d/m/Y H:i:s', $datos["FechaDesde"] . " 00:00:00");
            if ($fecha_desde !== false) {
                $sparam['FechaDesde']= $fecha_desde->format('Y-m-d H:i:s');
                $sparam['xFechaDesde']= 1;
            }
		}
		if(isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
		{
            $fecha_hasta = DateTime::createFromFormat('d/m/Y H:i:s', $datos["FechaHasta"] . " 23:59:59");
            if ($fecha_hasta !== false) {
                $sparam['FechaHasta']= $fecha_hasta->format('Y-m-d H:i:s');
                $sparam['xFechaHasta']= 1;
            }
		}
		if(isset($datos['Link']) && $datos['Link']!="")
		{
			$sparam['Link']= utf8_decode($datos['Link']);
			$sparam['xLink']= 1;
		}
		if(isset($datos['Orden']) && $datos['Orden']!="")
		{
			$sparam['Orden']= $datos['Orden'];
			$sparam['xOrden']= 1;
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

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarxIdRol($datos,&$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxIdRol($datos,$resultado,$numfilas))
            return false;
        return true;
    }

	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas): bool
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarCombo(&$resultado,&$numfilas): bool
	{
		if (!parent::BuscarCombo($resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarRolesNoticia($datos, &$resultado, &$numfilas): bool
	{
		if (!parent::BuscarRolesNoticia($datos, $resultado, $numfilas))
			return false;
		return true;
	}

	public function RolesSP(&$spnombre,&$sparam): void
	{
		parent::RolesSP($spnombre,$sparam);
	}

	public function RolesSPResult(&$resultado, &$numfilas): bool
	{
		$this->RolesSP($spnombre,$sparam);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar roles. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}

	public function Insertar($datos,&$codigoInsertado): bool
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

        $fecha_desde = DateTime::createFromFormat('d/m/Y H:i:s', $datos["FechaDesde"] . " 00:00:00");
        $fecha_hasta = DateTime::createFromFormat('d/m/Y H:i:s', $datos["FechaHasta"] . " 23:59:59");
        if ($fecha_desde === false || $fecha_hasta === false) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Formato de fecha invalido", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        // Convertir a formato MySQL datetime
        $datos["FechaDesde"] = $fecha_desde->format('Y-m-d H:i:s');
        $datos["FechaHasta"] = $fecha_hasta->format('Y-m-d H:i:s');

		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;

		if (!parent::Insertar($datos,$codigoInsertado))
			return false;

        if (isset($datos["Archivo"]) && $datos["Archivo"]!="")
        {
            $datosArchivo=array("IdNoticia"=>$codigoInsertado,"Archivo"=>$datos["Archivo"]);
            if (!$this->ActualizarArchivo($datosArchivo)) {
                return false;
            }
        }

		if (isset($datos['Roles']) && is_array($datos['Roles'])) {
			foreach ($datos['Roles'] as $idRol) {
				$datosRol = array(
						'IdNoticia' => $codigoInsertado,
						'IdRol' => $idRol,
						'Estado' => ACTIVO,
						'AltaFecha' => date("Y-m-d H:i:s"),
						'AltaUsuario' => $_SESSION['usuariocod']
					);
                if (!parent::InsertarNoticiaRol($datosRol))
						return false;

			}
		}
		/*
		$oAuditoriasNoticias = new cAuditoriasNoticias($this->conexion,$this->formato);
		$datos['IdNoticia'] = $codigoInsertado;
		$datos['Accion'] = INSERTAR;
		if(!$oAuditoriasNoticias->InsertarLog($datos,$codigoInsertadolog))
			return false;
		*/
		return true;
	}

	public function Modificar($datos): bool
	{

		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

        $fecha_desde = DateTime::createFromFormat('d/m/Y H:i:s', $datos["FechaDesde"] . " 00:00:00");
        $fecha_hasta = DateTime::createFromFormat('d/m/Y H:i:s', $datos["FechaHasta"] . " 23:59:59");
        if ($fecha_desde === false || $fecha_hasta === false) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Formato de fecha invalido", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        // Convertir a formato MySQL datetime
        $datos["FechaDesde"] = $fecha_desde->format('Y-m-d H:i:s');
        $datos["FechaHasta"] = $fecha_hasta->format('Y-m-d H:i:s');

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		$archivoAnterior = $datosRegistro['Archivo'];
		if (isset($datos['Archivo']) && $datos['Archivo'] != $archivoAnterior && !empty($archivoAnterior)) {
			$this->_EliminarArchivoDisco($archivoAnterior);
		}

		if (!parent::Modificar($datos))
			return false;

        if (isset($datos["Archivo"]) && $datos["Archivo"]!="")
        {
            $datosArchivo=array("IdNoticia"=>$datos["IdNoticia"],"Archivo"=>$datos["Archivo"]);
            if (!$this->ActualizarArchivo($datosArchivo)) {
                return false;
            }
        }

		if (isset($datos['Roles'])) {
			// Eliminar roles existentes
			if (!parent::EliminarRolesNoticia($datos))
				return false;

			// Insertar nuevos roles evitando duplicados
			if (is_array($datos['Roles'])) {
				foreach ($datos['Roles'] as $idRol) {
                    $datosRol = array(
                        'IdNoticia' => $datos["IdNoticia"],
                        'IdRol' => $idRol,
                        'Estado' => ACTIVO,
                        'AltaFecha' => date("Y-m-d H:i:s"),
                        'AltaUsuario' => $_SESSION['usuariocod']
                    );
					if (!parent::InsertarNoticiaRol($datosRol))
							return false;
				}
			}
        }

        /*
		$oAuditoriasNoticias = new cAuditoriasNoticias($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasNoticias->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
        */
		return true;
	}

	public function Eliminar($datos): bool
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		if (!empty($datosRegistro['Archivo'])) {
			$this->_EliminarArchivoDisco($datosRegistro['Archivo']);
		}
/*
		$oAuditoriasNoticias = new cAuditoriasNoticias($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasNoticias->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
*/
		$datosmodif['IdNoticia'] = $datos['IdNoticia'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}

	public function Activar(array $datos): bool
	{
		$datosmodif['IdNoticia'] = $datos['IdNoticia'];
		$datosmodif['Estado'] = ACTIVO;

        if (!$this->_ValidarEliminar($datos,$datosRegistro))
            return false;

		if (!$this->ModificarEstado($datosmodif))
			return false;

        /*
		$oAuditoriasNoticias = new cAuditoriasNoticias($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasNoticias->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
        */
		return true;
	}

	public function DesActivar(array $datos): bool
	{
		$datosmodif['IdNoticia'] = $datos['IdNoticia'];
		$datosmodif['Estado'] = NOACTIVO;

        if (!$this->_ValidarEliminar($datos,$datosRegistro))
            return false;

		if (!$this->ModificarEstado($datosmodif))
			return false;

        /*
		$oAuditoriasNoticias = new cAuditoriasNoticias($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasNoticias->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
        */
		return true;
	}

    public function ModificarOrdenCompleto($datos): bool
    {
        $datosmodif['Orden'] = 1;
        $arregloOrden = explode(",",$datos['orden']);
        foreach ($arregloOrden as $IdNoticia){
            $datosmodif['IdNoticia'] = $IdNoticia;
            if (!parent::ModificarOrden($datosmodif))
                return false;
            $datosmodif['Orden']++;
        }
        return true;
    }

//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _EliminarArchivoDisco($nombreArchivo): bool
	{
		if (empty($nombreArchivo)) {
			return true;
		}

		$rutaArchivo = DIR_ROOT . 'uploads/noticias/' . $nombreArchivo;

		if (file_exists($rutaArchivo)) {
			return unlink($rutaArchivo);
		}

		return true; // Si no existe, consideramos que ya está "eliminado"
	}

	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		return true;
	}

	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		return true;
	}

	private function _ValidarEliminar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}

	private function _SetearNull(&$datos): void
	{
		if (!isset($datos['Titulo']) || $datos['Titulo']=="")
			$datos['Titulo']="NULL";

		if (!isset($datos['Cuerpo']) || $datos['Cuerpo']=="")
			$datos['Cuerpo']="NULL";

		if (!isset($datos['FechaDesde']) || $datos['FechaDesde']=="")
			$datos['FechaDesde']="NULL";

		if (!isset($datos['FechaHasta']) || $datos['FechaHasta']=="")
			$datos['FechaHasta']="NULL";

		if (!isset($datos['Link']) || $datos['Link']=="")
			$datos['Link']="NULL";

		if (!isset($datos['Orden']) || $datos['Orden']=="")
			$datos['Orden']="NULL";

        if (!isset($datos['Archivo']) || $datos['Archivo']=="")
            $datos['Archivo']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
	}

	private function _ValidarDatosVacios($datos)
	{
		if (!isset($datos['Titulo']) || $datos['Titulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,FuncionesPHPLocal::HtmlspecialcharsSistema("Debe ingresar un título", ENT_QUOTES),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Cuerpo']) || $datos['Cuerpo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar el cuerpo de la noticia",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['FechaDesde']) || $datos['FechaDesde']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar la fecha desde",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

        if (!isset($datos['FechaHasta']) || $datos['FechaHasta']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar la fecha hasta",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

		if (!isset($datos['Roles']) || !is_array($datos['Roles']) || empty($datos['Roles']))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar al menos un rol",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    public function ActualizarArchivo($datos): bool
    {

        $datos = array(
            'IdNoticia' => $datos["IdNoticia"],
            'Archivo' => $datos["Archivo"]
        );

        if (!parent::ActualizarArchivo($datos))
            return false;

        return true;
    }
}
