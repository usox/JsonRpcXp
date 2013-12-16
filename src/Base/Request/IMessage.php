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

use Lx\JsonRpcXp\Lib\Protocol;

/**
 * Interface IMessage
 *
 * Interface for json-rpc request messages
 *
 * @package Lx\JsonRpcXp\Base\Request
 */
interface IMessage {

	/**
	 * Sets the message id
	 *
	 * @param int|float|string $id
	 *
	 * @return $this
	 */
	public function setId($id);

	/**
	 * Returns the message id
	 *
	 * @return int|float|string
	 */
	public function getId();

	/**
	 * Sets the json-rpc version string
	 *
	 * @param string $jsonrpc
	 *
	 * @return $this
	 */
	public function setJsonrpc($jsonrpc);

	/**
	 * Returns the json-rpc version string
	 *
	 * @return string
	 */
	public function getJsonrpc();

	/**
	 * Sets the method name
	 *
	 * @param string $method
	 *
	 * @return $this
	 */
	public function setMethod($method);

	/**
	 * Returns the method name
	 *
	 * @return string
	 */
	public function getMethod();

	/**
	 * Sets the parameters
	 *
	 * @param array $params
	 *
	 * @return $this
	 */
	public function setParams(array $params);

	/**
	 * Returns the parameters
	 *
	 * @return array
	 */
	public function getParams();

	/**
	 * Sets the protocol instance to use
	 *
	 * @param Protocol $protocol
	 *
	 * @return $this
	 */
	public function setProtocol(Protocol $protocol);

	/**
	 * Returns the protocol instance to use
	 *
	 * @return Protocol
	 */
	public function getProtocol();

} 
