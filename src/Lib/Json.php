<?php

namespace Lx\JsonRpcXp\Lib;

use Composer\Json\JsonValidationException;

class Json {
	public static function encode($data) {
		return json_encode($data);
	}

	public static function decode($json_string) {
		if (!$data = json_decode($json_string, true)) {
			throw new \InvalidArgumentException('Could not parse json string');
		}

		return $data;
	}
} 
