<?php
require_once DIR_LIBRERIAS . 'accesoBDLocal.php';

use Bigtree\ExcepcionDB;

/**
 * Class accesoBDLocal
 *
 * @see Bigtree\accesoBDLocal
 *
 */
class accesoBDLocal
{
    use ManejoErrores;
    private $formato;
    public $parent;

    /**
     * accesoBDLocal constructor.
     * @param string $servidor
     * @param string $usuariodb
     * @param string $clave
     * @param int $puerto
     */
    public function __construct(string $servidor, string $usuariodb, string $clave, int $puerto = 3306)
    {
        if(defined('DBPUERTO'))
            $puerto = DBPUERTO;
        $this->parent = new Bigtree\accesoBDLocal($servidor, $usuariodb, $clave, $puerto);
        $this->formato = defined('FMT_ARRAY') ? FMT_ARRAY : 3;
    }

    /**
     * @param mysqli_result|null $resultado
     * @return array|null
     * @see Bigtree\accesoBDLocal::ObtenerSiguienteRegistro
     */
    public function ObtenerSiguienteRegistro(?mysqli_result $resultado): ?array {
        return $this->parent->ObtenerSiguienteRegistro($resultado);
    }

    /**
     * @param mysqli_result $resultado
     * @return array|null
     * @see Bigtree\accesoBDLocal::ObtenerSiguienteRegistroArray
     */
    public function ObtenerSiguienteRegistroArray(mysqli_result $resultado): ?array {
        return $this->parent->ObtenerSiguienteRegistroArray($resultado);
    }

    /**
     * @param string $string
     * @return string
     * @see Bigtree\accesoBDLocal::EscapeaElString
     */
    public function EscapeaElString(string $string): string {
        return $this->parent->EscapeaElString($string);

    }

    /**
     * @param mysqli_result|null $result
     * @return int
     * @see Bigtree\accesoBDLocal::ObtenerCantidadDeRegistros
     */
    public function ObtenerCantidadDeRegistros(?mysqli_result $result): int {
        return $this->parent->ObtenerCantidadDeRegistros($result);
    }

    /**
     * @param string $sp_nombre
     * @param array $sp_param
     * @return string
     * @throws ExcepcionDB
     * @see Bigtree\accesoBDLocal::ArmarStoredProcedure
     */
    public function ArmarStoredProcedure(string $sp_nombre, array $sp_param): string {
        try {
            return $this->parent->ArmarStoredProcedure($sp_nombre, $sp_param);
        } catch (ExcepcionDB $e) {
            throw $e;
        }
    }

    /**
     * @return string
     * @see Bigtree\accesoBDLocal::TextoError
     */
    public function TextoError(): string {
        return $this->parent->TextoError();
    }

    /**
     * @return int
     * @see Bigtree\accesoBDLocal::VerAdmiGeneral
     */
    public function VerAdmiGeneral(): int {
        return $this->parent->VerAdmiGeneral();
    }

    /**
     * @return int
     * @see Bigtree\accesoBDLocal::UltimoCodigoInsertado
     */
    public function UltimoCodigoInsertado(): int {
        return $this->parent->UltimoCodigoInsertado();
    }

    /**
     * @param string $tabla
     *
     * @return array|false
     * @see Bigtree\accesoBDLocal::ObtenerCamposTabla
     */
    public function ObtenerCamposTabla(string $tabla) {
        try {
            return $this->parent->ObtenerCamposTabla($tabla);
        } catch (ExcepcionDB $e) {
            $this->setError($e->getError());
            return false;
        }
    }

    /**
     * @param string $sp_nombre
     * @param array $sp_param
     * @param mysqli_result|null $resultado_salida
     * @param int|null $numfilas_salida
     * @param int|null $errno_salida
     * @return bool
     * @see Bigtree\accesoBDLocal::ejecutarStoredProcedure
     */
    public function ejecutarStoredProcedure(string $sp_nombre, array $sp_param, &$resultado_salida, ?int &$numfilas_salida, ?int &$errno_salida): bool {
        try {
            $this->parent->ejecutarStoredProcedure($sp_nombre, $sp_param, $resultado_salida, $numfilas_salida, $errno_salida);
        } catch (ExcepcionDB $e) {
            $this->setError($e->getError());
            return false;
        }
        return true;
    }

