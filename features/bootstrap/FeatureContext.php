<?php # features/bootstrap/FeatureContext.php

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\MinkExtension\Context\MinkContext;

use Behat\CommonContexts\MinkRedirectContext;

if (file_exists(__DIR__ . '/../support/bootstrap.php')) {
    require_once __DIR__ . '/../support/bootstrap.php';
}

class FeatureContext extends MinkContext implements ClosuredContextInterface
{
    public $parameters = array();

    public function __construct(array $parameters) {
        $this->parameters = $parameters;

        if (file_exists(__DIR__ . '/../Support/env.php')) {
            $world = $this;
            require(__DIR__ . '/../Support/env.php');
        }
        $this->useContext('MinkRedirectContext', new MinkRedirectContext());
    }

    public function getStepDefinitionResources()
    {
        return glob(__DIR__.'/../Step/*.php');
    }

    public function getHookDefinitionResources()
    {
        return array(__DIR__ . '/../Support/hooks.php');
    }

    public function locatePath($path) {
        return parent::locatePath($this->getPathTo($path));
    }

    public function __call($name, array $args) {
        if (isset($this->$name) && is_callable($this->$name)) {
            return call_user_func_array($this->$name, $args);
        } else {
            $trace = debug_backtrace();
            trigger_error(
                'Call to undefined method ' . get_class($this) . '::' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_ERROR
            );
        }
    }


    public function getModel($name) {
        $model = ClassRegistry::init(array('class' => $name, 'ds' => 'test'));
        return $model;
    }
    public function truncateModel($name) {
        $model = ClassRegistry::init(array('class' => $name, 'ds' => 'test'));
        $table = $model->table;
        $db = ConnectionManager::getDataSource('test_suite');
        $db->truncate($table);
    }

}
