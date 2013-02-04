<?php
// features/support/hooks.php

$hooks->beforeSuite(function($event) {
    // Do something before whole test suite
});
$hooks->afterSuite(function($event) {
    // Do something after whole test suite
});

$hooks->beforeFeature('', function($event) {
    // do something before each feature
});
$hooks->afterFeature('', function($event) {
    // do something after each feature
});

$hooks->beforeScenario('', function($event) {
    // do something before each scenario
});
$hooks->afterScenario('', function($event) {
    // do something after each scenario
});

