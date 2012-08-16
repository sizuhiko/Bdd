<?php
App::uses('ControllerTestCase', 'TestSuite');
App::uses('FixtureManagerProxy', 'Bdd.Console');

abstract class ControllerSpec extends ControllerTestCase {
	public function run(PHPUnit_Framework_TestResult $result = NULL) {
		FixtureManagerProxy::fixturize($this);
		parent::run($result);
		$this->fixtureManager->unload($this);
	}
}