<?php

namespace Lx\JsonRpcXp\Lib;

class Json {

	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	public static function encode($data) {
		return json_encode($data);
	}

	/**
	 * @param string $json_string
	 *
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public static function decode($json_string) {
		if (!$data = json_decode($json_string, true)) {
			throw new \InvalidArgumentException('Could not parse json string');
		}

		return $data;
	}
} 
