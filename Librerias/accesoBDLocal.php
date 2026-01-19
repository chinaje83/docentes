<?php
/**
 * @noinspection PhpStatementHasEmptyBodyInspection
 * @noinspection PhpUnused
 */

namespace Bigtree;

use Exception;
use FuncionesPHPLocal;
use mysqli;
use mysqli_result;

/**
 * Class accesoBDLocal
 *
 * Define el acceso a la Base de datos.
 */
class accesoBDLocal
{
    /** @var false|mysqli  */
	private $idConexion;
	/** @var integer|null */
	private $admin_general;
	/** @var string */
	private $base_datos;
	/** @var bool  */
	private $store_memory;
	/** @var bool */
    private $transaccion_activa = false;

    /**
     * accesoBDLocal constructor.
     * @param string $servidor
     * @param string $usuariodb
     * @param string $clave
     * @param int $puerto
     */
    function __construct(string $servidor, string $usuariodb, string $clave, int $puerto=3306) {
		$this->idConexion = new mysqli($servidor, $usuariodb, $clave, BASEDATOS, $puerto) or die('No es posible establecer conexion con la base de datos.');
		$this->base_datos = BASEDATOS;
   		$this->store_memory = true;
	}
//-----------------------------------------------------------------------------------------
//							 PUBLICAS
//-----------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------

    /**
     * Permite la selección de la base de datos sobre la cual se va a trabajar.
     *
     *
     * @param string $base_datos
     */
    public function SeleccionBD(string $base_datos): void
	{
	    if($base_datos !== $this->base_datos) {
            $this->idConexion->select_db($base_datos) or die('No es posible conectar a la base de datos.');
            $this->base_datos = $base_datos;
        }
	}

//-----------------------------------------------------------------------------------------
//
    /**
     * Obtiene el siguiente registro de un resultado.
     *
     * @param mysqli_result $resultado
     * @return string[]|null
     */
    public function ObtenerSiguienteRegistro(?mysqli_result $resultado): ?array
	{
        return $resultado->fetch_assoc();
	}

//-----------------------------------------------------------------------------------------

    /**
     * Obtiene el siguiente registro de un resultado.
     *
     * @param mysqli_result $resultado
     * @return array|null
     */
	public function ObtenerSiguienteRegistroArray(mysqli_result $resultado): ?array
	{
        return $resultado->fetch_array();
	}

//-----------------------------------------------------------------------------------------

    /**
     * Escapea El String ingresado.
     * @param string $string
     * @return string
     */
	public function EscapeaElString(string $string): string
	{
        return $this->idConexion->real_escape_string($string);
	}

//-----------------------------------------------------------------------------------------

    /**
     * Obtiene la cantidad de registros de un resultado
     *
     * @param mysqli_result $resultado
     * @return int
     */
	public function ObtenerCantidadDeRegistros(?mysqli_result $resultado): int
	{
		$cantidad = 0;
		if ($this->store_memory)
			$cantidad = $resultado->num_rows;
		return $cantidad;
	}

//-----------------------------------------------------------------------------------------

    /**
     * Mueve el puntero de resultados interno
     *
     * @param mysqli_result $resultado
     * @param int $posicion
     */
	function MoverPunteroaPosicion(mysqli_result $resultado, int $posicion=0): void
	{
        $resultado->data_seek($posicion);
	}

//-----------------------------------------------------------------------------------------