    /**
     * @param string $sql_sp
     * @param string $operacion
     * @param mysqli_result|null $resultado_salida
     * @param int|null $numfilas_salida
     * @param int|null $errno_salida
     * @return bool
     * @see Bigtree\accesoBDLocal::ejecutarSQL()
     */
    public function ejecutarSQL(string $sql_sp, string $operacion, ?mysqli_result &$resultado_salida, ?int &$numfilas_salida, ?int &$errno_salida): bool {
        try {
            $this->parent->ejecutarSQL($sql_sp, $operacion, $resultado_salida, $numfilas_salida, $errno_salida);
        } catch (ExcepcionDB $e) {
            $this->setError($e->getError());
            return false;
        }
        return true;
    }

    public function buscarStoredProcedure($sp_nombre, &$sql): bool {
        try {
            $sql = $this->parent->buscarStoredProcedure($sp_nombre);
        } catch (ExcepcionDB $e) {
            $this->setError($e->getError());
            return false;
        }

        return true;
    }

    /**
     * @param string $tabla
     * @param string $campo_nombre
     * @param array $array_where
     * @param string|null $dato
     * @param int|null $numfilas
     * @param int|null $errno
     * @return bool
     * @see Bigtree\accesoBDLocal::TraerCampo()
     */
    public function TraerCampo(string $tabla, string $campo_nombre, array $array_where, ?string &$dato, ?int &$numfilas, ?int &$errno): bool {
        try {
            $this->parent->TraerCampo($tabla, $campo_nombre, $array_where, $dato, $numfilas, $errno);
        } catch (ExcepcionDB $e) {
            $this->setError($e->getError());
            return false;
        }
        return true;
    }

    /**
     * @param string $tabla
     * @param string $campo_nombre
     * @param string $campo_valor
     * @param array $array_where
     * @param string $datos_auditoria
     * @param int|null $numfilas
     * @param int|null $errno
     * @return bool
     * @see Bigtree\accesoBDLocal::ActualizarCampo()
     */
    public function ActualizarCampo(string $tabla, string $campo_nombre, string $campo_valor, array $array_where, string $datos_auditoria, ?int &$numfilas, ?int &$errno): bool {
        try {
            $this->parent->ActualizarCampo($tabla, $campo_nombre, $campo_valor, $array_where, $datos_auditoria, $numfilas, $errno);
        } catch (ExcepcionDB $e) {
            $this->setError($e->getError());
            return false;
        }
        return true;
    }

    /**
     * @param string $sp_nombre
     * @param array $param
     * @param array $array_busqueda
     * @param mysqli_result|null $resultado
     * @param array|null $fila_retorno
     * @param int|null $numfilas_matcheo
     * @param int|null $errno
     * @return bool
     * @see Bigtree\accesoBDLocal::BuscarRegistroxClave()
     */
    public function BuscarRegistroxClave(string $sp_nombre, array $param, array $array_busqueda, ?mysqli_result &$resultado, ?array &$fila_retorno, ?int &$numfilas_matcheo, ?int &$errno): bool {
        try {
            $this->parent->BuscarRegistroxClave($sp_nombre, $param, $array_busqueda, $resultado, $fila_retorno, $numfilas_matcheo, $errno);
        } catch (ExcepcionDB $e) {
            $this->setError($e->getError());
            return false;
        }
        return true;
    }

    /**
     * @param string $sql
     * @param string $erroren
     * @param mysqli_result|null $resultado
     * @param int|null $errno
     * @return bool
     * @see Bigtree\accesoBDLocal::_EjecutarQuery()
     */
    public function _EjecutarQuery(string $sql, string $erroren, ?mysqli_result &$resultado, ?int &$errno): bool {
        try {
            $this->parent->_EjecutarQuery($sql, $erroren, $resultado, $errno);
        } catch (ExcepcionDB $e) {
            $this->setError($e->getError());
            return false;
        }
        return true;
    }

