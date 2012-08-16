<?php
App::uses('CakeTestCase', 'TestSuite');
App::uses('FixtureManagerProxy', 'Bdd.Console');

abstract class CakeSpec extends CakeTestCase {
	public function run(PHPUnit_Framework_TestResult $result = NULL) {
		FixtureManagerProxy::fixturize($this);
		parent::run($result);
		$this->fixtureManager->unload($this);
	}
}