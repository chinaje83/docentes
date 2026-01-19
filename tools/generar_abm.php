<?php

declare(strict_types=1);

function usage(): void {
    $script = basename(__FILE__);
    echo "Uso:\n";
    echo "  php {$script} --schema=archivo.json [--output=/ruta/salida]\n";
    echo "  php {$script} --create=tabla.sql [--modulo=mi_modulo] [--clase=MiClase] [--output=/ruta/salida]\n\n";
    echo "Parametros:\n";
    echo "  --schema   Ruta al JSON con la estructura de la tabla.\n";
    echo "  --create   Ruta al archivo .sql con el CREATE TABLE.\n";
    echo "  --modulo   Nombre del modulo (cuando se usa --create).\n";
    echo "  --clase    Nombre de la clase logica (cuando se usa --create).\n";
    echo "  --output   Carpeta de salida (opcional). Por defecto: abm_generados/<modulo>\n";
}

$options = getopt('', ['schema:', 'create:', 'modulo::', 'clase::', 'output::']);
if (!isset($options['schema']) && !isset($options['create'])) {
    usage();
    exit(1);
}

function fieldExists(array $fields, string $name): bool {
    foreach ($fields as $field) {
        if (($field['nombre'] ?? '') === $name) {
            return true;
        }
    }
    return false;
}

function fieldLabel(array $field): string {
    return $field['label'] ?? $field['nombre'];
}

function fieldInputType(array $field): string {
    $type = strtolower($field['tipo'] ?? 'string');
    if (in_array($type, ['int', 'integer', 'float', 'decimal', 'number'], true)) {
        return 'number';
    }
    if (in_array($type, ['date', 'datetime'], true)) {
        return 'date';
    }
    return 'text';
}

function isRequired(array $field): bool {
    return (bool)($field['requerido'] ?? false);
}

function isAutoIncrement(array $field, string $pk): bool {
    if (($field['nombre'] ?? '') !== $pk) {
        return false;
    }
    return (bool)($field['auto'] ?? true);
}

function sqlEscape(string $value): string {
    return str_replace("'", "''", $value);
}

function normalizeSql(string $sql): string {
    $sql = str_replace(["\r\n", "\r"], "\n", $sql);
    $sql = trim($sql);
    return $sql;
}

function parseCreateTable(string $sql): array {
    $sql = normalizeSql($sql);
    if (!preg_match('/CREATE\\s+TABLE\\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $match)) {
        throw new RuntimeException('No se pudo encontrar el nombre de la tabla en el CREATE TABLE.');
    }
    $table = $match[1];

    $pk = '';
    if (preg_match('/PRIMARY\\s+KEY\\s*\\(`?([a-zA-Z0-9_]+)`?\\)/i', $sql, $pkMatch)) {
        $pk = $pkMatch[1];
    }

    $start = strpos($sql, '(');
    $end = strrpos($sql, ')');
    if ($start === false || $end === false || $end <= $start) {
        throw new RuntimeException('No se pudo interpretar la definicion de columnas.');
    }
    $columnsBlock = substr($sql, $start + 1, $end - $start - 1);
    $lines = array_filter(array_map('trim', explode("\n", $columnsBlock)));

    $fields = [];
    foreach ($lines as $line) {
        $line = rtrim($line, ',');
        if ($line === '') {
            continue;
        }
        if (preg_match('/^(PRIMARY|UNIQUE|KEY|CONSTRAINT)\\s+/i', $line)) {
            continue;
        }
        if (!preg_match('/^`([^`]+)`\\s+([^\\s]+)(.*)$/i', $line, $colMatch)) {
            continue;
        }
        $name = $colMatch[1];
        $typeRaw = strtolower($colMatch[2]);
        $rest = strtolower($colMatch[3] ?? '');

        $baseType = preg_replace('/\\(.*/', '', $typeRaw);
        $baseType = preg_replace('/unsigned$/', '', trim($baseType));

        $tipo = 'string';
        if (in_array($baseType, ['int', 'integer', 'smallint', 'tinyint', 'bigint', 'mediumint'], true)) {
            $tipo = 'int';
        } elseif (in_array($baseType, ['decimal', 'numeric', 'float', 'double'], true)) {
            $tipo = 'decimal';
        } elseif (in_array($baseType, ['date'], true)) {
            $tipo = 'date';
        } elseif (in_array($baseType, ['datetime', 'timestamp'], true)) {
            $tipo = 'datetime';
        }

        $auto = str_contains($rest, 'auto_increment');
        $required = str_contains($rest, 'not null') && !str_contains($rest, 'default') && !$auto;

        $label = $name;
        if (preg_match("/comment\\s+'([^']+)'/i", $colMatch[3] ?? '', $commentMatch)) {
            $label = $commentMatch[1];
        }

        $isAudit = in_array($name, ['AltaFecha', 'AltaUsuario', 'UltimaModificacionUsuario', 'UltimaModificacionFecha'], true);
        $isEstado = $name === 'Estado';

        $fields[] = [
            'nombre' => $name,
            'tipo' => $tipo,
            'label' => $label,
            'auto' => $auto,
            'requerido' => $required,
            'busqueda' => !$isAudit && !$isEstado,
            'listado' => !$isAudit && !$isEstado,
            'form' => !$isAudit && !$isEstado,
        ];
    }

    if ($pk === '') {
        foreach ($fields as $field) {
            if (!empty($field['auto'])) {
                $pk = $field['nombre'];
                break;
            }
        }
    }

    return [
        'tabla' => $table,
        'pk' => $pk,
        'campos' => $fields,
    ];
}

if (isset($options['schema'])) {
    $schemaPath = $options['schema'];
    if (!file_exists($schemaPath)) {
        fwrite(STDERR, "No se encuentra el archivo de esquema: {$schemaPath}\n");
        exit(1);
    }

    $schemaRaw = file_get_contents($schemaPath);
    $schema = json_decode($schemaRaw, true);
    if (!is_array($schema)) {
        fwrite(STDERR, "El esquema JSON es invalido.\n");
        exit(1);
    }
} else {
    $createPath = $options['create'];
    if (!file_exists($createPath)) {
        fwrite(STDERR, "No se encuentra el archivo de CREATE TABLE: {$createPath}\n");
        exit(1);
    }
    $createSql = file_get_contents($createPath);
    try {
        $schema = parseCreateTable($createSql);
    } catch (RuntimeException $exception) {
        fwrite(STDERR, $exception->getMessage() . "\n");
        exit(1);
    }
    $schema['modulo'] = $options['modulo'] ?? strtolower($schema['tabla']);
    $schema['clase'] = $options['clase'] ?? $schema['tabla'];
}

$requiredKeys = ['tabla', 'modulo', 'clase', 'pk', 'campos'];
foreach ($requiredKeys as $key) {
    if (!array_key_exists($key, $schema)) {
        fwrite(STDERR, "Falta la clave obligatoria '{$key}' en el esquema.\n");
        exit(1);
    }
}

if (!is_array($schema['campos']) || count($schema['campos']) === 0) {
    fwrite(STDERR, "'campos' debe ser un arreglo no vacio.\n");
    exit(1);
}

$tabla = $schema['tabla'];
$modulo = $schema['modulo'];
$clase = $schema['clase'];
$pk = $schema['pk'];
$fields = $schema['campos'];

$auditFields = ['AltaFecha', 'AltaUsuario', 'UltimaModificacionUsuario', 'UltimaModificacionFecha'];
$insertFields = array_filter($fields, function (array $field) use ($pk) {
    return !isAutoIncrement($field, $pk);
});
$updateFields = array_filter($fields, function (array $field) use ($pk, $auditFields) {
    if (($field['nombre'] ?? '') === $pk) {
        return false;
    }
    if (in_array($field['nombre'] ?? '', ['AltaFecha', 'AltaUsuario'], true)) {
        return false;
    }
    return true;
});

$root = dirname(__DIR__);
$outputBase = $options['output'] ?? ($root . '/abm_generados/' . $modulo);

$dirs = [
    $outputBase . '/Datos',
    $outputBase . '/Logica',
    $outputBase . '/bt-admin/modulos/' . $modulo . '/js',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        fwrite(STDERR, "No se pudo crear la carpeta {$dir}\n");
        exit(1);
    }
}

