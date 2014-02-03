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

require_once __DIR__.'/../vendor/autoload.php';

/**
 * Class TestObject
 *
 * @package Lx\JsonRpcXp
 */
class TestObject {
	public function foo() {

	}

	public function bar() {

	}

	protected function baz() {

	}

	private function snafu() {

	}
}

/**
 * Class ServerProxy
 *
 * Proxy object for access to protected methods and members
 *
 * @package Lx\JsonRpcXp
 */
class ServerProxy extends Server {
	public function get($key) {
		return $this->$key;
	}

	public function set($key, $value) {
		$this->$key = $value;
	}

	public function call($method, $arguments = array()) {
		return call_user_func_array(array($this, $method), $arguments);
	}
}

/**
 * Class ServerTest
 *
 * @package Lx\JsonRpcXp
 */
class ServerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Server
	 */
	protected $obj;

	/**
	 * @var \stdClass
	 */
	protected $message;

	/**
	 * Testcase setup
	 */
	public function setUp() {

		$this->obj = new ServerProxy();
		$this->message = (object) array(
			'id'            => 'id',
			'jsonrpc'       => '2.0',
			'method'        => 'foo',
			'params'        => array('bar'),
		);
		$this->obj->set('callbacks', array(
		                             $this->message->method => true
		                        )
		);
	}

	/**
	 * @param $exception
	 * @param $id
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	public function getFaultMock() {
		$mock = $this->getMock(__NAMESPACE__.'\ServerProxy', array('fault'));
		$mock->expects($this->once())->method('fault')->will(($this->returnArgument(0)));
		$mock->set('callbacks', array(
		                            $this->message->method => true
		                       )
		);
		return $mock;
	}

	/**
	 * @test
	 * @testdox Server::registerException() throws an exception on non existing exception class
	 *
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Argument must be a valid exception class or array of valid exception classes
	 */
	public function registerExceptionThrowsExceptionOnNonExistingClass() {
		$this->obj->registerException('snafu');
	}

	/**
	 * @test
	 * @testdox Server::registerException() properly registers a single exception class
	 */
	public function registerExceptionAddsSingleClass() {
		$this->obj->registerException('Exception');
		$this->assertEquals(array('Exception'), $this->obj->get('registered_exceptions'));

		$this->obj->registerException('InvalidArgumentException');
		$this->assertEquals(array('Exception', 'InvalidArgumentException'), $this->obj->get('registered_exceptions'));
	}

	/**
	 * @test
	 * @testdox Server::registerException() properly registers an array of exception classes
	 */
	public function registerExceptionAddsMultipleClasses() {
		$classes = array('Exception', 'InvalidArgumentException');

		$this->obj->registerException($classes);
		$this->assertEquals($classes, $this->obj->get('registered_exceptions'));
	}

	/**
	 * @test
	 * @testdox Server::registerException() eliminates double registered exceptions
	 */
	public function registerExceptionEleminatesDupes() {
		$this->obj->registerException('Exception');
		$this->obj->registerException('Exception');
		$this->assertEquals(array('Exception'), $this->obj->get('registered_exceptions'));

		$this->obj->registerException(array('Exception', 'Exception'));
		$this->assertEquals(array('Exception'), $this->obj->get('registered_exceptions'));
	}


	/**
	 * @test
	 * @testdox Server::isExceptionRegistered() returns proper value
	 */
	public function isExceptionRegistered() {
		$this->obj->set('registered_exceptions', array('InvalidArgumentException'));

		$this->assertTrue($this->obj->call('isExceptionRegistered', array(new \InvalidArgumentException())));
		$this->assertFalse($this->obj->call('isExceptionRegistered', array(new \Exception())));
	}

	/**
	 * @test
	 * @testdox Server::getMessageStub() returns proper structure
	 */
	public function getMessageStub() {
		$id = 'test';
		$expected = array(
			'id'            => $id,
			'jsonrpc'       => '2.0',
		);

		$this->assertEquals($expected, $this->obj->call('getMessageStub', array($id)));
	}

	/**
	 * @test
	 * @testdox Server::fault() returns proper structure
	 */
	public function fault() {
		$id = 'test';

		$fault = $this->getMock(__NAMESPACE__.'\Fault', array('toArray'));
		$fault
			->expects($this->once())
			->method('toArray')
			->will($this->returnValue(array('fault')))
		;

		$obj = $this->getMock(__NAMESPACE__.'\ServerProxy', array('getMessageStub'));
		$obj
			->expects($this->once())
			->method('getMessageStub')
			->with($id)
			->will($this->returnValue(array('stub')))
		;

		$expected = array(
			'stub',
			'error' => array('fault'),
		);

		$this->assertEquals($expected, $obj->call('fault', array($fault, $id)));
	}

	/**
	 * @test
	 * @testdox Server::registerObject() properly calls Server::registerFunction() for every object's method
	 */
	public function registerObject() {
		$object = new TestObject();
		$namespace = 'foo';
		$methods = get_class_methods($object);

		$obj = $this->getMock(__NAMESPACE__.'\ServerProxy', array('registerFunction'));
		$obj->expects($this->exactly(count($methods)))
			->method('registerFunction')
			->will($this->returnSelf())
		;

		$obj->registerObject($object, $namespace);
	}

	/**
	 * @test
	 * @testdox Server::registerFunction() properly wraps and registers callback
	 */
	public function registerFunction() {
		$name = 'bar';
		$namespace = 'foo';
		$callback = 'baz';

		$expected = array("$namespace.$name" => $callback);

		$obj = $this->getMock(__NAMESPACE__.'\ServerProxy', array('wrapCallback'));
		$obj->expects($this->once())
			->method('wrapCallback')
			->with($callback)
			->will($this->returnArgument(0))
		;

		$obj->registerFunction($name, $callback, $namespace);
		$this->assertEquals($expected, $obj->get('callbacks'));
	}

	/**
	 * @test
	 * @testdox Server::validateMessage() return true on valid message
	 */
	public function validateMessageReturnsTrueOnSuccess() {
		$obj = $this->getMock(__NAMESPACE__.'\ServerProxy', array('fault'));
		$obj->set('callbacks', array(
		                            $this->message->method => true
		                       )
		);

		$obj->expects($this->never())
			->method('fault');

		$this->assertTrue($obj->call('validateMessage', array($this->message)));
	}

	public function assertValidateMessageFault($message, $fault_class) {
		$obj = $this->getFaultMock();

		$result = $obj->call('validateMessage', array($message));
		$this->assertInstanceOf(__NAMESPACE__.'\Fault\\'.$fault_class, $result);
	}

	/**
	 * @test
	 * @testdox Server::validateMessage() returns InvalidRequest fault on missing or wrong version string
	 */
	public function validateMessageReturnsFaultOnMissingOrWrongVersion() {
		$message = clone $this->message;
		$message->jsonrpc = '3.0';
		$this->assertValidateMessageFault($message, 'InvalidRequest');

		unset($message->jsonrpc);
		$this->assertValidateMessageFault($message, 'InvalidRequest');
	}

	/**
	 * @test
	 * @testdox Server::validateMessage() returns InvalidRequest fault on missing method parameter
	 */
	public function validateMessageReturnsFaultOnMissingMethodName() {
		$message = clone $this->message;
		unset($message->method);
		$this->assertValidateMessageFault($message, 'InvalidRequest');
	}

	/**
	 * @test
	 * @testdox Server::validateMessage() returns MethodNotFound fault on unknown method
	 */
	public function validateMessageReturnsFaultOnUnknownMethod() {
		$message = clone $this->message;
		$message->method = 'nonexisting';
		$this->assertValidateMessageFault($message, 'MethodNotFound');
	}

	/**
	 * @test
	 * @testdox Server::validateMessage() returns InvalidParams fault on invalid parameter type
	 */
	public function validateMessageReturnsFaultOnInvalidParams() {
		$message = clone $this->message;
		$message->params = 'some string';
		$this->assertValidateMessageFault($message, 'InvalidParams');
	}

	/**
	 * @test
	 * @testdox Server::validateMessage() sets message's id to null when missing
	 */
	public function validateMessageSetsIdWhenMissing() {
		$message = clone $this->message;
		unset($message->id);

		$this->obj->call('validateMessage', array($message));

		$this->assertNull($message->id);
	}

	/**
	 * @test
	 * @testdox Server::validateMessage() sets message's params to array() when missing
	 */
	public function validateMessageSetsParamsWhenMissing() {
		$message = clone $this->message;
		unset($message->params);

		$this->obj->call('validateMessage', array($message));

		$this->assertEquals(array(), $message->params);
	}
}