    /**
     * Ejecuta el SQL almacenado en la base stored_procedures con nombre $sp_nombre
     *
     * 1) Sintaxis del SQL en la tabla stored_procedures:
     *      Los parámetros deben ser escritos entre #.
     * 2) Recibe un array asociativo con los parámetros del requeridos para el SQL.
     *      - La cantidad de parámetros enviados debe se igual a la cantidad de parámetros.
     *      - Los nombres de los parametros no pueden ser igual al de campos asociados a las tablas
     *        involucradas en el SQL ni pueden ser palabras reservadas del SQL.
     * Ejemplo llamada:
     * 		$param=array("parchivonom" => $_POST['archivonom']);
     *		$query=$conexion->ejecutarStoredProcedure("sel_archivos_xnombre",$param,$resultado,$numfilas,$errno);
     * Ejemplo definicion en la base de stored_procedures:
     *      select archivonom from archivos where archivonom="#parchivonom#"
     *
     * Retorna:
     *		- En un SELECT:
     *			-> si se ejecutó bien retorna el resultado del query y la cantidad de filas
     *			-> en caso contrario, genera una excepción y retorna el numero de error
     *		- En otra operación:
     *			-> si se ejecutó bien retorna la cantidad de filas afectadas
     *			-> en caso contrario, genera una excepción y retorna el numero de error
     *
     * @param string $sp_nombre
     * @param array $sp_param
     * @param mysqli_result|null $resultado_salida
     * @param int|null $numfilas_salida
     * @param int|null $errno_salida
     * @throws ExcepcionDB
     */
    public function ejecutarStoredProcedure(string $sp_nombre, array $sp_param, &$resultado_salida,
                                            ?int &$numfilas_salida, ?int &$errno_salida): void
	{
		$resultado_salida = null;
		$numfilas_salida = -1;
		$errno_salida = 0;


		if (!is_array($sp_param))
		{
            throw new ExcepcionDB('Problema en la llamada al stored procedure, no se envia un array.', 1);
		}

		$sql = sprintf('SELECT * FROM stored_procedures WHERE ucase(spnombre)=\'%s\'', strtoupper($sp_nombre));
		try {
            $this->_EjecutarQuery($sql,'ejecutarStoredProcedure',$resultado,$errno);
        } catch (ExcepcionDB $e)
		{
			throw new ExcepcionDB('Problema en la tabla stored_procedures.', 1);
		}
		// Debe estar en la tabla de stored procedures
		if ($this->ObtenerCantidadDeRegistros($resultado)!=1)
		{
			throw new ExcepcionDB("No se encuentra el procedimiento: $sp_nombre en la tabla stored_procedures.", 1);
		}

		$fila=$this->ObtenerSiguienteRegistro($resultado);

		// Valida que los nombres y cantidad de parametros
		try {
            $this->_ValidarParametros($fila['spsqlstring'],$sp_param);
        } catch (ExcepcionDB $e) {
		    throw $e;
        }

		// Todo es inicialmente valido para la ejecucion del Stored Procedure
		// Reemplazo los parametros enviados en el string SQL
        // echo "<br /> ANTES ".$fila['spsqlstring'];
		try {
			if('BLK' == $fila['spoperacion'])
				$this->_ReemplazarParametrosBulk($fila['spsqlstring'], $sp_param, $sql_sp);
			else
				$this->_ReemplazarParametros($fila['spsqlstring'],$sp_param,$sql_sp);
		}
		catch (Exception $e)
		{
			throw new ExcepcionDB("Error al reemplazar parámetros en $sp_nombre.", 1);
		}
        //echo $sp_nombre."<br>";
		/*if ($sp_nombre=="sel_LogMovimientosNovedades_busqueda_avanzada" )
		{
	        echo $sql_sp.PHP_EOL;
		    die();
		}*/

		try {
			if('BLK' == $fila['spoperacion'])
				$this->_EjecutarMultiplesQueries($sql_sp,"stored procedure ".$fila['spcod']." - ".$fila['spnombre'],$resultado_salida,$errno_salida);
			else
				$this->_EjecutarQuery($sql_sp,"stored procedure ".$fila['spcod']." - ".$fila['spnombre'],$resultado_salida,$errno_salida);
		}
		catch (ExcepcionDB $e)
		{

			if ($this->idConexion->sqlstate!=45000)
				throw new ExcepcionDB('Problema en la ejecución del stored procedure.', 2);

		}

        $numfilas_salida = $fila['spoperacion'] == "SEL" ? $this->ObtenerCantidadDeRegistros($resultado_salida) :
            $this->idConexion->affected_rows;

	}


    /**
     * Ejecuta un query prearmado
     *
     * @param string $sql_sp
     * @param string $operacion
     * @param mysqli_result|null $resultado_salida
     * @param int|null $numfilas_salida
     * @param int|null $errno_salida
     * @throws ExcepcionDB
     */
    public function ejecutarSQL(string $sql_sp, string $operacion, ?mysqli_result &$resultado_salida,
                                ?int &$numfilas_salida, ?int &$errno_salida): void
	{
		$resultado_salida = null;
		$numfilas_salida = -1;
		$errno_salida = 0;

		try {
            $this->_EjecutarQuery($sql_sp,"stored procedure prearmado",$resultado_salida,$errno_salida);
        } catch (ExcepcionDB $e)
		{

			if ($this->idConexion->sqlstate!=45000)
				throw new ExcepcionDB('Problema en la ejecución del stored procedure Prearmado.', 2);

		}

        $numfilas_salida = $operacion == "SEL" ? $this->ObtenerCantidadDeRegistros($resultado_salida) :
            $this->idConexion->affected_rows;

	}


