<?php
namespace core\controller;

use core\controller as RLA;

defined('APP_OWNER') or exit('No direct script access allowed');

new Router();

class Router
{
	public static $payload;

	public static $respon = [];

	public $requestContentType;

	public $targetIsAddon = false;

	private $controller;

	private $actionName;

	private $skip = '';

	public function __construct()
	{
		$this->cekRequest();

		$this->cekLogin();

		if ( $this->skip == 'loginout' || $this->skip == '' ) $this->cekController();

		// if($this->skip == 'download') {
		// 	self::sendFile(path: 'download', filename: self::$payload['path'][1]);
		// }
	}

	private function cekLogin()
	{
		if (self::$payload['path'] == '')
			return true;

		if (in_array(self::$payload['path'][0], ['webhook', 'view'])){
			$this->skip = 'assets';

			return true;
		// } elseif (in_array(self::$payload['path'][0], ['download'])) {
		// 	$this->skip = 'download';

		// 	return true;
		} elseif (in_array(self::$payload['path'][0], ['login', 'logout'])) {
			$this->skip = 'loginout';

			return true;
		}

		if (!isset(RLA\App::$loggedInUser->user)) {
			RLA\App::respon([
				'responCode' => 401,
				'result' => [
					"status" => "gagal",
					"pesan" => "Harap <a href='/'>login</a> dahulu!"
				]
			]);

			return false;
		}

		return true;
	}


	private function sendFile(string $path, string $filename): void
	{
		$filePath = $path . '/' . $filename;

		echo $filePath;
		// Check if the file exists
		if (file_exists($filePath)) {
			// Set appropriate headers for download
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Content-Length: ' . filesize($filePath));

			// Read the file and output it to the browser
			readfile($filePath);

			// Exit to prevent additional output
			exit;
		} else {
			// Handle the case where the file does not exist
			App::responGagal('File tidak ditemukan!');
		}
	}

	private function webhookHandler()
	{
		$classPath = DS . PROGRAM_PATH . DS . 'controller' . DS . 'Admin' . DS . 'Webhook';

		$webhook = str_replace('/', '\\', $classPath);

		$method = self::$payload['path'][1];

		if (method_exists($webhook, $method)) {
			$webhook::{$method}(self::$payload);
		}

		exit;
	}

	private function cekController()
	{
		/* user buka halaman index */
		if (is_null(self::$payload['path'])) return;

		if (self::$payload['path'][0] == '/' || !self::$payload['path']) return;

		if (self::$payload['path'][0] == 'webhook' ) $this->webhookHandler();

		$this->cariController();

		// kalau method tidak ada coba kirim ke index
		if ($this->actionName == '' && method_exists($this->controller, 'index')) {
			$this->controller->index(self::$payload);
		}


		/**
		 * cek method atau ke index
		 */
		try {
			if (method_exists($this->controller, $this->actionName)) {
				if (!is_callable([$this->controller, $this->actionName])) {
					RLA\App::respon([
						'responCode' => 400,
						'result' => [
							'status' => 'gagal',
							'pesan' => 'Method tidak dibolehkan'
						]
					]);
				}

				/* kalau masih ada path berikutnya */
				$nextPath = self::$payload['path'] ?? '';

				if ($nextPath) self::$payload['path'] = $nextPath;

				$this->controller->{$this->actionName}(self::$payload);
			}
		} catch (\Exception $e) {
			RLA\App::responGagal($e->getMessage());
		}

		/**
		 * cek kalau ada respon dan kembalikan ke user
		 */
		if (!empty(RLA\App::$result)) {
			RLA\App::respon(RLA\App::$result);
		} else {
			self::notFound();
		}
	}

	private function cariController()
	{
		$fullPath = self::$payload['path'];
		/** Ambil nama Controller dari path - hapus */
		$controllerName  = ucfirst(array_shift(self::$payload['path']));

		/** Ambil nama method yg dipanggil dari path - hapus */
		$this->actionName= array_shift(self::$payload['path']) ?? '';

		/* path nya cuma untuk baca variable? */
		if ($controllerName == 'Param') {
			$this->getVariable(parameter: $controllerName, path: $this->actionName);
		}


		/** Untuk Addon (aplikasi utama) jika tidak ditemukan oleh app Core */
		if ( strtolower( $controllerName) == strtolower(PROGRAM_PATH) ) {
			$controllerName = ucfirst( $this->actionName );
			$program_action = ucfirst(self::$payload['path'][0]);

			$controllerName  = strtoupper(PROGRAM_PATH) . "\\controller\\" . $controllerName;
			
			if ( class_exists($controllerName) ) {

				$this->controller = new $controllerName;

				$this->actionName = ucwords($program_action);

				$this->targetIsAddon = true;
			}
		} else {
			if (class_exists( CLASS_CORE . '\\' . $controllerName )) {
				$_rlaClass = CLASS_CORE . '\\' . $controllerName;

				$this->controller = new $_rlaClass;
			} else {
				$this->responNotFound();
			}
		}
	}

