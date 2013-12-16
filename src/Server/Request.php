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
 * @subpackage  Server
 * @author      Alexander Wühr <lx@boolshit.de>
 * @copyright   2013-2014 Alexander Wühr <lx@boolshit.de>
 * @license     http://opensource.org/licenses/MIT  The MIT License (MIT)
 * @link        https://github.com/l-x/JsonRpcXp
 */

namespace Lx\JsonRpcXp\Server;

use Lx\JsonRpcXp\Base;

/**
 * Class Request
 *
 * @package Lx\JsonRpcXp\Server
 */
class Request implements Base\IRequest{

	/**
	 * Contains the base request instance
	 *
	 * @var Base\Request
	 */
	protected $baserequest;

	/**
	 * Sets the base request instance and returns self
	 *
	 * @param Base\Request $baserequest
	 *
	 * @return Request
	 */
	public function setBaseRequest(Base\Request $baserequest) {
		$this->baserequest = $baserequest;

		return $this;
	}

	/**
	 * Instanciates the base message (if necessary) and returns it
	 *
	 * @return Base\Request
	 */
	public function getBaseRequest() {
		if (is_null($this->baserequest)) {
			$this->baserequest = new Base\Request();
		}

		return $this->baserequest;
	}

	/**
	 * Adds a request message to the queue
	 *
	 * @param Base\Request\IMessage $message
	 *
	 * @return Request
	 */
	public function addMessage(Base\Request\IMessage $message) {
		$this->getBaseRequest()->addMessage($message);

		return $this;
	}

	/**
	 * Returns the registered base messages
	 *
	 * @return Base\Request\IMessage[]
	 */
	public function getMessages() {
		return $this->getBaseRequest()->getMessages();
	}

	/**
	 * Hydrates a request array
	 *
	 * @param array $data
	 */
	public function hydrate(array $data) {
		foreach ($data as $raw_message) {
			$message = new Request\Message();
			$message->hydrate($raw_message);
			$this->addMessage($message);
		}

		return $this;
	}
} 