    /**
     * Reemplaza los parametros y devuelve la consulta SQL armada
     *
     * @param string $sp_nombre
     * @param array $sp_param
     * @return string
     * @throws ExcepcionDB
     */
    public function ArmarStoredProcedure(string $sp_nombre, array $sp_param): string
    {

		if (!is_array($sp_param))
		{

			throw new ExcepcionDB('Problema en la llamada al stored procedure, no se envia un array.', 1);
		}

		$sql = sprintf('SELECT * FROM stored_procedures WHERE ucase(spnombre)=\'%s\'', strtoupper($sp_nombre));

		try {
            $this->_EjecutarQuery($sql,'ejecutarStoredProcedure',$resultado,$errno);
        }
		catch (ExcepcionDB $e)
		{
			throw new ExcepcionDB('Problema en la tabla stored_procedures.', 1);
		}

		// Debe estar en la tabla de stored procedures
		if ($this->ObtenerCantidadDeRegistros($resultado)!=1)
		{
			throw new ExcepcionDB("No se encuentra el procedimiento: $sp_nombre.", 1);
		}

		$fila=$this->ObtenerSiguienteRegistro($resultado);

		// Valida que los nombres y cantidad de parametros
        try {
            $this->_ValidarParametros($fila['spsqlstring'],$sp_param);
        } catch (ExcepcionDB $e) {
            throw $e;
        }


		// Todo es inicialmente valido para la ejecucion del Stored Procedure
		// Reemplazo los parametros enviados en el string SQL
        // echo "<br /> ANTES ".$fila['spsqlstring'];
        $this->_ReemplazarParametros($fila['spsqlstring'],$sp_param,$sql);
        return $sql;
	}
//-----------------------------------------------------------------------------------------

    /**
     * Usada para el manejo de transacciones
     *
     * Genera una excepcion si hubo un problema
     *
     * @param string $transaccion_tipo
     * @throws ExcepcionDB
     */
    function ManejoTransacciones(string $transaccion_tipo): void
	{
		switch($transaccion_tipo)
		{
			case 'B': // begin
                if($this->transaccion_activa)
                    throw new ExcepcionDB('No se puede comenzar una nueva transacción.',1);
				try {
                    $this->_EjecutarQuery("BEGIN", 'ManejoTransacciones', $resultado, $errno);
                } catch (ExcepcionDB $e) {
				    throw $e;
                }
                $this->transaccion_activa = true;
				break;
			case 'C': // commit
				try {
				    $this->_EjecutarQuery("COMMIT",'ManejoTransacciones',$resultado,$errno);
                } catch (ExcepcionDB $e) {
				    throw $e;
                }
                $this->transaccion_activa = false;
				break;
			case 'R': // rollback
				try {
				    $this->_EjecutarQuery("ROLLBACK",'ManejoTransacciones',$resultado,$errno);
                } catch (ExcepcionDB $e) {
				    throw $e;
                }
                $this->transaccion_activa = false;
				break;
			default:
				throw new ExcepcionDB('Tipo de transacción no definida.', 1);
				break;
		}
	}
//--------------------------------------------------------------------------

