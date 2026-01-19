<?php  

//--------------------------------------------------------------------------
//  Define el acceso a la Base de datos.
//--------------------------------------------------------------------------

//@class_alias('mysqli_result', 'Resultado');
use mysqli_result as Resultado;

/**
 * Class accesoBDLocal
 *
 * @deprecated
 */
class accesoBDLocalMysqli
{
	var $idconexion;
	var $admigeneral;
	var $base_datos;
	var $store_memory;

	function __construct($servidor,$usuariodb,$clave) {
		$this->idconexion = mysqli_connect($servidor, $usuariodb, $clave) or die("No es posible establecer conexion con la base de datos");
   		$this->store_memory = true;
	}
//-----------------------------------------------------------------------------------------
//							 PUBLICAS
//-----------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------
// Permite la selecci�n de la base de datos sobre la cual se va a trabajar.


	function SeleccionBD($base_datos)
	{
		mysqli_select_db($this->idconexion,$base_datos) or die("No es posible conectar a la base de datos");

		$this->base_datos=$base_datos;
	}



//-----------------------------------------------------------------------------------------
// Obtiene el siguiente registro de un resultado.
	function ObtenerSiguienteRegistro(&$resultado)
	{
		$datos = mysqli_fetch_assoc($resultado);
		return $datos;
	}

//-----------------------------------------------------------------------------------------
// Obtiene el siguiente registro de un resultado.
	function ObtenerSiguienteRegistroArray(&$resultado)
	{
		$datos = mysqli_fetch_array($resultado);
		return $datos;
	}


//-----------------------------------------------------------------------------------------
// Escapea El String ingresado.
	function EscapeaElString(&$string)
	{
		$dato = mysqli_real_escape_string($this->idconexion,$string);
		return $dato;
	}



//-----------------------------------------------------------------------------------------
// Obtiene la cantidad de registros de un resultado
	function ObtenerCantidadDeRegistros(&$resultado)
	{
		$cantidad = 0;
		if ($this->store_memory)
			$cantidad = mysqli_num_rows($resultado);
		return $cantidad;
	}


//-----------------------------------------------------------------------------------------

// Mueve el puntero de resultados interno
	function MoverPunteroaPosicion(&$resultado,$posicion=0)
	{
		mysqli_data_seek($resultado,$posicion);
	}


//-----------------------------------------------------------------------------------------
// Ejecuta el SQL almacenado en la base stored_procedures con nombre $sp_nombre

// 1) Sintaxis del SQL en la tabla stored_procedures:
//		Los parametros deben ser escritos entre #.

// 2) Recibe un array asociativo con los parametros del requeridos para el SQL.

//	  - La cantidad de parametros enviados debe se igual a la cantidad de parametros.
//    - Los nombres de los parametros no pueden ser igual al de campos asociados a las tablas
//      involucradas en el SQL ni pueden ser palabras reservadas del SQL.

// Ejemplo llamada:
// 		$param=array("parchivonom" => $_POST['archivonom']);
//		$query=$conexion->ejecutarStoredProcedure("sel_archivos_xnombre",$param,$resultado,$numfilas,$errno);
// Ejemplo definicion en la base de stored_procedures:
//      select archivonom from archivos where archivonom="#parchivonom#"

// Retorna:
//		- En un SELECT:
//			-> si se ejecut� bien retorna true, el resultado del query y la cantidad de filas
//			-> en caso contrario, retorna false y el numero de error
//		- En otra operaci�n:
//			-> si se ejecut� bien retorna true y la cantidad de filas afectadas
//			-> en caso contrario, retorna false y el numero de error

