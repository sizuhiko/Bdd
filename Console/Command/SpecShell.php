<?php

require_once 'PHPUnit/Autoload.php';
require_once 'PHP/CodeCoverage/Filter.php';
if(file_exists(dirname(dirname(dirname(__FILE__))).DS.'vendor/autoload.php')) {
	require dirname(dirname(dirname(__FILE__))).DS.'vendor/autoload.php';
}
require_once 'Console/CommandLine/Result.php';

App::uses('CakeSpec', 'Bdd.Console');
App::uses('ControllerSpec', 'Bdd.Console');
App::uses('CodeCoverageManager', 'Bdd.Console');

App::uses('Model', 'Model');
App::uses('ClassRegistry', 'Utility');
App::uses('ConnectionManager', 'Model');

class SpecShell extends Shell {
	
	public function main() {
		$this->_initDb();

		$cli = new \Console_CommandLine_Result;
		$cli->options = array();
		foreach ($this->params as $key => $value) {
			$cli->options[str_replace('-', '_', $key)] = $value;
		}
		$cli->args = array('files' => $this->args);

		// Check if we can use colors
		if ($cli->options['color'] === 'auto') {
			$cli->options['color'] = DIRECTORY_SEPARATOR != '\\' && function_exists('posix_isatty') && @posix_isatty(STDOUT);
		} else {
			$cli->options['color'] = filter_var($cli->options['color'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		}
		if (empty($cli->args['files'])) {
			$cli->args['files'] = array(ROOT . DS . 'spec');
		}

		if (!($cli->options['verbose'] || $cli->options['debug'])) {
			set_error_handler(array($this, 'skipWarning'), E_WARNING | E_NOTICE);
		}

		// set blacklist to CakePHP core and plugins
		if ((!empty($cli->options['coverage_clover']) ||
			 !empty($cli->options['coverage_html']) ||
			 !empty($cli->options['coverage_php']) ||
			 isset($cli->options['coverage_text'])) &&
			 extension_loaded('xdebug')) {
			$filter = \DrSlump\Spec\Coverage\Filter::getInstance();
			$filter->addDirectoryToWhitelist(APP);
			foreach (App::path('plugins') as $plugin_dir) {
				$filter->removeDirectoryFromWhitelist($plugin_dir);
			}
			foreach (App::path('vendors') as $vendor_dir) {
				$filter->removeDirectoryFromWhitelist($vendor_dir);
			}
			$filter->removeDirectoryFromWhitelist(APP . DS . 'Test');

			$codeCoverage = CodeCoverageManager::getInstance($filter);
			$codeCoverage->setProcessUncoveredFilesFromWhitelist(FALSE);
			$codeCoverage->start(ROOT . DS . 'index');
		}

		if ($cli->options['dump']) {
			$module = new DrSlump\Spec\Cli\Modules\Dump($cli);
			$module->run();
		} else {
			$module = new DrSlump\Spec\Cli\Modules\Test($cli);
			$module->run();
		}

		// print code coverage result
		if (isset($codeCoverage)) {
			$codeCoverage->stop();
			$shell = $this;
			if (!empty($cli->options['coverage_clover'])) {
				CodeCoverageManager::codeCoverageReportClover($cli->options['coverage_clover'], function($out) use($shell) { $shell->out($out); });
			}
			if (!empty($cli->options['coverage_html'])) {
				CodeCoverageManager::codeCoverageReportHTML($cli->options['coverage_html'], function($out) use($shell) { $shell->out($out); });
			}
			if (!empty($cli->options['coverage_php'])) {
				CodeCoverageManager::codeCoverageReportPHP($cli->options['coverage_php'], function($out) use($shell) { $shell->out($out); });
			}
			if (isset($cli->options['coverage_text'])) {
				CodeCoverageManager::codeCoverageText($cli->options['coverage_text'], function($out) use($shell) { $shell->out($out); });
			}
		}

	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description('Spec for PHP ' . DrSlump\Spec::VERSION . ' by Ivan -DrSlump- Montes');
		$parser->addOption(
			'color',
			array(
				'help'      => __('turn on colored output'),
				'choices'   => array('auto', 'yes', 'no'),
				'default'   => 'auto',
			)
		);

		$parser->addOption(
			'debug',
			array(
				'help'      => 'turn on debug output',
				'boolean'   => true
			)
		);

		$parser->addOption(
			'filter',
			array(
				'short'     => 'f',
				'help'      => 'filter which tests to run (regexp)',
				'default'   => array()
			)
		);

		$parser->addOption(
			'groups',
			array(
				'short'     => 'g',
				'help'      => 'run only this group (csv)',
				'default'   => array()
			)
		);

		$parser->addOption(
			'exclude-groups',
			array(
				'help'      => 'do not run this group (csv)',
				'default'   => array()
			)
		);

		$parser->addOption(
			'list-groups',
			array(
				'help'      => 'show available groups',
				'boolean'   => true
			)
		);

		$parser->addOption(
			'story',
			array(
				'short'     => 's',
				'help'      => 'turn on story style formatting',
				'boolean'   => true
			)
		);

		$parser->addOption(
			'format',
			array(
				'help'      => 'output format',
				'choices'   => array('dots', 'story'),
				'default'   => 'dots',
			)
		);

		$parser->addOption(
			'beep',
			array(
				'short'     => 'b',
				'help'      => 'turn on beep on failure',
				'boolean'   => true
			)
		);

		$parser->addOption(
			'dump',
			array(
				'short'     => 'd',
				'help'      => 'dump a spec file transformed to PHP',
				'boolean'   => true
			)
		);

		$parser->addOption(
			'coverage-html',
			array(
				'help'      => '<dir> Generate code coverage report in HTML format.'
			)
		);

		$parser->addOption(
			'coverage-clover',
			array(
				'help'      => '<file> Generate code coverage report in Clover XML format.'
			)
		);

		$parser->addOption(
			'coverage-php',
			array(
				'help'      => '<file> Serialize PHP_CodeCoverage object to file.'
			)
		);

		$parser->addOption(
			'coverage-text',
			array(
				'help'      => '<file> Generate code coverage report in text format. Default to writing to the standard output.'
			)
		);

		$parser->addArgument(
			'file',
			array(
				'help'      => 'spec file'
			)
		);
		return $parser;
	}
	public function skipWarning($code, $message, $file, $line)
	{
		return;
	}

	protected function _initDb() {
		$testDbAvailable = in_array('test', array_keys(ConnectionManager::enumConnectionObjects()));

		if ($testDbAvailable) {
			// Try for test DB
			restore_error_handler();
			$db = ConnectionManager::getDataSource('test');
			$testDbAvailable = $db->isConnected();
		}

		// Try for default DB
		if (!$testDbAvailable) {
			$db = ConnectionManager::getDataSource('default');
			$db->config['prefix'] = 'test_suite_';
		}

		ConnectionManager::create('test_suite', $db->config);
		ClassRegistry::config(array('ds' => 'test_suite'));
	}
	

}
