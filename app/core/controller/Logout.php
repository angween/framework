<?php
namespace core\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use core\controller\App;
class Logout {
	public function __construct() {
	}

	public function index()
	{
		// catat riwayat signout
		$riwayat = new Riwayat();

		if ( isset( App::$loggedInUser->id ) ) {
			$riwayat->postRiwayat([
				'table' => '_user_riwayat',
				'key_id' => 'user_id',
				'key_value' => App::$loggedInUser->id,
				'riwayat' => [
					'tm' => date('Y-m-d H:i:s'),
					'rw' => 'LOU'
				],
			]);
		}

		unset($_SESSION[SESSION_NAME]);

		// session_unset();
		// session_destroy();

		App::respon(
			redirect: URL_ROOT,
			contentType: ['text/html'],
			result: 'Anda berhasil logout dari sistem.'
		);
	}
}
?>