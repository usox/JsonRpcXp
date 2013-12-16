<?php

namespace Lx\JsonRpcXp\Server\Request;

use Lx\JsonRpcXp;

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

	protected $object;

	public function setUp() {
		$this->object = new MessageProxy();
	}

	public function getterProvider() {
		return array(
			array('getJsonrpc'),
			array('getId'),
			array('getMethod'),
			array('getParams'),
		);
	}

	/**
	 * @test
	 * @testdox Message getters properly call the basemessages' equivalents
	 *
	 * @dataProvider getterProvider
	 * @param $getter
	 */
	public function baseGetter($getter) {
		$base_setter = $this->getMock('\Lx\JsonRpcXp\Base\Message', array($getter));
		$base_setter->expects($this->once())->method($getter);

		$sut = $this->getMock(get_class($this->object), array('getBaseMessage'));
		$sut->expects($this->once())->method('getBaseMessage')->will($this->returnValue($base_setter));

		$sut->$getter();
	}

	public function setterProvider() {
		return array(
			array('setJsonrpc'),
			array('setId'),
			array('setMethod'),
			array('setParams'),
		);
	}

	/**
	 * @test
	 * @testdox Message setters properly call the basemessages' equivalents
	 *
	 * @dataProvider setterProvider
	 * @param $setter
	 */
	public function baseSetter($setter) {
		$exception = new \Exception();

		$base_setter = $this->getMock('\Lx\JsonRpcXp\Base\Request\Message', array($setter));
		$base_setter->expects($this->once())->method($setter)->will($this->throwException($exception));

		$sut = $this->getMock(get_class($this->object), array('getBaseMessage', 'addException'));
		$sut->expects($this->once())->method('getBaseMessage')->will($this->returnValue($base_setter));

		$sut->expects($this->once())->method('addException')->with($exception);

		$instance = $sut->$setter(array());

		$this->assertEquals($sut, $instance);
	}

	/**
	 * @test
	 * @testdox Message::getBaseMessage() returns correct property value
	 */
	public function getBaseMessage() {
		$data = 'test';

		$this->object->_set('basemessage', $data);
		$this->assertEquals($data, $this->object->getBaseMessage());
	}

	/**
	 * @test
	 * @testdox Message::setProtocol() calls the equivalent method in base message
	 */
	public function setProtocol() {
		$setter = 'setProtocol';

		$base_setter = $this->getMock('\Lx\JsonRpcXp\Base\Request\Message', array($setter));
		$base_setter->expects($this->once())->method($setter);

		$sut = $this->getMock(get_class($this->object), array('getBaseMessage'));
		$sut->expects($this->once())->method('getBaseMessage')->will($this->returnValue($base_setter));

		$instance = $sut->$setter(new \Lx\JsonRpcXp\Lib\Protocol());

		$this->assertEquals($sut, $instance);
	}

	/**
	 * @test
	 * @testdox Message::getProtocol() calls the equivalent method in base message
	 */
	public function getProtocol() {
		$getter = 'getProtocol';

		$base_setter = $this->getMock('\Lx\JsonRpcXp\Base\Message', array($getter));
		$base_setter->expects($this->once())->method($getter);

		$sut = $this->getMock(get_class($this->object), array('getBaseMessage'));
		$sut->expects($this->once())->method('getBaseMessage')->will($this->returnValue($base_setter));

		$sut->$getter();
	}

	/**
	 * @test
	 * @testdox Message::getBaseMessage() initializes new base message and calls Message::setBaseMessage()
	 */
	public function getBaseMessageUninitialized() {
		$sut = $this->getMock(get_class($this->object), array('setBaseMessage'));
		$sut->expects($this->once())->method('setBaseMessage');

		$sut->getBaseMessage();
	}

	/**
	 * @test
	 * @testdox Message::setBaseMessage() sets the correct property
	 */
	public function setBaseMessage() {
		$basemessage = new \Lx\JsonRpcXp\Base\Request\Message();
		$this->object->setBaseMessage($basemessage);

		$this->assertEquals($basemessage, $this->object->_get('basemessage'));

	}

	/**
	 * @test
	 * @testdox Message::addException() sets the correct property
	 */
	public function addException() {
		$exception = new \Exception();

		$this->object->addException($exception);
		$this->assertEquals(array($exception), $this->object->_get('exceptions'));
	}

	/**
	 * @test
	 * @testdox Message::getExceptions() returns the correct property value
	 */
	public function getExceptions() {
		$expected = 'test';

		$this->object->_set('exceptions', $expected);
		$this->assertEquals($expected, $this->object->getExceptions());
	}


	/**
	 * @test
	 * @testdox Message::hydrate calls correct setter methods with array as argument
	 * @fixme Testsmell, two assertions in one test (array and object as parameter)
	 */
	public function hydrate() {
		$config = array(
			'setFoo'        => array('foo', 'foo value'),
			'setBar'        => array('bar', 'bar value'),
			'setBaz'        => array('baz', 'baz value'),
		);

		$mock_methods = array_keys($config);
		$mock_methods[] = 'getBaseMessage';

		$sut = $this->getMock(get_class($this->object), $mock_methods);
		$sut->expects($this->exactly(count($config) * 2))->method('getBaseMessage')->will($this->returnValue($sut));

		$data_array = array();
		$data_object = new \stdClass();

		foreach ($config as $setter => $item) {
			list($property, $value) = $item;
			$sut->expects($this->exactly(2))->method($setter)->with($value);
			$data_array[$property] = $value;
			$data_object->$property = $value;
		}

		$sut->hydrate($data_array);
		$sut->hydrate($data_object);
	}

	/**
	 * @test
	 * @testdox Message::hydrate() adds exception on exception in called setter
	 */
	public function hydrateAddsException() {
		$sut = $this->getMock(get_class($this->object), array('getBaseMessage', 'addException'));
		$sut->expects($this->once())->method('getBaseMessage')->will($this->returnValue($sut));
		$sut->expects($this->once())->method('addException')->with($this->isInstanceOf('\InvalidArgumentException'));

		$sut->hydrate(array('this_does' => 'not exist'));
	}
}
