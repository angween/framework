<?php
namespace core\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use core\controller\App;
class View {
	public $path = "aset/js/";

	public function __construct(){}

	public function js( $params )
	{
		if ( ! Router::$payload['path'][0] ) {
			App::respon([
				'status' => 'gagal',
				'pesan' => 'Perintah tidak dikenal.'
			]);
		}

		$file = Router::$payload['path'][0];

		$path = $this->path . $file . ".js.php";

		if( file_exists( $path ) ) include( $path );

		exit;
	}
}
?>