	public static function notFound($type = 'Method')
	{
		RLA\App::respon(
			responCode: 404,
			result: [
				'status' => 'gagal',
				'pesan' => ucwords($type) . ' tidak ditemukan'
			]
		);
	}

	private function cekRequest()
	{
		$path = [
			'accept' => $_SERVER['HTTP_ACCEPT'] ?? '',
			'requestMethod' => $this->cekRequestMethod(),
			'path' => $this->cekPath(),
			'payload' => []
		];

		// req ContentType
		$this->requestContentType = ['application/x-www-form-urlencoded'];

		if (isset($_SERVER['CONTENT_TYPE'])) {
			$this->requestContentType = array_map(
				'trim',
				explode(";", $_SERVER['CONTENT_TYPE'])
			);
		}

		// GET / POST / FILES / DELETE / PUT / PATCH
		if (isset($_GET)) {
			foreach ($_GET as $key => $value) {
				if ($key == 'path') {
					$path['path'] = explode('/', rtrim($value, '/'));
				} else
					$path['get'][$key] = filter_var(trim($value), FILTER_SANITIZE_URL);
			}
		}

		if (isset($_POST)) {
			if (
				$this->cekContentType('application/x-www-form-urlencoded')
				|| $this->cekContentType('multipart/form-data')
			) {
				foreach ($_POST as $key => $value) {
					if (is_array($value)) {
						$path['post'][$key] = $value;
					} else {
						$value              = preg_replace("/[^\x20-\x7E]/", "", $value);
						$path['post'][$key] = $value;
					}
				}
			} elseif ($this->cekContentType('application/json')) {
				$body   = file_get_contents('php://input');
				$_body2 = null;

				parse_str($body, $path['post']);

				$_body  = json_decode($body, true);
				$_body2 = parse_str($body, $path['post']) ?? null;

				if (!$_body && $_body2) $path['post'] = $_body2;

				if ($_body && !$_body2) $path['post'] = $_body;
			}
		}

		if (in_array($path['requestMethod'], ['DELETE', 'PUT'])) {
			$path['body'] = file_get_contents('php://input');

			if ($path['body']) $path['post'] = json_decode($path['body'], true);

			if (empty($path['post']) && strpos($path['body'], '&') !== false)
				parse_str($path['body'], $path['post']);
		}

		// FILES
		if (isset($_FILES) && !empty($_FILES)) $path['files'] = $_FILES;


		// Kumpulkan semua data http
		$data = [];

		if (isset($path['post'])) $data = $path['post'];

		if (isset($path['get'])) $data = array_merge($data, $path['get']);

		if (!empty($data)) $path['data'] = $data;

		// Muat ke dalam payload
		self::$payload = [
			'accept' => $path['accept'] ? array_map('trim', explode(',', $path['accept']) ) : null,
			'requestMethod' => $path['requestMethod'],
			'path' => $path['path'],
			'payload' => $path['data'] ?? null,
			'files' => $path['files'] ?? null,
		];
	}

	private function cekPath()
	{
		// print_r( str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) );
	}

	private function cekRequestMethod()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET') return 'GET';
		elseif ($_SERVER['REQUEST_METHOD'] === 'POST') return 'POST';
		elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') return 'PUT';
		elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') return 'DELETE';
		elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') return 'PATCH';
	}

	private function cekContentType(string $cekContent = '')
	{
		if (empty($this->requestContentType) || empty($cekContent)) return false;

		if (in_array($cekContent, $this->requestContentType)) return true;

		return false;
	}

	private function getVariable(string $parameter, string $path): void
	{
		$path   = strtoupper($path);
		$respon = '';

		if ($path == 'COMPANY_NAME') $respon = COMPANY_NAME;
		elseif ($path == 'COMPANY_ADDRESS') $respon = COMPANY_ADDRESS;
		elseif ($path == 'NAME') $respon = NAME;

		if ($respon) {
			RLA\App::respon($respon);
		}
	}

	private function responNotFound(): void
	{
		RLA\App::responGagal('Modul tidak ditemukan');
	}
}
?>