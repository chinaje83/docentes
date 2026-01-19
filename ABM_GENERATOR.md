# Generador de ABMs

Este repositorio incluye un generador de ABMs a partir de la estructura de una tabla en formato JSON. El script crea las clases de `Datos`/`Logica` y un conjunto basico de pantallas (`bt-admin`) junto con sus JS asociados.

## Uso rapido

1. Crear un JSON con la estructura de la tabla (ver ejemplo en `tools/abm_schema_example.json`) **o** un archivo `.sql` con el `CREATE TABLE` (ver `tools/create_table_example.sql`).
2. Ejecutar el generador:

```bash
php tools/generar_abm.php --schema=tools/abm_schema_example.json
```

Si preferis usar el `CREATE TABLE`:

```bash
php tools/generar_abm.php --create=tools/create_table_example.sql --modulo=car_escalafones --clase=Escalafones
```

Por defecto, el resultado se genera en `abm_generados/<modulo>`. Se puede especificar una carpeta de salida:

```bash
php tools/generar_abm.php --schema=mi_tabla.json --output=/ruta/salida
```

## Formato del esquema

Claves obligatorias:

- `tabla`: nombre de la tabla en la base de datos (ej: `Cargos`).
- `modulo`: nombre del modulo (ej: `car_cargos`).
- `clase`: nombre de la clase logica (ej: `Cargos`).
- `pk`: nombre de la clave primaria (ej: `IdCargo`).
- `campos`: array de campos con:
  - `nombre` (string): nombre de la columna.
  - `tipo` (string): `string`, `int`, `decimal`, `date`, etc.
  - `label` (string): etiqueta para el formulario/listado.
  - `requerido` (bool, opcional): marca validacion requerida.
  - `busqueda` (bool, opcional): incluir en filtros de busqueda.
  - `listado` (bool, opcional): incluir en columnas del listado.
  - `form` (bool, opcional): incluir en formulario alta/modificacion.
  - `auto` (bool, opcional): si el PK es autoincremental (default `true`).

Cuando se usa `--create`, el generador intenta inferir `campos`, `pk` y etiquetas a partir de los comentarios SQL. Los campos de auditoria (`AltaFecha`, `AltaUsuario`, `UltimaModificacionUsuario`, `UltimaModificacionFecha`) y `Estado` se excluyen por defecto del formulario, busqueda y listado, pero siguen presentes en la capa de datos si se necesita manipularlos manualmente.

El generador tambien crea un archivo `stored_procedures.sql` con los `INSERT` necesarios para la tabla `stored_procedures` usando convenciones como `sel_<Tabla>_x<PK>`, `ins_<Tabla>`, `upd_<Tabla>_x<PK>`, `del_<Tabla>_x<PK>` y `sel_<Tabla>_busqueda_avanzada`. Si existe el campo `Estado`, se agrega `upd_<Tabla>_Estado_x<PK>`.

## Notas

- El generador asume la existencia de stored procedures con los nombres:
  - `sel_<Tabla>_x<PK>`
  - `sel_<Tabla>_busqueda_avanzada`
  - `ins_<Tabla>`
  - `upd_<Tabla>_x<PK>`
  - `del_<Tabla>_x<PK>`
  - `upd_<Tabla>_Estado_x<PK>` (solo si existe el campo `Estado`)
- Ajustar los procedimientos y los campos segun la base de datos real.
