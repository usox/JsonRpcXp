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

require_once __DIR__.'/../../../src/autoload.php';

class MessageProxy extends Message {

	public function _get($name) {
		return $this->$name;
	}

	public function _set($name, $value) {
		$this->$name = $value;
	}
}

class MessageTest extends \PHPUnit_Framework_TestCase {

	/** @var Message */
	protected $object;

	/** @var \PHPUnit_Framework_MockObject_MockObject */
	protected $protocol;

	public function setUp() {
		$this->protocol = $this->getMock('\Lx\JsonRpcXp\Lib\Protocol');
		$this->object = new MessageProxy();
		$this->object->setProtocol($this->protocol);
	}

	/**
	 * @test
	 * @testdox Message::setJsonrpc() sets right value
	 */
	public function setJsonrpc() {
		$data = 'test';
		$this->protocol->expects($this->once())
		               ->method('validateJsonrpcVersion')
		               ->with($data)
		               ->will($this->returnValue(true));

		$this->object->setJsonrpc($data);
		$this->assertEquals($data, $this->object->_get('jsonrpc'));
	}

	/**
	 * @test
	 * @testdox Message::setJsonrpc() throws exception on invalid version
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Invalid json-rpc version
	 */
	public function setJsoncpThrowsExceptionOnInvalid() {
		$this->protocol->expects($this->once())
		               ->method('validateJsonrpcVersion')
		               ->will($this->returnValue(false));

		$this->object->setJsonrpc(null);
	}

	/**
	 * @test
	 * @testdox Message::getJsonrpc() returns right value
	 */
	public function getJsonrpc() {
		$data = 'test';
		$this->object->_set('jsonrpc', $data);

		$this->assertEquals($data, $this->object->getJsonrpc());
	}

	/**
	 * @test
	 * @testdox Message::setId() sets right value
	 */
	public function setId() {
		$data = 'test';
		$this->protocol->expects($this->once())
			->method('validateId')
			->with($data)
			->will($this->returnValue(true));

		$this->object->setId($data);
		$this->assertEquals($data, $this->object->_get('id'));
	}

	/**
	 * @test
	 * @testdox Message::setId() throws exception on invalid id
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Invalid id
	 */
	public function setIdThrowsExceptionOnInvalid() {
		$this->protocol->expects($this->once())
		               ->method('validateId')
		               ->will($this->returnValue(false));

		$this->object->setId(null);
	}

	/**
	 * @test
	 * @testdox Message::getId() returns right value
	 */
	public function getId() {
		$data = 'test';
		$this->object->_set('id', $data);

		$this->assertEquals($data, $this->object->getId());
	}

	/**
	 * @test
	 * @testdox Message::setMethod() sets right value
	 */
	public function setMethod() {
		$data = 'test';
		$this->protocol->expects($this->once())
		               ->method('validateMethod')
		               ->with($data)
		               ->will($this->returnValue(true));

		$this->object->setMethod($data);
		$this->assertEquals($data, $this->object->_get('method'));
	}

	/**
	 * @test
	 * @testdox Message::setMethod() throws exception on invalid method name
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Invalid method name
	 */
	public function setMethodThrowsExceptionOnInvalid() {
		$this->protocol->expects($this->once())
		               ->method('validateMethod')
		               ->will($this->returnValue(false));

		$this->object->setMethod(null);
	}

	/**
	 * @test
	 * @testdox Message::getMethod() returns right value
	 */
	public function getMethod() {
		$data = 'test';
		$this->object->_set('method', $data);

		$this->assertEquals($data, $this->object->getMethod());
	}

	/**
	 * @test
	 * @testdox Message::setParams() sets right value
	 */
	public function setParams() {
		$data = array('test');
		$this->protocol->expects($this->once())
		               ->method('validateParams')
		               ->with($data)
		               ->will($this->returnValue(true));

		$this->object->setParams($data);
		$this->assertEquals($data, $this->object->_get('params'));
	}

	/**
	 * @test
	 * @testdox Message::setParams() throws exception on invalid parameter format
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Invalid parameter format
	 */
	public function setParamsThrowsExceptionOnInvalid() {
		$this->protocol->expects($this->once())
		               ->method('validateParams')
		               ->will($this->returnValue(false));

		$this->object->setParams(array());
	}

	/**
	 * @test
	 * @testdox Message::getParams() returns right value
	 */
	public function getParams() {
		$data = array('test');
		$this->object->_set('params', $data);

		$this->assertEquals($data, $this->object->getParams());
	}
}