	public function ejecutarStoredProcedure($sp_nombre,$sp_param,&$resultadosalida,&$numfilassalida,&$errnosalida)
	{
		$resultadosalida=false;
		$numfilassalida=-1;
		$errnosalida=0;


		if (!is_array($sp_param))
		{

			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Problema en la llamada al stored procedure, no se envia un array. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		$sql ="SELECT * FROM stored_procedures";
		$sql.=" WHERE ucase(spnombre)='".strtoupper($sp_nombre)."'";
		if(!$this->_EjecutarQuery($sql,'ejecutarStoredProcedure',$resultado,$errno))
		{

			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Problema en la tabla stored_procedures. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}
		// Debe estar en la tabla de stored procedures
		if ($this->ObtenerCantidadDeRegistros($resultado)!=1)
		{
					if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"No se encuentra el procedimiento: ".$sp_nombre." en la tabla stored_procedures. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		$fila=$this->ObtenerSiguienteRegistro($resultado);

		// Valida que los nombres y cantidad de parametros
		if (!$this->_ValidarParametros($fila['spsqlstring'],$sp_param))
		{

			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Error en los par�metros enviados a ".$sp_nombre.". ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		// Todo es inicialmente valido para la ejecucion del Stored Procedure
		// Reemplazo los parametros enviados en el string SQL
// echo "<br /> ANTES ".$fila['spsqlstring'];
		if (!$this->_ReemplazarParametros($fila['spsqlstring'],$sp_param,$sql_sp))
		{
			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Error al reemplazar par�metros en ".$sp_nombre.". ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		if ($sp_nombre=="sel_SolicitudesCobertura_es_xId")
		{
			//echo $sql_sp;
			//die();
		}

		if (!$this->_EjecutarQuery($sql_sp,"stored procedure ".$fila['spcod']." - ".$fila['spnombre'],$resultadosalida,$errnosalida))
		{

			if (RUN_ACTUAL==RUN_DESARROLLO && mysqli_sqlstate($this->idconexion)!=45000)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Problema en la ejecuci�n del stored procedure ".$sp_nombre.". ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		if($fila['spoperacion']=="SEL")
			$numfilassalida=$this->ObtenerCantidadDeRegistros($resultadosalida);
		else
			$numfilassalida=mysqli_affected_rows($this->idconexion);


		return true;
	}


	public function ejecutarSQL($sql_sp,$operacion,&$resultadosalida,&$numfilassalida,&$errnosalida)
	{
		$resultadosalida=false;
		$numfilassalida=-1;
		$errnosalida=0;

		if (!$this->_EjecutarQuery($sql_sp,"stored procedure prearmado",$resultadosalida,$errnosalida))
		{

			if (RUN_ACTUAL==RUN_DESARROLLO && mysqli_sqlstate($this->idconexion)!=45000)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Problema en la ejecuci�n del stored procedure Prearmado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		if($operacion=="SEL")
			$numfilassalida=$this->ObtenerCantidadDeRegistros($resultadosalida);
		else
			$numfilassalida=mysqli_affected_rows($this->idconexion);


		return true;
	}



	function ArmarStoredProcedure($sp_nombre,$sp_param,&$sql)
	{
		$resultadosalida=false;
		$numfilassalida=-1;
		$errnosalida=0;

		if (!is_array($sp_param))
		{

			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Problema en la llamada al stored procedure, no se envia un array. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		$sql ="SELECT * FROM stored_procedures";
		$sql.=" WHERE ucase(spnombre)='".strtoupper($sp_nombre)."'";

		if(!$this->_EjecutarQuery($sql,'ejecutarStoredProcedure',$resultado,$errno))
		{
			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Problema en la tabla stored_procedures. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		// Debe estar en la tabla de stored procedures
		if ($this->ObtenerCantidadDeRegistros($resultado)!=1)
		{
			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"No se encuentra el procedimiento: ".$sp_nombre." en la tabla stored_procedures. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		$fila=$this->ObtenerSiguienteRegistro($resultado);

		// Valida que los nombres y cantidad de parametros
		if (!$this->_ValidarParametros($fila['spsqlstring'],$sp_param))
		{
			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Error en los par�metros enviados a ".$sp_nombre.". ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		// Todo es inicialmente valido para la ejecucion del Stored Procedure
		// Reemplazo los parametros enviados en el string SQL
// echo "<br /> ANTES ".$fila['spsqlstring'];
		if (!$this->_ReemplazarParametros($fila['spsqlstring'],$sp_param,$sql_sp))
		{
			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Error al reemplazar par�metros en ".$sp_nombre.". ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}
		$sql = $sql_sp;
		return true;
	}
//-----------------------------------------------------------------------------------------
// Usada para el manejo de transacciones

// Retorna true si no hubo problema, sino retorna false

	function ManejoTransacciones($transtipo)
	{
		switch($transtipo)
		{
			case "B": // begin
				if (!$this->_EjecutarQuery("BEGIN",'ManejoTransacciones',$resultado,$errno))
					return false;
				break;
			case "C": // commit
				if (!$this->_EjecutarQuery("COMMIT",'ManejoTransacciones',$resultado,$errno))
					return false;
				break;
			case "R": // rollback
				if (!$this->_EjecutarQuery("ROLLBACK",'ManejoTransacciones',$resultado,$errno))
					return false;
				break;
			default:
				if (RUN_ACTUAL==RUN_DESARROLLO)
					FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Tipo de transacci�n no definida. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
				return false;
				break;
		}

		return true;
	}
//--------------------------------------------------------------------------
// Trae el valor de un campo de un registro de una tabla, seg�n la condici�n especificada

// Par�metros:
//		$arraywhere: un array (no asociativo) simulando tal cual lo que iria en el where del sql,
//			aplic�ndose el escapeado a los elementos impares.

// Retorna:
//		Si se produjo un error, retorna false y el numero de error
//		Si se ejecut� con exito, retorna true y el numero de filas encontradas. En caso que el numero de filas
//			sea mayor a 0, retorna el valor del campo seleccionado de la primer fila.

	function TraerCampo($tabla,$camponom,$arraywhere,&$dato,&$numfilas,&$errno)
	{
		$resultado=false;
		$numfilas=-1;
		$errno=0;

		$sql = "select ".$camponom." as campodevuelto  "."from ".$tabla;
		if (count($arraywhere)>0)
		{
			$sql.= " where ";

			for ($i=0;$i<count($arraywhere);$i++)
			{
				if ($i%2 == 0)
					$sql.=$arraywhere[$i];
				else
					$sql.=mysqli_real_escape_string($this->idconexion,$arraywhere[$i]);
			}
		}

		if (!$this->_EjecutarQuery($sql,'TraerCampo',$resultado,$errno))
		{
			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Problema en TraerCampo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		$numfilas=$this->ObtenerCantidadDeRegistros($resultado);
		if($numfilas>0)
		{
			$fila=$this->ObtenerSiguienteRegistro($resultado);
			$dato=$fila['campodevuelto'];
		}
		return true;
	}

//-----------------------------------------------------------------------------------------
// Actualiza el valor de un campo de un registro de una tabla, seg�n la condici�n especificada

// Par�metros:
//		$arraywhere: un array (no asociativo) simulando tal cual lo que iria en el where del sql,
//			aplic�ndose el escapeado a los elementos impares.
//		$datosauditoria: S/N para determinar si se actualiza o no ultmodusuario/ultmodfecha

// Retorna:
//		Si se produjo un error, retorna false y el numero de error
//		Si se ejecut� con exito, retorna true y el numero de filas afectadas

	function ActualizarCampo($tabla,$camponom,$campovalor,$arraywhere,$datosauditoria,&$numfilas,&$errno)
	{
		$sql="update $tabla";
		if($campovalor=="NULL")
			$sql.=" set $camponom=NULL";
		else
			$sql.=" set $camponom='".mysqli_real_escape_string($this->idconexion,$campovalor)."'";
		if ($datosauditoria=="S")
		{
			$sql.=",ultmodusuario=".$_SESSION['usuariocod'];
			$sql.=",ultmodfecha='".date('Y/m/d H:i:s')."'";
		}
		if (count($arraywhere)>0)
		{
			$sql.=" where ";
			for ($i=0;$i<count($arraywhere);$i++)
			{
				if ($i%2 == 0)
					$sql.=$arraywhere[$i];
				else
					$sql.=mysqli_real_escape_string($this->idconexion,$arraywhere[$i]);
			}
		}

		if (!$this->_EjecutarQuery($sql,'ActualizarCampo',$resultado,$errno))
		{
			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Problema en ActualizarCampo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		$numfilas=mysqli_affected_rows($this->idconexion);
		return true;
	}

//-----------------------------------------------------------------------------------------
// Busca en el resultado de la ejecucion de un SP, la/s clave/s enviada/s
//	$arraybusq: es una array asociativo donde la clave ser� el nombre del campo
//				y el valor el valor buscado.

// Ejemplo de uso:
// $param=array('pusuariocod' => $_SESSION['usuariocod']);
// BuscarRegistroxClave('sel_jurisdicciones_habilitadas_para_auditor',$param,array("jurisdcod" => $_POST['jurisdcod']),$resultado,$filaret,$numfilasmatcheo,$errno)

//  Retorna:
//		Si se produjo un error, false y el numero de error
//		Si se ejecut� con exito:
//			-> retorna true
//			-> en resultado, todo el query de la ejecuci�n del SP
//			-> en filaret, la fila que cumple las condiciones, en caso que sea la unica de todos los registros del SP
//			-> en numfilasmatcheo, la cantidad de filas que cumplen las condiciones

	function BuscarRegistroxClave($sp_nombre,$param,$arraybusq,&$resultado,&$filaret,&$numfilasmatcheo,&$errno)
	{
		$filaret=array();

		if(!$this->ejecutarStoredProcedure($sp_nombre,$param,$resultado,$numfilas,$errno))
			return false;

		$numfilasmatcheo=0;
		while($fila=$this->ObtenerSiguienteRegistro($resultado))
		{
			foreach ($arraybusq as $clave => $valor)
			{
				$matcheo=true;
				if (RUN_ACTUAL==RUN_DESARROLLO && !isset($fila[$clave]))
				{
					FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Problema en BuscarRegistroxClave. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
					return false;
				}

				if($fila[$clave]!=$valor)
				{
					$matcheo=false;
					break;
				}
			} // for each arraybusq
			if($matcheo)
			{
				$filaret=$fila;
				$numfilasmatcheo++;
			}
		} // while del query
		if($numfilasmatcheo!=1)
			$filaret=array();

		return true;
	}
//--------------------------------------------------------------------------
// Retorna el texto del �ltimo error producido

	function TextoError()
	{
		return mysqli_error($this->idconexion);
	}

//--------------------------------------------------------------------------
// Retorna el codigo del administrador general

	function VerAdmiGeneral()
	{
		return $this->admigeneral;
	}

//--------------------------------------------------------------------------
// Setea el codigo del administrador general

	function SetearAdmiGeneral($admigeneralcod)
	{
		$this->admigeneral=$admigeneralcod;
	}

//--------------------------------------------------------------------------
// Cierra la conexion

	function CerrarConexion()
	{
		mysqli_close($this->idconexion);
	}

//--------------------------------------------------------------------------
// Retorna el ultimo codigo insertado

	function UltimoCodigoInsertado()
	{
		return mysqli_insert_id($this->idconexion);
	}

//--------------------------------------------------------------------------
// Retorna los campos de una tabla

	function ObtenerCamposTabla($tabla)
	{
		return mysql_list_fields($this->base_datos, $tabla, $this->idconexion);
	}


	function StoreMemory($store)
	{
		$this->store_memory = $store;
	}

//--------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------
//							 PRIVADAS
//-----------------------------------------------------------------------------------------
// Ejecuta una instrucci�n SQL. En caso de error, env�a un mail a los usuarios admigeneral

// Retorna:
//		En caso de error, retorna false y el numero de error
//		Si se ejecut� con �xito, retorna true y el resultado del query

	function _EjecutarQuery($sql,$erroren,&$resultado,&$errno)
	{

		//$oMensajesInternos = new cMensajesInternos($this->idconexion);
		$errno=0;
		if ($this->store_memory)
			$resultado=mysqli_query($this->idconexion,$sql);
		else
			$resultado=mysqli_query($this->idconexion,$sql,MYSQLI_USE_RESULT);

		/*print_r($sql);*/
	/*	print_r($resultado);*/

		if(!$resultado)
		{
			$errno=mysqli_errno($this->idconexion);
			$textoerror="Error Mysql: ".mysqli_errno($this->idconexion)." - ".mysqli_error($this->idconexion);

			// MANDAR MAIL
			$subject=" ( ".SISTEMA.' - '.$_SERVER['SERVER_NAME']." ) - Error en ejecutar stored procedure";
			$texto="Se ha producido un error en ".$_SERVER['PHP_SELF']." - Error en ".$erroren."\n\n";
			/*
			if(isset($_SESSION['usuariocod']) && $_SESSION['usuariocod']!=0)
			{
				if(!$this->TraerCampo("usuarios","concat(usuarioapellido,',',usuarionombre)",array("usuariocod='",$_SESSION['usuariocod'],"'" ),$nombreusuario,$numfilas,$errno1))
					$texto.="Usuario ".$nombreusuario."\n\n";
			}*/
			$texto.="SQL= ".$sql."\n\n";
			$texto.=$textoerror;
			if (RUN_ACTUAL==RUN_DESARROLLO && mysqli_sqlstate($this->idconexion)!=45000)
				echo $texto;


			return false;
		}
		else
			return true;
	}
//-----------------------------------------------------------------------------------------
// Valida los parametros del SQL contra los enviados

// Retorna: true o false seg�n si est�n correctos los par�metros o no

	function _ValidarParametros($sql,$param) 	{

		/*
		$param_count = substr_count($sql,"#")/2;
		if ($param_count != count($param))
		{
			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,"Error en la cantidad de par�metros en la llamada al procedimiento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}*/


		$strSql=trim($sql);
		$i=0;

		$param_nomSQL=array();
		// Armo un arreglo con los nombres de los parametros en $sql (vienen de la tabla stored_procedure)
		while (strpos($strSql,"#")>0 )
		{
			$pos_ini = strpos($strSql,"#");
			$strSql = substr($strSql,$pos_ini+1,strlen($strSql) - $pos_ini+1) ;
			$pos_fin = strpos($strSql, "#");
			$param_nomSQL[$i] = substr($strSql,0,$pos_fin);
			$strSql = substr($strSql,$pos_fin+1,strlen($strSql) - $pos_fin);
			$i=$i+1;
		}

		// Comparo un arreglo con las claves del array enviado como parametro
		// con el arreglo que surge del string sql.
		//$param_nomArray=array_keys(array_change_key_case($param,CASE_LOWER));
		$param_nomArray=array_keys($param);

		// No hay parametros en la llamada ni definicion del stored proceduce
		if (count($param_nomSQL)==0 && count($param_nomArray)==0 )
			return true;

		$diff_array1=array_diff($param_nomSQL,$param_nomArray);
		$diff_array2=array_diff($param_nomArray,$param_nomSQL);

		if ( count($diff_array1)!=0  || count($diff_array2)!=0 )
		{
			$texto="Error en el nombre de los par�metros en la llamada al procedimiento.\r\n";
			$texto.="Par�metros que sobran del SQL:\r\n";
			$texto.=var_export($diff_array1,true);
			$texto.="\r\nPar�metros que sobran del array de par�metros:\r\n";
			$texto.=var_export($diff_array2,true);
			$texto.="\r\n";

			if (RUN_ACTUAL==RUN_DESARROLLO)
				FuncionesPHPLocal::MostrarMensaje($this,MSG_ERRGRAVE,$texto,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));

			return false;
		}

		return true;
	}

//--------------------------------------------------------------------------
// Reemplaza los parametros en el string SQL

// Retorna en sql_resu el resultado de reemplazar los parametros del SP con los enviados

	function _ReemplazarParametros($sql,$param,&$sql_resu)
	{
		// sql separado en piezas pasado o no a minuscula
		$sql_pieces = explode("#",trim($sql));
	    $sql_pieces_may_y_min = explode("#",(trim($sql)));

		// Los parametros vienen siempre en minuscula
		//$param_nom = array_keys(array_change_key_case($param,CASE_LOWER));
		$param_nom = array_keys($param);

		while (count($param_nom))
		{
			$param_search=array_shift($param_nom);

			// Busco la key en las piezas y lo reemplazo por el valor
			foreach ($sql_pieces as $xsql_pieces)
			{
				$posicion=array_search($param_search,$sql_pieces);
				if ($posicion)
				{
					// busco si el parametro es null
					if(is_array($param[$param_search]))
					{
						$sql_pieces[$posicion]="";
						$sql_pieces_may_y_min[$posicion]="";
						$param[$param_search] = '"'.implode('","',$param[$param_search]).'"';
						$sql_pieces[$posicion]=$param[$param_search];
						$sql_pieces_may_y_min[$posicion]=$param[$param_search];
					}
					elseif(trim($param[$param_search])=="NULL")
					{
						$sql_pieces[$posicion-1]=substr($sql_pieces[$posicion-1],0,strlen($sql_pieces[$posicion-1])-1);
						$sql_pieces[$posicion+1]=substr($sql_pieces[$posicion+1],1);
						$sql_pieces[$posicion]="NULL";

						$sql_pieces_may_y_min[$posicion-1]=substr($sql_pieces_may_y_min[$posicion-1],0,strlen($sql_pieces_may_y_min[$posicion-1])-1);
						$sql_pieces_may_y_min[$posicion+1]=substr($sql_pieces_may_y_min[$posicion+1],1);
						$sql_pieces_may_y_min[$posicion]="NULL";

					}
					else {  // Saco caracteres que podrian traer problemas para el SQL.

						$sql_pieces[$posicion]=mysqli_real_escape_string($this->idconexion,$param[$param_search]);
						$sql_pieces_may_y_min[$posicion]=mysqli_real_escape_string($this->idconexion,$param[$param_search]);
					}

				}
			}
		}

	   // Se pasa todo lo que no es parametro a minuscula.
	   // $sql_resu=implode("",$sql_pieces);

	   // Se respeta mayuscula y minuscula del stored, solo se toma en minuscula el nombre de los parametros
	   $sql_resu=implode("",$sql_pieces_may_y_min);

	   return true;

	}

	//-----------------------------------------------------------------------------------------
//							 PRIVADAS
//-----------------------------------------------------------------------------------------
// Ejecuta una instrucci�n SQL. En caso de error, env�a un mail a los usuarios admigeneral

// Retorna:
//		En caso de error, retorna false y el numero de error
//		Si se ejecut� con �xito, retorna true y el resultado del query

	function _EjecutarMultiplesQueries($sqls,$erroren,&$resultado,&$errno)
	{

		//$oMensajesInternos = new cMensajesInternos($this->idconexion);
		$errno=0;
		$resultado=mysqli_multi_query($this->idconexion,$sqls);
		if(!$resultado)
		{
			$errno=mysqli_errno($this->idconexion);
			return false;
		}
		while ($this->idconexion->next_result()) {;} // flush multi_queries
		/*
		if(!$resultado)
		{
			$errno=mysqli_errno($this->idconexion);
			$textoerror="Error Mysql: ".mysqli_errno($this->idconexion)." - ".mysqli_error($this->idconexion);

			// MANDAR MAIL
			$subject=" ( ".SISTEMA.' - '.$_SERVER['SERVER_NAME']." ) - Error en ejecutar stored procedure";
			$texto="Se ha producido un error en ".$_SERVER['PHP_SELF']." - Error en ".$erroren."\n\n";

			$texto.="SQL= ".$sql."\n\n";
			$texto.=$textoerror;
			if (RUN_ACTUAL==RUN_DESARROLLO && mysqli_sqlstate($this->idconexion)!=45000)
				echo $texto;


			return false;
		}
		else
		*/
		return true;
	}
//--------------------------------------------------------------------------
} // fin clase


