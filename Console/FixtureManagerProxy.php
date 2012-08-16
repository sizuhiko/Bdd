<?php
App::uses('CakeFixtureManager', 'TestSuite/Fixture');

class FixtureManagerProxy {
	public static function fixturize($test) {
		$world = $test->getSuite()->getWorld();
		if (property_exists($world, 'fixtures')) {
			$test->fixtures = $world->fixtures;
		}

		if (empty($test->fixtureManager)) {
			App::uses('AppFixtureManager', 'TestSuite');
			if (class_exists('AppFixtureManager')) {
				$fixture = new AppFixtureManager();
			} else {
				$fixture = new CakeFixtureManager();
			}
			$test->fixtureManager = $fixture;
		}
		$test->fixtureManager->fixturize($test);
	}
}