    /**
     * Trae el valor de un campo de un registro de una tabla, según la condición especificada
     *
     * Parámetros:
     *		$array_where: un array (no asociativo) simulando tal cual lo que iría en el where del sql,
     *			aplicándose el escapeado a los elementos impares.
     *
     * Retorna:
     *		Si se produjo un error, genera na excepcion y retorna el numero de error
     *		Si se ejecutó con éxito, retorna el numero de filas encontradas. En caso que el numero de filas
     *			sea mayor a 0, retorna el valor del campo seleccionado de la primer fila.
     * @param string $tabla
     * @param string $campo_nombre
     * @param array $array_where
     * @param string|null $dato
     * @param int|null $numfilas
     * @param int|null $errno
     * @throws ExcepcionDB
     * @todo revisar el armado del where, debería haber una forma mejor de hacerlo
     */
    function TraerCampo(string $tabla, string $campo_nombre, array $array_where, ?string &$dato, ?int &$numfilas,
                        ?int &$errno): void
	{
		$resultado = null;
		$numfilas = -1;
		$errno = 0;

		$sql = "SELECT {$campo_nombre} as campodevuelto  FROM {$tabla}";
		if (count($array_where)>0)
		{
			$sql.= " WHERE ";

			for ($i=0; $i<count($array_where); $i++)
			{
				if ($i%2 == 0)
					$sql.=$array_where[$i];
				else
					$sql.=$this->idConexion->real_escape_string($array_where[$i]);
			}
		}

		try {
            $this->_EjecutarQuery($sql,'TraerCampo',$resultado,$errno);
        } catch (ExcepcionDB $e) {
		    throw new ExcepcionDB('Problema en TraerCampo.', 1);
        }

		$numfilas=$this->ObtenerCantidadDeRegistros($resultado);
		if($numfilas>0)
		{
			$fila=$this->ObtenerSiguienteRegistro($resultado);
			$dato=$fila['campodevuelto'];
		}
	}

//-----------------------------------------------------------------------------------------

    /**
     * Actualiza el valor de un campo de un registro de una tabla, según la condición especificada
     *
     * Retorna:
     *		Si se produjo un error, retorna el numero de error
     *		Si se ejecutó con éxito, retorna  el numero de filas afectadas
     * @param string $tabla
     * @param string $campo_nombre
     * @param string $campo_valor
     * @param array $array_where un array (no asociativo) simulando tal cual lo que iría en el where del sql, aplicándose el escapeado a los elementos impares.
     * @param string $datos_auditoria S/N para determinar si se actualiza o no ultmodusuario/ultmodfecha
     * @param int|null $numfilas
     * @param int|null $errno
     * @throws ExcepcionDB
     */
    function ActualizarCampo(string $tabla, string $campo_nombre, string $campo_valor, array $array_where,
                             string $datos_auditoria, ?int &$numfilas, ?int &$errno): void
	{
		$sql="update $tabla";
		if($campo_valor=="NULL")
			$sql.=" set $campo_nombre=NULL";
		else
			$sql.=" set $campo_nombre='".$this->idConexion->real_escape_string($campo_valor)."'";
		if ($datos_auditoria=="S")
		{
			$sql.=",ultmodusuario=".$_SESSION['usuariocod'];
			$sql.=",ultmodfecha='".date('Y/m/d H:i:s')."'";
		}
		if (count($array_where)>0)
		{
			$sql.=" where ";
			for ($i=0; $i<count($array_where); $i++)
			{
				if ($i%2 == 0)
					$sql.=$array_where[$i];
				else
					$sql.=$this->idConexion->real_escape_string($array_where[$i]);
			}
		}

		try {
		    $this->_EjecutarQuery($sql,'ActualizarCampo',$resultado,$errno);
		}
		catch (ExcepcionDB $e)
		{
			throw new ExcepcionDB('Problema en ActualizarCampo.');
		}

		$numfilas = $this->idConexion->affected_rows;
	}

    /**
     * @param $spnombre
     * @return string
     * @throws ExcepcionDB
     */
    public function buscarStoredProcedure($sp_nombre): string {
        $sql = sprintf('SELECT * FROM stored_procedures WHERE ucase(spnombre)=\'%s\'', strtoupper($sp_nombre));

        try {
            $this->_EjecutarQuery($sql,'ejecutarStoredProcedure',$resultado,$errno);
        }
        catch (ExcepcionDB $e)
        {
            throw new ExcepcionDB('Problema en la tabla stored_procedures.', 1);
        }

        // Debe estar en la tabla de stored procedures
        if ($this->ObtenerCantidadDeRegistros($resultado)!=1)
        {
            throw new ExcepcionDB("No se encuentra el procedimiento: $sp_nombre.", 1);
        }

        $fila=$this->ObtenerSiguienteRegistro($resultado);

        return $fila['spsqlstring'];
    }

//-----------------------------------------------------------------------------------------

