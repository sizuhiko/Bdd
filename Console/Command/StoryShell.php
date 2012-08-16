<?php

define('BEHAT_VERSION',         'DEV');
define('CAKEBEHAT_ROOT',         dirname(__FILE__));

require dirname(dirname(dirname(__FILE__))).DS.'vendor/autoload.php';

App::uses('Model', 'Model');
App::uses('ClassRegistry', 'Utility');
App::uses('ConnectionManager', 'Model');

class StoryShell extends Shell {
    
    public function main() {
        $this->_initDb();

        $args = $_SERVER['argv'];
        do {
            array_shift($args);
        } while($args[0] != 'Bdd.story');

        // Internal encoding to utf8
        mb_internal_encoding('utf8');

        $app = new Behat\Behat\Console\BehatApplication(BEHAT_VERSION);
        
        $command_option = false;
        foreach($args as $option) {
            $option = str_replace("-", "", $option);
            if($app->getDefinition()->hasOption($option) || $app->getDefinition()->hasShortcut($option)) {
                $command_option = true;
                break;
            }
        }
        // Load default config
        if(!in_array('--config', $args) && !in_array('-c', $args) && !$command_option) {
            array_push($args, '--config', APP . DS . 'Config' . DS . 'behat.yml');
        }
        $input = new Symfony\Component\Console\Input\ArgvInput($args);

        $app->run($input);
    }

	protected function _initDb() {
		$testDbAvailable = in_array('test', array_keys(ConnectionManager::enumConnectionObjects()));

		$_prefix = null;

		if ($testDbAvailable) {
			// Try for test DB
			restore_error_handler();
			$db = ConnectionManager::getDataSource('test');
			$testDbAvailable = $db->isConnected();
		}

		// Try for default DB
		if (!$testDbAvailable) {
			$db = ConnectionManager::getDataSource('default');
			$_prefix = $db->config['prefix'];
			$db->config['prefix'] = 'test_suite_';
		}

		ConnectionManager::create('test_suite', $db->config);
		$db->config['prefix'] = $_prefix;
		ClassRegistry::config(array('ds' => 'test_suite'));
	}
    

    public function getOptionParser() {
        $parser = new BehatConsoleOptionParser($this->name);
        return $parser;
    }

}
class BehatConsoleOptionParser extends ConsoleOptionParser {
    public function parse($argv, $command = null) {
        $params = $args = array();
        return array($params, $args);
    }
}
