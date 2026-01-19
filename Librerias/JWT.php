<?php
/*
 * Copyright(c)2011 Miguel Angel Nubla Ruiz (miguelangel.nubla@gmail.com). All rights reserved
 */
class JWT {
	private $alg;
	private $hash;
	private $data;
	
	private function base64url_decode($data) {
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}
	
	public function decode($token) {
		if (count(explode('.', $token))==3)
			list($header, $payload, $signature) = explode('.', $token);
		else
			return false;
		
		$this->data = $header . '.' . $payload;
		return $this->base64url_decode($payload);
	}
}
?>