    /**
     * Busca en el resultado de la ejecución de un SP, la/s clave/s enviada/s
     *
     * Ejemplo de uso:
     *      $param=['pIdUsuario' => $_SESSION['usuariocod']];
     *      BuscarRegistroxClave('sel_stored_de_ejemplo',$param,["IdEjemplo" => $_POST['IdEjemplo']],$resultado,$fila_retorno,$numfilas_matcheo,$errno)
     *
     *  Retorna:
     *		Si se produjo un error, retorna el numero de error y genera una excepción
     *		Si se ejecutó con éxito:
     *			-> en $resultado, todo el query de la ejecución del SP
     *			-> en $fila_retorno, la fila que cumple las condiciones, en caso que sea la unica de todos los registros del SP
     *			-> en $numfilas_matcheo, la cantidad de filas que cumplen las condiciones
     * @param string $sp_nombre
     * @param array $param
     * @param array $array_busqueda             array asociativo donde la clave será el nombre del campo y el valor el valor buscado.
     * @param mysqli_result|null $resultado
     * @param array|null $fila_retorno
     * @param int|null $numfilas_matcheo
     * @param int|null $errno
     * @throws ExcepcionDB
     */
    function BuscarRegistroxClave(string $sp_nombre, array $param, array $array_busqueda, ?mysqli_result &$resultado,
                                  ?array &$fila_retorno, ?int &$numfilas_matcheo, ?int &$errno): void
	{
        $matcheo = false;
		$fila_retorno=array();

		try {
            $this->ejecutarStoredProcedure($sp_nombre,$param,$resultado,$numfilas,$errno);
        } catch (ExcepcionDB $e) {
		    throw $e;
        }

		$numfilas_matcheo=0;
		while($fila=$this->ObtenerSiguienteRegistro($resultado))
		{
			foreach ($array_busqueda as $clave => $valor)
			{
				$matcheo = true;
				if (!isset($fila[$clave]))
				{
                    throw new ExcepcionDB('Problema en BuscarRegistroxClave.', 1);
				}

				if($fila[$clave]!=$valor)
				{
					$matcheo = false;
					break;
				}
			} // for each arraybusq
			if($matcheo)
			{
				$fila_retorno=$fila;
				$numfilas_matcheo++;
			}
		} // while del query
		if($numfilas_matcheo!=1)
			$fila_retorno = array();

	}
//--------------------------------------------------------------------------

    /**
     * Retorna el texto del último error producido
     * @return string
     */
    function TextoError(): string
	{
		return $this->idConexion->error;
	}

//--------------------------------------------------------------------------

    /**
     * Retorna el código del administrador general
     * @return int|null
     */
    function VerAdmiGeneral(): ?int
	{
		return $this->admin_general;
	}

//--------------------------------------------------------------------------

    /**
     * Setea el código del administrador general
     * @param int $admigeneralcod
     */
	function SetearAdmiGeneral(int $admigeneralcod): void
	{
		$this->admin_general=$admigeneralcod;
	}

//--------------------------------------------------------------------------

    /**
     * Cierra la conexion
     */
	function CerrarConexion(): void
	{
        $this->idConexion->close();
	}

//--------------------------------------------------------------------------

    /**
     * Retorna el ultimo código insertado
     * @return int
     */
    function UltimoCodigoInsertado(): int
	{
		return (int) $this->idConexion->insert_id;
	}

//--------------------------------------------------------------------------

    /**
     * @param string $tabla
     *
     * @return array
     * @throws \Bigtree\ExcepcionDB
     * @todo buscar una forma de hacer esto con mysqli
     */
	function ObtenerCamposTabla(string $tabla): array
	{
        $sql_sp = sprintf('SHOW COLUMNS FROM %s', $tabla);
        $this->ejecutarSQL($sql_sp, 'SEL', $resultado, $numfilas, $errno);
        $estructura = [];
        while ($fila = $this->ObtenerSiguienteRegistro($resultado))
            $estructura[] = $fila;
		return $estructura;
	}

//--------------------------------------------------------------------------


    /**
     * Define el modo de retorno de la consulta
     *
     * @param bool $store
     */
    function StoreMemory(bool $store):void
    {
		$this->store_memory = $store;
	}

//-----------------------------------------------------------------------------------------
//							 PRIVADAS
//-----------------------------------------------------------------------------------------

