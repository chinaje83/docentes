<?php


namespace Elastic\Tipos;


use stdClass;

class EnteroCorto
{
	use CamposDinamicos;
	use SubCampo;
	public $type = 'short';
}