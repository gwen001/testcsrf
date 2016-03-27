<?php

/**
 * I don't believe in license
 * You can do want you want with this program
 * - gwen -
 */

class TestCsrfRequest extends HttpRequest
{
	private $csrf = false;


	public function getCsrf() {
		return $this->csrf;
	}
	public function setCsrf($v) {
		$this->csrf = (bool)$v;
	}
}

?>