$hasEstado = fieldExists($fields, 'Estado');

$insertParams = [];
foreach ($fields as $field) {
    $name = $field['nombre'];
    if (isAutoIncrement($field, $pk)) {
        continue;
    }
    $insertParams[] = "            'p{$name}' => \$datos['{$name}'],";
}
$insertParamsBlock = implode("\n", $insertParams);

$updateParams = [];
foreach ($fields as $field) {
    $name = $field['nombre'];
    if ($name === $pk) {
        continue;
    }
    $updateParams[] = "            'p{$name}' => \$datos['{$name}'],";
}
$updateParams[] = "            'p{$pk}' => \$datos['{$pk}'],";
$updateParamsBlock = implode("\n", $updateParams);

$searchParams = [];
foreach ($fields as $field) {
    $name = $field['nombre'];
    $searchParams[] = "            'px{$name}' => \$datos['x{$name}'],";
    $searchParams[] = "            'p{$name}' => \$datos['{$name}'],";
}
$searchParams[] = "            'plimit' => \$datos['limit'],";
$searchParams[] = "            'porderby' => \$datos['orderby'],";
$searchParamsBlock = implode("\n", $searchParams);

$dbClass = <<<PHP
<?php

use Bigtree\\ExcepcionDB;

abstract class c{$clase}DB {
    /** @var accesoBDLocal */
    protected \$conexion;
    /** @var mixed */
    protected \$formato;
    /** @var array */
    protected \$error;

    function __construct(accesoBDLocal \$conexion, \$formato) {
        \$this->conexion = &\$conexion;
        \$this->formato = &\$formato;
    }

    function __destruct() {}

    public abstract function getError(): array;

    protected function setError(\$error, \$error_description = ''): void {
        \$this->error = is_array(\$error) ? \$error : ['error' => \$error, 'error_description' => \$error_description];
    }