    /**
     * @param string $sqls
     * @param string $erroren
     * @param array|null $resultados
     * @param int|null $errno
     * @param array|null $filasAfectadas
     * @param array|null $codigosInsertados
     * @return bool
     * @see Bigtree\accesoBDLocal::_EjecutarMultiplesQueries()
     */
    public function _EjecutarMultiplesQueries(string $sqls, string $erroren, ?array &$resultados, ?int &$errno, ?array &$filasAfectadas=null, ?array &$codigosInsertados=null): bool {
        try {
            $this->parent->_EjecutarMultiplesQueries($sqls, $erroren, $resultados, $errno, $filasAfectadas, $codigosInsertados);
        } catch (ExcepcionDB $e) {
            $this->setError($e->getError());
            return false;
        }
        return true;
    }


    /**
     * @param string $base_datos
     * @see Bigtree\accesoBDLocal::SeleccionBD()
     */
    public function SeleccionBD(string $base_datos): void {
        $this->parent->SeleccionBD($base_datos);
    }

    /**
     * @param mysqli_result $resultado
     * @param int $posicion
     * @see Bigtree\accesoBDLocal::MoverPunteroaPosicion()
     */
    public function MoverPunteroaPosicion(mysqli_result $resultado, int $posicion=0): void {
        $this->parent->MoverPunteroaPosicion($resultado, $posicion);
    }

    /**
     * @param string $transaccion_tipo
     * @return bool
     * @see Bigtree\accesoBDLocal::ManejoTransacciones()
     */
    public function ManejoTransacciones(string $transaccion_tipo): bool {
        try {
            $this->parent->ManejoTransacciones($transaccion_tipo);
        } catch (ExcepcionDB $e) {
            $this->setError($e->getError());
            return false;
        }
        return true;
    }

    /**
     * @param int $admigeneralcod
     * @see Bigtree\accesoBDLocal::SetearAdmiGeneral()
     */
    public function SetearAdmiGeneral(int $admigeneralcod): void {
        $this->parent->SetearAdmiGeneral($admigeneralcod);
    }

    /**
     * @see Bigtree\accesoBDLocal::CerrarConexion()
     */
    public function CerrarConexion(): void {
        $this->parent->CerrarConexion();
    }

    /**
     * @param bool $store
     * @see Bigtree\accesoBDLocal::StoreMemory()
     */
    public function StoreMemory(bool $store): void {
        $this->parent->StoreMemory($store);
    }



	/* ************************************************************************ *
	 *                                                                          *
	 *               Funciones privadas                                         *
	 *                                                                          *
	 * ************************************************************************ */
	/**
	 * @return \Bigtree\accesoBDLocal
	 */
	public function getParent(): \Bigtree\accesoBDLocal
	{
		return $this->parent;
	}

	/**
	 * @param string $sql
	 * @param array $param
	 * @return bool
	 * @see Bigtree\accesoBDLocal::_ValidarParametros()
	 */
	private function _ValidarParametros(string $sql, array $param): bool {
		try {
			$this->parent->_ValidarParametros($sql, $param);
		} catch (ExcepcionDB $e) {
			$this->setError($e->getError());
			return false;
		}
		return true;
	}

	/**
	 * @param string $sql
	 * @param array $param
	 * @param string|null $sql_resultado
	 * @return bool
	 * @see Bigtree\accesoBDLocal::_ReemplazarParametros()
	 */
	public function _ReemplazarParametros(string $sql, array $param, ?string &$sql_resultado): bool {
		try {
			$this->parent->_ReemplazarParametros($sql, $param, $sql_resultado);
		} catch (ExcepcionDB $e) {
			$this->setError($e->getError());
			return false;
		}
		return true;
	}

	/**
	 * @param string $sql
	 * @param array $param
	 * @param string|null $sql_resultado
	 * @return bool
	 * @see Bigtree\accesoBDLocal::_ReemplazarParametrosBulk()
	 */
	private function _ReemplazarParametrosBulk(string $sql, array $param, ?string &$sql_resultado): bool {
		try {
			$this->parent->_ReemplazarParametrosBulk($sql, $param, $sql_resultado);
		} catch (ExcepcionDB $e) {
			$this->setError($e->getError());
			return false;
		}
		return true;
	}

	public function escapearCaracteres(string $str): string {
		return $this->parent->escapearCaracteres($str);
	}

}
