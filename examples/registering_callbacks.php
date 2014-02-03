<?php

require_once '../vendor/autoload.php';

use \Lx\JsonRpcXp\Server as Server;

function plain_old_function($with, $arguments) {
	return "$with $arguments";
}

$shiny_new_closure = function ($with, $arguments) {
	return "$with, $arguments";
};

class WhatIRecommendToUse {

	public function pow($with, $arguments) {
		return "$with, $arguments";
	}
}


$server = new Server();

/* Registering functions */
$server->registerFunction('pew', '\plain_old_function'); // accessible via `{"method":"pew",...}`
$server->registerFunction('pew', $shiny_new_closure, 'bang.pow'); // accessible via `{"method":"bang.pow.pew",...}`

/* Registering objects */
$server->registerObject(new WhatIRecommendToUse()); // methods are accessible via `{"method":"<public_object_method>",...}`
$server->registerObject(new WhatIRecommendToUse(), 'eek'); // methods are accessible via `{"method":"eek.<public_object_method>",...}

/* Registering classes */
/* When registering classes all method calls will be of static nature. Thus any ocourence of the variable `$this` will result in an error */
$server->registerObject('\WhatIRecommendToUse'); // methods are accessible via `{"method":"<public_object_method>",...}
$server->registerObject('\WhatIRecommendToUse', 'ouch'); // methods are accessible via `{"method":"ouch.<public_object_method>",...}

echo $server->handle(
            file_get_contents('php://input')
);
