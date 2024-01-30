<?php
namespace RLAtech\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use RLAtech\controller\App;
class Debug
{
	public function index()
	{
	}

	public function delete( $parameter = null )
	{
		App::respon(
			contentType: ["aplication/json"],
			result: [
				'info' => 'DELETE request',
				'params' => $parameter,
			]
		);
	}

	/**
	 * Untuk tes method 
	 * @param
	 */
	public function tes( $value ) 
	{
		require ( "addons/controller/Products.php");
		$controllerAddon = "addons\\controller\\Products" ;
		$produk = new $controllerAddon;

		print_r(
			App::myNavigations()
		);
	}

	public function put( $parameter = null ) 
	{
		App::respon(
			contentType: ["aplication/json"],
			result: [
				'info' => 'PUT request',
				'params' => $parameter,
			]
		);
	}
}