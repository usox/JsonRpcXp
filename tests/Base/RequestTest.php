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
 * @subpackage  Base
 * @author      Alexander Wühr <lx@boolshit.de>
 * @copyright   2013-2014 Alexander Wühr <lx@boolshit.de>
 * @license     http://opensource.org/licenses/MIT  The MIT License (MIT)
 * @link        https://github.com/l-x/JsonRpcXp
 */

namespace Lx\JsonRpcXp\Base;

use Lx\JsonRpcXp\Base\Request\Message;

require_once __DIR__.'/../../src/autoload.php';

class RequestProxy extends Request {

	public function _get($name) {
		return $this->$name;
	}

	public function _set($name, $value) {
		$this->$name = $value;
	}
}

class RequestTest extends \PHPUnit_Framework_TestCase {

	protected $object;

	public function setUp() {
		$this->object = new RequestProxy();
	}

	/**
	 * @test
	 * @testdox Request::addMessage() adds message to Request::$messages and returns self
	 */
	public function addMessage() {
		$message = new Message();
		$instance = $this->object->addMessage($message);

		$this->assertEquals($message, current($this->object->_get('messages')));
		$this->assertEquals($this->object, $instance);
	}

	/**
	 * @test
	 * @testdox Request::getMessages() returns value of Requst::$messages
	 */
	public function getMessages() {
		$message = 'foo';
		$this->object->_set('messages', $message);

		$this->assertEquals($message, $this->object->getMessages());
	}

}
