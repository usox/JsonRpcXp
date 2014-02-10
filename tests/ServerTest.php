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
 * @package   Lx\JsonRpcXp
 * @author    Alexander Wühr <lx@boolshit.de>
 * @copyright 2014 Alexander Wühr <lx@boolshit.de>
 * @license   http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      https://github.com/l-x/JsonRpcXp
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
			'id' => 'id',
			'jsonrpc' => '2.0',
			'method' => 'foo',
			'params' => array('bar'),
		);
		$this->obj->set(
			'callbacks',
			array(
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
		$mock->set(
			'callbacks',
			array(
				$this->message->method => true
			)
		);

		return $mock;
	}

	/**
	 * @test
	 * @testdox                  Server::registerException() throws an exception on non existing exception class
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
		$this->assertEquals(
			array(
				'Exception', 'InvalidArgumentException'
			),
			$this->obj->get('registered_exceptions')
		);
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
	 * @testdox Server::fault() returns proper structure
	 */
	public function fault() {
		$id = 'test';

		$fault = $this->getMock(__NAMESPACE__.'\Fault', array('toArray'));
		$fault->expects($this->once())->method('toArray')->will($this->returnValue(array('fault')));

		$obj = $this->getMock(__NAMESPACE__.'\ServerProxy', array('getMessageStub'));
		$obj->expects($this->once())->method('getMessageStub')->with($id)->will(
				$this->returnValue(array('stub'))
			);

		$expected = array(
			'stub', 'error' => array('fault'),
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
		$obj->expects($this->exactly(count($methods)))->method('registerFunction')->will($this->returnSelf());

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
		$obj->expects($this->once())->method('wrapCallback')->with($callback)->will($this->returnArgument(0));

		$obj->registerFunction($name, $callback, $namespace);
		$this->assertEquals($expected, $obj->get('callbacks'));
	}

	/**
	 * @test
	 * @testdox Server::validateMessage() return true on valid message
	 */
	public function validateMessageReturnsTrueOnSuccess() {
		$obj = $this->getMock(__NAMESPACE__.'\ServerProxy', array('fault'));
		$obj->set(
			'callbacks',
			array(
				$this->message->method => true
			)
		);

		$obj->expects($this->never())->method('fault');

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

	/**
	 * @test
	 * @testdox Server::wrapCallback() returns instance of \Lx\Fna\Wrapper
	 */
	public function wrapCallback() {
		$this->assertInstanceOf(
			'\Lx\Fna\Wrapper',
			$this->obj->call(
				'wrapCallback',
				array(
					function () {
					},
				)
			)
		);
	}

	/**
	 * @test
	 * @testdox Server::handleCallbackResponse() returns proper value
	 */
	public function handleCallbackResponse() {
		$message_id = 'foo';

		$result = 'bar';
		$expected = array(
			'result' => $result,
		);

		$sut = $this->getMock(__NAMESPACE__.'\ServerProxy', array('getMessageStub'));
		$sut->expects($this->once())->method('getMessageStub')->with($message_id)->will(
				$this->returnValue(array())
			);

		$this->assertEquals($expected, $sut->call('handleCallbackResponse', array($result, $message_id)));
	}

	public function getHandleCallbackExceptionMock($message_id, $exception_class) {
		$sut = $this->getMock(__NAMESPACE__.'\ServerProxy', array('isExceptionRegistered', 'fault'));
		$sut->expects($this->once())->method('fault')->with($this->isInstanceOf($exception_class), $message_id)
			->will($this->returnValue(true));

		return $sut;
	}

	/**
	 * @test
	 * @testdox Server::handleCallbackException() returns proper value on thrown \Lx\JsonRpcXp\Fault
	 */
	public function handleCallbackExceptionOnFault() {
		$message_id = 'foo';
		$exception_class = __NAMESPACE__.'\Fault';
		$sut = $this->getHandleCallbackExceptionMock($message_id, $exception_class);
		$sut->expects($this->never())->method('isExceptionRegistered');

		$this->assertTrue($sut->call('handleCallbackException', array(new $exception_class(), $message_id)));
	}

	/**
	 * @test
	 * @testdox Server::handleCallbackException() returns proper value on thrown registered exception
	 */
	public function handleCallbackExceptionOnRegisteredException() {
		$message_id = 'foo';
		$exception_class = '\Exception';
		$sut = $this->getHandleCallbackExceptionMock($message_id, __NAMESPACE__.'\Fault');
		$sut->expects($this->once())->method('isExceptionRegistered')->will($this->returnValue(true));

		$this->assertTrue($sut->call('handleCallbackException', array(new $exception_class(), $message_id)));
	}

	/**
	 * @test
	 * @testdox Server::handleCallbackException() returns proper value on thrown unregistered exception
	 */
	public function handleCallbackExceptionOnUnregisteredException() {
		$message_id = 'foo';
		$exception_class = '\Exception';
		$sut = $this->getHandleCallbackExceptionMock($message_id, __NAMESPACE__.'\Fault\InternalError');
		$sut->expects($this->once())->method('isExceptionRegistered')->will($this->returnValue(false));

		$this->assertTrue($sut->call('handleCallbackException', array(new $exception_class(), $message_id)));
	}

	/**
	 * @test
	 * @testdox Server::handleMessage() returns result of Server::validateMessage() on validation error
	 */
	public function handleMessageReturnsValidationResultOnError() {
		$message = new \stdClass();
		$sut = $this->getMock(__NAMESPACE__.'\ServerProxy', array('validateMessage'));

		$sut->expects($this->once())->method('validateMessage')->with($message)->will(
				$this->returnValue('something')
			);

		$this->assertEquals('something', $sut->call('handleMessage', array($message)));
	}


	public function handleMessageProvider() {
		return array(
			array('test_id', 'test_response'), array(null, null),
		);
	}

	/**
	 * @test
	 * @dataProvider handleMessageProvider
	 * @testdox Server::handleMessage() returns proper result on message
	 */
	public function handleMessageReturnsResultOnMessage($message_id, $expected) {
		$message = (object) array(
			'id' => $message_id, 'method' => 'test_method', 'params' => array(),
		);

		$sut = $this->getMock(__NAMESPACE__.'\ServerProxy', array('validateMessage', 'handleCallbackResponse'));

		$sut->expects($this->once())->method('validateMessage')->with($message)->will($this->returnValue(true));

		$sut->expects($this->once())->method('handleCallbackResponse')->with($expected, $message->id)->will(
				$this->returnValue($expected)
			);

		$sut->set(
			'callbacks',
			array(
				$message->method => function () use ($expected) {
						return $expected;
					}
			)
		);

		$this->assertEquals($expected, $sut->call('handleMessage', array($message)));
	}

	/**
	 * @test
	 * @dataProvider handleMessageProvider
	 * @testdox Server::handleMessage() returns proper result on fault/exception
	 */
	public function handleMessageReturnsResultOnFault($message_id, $expected) {
		$message = (object) array(
			'id' => $message_id, 'method' => 'test_method', 'params' => array(),
		);

		$test_response = 'response';

		$sut = $this->getMock(
			__NAMESPACE__.'\ServerProxy',
			array(
				'validateMessage', 'handleCallbackResponse', 'handleCallbackException'
			)
		);

		$sut->expects($this->once())->method('validateMessage')->with($message)->will($this->returnValue(true));

		$sut->expects($this->never())->method('handleCallbackResponse');

		$sut->expects($this->once())->method('handleCallbackException')->with(
				$this->isInstanceOf('\Exception'),
				$message->id
			)->will($this->returnValue($expected));

		$sut->set(
			'callbacks',
			array(
				$message->method => function () use ($test_response) {
						throw new \Exception();
					}
			)
		);

		$this->assertEquals($expected, $sut->call('handleMessage', array($message)));
	}

	/**
	 * @test
	 * @testdox Server::handle() returns proper result on json parse error
	 */
	public function handleReturnsFaultOnDecodeError() {
		$sut = $this->getMock(__NAMESPACE__.'\ServerProxy', array('jsonEncode', 'jsonDecode', 'fault'));

		$request = 'test_request';
		$json_encode_result = 'encode_result';
		$fault_result = 'fault_result';

		$sut->expects($this->once())->method('jsonDecode')->with($request)->will($this->returnValue(false));

		$sut->expects($this->once())->method('fault')->with(
				$this->isInstanceOf(__NAMESPACE__.'\Fault\ParseError')
			)->will($this->returnValue($fault_result));

		$sut->expects($this->once())->method('jsonEncode')->with($fault_result)->will(
				$this->returnValue($json_encode_result)
			);

		$this->assertEquals($json_encode_result, $sut->call('handle', array($request)));
	}

	/**
	 * @test
	 * @testdox Server::handle() returns proper result on single message request
	 */
	public function handleReturnsResponseOnSingleRequest() {
		$request = 'test_request';

		$response = 'test_response';

		$jsondecode_result = (object) array(
			'some' => 'value',
		);

		$sut = $this->getMock(__NAMESPACE__.'\ServerProxy', array('handleMessage', 'jsonEncode', 'jsonDecode'));

		$sut->expects($this->once())->method('jsonDecode')->with($request)->will(
				$this->returnValue($jsondecode_result)
			);

		$sut->expects($this->once())->method('handleMessage')->with($jsondecode_result)->will(
				$this->returnValue($response)
			);

		$sut->expects($this->once())->method('jsonEncode')->with($response)->will(
				$this->returnValue($response)
			);

		$this->assertEquals($response, $sut->call('handle', array($request)));
	}

	/**
	 * @test
	 * @testdox Server::handle() returns proper result on batch request
	 */
	public function handleReturnsResultsOnBatchMessage() {
		$request = 'test_request';

		$jsondecode_result = array();
		foreach (array('some', 'test', 'values') as $item) {
			$jsondecode_result[] = (object) array(
				'key' => $item
			);
		}

		$response = 'test_response';

		$sut = $this->getMock(__NAMESPACE__.'\ServerProxy', array('handleMessage', 'jsonEncode', 'jsonDecode'));

		$sut->expects($this->once())->method('jsonDecode')->with($request)->will(
				$this->returnValue($jsondecode_result)
			);

		$sut->expects($this->exactly(count($jsondecode_result)))->method('handleMessage')->with(
				$this->isInstanceOf('\stdClass')
			)->will($this->returnValue($response));

		$sut->expects($this->once())->method('jsonEncode')->with(
				array_pad(array(), count($jsondecode_result), $response)
			)->will($this->returnValue($response));

		$this->assertEquals($response, $sut->call('handle', array($request)));
	}

	/**
	 * @test
	 * @testdox Server::handle() returns null request having no return value (notification)
	 */
	public function handleReturnsNullOnNotifications() {
		$sut = $this->getMock(__NAMESPACE__.'\ServerProxy', array('handleMessage', 'jsonEncode', 'jsonDecode'));

		$sut->expects($this->once())->method('jsonDecode')->will($this->returnValue(new \stdClass()));

		$sut->expects($this->once())->method('handleMessage')->will($this->returnValue(null));

		$sut->expects($this->never())->method('jsonEncode');

		$this->assertNull($sut->call('handle', array('some_dummy_request')));
	}
}
