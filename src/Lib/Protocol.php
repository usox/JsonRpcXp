<?php
/**
 * JsonRpcXp
 *
 * Copyright (c) 2013-2014, Alexander Wühr <lx@boolshit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Alexander Wühr nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Lx\JsonRpcXp
 * @subpackage  Lib
 * @author      Alexander Wühr <lx@boolshit.de>
 * @copyright   2013-2014 Alexander Wühr <lx@boolshit.de>
 * @license     http://opensource.org/licenses/MIT  The MIT License (MIT)
 * @link        https://github.com/l-x/JsonRpcXp
 */

namespace Lx\JsonRpcXp\Lib;


/**
 * Class Protocol
 *
 * Methods for protocol validation
 *
 * @package Lx\JsonRpcXp\Lib
 */
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
	public function validateJsonRpcVersion($version_string) {
		return static::JSONRPC_VERSION === $version_string;
	}

	/**
	 * Validate json-rpc message id
	 *
	 * @param int|float|string $id
	 *
	 * @return bool
	 */
	public function validateId($id) {
		return is_null($id) || is_string($id) || is_numeric($id);
	}

	/**
	 * Validate params
	 *
	 * @param mixed $params
	 *
	 * @return bool
	 */
	public function validateParams($params) {
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
	public function validateMethod($method) {
		return is_string($method) && (bool) preg_match('/^[a-zA-Z\_][\w\.]*$/', $method);
	}
} 
