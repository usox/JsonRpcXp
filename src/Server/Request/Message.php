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
 * @package     JsonRpcXp
 * @subpackage  Server\Request
 * @author      Alexander Wühr <lx@boolshit.de>
 * @copyright   2013-2014 Alexander Wühr <lx@boolshit.de>
 * @license     http://opensource.org/licenses/MIT  The MIT License (MIT)
 * @link        https://github.com/l-x/JsonRpcXp
 */

namespace Lx\JsonRpcXp\Server\Request;

use Lx\JsonRpcXp;
use Lx\JsonRpcXp\Base;

/**
 * Class Message
 *
 * Decorator class for request messages handled by the server. Setters and getters that redirect to the base message
 * catch exceptions and store them in the property `$exceptions`.
 *
 * @package Lx\JsonRpcXp\Server\Request
 */
class Message implements Base\Request\IMessage {

	/**
	 * Base message instance
	 *
	 * @var Base\Request\Message
	 */
	protected $basemessage;

	/**
	 * Exceptions thrown while hydrating the base message
	 *
	 * @var \Exception[]
	 */
	protected $exceptions = array();

	/**
	 * Setter for json-rpc version string
	 *
	 * @see Base\Request\Message::setJsonrpc()
	 * @param string $jsonrpc
	 *
	 * @return Message
	 */
	public function setJsonrpc($jsonrpc) {
		try {
			$this->getBaseMessage()->setJsonrpc($jsonrpc);
		} catch (\Exception $e) {
			$this->addException($e);
		}

		return $this;
	}

	/**
	 * Getter for json-rpc version string
	 *
 	 * @see Base\Request\Message::getJsonrpc()
	 *
	 * @return string
	 */
	public function getJsonrpc() {
		return $this->getBaseMessage()->getJsonrpc();
	}

	/**
	 * Setter for message id
	 *
	 * @param float|int|string $id
	 * @see Base\Request\Message::setId()
	 *
	 * @return Message
	 */
	public function setId($id) {
		try {
			$this->getBaseMessage()->setId($id);
		} catch (\Exception $e) {
			$this->addException($e);
		}

		return $this;
	}

	/**
	 * Getter for message id
	 *
	 * @see Base\Request\Message::getJsonrpc()
	 *
	 * @return float|int|string
	 */
	public function getId() {
		return $this->getBaseMessage()->getId();
	}

	/**
	 * Sets the method name
	 *
	 * @see Base\Request\Message::setMethod()
	 * @param string $method
	 *
	 * @return Message
	 */
	public function setMethod($method) {
		try {
			$this->getBaseMessage()->setMethod($method);
		} catch (\Exception $e) {
			$this->addException($e);
		}

		return $this;
	}

	/**
	 * Returns the method name
	 *
	 * @see Base\Request\Message::getMethod()
	 *
	 * @return string
	 */
	public function getMethod() {
		return $this->getBaseMessage()->getMethod();
	}

	/**
	 * Sets the parameters
	 *
	 * @see Base\Request\Message::setParams()
	 * @param array $params
	 *
	 * @return Message
	 */
	public function setParams(array $params) {
		try {
			$this->getBaseMessage()->setParams($params);
		} catch (\Exception $e) {
			$this->addException($e);
		}

		return $this;
	}

	/**
	 * Returns the parameters
	 *
	 * @see Base\Request\Message::getParams()
	 *
	 * @return array
	 */
	public function getParams() {
		return $this->getBaseMessage()->getParams();
	}

	/**
	 * Sets the protocol instance to use
	 *
	 * @see Base\Request\Message::setProtocol()
	 * @param JsonRpcXp\Lib\Protocol $protocol
	 *
	 * @return Message
	 */
	public function setProtocol(\Lx\JsonRpcXp\Lib\Protocol $protocol) {
		$this->getBaseMessage()->setProtocol($protocol);

		return $this;
	}

	/**
	 * Returns the protocol instance to use
	 *
	 * @see Base\Request\Message::getProtocol()
	 *
	 * @return JsonRpcXp\Lib\Protocol
	 */
	public function getProtocol() {
		return $this->getBaseMessage()->getProtocol();
	}

	/**
	 * Adds an exception, mainly used in setters and Message::hydrate()
	 *
	 * @see Message::hydrate()
	 * @param \Exception $e
	 *
	 * @return Message
	 */
	public function addException(\Exception $e) {
		$this->exceptions[] = $e;

		return $this;
	}

	/**
	 * Returns the exceptions thrown while hydrating json-rpc data
	 *
	 * @see Message::hydrate()
	 *
	 * @return \Exception[]
	 */
	public function getExceptions() {
		return $this->exceptions;
	}

	/**
	 * Sets the base message instance
	 *
	 * @param JsonRpcXp\Base\Request\Message $basemessage
	 *
	 * @return Message
	 */
	public function setBaseMessage(JsonRpcXp\Base\Request\Message $basemessage) {
		$this->basemessage = $basemessage;

		return $this;
	}

	/**
	 * Returns the base message instance
	 *
	 * @return JsonRpcXp\Base\Request\Message
	 */
	public function getBaseMessage() {
		if (is_null($this->basemessage)) {
			$this->setBaseMessage(new JsonRpcXp\Base\Request\Message());
		}
		return $this->basemessage;
	}

	/**
	 * Creates a new base message with the parameters' data
	 *
	 * @param array|object $data
	 *
	 * @return Message
	 * @todo In this way every setter of base message can be overwritten, like the protocol
	 */
	public function hydrate($data) {
		if (is_object($data)) {
			$data = (array) $data;
		}

		foreach ($data as $key => $value) {
			$setter = 'set'.ucfirst(strtolower($key));
			if (!method_exists($this->getBaseMessage(), $setter)) {
				$this->addException(new \InvalidArgumentException('Unknown property '.$key));
			} else {
				$this->$setter($value);
			}
		}

		return $this;
	}
} 
