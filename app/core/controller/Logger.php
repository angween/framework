<?php
namespace RLAtech\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

class Logger
{
	private const LOG_FILE = PATH_FILE . DS . 'logs.txt';

	private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB

	public function __construct()
	{
		if (!file_exists(self::LOG_FILE)) {
			$fileHandle = fopen(self::LOG_FILE, 'w'); // Opens the file for writing; creates if it doesn't exist
			fclose($fileHandle); // Closes the file handle
		}
	}

	public static function log(string $description)
	{
		$callingFunction = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1];
		$class           = isset($callingFunction['class']) ? $callingFunction['class'] : 'Global Scope';
		$method          = isset($callingFunction['function']) ? $callingFunction['function'] : 'Outside Method';

		$logMessage = date('Y-m-d H:i:s') . ' - ' . $description . ' - ' . $class . '::' . $method . PHP_EOL;

		if (filesize(self::LOG_FILE) !== false) {
			if (filesize(self::LOG_FILE) > self::MAX_FILE_SIZE) {
				self::pruneLogFile();
			}
		}

		file_put_contents(self::LOG_FILE, $logMessage, FILE_APPEND);
	}

	private static function pruneLogFile()
	{
		$fileHandle = fopen(self::LOG_FILE, 'r+'); // Opens the file for reading and writing
		if ($fileHandle) {
			fseek($fileHandle, -self::MAX_FILE_SIZE, SEEK_END); // Move pointer to the beginning of the last 10 MB
			$truncated = ftruncate($fileHandle, ftell($fileHandle)); // Truncate file to the current position
			fclose($fileHandle); // Close the file handle
			if (!$truncated) {
				throw new \Exception('Failed to truncate log file.');
			}
		} else {
			throw new \Exception('Failed to open log file for truncation.');
		}
	}
}
?>