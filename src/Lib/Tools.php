<?php

namespace Lx\JsonRpcXp\Lib;


class Tools {

	const ARRAY_TYPE_MIXED = -1;
	const ARRAY_TYPE_EMPTY = 0;
	const ARRAY_TYPE_LIST = 1;
	const ARRAY_TYPE_DICT = 2;

	/**
	 * Returns array type (empty, index based, key based, mixed)
	 *
	 * @param array $array
	 *
	 * @return int
	 */
	public static function getArrayType(array $array) {
		$indices = count(array_filter(array_keys($array), 'is_string'));
		$count = count($array);

		if ($count == 0) {
			$type = self::ARRAY_TYPE_EMPTY;
		} elseif ($indices == 0) {
			$type = self::ARRAY_TYPE_LIST;
		} elseif ($indices == count($array)) {
			$type = self::ARRAY_TYPE_DICT;
		} else {
			$type = self::ARRAY_TYPE_MIXED;
		}

		return $type;
	}
} 
