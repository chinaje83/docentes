<?php 
include(DIR_CLASES_DB."cDocumentosTiposModulos.db.php");

class cDocumentosTiposModulos extends cDocumentosTiposModulosdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxIdRegistroTipoDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BuscarxIdRegistroTipoDocumentoxIdDocumentoTipoModulo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroTipoDocumentoxIdDocumentoTipoModulo($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdTipoDocumentoModulo'=> 0,
			'IdTipoDocumentoModulo'=> "",
			'xIdRegistroTipoDocumento'=> 0,
			'IdRegistroTipoDocumento'=> "",
			'xIdTipoDocumento'=> 0,
			'IdTipoDocumento'=> "",
			'xIdDocumentoTipoModulo'=> 0,
			'IdDocumentoTipoModulo'=> "",
			'xTitulo'=> 0,
			'Titulo'=> "",
			'xDescripcion'=> 0,
			'Descripcion'=> "",
			'limit'=> '',
			'orderby'=> "Orden ASC"
		);

		if(isset($datos['IdTipoDocumentoModulo']) && $datos['IdTipoDocumentoModulo']!="")
		{
			$sparam['IdTipoDocumentoModulo']= $datos['IdTipoDocumentoModulo'];
			$sparam['xIdTipoDocumentoModulo']= 1;
		}
		if(isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento']!="")
		{
			$sparam['IdRegistroTipoDocumento']= $datos['IdRegistroTipoDocumento'];
			$sparam['xIdRegistroTipoDocumento']= 1;
		}
		if(isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento']!="")
		{
			$sparam['IdTipoDocumento']= $datos['IdTipoDocumento'];
			$sparam['xIdTipoDocumento']= 1;
		}
		if(isset($datos['IdDocumentoTipoModulo']) && $datos['IdDocumentoTipoModulo']!="")
		{
			$sparam['IdDocumentoTipoModulo']= $datos['IdDocumentoTipoModulo'];
			$sparam['xIdDocumentoTipoModulo']= 1;
		}
		if(isset($datos['Titulo']) && $datos['Titulo']!="")
		{
			$sparam['Titulo']= $datos['Titulo'];
			$sparam['xTitulo']= 1;
		}
		if(isset($datos['Descripcion']) && $datos['Descripcion']!="")
		{
			$sparam['Descripcion']= $datos['Descripcion'];
			$sparam['xDescripcion']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function Insertar($datos,&$codigoinsertado)
	{
//        var_dump($datos);
//        die();

		if (!$this->_ValidarInsertar($datos))
			return false;
		$this->_SetearNull($datos);
        if ($datos['IdDocumentoTipoModulo'] == 1 ){
            $datos['Orden'] = "1";
        }elseif ($datos['IdDocumentoTipoModulo'] == 11){
            $datos['Orden'] = "2";
        } else {
            $this->ObtenerProximoOrden($datos,$proxorden);
            $datos['Orden'] = $proxorden;
        }
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['AltaApp']= APP;
		$datos['UltimaModificacionApp']= APP;
		
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		$datos['IdTipoDocumentoModulo'] = $codigoinsertado;

		if(!$this->ModificarDatosJson($datos))
                return false;
		
		$oObjeto = new cDocumentosTipos($this->conexion,$this->formato);
		if(!$oObjeto->AgregarCamposJson($datos))
			return false;
		
		
		$oAuditoriasDocumentosTiposModulos = new cAuditoriasDocumentosTiposModulos($this->conexion,$this->formato);
		
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasDocumentosTiposModulos->InsertarLog($datos,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionApp']= APP;
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		$oAuditoriasDocumentosTiposModulos = new cAuditoriasDocumentosTiposModulos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposModulos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{

		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;


		$oAuditoriasDocumentosTiposModulos = new cAuditoriasDocumentosTiposModulos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosTiposModulos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		if (!parent::Eliminar($datos))
			return false;
		
		$oObjeto = new cDocumentosTipos($this->conexion,$this->formato);
		if(!$oObjeto->AgregarCamposJson($datos))
			return false;

		return true;
	}
	
	
	public function ModificarObligatorio($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionApp']= APP;
		if (!parent::ModificarObligatorio($datos))
			return false;
		
		$oObjeto = new cDocumentosTipos($this->conexion,$this->formato);
		if(!$oObjeto->AgregarCamposJson($datos))
			return false;
		
		$oAuditoriasDocumentosTiposModulos = new cAuditoriasDocumentosTiposModulos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposModulos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		

		return true;
	}
	
	
	public function ModificarTituloDescripcion($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionApp']= APP;
		$this->_SetearNull($datos);
		if (!parent::ModificarTituloDescripcion($datos))
			return false;
		
		$oObjeto = new cDocumentosTipos($this->conexion,$this->formato);
		if(!$oObjeto->AgregarCamposJson($datos))
			return false;
		
		$oAuditoriasDocumentosTiposModulos = new cAuditoriasDocumentosTiposModulos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposModulos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		

		return true;
	}


    public function ModificarDatosJson($datos)
    {

        if(!isset($datos['MiEscuela']) || $datos['MiEscuela']!=1)
            $datosjson['MiEscuela'] = false;
        else
            $datosjson['MiEscuela'] = true;

        if(!isset($datos['TodosLosEstados']) || $datos['TodosLosEstados']!=1)
            $datosjson['TodosLosEstados'] = false;
        else
            $datosjson['TodosLosEstados'] = true;

        if(!isset($datos['TodosLosNiveles']) || $datos['TodosLosNiveles']!=1){
            if(isset($datos['NivelNovedad'])){
                $datosjson['TodosLosNiveles'] = $datos['NivelNovedad'];
            }else{
                $datosjson['TodosLosNiveles'] = false;
            }
        } else{
            $datosjson['TodosLosNiveles'] = true;
        }


        if(!isset($datos['TodosLosCargosSeleccionados']) || $datos['TodosLosCargosSeleccionados']!=1)
            $datosjson['TodosLosCargosSeleccionados'] = false;
        else
            $datosjson['TodosLosCargosSeleccionados'] = true;

        if(isset($datos['ClasificacionNovedad'])){
            $datosjson['TodasLasClasificaciones'] = $datos['ClasificacionNovedad'];
        }else{
            $datosjson['TodasLasClasificaciones'] = false;
        }


        if(isset($datos['IdRevista']))
            $datosjson['IdRevista'] = $datos['IdRevista'];

        $datosEnviar['IdTipoDocumentoModulo'] = $datos['IdTipoDocumentoModulo'];
        $datosEnviar['DatosJson'] = json_encode($datosjson);

        if(!parent::ModificarDatosJson($datosEnviar))
            return false;


        return true;
    }


	public function ModificarOrdenCompleto($datos)
	{
//        var_dump($datos);
//        die();

		$datosmodif['Orden'] = 3;
		foreach ($datos['IdTipoDocumentoModulo'] as $IdTipoDocumentoModulo){
			$datosmodif['IdTipoDocumentoModulo'] = $IdTipoDocumentoModulo;
			//print_r($datosmodif);
			if (!parent::ModificarOrden($datosmodif))
				return false;
			$datosmodif['Orden']++;
		}
		
		$oObjeto = new cDocumentosTipos($this->conexion,$this->formato);
		if(!$oObjeto->AgregarCamposJson($datos))
			return false;
		
		return true;
	}



	private function ObtenerProximoOrden($datos,&$proxorden)
	{
		$proxorden = 2;
		if (!parent::BuscarUltimoOrden($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=0){
			$datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$proxorden = $proxorden + $datos['maximo'] + 1;
		}
		return true;
	}




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		
		if (!$this->BuscarxIdRegistroTipoDocumentoxIdDocumentoTipoModulo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el modulo ya fue agregado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c�digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c�digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
			$datos['IdRegistroTipoDocumento']="NULL";

		if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento']=="")
			$datos['IdTipoDocumento']="NULL";

		if (!isset($datos['IdDocumentoTipoModulo']) || $datos['IdDocumentoTipoModulo']=="")
			$datos['IdDocumentoTipoModulo']="NULL";

		if (!isset($datos['Titulo']) || $datos['Titulo']=="")
			$datos['Titulo']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

		if (!isset($datos['Visualiza']) || $datos['Visualiza']=="")
			$datos['Visualiza']="1";

		if (!isset($datos['Obligatorio']) || $datos['Obligatorio']=="")
			$datos['Obligatorio']="0";

		
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Id Registro Tipo Documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRegistroTipoDocumento'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Tipo Documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTipoDocumento'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['IdDocumentoTipoModulo']) || $datos['IdDocumentoTipoModulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Modulo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdDocumentoTipoModulo']) && $datos['IdDocumentoTipoModulo']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdDocumentoTipoModulo'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		/*if (!isset($datos['Titulo']) || $datos['Titulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un T�tulo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una Descripci�n",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		/*if (!isset($datos['Visualiza']) || $datos['Visualiza']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar  Visualiza",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Visualiza']) && $datos['Visualiza']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Visualiza'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}*/

		if (!isset($datos['Obligatorio']) || $datos['Obligatorio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Obligatorio",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Obligatorio']) && $datos['Obligatorio']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Obligatorio'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		
		return true;
	}





}
?>