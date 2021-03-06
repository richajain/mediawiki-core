<?php

abstract class SeleniumTestSuite extends PHPUnit_Framework_TestSuite {
	private $selenium;
	private $isSetUp = false;
	private $loginBeforeTests = true;
	private $triggerClientTestResources = true;

	// Do not add line break after test output
	const CONTINUE_LINE = 1;
	const RESULT_OK = 2;
	const RESULT_ERROR = 3;

	abstract public function addTests();

	public function setUp() {
		// Hack because because PHPUnit version 3.0.6 which is on prototype does not
		// run setUp as part of TestSuite::run
		if ( $this->isSetUp ) {
			return;
		}
		$this->isSetUp = true;
		$this->selenium = Selenium::getInstance();
		$this->selenium->start();
		if ( $this->triggerClientTestResources ) {
			$this->selenium->open( $this->selenium->getUrl() . '/index.php?setupTestSuite=' . $this->getName() );
			//wait a little longer for the db operation
			$this->selenium->waitForPageToLoad( 6000 );
		}
		if ( $this->loginBeforeTests ) {
			$this->login();
		}
	}

	public function tearDown() {
		if ( $this->triggerClientTestResources ) {
			$this->selenium->open( $this->selenium->getUrl() . '/index.php?clearTestSuite=' . $this->getName() );
		}
		$this->selenium->stop();
	}

	public function login() {
		$this->selenium->login();
	}

	public function loadPage( $title, $action ) {
		$this->selenium->loadPage( $title, $action );
	}

	protected function setLoginBeforeTests( $loginBeforeTests = true ) {
		$this->loginBeforeTests = $loginBeforeTests;
	}

	protected function setTriggerClientTestResources( $triggerClientTestResources = true ) {
		$this->triggerClientTestResources = $triggerClientTestResources;
	}
}
