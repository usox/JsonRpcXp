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
 * @subpackage  Base\Request
 * @author      Alexander Wühr <lx@boolshit.de>
 * @copyright   2013-2014 Alexander Wühr <lx@boolshit.de>
 * @license     http://opensource.org/licenses/MIT  The MIT License (MIT)
 * @link        https://github.com/l-x/JsonRpcXp
 */

namespace Lx\JsonRpcXp\Base\Request;

use Lx\JsonRpcXp;
use Lx\JsonRpcXp\Lib\Protocol;

/**
 * Class Message
 *
 * Base class for request messages
 *
 * @package Lx\JsonRpcXp\Base\Request
 */
class Message {

	/** @var Protocol */
	protected $protocol;

	/**
	 * @var string
	 */
	protected $jsonrpc;

	/**
	 * @var string|int|float
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var array
	 */
	protected $params;


	public function __construct() {
		$this->setProtocol(new Protocol());
	}

	/**
	 * Sets the protocol instance to use
	 *
	 * @param Protocol $protocol
	 *
	 * @return $this
	 */
	public function setProtocol(Protocol $protocol) {
		$this->protocol = $protocol;

		return $this;
	}

	/**
	 * Returns the protocol instance to use
	 *
	 * @return Protocol
	 */
	public function getProtocol() {
		return $this->protocol;
	}

	/**
	 * @param int|float|string $id
	 *
	 * @return $this;
	 */
	public function setId($id) {
		if (!$this->getProtocol()->validateId($id)) {
			throw new \InvalidArgumentException('Invalid id');
		}
		$this->id = $id;

		return $this;
	}

	/**
	 * @return int|float|string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $jsonrpc
	 *
	 * @return $this
	 */
	public function setJsonrpc($jsonrpc) {
		if (!$this->getProtocol()->validateJsonRpcVersion($jsonrpc)) {
			throw new \InvalidArgumentException('Invalid json-rpc version');
		}
		$this->jsonrpc = $jsonrpc;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getJsonrpc() {
		return $this->jsonrpc;
	}

	/**
	 * @param string $method
	 *
	 * @return $this;
	 */
	public function setMethod($method) {
		if (!$this->getProtocol()->validateMethod($method)) {
			throw new \InvalidArgumentException('Invalid method name');
		}
		$this->method = $method;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @param array $params
	 *
	 * @return $this
	 */
	public function setParams(array $params) {
		if (!$this->getProtocol()->validateParams($params)) {
			throw new \InvalidArgumentException('Invalid parameter format');
		}
		$this->params = $params;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

} 