    protected function BuscarxCodigo(array \$datos, &\$resultado, ?int &\$numfilas): bool {
        \$spnombre = "sel_{$tabla}_x{$pk}";
        \$sparam = [
            'p{$pk}' => \$datos['{$pk}'],
        ];
        if (!\$this->conexion->ejecutarStoredProcedure(\$spnombre, \$sparam, \$resultado, \$numfilas, \$errno)) {
            FuncionesPHPLocal::MostrarMensaje(\$this->conexion, MSG_ERRGRAVE, "Error al buscar por codigo.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => \$this->formato]);
            return false;
        }
        return true;
    }

    protected function BusquedaAvanzada(array \$datos, &\$resultado, ?int &\$numfilas): bool {
        \$spnombre = "sel_{$tabla}_busqueda_avanzada";
        \$sparam = [
{$searchParamsBlock}
        ];
        if (!\$this->conexion->ejecutarStoredProcedure(\$spnombre, \$sparam, \$resultado, \$numfilas, \$errno)) {
            FuncionesPHPLocal::MostrarMensaje(\$this->conexion, MSG_ERRGRAVE, "Error al realizar la busqueda avanzada.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => \$this->formato]);
            return false;
        }
        return true;
    }

    protected function Insertar(array \$datos, ?int &\$codigoInsertado): bool {
        \$spnombre = "ins_{$tabla}";
        \$sparam = [
{$insertParamsBlock}
        ];
        if (!\$this->conexion->ejecutarStoredProcedure(\$spnombre, \$sparam, \$resultado, \$numfilas, \$errno)) {
            FuncionesPHPLocal::MostrarMensaje(\$this->conexion, MSG_ERRGRAVE, "Error al insertar.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => \$this->formato]);
            return false;
        }
        \$codigoInsertado = \$this->conexion->UltimoCodigoInsertado();
        return true;
    }

    protected function Modificar(array \$datos): bool {
        \$spnombre = "upd_{$tabla}_x{$pk}";
        \$sparam = [
{$updateParamsBlock}
        ];
        if (!\$this->conexion->ejecutarStoredProcedure(\$spnombre, \$sparam, \$resultado, \$numfilas, \$errno)) {
            FuncionesPHPLocal::MostrarMensaje(\$this->conexion, MSG_ERRGRAVE, "Error al modificar.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => \$this->formato]);
            return false;
        }
        return true;
    }

    protected function Eliminar(array \$datos): bool {
        \$spnombre = "del_{$tabla}_x{$pk}";
        \$sparam = [
            'p{$pk}' => \$datos['{$pk}'],
        ];
        if (!\$this->conexion->ejecutarStoredProcedure(\$spnombre, \$sparam, \$resultado, \$numfilas, \$errno)) {
            FuncionesPHPLocal::MostrarMensaje(\$this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => \$this->formato]);
            return false;
        }
        return true;
    }

PHP;

if ($hasEstado) {
    $dbClass .= <<<PHP
    protected function ModificarEstado(array \$datos): bool {
        \$spnombre = "upd_{$tabla}_Estado_x{$pk}";
        \$sparam = [
            'pEstado' => \$datos['Estado'],
            'p{$pk}' => \$datos['{$pk}'],
        ];
        if (!\$this->conexion->ejecutarStoredProcedure(\$spnombre, \$sparam, \$resultado, \$numfilas, \$errno)) {
            FuncionesPHPLocal::MostrarMensaje(\$this->conexion, MSG_ERRGRAVE, "Error al modificar el estado.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => \$this->formato]);
            return false;
        }
        return true;
    }

PHP;
}

$dbClass .= "}\n";

$requiredFields = [];
foreach ($fields as $field) {
    if (isRequired($field) && ($field['nombre'] ?? '') !== $pk) {
        $requiredFields[] = $field['nombre'];
    }
}

$validations = [];
foreach ($requiredFields as $fieldName) {
    $validations[] = "        if (!isset(\$datos['{$fieldName}']) || \$datos['{$fieldName}'] === '') {\n            \$this->setError('{$fieldName}', 'El campo {$fieldName} es obligatorio.');\n            return false;\n        }";
}
if (count($validations) === 0) {
    $validations[] = "        return true;";
}
$validationsBlock = implode("\n", $validations);

$setNullLines = [];
foreach ($fields as $field) {
    $name = $field['nombre'];
    $setNullLines[] = "        if (array_key_exists('{$name}', \$datos) && \$datos['{$name}'] === '') {\n            \$datos['{$name}'] = null;\n        }";
}
$setNullBlock = implode("\n", $setNullLines);

$auditAssignments = [];
if (fieldExists($fields, 'AltaFecha')) {
    $auditAssignments[] = "        \$datos['AltaFecha'] = date('Y-m-d H:i:s');";
}
if (fieldExists($fields, 'AltaUsuario')) {
    $auditAssignments[] = "        \$datos['AltaUsuario'] = \$_SESSION['usuariocod'];";
}
if (fieldExists($fields, 'UltimaModificacionUsuario')) {
    $auditAssignments[] = "        \$datos['UltimaModificacionUsuario'] = \$_SESSION['usuariocod'];";
}
if (fieldExists($fields, 'UltimaModificacionFecha')) {
    $auditAssignments[] = "        \$datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');";
}
if ($hasEstado) {
    $auditAssignments[] = "        \$datos['Estado'] = ACTIVO;";
}

$updateAuditAssignments = [];
if (fieldExists($fields, 'UltimaModificacionUsuario')) {
    $updateAuditAssignments[] = "        \$datos['UltimaModificacionUsuario'] = \$_SESSION['usuariocod'];";
}
if (fieldExists($fields, 'UltimaModificacionFecha')) {
    $updateAuditAssignments[] = "        \$datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');";
}

$logicClass = <<<PHP
<?php

include(DIR_CLASES_DB . "c{$clase}.db.php");

class c{$clase} extends c{$clase}DB {
    function __construct(accesoBDLocal \$conexion, \$formato = FMT_TEXTO) {
        parent::__construct(\$conexion, \$formato);
    }

    function __destruct() {
        parent::__destruct();
    }

    public function getError(): array {
        return \$this->error;
    }

    public function BuscarxCodigo(\$datos, &\$resultado, &\$numfilas): bool {
        if (!parent::BuscarxCodigo(\$datos, \$resultado, \$numfilas)) {
            return false;
        }
        return true;
    }

    public function BusquedaAvanzada(\$datos, &\$resultado, &\$numfilas): bool {
        \$sparam = [
PHP;

foreach ($fields as $field) {
    $name = $field['nombre'];
    $logicClass .= "            'x{$name}' => 0,\n";
    $logicClass .= "            '{$name}' => '',\n";
}
$logicClass .= "            'limit' => '',\n            'orderby' => '{$pk} DESC',\n        ];\n";

foreach ($fields as $field) {
    $name = $field['nombre'];
    $logicClass .= "        if (isset(\$datos['{$name}']) && \$datos['{$name}'] !== '') {\n            \$sparam['{$name}'] = \$datos['{$name}'];\n            \$sparam['x{$name}'] = 1;\n        }\n";
}

$logicClass .= <<<PHP
        if (isset(\$datos['orderby']) && \$datos['orderby'] !== '') {
            \$sparam['orderby'] = \$datos['orderby'];
        }
        if (isset(\$datos['limit']) && \$datos['limit'] !== '') {
            \$sparam['limit'] = \$datos['limit'];
        }
        if (!parent::BusquedaAvanzada(\$sparam, \$resultado, \$numfilas)) {
            return false;
        }
        return true;
    }

    public function Insertar(\$datos, &\$codigoInsertado): bool {
        if (!\$this->_ValidarInsertar(\$datos)) {
            return false;
        }
        \$this->_SetearNull(\$datos);
PHP;

if (count($auditAssignments) > 0) {
    $logicClass .= implode("\n", $auditAssignments) . "\n";
}

$logicClass .= <<<PHP
        if (!parent::Insertar(\$datos, \$codigoInsertado)) {
            return false;
        }
        return true;
    }

    public function Modificar(\$datos): bool {
        if (!\$this->_ValidarModificar(\$datos)) {
            return false;
        }
        \$this->_SetearNull(\$datos);
PHP;

if (count($updateAuditAssignments) > 0) {
    $logicClass .= implode("\n", $updateAuditAssignments) . "\n";
}

$logicClass .= <<<PHP
        if (!parent::Modificar(\$datos)) {
            return false;
        }
        return true;
    }

    public function Eliminar(\$datos): bool {
        if (!parent::Eliminar(\$datos)) {
            return false;
        }
        return true;
    }
PHP;

if ($hasEstado) {
    $logicClass .= <<<PHP

    public function Activar(\$datos): bool {
        \$datos['Estado'] = ACTIVO;
        if (!parent::ModificarEstado(\$datos)) {
            return false;
        }
        return true;
    }

    public function DesActivar(\$datos): bool {
        \$datos['Estado'] = NOACTIVO;
        if (!parent::ModificarEstado(\$datos)) {
            return false;
        }
        return true;
    }
PHP;
}

$logicClass .= <<<PHP

    private function _ValidarInsertar(\$datos): bool {
{$validationsBlock}
    }

    private function _ValidarModificar(\$datos): bool {
        if (!isset(\$datos['{$pk}']) || \$datos['{$pk}'] === '') {
            \$this->setError('{$pk}', 'El codigo es obligatorio.');
            return false;
        }
{$validationsBlock}
    }

    private function _SetearNull(&\$datos): void {
{$setNullBlock}
    }
}
PHP;

$searchInputs = '';
foreach ($fields as $field) {
    if ($field['nombre'] === $pk) {
        continue;
    }
    if (isset($field['busqueda']) && !$field['busqueda']) {
        continue;
    }
    $label = fieldLabel($field);
    $inputType = fieldInputType($field);
    $fieldName = $field['nombre'];
    $searchInputs .= sprintf(<<<HTML
                <div class="col-md-3">
                    <div class="form-group clearfix">
                        <input name="%s" placeholder="%s" id="%s" class="form-control input-md" type="%s" maxlength="255" value="<?php echo (isset(\$_SESSION['BusquedaAvanzada']['%s'])) ? FuncionesPHPLocal::HtmlspecialcharsSistema(\$_SESSION['BusquedaAvanzada']['%s'],ENT_QUOTES) : ''; ?>" />
                        <span class="bar"></span>
                        <label for="%s">%s:</label>
                    </div>
                </div>

HTML,
        $fieldName,
        $label,
        $fieldName,
        $inputType,
        $fieldName,
        $fieldName,
        $fieldName,
        $label
    );
}

$listPage = <<<PHP
<?php
require("./config/include.php");
require_once(DIR_CLASES_LOGICA.'c{$clase}.class.php');

\$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
\$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes(\$conexion,array("roles"=>"si","sistema"=>SISTEMA));
\$conexion->SetearAdmiGeneral(ADMISITE);

\$sesion = new Sesion(\$conexion,false);
// \$sesion->TienePermisos(\$conexion,\$_SESSION['usuariocod'],\$_SESSION['rolcod'],\$_SERVER['PHP_SELF']);

\$oSistemaBloqueo = new SistemaBloqueo();
\$oSistemaBloqueo->VerificarBloqueo(\$conexion);

\$oEncabezados = new cEncabezados(\$conexion);
\$oEncabezados->CargarPreload();
\$oEncabezados->setTitle("{$clase}");
\$oEncabezados->addScript("modulos/{$modulo}/js/{$modulo}.js?v=1.0");
\$oEncabezados->EncabezadoMenuEmergente(\$_SESSION['rolcod'],\$_SESSION['usuariocod']);

?>
<div class="card">
<div class="card-body">

    <form action="{$modulo}.php" method="post" name="formbusqueda" class="floating-labels mt-3" id="formbusqueda">
        <div class="search-filters">
            <div class="row">
{$searchInputs}
                <input type="hidden" name="Estado" id="Estado" value="<?php echo ACTIVO.','.NOACTIVO ?>" />
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-8">
                    <a class="btn btn-primary" href="javascript:void(0)" id="btnBuscar">Buscar</a>
                    <a class="btn btn-secondary" href="javascript:void(0)" id="btnLimpiar">Limpiar</a>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-4">
                    <div class="float-right"><a class="btn btn-success" href="{$modulo}_am.php"><i class="mdi mdi-plus-circle"></i>&nbsp;Crear nuevo</a>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="card-body" id="LstDatos">
    <table id="listarDatos"></table>
    <div id="pager2"></div>
</div>
</div>
<?php
\$oEncabezados->PieMenuEmergente();
?>
PHP;

$formInputs = '';
foreach ($fields as $field) {
    $name = $field['nombre'];
    if ($name === $pk) {
        continue;
    }
    if (isset($field['form']) && !$field['form']) {
        continue;
    }
    $label = fieldLabel($field);
    $inputType = fieldInputType($field);
    $required = isRequired($field) ? 'required' : '';
    $formInputs .= sprintf(<<<HTML
                    <div class="col-md-4">
                        <div class="form-group clearfix">
                            <label for="%s">%s</label>
                            <input type="%s" class="form-control input-md" name="%s" id="%s" value="<?php echo FuncionesPHPLocal::HtmlspecialcharsSistema($%s ?? '',ENT_QUOTES) ?>" %s />
                        </div>
                    </div>

HTML,
        $name,
        $label,
        $inputType,
        $name,
        $name,
        $name,
        $required
    );
}

$amPage = <<<PHP
<?php
require("./config/include.php");
require_once(DIR_CLASES_LOGICA.'c{$clase}.class.php');

\$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
\$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes(\$conexion,array("roles"=>"si","sistema"=>SISTEMA));
\$conexion->SetearAdmiGeneral(ADMISITE);

\$sesion = new Sesion(\$conexion,false);
// \$sesion->TienePermisos(\$conexion,\$_SESSION['usuariocod'],\$_SESSION['rolcod'],\$_SERVER['PHP_SELF']);

\$oSistemaBloqueo = new SistemaBloqueo();
\$oSistemaBloqueo->VerificarBloqueo(\$conexion);

\$oEncabezados = new cEncabezados(\$conexion);
\$oEncabezados->CargarPreload();
\$oEncabezados->setTitle("{$clase}");
\$oEncabezados->addScript("modulos/{$modulo}/js/{$modulo}_am.js?v=1.0");
\$oEncabezados->EncabezadoMenuEmergente(\$_SESSION['rolcod'],\$_SESSION['usuariocod']);

\$oObjeto = new c{$clase}(\$conexion);

\$esmodif = false;
\$btn = "BtnInsertar";
\${$pk} = "";
PHP;

foreach ($fields as $field) {
    $name = $field['nombre'];
    if ($name === $pk) {
        continue;
    }
    $amPage .= "\${$name} = \"\";\n";
}

$amPage .= <<<PHP
if (isset(\$_GET['{$pk}']) && \$_GET['{$pk}']!="")
{
    \$esmodif = true;
    \$datos = \$_GET;
    if(!\$oObjeto->BuscarxCodigo(\$datos,\$resultado,\$numfilas))
        return false;
    if(\$numfilas!=1){
        FuncionesPHPLocal::MostrarMensaje(\$conexion,MSG_ERRGRAVE,"Codigo inexistente.",["archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__],["formato"=>FMT_TEXTO]);
        return false;
    }
    \$datosregistro = \$conexion->ObtenerSiguienteRegistro(\$resultado);
    \$btn = "BtnModificar";
    \${$pk} = \$datosregistro["{$pk}"];
PHP;

foreach ($fields as $field) {
    $name = $field['nombre'];
    if ($name === $pk) {
        continue;
    }
    $amPage .= "    \${$name} = \$datosregistro[\"{$name}\"];\n";
}

$amPage .= <<<PHP
}
?>

<div class="card">
<div class="card-body">
    <div class="form row">
        <div class="col-md-12">
            <form class="form-material" action="{$modulo}" method="post" name="formalta" id="formalta">
                <input type="hidden" name="{$pk}" id="{$pk}" value="<?php echo \${$pk}; ?>" />
                <div class="row">
{$formInputs}
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button class="btn btn-success" type="button" id="<?php echo \$btn; ?>">Guardar</button>
                        <?php if (\$esmodif) { ?>
                            <button class="btn btn-danger" type="button" id="BtnEliminar">Eliminar</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<?php
\$oEncabezados->PieMenuEmergente();
?>
PHP;

$listColumns = [];
foreach ($fields as $field) {
    if (isset($field['listado']) && !$field['listado']) {
        continue;
    }
    $listColumns[] = $field;
}
if (count($listColumns) === 0) {
    $listColumns = $fields;
}

$colNames = [];
$colModels = [];
foreach ($listColumns as $field) {
    $label = fieldLabel($field);
    $name = $field['nombre'];
    $colNames[] = "'{$label}'";
    $colModels[] = "{name:'{$name}',index:'{$name}', align:'left', width:40}";
}
if ($hasEstado) {
    $colNames[] = "'Estado'";
    $colModels[] = "{name:'act',index:'act', width:35, align:'center', sortable:false}";
}
$colNames[] = "'Editar'";
$colModels[] = "{name:'edit',index:'edit', width:35, align:'center', sortable:false}";
$colNamesBlock = implode(",\n            ", $colNames);
$colModelsBlock = implode(",\n            ", $colModels);

$jsList = <<<JS
jQuery(function($){
    if (typeof $.fn.serializeObject !== "function") {
        $.fn.serializeObject = function() {
            var obj = {};
            $.each(this.serializeArray(), function() {
                if (obj[this.name] !== undefined) {
                    if (!Array.isArray(obj[this.name])) {
                        obj[this.name] = [obj[this.name]];
                    }
                    obj[this.name].push(this.value || "");
                } else {
                    obj[this.name] = this.value || "";
                }
            });
            return obj;
        };
    }
});

jQuery(document).ready(function(){
    listar();
    $(document).on('click', '#btnBuscar', function () {
        gridReload(1);
    });
    $(document).on('click', '#btnLimpiar', function () {
        Resetear();
    });
});

var timeoutHnd;
function gridReload(page){
    var datos = $("#formbusqueda").serializeObject();
    jQuery("#listarDatos").jqGrid('setGridParam', {url:"{$modulo}_lst_ajax.php?rand="+Math.random(), postData: datos,page:page}).trigger("reloadGrid");
}
function Resetear(){
JS;

foreach ($fields as $field) {
    $name = $field['nombre'];
    $jsList .= "    \$(\"#{$name}\").val(\"\");\n";
}

$jsList .= <<<JS
    timeoutHnd = setTimeout(function() {gridReload(1);},500);
}

function listar(){
    var datos = $("#formbusqueda").serializeObject();
    jQuery("#listarDatos").jqGrid(
    {
        url:'{$modulo}_lst_ajax.php?rand='+Math.random(),
        postData: datos,
        datatype: "json",
        colNames:[
            {$colNamesBlock}
        ],
        colModel:[
            {$colModelsBlock}
        ],
        rowNum:20,
        ajaxGridOptions: {cache: false},
        rowList:[20,40,60],
        mtype: "POST",
        pager: '#pager2',
        sortname: '{$pk}',
        viewrecords: true,
        sortorder: "DESC",
        styleUI:'Bootstrap4',
        iconSet:'fontAwesome',
        height:390,
        caption:"",
        responsive:true,
        autowidth: true,
        emptyrecords: "Sin datos para mostrar."
    });

    $(window).bind('resize', function() {
        $("#listarDatos").setGridWidth($("#LstDatos").width());
    }).trigger('resize');

    jQuery("#listarDatos").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false,refresh:false});
}

function ActivarDesactivar(codigo,tipo){
    var param;
    $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',baseZ: 9999999999 })
    param = "{$pk}="+codigo;
    param += "&accion="+tipo;
    EnviarDatos(param);
}

function EnviarDatos(param){
    $.ajax({
        type: "POST",
        url: "{$modulo}_upd.php",
        data: param,
        dataType:"json",
        success: function(msg){
            if (msg.IsSucceed==true)
            {
                swal({
                    title: msg.Msg,
                    text: "Operaci\u00F3n finalizada",
                    confirmButtonColor: "#8bc71b",
                    confirmButtonText: "Ok",
                    type: "success"
                });
                var currentPageVar = jQuery("#listarDatos").getGridParam('page');
                gridReload(currentPageVar);
                $.unblockUI();
            }
            else
            {
                alert(msg.Msg);
                $.unblockUI();
            }
        }
    });
}
JS;

$jsForm = <<<JS
jQuery(document).ready(function(){
    $(document).on('click', '#BtnInsertar', function () {
        Insertar();
    });
    $(document).on('click', '#BtnModificar', function () {
        Modificar();
    });
    $(document).on('click', '#BtnEliminar', function () {
        var  Codigo = $("#formalta #{$pk}").val();
        Eliminar(Codigo);
    });
});

function Insertar(){
    var param;
    $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Agregando...</h1>',baseZ: 9999999999 });
    param = $("#formalta").serialize();
    param += "&accion=1";
    enviarDatosInsertarModificar(param,1);
    return true;
}

function Modificar(){
    var param;
    $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',baseZ: 9999999999 });
    param = $("#formalta").serialize();
    param += "&accion=2";
    enviarDatosInsertarModificar(param,2);
    return true;
}

function Eliminar(codigo){
    var param;
    swal({
        title: "Eliminar",
        text: "Est\u00E1 seguro que desea eliminar?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        confirmButtonColor: "#DD6B55",
        cancelButtonText: "No, cancelar!"
    }).then(result => {if (result.value) {
        param = "{$pk}="+ codigo;
        param += "&accion=3";
        $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Eliminando...</h1>',baseZ: 9999999999 });
        enviarDatosInsertarModificar(param,3);}
    });
}

function enviarDatosInsertarModificar(param,tipo){
    $.ajax({
        type: "POST",
        url: "{$modulo}_upd.php",
        data: param,
        dataType:"json",
        success: function(msg){
            if (msg.IsSucceed==true)
            {
                $.unblockUI();
                if(tipo===1)
                {
                    swal({
                        title: "Ha generado correctamente",
                        text: "Aguarde unos segundos mientras lo redireccionamos.",
                        type: "success",
                        showCancelButton: false,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(result => {
                        $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recargando...</h1>',baseZ: 9999999999 });
                        window.location=msg.header;
                    });
                }
                if(tipo===2)
                {
                    swal({
                        title: "Sus datos han sido modificados con exito",
                        text: "Operaci\u00F3n finalizada",
                        confirmButtonColor: "#8bc71b",
                        confirmButtonText: "Ok",
                        type: "success"
                    });
                }
                if(tipo===3)
                {
                    swal({
                        title: "Ha eliminado correctamente",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#8bc71b",
                        confirmButtonText: "Ok"
                    }).then(result => {if (result.value) {
                        $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recargando...</h1>',baseZ: 9999999999 });
                        window.location=msg.header;}
                    });
                }
            }
            else
            {
                swal({
                    title: "Error",
                    text: msg.Msg,
                    type: "error"
                });
                $.unblockUI();
            }
        }
    });
}
JS;

$listAjaxRows = '';
foreach ($listColumns as $field) {
    $name = $field['nombre'];
    $listAjaxRows .= "\t\t\tutf8_encode(FuncionesPHPLocal::HtmlspecialcharsSistema(\$fila['{$name}'],ENT_QUOTES)),\n";
}

if ($hasEstado) {
    $listAjaxRows .= "\t\t\t\$linkestado,\n";
}
$listAjaxRows .= "\t\t\t\$linkedit\n";

$listAjax = <<<PHP
<?php
require("./config/include.php");
require_once(DIR_CLASES_LOGICA.'c{$clase}.class.php');
\$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
\$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes(\$conexion,array("roles"=>"si","sistema"=>SISTEMA));
\$sesion = new Sesion(\$conexion,false);
// \$sesion->TienePermisos(\$conexion,\$_SESSION['usuariocod'],\$_SESSION['rolcod'],\$_SERVER['PHP_SELF']);

\$oSistemaBloqueo = new SistemaBloqueo();
\$oSistemaBloqueo->VerificarBloqueo(\$conexion);

\$oObjeto = new c{$clase}(\$conexion,"");
header('Content-Type: text/html; charset=iso-8859-1');
if (isset (\$_POST['page']))
    \$page = \$_POST['page'];
else
    \$page = 1;

if (isset (\$_POST['rows']))
    \$limit = \$_POST['rows'];
else
    \$limit = 1;

\$sidx = "{$pk}";
\$sord = "DESC";

\$datos = \$_SESSION['BusquedaAvanzada'] = \$_POST;

if(!\$oObjeto->BusquedaAvanzada (\$datos,\$resultado,\$numfilas))
    die();

if (isset (\$_POST['sord']))
    \$sord = \$_POST['sord'];
if (isset (\$_POST['sidx']))
    \$sidx = \$_POST['sidx'];
\$count = \$numfilas;
if( \$count >0 )
    \$total_pages = ceil(\$count/\$limit);
else
    \$total_pages = 0;

if( \$page > \$total_pages )
    \$page = \$total_pages;

if( \$limit<0 )
    \$limit = 0;

\$start = \$limit*\$page - \$limit; if( \$start<0 )
    \$start = 0;

\$datos['orderby'] = \$sidx." ".\$sord;
\$datos['limit'] = "LIMIT ".\$start." , ".\$limit;

if(!\$oObjeto->BusquedaAvanzada (\$datos,\$resultado,\$numfilas))
    die();

\$i = 0;
\$responce = new StdClass;
\$responce->page = \$page;
\$responce->total = \$total_pages;
\$responce->records = \$count;
\$responce->rows = array();
while (\$fila = \$conexion->ObtenerSiguienteRegistro(\$resultado))
{
    \$linkedit = '<a class="btn btn-sm btn-info" href="{$modulo}_am.php?{$pk}='.\$fila["{$pk}"].'" title="Editar" id="editar_'.\$fila['{$pk}'].'"><i class="fas fa-edit" aria-hidden="true"></i>&nbsp;Editar</a>';
PHP;

if ($hasEstado) {
    $listAjax .= <<<PHP
    \$tipoactivacion = 5;
    \$class = "btn-default";
    \$classInactivo = "btn-danger disabled";
    \$style= "";
    \$checked= "";
    if (\$fila['Estado']==ACTIVO)
    {
        \$style= 'style="color:#FFF"';
        \$checked= "checked='checked'";
        \$tipoactivacion = 4;
        \$class = "btn-success disabled";
        \$classInactivo = "btn-default";
    }

    \$linkestado = '<div class="onoffswitch">';
    \$linkestado .= '<input type="checkbox" '.\$checked.' onclick="ActivarDesactivar('.\$fila['{$pk}'].',' .\$tipoactivacion. ')" name="opcion_'.\$fila['{$pk}'].'" class="onoffswitch-checkbox" id="opcion_'.\$fila['{$pk}'].'">';
    \$linkestado .= '<label class="onoffswitch-label" for="opcion_'.\$fila['{$pk}'].'">';
    \$linkestado .= '<span class="onoffswitch-inner"></span>';
    \$linkestado .= '<span class="onoffswitch-switch"></span>';
    \$linkestado .= '</label>';
    \$linkestado .= '</div>';

PHP;
}

$listAjax .= <<<PHP
    \$datosmostrar = array(
{$listAjaxRows}
    );
    \$responce->rows[\$i]['{$pk}'] = \$fila['{$pk}'];
    \$responce->rows[\$i]['id'] = \$fila['{$pk}'];
    \$responce->rows[\$i]['cell'] = \$datosmostrar;
    \$i++;
}

echo json_encode(\$responce);
?>
PHP;

$updatePage = <<<PHP
<?php
ob_start();
require("./config/include.php");
require_once(DIR_CLASES_LOGICA.'c{$clase}.class.php');

\$conexion = new accesoBDLocal(SERVIDORBD,USUARIOBD,CLAVEBD);
\$conexion->SeleccionBD(BASEDATOS);

FuncionesPHPLocal::CargarConstantes(\$conexion,array("roles"=>"si","sistema"=>SISTEMA));
\$conexion->SetearAdmiGeneral(ADMISITE);

\$sesion = new Sesion(\$conexion,false);
// \$sesion->TienePermisos(\$conexion,\$_SESSION['usuariocod'],\$_SESSION['rolcod'],\$_SERVER['PHP_SELF']);

\$oSistemaBloqueo = new SistemaBloqueo();
\$oSistemaBloqueo->VerificarBloqueo(\$conexion);

header('Content-Type: text/html; charset=iso-8859-1');
\$_POST=FuncionesPHPLocal::DecodificarUtf8 (\$_POST);
\$msg = array();
\$msg['IsSucceed'] = false;
\$datos = \$_POST;
\$conexion->ManejoTransacciones("B");

if (!isset(\$datos['accion']) || \$datos['accion']=="")
{
    \$msg['Msg'] = "Error al procesar";
    echo json_encode(\$msg);
    ob_end_flush();
    die();
}
\$oObjeto = new c{$clase}(\$conexion,"");

switch(\$datos['accion'])
{
    case 1:
        if(\$oObjeto->Insertar(\$datos,\$codigoInsertado))
        {
            \$msg['IsSucceed'] = true;
            \$msg['Msg'] = "Se ha agregado correctamente a las ".date("H").":".date("i")."Hs";
            \$msg['{$pk}'] = \$codigoInsertado;
            \$msg['header'] = "{$modulo}_am.php?{$pk}=".\$codigoInsertado;
        }
    break;
    case 2:
        if(\$oObjeto->Modificar(\$datos))
        {
            \$msg['IsSucceed'] = true;
            \$msg['Msg'] = "Se ha modificado correctamente a las ".date("H").":".date("i")."Hs";
        }
    break;
    case 3:
        if(\$oObjeto->Eliminar(\$datos))
        {
            \$msg['IsSucceed'] = true;
            \$msg['Msg'] = "Se ha eliminado correctamente a las ".date("H").":".date("i")."Hs";
            \$msg['header'] = "{$modulo}.php";
        }
    break;
PHP;

if ($hasEstado) {
    $updatePage .= <<<PHP
    case 4:
        if(\$oObjeto->DesActivar(\$datos))
        {
            \$msg['IsSucceed'] = true;
            \$msg['Msg'] = "Se ha desactivado correctamente";
        }
    break;
    case 5:
        if(\$oObjeto->Activar(\$datos))
        {
            \$msg['IsSucceed'] = true;
            \$msg['Msg'] = "Se ha activado correctamente";
        }
    break;
PHP;
}

$updatePage .= <<<PHP
}

if (\$msg['IsSucceed'])
    \$conexion->ManejoTransacciones("C");
else
{
    \$msg['Msg'] = utf8_encode(ob_get_contents());
    \$conexion->ManejoTransacciones("R");
}
ob_clean();
echo json_encode(\$msg);
ob_end_flush();
PHP;

file_put_contents($outputBase . '/Datos/c' . $clase . '.db.php', $dbClass);
file_put_contents($outputBase . '/Logica/c' . $clase . '.class.php', $logicClass);
file_put_contents($outputBase . '/bt-admin/' . $modulo . '.php', $listPage);
file_put_contents($outputBase . '/bt-admin/' . $modulo . '_am.php', $amPage);
file_put_contents($outputBase . '/bt-admin/' . $modulo . '_lst_ajax.php', $listAjax);
file_put_contents($outputBase . '/bt-admin/' . $modulo . '_upd.php', $updatePage);
file_put_contents($outputBase . '/bt-admin/modulos/' . $modulo . '/js/' . $modulo . '.js', $jsList);
file_put_contents($outputBase . '/bt-admin/modulos/' . $modulo . '/js/' . $modulo . '_am.js', $jsForm);

$spTable = strtoupper($tabla);
$spInserts = [];
$spInserts[] = [
    'spnombre' => "sel_{$tabla}_x{$pk}",
    'spoperacion' => 'SEL',
    'sptabla' => $spTable,
    'spsqlstring' => "SELECT * FROM {$tabla} WHERE {$pk}=\"#p{$pk}#\"",
];
$spInserts[] = [
    'spnombre' => "del_{$tabla}_x{$pk}",
    'spoperacion' => 'DEL',
    'sptabla' => $spTable,
    'spsqlstring' => "DELETE FROM {$tabla} WHERE {$pk}=\"#p{$pk}#\"",
];
if ($hasEstado) {
    $spInserts[] = [
        'spnombre' => "upd_{$tabla}_Estado_x{$pk}",
        'spoperacion' => 'UPD',
        'sptabla' => $spTable,
        'spsqlstring' => "UPDATE {$tabla} SET Estado=\"#pEstado#\" WHERE {$pk}=\"#p{$pk}#\"",
    ];
}

$insertColumns = [];
$insertValues = [];
foreach ($insertFields as $field) {
    $name = $field['nombre'];
    $insertColumns[] = $name;
    $insertValues[] = "\"#p{$name}#\"";
}
$spInserts[] = [
    'spnombre' => "ins_{$tabla}",
    'spoperacion' => 'INS',
    'sptabla' => $spTable,
    'spsqlstring' => "INSERT INTO {$tabla} (" . implode(",\n    ", $insertColumns) . ")\nVALUES (" . implode(",\n    ", $insertValues) . ")",
];

$updateAssignments = [];
foreach ($updateFields as $field) {
    $name = $field['nombre'];
    $updateAssignments[] = "{$name}=\"#p{$name}#\"";
}
$spInserts[] = [
    'spnombre' => "upd_{$tabla}_x{$pk}",
    'spoperacion' => 'UPD',
    'sptabla' => $spTable,
    'spsqlstring' => "UPDATE {$tabla}\nSET " . implode(",\n    ", $updateAssignments) . "\nWHERE {$pk}=\"#p{$pk}#\"",
];

$whereParts = [];
foreach ($fields as $field) {
    $name = $field['nombre'];
    $tipo = strtolower($field['tipo'] ?? 'string');
    if ($name === 'Estado') {
        $whereParts[] = "IF(\"#px{$name}#\",{$name} IN (#p{$name}#),1)";
        continue;
    }
    if (in_array($tipo, ['string', 'text', 'varchar', 'char'], true)) {
        $whereParts[] = "IF(\"#px{$name}#\", LCASE({$name}) LIKE LCASE(\"%#p{$name}#%\"),1)";
    } else {
        $whereParts[] = "IF(\"#px{$name}#\",{$name}=\"#p{$name}#\",1)";
    }
}
$spInserts[] = [
    'spnombre' => "sel_{$tabla}_busqueda_avanzada",
    'spoperacion' => 'SEL',
    'sptabla' => $spTable,
    'spsqlstring' => "SELECT * FROM {$tabla}\nWHERE\n" . implode("\nAND\n", $whereParts) . "\nORDER BY #porderby# #plimit#",
];

$spSqlLines = [];
foreach ($spInserts as $sp) {
    $spSqlLines[] = sprintf(
        "INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('%s','%s','%s','%s',NULL,'1',NOW());",
        sqlEscape($sp['spnombre']),
        sqlEscape($sp['spoperacion']),
        sqlEscape($sp['sptabla']),
        sqlEscape($sp['spsqlstring'])
    );
}
file_put_contents($outputBase . '/stored_procedures.sql', implode("\n", $spSqlLines) . "\n");

$files = [
    'Datos/c' . $clase . '.db.php',
    'Logica/c' . $clase . '.class.php',
    'bt-admin/' . $modulo . '.php',
    'bt-admin/' . $modulo . '_am.php',
    'bt-admin/' . $modulo . '_lst_ajax.php',
    'bt-admin/' . $modulo . '_upd.php',
    'bt-admin/modulos/' . $modulo . '/js/' . $modulo . '.js',
    'bt-admin/modulos/' . $modulo . '/js/' . $modulo . '_am.js',
    'stored_procedures.sql',
];

echo "ABM generado en: {$outputBase}\n";
foreach ($files as $file) {
    echo " - {$file}\n";
}