    /**
     * Ejecuta una instrucción SQL.
     *
     * Retorna:
     *		En caso de error, genera una excepción y retorna el numero de error
     *		Si se ejecutó con éxito, retorna el resultado del query
     * @param string $sql
     * @param string $erroren
     * @param mysqli_result|null $resultado
     * @param int|null $errno
     * @throws ExcepcionDB
     */
    function _EjecutarQuery(string $sql, string $erroren, ?mysqli_result &$resultado, ?int &$errno): void
	{
		$errno = 0;
		if ($this->store_memory)
			$resultado = $this->idConexion->query($sql);
		else
			$resultado = $this->idConexion->query($sql,MYSQLI_USE_RESULT);

		if(!$resultado)
		{
			$errno = $this->idConexion->errno;
			$texto= sprintf("Se ha producido un error en %s - Error en %s\n\nSQL= %s\n\nError Mysql: %s - %s",
                $_SERVER['PHP_SELF'], $erroren, $sql, $errno, $this->idConexion->error);
			throw new ExcepcionDB($texto, 2);
		}
	}

//-----------------------------------------------------------------------------------------

    /**
     * Valida los parametros del SQL contra los enviados
     * @param string $sql
     * @param array $param
     * @throws ExcepcionDB si los parámetros son incorrectos
     */
    function _ValidarParametros(string $sql, array $param):void
    {

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

		// Comparo un arreglo con las claves del array enviado como parámetro
		// con el arreglo que surge del string sql.
		//$param_nomArray=array_keys(array_change_key_case($param,CASE_LOWER));
		$param_nomArray=array_keys($param);

		// No hay parametros en la llamada ni definición del stored procedure
		if (count($param_nomSQL)==0 && count($param_nomArray)==0 )
			return;

		$diff_array1=array_diff($param_nomSQL,$param_nomArray);
		$diff_array2=array_diff($param_nomArray,$param_nomSQL);

		if ( count($diff_array1)!=0  || count($diff_array2)!=0 )
		{
			$texto="Error en el nombre de los parámetros en la llamada al procedimiento.\r\n";
			$texto.="Parámetros que sobran del SQL:\r\n";
			$texto.=var_export($diff_array1,true);
			$texto.="\r\nParámetros que sobran del array de parámetros:\r\n";
			$texto.=var_export($diff_array2,true);
			$texto.="\r\n";

			throw new ExcepcionDB($texto);
		}

		return;
	}

//--------------------------------------------------------------------------

    /**
     * Reemplaza los parámetros en el string SQL
     *
     * Retorna en $sql_resultado el resultado de reemplazar los parámetros del SP con los enviados
     * @param string $sql
     * @param array $param
     * @param string|null $sql_resultado
     */
    function _ReemplazarParametros(string $sql, array $param, ?string &$sql_resultado): void
	{
		// sql separado en piezas pasado o no a minuscula
		//$sql_pieces = explode("#",trim($sql));
	    $sql_pieces_may_y_min = explode("#",(trim($sql)));

		// Los parametros vienen siempre en minuscula
		//$param_nom = array_keys(array_change_key_case($param,CASE_LOWER));
		$param_nom = array_keys($param);

		while (count($param_nom))
		{
			$param_search=array_shift($param_nom);

			// Busco la key en las piezas y lo reemplazo por el valor
            /** @noinspection PhpUnusedLocalVariableInspection */
            foreach ($sql_pieces_may_y_min as $sql_piece)
			{
				$posicion=array_search($param_search,$sql_pieces_may_y_min);
				if ($posicion)
				{
					// busco si el parámetro es null
					if(is_array($param[$param_search]))
					{
						//$sql_pieces[$posicion]="";
						$sql_pieces_may_y_min[$posicion]="";
						$param[$param_search] = '"'.implode('","',$param[$param_search]).'"';
						//$sql_pieces[$posicion]=$param[$param_search];
						$sql_pieces_may_y_min[$posicion]=$param[$param_search];
					}
					elseif(isset($param[$param_search]) && trim($param[$param_search])=="NULL")
					{
						//$sql_pieces[$posicion-1]=substr($sql_pieces[$posicion-1],0,strlen($sql_pieces[$posicion-1])-1);
						//$sql_pieces[$posicion+1]=substr($sql_pieces[$posicion+1],1);
						//$sql_pieces[$posicion]="NULL";

						$sql_pieces_may_y_min[$posicion-1]=substr($sql_pieces_may_y_min[$posicion-1],0,strlen($sql_pieces_may_y_min[$posicion-1])-1);
						$sql_pieces_may_y_min[$posicion+1]=substr($sql_pieces_may_y_min[$posicion+1],1);
						$sql_pieces_may_y_min[$posicion]="NULL";

					}
					else {
                        $param_empty=$param[$param_search];

                        if(isset($param_empty) && trim($param_empty)=="" && strtolower($param_search)!="plimit" && strtolower($param_search)!="porderby")
                        {
                            $sql_pieces_may_y_min[$posicion-1]=substr($sql_pieces_may_y_min[$posicion-1],0,strlen($sql_pieces_may_y_min[$posicion-1])-1);
                            $sql_pieces_may_y_min[$posicion+1]=substr($sql_pieces_may_y_min[$posicion+1],1);
                            $sql_pieces_may_y_min[$posicion]="NULL";
                        }
                        else
                            $sql_pieces_may_y_min[$posicion] = $this->idConexion->real_escape_string($param[$param_search]??'');
					}

				}
			}
		}

	   // Se respeta mayúscula y minúscula del stored, solo se toma en minúscula el nombre de los parametros
	   $sql_resultado=implode("",$sql_pieces_may_y_min);

	}

//--------------------------------------------------------------------------

