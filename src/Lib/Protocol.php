<?php

namespace Lx\JsonRpcXp\Lib;


class Protocol {

	/**
	 * json-rpc version string
	 */
	const JSONRPC_VERSION = '2.0';

	/**
	 * Validate json-rpc version string
	 *
	 * @param $version_string
	 *
	 * @return bool
	 */
	public static function validateJsonRpcVersion($version_string) {
		return static::JSONRPC_VERSION === $version_string;
	}

	/**
	 * Validate json-rpc message id
	 *
	 * @param int|float|string $id
	 *
	 * @return bool
	 */
	public static function validateId($id) {
		return is_null($id) || is_string($id) || is_numeric($id);
	}

	/**
	 * Validate params
	 *
	 * @param mixed $params
	 *
	 * @return bool
	 */
	public static function validateParams($params) {
		if (is_object($params)) {
			$params = (array) $params;
		}

		if (is_array($params)) {
			$valid = Tools::getArrayType($params) !== Tools::ARRAY_TYPE_MIXED;
		} else {
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Validate method name
	 *
	 * @param $method
	 *
	 * @return bool
	 */
	public static function validateMethod($method) {
		return is_string($method) && (bool) preg_match('/^[a-zA-Z\_][\w\.]*$/', $method);
	}
} 
