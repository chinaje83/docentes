<?php 
include(DIR_CLASES_DB."cAlertas.db.php");

class cAlertas extends cAlertasdb
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



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdAlertaTipo'=> 0,
			'IdAlertaTipo'=> "",
			'xIdModulo'=> 0,
			'IdModulo'=> "-1",
			'xIdRol'=> 0,
			'IdRol'=> "-1",
			'xUsuarioGenero'=> 0,
			'UsuarioGenero'=> "",
			'xUsuarioIgnorar'=> 0,
			'xEsObligatorio'=> 0,
			'EsObligatorio'=> "",
			'xFechaAlta'=> 0,
			'FechaAlta'=> "",
			'limit'=> '',
			'orderby'=> "IdAlerta DESC"
		);

		if(isset($datos['IdAlertaTipo']) && $datos['IdAlertaTipo']!="")
		{
			$sparam['IdAlertaTipo']= $datos['IdAlertaTipo'];
			$sparam['xIdAlertaTipo']= 1;
		}
		if(isset($datos['IdModulo']) && $datos['IdModulo']!="")
		{
			$sparam['IdModulo']= $datos['IdModulo'];
			$sparam['xIdModulo']= 1;
		}
		if(isset($datos['IdRol']) && $datos['IdRol']!="")
		{
			$sparam['IdRol']= $datos['IdRol'];
			$sparam['xIdRol']= 1;
		}
		if(isset($datos['UsuarioGenero']) && $datos['UsuarioGenero']!="")
		{
			$sparam['UsuarioGenero']= $datos['UsuarioGenero'];
			$sparam['xUsuarioGenero']= 1;
		}
		if(isset($datos['Ignorar']) && $datos['Ignorar']!="")
		{
			$sparam['xUsuarioIgnorar']= 1;
		}
		if(isset($datos['EsObligatorio']) && $datos['EsObligatorio']!="")
		{
			$sparam['EsObligatorio']= $datos['EsObligatorio'];
			$sparam['xEsObligatorio']= 1;
		}
		if(isset($datos['FechaAlta']) && $datos['FechaAlta']!="")
		{
			$sparam['FechaAlta']=FuncionesPHPLocal::ConvertirFecha( $datos['FechaAlta'],'dd/mm/aaaa','aaaa-mm-dd');
			$sparam['xFechaAlta']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function ObtenerCantidadMsgNoLeidos()
	{		
		$total = 0;
		
		if (!parent::BuscarCantidadMsgNoLeidosxUsuario($resultado,$numfilas))
			return false;
		if ($numfilas>0)
		{
			$datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$total = $datos['total'];	
		}

		
		
		return $total;

	}



	public function BuscarNoLeidas($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdAlertaTipo'=> 0,
			'IdAlertaTipo'=> "",
			'xIdModulo'=> 0,
			'IdModulo'=> "-1",
			'xIdRol'=> 0,
			'IdRol'=> "-1",
			'xUsuarioGenero'=> 0,
			'UsuarioGenero'=> "",
			'xEsObligatorio'=> 0,
			'EsObligatorio'=> "",
			'xFechaAlta'=> 0,
			'FechaAlta'=> "",
			'limit'=> '',
			'orderby'=> "IdAlerta DESC"
		);

		if(isset($datos['IdAlertaTipo']) && $datos['IdAlertaTipo']!="")
		{
			$sparam['IdAlertaTipo']= $datos['IdAlertaTipo'];
			$sparam['xIdAlertaTipo']= 1;
		}
		if(isset($datos['IdModulo']) && $datos['IdModulo']!="")
		{
			$sparam['IdModulo']= $datos['IdModulo'];
			$sparam['xIdModulo']= 1;
		}
		if(isset($datos['IdRol']) && $datos['IdRol']!="")
		{
			$sparam['IdRol']= $datos['IdRol'];
			$sparam['xIdRol']= 1;
		}
		if(isset($datos['UsuarioGenero']) && $datos['UsuarioGenero']!="")
		{
			$sparam['UsuarioGenero']= $datos['UsuarioGenero'];
			$sparam['xUsuarioGenero']= 1;
		}
		if(isset($datos['EsObligatorio']) && $datos['EsObligatorio']!="")
		{
			$sparam['EsObligatorio']= $datos['EsObligatorio'];
			$sparam['xEsObligatorio']= 1;
		}
		if(isset($datos['FechaAlta']) && $datos['FechaAlta']!="")
		{
			$sparam['FechaAlta']=FuncionesPHPLocal::ConvertirFecha( $datos['FechaAlta'],'dd/mm/aaaa','aaaa-mm-dd');
			$sparam['xFechaAlta']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BuscarNoLeidas($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function ModuloAccionesAlertasTiposSP(&$spnombre,&$sparam)
	{
		if (!parent::ModuloAccionesAlertasTiposSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function ModuloAccionesAlertasTiposSPResult(&$resultado,&$numfilas)
	{
		if (!$this->ModuloAccionesAlertasTiposSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function ModulosSP(&$spnombre,&$sparam)
	{
		if (!parent::ModulosSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function ModulosSPResult(&$resultado,&$numfilas)
	{
		if (!$this->ModulosSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function RolesSP(&$spnombre,&$sparam)
	{
		if (!parent::RolesSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function RolesSPResult(&$resultado,&$numfilas)
	{
		if (!$this->RolesSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function UsuariosSP(&$spnombre,&$sparam)
	{
		if (!parent::UsuariosSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function UsuariosSPResult(&$resultado,&$numfilas)
	{
		if (!$this->UsuariosSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		if(isset($datos['FechaAlta']) && $datos['FechaAlta'] !="" && substr($datos['FechaAlta'],2,1) == "/")
			$datos['FechaAlta']=FuncionesPHPLocal::ConvertirFecha( $datos['FechaAlta'],'dd/mm/aaaa','aaaa-mm-dd');
		
		$this->_SetearNull($datos);
		
		$datos['JsonData'] = json_encode(FuncionesPHPLocal::ConvertiraUtf8($datos));
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		return true;
	}
	
	
	
	public function InsertarAlerta($datos,&$codigoinsertado)
	{
		$datosMail = array();
		//acá debería cargar las direcciones de email, el texto predeterminado, modulos, etc
		$oTipos = new cModulosAccionesAlertasTipos($this->conexion,$this->formato);
		$oModulosRoles = new  cModulosAlertasModulosRoles($this->conexion,$this->formato);
		$oUsuarios = new cUsuarios($this->conexion,$this->formato);
		$datosBuscar['IdAlertaTipo'] = $datos['IdAlertaTipo'];
		if(!$oTipos->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		if($numfilas != 1)
		{
			//print_r($resultado);return false;
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un tipo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$filaTipos = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if(boolval($filaTipos['UsaDefault']))
			$datos['Texto'] = $filaTipos['TextoDefault'];
		$datos['AlertaNombre'] = $filaTipos['Nombre'];
		$datos['Accion'] = $filaTipos['Accion'];
		$datos['ModuloSalida'] = $filaTipos['ModuloSalida'];
		unset($datosBuscar);
		$datosBuscar['IdModuloInicial'] = $filaTipos['IdModulo'];
		//print_r($datosBuscar);return false;
		if(!$oModulosRoles->BusquedaAvanzada($datosBuscar,$resultado,$numfilas))
			return false;
			

		//print_r($resultado);return false;
		if($numfilas === 0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un modulo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		unset($datosBuscar);
		$enviamail = false;
		$filaModulosRoles = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$Modulos = explode(",",$filaModulosRoles['IdModuloFinal']);
		$Roles = explode(",",$filaModulosRoles['IdRol']);
		$datos['EsObligatorio'] = $filaModulosRoles['EsObligatorio'];
		$datosMail['datos'] = $datos;
		if(count($Modulos)>0)
		{
			foreach($Modulos as $modulo)
			{
				$datosBuscar['IdModulo'] = $datos['IdModulo'] = $modulo;
				if(count($Roles)>0)
				{
					foreach($Roles as $rol)
					{
						$datosBuscar['IdRol'] = $datos['IdRol'] = $rol;
						if(boolval($filaModulosRoles['EnviaMail']))
						{
							$enviamail = true;
							if(!$oUsuarios->BuscarUsuariosAlertas($datosBuscar,$resultado,$numfilas))
								return false;
							if($numfilas != 0)
							{
								$datos['UsuarioEmail'] = array();
								$datos['UsuarioNombre'] = array();
								$datos['UsuarioApellido'] = array();
								while($filaUsuarios = $this->conexion->obtenerSiguienteRegistro($resultado))
								{
									$datosMail[$filaUsuarios['IdUsuario']]['Email'] = $datos['UsuarioEmail'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Email'];
									$datosMail[$filaUsuarios['IdUsuario']]['Nombre'] = $datos['UsuarioNombre'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Nombre'];
									$datosMail[$filaUsuarios['IdUsuario']]['Apellido'] = $datos['UsuarioApellido'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Apellido'];
								}
							}
							
						}
						
						if(!$this->Insertar($datos,$codigoinsertado))
							return false;
					}
				}
				else
				{
					if(boolval($filaModulosRoles['EnviaMail']))
					{
						$enviamail = true;
						if(!$oUsuarios->BuscarUsuariosAlertas($datosBuscar,$resultado,$numfilas))
							return false;
						if($numfilas != 0)
						{
							$datos['UsuarioEmail'] = array();
							$datos['UsuarioNombre'] = array();
							$datos['UsuarioApellido'] = array();
							while($filaUsuarios = $this->conexion->obtenerSiguienteRegistro($resultado))
							{
								$datosMail[$filaUsuarios['IdUsuario']]['Email'] = $datos['UsuarioEmail'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Email'];
								$datosMail[$filaUsuarios['IdUsuario']]['Nombre'] = $datos['UsuarioNombre'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Nombre'];
								$datosMail[$filaUsuarios['IdUsuario']]['Apellido'] = $datos['UsuarioApellido'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Apellido'];
							}
						}
						
					}
					
					if(!$this->Insertar($datos,$codigoinsertado))
						return false;
				}
			}
		}
		else
		{
			foreach($Roles as $rol)
			{
				$datosBuscar['IdRol'] = $datos['IdRol'] = $rol;
				if(boolval($filaModulosRoles['EnviaMail']))
				{
					$enviamail = true;
					if(!$oUsuarios->BuscarUsuariosAlertas($datosBuscar,$resultado,$numfilas))
						return false;
					if($numfilas != 0)
					{
						$datos['UsuarioEmail'] = array();
						$datos['UsuarioNombre'] = array();
						$datos['UsuarioApellido'] = array();
						while($filaUsuarios = $this->conexion->obtenerSiguienteRegistro($resultado))
						{
							$datosMail[$filaUsuarios['IdUsuario']]['Email'] = $datos['UsuarioEmail'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Email'];
							$datosMail[$filaUsuarios['IdUsuario']]['Nombre'] = $datos['UsuarioNombre'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Nombre'];
							$datosMail[$filaUsuarios['IdUsuario']]['Apellido'] = $datos['UsuarioApellido'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Apellido'];
						}
					}
					
				}
				
				if(!$this->Insertar($datos,$codigoinsertado))
					return false;
			}
		}
		//print_r($datosMail);return false;
		if(boolval(ENVIAREMAILALERTA) && $enviamail)
		{	
			if(!$this->_enviarMailUsuarios($datosMail))
				return false;
		}
		return true;
	}
	
	
	
	public function InsertarAlertaxIdAccion($datos)
	{
		$datosMail = array();
		//acá debería cargar las direcciones de email, el texto predeterminado, modulos, etc
		$oTipos = new cModulosAccionesAlertasTipos($this->conexion,$this->formato);
		$oModulosRoles = new  cModulosAlertasModulosRoles($this->conexion,$this->formato);
		$oUsuarios = new cUsuarios($this->conexion,$this->formato);
		if(!$oTipos->BuscarxAccion($datos,$resultado,$numfilas))
			return false;
		if($numfilas == 0)
		{
			//print_r($resultado);return false;
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"La accion no genera alertas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return true;
		}
		$filaTipos = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$datos['IdAlertaTipo'] = $filaTipos['IdAlertaTipo'];
		if(boolval($filaTipos['UsaDefault']))
			$datos['Texto'] = $filaTipos['TextoDefault'];
		$datos['AlertaNombre'] = $filaTipos['Nombre'];
		$datos['Accion'] = $filaTipos['Accion'];
		$datos['ModuloSalida'] = $filaTipos['ModuloSalida'];
		unset($datosBuscar);
		$datosBuscar['IdModuloInicial'] = $filaTipos['IdModulo'];
		//print_r($datosBuscar);return false;
		if(!$oModulosRoles->BusquedaAvanzada($datosBuscar,$resultado,$numfilas))
			return false;
			

		//print_r($resultado);return false;
		if($numfilas === 0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un modulo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		unset($datosBuscar);
		$enviamail = false;
		$filaModulosRoles = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$Modulos = explode(",",$filaModulosRoles['IdModuloFinal']);
		$Roles = explode(",",$filaModulosRoles['IdRol']);
		$datos['EsObligatorio'] = $filaModulosRoles['EsObligatorio'];
		$datosMail['datos'] = $datos;
		if(count($Modulos)>0)
		{
			foreach($Modulos as $modulo)
			{
				$datosBuscar['IdModulo'] = $datos['IdModulo'] = $modulo;
				if(count($Roles)>0)
				{
					foreach($Roles as $rol)
					{
						$datosBuscar['IdRol'] = $datos['IdRol'] = $rol;
						if(boolval($filaModulosRoles['EnviaMail']))
						{
							$enviamail = true;
							if(!$oUsuarios->BuscarUsuariosAlertas($datosBuscar,$resultado,$numfilas))
								return false;
							if($numfilas != 0)
							{
								$datos['UsuarioEmail'] = array();
								$datos['UsuarioNombre'] = array();
								$datos['UsuarioApellido'] = array();
								while($filaUsuarios = $this->conexion->obtenerSiguienteRegistro($resultado))
								{
									$datosMail[$filaUsuarios['IdUsuario']]['Email'] = $datos['UsuarioEmail'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Email'];
									$datosMail[$filaUsuarios['IdUsuario']]['Nombre'] = $datos['UsuarioNombre'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Nombre'];
									$datosMail[$filaUsuarios['IdUsuario']]['Apellido'] = $datos['UsuarioApellido'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Apellido'];
								}
							}
							
						}
						
						if(!$this->Insertar($datos,$codigoinsertado))
							return false;
					}
				}
				else
				{
					if(boolval($filaModulosRoles['EnviaMail']))
					{
						$enviamail = true;
						if(!$oUsuarios->BuscarUsuariosAlertas($datosBuscar,$resultado,$numfilas))
							return false;
						if($numfilas != 0)
						{
							$datos['UsuarioEmail'] = array();
							$datos['UsuarioNombre'] = array();
							$datos['UsuarioApellido'] = array();
							while($filaUsuarios = $this->conexion->obtenerSiguienteRegistro($resultado))
							{
								$datosMail[$filaUsuarios['IdUsuario']]['Email'] = $datos['UsuarioEmail'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Email'];
								$datosMail[$filaUsuarios['IdUsuario']]['Nombre'] = $datos['UsuarioNombre'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Nombre'];
								$datosMail[$filaUsuarios['IdUsuario']]['Apellido'] = $datos['UsuarioApellido'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Apellido'];
							}
						}
						
					}
					
					if(!$this->Insertar($datos,$codigoinsertado))
						return false;
				}
			}
		}
		else
		{
			foreach($Roles as $rol)
			{
				$datosBuscar['IdRol'] = $datos['IdRol'] = $rol;
				if(boolval($filaModulosRoles['EnviaMail']))
				{
					$enviamail = true;
					if(!$oUsuarios->BuscarUsuariosAlertas($datosBuscar,$resultado,$numfilas))
						return false;
					if($numfilas != 0)
					{
						$datos['UsuarioEmail'] = array();
						$datos['UsuarioNombre'] = array();
						$datos['UsuarioApellido'] = array();
						while($filaUsuarios = $this->conexion->obtenerSiguienteRegistro($resultado))
						{
							$datosMail[$filaUsuarios['IdUsuario']]['Email'] = $datos['UsuarioEmail'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Email'];
							$datosMail[$filaUsuarios['IdUsuario']]['Nombre'] = $datos['UsuarioNombre'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Nombre'];
							$datosMail[$filaUsuarios['IdUsuario']]['Apellido'] = $datos['UsuarioApellido'][$filaUsuarios['IdUsuario']] = $filaUsuarios['Apellido'];
						}
					}
					
				}
				
				if(!$this->Insertar($datos,$codigoinsertado))
					return false;
			}
		}
		//print_r($datosMail);return false;
		if(boolval(ENVIAREMAILALERTA) && $enviamail)
		{	
			if(!$this->_enviarMailUsuarios($datosMail))
				return false;
		}
		
		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos))
			return false;

		if (!parent::Eliminar($datos))
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



	private function _ValidarModificar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	private function _ValidarEliminar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdAlertaTipo']) || $datos['IdAlertaTipo']=="")
			$datos['IdAlertaTipo']="NULL";

		if (!isset($datos['IdModulo']) || $datos['IdModulo']=="")
			$datos['IdModulo']="NULL";

		if (!isset($datos['IdRol']) || $datos['IdRol']=="")
			$datos['IdRol']="NULL";

		if (!isset($datos['UsuarioGenero']) || $datos['UsuarioGenero']=="")
			$datos['UsuarioGenero']="NULL";

		if (!isset($datos['EsObligatorio']) || $datos['EsObligatorio']=="")
			$datos['EsObligatorio']="NULL";

		if (!isset($datos['FechaAlta']) || $datos['FechaAlta']=="")
			$datos['FechaAlta']="NULL";

		if (!isset($datos['Texto']) || $datos['Texto']=="")
			unset($datos['Texto']);
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdAlertaTipo']) || $datos['IdAlertaTipo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdAlertaTipo'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if ((!isset($datos['IdModulo']) || $datos['IdModulo']=="") && (!isset($datos['IdRol']) || $datos['IdRol']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un modulo o un rol",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if(isset($datos['IdModulo']) && $datos['IdModulo']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdModulo'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (!$this->conexion->TraerCampo('Modulos','IdModulo',array('IdModulo='.$datos['IdModulo']),$dato,$numfilas,$errno))
				return false;
	
	
			if ($numfilas!=1)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}

		}
		if(isset($datos['IdRol']) && $datos['IdRol']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRol'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			
		
			if (!$this->conexion->TraerCampo('Roles','IdRol',array('IdRol='.$datos['IdRol']),$dato,$numfilas,$errno))
				return false;
	
	
			if ($numfilas!=1)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}


		if (!isset($datos['EsObligatorio']) || $datos['EsObligatorio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seleccionar si es obliogatorio",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!$this->conexion->TraerCampo('ModuloAccionesAlertasTipos','IdAlertaTipo',array('IdAlertaTipo='.$datos['IdAlertaTipo']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	
	private function _enviarMailUsuarios($datos)
	{
		include(DIR_LIBRERIAS."PHPMailerAutoload.php");
		$datosGrales = $datos['datos'];
		unset($datos['datos']);
		
		/**
		 * Envía el mail usando la librería PHPMailier
		 */
		$mail = new PHPMailer ();
		$mail->SetLanguage( 'es', 'phpmailer/language/' );
		$mail -> SMTPDebug=0;
		$mail -> FromName = EMAIL_FROMNAME;
		$mail -> From = EMAIL_FROM;
		$mail -> Subject = "Se ha generado una alerta en ".PROJECTNAME;
		$mail -> AddAddress("undisclosed-recipients:;");
		foreach($datos as $para)
			$mail->AddBCC($para['Email'], "{$para['Nombre']} {$para['Apellido']}");
		/**
		 * Genera el cuerpo del mail
		 */
		$htmlmail =  "
<html> 
	<body style='margin:25px;'>
		Se ha generado una alerta {$datosGrales['AlertaNombre']} en el sistema ".PROJECTNAME;
		$htmlmail .= " debido a la acci&oacute;n {$datosGrales['Accion']} en el m&oacute;dulo {$datosGrales['ModuloSalida']}<br />";
		$htmlmail .= "Contenido de la alerta:<br />";
		$htmlmail .= $datosGrales['Texto'];
		$htmlmail .= "
		<span style='font-size:9px; color:#999;'>La informaci&oacute;n contenida tanto en este e-mail, es informaci&oacute;n confidencial y privilegiada para uso exclusivo de la persona o personas a las que va dirigido. No est&aacute; permitido el acceso a este mensaje a cualquier otra persona distinta de los indicados. Si no es el destinatario o ha recibido este mensaje por error, cualquier duplicaci&oacute;n, reproducci&oacute;n, distribuci&oacute;n, as&iacute; como cualquier uso de la informaci&oacute;n contenida o cualquiera otra acci&oacute;n tomada en relaci&oacute;n con el mismo, est&aacute; prohibida y puede ser ilegal. No se autoriza su utilizaci&oacute;n con fines comerciales o para su incorporaci&oacute;n a ficheros automatizados de las direcciones del emisor o del destinatario. En consecuencia, si recibe este correo sin ser el destinatario del mismo, le rogamos proceda a su eliminaci&oacute;n y lo ponga en conocimiento del emisor.</span>
	</body>
</html>";

		//echo $htmlmail; return false;

	
		$mail -> Body = $htmlmail;
		$mail -> IsHTML (true);
		/**
		 * Establacemos que utilzaremos SMTP 
		 * y habilitamos la autenticación.
		 */
		if (boolval(SMTP))
		{
			$mail->IsSMTP(); // establecemos que utilizaremos SMTP
			$mail->SMTPAuth   = true; // habilitamos la autenticación SMTP
		}
		
		/**
		 * La siguiente parte debería estar comentada en la mayoría de los casos
		 * ya que desactiva la verificación de certificados, solo se debe usar cuando 
		 * la verificacion de certificados falla constantemente (geralmente pasa en windows)
		 */
//----------------------------------------------------------------------------
//		$mail -> SMTPOptions = array(										//
//			'ssl' => array(													//
//				'verify_peer' => false,										//
//				'verify_peer_name' => false,								//
//				'allow_self_signed' => true									//
//			)																//
//		);																	//
//----------------------------------------------------------------------------
	
		/**
		 * Seleccióna el tipo de autenticación dependiendo de los parametros
		 */
		if (SMTP_SSL==1)
			$mail->SMTPSecure = "ssl";
		if (SMTP_TLS==1)
			$mail->SMTPSecure = "tls";
		$mail->SMTPKeepAlive = true;
		$mail->Host       = SMTP_HOST;
		$mail->Port       = SMTP_PORT;
		$mail->Username   = SMTP_USER;
		$mail->Password   = SMTP_PASSW;
		$mail->SetFrom(EMAIL_FROM, EMAIL_FROMNAME);
		/**
		 * Envía e mail.
		 */
		if(!$mail->Send()) 
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al enviar mail: {$mail->ErrorInfo}",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		
		return true;
	}




//-----------------------------------------------------------------------------------------
//FUNCIONES ESTATICAS
//-----------------------------------------------------------------------------------------

	static function GenerarAlerta($IdAccion,$conexion)
	{
		$oObjeto = new cAlertas($conexion,"");
		$datos['IdAccion'] = $IdAccion;
		if(!$oObjeto->InsertarAlertaxIdAccion($datos))
		{
			FuncionesPHPLocal::MostrarMensaje($conexion,MSG_ERRGRAVE,"No se pude insertar",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}
		return true;
	}




}
?>