<?php
namespace core\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use core\controller\DB;

class Scheduler 
{
	private bool $updatePeriode = false;

	private static array $files = [];

	public function __construct(){}

	public static function check(){
		$query = "SELECT 
			name, value
			FROM `".TBL_CONFIGURATION."`
			WHERE name = 'hari_lalu' OR
				name = 'bulan_lalu' OR
				name = 'tahun_lalu'";

		$result = DB::getInstance()->query($query)->fetchAll();

		// if ( $result === false ) self::createConfig();

		foreach ($result as $item) {
			$periode[$item['name']] = $item['value'];
		}

		/** kalau masih hari yg sama skip */
		if ($periode['hari_lalu'] == date('d') && $periode['bulan_lalu'] == date('m')) {
			return true;
		}

		$classPath  = PROGRAM_PATH . DS . 'controller' . DS . 'Admin';
		$folderPath = PATH_FILE . DS . 'app' . DS . $classPath;

		self::scanFilename(folderPath: $folderPath);

		/** Kalau file kosong */
		if ( empty( self::$files ) ) return;

		/** Kalau sudah berganti hari */
		self::jalankanSchedule(classPath: $classPath);

		/** Berganti bulan */
		if ($periode['bulan_lalu'] != date('m') && $periode['tahun_lalu'] == date('Y')) {
			self::jalankanScheduleBulanan(classPath: $classPath);
		}

		/** Berganti tahun */
		if ($periode['tahun_lalu'] != date('Y')) {
			self::jalankanScheduleTahunan(classPath: $classPath);
		}

		/** Update jadi tanggal sekarang */
		self::updatePeriode();

		return;
	}


	private static function scanFilename(string $folderPath)
	{
		if ( ! file_exists($folderPath ) ) {
			self::$files = [];

			return false;
		}

		self::$files = scandir($folderPath);

		return;
	}


	private static function updatePeriode() 
	{
		foreach (['hari_lalu', 'bulan_lalu', 'tahun_lalu'] as $name) {
			if ($name == 'hari_lalu') $value = date('d');
			elseif ($name == 'bulan_lalu') $value = date('m');
			elseif ($name == 'tahun_lalu') $value = date('Y');

			$query = "UPDATE `" . TBL_CONFIGURATION . "` SET value = ? WHERE name = ?";

			DB::getInstance()->query($query, $value, $name);
		}

		return;
	}


	private static function jalankanSchedule(string $classPath)
	{
		$files = self::$files;

		foreach ($files as $file) {
			$panggilClass = '';

			if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
				$className = pathinfo($file, PATHINFO_FILENAME);

				$panggilClass = '\\' . $classPath . "\\" . $className;

				$panggilClass = str_replace('/','\\', $panggilClass);
				
				if ( method_exists($panggilClass, 'dailySchedule')) {
					$panggilClass::dailySchedule();

					return;
				}
			}
		}

		return;
	}


	private static function jalankanScheduleBulanan(string $classPath)
	{
		$files = self::$files;

		foreach ($files as $file) {
			$panggilClass = '';

			if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
				$className = pathinfo($file, PATHINFO_FILENAME);

				$panggilClass = '\\' . $classPath . "\\" . $className;

				$panggilClass = str_replace('/', '\\', $panggilClass);

				if (method_exists($panggilClass, 'monthlySchedule')) {
					$panggilClass::monthlySchedule();

					return;
				}
			}
		}

		return;
	}


	private static function jalankanScheduleTahunan(string $classPath)
	{
		$files = self::$files;

		foreach ($files as $file) {
			$panggilClass = '';

			if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
				$className = pathinfo($file, PATHINFO_FILENAME);

				$panggilClass = '\\' . $classPath . "\\" . $className;

				$panggilClass = str_replace('/', '\\', $panggilClass);

				if (method_exists($panggilClass, 'yearlySchedule')) {
					$panggilClass::yearlySchedule();

					return;
				}
			}
		}

		return;
	}


	private static function createConfig()
	{
		$affected = 0;

		$name = '';

		$value = '';

		$query = "INSERT INTO `_configuration` SET name = ?, value = ?";

		$stmt = DB::getInstance()->prepare($query);

		$stmt->bind_param("ss", $name, $value);

		$data = [[
			'name' => 'hari_lalu',
			'value' => date('d')
		],[
			'name' => 'bulan_lalu',
			'value' => date('m')
		],[
			'name' => 'tahun_lalu',
			'value' => date('Y')
		]];

		foreach($data as $periode) {
			$name = $periode['name'];
			$value = $periode['value'];

			$stmt->execute();

			$result = $stmt->affected_rows;

			if ( $result >= 0 ) $affected += $result;
		}

		return $affected;
	}
}