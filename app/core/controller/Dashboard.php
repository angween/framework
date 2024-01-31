<?php
namespace core\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use core\controller\App;

//use core\model\Cari as Model;

class Dashboard {

	public function index()
	{
		if ( ! App::view( PATH_CORE_VIEW . 'page/dashboard.php' ) ) {
			Router::notFound('index');
		}
	}
}