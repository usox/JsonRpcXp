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

class BaseProxy extends Base {

	public function _get($key) {
		return $this->$key;
	}

	public function _set($key, $value) {
		$this->$key = $value;

		return $this;
	}

	public function _call($method, $arguments = array()) {
		return call_user_func_array(array($this, $method), $arguments);
	}
}


class BaseTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var BaseProxy
	 */
	protected $obj;

	public function setUp() {
		$this->obj = new BaseProxy();
	}

	public function stubIdProvider() {
		return array(
			array(123, array('jsonrpc' => '2.0', 'id' => 123)),
			array(null, array('jsonrpc' => '2.0', 'id' => null)),
			array(false, array('jsonrpc' => '2.0')),
		);
	}

	/**
	 * @test
	 * @testdox Base::getMessageStub() returns proper structure
	 * @dataProvider stubIdProvider
	 */
	public function getMessageStub($id, $stub) {
		$this->assertEquals($stub, $this->obj->_call('getMessageStub', array($id)));
	}
}
