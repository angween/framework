<?php
namespace RLAtech\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use RLAtech\controller\App;

//use RLAtech\model\Cari as Model;

class Dashboard {

	public function index()
	{
		if ( ! App::view( PATH_FILE_VIEW . 'html/systems/dashboard.php' ) ) {
			Router::notFound('index');
		}
	}
}