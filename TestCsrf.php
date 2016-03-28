<?php

/**
 * I don't believe in license
 * You can do want you want with this program
 * - gwen -
 */

class TestCsrf
{
	const DEFAULT_TOLERANCE = 5;
	const TOKEN_DELIM = '$';

	/**
	 * @var TestCsrfRequest
	 *
	 * reference request
	 */
	private $reference = null;

	/**
	 * @var int
	 *
	 * test mode
	 */
	private $mode = -1;

	/**
	 * @var string
	 *
	 * token to remove
	 */
	private $token_name = null;

	/**
	 * @var array
	 *
	 * value of the token to remove
	 */
	private $token_value = null;

	/**
	 * @var array
	 *
	 * payloads table
	 */
	private $t_payloads = array();

	/**
	 * @var int
	 *
	 * tolerance for output result
	 */
	private $tolerance = self::DEFAULT_TOLERANCE; // percent
	private $_tolerance = 0; // real value

	/**
	 * @var array
	 *
	 * results table
	 */
	private $t_result = array();


	public function getMode() {
		return $this->mode;
	}
	public function setMode( $v ) {
		$v = (int)$v;
		if( $v>=0 || $v<=3 ) {
			$this->mode = $v;
			return true;
		} else {
			return false;
		}
	}


	public function getToken() {
		return $this->token_name;
	}
	public function setToken( $v ) {
		$this->token_name = trim( $v );
		return true;
	}


	public function getTolerance() {
		return $this->tolerance;
	}
	public function setTolerance( $v ) {
		$this->tolerance = (int)$v;
		return true;
	}


	public function getPayloads() {
		return $this->t_payloads;
	}
	public function addPayload( $k, $p )
	{
		$this->t_payloads[$k] = $p;
		return true;
	}


	public function getReference() {
		return $this->reference;
	}
	public function setReference( $v ) {
		$this->reference = $v;
		return true;
	}


	public function runReference()
	{
		$this->reference->request();
		//var_dump( $this->reference );
		//$this->reference->export();

		$this->_tolerance = (int)($this->reference->getResultLength() * $this->getTolerance() / 100);
		echo "\n-> Reference: RC=" . $this->reference->getResultCode() . ', RL=' . $this->reference->getResultLength() . ', T=' . $this->getTolerance() . '%, T2=' . $this->_tolerance . "\n";
		//exit();
	}


	public function run()
	{
		$n_injection = $this->preparePayloads();
		if( !$n_injection || !$this->token_value ) {
			exit( "Token not found!\n" );
		}

		foreach ($this->getPayloads() as $mode=>$p)
		{
			$r = clone $this->reference;
			$params = $r->getParams();
			$params = preg_replace( '#'.$this->token_name.'#', $p[0], $params );
			$params = preg_replace( '#'.$this->token_value.'#', $p[1], $params );
			$r->setParams( $params );
			$r->request();
			//var_dump( $r );
			//$r->export();
			$this->result( $mode, $r );
			$this->t_result[] = $r;
			unset( $r );
		}

		if( ($this->mode==-1 || $this->mode==3) && !$this->reference->isMultipart() )
		{
			$r = clone $this->reference;
			$url = $r->getUrl();
			$url .= strstr($url,'?') ? '&' : '?';
			$r->setUrl( $url.$r->getParams() );
			$r->setMethod( 'GET' );
			$r->setParams( '' );
			$r->request();
			//var_dump( $r );
			//$r->export();
			$this->result( 3, $r );
			$this->t_result[] = $r;
			unset( $r );
		}

		echo "\n";
	}


	private function preparePayloads()
	{
		if( $this->reference->isMultipart() ) {
			preg_match_all('#"' . $this->token_name . '"\n\n(.*)\n#', $this->reference->getParams(), $matches);
		} else {
			preg_match_all('#' . $this->token_name . '=([^\&]*)#', $this->reference->getParams(), $matches);
		}
		//var_dump($matches);
		$n_injection = count( $matches[0] );

		if( $n_injection ) {
			//$this->injection_string = $matches[0][0];
			$this->token_value = $matches[1][0];
			$token_value = $matches[1][0];

			if ($this->mode == -1 || $this->mode == 0) {
				$this->addPayload( 0, array(uniqid(),'z') );
			}
			if ($this->mode == -1 || $this->mode == 1) {
				$this->addPayload( 1, array($this->token_name,strrev($token_value)) );
			}
			if ($this->mode == -1 || $this->mode == 2) {
				$this->addPayload( 2, array($this->token_name,'') );
			}
			//var_dump($this->getPayloads());
		}

		return $n_injection;
	}


	private function result( $mode, $r )
	{
		$color = 'white';
		$diff = $r->getResultLength() - $this->reference->getResultLength();
		$text = 'M='.$mode.', U=' . $r->getUrl() . ', C=' . $r->getResultCode() . ', L=' . $r->getResultLength() . ', D=' . $diff;

		if( abs($diff) < $this->_tolerance )
		{
			// match ?!
			if( $this->isReference($r) ) {
				// this is the reference
				$color = 'dark_grey';
				$text .= ' -> REFERENCE';
			} else {
				$r->setCsrf( true );
				$text .= ' -> LENGTH OK';
			}
		}
		else
		{
			// no match !!
			if( $this->isReference($r) ) {
				// this is the reference
				$color = 'red';
				$text .= ' -> ERROR';
			} else {
				//echo ' -> NORMAL';
			}
		}

		if( $r->getCsrf() ) {
			if( $r->getResultCode() == $this->reference->getResultCode() ) {
				$color = 'green';
				$text .= ' AND CODE MATCH!';
			} else {
				$color = 'yellow';
				$text .= ' BUT CODE DO NOT MATCH!';
			}
		}

		Utils::_print( $text, $color );
		echo "\n";
	}


	private function isReference( $request )
	{
		if( $request->getUrl()!=$this->reference->getUrl() || $request->getHeaders(true)!=$this->reference->getHeaders(true)
			|| $request->getCookies(true)!=$this->reference->getCookies(true) || $request->getParams(true)!=$this->reference->getParams(true) ) {
			return false;
		}

		return true;
	}
}

?>
