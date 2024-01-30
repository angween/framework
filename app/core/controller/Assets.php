<?php
namespace RLAtech\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

use RLAtech\controller\App;

class Assets 
{
	public function image(array $params)
	{
		if ( $params['payload']['query'] == 'logo' ) $pathImg = 'view/assets/images/logo.svg';
		if ( $params['payload']['query'] == 'brand' ) $pathImg = 'view/assets/images/brand.svg';

		App::view( $pathImg );
	}
}