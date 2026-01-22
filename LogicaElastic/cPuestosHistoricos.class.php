<?php


namespace Elastic;

use DateTime;
use Exception;
use FuncionesPHPLocal;
use ManejoErrores;
use stdClass;

class PuestosHistoricos implements InterfaceBase
{
	use ManejoErrores;
	/** @var Conexion */
	private $cnx;
	/** @var string  */
	private const INDEX = INDEXPREFIX.SUFFIX_HISTORICOS;

    public function __construct(Conexion $cnx)
    {
        $this->cnx =& $cnx;
    }
	/**
	 * @inheritDoc
	 */
	public static function Configuracion(&$jsonData): void { }

	/**
	 * @inheritDoc
	 */
	public static function Estructura($devolverJson = true)
	{
		$jsonData = new stdClass();
		$jsonData->dynamic = 'strict';
		$jsonData->properties = new stdClass();

		$jsonData->properties->Id = new stdClass();
		$jsonData->properties->Id->type = 'keyword';


		$jsonData->properties->IdAutonumerico = new Tipos\EnteroLargo();

		$jsonData->properties->IdPuesto = new stdClass();
		$jsonData->properties->IdPuesto->type = 'keyword';

        $jsonData->properties->Orden = new stdClass();
        $jsonData->properties->Orden->type = 'keyword';

        $jsonData->properties->IdLicencia = new stdClass();
        $jsonData->properties->IdLicencia->type = 'keyword';

        $jsonData->properties->IdNovedad = new stdClass();
        $jsonData->properties->IdNovedad->type = 'keyword';

        $jsonData->properties->NroResolucion = new Tipos\Keyword();

        $jsonData->properties->IdTipoCargo = new stdClass();
        $jsonData->properties->IdTipoCargo->type = 'integer';

        $jsonData->properties->IdEscalafon = new stdClass();
        $jsonData->properties->IdEscalafon->type = 'integer';

        $jsonData->properties->DesempenoLugar = new stdClass();
        $jsonData->properties->DesempenoLugar->type = 'integer';


        $jsonData->properties->TipoDocumento = new Tipos\Objeto();
        $jsonData->properties->TipoDocumento->Id = new Tipos\Entero();
        $jsonData->properties->TipoDocumento->IdRegistro = new Tipos\Entero();
        $jsonData->properties->TipoDocumento->Nombre = new Tipos\Texto('spanish');
        $jsonData->properties->TipoDocumento->Nombre->addField('raw', new Tipos\Keyword());

        $jsonData->properties->Revista = new stdClass();
		$jsonData->properties->Revista->type = 'object';
		$jsonData->properties->Revista->properties = new stdClass();
		$jsonData->properties->Revista->properties->Id = new stdClass();
		$jsonData->properties->Revista->properties->Id->type = 'keyword';
		$jsonData->properties->Revista->properties->Codigo = new stdClass();
		$jsonData->properties->Revista->properties->Codigo->type = 'keyword';
		$jsonData->properties->Revista->properties->Descripcion = new stdClass();
		$jsonData->properties->Revista->properties->Descripcion->type = 'keyword';
		$jsonData->properties->Revista->properties->Descripcion->index = false;

		$jsonData->properties->Persona = new stdClass();
		$jsonData->properties->Persona->type = 'object';
		$jsonData->properties->Persona->properties = new stdClass();
		$jsonData->properties->Persona->properties->Id = new stdClass();
		$jsonData->properties->Persona->properties->Id->type = 'keyword';
		$jsonData->properties->Persona->properties->NombreCompleto = new stdClass();
		$jsonData->properties->Persona->properties->NombreCompleto->type = 'keyword';
		$jsonData->properties->Persona->properties->NombreCompleto->index = false;
		$jsonData->properties->Persona->properties->CUIL = new stdClass();
		$jsonData->properties->Persona->properties->CUIL->type = 'keyword';
		$jsonData->properties->Persona->properties->CUIL->index = false;
		$jsonData->properties->Persona->properties->Documento = new stdClass();
		$jsonData->properties->Persona->properties->Documento->type = 'object';
		$jsonData->properties->Persona->properties->Documento->properties = new stdClass();
		$jsonData->properties->Persona->properties->Documento->properties->Tipo = new stdClass();
		$jsonData->properties->Persona->properties->Documento->properties->Tipo->type = 'object';
		$jsonData->properties->Persona->properties->Documento->properties->Tipo->properties = new stdClass();
		$jsonData->properties->Persona->properties->Documento->properties->Tipo->properties->Id = new stdClass();
		$jsonData->properties->Persona->properties->Documento->properties->Tipo->properties->Id->type = 'keyword';
		$jsonData->properties->Persona->properties->Documento->properties->Tipo->properties->Descripcion = new stdClass();
		$jsonData->properties->Persona->properties->Documento->properties->Tipo->properties->Descripcion->type = 'keyword';
		$jsonData->properties->Persona->properties->Documento->properties->Tipo->properties->Descripcion->index = false;
		$jsonData->properties->Persona->properties->Documento->properties->Numero = new stdClass();
		$jsonData->properties->Persona->properties->Documento->properties->Numero->type = 'keyword';
		$jsonData->properties->Persona->properties->Documento->properties->Numero->index = false;

        $jsonData->properties->Fechas = new stdClass();
        $jsonData->properties->Fechas->type = 'date_range';
        $jsonData->properties->Fechas->format = 'strict_date||epoch_millis';

        $jsonData->properties->FechaDesde = new stdClass();
        $jsonData->properties->FechaDesde->type = 'date';
        $jsonData->properties->FechaDesde->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

        $jsonData->properties->FechaHasta = new stdClass();
        $jsonData->properties->FechaHasta->type = 'date';
        $jsonData->properties->FechaHasta->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

		$jsonData->properties->FechaDesignacion = new Tipos\Fecha('strict_date||epoch_millis');

		$jsonData->properties->FechaTomaPosesion = new Tipos\Fecha('strict_date||epoch_millis');

        $jsonData->properties->FechaDesignacion = new stdClass();
        $jsonData->properties->FechaDesignacion->type = 'date';
        $jsonData->properties->FechaDesignacion->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

        $jsonData->properties->CodigoLiquidacion = new stdClass();
        $jsonData->properties->CodigoLiquidacion->type = 'keyword';

        $jsonData->properties->Razon = new Tipos\Texto('spanish');
        $jsonData->properties->Razon->addField('raw', new Tipos\Keyword());

		$jsonData->properties->Alta = new stdClass();
		$jsonData->properties->Alta->type = 'object';
		$jsonData->properties->Alta->properties = new stdClass();
		$jsonData->properties->Alta->properties->Fecha = new stdClass();
		$jsonData->properties->Alta->properties->Fecha->type = 'date';
		$jsonData->properties->Alta->properties->Fecha->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
		$jsonData->properties->Alta->properties->Usuario = new stdClass();
		$jsonData->properties->Alta->properties->Usuario->type = 'object';
		$jsonData->properties->Alta->properties->Usuario->properties = new stdClass();
		$jsonData->properties->Alta->properties->Usuario->properties->Id = new stdClass();
		$jsonData->properties->Alta->properties->Usuario->properties->Id->type = 'keyword';
		$jsonData->properties->Alta->properties->Usuario->properties->Nombre = new stdClass();
		$jsonData->properties->Alta->properties->Usuario->properties->Nombre->type = 'keyword';
		$jsonData->properties->Alta->properties->Usuario->properties->Nombre->index = false;


		$jsonData->properties->UltimaModificacion = new stdClass();
		$jsonData->properties->UltimaModificacion->type = 'object';
		$jsonData->properties->UltimaModificacion->properties = new stdClass();
		$jsonData->properties->UltimaModificacion->properties->Fecha = new stdClass();
		$jsonData->properties->UltimaModificacion->properties->Fecha->type = 'date';
		$jsonData->properties->UltimaModificacion->properties->Fecha->format = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
		$jsonData->properties->UltimaModificacion->properties->Usuario = new stdClass();
		$jsonData->properties->UltimaModificacion->properties->Usuario->type = 'object';
		$jsonData->properties->UltimaModificacion->properties->Usuario->properties = new stdClass();
		$jsonData->properties->UltimaModificacion->properties->Usuario->properties->Id = new stdClass();
		$jsonData->properties->UltimaModificacion->properties->Usuario->properties->Id->type = 'keyword';
		$jsonData->properties->UltimaModificacion->properties->Usuario->properties->Nombre = new stdClass();
		$jsonData->properties->UltimaModificacion->properties->Usuario->properties->Nombre->type = 'keyword';
		$jsonData->properties->UltimaModificacion->properties->Usuario->properties->Nombre->index = false;

		return $devolverJson? json_encode($jsonData) : $jsonData;
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public static function armarDatosElastic(array $datos, bool $encode = false)
	{
		$jsonData = new stdClass();

		$jsonData->Id = $datos['Id'];

		$jsonData->IdAutonumerico = $datos['_id'];

		if(!FuncionesPHPLocal::isEmpty($datos['IdPuesto'])) {
			$jsonData->IdPuesto = $datos['IdPuesto'];
		}

        if(!FuncionesPHPLocal::isEmpty($datos['Orden'])) {
            $jsonData->Orden = $datos['Orden'];
        }

        if (!FuncionesPHPLocal::isEmpty($datos['NroResolucion'])) {
            $jsonData->NroResolucion = $datos['NroResolucion'];
        }

		if(!FuncionesPHPLocal::isEmpty($datos['IdLicencia'])) {
			$jsonData->IdLicencia = $datos['IdLicencia'];
		}

		if(!FuncionesPHPLocal::isEmpty($datos['IdNovedad'])) {
			$jsonData->IdNovedad = $datos['IdNovedad'];
        }

		$jsonData->TipoDocumento = new stdClass();
		if(!FuncionesPHPLocal::isEmpty($datos['IdTipoDocumento']))
			$jsonData->TipoDocumento->Id = (int) $datos['IdTipoDocumento'];
		if(!FuncionesPHPLocal::isEmpty($datos['IdRegistroTipoDocumento']))
			$jsonData->TipoDocumento->IdRegistro = (int) $datos['IdRegistroTipoDocumento'];
		if(!FuncionesPHPLocal::isEmpty($datos['NombreTipoDocumento']))
			$jsonData->TipoDocumento->Nombre = $datos['NombreTipoDocumento'];

        if(!FuncionesPHPLocal::isEmpty($datos['Razon'])) {
            $jsonData->Razon = trim($datos['Razon']);
        }

        if (!FuncionesPHPLocal::isEmpty($datos['IdTipoCargo']))
            $jsonData->IdTipoCargo = $datos['IdTipoCargo'];
        if (!FuncionesPHPLocal::isEmpty($datos['DesempenoLugar']))
            $jsonData->DesempenoLugar = $datos['DesempenoLugar'];
        if (!FuncionesPHPLocal::isEmpty($datos['IdEscalafon']))
            $jsonData->IdEscalafon = $datos['IdEscalafon'];

        $jsonData->Persona = new stdClass();
		if(!FuncionesPHPLocal::isEmpty($datos['IdPersona'])) {
			$jsonData->Persona->Id = $datos['IdPersona'];
		}
		if(!FuncionesPHPLocal::isEmpty($datos['NombreCompleto'])) {
			$jsonData->Persona->NombreCompleto = $datos['NombreCompleto'];
		}
		if(!FuncionesPHPLocal::isEmpty($datos['CUIL'])) {
			$jsonData->Persona->CUIL = $datos['CUIL'];
		}
		$jsonData->Persona->Documento = new stdClass();
		$jsonData->Persona->Documento->Tipo = new stdClass();
		if(!FuncionesPHPLocal::isEmpty($datos['TipoDocumentoPersona'])) {
			$jsonData->Persona->Documento->Tipo->Id = $datos['TipoDocumentoPersona'];
		}
		if(!FuncionesPHPLocal::isEmpty($datos['NombreTipoDocumentoPersona'])) {
			$jsonData->Persona->Documento->Tipo->Descripcion = $datos['NombreTipoDocumentoPersona'];
		}
		if(!FuncionesPHPLocal::isEmpty($datos['DNI'])) {
			$jsonData->Persona->Documento->Numero = $datos['DNI'];
		}

		$inicio = new DateTime('+1 year');
        $jsonData->Fechas = new stdClass();
        if(!FuncionesPHPLocal::isEmpty($datos['FechaDesde'])) {
            $inicio = new DateTime($datos['FechaDesde']);
            $jsonData->FechaDesde = $inicio->format('Y-m-d H:i:s');
            $jsonData->Fechas->gte = $inicio->format('Y-m-d');
            $jsonData->Fechas->lte = NULL;
        }
        if(!FuncionesPHPLocal::isEmpty($datos['FechaHasta'])) {
            $fin = new DateTime($datos['FechaHasta']);
            $jsonData->FechaHasta = $datos['FechaHasta'];
            if($fin > $inicio)
                $jsonData->Fechas->lte = $fin->format('Y-m-d');
        }

		if(!FuncionesPHPLocal::isEmpty($datos['FechaDesignacion'])) {
			$jsonData->FechaDesignacion = $datos['FechaDesignacion'];
		}

		if(!FuncionesPHPLocal::isEmpty($datos['FechaTomaPosesion'])) {
			$jsonData->FechaTomaPosesion = $datos['FechaTomaPosesion'];
		}

		$jsonData->Revista = new stdClass();
		if(!empty($datos['IdRevista']))
			$jsonData->Revista->Id = (int) $datos['IdRevista'];
		if(!empty($datos['CodigoRevista']))
			$jsonData->Revista->Codigo = $datos['CodigoRevista'];
		if(!empty($datos['DescripcionRevista']))
			$jsonData->Revista->Descripcion = $datos['DescripcionRevista'];

		$jsonData->Alta = new stdClass();
		if(!FuncionesPHPLocal::isEmpty($datos['AltaFecha']))
			$jsonData->Alta->Fecha = $datos['AltaFecha'];
		$jsonData->Alta->Usuario = new stdClass();
		if(!FuncionesPHPLocal::isEmpty($datos['AltaUsuario']))
			$jsonData->Alta->Usuario->Id = (int) $datos['AltaUsuario'];
		if(!FuncionesPHPLocal::isEmpty($datos['AltaUsuarioNombre']))
			$jsonData->Alta->Usuario->Nombre = $datos['AltaUsuarioNombre'];

		$jsonData->UltimaModificacion = new stdClass();
		if(!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionFecha']))
			$jsonData->UltimaModificacion->Fecha = $datos['UltimaModificacionFecha'];
		$jsonData->UltimaModificacion->Usuario = new stdClass();
		if(!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionUsuario']))
			$jsonData->UltimaModificacion->Usuario->Id = (int) $datos['UltimaModificacionUsuario'];
		if(!FuncionesPHPLocal::isEmpty($datos['UltimaModificacionUsuarioNombre']))
			$jsonData->UltimaModificacion->Usuario->Nombre = $datos['UltimaModificacionUsuarioNombre'];

		$jsonData = FuncionesPHPLocal::ConvertiraUtf8($jsonData);

		return $encode? json_encode($jsonData) : $jsonData;
	}



    public function getPuestosHistoricosxIdPuesto(array $datos, ?array &$resultado): bool
    {
	    $SortField = 'Alta.fecha';
	    $SortOrder = 'desc';
        //print_r($datos);die;
        $jsonData = new stdClass();
        $jsonData->query = new stdClass();
        $jsonData->query = new stdClass();
        $jsonData->query->bool = new stdClass();
        $jsonData->query->bool->filter = [];
        $jsonData->from = $datos['from'] ?? 0;
        $jsonData->size = $datos['size'] ?? 1000;

        $sort = true;
        $i = 0;
        $ss = -1;
        $jsonData->query->bool->filter[$i] = new stdClass();
        $jsonData->query->bool->filter[$i]->term = new stdClass();
        $jsonData->query->bool->filter[$i]->term->{'IdPuesto'} = new stdClass();
        $jsonData->query->bool->filter[$i]->term->{'IdPuesto'}->value = $datos['IdPuesto'];

        if ($sort)
        {
            $jsonData->sort = [];

            if (isset($datos['sort']) && is_array($datos['sort']) && count($datos['sort']) > 0)
            {
                foreach ($datos['sort'] as $sort)
                {
                    $jsonData->sort[++$ss] = new StdClass;
                    $jsonData->sort[$ss]->{$sort['field']} = new StdClass;
                    $jsonData->sort[$ss]->{$sort['field']}->order = $sort['order'];
                }
            }
            else
            {
                $jsonData->sort[++$ss] = new StdClass;
                $jsonData->sort[$ss]->{$SortField} = new StdClass;
                $jsonData->sort[$ss]->{$SortField}->order = $SortOrder;
            }
        }

        $cuerpo = json_encode($jsonData);
        //echo $cuerpo;
        //$this->cnx->setDebug(true);
        if (!$this->cnx->sendPost(self::INDEX, '_search', $cuerpo, $resultado, $codigoRetorno))
        {
            $this->setError($this->cnx->getError());
            return false;
        }
        //die;
        if (!isset($resultado['hits']))
        {
            $this->setError(500, Funciones::DevolverError($resultado));
            return false;
        }

        if ($resultado['hits']['total']['value'] < 1)
        {
            $this->setError(404, 'No se encuentra');
//			return true;
        }

        return true;

    }


    /*public function buscarAltasxPersona(array $datos, ?array &$resultado, ?int &$numfilas, ?int &$total): bool {

	    $sort = true;

	    $defaultSort = new Consultas\Sort('IdAutonumerico', 'desc');

	    $jsonData = (new Consultas\Base($datos['size'] ?? PAGINAR, $datos['from'] ?? 0))
		    ->setQuery(Consultas\Query::bool());

	    $jsonData->getQuery()->addFilter(Consultas\Query::match('Razon', 'Alta'));



	    if (!FuncionesPHPLocal::isEmpty($datos['IdPersona'])) {
		    $jsonData
			    ->getQuery()
			    ->addFilter(
				    Consultas\Query::term('Persona.Id', (int) $datos['IdPersona'])
			    );
	    }

	    if ($sort) {
		    if (isset($datos['sort']) && is_array($datos['sort']) && count($datos['sort']) > 0) {
			    if (isset($datos['sort']['field']))
				    $jsonData->setSort(new Consultas\Sort($datos['sort']['field'], $datos['sort']['order']));
			    else
				    foreach ($datos['sort'] as $sort)
					    $jsonData->addSort(new Consultas\Sort($sort['field'], $sort['order']));
		    } else
			    $jsonData->setSort($defaultSort);
	    }



	    $cuerpo = json_encode($jsonData);
	    $this->cnx->setDebug(true);

	    if (!$this->cnx->sendGet(self::INDEX, '_search', $data, $codigoRetorno, 'track_total_hits=true', $cuerpo)) {
		    $this->setError($this->cnx->getError());
		    return false;
	    }

	    if (200 != $codigoRetorno || !isset($data['hits'])) {
		    $this->setError(400, Funciones::DevolverError($data));
		    return false;
	    }

	    $resultado = $data['hits']['hits'] ?? [];
	    $numfilas = count($resultado);
	    $total = (int) $data['hits']['total']['value'] ?? 0;
		return true;
    }*/



	/**
	 * @inheritDoc
	 */
	public static function obtenerId($datos)
	{
		if(is_array($datos) && isset($datos['Id']))
			return $datos['Id'];
		if(is_object($datos) && isset($datos->Id))
			return $datos->Id;
		return null;
	}
	/**
	 * @inheritDoc
	 */
	public static function getIndex(): string
	{
		return self::INDEX;
	}
}
