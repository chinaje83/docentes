<?php


namespace Elastic\Tipos;


use stdClass;

class Byte
{
	use CamposDinamicos;
	use SubCampo;
	public $type = 'byte';
}