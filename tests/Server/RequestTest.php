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

namespace Lx\JsonRpcXp\Server;

use Lx\JsonRpcXp\Base;
use Lx\JsonRpcXp\Server\Request\Message;

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

	/** @var RequestProxy */
	protected $object;

	public function setUp() {
		$this->object = new RequestProxy();
	}

	/**
	 * @test
	 * @testdox Request::setBaseRequest() sets correct property
	 */
	public function setBaseRequest() {
		$request = new Base\Request();
		$instance = $this->object->setBaseRequest($request);

		$this->assertEquals($request, $this->object->_get('baserequest'));
		$this->assertEquals($instance, $this->object);
	}

	/**
	 * @test
	 * @testdox Request::getBaseRequests instanciates (if necessary) new base request instance and returns it
	 */
	public function getBaseRequest() {
		$request = new Base\Request();

		$this->object->_set('baserequest', $request);
		$this->assertEquals($request, $this->object->getBaseRequest());

		$this->object->_set('baserequest', null);
		$this->assertInstanceOf(get_class($request), $this->object->getBaseRequest());

	}

	/**
	 * @test
	 * @testdox Request::addMessage() calls base requests' equivalent method
	 */
	public function addMessage() {
		$message = new \Lx\JsonRpcXp\Server\Request\Message();

		$base_request = $this->getMock('Lx\JsonRpcXp\Base\Request', array('addMessage'));
		$base_request->expects($this->once())->method('addMessage')->with($message);

		$sut = $this->getMock(get_class($this->object), array('getBaseRequest'));
		$sut->expects($this->once())->method('getBaseRequest')->will($this->returnValue($base_request));

		$sut->addMessage($message);
	}

	/**
	 * @test
	 * @testdox Request::getMessages() calls base requests' equivalent method
	 */
	public function getMessages() {
		$message = 'test';

		$base_request = $this->getMock('Lx\JsonRpcXp\Base\Request', array('getMessages'));
		$base_request->expects($this->once())->method('getMessages')->will($this->returnValue($message));

		$sut = $this->getMock(get_class($this->object), array('getBaseRequest'));
		$sut->expects($this->once())->method('getBaseRequest')->will($this->returnValue($base_request));

		$this->assertEquals($message, $sut->getMessages());
	}

	/**
	 * @test
	 * @testdox Request::hydrate adds server request messages via Request::addMessage()
	 */
	public function hydrate() {
		$data = array(array('foo' => 'bar'));

		$sut = $this->getMock(get_class($this->object), array('addMessage'));
		$sut->expects($this->once())->method('addMessage')->with($this->isInstanceOf('\Lx\JsonRpcXp\Server\Request\Message'));

		$sut->hydrate($data);

	}

}
