<?php

require_once '../vendor/autoload.php';

use \Lx\JsonRpcXp\Server as Server;

class ExceptionalException extends Exception {

}

$exceptional_function = function () {
	throw new ExceptionalException('Deal with it!', 42);
};

$server = new Server();
$server->registerFunction('bang', $exceptional_function, 'boom');
/* A call to `{"method":"boom.bang",...}` would result in an json-rpc internal server fault */

$server->registerException('\ExceptionalException');
/*
Now the same call will result in json-rpc fault with the fault code `-32000 - <exception_code>`
and the the fault message `<exception_message>`
*/

$server->registerException(
       array(
	        '\InvalidArgumentException',
	        '\ExceptionalException',
       )
); // this should be self-explanatory - registering multiple exception classes at once...

echo $server->handle();
