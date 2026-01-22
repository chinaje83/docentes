<?php
include(DIR_CLASES_DB."cGruposOcupacional.db.php");

class cGruposOcupacional extends cGruposOcupacionaldb
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


	public function BuscarGruposActivosAgrupadosxCodigo($datos,&$resultado,&$numfilas)
	{

		$sparam=array(
			'orderby'=> "Descripcion ASC"
		);

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if (!parent::BuscarGruposActivosAgrupadosxCodigo($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdGrupoOcupacional'=> 0,
			'IdGrupoOcupacional'=> "",
			'xIdGrupoOcupacionalExterno'=> 0,
			'IdGrupoOcupacionalExterno'=> "",
			'xCodigo'=> 0,
			'Codigo'=> "",
			'xDescripcion'=> 0,
			'Descripcion'=> "",
			'xIdRevistaExterno'=> 0,
			'IdRevistaExterno'=> "",
			'xIdRegimenEstatutarioExterno'=> 0,
			'IdRegimenEstatutarioExterno'=> "",
            'xIdRegimenSalarial'=> 0,
			'IdRegimenSalarial'=> "",
            'xIdRevista'=> 0,
			'IdRevista'=> "",
			'limit'=> '',
			'orderby'=> "IdGrupoOcupacional DESC"
		);

		if(isset($datos['IdGrupoOcupacional']) && $datos['IdGrupoOcupacional']!="")
		{
			$sparam['IdGrupoOcupacional']= $datos['IdGrupoOcupacional'];
			$sparam['xIdGrupoOcupacional']= 1;
		}
		if(isset($datos['IdGrupoOcupacionalExterno']) && $datos['IdGrupoOcupacionalExterno']!="")
		{
			$sparam['IdGrupoOcupacionalExterno']= $datos['IdGrupoOcupacionalExterno'];
			$sparam['xIdGrupoOcupacionalExterno']= 1;
		}
		if(isset($datos['Codigo']) && $datos['Codigo']!="")
		{
			$sparam['Codigo']= $datos['Codigo'];
			$sparam['xCodigo']= 1;
		}
		if(isset($datos['Descripcion']) && $datos['Descripcion']!="")
		{
			$sparam['Descripcion']= $datos['Descripcion'];
			$sparam['xDescripcion']= 1;
		}
		if(isset($datos['IdRevistaExterno']) && $datos['IdRevistaExterno']!="")
		{
			$sparam['IdRevistaExterno']= $datos['IdRevistaExterno'];
			$sparam['xIdRevistaExterno']= 1;
		}
		if(isset($datos['IdRegimenEstatutarioExterno']) && $datos['IdRegimenEstatutarioExterno']!="")
		{
			$sparam['IdRegimenEstatutarioExterno']= $datos['IdRegimenEstatutarioExterno'];
			$sparam['xIdRegimenEstatutarioExterno']= 1;
		}
        if(isset($datos['IdRevista']) && $datos['IdRevista']!="")
        {
            $sparam['IdRevista']= $datos['IdRevista'];
            $sparam['xIdRevista']= 1;
        }

        if(isset($datos['IdRegimenSalarial']) && $datos['IdRegimenSalarial']!="")
        {
            $sparam['IdRegimenSalarial']= $datos['IdRegimenSalarial'];
            $sparam['xIdRegimenSalarial']= 1;
        }

		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarCombo($datos,&$resultado,&$numfilas)
    {
        $sparam = [
            'xIdRegimenSalarial' => 0,
            'IdRegimenSalarial' => ''
        ];
        if(isset($datos['IdRegimenSalarial']) && $datos['IdRegimenSalarial']!="")
        {
            $sparam['IdRegimenSalarial']= $datos['IdRegimenSalarial'];
            $sparam['xIdRegimenSalarial']= 1;
        }
        $sparam['Estado'] = 10;
        if (!parent::BuscarCombo($sparam,$resultado,$numfilas))
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
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;

		if (!parent::Insertar($datos,$codigoinsertado))
			return false;

		$oAuditoriasGruposOcupacional = new cAuditoriasGruposOcupacional($this->conexion,$this->formato);
		$datos['IdGrupoOcupacional'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasGruposOcupacional->InsertarLog($datos,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		$oAuditoriasGruposOcupacional = new cAuditoriasGruposOcupacional($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasGruposOcupacional->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasGruposOcupacional = new cAuditoriasGruposOcupacional($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasGruposOcupacional->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		$datosmodif['IdGrupoOcupacional'] = $datos['IdGrupoOcupacional'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function ModificarEstado($datos)
	{
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}



	public function Activar($datos)
	{
		$datosmodif['IdGrupoOcupacional'] = $datos['IdGrupoOcupacional'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdGrupoOcupacional'] = $datos['IdGrupoOcupacional'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un cÃ³digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un cÃ³digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdGrupoOcupacionalExterno']) || $datos['IdGrupoOcupacionalExterno']=="")
			$datos['IdGrupoOcupacionalExterno']="NULL";

		if (!isset($datos['Codigo']) || $datos['Codigo']=="")
			$datos['Codigo']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

		if (!isset($datos['IdRevistaExterno']) || $datos['IdRevistaExterno']=="")
			$datos['IdRevistaExterno']="NULL";

		if (!isset($datos['IdRegimenEstatutarioExterno']) || $datos['IdRegimenEstatutarioExterno']=="")
			$datos['IdRegimenEstatutarioExterno']="NULL";

		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdGrupoOcupacionalExterno']) || $datos['IdGrupoOcupacionalExterno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un IdGrupoOcupacionalExterno",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdGrupoOcupacionalExterno']) && $datos['IdGrupoOcupacionalExterno']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdGrupoOcupacionalExterno'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['Codigo']) || $datos['Codigo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Código",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una Descripción",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdRevistaExterno']) || $datos['IdRevistaExterno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una Revista",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdRevistaExterno']) && $datos['IdRevistaExterno']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRevistaExterno'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['IdRegimenEstatutarioExterno']) || $datos['IdRegimenEstatutarioExterno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Régimen Estatutario",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdRegimenEstatutarioExterno']) && $datos['IdRegimenEstatutarioExterno']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRegimenEstatutarioExterno'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		return true;
	}





}
?>