    /**
     * Reemplaza los parametros en el string SQL
     *
     * Retorna en $sql_resultado el resultado de reemplazar los parametros del SP con los enviados
     * @param string $sql
     * @param array $param
     * @param string|null $sql_resultado
     * @throws ExcepcionDB
     */
    function _ReemplazarParametrosBulk(string $sql, array $param, ?string &$sql_resultado): void
    {
        /** @var string[] $arraySql */
        $arraySql = [];

        $noEsArray = [];
        $ultimoArray = NULL;
	    $cant = 1;
        foreach($param as $item=>$value) {
        	if(is_array($value)) {
		        if (!empty($ultimoArray) && (count($param[$ultimoArray]) !== count($value)))
			        throw new ExcepcionDB('Error, cantidad e parámetros incorrecta');
		        $cant = count($value);
		        $ultimoArray = $item;
	        } else
		        $noEsArray[] = $item;
        }

        foreach($noEsArray as $item)
        	$param[$item] = array_fill(0, $cant, $param[$item]);

        $arrayParam = FuncionesPHPLocal::transpose($param);

        foreach($arrayParam as $param)
            $this->_ReemplazarParametros($sql, $param, $arraySql[]);

        $sql_resultado = implode(';' . PHP_EOL, $arraySql);

    }

//--------------------------------------------------------------------------

    /**
     * Ejecuta una instrucción SQL.
     *
     * Retorna:
     *        En caso de error, genera una excepción y retorna el numero de error
     *        Si se ejecutó con éxito, retorna el resultado del query
     * @param string $sqls
     * @param string $erroren
     * @param array|null $resultados
     * @param int|null $errno
     * @param array|null $filasAfectadas
     * @param array|null $codigosInsertados
     * @throws ExcepcionDB
     * @todo revisar este método
     * @noinspection PhpUnusedParameterInspection
     */
    function _EjecutarMultiplesQueries(string $sqls, string $erroren, ?array &$resultados, ?int &$errno,
                                       ?array &$filasAfectadas=null, ?array &$codigosInsertados=null): void
	{
        $errno = 0;
        $ii = 0;
        $resultados = [];
        $codigosInsertados = [];
        $filasAfectadas = [];
        $ejecuto=$this->idConexion->multi_query($sqls);
        if(!$ejecuto) {
            $errno = $this->idConexion->errno;
            throw new ExcepcionDB($this->idConexion->error, 500);
        }
        $resultados[$ii] = $this->store_memory ? $this->idConexion->store_result() : $this->idConexion->use_result();
        $codigosInsertados[$ii] = $this->UltimoCodigoInsertado();
        $filasAfectadas[$ii] = ($resultados[$ii] instanceof mysqli_result) ? $this->ObtenerCantidadDeRegistros($resultados[$ii]):0;
        while ($this->idConexion->more_results()) {
            $this->idConexion->next_result();
            $resultados[++$ii] = $this->store_memory ? $this->idConexion->store_result() : $this->idConexion->use_result();
            $codigosInsertados[$ii] = $this->UltimoCodigoInsertado();
            $filasAfectadas[$ii] = ($resultados[$ii] instanceof mysqli_result) ? $this->ObtenerCantidadDeRegistros($resultados[$ii]):0;
        } // flush multi_queries

	}

	public function escapearCaracteres(string $str): string {
    	return $this->idConexion->real_escape_string($str);
	}

} // fin clase
