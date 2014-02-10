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
 * * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in
 * the documentation and/or other materials provided with the
 * distribution.
 *
 * * Neither the name of Alexander Wühr nor the names of his
 * contributors may be used to endorse or promote products derived
 * from this software without specific prior written permission.
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
 * @package Lx\JsonRpcXp
 * @author Alexander Wühr <lx@boolshit.de>
 * @copyright 2014 Alexander Wühr <lx@boolshit.de>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link https://github.com/l-x/JsonRpcXp
 */

namespace Lx\JsonRpcXp;

/**
 * Class Fault
 *
 * @package Lx\JsonRpcXp
 */
class Fault extends \Exception {

	const INTERNAL_ERROR = -32603;

	const INVALID_PARAMS = -32602;

	const INVALID_REQUEST = -32600;

	const METHOD_NOT_FOUND = -32601;

	const PARSE_ERROR = -32700;

	/**
	 * @var mixed
	 */
	protected $data = null;

	public function __construct($message = '', $code = 0, $data = null) {
		parent::__construct($message, $code);
		$this->setData($data);
	}

	/**
	 * Sets the error object's data attribute
	 *
	 * @param mixed $data
	 *
	 * @return $this
	 */
	public function setData($data = null) {
		$this->data = $data;

		return $this;
	}

	/**
	 * Returns the error object's data attribute
	 *
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Hydrates an instance of Fault (or child class) with the exceptions's data
	 *
	 * @param \Exception $e
	 *
	 * @return static
	 */
	final public static function hydrate(\Exception $e) {
		return new static($e->getMessage(), -32000 - $e->getCode());

	}

	/**
	 * Returns an json-rpc error structure
	 *
	 * @return array
	 */
	public function toArray() {
		$data = array(
			'code'          => $this->getCode(),
			'message'       => $this->getMessage(),
		);

		$additional_data = $this->getData();
		if (!is_null($additional_data)) {
			$data['data'] = $additional_data;
		}

		return $data;
	}
} 
