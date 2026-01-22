<?php
include(DIR_CLASES_DB."cDocumentosPermisos.db.php");

class cDocumentosPermisos extends cDocumentosPermisosdb
{

	protected $conexion;
	protected $formato;
	protected $TiposDocumentosAccedo;
	protected $TiposDocumentosVisualizoSoloArea;
	protected $TiposDocumentosVisualizoTodos;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		$this->TiposDocumentosAccedo = array();
		$this->TiposDocumentosVisualizoSoloArea = array();
		$this->TiposDocumentosVisualizoTodos = array();
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}



	public function getTiposDocumentosAccedo(){return $this->TiposDocumentosAccedo;}
	public function getTiposDocumentosVisualizoSoloArea(){return $this->TiposDocumentosVisualizoSoloArea;}
	public function getTiposDocumentosVisualizoTodos(){return $this->TiposDocumentosVisualizoTodos;}

	public function PuedoAccederTipoDocumento($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdArea'=> $datos['IdArea'],
			'IdRol'=> $roles,
			'Vigencia'=> date("Ymd")
		);
		if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}




	public function PuedeAgregarDocumento($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdArea'=> $datos['IdArea'],
			'IdRol'=> $roles,
			'IdAccion'=> "000001",
			'Vigencia'=> date("Ymd")
		);
		if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxAccion($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function PuedeAgregarComentario($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdDocumento'=> $datos['IdDocumento'],
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdArea'=> $datos['IdArea'],
			'IdRol'=> $roles,
			'xIdAccion'=> 1,
			'IdAccion'=> "000007",
			'Vigencia'=> date("Ymd")
		);

		if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdDocumentoxAccion($datos,$resultado,$numfilas))
			return false;
		return true;
	}

    public function buscarAccionesNodoInicial($datos, &$resultado, &$numfilas): bool {

        $datos = [
            'IdTipoDocumento'=> $datos['IdTipoDocumento'],
            'IdRol'=> implode(",",$_SESSION['rolcod']),
            'Vigencia'=> date("Ymd")
        ];

        if (!parent::buscarAccionesNodoInicial($datos,$resultado,$numfilas))
            return false;
        return true;
    }


    public function BuscarPermisosxDocumento($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdDocumento'=> $datos['IdDocumento'],
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdArea'=> $datos['IdArea'],
			'IdRol'=> $roles,
			'xIdAccion'=> 0,
			'IdAccion'=> "",
			'Vigencia'=> date("Ymd")
		);

		if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdDocumentoxAccion($datos,$resultado,$numfilas))
			return false;


		return true;
	}

	public function BuscarPermisosxSolicitudCobertura($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdSolicitudCobertura'=> $datos['IdSolicitudCobertura'],
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdArea'=> $datos['IdArea'],
			'IdRol'=> $roles,
			'xIdAccion'=> 0,
			'IdAccion'=> "-1",
			'Vigencia'=> date("Ymd")
		);

		if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdSolicitudCoberturaxAccion($datos,$resultado,$numfilas))
			return false;


		return true;
	}

    public function BuscarPermisosxMad($datos,&$resultado,&$numfilas)
    {
        $roles = implode(",",$_SESSION['rolcod']);
        $datos = [
            'Id'=> $datos['Id'],
            'IdTipoDocumento'=> $datos['IdTipoDocumento'],
            'IdArea'=> $datos['IdArea'],
            'IdRol'=> $roles,
            'xIdAccion'=> 0,
            'IdAccion'=> "-1",
            'Vigencia'=> date("Ymd")
        ];

        if (!parent::BuscarPermisosxMad($datos,$resultado,$numfilas))
            return false;


        return true;
    }


	public function PuedeModificarDocumento($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdDocumento'=> $datos['IdDocumento'],
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdArea'=> $datos['IdArea'],
			'IdRol'=> $roles,
			'xIdAccion'=> 1,
			'IdAccion'=> '000002,000025',
			'Vigencia'=> date("Ymd")
		);

		if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdDocumentoxAccion($datos,$resultado,$numfilas))
			return false;


		return true;
	}


	public function PuedeModificarCargos($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdDocumento'=> $datos['IdDocumento'],
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdArea'=> $datos['IdArea'],
			'IdRol'=> $roles,
			'xIdAccion'=> 1,
			'IdAccion'=> "000008",
			'Vigencia'=> date("Ymd")
		);

		if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdDocumentoxAccion($datos,$resultado,$numfilas))
			return false;


		return true;
	}

    public function PuedeModificarSecuenciaSubSecuencia($datos,&$resultado,&$numfilas)
    {
        $roles = implode(",",$_SESSION['rolcod']);
        $datos=array(
            'IdDocumento'=> $datos['IdDocumento'],
            'IdTipoDocumento'=> $datos['IdTipoDocumento'],
            'IdArea'=> $datos['IdArea'],
            'IdRol'=> $roles,
            'xIdAccion'=> 1,
            'IdAccion'=> "000009",
            'Vigencia'=> date("Ymd")
        );

        if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdDocumentoxAccion($datos,$resultado,$numfilas))
            return false;


        return true;
    }


	public function PuedeModificarComentario($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdDocumento'=> $datos['IdDocumento'],
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdArea'=> $datos['IdArea'],
			'IdRol'=> $roles,
			'xIdAccion'=> 1,
			'IdAccion'=> "000007",
			'Vigencia'=> date("Ymd")
		);

		if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdDocumentoxAccion($datos,$resultado,$numfilas))
			return false;


		return true;
	}


	public function PuedeEliminarDocumento($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdDocumento'=> $datos['IdDocumento'],
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdArea'=> $datos['IdArea'],
			'IdRol'=> $roles,
			'xIdAccion'=> 1,
			'IdAccion'=> "000003",
			'Vigencia'=> date("Ymd")
		);
		if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdDocumentoxAccion($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function PuedeAgregarAdjuntoDocumento($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdDocumento'=> $datos['IdDocumento'],
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdArea'=> $datos['IdArea'],
			'IdRol'=> $roles,
			'xIdAccion'=> 1,
			'IdAccion'=> "000006",
			'Vigencia'=> date("Ymd")
		);
		if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdDocumentoxAccion($datos,$resultado,$numfilas))
			return false;
		return true;
	}


    public function PuedeCargarClaveEscuelaDestino($datos,&$resultado,&$numfilas)
    {
        $roles = implode(",",$_SESSION['rolcod']);
        $datos=array(
            'IdDocumento'=> $datos['IdDocumento'],
            'IdTipoDocumento'=> $datos['IdTipoDocumento'],
            'IdArea'=> $datos['IdArea'],
            'IdRol'=> $roles,
            'xIdAccion'=> 1,
            'IdAccion'=> "000011",
            'Vigencia'=> date("Ymd")
        );
        if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdDocumentoxAccion($datos,$resultado,$numfilas))
            return false;
        return true;
    }


	public function BuscarAccionesDocumentoObligatorios($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdArea'=> $datos['IdArea'],
			'IdRol'=> $roles,
			'Vigencia'=> date("Ymd")
		);
		if (!parent::BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxAccionObligatorio($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BuscarTiposDocumentosParaDarAlta($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdArea'=> $_SESSION['IdArea'],
			'IdRol'=> $roles,
			'Vigencia'=> date("Ymd")
		);
		if (!parent::BuscarTiposDocumentosParaDarAlta($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarWorkflowxIdDocumentoxRol($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdArea'=> $_SESSION['IdArea'],
			'IdDocumento'=> $datos['IdDocumento'],
			'IdRol'=> $roles,
			'Vigencia'=> date("Ymd")
		);
		if (!parent::BuscarWorkflowxIdDocumentoxRol($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarAreasEnvioxIdWorkflowIdDocumentoxRol($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdArea'=> $_SESSION['IdArea'],
			'IdDocumento'=> $datos['IdDocumento'],
			'IdWorkflow'=> $datos['IdWorkflow'],
			'IdRol'=> $roles,
			'Vigencia'=> date("Ymd")
		);
		if (!parent::BuscarAreasEnvioxIdWorkflowIdDocumentoxRol($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarAreasEnvioxIdWorkflowIdSolicitudCoberturaxRol($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdSolicitudCobertura'=> $datos['IdSolicitudCobertura'],
			'IdWorkflow'=> $datos['IdWorkflow'],
			'IdRol'=> $roles,
			'Vigencia'=> date("Ymd")
		);
		if (!parent::BuscarAreasEnvioxIdWorkflowIdSolicitudCoberturaxRol($datos,$resultado,$numfilas))
			return false;
		return true;
	}

    public function BuscarAreasEnvioxIdWorkflowMadxRol($datos,&$resultado,&$numfilas)
    {
        $roles = implode(",",$_SESSION['rolcod']);
        $datos=array(
            'IdMad'=> $datos['IdMad'],
            'IdWorkflow'=> $datos['IdWorkflow'],
            'IdRol'=> $roles,
            'Vigencia'=> date("Ymd")
        );
        if (!parent::BuscarAreasEnvioxIdWorkflowMadxRol($datos,$resultado,$numfilas))
            return false;
        return true;
    }



    public function BuscarAccionesxIdWorkflowxRolxObligatorio($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdWorkflow'=> $datos['IdWorkflow'],
			'IdRol'=> $roles
		);
		if (!parent::BuscarAccionesxIdWorkflowxRolxObligatorio($datos,$resultado,$numfilas))
			return false;
		return true;
	}




	public function BuscarTiposDocumentosAccesoxIdDocumento($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdArea'=> $_SESSION['IdArea'],
			'IdDocumento'=> $datos['IdDocumento'],
			'IdRol'=> $roles,
			'Vigencia'=> date("Ymd")
		);
		if (!parent::BuscarTiposDocumentosAccesoxIdDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarTiposDocumentosAccesoxTipoDocumento($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdArea'=> $_SESSION['IdArea'],
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdRol'=> $roles,
			'Vigencia'=> date("Ymd")
		);
		if (!parent::BuscarTiposDocumentosAccesoxTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function CargarTiposDocumentosAcceso($datos,$tipo="documento")
	{
		$numfilas=0;
		if ($tipo=="documento")
		{
			if(!$this->BuscarTiposDocumentosAccesoxIdDocumento($datos,$resultado,$numfilas))
				return false;
		}else
		{
			if(!$this->BuscarTiposDocumentosAccesoxTipoDocumento($datos,$resultado,$numfilas))
				return false;
		}

		if ($numfilas>0)
		{
			while($fila=$this->conexion->ObtenerSiguienteRegistro($resultado))
			{
				if ($fila['DocumentoVisualizaTodasLasAreas']==1)
					$this->TiposDocumentosVisualizoTodos[$fila['IdTipoDocumento']] = $fila;
				else
					$this->TiposDocumentosVisualizoSoloArea[$fila['IdTipoDocumento']] = $fila;

				$this->TiposDocumentosAccedo[$fila['IdTipoDocumento']] = $fila;
			}
		}
		return true;

	}


	public function BuscarAccionesxIdWorkflowxRol($datos,&$resultado,&$numfilas)
	{
		$roles = implode(",",$_SESSION['rolcod']);
		$datos=array(
			'IdWorkflow'=> $datos['IdWorkflow'],
			'IdRol'=> $roles
		);
		if (!parent::BuscarAccionesxIdWorkflowxRol($datos,$resultado,$numfilas))
			return false;
		return true;
	}


    public function BuscarMetodosAcciones($datos, &$resultado, &$numfilas) {

        $datos = [
            'IdWorkflow'=> $datos['IdWorkflow'],
            'IdRol'=> implode(",",$_SESSION['rolcod'])
        ];

        return parent::BuscarMetodosAcciones($datos,$resultado,$numfilas);
    }



	public function BuscarTiposDocumentosaDarDeAlta($datos,&$resultado,&$numfilas)
	{

		$roles = implode(",",$_SESSION['rolcod']);
		$sparam=array(
			'IdArea'=> $_SESSION['IdArea'],
			'IdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
			'IdRol'=> $roles,
			'Nombre' => "",
			'xNombre' => 0,
			'NombreCorto' => "",
			'xNombreCorto' => 0,
			'IdCategoria'=>"",
			'xIdCategoria'=>0,
			'Vigencia'=> date("Ymd"),
			'Limit'=> ""
		);
		if (isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre'] = $datos['Nombre'];
			$sparam['xNombre'] = 1;
		}
		if (isset($datos['IdCategoria']) && $datos['IdCategoria']!="")
		{
			$sparam['IdCategoria'] = $datos['IdCategoria'];
			$sparam['xIdCategoria'] = 1;
		}

		if (isset($datos['NombreCorto']) && $datos['NombreCorto']!="")
		{
			$sparam['NombreCorto'] = $datos['NombreCorto'];
			$sparam['xNombreCorto'] = 1;
		}
		if (isset($datos['Limit']) && $datos['Limit']!="")
			$sparam['Limit'] = $datos['Limit'];
		if (!parent::BuscarDocumentosAltaxIdTipoDocumentoxIdRol($sparam,$resultado,$numfilas))
			return false;

		return true;
	}



	public function BuscarTiposDocumentosaDarDeAltaAvanzada($datos,&$resultado,&$numfilas)
	{

		$roles = implode(",",$_SESSION['rolcod']);
		$sparam=array(
			'IdArea'=> $_SESSION['IdArea'],
			'IdRol'=> $roles,
            'IdNivel' => $datos['IdNivel'],
            'xIdNivel' => $datos['IdNivel'],
			'Nombre' => "",
			'xNombre' => 0,
			'NombreCorto' => "",
			'xNombreCorto' => 0,
			'IdTipoDocumentoPadre'=> "",
			'xIdTipoDocumentoPadre'=> "",
			'IdCategoria'=>"",
			'xIdCategoria'=>0,
			'IdClasificacion'=>"",
			'xIdClasificacion'=>0,
			'Vigencia'=> date("Ymd"),
			'MostrarAlta'=> "",
			'xMostrarAlta'=> 0,
            'MostrarAltaPON' => '',
            'xMostrarAltaPON' => 0,
			'Limit'=> ""
		);
		if (isset($datos['IdTipoDocumentoPadre']) && $datos['IdTipoDocumentoPadre']!="")
		{
			$sparam['IdTipoDocumentoPadre'] = $datos['IdTipoDocumentoPadre'];
			$sparam['xIdTipoDocumentoPadre'] = 1;
		}
		if (isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre'] = $datos['Nombre'];
			$sparam['xNombre'] = 1;
		}
		if (isset($datos['IdCategoria']) && $datos['IdCategoria']!="")
		{
			$sparam['IdCategoria'] = $datos['IdCategoria'];
			$sparam['xIdCategoria'] = 1;
		}

		if (isset($datos['IdClasificacion']) && $datos['IdClasificacion']!="")
		{
			$sparam['IdClasificacion'] = $datos['IdClasificacion'];
			$sparam['xIdClasificacion'] = 1;
		}

		if (isset($datos['NombreCorto']) && $datos['NombreCorto']!="")
		{
			$sparam['NombreCorto'] = $datos['NombreCorto'];
			$sparam['xNombreCorto'] = 1;
		}

		if (isset($datos['MostrarAlta']) && $datos['MostrarAlta']!="") {
			$sparam['MostrarAlta'] = $datos['MostrarAlta'];
			$sparam['xMostrarAlta'] = 1;
		}

        if (isset($datos['MostrarAltaPON']) && $datos['MostrarAltaPON']!="") {
            $sparam['MostrarAltaPON'] = $datos['MostrarAltaPON'];
            $sparam['xMostrarAltaPON'] = 1;
        }

		if (isset($datos['Limit']) && $datos['Limit']!="")
			$sparam['Limit'] = $datos['Limit'];
		if (!parent::BuscarDocumentosAltaxIdRol($sparam,$resultado,$numfilas))
			return false;

		return true;
	}



	public function BuscarDocumentosAltaDependientesxIdRolTipoDocumentoEstado($datos,&$resultado,&$numfilas)
	{

		$roles = implode(",",$_SESSION['rolcod']);
		$sparam=array(
			'IdRol'=> $roles,
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdEstado'=> $datos['IdEstado']
		);
		if (!parent::BuscarDocumentosAltaDependientesxIdRolTipoDocumentoEstado($sparam,$resultado,$numfilas))
			return false;

		return true;
	}

	public function BuscarDocumentosAltaDependientesxIdRolTipoDocumentoEstadoxIdTipoDocumento($datos,&$resultado,&$numfilas)
	{

		$roles = implode(",",$_SESSION['rolcod']);
		$sparam=array(
			'IdRol'=> $roles,
			'IdTipoDocumento'=> $datos['IdTipoDocumento'],
			'IdEstado'=> $datos['IdEstado'],
			'IdTipoDocumentoSeleccionado'=> $datos['IdTipoDocumentoSeleccionado'],
		);
		if (!parent::BuscarDocumentosAltaDependientesxIdRolTipoDocumentoEstadoxIdTipoDocumento($sparam,$resultado,$numfilas))
			return false;

		return true;
	}



}
?>
