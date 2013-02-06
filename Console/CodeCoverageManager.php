<?php
require_once 'PHPUnit/Autoload.php';

class CodeCoverageManager {
	protected static $_instance;

	public static function getInstance($filter = NULL) {
		if (self::$_instance === NULL) {
			self::$_instance = new \PHP_CodeCoverage(NULL, $filter);
		}
		return self::$_instance;
	}

	public static function codeCoverageReportClover($output_file, $console_output_function = false) {
		if($console_output_function) {
			$console_output_function(
			  "\nWriting code coverage data to XML file, this may take a moment."
			);
		}

		$writer = new \PHP_CodeCoverage_Report_Clover;
		$writer->process(self::getInstance(), $output_file);
		unset($writer);

		if($console_output_function) {
			$console_output_function("\n");
		}
	}

	public static function codeCoverageReportHTML($output_directory, $console_output_function = false) {
		if($console_output_function) {
			$console_output_function(
			  "\nGenerating code coverage report, this may take a moment."
			);
		}

		$writer = new \PHP_CodeCoverage_Report_HTML;
		$writer->process(self::getInstance(), $output_directory);
		unset($writer);

		if($console_output_function) {
			$console_output_function("\n");
		}
	}

	public static function codeCoverageReportPHP($output_file, $console_output_function = false) {
		if($console_output_function) {
			$console_output_function(
			  "\nSerializing PHP_CodeCoverage object to file, this may take a moment."
			);
		}

		$writer = new \PHP_CodeCoverage_Report_PHP;
		$writer->process(self::getInstance(), $output_file);
		unset($writer);

		if($console_output_function) {
			$console_output_function("\n");
		}
	}

	public static function codeCoverageText($output_file = 'php://stdout', $console_output_function = false) {
		if($console_output_function) {
			$console_output_function(
			  "\nGenerating textual code coverage report, this may take a moment."
			);
		}

		$outputStream = new \PHPUnit_Util_Printer($output_file);
		$colors       = FALSE;
		$writer = new \PHP_CodeCoverage_Report_Text($outputStream);
		$writer->process(self::getInstance(), $colors);
		unset($writer);

		if($console_output_function) {
			$console_output_function("\n");
		}
	}

}
