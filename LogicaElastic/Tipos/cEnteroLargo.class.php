<?php


namespace Elastic\Tipos;


use stdClass;

class EnteroLargo
{
	use CamposDinamicos;
	use SubCampo;
	public $type = 'long';
}