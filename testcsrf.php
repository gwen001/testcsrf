<?php

/**
 * I don't believe in license
 * You can do want you want with this program
 * - gwen -
 */

include( 'Utils.php' );
include( 'TestCsrf.php' );
include( 'TestCsrfRequest.php' );


// parse command line
{
	$testcsrf = new TestCsrf();
	$reference = new TestCsrfRequest();

	$ssl = false;
	$argc = $_SERVER['argc'] - 1;

	for ($i = 1; $i <= $argc; $i++) {
		switch ($_SERVER['argv'][$i]) {
			case '-f':
				$request_file = $_SERVER['argv'][$i + 1];
				$i++;
				break;

			case '-h':
				Utils::help();
				break;

			case '-m':
				$testcsrf->setMode($_SERVER['argv'][$i + 1]);
				$i++;
				break;

			case '-o':
				$testcsrf->setToken($_SERVER['argv'][$i + 1]);
				$i++;
				break;

			case '-r':
				$reference->setRedirect( false );
				break;

			case '-s':
				$reference->setSsl( true );
				break;

			case '-t':
				$testcsrf->setTolerance($_SERVER['argv'][$i + 1]);
				$i++;
				break;
		}
	}

	if( !$testcsrf->getToken() ) {
		Utils::help('Token not found!');
	}
}
// ---


// init
{
	if( !$reference->loadFile($request_file) ) {
		Utils::help('Request file not found!');
	}
	$reference->setSsl( $ssl );

	$testcsrf->setReference( $reference );
	$testcsrf->runReference();
}
// ---


// main loop
{
	$testcsrf->run();
}
// ---


exit();

?>
